<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Helpers\LogHelper;
use App\Helpers\OrderHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class AutoReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:auto-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending auto reminders to customers who didn\'t reply';

    /**
     * Create a new command instance.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);

        try {
            $report = $this->createCronJobReport();
            $customers = $this->getCustomersWithOrders();

            foreach ($customers as $customer) {
                foreach ($customer['orders'] as $order) {
                    $this->handleOrderForCustomer($customer, $order);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function createCronJobReport()
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);

        return CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);
    }

    private function getCustomersWithOrders()
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer query started.']);

        return Customer::with(['Orders' => function ($query) {
            $query->where('order_status_id', OrderHelper::$proceedWithOutAdvance)
                ->where('auto_messaged', 1)
                ->latest();
        }])->whereHas('Orders', function ($query) {
            $query->where('order_status_id', OrderHelper::$proceedWithOutAdvance)
                ->where('auto_messaged', 1)
                ->latest();
        })->get()->toArray();
    }

    private function handleOrderForCustomer(array $customer, array $order)
    {
        $time_diff = Carbon::parse($order['auto_messaged_date'])->diffInHours(Carbon::now());

        $params = [
            'customer_id' => $customer['id'],
            'number' => null,
            'status' => 1,
            'user_id' => 6,
        ];

        if ($this->shouldSendMessage($time_diff, $params)) {
            $this->processChatMessages($customer, $order, $params);
        }
    }

    private function shouldSendMessage(int $time_diff, array &$params): bool
    {
        if ($time_diff == 24) {
            $params['message'] = 'Reminder about COD after 24 hours';

            return true;
        }

        if ($time_diff == 72) {
            $params['message'] = 'Please note that since your order was placed on COD, an initial advance needs to be paid to process the order.';

            return true;
        }

        return false;
    }

    private function processChatMessages(array $customer, array $order, array $params)
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query started.']);

        $chat_messages = ChatMessage::where('customer_id', $customer['id'])
            ->whereBetween('created_at', [$order['auto_messaged_date'], Carbon::now()])
            ->latest()->get();

        if (! $this->hasReceivedMessage($chat_messages)) {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Chat message saved.']);
        }
    }

    private function hasReceivedMessage($chat_messages): bool
    {
        foreach ($chat_messages as $chat_message) {
            if ($chat_message->number) {
                return true;
            }
        }

        return false;
    }
}
