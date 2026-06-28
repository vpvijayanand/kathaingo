<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\GrandchildCategory;

class SearchService
{
    /**
     * Search posts, authors, and category paths matching the search term.
     *
     * @param string $term
     * @return array
     */
    public function searchAll(string $term): array
    {
        $term = trim($term);
        if (strlen($term) < 2) {
            return [
                'posts' => [],
                'authors' => [],
                'categories' => []
            ];
        }

        // 1. Search Posts
        $posts = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            })
            ->limit(5)
            ->get()
            ->map(function($post) {
                return [
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt ?? (mb_substr(strip_tags($post->content), 0, 100) . '...'),
                    'author_name' => $post->author ? $post->author->name : null,
                ];
            })
            ->toArray();

        // 2. Search Authors (Subcategories of pathivargal category)
        $bloggersCategory = Category::where('slug', 'pathivargal')->first();
        $bloggersCategoryId = $bloggersCategory ? $bloggersCategory->id : 4;

        $authors = Subcategory::where('category_id', $bloggersCategoryId)
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_en', 'like', "%{$term}%");
            })
            ->limit(5)
            ->get()
            ->map(function($sub) {
                return [
                    'name' => $sub->name,
                    'slug' => $sub->slug,
                ];
            })
            ->toArray();

        // 3. Search Categories (subcategories, child, and grandchild tiers)
        $categoriesList = [];

        // Search subcategories (excluding authors)
        $subCategories = Subcategory::where('category_id', '!=', $bloggersCategoryId)
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('name_en', 'like', "%{$term}%");
            })
            ->limit(3)
            ->get();
            
        foreach ($subCategories as $sub) {
            $parentName = $sub->category ? $sub->category->name : '';
            $categoriesList[] = [
                'name' => $sub->name,
                'slug' => $sub->slug,
                'type' => 'subcategory',
                'path' => (!empty($parentName) ? ($parentName . ' > ') : '') . $sub->name
            ];
        }

        // Search child categories
        $childCategories = ChildCategory::where('name', 'like', "%{$term}%")
            ->orWhere('name_en', 'like', "%{$term}%")
            ->limit(3)
            ->get();

        foreach ($childCategories as $child) {
            $path = '';
            if ($child->subcategory) {
                if ($child->subcategory->category) {
                    $path .= $child->subcategory->category->name . ' > ';
                }
                $path .= $child->subcategory->name . ' > ';
            }
            $path .= $child->name;

            $categoriesList[] = [
                'name' => $child->name,
                'slug' => $child->slug,
                'type' => 'child_category',
                'path' => $path
            ];
        }

        // Search grandchild categories
        $grandchildCategories = GrandchildCategory::where('name', 'like', "%{$term}%")
            ->orWhere('name_en', 'like', "%{$term}%")
            ->limit(3)
            ->get();

        foreach ($grandchildCategories as $grand) {
            $path = '';
            if ($grand->childCategory) {
                if ($grand->childCategory->subcategory) {
                    if ($grand->childCategory->subcategory->category) {
                        $path .= $grand->childCategory->subcategory->category->name . ' > ';
                    }
                    $path .= $grand->childCategory->subcategory->name . ' > ';
                }
                $path .= $grand->childCategory->name . ' > ';
            }
            $path .= $grand->name;

            $categoriesList[] = [
                'name' => $grand->name,
                'slug' => $grand->slug,
                'type' => 'grandchild_category',
                'path' => $path
            ];
        }

        return [
            'posts' => $posts,
            'authors' => $authors,
            'categories' => array_slice($categoriesList, 0, 5)
        ];
    }
}
