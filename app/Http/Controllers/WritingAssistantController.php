<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WritingAssistantWord;
use App\Models\PersonalDictionary;
use App\Models\CommunitySuggestedWord;
use App\Models\WritingAssistantLearning;
use Illuminate\Support\Facades\Auth;

class WritingAssistantController extends Controller
{
    /**
     * Check a single paragraph/block of text for spelling, punctuation, and style.
     */
    public function checkBlock(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'language' => 'nullable|string|in:ta,en,auto'
        ]);

        $text = $request->input('text');
        
        // Normalize non-breaking spaces (\x{00A0}) and other space variants to standard ASCII spaces
        $text = preg_replace('/[\x{00A0}\x{2007}\x{202F}\x{20AF}]/u', ' ', $text);

        $lang = $request->input('language', 'auto');

        if ($lang === 'auto') {
            $lang = $this->detectLanguage($text);
        }

        $matches = [];
        $userId = Auth::id();

        // 1. Punctuation Checks (Blue underline)
        $punctuationMatches = $this->checkPunctuation($text);
        $matches = array_merge($matches, $punctuationMatches);

        // 2. Writing Coach / Style Checks (Green underline)
        $styleMatches = $this->checkStyle($text);
        $matches = array_merge($matches, $styleMatches);

        // 3. Spell Checking (Red underline)
        $spellingMatches = $this->checkSpelling($text, $lang, $userId);
        $matches = array_merge($matches, $spellingMatches);

        // 4. Grammar / Sandhi Checks (Purple underline)
        if ($lang === 'ta') {
            $grammarMatches = $this->checkGrammar($text);
            $matches = array_merge($matches, $grammarMatches);
        }

        // Deduplicate matches by checking for overlapping ranges
        $uniqueMatches = [];
        // Sort matches by priority: grammar > punctuation > style > spelling > spelling-warning
        $priority = ['grammar' => 1, 'punctuation' => 2, 'style' => 3, 'spelling' => 4, 'spelling-warning' => 5];
        usort($matches, function($a, $b) use ($priority) {
            return $priority[$a['type']] <=> $priority[$b['type']];
        });

        foreach ($matches as $match) {
            $matchStart = $match['offset'];
            $matchEnd = $match['offset'] + $match['length'];
            $overlaps = false;
            
            foreach ($uniqueMatches as $uniqueMatch) {
                $uStart = $uniqueMatch['offset'];
                $uEnd = $uniqueMatch['offset'] + $uniqueMatch['length'];
                
                // Check if ranges overlap
                if ($matchStart < $uEnd && $matchEnd > $uStart) {
                    $overlaps = true;
                    break;
                }
            }
            
            if (!$overlaps) {
                $uniqueMatches[] = $match;
            }
        }

        return response()->json([
            'language' => $lang,
            'matches' => $uniqueMatches
        ]);
    }

    /**
     * Add a word to the user's personal dictionary.
     */
    public function addToDictionary(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'language' => 'required|string|in:ta,en'
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();
        $word = trim($request->input('word'));
        $lang = $request->input('language');

        try {
            PersonalDictionary::updateOrCreate([
                'user_id' => $userId,
                'word' => $word,
                'language' => $lang
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error'], 500);
        }
    }

    /**
     * Nominate a word for community-driven dictionary expansion.
     */
    public function suggestWord(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'language' => 'required|string|in:ta,en'
        ]);

        $word = trim($request->input('word'));
        $lang = $request->input('language');
        $userId = Auth::id();

        try {
            // Update or create with nominations count increment
            $suggestion = CommunitySuggestedWord::where('word', $word)
                ->where('language', $lang)
                ->first();

            if ($suggestion) {
                $suggestion->increment('nominations_count');
            } else {
                CommunitySuggestedWord::create([
                    'word' => $word,
                    'language' => $lang,
                    'user_id' => $userId,
                    'status' => 'pending',
                    'nominations_count' => 1
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error'], 500);
        }
    }

    /**
     * Learn from writer corrections.
     */
    public function learnCorrection(Request $request)
    {
        $request->validate([
            'original_text' => 'required|string|max:255',
            'corrected_text' => 'required|string|max:255',
            'language' => 'required|string|in:ta,en',
            'count' => 'nullable|integer|min:1'
        ]);

        $original = trim($request->input('original_text'));
        $corrected = trim($request->input('corrected_text'));
        $lang = $request->input('language');
        $count = $request->input('count', 1);

        if ($original === $corrected || empty($corrected)) {
            return response()->json(['success' => true]);
        }

        $userId = Auth::id();

        // 1. Linguistic Validation of the corrected text
        $spellingErrors = $this->checkSpelling($corrected, $lang, $userId);
        $grammarErrors = ($lang === 'ta') ? $this->checkGrammar($corrected) : [];
        $puncErrors = $this->checkPunctuation($corrected);

        if (!empty($spellingErrors) || !empty($grammarErrors) || !empty($puncErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Corrected text contains spelling, grammar, or punctuation errors.'
            ], 422);
        }

        // 2. Determine Writer Trust Level
        $trust = 1; // Default/Visitor
        $user = Auth::user();
        if ($user) {
            if ($user->isAdmin() || $user->isEditor()) {
                $trust = 3;
            } elseif ($user->isAuthor()) {
                $trust = 2;
            }
        }

        try {
            $learning = WritingAssistantLearning::where('original_text', $original)
                ->where('corrected_text', $corrected)
                ->where('language', $lang)
                ->first();

            if ($learning) {
                $learning->increment('frequency', $count);
                if ($trust > $learning->writer_trust_level) {
                    $learning->writer_trust_level = $trust;
                    $learning->save();
                }
            } else {
                WritingAssistantLearning::create([
                    'original_text' => $original,
                    'corrected_text' => $corrected,
                    'language' => $lang,
                    'frequency' => $count,
                    'writer_trust_level' => $trust,
                    'approval_status' => 'pending'
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error'], 500);
        }
    }

    /**
     * Analyze document for spelling inconsistencies (Consistency Checker).
     */
    public function analyzeConsistency(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'language' => 'nullable|string|in:ta,en,auto'
        ]);

        $text = $request->input('text');
        $lang = $request->input('language', 'auto');

        if ($lang === 'auto') {
            $lang = $this->detectLanguage($text);
        }

        $userId = Auth::id();
        $cleanedText = $this->getCleanedText($text);

        // Match all words (Tamil/English)
        preg_match_all('/([a-zA-Z\']+|[\x{0B80}-\x{0BFF}\x{200C}\x{200D}]+)/u', $cleanedText, $rawMatches);
        if (!isset($rawMatches[0]) || empty($rawMatches[0])) {
            return response()->json(['inconsistencies' => (object)[]]);
        }

        // Clean, normalize, and count frequencies
        $wordCounts = [];
        foreach ($rawMatches[0] as $word) {
            if (mb_strlen($word, 'UTF-8') <= 2) {
                continue;
            }
            $cleanWord = preg_replace('/[\x{200C}\x{200D}\x{200E}\x{200F}\x{FEFF}]/u', '', $word);
            
            // For English, check lowercase consistency
            if ($lang === 'en') {
                $cleanWord = strtolower($cleanWord);
            }

            if (isset($wordCounts[$cleanWord])) {
                $wordCounts[$cleanWord]++;
            } else {
                $wordCounts[$cleanWord] = 1;
            }
        }

        $uniqueWords = array_keys($wordCounts);
        if (count($uniqueWords) < 2) {
            return response()->json(['inconsistencies' => (object)[]]);
        }

        // Batch fetch dictionary validations for all unique words
        $validMasterWords = WritingAssistantWord::where('language', $lang)
            ->whereIn('word', $uniqueWords)
            ->pluck('word')
            ->toArray();
        $validPersonalWords = [];
        if ($userId) {
            $validPersonalWords = PersonalDictionary::where('user_id', $userId)
                ->where('language', $lang)
                ->whereIn('word', $uniqueWords)
                ->pluck('word')
                ->toArray();
        }
        $allValidWords = array_flip(array_merge($validMasterWords, $validPersonalWords));

        $inconsistencies = [];
        $uniqueCount = count($uniqueWords);

        for ($i = 0; $i < $uniqueCount; $i++) {
            $w1 = $uniqueWords[$i];
            $w1Len = mb_strlen($w1, 'UTF-8');

            for ($j = $i + 1; $j < $uniqueCount; $j++) {
                $w2 = $uniqueWords[$j];
                $w2Len = mb_strlen($w2, 'UTF-8');

                // 1. Length constraint (max difference of 2)
                if (abs($w1Len - $w2Len) > 2) {
                    continue;
                }

                // 2. Check similarity (Tamil homophones or Levenshtein distance <= 2)
                $isHomophoneVariant = false;
                if ($lang === 'ta') {
                    $norm1 = $this->normalizeTamilHomophones($w1);
                    $norm2 = $this->normalizeTamilHomophones($w2);
                    if ($norm1 === $norm2) {
                        $isHomophoneVariant = true;
                    }
                }

                $dist = $this->levenshteinUtf8($w1, $w2);

                if ($isHomophoneVariant || $dist <= 2) {
                    $w1Valid = isset($allValidWords[$w1]);
                    $w2Valid = isset($allValidWords[$w2]);

                    $w1Freq = $wordCounts[$w1];
                    $w2Freq = $wordCounts[$w2];

                    $preferred = null;
                    $incorrect = null;

                    if ($w1Valid && !$w2Valid) {
                        $preferred = $w1;
                        $incorrect = $w2;
                    } elseif ($w2Valid && !$w1Valid) {
                        $preferred = $w2;
                        $incorrect = $w1;
                    } else {
                        // Both valid or both invalid: choose by frequency
                        if ($w1Freq > $w2Freq) {
                            $preferred = $w1;
                            $incorrect = $w2;
                        } elseif ($w2Freq > $w1Freq) {
                            $preferred = $w2;
                            $incorrect = $w1;
                        } else {
                            // Equal frequency: pick alphabetical order fallback
                            if (strcmp($w1, $w2) < 0) {
                                $preferred = $w1;
                                $incorrect = $w2;
                            } else {
                                $preferred = $w2;
                                $incorrect = $w1;
                            }
                        }
                    }

                    if ($preferred && $incorrect) {
                        $message = $lang === 'ta'
                            ? "முரண்பட்ட எழுத்து: இக்கட்டுரையில் '{$incorrect}' மற்றும் '{$preferred}' ஆகிய இரு எழுத்துக்கூட்டல்கள் பயன்படுத்தப்பட்டுள்ளன. '{$preferred}' என்று மாற்றலாமா?"
                            : "Inconsistent spelling: You used both '{$incorrect}' and '{$preferred}' in this article. Would you like to make them consistent with '{$preferred}'?";

                        $inconsistencies[$incorrect] = [
                            'preferred' => $preferred,
                            'message' => $message
                        ];
                    }
                }
            }
        }

        // Overused words analysis
        $overused = [];
        $tamilStopwords = ['மற்றும்', 'இருந்து', 'போது', 'வழியாக', 'எனவே', 'ஆகிய', 'ஒரு', 'இந்த', 'அந்த', 'எந்த', 'அவர்', 'அவர்கள்', 'அது', 'அவை', 'தான்', 'தன்', 'என', 'என்று', 'கொண்டு', 'மூலம்', 'மட்டுமே', 'மேலும்'];
        $englishStopwords = ['the', 'and', 'a', 'of', 'to', 'is', 'in', 'it', 'that', 'this', 'these', 'those', 'for', 'with', 'as', 'was', 'were', 'or', 'by', 'on', 'an', 'be', 'are', 'at'];
        $stopwordsFlip = array_flip(array_merge($tamilStopwords, $englishStopwords));

        foreach ($wordCounts as $word => $count) {
            if ($count >= 10 && !isset($stopwordsFlip[$word])) {
                $message = $lang === 'ta'
                    ? "இக்கட்டுரையில் '{$word}' என்ற சொல் {$count} முறை பயன்படுத்தப்பட்டுள்ளது. மாற்றுச் சொற்களைப் பயன்படுத்தலாமா?"
                    : "You have used the word '{$word}' {$count} times in this article. Consider using synonyms to improve style.";

                $overused[$word] = [
                    'count' => $count,
                    'message' => $message
                ];
            }
        }

        return response()->json([
            'inconsistencies' => (object)$inconsistencies,
            'overused' => (object)$overused
        ]);
    }

    /**
     * Analyze document and return summary metrics for spelling, grammar, style, consistency, etc.
     */
    public function reviewArticle(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'language' => 'nullable|string|in:ta,en,auto'
        ]);

        $text = $request->input('text');
        $lang = $request->input('language', 'auto');
        if ($lang === 'auto') {
            $lang = $this->detectLanguage($text);
        }

        // Split text by newlines into blocks (paragraphs)
        $blocks = preg_split('/\R+/u', $text);
        $allMatches = [];

        foreach ($blocks as $blockText) {
            $blockText = trim($blockText);
            if (empty($blockText)) {
                continue;
            }

            // Spelling matches
            $spellingMatches = $this->checkSpelling($blockText, $lang, \Illuminate\Support\Facades\Auth::id());

            // Style matches
            $styleMatches = $this->checkStyle($blockText);

            // Grammar matches
            $grammarMatches = $this->checkGrammar($blockText);

            // Punctuation matches
            $punctuationMatches = $this->checkPunctuation($blockText);

            $allMatches = array_merge($allMatches, $spellingMatches, $styleMatches, $grammarMatches, $punctuationMatches);
        }

        // Run consistency check on the full text
        $consistencyResponse = $this->analyzeConsistency($request);
        $consistencyData = $consistencyResponse->getData(true);
        $inconsistencies = $consistencyData['inconsistencies'] ?? [];
        $overused = $consistencyData['overused'] ?? [];

        // Count occurrences of consistency inconsistencies
        $consistencyCount = 0;
        foreach (array_keys($inconsistencies) as $word) {
            $consistencyCount += mb_substr_count($text, $word);
        }

        // Count categories
        $spellingCount = 0;
        $unknownCount = 0;
        $grammarCount = 0;
        $styleCount = 0;
        $punctuationCount = 0;
        $repeatedWordsCount = 0;
        $longSentencesCount = 0;

        foreach ($allMatches as $m) {
            if (isset($inconsistencies[$m['text']])) {
                continue;
            }
            if ($m['type'] === 'spelling') {
                $spellingCount++;
            } elseif ($m['type'] === 'spelling-warning') {
                $unknownCount++;
            } elseif ($m['type'] === 'grammar') {
                $grammarCount++;
            } elseif ($m['type'] === 'punctuation') {
                $punctuationCount++;
                $styleCount++;
            } elseif ($m['type'] === 'style') {
                $styleCount++;
                if (isset($m['message']) && (str_contains($m['message'], 'Repeated') || str_contains($m['message'], 'அடுத்தடுத்து'))) {
                    $repeatedWordsCount++;
                } elseif (isset($m['message']) && (str_contains($m['message'], 'words') || str_contains($m['message'], 'சொற்கள்') || str_contains($m['message'], 'Sentence'))) {
                    $longSentencesCount++;
                }
            }
        }

        // Add overuse style warnings count
        foreach ($overused as $word => $meta) {
            $styleCount += mb_substr_count($text, $word);
        }

        // Community safety concerns check
        $safetyCount = 0;
        $inappropriateWords = [
            'inappropriateword1', 'inappropriateword2',
            'தேவையற்றசொல்', 'offensiveword'
        ];
        foreach ($inappropriateWords as $word) {
            if (mb_stripos($text, $word) !== false) {
                $safetyCount += mb_substr_count(mb_strtolower($text), $word);
            }
        }

        // Calculate readability
        $sentences = preg_split('/[.!?]/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = max(1, count($sentences));

        $tokens = preg_split('/\s+/u', trim($text));
        $wordCount = 0;
        foreach ($tokens as $tok) {
            if (preg_match('/[a-zA-Z\x{0B80}-\x{0BFF}]/u', $tok)) {
                $wordCount++;
            }
        }

        $avgSentenceLength = $wordCount / $sentenceCount;
        if ($avgSentenceLength > 22) {
            $readability = 'Difficult (கடினம்)';
        } elseif ($avgSentenceLength > 15) {
            $readability = 'Good (நன்று)';
        } else {
            $readability = 'Easy (எளிமை)';
        }

        return response()->json([
            'summary' => [
                'spelling' => $spellingCount,
                'grammar' => $grammarCount,
                'style' => $styleCount,
                'consistency' => $consistencyCount,
                'unknown' => $unknownCount,
                'readability' => $readability,
                'punctuation' => $punctuationCount,
                'repeated_words' => $repeatedWordsCount,
                'long_sentences' => $longSentencesCount,
                'safety' => $safetyCount,
                'plagiarism' => 0
            ]
        ]);
    }


    /**
     * Simple language detector based on character ranges.
     */
    private function detectLanguage(string $text): string
    {
        // Tamil unicode block is U+0B80 to U+0BFF
        if (preg_match('/[\x{0B80}-\x{0BFF}]/u', $text)) {
            return 'ta';
        }
        return 'en';
    }

    /**
     * Clean text by replacing URLs, emails, hashtags, mentions, code tokens, and numbers
     * with spaces of identical character length. This preserves exact offsets for other words.
     */
    private function getCleanedText(string $text): string
    {
        $cleanText = $text;

        // 1. URLs
        $cleanText = preg_replace_callback('/https?:\/\/\S+/ui', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);

        // 2. Emails
        $cleanText = preg_replace_callback('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/u', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);

        // 3. Hashtags & Mentions
        $cleanText = preg_replace_callback('/[#@][a-zA-Z0-9_\x{0B80}-\x{0BFF}]+/u', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);

        // 4. Numbers/Decimals (must start with a digit to avoid swallowing standalone dots/commas)
        $cleanText = preg_replace_callback('/\b[0-9][0-9.,]*\b/u', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);

        // 5. Code-like tokens (snake_case/kebab-case or camelCase variables)
        $cleanText = preg_replace_callback('/\b[a-zA-Z0-9]+[_-][a-zA-Z0-9_-]*\b/u', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);
        $cleanText = preg_replace_callback('/\b[a-zA-Z0-9]*[a-z]+[A-Z]+[a-zA-Z0-9]*\b/u', function($m) { 
            return str_repeat(' ', mb_strlen($m[0], 'UTF-8')); 
        }, $cleanText);

        return $cleanText;
    }

    /**
     * Punctuation logic rules (Blue Highlights).
     */
    private function checkPunctuation(string $text): array
    {
        $matches = [];
        $cleanedText = $this->getCleanedText($text);

        // Rule A: Repeated punctuation (e.g. ,, or !! or mixed like ?! but not exactly ...)
        preg_match_all('/([.,!?;:]{2,})/u', $cleanedText, $rawMatches, PREG_OFFSET_CAPTURE);
        if (isset($rawMatches[0])) {
            foreach ($rawMatches[0] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($cleanedText, 0, $match[1]), 'UTF-8');
                
                // Allow exactly "..." as ellipsis
                if ($val === '...') {
                    continue;
                }

                $suggest = mb_substr($val, 0, 1);
                if ($val === '?!' || $val === '!?') {
                    $suggest = '?';
                }

                $matches[] = [
                    'type' => 'punctuation',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => [$suggest],
                    'message' => 'தவறான நிறுத்தற்குறி பயன்பாடு (Invalid repeated punctuation).'
                ];
            }
        }

        // Rule B: Missing spaces after punctuation (e.g., வணக்கம்.எப்படி)
        preg_match_all('/([.,!?;:])([a-zA-Z\x{0B80}-\x{0BFF}])/u', $cleanedText, $rawMatches, PREG_OFFSET_CAPTURE);
        if (isset($rawMatches[0])) {
            foreach ($rawMatches[0] as $i => $match) {
                $val = $match[0];
                $punc = $rawMatches[1][$i][0];
                $letter = $rawMatches[2][$i][0];
                $offset = mb_strlen(substr($cleanedText, 0, $match[1]), 'UTF-8');

                $matches[] = [
                    'type' => 'punctuation',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => [$punc . ' ' . $letter],
                    'message' => 'நிறுத்தற்குறிக்குப் பின் இடைவெளி தேவை (Space required after punctuation).'
                ];
            }
        }

        // Rule C: Unmatched brackets
        $brackets = [
            '(' => ')',
            '[' => ']',
            '{' => '}'
        ];

        foreach ($brackets as $open => $close) {
            $openCount = substr_count($cleanedText, $open);
            $closeCount = substr_count($cleanedText, $close);
            if ($openCount !== $closeCount) {
                $matches[] = [
                    'type' => 'punctuation',
                    'text' => $text,
                    'offset' => 0,
                    'length' => mb_strlen($text, 'UTF-8'),
                    'suggestions' => [],
                    'message' => "அடைப்புக்குறி ஒத்திசைவில்லை (Unmatched brackets: '{$open}' and '{$close}')."
                ];
            }
        }

        // Rule D: Space before punctuation (e.g. "கொள்வது  .")
        preg_match_all('/(?<!\s)(\s+)([.,!?;:]+)/u', $cleanedText, $rawMatches, PREG_OFFSET_CAPTURE);
        if (isset($rawMatches[0])) {
            foreach ($rawMatches[0] as $i => $match) {
                $val = $match[0];
                $punc = $rawMatches[2][$i][0];
                $offset = mb_strlen(substr($cleanedText, 0, $match[1]), 'UTF-8');

                $matches[] = [
                    'type' => 'punctuation',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => [$punc],
                    'message' => 'நிறுத்தற்குறிக்கு முன் இடைவெளி தவிர்க்கவும் (Avoid space before punctuation).'
                ];
            }
        }

        return $matches;
    }

    /**
     * Writing style / Coaching rules (Green Highlights).
     */
    private function checkStyle(string $text): array
    {
        $matches = [];

        // Rule A: Repeated words (e.g. 'the the', 'அது அது') - matching multiple consecutive repeats using Unicode-safe boundary checks
        preg_match_all('/(?<![a-zA-Z\x{0B80}-\x{0BFF}])([a-zA-Z\x{0B80}-\x{0BFF}]+)(?:\s+\1)+(?![a-zA-Z\x{0B80}-\x{0BFF}])/ui', $text, $rawMatches, PREG_OFFSET_CAPTURE);
        if (isset($rawMatches[0])) {
            foreach ($rawMatches[0] as $i => $match) {
                $val = $match[0];
                $word = $rawMatches[1][$i][0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');

                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => [$word],
                    'message' => 'அடுத்தடுத்து வரும் ஒரே சொல். தவிர்க்கலாமா? (Repeated word. Consider removing duplicate.)'
                ];
            }
        }

        // Rule B: Multiple consecutive spaces
        preg_match_all('/ {2,}/', $text, $rawMatches, PREG_OFFSET_CAPTURE);
        if (isset($rawMatches[0])) {
            foreach ($rawMatches[0] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');

                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => [' '],
                    'message' => 'கூடுதல் இடைவெளி நீக்குக (Multiple consecutive spaces).'
                ];
            }
        }

        // Rule C: Excessively long sentences (>25 words)
        $sentences = preg_split('/([.!?])\s+/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $currentOffset = 0;
        
        for ($i = 0; $i < count($sentences); $i += 2) {
            $sentenceText = $sentences[$i];
            $punctuation = $sentences[$i+1] ?? '';
            $fullSentence = $sentenceText . $punctuation;
            
            $tokens = preg_split('/\s+/u', trim($sentenceText));
            $wordCount = 0;
            foreach ($tokens as $tok) {
                if (preg_match('/[a-zA-Z\x{0B80}-\x{0BFF}]/u', $tok)) {
                    $wordCount++;
                }
            }

            if ($wordCount > 25) {
                $matches[] = [
                    'type' => 'style',
                    'text' => $fullSentence,
                    'offset' => $currentOffset,
                    'length' => mb_strlen($fullSentence, 'UTF-8'),
                    'suggestions' => [],
                    'message' => "இவ்வாக்கியத்தில் {$wordCount} சொற்கள் உள்ளன. வாசகர்கள் எளிதாகப் புரிந்துகொள்ள இதனைப் பிரித்து எழுதலாமா? (Sentence has {$wordCount} words. Consider splitting.)"
                ];
            }
            
            $currentOffset += mb_strlen($fullSentence, 'UTF-8') + 1;
        }

        // Rule D: Context-aware confusing Tamil words
        // e.g. "நாளம்" -> "நலம்"
        if (preg_match_all('/(?<![a-zA-Z\x{0B80}-\x{0BFF}])(நாளம்)(?![a-zA-Z\x{0B80}-\x{0BFF}])/u', $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[1] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');
                
                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => ['நலம்'],
                    'message' => 'சூழலுக்கு "நலம்" (நல்வாழ்வு/நலம்) என்பதே சரியானது ("நலம்" is more appropriate).'
                ];
            }
        }

        // e.g. "நாளமா" -> "நலமா"
        if (preg_match_all('/(?<![a-zA-Z\x{0B80}-\x{0BFF}])(நாளமா)(?![a-zA-Z\x{0B80}-\x{0BFF}])/u', $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[1] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');
                
                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => ['நலமா'],
                    'message' => 'சூழலுக்கு "நலமா" (சுகமா) என்பதே சரியானது ("நலமா" is more appropriate).'
                ];
            }
        }

        // Rule E: Colloquialisms
        if (preg_match_all('/(?<![a-zA-Z\x{0B80}-\x{0BFF}])(என்னது)(?![a-zA-Z\x{0B80}-\x{0BFF}])/u', $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[1] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');
                
                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => ['என்னுடைய', 'எங்களது'],
                    'message' => 'பேச்சுவழக்குச் சொல். "என்னுடைய" அல்லது "எங்களது" என மாற்றலாம் (Colloquial word).'
                ];
            }
        }

        // Rule F: Contextual spelling for "விடு" -> "வீடு"
        if (preg_match_all('/(எங்கள்|உங்கள்|தங்கள்|என்|உன்|தன்|அவர்|அவர்கள்)\s+(விடு)(?![a-zA-Z\x{0B80}-\x{0BFF}])/u', $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[2] as $match) {
                $val = $match[0];
                $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');
                
                $matches[] = [
                    'type' => 'style',
                    'text' => $val,
                    'offset' => $offset,
                    'length' => mb_strlen($val, 'UTF-8'),
                    'suggestions' => ['வீடு'],
                    'message' => 'சூழலுக்கு "வீடு" என்பதே சரியானது ("வீடு" is appropriate here).'
                ];
            }
        }

        // Rule G: Confusing homophones (கொல்/கொள், கொன்று/கொண்டு, கொன்றது/கொண்டது)
        $confusables = [
            'கொல்வது' => ['suggest' => 'கொள்வது', 'desc' => 'செயல்/உடைமை', 'orig' => 'கொலை செய்தல்'],
            'கொல்வதை' => ['suggest' => 'கொள்வதை', 'desc' => 'செயல்/உடைமை', 'orig' => 'கொலை செய்தல்'],
            'கொல்வதற்கு' => ['suggest' => 'கொள்வதற்கு', 'desc' => 'செயல்/உடைமை', 'orig' => 'கொலை செய்தல்'],
            'கொல்கிறது' => ['suggest' => 'கொள்கிறது', 'desc' => 'செயல்/உடைமை', 'orig' => 'கொலை செய்தல்'],
            'கொன்று' => ['suggest' => 'கொண்டு', 'desc' => 'சேர்த்து/மூலம்', 'orig' => 'கொலை செய்து'],
            'கொன்றது' => ['suggest' => 'கொண்டது', 'desc' => 'பெற்றது/உடையது', 'orig' => 'கொலை செய்தது'],
        ];

        foreach ($confusables as $target => $meta) {
            if (preg_match_all('/(?<![a-zA-Z\x{0B80}-\x{0BFF}])(' . $target . ')(?![a-zA-Z\x{0B80}-\x{0BFF}])/u', $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
                foreach ($rawMatches[1] as $match) {
                    $val = $match[0];
                    $offset = mb_strlen(substr($text, 0, $match[1]), 'UTF-8');
                    
                    $matches[] = [
                        'type' => 'style',
                        'text' => $val,
                        'offset' => $offset,
                        'length' => mb_strlen($val, 'UTF-8'),
                        'suggestions' => [$meta['suggest']],
                        'message' => "சூழலுக்கு \"{$meta['suggest']}\" ({$meta['desc']}) அல்லது \"{$val}\" ({$meta['orig']}) இதில் எது பொருத்தமானது என சரிபார்க்கவும்."
                    ];
                }
            }
        }

        // Rule H: Paragraph word count check (>150 words)
        $paraTokens = preg_split('/\s+/u', trim($text));
        $paraWordCount = 0;
        foreach ($paraTokens as $tok) {
            if (preg_match('/[a-zA-Z\x{0B80}-\x{0BFF}]/u', $tok)) {
                $paraWordCount++;
            }
        }
        if ($paraWordCount > 150) {
            // Match the first word of the paragraph to display the warning cleanly
            if (preg_match('/^([a-zA-Z\x{0B80}-\x{0BFF}]+)/u', trim($text), $firstWordMatch)) {
                $firstWord = $firstWordMatch[1];
                $pos = mb_strpos($text, $firstWord, 0, 'UTF-8');
                if ($pos !== false) {
                    $matches[] = [
                        'type' => 'style',
                        'text' => $firstWord,
                        'offset' => $pos,
                        'length' => mb_strlen($firstWord, 'UTF-8'),
                        'suggestions' => [],
                        'message' => "இப்பத்தியில் {$paraWordCount} சொற்கள் உள்ளன. வாசகர்கள் படிக்க எளிதாக இருக்க இப்பத்தியைப் பிரிக்கலாமா? (Paragraph has {$paraWordCount} words. Readers may find this difficult.)"
                    ];
                }
            }
        }

        return $matches;
    }

    /**
     * Grammar Checking logic (Purple Highlights).
     * Focuses on Tamil Sandhi Rules (வல்லினம் மிகும் இடங்கள்).
     */
    private function checkGrammar(string $text): array
    {
        $matches = [];
        
        // Target 1: Demonstratives, Interrogatives, etc. + க, ச, த, ப
        // e.g., இப்படி, அப்படி, எப்படி, அந்த, இந்த, எந்த, அங்கு, இங்கு, எங்கு, ஆக, என
        $sandhiKeywords = ['இப்படி', 'அப்படி', 'எப்படி', 'அந்த', 'இந்த', 'எந்த', 'அங்கு', 'இங்கு', 'எங்கு', 'என'];
        
        foreach ($sandhiKeywords as $keyword) {
            // Match keyword followed by space and a word starting with க, ச, த, ப
            $pattern = '/(?<![a-zA-Z\x{0B80}-\x{0BFF}])(' . $keyword . ')\s+((க|ச|த|ப)[a-zA-Z\x{0B80}-\x{0BFF}]*)/u';
            
            if (preg_match_all($pattern, $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
                foreach ($rawMatches[1] as $i => $match) {
                    $word1 = $match[0];
                    $word2 = $rawMatches[2][$i][0];
                    $fullText = $rawMatches[0][$i][0]; // "இப்படி பொதுவெளியில்"
                    $offset = mb_strlen(substr($text, 0, $rawMatches[0][$i][1]), 'UTF-8');
                    $nextWordStartChar = $rawMatches[3][$i][0];
                    
                    // Determine which consonant to add
                    $consonantMap = ['க' => 'க்', 'ச' => 'ச்', 'த' => 'த்', 'ப' => 'ப்'];
                    $consonant = $consonantMap[$nextWordStartChar];
                    
                    $suggestion = $word1 . $consonant . ' ' . $word2;
                    
                    $matches[] = [
                        'type' => 'grammar',
                        'text' => $fullText,
                        'offset' => $offset,
                        'length' => mb_strlen($fullText, 'UTF-8'),
                        'suggestions' => [$suggestion],
                        'message' => "சந்திப் பிழை: '{$word1}' என்பதற்குப் பின் வல்லினம் ({$consonant}) மிகும்."
                    ];
                }
            }
        }
        
        // Target 2: Words ending in 'ஆக' sound (usually written as 'ாக' with vowel modifier) + க, ச, த, ப
        // 'ா' (U+0BBE) + 'க' (U+0B95)
        $patternAaga = '/([a-zA-Z\x{0B80}-\x{0BFF}]+ாக)\s+((க|ச|த|ப)[a-zA-Z\x{0B80}-\x{0BFF}]*)/u';
        if (preg_match_all($patternAaga, $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[1] as $i => $match) {
                $word1 = $match[0];
                $word2 = $rawMatches[2][$i][0];
                $fullText = $rawMatches[0][$i][0];
                $offset = mb_strlen(substr($text, 0, $rawMatches[0][$i][1]), 'UTF-8');
                $nextWordStartChar = $rawMatches[3][$i][0];
                
                $consonantMap = ['க' => 'க்', 'ச' => 'ச்', 'த' => 'த்', 'ப' => 'ப்'];
                $consonant = $consonantMap[$nextWordStartChar];
                
                $suggestion = $word1 . $consonant . ' ' . $word2;
                
                $matches[] = [
                    'type' => 'grammar',
                    'text' => $fullText,
                    'offset' => $offset,
                    'length' => mb_strlen($fullText, 'UTF-8'),
                    'suggestions' => [$suggestion],
                    'message' => "சந்திப் பிழை: 'ஆக' என முடியும் சொல்லுக்குப் பின் வல்லினம் ({$consonant}) மிகும்."
                ];
            }
        }
        
        // Target 3: Words ending in 'ஐ' (Second case) + க, ச, த, ப
        // Match words explicitly ending with the 'ஐ' character (U+0B90)
        // Note: Many words have 'ஐ' sound but end in vowel-consonants (like 'யை', 'லை').
        // To be safe and precise, we match the common ones using 'ஐ' or 'யை'.
        $patternAi = '/([a-zA-Z\x{0B80}-\x{0BFF}]+(ஐ|யை))\s+((க|ச|த|ப)[a-zA-Z\x{0B80}-\x{0BFF}]*)/u';
        if (preg_match_all($patternAi, $text, $rawMatches, PREG_OFFSET_CAPTURE)) {
            foreach ($rawMatches[1] as $i => $match) {
                $word1 = $match[0];
                // Ignore if it's already a single letter or just 'ஐ'
                if (mb_strlen($word1, 'UTF-8') <= 2) continue;
                
                $word2 = $rawMatches[3][$i][0];
                $fullText = $rawMatches[0][$i][0];
                $offset = mb_strlen(substr($text, 0, $rawMatches[0][$i][1]), 'UTF-8');
                $nextWordStartChar = $rawMatches[4][$i][0];
                
                $consonantMap = ['க' => 'க்', 'ச' => 'ச்', 'த' => 'த்', 'ப' => 'ப்'];
                $consonant = $consonantMap[$nextWordStartChar];
                
                $suggestion = $word1 . $consonant . ' ' . $word2;
                
                // Exclude words that are typically not case markers if needed, but 'யை' is heavily used as one.
                $matches[] = [
                    'type' => 'grammar',
                    'text' => $fullText,
                    'offset' => $offset,
                    'length' => mb_strlen($fullText, 'UTF-8'),
                    'suggestions' => [$suggestion],
                    'message' => "சந்திப் பிழை: இரண்டாம் வேற்றுமை விரிக்குப் பின் வல்லினம் ({$consonant}) மிகும்."
                ];
            }
        }

        return $matches;
    }

    /**
     * Spell Checking logic (Red Highlights).
     */
    private function checkSpelling(string $text, string $lang, ?int $userId): array
    {
        $matches = [];
        $cleanedText = $this->getCleanedText($text);

        // Match all words in the cleaned text (excluding symbols/spaces, including ZWNJ/ZWJ)
        preg_match_all('/([a-zA-Z\']+|[\x{0B80}-\x{0BFF}\x{200C}\x{200D}]+)/u', $cleanedText, $rawMatches, PREG_OFFSET_CAPTURE);
        if (!isset($rawMatches[0])) {
            return [];
        }

        $candidatesToCheck = [];
        foreach ($rawMatches[0] as $match) {
            $word = $match[0];
            $byteOffset = $match[1];
            $charOffset = mb_strlen(substr($cleanedText, 0, $byteOffset), 'UTF-8');

            // Exclude single letter words
            if (mb_strlen($word, 'UTF-8') <= 1) {
                continue;
            }

            // Strip ZWNJ/ZWJ and invisible control characters
            $cleanWord = preg_replace('/[\x{200C}\x{200D}\x{200E}\x{200F}\x{FEFF}]/u', '', $word);

            $candidatesToCheck[] = [
                'word' => $word,
                'clean_word' => $cleanWord,
                'offset' => $charOffset,
                'length' => mb_strlen($word, 'UTF-8')
            ];
        }

        if (empty($candidatesToCheck)) {
            return [];
        }

        $cleanWordsList = array_map(fn($c) => $c['clean_word'], $candidatesToCheck);
        
        $validMasterWords = WritingAssistantWord::where('language', $lang)
            ->whereIn('word', $cleanWordsList)
            ->pluck('word')
            ->toArray();

        $validPersonalWords = [];
        if ($userId) {
            $validPersonalWords = PersonalDictionary::where('user_id', $userId)
                ->where('language', $lang)
                ->whereIn('word', $cleanWordsList)
                ->pluck('word')
                ->toArray();
        }

        $allValidWords = array_flip(array_merge($validMasterWords, $validPersonalWords));
        $frequencies = array_count_values($cleanWordsList);

        foreach ($candidatesToCheck as $cand) {
            $word = $cand['word'];
            $cleanWord = $cand['clean_word'];
            
            if ($lang === 'en') {
                $variants = [$cleanWord, strtolower($cleanWord), ucfirst(strtolower($cleanWord))];
                $isValid = false;
                foreach ($variants as $v) {
                    if (isset($allValidWords[$v])) {
                        $isValid = true;
                        break;
                    }
                }
                if ($isValid) continue;
            } else {
                if (isset($allValidWords[$cleanWord])) {
                    continue;
                }
            }

            // Start base confidence at 100
            $confidence = 100;
            $len = mb_strlen($cleanWord, 'UTF-8');

            if ($len > 10) {
                $confidence -= 25;
            }

            // Expanded Suffix check
            $hasMorphology = false;
            if ($lang === 'ta') {
                if ($this->checkTamilCompoundWord($cleanWord, $allValidWords) || $this->checkTamilVerbInflection($cleanWord, $allValidWords)) {
                    $hasMorphology = true;
                }
                
                // Add common Tamil suffixes like கள், உடன், ஓடு, இன், க்கு
                $commonSuffixes = ['கள்', 'களுக்கு', 'உடன்', 'ஓடு', 'இன்', 'க்கு', 'ஐ', 'ஆல்', 'ஐயும்', 'கிறோம்', 'கிறார்கள்', 'கிறார்'];
                foreach ($commonSuffixes as $suffix) {
                    $sLen = mb_strlen($suffix, 'UTF-8');
                    if ($len > $sLen + 2 && mb_substr($cleanWord, -$sLen, null, 'UTF-8') === $suffix) {
                        $stripped = mb_substr($cleanWord, 0, -$sLen, 'UTF-8');
                        if (isset($allValidWords[$stripped]) || $this->isWordInDictionary($stripped, $allValidWords)) {
                            $hasMorphology = true;
                        } else {
                            // even if root not known, having a common suffix drops confidence
                            $confidence -= 25; 
                        }
                        break;
                    }
                }
            }
            
            if ($hasMorphology) {
                // If it explicitly matches a valid morphological derivation, it's valid.
                continue;
            }

            $suggestionsData = $this->getSuggestions($cleanWord, $lang, $userId);
            $suggestions = $suggestionsData['words'];
            $bestDistance = $suggestionsData['best_distance'];

            if (empty($suggestions)) {
                $confidence -= 50; // no close suggestions -> probably valid noun/slang
            } else {
                if ($bestDistance == 2) $confidence -= 10;
                if ($bestDistance >= 3) $confidence -= 20;
            }

            if ($confidence < 80) {
                // Ignore it
                continue;
            }

            $type = ($confidence < 95) ? 'spelling-warning' : 'spelling';
            $message = ($confidence < 95) ? 'சந்தேகத்திற்குரிய எழுத்துப்பிழை (Low confidence typo).' : 'சந்தேகத்திற்குரிய எழுத்துப்பிழை (Suspected spelling mistake).';

            $matches[] = [
                'type' => $type,
                'text' => $word,
                'offset' => $cand['offset'],
                'length' => $cand['length'],
                'suggestions' => $suggestions,
                'message' => $message
            ];
        }

        return $matches;
    }

    /**
     * Heuristics for conjoined words (Sandhi) in Tamil.
     */
    private function checkTamilCompoundWord(string $word, array $allValidWords): bool
    {
        $suffixes = [
            'கொள்வது', 'கொள்கிறது', 'கொண்டது', 'கொண்டு', 'கொள்',
            'படுவது', 'பட்டது', 'பட்டு', 'படு',
            'உள்ளது', 'உள்ள', 'உள',
            'வேண்டும்', 'வேண்டாம்',
            'விடுகிறது', 'விட்டது', 'விட்டு',
            'போது', 'பொழுது',
            'இல்லை', 'இல்லாத',
            'ஆகும்', 'ஆன', 'ஆகி', 'ஆக'
        ];

        foreach ($suffixes as $suffix) {
            $suffixLen = mb_strlen($suffix, 'UTF-8');
            if (mb_substr($word, -$suffixLen, null, 'UTF-8') === $suffix) {
                $root = mb_substr($word, 0, -$suffixLen, 'UTF-8');
                
                if (empty($root)) {
                    continue;
                }

                if ($this->isWordInDictionary($root, $allValidWords)) {
                    return true;
                }

                $sandhiConsonants = ['க்', 'ச்', 'த்', 'ப்', 'வ்', 'ய்'];
                foreach ($sandhiConsonants as $sc) {
                    $scLen = mb_strlen($sc, 'UTF-8');
                    if (mb_substr($root, -$scLen, null, 'UTF-8') === $sc) {
                        $strippedRoot = mb_substr($root, 0, -$scLen, 'UTF-8');
                        if (!empty($strippedRoot) && $this->isWordInDictionary($strippedRoot, $allValidWords)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check for Tamil verb inflections and auxiliary words.
     */
    private function checkTamilVerbInflection(string $word, array $allValidWords): bool
    {
        // 1. Verbal nouns ending in வது, ப்பது, பது
        $verbalNounEndings = ['வது', 'ப்பது', 'பது'];
        foreach ($verbalNounEndings as $ending) {
            $endingLen = mb_strlen($ending, 'UTF-8');
            if (mb_substr($word, -$endingLen, null, 'UTF-8') === $ending) {
                $root = mb_substr($word, 0, -$endingLen, 'UTF-8');
                if (!empty($root) && $this->isWordInDictionary($root, $allValidWords)) {
                    return true;
                }
                
                // Try stripping sandhi consonant if present (e.g. க், ச், த், ப்)
                $sandhiConsonants = ['க்', 'ச்', 'த்', 'ப்'];
                foreach ($sandhiConsonants as $sc) {
                    $scLen = mb_strlen($sc, 'UTF-8');
                    if (mb_substr($root, -$scLen, null, 'UTF-8') === $sc) {
                        $strippedRoot = mb_substr($root, 0, -$scLen, 'UTF-8');
                        if (!empty($strippedRoot) && $this->isWordInDictionary($strippedRoot, $allValidWords)) {
                            return true;
                        }
                    }
                }
            }
        }

        // 2. Auxiliary verb roots/forms that are valid standalone words
        $auxiliaryWords = [
            'கொள்வது', 'கொள்கிறது', 'கொண்டது', 'கொண்டு', 'கொள்',
            'படுவது', 'பட்டது', 'பட்டு', 'படு',
            'உள்ளது', 'உள்ள', 'உள',
            'வேண்டும்', 'வேண்டாம்',
            'விடுகிறது', 'விட்டது', 'விட்டு',
            'போது', 'பொழுது',
            'இல்லை', 'இல்லாத',
            'ஆகும்', 'ஆன', 'ஆகி', 'ஆக'
        ];
        if (in_array($word, $auxiliaryWords)) {
            return true;
        }

        return false;
    }

    /**
     * Normalize Tamil homophone (மயங்கொலி) letters to representative letters.
     */
    private function normalizeTamilHomophones(string $word): string
    {
        $word = str_replace(['ண', 'ந', 'ண்', 'ந்'], 'ன', $word);
        $word = str_replace(['ற', 'ற்'], 'ர', $word);
        $word = str_replace(['ள', 'ழ', 'ள்', 'ழ்'], 'ல', $word);
        return $word;
    }

    /**
     * Check if a word is in dictionary or database directly.
     */
    private function isWordInDictionary(string $word, array $allValidWords): bool
    {
        if (isset($allValidWords[$word])) {
            return true;
        }
        return WritingAssistantWord::where('language', 'ta')->where('word', $word)->exists();
    }

    /**
     * Get suggestions using length-based filtering and UTF-8 Levenshtein.
     * Returns an array with 'words' and 'best_distance'
     */
    private function getSuggestions(string $word, string $lang, ?int $userId): array
    {
        $len = mb_strlen($word, 'UTF-8');
        $first = mb_substr($word, 0, 1, 'UTF-8');

        // Query master words starting with same prefix
        $candidates = WritingAssistantWord::where('language', $lang)
            ->where('word', 'like', $first . '%')
            ->pluck('word')
            ->toArray();

        if ($userId) {
            $personalCandidates = PersonalDictionary::where('user_id', $userId)
                ->where('language', $lang)
                ->where('word', 'like', $first . '%')
                ->pluck('word')
                ->toArray();
            $candidates = array_merge($candidates, $personalCandidates);
        }

        $suggestions = [];
        $maxDistance = ($len > 6) ? 3 : 2;
        $bestDistance = 999;

        foreach ($candidates as $candidate) {
            $candLen = mb_strlen($candidate, 'UTF-8');
            // Filter by length difference
            if (abs($candLen - $len) > 2) {
                continue;
            }

            $dist = $this->levenshteinUtf8($word, $candidate);
            if ($dist <= $maxDistance) {
                if ($dist < $bestDistance) {
                    $bestDistance = $dist;
                }

                // Secondary sorting key: length difference
                $weight = ($dist * 10) + abs($candLen - $len);
                
                // Homophone normalization bonus for Tamil
                if ($lang === 'ta') {
                    $normWord = $this->normalizeTamilHomophones($word);
                    $normCand = $this->normalizeTamilHomophones($candidate);
                    if ($normWord === $normCand) {
                        $weight -= 5;
                    }
                }
                
                $suggestions[$candidate] = $weight;
            }
        }

        // Sort by computed weight (ascending)
        asort($suggestions);
        
        // Return up to 6 suggestions to give better options coverage
        return [
            'words' => array_slice(array_keys($suggestions), 0, 6),
            'best_distance' => $bestDistance
        ];
    }

    /**
     * Custom Unicode-safe Levenshtein distance.
     */
    private function levenshteinUtf8(string $s1, string $s2): int
    {
        $char1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $char2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);
        $l1 = count($char1);
        $l2 = count($char2);
        
        if ($l1 === 0) return $l2;
        if ($l2 === 0) return $l1;

        $dp = range(0, $l2);
        
        for ($i = 0; $i < $l1; $i++) {
            $prev = $i + 1;
            for ($j = 0; $j < $l2; $j++) {
                $val = ($char1[$i] === $char2[$j]) ? $dp[$j] : min($dp[$j] + 1, $dp[$j+1] + 1, $prev + 1);
                $dp[$j] = $prev;
                $prev = $val;
            }
            $dp[$l2] = $prev;
        }

        return $dp[$l2];
    }
}
