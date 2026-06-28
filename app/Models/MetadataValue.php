<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetadataValue extends Model
{
    protected $fillable = ['metadata_type_id', 'parent_id', 'name', 'slug', 'name_en'];

    protected static function booted()
    {
        static::saving(function ($value) {
            $translator = app(\App\Services\TranslationService::class);

            if (($value->isDirty('name') && !$value->isDirty('name_en')) || empty($value->name_en)) {
                $value->name_en = $translator->translate($value->name, 'ta', 'en');
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

    public function type()
    {
        return $this->belongsTo(MetadataType::class, 'metadata_type_id');
    }

    public function parent()
    {
        return $this->belongsTo(MetadataValue::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MetadataValue::class, 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_metadata', 'metadata_value_id', 'post_id');
    }
}
