<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Helpers\SettingHelper;
use App\Services\LanguageHelperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test style detection in LanguageHelperService.
     */
    public function test_language_helper_service_detects_style(): void
    {
        $service = new LanguageHelperService();

        // Tanglish detection
        $this->assertEquals('tanglish', $service->detectStyle('naan unga article ah romba rasichen'));
        $this->assertEquals('tanglish', $service->detectStyle('romba nalla ezhuthi irukkeenga'));

        // Tamil script detection
        $this->assertEquals('tamil', $service->detectStyle('எனக்கு பிடிச்சிருக்கு'));
        $this->assertEquals('tamil', $service->detectStyle('ஐ லவ் யூ')); // Tamil script phonetic English

        // English script detection
        $this->assertEquals('english', $service->detectStyle('I really loved your article'));
        $this->assertEquals('english', $service->detectStyle('This is a great story'));

        // Mixed script detection
        $this->assertEquals('mixed', $service->detectStyle('This article ரொம்ப நல்லா இருக்கு'));
    }

    /**
     * Test transliteration of Tanglish to Tamil.
     */
    public function test_language_helper_service_transliterates_tanglish(): void
    {
        $service = new LanguageHelperService();

        // Exact dictionary mapping tests
        $this->assertEquals('நான்', $service->suggest('naan'));
        $this->assertEquals('ரொம்ப', $service->suggest('romba'));
        $this->assertEquals('எனக்கு பிடிச்சிருக்கு', $service->suggest('enakku pidichirukku'));

        // Mixed / Phonetic tests preserving English words
        $this->assertEquals('நான் உங்கள் article-ஐ ரொம்ப ரசிச்சேன்', $service->suggest('naan unga article ah romba rasichen'));
        $this->assertEquals('ரொம்ப நல்லா எழுதி இருக்கீங்க', $service->suggest('romba nalla ezhuthi irukkeenga'));
    }

    /**
     * Test script conversion of Tamil-script English.
     */
    public function test_language_helper_service_transliterates_tamil_english(): void
    {
        $service = new LanguageHelperService();

        // Tamil-script English conversion to proper English
        $this->assertEquals('I love you', $service->suggest('ஐ லவ் யூ'));
        $this->assertEquals('I love this article', $service->suggest('ஐ லவ் திஸ் ஆர்டிக்கிள்'));
    }

    /**
     * Test that administrators can view and update the language helper setting.
     */
    public function test_admin_can_toggle_language_helper_setting(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true
        ]);

        $visitor = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true
        ]);

        // Visitor is unauthorized to view settings
        $response = $this->actingAs($visitor)->get(route('admin.settings.edit'));
        $response->assertStatus(403);

        // Admin can view settings
        $response = $this->actingAs($admin)->get(route('admin.settings.edit'));
        $response->assertStatus(200);
        $response->assertSee('அமைப்புகள்');

        // Admin can disable setting
        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            // leaving global_language_helper_enabled empty means disabled
        ]);
        $response->assertRedirect(route('admin.settings.edit'));
        $this->assertEquals('0', SettingHelper::get('global_language_helper_enabled'));

        // Admin can enable setting
        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'global_language_helper_enabled' => '1'
        ]);
        $response->assertRedirect(route('admin.settings.edit'));
        $this->assertEquals('1', SettingHelper::get('global_language_helper_enabled'));
    }

    /**
     * Test the API suggest endpoint when the setting is enabled.
     */
    public function test_api_returns_suggestions_when_setting_is_enabled(): void
    {
        SettingHelper::set('global_language_helper_enabled', '1');

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'text' => 'naan unga article ah romba rasichen'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected' => 'tanglish',
            'suggestion' => 'நான் உங்கள் article-ஐ ரொம்ப ரசிச்சேன்'
        ]);
    }

    /**
     * Test the API suggest endpoint when the setting is disabled.
     */
    public function test_api_returns_null_when_setting_is_disabled(): void
    {
        SettingHelper::set('global_language_helper_enabled', '0');

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'text' => 'naan unga article ah romba rasichen'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'detected' => null,
            'suggestion' => null
        ]);
    }

    /**
     * Test the API suggest endpoint when requesting word candidates and settings are enabled.
     */
    public function test_api_returns_candidates_for_word_when_setting_is_enabled(): void
    {
        SettingHelper::set('global_language_helper_enabled', '1');

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'naan'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('candidates', $data);
        $this->assertContains('நான்', $data['candidates']);
    }

    public function test_api_returns_empty_candidates_when_setting_is_disabled(): void
    {
        SettingHelper::set('global_language_helper_enabled', '0');

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'naan'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'candidates' => []
        ]);
    }

    /**
     * Test the candidate generator prioritizations and English loan words retention.
     */
    public function test_api_prioritizes_spoken_tamil_and_loan_words(): void
    {
        SettingHelper::set('global_language_helper_enabled', '1');

        // Spoken Tamil prioritization check (padichen -> படிச்சேன் first, then படித்தேன்)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'padichen'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertGreaterThan(1, count($candidates));
        $this->assertEquals('படிச்சேன்', $candidates[0]);
        $this->assertEquals('படித்தேன்', $candidates[1]);

        // English loan words priority check (article -> article, then transliterated/translated)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'article'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('article', $candidates[0]);
        $this->assertEquals('ஆர்டிக்கிள்', $candidates[1]);

        // Standard dictionary check (panni -> பண்ணி, பன்னி)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'panni'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பண்ணி', $candidates[0]);
        $this->assertEquals('பன்னி', $candidates[1]);

        // Mappings for custom test words
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'anbulla'
        ]);
        $this->assertContains('அன்புள்ள', $response->json('candidates'));
        $this->assertContains('அன்புல்ல', $response->json('candidates'));

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'eluthalare'
        ]);
        $this->assertContains('எழுத்தாளரே', $response->json('candidates'));
        $this->assertContains('எழுதுபவரே', $response->json('candidates'));

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'naam'
        ]);
        $this->assertContains('நாம்', $response->json('candidates'));

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'rendu'
        ]);
        $this->assertContains('இரண்டு', $response->json('candidates'));
        $this->assertContains('ரெண்டு', $response->json('candidates'));

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'serndhu'
        ]);
        $this->assertContains('சேர்ந்து', $response->json('candidates'));
        $this->assertContains('செர்ந்து', $response->json('candidates'));
    }

    /**
     * Test the suggestions for long vowel / spoken Tamil handling.
     */
    public function test_api_handles_long_vowels_and_spoken_tamil(): void
    {
        SettingHelper::set('global_language_helper_enabled', '1');

        // adei -> அடேய் (first option), அடெய் (second), அடெஇ (third)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'adei'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('அடேய்', $candidates[0]);
        $this->assertEquals('அடெய்', $candidates[1]);
        $this->assertEquals('அடெஇ', $candidates[2]);

        // adaey -> அடேய் (first option)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'adaey'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('அடேய்', $candidates[0]);
        $this->assertEquals('அடெய்', $candidates[1]);
        $this->assertEquals('அடெஇ', $candidates[2]);

        // adey -> அதே / அடே options
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'adey'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertContains('அதே', $candidates);
        $this->assertContains('அடே', $candidates);

        // adae -> அடே
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'adae'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertContains('அடே', $candidates);

        // irukken -> இருக்கேன் / இருக்கென்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'irukken'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இருக்கேன்', $candidates[0]);
        $this->assertContains('இருக்கென்', $candidates);

        // irukkaen -> இருக்கேன்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'irukkaen'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இருக்கேன்', $candidates[0]);

        // Stage 2: indha -> இந்த
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'indha'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இந்த', $candidates[0]);

        // Stage 2: inge -> இங்கே
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'inge'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இங்கே', $candidates[0]);

        // Stage 2: ivan -> இவன்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ivan'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இவன்', $candidates[0]);

        // Stage 3: aah -> ஆ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'aah'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஆ', $candidates[0]);

        // Stage 3: kaadhal -> காதல்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'kaadhal'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('காதல்', $candidates[0]);

        // Stage 3: paavam -> பாவம்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'paavam'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பாவம்', $candidates[0]);

        // Stage 3: vaanga -> வாங்க
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vaanga'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வாங்க', $candidates[0]);

        // Stage 4: nee -> நீ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'nee'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('நீ', $candidates[0]);

        // Stage 4: veedu -> வீடு
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'veedu'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வீடு', $candidates[0]);

        // Stage 4: thee -> தீ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'thee'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('தீ', $candidates[0]);

        // Stage 5: ungal -> உங்கள்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ungal'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('உங்கள்', $candidates[0]);

        // Stage 5: udan -> உடன்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'udan'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('உடன்', $candidates[0]);

        // Stage 6: poo -> பூ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'poo'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பூ', $candidates[0]);

        // Stage 6: ooru -> ஊர்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ooru'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஊர்', $candidates[0]);
        $this->assertContains('ஊரு', $candidates);

        // Stage 7: enna -> என்ன
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'enna'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('என்ன', $candidates[0]);

        // Stage 7: enge -> எங்கே
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'enge'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('எங்கே', $candidates[0]);

        // Stage 8: vaera -> வேற
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vaera'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வேற', $candidates[0]);

        // Stage 8: paeru -> பேரு
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'paeru'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பேரு', $candidates[0]);

        // Stage 9: paiyan -> பையன்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'paiyan'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பையன்', $candidates[0]);

        // Stage 9: vai -> வை
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vai'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வை', $candidates[0]);

        // Stage 10: oru -> ஒரு
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'oru'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஒரு', $candidates[0]);

        // Stage 10: ondru -> ஒன்று
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ondru'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஒன்று', $candidates[0]);

        // Stage 11: poonga -> போங்க
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'poonga'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('போங்க', $candidates[0]);

        // Stage 11: ponga -> போங்க
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ponga'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('போங்க', $candidates[0]);

        // Stage 1.5: auvai -> ஔவை
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'auvai'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஔவை', $candidates[0]);

        // Stage 1.5: avvai -> ஔவை
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'avvai'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஔவை', $candidates[0]);

        // Stage 1.5: kauravam -> கௌரவம்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'kauravam'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('கௌரவம்', $candidates[0]);

        // Stage 1.5: mounam -> மௌனம் (with correct ranking)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'mounam'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('மௌனம்', $candidates[0]);
        $this->assertEquals('மோனம்', $candidates[1]);
        $this->assertEquals('மொனம்', $candidates[2]);

        // Stage 14: fan -> ஃபேன்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'fan'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபேன்', $candidates[0]);

        // Stage 14: file -> ஃபைல்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'file'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபைல்', $candidates[0]);

        // Stage 14: coffee -> காஃபி
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'coffee'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('காஃபி', $candidates[0]);

        // Stage 14: office -> ஆஃபிஸ்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'office'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஆஃபிஸ்', $candidates[0]);

        // Stage 14: phone -> ஃபோன் (with correct ranking)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'phone'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபோன்', $candidates[0]);
        $this->assertEquals('போன்', $candidates[1]);

        // Stage 15: japan -> ஜப்பான்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'japan'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜப்பான்', $candidates[0]);

        // Stage 15: jam -> ஜாம்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'jam'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜாம்', $candidates[0]);

        // Stage 15: judge -> ஜட்ஜ்
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'judge'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜட்ஜ்', $candidates[0]);

        // Stage 15: ja -> ஜ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ja'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜ', $candidates[0]);

        // Stage 15: za -> ஜ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'za'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜ', $candidates[0]);

        // Stage 15: j -> ஜ
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'j'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜ', $candidates[0]);

        // Stage 15: zoya -> ஜோயா
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'zoya'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஜோயா', $candidates[0]);

        // Stage 12: ஃ (Aytham) Family
        foreach (['ahdhu', 'akhdhu', 'aqdhu'] as $w) {
            $response = $this->postJson(route('api.language-helper.suggest'), [
                'word' => $w
            ]);
            $response->assertStatus(200);
            $candidates = $response->json('candidates');
            $this->assertEquals('அஃது', $candidates[0]);
        }

        foreach (['ehgu', 'ekhgu', 'eqgu'] as $w) {
            $response = $this->postJson(route('api.language-helper.suggest'), [
                'word' => $w
            ]);
            $response->assertStatus(200);
            $candidates = $response->json('candidates');
            $this->assertEquals('எஃகு', $candidates[0]);
        }

        foreach (['ahrinai', 'akhrinai', 'aqrinai'] as $w) {
            $response = $this->postJson(route('api.language-helper.suggest'), [
                'word' => $w
            ]);
            $response->assertStatus(200);
            $candidates = $response->json('candidates');
            $this->assertEquals('அஃறிணை', $candidates[0]);
        }

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'q'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃ', $candidates[0]);

        // Stage 16: ஃஸ Family
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'school'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஸ்கூல்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'station'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஸ்டேஷன்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'status'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஸ்டேட்டஸ்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'sa'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஸ', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 's'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஸ', $candidates[0]);

        // Stage 17: ஷ Family
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'shiva'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertContains('சிவா', $candidates);
        $this->assertContains('ஷிவா', $candidates);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'shankar'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertContains('சங்கர்', $candidates);
        $this->assertContains('ஷங்கர்', $candidates);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'sha'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஷ', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'sh'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஷ', $candidates[0]);

        // Stage 18: ஹ Family
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'hotel'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஹோட்டல்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'hello'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஹலோ', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'ha'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஹ', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'h'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஹ', $candidates[0]);

        // Stage 19: ou Patterns (house, mouse, mount, sound, found)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'sound'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('சௌண்ட்', $candidates[0]);
        $this->assertEquals('சவுண்ட்', $candidates[1]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'mount'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('மவுண்ட்', $candidates[0]);
        $this->assertEquals('மௌண்ட்', $candidates[1]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'house'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஹவுஸ்', $candidates[0]);
        $this->assertEquals('ஹௌஸ்', $candidates[1]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'mouse'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('மவுஸ்', $candidates[0]);
        $this->assertEquals('மௌஸ்', $candidates[1]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'found'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபவுண்ட்', $candidates[0]);
        $this->assertEquals('ஃபௌண்ட்', $candidates[1]);
        $this->assertEquals('பவுண்ட்', $candidates[2]);

        // Stage 20: Modern Loan Words (facebook, film, fashion)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'facebook'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபேஸ்புக்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'film'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபிலிம்', $candidates[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'fashion'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ஃபேஷன்', $candidates[0]);

        // Stage 21: Romba Pattern
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'romba'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('ரொம்ப', $candidates[0]);
        $this->assertNotEquals('ரொம்பா', $candidates[0]);

        // Stage 22: Irukken Pattern
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'irukaen'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இருக்கேன்', $candidates[0]);
        $this->assertEquals('இருக்கென்', $candidates[1]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'iruken'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('இருக்கேன்', $candidates[0]);
        $this->assertEquals('இருக்கென்', $candidates[1]);

        // Stage 23: Serndhu Pattern
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'serndhu'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('சேர்ந்து', $candidates[0]);
        $this->assertEquals('செர்ந்து', $candidates[1]);

        // Stage 3 — Tamil Consonant Ambiguity (pani -> பணி / பனி)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'pani'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('பணி', $candidates[0]);
        $this->assertEquals('பனி', $candidates[1]);

        // Stage 4 — Tamil Consonant Ambiguity (maram -> மரம் / மறம்)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'maram'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('மரம்', $candidates[0]);
        $this->assertEquals('மறம்', $candidates[1]);

        // Stage 4 — Comment Section Dictionary
        $words = [
            'anbulla' => 'அன்புள்ள',
            'eluthalar' => 'எழுத்தாளர்',
            'katturai' => 'கட்டுரை',
            'kadhai' => 'கதை',
            'padichen' => 'படிச்சேன்',
            'pidichirukku' => 'பிடிச்சிருக்கு',
            'rasichen' => 'ரசிச்சேன்',
            'super' => 'சூப்பர்'
        ];

        foreach ($words as $word => $tamil) {
            $response = $this->postJson(route('api.language-helper.suggest'), [
                'word' => $word
            ]);
            $response->assertStatus(200);
            $candidates = $response->json('candidates');
            $this->assertEquals($tamil, $candidates[0]);
        }
    }

    /**
     * Test transliteration of target lyrics words.
     */
    public function test_api_lyrics_transliteration(): void
    {
        SettingHelper::set('global_language_helper_enabled', '1');

        $lyricsWords = [
            'manjakattu' => 'மஞ்சக்காட்டு',
            'manjakkattu' => 'மஞ்சக்காட்டு',
            'manjakkaattu' => 'மஞ்சக்காட்டு',
            'maina' => 'மைனா',
            'mainaa' => 'மைனா',
            'manina' => 'மைனா',
            'maninaa' => 'மைனா',
            'ennai' => 'என்னை',
            'ennaik' => 'என்னைக்',
            'konji' => 'கொஞ்சி',
            'konjik' => 'கொஞ்சிக்',
            'konjip' => 'கொஞ்சிப்',
            'pona' => 'போன',
            'ponaa' => 'போனா',
            'mustafa' => 'முஸ்தஃபா',
            'mustafaa' => 'முஸ்தஃபா',
            'mustafah' => 'முஸ்தஃபா',
            'musthafaa' => 'முஸ்தஃபா',
            'musthafaah' => 'முஸ்தஃபா',
            'dont' => 'டோன்ட்',
            'vory' => 'வொரி',
            'tholan' => 'தோழன்',
            'thozhan' => 'தோழன்',
            'moolgaatha' => 'மூழ்காத',
            'moolgatha' => 'மூழ்காத',
            'moozhgaatha' => 'மூழ்காத',
            'moozhgaadha' => 'மூழ்காத',
            'moolgaada' => 'மூழ்காத',
            'moolkaadha' => 'மூழ்காத',
            'moolkaatha' => 'மூழ்காத',
            'moozhgatha' => 'மூழ்காத',
            'moozhgadha' => 'மூழ்காத',
            'moozhgada' => 'மூழ்காத',
            'moolgada' => 'மூழ்காத',
            'friendshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
            'frendshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
            'frandshippaa' => 'ஃப்ரண்ட்ஷிப்பா',
            'friendship' => 'ஃப்ரெண்ட்ஷிப்',
            'frendship' => 'ஃப்ரெண்ட்ஷிப்',
            'frandship' => 'ஃப்ரெண்ட்ஷிப்',
        ];

        foreach ($lyricsWords as $tanglish => $tamil) {
            $response = $this->postJson(route('api.language-helper.suggest'), [
                'word' => $tanglish
            ]);
            $response->assertStatus(200);
            $candidates = $response->json('candidates');
            $this->assertContains($tamil, $candidates);
            // Ensure target Tamil word is the first option
            $this->assertEquals($tamil, $candidates[0]);
        }

        // Assert valukkaatha variations (வலுக்காத, வளுக்காத, வழுக்காத) in strict priority order
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'valukkaatha'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வலுக்காத', $candidates[0]);
        $this->assertEquals('வளுக்காத', $candidates[1]);
        $this->assertEquals('வழுக்காத', $candidates[2]);

        // Assert valukkaada variations (வலுக்காட, வளுக்காட, வழுக்காட) in strict priority order
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'valukkaada'
        ]);
        $response->assertStatus(200);
        $candidates = $response->json('candidates');
        $this->assertEquals('வலுக்காட', $candidates[0]);
        $this->assertEquals('வளுக்காட', $candidates[1]);
        $this->assertEquals('வழுக்காட', $candidates[2]);

        // Assert curry leaf (kariveppila / kariveppilai) maps to கறிவேப்பில / கறிவேப்பிலை
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'kariveppila'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('கறிவேப்பில', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'kariveppilai'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('கறிவேப்பிலை', $response->json('candidates')[0]);

        // Assert neem leaf (veppila / veppilai) maps to வேப்பில / வேப்பிலை
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'veppila'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('வேப்பில', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'veppilai'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('வேப்பிலை', $response->json('candidates')[0]);

        // Assert general e -> ae rule (e.g. pena -> paena -> பேனா candidate)
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'pena'
        ]);
        $response->assertStatus(200);
        $this->assertContains('பேனா', $response->json('candidates'));

        // Assert take and tak map to டேக் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'take'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('டேக்', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'tak'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('டேக்', $response->json('candidates')[0]);

        // Assert it maps to இட் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'it'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('இட்', $response->json('candidates')[0]);

        // Assert vottu maps to வோட்டு as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vottu'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('வோட்டு', $response->json('candidates')[0]);

        // Assert poda contains போட
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'poda'
        ]);
        $response->assertStatus(200);
        $this->assertContains('போட', $response->json('candidates'));

        // Assert pothu maps to போது as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'pothu'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('போது', $response->json('candidates')[0]);

        // Assert nyayiru and gnayiru map to ஞாயிறு as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'nyayiru'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாயிறு', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'gnayiru'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாயிறு', $response->json('candidates')[0]);

        // Assert nyabagam and gnabagam map to ஞாபகம் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'nyabagam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாபகம்', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'gnabagam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாபகம்', $response->json('candidates')[0]);

        // Assert nyaayiru and gnyaayiru map to ஞாயிறு as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'nyaayiru'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாயிறு', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'gnyaayiru'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாயிறு', $response->json('candidates')[0]);

        // Assert nyaabagam and gnyaabagam map to ஞாபகம் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'nyaabagam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாபகம்', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'gnyaabagam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('ஞாபகம்', $response->json('candidates')[0]);

        // Assert vingyaanam and vingyanam map to விஞ்ஞானம் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vingyaanam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('விஞ்ஞானம்', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'vingyanam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('விஞ்ஞானம்', $response->json('candidates')[0]);

        // Assert angyaanam and angyanam map to அஞ்ஞானம் as the first candidate
        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'angyaanam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('அஞ்ஞானம்', $response->json('candidates')[0]);

        $response = $this->postJson(route('api.language-helper.suggest'), [
            'word' => 'angyanam'
        ]);
        $response->assertStatus(200);
        $this->assertEquals('அஞ்ஞானம்', $response->json('candidates')[0]);
    }

    /**
     * Test transliteration of special dictionary words (system, sri, synthesis) and Sanskrit/Grantha consonant mappings (ksh).
     */
    public function test_sanskrit_and_dictionary_transliteration(): void
    {
        $service = new LanguageHelperService();

        // 1. Dictionary and English-preserved exceptions
        $this->assertContains('சிஸ்டம்', $service->getCandidates('system'));
        $this->assertContains('சிஸ்டம்', $service->getCandidates('sistam'));
        $this->assertContains('ஸ்ரீ', $service->getCandidates('sri'));
        $this->assertContains('ஸ்ரீ', $service->getCandidates('shri'));
        $this->assertContains('சிந்தஸிஸ்', $service->getCandidates('synthesis'));
        $this->assertContains('சின்தஸிஸ்', $service->getCandidates('synthesis'));
        $this->assertContains('சிந்தஸிஸ்', $service->getCandidates('sinthasis'));
        $this->assertContains('சின்தஸிஸ்', $service->getCandidates('sinthasis'));
        $this->assertContains('சிந்தஸிஸ்', $service->getCandidates('sinthesis'));
        $this->assertContains('சின்தஸிஸ்', $service->getCandidates('sinthesis'));

        // 2. Core phonetic translation for Sanskrit ksh
        $this->assertContains('லக்ஷ்மி', $service->getCandidates('lakshmi'));
        $this->assertContains('அக்ஷய', $service->getCandidates('akshaya'));

        // 3. Candidate order verification for 'poi'
        $poiCandidates = $service->getCandidates('poi');
        $this->assertEquals('போய்', $poiCandidates[0]);
        $this->assertEquals('பொய்', $poiCandidates[1]);
        $this->assertEquals('போயி', $poiCandidates[2]);

        // 4. Candidate verification for short/long 'o' combinations
        $soCandidates = $service->getCandidates('so');
        $this->assertContains('சோ', $soCandidates);
        $this->assertContains('சொ', $soCandidates);

        $noCandidates = $service->getCandidates('no');
        $this->assertContains('நோ', $noCandidates);
        $this->assertContains('நொ', $noCandidates);

        $goCandidates = $service->getCandidates('go');
        $this->assertContains('கோ', $goCandidates);
        $this->assertContains('கொ', $goCandidates);

        $doCandidates = $service->getCandidates('do');
        $this->assertContains('டோ', $doCandidates);
        $this->assertContains('டொ', $doCandidates);

        $toCandidates = $service->getCandidates('to');
        $this->assertContains('டோ', $toCandidates);
        $this->assertContains('டொ', $toCandidates);

        // 5. Colloquial past-tense suffix combinations and 'nth' mapping verification
        $irunthichuCandidates = $service->getCandidates('irunthichu');
        $this->assertContains('இருந்திச்சு', $irunthichuCandidates);
        $this->assertContains('இருந்துச்சு', $irunthichuCandidates);
        $this->assertContains('இருந்திச்சி', $irunthichuCandidates);

        $vanthichuCandidates = $service->getCandidates('vanthichu');
        $this->assertContains('வந்திச்சு', $vanthichuCandidates);
        $this->assertContains('வந்துச்சு', $vanthichuCandidates);
        $this->assertContains('வந்திச்சி', $vanthichuCandidates);

        $santhichuCandidates = $service->getCandidates('santhichu');
        $this->assertContains('சந்திச்சு', $santhichuCandidates);
        $this->assertContains('சந்திச்சி', $santhichuCandidates);

        // 'nth' mapping in standard words
        $santhiCandidates = $service->getCandidates('santhi');
        $this->assertContains('சந்தி', $santhiCandidates);
    }




}
