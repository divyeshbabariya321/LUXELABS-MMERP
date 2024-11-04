<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckMessagesErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:messages-errors';

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
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $hour_ago = Carbon::now()->subHour();
            $two_ago = Carbon::now()->subHours(2);

            $data = ChatMessage::whereNull('number')
            ->where('approved', 1)
            ->where('status', 2)
            ->where('sent', 0)
            ->whereBetween('created_at', [$two_ago, $hour_ago])
            ->where(function ($query) {
                $query->where('error_status', 0)
                ->orWhere('error_status', 1);
            })
            ->get()
            ->groupBy('error_status');

            foreach ($data as $error_status => $chat_messages) {
                $error = $error_status == 0 ? 1 : ($error_status == 1 ? 2 : 2);
                dump($error);
                foreach ($chat_messages as $chat_message) {
                    if ($customer = Customer::find($chat_message->customer_id)) {
                        $customer->is_error_flagged = 1;
                        $customer->save();
                    }

                    if ($chat_message->message != '') {
                        dump('text');
                        $params['message'] = $chat_message->message;

                        if ($error == 1) {
                            app(WhatsAppController::class)->sendWithWhatsApp($chat_message->customer->phone, $chat_message->customer->whatsapp_number, $params['message'], false, $chat_message->id);
                        }
                    }

                    if ($chat_message->hasMedia(config('constants.media_tags'))) {
                        dump('images');

                        foreach ($chat_message->getMedia(config('constants.media_tags')) as $image) {
                            if ($error == 1) {
                                app(WhatsAppController::class)->sendWithWhatsApp($chat_message->customer->phone, $chat_message->customer->whatsapp_number, str_replace(' ', '%20', getMediaUrl($image)), false, $chat_message->id);
                            }
                        }
                    }

                    $chat_message->update([
                        'error_status' => $error,
                        'created_at' => Carbon::now(),
                    ]);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
