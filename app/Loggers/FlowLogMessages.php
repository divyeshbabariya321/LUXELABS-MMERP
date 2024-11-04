<?php

namespace App\Loggers;
use App\Loggers\FlowLog;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class FlowLogMessages extends Model
{
    protected $guarded = [];

    public static function log($result)
    {
        // Log result to database
        $flowLogMessages                   = new FlowLogMessages();
        $flowLogMessages->messages         = $result['messages'];
        $flowLogMessages->flow_action      = $result['flow_action'];
        $flowLogMessages->modalType        = $result['modalType'];
        $flowLogMessages->leads            = $result['leads'];
        $flowLogMessages->store_website_id = $result['store_website_id'];
        $flowLogMessages->flow_log_id      = $result['flow_log_id'];
        $flowLogMessages->scraper_id       = $result['scraper_id'];
        $flowLogMessages->save();

        // Return
    }

    public function flowlog(): BelongsTo
    {
        return $this->belongsTo(FlowLog::class);
    }
}
