<?php

namespace App\Loggers;
use App\Loggers\FlowLogMessages;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class FlowLog extends Model
{
    public static function log($result)
    {
        // Log result to database
        $flowlog           = new FlowLog();
        $flowlog->flow_id  = $result['flow_id'];
        $flowlog->messages = $result['messages'];
        $flowlog->save();

        // Return
        return $flowlog;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(FlowLogMessages::class);
    }
}
