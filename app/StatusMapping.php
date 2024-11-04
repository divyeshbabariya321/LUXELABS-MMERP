<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\OrderStatus;
use App\PurchaseStatus;
use App\StatusMappingHistory;

class StatusMapping extends Model
{
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function purchaseStatus(): BelongsTo
    {
        return $this->belongsTo(PurchaseStatus::class);
    }

    public function statusMappingHistories(): HasMany
    {
        return $this->hasMany(StatusMappingHistory::class)->latest();
    }
}
