<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PostViewsCountTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $post;
    private $category;
    private $subCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->user = User::factory()->create([
            'is_approved' => true,
            'role' => 'author'
        ]);

        $this->category = Category::create([
            'name' => 'பதிவுகள்',
            'slug' => 'pathivugal',
            'order' => 1
        ]);

        $this->subCategory = Subcategory::create([
            'category_id' => $this->category->id,
            'name' => 'பயணக் கதைகள்',
            'slug' => 'travel-stories',
            'order' => 1
        ]);

        $this->post = Post::create([
            'title' => 'விண்மீன் கதை',
            'slug' => 'star-story',
            'content' => 'இது ஒரு விண்மீன் கதை.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $this->category->id,
            'subcategory_id' => $this->subCategory->id,
            'author_id' => $this->user->id,
            'views_count' => 0
        ]);
    }

    public function test_viewing_post_does_not_immediately_increment_views_count(): void
    {
        $this->assertEquals(0, $this->post->fresh()->views_count);

        $response = $this->get(route('posts.show', $this->post->slug));
        $response->assertStatus(200);

        // Should still be 0
        $this->assertEquals(0, $this->post->fresh()->views_count);
    }

    public function test_telemetry_post_to_read_increments_views_count(): void
    {
        $this->assertEquals(0, $this->post->fresh()->views_count);

        // Guest user sends telemetry
        $response = $this->post(route('posts.read', $this->post->slug));
        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true, 'incremented' => true, 'views_count' => 1]);

        $this->assertEquals(1, $this->post->fresh()->views_count);
    }

    public function test_subsequent_telemetry_posts_in_same_session_do_not_increment(): void
    {
        $this->assertEquals(0, $this->post->fresh()->views_count);

        // First telemetry post
        $response = $this->post(route('posts.read', $this->post->slug));
        $response->assertStatus(200);
        $this->assertEquals(1, $this->post->fresh()->views_count);

        // Second telemetry post in same session
        $response2 = $this->post(route('posts.read', $this->post->slug));
        $response2->assertStatus(200);
        $response2->assertJsonFragment(['success' => true, 'incremented' => false, 'views_count' => 1]);
        $this->assertEquals(1, $this->post->fresh()->views_count);
    }

    public function test_logged_in_user_telemetry_persists_read_status_across_sessions(): void
    {
        $this->assertEquals(0, $this->post->fresh()->views_count);

        // Login user
        $this->actingAs($this->user);

        // Record read
        $response = $this->post(route('posts.read', $this->post->slug));
        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true, 'incremented' => true, 'views_count' => 1]);
        $this->assertEquals(1, $this->post->fresh()->views_count);

        // Log out user
        auth()->logout();

        // Login again (new session simulated by actingAs again)
        $this->actingAs($this->user);

        // Send telemetry again
        $response2 = $this->post(route('posts.read', $this->post->slug));
        $response2->assertStatus(200);
        // It shouldn't increment because user ID is cached forever
        $response2->assertJsonFragment(['success' => true, 'incremented' => false, 'views_count' => 1]);
        $this->assertEquals(1, $this->post->fresh()->views_count);
    }

    public function test_reactions_and_comments_do_not_increment_views_count(): void
    {
        $this->actingAs($this->user);

        // 1. View page (does not increment)
        $this->get(route('posts.show', $this->post->slug));
        $this->assertEquals(0, $this->post->fresh()->views_count);

        // 2. React (does not increment)
        $response = $this->post(route('posts.react', $this->post->slug), [
            'type' => 'love'
        ]);
        $response->assertJsonFragment(['success' => true]);
        $this->assertEquals(0, $this->post->fresh()->views_count);

        // 3. Comment (does not increment)
        $response2 = $this->post(route('posts.storeComment', $this->post->slug), [
            'author_name' => 'விஜய் ஆனந்த்',
            'content' => 'நன்று!'
        ]);
        $response2->assertStatus(302);
        $this->assertEquals(0, $this->post->fresh()->views_count);
    }
}
