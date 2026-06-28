<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostFeedback extends Model
{
    public $timestamps = false;

    protected $table = 'post_feedback';

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
