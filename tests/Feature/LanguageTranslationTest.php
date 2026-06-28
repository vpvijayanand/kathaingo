<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;

class LanguageTranslationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the language switcher route sets session locale and redirects.
     */
    public function test_language_switch_route(): void
    {
        // Switch to English
        $response = $this->get(route('lang.switch', 'en'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        // Switch back to Tamil
        $response = $this->get(route('lang.switch', 'ta'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ta');
    }

    /**
     * Test that SetLocale middleware applies session locale to the app locale.
     */
    public function test_locale_middleware_applies_locale(): void
    {
        // Set session locale to en
        $response = $this->withSession(['locale' => 'en'])->get('/');
        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());

        // Set session locale to ta
        $response = $this->withSession(['locale' => 'ta'])->get('/');
        $response->assertStatus(200);
        $this->assertEquals('ta', app()->getLocale());
    }

    /**
     * Test that saving a post automatically triggers Google Translate API and saves English columns.
     */
    public function test_post_automatically_translates_on_save(): void
    {
        // Fake Google Translate HTTP response
        Http::fake([
            'translate.googleapis.com/*' => Http::sequence()
                ->push([[["Translated Title", "வணக்கம் தலைப்பு", null, null, 1]]], 200)
                ->push([[["Translated Content", "<p>வணக்கம் உள்ளடக்கம்</p>", null, null, 1]]], 200)
        ]);

        $user = User::factory()->create();
        $category = new Category([
            'name' => 'சமூக அரசியல்',
            'slug' => 'social-politics',
            'name_en' => 'Social Politics'
        ]);
        $category->saveQuietly();

        $post = new Post([
            'title' => 'வணக்கம் தலைப்பு',
            'content' => '<p>வணக்கம் உள்ளடக்கம்</p>',
            'slug' => 'vanakkam-thalaippu',
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published'
        ]);
        $post->save();

        // Verify translations are saved in the database
        $this->assertEquals('Translated Title', $post->title_en);
        $this->assertEquals('<p>Translated Content</p>', $post->content_en);
    }

    /**
     * Test that Eloquent accessors return English content dynamically when locale is set to 'en'.
     */
    public function test_accessors_return_correct_fields_based_on_locale(): void
    {
        $user = User::factory()->create();
        $category = new Category([
            'name' => 'சமூக அரசியல்',
            'slug' => 'social-politics',
            'name_en' => 'Social Politics'
        ]);
        $category->saveQuietly();

        $post = new Post([
            'title' => 'வணக்கம் தலைப்பு',
            'content' => '<p>வணக்கம் உள்ளடக்கம்</p>',
            'slug' => 'vanakkam-thalaippu',
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'title_en' => 'English Title',
            'content_en' => '<p>English Content</p>'
        ]);
        
        // Save quietly to prevent observer from overwriting our manually set English translations
        $post->saveQuietly();

        // When locale is Tamil (default)
        app()->setLocale('ta');
        $this->assertEquals('வணக்கம் தலைப்பு', $post->title);
        $this->assertEquals('<p>வணக்கம் உள்ளடக்கம்</p>', $post->content);

        // When locale is English
        app()->setLocale('en');
        $this->assertEquals('English Title', $post->title);
        $this->assertEquals('<p>English Content</p>', $post->content);
    }
}
