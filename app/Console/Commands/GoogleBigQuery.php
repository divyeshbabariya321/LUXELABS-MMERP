<?php

namespace App\Console\Commands;

use App\GoogleBigQueryData;
use App\StoreWebsite;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Console\Command;

class GoogleBigQuery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:googleBigData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google big query data';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $storewebsites = StoreWebsite::select('id', 'key_file_path', 'project_id')->get();
        foreach ($storewebsites as $storewebsite) {
            if ($storewebsite->key_file_path && $storewebsite->project_id) {
                $config = [
                    'keyFilePath' => public_path('bigData/'.$storewebsite->key_file_path),
                    'projectId' => $storewebsite->project_id,
                ];
                $bigQuery = new BigQueryClient($config);
                $query = 'SELECT * FROM `brandsandlabels.firebase_crashlytics.com_app_brandslabels_ANDROID_REALTIME` WHERE DATE(event_timestamp) = "'.date('Y-m-d').'"';
                $queryJobConfig = $bigQuery->query($query)
                    ->parameters([]);
                $queryResults = $bigQuery->runQuery($queryJobConfig);
                foreach ($queryResults as $row) {
                    $row = (object) $row;
                    $gBigQ = new GoogleBigQueryData;
                    $gBigQ->google_project_id = $storewebsite->project_id;
                    $gBigQ->platform = $row->platform;
                    $gBigQ->bundle_identifier = $row->bundle_identifier;
                    $gBigQ->event_id = $row->event_id;
                    $gBigQ->is_fatal = $row->is_fatal;
                    $gBigQ->issue_id = $row->issue_id;
                    $gBigQ->issue_title = $row->issue_title;
                    $gBigQ->issue_subtitle = $row->issue_subtitle;
                    $gBigQ->event_timestamp = $row->event_timestamp;
                    $gBigQ->received_timestamp = $row->received_timestamp;
                    $gBigQ->device = json_encode($row->device);
                    $gBigQ->memory = json_encode($row->memory);
                    $gBigQ->storage = json_encode($row->storage);
                    $gBigQ->operating_system = json_encode($row->operating_system);
                    $gBigQ->application = json_encode($row->application);
                    $gBigQ->user = $row->user;
                    $gBigQ->custom_keys = json_encode($row->custom_keys);
                    $gBigQ->installation_uuid = json_encode($row->installation_uuid);
                    $gBigQ->crashlytics_sdk_version = $row->crashlytics_sdk_version;
                    $gBigQ->app_orientation = $row->app_orientation;
                    $gBigQ->device_orientation = $row->device_orientation;
                    $gBigQ->process_state = $row->process_state;
                    $gBigQ->logs = json_encode($row->logs);
                    $gBigQ->breadcrumbs = json_encode($row->breadcrumbs);
                    $gBigQ->blame_frame = json_encode($row->blame_frame);
                    $gBigQ->exceptions = json_encode($row->exceptions);
                    $gBigQ->errors = json_encode($row->errors);
                    $gBigQ->threads = json_encode($row->threads);
                    $gBigQ->website_id = $storewebsite->id;
                    $gBigQ->save();
                }
            } // end if
        } //foreach
        echo 'Done';
    }
}
