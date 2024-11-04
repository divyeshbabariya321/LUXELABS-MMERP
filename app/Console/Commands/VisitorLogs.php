<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\VisitorLog;
use Exception;
use Illuminate\Console\Command;

class VisitorLogs extends Command
{
    const LIVE_CHAT_CREDNTIAL = 'NmY0M2ZkZDUtOTkwMC00OWY4LWI4M2ItZThkYzg2ZmU3ODcyOmRhbDp0UkFQdWZUclFlLVRkQUI4Y2pFajNn';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the visitor Log From The Live Chat and save it , this api hit should be continous like in 5 mins to get accurate data from LiveChat';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron started to run']);

            $curl = curl_init();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting the visitors detail this api: https://api.livechatinc.com/v2/visitors']);
            $url = 'https://api.livechatinc.com/v2/visitors';
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic '.self::LIVE_CHAT_CREDNTIAL,
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, VisitorLogs::class, 'handle');

            if ($err) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'error found from curl request']);

                echo 'cURL Error #:'.$err;
            } else {
                $logs = json_decode($response);
                if (count($logs) != 0) {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'visitor logs records found']);

                    foreach ($logs as $log) {
                        $logExist = VisitorLog::where('ip', $log->ip)->whereDate('last_visit', '<=', $log->last_visit)->first();
                        if ($logExist == null) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'No found any visior log using ip: '.$log->ip]);

                            $logSave = new VisitorLog;
                            $logSave->ip = $log->ip;
                            $logSave->browser = $log->browser;
                            $logSave->location = $log->city.' '.$log->region.' '.$log->country.' '.$log->country_code;
                            foreach ($log->visit_path as $path) {
                                $pathArray[] = $path->page;
                            }
                            $logSave->page = json_encode($pathArray);
                            $logSave->visits = $log->visits;
                            $logSave->last_visit = $log->last_visit;
                            $logSave->page_current = $log->page_current;
                            $logSave->chats = $log->chats;
                            $logSave->customer_name = $log->name;
                            $logSave->save();

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved visitor log by ID: '.$logSave->id]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
