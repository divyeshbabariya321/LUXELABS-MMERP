<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class BackLinking extends Model
{
    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    /**
     * @var string
     *
     * @SWG\Property(property="title",type="string")
     * @SWG\Property(property="description",type="text")
     * @SWG\Property(property="url",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    protected $fillable = [
        'title', 'description',
        'url',
    ];

    /**
     * Protected Date
     *
     * @var array
     */
}
