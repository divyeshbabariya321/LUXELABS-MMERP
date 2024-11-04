<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Sentry\SentryAccount;
use App\Sentry\SentryErrorLog;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class LoadSentryLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sentry:load_error_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load error logs for Sentry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $accounts = SentryAccount::get();
            if ($accounts) {
                SentryErrorLog::truncate();
            }
            ini_set('max_execution_time', 0);
            foreach ($accounts as $account) {
                $url = 'https://sentry.io/api/0/projects/'.$account->sentry_organization.'/'.$account->sentry_project.'/issues/';
                $httpClient = new Client;

                $response = $httpClient->get(
                    $url,
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer '.$account->sentry_token,
                        ],
                    ]
                );
                $responseJson = json_decode($response->getBody()->getContents());

                foreach ($responseJson as $error_log) {
                    $eventurl = 'https://sentry.io/api/0/projects/'.$account->sentry_organization.'/'.$account->sentry_project.'/events/';
                    $httpClient = new Client;
                    $response1 = $httpClient->get(
                        $eventurl,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer '.$account->sentry_token,
                            ],
                        ]
                    );
                    $eventResponseJson = json_decode($response1->getBody()->getContents());

                    if (isset($eventResponseJson[0])) {
                        $eventData = $eventResponseJson[0]->tags;

                        $device = isset($eventData[0]->value) ? $eventData[0]->value : '';
                        $os = isset($eventData[9]->value) ? $eventData[9]->value : '';
                        $os_name = isset($eventData[10]->value) ? $eventData[10]->value : '';

                        if (isset($eventData[13]->value)) {
                            $release = explode('@', $eventData[13]->value);
                            $release = isset($release[1]) ? $release[1] : '';
                        } else {
                            $release = '';
                        }
                    } else {
                        $eventData = '';
                        $device = '';
                        $os = '';
                        $os_name = '';
                        $release = '';
                    }

                    SentryErrorLog::create([
                        'error_id' => $error_log->id,
                        'error_title' => $error_log->title,
                        'issue_type' => $error_log->issueType,
                        'issue_category' => $error_log->issueCategory,
                        'is_unhandled' => ($error_log->isUnhandled == 'false') ? 0 : 1,
                        'first_seen' => date('d-m-y H:i:s', strtotime($error_log->firstSeen)),
                        'last_seen' => date('d-m-y H:i:s', strtotime($error_log->lastSeen)),
                        'project_id' => $account->id,
                        'total_events' => $error_log->count,
                        'total_user' => $error_log->userCount,
                        'device_name' => $device,
                        'os' => $os,
                        'os_name' => $os_name,
                        'release_version' => $release,
                    ]);
                }
            }
            $report->update(['end_time' => Carbon::now()]);

            return true;
        } catch (Exception $e) {
            dd($e);
            CronJob::insertLastError($this->signature, $e->getMessage());

            return false;
        }
    }
}
