<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'gender',
        'location',
        'dob',
        'avatar',
        'is_admin',
        'is_approved',
        'role',
        'google_id',
        'facebook_id',
        'linkedin_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->role === 'admin' || (bool)$user->is_admin) {
                $user->role = 'admin';
                $user->is_admin = true;
                $user->is_approved = true; // Admins are always approved
            } else {
                $user->is_admin = false;
                if (empty($user->role)) {
                    $user->role = 'visitor';
                }
            }
        });
    }

    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || (bool)$this->is_admin;
    }

    public function isSeoManager(): bool
    {
        return $this->role === 'seo_manager';
    }

    public function authorProfile()
    {
        return $this->hasOne(Subcategory::class, 'user_id');
    }

    public function loginActivities()
    {
        return $this->hasMany(LoginActivity::class)->orderBy('logged_at', 'desc');
    }
}
