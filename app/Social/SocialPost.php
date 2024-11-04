<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Plank\Mediable\Mediable;
use App\Models\SocialComments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="config_id",type="integer")
     * @SWG\Property(property="caption",type="string")
     * @SWG\Property(property="post_body",type="string")
     * @SWG\Property(property="post_by",type="string")
     * @SWG\Property(property="posted_on",type="datetime")
     * @SWG\Property(property="status",type="string")
     */
    use Mediable;

    protected $fillable = [
        'config_id',
        'caption',
        'post_body',
        'post_by',
        'posted_on',
        'ref_post_id',
        'image_path',
        'status',
        'hashtag',
        'translation_approved_by',
        'post_medium',
        'media_file',
        'permalink',
        'custom_data',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialConfig::class, 'config_id');
    }

    protected function casts(): array
    {
        return [
            'posted_on'   => 'datetime',
            'media_file'  => 'json',
            'custom_data' => 'json',
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SocialComments::class, 'post_id');
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(SocialConfig::class, 'config_id');
    }
}
