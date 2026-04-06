<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Park extends Model
{
    protected $fillable = [
        'api_id',
        'arendz_id',
        'name',
        'slug',
        'destination_id',
        'latitude',
        'longitude',
        'arendz_image_url',
        'arendz_description_nl',
        'arendz_synced_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'arendz_synced_at' => 'datetime',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function attractionMetadata(): HasMany
    {
        return $this->hasMany(AttractionMetadata::class);
    }

    public function usersWhoFavorited()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}