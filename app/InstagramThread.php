<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class InstagramThread extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="scrap_influencer_id",type="integer")
     */
    protected $fillable = ['scrap_influencer_id'];

    public function conversation(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'unique_id', 'thread_id');
    }

    public function influencerConversation(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'instagram_user_id', 'instagram_user_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(ColdLeads::class, 'cold_lead_id', 'id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id')->whereNotNull('proxy');
    }

    public function instagramUser(): HasOne
    {
        return $this->hasOne(InstagramUsersList::class, 'id', 'instagram_user_id');
    }

    public function erpUser(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'unique_id', 'thread_id')->orderByDesc('id')->whereNotNull('message');
    }
}
