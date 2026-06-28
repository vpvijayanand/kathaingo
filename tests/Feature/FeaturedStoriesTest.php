<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedStoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_posts_selection_and_exclusion(): void
    {
        // 1. Setup User
        $user = User::factory()->create([
            'is_approved' => true,
            'role' => 'author'
        ]);

        // 2. Setup 3 Categories
        $cat1 = Category::create(['name' => 'Category One', 'slug' => 'category-one', 'order' => 1]);
        $cat2 = Category::create(['name' => 'Category Two', 'slug' => 'category-two', 'order' => 2]);
        $cat3 = Category::create(['name' => 'Category Three', 'slug' => 'category-three', 'order' => 3]);

        // 3. Create posts with different engagement levels
        // Post A: Category 1. 5 reactions, 2 approved comments. Total = 7
        $postA = Post::create([
            'title' => 'Post A Title',
            'slug' => 'post-a',
            'content' => 'Content of Post A',
            'status' => 'published',
            'published_at' => now()->subHours(4),
            'category_id' => $cat1->id,
            'author_id' => $user->id,
        ]);
        Comment::create(['post_id' => $postA->id, 'author_name' => 'John', 'content' => 'Comment 1', 'is_approved' => true]);
        Comment::create(['post_id' => $postA->id, 'author_name' => 'John', 'content' => 'Comment 2', 'is_approved' => true]);

        // Post B: Category 1. 2 reactions, 1 approved comment. Total = 3
        $postB = Post::create([
            'title' => 'Post B Title',
            'slug' => 'post-b',
            'content' => 'Content of Post B',
            'status' => 'published',
            'published_at' => now()->subHours(3),
            'category_id' => $cat1->id,
            'author_id' => $user->id,
        ]);
        Comment::create(['post_id' => $postB->id, 'author_name' => 'John', 'content' => 'Comment 3', 'is_approved' => true]);

        // Post C: Category 2. 1 reaction, 0 comments. Total = 1
        $postC = Post::create([
            'title' => 'Post C Title',
            'slug' => 'post-c',
            'content' => 'Content of Post C',
            'status' => 'published',
            'published_at' => now()->subHours(2),
            'category_id' => $cat2->id,
            'author_id' => $user->id,
        ]);

        // Post D: Category 3. 4 reactions, 1 approved comment. Total = 5
        $postD = Post::create([
            'title' => 'Post D Title',
            'slug' => 'post-d',
            'content' => 'Content of Post D',
            'status' => 'published',
            'published_at' => now()->subHours(1),
            'category_id' => $cat3->id,
            'author_id' => $user->id,
        ]);
        Comment::create(['post_id' => $postD->id, 'author_name' => 'John', 'content' => 'Comment 4', 'is_approved' => true]);

        // Create reactors (users) and actual reaction records
        $reactors = User::factory()->count(5)->create();
        
        // Post A: 5 reactions (3 like, 2 love)
        foreach ($reactors->take(3) as $r) {
            \App\Models\PostReaction::create(['post_id' => $postA->id, 'user_id' => $r->id, 'reaction_type' => 'like']);
        }
        foreach ($reactors->slice(3, 2) as $r) {
            \App\Models\PostReaction::create(['post_id' => $postA->id, 'user_id' => $r->id, 'reaction_type' => 'love']);
        }

        // Post B: 2 reactions (2 like)
        foreach ($reactors->take(2) as $r) {
            \App\Models\PostReaction::create(['post_id' => $postB->id, 'user_id' => $r->id, 'reaction_type' => 'like']);
        }

        // Post C: 1 reaction (1 clap)
        \App\Models\PostReaction::create(['post_id' => $postC->id, 'user_id' => $reactors[0]->id, 'reaction_type' => 'clap']);

        // Post D: 4 reactions (4 agree)
        foreach ($reactors->take(4) as $r) {
            \App\Models\PostReaction::create(['post_id' => $postD->id, 'user_id' => $r->id, 'reaction_type' => 'agree']);
        }

        // 4. Hit stories page
        $response = $this->get(route('stories.index'));

        // 5. Verify Response is successful
        $response->assertStatus(200);

        // 6. Verify Featured Posts data
        $response->assertViewHas('featuredPosts', function ($featured) use ($postA, $postC, $postD, $postB) {
            $ids = $featured->pluck('id')->toArray();
            
            // Should contain Post A, D, and C
            $hasPostA = in_array($postA->id, $ids);
            $hasPostD = in_array($postD->id, $ids);
            $hasPostC = in_array($postC->id, $ids);
            $hasPostB = in_array($postB->id, $ids);

            // Featured posts count should be 3
            return $featured->count() === 3 && $hasPostA && $hasPostD && $hasPostC && !$hasPostB;
        });

        // 7. Verify Main Grid ($posts) has Post B but NOT Post A, D, or C
        $response->assertViewHas('posts', function ($posts) use ($postA, $postB, $postC, $postD) {
            $ids = $posts->pluck('id')->toArray();
            return in_array($postB->id, $ids) && 
                   !in_array($postA->id, $ids) && 
                   !in_array($postC->id, $ids) && 
                   !in_array($postD->id, $ids);
        });

        // 8. Verify featured posts titles are visible in response HTML
        $response->assertSee('Post A Title');
        $response->assertSee('Post C Title');
        $response->assertSee('Post D Title');
        $response->assertSee('Post B Title'); // visible in the latest stories grid
    }
}
