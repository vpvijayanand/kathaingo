<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Helpers\CountryHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryMapTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $writer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::factory()->create([
            'is_admin' => true,
            'is_approved' => true,
        ]);

        // Create Writer User
        $this->writer = User::factory()->create([
            'is_admin' => false,
            'is_approved' => true,
        ]);
    }

    public function test_can_view_countries_map_page()
    {
        $response = $this->get(route('countries.index'));
        $response->assertStatus(200);
        $response->assertViewHas('activeCountries');
    }

    public function test_can_view_specific_country_page()
    {
        // View page for India ('in')
        $response = $this->get(route('countries.show', 'in'));
        $response->assertStatus(200);
        $response->assertViewHas('posts');
        $response->assertSee('India');
    }

    public function test_invalid_country_returns_404()
    {
        $response = $this->get(route('countries.show', 'xx'));
        $response->assertStatus(404);
    }

    public function test_admin_can_save_post_with_country_code()
    {
        $this->actingAs($this->admin);

        // Seed a category
        $category = Category::create([
            'name' => 'பதிவுகள்',
            'slug' => 'pathivugal',
            'order' => 1
        ]);

        $postData = [
            'title' => 'Travel story in India',
            'content' => 'This is a travel story set in India.',
            'status' => 'published',
            'category_id' => $category->id,
            'country_code' => 'IN', // Upper case, should be converted to lower case by controller
        ];

        $response = $this->post(route('posts.store'), $postData);
        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Travel story in India',
            'country_code' => 'in', // verify it was saved in lower case
        ]);
    }

    public function test_sorting_by_popularity_and_latest()
    {
        // Seed a category
        $category = Category::create([
            'name' => 'பதிவுகள்',
            'slug' => 'pathivugal',
            'order' => 1
        ]);

        // Create two posts for India ('in')
        $post1 = Post::create([
            'title' => 'Earlier Post',
            'slug' => 'earlier-post',
            'content' => 'Content...',
            'status' => 'published',
            'published_at' => now()->subDays(5),
            'views_count' => 100,
            'country_code' => 'in',
            'category_id' => $category->id,
            'author_id' => $this->writer->id,
        ]);

        $post2 = Post::create([
            'title' => 'Later Post',
            'slug' => 'later-post',
            'content' => 'Content...',
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 10,
            'country_code' => 'in',
            'category_id' => $category->id,
            'author_id' => $this->writer->id,
        ]);

        // Test sorting by latest (default)
        $response = $this->get(route('countries.show', ['country_code' => 'in', 'sort' => 'latest']));
        $posts = $response->viewData('posts');
        $this->assertEquals('Later Post', $posts->first()->title); // $post2 is newer

        // Test sorting by popularity
        $response = $this->get(route('countries.show', ['country_code' => 'in', 'sort' => 'popular']));
        $posts = $response->viewData('posts');
        $this->assertEquals('Earlier Post', $posts->first()->title); // $post1 has more views
    }
}
