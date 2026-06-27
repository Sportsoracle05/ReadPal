<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\Ai\KnowledgeBase;
use App\Models\Ai\KnowledgeParagraph;
use App\Models\Ai\KnowledgeTag;
use App\Services\Ai\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Ai\AiConversation;
    
class AiController extends Controller
{
    
    
    // ── Chat page ─────────────────────────────────────────────────
    
    public function chatPage(Request $request)
    {
        $user   = $request->user();
        $baseId = $request->query('base');
    
        // Sidebar: all user's bases (lightweight — no content)
        $knowledgeBases = KnowledgeBase::withCount('paragraphs')
        ->orderByDesc('updated_at')
        ->get(['id', 'title', 'subject', 'course_code', 'is_public', 'updated_at']);
    
        // Current base (if selected)
        $currentBase = $baseId
            ? KnowledgeBase::where('id', $baseId)
                            ->where('user_id', $user->id)
                            ->first()
            : null;
    
        // Sidebar recent history (5 items max — no content column)
        $recentHistory = AiConversation::where('user_id', $user->id)
            ->select(['id', 'question', 'knowledge_base_id', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    
        // Pre-populate question from ?q= (from "ask again" on history page)
        $prefillQuestion = $request->query('q');
        
        return view('ai.chat', compact(
            'knowledgeBases', 'currentBase', 'recentHistory', 'prefillQuestion'
        ));
    }
    
    // ── Knowledge bases index ──────────────────────────────────────
    
    public function knowledgeBasesIndex(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can create knowledge bases.');
        }
        
        $user = $request->user();
    
        $knowledgeBases = KnowledgeBase::where('user_id', $user->id)
            ->withCount('paragraphs')
            ->orderByDesc('created_at')
            ->get();
    
        $recentHistory = AiConversation::where('user_id', $user->id)
            ->select(['id', 'question', 'knowledge_base_id', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    
        $totalConversations = AiConversation::where('user_id', $user->id)->count();
    
        return view('ai.knowledge-bases.index', compact(
            'knowledgeBases', 'recentHistory', 'totalConversations'
        ));
    }
    
    // ── Create page ────────────────────────────────────────────────
    
    public function createPage(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can create knowledge bases.');
        }
        
        $user   = $request->user();
        $addToId = $request->query('add_to');
    
        $knowledgeBases = KnowledgeBase::where('user_id', $user->id)
            ->withCount('paragraphs')
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'subject', 'course_code', 'is_public', 'updated_at']);
    
        $recentHistory = AiConversation::where('user_id', $user->id)
            ->select(['id', 'question', 'knowledge_base_id', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    
        // If "add_to" param set, pre-load the target base
        $selectedBase = $addToId
            ? KnowledgeBase::where('id', $addToId)
                            ->where('user_id', $user->id)
                            ->withCount('paragraphs')
                            ->first()
            : null;
    
        return view('ai.knowledge-bases.create', compact(
            'knowledgeBases', 'recentHistory', 'selectedBase'
        ));
    }
    
    // ── History page ───────────────────────────────────────────────
    
    public function historyPage(Request $request)
    {
        $user = $request->user();
    
        $history = AiConversation::where('user_id', $user->id)
            ->with(['knowledgeBase:id,title']) // eager load base name only
            ->select([
                'id', 'question', 'answer', 'confidence_score',
                'search_keywords', 'matched_paragraph_ids',
                'from_cache', 'knowledge_base_id', 'created_at'
            ])
            ->orderByDesc('created_at')
            ->paginate(15);
    
        $knowledgeBases = KnowledgeBase::where('user_id', $user->id)
            ->withCount('paragraphs')
            ->orderByDesc('updated_at')
            ->get(['id', 'title', 'subject', 'course_code', 'is_public', 'updated_at']);
    
        $recentHistory = $history->getCollection()
            ->take(5)
            ->map(fn($c) => (object)['id' => $c->id, 'question' => $c->question]);
    
        return view('ai.history', compact('history', 'knowledgeBases', 'recentHistory'));
    }
    
    
    
    public function __construct(
        private readonly AiAssistantService $assistant
    ) {}

    // ──────────────────────────────────────────────────────────────────
    // POST /ai/ask
    // Main chat endpoint
    // ──────────────────────────────────────────────────────────────────

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question'          => ['required', 'string', 'min:3', 'max:1000'],
            'knowledge_base_id' => ['nullable', 'integer', 'exists:ai.knowledge_bases,id'],
        ]);

        $result = $this->assistant->ask(
            userId:   $request->user()->id,
            question: $validated['question'],
            baseId:   $validated['knowledge_base_id'] ?? null
        );

        return response()->json([
            'success'            => true,
            'answer'             => $result['answer'],
            'confidence'         => $result['confidence'],
            'from_cache'         => $result['from_cache'],
            'conversation_id'    => $result['conversation_id'],
            'matched_paragraphs' => $result['matched_paragraphs'],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // GET /ai/history
    // ──────────────────────────────────────────────────────────────────

    public function history(Request $request): JsonResponse
    {
        $history = $this->assistant->getHistory($request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // POST /ai/knowledge-bases
    // Create a new knowledge base
    // ──────────────────────────────────────────────────────────────────

    public function storeBase(Request $request)
{
    if (!$request->user()->isAdmin()) {
        abort(403, 'Only admins can create knowledge bases.');
    }
    
    $validated = $request->validate([
        'title'       => ['required', 'string', 'max:200'],
        'subject'     => ['nullable', 'string', 'max:100'],
        'course_code' => ['nullable', 'string', 'max:20'],
        'description' => ['nullable', 'string', 'max:500'],
        'is_public'   => ['boolean'],
        'text'        => ['nullable', 'string'], // Add this
    ]);

    $base = KnowledgeBase::create([
        'title'       => $validated['title'],
        'subject'     => $validated['subject'],
        'course_code' => $validated['course_code'],
        'description' => $validated['description'],
        'is_public'   => $request->boolean('is_public'),
        'user_id'     => $request->user()->id,
    ]);

    // Check if there is text to process
    if ($request->filled('text')) {
        // Instead of redirecting, call your storeContent logic directly
        // We pass the $base->id as the second argument
        $this->storeContent($request, $base->id);
    }

    return redirect()->route('ai.knowledge-bases.index')
                     ->with('success', 'Knowledge base and content created!');
}

    
    /**
     * Extract the paragraph-saving logic into its own internal method 
     * so both storeBase and storeContent can use it.
     */
    private function processTextIntoParagraphs($base, $request) {
        $base = KnowledgeBase::where('id', $baseId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            // Accept either a full text block or pre-split paragraphs
            'text'             => ['nullable', 'string', 'max:100000'],
            'paragraphs'       => ['nullable', 'array', 'max:200'],
            'paragraphs.*'     => ['string', 'min:10', 'max:10000'],
            'section_heading'  => ['nullable', 'string', 'max:200'],
            'tags'             => ['nullable', 'array', 'max:30'],
            'tags.*'           => ['string', 'max:80'],
        ]);

        // Auto-split a full text block into paragraphs if needed
        if (!empty($validated['text']) && empty($validated['paragraphs'])) {
            $validated['paragraphs'] = $this->splitIntoParagraphs($validated['text']);
        }

        if (empty($validated['paragraphs'])) {
            return response()->json(['success' => false, 'message' => 'No content provided.'], 422);
        }

        // Get current max position for this base
        $maxPosition = KnowledgeParagraph::where('knowledge_base_id', $baseId)->max('position') ?? 0;

        $createdIds = [];

        // Wrap in DB transaction so partial uploads don't corrupt state
        DB::connection('ai')->transaction(function () use (
            $base, $validated, $maxPosition, $request, &$createdIds
        ) {
            foreach ($validated['paragraphs'] as $index => $text) {
                $text = trim($text);
                if (empty($text) || str_word_count($text) < 5) {
                    continue; // skip junk fragments
                }

                $para = KnowledgeParagraph::create([
                    'knowledge_base_id' => $base->id,
                    'user_id'           => $request->user()->id,
                    'content'           => $text,
                    'section_heading'   => $validated['section_heading'] ?? null,
                    'position'          => $maxPosition + $index + 1,
                    // excerpt and word_count are auto-set in the model's saving hook
                ]);

                $createdIds[] = $para->id;

                // Attach tags to this paragraph
                if (!empty($validated['tags'])) {
                    foreach ($validated['tags'] as $tag) {
                        KnowledgeTag::firstOrCreate([
                            'knowledge_paragraph_id' => $para->id,
                            'knowledge_base_id'      => $base->id,
                            'tag'                    => mb_strtolower(trim($tag)),
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'success'             => true,
            'paragraphs_created'  => count($createdIds),
            'message'             => count($createdIds) . ' paragraph(s) added to your knowledge base.',
        ], 201);
    }


    // ──────────────────────────────────────────────────────────────────
    // POST /ai/knowledge-bases/{base}/content
    // Upload content into a knowledge base (paragraph-by-paragraph)
    // ──────────────────────────────────────────────────────────────────

    public function storeContent(Request $request, int $baseId)
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can create knowledge bases.');
        }
    
        $base = KnowledgeBase::where('id', $baseId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            // Accept either a full text block or pre-split paragraphs
            'text'             => ['nullable', 'string', 'max:100000'],
            'paragraphs'       => ['nullable', 'array', 'max:200'],
            'paragraphs.*'     => ['string', 'min:10', 'max:10000'],
            'section_heading'  => ['nullable', 'string', 'max:200'],
            'tags'             => ['nullable', 'array', 'max:30'],
            'tags.*'           => ['string', 'max:80'],
        ]);

        // Auto-split a full text block into paragraphs if needed
        if (!empty($validated['text']) && empty($validated['paragraphs'])) {
            $validated['paragraphs'] = $this->splitIntoParagraphs($validated['text']);
        }

        if (empty($validated['paragraphs'])) {
            return response()->json(['success' => false, 'message' => 'No content provided.'], 422);
        }

        // Get current max position for this base
        $maxPosition = KnowledgeParagraph::where('knowledge_base_id', $baseId)->max('position') ?? 0;

        $createdIds = [];

        // Wrap in DB transaction so partial uploads don't corrupt state
        DB::connection('ai')->transaction(function () use (
            $base, $validated, $maxPosition, $request, &$createdIds
        ) {
            foreach ($validated['paragraphs'] as $index => $text) {
                $text = trim($text);
                if (empty($text) || str_word_count($text) < 5) {
                    continue; // skip junk fragments
                }

                $para = KnowledgeParagraph::create([
                    'knowledge_base_id' => $base->id,
                    'user_id'           => $request->user()->id,
                    'content'           => $text,
                    'section_heading'   => $validated['section_heading'] ?? null,
                    'position'          => $maxPosition + $index + 1,
                    // excerpt and word_count are auto-set in the model's saving hook
                ]);

                $createdIds[] = $para->id;

                // Attach tags to this paragraph
                if (!empty($validated['tags'])) {
                    foreach ($validated['tags'] as $tag) {
                        KnowledgeTag::firstOrCreate([
                            'knowledge_paragraph_id' => $para->id,
                            'knowledge_base_id'      => $base->id,
                            'tag'                    => mb_strtolower(trim($tag)),
                        ]);
                    }
                }
            }
        });

        // At the bottom of storeContent(), replace the return statement with this:
        if ($request->expectsJson()) {
            return response()->json([
                'success'             => true,
                'paragraphs_created'  => count($createdIds),
                'message'             => count($createdIds) . ' paragraph(s) added.',
            ], 201);
        }
        
        return redirect()->route('ai.knowledge-bases.index')
                         ->with('success', count($createdIds) . ' paragraph(s) added successfully.');

    }

    // ──────────────────────────────────────────────────────────────────
    // GET /ai/knowledge-bases
    // ──────────────────────────────────────────────────────────────────

    public function listBases(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Only admins can create knowledge bases.');
        }
    
        $bases = KnowledgeBase::where('user_id', $request->user()->id)
            ->withCount('paragraphs')
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'subject', 'course_code', 'description', 'is_public', 'created_at']);

        return response()->json(['success' => true, 'data' => $bases]);
    }

    // ──────────────────────────────────────────────────────────────────
    // DELETE /ai/knowledge-bases/{base}
    // ──────────────────────────────────────────────────────────────────

    public function deleteBase(Request $request, int $baseId): JsonResponse
    {
        $base = KnowledgeBase::where('id', $baseId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $base->delete(); // soft delete — paragraphs cascade

        return response()->json(['success' => true, 'message' => 'Knowledge base deleted.']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Split a large text block into paragraphs.
     * Strategy: split on double newlines, then re-split long single blocks by sentence.
     */
    private function splitIntoParagraphs(string $text): array
    {
        // Primary split: double newline (standard paragraph break)
        $chunks = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $paragraphs = [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            $wordCount = str_word_count($chunk);

            // Very long single paragraph? Split by sentence groups (every 5 sentences)
            if ($wordCount > 300) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $chunk, -1, PREG_SPLIT_NO_EMPTY);
                $groups    = array_chunk($sentences, 5);

                foreach ($groups as $group) {
                    $joined = implode(' ', $group);
                    if (str_word_count($joined) >= 10) {
                        $paragraphs[] = $joined;
                    }
                }
            } elseif ($wordCount >= 5) {
                $paragraphs[] = $chunk;
            }
        }

        return $paragraphs;
    }
}
