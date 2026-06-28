<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrandchildCategory extends Model
{
    protected $fillable = [
        'child_category_id',
        'name',
        'slug',
        'order',
        'name_en'
    ];

    protected static function booted()
    {
        static::saving(function ($grandchildCategory) {
            $translator = app(\App\Services\TranslationService::class);

            if ($grandchildCategory->isDirty('name') || empty($grandchildCategory->name_en)) {
                $grandchildCategory->name_en = $translator->translate($grandchildCategory->name, 'ta', 'en');
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

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
