<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunitySuggestedWord extends Model
{
    protected $fillable = ['word', 'language', 'user_id', 'status', 'nominations_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
