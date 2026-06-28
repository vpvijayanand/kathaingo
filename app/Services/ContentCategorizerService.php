<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use App\Models\MetadataType;
use App\Models\MetadataValue;

class ContentCategorizerService
{
    /**
     * Categorize a post based on its title and content.
     *
     * @param string $title
     * @param string $content
     * @return array
     */
    public function categorize(string $title, string $content): array
    {
        $text = mb_strtolower($title . ' ' . strip_tags($content));

        // 1. Determine Main Category (பதிவுகள் / நல்லசினிமா vs பயணங்கள்)
        $travelKeywords = [
            'பயணம்', 'டூர்', 'விசிட்', 'ஊர்', 'பயணங்கள்', 'மலை', 'காடு', 'கடற்கரை', 'மலைவாழிடம்', 
            'சுற்றுலா', 'சுற்றுலாத் தலம்', 'விமானம்', 'ரயில்', 'travel', 'trip', 'tour', 'visit', 
            'journey', 'explore', 'flight', 'train', 'waterfall', 'temple', 'ஹோட்டல்', 'hotel', 'அனுபவம்'
        ];

        $cinemaKeywords = [
            'படம்', 'சினிமா', 'பராசக்தி', 'திரைப்படம்', 'திரை விமர்சனம்', 'இயக்குநர்', 'நடிகர்', 'நடிகை', 
            'பாக்ஸ் ஆபீஸ்', 'திரையரங்கம்', 'ஓடிடி', 'cinema', 'movie', 'film', 'director', 'actor', 
            'actress', 'review', 'ott', 'theater', 'trailer', 'கதை', 'திரைக்கதை', 'வசனம்', 'பாடல்', 
            'இசை', 'மியூசிக்', 'பாடல் வரிகள்', 'கதாபாத்திரம்'
        ];

        $travelScore = $this->calculateScore($text, $travelKeywords);
        $cinemaScore = $this->calculateScore($text, $cinemaKeywords);

        // If travel score is higher, classify under பயணக் கதைகள்/payanangal subcategory of பதிவுகள்
        if ($travelScore > $cinemaScore) {
            $postsCat = Category::where('slug', 'pathivugal')->first();
            $travelSub = Subcategory::whereIn('slug', ['பயணக்-கதைகள்', 'payanangal'])->first();

            // Determine Child Category (தனிப் பயணம், பயண அனுபவம், சாகசங்கள், சுற்றுலா)
            $childKeywords = [
                'தனிப்-பயணம்' => ['தனிப் பயணம்', 'தனிப்பயணம்', 'தனித்துப் பயணம்', 'ஒற்றைப்பயணம்', 'solo travel', 'solo trip', 'backpacking'],
                'பயண-அனுபவம்' => ['பயண அனுபவம்', 'பயணக் குறிப்பு', 'பயணக்குறிப்புகள்', 'அனுபவங்கள்', 'travel experience', 'travelogue', 'my travel', 'my trip'],
                'சாகசங்கள்' => ['சாகசம்', 'சாகசங்கள்', 'மலையேற்றம்', 'trekking', 'hiking', 'climbing', 'camping', 'adventure', 'adventures'],
                'சுற்றுலா' => ['சுற்றுலா', 'சுற்றுலாத்தலம்', 'tourist', 'tourism', 'sightseeing'],
            ];

            $highestChild = 'சுற்றுலா';
            $highestChildScore = -1;

            foreach ($childKeywords as $slug => $keywords) {
                $score = $this->calculateScore($text, $keywords);
                if ($score > $highestChildScore) {
                    $highestChildScore = $score;
                    $highestChild = $slug;
                }
            }

            $childCat = ChildCategory::where('subcategory_id', $travelSub?->id)
                ->where('slug', $highestChild)
                ->first();

            if (!$childCat) {
                $childCat = ChildCategory::where('subcategory_id', $travelSub?->id)
                    ->where('slug', 'நாடு')
                    ->first();
            }

            // Fallback: if highest child doesn't exist, default to the first child category under this subcategory
            if (!$childCat && $travelSub) {
                $childCat = ChildCategory::where('subcategory_id', $travelSub->id)->orderBy('order')->first();
            }

            return [
                'category_id' => $postsCat ? $postsCat->id : null,
                'subcategory_id' => $travelSub ? $travelSub->id : null,
                'child_category_id' => $childCat ? $childCat->id : null,
                'grandchild_category_id' => null,
            ];
        }

        // Default to Cinema (பதிவுகள் / நல்லசினிமா)
        $cinemaCat = Category::where('slug', 'pathivugal')->first();
        $cinemaSub = Subcategory::where('slug', 'நல்லசினிமா')->first();

        // 2. Determine Child Category (தமிழ், ஆங்கிலம், ஆவணப் படம்)
        $tamilKeywords = ['தமிழ்', 'தமிழ்நாடு', 'கோலிவுட்', 'kollywood', 'tamil'];
        $englishKeywords = ['ஆங்கிலம்', 'ஹாலிவுட்', 'hollywood', 'english'];
        $documentaryKeywords = ['ஆவணப் படம்', 'ஆவணப்படம்', 'டாகுமெண்டரி', 'documentary'];

        $tamilScore = $this->calculateScore($text, $tamilKeywords);
        $englishScore = $this->calculateScore($text, $englishKeywords);
        $docScore = $this->calculateScore($text, $documentaryKeywords);

        $childSlug = 'தமிழ்';
        if ($englishScore > $tamilScore && $englishScore > $docScore) {
            $childSlug = 'ஆங்கிலம்';
        } elseif ($docScore > $tamilScore && $docScore > $englishScore) {
            $childSlug = 'ஆவணப்-படம்';
        }

        $childCat = ChildCategory::where('slug', $childSlug)->first();

        // 3. Determine Genre (Grandchild Category)
        $genreKeywords = [
            'family_drama' => ['குடும்ப', 'அம்மா', 'அப்பா', 'பாசம்', 'உறவு', 'நாடகம்', 'family', 'drama', 'sentiment', 'relationships'],
            'love' => ['காதல்', 'காதலி', 'காதலன்', 'லவ்', 'romance', 'love', 'romantic'],
            'fight' => ['சண்டை', 'அடிதடி', 'ஆக்சன்', 'action', 'fight', 'stunt', 'thriller'],
            'mystery' => ['மர்மம்', 'சஸ்பென்ஸ்', 'திரில்லர்', 'கொலை', 'புதிரான', 'mystery', 'thriller', 'suspense', 'murder', 'investigation'],
            'horror' => ['அமானுஷ்யம்', 'பேய்', 'பயம்', 'ஹாரர்', 'horror', 'ghost', 'supernatural', 'paranormal'],
            'sports' => ['விளையாட்டு', 'கிரிக்கெட்', 'கால்பந்து', 'கபடி', 'ஸ்போர்ட்ஸ்', 'sports', 'game', 'cricket', 'football', 'athlete'],
            'inspiration' => ['உத்வேகம்', 'முயற்சி', 'வெற்றி', 'நம்பிக்கை', 'inspirational', 'motivational', 'inspiration', 'success'],
            'history' => ['வரலாறு', 'வரலாற்று', 'ராஜா', 'அரசர்', 'கோட்டை', 'பண்டைய', 'history', 'historical', 'king', 'empire', 'ancient'],
            'personalities' => ['ஆளுமை', 'தலைவர்', 'நடிகர்', 'வாழ்க்கை வரலாறு', 'personalities', 'biography', 'celebrity', 'leader'],
            'true_stories' => ['உண்மைச் சம்பவம்', 'உண்மைக் கதை', 'நிஜக் கதை', 'true story', 'real events', 'based on true'],
            'cases' => ['வழக்கு', 'நீதிமன்றம்', 'பொலிஸ்', 'கைது', 'சட்டம்', 'court', 'police', 'arrest', 'law', 'case', 'investigation']
        ];

        $highestGenre = 'family_drama';
        $highestScore = -1;

        foreach ($genreKeywords as $genre => $keywords) {
            $score = $this->calculateScore($text, $keywords);
            if ($score > $highestScore) {
                $highestScore = $score;
                $highestGenre = $genre;
            }
        }

        $genreSuffixes = [
            'family_drama' => 'குடும்ப-நாடகம்',
            'love' => 'காதல்',
            'fight' => 'சண்டை',
            'mystery' => 'மர்மம்',
            'horror' => 'அமானுஷ்யம்',
            'sports' => 'விளையாட்டு',
            'inspiration' => 'உத்வேகம்',
            'history' => 'வரலாறு',
            'personalities' => 'ஆளுமைகள்',
            'true_stories' => 'உண்மைக்-கதைகள்',
            'cases' => 'வழக்குகள்'
        ];

        $grandchildSlug = $childSlug . '-' . $genreSuffixes[$highestGenre];
        $grandchildCat = GrandchildCategory::where('slug', $grandchildSlug)->first();

        return [
            'category_id' => $cinemaCat ? $cinemaCat->id : null,
            'subcategory_id' => $cinemaSub ? $cinemaSub->id : null,
            'child_category_id' => $childCat ? $childCat->id : null,
            'grandchild_category_id' => $grandchildCat ? $grandchildCat->id : null,
        ];
    }

    /**
     * Classify content into the new taxonomy system.
     *
     * @param string $title
     * @param string $content
     * @return array
     */
    public function classify(string $title, string $content): array
    {
        $text = mb_strtolower($title . ' ' . strip_tags($content));

        // 1. Identify Category
        $categoryKeywords = [
            'good-cinema' => ['படம்', 'சினிமா', 'பராசக்தி', 'திரைப்படம்', 'திரை விமர்சனம்', 'இயக்குநர்', 'நடிகர்', 'நடிகை', 'பாக்ஸ் ஆபீஸ்', 'திரையரங்கம்', 'ஓடிடி', 'cinema', 'movie', 'film', 'director', 'actor', 'actress', 'review', 'ott', 'theater', 'trailer', 'கதை', 'திரைக்கதை', 'வசனம்', 'பாடல்', 'இசை', 'மியூசிக்', 'கதாபாத்திரம்'],
            'books' => ['நூல்', 'நூல்கள்', 'புத்தகம்', 'புத்தகங்கள்', 'நாவல்', 'சிறுகதை', 'வாசிப்பு', 'விமர்சனம்', 'கவிதை', 'படித்தேன்', 'எழுதியது', 'book', 'books', 'novel', 'read', 'author', 'reading', 'வாசகர்', 'பக்கங்கள்'],
            'travel' => ['பயணம்', 'டூர்', 'விசிட்', 'ஊர்', 'பயணங்கள்', 'மலை', 'காடு', 'கடற்கரை', 'மலைவாழிடம்', 'சுற்றுலா', 'சுற்றுலாத் தலம்', 'விமானம்', 'ரயில்', 'travel', 'trip', 'tour', 'visit', 'journey', 'explore', 'flight', 'train', 'waterfall', 'temple', 'ஹோட்டல்', 'hotel', 'விடுமுறை'],
            'people' => ['ஆசிரியர்', 'எழுத்தாளர்', 'அரசியல்வாதி', 'விஞ்ஞானி', 'விளையாட்டு வீரர்', 'நடிகர்', 'இயக்குனர்', 'சமூக செயற்பாட்டாளர்', 'தொழிலதிபர்', 'வாழ்க்கை வரலாறு', 'நேர்காணல்', 'நினைவுகள்', 'interview', 'personality', 'biography', 'memoir', 'activist'],
            'history-geography' => ['வரலாறு', 'சரித்திரம்', 'காலம்', 'பண்டைய', 'பேரரசு', 'ராஜா', 'போர்', 'புவியியல்', 'ஏரி', 'மலை', 'ஆறு', 'கடல்', 'காடு', 'நிலப்பரப்பு', 'பூகோளம்', 'ancient', 'history', 'historical', 'geography', 'geographical', 'lake', 'river', 'sea', 'forest', 'mountain', 'map', 'landscape', 'medieval', 'king', 'empire', 'பண்டைய காலம்', 'இடைக்காலம்', 'நவீன காலம்'],
            'science' => ['அறிவியல்', 'இயற்பியல்', 'வேதியியல்', 'உயிரியல்', 'வானியல்', 'மருத்துவம்', 'செயற்கை நுண்ணறிவு', 'விஞ்ஞானம்', 'ஆராய்ச்சி', 'science', 'physics', 'chemistry', 'biology', 'astronomy', 'medicine', 'விஞ்ஞானி'],
            'education' => ['கல்வி', 'பள்ளி', 'கல்லூரி', 'ஆசிரியர்', 'மாணவர்', 'படிப்பு', 'கணிதம்', 'education', 'school', 'college', 'teacher', 'student', 'math', 'கல்விப்'],
            'society' => ['சமூகம்', 'சமூகப்', 'மக்கள்', 'பாலினம்', 'பெண்கள்', 'இணையம்', 'இளைஞர்கள்', 'culture', 'society', 'social', 'gender', 'media', 'ஊடகம்'],
            'politics' => ['அரசியல்', 'தேர்தல்', 'ஆட்சி', 'கொள்கை', 'அமைச்சர்', 'கட்சி', 'ஓட்டு', 'politics', 'election', 'government', 'party', 'vote', 'நாடாளுமன்றம்', 'சட்டமன்றம்'],
            'religion-spirituality' => ['மதம்', 'ஆன்மீகம்', 'இந்து', 'இஸ்லாம்', 'கிறிஸ்தவம்', 'கடவுள்', 'சாதி', 'மூட நம்பிக்கை', 'religion', 'spiritual', 'hindu', 'islam', 'christian', 'god', 'temple', 'caste', 'பௌத்தம்', 'ஜைனம்', 'சீக்கியம்', 'அடிமைத்தனம்', 'பழமை வாதம்'],
            'sports' => ['விளையாட்டு', 'விளையாட்டுகள்', 'கிரிக்கெட்', 'கால்பந்து', 'டென்னிஸ்', 'சதுரங்கம்', 'ஒலிம்பிக்', 'sports', 'game', 'cricket', 'football', 'chess', 'olympics', 'விளையாட்டு வீரர்'],
            'technology' => ['தொழில்நுட்பம்', 'மென்பொருள்', 'வன்பொருள்', 'செயற்கை நுண்ணறிவு', 'சைபர்', 'AI', 'software', 'hardware', 'cyber', 'internet', 'technology', 'gadgets', 'cyber security'],
            'lifestyle' => ['வாழ்வியல்', 'வாழ்க்கை', 'மகிழ்ச்சி', 'வெற்றி', 'நிதி', 'பணம்', 'சேமிப்பு', 'முதலீடு', 'ஆரோக்கியம்', 'lifestyle', 'happiness', 'success', 'health', 'finance', 'money', 'investment', 'மனநலம்', 'உறவுகள்'],
            'experiences' => ['அனுபவம்', 'அனுபவங்கள்', 'நண்பர்கள்', 'பணியிடம்', 'பாடம்', 'எச்சரிக்கை', 'experience', 'lessons', 'friends', 'workplace', 'inspiration', 'தனிப்பட்ட அனுபவம்'],
            'kathaingo-special-series' => ['தொடர்', 'சிறப்புத் தொடர்', 'பாகம்', 'அத்தியாயம்', 'series', 'volume', 'chapter', 'கதைங்கோ சிறப்பு']
        ];

        $highestCategorySlug = 'lifestyle';
        $highestCategoryScore = -1;

        foreach ($categoryKeywords as $slug => $keywords) {
            $score = $this->calculateScore($text, $keywords);
            if ($score > $highestCategoryScore) {
                $highestCategoryScore = $score;
                $highestCategorySlug = $slug;
            }
        }

        $category = Category::where('slug', $highestCategorySlug)->first();
        if (!$category) {
            $category = Category::where('slug', '!=', 'pathivargal')->first(); // fallback to any main category
        }

        $categoryId = $category ? $category->id : null;
        $metadataValueIds = [];

        if ($category) {
            // Fetch all MetadataTypes and values for this category
            $types = MetadataType::where('category_id', $categoryId)->with('values')->get();

            foreach ($types as $type) {
                $highestValId = null;
                $highestValScore = -1;

                // For location-hierarchy, do special nested detection
                if ($type->slug === 'location-hierarchy') {
                    // Try to match city first, then region, then state, then country
                    $allVals = MetadataValue::where('metadata_type_id', $type->id)->get();
                    $matchedVal = null;
                    foreach ($allVals as $val) {
                        $valKeywords = [mb_strtolower($val->name)];
                        if ($val->name_en) {
                            $valKeywords[] = mb_strtolower($val->name_en);
                        }
                        if ($this->calculateScore($text, $valKeywords) > 0) {
                            // If we match a deeper node, prefer it
                            if (!$matchedVal || ($val->parent_id && !$matchedVal->parent_id)) {
                                $matchedVal = $val;
                            }
                        }
                    }
                    if ($matchedVal) {
                        $metadataValueIds[] = $matchedVal->id;
                        // Add parent chain too!
                        $curr = $matchedVal;
                        while ($curr->parent_id) {
                            $parent = MetadataValue::find($curr->parent_id);
                            if ($parent) {
                                $metadataValueIds[] = $parent->id;
                                $curr = $parent;
                            } else {
                                break;
                            }
                        }
                    }
                    continue;
                }

                // Standard value keyword matching
                foreach ($type->values as $value) {
                    $valKeywords = [mb_strtolower($value->name)];
                    if ($value->name_en) {
                        $valKeywords[] = mb_strtolower($value->name_en);
                    }
                    $score = $this->calculateScore($text, $valKeywords);
                    if ($score > $highestValScore && $score > 0) {
                        $highestValScore = $score;
                        $highestValId = $value->id;
                    }
                }

                if ($highestValId) {
                    $metadataValueIds[] = $highestValId;
                }
            }
        }

        // 3. Extract Tags (suggested tags from text)
        $words = preg_split('/[\s,\.\?\!\-\(\)\"\'\#]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopwords = [
            'ஒரு', 'மற்றும்', 'இந்த', 'என்று', 'இருந்தது', 'இருக்கிறார்', 'அவர்', 'அவர்கள்', 'அது', 'தான்',
            'the', 'and', 'with', 'this', 'that', 'from', 'have', 'were', 'also', 'about', 'here', 'there'
        ];
        $tagCandidates = [];
        foreach ($words as $word) {
            if (mb_strlen($word) > 3 && !in_array($word, $stopwords)) {
                if (!isset($tagCandidates[$word])) {
                    $tagCandidates[$word] = 0;
                }
                $tagCandidates[$word]++;
            }
        }
        arsort($tagCandidates);
        $suggestedTags = array_slice(array_keys($tagCandidates), 0, 5);

        return [
            'category_id' => $categoryId,
            'metadata_value_ids' => array_unique($metadataValueIds),
            'tags' => $suggestedTags,
        ];
    }

    /**
     * Helper to count occurrences of keywords in text.
     */
    private function calculateScore(string $text, array $keywords): int
    {
        $score = 0;
        foreach ($keywords as $keyword) {
            $score += substr_count($text, mb_strtolower($keyword));
        }
        return $score;
    }
}
