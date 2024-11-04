<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class GoogleDeveloper extends Model
{
    protected $table = 'google_developer_reporting';

    protected $fillable = [

        'name',
        'report',
        'aggregation_period',
        'latestEndTime',
        'timezone',
    ];

    public $timestamps = false;
}
