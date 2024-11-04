<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = ['customer_id', 'status'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}
