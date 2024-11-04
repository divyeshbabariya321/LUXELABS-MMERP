<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class GoogleDeveloperLogs extends Model
{
    protected $table = 'google_dev_report_logs';

    protected $fillable = [

        'log_name',
        'api',
        'result',

    ];
}
