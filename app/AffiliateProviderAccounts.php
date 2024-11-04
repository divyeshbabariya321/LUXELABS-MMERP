<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class AffiliateProviderAccounts extends Model
{
    protected $fillable = ['affiliates_provider_id', 'store_website_id', 'api_key', 'status'];

    public function provider(): HasOne
    {
        return $this->hasOne(AffiliateProviders::class, 'id', 'affiliates_provider_id');
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }
}
