<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;

class MailinglistIinfluencersDetailLogs extends Model
{
    protected $guarded = [];

    protected $table = 'mailinglist_iInfluencers_detail_logs';

    protected $fillable = ['mailinglist_iInfluencers_log_id', 'service', 'maillist_id', 'email', 'name', 'url', 'request_data', 'response_data', 'message'];

    public static function log($result)
    {
        // Log result to database
        $log                = new MailinglistIinfluencersDetailLogs();
        $log->service       = $result['service'];
        $log->maillist_id   = $result['maillist_id'];
        $log->email         = $result['email'];
        $log->name          = $result['name'];
        $log->url           = $result['url'];
        $log->request_data  = $result['request_data'];
        $log->response_data = $result['response_data'];
        $log->save();

        // Return
        return $log;
    }
}
