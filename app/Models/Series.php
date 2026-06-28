<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $table = 'series';

    protected $fillable = ['title', 'slug', 'description', 'image_path', 'status', 'title_en', 'description_en'];

    protected static function booted()
    {
        static::saving(function ($series) {
            $translator = app(\App\Services\TranslationService::class);

            if (!$series->isDirty('title_en') && ($series->isDirty('title') || empty($series->title_en))) {
                $series->title_en = $translator->translate($series->title, 'ta', 'en');
            }

            if (isset($series->description) && !$series->isDirty('description_en') && ($series->isDirty('description') || empty($series->description_en))) {
                $series->description_en = $translator->translate($series->description, 'ta', 'en');
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

    public function getDescriptionAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $value;
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'series_id')->orderBy('chapter_number');
    }
}
