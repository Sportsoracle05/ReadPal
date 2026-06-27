<?php

namespace App\Services\Ai;

use App\Models\Ai\AssignmentSection;
use Illuminate\Support\Facades\Cache;
use App\Services\Ai\Providers\GeminiService;
use App\Services\Ai\Providers\GroqService;
use App\Services\Ai\Providers\OpenRouterService;

/**
 * AssignmentAiService
 *
 * Specialized AI service for the Assignment Writing feature.
 *
 * Extends the core hybrid AI pipeline with:
 *  1. Section-question awareness (uses the questions as core prompt)
 *  2. Knowledge base context injection (FULLTEXT search)
 *  3. Intelligent fallback: DB context → general AI knowledge
 *  4. Section-level caching (same section + same user doesn't re-call AI)
 *
 * Does NOT replace AiAssistantService — composes it.
 */
class AssignmentAiService
{
    private const CACHE_TTL = 1800; // 30 min per AI-generated section

    public function __construct(
        private readonly QueryPreprocessorService  $preprocessor,
        private readonly KnowledgeRetrieverService $retriever,
        private readonly PromptBuilderService      $promptBuilder,
        private readonly GeminiService             $gemini,
        private readonly GroqService               $groq,
        private readonly OpenRouterService         $openRouter,
        private readonly ResponseFormatterService  $formatter,
    ) {}

    /**
     * Generate content for a single section.
     *
     * @param  AssignmentSection  $section     The section being written
     * @param  string             $topic       The overall assignment topic
     * @param  string             $course      Course name/code for context
     * @param  int                $userId      For scoping KB search to user's materials
     * @param  int|null           $baseId      Optional: specific knowledge base to search
     * @return array{text: string, provider: string, used_kb: bool}
     */
    public function generateSection(
        AssignmentSection $section,
        string            $topic,
        string            $course,
        int               $userId,
        ?int              $baseId = null
    ): array {
        // ── 1. Cache check ──────────────────────────────────────
        $cacheKey = $this->buildCacheKey($section->id, $userId, $topic);
        $cached   = Cache::get($cacheKey);

        if ($cached) {
            return array_merge($cached, ['from_cache' => true]);
        }

        // ── 2. Build search query from section title + questions ─
        // We search using section title + first 2 questions for relevance
        $searchText = $section->title . ' ' . implode(' ', array_slice($section->questions ?? [], 0, 2));
        $processed  = $this->preprocessor->process($searchText);

        // ── 3. FULLTEXT search for relevant paragraphs ──────────
        $retrieval = $this->retriever->retrieve($processed, $userId, $baseId);

        $paragraphTexts = $retrieval['paragraphs']->pluck('content')->toArray();
        $hasContext     = !empty($paragraphTexts) && $retrieval['confidence'] >= 20;

        // ── 4. Build assignment-specific prompt ─────────────────
        $prompts = $this->buildAssignmentPrompt(
            section:   $section,
            topic:     $topic,
            course:    $course,
            context:   $paragraphTexts,
            hasContext: $hasContext
        );

        // ── 5. Run AI chain ──────────────────────────────────────
        $aiResult = $this->runAIChain($prompts['system'], $prompts['user']);

        // ── 6. Fallback to formatter if all AI fails ─────────────
        if (!$aiResult['success'] && $hasContext) {
            $formatted = $this->formatter->format(
                $retrieval['paragraphs'], $retrieval['confidence'],
                $retrieval['strategy_used'], $section->title
            );

            return [
                'text'       => $formatted['answer'],
                'provider'   => 'db_only',
                'used_kb'    => true,
                'from_cache' => false,
            ];
        }

        if (!$aiResult['success']) {
            return [
                'text'       => 'Unable to generate content at this time. Please try again or write manually.',
                'provider'   => 'error',
                'used_kb'    => false,
                'from_cache' => false,
            ];
        }

        $result = [
            'text'       => $aiResult['text'],
            'provider'   => $aiResult['provider'],
            'used_kb'    => $hasContext,
            'from_cache' => false,
        ];

        Cache::put($cacheKey, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * "Improve" existing user-written content.
     * Takes what the user wrote + section questions and polishes it.
     */
    public function improveContent(
        AssignmentSection $section,
        string            $existingContent,
        string            $topic
    ): array {
        $questionsText = $section->getQuestionsAsText();

        $system = <<<SYS
You are an academic writing assistant.
Your task is to improve a student's draft for a university assignment section.
Do NOT change the meaning or add new facts.
Improve: clarity, academic tone, sentence flow, and coherence.
Ensure the questions below are properly addressed in the final text.
Keep response between 120–200 words. Use formal academic language.
SYS;

        $user = <<<USR
SECTION: {$section->title}
TOPIC: {$topic}

QUESTIONS THIS SECTION MUST ANSWER:
{$questionsText}

STUDENT'S DRAFT:
{$existingContent}

Improve this draft to be more academic and ensure all questions are clearly addressed.
USR;

        return $this->runAIChain(trim($system), trim($user));
    }

    // ──────────────────────────────────────────────────────────
    // Prompt Engineering
    // ──────────────────────────────────────────────────────────

    private function buildAssignmentPrompt(
        AssignmentSection $section,
        string $topic,
        string $course,
        array  $context,
        bool   $hasContext
    ): array {
        $questionsText = $section->getQuestionsAsText();
        $courseInfo    = $course ? "Course: {$course}" : '';

        $system = <<<SYS
You are an academic assistant helping a university student write an assignment.
{$courseInfo}

STRICT RULES:
- Answer the listed questions directly and in order
- Use formal academic language (no slang, no contractions)
- Write as a single coherent section — NOT a Q&A list
- Do NOT copy context verbatim — paraphrase and integrate naturally
- If context is weak or missing, use accurate general academic knowledge
- Keep your response between 120 and 200 words
- Do NOT include a heading or title in your response
- Start directly with substantive content
SYS;

        // Context section of user prompt
        $contextBlock = '';
        if ($hasContext) {
            $preparedContext = $this->promptBuilder->prepareContext($context);
            $contextBlock    = "\nCONTEXT FROM STUDY MATERIALS:\n{$preparedContext}\n\nUse the above context where relevant. If it does not fully address the questions, supplement with accurate general knowledge.";
        } else {
            $contextBlock = "\nNote: No specific study material context was found. Use your accurate general academic knowledge to answer the questions.";
        }

        $user = <<<USR
Write a well-structured section titled "{$section->title}" for an assignment on the topic:
"{$topic}"

Answer the following questions in your section — integrate them into flowing paragraphs, do NOT list them as Q&A:
{$questionsText}
{$contextBlock}

Write the section now:
USR;

        return ['system' => trim($system), 'user' => trim($user)];
    }

    // ──────────────────────────────────────────────────────────
    // AI Chain (Gemini → Groq → OpenRouter)
    // ──────────────────────────────────────────────────────────

    private function runAIChain(string $system, string $user): array
    {
        if ($this->gemini->isAvailable()) {
            $r = $this->gemini->generate($system, $user);
            if ($r['success']) return $r;
        }

        if ($this->groq->isAvailable()) {
            $r = $this->groq->generate($system, $user);
            if ($r['success']) return $r;
        }

        if ($this->openRouter->isAvailable()) {
            $r = $this->openRouter->generateWithFallbackModels($system, $user);
            if ($r['success']) return $r;
        }

        return ['success' => false, 'text' => '', 'provider' => 'none', 'tokens_used' => 0, 'error' => 'All providers failed'];
    }

    private function buildCacheKey(int $sectionId, int $userId, string $topic): string
    {
        return 'assign_section_' . md5("{$sectionId}_{$userId}_{$topic}");
    }
}
