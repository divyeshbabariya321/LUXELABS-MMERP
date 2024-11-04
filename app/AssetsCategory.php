<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class AssetsCategory extends Model
{
    protected $table = 'assets_category';

    /**
     * @var string
     *
     * @SWG\Property(property="cat_name",type="string")
     */
    protected $fillable = [
        'cat_name',
    ];
}
