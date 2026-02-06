<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'publish_date',
    ];

    public function translations()
    {
        return $this->hasMany(JobPostTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'job_post_tag');
    }

    public function media()
    {
        return $this->hasMany(JobPostMedia::class);
    }
}
