<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class InstagramPosts extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="location",type="string")
     */
    use Mediable;

    protected $fillable = ['location'];

    public function send_comment(): HasMany
    {
        return $this->hasMany(CommentsStats::class, 'code', 'code');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(InstagramPostsComments::class, 'instagram_post_id', 'id');
    }

    public function hashTags(): BelongsTo
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }

    public function userDetail(): HasOne
    {
        return $this->hasOne(InstagramUsersList::class, 'user_id', 'user_id');
    }

    public function commentQueue(): HasMany
    {
        return $this->hasMany(InstagramCommentQueue::class, 'post_id', 'post_id');
    }
}
