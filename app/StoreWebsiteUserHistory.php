<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;
use App\StoreWebsiteUsers;
use App\User;

class StoreWebsiteUserHistory extends Model
{
    protected $table = 'store_website_user_history';

    protected $fillable = [
        'store_website_id',
        'store_website_user_id',
        'model',
        'attribute',
        'old_value',
        'new_value',
        'user_id',
    ];

    public function storewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id', 'id');
    }

    public function websiteuser(): BelongsTo
    {
        return $this->belongsTo(StoreWebsiteUsers::class, 'store_website_user_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
