<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPostMedia extends Model
{
    protected $fillable = [
        'job_post_id',
        'path',
        'alt',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }
}
