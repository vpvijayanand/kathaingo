<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetadataType extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'name_en', 'is_hierarchical'];

    protected $casts = [
        'is_hierarchical' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($type) {
            $translator = app(\App\Services\TranslationService::class);

            if ($type->isDirty('name') || empty($type->name_en)) {
                $type->name_en = $translator->translate($type->name, 'ta', 'en');
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function values()
    {
        return $this->hasMany(MetadataValue::class);
    }
}
