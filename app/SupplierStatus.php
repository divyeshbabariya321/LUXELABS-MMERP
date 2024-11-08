<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class SupplierStatus extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="supplier_status",type="string")
     * @SWG\Property(property="timestamps",type="datetime")
     * @SWG\Property(property="name",type="string")
     */
    protected $table = 'supplier_status';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
