<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\MailinglistEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class MailingListSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MailingListSendMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending templates with sendinblue';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $mailing_list = MailinglistEmail::orderByDesc('created_at')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Mail list query finished.']);
            $now = Carbon::now();
            foreach ($mailing_list as $mailing) {
                $emails = $mailing->audience->listCustomers->pluck('email');
                $array_emails = [];
                foreach ($emails as $email) {
                    array_push($array_emails, ['email' => $email]);
                }
                $diff = $now->diffInMinutes($mailing->scheduled_date);
                if ($diff <= 15 && $mailing->progress == 0) {
                    $htmlContent = $mailing->html;
                    $data = [
                        'to' => $array_emails,
                        'sender' => [
                            'id' => 1,
                            'email' => 'Info@theluxuryunlimited.com',
                        ],
                        'subject' => 'test',
                        'htmlContent' => $htmlContent,
                    ];
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL request started => https://api.sendinblue.com/v3/smtp/email']);
                    $curl = curl_init();
                    $url = 'https://api.sendinblue.com/v3/smtp/email';
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,

                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($data),
                        CURLOPT_HTTPHEADER => [
                            'api-key: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',
                            'Content-Type: application/json',
                        ],
                    ]);
                    $result = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($result), $httpcode, MailingListSendMail::class, 'handle');
                    curl_close($curl);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL request ended => https://api.sendinblue.com/v3/smtp/email']);
                    $mailing->progress = 1;
                    $mailing->save();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Mail saved.']);
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
