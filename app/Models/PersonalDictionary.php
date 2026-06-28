<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalDictionary extends Model
{
    protected $fillable = ['user_id', 'word', 'language'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
