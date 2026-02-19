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
        'firstname',
        'email',
        'password',
        'language',
        'profile_picture',
        'tags',
        'is_admin',
        'deleted',
        'deleted_at',
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
            'tags' => 'array',
            'is_admin' => 'boolean',
            'deleted' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->is_admin === true;
    }

    public function favoriteParks()
    {
        return $this->belongsToMany(Park::class)->withTimestamps();
    }

    public function favoriteAttractions()
    {
        return $this->hasMany(UserFavoriteAttraction::class);
    }
}
