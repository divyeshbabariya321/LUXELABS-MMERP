<?php

namespace App;
use App\User;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HashTag extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="hashtag",type="string")
     */
    protected $fillable = ['hashtag', 'platforms_id', 'instagram_account_id'];

    public function posts(): HasMany
    {
        return $this->hasMany(HashtagPosts::class, 'hashtag_id', 'id');
    }

    public function instagramPost(): HasMany
    {
        return $this->hasMany(InstagramPosts::class, 'hashtag_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
