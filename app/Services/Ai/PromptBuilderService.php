<?php

namespace App\Services\Ai;

/**
 * PromptBuilderService
 *
 * Single source of truth for all AI prompt construction.
 * Keeps prompt logic out of individual provider services.
 *
 * Core principle: every prompt follows the same structure —
 *   ROLE → CONTEXT → TASK → CONSTRAINTS
 *
 * The context window budget is ~2000 chars of DB content.
 * Tokens beyond that waste API credits and slow responses.
 */
class PromptBuilderService
{
    // Hard ceiling on context characters sent to any AI
    private const MAX_CONTEXT_CHARS = 2000;

    // Target response length instruction (words)
    private const TARGET_WORDS = '120 to 180';

    /**
     * Build a full system + user prompt pair for answering from DB context.
     *
     * @param  string   $question    Original user question
     * @param  string[] $paragraphs  Raw DB paragraphs (content strings)
     * @param  string   $subject     Optional subject area for framing (e.g. "Sociology")
     * @return array{system: string, user: string}
     */
    public function buildAnswerPrompt(
        string $question,
        array  $paragraphs,
        string $subject = ''
    ): array {
        $context = $this->prepareContext($paragraphs);

        $subjectLine = $subject
            ? "The content is from the field of **{$subject}**."
            : '';

        $system = <<<SYSTEM
You are a focused academic assistant helping a university student understand their study materials.
{$subjectLine}
Your role is to explain content clearly and concisely — like a knowledgeable tutor, not a textbook.

STRICT RULES:
- Base your answer ONLY on the provided context below. Do not invent information.
- Write in a natural, conversational tone. Avoid academic jargon unless explaining it.
- Summarize — do NOT copy the context word-for-word.
- Keep your response between {$this->buildWordTarget()} words.
- Use plain paragraphs. No markdown headers. No bullet lists unless genuinely helpful.
- If the context does not clearly answer the question, say so briefly and explain what the context does say.
SYSTEM;

        $user = <<<USER
CONTEXT FROM STUDY MATERIALS:
{$context}

STUDENT QUESTION:
{$question}

Provide a clear, helpful explanation based only on the context above.
USER;

        return ['system' => trim($system), 'user' => trim($user)];
    }

    /**
     * Build a no-context prompt when DB search found nothing.
     * We still call AI but with a honest framing.
     */
    public function buildNoContextPrompt(string $question, string $subject = ''): array
    {
        $subjectLine = $subject ? " in the field of {$subject}" : '';

        $system = <<<SYSTEM
You are an academic assistant for a university student.
The student's personal study materials did not contain information about their question.
Be honest about this limitation but still provide brief general knowledge if you can.
Keep your response under 100 words. Be helpful and encourage the student.
SYSTEM;

        $user = "The student's uploaded materials don't cover this topic. "
              . "Their question: \"{$question}\"{$subjectLine}. "
              . "Acknowledge the gap briefly and offer any general knowledge you have on the topic in 1-2 sentences.";

        return ['system' => trim($system), 'user' => trim($user)];
    }

    /**
     * Prepare and clean context from raw paragraph strings.
     *
     * Steps:
     *  1. Strip HTML
     *  2. Normalize whitespace
     *  3. Deduplicate sentences
     *  4. Merge paragraphs with separator
     *  5. Truncate to MAX_CONTEXT_CHARS
     */
    public function prepareContext(array $paragraphs): string
    {
        $cleaned = [];

        foreach ($paragraphs as $para) {
            $text = strip_tags((string) $para);
            $text = preg_replace('/\s+/', ' ', trim($text));
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (strlen($text) < 30) {
                continue; // skip noise fragments
            }

            $cleaned[] = $text;
        }

        if (empty($cleaned)) {
            return '';
        }

        // Deduplicate: remove nearly identical paragraphs
        $cleaned = $this->deduplicateParagraphs($cleaned);

        // Join with clear separator
        $merged = implode("\n\n---\n\n", $cleaned);

        // Hard truncate to budget
        if (mb_strlen($merged) > self::MAX_CONTEXT_CHARS) {
            $merged = mb_substr($merged, 0, self::MAX_CONTEXT_CHARS);
            // Don't cut mid-sentence — find last period
            $lastPeriod = strrpos($merged, '.');
            if ($lastPeriod > self::MAX_CONTEXT_CHARS * 0.7) {
                $merged = substr($merged, 0, $lastPeriod + 1);
            }
            $merged .= ' [content truncated for length]';
        }

        return $merged;
    }

    /**
     * Remove paragraphs that are > 80% similar to an already-included one.
     * Simple character-level overlap check — no heavy libs needed.
     */
    private function deduplicateParagraphs(array $paragraphs): array
    {
        $result = [];

        foreach ($paragraphs as $candidate) {
            $isDuplicate = false;

            foreach ($result as $existing) {
                similar_text(
                    mb_strtolower($candidate),
                    mb_strtolower($existing),
                    $similarity
                );

                if ($similarity > 75) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $result[] = $candidate;
            }
        }

        return $result;
    }

    private function buildWordTarget(): string
    {
        return self::TARGET_WORDS;
    }
}
