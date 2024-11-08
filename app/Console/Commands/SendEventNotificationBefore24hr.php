<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use App\User;
use App\UserEvent\UserEvent;
use App\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendEventNotificationBefore24hr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:event-notification24hr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Event notification before 24 hr';

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

            // get the events which has 24 hr left
            $events = UserEvent::havingRaw('TIMESTAMPDIFF(HOUR,now() , start) = 24')->get();

            $userWise = [];
            $vendorParticipants = [];
            if (! $events->isEmpty()) {
                foreach ($events as $event) {
                    $userWise[$event->user_id][] = $event;
                    $participants = $event->attendees;
                    if (! $participants->isEmpty()) {
                        foreach ($participants as $participant) {
                            if ($participant->object == Vendor::class) {
                                $vendorParticipants[$participant->object_id] = $event;
                            }
                        }
                    }
                }
            }

            if (! empty($userWise)) {
                foreach ($userWise as $id => $events) {
                    // find user into database
                    $user = User::find($id);
                    // if user exist
                    if (! empty($user)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on within the next 24 hours';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;
                        }

                        $params['user_id'] = $user->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithWhatsApp($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            if (! empty($vendorParticipants)) {
                foreach ($vendorParticipants as $id => $vendorParticipant) {
                    $vendor = Vendor::find($id);
                    if (! empty($vendor)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on within the next 24 hours';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;
                        }

                        $params['vendor_id'] = $vendor->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithWhatsApp($vendor->phone, $vendor->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
