<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\MailinglistEmail;
use Illuminate\Console\Command;

class GetStatsFromEmailServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get stats from email template servers';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $mailEmails = MailinglistEmail::where('progress', 0)->get();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        foreach ($mailEmails as $mailEmail) {
            $list = $mailEmail->audience;

            if ($list->service) {
                if ($list->service && isset($list->service->name)) {
                    if ($list->service->name == 'AcelleMail') {
                        $url = 'http://165.232.42.174/api/v1/campaigns/'.$mailEmail->api_template_id.'?api_token='.config('env.ACELLE_MAIL_API_TOKEN');

                        $ch = curl_init();

                        // set url
                        curl_setopt($ch, CURLOPT_URL, $url);

                        //return the transfer as a string
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        // $output contains the output string
                        $output = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($output), $httpcode, GetStatsFromEmailServers::class, 'handle');

                        // close curl resource to free up system resources
                        curl_close($ch);

                        $response = json_decode($output);

                        if ($response->statistics) {
                            $mailEmail->total_emails_scheduled = $response->statistics->subscriber_count;
                            $mailEmail->total_emails_sent = $response->statistics->delivered_count;
                            $pending = ($response->statistics->subscriber_count - $response->statistics->delivered_count);
                            $mailEmail->total_emails_undelivered = $pending;
                            $mailEmail->save();
                        }

                        if ($response->campaign) {
                            if ($response->campaign->status == 'done') {
                                //save email
                                $mailEmail->progress = 1;
                                $mailEmail->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
