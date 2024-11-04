<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ScrapPythonLog extends Model
{
    protected $fillable = [
        'website', 'date', 'device', 'log_text',
    ];

    public static function log($result)
    {
        // Log result to database
        $log           = new ScrapPythonLog();
        $log->website  = $result['website'];
        $log->date     = $result['date'];
        $log->device   = $result['device'];
        $log->log_text = $result['log_text'];

        $log->save();

        // Return
        return $log;
    }

    public function messages(): HasMany
    {
        return $this->hasMany('App\Loggers\logMessages');
    }
}
