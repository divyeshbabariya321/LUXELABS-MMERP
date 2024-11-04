<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\OrderStatus;

class OrderReport extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="status",type="string")
     */
    protected $appends = ['status'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function statusName(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id')->first()->status;
    }

    public function getStatusAttribute()
    {
        return $this->statusName();
    }
}
