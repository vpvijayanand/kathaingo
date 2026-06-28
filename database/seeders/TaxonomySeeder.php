<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MetadataType;
use App\Models\MetadataValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // Keep the pathivargal category and its subcategories (writers) intact
        Category::where('slug', '!=', 'pathivargal')->delete();

        // Ensure the pathivargal (Writers) category exists
        Category::firstOrCreate(
            ['slug' => 'pathivargal'],
            [
                'name' => 'Writers',
                'name_en' => 'Writers',
                'description' => 'கதைங்கோ எழுத்தாளர்கள் / Writers of Kathaingo',
                'description_en' => 'Writers of Kathaingo',
                'order' => 100
            ]
        );

        $categories = [
            [
                'name' => 'நல்ல சினிமா',
                'slug' => 'good-cinema',
                'name_en' => 'Good Cinema',
                'description' => 'திரைப்பட விமர்சனங்கள் மற்றும் தகவல்கள்',
                'description_en' => 'Movie reviews and information',
                'order' => 1,
                'metadata_types' => [
                    [
                        'name' => 'மொழி',
                        'slug' => 'language',
                        'name_en' => 'Language',
                        'values' => [
                            ['name' => 'தமிழ்', 'slug' => 'tamil', 'name_en' => 'Tamil'],
                            ['name' => 'ஆங்கிலம்', 'slug' => 'english', 'name_en' => 'English'],
                            ['name' => 'பிறமொழி', 'slug' => 'other-language', 'name_en' => 'Other Language'],
                        ]
                    ],
                    [
                        'name' => 'வகை',
                        'slug' => 'genre',
                        'name_en' => 'Genre',
                        'values' => [
                            ['name' => 'நாடகம்', 'slug' => 'drama', 'name_en' => 'Drama'],
                            ['name' => 'சண்டை', 'slug' => 'action', 'name_en' => 'Action'],
                            ['name' => 'போர்', 'slug' => 'war', 'name_en' => 'War'],
                            ['name' => 'குற்றம்', 'slug' => 'crime', 'name_en' => 'Crime'],
                            ['name' => 'மர்மம்', 'slug' => 'mystery', 'name_en' => 'Mystery'],
                            ['name' => 'அமானுஷ்யம்', 'slug' => 'supernatural', 'name_en' => 'Supernatural'],
                            ['name' => 'திகில்', 'slug' => 'horror', 'name_en' => 'Horror'],
                            ['name' => 'காதல்', 'slug' => 'romance', 'name_en' => 'Romance'],
                            ['name' => 'காமம்', 'slug' => 'erotica', 'name_en' => 'Erotica'],
                            ['name' => 'பாண்டஸி', 'slug' => 'fantasy', 'name_en' => 'Fantasy'],
                            ['name' => 'வரலாறு', 'slug' => 'history', 'name_en' => 'History'],
                            ['name' => 'நீதிமன்றம்', 'slug' => 'courtroom', 'name_en' => 'Courtroom'],
                            ['name' => 'விளையாட்டு', 'slug' => 'sports', 'name_en' => 'Sports'],
                            ['name' => 'வாழ்க்கை வரலாறு', 'slug' => 'biography', 'name_en' => 'Biography'],
                            ['name' => 'அரசியல்', 'slug' => 'politics', 'name_en' => 'Politics'],
                            ['name' => 'நகைச்சுவை', 'slug' => 'comedy', 'name_en' => 'Comedy'],
                            ['name' => 'குடும்பம்', 'slug' => 'family', 'name_en' => 'Family'],
                            ['name' => 'சாகசம்', 'slug' => 'adventure', 'name_en' => 'Adventure'],
                            ['name' => 'அறிவியல் புனைகதை', 'slug' => 'sci-fi', 'name_en' => 'Sci-Fi'],
                        ]
                    ],
                    [
                        'name' => 'வடிவம்',
                        'slug' => 'format',
                        'name_en' => 'Format',
                        'values' => [
                            ['name' => 'திரைப்படம்', 'slug' => 'movie', 'name_en' => 'Movie'],
                            ['name' => 'குறும்படம்', 'slug' => 'short-film', 'name_en' => 'Short Film'],
                            ['name' => 'ஆவணப்படம்', 'slug' => 'documentary', 'name_en' => 'Documentary'],
                            ['name' => 'இணையத் தொடர்', 'slug' => 'web-series', 'name_en' => 'Web Series'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'நூல்கள்',
                'slug' => 'books',
                'name_en' => 'Books',
                'description' => 'புத்தக விமர்சனங்கள் மற்றும் அறிமுகங்கள்',
                'description_en' => 'Book reviews and introductions',
                'order' => 2,
                'metadata_types' => [
                    [
                        'name' => 'மொழி',
                        'slug' => 'language',
                        'name_en' => 'Language',
                        'values' => [
                            ['name' => 'தமிழ்', 'slug' => 'tamil', 'name_en' => 'Tamil'],
                            ['name' => 'ஆங்கிலம்', 'slug' => 'english', 'name_en' => 'English'],
                            ['name' => 'பிறமொழி', 'slug' => 'other-language', 'name_en' => 'Other Language'],
                        ]
                    ],
                    [
                        'name' => 'வகை',
                        'slug' => 'genre',
                        'name_en' => 'Genre',
                        'values' => [
                            ['name' => 'நாவல்', 'slug' => 'novel', 'name_en' => 'Novel'],
                            ['name' => 'சிறுகதை', 'slug' => 'short-story', 'name_en' => 'Short Story'],
                            ['name' => 'வரலாறு', 'slug' => 'history', 'name_en' => 'History'],
                            ['name' => 'அறிவியல்', 'slug' => 'science', 'name_en' => 'Science'],
                            ['name' => 'தத்துவம்', 'slug' => 'philosophy', 'name_en' => 'Philosophy'],
                            ['name' => 'வாழ்க்கை வரலாறு', 'slug' => 'biography', 'name_en' => 'Biography'],
                            ['name' => 'சுயமுன்னேற்றம்', 'slug' => 'self-help', 'name_en' => 'Self-Help'],
                            ['name' => 'சமூகம்', 'slug' => 'society', 'name_en' => 'Society'],
                            ['name' => 'அரசியல்', 'slug' => 'politics', 'name_en' => 'Politics'],
                            ['name' => 'மதம்', 'slug' => 'religion', 'name_en' => 'Religion'],
                            ['name' => 'பயணம்', 'slug' => 'travel', 'name_en' => 'Travel'],
                            ['name' => 'கவிதை', 'slug' => 'poetry', 'name_en' => 'Poetry'],
                        ]
                    ],
                    [
                        'name' => 'வகைப்பாடு',
                        'slug' => 'type',
                        'name_en' => 'Type',
                        'values' => [
                            ['name' => 'புத்தக விமர்சனம்', 'slug' => 'book-review', 'name_en' => 'Book Review'],
                            ['name' => 'புத்தக அறிமுகம்', 'slug' => 'book-introduction', 'name_en' => 'Book Introduction'],
                            ['name' => 'வாசிப்பு குறிப்புகள்', 'slug' => 'reading-notes', 'name_en' => 'Reading Notes'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'பயணம்',
                'slug' => 'travel',
                'name_en' => 'Travel',
                'description' => 'பயணக் கட்டுரைகள் மற்றும் சுற்றுலா வழிகாட்டிகள்',
                'description_en' => 'Travelogues and tourist guides',
                'order' => 3,
                'metadata_types' => [
                    [
                        'name' => 'பயண வகை',
                        'slug' => 'travel-type',
                        'name_en' => 'Travel Type',
                        'values' => [
                            ['name' => 'குடும்பப் பயணம்', 'slug' => 'family-trip', 'name_en' => 'Family Trip'],
                            ['name' => 'தனிப் பயணம்', 'slug' => 'solo-trip', 'name_en' => 'Solo Trip'],
                            ['name' => 'சாகசப் பயணம்', 'slug' => 'adventure-trip', 'name_en' => 'Adventure Trip'],
                            ['name' => 'ஆன்மீகப் பயணம்', 'slug' => 'spiritual-trip', 'name_en' => 'Spiritual Trip'],
                            ['name' => 'கல்விப் பயணம்', 'slug' => 'educational-trip', 'name_en' => 'Educational Trip'],
                            ['name' => 'வரலாற்றுப் பயணம்', 'slug' => 'historical-trip', 'name_en' => 'Historical Trip'],
                        ]
                    ],
                    [
                        'name' => 'இடத்தின் வகை',
                        'slug' => 'place-type',
                        'name_en' => 'Place Type',
                        'values' => [
                            ['name' => 'நகரம்', 'slug' => 'city', 'name_en' => 'City'],
                            ['name' => 'கிராமம்', 'slug' => 'village', 'name_en' => 'Village'],
                            ['name' => 'மலை', 'slug' => 'mountain', 'name_en' => 'Mountain'],
                            ['name' => 'கடற்கரை', 'slug' => 'beach', 'name_en' => 'Beach'],
                            ['name' => 'தீவு', 'slug' => 'island', 'name_en' => 'Island'],
                            ['name' => 'கோவில்', 'slug' => 'temple', 'name_en' => 'Temple'],
                            ['name' => 'அரண்மனை', 'slug' => 'palace', 'name_en' => 'Palace'],
                            ['name' => 'கோட்டை', 'slug' => 'fort', 'name_en' => 'Fort'],
                            ['name' => 'அருங்காட்சியகம்', 'slug' => 'museum', 'name_en' => 'Museum'],
                            ['name' => 'இயற்கை', 'slug' => 'nature', 'name_en' => 'Nature'],
                            ['name' => 'ஏரி', 'slug' => 'lake', 'name_en' => 'Lake'],
                            ['name' => 'ஆறு', 'slug' => 'river', 'name_en' => 'River'],
                        ]
                    ],
                    [
                        'name' => 'இட வரிசைமுறை',
                        'slug' => 'location-hierarchy',
                        'name_en' => 'Location Hierarchy',
                        'is_hierarchical' => true,
                        'values' => [] // Populated programmatically below
                    ]
                ]
            ],
            [
                'name' => 'மனிதர்கள்',
                'slug' => 'people',
                'name_en' => 'People',
                'description' => 'மனிதர்களின் ஆளுமை மற்றும் நேர்காணல்',
                'description_en' => 'Personalities and interviews',
                'order' => 4,
                'metadata_types' => [
                    [
                        'name' => 'தொழில்',
                        'slug' => 'profession',
                        'name_en' => 'Profession',
                        'values' => [
                            ['name' => 'ஆசிரியர்', 'slug' => 'teacher', 'name_en' => 'Teacher'],
                            ['name' => 'எழுத்தாளர்', 'slug' => 'writer', 'name_en' => 'Writer'],
                            ['name' => 'அரசியல்வாதி', 'slug' => 'politician', 'name_en' => 'Politician'],
                            ['name' => 'விஞ்ஞானி', 'slug' => 'scientist', 'name_en' => 'Scientist'],
                            ['name' => 'விளையாட்டு வீரர்', 'slug' => 'athlete', 'name_en' => 'Athlete'],
                            ['name' => 'நடிகர்', 'slug' => 'actor', 'name_en' => 'Actor'],
                            ['name' => 'இயக்குனர்', 'slug' => 'director', 'name_en' => 'Director'],
                            ['name' => 'சமூக செயற்பாட்டாளர்', 'slug' => 'social-activist', 'name_en' => 'Social Activist'],
                            ['name' => 'தொழிலதிபர்', 'slug' => 'entrepreneur', 'name_en' => 'Entrepreneur'],
                        ]
                    ],
                    [
                        'name' => 'உள்ளடக்க வகை',
                        'slug' => 'content-type',
                        'name_en' => 'Content Type',
                        'values' => [
                            ['name' => 'வாழ்க்கை வரலாறு', 'slug' => 'biography', 'name_en' => 'Biography'],
                            ['name' => 'அனுபவம்', 'slug' => 'experience', 'name_en' => 'Experience'],
                            ['name' => 'நேர்காணல்', 'slug' => 'interview', 'name_en' => 'Interview'],
                            ['name' => 'நினைவுகள்', 'slug' => 'memoirs', 'name_en' => 'Memoirs'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'வரலாறு மற்றும் புவியியல்',
                'slug' => 'history-geography',
                'name_en' => 'History and Geography',
                'description' => 'வரலாறு மற்றும் புவியியல் தகவல்கள்',
                'description_en' => 'Historical and geographical information',
                'order' => 5,
                'metadata_types' => [
                    [
                        'name' => 'வரலாற்று காலம்',
                        'slug' => 'historical-period',
                        'name_en' => 'Historical Period',
                        'values' => [
                            ['name' => 'பண்டைய காலம்', 'slug' => 'ancient', 'name_en' => 'Ancient'],
                            ['name' => 'இடைக்காலம்', 'slug' => 'medieval', 'name_en' => 'Medieval'],
                            ['name' => 'நவீன காலம்', 'slug' => 'modern', 'name_en' => 'Modern'],
                            ['name' => 'சமகாலம்', 'slug' => 'contemporary', 'name_en' => 'Contemporary'],
                        ]
                    ],
                    [
                        'name' => 'கருப்பொருள்',
                        'slug' => 'theme',
                        'name_en' => 'Theme',
                        'values' => [
                            ['name' => 'போர்', 'slug' => 'war', 'name_en' => 'War'],
                            ['name' => 'அரசியல்', 'slug' => 'politics', 'name_en' => 'Politics'],
                            ['name' => 'பேரரசுகள்', 'slug' => 'empires', 'name_en' => 'Empires'],
                            ['name' => 'கலாச்சாரம்', 'slug' => 'culture', 'name_en' => 'Culture'],
                            ['name' => 'அறிவியல்', 'slug' => 'science', 'name_en' => 'Science'],
                            ['name' => 'மதம்', 'slug' => 'religion', 'name_en' => 'Religion'],
                        ]
                    ],
                    [
                        'name' => 'இடத்தின் வகை',
                        'slug' => 'place-type',
                        'name_en' => 'Place Type',
                        'values' => [
                            ['name' => 'நகரம்', 'slug' => 'city', 'name_en' => 'City'],
                            ['name' => 'கிராமம்', 'slug' => 'village', 'name_en' => 'Village'],
                            ['name' => 'மலை', 'slug' => 'mountain', 'name_en' => 'Mountain'],
                            ['name' => 'கடற்கரை', 'slug' => 'beach', 'name_en' => 'Beach'],
                            ['name' => 'தீவு', 'slug' => 'island', 'name_en' => 'Island'],
                            ['name' => 'கோவில்', 'slug' => 'temple', 'name_en' => 'Temple'],
                            ['name' => 'அரண்மனை', 'slug' => 'palace', 'name_en' => 'Palace'],
                            ['name' => 'கோட்டை', 'slug' => 'fort', 'name_en' => 'Fort'],
                            ['name' => 'அருங்காட்சியகம்', 'slug' => 'museum', 'name_en' => 'Museum'],
                            ['name' => 'இயற்கை', 'slug' => 'nature', 'name_en' => 'Nature'],
                            ['name' => 'ஏரி', 'slug' => 'lake', 'name_en' => 'Lake'],
                            ['name' => 'ஆறு', 'slug' => 'river', 'name_en' => 'River'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'அறிவியல்',
                'slug' => 'science',
                'name_en' => 'Science',
                'description' => 'அறிவியல் பூர்வமான செய்திகள் மற்றும் விளக்கங்கள்',
                'description_en' => 'Scientific news and explanations',
                'order' => 6,
                'metadata_types' => [
                    [
                        'name' => 'துறை',
                        'slug' => 'field',
                        'name_en' => 'Field',
                        'values' => [
                            ['name' => 'இயற்பியல்', 'slug' => 'physics', 'name_en' => 'Physics'],
                            ['name' => 'வேதியியல்', 'slug' => 'chemistry', 'name_en' => 'Chemistry'],
                            ['name' => 'உயிரியல்', 'slug' => 'biology', 'name_en' => 'Biology'],
                            ['name' => 'வானியல்', 'slug' => 'astronomy', 'name_en' => 'Astronomy'],
                            ['name' => 'மருத்துவம்', 'slug' => 'medicine', 'name_en' => 'Medicine'],
                            ['name' => 'தொழில்நுட்பம்', 'slug' => 'technology', 'name_en' => 'Technology'],
                            ['name' => 'செயற்கை நுண்ணறிவு', 'slug' => 'ai', 'name_en' => 'AI'],
                            ['name' => 'சுற்றுச்சூழல்', 'slug' => 'environment', 'name_en' => 'Environment'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'கல்வி',
                'slug' => 'education',
                'name_en' => 'Education',
                'description' => 'பாடங்கள் மற்றும் கல்வி சார்ந்த வழிகாட்டி',
                'description_en' => 'Lessons and educational guides',
                'order' => 7,
                'metadata_types' => [
                    [
                        'name' => 'பாடம்',
                        'slug' => 'subject',
                        'name_en' => 'Subject',
                        'values' => [
                            ['name' => 'தமிழ்', 'slug' => 'tamil', 'name_en' => 'Tamil'],
                            ['name' => 'ஆங்கிலம்', 'slug' => 'english', 'name_en' => 'English'],
                            ['name' => 'கணிதம்', 'slug' => 'math', 'name_en' => 'Math'],
                            ['name' => 'அறிவியல்', 'slug' => 'science', 'name_en' => 'Science'],
                            ['name' => 'வரலாறு', 'slug' => 'history', 'name_en' => 'History'],
                            ['name' => 'பொருளாதாரம்', 'slug' => 'economics', 'name_en' => 'Economics'],
                        ]
                    ],
                    [
                        'name' => 'வாசகர்கள்',
                        'slug' => 'audience',
                        'name_en' => 'Audience',
                        'values' => [
                            ['name' => 'பள்ளி', 'slug' => 'school', 'name_en' => 'School'],
                            ['name' => 'கல்லூரி', 'slug' => 'college', 'name_en' => 'College'],
                            ['name' => 'ஆசிரியர்கள்', 'slug' => 'teachers', 'name_en' => 'Teachers'],
                            ['name' => 'பொதுவாசகர்கள்', 'slug' => 'general-readers', 'name_en' => 'General Readers'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'சமூகம்',
                'slug' => 'society',
                'name_en' => 'Society',
                'description' => 'சமூகப் பிரச்சினைகள் மற்றும் கருத்துக்கள்',
                'description_en' => 'Social issues and reflections',
                'order' => 8,
                'metadata_types' => [
                    [
                        'name' => 'கருப்பொருள்',
                        'slug' => 'theme',
                        'name_en' => 'Theme',
                        'values' => [
                            ['name' => 'சமூக பிரச்சினைகள்', 'slug' => 'social-issues', 'name_en' => 'Social Issues'],
                            ['name' => 'குடும்பம்', 'slug' => 'family', 'name_en' => 'Family'],
                            ['name' => 'பாலினம்', 'slug' => 'gender', 'name_en' => 'Gender'],
                            ['name' => 'ஊடகம்', 'slug' => 'media', 'name_en' => 'Media'],
                            ['name' => 'இணையம்', 'slug' => 'internet', 'name_en' => 'Internet'],
                            ['name' => 'இளைஞர்கள்', 'slug' => 'youth', 'name_en' => 'Youth'],
                            ['name' => 'கலாச்சாரம்', 'slug' => 'culture', 'name_en' => 'Culture'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'அரசியல்',
                'slug' => 'politics',
                'name_en' => 'Politics',
                'description' => 'அரசியல் செய்திகள் மற்றும் பகுப்பாய்வு',
                'description_en' => 'Political news and analysis',
                'order' => 9,
                'metadata_types' => [
                    [
                        'name' => 'கருப்பொருள்',
                        'slug' => 'theme',
                        'name_en' => 'Theme',
                        'values' => [
                            ['name' => 'தேர்தல்', 'slug' => 'election', 'name_en' => 'Election'],
                            ['name' => 'ஆட்சி', 'slug' => 'governance', 'name_en' => 'Governance'],
                            ['name' => 'கொள்கை', 'slug' => 'policy', 'name_en' => 'Policy'],
                            ['name' => 'அரசியல் வரலாறு', 'slug' => 'political-history', 'name_en' => 'Political History'],
                            ['name' => 'சர்வதேச உறவுகள்', 'slug' => 'international-relations', 'name_en' => 'International Relations'],
                            ['name' => 'கட்சி', 'slug' => 'party', 'name_en' => 'Party'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'மதம் மற்றும் ஆன்மீகம்',
                'slug' => 'religion-spirituality',
                'name_en' => 'Religion & Spirituality',
                'description' => 'மதக் கொள்கைகள், தத்துவம் மற்றும் விவாதம்',
                'description_en' => 'Religions, philosophy and debate',
                'order' => 10,
                'metadata_types' => [
                    [
                        'name' => 'மதம்',
                        'slug' => 'religion',
                        'name_en' => 'Religion',
                        'values' => [
                            ['name' => 'இந்து', 'slug' => 'hindu', 'name_en' => 'Hindu'],
                            ['name' => 'இஸ்லாம்', 'slug' => 'islam', 'name_en' => 'Islam'],
                            ['name' => 'கிறிஸ்தவம்', 'slug' => 'christian', 'name_en' => 'Christian'],
                            ['name' => 'பௌத்தம்', 'slug' => 'buddhist', 'name_en' => 'Buddhist'],
                            ['name' => 'ஜைனம்', 'slug' => 'jain', 'name_en' => 'Jain'],
                            ['name' => 'சீக்கியம்', 'slug' => 'sikh', 'name_en' => 'Sikh'],
                            ['name' => 'பிற', 'slug' => 'other', 'name_en' => 'Other'],
                        ]
                    ],
                    [
                        'name' => 'கருப்பொருள்',
                        'slug' => 'theme',
                        'name_en' => 'Theme',
                        'values' => [
                            ['name' => 'சாதி', 'slug' => 'caste', 'name_en' => 'Caste'],
                            ['name' => 'அடிமைத்தனம்', 'slug' => 'slavery', 'name_en' => 'Slavery'],
                            ['name' => 'மூட நம்பிக்கைகள்', 'slug' => 'superstitions', 'name_en' => 'Superstitions'],
                            ['name' => 'பழமை வாதம்', 'slug' => 'orthodoxy', 'name_en' => 'Orthodoxy'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'விளையாட்டு',
                'slug' => 'sports',
                'name_en' => 'Sports',
                'description' => 'விளையாட்டுச் செய்திகள் மற்றும் நிகழ்வுகள்',
                'description_en' => 'Sports news and matches',
                'order' => 11,
                'metadata_types' => [
                    [
                        'name' => 'விளையாட்டு',
                        'slug' => 'sport',
                        'name_en' => 'Sport',
                        'values' => [
                            ['name' => 'கிரிக்கெட்', 'slug' => 'cricket', 'name_en' => 'Cricket'],
                            ['name' => 'கால்பந்து', 'slug' => 'football', 'name_en' => 'Football'],
                            ['name' => 'டென்னிஸ்', 'slug' => 'tennis', 'name_en' => 'Tennis'],
                            ['name' => 'சதுரங்கம்', 'slug' => 'chess', 'name_en' => 'Chess'],
                            ['name' => 'ஒலிம்பிக்', 'slug' => 'olympics', 'name_en' => 'Olympics'],
                            ['name' => 'பிற', 'slug' => 'other', 'name_en' => 'Other'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'தொழில்நுட்பம்',
                'slug' => 'technology',
                'name_en' => 'Technology',
                'description' => 'சமீபத்திய தொழில்நுட்ப போக்குகள் மற்றும் புதுமைகள்',
                'description_en' => 'Latest tech trends and innovations',
                'order' => 12,
                'metadata_types' => [
                    [
                        'name' => 'தொழில்நுட்ப பகுதி',
                        'slug' => 'technology-area',
                        'name_en' => 'Technology Area',
                        'values' => [
                            ['name' => 'AI', 'slug' => 'ai', 'name_en' => 'AI'],
                            ['name' => 'Software', 'slug' => 'software', 'name_en' => 'Software'],
                            ['name' => 'Hardware', 'slug' => 'hardware', 'name_en' => 'Hardware'],
                            ['name' => 'Internet', 'slug' => 'internet', 'name_en' => 'Internet'],
                            ['name' => 'Cyber Security', 'slug' => 'cyber-security', 'name_en' => 'Cyber Security'],
                            ['name' => 'Gadgets', 'slug' => 'gadgets', 'name_en' => 'Gadgets'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'வாழ்வியல்',
                'slug' => 'lifestyle',
                'name_en' => 'Lifestyle',
                'description' => 'நல்ல வாழ்க்கை முறை மற்றும் சுய வளர்ச்சி',
                'description_en' => 'Well-balanced life and self-development',
                'order' => 13,
                'metadata_types' => [
                    [
                        'name' => 'கருப்பொருள்',
                        'slug' => 'theme',
                        'name_en' => 'Theme',
                        'values' => [
                            ['name' => 'மகிழ்ச்சி', 'slug' => 'happiness', 'name_en' => 'Happiness'],
                            ['name' => 'பழக்கவழக்கங்கள்', 'slug' => 'habits', 'name_en' => 'Habits'],
                            ['name' => 'வெற்றி', 'slug' => 'success', 'name_en' => 'Success'],
                            ['name' => 'தோல்வி', 'slug' => 'failure', 'name_en' => 'Failure'],
                            ['name' => 'நேர மேலாண்மை', 'slug' => 'time-management', 'name_en' => 'Time Management'],
                            ['name' => 'உறவுகள்', 'slug' => 'relationships', 'name_en' => 'Relationships'],
                            ['name' => 'ஆரோக்கியம்', 'slug' => 'health', 'name_en' => 'Health'],
                            ['name' => 'மனநலம்', 'slug' => 'mental-health', 'name_en' => 'Mental Health'],
                            ['name' => 'தொழில்', 'slug' => 'career', 'name_en' => 'Career'],
                        ]
                    ],
                    [
                        'name' => 'நிதி மேலாண்மை',
                        'slug' => 'financial-management',
                        'name_en' => 'Financial Management',
                        'values' => [
                            ['name' => 'தங்கம்', 'slug' => 'gold', 'name_en' => 'Gold'],
                            ['name' => 'பங்குச்சந்தை', 'slug' => 'stock-market', 'name_en' => 'Stock Market'],
                            ['name' => 'ரியல் எஸ்டேட்', 'slug' => 'real-estate', 'name_en' => 'Real Estate'],
                            ['name' => 'கடன் பத்திரங்கள்', 'slug' => 'bonds', 'name_en' => 'Bonds'],
                            ['name' => 'மியூச்சுவல் ஃபண்ட்', 'slug' => 'mutual-funds', 'name_en' => 'Mutual Funds'],
                            ['name' => 'சேமிப்பு', 'slug' => 'savings', 'name_en' => 'Savings'],
                            ['name' => 'முதலீடு', 'slug' => 'investment', 'name_en' => 'Investment'],
                            ['name' => 'ஓய்வூதிய திட்டமிடல்', 'slug' => 'retirement-planning', 'name_en' => 'Retirement Planning'],
                            ['name' => 'வரி திட்டமிடல்', 'slug' => 'tax-planning', 'name_en' => 'Tax Planning'],
                            ['name' => 'தனிநபர் நிதி', 'slug' => 'personal-finance', 'name_en' => 'Personal Finance'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'அனுபவங்கள்',
                'slug' => 'experiences',
                'name_en' => 'Experiences',
                'description' => 'தனிநபர் மற்றும் சமூக அனுபவ பாடங்கள்',
                'description_en' => 'Personal and social life lessons',
                'order' => 14,
                'metadata_types' => [
                    [
                        'name' => 'மூலம்',
                        'slug' => 'source',
                        'name_en' => 'Source',
                        'values' => [
                            ['name' => 'தனிப்பட்ட அனுபவம்', 'slug' => 'personal', 'name_en' => 'Personal'],
                            ['name' => 'பணியிடம்', 'slug' => 'workplace', 'name_en' => 'Workplace'],
                            ['name' => 'பயணம்', 'slug' => 'travel', 'name_en' => 'Travel'],
                            ['name' => 'குடும்பம்', 'slug' => 'family', 'name_en' => 'Family'],
                            ['name' => 'நண்பர்கள்', 'slug' => 'friends', 'name_en' => 'Friends'],
                            ['name' => 'சமூக ஊடகம்', 'slug' => 'social-media', 'name_en' => 'Social Media'],
                        ]
                    ],
                    [
                        'name' => 'பாடம்',
                        'slug' => 'lesson-type',
                        'name_en' => 'Lesson Type',
                        'values' => [
                            ['name' => 'ஊக்கம்', 'slug' => 'inspiration', 'name_en' => 'Inspiration'],
                            ['name' => 'எச்சரிக்கை', 'slug' => 'warning', 'name_en' => 'Warning'],
                            ['name' => 'நகைச்சுவை', 'slug' => 'humor', 'name_en' => 'Humor'],
                            ['name' => 'சிந்தனை', 'slug' => 'reflection', 'name_en' => 'Reflection'],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'கதைங்கோ தொடர்கள்',
                'slug' => 'kathaingo-special-series',
                'name_en' => 'Kathaingo Series',
                'description' => 'கதைங்கோ வழங்கும் தொடர்கள்',
                'description_en' => 'Series from Kathaingo',
                'order' => 15,
                'metadata_types' => []
            ]
        ];

        foreach ($categories as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'name_en' => $catData['name_en'],
                'description' => $catData['description'],
                'description_en' => $catData['description_en'],
                'order' => $catData['order']
            ]);

            foreach ($catData['metadata_types'] as $typeData) {
                $type = MetadataType::create([
                    'category_id' => $category->id,
                    'name' => $typeData['name'],
                    'slug' => $typeData['slug'],
                    'name_en' => $typeData['name_en'],
                    'is_hierarchical' => $typeData['is_hierarchical'] ?? false
                ]);

                if ($type->slug === 'location-hierarchy') {
                    // Seed Country -> State -> Region -> City
                    $india = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => null,
                        'name' => 'இந்தியா',
                        'slug' => 'india',
                        'name_en' => 'India'
                    ]);

                    $tn = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $india->id,
                        'name' => 'தமிழ்நாடு',
                        'slug' => 'tamil-nadu',
                        'name_en' => 'Tamil Nadu'
                    ]);

                    $kongu = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $tn->id,
                        'name' => 'கொங்கு நாடு',
                        'slug' => 'kongu-nadu',
                        'name_en' => 'Kongu Nadu'
                    ]);

                    MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $kongu->id,
                        'name' => 'கோயம்புத்தூர்',
                        'slug' => 'coimbatore',
                        'name_en' => 'Coimbatore'
                    ]);

                    $chennaiRegion = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $tn->id,
                        'name' => 'சென்னை',
                        'slug' => 'chennai-region',
                        'name_en' => 'Chennai'
                    ]);

                    MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $chennaiRegion->id,
                        'name' => 'சென்னை',
                        'slug' => 'chennai',
                        'name_en' => 'Chennai'
                    ]);

                    $lanka = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => null,
                        'name' => 'இலங்கை',
                        'slug' => 'sri-lanka',
                        'name_en' => 'Sri Lanka'
                    ]);

                    $northProvince = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $lanka->id,
                        'name' => 'வட மாகாணம்',
                        'slug' => 'northern-province',
                        'name_en' => 'Northern Province'
                    ]);

                    $jaffnaRegion = MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $northProvince->id,
                        'name' => 'யாழ்ப்பாணம்',
                        'slug' => 'jaffna-region',
                        'name_en' => 'Jaffna'
                    ]);

                    MetadataValue::create([
                        'metadata_type_id' => $type->id,
                        'parent_id' => $jaffnaRegion->id,
                        'name' => 'யாழ்ப்பாணம்',
                        'slug' => 'jaffna',
                        'name_en' => 'Jaffna'
                    ]);
                } else {
                    foreach ($typeData['values'] as $valData) {
                        MetadataValue::create([
                            'metadata_type_id' => $type->id,
                            'parent_id' => null,
                            'name' => $valData['name'],
                            'slug' => $valData['slug'],
                            'name_en' => $valData['name_en']
                        ]);
                    }
                }
            }
        }
    }
}
