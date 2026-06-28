<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected $writer;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writer = User::factory()->create([
            'is_approved' => true,
            'role' => 'author'
        ]);

        $this->category = Category::create([
            'name' => 'பதிவுகள்',
            'slug' => 'pathivugal',
            'order' => 1
        ]);
    }

    public function test_suggestions_api_requires_at_least_two_characters()
    {
        // 1 character search query
        $response = $this->getJson(route('api.search', ['q' => 'a']));
        
        $response->assertStatus(200);
        $response->assertJson([
            'posts' => [],
            'authors' => [],
            'categories' => []
        ]);
    }

    public function test_suggestions_api_returns_grouped_results()
    {
        // Create matching blogger subcategory (Author)
        $bloggersCategory = Category::create([
            'name' => 'பதிவர்கள்',
            'slug' => 'pathivargal',
            'order' => 2
        ]);
        
        $authorSub = Subcategory::create([
            'category_id' => $bloggersCategory->id,
            'name' => 'விஜய் ஆனந்த்',
            'name_en' => 'Vijay Anand',
            'slug' => 'vijay-anand',
            'order' => 1
        ]);

        // Create matching category path
        $travelSub = Subcategory::create([
            'category_id' => $this->category->id,
            'name' => 'பயணக் கதைகள்',
            'name_en' => 'Travel Stories',
            'slug' => 'travel-stories',
            'order' => 1
        ]);

        // Create matching post
        $post = Post::create([
            'title' => 'அற்புதமான பயணம்',
            'slug' => 'amazing-travel-post',
            'content' => 'இது ஒரு அற்புதமான பயணக் கதை.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $this->category->id,
            'author_id' => $this->writer->id,
        ]);

        // Search for 'பயண' (Travel in Tamil)
        $response = $this->getJson(route('api.search', ['q' => 'பயண']));
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'posts',
            'authors',
            'categories'
        ]);
        
        // Assert category "பயணக் கதைகள்" is returned
        $response->assertJsonFragment([
            'name' => 'பயணக் கதைகள்',
            'slug' => 'travel-stories',
            'type' => 'subcategory'
        ]);

        // Assert post "அற்புதாமான பயணம்" is returned
        $response->assertJsonFragment([
            'title' => 'அற்புதமான பயணம்',
            'slug' => 'amazing-travel-post'
        ]);
    }

    public function test_homepage_filters_posts_by_search_term()
    {
        // Post 1 matching search term
        $matchPost = Post::create([
            'title' => 'Unique Searchable Title',
            'slug' => 'unique-searchable-title',
            'content' => 'Content of match post.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $this->category->id,
            'author_id' => $this->writer->id,
        ]);

        // Post 2 not matching search term
        $nonMatchPost = Post::create([
            'title' => 'Unrelated Topic',
            'slug' => 'unrelated-topic',
            'content' => 'Content of other post.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $this->category->id,
            'author_id' => $this->writer->id,
        ]);

        // Visit stories index page with search query parameter
        $response = $this->get('/stories?search=Unique');
        
        $response->assertStatus(200);
        
        // Assert matches are displayed and unrelated items are omitted
        $response->assertSee('Unique Searchable Title');
        $response->assertDontSee('Unrelated Topic');
        
        // Assert active search filter text is displayed
        if (app()->getLocale() === 'ta') {
            $response->assertSee('"Unique" க்கான தேடல் முடிவுகள்');
        } else {
            $response->assertSee('Search results for "Unique"');
        }
    }
}
