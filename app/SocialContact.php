<?php

namespace App;

use App\Models\SocialMessages;
use App\Social\SocialConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SocialContact extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const TEXT_INSTA = 'instagram';

    const TEXT_FB = 'page';

    protected $fillable = ['account_id', 'name', 'social_config_id', 'platform', 'conversation_id', 'can_reply'];

    protected function casts(): array
    {
        return [
            'can_reply' => 'boolean',
        ];
    }

    public function socialConfig(): BelongsTo
    {
        return $this->belongsTo(SocialConfig::class);
    }

    public function messages(): HasMany
    {
        return $this
            ->hasMany(SocialMessages::class, 'social_contact_id')
            ->orderBy('created_time');
    }

    public function getLatestSocialContactThread(): HasOne
    {
        return $this
            ->hasOne(SocialContactThread::class, 'social_contact_id')
            ->latest();
    }

    public function thread_messages(): HasMany
    {
        return $this
            ->hasMany(SocialContactThread::class, 'social_contact_id')
            ->orderBy('created_at');
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        return $this->hasMany(\App\ChatMessage::class, 'message_type_id')->latest();
    }
}
