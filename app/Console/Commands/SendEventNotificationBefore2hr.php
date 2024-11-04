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

class SendEventNotificationBefore2hr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:event-notification2hr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Event notification before 2 hr';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);

            // get the events which has 24 hr left
            $events = UserEvent::havingRaw('TIMESTAMPDIFF(HOUR,now() , start) = 2')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Event query finished.']);

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
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'user query finished.']);
                    // if user exist
                    if (! empty($user)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on within the next 2 hours';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;
                        }

                        $params['user_id'] = $user->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message created.']);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            if (! empty($vendorParticipants)) {
                foreach ($vendorParticipants as $id => $vendorParticipant) {
                    $vendor = Vendor::find($id);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'vendor created.']);
                    if (! empty($vendor)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on within the next 2 hours';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;
                        }

                        $params['vendor_id'] = $vendor->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message created.']);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithThirdApi($vendor->phone, $vendor->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            //

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
