<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'order', 'image_path', 'user_id', 'email', 'phone', 'facebook_url', 'instagram_url', 'linkedin_url', 'topics', 'name_en', 'description_en', 'trust_level', 'tagline', 'tagline_en', 'is_featured'];

    protected static function booted()
    {
        static::saving(function ($subcategory) {
            $translator = app(\App\Services\TranslationService::class);

            if (!$subcategory->isDirty('name_en') && ($subcategory->isDirty('name') || empty($subcategory->name_en))) {
                $subcategory->name_en = $translator->translate($subcategory->name, 'ta', 'en');
            }

            if (isset($subcategory->description) && !$subcategory->isDirty('description_en') && ($subcategory->isDirty('description') || empty($subcategory->description_en))) {
                $subcategory->description_en = $translator->translate($subcategory->description, 'ta', 'en');
            }

            if (isset($subcategory->tagline) && !$subcategory->isDirty('tagline_en') && ($subcategory->isDirty('tagline') || empty($subcategory->tagline_en))) {
                $subcategory->tagline_en = $translator->translate($subcategory->tagline, 'ta', 'en');
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

    public function getTaglineAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->tagline_en)) {
            return $this->tagline_en;
        }
        return $value;
    }

    /**
     * Efficient statistics query scope for listing/discovery pages.
     */
    public function scopeWithWriterStats($query)
    {
        return $query->withCount(['authoredPosts as published_posts_count' => function ($q) {
            $q->where('status', 'published');
        }])->withSum(['authoredPosts as total_reads' => function ($q) {
            $q->where('status', 'published');
        }], 'views_count')->withSum(['authoredPosts as total_likes' => function ($q) {
            $q->where('status', 'published');
        }], 'likes_count');
    }

    /**
     * Scope to filter writers by content category.
     */
    public function scopeWhereWritesInCategory($query, $categoryId)
    {
        return $query->whereHas('authoredPosts', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId)->where('status', 'published');
        });
    }

    /**
     * Scope to filter featured writers.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter writer profiles only (subcategories of pathivargal category).
     */
    public function scopeWritersOnly($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('slug', 'pathivargal');
        });
    }

    /**
     * Single writer page accessors (safe for detail pages, not large listings).
     */
    public function getPostCountAttribute()
    {
        return $this->authoredPosts()->where('status', 'published')->count();
    }

    public function getTotalReadsAttribute()
    {
        return (int) $this->authoredPosts()->where('status', 'published')->sum('views_count');
    }

    public function getEngagementScoreAttribute()
    {
        return (int) $this->authoredPosts()->where('status', 'published')->sum('likes_count');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function childCategories()
    {
        return $this->hasMany(ChildCategory::class)->orderBy('order');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function authoredPosts()
    {
        return $this->hasMany(Post::class, 'author_subcategory_id');
    }

    public function getAvatarUrl()
    {
        $isKathaingo = str_contains(strtolower($this->slug ?? ''), 'kathaingo') ||
                       str_contains(mb_strtolower($this->getRawOriginal('name') ?? '', 'UTF-8'), 'கதைங்கோ') ||
                       str_contains(strtolower($this->name_en ?? ''), 'kathaingo');

        if ($isKathaingo) {
            return asset('images/logo/apple-touch-icon.png');
        }

        if ($this->image_path && file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }

        return null;
    }
}
