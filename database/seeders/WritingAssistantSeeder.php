<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WritingAssistantWord;
use Illuminate\Support\Facades\DB;

class WritingAssistantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coreTamilWords = [
            // Pronouns & Case Variants
            'நான்', 'நாம்', 'நாங்கள்', 'நீ', 'நீங்கள்', 'அவன்', 'அவள்', 'அவர்', 'அவர்கள்', 'அது', 'அவை',
            'இவன்', 'இவள்', 'இவர்', 'இவர்கள்', 'இது', 'இவை', 'என்', 'எனது', 'எங்கள்', 'எங்களது',
            'உன்', 'உனது', 'உங்கள்', 'உங்களது', 'அவன்', 'அவனது', 'அவள்', 'அவளது', 'அவர்', 'அவரது',
            'அவர்கள்', 'அவர்களது', 'தங்கள்', 'தங்களது', 'தனது', 'தமது', 'நமது', 'எம்', 'எமது', 'உமது',
            'தங்களுக்கு', 'தங்களை', 'தங்களால்', 'தங்களின்', 'தங்களோடு',
            'உங்களுக்கு', 'உங்களை', 'உங்களால்', 'உங்களின்', 'உங்களோடு',
            'எங்களுக்கு', 'எங்களை', 'எங்களால்', 'எங்களின்', 'எங்களோடு',
            'அவர்களுக்கு', 'அவர்களை', 'அவர்களால்', 'அவர்களின்', 'அவர்களோடு',
            'என்னை', 'எனை', 'எனக்கு', 'என்னால்', 'என்னோடு', 'என்னிடம்',
            'உன்னை', 'உனக்கு', 'உன்னால்', 'உன்னோடு', 'உன்னிடம்',
            
            // Adverbs of Place, Time, Manner
            'இங்கு', 'அங்கு', 'எங்கு', 'இங்கிருந்து', 'அங்கிருந்து', 'எங்கிருந்து', 'இங்குள்ள', 'அங்குள்ள', 'எங்குள்ள',
            'இப்படி', 'அப்படி', 'எப்படி', 'இப்போது', 'அப்போது', 'எப்போது', 'இங்கேயும்', 'அங்கேயும்', 'எங்கேயும்',
            'இங்கேயே', 'அங்கேயே', 'எங்கேயே', 'இப்பொழுது', 'அப்பொழுது', 'எப்பொழுது',
            
            // Conjunctions & Connectives
            'மற்றும்', 'ஆனால்', 'ஆகவே', 'எனவே', 'அல்லது', 'ஏனெனில்', 'பிறகு', 'உடன்', 'மேலும்', 'எனினும்',
            'ஆயினும்', 'எனக்', 'என்று', 'என்றுமே', 'என்றும்', 'ஏன்', 'ஏற்கனவே', 'ஏறக்குறைய', 'ஒருவேளை',
            
            // Conversational, Greetings & Proper Names
            'வணக்கம்', 'நன்றி', 'நலம்', 'நலமா', 'நலமுடன்', 'நலமாக', 'சௌக்கியமா', 'விசாரி', 'விசாரிக்கவும்',
            'வாழ்த்துகள்', 'வாழ்த்துக்கள்', 'வாழ்த்து', 'அன்புள்ள', 'அன்புடன்', 'அன்பான', 'அன்பு',
            'கதைங்கோ', 'இளங்கோ', 'இளங்கோவன்', 'மகன்', 'மகான்', 'அப்பாவுக்கு', 'அப்பாவிற்கு',
            
            // Basic Verbs & Inflections
            'இருக்கிறது', 'இருக்கின்றன', 'இருக்கிறேன்', 'இருக்கிறோம்', 'இருக்கிறாய்', 'இருக்கிறீர்கள்',
            'இருந்தது', 'இருந்தன', 'இருந்தேன்', 'இருந்தோம்', 'இருந்தாய்', 'இருந்தீர்கள்',
            'இருக்க', 'இருந்து', 'இருப்பேன்', 'இருப்போம்', 'இருப்பாய்', 'இருப்பீர்கள்', 'இருக்கும்',
            'உளது', 'உள்ளது', 'உள்ளன', 'உள்ளேன்', 'உள்ளோம்', 'உள்ளாய்', 'உள்ளீர்கள்',
            'செல்கிறது', 'சென்றது', 'செல்லும்', 'சென்று', 'செல்ல', 'போகிறது', 'போனது', 'போகும்', 'போய்', 'போக',
            'வருகிறது', 'வந்தது', 'வரும்', 'வந்து', 'வர', 'வருக', 'வா',
            'செய்கிறது', 'செய்தது', 'செய்யும்', 'செய்து', 'செய்ய', 'செய்கிறேன்', 'செய்கிறோம்',
            'படிக்கிறது', 'படித்தது', 'படிக்கும்', 'படித்து', 'படிக்க',
            'எழுதுகிறது', 'எழுதியது', 'எழுதும்', 'எழுதி', 'எழுத', 'எழுதிக்கொள்வது',
            'பேசுகிறது', 'பேசியது', 'பேசும்', 'பேசி', 'பேச'
        ];

        $this->command->info("Seeding core Tamil vocabulary...");
        $this->seedWords($coreTamilWords, 'ta');

        $tamilFile = storage_path('app/writing_assistant/tamil_words.txt');
        $englishFile = storage_path('app/writing_assistant/english_words.txt');

        // Check and seed Tamil words
        if (file_exists($tamilFile)) {
            $this->command->info("Seeding Tamil words...");
            $words = file($tamilFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->seedWords($words, 'ta');
        } else {
            $this->command->warn("Tamil words list file not found at: {$tamilFile}");
        }

        // Check and seed English words
        if (file_exists($englishFile)) {
            $this->command->info("Seeding English words...");
            $words = file($englishFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->seedWords($words, 'en');
        } else {
            $this->command->warn("English words list file not found at: {$englishFile}");
        }
    }

    /**
     * Seed words in optimized chunks using insertOrIgnore.
     */
    private function seedWords(array $words, string $lang): void
    {
        $chunkSize = 1000;
        $chunks = array_chunk($words, $chunkSize);
        $total = count($words);
        $seeded = 0;

        DB::beginTransaction();
        try {
            foreach ($chunks as $chunk) {
                $insertData = [];
                foreach ($chunk as $word) {
                    $trimmed = trim($word);
                    if ($trimmed !== '') {
                        $insertData[] = [
                            'word' => $trimmed,
                            'language' => $lang
                        ];
                    }
                }

                if (!empty($insertData)) {
                    WritingAssistantWord::insertOrIgnore($insertData);
                    $seeded += count($insertData);
                }
            }
            DB::commit();
            $this->command->info("Successfully seeded {$seeded} of {$total} words for language: {$lang}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Failed to seed words: " . $e->getMessage());
        }
    }
}
