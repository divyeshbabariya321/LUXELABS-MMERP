<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\OrderStatus;
use App\StoreWebsite;
use App\StoreMasterStatus;

class Store_order_status extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="order_status_id",type="integer")
     * @SWG\Property(property="store_website_id",type="integer")
     * @SWG\Property(property="store_master_status_id",type="integer")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = ['order_status_id', 'store_website_id', 'status', 'store_master_status_id'];

    public function order_status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }

    public function store_master_status(): BelongsTo
    {
        return $this->belongsTo(StoreMasterStatus::class);
    }
}
