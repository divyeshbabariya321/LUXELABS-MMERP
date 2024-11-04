<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BrandLogo extends Model
{
    //
    protected $fillable = ['logo_image_name', 'user_id'];
}
