<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPostTranslation extends Model
{
    protected $fillable = [
        'job_post_id',
        'locale',
        'title',
        'description',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }
}
