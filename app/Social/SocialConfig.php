<?php

namespace App\Social;

use App\StoreWebsite;
use App\SocialContact;
use App\Models\SocialAdAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\BusinessPost;

class SocialConfig extends Model
{
    protected $fillable = [
        'store_website_id',
        'page_language',
        'platform',
        'name',
        'email',
        'password',
        'api_key',
        'api_secret',
        'token',
        'status',
        'page_id',
        'page_token',
        'account_id',
        'webhook_token',
        'ads_manager',
        'ad_account_id',
        'user_name',
        'phone_number',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function businessPost(): HasMany
    {
        return $this->hasMany(BusinessPost::class);
    }

    public function bussiness_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }

    public function setPasswordAttribute($password): void
    {
        if (trim($password) == '') {
            return;
        }
        $this->attributes['password'] = encrypt($password);
    }
    
    public function setApiSecretAttribute($api_secret): void
    {
        if (trim($api_secret) == '') {
            return;
        }
        $this->attributes['api_secret'] = encrypt($api_secret);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class, 'config_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SocialContact::class, 'social_config_id');
    }

    public function ad_account(): BelongsTo
    {
        return $this->belongsTo(SocialAdAccount::class, 'ad_account_id');
    }
}
