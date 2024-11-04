<?php

namespace App;
use App\Vendor;
use App\Customer;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use seo2websites\ErpCustomer\ErpCustomer;

class KeywordAutoGenratedMessageLog extends Model
{
    protected $fillable = ['model', 'model_id', 'keyword', 'keyword_match', 'message_sent_id', 'comment'];

    protected $appends = ['typeName'];

    public function getTypeNameAttribute()
    {
        if ($this->model == Customer::class) {
            $typeName = @$this->customer->name;
        } elseif ($this->model == Vendor::class) {
            $typeName = @$this->vendor->name;
        } else {
            $typeName = @$this->supplier->supplier;
        }

        return $typeName;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ErpCustomer::class, 'model_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'model_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'model_id', 'id');
    }
}
