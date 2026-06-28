<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\Post;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $writerUser1;
    private User $writerUser2;
    private Subcategory $authorProfile1;
    private Subcategory $authorProfile2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true
        ]);

        // Create standard users
        $this->writerUser1 = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true,
            'role' => 'author',
        ]);

        $this->writerUser2 = User::factory()->create([
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

        // Create author subcategory profiles linked to the users
        $this->authorProfile1 = Subcategory::create([
            'category_id' => $writersCategory->id,
            'name' => 'Writer One',
            'slug' => 'writer-one',
            'user_id' => $this->writerUser1->id,
            'email' => 'writer1@example.com'
        ]);

        $this->authorProfile2 = Subcategory::create([
            'category_id' => $writersCategory->id,
            'name' => 'Writer Two',
            'slug' => 'writer-two',
            'user_id' => $this->writerUser2->id,
            'email' => 'writer2@example.com'
        ]);
    }

    public function test_public_author_profile_is_accessible_and_lists_only_published_posts()
    {
        // Create a published and draft post for writer 1
        Post::create([
            'author_id' => $this->writerUser1->id,
            'author_subcategory_id' => $this->authorProfile1->id,
            'title' => 'Published Story',
            'slug' => 'published-story',
            'content' => 'Story content here...',
            'status' => 'published',
            'published_at' => now()
        ]);

        Post::create([
            'author_id' => $this->writerUser1->id,
            'author_subcategory_id' => $this->authorProfile1->id,
            'title' => 'Draft Story',
            'slug' => 'draft-story',
            'content' => 'Story content here...',
            'status' => 'draft'
        ]);

        $response = $this->get(route('authors.show', $this->authorProfile1->slug));
        $response->assertStatus(200);
        $response->assertSee('Writer One');
        $response->assertSee('Published Story');
        $response->assertDontSee('Draft Story');
    }

    public function test_owner_can_edit_their_own_profile_but_other_users_cannot()
    {
        // Logged-in as writer 1
        $this->actingAs($this->writerUser1);
        
        $response = $this->get(route('authors.edit', $this->authorProfile1->slug));
        $response->assertStatus(200);

        // Try to edit writer 2's profile
        $response = $this->get(route('authors.edit', $this->authorProfile2->slug));
        $response->assertStatus(403);
    }

    public function test_admin_can_edit_anyones_profile()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('authors.edit', $this->authorProfile1->slug));
        $response->assertStatus(200);

        $response = $this->get(route('authors.edit', $this->authorProfile2->slug));
        $response->assertStatus(200);
    }

    public function test_profile_updates_correctly()
    {
        $this->actingAs($this->writerUser1);

        $response = $this->put(route('authors.update', $this->authorProfile1->slug), [
            'name' => 'Updated Writer Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'facebook_url' => 'https://facebook.com/updated',
            'instagram_url' => 'https://instagram.com/updated',
            'linkedin_url' => 'https://linkedin.com/in/updated',
            'topics' => 'Tamil culture, short stories',
            'description' => 'A passionate writer.'
        ]);

        $response->assertRedirect(route('authors.show', 'Updated-Writer-Name'));

        $this->assertDatabaseHas('subcategories', [
            'id' => $this->authorProfile1->id,
            'name' => 'Updated Writer Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'facebook_url' => 'https://facebook.com/updated',
            'instagram_url' => 'https://instagram.com/updated',
            'linkedin_url' => 'https://linkedin.com/in/updated',
            'topics' => 'Tamil culture, short stories',
            'description' => 'A passionate writer.'
        ]);
    }

    public function test_author_can_update_profile_with_cropped_image()
    {
        $this->actingAs($this->writerUser1);
        \Illuminate\Support\Facades\Storage::fake('public');

        // A fake 1x1 red pixel JPEG base64 Data URL
        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->put(route('authors.update', $this->authorProfile1->slug), [
            'name' => 'Writer One Cropped',
            'email' => 'writer1cropped@example.com',
            'cropped_image' => $base64Image,
        ]);

        $response->assertRedirect(route('authors.show', 'Writer-One-Cropped'));

        $this->authorProfile1 = $this->authorProfile1->fresh();
        $this->assertNotNull($this->authorProfile1->image_path);
        $this->assertStringContainsString('images/', $this->authorProfile1->image_path);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($this->authorProfile1->image_path);
    }

    public function test_non_admin_post_submission_goes_to_submitted()
    {
        $this->actingAs($this->writerUser1);

        // We need category/subcategory/etc to satisfy validation rules
        $category = Category::create(['name' => 'General', 'slug' => 'general']);

        $response = $this->post(route('posts.store'), [
            'title' => 'My New Story',
            'content' => 'Story content',
            'category_id' => $category->id,
            'status' => 'published' // Request to publish directly
        ]);

        // Should save as submitted in DB
        $this->assertDatabaseHas('posts', [
            'title' => 'My New Story',
            'status' => 'submitted',
            'published_at' => null
        ]);
    }

    public function test_admin_post_submission_publishes_directly()
    {
        $this->actingAs($this->admin);

        $category = Category::create(['name' => 'General', 'slug' => 'general']);

        $response = $this->post(route('posts.store'), [
            'title' => 'Admin Story',
            'content' => 'Story content',
            'category_id' => $category->id,
            'status' => 'published'
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Admin Story',
            'status' => 'published'
        ]);
        
        $post = Post::where('title', 'Admin Story')->first();
        $this->assertNotNull($post->published_at);
    }

    public function test_admin_can_approve_submitted_post()
    {
        $category = Category::create(['name' => 'General', 'slug' => 'general']);
        $post = Post::create([
            'author_id' => $this->writerUser1->id,
            'title' => 'Pending Post',
            'slug' => 'pending-post',
            'content' => 'Content...',
            'category_id' => $category->id,
            'status' => 'submitted'
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.posts.approve', $post->id));
        
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'published'
        ]);
    }

    public function test_post_creation_auto_categorizes_and_auto_assigns_author()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->writerUser1);

        // Seed Category: Posts (பதிவுகள்)
        $postsCat = Category::create([
            'name' => 'Posts',
            'slug' => 'pathivugal',
            'order' => 0
        ]);

        // Seed Subcategory: Good Cinema (நல்லசினிமா)
        $cinemaSub = Subcategory::create([
            'category_id' => $postsCat->id,
            'name' => 'Good Cinema',
            'slug' => 'நல்லசினிமா',
            'order' => 1
        ]);

        // Seed Child Categories (தமிழ்)
        $tamilChild = ChildCategory::create([
            'subcategory_id' => $cinemaSub->id,
            'name' => 'Tamil',
            'slug' => 'தமிழ்',
            'order' => 1
        ]);

        // Seed Grandchild Categories
        $familyGrandchild = GrandchildCategory::create([
            'child_category_id' => $tamilChild->id,
            'name' => 'Family/Drama',
            'slug' => 'தமிழ்-குடும்ப-நாடகம்',
            'order' => 1
        ]);

        $response = $this->post(route('posts.store'), [
            'title' => 'ஒரு நல்ல குடும்பத் திரைப்படம்',
            'content' => 'மணிரத்னத்தின் இந்த சினிமா மிக நல்ல பாசம் நிறைந்த குடும்பக் கதை.',
            'status' => 'published'
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'ஒரு நல்ல குடும்பத் திரைப்படம்',
            'author_subcategory_id' => $this->authorProfile1->id, // Auto-assigned from user profile
            'category_id' => $postsCat->id,
            'subcategory_id' => $cinemaSub->id,
            'child_category_id' => $tamilChild->id,
            'grandchild_category_id' => $familyGrandchild->id,
            'status' => 'submitted'
        ]);
    }

    public function test_author_profile_page_displays_category_tiles_and_filters_correctly()
    {
        // Seed Category: Posts (பதிவுகள்)
        $postsCat = Category::create([
            'name' => 'Posts',
            'slug' => 'pathivugal',
            'order' => 0
        ]);

        // Seed Subcategories: Good Movie (நல்லசினிமா) and Travel Stories (பயணக் கதைகள்)
        $cinemaSub = Subcategory::create([
            'category_id' => $postsCat->id,
            'name' => 'Good Movie',
            'slug' => 'good-movie',
            'order' => 1
        ]);

        $travelSub = Subcategory::create([
            'category_id' => $postsCat->id,
            'name' => 'Travel Stories',
            'slug' => 'travel-stories',
            'order' => 2
        ]);

        // Create published posts for authorProfile1 under Good Movie and Travel Stories
        Post::create([
            'author_id' => $this->writerUser1->id,
            'author_subcategory_id' => $this->authorProfile1->id,
            'category_id' => $postsCat->id,
            'subcategory_id' => $cinemaSub->id,
            'title' => 'My Movie Review',
            'slug' => 'my-movie-review',
            'content' => 'Review content...',
            'status' => 'published',
            'published_at' => now()
        ]);

        Post::create([
            'author_id' => $this->writerUser1->id,
            'author_subcategory_id' => $this->authorProfile1->id,
            'category_id' => $postsCat->id,
            'subcategory_id' => $travelSub->id,
            'title' => 'My Travel Blog',
            'slug' => 'my-travel-blog',
            'content' => 'Travel content...',
            'status' => 'published',
            'published_at' => now()
        ]);

        // Access public profile of author
        $response = $this->get(route('authors.show', $this->authorProfile1->slug));
        $response->assertStatus(200);

        // Verify category tiles are displayed
        $response->assertSee('Good Movie');
        $response->assertSee('Travel Stories');
        // Both stories should be listed
        $response->assertSee('My Movie Review');
        $response->assertSee('My Travel Blog');

        // Filter by Good Movie
        $responseFilteredCinema = $this->get(route('authors.show', [
            'subcategory' => $this->authorProfile1->slug,
            'category' => $cinemaSub->slug
        ]));
        $responseFilteredCinema->assertStatus(200);
        $responseFilteredCinema->assertSee('My Movie Review');
        $responseFilteredCinema->assertDontSee('My Travel Blog');

        // Filter by Travel Stories
        $responseFilteredTravel = $this->get(route('authors.show', [
            'subcategory' => $this->authorProfile1->slug,
            'category' => $travelSub->slug
        ]));
        $responseFilteredTravel->assertStatus(200);
        $responseFilteredTravel->assertSee('My Travel Blog');
        $responseFilteredTravel->assertDontSee('My Movie Review');
    }

    public function test_additional_writer_profiles_creation()
    {
        $migration = require database_path('migrations/2026_06_11_085000_create_additional_writer_profiles.php');
        $migration->up();

        $this->assertDatabaseHas('subcategories', [
            'slug' => 'face-book-post',
            'name' => 'Face Book Post'
        ]);

        $this->assertDatabaseHas('subcategories', [
            'slug' => 'whatsapp-forward',
            'name' => 'Whatsapp Forward'
        ]);

        $this->assertDatabaseHas('subcategories', [
            'slug' => 'yaro-anonymous',
            'name' => 'யாரோ (Anonymous)',
            'name_en' => 'Anonymous'
        ]);

        $this->assertDatabaseHas('subcategories', [
            'slug' => 'padithathil-pidithathu',
            'name' => 'படித்ததில் பிடித்தது',
            'name_en' => 'Favorite Reads'
        ]);
    }

    public function test_public_story_tiles_actions_rendered_correctly()
    {
        // 1. Create a published post owned by writerUser1
        $post = Post::create([
            'author_id' => $this->writerUser1->id,
            'author_subcategory_id' => $this->authorProfile1->id,
            'title' => 'Story Action Testing',
            'slug' => 'story-action-testing',
            'content' => 'Content...',
            'category_id' => Category::create(['name' => 'General', 'slug' => 'general-cat'])->id,
            'status' => 'published',
            'published_at' => now()
        ]);

        // 2. Guest visits the homepage: should NOT see Edit or Delete links/forms
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Story Action Testing');
        $response->assertDontSee(route('posts.edit', $post->id));
        $response->assertDontSee(route('posts.destroy', $post->id));

        // 3. Log in as writerUser2 (different author): should NOT see Edit or Delete links/forms for writerUser1's post
        $this->actingAs($this->writerUser2);
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee(route('posts.edit', $post->id));
        $response->assertDontSee(route('posts.destroy', $post->id));

        // 4. Log in as writerUser1 (the owner): should see Edit and Delete buttons/forms
        $this->actingAs($this->writerUser1);
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee(route('posts.edit', $post->id));
        $response->assertSee(route('posts.destroy', $post->id));

        // Check the stories index page as owner
        $response = $this->get(route('stories.index'));
        $response->assertStatus(200);
        $response->assertSee(route('posts.edit', $post->id));
        $response->assertSee(route('posts.destroy', $post->id));

        // Check the author profile page as owner
        $response = $this->get(route('authors.show', $this->authorProfile1->slug));
        $response->assertStatus(200);
        $response->assertSee(route('posts.edit', $post->id));
        $response->assertSee(route('posts.destroy', $post->id));

        // 5. Log in as Admin: should see Edit and Delete buttons/forms even though not the owner
        $this->actingAs($this->admin);
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee(route('posts.edit', $post->id));
        $response->assertSee(route('posts.destroy', $post->id));
    }
}
