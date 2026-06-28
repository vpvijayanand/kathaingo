<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriterEngineTest extends TestCase
{
    use RefreshDatabase;

    private Category $writersCategory;
    private Category $contentCategory;
    private User $authorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writersCategory = Category::create([
            'name' => 'Writers',
            'slug' => 'pathivargal',
            'order' => 100,
        ]);

        $this->contentCategory = Category::create([
            'name' => 'Good Cinema',
            'slug' => 'good-cinema',
            'order' => 1,
        ]);

        $this->authorUser = User::factory()->create([
            'role' => 'author',
            'is_approved' => true,
        ]);
    }

    public function test_can_fill_and_save_writer_discovery_fields()
    {
        $subcategory = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
            'user_id' => $this->authorUser->id,
            'trust_level' => 2,
            'tagline' => 'சினிமா மற்றும் இலக்கியம் மீதான பார்வை',
            'tagline_en' => 'Views on cinema and literature',
            'is_featured' => true,
        ]);

        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'trust_level' => 2,
            'tagline' => 'சினிமா மற்றும் இலக்கியம் மீதான பார்வை',
            'tagline_en' => 'Views on cinema and literature',
            'is_featured' => true,
        ]);

        $this->assertEquals(2, $subcategory->fresh()->trust_level);
        $this->assertTrue((bool)$subcategory->fresh()->is_featured);
    }

    public function test_tagline_auto_translation_on_saving()
    {
        $subcategory = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
            'tagline' => 'சினிமா மீதான பார்வை',
        ]);

        $this->assertNotNull($subcategory->tagline_en);
        $this->assertEquals('Translated: சினிமா மீதான பார்வை', $subcategory->tagline_en);
    }

    public function test_tagline_localization_accessor()
    {
        $subcategory = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
            'tagline' => 'தமிழ் வாசகம்',
            'tagline_en' => 'English tagline',
        ]);

        // Default locale (should return Tamil)
        app()->setLocale('ta');
        $this->assertEquals('தமிழ் வாசகம்', $subcategory->tagline);

        // English locale (should return English)
        app()->setLocale('en');
        $this->assertEquals('English tagline', $subcategory->tagline);
    }

    public function test_single_writer_profile_stats_accessors()
    {
        $writer = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'Writer',
            'slug' => 'writer',
            'user_id' => $this->authorUser->id,
        ]);

        // Create published and draft posts
        $post1 = Post::create([
            'title' => 'Story 1',
            'slug' => 'story-1',
            'content' => 'Story 1 content',
            'status' => 'published',
            'author_subcategory_id' => $writer->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);
        $post1->views_count = 10;
        $post1->likes_count = 5;
        $post1->save();

        $post2 = Post::create([
            'title' => 'Story 2',
            'slug' => 'story-2',
            'content' => 'Story 2 content',
            'status' => 'published',
            'author_subcategory_id' => $writer->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);
        $post2->views_count = 20;
        $post2->likes_count = 10;
        $post2->save();

        $post3 = Post::create([
            'title' => 'Draft Story',
            'slug' => 'draft-story',
            'content' => 'Draft content',
            'status' => 'draft',
            'author_subcategory_id' => $writer->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);
        $post3->views_count = 50;
        $post3->likes_count = 20;
        $post3->save();

        // Accessors should compute stats dynamically (published posts only)
        $this->assertEquals(2, $writer->post_count);
        $this->assertEquals(30, $writer->total_reads);
        $this->assertEquals(15, $writer->engagement_score);
    }

    public function test_efficient_writer_stats_query_scope()
    {
        $writer1 = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'Writer One',
            'slug' => 'writer-one',
        ]);

        $writer2 = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'Writer Two',
            'slug' => 'writer-two',
        ]);

        $post1 = Post::create([
            'title' => 'Story 1',
            'slug' => 'story-1',
            'content' => 'Story 1 content',
            'status' => 'published',
            'author_subcategory_id' => $writer1->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);
        $post1->views_count = 100;
        $post1->likes_count = 50;
        $post1->save();

        $post2 = Post::create([
            'title' => 'Story 2',
            'slug' => 'story-2',
            'content' => 'Story 2 content',
            'status' => 'published',
            'author_subcategory_id' => $writer2->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);
        $post2->views_count = 20;
        $post2->likes_count = 10;
        $post2->save();

        // Retrieve writers with scopes
        $writersWithStats = Subcategory::withWriterStats()->get();

        $w1WithStats = $writersWithStats->firstWhere('id', $writer1->id);
        $w2WithStats = $writersWithStats->firstWhere('id', $writer2->id);

        $this->assertEquals(1, $w1WithStats->published_posts_count);
        $this->assertEquals(100, $w1WithStats->total_reads);
        $this->assertEquals(50, $w1WithStats->total_likes);

        $this->assertEquals(1, $w2WithStats->published_posts_count);
        $this->assertEquals(20, $w2WithStats->total_reads);
        $this->assertEquals(10, $w2WithStats->total_likes);
    }

    public function test_writer_discovery_scopes()
    {
        $writer1 = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'Writer One',
            'slug' => 'writer-one',
            'is_featured' => true,
        ]);

        $writer2 = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'Writer Two',
            'slug' => 'writer-two',
            'is_featured' => false,
        ]);

        // Test scopeFeatured
        $featured = Subcategory::featured()->get();
        $this->assertTrue($featured->contains($writer1));
        $this->assertFalse($featured->contains($writer2));

        // Test scopeWritersOnly
        $contentSub = Subcategory::create([
            'category_id' => $this->contentCategory->id,
            'name' => 'Fiction Sub',
            'slug' => 'fiction-sub',
        ]);

        $allWriters = Subcategory::writersOnly()->get();
        $this->assertTrue($allWriters->contains($writer1));
        $this->assertTrue($allWriters->contains($writer2));
        $this->assertFalse($allWriters->contains($contentSub));

        // Create post for writer 1 under contentCategory
        Post::create([
            'title' => 'Story 1',
            'slug' => 'story-1',
            'content' => 'Story 1 content',
            'status' => 'published',
            'author_subcategory_id' => $writer1->id,
            'category_id' => $this->contentCategory->id,
            'author_id' => $this->authorUser->id,
        ]);

        // Test scopeWhereWritesInCategory
        $writersInCinema = Subcategory::whereWritesInCategory($this->contentCategory->id)->get();
        $this->assertTrue($writersInCinema->contains($writer1));
        $this->assertFalse($writersInCinema->contains($writer2));
    }

    public function test_admin_can_access_verification_dashboard_and_see_writers_list()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true,
            'role' => 'admin',
        ]);

        $writer = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
            'tagline' => 'தமிழ் வாசகம்',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.writers.verification'));

        $response->assertStatus(200);
        $response->assertSee('விஜயன்');
        $response->assertSee('தமிழ் வாசகம்');
    }

    public function test_admin_can_update_writer_verification_details()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true,
            'role' => 'admin',
        ]);

        $writer = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
            'tagline' => 'பழைய வாசகம்',
            'trust_level' => 1,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.writers.verification.update', $writer->id), [
            'trust_level' => 2,
            'is_featured' => 1,
            'tagline' => 'புதிய வாசகம்',
            'tagline_en' => 'New English tagline',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('subcategories', [
            'id' => $writer->id,
            'trust_level' => 2,
            'is_featured' => 1,
            'tagline' => 'புதிய வாசகம்',
            'tagline_en' => 'New English tagline',
        ]);
    }

    public function test_non_admin_cannot_access_verification_dashboard_or_update_details()
    {
        $nonAdmin = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true,
            'role' => 'author',
        ]);

        $writer = Subcategory::create([
            'category_id' => $this->writersCategory->id,
            'name' => 'விஜயன்',
            'slug' => 'vijayan',
        ]);

        // Non-admin view dashboard
        $responseView = $this->actingAs($nonAdmin)->get(route('admin.writers.verification'));
        $responseView->assertStatus(403);

        // Non-admin update dashboard
        $responseUpdate = $this->actingAs($nonAdmin)->post(route('admin.writers.verification.update', $writer->id), [
            'trust_level' => 2,
            'is_featured' => 1,
            'tagline' => 'புதிய வாசகம்',
        ]);
        $responseUpdate->assertStatus(403);
    }
}

