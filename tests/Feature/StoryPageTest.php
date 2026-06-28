<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\HeroImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_story_page_loads_and_passes_correct_data(): void
    {
        // 1. Setup mock data
        $user = User::factory()->create([
            'is_approved' => true,
            'role' => 'author'
        ]);

        $category = Category::create([
            'name' => 'பதிவுகள்',
            'slug' => 'pathivugal',
            'order' => 1
        ]);

        $bloggersCategory = Category::create([
            'name' => 'பதிவர்கள்',
            'slug' => 'pathivargal',
            'order' => 2
        ]);
        
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'பயணக் கதைகள்',
            'slug' => 'travel-stories',
            'order' => 1
        ]);
        
        $authorSub = Subcategory::create([
            'category_id' => $bloggersCategory->id,
            'name' => 'விஜய் ஆனந்த்',
            'slug' => 'vijay-anand',
            'order' => 1
        ]);

        $post = Post::create([
            'title' => 'விண்மீன் கதை',
            'slug' => 'star-story',
            'content' => 'இது ஒரு விண்மீன் கதை.',
            'status' => 'published',
            'published_at' => now(),
            'category_id' => $category->id,
            'subcategory_id' => $subCategory->id,
            'author_id' => $user->id,
            'author_subcategory_id' => $authorSub->id,
        ]);

        $metadataType = \App\Models\MetadataType::create([
            'category_id' => $category->id,
            'name' => 'மொழி',
            'slug' => 'language',
            'name_en' => 'Language'
        ]);

        $metadataValue = \App\Models\MetadataValue::create([
            'metadata_type_id' => $metadataType->id,
            'name' => 'தமிழ்',
            'slug' => 'tamil',
            'name_en' => 'Tamil'
        ]);

        $post->metadataValues()->sync([$metadataValue->id]);

        $heroImage = HeroImage::create([
            'image_path' => 'hero-1.jpg',
            'is_active' => true,
            'order' => 1
        ]);

        // 2. Perform Request
        $response = $this->get(route('about'));

        // 3. Verify Response
        $response->assertStatus(200);
        $response->assertViewIs('story');
        $response->assertViewHas('categories');
        $response->assertViewHas('heroImages');
        $response->assertViewHas('universeCategories');
        $response->assertViewHas('universeWriters');

        // Verify that the metadata value is present in universeCategories
        $universeCategories = $response->viewData('universeCategories');
        $this->assertTrue(collect($universeCategories)->contains(function($val) {
            return $val['name'] === 'தமிழ் (மொழி)' && $val['type'] === 'metadata' && $val['url'] === '/stories?metadata_values[]=tamil';
        }));

        // Check if title and writers are in response view data or page text
        $response->assertSee('“கதைங்கோவின்” கதை!!!');
        $response->assertSee('கதைங்கோவின் பிரபஞ்சம்');
    }
}
