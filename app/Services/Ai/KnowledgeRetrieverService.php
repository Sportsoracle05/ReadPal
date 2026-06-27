<?php

namespace App\Services\Ai;

use App\Models\Ai\KnowledgeParagraph;
use App\Models\Ai\KnowledgeTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * KnowledgeRetrieverService
 *
 * The search engine brain. Takes preprocessed keywords and returns
 * the top N most relevant paragraphs from the knowledge base.
 *
 * Three-stage retrieval:
 *   Stage 1 — Tag exact match (fastest, most precise)
 *   Stage 2 — FULLTEXT MATCH AGAINST in BOOLEAN MODE (primary search)
 *   Stage 3 — LIKE fallback (last resort, catches short words MySQL FT ignores)
 *
 * Results are merged, deduplicated, and scored before returning.
 */
class KnowledgeRetrieverService
{
    // Never return more than this many paragraphs — protects RAM
    private const MAX_RESULTS = 5;

    // Minimum FULLTEXT relevance score to include a result
    // (0.0 = any match, higher = stricter. 0.1 is a good starting point)
    private const MIN_RELEVANCE = 0.05;

    public function __construct(
        private readonly QueryPreprocessorService $preprocessor
    ) {}

    /**
     * Main entry point.
     *
     * @param  array       $processed     Output from QueryPreprocessorService::process()
     * @param  int         $userId        Current user's ID
     * @param  int|null    $baseId        Limit search to a specific knowledge base (null = search all)
     * @return array{
     *   paragraphs: Collection,
     *   confidence: int,
     *   strategy_used: string
     * }
     */
    public function retrieve(array $processed, ?int $userId = null, ?int $baseId = null): array
    {
        $keywords       = $processed['keywords'];
        $fulltextQuery  = $processed['fulltext_query'];

        // ── Stage 1: Tag exact match ──────────────────────────────────
        $tagResults = $this->searchByTags($keywords, null, $baseId);

        if ($tagResults->count() >= 2) {
            return [
                'paragraphs'    => $tagResults->take(self::MAX_RESULTS),
                'confidence'    => 85,
                'strategy_used' => 'tag_match',
            ];
        }

        // ── Stage 2: FULLTEXT BOOLEAN MODE ───────────────────────────
        if (!empty($fulltextQuery)) {
            $ftResults = $this->searchFulltext($fulltextQuery, null, $baseId);

            if ($ftResults->count() > 0) {
                // Merge with any tag results for bonus hits
                $merged = $this->mergeAndRank($tagResults, $ftResults);

                return [
                    'paragraphs'    => $merged->take(self::MAX_RESULTS),
                    'confidence'    => $this->calculateConfidence($ftResults),
                    'strategy_used' => 'fulltext',
                ];
            }

            // 2b: Retry with relaxed (no + operators) if strict returned nothing
            $relaxedQuery  = $this->preprocessor->buildRelaxedQuery($keywords);
            $ftResultsRelaxed = $this->searchFulltext($relaxedQuery, $userId, $baseId);

            if ($ftResultsRelaxed->count() > 0) {
                return [
                    'paragraphs'    => $ftResultsRelaxed->take(self::MAX_RESULTS),
                    'confidence'    => 35,
                    'strategy_used' => 'fulltext_relaxed',
                ];
            }
        }

        // ── Stage 3: LIKE fallback ────────────────────────────────────
        $likeResults = $this->searchLike($processed['like_query'], $userId, $baseId);

        if ($likeResults->count() > 0) {
            return [
                'paragraphs'    => $likeResults->take(self::MAX_RESULTS),
                'confidence'    => 20,
                'strategy_used' => 'like_fallback',
            ];
        }

        // ── Nothing found ─────────────────────────────────────────────
        return [
            'paragraphs'    => collect(),
            'confidence'    => 0,
            'strategy_used' => 'no_match',
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Stage 1: Tag Search
    // ──────────────────────────────────────────────────────────────────

    private function searchByTags(array $keywords, ?int $userId, ?int $baseId): Collection
    {
        if (empty($keywords)) return collect();
    
        $tagQuery = KnowledgeTag::whereIn('tag', $keywords)
            ->when($baseId, fn($q) => $q->where('knowledge_base_id', $baseId))
            // ->where('user_id', $userId) // REMOVE THIS IF IT EXISTS HERE
            ->select('knowledge_paragraph_id', DB::raw('COUNT(*) as tag_hits'))
            ->groupBy('knowledge_paragraph_id')
            ->orderByDesc('tag_hits')
            ->limit(10)
            ->pluck('knowledge_paragraph_id');
    
        if ($tagQuery->isEmpty()) return collect();
    
        return KnowledgeParagraph::whereIn('id', $tagQuery)
            // REMOVED: ->where('user_id', $userId) 
            ->substantial() 
            ->select(['id', 'knowledge_base_id', 'excerpt', 'section_heading', 'word_count', 'content'])
            ->get();
    }


    // ──────────────────────────────────────────────────────────────────
    // Stage 2: FULLTEXT Search
    // ──────────────────────────────────────────────────────────────────

    private function searchFulltext(string $fulltextQuery, ?int $userId, ?int $baseId): Collection
    {
        if (empty($fulltextQuery)) return collect();
    
        try {
            return DB::connection('ai')
                ->table('knowledge_paragraphs')
                ->select([
                    'id',
                    'knowledge_base_id',
                    'excerpt',
                    'section_heading',
                    'word_count',
                    'content',
                    DB::raw("MATCH(content, section_heading) AGAINST(? IN BOOLEAN MODE) AS relevance"),
                ])
                ->addBinding($fulltextQuery, 'select')
                // REMOVED: ->where('user_id', $userId)
                ->where('word_count', '>=', 15)
                ->when($baseId, fn($q) => $q->where('knowledge_base_id', $baseId))
                ->whereRaw("MATCH(content, section_heading) AGAINST(? IN BOOLEAN MODE)", [$fulltextQuery])
                ->having('relevance', '>', self::MIN_RELEVANCE)
                ->orderByDesc('relevance')
                ->limit(self::MAX_RESULTS + 2)
                ->get();
        } catch (\Exception $e) {
            Log::warning('AI FULLTEXT search failed: ' . $e->getMessage());
            return collect();
        }
    }


    // ──────────────────────────────────────────────────────────────────
    // Stage 3: LIKE Fallback
    // ──────────────────────────────────────────────────────────────────

    private function searchLike(string $keyword, ?int $userId, ?int $baseId): Collection
    {
        if (empty($keyword) || strlen($keyword) < 3) return collect();
    
        return KnowledgeParagraph::query()
            // REMOVED: ->where('user_id', $userId)
            ->substantial()
            ->when($baseId, fn($q) => $q->where('knowledge_base_id', $baseId))
            ->where('content', 'LIKE', '%' . $keyword . '%')
            ->select(['id', 'knowledge_base_id', 'excerpt', 'section_heading', 'word_count', 'content'])
            ->orderByDesc('word_count')
            ->limit(self::MAX_RESULTS)
            ->get();
    }


    // ──────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Merge tag results + fulltext results, deduplicate by ID.
     * Tag results get priority (they stay at the front).
     */
    private function mergeAndRank(Collection $tagResults, Collection $ftResults): Collection
    {
        $tagIds = $tagResults->pluck('id')->toArray();

        // Add FT results that aren't already in tag results
        $additional = $ftResults->filter(fn($p) => !in_array($p->id, $tagIds));

        return $tagResults->concat($additional);
    }

    /**
     * Map FULLTEXT relevance scores to a 0-100 confidence int.
     * Scores above ~1.0 are very rare; typical good matches are 0.1–0.5.
     */
    private function calculateConfidence(Collection $results): int
    {
        if ($results->isEmpty()) {
            return 0;
        }

        $topScore = $results->max('relevance') ?? 0;

        return match(true) {
            $topScore >= 0.5  => 80,
            $topScore >= 0.2  => 65,
            $topScore >= 0.1  => 50,
            $topScore >= 0.05 => 35,
            default           => 15,
        };
    }
}
