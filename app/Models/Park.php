<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Park extends Model
{
    protected $fillable = [
        'api_id',
        'name',
        'destination_id',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function usersWhoFavorited()
    {
    return $this->belongsToMany(User::class)->withTimestamps();
    }
}