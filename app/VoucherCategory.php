<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Nestable\NestableTrait;
use Illuminate\Database\Eloquent\Model;

class VoucherCategory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="parent_id",type="integer")
     */
    use NestableTrait;

    protected $parent = 'parent_id';

    protected $fillable = [
        'title', 'parent_id',
    ];
}
