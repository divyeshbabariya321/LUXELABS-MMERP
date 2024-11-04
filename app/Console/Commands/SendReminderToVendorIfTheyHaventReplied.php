<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use App\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendReminderToVendorIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-vendor';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);

            $now = Carbon::now()->toDateTimeString();

            // get the latest message for this vendor excluding the auto messages like supplier and customers
            $messagesIds = ChatMessage::selectRaw('MAX(id) as id, vendor_id')
                ->groupBy('vendor_id')
                ->whereNotNull('message')
                ->where('vendor_id', '>', '0')
                ->where(function ($query) {
                    $query->whereNotIn('status', [7, 8, 9]);
                })
                ->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query finished.']);

            foreach ($messagesIds as $messagesId) {
                $vendor = Vendor::find($messagesId->vendor_id);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'vendor query finished.']);
                if (! $vendor) {
                    continue;
                }

                $frequency = $vendor->frequency;
                if ($frequency < 5) {
                    continue;
                }

                if ($vendor->reminder_from == '0000-00-00 00:00' || strtotime($vendor->reminder_from) >= strtotime('now')) {
                    dump('here'.$vendor->name);
                    $templateMessage = $vendor->reminder_message;
                    if ($vendor->reminder_last_reply == 0) {
                        //sends messahe
                        $this->sendMessage($vendor->id, $templateMessage);
                        dump('saving...');
                    } else {
                        // get the message if the interval is greater or equal to time which is set for this customer
                        $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "'.$now.'") >= '.$frequency)
                            ->where('id', $messagesId->id)
                            ->where('user_id', '>', '0')
                            ->where('approved', '1')
                            ->first();

                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Chat message query finished.']);
                        if (! $message) {
                            continue;
                        }
                        //send the message
                        $this->sendMessage($vendor->id, $templateMessage);
                        dump('saving...');
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    /**
     * @param  $message
     *                  create chat message entry and then approve the message and send the message...
     * @param  mixed  $vendorId
     */
    private function sendMessage($vendorId, $message)
    {
        $params = [
            'number' => null,
            'user_id' => 6,
            'approved' => 1,
            'status' => 1,
            'vendor_id' => $vendorId,
            'message' => $message,
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('vendor', $myRequest);
    }
}
