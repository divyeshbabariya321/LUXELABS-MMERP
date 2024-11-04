<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\SocialContact;
use App\ChatMessage;

class SocialContactThread extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const SEND = 1;

    const RECEIVE = 2;

    protected $fillable = ['social_contact_id', 'sender_id', 'recipient_id',  'message_id', 'text', 'type', 'sending_at'];

    public function socialContact(): BelongsTo
    {
        return $this->belongsTo(SocialContact::class);
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'message_type_id')->latest();
    }
}
