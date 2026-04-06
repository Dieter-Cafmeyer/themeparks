<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttractionMetadata extends Model
{
    protected $table = 'attraction_metadata';

    protected $fillable = [
        'park_id',
        'themeparks_api_id',
        'arendz_id',
        'name',
        'arendz_image_url',
        'arendz_description_nl',
        'arendz_synced_at',
    ];

    protected $casts = [
        'arendz_synced_at' => 'datetime',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }
}
