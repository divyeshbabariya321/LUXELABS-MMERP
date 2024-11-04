<?php

namespace App\Console\Commands;

use App\ChatMessagesQuickData;
use App\CronJob;
use App\Customer;
use App\Helpers\LogHelper;
use Exception;
use Illuminate\Console\Command;

class CustomerChatMessageQuickData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:chat-message-quick-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get customers last message and store it into new table';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer chunk query started.']);
            Customer::with(['allMessages' => function ($qr) {
                $qr->orderByDesc('created_at');
            }])->chunk(100, function ($customers) {
                foreach ($customers as $customer) {
                    if (count($customer->allMessages)) {
                        foreach ($customer->allMessages as $item1) {
                            $data['last_unread_message'] = ($item1->status == 0) ? $item1->message : null;
                            $data['last_unread_message_at'] = ($item1->status == 0) ? $item1->created_at : null;
                            $data['last_communicated_message'] = ($item1->status > 0) ? $item1->message : null;

                            $data['last_communicated_message_at'] = ($item1->status > 0) ? $item1->created_at : null;
                            $data['last_communicated_message_at'] = ($item1->status > 0) ? $item1->created_at : null;
                            $data['last_unread_message_id'] = null;
                            $data['last_communicated_message_id'] = null;

                            if (! empty($data['last_unread_message'])) {
                                $data['last_unread_message_id'] = $item1->id;
                            }
                            if (! empty($data['last_communicated_message'])) {
                                $data['last_communicated_message_id'] = $item1->id;
                            }

                            if (! empty($data['last_unread_message']) || ! empty($data['last_communicated_message'])) {
                                ChatMessagesQuickData::updateOrCreate([

                                    'model' => Customer::class,
                                    'model_id' => $customer->id,
                                ], $data);
                                break;
                            }
                        }
                    }
                }
            });
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer chunk query ended.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
