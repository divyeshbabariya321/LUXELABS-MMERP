<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class ProductTemplateLog extends Model
{
    protected $fillable = [
        'url',
        'data',
        'response',
    ];
}
