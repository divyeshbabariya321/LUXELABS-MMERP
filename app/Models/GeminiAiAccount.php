<?php

namespace App\Models;
use App\StoreWebsite;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeminiAiAccount extends Model
{
    protected $fillable = [
        'store_website_id', 'api_key', 'api_url', 'prompt','fallback_message'
    ];

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }
}
