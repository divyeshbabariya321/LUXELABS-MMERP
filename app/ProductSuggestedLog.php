<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ProductSuggestedLog extends Model
{

    /**
     * @var string
     *
     * @SWG\Property(property="parent_id",type="integer")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="log",type="string")
     */
    protected $fillable = ['id', 'parent_id', 'type', 'log'];
}
