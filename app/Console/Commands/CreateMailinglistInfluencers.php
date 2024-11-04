<?php

namespace App\Console\Commands;

use App\Customer;
use App\Loggers\MailinglistIinfluencersDetailLogs;
use App\Loggers\MailinglistIinfluencersLogs;
use App\LogRequest;
use App\Mailinglist;
use App\MaillistCustomerHistory;
use App\ScrapInfluencer;
use App\Service;
use App\Setting;
use App\StoreWebsite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateMailinglistInfluencers extends Command
{
    protected $signature = 'create-mailinglist-influencers';

    protected $description = 'This command is using for create mailing list from influencers ';

    const SEND_IN_BLUE_API_URL = 'https://api.sendinblue.com/v3/contacts/lists';

    const TIME_FORMATE = 'Y-m-d H:i:s';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $mailList = [];

    public function handle(): void
    {
        if (Setting::get('run_mailing_command') == 1) {
            $influencers = $this->getInfluencers();
            MailinglistIinfluencersLogs::log(count($influencers).' influencers found for CreateMailinglistInfluencers on -> '.now());

            $websites = $this->getWebsites();
            MailinglistIinfluencersLogs::log(count($websites).' websites found for CreateMailinglistInfluencers on -> '.now());

            foreach ($websites as $website) {

                $this->processWebsite($website);
            }

            if (! empty($influencers) && ! empty($this->mailList)) {
                $this->processInfluencers($influencers);
            }
        }
    }

    private function getInfluencers()
    {
        return ScrapInfluencer::where(function ($q) {
            $q->orWhere('read_status', '!=', 1)->orWhereNull('read_status');
        })->where('email', '!=', '')->limit(1)->get();
    }

    private function getWebsites()
    {
        return StoreWebsite::select('id', 'title', 'mailing_service_id', 'send_in_blue_api', 'send_in_blue_account')
            ->where('website_source', 'magento')
            ->whereNotNull('mailing_service_id')
            ->where('mailing_service_id', '>', 0)
            ->where('id', 1)
            ->orderByDesc('id')
            ->get();
    }

    private function processWebsite($website)
    {
        $service = Service::find($website->mailing_service_id);
        if (! $service) {
            MailinglistIinfluencersLogs::log('Service is not found for this website');

            return;
        }

        $name = $this->generateMailingListName($website);
        $mailingList = Mailinglist::where('name', $name)
            ->where('service_id', $website->mailing_service_id)
            ->where('website_id', $website->id)
            ->where('remote_id', '>', 0)
            ->first();

        if (! $mailingList) {
            $mailList = $this->createMailingList($name, $website);
            $this->handleMailingListCreation($mailList, $service, $name, $website);
        } else {
            MailinglistIinfluencersLogs::log('MailingList for this website found is --> '.$mailingList->id);
            $this->mailList[] = $mailingList;
        }
    }

    private function generateMailingListName($website)
    {
        $name = $website->title ?: 'WELCOME_LIST';

        return $name.'_'.date('d_m_Y');
    }

    private function createMailingList($name, $website)
    {
        MailinglistIinfluencersLogs::log('MailingList not found for website --> '.$website->title);
        $mailList = Mailinglist::create([
            'name' => $name,
            'website_id' => $website->id,
            'service_id' => $website->mailing_service_id,
            'send_in_blue_api' => $website->send_in_blue_api,
            'send_in_blue_account' => $website->send_in_blue_account,
        ]);
        MailinglistIinfluencersLogs::log('MailingList created with name --> '.$name);

        return $mailList;
    }

    private function handleMailingListCreation($mailList, $service, $name, $website)
    {
        if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) {
            $this->handleSendInBlue($mailList, $name, $website);
        } elseif (strpos($service->name, 'AcelleMail') !== false) {
            $this->handleAcelleMail($mailList, $name, $website);
        }
    }

    private function handleSendInBlue($mailList, $name, $website)
    {
        $url = self::SEND_IN_BLUE_API_URL;
        $req = ['folderId' => 1, 'name' => $name];
        $response = $this->callApi($url, 'POST', $req, $website->send_in_blue_api);

        if (isset($response->id)) {
            $mailList->remote_id = $response->id;
            $mailList->save();
            $this->mailList[] = $mailList;
        }
    }

    private function handleAcelleMail($mailList, $name, $website)
    {
        $url = 'https://acelle.theluxuryunlimited.com/api/v1/lists?api_token='.config('env.ACELLE_MAIL_API_TOKEN');
        $req = [
            'contact[company]' => '.',
            'name' => $name,
            'contact[state]' => 'afdf',
            'default_subject' => $name,
            'from_email' => 'welcome@test.com',
            'from_name' => 'dsfsd',
            'contact[address_1]' => 'af',
            'contact[country_id]' => '219',
            'contact[city]' => 'sdf',
            'contact[zip]' => 'd',
            'contact[phone]' => 'd',
            'contact[email]' => 'welcome@test.com',
        ];

        $response = $this->makeCurlRequest($url, $req);
        if ($response->status == 1 && isset($response->list_uid)) {
            $mailList->remote_id = $response->list_uid;
            $mailList->save();
            $this->mailList[] = $mailList;
        }
    }

    private function makeCurlRequest($url, $req)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }

    private function processInfluencers($influencers)
    {
        foreach ($influencers as $list) {
            foreach ($this->mailList as $mllist) {
                $this->addInfluencerToMailingList($list, $mllist);
                $this->handleCustomer($list);
            }
            $list->read_status = 1;
            $list->save();
        }
    }

    private function addInfluencerToMailingList($list, $mllist)
    {
        $serviceName = $mllist->service->name ?? '';
        $api_key = $mllist->send_in_blue_api;

        if (strpos(strtolower($serviceName), strtolower('SendInBlue')) !== false) {
            $this->addToSendInBlue($list, $mllist, $api_key);
        } elseif (strpos($serviceName, 'AcelleMail') !== false) {
            $this->addToAcelleMail($list, $mllist);
        }
    }

    private function addToSendInBlue($list, $mllist, $api_key)
    {
        $reqData = [
            'emails' => [$list->email],
            'attributes' => ['firstname' => $list->name],
            'updateEnabled' => true,
        ];
        $url = self::SEND_IN_BLUE_API_URL.'/'.$mllist->remote_id.'/contacts';
        $response = $this->callApi($url, 'POST', $reqData, $api_key);

        if ($response->code == 'success') {
            MailinglistIinfluencersDetailLogs::log('Successfully added influencer to SendInBlue list '.$mllist->id.' with email '.$list->email);
        } else {
            MailinglistIinfluencersDetailLogs::log('Failed to add influencer to SendInBlue list: '.json_encode($response));
        }
    }

    private function addToAcelleMail($list, $mllist)
    {
        $url = 'https://acelle.theluxuryunlimited.com/api/v1/lists/'.$mllist->remote_id.'/contacts?api_token='.config('env.ACELLE_MAIL_API_TOKEN');
        $req = [
            'email' => $list->email,
            'first_name' => $list->name,
            // additional fields...
        ];
        $response = $this->makeCurlRequest($url, $req);
        if ($response->status == 1) {
            MailinglistIinfluencersDetailLogs::log('Successfully added influencer to AcelleMail list '.$mllist->id.' with email '.$list->email);
        } else {
            MailinglistIinfluencersDetailLogs::log('Failed to add influencer to AcelleMail list: '.json_encode($response));
        }
    }

    private function handleCustomer($list)
    {
        $customer = Customer::where('email', $list->email)->first();
        if (! $customer) {
            $customer = new Customer;
            $customer->fill([
                'email' => $list->email,
                'name' => $list->name,
                'source' => 'scrap_influencer',
            ]);
            $customer->save();
        }
        MaillistCustomerHistory::create([
            'mailinglist_id' => $this->mailList[0]->id ?? null,
            'customer_id' => $customer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function callApi($url, $method, $data = [], $send_in_blue_api = '')
    {
        $startTime = date(self::TIME_FORMATE, LARAVEL_START);
        $curl = curl_init();
        $api_key = ($send_in_blue_api == '') ? getenv('SEND_IN_BLUE_API') : $send_in_blue_api;
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'api-key: '.$api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, $method, json_encode($data), json_decode($response), $httpcode, CreateMailinglistInfluencers::class, 'callApi');
        curl_close($curl);
        Log::info($response);

        return json_decode($response);
    }
}
