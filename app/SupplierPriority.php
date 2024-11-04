<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SupplierPriority extends Model
{
    protected $table = 'supplier_priority';

    protected $fillable = [
        'priority',
    ];
}
