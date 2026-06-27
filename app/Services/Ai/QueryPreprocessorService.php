<?php

namespace App\Services\Ai;

/**
 * QueryPreprocessorService
 *
 * Cleans user questions before sending to MySQL FULLTEXT.
 *
 * Pipeline:
 *   Raw question → lowercase → strip punctuation → remove stopwords
 *   → extract keywords → build MySQL FULLTEXT query string
 *
 * Why we do this in PHP and not rely on MySQL alone:
 *   MySQL stopword lists vary by version. We want consistent behavior
 *   and we want to keep the BOOLEAN MODE query clean and safe.
 */
class QueryPreprocessorService
{
    /**
     * English stopwords that add noise to search.
     * Keep this list small — over-filtering removes meaning.
     */
    private const STOPWORDS = [
        'a', 'an', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to',
        'for', 'of', 'with', 'by', 'from', 'is', 'was', 'are', 'were',
        'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did',
        'will', 'would', 'could', 'should', 'may', 'might', 'can', 'shall',
        'not', 'no', 'nor', 'so', 'yet', 'both', 'either', 'neither',
        'what', 'which', 'who', 'whom', 'whose', 'this', 'that', 'these',
        'those', 'it', 'its', 'i', 'me', 'my', 'we', 'our', 'you', 'your',
        'he', 'she', 'they', 'them', 'their', 'about', 'tell', 'explain',
        'please', 'give', 'me', 'us', 'just', 'how', 'why', 'when', 'where',
    ];

    /**
     * Process a raw user question into structured search data.
     *
     * @param  string $rawQuestion
     * @return array{
     *   original: string,
     *   keywords: array<string>,
     *   fulltext_query: string,
     *   like_query: string
     * }
     */
    public function process(string $rawQuestion): array
    {
        $original = trim($rawQuestion);

        // Step 1: Lowercase everything
        $text = mb_strtolower($original);

        // Step 2: Strip punctuation but keep hyphens (e.g. "socio-economic")
        $text = preg_replace('/[^\w\s\-]/', ' ', $text);

        // Step 3: Normalize whitespace
        $text = preg_replace('/\s+/', ' ', trim($text));

        // Step 4: Tokenize
        $words = explode(' ', $text);

        // Step 5: Remove stopwords and very short words (< 3 chars)
        $keywords = array_filter($words, function (string $word): bool {
            return strlen($word) >= 3
                && !in_array($word, self::STOPWORDS, true);
        });

        $keywords = array_values(array_unique($keywords));

        // Edge case: if ALL words were filtered out, fall back to original words
        if (empty($keywords)) {
            $keywords = array_values(array_unique(
                array_filter($words, fn($w) => strlen($w) >= 2)
            ));
        }

        return [
            'original'       => $original,
            'keywords'       => $keywords,
            'fulltext_query' => $this->buildFulltextQuery($keywords),
            'like_query'     => $keywords[0] ?? $original, // primary keyword for LIKE fallback
        ];
    }

    /**
     * Build a MySQL BOOLEAN MODE query string.
     *
     * Strategy:
     *  - Longer keywords (5+ chars) are REQUIRED (+keyword)
     *  - Shorter keywords are optional (keyword) — they boost score but don't exclude
     *  - Very important: all keywords in a phrase get +prefix
     *
     * Example input:  ['social', 'stratification', 'class', 'weber']
     * Example output: '+stratification +social class +weber'
     */
    private function buildFulltextQuery(array $keywords): string
    {
        if (empty($keywords)) {
            return '';
        }

        $parts = [];

        foreach ($keywords as $keyword) {
            // Escape special BOOLEAN MODE chars to prevent injection / syntax errors
            $safe = $this->escapeFulltext($keyword);

            if (empty($safe)) {
                continue;
            }

            // Require keywords >= 5 chars (more specific = safer to require)
            if (strlen($keyword) >= 5) {
                $parts[] = '+' . $safe;
            } else {
                $parts[] = $safe;
            }
        }

        // If we ended up with only required terms and MySQL returns 0 results,
        // the caller will retry without +. But start strict.
        return implode(' ', $parts);
    }

    /**
     * Sanitize keyword for MySQL FULLTEXT BOOLEAN MODE.
     * Strips characters that have special meaning in boolean mode.
     */
    private function escapeFulltext(string $word): string
    {
        // Remove FULLTEXT boolean operators to prevent injection
        return preg_replace('/[+\-><()\~*"@]/', '', $word);
    }

    /**
     * Build a relaxed (OR-style) fulltext query.
     * Used as fallback when the strict +keyword query returns nothing.
     */
    public function buildRelaxedQuery(array $keywords): string
    {
        return implode(' ', array_map(
            fn($k) => $this->escapeFulltext($k),
            $keywords
        ));
    }
}
