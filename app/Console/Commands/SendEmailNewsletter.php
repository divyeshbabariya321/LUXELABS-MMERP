<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\Newsletter;
use Illuminate\Console\Command;

class SendEmailNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $newsletters = Newsletter::whereNull('sent_on')->where('sent_at', '!=', '')->get();

        foreach ($newsletters as $newsletter) {
            $template = $newsletter->mailinglistTemplate;
            if ($template) {
                $products = $newsletter->products;

                if (! $products->isEmpty()) {
                    foreach ($products as $product) {
                        if ($product->hasMedia(config('constants.attach_image_tag'))) {
                            foreach ($product->getMedia(config('constants.attach_image_tag')) as $image) {
                                $product->images[] = getMediaUrl($image);
                            }
                        }
                    }
                }

                $mailinglist = $newsletter->mailinglist;

                if (! empty($mailinglist) && $mailinglist->remote_id) {
                    if ($mailinglist->service && isset($mailinglist->service->name)) {
                        if ($mailinglist->service->name == 'AcelleMail') {
                            $curl = curl_init();
                            $url = 'http://165.232.42.174/api/v1/campaign/create/'.$mailinglist->remote_id.'?api_token='.config('env.ACELLE_MAIL_API_TOKEN');
                            $req = [
                                'name' => $template->subject,
                                'subject' => $template->subject,
                                'run_at' => $newsletter->sent_at,
                                'template_content' => view($template->mail_tpl, compact('products', 'newsletter')),
                            ];
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => $req,
                            ]);

                            $response = curl_exec($curl);
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            LogRequest::log($startTime, $url, 'POST', json_encode($req), json_decode($response), $httpcode, SendEmailNewsletter::class, 'handle');

                            $response = json_decode($response);
                            $newsletter->sent_on = date('Y-m-d H:i:s');
                            $newsletter->save();
                        }
                    }
                }
            }
        }
    }
}
