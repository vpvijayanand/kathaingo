<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'name_en'];

    protected static function booted()
    {
        static::saving(function ($tag) {
            $translator = app(\App\Services\TranslationService::class);

            if ($tag->isDirty('name') || empty($tag->name_en)) {
                $tag->name_en = $translator->translate($tag->name, 'ta', 'en');
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

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id');
    }
}
