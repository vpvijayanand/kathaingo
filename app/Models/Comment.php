<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'parent_id', 'author_name', 'content', 'is_approved'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('is_approved', true)->orderBy('created_at', 'asc');
    }

    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }
}
