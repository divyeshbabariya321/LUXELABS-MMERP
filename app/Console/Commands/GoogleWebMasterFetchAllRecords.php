<?php

namespace App\Console\Commands;

use App\CronJob;
use App\GoogleClientAccount;
use App\GoogleClientAccountMail;
use App\GoogleSearchAnalytics;
use App\GoogleWebMasters;
use App\Helpers\LogHelper;
use App\LogRequest;
use App\Site;
use Exception;
use Illuminate\Console\Command;

class GoogleWebMasterFetchAllRecords extends Command
{
    protected $signature = 'fetch-all-records:start';

    protected $description = 'it will fetch data from that date and insert it';

    const DATE_FORMATE = 'Y-m-d H:i:s';

    public function handle(): void
    {
        try {
            $startTime = date(self::DATE_FORMATE, LARAVEL_START);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron stared to run']);

            $GoogleClientAccounts = GoogleClientAccount::get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting google client accounts']);

            foreach ($GoogleClientAccounts as $GoogleClientAccount) {
                $refreshToken = GoogleClientAccountMail::where('google_client_account_id', $GoogleClientAccount->id)->first();
                if (isset($refreshToken['GOOGLE_CLIENT_REFRESH_TOKEN']) && $refreshToken['GOOGLE_CLIENT_REFRESH_TOKEN'] != null) {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'found the refresh token from google account id:'.$GoogleClientAccount->id]);

                    $GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN = $refreshToken->GOOGLE_CLIENT_REFRESH_TOKEN;

                    if ($GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN == null) {
                        continue;
                    }

                    $this->client = new \Google_Client;
                    $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
                    $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
                    $this->client->refreshToken($GoogleClientAccount->GOOGLE_CLIENT_REFRESH_TOKEN);

                    $token = $this->client->getAccessToken();
                    if (empty($token)) {
                        continue;
                    }

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Connecting to google client']);

                    if ($this->client->getAccessToken()) {
                        $this->updateSitesData($token);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Updated sites data']);

                        $url = 'https://www.googleapis.com/webmasters/v3/sites/';
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => [
                                'authorization:Bearer '.$this->client->getAccessToken()['access_token'],
                            ],
                        ]);

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        if (curl_errno($curl)) {
                            $error_msg = curl_error($curl);
                        }

                        if (isset($error_msg)) {
                            $this->curl_errors_array[] = ['key' => 'sites', 'error' => $error_msg, 'type' => 'sites'];
                            activity('v3_sites')->log($error_msg);
                        }

                        $check_error_response = json_decode($response);

                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        //here response alredy decode
                        LogRequest::log($startTime, $url, 'GET', json_encode([]), $check_error_response, $httpcode, GoogleWebMasterFetchAllRecords::class, 'handle');

                        curl_close($curl);

                        if (isset($check_error_response->error->message) || $err) {
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Error found from the curl request']);

                            $this->curl_errors_array[] = ['key' => 'sites', 'error' => $check_error_response->error->message, 'type' => 'sites'];
                            activity('v3_sites')->log($check_error_response->error->message);
                            echo $this->curl_errors_array[0]['error'];
                        } else {
                            if (is_array(json_decode($response)->siteEntry)) {
                                foreach (json_decode($response)->siteEntry as $site) {
                                    // Create ot update site url
                                    GoogleWebMasters::updateOrCreate(['sites' => $site->siteUrl]);

                                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google web master record']);

                                    echo 'https://www.googleapis.com/webmasters/v3/sites/'.urlencode($site->siteUrl).'/sitemaps';
                                    $curl1 = curl_init();
                                    //replace website name with code coming form site list
                                    $url = "https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site->siteUrl) . '/sitemaps";
                                    curl_setopt_array($curl1, [
                                        CURLOPT_URL => $url,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 30,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'GET',
                                        CURLOPT_HTTPHEADER => [
                                            'authorization: Bearer '.$this->client->getAccessToken()['access_token'],
                                        ],
                                    ]);

                                    $response1 = curl_exec($curl1);
                                    $err = curl_error($curl1);
                                    $httpcode = curl_getinfo($curl1, CURLINFO_HTTP_CODE);
                                    LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response1), $httpcode, GoogleWebMasterFetchAllRecords::class, 'handle');

                                    if ($err) {
                                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Error found from the curl request']);

                                        activity('v3_sites')->log($err);
                                        echo 'cURL Error #:'.$err;
                                    } else {
                                        if (isset(json_decode($response1)->sitemap) && is_array(json_decode($response1)->sitemap)) {
                                            foreach (json_decode($response1)->sitemap as $sitemap) {
                                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'updated crawls detail for site'.$site->siteUrl]);

                                                GoogleWebMasters::where('sites', $site->siteUrl)->update(['crawls' => $sitemap->errors]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function updateSitesData($token)
    {
        if (! (isset($token['access_token']))) {
            redirect()->route('googlewebmaster.get-access-token');
        }

        $GOOGLE_CLIENT_MULTIPLE_KEYS = config('google.GOOGLE_CLIENT_MULTIPLE_KEYS');

        $google_keys = explode(',', $GOOGLE_CLIENT_MULTIPLE_KEYS);
        $startTime = date(self::DATE_FORMATE, LARAVEL_START);

        //$token = $request->session()->get('token');
        foreach ($google_keys as $google_key) {
            if ($google_key) {
                $this->apiKey = $google_key;
                $this->googleToken = $token['access_token'];
                $url_for_sites = 'https://www.googleapis.com/webmasters/v3/sites?key='.$this->apiKey.'<br>';
                $curl = curl_init();

                //replace website name with code coming form site list
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url_for_sites,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'authorization: Bearer '.$this->googleToken,

                    ],
                ]);

                $response = curl_exec($curl);
                $response = json_decode($response); // here Response decode
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url_for_sites, 'GET', json_encode([]), $response, $httpcode, GoogleWebMasterFetchAllRecords::class, 'updateSitesData');

                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                }

                curl_close($curl);

                if (isset($error_msg)) {
                    $this->curl_errors_array[] = ['key' => $google_key, 'error' => $error_msg, 'type' => 'site_list'];
                    activity('v3_sites')->log($error_msg);
                }

                if (isset($response->error->message)) {
                    $this->curl_errors_array[] = ['key' => $google_key, 'error' => $response->error->message, 'type' => 'site_list'];
                    activity('v3_sites')->log($response->error->message);
                }

                if (isset($response->siteEntry) && count($response->siteEntry)) {
                    $this->updateSites($response->siteEntry);
                }
            }
        }
    }

    public function updateSites($sites)
    {
        foreach ($sites as $site) {
            if ($siteRow = Site::whereSiteUrl($site->siteUrl)->first()) {
                $siteRow->update(['permission_level' => $site->permissionLevel]);
                $this->sitesUpdated++;
            } else {
                $siteRow = Site::create(['site_url' => $site->siteUrl, 'permission_level' => $site->permissionLevel]);
                $this->sitesCreated++;
            }
            $this->SearchAnalytics($site->siteUrl, $siteRow->id);
            $this->SearchAnalyticsBysearchApperiance($site->siteUrl, $siteRow->id);
        }
    }

    public function SearchAnalyticsBysearchApperiance($siteUrl, $siteID)
    {
        $params['startDate'] = '2000-01-01';
        $params['endDate'] = date('Y-m-d');
        $params['dimensions'] = ['searchAppearance'];

        $response = $this->googleResultForAnaylist($siteUrl, $params);

        if (isset($response->rows) && count($response->rows)) {
            $this->updateSearchAnalyticsForSearchAppearence($response->rows, $siteID);
        }
    }

    public function updateSearchAnalyticsForSearchAppearence($rows, $siteID)
    {
        foreach ($rows as $row) {
            $record = ['clicks' => $row->clicks, 'impressions' => $row->impressions, 'position' => $row->position, 'ctr' => $row->ctr, 'site_id' => $siteID];

            $record['search_apperiance'] = $row->keys[0];
            $rowData = new GoogleSearchAnalytics;

            foreach ($record as $col => $val) {
                $rowData = $rowData->where($col, $val);
            }

            if (! $rowData->first()) {
                GoogleSearchAnalytics::create($record);
                $this->searchAnalyticsCreated++;
            }
        }
    }

    public function SearchAnalytics($siteUrl, $siteID)
    {
        $params['startDate'] = '2000-01-01';
        $params['endDate'] = date('Y-m-d');
        $params['dimensions'] = ['country', 'device', 'page', 'query', 'date'];

        $response = $this->googleResultForAnaylist($siteUrl, $params);

        if (isset($response->rows) && count($response->rows)) {
            $this->updateSearchAnalytics($response->rows, $siteID);
        }
    }

    public function updateSearchAnalytics($rows, $siteID)
    {
        foreach ($rows as $row) {
            $record = ['clicks' => $row->clicks, 'impressions' => $row->impressions, 'position' => $row->position, 'ctr' => $row->ctr, 'site_id' => $siteID];

            $record['country'] = $row->keys[0];
            $record['device'] = $row->keys[1];
            $record['page'] = $row->keys[2];
            $record['query'] = $row->keys[3];
            $record['date'] = $row->keys[4];

            $rowData = new GoogleSearchAnalytics;

            foreach ($record as $col => $val) {
                $rowData = $rowData->where($col, $val);
            }

            if (! $rowData->first()) {
                GoogleSearchAnalytics::create($record);
                $this->searchAnalyticsCreated++;
            }
        }
    }

    public function googleResultForAnaylist($siteUrl, $params)
    {
        $url = 'https://www.googleapis.com/webmasters/v3/sites/'.urlencode($siteUrl).'/searchAnalytics/query';
        $startTime = date(self::DATE_FORMATE, LARAVEL_START);

        $curl = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //  CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer '.$this->googleToken,
                'Content-Type:application/json',
            ],
        ]);

        $response = curl_exec($curl);

        $response = json_decode($response); // response decoded
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($params), $response, $httpcode, 'googleResultForAnaylist', GoogleWebMasterFetchAllRecords::class);

        if (isset($response->error->message)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $response->error->message, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($response->error->message);
        }

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }

        curl_close($curl);
        if (isset($error_msg)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $error_msg, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($error_msg);
        }

        return $response;
    }
}
