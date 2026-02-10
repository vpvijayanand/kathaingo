<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Technology Category
        $tech = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Latest tech trends, innovations, and insights',
            'order' => 1,
        ]);

        Subcategory::create(['category_id' => $tech->id, 'name' => 'Web Development', 'slug' => 'web-development', 'order' => 1]);
        Subcategory::create(['category_id' => $tech->id, 'name' => 'Mobile Apps', 'slug' => 'mobile-apps', 'order' => 2]);
        Subcategory::create(['category_id' => $tech->id, 'name' => 'AI & Machine Learning', 'slug' => 'ai-machine-learning', 'order' => 3]);

        // Lifestyle Category
        $lifestyle = Category::create([
            'name' => 'Lifestyle',
            'slug' => 'lifestyle',
            'description' => 'Living well, balanced and inspired',
            'order' => 2,
        ]);

        Subcategory::create(['category_id' => $lifestyle->id, 'name' => 'Health & Fitness', 'slug' => 'health-fitness', 'order' => 1]);
        Subcategory::create(['category_id' => $lifestyle->id, 'name' => 'Travel', 'slug' => 'travel', 'order' => 2]);
        Subcategory::create(['category_id' => $lifestyle->id, 'name' => 'Food & Cooking', 'slug' => 'food-cooking', 'order' => 3]);

        // Business Category
        $business = Category::create([
            'name' => 'Business',
            'slug' => 'business',
            'description' => 'Entrepreneurship, strategy, and growth',
            'order' => 3,
        ]);

        Subcategory::create(['category_id' => $business->id, 'name' => 'Startups', 'slug' => 'startups', 'order' => 1]);
        Subcategory::create(['category_id' => $business->id, 'name' => 'Marketing', 'slug' => 'marketing', 'order' => 2]);
        Subcategory::create(['category_id' => $business->id, 'name' => 'Finance', 'slug' => 'finance', 'order' => 3]);

        // Creative Category
        $creative = Category::create([
            'name' => 'Creative',
            'slug' => 'creative',
            'description' => 'Art, design, and creative expression',
            'order' => 4,
        ]);

        Subcategory::create(['category_id' => $creative->id, 'name' => 'Design', 'slug' => 'design', 'order' => 1]);
        Subcategory::create(['category_id' => $creative->id, 'name' => 'Photography', 'slug' => 'photography', 'order' => 2]);
        Subcategory::create(['category_id' => $creative->id, 'name' => 'Writing', 'slug' => 'writing', 'order' => 3]);
    }
}
