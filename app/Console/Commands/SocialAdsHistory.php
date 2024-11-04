<?php

namespace App\Console\Commands;

use App\Helpers;
use App\Helpers\SocialHelper;
use App\LogRequest;
use App\Setting;
use App\Social\SocialAdHistory;
use App\Social\SocialConfig;
use Exception;
use Facebook\Facebook;
use Illuminate\Console\Command;

class SocialAdsHistory extends Command
{
    private $fb;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:ads-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     *
     * Dev Note: "php artisan social:ads-history" Once this command will run,that time all ads will be fatch which we published via using our config credentials
     */
    public function handle(): int
    {
        $socialConfigs = SocialConfig::latest()->paginate(Setting::get('pagination'));
        $adAccountCollection = [];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        foreach ($socialConfigs as $config) {
            $this->fb = new Facebook([
                'app_id' => $config->api_key,
                'app_secret' => Helpers::getDecryptedData($config->api_secret),
                'default_graph_version' => 'v15.0',
            ]);

            $response = $this->getAdAccount($config, $this->fb);

            if ($response) {
                $pages = $response->getGraphEdge()->asArray();

                foreach ($pages as $key => $page) {
                    $ad_acc_id = $page['id'];
                    $adAccountCollection[$key]['config_id'] = $config->id;
                    $adAccountCollection[$key]['ad_ac_id'] = $ad_acc_id;
                    $adAccountCollection[$key]['token'] = $config->token;
                }
            }
        }

        foreach ($adAccountCollection as $key => $adaccountAds) {
            $query = 'https://graph.facebook.com/v15.0/'.$adaccountAds['ad_ac_id'].'/campaigns?fields=ads{id,name,status,created_time,adcreatives{thumbnail_url},adset{name},insights.level(adset){campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend}}&limit=3000&access_token='.$adaccountAds['token'].'';

            // Call to Graph api here
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $query);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 0);

            $resp = curl_exec($ch);
            $resp = json_decode($resp); //response decodes
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $parameters = [];
            LogRequest::log($startTime, $query, 'GET', json_encode($parameters), $resp, $httpcode, SocialAdsHistory::class, 'handle');
            curl_close($ch);

            $resp->token = $adaccountAds['config_id'];

            if ($resp->data) {
                foreach ($resp->data as $data) {
                    if (isset($data->ads) && ! empty($data->ads)) {
                        foreach ($data->ads->data as $ads) {
                            $ad = new SocialAdHistory;
                            $ad->ad_ac_id = isset($ads->id) ? $ads->id : '';

                            $ad->account_id = isset($ads->insights->data[0]->account_id) ? $ads->insights->data[0]->account_id : '';
                            $ad->reach = isset($ads->insights->data[0]->reach) ? number_format($ads->insights->data[0]->reach, 2) : '';
                            $ad->Impressions = isset($ads->insights->data[0]->impressions) ? number_format($ads->insights->data[0]->impressions, 2) : '';
                            $ad->amount = isset($ads->insights->data[0]->spend) ? number_format($ads->insights->data[0]->spend, 2) : '';
                            $ad->cost_p_result = isset($ads->insights->data[0]->cost_per_unique_click) ? number_format($ads->insights->data[0]->cost_per_unique_click, 2) : '';
                            $ad->ad_name = isset($ads->name) ? $ads->name : '';
                            $ad->status = isset($ads->status) ? $ads->status : '';
                            $ad->adset_name = isset($ads->adset->name) ? $ads->adset->name : '';
                            $ad->action_type = isset($ads->insights->data[0]->actions[0]->action_type) ? $ads->insights->data[0]->actions[0]->action_type : '';
                            $ad->campaign_name = isset($ads->insights->data[0]->campaign_name) ? $ads->insights->data[0]->campaign_name : '';
                            $ad->thumb_image = isset($ads->adcreatives->data[0]->thumbnail_url) ? $ads->adcreatives->data[0]->thumbnail_url : '';
                            $ad->end_time = isset($ads->insights->data[0]->date_stop) ? $ads->insights->data[0]->date_stop : '';
                            $ad->save();
                        }
                    }
                }
            }
        }
    }

    public function getAdAccount($config, $fb)
    {
        $response = '';
        try {
            $token = $config->token;
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            // return $response = $fb->get('/me/adaccounts', $token); //Old
            $url = sprintf('https://graph.facebook.com/v15.0//me/adaccounts?access_token='.$token); //New using graph API

            return $response = SocialHelper::httpGetRequest($url);
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            $this->socialPostLog($config->id, $config->platform, 'error', 'not get adaccounts id->'.$e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id, $config->platform, 'error', 'not get adaccounts id->'.$e->getMessage());
        }
        if ($response != '') {
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                    return $key['id'];
                }
            } catch (exception $e) {
                $this->socialPostLog($config->id, $config->platform, 'error', 'not get adaccounts id->'.$e->getMessage());
            }
        }
    }
}
