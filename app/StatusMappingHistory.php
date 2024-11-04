<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\StatusMapping;
class StatusMappingHistory extends Model
{
    const STATUS_TYPE_PURCHASE = 'Purchase';

    const STATUS_TYPE_SHIPPING = 'Shipping';

    const STATUS_TYPE_RETURN_EXCHANGE = 'Return Exchange';

    public function statusMapping(): BelongsTo
    {
        return $this->belongsTo(related: StatusMapping::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
