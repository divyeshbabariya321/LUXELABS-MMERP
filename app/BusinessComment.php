<?php

namespace App;

use App\Social\SocialConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessComment extends Model
{
    protected $primaryKey = 'comment_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $appends = ['user'];

    protected $fillable = ['comment_id', 'post_id', 'is_admin_comment', 'social_contact_id', 'message', 'photo', 'is_parent', 'parent_comment_id', 'verb', 'time'];

    public function replyComment(): HasMany
    {
        return $this->hasMany(BusinessComment::class, 'parent_comment_id', 'comment_id')->where('is_parent', 1)->latest('time');
    }

    public function getUserAttribute()
    {
        if ($this->is_admin_comment) {
            return SocialConfig::find($this->social_contact_id);
        } else {
            return SocialContact::find($this->social_contact_id);
        }
    }

    public function bussiness_post(): BelongsTo
    {
        return $this->belongsTo(BusinessPost::class, 'post_id')->select('post_id', 'social_config_id');
    }
}
