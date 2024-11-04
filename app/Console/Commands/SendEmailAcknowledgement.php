<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Email;
use App\Jobs\SendEmail;
use App\Models\EMailAcknowledgement;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendEmailAcknowledgement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acknowledgement:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Acknowledgement Email';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $currentTime = Carbon::now()->format('Y-m-d H:i:s');

            $EMailAcknowledgement = EMailAcknowledgement::with('email_address_record')->where('end_date', '>=', $currentTime)->get();

            if (! empty($EMailAcknowledgement)) {
                foreach ($EMailAcknowledgement as $value) {
                    $latest_email = Email::where('to', $value->email_address_record->username)->where('is_reply', 0)->where('created_at', '>', $value->start_date)->where('created_at', '<', $value->end_date)->get();

                    if (! empty($latest_email)) {
                        foreach ($latest_email as $email) {

                            Email::where('id', $email->id)->update(['is_reply' => 1]);

                            if ($value->ack_status == 1) {
                                $status = 'outgoing';
                                $is_draft = 0;
                            } else {
                                $status = 'pre-send';
                                $is_draft = 1;
                            }

                            $email = Email::create([
                                'model_id' => $email->id,
                                'model_type' => Email::class,
                                'from' => $email->to,
                                'to' => $email->from,
                                'subject' => 'Re: '.$email->subject,
                                'message' => $value->ack_message,
                                'template' => 'reply-email',
                                'additional_data' => '',
                                'type' => 'outgoing',
                                'status' => $status,

                                'store_website_id' => null,
                                'is_draft' => $is_draft,
                            ]);

                            SendEmail::dispatch($email)->onQueue('send_email');
                        }
                    }
                }
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
