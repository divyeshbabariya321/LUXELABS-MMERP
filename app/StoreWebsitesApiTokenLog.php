<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
use App\StoreWebsiteUsers;
use App\User;

class StoreWebsitesApiTokenLog extends Model
{
    protected $fillable = [
        'user_id',
        'store_website_id',
        'store_website_users_id',
        'response',
        'status_code',
        'status',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function StoreWebsiteUsers(): HasOne
    {
        return $this->hasOne(StoreWebsiteUsers::class, 'id', 'store_website_users_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
