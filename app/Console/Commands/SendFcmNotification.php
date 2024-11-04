<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\PushFcmNotification;
use App\PushFcmNotificationHistory;
use App\Services\CommonGoogleTranslateService;
use App\Translations;
use Exception;
use FCM;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class SendFcmNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command For push notification';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $fromdate = date('Y-m-d H:i:s');
            $newtimestamp = strtotime($fromdate.' + 4 minute');
            $todate = date('Y-m-d H:i:s', $newtimestamp);
            echo $fromdate.' # '.$todate;
            echo PHP_EOL;
            Log::info('fcm:send was started to run');
            $Notifications = PushFcmNotification::select('sw.push_web_key', 'sw.push_web_id', 'ft.token', 'ft.lang', 'push_fcm_notifications.*')
                ->leftJoin('fcm_tokens as ft', 'ft.store_website_id', '=', 'push_fcm_notifications.store_website_id')
                ->leftJoin('store_websites as sw', 'sw.id', '=', 'push_fcm_notifications.store_website_id')
                ->where('ft.token', '!=', '')
                ->where('sw.push_web_key', '!=', '')
                ->where('sw.push_web_id', '!=', '')
                ->whereBetween('push_fcm_notifications.sent_at', [$fromdate, $todate])
                ->get();
            Log::info('fcm:send query was finished');

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'PushFcmNotification model query was finished']);

            if (! $Notifications->isEmpty()) {
                Log::info('fcm:send record was found');

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'notifications records was found']);

                foreach ($Notifications as $Notification) {
                    $errorMessage = '';
                    $token = '';
                    try {
                        config(['fcm.http.sender_id' => $Notification['push_web_id']]);
                        config(['fcm.http.server_key' => $Notification['push_web_key']]);
                        Log::info('fcm:send sender_id was '.$Notification['push_web_id'].' found with key '.$Notification['push_web_key']);

                        $title = $Notification->title;
                        $googleTranslate = new CommonGoogleTranslateService;
                        $translationString = $googleTranslate->translate($Notification->lang, $Notification->title);

                        if ($translationString != '') {
                            Translations::addTranslation($Notification->title, $translationString, 'en', $Notification->lang);
                            $title = htmlspecialchars_decode($translationString, ENT_QUOTES);
                        }

                        $body = $Notification->body;
                        $googleTranslate = new CommonGoogleTranslateService;
                        $translationString = $googleTranslate->translate($Notification->lang, $Notification->body);

                        if ($translationString != '') {
                            Translations::addTranslation($Notification->body, $translationString, 'en', $Notification->lang);
                            $body = htmlspecialchars_decode($translationString, ENT_QUOTES);
                        }

                        $optionBuilder = new OptionsBuilder;
                        $optionBuilder->setTimeToLive(60 * 20);

                        $notificationBuilder = new PayloadNotificationBuilder($title);
                        $notificationBuilder->setBody($body)
                            ->setSound('default');

                        $dataBuilder = new PayloadDataBuilder;
                        $dataBuilder->addData([
                            'icon' => $Notification->icon,
                            'expired_day' => $Notification->expired_day,
                        ]);

                        $option = $optionBuilder->build();
                        $notification = $notificationBuilder->build();
                        $data = $dataBuilder->build();

                        $token = $Notification->token;

                        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                        $success = false;
                        if ($downstreamResponse->numberSuccess()) {
                            $this->info('Message Sent Succesfully');
                            Log::info('fcm:send Message Sent Succesfully');
                            $Notification->status = 'Success';
                            $success = true;

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'message sent successfully']);
                        } elseif ($downstreamResponse->numberFailure()) {
                            $Notification->status = 'Failed';
                            $this->info(json_encode($downstreamResponse->tokensWithError()));
                            $errorMessage = json_encode($downstreamResponse->tokensWithError());
                            Log::info('fcm:send Message Error message =>'.$errorMessage);

                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'message sent error'.$errorMessage]);
                        }
                    } catch (Exception $e) {
                        $Notification->status = 'Failed';
                        $success = false;
                        $errorMessage = $e->getMessage();
                        Log::info('fcm:send Exception Error message =>'.$errorMessage);

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'message sent error'.$errorMessage]);
                    }

                    $Notification->sent_on = date('Y-m-d H:i');
                    $Notification->save();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'notification detail updated by ID'.$Notification->id]);

                    PushFcmNotificationHistory::create([
                        'token' => $token,
                        'notification_id' => $Notification->id,
                        'success' => $success,
                        'error_message' => $errorMessage,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved notification history']);
                }
            } else {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'No any pending notification found.']);

                Log::info('fcm:send Exception No notification available for sending at the moment');
                $this->info('No notification available for sending at the moment');
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
