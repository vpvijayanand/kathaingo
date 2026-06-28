<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    public $timestamps = false;

    protected $table = 'login_activities';

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'is_successful',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'is_successful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
