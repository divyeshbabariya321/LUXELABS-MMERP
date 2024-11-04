<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJobReport;
use App\Marketing\WhatsappConfig;
use App\Services\Whatsapp\ChatApi\ChatApi;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SendQueuePendingChatMessages extends Command
{
    const BROADCAST_PRIORITY = 8;

    const MARKETING_MESSAGE_TYPE_ID = 3;

    const DATE_FORMATE = 'Y-m-d H:i:s';

    public $waitingMessages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:queue-pending-chat-messages {number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send queue pending chat messages, run at every 3rd minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public static function getNumberList()
    {
        $q = WhatsappConfig::select([
            'number', 'instance_id', 'token', 'is_customer_support', 'status', 'is_default',
        ])->where('instance_id', '!=', '')
            ->where('token', '!=', '')
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->get();

        $noList = [];
        foreach ($q as $queue) {
            $noList[] = $queue->number;
        }

        return $noList;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        if (! $this->canExecute()) {
            return;
        }

        $queueStartTime = ChatMessage::getStartTime();
        $queueEndTime = ChatMessage::getEndTime();
        $queueTime = ChatMessage::getQueueTime();

        if (! empty($queueStartTime) && ! empty($queueEndTime) && ! empty($queueTime)) {
            foreach ($queueTime as $no => $time) {
                if ($time > 0) {
                    $this->processQueueMessages($no);

                }
            }
        }
    }

    private function canExecute(): bool
    {
        return ! config('settings.ci') && Schema::hasTable('chat_messages');
    }

    private function processQueueMessages($number): void
    {
        $now = Carbon::now()->format(self::DATE_FORMATE);
        try {
            $report = $this->startCronReport();

            $numberList = [$this->argument('number')];
            $this->waitingMessages = $this->initializeWaitingMessages($numberList);

            if ($this->shouldProcessMessages()) {
                $this->processMessages($numberList, $now, 'customer');
                $this->processMessages($numberList, $now, 'vendor');
            }

            $this->endCronReport($report);
        } catch (Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function startCronReport()
    {
        return CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);
    }

    private function endCronReport($report)
    {
        $report->update(['end_time' => Carbon::now()]);
    }

    private function initializeWaitingMessages(array $numberList): array
    {
        $waitingMessages = [];
        foreach ($numberList as $no) {
            $chatApi = new ChatApi;
            $waitingMessages[$no] = $chatApi->waitingLimit($no);
        }

        return $waitingMessages;
    }

    private function shouldProcessMessages(): bool
    {
        return \App\Helpers\DevelopmentHelper::needToApproveMessage() == 1;
    }

    private function processMessages(array $numberList, string $now, string $type): void
    {
        foreach ($numberList as $number) {
            $sendLimit = ChatMessage::getQueueLimit()[$number] ?? 0;

            $chatMessages = $this->getPendingMessages($number, $sendLimit, $now, $type);

            foreach ($chatMessages as $value) {
                $this->sendMessage($value, $number, $type);
            }
        }
    }

    private function getPendingMessages($number, $limit, $now, $type)
    {
        $model = ChatMessage::where('is_queue', '>', 0);

        if ($type == 'customer') {
            $model = $model->join('customers as c', 'c.id', 'chat_messages.customer_id')
                ->where('c.whatsapp_number', $number)
                ->where(function ($q) {
                    $q->orWhere('chat_messages.group_id', '<=', 0)
                        ->orWhereNull('chat_messages.group_id')
                        ->orWhere('chat_messages.group_id', '');
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('chat_messages.scheduled_at')
                        ->orWhere('chat_messages.scheduled_at', '<=', $now);
                });
        } else {
            $model = $model->join('vendors as v', 'v.id', 'chat_messages.vendor_id')
                ->where('v.whatsapp_number', $number);
        }

        return $model->select('chat_messages.*')->limit($limit)->get();
    }

    private function sendMessage($value, $number, $type)
    {
        if ($value->is_queue > 1) {
            $this->sendBroadcastMessage($value);
        } else {
            if (! $this->isSendingLimitFull($value, $type)) {
                $this->approveMessage($value, $type);
            }
        }
    }

    private function sendBroadcastMessage($value)
    {
        $sendNumber = WhatsappConfig::where('id', $value->is_queue)->first();

        if ($images = $value->getMedia(config('constants.media_tags'))) {
            foreach ($images as $k => $image) {
                \App\ImQueue::create([
                    'im_client' => 'whatsapp',
                    'number_to' => $value->customer->phone,
                    'number_from' => $sendNumber->number ?? $value->customer->whatsapp_number,
                    'text' => ($k == 0) ? $value->message : '',
                    'image' => getMediaUrl($image),
                    'priority' => self::BROADCAST_PRIORITY,
                    'marketing_message_type_id' => self::MARKETING_MESSAGE_TYPE_ID,
                ]);
            }
        } else {
            \App\ImQueue::create([
                'im_client' => 'whatsapp',
                'number_to' => $value->customer->phone,
                'number_from' => $sendNumber->number ?? $value->customer->whatsapp_number,
                'text' => $value->message,
                'priority' => self::BROADCAST_PRIORITY,
                'marketing_message_type_id' => self::MARKETING_MESSAGE_TYPE_ID,
            ]);
        }

        $value->is_queue = 0;
        $value->save();
    }

    private function isSendingLimitFull($value, $type)
    {
        $phone = $type === 'customer' ? $value->customer->whatsapp_number : $value->vendor->whatsapp_number;
        $isSendingLimitFull = $this->waitingMessages[$phone] ?? 0;

        return $isSendingLimitFull >= config('apiwha.message_queue_limit', 100);
    }

    private function approveMessage($value, $type)
    {
        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $value->id]);

        app(\App\Http\Controllers\WhatsAppController::class)->approveMessage($type, $myRequest);
    }
}
