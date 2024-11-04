<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApiResponseMessagesTranslation extends Model
{
    protected $fillable = [
        'id', 'store_website_id', 'key', 'lang_code', 'lang_name', 'value',
    ];

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function websiteStoreView(): HasOne
    {
        return $this->hasOne(WebsiteStoreView::class, 'code', 'lang_code');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'id');
    }
}
