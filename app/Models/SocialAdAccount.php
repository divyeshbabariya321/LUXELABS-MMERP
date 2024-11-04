<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\StoreWebsite;
use App\Social\SocialConfig;
use Illuminate\Database\Eloquent\Model;

class SocialAdAccount extends Model
{
    protected $fillable = [
        'store_website_id',
        'name',
        'ad_account_id',
        'page_token',
        'status',
        'api_key',
        'api_secret',
    ];

    protected function casts(): array
    {
        return [
            'api_secret' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($account) {
            $account->api_secret = encrypt($account->api_secret);
        });
    }

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function social_configs(): HasMany
    {
        return $this->hasOne(SocialConfig::class, 'ad_account_id');
    }
}
