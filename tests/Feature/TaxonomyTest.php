<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MetadataType;
use App\Models\MetadataValue;
use App\Models\Post;
use App\Models\Series;
use App\Models\Subcategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyTest extends TestCase
{
    use RefreshDatabase;

    private User $author;
    private Subcategory $authorProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard author user
        $this->author = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true,
            'role' => 'author',
        ]);

        // Create parent category for writers
        $writersCategory = Category::create([
            'name' => 'Writers',
            'slug' => 'pathivargal',
            'order' => 1
        ]);

        // Create author subcategory profile linked to the user
        $this->authorProfile = Subcategory::create([
            'category_id' => $writersCategory->id,
            'name' => 'Writer One',
            'slug' => 'writer-one',
            'user_id' => $this->author->id,
            'email' => 'writer1@example.com'
        ]);

        // Seed the 15 main categories and metadata
        $this->seed(\Database\Seeders\TaxonomySeeder::class);
    }

    public function test_post_creation_with_new_taxonomy(): void
    {
        $this->actingAs($this->author);

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $series = Series::create([
            'title' => 'பயணத் தொடர்',
            'slug' => 'travel-series',
            'status' => 'active'
        ]);

        $metadataType = MetadataType::where('category_id', $travelCategory->id)
            ->where('slug', 'travel-type')
            ->firstOrFail();

        $familyTripValue = MetadataValue::where('metadata_type_id', $metadataType->id)
            ->where('slug', 'family-trip')
            ->firstOrFail();

        $response = $this->post(route('posts.store'), [
            'title' => 'குடும்பப் பயணம் ஒரு சாகசம்',
            'content' => 'நாங்கள் அனைவரும் சேர்ந்து குடும்பப் பயணம் மேற்கொண்டோம்.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'series_id' => $series->id,
            'volume' => 'Volume 1',
            'chapter_number' => 2,
            'metadata_value_ids' => [$familyTripValue->id],
            'tags' => 'குடும்பம், பயணம், சாகசம்'
        ]);

        $response->assertRedirect(route('posts.index'));

        // Post should be saved and marked as submitted since author is not admin
        $post = Post::where('title', 'குடும்பப் பயணம் ஒரு சாகசம்')->firstOrFail();
        $this->assertEquals('submitted', $post->status);
        $this->assertEquals($series->id, $post->series_id);
        $this->assertEquals('Volume 1', $post->volume);
        $this->assertEquals(2, $post->chapter_number);
        $this->assertEquals($travelCategory->id, $post->category_id);
        $this->assertEquals($this->authorProfile->id, $post->author_subcategory_id);

        // Assert relations synced
        $this->assertTrue($post->metadataValues->contains($familyTripValue->id));
        $this->assertCount(3, $post->tags);
        $this->assertTrue($post->tags->contains(fn($t) => $t->slug === 'family' || $t->slug === 'பயணம்'));
        $this->assertEquals('#குடும்பம் #பயணம் #சாகசம்', $post->hashtags);
    }

    public function test_api_classify_endpoint(): void
    {
        $this->actingAs($this->author);

        $response = $this->postJson(route('api.posts.classify'), [
            'title' => 'பராசக்தி திரைப்பட விமர்சனம்',
            'content' => 'சிவாஜி கணேசன் நடித்த பராசக்தி திரைப்படம் ஒரு மிகச்சிறந்த சினிமா காவியம்.'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'category_id',
                'metadata_value_ids',
                'tags'
            ]);

        $data = $response->json();
        $cinemaCategory = Category::where('slug', 'good-cinema')->firstOrFail();
        $this->assertEquals($cinemaCategory->id, $data['category_id']);
        $this->assertNotEmpty($data['tags']);
    }

    public function test_stories_feed_filters(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $cinemaCategory = Category::where('slug', 'good-cinema')->firstOrFail();

        $metadataType = MetadataType::where('category_id', $travelCategory->id)
            ->where('slug', 'travel-type')
            ->firstOrFail();

        $familyTripValue = MetadataValue::where('metadata_type_id', $metadataType->id)
            ->where('slug', 'family-trip')
            ->firstOrFail();

        $soloTripValue = MetadataValue::where('metadata_type_id', $metadataType->id)
            ->where('slug', 'solo-trip')
            ->firstOrFail();

        $series = Series::create([
            'title' => 'பயணக் கதைகள்',
            'slug' => 'travel-stories-series',
            'status' => 'active'
        ]);

        // Post 1: Travel, Family Trip, Series
        $post1 = Post::create([
            'author_id' => $this->author->id,
            'author_subcategory_id' => $this->authorProfile->id,
            'title' => 'குடும்பச் சுற்றுலா',
            'slug' => 'family-tourism',
            'content' => 'குடும்பத்துடன் சென்ற சுற்றுலா.',
            'category_id' => $travelCategory->id,
            'series_id' => $series->id,
            'volume' => '1',
            'chapter_number' => 1,
            'status' => 'published',
            'published_at' => now()
        ]);
        $post1->metadataValues()->sync([$familyTripValue->id]);
        $tag = Tag::create(['name' => 'விளையாட்டு', 'slug' => 'sports-tag']);
        $post1->tags()->sync([$tag->id]);

        // Post 2: Travel, Solo Trip
        $post2 = Post::create([
            'author_id' => $this->author->id,
            'author_subcategory_id' => $this->authorProfile->id,
            'title' => 'தனிப் பயணம்',
            'slug' => 'solo-journey',
            'content' => 'தனியாகச் சென்ற பயணம்.',
            'category_id' => $travelCategory->id,
            'status' => 'published',
            'published_at' => now()->subDays(10) // Older
        ]);
        $post2->metadataValues()->sync([$soloTripValue->id]);

        // Post 3: Cinema
        $post3 = Post::create([
            'author_id' => $this->author->id,
            'author_subcategory_id' => $this->authorProfile->id,
            'title' => 'நல்ல சினிமா விமர்சனம்',
            'slug' => 'good-movie-review',
            'content' => 'திரைப்படப் பருந்துப் பார்வை.',
            'category_id' => $cinemaCategory->id,
            'status' => 'published',
            'published_at' => now()
        ]);

        // Filter by Category
        $response = $this->get(route('stories.index', ['category' => $travelCategory->slug]));
        $response->assertStatus(200);
        $response->assertSee('குடும்பச் சுற்றுலா');
        $response->assertSee('தனிப் பயணம்');
        $response->assertDontSee('நல்ல சினிமா விமர்சனம்');

        // Filter by Metadata Value (Family Trip)
        $response = $this->get(route('stories.index', ['metadata_values' => [$familyTripValue->slug]]));
        $response->assertStatus(200);
        $response->assertSee('குடும்பச் சுற்றுலா');
        $response->assertDontSee('தனிப் பயணம்');

        // Filter by Tag
        $response = $this->get(route('stories.index', ['tag' => $tag->slug]));
        $response->assertStatus(200);
        $response->assertSee('குடும்பச் சுற்றுலா');
        $response->assertDontSee('தனிப் பயணம்');

        // Filter by Series
        $response = $this->get(route('stories.index', ['series' => $series->slug]));
        $response->assertStatus(200);
        $response->assertSee('குடும்பச் சுற்றுலா');
        $response->assertDontSee('தனிப் பயணம்');

        // Filter by Date Range (This Week)
        $response = $this->get(route('stories.index', ['date_range' => 'this_week']));
        $response->assertStatus(200);
        $response->assertSee('குடும்பச் சுற்றுலா');
        // $post2 is older than this week, so it shouldn't show up if outside this week.
        // Depending on when the test runs, now()->subDays(10) is last week.
        $response->assertDontSee('தனிப் பயணம்');
    }

    public function test_series_pages(): void
    {
        $series = Series::create([
            'title' => 'வரலாற்றுத் தொடர்',
            'slug' => 'historical-series',
            'description' => 'தமிழ் நாட்டின் வரலாறு.',
            'status' => 'active'
        ]);

        $category = Category::where('slug', 'history-geography')->firstOrFail();

        // Create chapter 1 and chapter 2
        Post::create([
            'author_id' => $this->author->id,
            'author_subcategory_id' => $this->authorProfile->id,
            'title' => 'அத்தியாயம் 1',
            'slug' => 'chapter-1',
            'content' => 'ஆரம்பம்.',
            'category_id' => $category->id,
            'series_id' => $series->id,
            'volume' => 'பாகம் 1',
            'chapter_number' => 1,
            'status' => 'published',
            'published_at' => now()
        ]);

        Post::create([
            'author_id' => $this->author->id,
            'author_subcategory_id' => $this->authorProfile->id,
            'title' => 'அத்தியாயம் 2',
            'slug' => 'chapter-2',
            'content' => 'தொடர்ச்சி.',
            'category_id' => $category->id,
            'series_id' => $series->id,
            'volume' => 'பாகம் 1',
            'chapter_number' => 2,
            'status' => 'published',
            'published_at' => now()
        ]);

        // Test list series page
        $response = $this->get(route('series.index'));
        $response->assertStatus(200);
        $response->assertSee('வரலாற்றுத் தொடர்');
        $response->assertDontSee('சிறப்புத் தொடர்கள்');
        $response->assertSee('Writer One'); // Dynamic author name on the card

        // Test single series details
        $response = $this->get(route('series.show', $series->slug));
        $response->assertStatus(200);
        $response->assertSee('வரலாற்றுத் தொடர்');
        $response->assertSee('அத்தியாயம் 1');
        $response->assertSee('அத்தியாயம் 2');
        $response->assertSee('Writer One'); // Dynamic author name in cover page
    }

    public function test_create_custom_metadata_value_api(): void
    {
        $this->actingAs($this->author);

        // Mock translation service for Tamil -> English
        $this->mock(\App\Services\TranslationService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->with('குளம்', 'ta', 'en')
                ->once()
                ->andReturn('Pond');
        });

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $metadataType = MetadataType::where('category_id', $travelCategory->id)
            ->where('slug', 'place-type')
            ->firstOrFail();

        $response = $this->postJson(route('api.metadata-values.store', $metadataType->id), [
            'name' => 'குளம்'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'name_en',
                'slug'
            ]);

        $data = $response->json();
        $this->assertEquals('குளம்', $data['name']);
        $this->assertEquals('Pond', $data['name_en']);
        $this->assertEquals('pond', $data['slug']);

        $this->assertDatabaseHas('metadata_values', [
            'metadata_type_id' => $metadataType->id,
            'name' => 'குளம்',
            'slug' => 'pond'
        ]);
    }

    public function test_create_custom_metadata_value_api_english(): void
    {
        $this->actingAs($this->author);

        // Mock translation service for English -> Tamil
        $this->mock(\App\Services\TranslationService::class, function ($mock) {
            $mock->shouldReceive('translate')
                ->with('Dream Destination', 'en', 'ta')
                ->once()
                ->andReturn('கனவுப் பிரதேசம்');
        });

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $metadataType = MetadataType::where('category_id', $travelCategory->id)
            ->where('slug', 'place-type')
            ->firstOrFail();

        $response = $this->postJson(route('api.metadata-values.store', $metadataType->id), [
            'name' => 'Dream Destination'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'name_en',
                'slug'
            ]);

        $data = $response->json();
        $this->assertEquals('Dream Destination', $data['name_en']);
        $this->assertEquals('கனவுப் பிரதேசம்', $data['name']);
        $this->assertEquals('dream-destination', $data['slug']);

        $this->assertDatabaseHas('metadata_values', [
            'metadata_type_id' => $metadataType->id,
            'name_en' => 'Dream Destination',
            'slug' => 'dream-destination'
        ]);
    }

    public function test_post_creation_with_hashtags(): void
    {
        $this->actingAs($this->author);

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();

        // 1. Success case: 3 hashtags
        $response = $this->post(route('posts.store'), [
            'title' => 'ஏரிகள் பற்றிய ஒரு புவியியல் பதிவு',
            'content' => 'இது ஒரு அழகான ஏரி பற்றிய புவியியல் கட்டுரை ஆகும்.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'hashtags' => 'ஏரி, புவியியல், இயற்கை'
        ]);

        $response->assertRedirect(route('posts.index'));

        $post = Post::where('title', 'ஏரிகள் பற்றிய ஒரு புவியியல் பதிவு')->firstOrFail();
        // Mutator should format it as "#ஏரி #புவியியல் #இயற்கை"
        $this->assertEquals('#ஏரி #புவியியல் #இயற்கை', $post->hashtags);

        // Manually publish in test DB since authors can only submit drafts/submitted posts
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        // Verify hashtags are visible on the author profile page
        $authorProfileResponse = $this->get(route('authors.show', $this->authorProfile->slug));
        $authorProfileResponse->assertSee('#ஏரி');
        $authorProfileResponse->assertSee('#புவியியல்');
        $authorProfileResponse->assertSee('#இயற்கை');

        // 2. Validation failure case: 4 hashtags
        $responseFailure = $this->from(route('posts.create'))->post(route('posts.store'), [
            'title' => 'தவறான பதிவு',
            'content' => 'நான்கு ஹேஷ்டேகுகள் கொண்டது.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'hashtags' => 'ஒன்று, இரண்டு, மூன்று, நான்கு'
        ]);

        $responseFailure->assertRedirect(route('posts.create'));
        $responseFailure->assertSessionHasErrors('hashtags');
    }

    public function test_paginated_tag_filtering_and_breadcrumb_display(): void
    {
        $this->mock(\App\Services\TranslationService::class, function ($mock) {
            $mock->shouldReceive('translate')->andReturnUsing(function ($text, $from, $to) {
                if ($text === 'விளையாட்டு') {
                    return 'Sports';
                }
                if (str_contains($text, 'விளையாட்டு கட்டுரை')) {
                    $letter = substr($text, -1);
                    return "Sports Article {$letter}";
                }
                return $text;
            });
            $mock->shouldReceive('translateHtml')->andReturnUsing(fn($html) => $html);
        });

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $tag = Tag::create(['name' => 'விளையாட்டு', 'slug' => 'sports-tag']);

        // Create 10 posts with this tag (A through J)
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $posts = [];
        for ($i = 1; $i <= 10; $i++) {
            $letter = $letters[$i - 1];
            $post = Post::create([
                'author_id' => $this->author->id,
                'author_subcategory_id' => $this->authorProfile->id,
                'title' => "விளையாட்டு கட்டுரை {$letter}",
                'slug' => "sports-article-{$i}",
                'content' => "உள்ளடக்கம் {$i}",
                'category_id' => $travelCategory->id,
                'status' => 'published',
                'published_at' => now()->subMinutes($i)
            ]);
            $post->tags()->sync([$tag->id]);
            $posts[] = $post;
        }

        // Verify breadcrumbs on one of the post pages
        $showResponse = $this->get(route('posts.show', $posts[0]->slug));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('href="' . url('/stories?tag=' . $tag->slug) . '"', false);
        $showResponse->assertSee('விளையாட்டு');

        // Verify page 1 of tag stories feed
        $storiesResponse = $this->get(route('stories.index', ['tag' => $tag->slug]));
        $storiesResponse->assertStatus(200);
        for ($i = 1; $i <= 9; $i++) {
            $letter = $letters[$i - 1];
            $storiesResponse->assertSee("விளையாட்டு கட்டுரை {$letter}");
        }
        $storiesResponse->assertDontSee("விளையாட்டு கட்டுரை J");

        // Verify page 2 of tag stories feed
        $storiesResponsePage2 = $this->get(route('stories.index', ['tag' => $tag->slug, 'page' => 2]));
        $storiesResponsePage2->assertStatus(200);
        $storiesResponsePage2->assertSee("விளையாட்டு கட்டுரை J");
        for ($i = 1; $i <= 9; $i++) {
            $letter = $letters[$i - 1];
            $storiesResponsePage2->assertDontSee("விளையாட்டு கட்டுரை {$letter}");
        }
    }

    public function test_inline_series_creation_via_api(): void
    {
        $this->actingAs($this->author);

        // 1. Test validation error when creating a series without title
        $response = $this->postJson(route('api.series.store'), [
            'description' => 'Test description'
        ]);
        $response->assertStatus(422);

        // 2. Test successful creation with Tamil title
        $response = $this->postJson(route('api.series.store'), [
            'title' => 'புதிய அறிவியல் தொடர்',
            'description' => 'அறிவியல் தகவல்கள்'
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'title', 'title_en', 'slug', 'description', 'description_en']);
        
        $this->assertDatabaseHas('series', [
            'title' => 'புதிய அறிவியல் தொடர்',
            'description' => 'அறிவியல் தகவல்கள்',
            'status' => 'active'
        ]);

        // 3. Test successful creation with English title
        $responseEn = $this->postJson(route('api.series.store'), [
            'title' => 'New Science Series',
            'description' => 'Science info'
        ]);
        $responseEn->assertStatus(201);
        
        $this->assertDatabaseHas('series', [
            'title_en' => 'New Science Series',
            'description_en' => 'Science info',
            'status' => 'active'
        ]);
    }

    public function test_series_chapter_navigation_on_post_show(): void
    {
        $this->actingAs($this->author);

        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $series = Series::create([
            'title' => 'பயணத் தொடர்',
            'slug' => 'travel-series',
            'status' => 'active'
        ]);

        // Create 3 posts in the same series with different chapter numbers
        $post1 = Post::create([
            'title' => 'Chapter One',
            'slug' => 'chapter-one',
            'content' => 'Content of chapter 1.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
            'series_id' => $series->id,
            'chapter_number' => 1
        ]);

        $post2 = Post::create([
            'title' => 'Chapter Two',
            'slug' => 'chapter-two',
            'content' => 'Content of chapter 2.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
            'series_id' => $series->id,
            'chapter_number' => 2
        ]);

        $post3 = Post::create([
            'title' => 'Chapter Three',
            'slug' => 'chapter-three',
            'content' => 'Content of chapter 3.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
            'series_id' => $series->id,
            'chapter_number' => 3
        ]);

        // 1. Visit Chapter 1: Should show "Chapter Two" as Next Chapter, and no previous chapter
        $response1 = $this->get(route('posts.show', $post1->slug));
        $response1->assertStatus(200);
        $response1->assertSee('Chapter Two');
        $response1->assertSee('Next Chapter');
        $response1->assertSee('First Chapter');
        $response1->assertDontSee('Chapter Three'); // Not directly next

        // 2. Visit Chapter 2: Should show "Chapter One" as Previous Chapter and "Chapter Three" as Next Chapter
        $response2 = $this->get(route('posts.show', $post2->slug));
        $response2->assertStatus(200);
        $response2->assertSee('Chapter One');
        $response2->assertSee('Previous Chapter');
        $response2->assertSee('Chapter Three');
        $response2->assertSee('Next Chapter');

        // 3. Visit Chapter 3: Should show "Chapter Two" as Previous Chapter, and no next chapter
        $response3 = $this->get(route('posts.show', $post3->slug));
        $response3->assertStatus(200);
        $response3->assertSee('Chapter Two');
        $response3->assertSee('Previous Chapter');
        $response3->assertSee('Final Chapter');
        $response3->assertDontSee('Chapter One'); // Not directly previous
    }

    public function test_inline_series_creation_with_image_upload(): void
    {
        $this->actingAs($this->author);
        \Illuminate\Support\Facades\Storage::fake('public');

        $response = $this->postJson(route('api.series.store'), [
            'title' => 'புதிய வரலாற்றுத் தொடர்',
            'description' => 'வரலாற்றுப் பின்னணி',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('cover.jpg')
        ]);

        $response->assertStatus(201);
        $data = $response->json();
        
        $this->assertNotNull($data['image_path']);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($data['image_path']);
    }

    public function test_series_cover_fallback_logic(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();

        // Create a series without a cover
        $series = Series::create([
            'title' => 'முகப்புப் படம் இல்லாத தொடர்',
            'slug' => 'no-cover-series',
            'status' => 'active'
        ]);

        // Create a post in that series with an image
        Post::create([
            'title' => 'முதல் அத்தியாயம்',
            'slug' => 'first-chapter-img',
            'content' => 'Content here.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
            'series_id' => $series->id,
            'chapter_number' => 1,
            'image' => 'https://example.com/chapter-image.jpg'
        ]);

        // View series details page
        $response = $this->get(route('series.show', $series->slug));
        $response->assertStatus(200);
        // It should display the chapter's fallback image url
        $response->assertSee('https://example.com/chapter-image.jpg');

        // View series index page
        $responseIndex = $this->get(route('series.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('https://example.com/chapter-image.jpg');
    }

    public function test_unified_tag_and_category_filtering(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $cinemaCategory = Category::where('slug', 'good-cinema')->firstOrFail();

        // Tag: பயணம் (slug: travel-tag)
        $tag = Tag::create(['name' => 'பயணம்', 'slug' => 'travel-tag']);

        // Post 1: categorized under travelCategory but has no tags
        $post1 = Post::create([
            'title' => 'Lake Hillier Travel',
            'slug' => 'lake-hillier-travel',
            'content' => 'Travel info.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        // Post 2: categorized under cinemaCategory but tagged with பயணம்
        $post2 = Post::create([
            'title' => 'Cinema Travel Movie',
            'slug' => 'cinema-travel-movie',
            'content' => 'Movie about travel.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $cinemaCategory->id,
            'author_id' => $this->author->id,
        ]);
        $post2->tags()->sync([$tag->id]);

        // 1. Filter by tag=travel-tag: Should see both because Category is பயணம் and Tag is பயணம்
        $responseTag = $this->get(route('stories.index', ['tag' => $tag->slug]));
        $responseTag->assertStatus(200);
        $responseTag->assertSee('Lake Hillier Travel');
        $responseTag->assertSee('Cinema Travel Movie');

        // 2. Filter by category=travel: Should also see both
        $responseCat = $this->get(route('stories.index', ['category' => $travelCategory->slug]));
        $responseCat->assertStatus(200);
        $responseCat->assertSee('Lake Hillier Travel');
        $responseCat->assertSee('Cinema Travel Movie');
    }

    public function test_posts_create_and_edit_forms_contain_autosave_markup(): void
    {
        $this->actingAs($this->author);

        // 1. Check create post form
        $responseCreate = $this->get(route('posts.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('hasAutosave');
        $responseCreate->assertSee('loadAutosave()');
        $responseCreate->assertSee('discardAutosave()');
        $responseCreate->assertSee('getAutoSaveKey()');

        // 2. Check edit post form
        $post = Post::create([
            'title' => 'Test Post to Edit',
            'slug' => 'test-post-to-edit',
            'content' => 'Content here.',
            'category_id' => Category::first()->id,
            'author_id' => $this->author->id,
            'status' => 'draft'
        ]);

        $responseEdit = $this->get(route('posts.edit', $post->id));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('hasAutosave');
        $responseEdit->assertSee('loadAutosave()');
        $responseEdit->assertSee('discardAutosave()');
        $responseEdit->assertSee('getAutoSaveKey()');
        $responseEdit->assertSee("kathaingo_draft_autosave_edit_{$post->id}");
    }

    public function test_post_with_multiple_social_media_urls(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();

        // Create post with YouTube and Twitter URLs separated by newline
        $post = Post::create([
            'title' => 'சமூக ஊடகங்கள் கலந்த பதிவு',
            'slug' => 'social-media-mixed-post',
            'content' => 'இது ஒரு சமூக ஊடகப் பதிவு.',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
            'status' => 'published',
            'video_url' => "https://www.youtube.com/watch?v=dQw4w9WgXcQ\nhttps://twitter.com/LaravelFramework/status/123456789"
        ]);

        $response = $this->get(route('posts.show', $post->slug));
        $response->assertStatus(200);
        
        // Assert YouTube iframe present
        $response->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ');
        
        // Assert Twitter tweet blockquote present
        $response->assertSee('class="twitter-tweet"', false);
        $response->assertSee('platform.twitter.com/widgets.js');
    }

    public function test_tag_normalization_and_deduplication(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();

        // Submit a post with duplicate-prone tags, different spacing/separators, and case variations
        $response = $this->post(route('posts.store'), [
            'title' => 'Tag Test Post 1',
            'content' => 'Content of post 1.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'tags' => 'தமிழ்  சினிமா, தமிழ்_சினிமா, தமிழ்-சினிமா, travel, Travel, TRAVEL'
        ]);

        $response->assertRedirect(route('posts.index'));

        // Assert database has normalized unique tags
        $this->assertDatabaseHas('tags', ['slug' => 'தமிழ்-சினிமா']);
        $this->assertDatabaseHas('tags', ['slug' => 'travel']);

        // Assert only exactly 1 record exists per tag
        $this->assertCount(1, Tag::where('slug', 'தமிழ்-சினிமா')->get());
        $this->assertCount(1, Tag::where('slug', 'travel')->get());

        // Fetch post and check tag relations count is exactly 2
        $post = Post::where('title', 'Tag Test Post 1')->firstOrFail();
        $this->assertCount(2, $post->tags);
    }

    public function test_guest_reaction_triggers_unauthorized(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Reactions',
            'slug' => 'test-post-reactions',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        $response = $this->postJson(route('posts.react', $post->slug), [
            'type' => 'like'
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_reaction_toggle(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Reactions 2',
            'slug' => 'test-post-reactions-2',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        // Toggle on
        $response = $this->postJson(route('posts.react', $post->slug), [
            'type' => 'love'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'added',
                'reaction_type' => 'love',
                'count' => 1,
                'user_reactions' => ['love']
            ]);

        $this->assertDatabaseHas('post_reactions', [
            'post_id' => $post->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'love'
        ]);

        // Toggle off
        $response = $this->postJson(route('posts.react', $post->slug), [
            'type' => 'love'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'removed',
                'reaction_type' => 'love',
                'count' => 0,
                'user_reactions' => []
            ]);

        $this->assertDatabaseMissing('post_reactions', [
            'post_id' => $post->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'love'
        ]);
    }

    public function test_prevent_reaction_spam_and_db_uniqueness(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Reactions 3',
            'slug' => 'test-post-reactions-3',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        // Insert first record
        \App\Models\PostReaction::create([
            'post_id' => $post->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'clap'
        ]);

        // Attempting to insert a duplicate must throw a database exception
        $this->expectException(\Illuminate\Database\QueryException::class);

        \App\Models\PostReaction::create([
            'post_id' => $post->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'clap'
        ]);
    }

    public function test_guest_comment_reaction_triggers_unauthorized(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Comment Reactions',
            'slug' => 'test-post-comment-reactions',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        $comment = \App\Models\Comment::create([
            'post_id' => $post->id,
            'author_name' => 'John Doe',
            'content' => 'Test comment content',
        ]);

        $response = $this->postJson(route('comments.react', $comment->id), [
            'type' => 'like'
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_comment_reaction_toggle(): void
    {
        $this->actingAs($this->author);
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Comment Reactions 2',
            'slug' => 'test-post-comment-reactions-2',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        $comment = \App\Models\Comment::create([
            'post_id' => $post->id,
            'author_name' => 'John Doe',
            'content' => 'Test comment content',
        ]);

        // Toggle on
        $response = $this->postJson(route('comments.react', $comment->id), [
            'type' => 'love'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'added',
                'reaction_type' => 'love',
                'count' => 1,
                'user_reactions' => ['love']
            ]);

        $this->assertDatabaseHas('comment_reactions', [
            'comment_id' => $comment->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'love'
        ]);

        // Toggle off
        $response = $this->postJson(route('comments.react', $comment->id), [
            'type' => 'love'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'action' => 'removed',
                'reaction_type' => 'love',
                'count' => 0,
                'user_reactions' => []
            ]);

        $this->assertDatabaseMissing('comment_reactions', [
            'comment_id' => $comment->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'love'
        ]);
    }

    public function test_prevent_comment_reaction_spam_and_db_uniqueness(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Comment Reactions 3',
            'slug' => 'test-post-comment-reactions-3',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        $comment = \App\Models\Comment::create([
            'post_id' => $post->id,
            'author_name' => 'John Doe',
            'content' => 'Test comment content',
        ]);

        // Insert first record
        \App\Models\CommentReaction::create([
            'comment_id' => $comment->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'clap'
        ]);

        // Attempting to insert a duplicate must throw a database exception
        $this->expectException(\Illuminate\Database\QueryException::class);

        \App\Models\CommentReaction::create([
            'comment_id' => $comment->id,
            'user_id' => $this->author->id,
            'reaction_type' => 'clap'
        ]);
    }

    public function test_comment_reply_submission(): void
    {
        $travelCategory = Category::where('slug', 'travel')->firstOrFail();
        $post = Post::create([
            'title' => 'Test Post Comment Replies',
            'slug' => 'test-post-comment-replies',
            'content' => 'Content of post.',
            'status' => 'published',
            'category_id' => $travelCategory->id,
            'author_id' => $this->author->id,
        ]);

        $parentComment = \App\Models\Comment::create([
            'post_id' => $post->id,
            'author_name' => 'Parent User',
            'content' => 'Parent comment content',
        ]);

        $response = $this->post(route('posts.storeComment', $post->slug), [
            'author_name' => 'Child User',
            'content' => 'Child comment reply content',
            'parent_id' => $parentComment->id
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'parent_id' => $parentComment->id,
            'author_name' => 'Child User',
            'content' => 'Child comment reply content'
        ]);

        // Verify the parent comment's replies relationship
        $parentComment->refresh();
        $this->assertCount(1, $parentComment->replies);
        $this->assertEquals('Child User', $parentComment->replies->first()->author_name);
    }
}


