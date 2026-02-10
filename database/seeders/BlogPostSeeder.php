<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@kathaingo.com')->first();
        if (!$admin) {
            $this->command->error('Admin user not found. Please run AdminUserSeeder first.');
            return;
        }

        $tech = Category::where('slug', 'technology')->first();
        $lifestyle = Category::where('slug', 'lifestyle')->first();
        $business = Category::where('slug', 'business')->first();
        $creative = Category::where('slug', 'creative')->first();

        $posts = [
            [
                'title' => 'The Future of Web Development in 2026',
                'content' => 'As we navigate through 2026, web development continues to evolve at an unprecedented pace. Modern frameworks are pushing the boundaries of what\'s possible in the browser, with improved performance and developer experience. From server-side rendering to edge computing, the landscape is richer than ever. This article explores the cutting-edge technologies reshaping how we build for the web.',
                'category_id' => $tech?->id,
                'subcategory_id' => Subcategory::where('slug', 'web-development')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6',
            ],
            [
                'title' => 'AI Revolution: How Machine Learning Changes Everything',
                'content' => 'Artificial Intelligence has moved from science fiction to everyday reality. Machine learning models are now accessible to developers of all skill levels, democratizing AI development. Whether it\'s natural language processing, computer vision, or predictive analytics, AI tools are transforming industries across the board. Learn how you can leverage these powerful technologies in your next project.',
                'category_id' => $tech?->id,
                'subcategory_id' => Subcategory::where('slug', 'ai-machine-learning')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995',
            ],
            [
                'title' => '10 Essential Tips for a Healthier Lifestyle',
                'content' => 'Living a healthy lifestyle doesn\'t have to be complicated. Small, consistent changes can lead to remarkable transformations. From mindful eating to regular exercise, these ten practical tips will help you build sustainable habits. Discover simple strategies to boost your energy, improve your sleep, and feel your best every day.',
                'category_id' => $lifestyle?->id,
                'subcategory_id' => Subcategory::where('slug', 'health-fitness')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b',
            ],
            [
                'title' => 'Hidden Gems: Off-the-Beaten-Path Travel Destinations',
                'content' => 'Tired of crowded tourist spots? Discover breathtaking destinations that offer authentic experiences away from the masses. From secluded beaches to mountain villages, these hidden gems provide unforgettable adventures. Pack your bags and explore places where culture, nature, and tranquility meet in perfect harmony.',
                'category_id' => $lifestyle?->id,
                'subcategory_id' => Subcategory::where('slug', 'travel')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828',
            ],
            [
                'title' => 'Startup Success: From Idea to Launch in 90 Days',
                'content' => 'Building a startup doesn\'t have to take years. With the right approach, you can validate your idea and launch your minimum viable product in just three months. This comprehensive guide walks you through the essential steps: market research, rapid prototyping, customer feedback, and go-to-market strategy. Turn your entrepreneurial dreams into reality.',
                'category_id' => $business?->id,
                'subcategory_id' => Subcategory::where('slug', 'startups')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0',
            ],
            [
                'title' => 'Digital Marketing Strategies That Actually Work',
                'content' => 'Cut through the noise with proven digital marketing tactics that deliver real results. Learn how to craft compelling content, optimize for search engines, and leverage social media effectively. Whether you\'re a solopreneur or managing a team, these strategies will help you reach your target audience and drive meaningful engagement.',
                'category_id' => $business?->id,
                'subcategory_id' => Subcategory::where('slug', 'marketing')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f',
            ],
            [
                'title' => 'Mastering Modern UI/UX Design Principles',
                'content' => 'Great design is invisible. It guides users effortlessly through experiences without friction or confusion. Explore the fundamental principles of modern UI/UX design, from color theory and typography to user research and interaction patterns. Create interfaces that are not only beautiful but also intuitive and accessible.',
                'category_id' => $creative?->id,
                'subcategory_id' => Subcategory::where('slug', 'design')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5',
            ],
            [
                'title' => 'Photography Basics: Capturing Stunning Moments',
                'content' => 'Photography is the art of freezing time. Whether you\'re using a professional DSLR or just your smartphone, understanding composition, lighting, and storytelling will elevate your images. This beginner-friendly guide covers essential techniques that will help you capture stunning photographs and express your unique creative vision.',
                'category_id' => $creative?->id,
                'subcategory_id' => Subcategory::where('slug', 'photography')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d',
            ],
            [
                'title' => 'The Art of Storytelling: Write Content That Resonates',
                'content' => 'Every great piece of writing tells a story. Whether crafting blog posts, novels, or marketing copy, the principles remain the same: know your audience, create compelling characters or ideas, and structure your narrative for maximum impact. Discover how to write content that captivates readers and leaves a lasting impression.',
                'category_id' => $creative?->id,
                'subcategory_id' => Subcategory::where('slug', 'writing')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a',
            ],
            [
                'title' => 'Mobile App Development: iOS vs Android in 2026',
                'content' => 'Choosing between iOS and Android development? Both platforms offer unique advantages and challenges. Swift and SwiftUI provide elegant solutions for iOS, while Kotlin continues to dominate Android development. Cross-platform frameworks like Flutter and React Native offer compelling alternatives. Compare the ecosystems and make an informed decision for your next mobile project.',
                'category_id' => $tech?->id,
                'subcategory_id' => Subcategory::where('slug', 'mobile-apps')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c',
            ],
            [
                'title' => 'Delicious Plant-Based Recipes for Busy Weeknights',
                'content' => 'Eating plant-based doesn\'t mean sacrificing flavor or spending hours in the kitchen. These quick, nutritious recipes prove that healthy eating can be both delicious and convenient. From vibrant Buddha bowls to hearty pasta dishes, discover meals that the whole family will love. Transform your weeknight dinners with these simple, satisfying recipes.',
                'category_id' => $lifestyle?->id,
                'subcategory_id' => Subcategory::where('slug', 'food-cooking')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd',
            ],
            [
                'title' => 'Smart Personal Finance: Building Wealth in Your 20s and 30s',
                'content' => 'Financial freedom starts with smart decisions made early. Learn the fundamentals of budgeting, investing, and building emergency funds. Understand the power of compound interest and make your money work for you. Whether you\'re just starting your career or looking to optimize your finances, these strategies will set you on the path to long-term wealth.',
                'category_id' => $business?->id,
                'subcategory_id' => Subcategory::where('slug', 'finance')->first()?->id,
                'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e',
            ],
        ];

        foreach ($posts as $postData) {
            Post::create([
                'title' => $postData['title'],
                'slug' => \Illuminate\Support\Str::slug($postData['title']) . '-' . time() . '-' . rand(1000, 9999),
                'content' => $postData['content'],
                'image' => $postData['image'],
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'author_id' => $admin->id,
                'category_id' => $postData['category_id'],
                'subcategory_id' => $postData['subcategory_id'],
            ]);
        }

        $this->command->info('Created 12 sample blog posts with categories and subcategories');
    }
}
