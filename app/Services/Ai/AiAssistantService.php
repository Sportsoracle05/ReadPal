<?php

namespace App\Services\Ai;

use App\Models\Ai\AiConversation;
use App\Models\Ai\KnowledgeBase;
use App\Services\Ai\Providers\GeminiService;
use App\Services\Ai\Providers\GroqService;
use App\Services\Ai\Providers\HuggingFaceService;
use App\Services\Ai\Providers\OpenRouterService;
use App\Services\Ai\AiProviderLogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AiAssistantService (Hybrid Version)
 *
 * Orchestrates the full pipeline:
 *
 *  STEP 1 → KnowledgeRetrieverService  (FULLTEXT DB search — always runs)
 *  STEP 2 → GeminiService              (primary AI — rewrites DB content)
 *  STEP 3 → GroqService                (fast fallback if Gemini fails)
 *  STEP 4 → OpenRouterService          (multi-model fallback)
 *  STEP 5 → HuggingFaceService         (experimental last-resort AI)
 *  STEP 6 → DB-only ResponseFormatter  (no AI — raw template response)
 *
 * The DB search ALWAYS runs first. AI only receives the top 2-3 paragraphs
 * as context — it never sees the full knowledge base.
 *
 * Caching strategy:
 *  - Cache key: md5(user_id + base_id + normalized question)
 *  - TTL: 2 hours for AI responses, 30 min for DB-only responses
 *  - Only cache successful responses (confidence > 0)
 */
class AiAssistantService
{
    // Cache TTL constants (seconds)
    private const CACHE_TTL_AI      = 7200;  // 2 hours — AI responses change infrequently
    private const CACHE_TTL_DB_ONLY = 1800;  // 30 min — DB-only responses
    private const CACHE_TTL_NO_MATCH = 600;  // 10 min — "no result" answers

    public function __construct(
        private readonly QueryPreprocessorService  $preprocessor,
        private readonly KnowledgeRetrieverService $retriever,
        private readonly ResponseFormatterService  $formatter,
        private readonly PromptBuilderService      $promptBuilder,
        private readonly GeminiService             $gemini,
        private readonly GroqService               $groq,
        private readonly OpenRouterService         $openRouter,
        private readonly HuggingFaceService        $huggingFace,
    ) {}

    /**
     * Main entry point.
     *
     * @return array{
     *   answer: string,
     *   confidence: int,
     *   from_cache: bool,
     *   conversation_id: int|null,
     *   matched_paragraphs: int,
     *   ai_provider: string,
     *   strategy_used: string
     * }
     */
    public function ask(int $userId, string $question, ?int $baseId = null): array
    {
        $question = trim($question);

        if (empty($question) || mb_strlen($question) < 3) {
            return $this->errorResponse("Please ask a full question — at least a few words.");
        }

        if (mb_strlen($question) > 1000) {
            $question = mb_substr($question, 0, 1000);
        }

        // ── 1. Cache check ─────────────────────────────────────────────
        $cacheKey = $this->buildCacheKey($userId, $baseId, $question);
        $cached   = Cache::get($cacheKey);

        if ($cached !== null) {
            Log::debug("AI cache HIT for user {$userId}", ['key' => substr($cacheKey, -8)]);
            return array_merge($cached, ['from_cache' => true]);
        }

        // ── 3. DB Search (ALWAYS runs — never skipped) ──────────────────
        $processed = $this->preprocessor->process($question);

        if (empty($processed['keywords'])) {
            return $this->errorResponse(
                "Your question needs more specific terms. Try including a concept name or keyword."
            );
        }

        $retrieval = $this->retriever->retrieve($processed, null, $baseId); 

        // Determine subject for prompt framing
        $subject = $this->resolveSubject($baseId, $userId);

        // ── 4. Build AI context from DB results ─────────────────────────
        $paragraphTexts = $retrieval['paragraphs']->pluck('content')->toArray();
        $hasDBContent   = !empty($paragraphTexts) && $retrieval['confidence'] > 0;

        // ── 5. AI Fallback Chain ────────────────────────────────────────
        $aiResult = $this->runAIChain(
            question:      $question,
            paragraphs:    $paragraphTexts,
            subject:       $subject,
            hasDBContent:  $hasDBContent
        );

        // ── 6. Compose final answer ─────────────────────────────────────
        $finalAnswer = $this->composeFinalAnswer(
            aiResult:    $aiResult,
            retrieval:   $retrieval,
            question:    $question,
            hasDBContent: $hasDBContent
        );

        // ── 7. Persist conversation ─────────────────────────────────────
        $paragraphIds = $retrieval['paragraphs']->pluck('id')->toArray();

        $conversation = AiConversation::create([
            'user_id'               => $userId,
            'knowledge_base_id'     => $baseId,
            'question'              => $question,
            'search_keywords'       => implode(', ', $processed['keywords']),
            'answer'                => $finalAnswer,
            'matched_paragraph_ids' => $paragraphIds,
            'confidence_score'      => $retrieval['confidence'],
            'from_cache'            => false,
            // Extra columns (add migration if needed):
            // 'ai_provider'          => $aiResult['provider'],
            // 'tokens_used'          => $aiResult['tokens_used'],
        ]);

        // ── 8. Build and cache result ───────────────────────────────────
        $result = [
            'answer'             => $finalAnswer,
            'confidence'         => $retrieval['confidence'],
            'from_cache'         => false,
            'conversation_id'    => $conversation->id,
            'matched_paragraphs' => count($paragraphIds),
            'ai_provider'        => $aiResult['provider'],
            'strategy_used'      => $retrieval['strategy_used'],
        ];

        $ttl = $this->resolveCacheTTL($aiResult['provider'], $retrieval['confidence']);
        if ($ttl > 0) {
            Cache::put($cacheKey, $result, $ttl);
        }

        return $result;
    }

    // ──────────────────────────────────────────────────────────────────
    // AI Chain: Gemini → Groq → OpenRouter → HuggingFace → DB-only
    // ──────────────────────────────────────────────────────────────────

    private function runAIChain(
        string $system,
        string $user,
        string $context = 'chat',
        ?int   $userId  = null
    ): array {
        $timer = AiProviderLogService::startTimer();
    
        if ($this->gemini->isAvailable()) {
            $r = $this->gemini->generate($system, $user);
            AiProviderLogService::record($r, $timer, $context, $userId);
            if ($r['success']) return $r;
            $timer = AiProviderLogService::startTimer(); // reset for next provider
        } else {
            AiProviderLogService::recordSkipped('gemini', $context, $userId);
        }
    
        if ($this->groq->isAvailable()) {
            $r = $this->groq->generate($system, $user);
            AiProviderLogService::record($r, $timer, $context, $userId);
            if ($r['success']) return $r;
            $timer = AiProviderLogService::startTimer();
        } else {
            AiProviderLogService::recordSkipped('groq', $context, $userId);
        }
    
        if ($this->openRouter->isAvailable()) {
            $r = $this->openRouter->generateWithFallbackModels($system, $user);
            AiProviderLogService::record($r, $timer, $context, $userId);
            if ($r['success']) return $r;
            $timer = AiProviderLogService::startTimer();
        } else {
            AiProviderLogService::recordSkipped('openrouter', $context, $userId);
        }
    
        if ($this->huggingFace->isAvailable()) {
            $r = $this->huggingFace->generate($system, $user);
            AiProviderLogService::record($r, $timer, $context, $userId);
            if ($r['success']) return $r;
        } else {
            AiProviderLogService::recordSkipped('huggingface', $context, $userId);
        }
    
        return ['success'=>false,'text'=>'','provider'=>'none','tokens_used'=>0,'error'=>'All providers failed'];
    }

    // ──────────────────────────────────────────────────────────────────
    // Compose final answer text
    // ──────────────────────────────────────────────────────────────────

    private function composeFinalAnswer(
        array  $aiResult,
        array  $retrieval,
        string $question,
        bool   $hasDBContent
    ): string {
        // AI succeeded — use its text directly (already polished)
        if ($aiResult['success'] && !empty($aiResult['text'])) {
            return $aiResult['text'];
        }

        // AI failed — fall back to our template-based formatter
        $formatted = $this->formatter->format(
            paragraphs:       $retrieval['paragraphs'],
            confidence:       $retrieval['confidence'],
            strategy:         $retrieval['strategy_used'],
            originalQuestion: $question
        );

        // Add a subtle notice that AI enhancement wasn't available
        $suffix = "\n\n_Note: AI enhancement is temporarily unavailable. This answer comes directly from your study materials._";

        return $formatted['answer'] . ($hasDBContent ? $suffix : '');
    }

    // ──────────────────────────────────────────────────────────────────
    // History
    // ──────────────────────────────────────────────────────────────────

    public function getHistory(int $userId, int $limit = 20)
    {
        return AiConversation::where('user_id', $userId)
            ->select(['id', 'question', 'answer', 'confidence_score', 'created_at', 'knowledge_base_id'])
            ->orderByDesc('created_at')
            ->paginate($limit);
    }

    // ──────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────

    private function resolveSubject(int $baseId = null, int $userId): string
    {
        if (!$baseId) {
            return '';
        }

        return KnowledgeBase::where('id', $baseId)
            ->where('user_id', $userId)
            ->value('subject') ?? '';
    }

    private function buildCacheKey(int $userId, ?int $baseId, string $question): string
    {
        $normalized = mb_strtolower(trim(preg_replace('/\s+/', ' ', $question)));
        return 'ai_answer_v2_' . md5("{$userId}_{$baseId}_{$normalized}");
    }

    private function resolveCacheTTL(string $provider, int $confidence): int
    {
        if ($provider === 'db_only') {
            return $confidence > 0 ? self::CACHE_TTL_DB_ONLY : self::CACHE_TTL_NO_MATCH;
        }

        // AI responses cached longer — they're expensive to regenerate
        return self::CACHE_TTL_AI;
    }

    private function errorResponse(string $message): array
    {
        return [
            'answer'             => $message,
            'confidence'         => 0,
            'from_cache'         => false,
            'conversation_id'    => null,
            'matched_paragraphs' => 0,
            'ai_provider'        => 'none',
            'strategy_used'      => 'error',
        ];
    }
}
