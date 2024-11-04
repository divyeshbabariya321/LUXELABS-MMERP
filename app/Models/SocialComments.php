<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\Social\SocialPost;
use Illuminate\Database\Eloquent\Model;
use App\ChatMessage;

class SocialComments extends Model
{
    protected $fillable = [
        'comment_ref_id',
        'commented_by_id',
        'commented_by_user',
        'post_id',
        'config_id',
        'message',
        'parent_id',
        'user_id',
        'created_at',
        'can_comment',
    ];

    protected function casts(): array
    {
        return [
            'can_comment' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sub_comments(): HasMany
    {
        return $this->hasMany(SocialComments::class, 'parent_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'post_id');
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'message_type_id')->latest();
    }
}
