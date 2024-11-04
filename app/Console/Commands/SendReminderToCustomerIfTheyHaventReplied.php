<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendReminderToCustomerIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was updated.']);

            $now = Carbon::now()->toDateTimeString();

            //get latest messages for each customer ignoring the auto messages
            $messagesIds = ChatMessage::selectRaw('MAX(id) as id, customer_id')
                ->groupBy('customer_id')
                ->whereNotNull('message')
                ->where('customer_id', '>', '0')
                ->where(function ($query) {
                    $query->whereNotIn('status', [7, 8, 9, 10]);
                })
                ->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query was finished.']);

            foreach ($messagesIds as $messagesId) {
                $customer = Customer::find($messagesId->customer_id);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'customer query finished.']);
                if (! $customer) {
                    continue;
                }

                $frequency = $customer->frequency;
                if ($frequency < 5) {
                    continue;
                }

                if ($customer->reminder_from == '0000-00-00 00:00' || strtotime($customer->reminder_from) >= strtotime('now')) {
                    dump('here'.$customer->name);
                    $templateMessage = $customer->reminder_message;
                    if ($customer->reminder_last_reply == 0) {
                        //sends messahe
                        $this->sendMessage($customer->id, $templateMessage);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'message sent.']);
                    } else {
                        // get the message if the interval is greater or equal to time which is set for this customer
                        $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "'.$now.'") >= '.$frequency)
                            ->where('id', $messagesId->id)
                            ->where('user_id', '>', '0')
                            ->where('approved', '1')
                            ->first();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query was finished.']);

                        if (! $message) {
                            continue;
                        }
                        $this->sendMessage($customer->id, $templateMessage);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
                    }
                    dump('saving...');
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report time was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * @param  $message
     *                  Send message to customer, create message and then approve message...
     * @param  mixed  $customer
     */
    private function sendMessage($customer, $message): void
    {
        $params = [
            'number' => null,
            'user_id' => 6,
            'approved' => 1,
            'status' => 1,
            'customer_id' => $customer,
            'message' => $message,
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('customer', $myRequest);
    }
}
