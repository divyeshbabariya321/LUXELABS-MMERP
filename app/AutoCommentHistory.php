<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AutoCommentHistory extends Model
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function hashtag(): BelongsTo
    {
        return $this->belongsTo(AutoReplyHashtags::class, 'auto_reply_hashtag_id', 'id');
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_auto_comment_histories', 'auto_comment_history_id', 'user_id', 'id');
    }
}
