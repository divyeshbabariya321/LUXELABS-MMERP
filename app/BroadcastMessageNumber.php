<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use seo2websites\ErpCustomer\ErpCustomer;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BroadcastMessageNumber extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="broadcast_message_id",type="integer")
     * @SWG\Property(property="type_id",type="integer")
     * @SWG\Property(property="type",type="string")
     */
    protected $fillable = [
        'broadcast_message_id', 'type_id', 'type',
    ];

    protected $appends = ['typeName'];

    public function getTypeNameAttribute()
    {
        if ($this->type == 'App\Http\Controllers\App\Vendor') {
            $typeName = @$this->vendor->name;
        } elseif ($this->type == 'App\Http\Controllers\App\Supplier') {
            $typeName = @$this->supplier->supplier;
        } else {
            $typeName = @$this->customer->name;
        }

        return $typeName;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ErpCustomer::class, 'type_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'type_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'type_id', 'id');
    }
}
