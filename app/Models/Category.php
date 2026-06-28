<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order', 'name_en', 'description_en'];

    protected static function booted()
    {
        static::saving(function ($category) {
            $translator = app(\App\Services\TranslationService::class);

            if ($category->isDirty('name') || empty($category->name_en)) {
                $category->name_en = $translator->translate($category->name, 'ta', 'en');
            }

            if (isset($category->description) && ($category->isDirty('description') || empty($category->description_en))) {
                $category->description_en = $translator->translate($category->description, 'ta', 'en');
            }
        });
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $value;
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class)->orderBy('order');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function metadataTypes()
    {
        return $this->hasMany(MetadataType::class);
    }

    public static function getActiveCategoriesForNavigation()
    {
        $bloggersCategory = self::where('slug', 'pathivargal')->first();
        $bloggersCategoryId = $bloggersCategory ? $bloggersCategory->id : 4;

        $categories = self::where('slug', '!=', 'pathivargal')->with([
            'subcategories' => function ($query) {
                $query->withCount([
                    'authoredPosts' => function ($q) {
                        $q->where('status', 'published');
                    },
                    'posts' => function ($q) {
                        $q->where('status', 'published');
                    }
                ]);
            },
            'subcategories.childCategories' => function ($query) {
                $query->withCount([
                    'posts' => function ($q) {
                        $q->where('status', 'published');
                    }
                ]);
            },
            'subcategories.childCategories.grandchildCategories' => function ($query) {
                $query->withCount([
                    'posts' => function ($q) {
                        $q->where('status', 'published');
                    }
                ]);
            }
        ])->orderBy('order')->get();

        return $categories->map(function ($category) use ($bloggersCategoryId) {
            if ($category->id === $bloggersCategoryId) {
                // Keep only subcategories (writers) that have at least one published post
                $filteredSubcategories = $category->subcategories->filter(function ($subcategory) {
                    return $subcategory->authored_posts_count > 0;
                });
                $category->setRelation('subcategories', $filteredSubcategories->sortByDesc('authored_posts_count')->values());
            } else {
                // Filter subcategories, child categories, and grandchild categories
                $filteredSubcategories = $category->subcategories->map(function ($subcategory) {
                    // Filter child categories first
                    $filteredChildren = $subcategory->childCategories->map(function ($child) {
                        $filteredGrandchildren = $child->grandchildCategories->filter(function ($grandchild) {
                            return $grandchild->posts_count > 0;
                        });
                        $child->setRelation('grandchildCategories', $filteredGrandchildren->values());

                        $hasDirectPosts = $child->posts_count > 0;
                        $hasGrandchildPosts = $filteredGrandchildren->isNotEmpty();
                        return ($hasDirectPosts || $hasGrandchildPosts) ? $child : null;
                    })->filter()->values();

                    $subcategory->setRelation('childCategories', $filteredChildren);

                    $hasDirectPosts = $subcategory->posts_count > 0;
                    $hasChildPosts = $filteredChildren->isNotEmpty();
                    return ($hasDirectPosts || $hasChildPosts) ? $subcategory : null;
                })->filter()->values();

                $category->setRelation('subcategories', $filteredSubcategories);
            }

            return $category->subcategories->isNotEmpty() ? $category : null;
        })->filter()->values();
    }
}
