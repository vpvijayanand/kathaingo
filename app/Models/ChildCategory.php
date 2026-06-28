<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildCategory extends Model
{
    protected $fillable = ['subcategory_id', 'name', 'slug', 'order', 'name_en'];

    protected static function booted()
    {
        static::saving(function ($childCategory) {
            $translator = app(\App\Services\TranslationService::class);

            if ($childCategory->isDirty('name') || empty($childCategory->name_en)) {
                $childCategory->name_en = $translator->translate($childCategory->name, 'ta', 'en');
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

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function grandchildCategories()
    {
        return $this->hasMany(GrandchildCategory::class);
    }
}
