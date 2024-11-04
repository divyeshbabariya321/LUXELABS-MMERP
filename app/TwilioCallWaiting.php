<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\StoreWebsite;

class TwilioCallWaiting extends Model
{

    protected $fillable = ['call_sid', 'account_sid', 'from', 'to', 'store_website_id', 'status'];

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}
