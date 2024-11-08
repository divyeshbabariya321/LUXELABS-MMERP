<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Helpers\LogHelper;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;
use App\LogRequest;
use App\MessageQueue;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use App\Marketing\WhatsappConfig;

class RunMessageQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:message-queues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    // custom defined vars
    const WAITING_MESSAGE_LIMIT = 300;

    // waiting messages group
    public $waitingMessages = [];

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $time = Carbon::now();
            $morning = Carbon::create($time->year, $time->month, $time->day, 8, 0, 0);
            $evening = Carbon::create($time->year, $time->month, $time->day, 17, 00, 0);

            if ($time->between($morning, $evening, true)) {
                // Get groups
                $groups = MessageQueue::groupBy('group_id')->select('group_id')->get(['group_id']);

                $allWhatsappNo = WhatsappConfig::getWhatsappConfigs();
                $this->waitingMessages = [];
                if (! empty($allWhatsappNo)) {
                    foreach ($allWhatsappNo as $no => $dataInstance) {
                        $waitingMessage = $this->waitingLimit($no);
                        $this->waitingMessages[$no] = $waitingMessage;
                    }
                }

                foreach ($groups as $group) {
                    // Get messages
                    $message_queues = MessageQueue::where('group_id', $group->group_id)
                        ->where('sending_time', '<=', Carbon::now())
                        ->where('sent', 0)
                        ->where('status', '!=', 1)
                        ->orderBy('sending_time')
                        ->limit(12);

                    // Do we have results?
                    if (count($message_queues->get()) > 0) {
                        foreach ($message_queues->get() as $message) {
                            // check message can able to send
                            $number = ! empty($message->whatsapp_number) ? (string) $message->whatsapp_number : 0;

                            if ($message->type == 'message_all') {
                                $customer = Customer::find($message->customer_id);
                                $number = ! empty($customer->whatsapp_number) ? (string) $customer->whatsapp_number : 0;

                                // No number? Set to default
                                if ($number == 0 || ! array_key_exists($number, $allWhatsappNo)) {
                                    foreach ($allWhatsappNo as $no => $dataInstance) {
                                        if ($dataInstance['customer_number'] == true) {
                                            $customer->whatsapp_number = $no;
                                            $customer->save();
                                            $number = $no;
                                            break;
                                        }
                                    }
                                }

                                if (! $this->isWaitingFull($number)) {
                                    if ($customer && $customer->do_not_disturb == 0 && substr($number, 0, 3) == '971') {
                                        SendMessageToAll::dispatchSync($message->user_id, $customer, json_decode($message->data, true), $message->id, $group->group_id);

                                        dump('sent to all');
                                    } else {
                                        $message->delete();

                                        dump('deleting queue');
                                    }
                                } else {
                                    if (substr($number, 0, 3) == '971') {
                                        dump('sorry , message is full right now for this number : '.$number);
                                    } else {
                                        $message->delete();
                                        dump('deleting queue');
                                    }
                                }
                            } else {
                                if (! $this->isWaitingFull($number)) {
                                    if (substr($message->whatsapp_number, 0, 3) == '971') {
                                        SendMessageToSelected::dispatchSync($message->phone, json_decode($message->data, true), $message->id, $message->whatsapp_number, $message->group_id);
                                    } else {
                                        $message->delete();
                                    }

                                    dump('sent to selected');
                                } else {
                                    dump('sorry , message is full right now for this number : '.$number);
                                }
                            }

                            // start to add more if there is existing already
                            if (isset($this->waitingMessages[$number])) {
                                $this->waitingMessages[$number] = $this->waitingMessages[$number] + 1;
                            } else {
                                $this->waitingMessages[$number] = 1;
                            }
                        }
                    }
                }
            } else {
                dump('Not the right time for sending');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * Check waiting is full for given number
     *
     * @param  mixed  $number
     */
    private function isWaitingFull($number)
    {
        $number = ! empty($number) ? $number : 0;

        if (isset($this->waitingMessages[$number]) && $this->waitingMessages[$number] > self::WAITING_MESSAGE_LIMIT) {
            return true;
        }

        return false;
    }

    /**
     * Get instance from whatsapp number
     *
     * @param  null|mixed  $number
     */
    private function getInstance($number = null)
    {
        $number = ! empty($number) ? $number : 0;
        $config = WhatsappConfig::getWhatsappConfigs();

        return isset($config[$number])
            ? $config[$number]
            : $config[0];
    }

    /**
     * send request for find waiting message number
     *
     * @param  null|mixed  $number
     */
    private function waitingLimit($number = null)
    {
        $instance = $this->getInstance($number);
        $instanceId = isset($instance['instance_id']) ? $instance['instance_id'] : 0;
        $token = isset($instance['token']) ? $instance['token'] : 0;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $waiting = 0;

        if (! empty($instanceId) && ! empty($token)) {
            // executing curl
            $curl = curl_init();
            $url = "https://api.chat-api.com/instance$instanceId/showMessagesQueue?token=$token";

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, RunMessageQueue::class, 'handle');

            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                // throw some error if you want
            } else {
                $result = json_decode($response, true);
                if (isset($result['totalMessages']) && is_numeric($result['totalMessages'])) {
                    $waiting = $result['totalMessages'];
                }
            }
        }

        return $waiting;
    }
}
