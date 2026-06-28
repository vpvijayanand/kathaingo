<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;
use App\Models\Post;
use App\Services\TranslationService;

class TranslateExistingDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translates all existing Tamil content in categories, subcategories, tags, and posts to English';

    /**
     * Execute the console command.
     */
    public function handle(TranslationService $translator)
    {
        $this->info('Starting translation of existing database records...');

        // 1. Categories
        $categories = Category::all();
        $this->info("Translating {$categories->count()} Categories...");
        foreach ($categories as $category) {
            $this->line("Category: {$category->name}");
            if (empty($category->name_en)) {
                $category->name_en = $translator->translate($category->name);
            }
            if (empty($category->description_en) && !empty($category->description)) {
                $category->description_en = $translator->translate($category->description);
            }
            $category->saveQuietly();
        }

        // 2. Subcategories
        $subcategories = Subcategory::all();
        $this->info("Translating {$subcategories->count()} Subcategories...");
        foreach ($subcategories as $sub) {
            $this->line("Subcategory: {$sub->name}");
            if (empty($sub->name_en)) {
                $sub->name_en = $translator->translate($sub->name);
            }
            if (empty($sub->description_en) && !empty($sub->description)) {
                $sub->description_en = $translator->translate($sub->description);
            }
            $sub->saveQuietly();
        }

        // 3. ChildCategories
        $childCategories = ChildCategory::all();
        $this->info("Translating {$childCategories->count()} Child Categories...");
        foreach ($childCategories as $child) {
            $this->line("Child Category: {$child->name}");
            if (empty($child->name_en)) {
                $child->name_en = $translator->translate($child->name);
            }
            $child->saveQuietly();
        }

        // 4. GrandchildCategories
        $grandchildCategories = GrandchildCategory::all();
        $this->info("Translating {$grandchildCategories->count()} Grandchild Categories...");
        foreach ($grandchildCategories as $grandchild) {
            $this->line("Grandchild Category: {$grandchild->name}");
            if (empty($grandchild->name_en)) {
                $grandchild->name_en = $translator->translate($grandchild->name);
            }
            $grandchild->saveQuietly();
        }

        // 5. Posts
        $posts = Post::all();
        $this->info("Translating {$posts->count()} Posts...");
        foreach ($posts as $post) {
            $this->line("Post: {$post->title}");
            if (empty($post->title_en)) {
                $post->title_en = $translator->translate($post->title);
            }
            if (empty($post->content_en) && !empty($post->content)) {
                // Ensure content is parsed and translated safely
                $post->content_en = $translator->translateHtml($post->content);
            }
            $post->saveQuietly();
        }

        $this->info('Database translation complete!');
    }
}
