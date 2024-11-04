<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreWebsiteProductPriceHistory extends Model
{
    public $timestamps = true;

    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
}
