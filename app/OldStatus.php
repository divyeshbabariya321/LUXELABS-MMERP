<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class OldStatus extends Model
{
    protected $table = 'old_status';

    /**
     * @var string
     *
     * @SWG\Property(property="category",type="string")
     */
    protected $fillable = ['status'];
}
