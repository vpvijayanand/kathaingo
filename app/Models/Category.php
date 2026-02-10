<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class)->orderBy('order');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
