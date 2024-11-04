<?php

namespace App\Console\Commands\Manual;

use App\Language;
use App\LogRequest;
use App\Mailinglist;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateMailingListNewsLetters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create_mailing_list_news_letters';

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
        $storeWebsites = StoreWebsite::get();
        $languages = Language::get();

        foreach ($storeWebsites as $website) {
            foreach ($languages as $lang) {
                $anyFound = Mailinglist::where(['language' => $lang->id, 'website_id' => $website->id])->first();

                if (! $anyFound) {
                    $this->createSendInBlueMailingList($website, $lang);
                }
            }
        }
    }

    public function createSendInBlueMailingList($website = null, $lan = null)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $return_response = [];
        $curl = curl_init();
        $data = [
            'folderId' => 1,
            'name' => $website->title,
        ];
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : getenv('SEND_IN_BLUE_API');
        $url = 'https://api.sendinblue.com/v3/contacts/lists';
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                // "api-key: ".getenv('SEND_IN_BLUE_API'),
                'api-key: '.$api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($response), $httpcode, CreateMailingListNewsLetters::class, 'createSendInBlueMailingList');

        if (curl_errno($curl)) {
            $return_response['code'] = 401;
            $return_response['msg'] = curl_error($curl);

            return $return_response;
        }

        curl_close($curl);
        Log::info($response);
        $res = json_decode($response);
        if (isset($res->id)) {
            $last_record_id = Mailinglist::create([
                'id' => $res->id,
                'name' => $website->title,
                'language' => $lan->id,
                'website_id' => $website->id,
                'service_id' => 1,
                'remote_id' => $res->id,
                'send_in_blue_api' => $website->send_in_blue_api,
                'send_in_blue_account' => $website->send_in_blue_account,
            ]);

            $return_response['code'] = 200;
            $return_response['msg'] = 'success';
            $return_response['last_record_id'] = $last_record_id->id;
        } else {
            $return_response['code'] = 401;
            $return_response['msg'] = $res->message;
        }

        return $return_response;
    }
}
