<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class AffiliateProviders extends Model
{
    protected $fillable = ['provider_name', 'status'];

    public function sites(): HasMany
    {
        return $this->hasMany(AffiliateProviderAccounts::class, 'affiliates_provider_id', 'id');
    }
}
