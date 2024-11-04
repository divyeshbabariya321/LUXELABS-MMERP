<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class StoreWebsiteSalesPrice extends Model
{
    protected $fillable = [
        'type', 'type_id', 'supplier_id', 'from_date', 'amount', 'to_date', 'created_by',
    ];
}
