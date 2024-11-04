<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class Redisjob extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="type",type="string")
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'status',
    ];

    protected $table = 'redis_jobs';
}
