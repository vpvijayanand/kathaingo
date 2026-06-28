<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class ContentProcessorService
{
    /**
     * Format, check spelling, and correct grammar of the post content.
     *
     * @param string $content
     * @return string
     */
    public function process(string $content): string
    {
        if (empty(trim($content))) {
            return $content;
        }

        // 1. Ensure the content is wrapped in HTML paragraphs if it's plain text
        $content = $this->ensureHtmlParagraphs($content);

        // 2. Load into DOM to safely edit text nodes without breaking HTML tags or attributes
        $content = $this->cleanHtmlText($content);

        // 3. Optional: Run English grammar and spell checker for English sentences
        $content = $this->runLanguageToolCheck($content);

        return $content;
    }

    /**
     * Wrap plain text newlines into proper HTML paragraphs.
     */
    private function ensureHtmlParagraphs(string $content): string
    {
        // If it doesn't look like HTML (no tag elements), wrap paragraphs
        if (strip_tags($content) === $content) {
            $paragraphs = preg_split('/\n+/', $content);
            $formatted = '';
            foreach ($paragraphs as $para) {
                $trimmed = trim($para);
                if ($trimmed !== '') {
                    $formatted .= '<p>' . e($trimmed) . "</p>\n";
                }
            }
            return $formatted;
        }
        return $content;
    }

    /**
     * Parse HTML and apply spacing/punctuation corrections safely to text nodes.
     */
    private function cleanHtmlText(string $html): string
    {
        if (empty(trim($html))) {
            return $html;
        }

        // Use DOMDocument to load the HTML with UTF-8 encoding
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        
        // Wrap in a parent div to ensure DOMDocument parses it as a single node tree
        $wrappedHtml = '<div>' . $html . '</div>';
        
        // Use loadHTML with UTF-8 encoding declaration to parse HTML safely.
        // We use LIBXML_HTML_NOIMPLIED and LIBXML_HTML_NODEFDTD to avoid automatic doctype/body tags.
        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        if (!$loaded || !$dom->documentElement) {
            libxml_clear_errors();
            return $html; // Fallback to original content to prevent data loss on failure
        }
        
        $xpath = new DOMXPath($dom);

        // Clean attributes (styles, classes, lang) from all elements to remove MS Word junk and color overrides
        $elements = $xpath->query('//*');
        foreach ($elements as $element) {
            if ($element->hasAttribute('class')) {
                $class = $element->getAttribute('class');
                if (strtolower($element->tagName) !== 'img' && (preg_match('/^Mso/i', $class) || empty(trim($class)))) {
                    $element->removeAttribute('class');
                }
            }

            if ($element->hasAttribute('style')) {
                $style = $element->getAttribute('style');
                if (strtolower($element->tagName) === 'img') {
                    $allowedStyles = [];
                    $styleParts = explode(';', $style);
                    foreach ($styleParts as $part) {
                        $part = trim($part);
                        if (empty($part)) continue;
                        $propVal = explode(':', $part, 2);
                        if (count($propVal) === 2) {
                            $prop = strtolower(trim($propVal[0]));
                            $val = trim($propVal[1]);
                            if (in_array($prop, ['float', 'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom', 'max-width', 'display', 'width', 'height'])) {
                                $allowedStyles[] = "$prop: $val";
                            }
                        }
                    }
                    if (!empty($allowedStyles)) {
                        $element->setAttribute('style', implode('; ', $allowedStyles) . ';');
                    } else {
                        $element->removeAttribute('style');
                    }
                } else {
                    if (preg_match('/text-align\s*:\s*([^;]+)/i', $style, $matches)) {
                        $alignVal = strtolower(trim($matches[1]));
                        if ($alignVal !== 'justify') {
                            $element->setAttribute('style', 'text-align: ' . $alignVal . ';');
                        } else {
                            $element->removeAttribute('style');
                        }
                    } else {
                        $element->removeAttribute('style');
                    }
                }
            }

            if ($element->hasAttribute('align')) {
                $alignAttr = strtolower(trim($element->getAttribute('align')));
                if ($alignAttr === 'justify') {
                    $element->removeAttribute('align');
                }
            }

            if ($element->hasAttribute('lang')) {
                $element->removeAttribute('lang');
            }

            if (strtolower($element->tagName) === 'font') {
                $element->removeAttribute('face');
                $element->removeAttribute('size');
                $element->removeAttribute('color');
            }
        }

        $textNodes = $xpath->query('//text()');

        foreach ($textNodes as $node) {
            // Get original text
            $text = $node->nodeValue;

            // Apply standard typographical cleanups
            // A. Collapse duplicate spaces and tabs
            $text = preg_replace('/[ \t]+/u', ' ', $text);

            // B. Remove space before punctuation marks: "hello ." -> "hello."
            $text = preg_replace('/\s+([\.,!\?\:;])/u', '$1', $text);

            // C. Add space after punctuation marks: "hello,world" -> "hello, world"
            // Ignore decimal numbers (e.g. 3.14) and multiple dots/question marks (e.g. !!!, ..., ???)
            $text = preg_replace('/(?<!\d)([\.,!\?\:;])(?!\s|\d|[\.,!\?\:;]|$)/u', '$1 ', $text);

            // D. Clean up common double space or multiple spacing issues
            $text = preg_replace('/  +/u', ' ', $text);

            // Set modified text value back
            $node->nodeValue = $text;
        }

        // Save and extract the HTML content inside the wrapper div
        $outputHtml = $dom->saveHTML($dom->documentElement);
        
        libxml_clear_errors();

        // Strip <div> and </div> tags
        if (strpos($outputHtml, '<div>') === 0) {
            $outputHtml = substr($outputHtml, 5, -6);
        }

        // Clean up empty paragraphs or double paragraph tags that might occur
        $outputHtml = preg_replace('/<p>\s*<\/p>/u', '', $outputHtml);
        $outputHtml = preg_replace('/<p>&nbsp;<\/p>/u', '', $outputHtml);
        
        // Replace three or more consecutive <br> tags with a max of two <br> tags to prevent excessive spacing
        $outputHtml = preg_replace('/(<br\s*\/?>\s*){3,}/iu', '<br><br>', $outputHtml);

        return trim($outputHtml);
    }

    /**
     * Query LanguageTool API to check and clean up English portions of text.
     */
    private function runLanguageToolCheck(string $html): string
    {
        // Skip API request if there is no English text or if offline
        // Simple regex to check for substantial English text blocks
        if (!preg_match('/[a-zA-Z]{5,}/', strip_tags($html))) {
            return $html;
        }

        try {
            // Load text to check
            $text = strip_tags($html);
            if (strlen($text) < 5) {
                return $html;
            }

            // Call LanguageTool free API
            $response = Http::asForm()->timeout(5)->post('https://api.languagetool.org/v2/check', [
                'text' => $text,
                'language' => 'en-US',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $matches = $result['matches'] ?? [];

                // Sort matches from end of text to start to prevent offset displacement during replacements
                usort($matches, function ($a, $b) {
                    return $b['offset'] <=> $a['offset'];
                });

                $cleanedText = $text;

                foreach ($matches as $match) {
                    $offset = $match['offset'];
                    $length = $match['length'];
                    $replacements = $match['replacements'] ?? [];

                    // Apply if there is a high-confidence single replacement suggestion
                    if (count($replacements) > 0 && isset($replacements[0]['value'])) {
                        $suggestion = $replacements[0]['value'];
                        // Only auto-replace if it's a spelling issue or highly recommended (e.g. short edits)
                        if (strlen($match['message']) < 150) {
                            $cleanedText = substr_replace($cleanedText, $suggestion, $offset, $length);
                        }
                    }
                }

                // If corrections were made, let's map them back to the HTML paragraphs.
                // Because mapped text replacements in HTML can be complex, if we did corrections, 
                // we can safely apply them inside the text blocks.
                // For safety, we will do a direct word-for-word check in HTML for spelling errors.
                foreach ($matches as $match) {
                    $originalWord = substr($text, $match['offset'], $match['length']);
                    $replacements = $match['replacements'] ?? [];
                    if (count($replacements) > 0 && isset($replacements[0]['value'])) {
                        $newWord = $replacements[0]['value'];
                        // Only replace exact word matches in HTML text blocks to prevent HTML tag corruption
                        if (preg_match('/^[a-zA-Z\']+$/', $originalWord) && preg_match('/^[a-zA-Z\'\s]+$/', $newWord)) {
                            // Escape original word regex to be safe
                            $escapedWord = preg_quote($originalWord, '/');
                            // Replace only outside HTML tags
                            $html = preg_replace('/>([^<]*)\b' . $escapedWord . '\b([^<]*)</u', '>$1' . $newWord . '$2<', $html);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fallback if API is rate-limited or offline
        }

        return $html;
    }
}
