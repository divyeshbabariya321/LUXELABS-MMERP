<?php

namespace App;
use App\User;
use App\StoreWebsite;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MagentoCommand extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_ids');
    }
}
