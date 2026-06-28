<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WritingAssistantWord;
use App\Models\PersonalDictionary;
use App\Models\CommunitySuggestedWord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WritingAssistantTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an approved and verified user
        $this->user = User::factory()->create([
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test block spelling validation and suggestions.
     */
    public function test_spellcheck_detects_misspelled_words_and_provides_suggestions(): void
    {
        // Seed correct dictionary words
        WritingAssistantWord::create(['word' => 'மரம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'அறம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'தோட்டத்தில்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'உள்ளது', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'hello', 'language' => 'en']);

        // Check text with typos
        // "மறம்" is not seeded, so it's flagged. "மரம்" is closest.
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'தோட்டத்தில் மறம் உள்ளது',
                'language' => 'ta'
            ]);

        $response->assertOk()
            ->assertJsonPath('language', 'ta');

        $data = $response->json();
        $this->assertCount(1, $data['matches']);
        $match = $data['matches'][0];
        
        $this->assertEquals('spelling', $match['type']);
        $this->assertEquals('மறம்', $match['text']);
        $this->assertContains('மரம்', $match['suggestions']);
    }

    /**
     * Test token exclusions (URLs, Emails, Hashtags, Mentions, Numbers, Code).
     */
    public function test_spellcheck_filters_out_special_tokens(): void
    {
        // Seed the natural words in the sentence so they aren't flagged as typos
        WritingAssistantWord::create(['word' => 'Check', 'language' => 'en']);
        WritingAssistantWord::create(['word' => 'and', 'language' => 'en']);
        WritingAssistantWord::create(['word' => 'with', 'language' => 'en']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'Check http://google.com and admin@example.com with #tag and @user and 12.34 and snake_case_var and camelCaseVar',
                'language' => 'en'
            ]);

        $response->assertOk();
        $data = $response->json();
        
        // Assert no spelling errors are reported for these ignored tokens
        $spellingErrors = array_filter($data['matches'], fn($m) => $m['type'] === 'spelling');
        $this->assertCount(0, $spellingErrors);
    }

    /**
     * Test punctuation checker rules.
     */
    public function test_punctuation_rules_flag_repeated_punctuation_and_missing_spaces(): void
    {
        // 1. Repeated punctuation
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'வணக்கம்,, எப்படி உள்ளீர்கள்!!',
                'language' => 'ta'
            ]);

        $response->assertOk();
        $data = $response->json();

        $repeatedPuncMatches = array_filter($data['matches'], fn($m) => str_contains($m['message'], 'repeated punctuation'));
        $this->assertGreaterThanOrEqual(1, count($repeatedPuncMatches));

        // 2. Missing spaces
        $response2 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'வணக்கம்.எப்படி உள்ளீர்கள்?',
                'language' => 'ta'
            ]);

        $response2->assertOk();
        $data2 = $response2->json();

        $missingSpaceMatches = array_filter($data2['matches'], fn($m) => str_contains($m['message'], 'Space required'));
        $this->assertCount(1, $missingSpaceMatches);
        $this->assertEquals('.எ', $missingSpaceMatches[0]['text']);
        $this->assertEquals('. எ', $missingSpaceMatches[0]['suggestions'][0]);

        // 3. Space before punctuation (Rule D)
        $response3 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'எழுதிக் கொல்வது  .',
                'language' => 'ta'
            ]);

        $response3->assertOk();
        $data3 = $response3->json();

        $spaceBeforeMatches = array_filter($data3['matches'], fn($m) => str_contains($m['message'], 'Avoid space before'));
        $this->assertCount(1, $spaceBeforeMatches);
        $this->assertEquals('  .', $spaceBeforeMatches[0]['text']);
        $this->assertEquals('.', $spaceBeforeMatches[0]['suggestions'][0]);
    }

    /**
     * Test unmatched brackets rule.
     */
    public function test_brackets_matching_rule(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'இது (ஒரு தவறான அடைப்புக்குறி வாக்கியம்',
                'language' => 'ta'
            ]);

        $response->assertOk();
        $data = $response->json();

        $bracketMatches = array_filter($data['matches'], fn($m) => str_contains($m['message'], 'bracket'));
        $this->assertCount(1, $bracketMatches);
    }

    /**
     * Test writing style / coach rules.
     */
    public function test_writing_style_rules(): void
    {
        // 1. Repeated word check
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'இது இது மிகவும் நன்று.',
                'language' => 'ta'
            ]);

        $response->assertOk();
        $data = $response->json();
        
        $repeatedWordMatches = array_filter($data['matches'], fn($m) => str_contains($m['message'], 'Repeated word'));
        $this->assertCount(1, $repeatedWordMatches);
        $this->assertEquals('இது இது', $repeatedWordMatches[0]['text']);
        $this->assertEquals('இது', $repeatedWordMatches[0]['suggestions'][0]);

        // 2. Multiple spaces check
        $response2 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'வணக்கம்  நண்பா', // two spaces
                'language' => 'ta'
            ]);

        $response2->assertOk();
        $data2 = $response2->json();

        $spaceMatches = array_filter($data2['matches'], fn($m) => str_contains($m['message'], 'consecutive spaces'));
        $this->assertCount(1, $spaceMatches);

        // 3. Excessively long sentence (>25 words)
        $longSentenceText = 'வார்த்தை ஒன்று இரண்டு மூன்று நான்கு ஐந்து ஆறு ஏழு எட்டு ஒன்பது பத்து பதினொன்று பன்னிரண்டு பதிமூன்று பதினான்கு பதினைந்து பதினாறு பதினேழு பதினெட்டு பத்தொன்பது இருபது இருபத்தொன்று இருபத்திரண்டு இருபத்திமூன்று இருபத்தினான்கு இருபத்தைந்து இருபத்தாறு.';
        $response3 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => $longSentenceText,
                'language' => 'ta'
            ]);

        $response3->assertOk();
        $data3 = $response3->json();

        $longSentenceMatches = array_filter($data3['matches'], fn($m) => str_contains($m['message'], 'Consider splitting'));
        $this->assertCount(1, $longSentenceMatches);
    }

    /**
     * Test adding words to the personal dictionary and verifying they are no longer flagged.
     */
    public function test_personal_dictionary_integration(): void
    {
        $word = 'கதைங்கோ'; // custom branding word, not in main dictionary
        WritingAssistantWord::create(['word' => 'தளம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'கதைங்க', 'language' => 'ta']);

        // 1. Check spelling, should flag as typo
        $response1 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'கதைங்கோ தளம்',
                'language' => 'ta'
            ]);

        $response1->assertOk();
        $data1 = $response1->json();
        $this->assertCount(1, array_filter($data1['matches'], fn($m) => $m['type'] === 'spelling'));

        // 2. Add to personal dictionary
        $response2 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/dictionary/add', [
                'word' => $word,
                'language' => 'ta'
            ]);

        $response2->assertOk()->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('personal_dictionaries', [
            'user_id' => $this->user->id,
            'word' => $word,
            'language' => 'ta'
        ]);

        // 3. Check spelling again, should be accepted now
        $response3 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'கதைங்கோ தளம்',
                'language' => 'ta'
            ]);

        $response3->assertOk();
        $data3 = $response3->json();
        $this->assertCount(0, array_filter($data3['matches'], fn($m) => $m['type'] === 'spelling'));
    }

    /**
     * Test nominating a word for community review and tracking nomination count.
     */
    public function test_community_suggestion_nomination_and_increment(): void
    {
        $word = 'தமிழ்வாணி';

        // Nominate first time
        $response1 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/suggest-word', [
                'word' => $word,
                'language' => 'ta'
            ]);

        $response1->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('community_suggested_words', [
            'word' => $word,
            'language' => 'ta',
            'status' => 'pending',
            'nominations_count' => 1
        ]);

        // Nominate second time (same user or another, simulated here by repeating suggestion)
        $response2 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/suggest-word', [
                'word' => $word,
                'language' => 'ta'
            ]);

        $this->assertDatabaseHas('community_suggested_words', [
            'word' => $word,
            'language' => 'ta',
            'status' => 'pending',
            'nominations_count' => 2
        ]);
    }

    /**
     * Test conjoined word sandhi splitter, ZWNJ stripping, space normalization, and confusing words rules.
     */
    public function test_advanced_linguistic_rules_and_normalization(): void
    {
        // 1. Seed root words
        WritingAssistantWord::create(['word' => 'எழுதி', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நீங்கள்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'கொல்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'கொள்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நலம்', 'language' => 'ta']);

        // 2. Check compound conjoined word "எழுதிக்கொள்வது" (ends with "கொள்வது", sandhi "க்").
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'எழுதிக்கொள்வது',
                'language' => 'ta'
            ]);
        $response->assertOk();
        $this->assertCount(0, array_filter($response->json('matches'), fn($m) => $m['type'] === 'spelling'));

        // 3. Check trailing sandhi consonant "எழுதிக்" as a separate word.
        $responseSandhi = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'எழுதிக் கொள்வது',
                'language' => 'ta'
            ]);
        $responseSandhi->assertOk();
        $this->assertCount(0, array_filter($responseSandhi->json('matches'), fn($m) => $m['type'] === 'spelling'));

        // 4. Check verb inflection "கொல்வது" and "கொள்வது" as separate words.
        $responseInflection = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'கொல்வது கொள்வது',
                'language' => 'ta'
            ]);
        $responseInflection->assertOk();
        $this->assertCount(0, array_filter($responseInflection->json('matches'), fn($m) => $m['type'] === 'spelling'));

        // 5. Check ZWNJ (invisible char \u200C) in "நீங்க‌ள்" (written as நீங்க\u200Cள்). Should NOT flag as error because "நீங்கள்" is seeded.
        $response2 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => "நீங்க\u{200C}ள்",
                'language' => 'ta'
            ]);
        $response2->assertOk();
        $this->assertCount(0, array_filter($response2->json('matches'), fn($m) => $m['type'] === 'spelling'));

        // 6. Check confusing word rule "நாளம்" -> should suggest "நலம்" unconditionally.
        $response3 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'நாளம்',
                'language' => 'ta'
            ]);
        $response3->assertOk();
        $styleErrors = array_filter($response3->json('matches'), fn($m) => $m['type'] === 'style');
        $this->assertCount(1, $styleErrors);
        $this->assertEquals('நாளம்', $styleErrors[0]['text']);
        $this->assertEquals('நலம்', $styleErrors[0]['suggestions'][0]);

        // 7. Check non-breaking spaces (\x00A0) normalization inside consecutive spaces
        $response4 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => "நான்\u{00A0}\u{00A0}இங்கு", // two non-breaking spaces
                'language' => 'ta'
            ]);
        $response4->assertOk();
        $spaceErrors = array_filter($response4->json('matches'), fn($m) => str_contains($m['message'], 'spaces'));
        $this->assertCount(1, $spaceErrors);

        // 8. Check confusing homophone "கொல்வது" -> should suggest "கொள்வது"
        $response5 = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'எழுதிக் கொல்வது',
                'language' => 'ta'
            ]);
        $response5->assertOk();
        $homophoneErrors = array_filter($response5->json('matches'), fn($m) => $m['type'] === 'style' && $m['text'] === 'கொல்வது');
        $this->assertCount(1, $homophoneErrors);
        $this->assertEquals('கொள்வது', array_values($homophoneErrors)[0]['suggestions'][0]);
    }

    /**
     * Test learning correction validates and stores pending pair.
     */
    public function test_learn_correction_validates_and_stores_pending_pair(): void
    {
        // Seed the corrected word in the dictionary so it passes validity checks
        WritingAssistantWord::create(['word' => 'வாழ்த்திய', 'language' => 'ta']);

        // Set user role to author for trust level 2
        $this->user->update(['role' => 'author']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/learn-correction', [
                'original_text' => 'வால்தியா',
                'corrected_text' => 'வாழ்த்திய',
                'language' => 'ta',
                'count' => 1
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('writing_assistant_learnings', [
            'original_text' => 'வால்தியா',
            'corrected_text' => 'வாழ்த்திய',
            'language' => 'ta',
            'frequency' => 1,
            'writer_trust_level' => 2,
            'approval_status' => 'pending'
        ]);
    }

    /**
     * Test learning correction increments frequency and updates trust level.
     */
    public function test_learn_correction_increments_frequency_and_trust_level(): void
    {
        WritingAssistantWord::create(['word' => 'வாழ்த்திய', 'language' => 'ta']);

        // Insert initial learning record
        \App\Models\WritingAssistantLearning::create([
            'original_text' => 'வால்தியா',
            'corrected_text' => 'வாழ்த்திய',
            'language' => 'ta',
            'frequency' => 1,
            'writer_trust_level' => 1,
            'approval_status' => 'pending'
        ]);

        // Create an admin user (trust level 3)
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
            'is_approved' => true,
            'email_verified_at' => now()
        ]);

        $response = $this->actingAs($admin)
            ->postJson('/api/writing-assistant/learn-correction', [
                'original_text' => 'வால்தியா',
                'corrected_text' => 'வாழ்த்திய',
                'language' => 'ta',
                'count' => 5
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('writing_assistant_learnings', [
            'original_text' => 'வால்தியா',
            'corrected_text' => 'வாழ்த்திய',
            'language' => 'ta',
            'frequency' => 6,
            'writer_trust_level' => 3,
            'approval_status' => 'pending'
        ]);
    }

    /**
     * Test learning correction rejects still incorrect corrected_text.
     */
    public function test_learn_correction_rejects_still_incorrect_text(): void
    {
        // Seed a close word so "வாழ்திய்" has suggestions and gets flagged
        WritingAssistantWord::create(['word' => 'வாழ்தி', 'language' => 'ta']);

        // "வாழ்திய்" is not seeded and represents a spelling mistake
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/learn-correction', [
                'original_text' => 'வால்தியா',
                'corrected_text' => 'வாழ்திய்',
                'language' => 'ta',
                'count' => 1
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseMissing('writing_assistant_learnings', [
            'corrected_text' => 'வாழ்திய்'
        ]);
    }

    /**
     * Test Consistency Checker identifies and ranks spelling variants.
     */
    public function test_consistency_checker_identifies_and_ranks_spelling_variants(): void
    {
        // 1. Seed correct dictionary word "மனமார்ந்த" (preferred)
        WritingAssistantWord::create(['word' => 'மனமார்ந்த', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நன்றிகள்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'பின்னர்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நாம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'கூறுவோம்', 'language' => 'ta']);

        // Check text containing both "மனமார்ந்த" and its close misspelled variant "மனமர்ந்த"
        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/analyze-consistency', [
                'text' => 'மனமார்ந்த நன்றிகள். பின்னர் நாம் மனமர்ந்த நன்றிகள் கூறுவோம்.',
                'language' => 'ta'
            ]);

        $response->assertOk();
        $inconsistencies = $response->json('inconsistencies');
        $this->assertArrayHasKey('மனமர்ந்த', $inconsistencies);
        $this->assertEquals('மனமார்ந்த', $inconsistencies['மனமர்ந்த']['preferred']);
        $this->assertStringContainsString('மனமர்ந்த', $inconsistencies['மனமர்ந்த']['message']);
        $this->assertStringContainsString('மனமார்ந்த', $inconsistencies['மனமர்ந்த']['message']);

        // 2. Frequency ranking test: neither word is seeded, but one occurs more times.
        $responseFreq = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/analyze-consistency', [
                'text' => 'வார்த்தை மற்றும் வார்த்தை. பின்னர் வார்தை.',
                'language' => 'ta'
            ]);

        $responseFreq->assertOk();
        $inconsistenciesFreq = $responseFreq->json('inconsistencies');
        $this->assertArrayHasKey('வார்தை', $inconsistenciesFreq);
        $this->assertEquals('வார்த்தை', $inconsistenciesFreq['வார்தை']['preferred']);
    }

    /**
     * Test Style Coach rules for consecutive repeats, sentence lengths, and paragraphs.
     */
    public function test_style_coach_rules(): void
    {
        // 1. Triple repeated word check
        $responseRepeated = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => 'அவர் மிகவும் மிகவும் மிகவும் நல்லவர்',
                'language' => 'ta'
            ]);

        $responseRepeated->assertOk();
        $matchesRepeated = array_filter($responseRepeated->json('matches'), fn($m) => $m['type'] === 'style' && str_contains($m['text'], 'மிகவும் மிகவும் மிகவும்'));
        $this->assertCount(1, $matchesRepeated);
        $this->assertStringContainsString('தவிர்', array_values($matchesRepeated)[0]['message']);

        // 2. Softened sentence length check displaying exact word count (58 words)
        $longSentence = '';
        for ($w = 1; $w <= 58; $w++) {
            $longSentence .= "சொல்{$w} ";
        }
        $longSentence = trim($longSentence) . '.';
        $responseSentence = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => $longSentence,
                'language' => 'ta'
            ]);

        $responseSentence->assertOk();
        $matchesSentence = array_filter($responseSentence->json('matches'), fn($m) => $m['type'] === 'style' && str_contains($m['message'], '58 சொற்கள்'));
        $this->assertCount(1, $matchesSentence);

        // 3. Paragraph length check (>150 words)
        $longParagraphText = 'முதல் ' . str_repeat('வார்த்தை ', 158) . 'முடிவு.';
        $responseParagraph = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/check-block', [
                'text' => $longParagraphText,
                'language' => 'ta'
            ]);

        $responseParagraph->assertOk();
        $matchesParagraph = array_filter($responseParagraph->json('matches'), fn($m) => $m['type'] === 'style' && str_contains($m['message'], '160 சொற்கள்') && str_contains($m['message'], 'இப்பத்தியில்'));
        $this->assertCount(1, $matchesParagraph);
        // Warning should be placed on the first word of the paragraph ("முதல்")
        $this->assertEquals('முதல்', array_values($matchesParagraph)[0]['text']);
        $this->assertEquals(0, array_values($matchesParagraph)[0]['offset']);
    }

    /**
     * Test article-wide word overuse analysis.
     */
    public function test_article_wide_word_overuse_analysis(): void
    {
        // 10 times of "அருமை", plus 15 times of stopword "மற்றும்"
        $text = str_repeat('அருமை ', 10) . str_repeat('மற்றும் ', 15);
        $responseOveruse = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/analyze-consistency', [
                'text' => $text,
                'language' => 'ta'
            ]);

        $responseOveruse->assertOk();
        $overused = $responseOveruse->json('overused');
        
        // "அருமை" should be flagged as overused
        $this->assertArrayHasKey('அருமை', $overused);
        $this->assertEquals(10, $overused['அருமை']['count']);
        $this->assertStringContainsString('10 முறை', $overused['அருமை']['message']);
        
        // Stopword "மற்றும்" should NOT be flagged as overused
        $this->assertArrayNotHasKey('மற்றும்', $overused);
    }

    /**
     * Test Smart Review computes accurate dashboard counts and readability.
     */
    public function test_smart_review_computes_accurate_dashboard_counts(): void
    {
        // 1. Seed natural words
        WritingAssistantWord::create(['word' => 'விறகு', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'அடுப்பில்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'உள்ளது', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நல்லவர்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'அவர்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'செய்யுங்கள்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'இப்படி', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நன்றிகள்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'பின்னர்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'நாம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'கூறுவோம்', 'language' => 'ta']);
        WritingAssistantWord::create(['word' => 'hello', 'language' => 'ta']);

        // Build a text containing:
        // - 1 Spelling typo (வீறகு, suggestion விறகு)
        // - 1 Unknown word (hellllo, suggestion hello with edit distance 2 -> low confidence warning)
        // - 1 Grammar issue (இப்படி செய்யுங்கள் -> சந்திப் பிழை)
        // - 1 Style issue (அவர் நல்லவர் நல்லவர் -> repeated word)
        // - 1 Consistency issue (Seed "மனமார்ந்த" in dictionary, write "மனமார்ந்த" and "மனமர்ந்த")
        WritingAssistantWord::create(['word' => 'மனமார்ந்த', 'language' => 'ta']);

        $text = 'அடுப்பில் வீறகு உள்ளது. hellllo. இப்படி செய்யுங்கள். அவர் நல்லவர் நல்லவர். மனமார்ந்த நன்றிகள். பின்னர் நாம் மனமர்ந்த நன்றிகள் கூறுவோம்.';

        $response = $this->actingAs($this->user)
            ->postJson('/api/writing-assistant/review-article', [
                'text' => $text,
                'language' => 'ta'
            ]);

        $response->assertOk();
        $summary = $response->json('summary');
        
        $this->assertEquals(1, $summary['spelling']); // "வீறகு"
        $this->assertEquals(1, $summary['unknown']); // "hellllo" (low confidence warning)
        $this->assertEquals(1, $summary['grammar']); // "இப்படி செய்யுங்கள்"
        $this->assertEquals(1, $summary['consistency']); // "மனமர்ந்த" count
        
        // Readability check: average sentence length is small, so readability should be 'Easy' or 'Good'
        $this->assertNotEmpty($summary['readability']);
    }
}




