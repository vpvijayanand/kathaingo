<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'image', 'featured_image', 'video_url', 'status', 'published_at', 'author_id', 'category_id', 'subcategory_id', 'child_category_id', 'grandchild_category_id', 'author_subcategory_id', 'country_code', 'title_en', 'content_en', 'smiley_count', 'thumbs_up_count', 'thumbs_down_count', 'angry_count', 'crying_count', 'series_id', 'volume', 'chapter_number', 'hashtags'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($post) {
            $translator = app(\App\Services\TranslationService::class);

            if ($post->isDirty('title') || empty($post->title_en)) {
                $post->title_en = $translator->translate($post->title, 'ta', 'en');
            }

            if ($post->isDirty('content') || empty($post->content_en)) {
                $post->content_en = $translator->translateHtml($post->content, 'ta', 'en');
            }
        });
    }

    public function getTitleAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return $value;
    }

    public function getContentAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->content_en)) {
            return $this->content_en;
        }
        return $value;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function authorSubcategory()
    {
        return $this->belongsTo(Subcategory::class, 'author_subcategory_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', true)->orderBy('created_at', 'desc');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }

    public function grandchildCategory()
    {
        return $this->belongsTo(GrandchildCategory::class);
    }

    public function getCountryNameAttribute()
    {
        return $this->country_code ? \App\Helpers\CountryHelper::getName($this->country_code) : null;
    }

    public function revisions()
    {
        return $this->hasMany(PostRevision::class)->orderBy('created_at', 'desc');
    }

    public function feedback()
    {
        return $this->hasMany(PostFeedback::class)->orderBy('created_at', 'desc');
    }

    public function metadataValues()
    {
        return $this->belongsToMany(MetadataValue::class, 'post_metadata', 'post_id', 'metadata_value_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function setHashtagsAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['hashtags'] = null;
            return;
        }

        // Split by spaces, commas, or # characters
        $words = preg_split('/[\s,\#]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        
        // Take at most 3
        $words = array_slice($words, 0, 3);

        // Prepend # to each word and join them
        $formatted = array_map(function($word) {
            return '#' . ltrim($word, '#');
        }, $words);

        $this->attributes['hashtags'] = implode(' ', $formatted);
    }
}
