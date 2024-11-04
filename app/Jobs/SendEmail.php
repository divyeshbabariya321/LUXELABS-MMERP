<?php

namespace App\Jobs;

use App\CommunicationHistory;
use App\Email;
use App\EmailLog;
use App\Mails\Manual\DefaultSendEmail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailNewData;

    public $emailOldData;

    public $tries = 3;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     *
     * @return void
     */
    public function __construct(public Email $email, array $emaildetails = [])
    {
        $this->emailOldData = $email;
        $this->emailNewData = $emaildetails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Used to set customer email's data to send email to customer if question has auto approve flag is yes
        if (! empty($this->emailNewData)) {
            $updatedEmail = $this->emailNewData;

            $email = $this->email;
            $email->to = $updatedEmail['to'];
            $email->from = $updatedEmail['from'];
            $email->message = $updatedEmail['message'];
        } else {
            $email = $this->email;
        }

        $moduleName = Str::replace('::class', ' Module', $email->model_type);
        $emailData = [];
        if (! empty($email->to)) {
            $emailData['to'] = $email->to;
        }
        if (! empty($email->cc)) {
            $emailData['cc'] = $email->cc;
        }
        if (! empty($email->bcc)) {
            $emailData['bcc'] = $email->bcc;
        }
        if (! empty($email->from)) {
            $emailData['from'] = $email->from;
        }
        if (! empty($email->from)) {
            $emailData['module'] = $moduleName;
        }

        EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email processing job started for '.$moduleName,
            'message' => json_encode($emailData),
        ]);

        try {
            $multimail = Mail::to($email->to);

            if (! empty($email->cc)) {
                $multimail->cc($email->cc);
            }
            if (! empty($email->bcc)) {
                $multimail->bcc($email->bcc);
            }

            $data = json_decode($email->additional_data, true);

            $attchments = [];

            if (! empty($data['attachment'])) {
                $attchments = $data['attachment'];
            }

            $multimail->send(new DefaultSendEmail($email, $attchments));

            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'Email processing completed successfully for '.$moduleName,
                'message' => json_encode($emailData),
            ]);

            CommunicationHistory::create([
                'model_id' => $email->model_id,
                'model_type' => $email->model_type,
                'type' => $email->template,
                'refer_id' => $email->id,
                'method' => 'email',
            ]);
            if (! empty($this->emailNewData)) {
                $emailOld = $this->emailOldData;
                $email->to = $emailOld['to'];
                $email->from = $emailOld['from'];
                $email->message = $emailOld['message'];
            }
            $email->is_draft = 0;
            $email->status = 'send';
        } catch (Exception $e) {
            $email->is_draft = 0;
            $email->error_message = $e->getMessage();
            $email->save();

            Log::info('Issue fom SendEmail '.$e->getMessage());

            EmailLog::create([
                'email_id' => $email->id,
                'email_log' => 'An error occurred while email processing for '.$moduleName,
                'message' => $e->getMessage(),
            ]);
            throw new Exception($e->getMessage());
        }

        $email->save();
    }

    public function tags()
    {
        return ['SendEmail', $this->email->id];
    }
}
