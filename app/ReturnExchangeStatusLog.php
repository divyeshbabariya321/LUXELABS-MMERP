<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ReturnExchangeStatusLog extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="status_name",type="string")
     * @SWG\Property(property="status",type="intiger")
     * @SWG\Property(property="updated_by",type="intiger")
     */
    protected $fillable = [
        'return_exchanges_id',
        'status_name',
        'status',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
