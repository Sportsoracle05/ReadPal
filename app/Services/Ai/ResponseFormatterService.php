<?php

namespace App\Services\Ai;

use Illuminate\Support\Collection;

/**
 * ResponseFormatterService
 *
 * This is the "fake AI" layer — the part that turns raw database paragraphs
 * into something that feels like a helpful assistant wrote it.
 *
 * It does NOT invent information. It only:
 *   1. Selects the best paragraph(s) to present
 *   2. Wraps them in a conversational tone
 *   3. Summarizes long content intelligently
 *   4. Adapts the response style to the confidence level
 */
class ResponseFormatterService
{
    /**
     * Build the final response string.
     *
     * @param  Collection  $paragraphs    Retrieved paragraphs (already ranked)
     * @param  int         $confidence    0-100 confidence score
     * @param  string      $strategy      Which retrieval strategy was used
     * @param  string      $originalQuestion
     * @return array{answer: string, summary: string}
     */
    public function format(
        Collection $paragraphs,
        int $confidence,
        string $strategy,
        string $originalQuestion
    ): array {
        // ── No results found ──────────────────────────────────────────
        if ($paragraphs->isEmpty() || $strategy === 'no_match') {
            return $this->buildNoMatchResponse($originalQuestion);
        }

        // ── Weak match — be honest but still helpful ──────────────────
        if ($confidence <= 20) {
            return $this->buildWeakMatchResponse($paragraphs, $originalQuestion);
        }

        // ── Good match — build a proper answer ───────────────────────
        return $this->buildMainResponse($paragraphs, $confidence, $originalQuestion);
    }

    // ──────────────────────────────────────────────────────────────────
    // Main Response (confident match)
    // ──────────────────────────────────────────────────────────────────

    private function buildMainResponse(
        Collection $paragraphs,
        int $confidence,
        string $question
    ): array {
        $primary   = $paragraphs->first();
        $secondary = $paragraphs->slice(1, 2); // up to 2 supporting paragraphs

        $intro = $this->pickIntro($confidence);

        // Process the primary paragraph content
        $mainContent = $this->processContent($primary->content);

        $body = $intro . "\n\n" . $mainContent;

        // Add supporting context if we have it and it's not too long
        if ($secondary->isNotEmpty()) {
            $supplemental = $this->buildSupplementalBlock($secondary);
            if (!empty($supplemental)) {
                $body .= "\n\n" . $supplemental;
            }
        }

        // Add section context if available
        if (!empty($primary->section_heading)) {
            $body .= "\n\n_This comes from the \"" . $primary->section_heading . "\" section of your materials._";
        }

        // Add a gentle closer
        $body .= "\n\n" . $this->pickCloser($confidence);

        return [
            'answer'  => $body,
            'summary' => $this->generateSummary($primary->content),
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Weak Match Response
    // ──────────────────────────────────────────────────────────────────

    private function buildWeakMatchResponse(Collection $paragraphs, string $question): array
    {
        $primary = $paragraphs->first();
        $content = $this->processContent($primary->content);

        $intros = [
            "I found something in your materials that might be related, though it may not be an exact match for your question.",
            "I didn't find a perfect answer, but here's the closest thing I found in your content:",
            "This might not answer your question directly, but it seems relevant — take a look:",
        ];

        $body = $intros[array_rand($intros)] . "\n\n" . $content;

        $tips = [
            "\n\n💡 _Try rephrasing your question with more specific keywords for a better match._",
            "\n\n💡 _If this isn't what you were looking for, try using key terms directly (e.g., a concept name or person's name)._",
        ];

        $body .= $tips[array_rand($tips)];

        return [
            'answer'  => $body,
            'summary' => $this->generateSummary($primary->content),
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // No Match Response
    // ──────────────────────────────────────────────────────────────────

    private function buildNoMatchResponse(string $question): array
    {
        $responses = [
            "Hmm, I couldn't find anything in your materials that matches \"**{question}**\". This topic might not be covered yet, or it could be phrased differently in your notes. Try using specific keywords like names, concepts, or terms from your readings.",
            "I searched through your materials but didn't find a clear match for that question. If you've uploaded notes on this topic, try asking with the exact terminology from those notes.",
            "Nothing came up for \"**{question}**\" in what's been added to your knowledge base. Make sure the relevant material has been uploaded, or try simplifying your question to just the key concept.",
        ];

        $response = $responses[array_rand($responses)];
        $answer   = str_replace('{question}', e($question), $response);

        return [
            'answer'  => $answer,
            'summary' => 'No matching content found.',
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Content Processors
    // ──────────────────────────────────────────────────────────────────

    /**
     * Process raw paragraph content for display.
     * - Strips HTML tags
     * - Summarizes if too long
     * - Adds light markdown for readability
     */
    private function processContent(string $content): string
    {
        $clean = strip_tags(trim($content));
        $clean = preg_replace('/\s+/', ' ', $clean);

        $wordCount = str_word_count($clean);

        // Short paragraph: return as-is
        if ($wordCount <= 80) {
            return $clean;
        }

        // Medium paragraph: summarize to first 2 sentences + indicate continuation
        if ($wordCount <= 200) {
            return $this->extractFirstSentences($clean, 2);
        }

        // Long paragraph: extract 3 sentences and add a summary note
        $excerpt = $this->extractFirstSentences($clean, 3);
        return $excerpt . "\n\n_[Content summarized for clarity. Your full materials contain more detail on this.]_";
    }

    /**
     * Extract the first N sentences from a block of text.
     */
    private function extractFirstSentences(string $text, int $n): string
    {
        // Split on '. ', '! ', '? ' — handles most cases
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $result = array_slice($sentences, 0, $n);
        return implode(' ', $result);
    }

    /**
     * Build a "you might also want to know" block from secondary paragraphs.
     */
    private function buildSupplementalBlock(Collection $paragraphs): string
    {
        $parts = [];

        foreach ($paragraphs as $para) {
            $excerpt = !empty($para->excerpt)
                ? $para->excerpt
                : $this->extractFirstSentences(strip_tags($para->content), 1);

            if (!empty($excerpt)) {
                $parts[] = "• " . $excerpt;
            }
        }

        if (empty($parts)) {
            return '';
        }

        $headers = [
            "**Related information from your materials:**",
            "**You might also find this useful:**",
            "**Here's some additional context:**",
        ];

        return $headers[array_rand($headers)] . "\n" . implode("\n", $parts);
    }

    /**
     * Generate a short 1-sentence summary for storing in ai_conversations.
     */
    private function generateSummary(string $content): string
    {
        $clean = strip_tags(trim($content));
        return $this->extractFirstSentences($clean, 1);
    }

    // ──────────────────────────────────────────────────────────────────
    // Tone & Template Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Pick a natural-sounding intro based on confidence level.
     * High confidence → direct. Low confidence → hedged.
     */
    private function pickIntro(int $confidence): string
    {
        if ($confidence >= 75) {
            $intros = [
                "Here's what your materials say about this:",
                "Based on what I found in your notes:",
                "According to your content, here's a clear explanation:",
                "Your materials cover this well — here's the key part:",
            ];
        } elseif ($confidence >= 50) {
            $intros = [
                "I found something relevant in your materials:",
                "Here's what I found that relates to your question:",
                "Based on your notes, here's the most relevant section:",
                "This should help answer your question:",
            ];
        } else {
            $intros = [
                "I found a partial match in your materials:",
                "Here's the closest thing I found to your question:",
                "This section seems most relevant to what you're asking:",
            ];
        }

        return $intros[array_rand($intros)];
    }

    /**
     * Pick a closing line that feels helpful, not robotic.
     */
    private function pickCloser(int $confidence): string
    {
        $closers = [
            "Hope that helps! Let me know if you'd like more detail on any part of this.",
            "Feel free to ask a follow-up if you need more clarity.",
            "If this didn't fully answer your question, try rephrasing with more specific terms.",
            "You can also ask about a specific sub-topic for a more focused answer.",
        ];

        return $closers[array_rand($closers)];
    }
}
