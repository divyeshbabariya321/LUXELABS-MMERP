<?php

namespace App;
use App\WebsiteStoreViewValue;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CouponCodeRules extends Model
{

    public function store_labels(): HasMany
    {
        return $this->hasMany(WebsiteStoreViewValue::class, 'rule_id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
