<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class StoreSocialContentStatus extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['name'];
}
