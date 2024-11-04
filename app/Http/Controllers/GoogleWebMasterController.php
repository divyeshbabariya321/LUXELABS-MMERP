<?php

namespace App\Http\Controllers;

use App\GoogleClientAccount;
use App\GoogleClientAccountMail;
use App\GoogleClientNotification;
use App\GoogleSearchAnalytics;
use App\GoogleWebMasters;
use App\LogRequest;
use App\Setting;
use App\Site;
use App\WebmasterLog;
use App\WebsiteStoreView;
use App\WebsiteStoreViewsWebmasterHistory;
use Google_Service_Oauth2;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class GoogleWebMasterController extends Controller
{
    public $sitesCreated = 0;

    public $sitesUpdated = 0;

    public $searchAnalyticsCreated = 0;

    public $apiKey = '';

    public $googleToken = '';

    public $curl_errors_array = [];

    protected $client;

    public function index(Request $request): View
    {
        $getSites = GoogleWebMasters::paginate(Setting::get('pagination'), ['*'], 'crawls_per_page');

        $sites = Site::select('id', 'site_url')->get();

        $logs = Activity::where('log_name', 'v3_sites')->orWhere('log_name', 'v3_search_analytics')->latest()->paginate(Setting::get('pagination'), ['*'], 'logs_per_page');
        $webmaster_logs = WebmasterLog::latest()->paginate(Setting::get('pagination'), ['*'], 'webmaster_logs_per_page');
        $site_submit_history = WebsiteStoreViewsWebmasterHistory::latest()->paginate(Setting::get('pagination'), ['*'], 'history_per_page');

        $SearchAnalytics = new GoogleSearchAnalytics;

        $devices = $SearchAnalytics->select('device')->where('device', '!=', null)->groupBy('device')->orderBy('device')->get();

        $countries = $SearchAnalytics->select('country')->where('country', '!=', null)->groupBy('country')->orderBy('country')->get();
        $SearchAnalytics = $SearchAnalytics->orderByDesc('id');
        if ($request->site) {
            $SearchAnalytics = $SearchAnalytics->where('site_id', $request->site);
        }

        if ($request->device) {
            $SearchAnalytics = $SearchAnalytics->where('device', $request->device);
        }

        if ($request->country != 'all' && ! empty($request->country)) {
            $SearchAnalytics = $SearchAnalytics->where('country', $request->country);
        }

        if ($request->start_date) {
            $SearchAnalytics = $SearchAnalytics->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $SearchAnalytics = $SearchAnalytics->where('date', '<=', $request->end_date);
        }

        if ($request->clicks && ($request->clicks == 'asc' || $request->clicks == 'desc')) {
            $SearchAnalytics = $SearchAnalytics->orderBy('clicks', $request->clicks);
        }

        if ($request->position && ($request->position == 'asc' || $request->position == 'desc')) {
            $SearchAnalytics = $SearchAnalytics->orderBy('position', $request->position);
        }

        if ($request->ctr && ($request->ctr == 'asc' || $request->ctr == 'desc')) {
            $SearchAnalytics = $SearchAnalytics->orderBy('ctr', $request->ctr);
        }

        if ($request->impression && ($request->impression == 'asc' || $request->impression == 'desc')) {
            $SearchAnalytics = $SearchAnalytics->orderBy('impressions', $request->impression);
        }

        if ($request->country == 'all') {
            $SearchAnalytics = $SearchAnalytics->select('*', DB::raw('sum(clicks) as clicks,sum(impressions) as impressions, avg(position) as position,avg(ctr) as ctr'))->groupBy('country');
        }

        $sitesData = $SearchAnalytics->paginate(Setting::get('pagination'));

        return view('google-web-master/index', compact('getSites', 'sitesData', 'sites', 'request', 'devices', 'countries', 'logs', 'webmaster_logs', 'site_submit_history'));
    }

    public function googleLogin(Request $request): RedirectResponse
    {
        $google_redirect_url = route('googlewebmaster.get-access-token');

        $id = Cache::get('google_client_account_id');
        $GoogleClientAccount = GoogleClientAccount::find($id);
        $this->client = new \Google_Client;
        $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
        $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
        $this->client->setRedirectUri($google_redirect_url);
        $this->client->setScopes([
            'https://www.googleapis.com/auth/webmasters',
        ]);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $this->client->setAccessType('offline');
        $access_token = $this->client->authenticate($request->code);
        $oauth2 = new Google_Service_Oauth2($this->client);
        $accountInfo = $oauth2->userinfo->get();

        $mail_acc = new GoogleClientAccountMail;
        GoogleClientAccountMail::where('google_account', $accountInfo->name)->delete();
        $mail_acc->google_account = $accountInfo->name;
        $mail_acc->google_client_account_id = $id;
        $mail_acc->GOOGLE_CLIENT_ACCESS_TOKEN = $access_token['access_token'];
        if (! empty($access_token['refresh_token'])) {
            $mail_acc->GOOGLE_CLIENT_REFRESH_TOKEN = $access_token['refresh_token'];
        }
        $mail_acc->expires_in = $access_token['expires_in'];
        $mail_acc->save();

        return redirect()->route('googlewebmaster.index')->with('success', 'Account connected successfully!');
    }

    public function updateSitesData($request)
    {
        if (! (isset($request->session()->get('token')['access_token']))) {
            redirect()->route('googlewebmaster.get-access-token');
        }
        $this->googleToken = $request->session()->get('token')['access_token'];
        $url_for_sites = 'https://www.googleapis.com/webmasters/v3/sites';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
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

        $parameters['updateSitesData'] = [
            'sites' => $request->sites,
            'crawls' => $request->crawls,
        ];
        $response = curl_exec($curl);
        $response = json_decode($response);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url_for_sites, 'GET', json_encode($parameters), $response, $httpcode, GoogleWebMasterController::class, 'updateSitesData');

        curl_close($curl);

        if (isset($error_msg)) {
            $this->curl_errors_array[] = ['key' => $this->googleToken, 'error' => $error_msg, 'type' => 'site_list'];
            activity('v3_sites')->log($error_msg);
        }

        if (isset($response->error->message)) {
            $this->curl_errors_array[] = ['key' => $this->googleToken, 'error' => $response->error->message, 'type' => 'site_list'];
            activity('v3_sites')->log($response->error->message);
        }

        if (isset($response->siteEntry) && count($response->siteEntry)) {
            $this->updateSites($response->siteEntry);
        }

        return ['status' => 1, 'sitesUpdated' => $this->sitesUpdated, 'sitesCreated' => $this->sitesCreated, 'searchAnalyticsCreated' => $this->searchAnalyticsCreated, 'success' => $this->sitesUpdated.' of sites are updated.', 'error' => count($this->curl_errors_array).' error found in this request.', 'error_message' => $this->curl_errors_array[0]['error'] ?? ''];
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
            $this->updateSearchAnalytics($response->rows, $siteID, $siteUrl);
        }
    }

    public function updateSearchAnalytics($rows, $siteID, $siteUrl)
    {
        $indexData = [];
        foreach ($rows as $key => $row) {
            $record = ['clicks' => $row->clicks, 'impressions' => $row->impressions, 'position' => $row->position, 'ctr' => $row->ctr, 'site_id' => $siteID];

            $record['country'] = $row->keys[0];
            $record['device'] = $row->keys[1];
            $record['page'] = $row->keys[2];
            $record['query'] = $row->keys[3];
            $record['date'] = $row->keys[4];
            if ($key === 0) {
                $response = $this->googleResultForPageInspections($row->keys[2], $siteUrl);
                if (! empty($response) && isset($response->inspectionResult)) {
                    $inspectionResult = $response->inspectionResult;
                    $indexData['indexed'] = $inspectionResult->indexStatusResult->verdict === 'PASS';
                    $indexData['not_indexed'] = $inspectionResult->indexStatusResult->verdict !== 'PASS';
                    $indexData['not_indexed_reason'] = $inspectionResult->indexStatusResult->verdict !== 'PASS' ?
                        ($inspectionResult->indexStatusResult->indexingState !== 'INDEXING_ALLOWED' ? config('constants.google_indexing_state_enum')[$inspectionResult->indexStatusResult->indexingState] : config('constants.google_verdict_enum')[$inspectionResult->indexStatusResult->verdict])
                        : '-';
                    $indexData['mobile_usable'] = isset($inspectionResult->mobileUsabilityResult) ? $inspectionResult->mobileUsabilityResult->verdict === 'PASS' : false;
                    $enhancements = [];
                    if (isset($inspectionResult->richResultsResult) && $inspectionResult->richResultsResult->verdict !== 'PASS') {
                        foreach ($inspectionResult->richResultsResult->detectedItems as $items) {
                            $enhancements[$items->richResultType] = [];
                            foreach ($items->items as $item) {
                                if (isset($item->issues)) {
                                    foreach ($item->issues as $issue) {
                                        $enhancements[] = $issue->issueMessage;
                                    }
                                }
                            }
                        }
                    }
                    $indexData['enhancements'] = implode(',', $enhancements);
                }
            }

            $record = array_merge($record, $indexData);

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

    public function googleResultForPageInspections($pageUrl, $siteUrl)
    {
        $params = ['inspectionUrl' => $pageUrl, 'siteUrl' => $siteUrl];
        $url = 'https://searchconsole.googleapis.com/v1/urlInspection/index:inspect';

        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer '.$this->googleToken,
                'Content-Type:application/json',
            ],
        ]);

        $response = curl_exec($curl);

        $response = json_decode($response);

        if (isset($response->error->message)) {
            $this->curl_errors_array[] = ['siteUrl' => $pageUrl, 'error' => $response->error->message, 'type' => 'url_inspections'];

            activity('v3_url_inspections')->log($response->error->message);
        }

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        LogRequest::log($startTime, $url, 'POST', json_encode($params), json_decode($response), $httpcode, GoogleWebMasterController::class, 'googleResultForPageInspections');

        if (isset($error_msg)) {
            $this->curl_errors_array[] = ['siteUrl' => $pageUrl, 'error' => $error_msg, 'type' => 'url_inspections'];

            activity('v3_url_inspections')->log($error_msg);
        }

        return $response;
    }

    public function googleResultForAnaylist($siteUrl, $params)
    {
        $url = 'https://www.googleapis.com/webmasters/v3/sites/'.urlencode($siteUrl).'/searchAnalytics/query';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'authorization: Bearer '.$this->googleToken,
                'Content-Type:application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = json_decode($response); // response decode

        if (isset($response->error->message)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $response->error->message, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($response->error->message);
        }

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        LogRequest::log($startTime, $url, 'GET', json_encode($params), $response, $httpcode, GoogleWebMasterController::class, 'googleResultForAnaylist');

        if (isset($error_msg)) {
            $this->curl_errors_array[] = ['siteUrl' => $siteUrl, 'error' => $error_msg, 'type' => 'search_analytics'];

            activity('v3_search_analytics')->log($error_msg);
        }

        return $response;
    }

    /**
     * Show the site submit history.
     */
    public function getSiteSubmitHitory(): JsonResponse
    {
        $history = WebsiteStoreViewsWebmasterHistory::orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $history]);
    }

    /**
     * Submit site to webmaster.
     */
    public function SubmitSiteToWebmaster(Request $request): RedirectResponse
    {
        $fetchStores = WebsiteStoreView::whereNotNull('website_store_id')
            ->whereNotIn('site_submit_webmaster', [1])
            ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
            ->join('websites as w', 'w.id', 'ws.website_id')
            ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
            ->select('website_store_views.code', 'website_store_views.id', 'sw.website')
            ->get()->toArray();
        $google_acc = GoogleClientAccountMail::latest()->first();
        $params = [];
        $token = $google_acc->GOOGLE_CLIENT_ACCESS_TOKEN;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        foreach ($fetchStores as $value) {
            $websiter = urlencode(utf8_encode($value['website'].'/'.$value['code']));
            $url_for_sites = 'https://searchconsole.googleapis.com/webmasters/v3/sites/'.$websiter;
            $curl = curl_init();
            //replace website name with code coming form site list
            curl_setopt_array($curl, [
                CURLOPT_URL => $url_for_sites,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-length: 0',
                    'authorization: Bearer '.$token,
                ],
            ]);
            $response = curl_exec($curl);
            $response = json_decode($response); // response deoced

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                Log::info('Request URL::'.$url_for_sites);
                Log::info('Request Token::'.$token);
                Log::error('Error Msg::'.$error_msg);
            }

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            LogRequest::log($startTime, $url_for_sites, 'PUT', json_encode($params), $response, $httpcode, GoogleWebMasterController::class, 'SubmitSiteToWebmaster');

            if (! empty($response)) {
                $history = [
                    'website_store_views_id' => $value['id'],
                    'log' => isset($response->error->message) ? $value['website'].'/'.$value['code'].' - '.$response->error->message : $value['website'].'/'.$value['code'].' - '.'Error',
                ];

                WebsiteStoreViewsWebmasterHistory::create($history);

                Log::info('Request URL::'.$url_for_sites);
                Log::info('Request Token::'.$token);
                Log::error('Error Msg::'.$response->error->message);
                WebmasterLog::create([
                    'user_name' => Auth::user()->name,
                    'name' => 'Submit Site',
                    'status' => 'Error',
                    'message' => $value['website'].'/'.$value['code'].' - '.$response->error->message,
                ]);
            } else {
                WebmasterLog::create([
                    'user_name' => Auth::user()->name,
                    'name' => 'Submit Site',
                    'status' => 'Success',
                    'message' => 'Site submit successfully Website is '.$value['website'].'/'.$value['code'],
                ]);

                WebsiteStoreView::where('id', $value['id'])->update(['site_submit_webmaster' => 1]);
            }
        }

        return redirect()->route('googlewebmaster.index')->with('success', 'Sites submit successfully');
    }

    /**
     * Re-submit site to webmaster.
     */
    public function ReSubmitSiteToWebmaster(Request $request): JsonResponse
    {
        if (! empty($request->id)) {
            $fetchStores = WebsiteStoreView::whereNotNull('website_store_id')
                ->where('website_store_views.id', $request->id)
                ->join('website_stores as ws', 'ws.id', 'website_store_views.website_store_id')
                ->join('websites as w', 'w.id', 'ws.website_id')
                ->join('store_websites as sw', 'sw.id', 'w.store_website_id')
                ->select('website_store_views.code', 'website_store_views.id', 'sw.website')
                ->first();

            if ($fetchStores) {
                $websiter = urlencode(utf8_encode($fetchStores->website.'/'.$fetchStores->code));
                $url_for_sites = 'https://searchconsole.googleapis.com/webmasters/v3/sites/'.$websiter;

                $google_acc = GoogleClientAccountMail::latest()->first();
                $token = $google_acc->GOOGLE_CLIENT_ACCESS_TOKEN;
                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                $parameters = [];
                if ($token) {
                    $curl = curl_init();
                    //replace website name with code coming form site list
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url_for_sites,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'PUT',
                        CURLOPT_HTTPHEADER => [
                            'Accept: application/json',
                            'Content-length: 0',
                            'authorization: Bearer '.$token,
                        ],
                    ]);
                    $response = curl_exec($curl);
                    $response = json_decode($response);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    LogRequest::log($startTime, $url_for_sites, 'PUT', json_encode($parameters), $response, $httpcode, GoogleWebMasterController::class, 'ReSubmitSiteToWebmaster');

                    if (! empty($response)) {
                        $history = [
                            'website_store_views_id' => $fetchStores->id,
                            'log' => isset($response->error->message) ? $response->error->message : 'Error',
                        ];
                        WebsiteStoreViewsWebmasterHistory::create($history);

                        return response()->json(['code' => 400, 'message' => $response->error->message]);
                    } else {
                        WebsiteStoreView::where('id', $fetchStores->id)->update(['site_submit_webmaster' => 1]);

                        WebmasterLog::create([
                            'user_name' => Auth::user()->name,
                            'name' => 'Resubmit Site',
                            'status' => 'Success',
                            'message' => 'Site submit successfully',
                        ]);

                        return response()->json(['code' => 200, 'message' => 'Site submit successfully']);
                    }
                } else {
                    WebmasterLog::create([
                        'user_name' => Auth::user()->name,
                        'name' => 'Resubmit Site',
                        'status' => 'Error',
                        'message' => 'GOOGLE_CLIENT_ACCESS_TOKEN not found.',
                    ]);

                    return response()->json(['code' => 400, 'message' => 'GOOGLE_CLIENT_ACCESS_TOKEN not found']);
                }
            }

            return response()->json(['code' => 400, 'message' => 'No record found']);
        }
    }

    public function getAccounts(): JsonResponse
    {
        $GoogleClientAccounts = GoogleClientAccount::with('mails')->orderByDesc('id')->get();
        WebmasterLog::create([
            'user_name' => Auth::user()->name,
            'name' => 'Show Acount',
            'status' => 'Success',
            'message' => 'Total Account '.count($GoogleClientAccounts),
        ]);

        return response()->json(['code' => 200, 'data' => $GoogleClientAccounts]);
    }

    public function getAccountNotifications(): JsonResponse
    {
        $notifications = GoogleClientNotification::with('user', 'account')->orderByDesc('id')->get();

        WebmasterLog::create([
            'user_name' => Auth::user()->name,
            'name' => 'Show Notifications',
            'status' => 'Success',
            'message' => 'Total Account '.count($notifications),
        ]);

        return response()->json(['code' => 200, 'data' => $notifications]);
    }

    public function addAccount(Request $request): RedirectResponse
    {
        GoogleClientAccount::create($request->all());

        WebmasterLog::create([
            'user_name' => Auth::user()->name,
            'name' => 'Add Acount',
            'status' => 'Success',
            'message' => json_encode($request->all()),
        ]);

        return redirect()->route('googlewebmaster.index')->with('success', 'google client account added successfully!');
    }

    public function allRecords(Request $request): RedirectResponse
    {
        $GoogleClientAccounts = GoogleClientAccount::get();
        foreach ($GoogleClientAccounts as $GoogleClientAccount) {
            $refreshToken = GoogleClientAccountMail::where('google_client_account_id', $GoogleClientAccount->id)->first();

            if (! $refreshToken || $refreshToken['GOOGLE_CLIENT_REFRESH_TOKEN'] == null) {
                continue;
            }

            $this->client = new \Google_Client;
            $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
            $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
            $this->client->refreshToken($refreshToken->GOOGLE_CLIENT_REFRESH_TOKEN);

            $token = $this->client->getAccessToken();
            $request->session()->put('token', $token);
            $request->session()->put('GOOGLE_CLIENT_MULTIPLE_KEYS', $GoogleClientAccount->GOOGLE_CLIENT_MULTIPLE_KEYS);
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            if (empty($token)) {
                continue;
            }

            if ($this->client->getAccessToken()) {
                $this->updateSitesData($request);
                $curl = curl_init();
                $url = 'https://www.googleapis.com/webmasters/v3/sites/';
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
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, GoogleWebMasterController::class, 'allRecords');
                $err = curl_error($curl);

                if (curl_errno($curl)) {
                    $error_msg = curl_error($curl);
                }

                if (isset($error_msg)) {
                    $this->curl_errors_array[] = ['key' => 'sites', 'error' => $error_msg, 'type' => 'sites'];
                    activity('v3_sites')->log($error_msg);
                }

                $check_error_response = json_decode($response);

                curl_close($curl);

                if (isset($check_error_response->error->message) || $err) {
                    $this->curl_errors_array[] = ['key' => 'sites', 'error' => $check_error_response->error->message, 'type' => 'sites'];
                    activity('v3_sites')->log($check_error_response->error->message);
                    echo $this->curl_errors_array[0]['error'];

                    WebmasterLog::create([
                        'user_name' => Auth::user()->name,
                        'name' => 'Refresh Record',
                        'status' => 'Error',
                        'message' => $this->curl_errors_array[0]['error'],
                    ]);
                } else {
                    if (is_array(json_decode($response)->siteEntry)) {
                        foreach (json_decode($response)->siteEntry as $site) {
                            // Create ot update site url
                            GoogleWebMasters::updateOrCreate(['sites' => $site->siteUrl]);

                            echo 'https://www.googleapis.com/webmasters/v3/sites/'.urlencode($site->siteUrl).'/sitemaps';
                            $curl1 = curl_init();
                            $url = "https://www.googleapis.com/webmasters/v3/sites/' . urlencode($site->siteUrl) . '/sitemaps";
                            //replace website name with code coming form site list

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
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            LogRequest::log($startTime, $url, 'GET', json_encode($parameters), json_decode($response1), $httpcode, GoogleWebMasterController::class, 'allRecords');
                            $err = curl_error($curl1);

                            if ($err) {
                                activity('v3_sites')->log($err);
                                echo 'cURL Error #:'.$err;

                                WebmasterLog::create([
                                    'user_name' => Auth::user()->name,
                                    'name' => 'Refresh Record',
                                    'status' => 'Error',
                                    'message' => $this->curl_errors_array[0]['error'],
                                ]);
                            } else {
                                if (isset(json_decode($response1)->sitemap) && is_array(json_decode($response1)->sitemap)) {
                                    foreach (json_decode($response1)->sitemap as $key => $sitemap) {
                                        GoogleWebMasters::where('sites', $site->siteUrl)->update(['crawls' => $sitemap->errors]);
                                    }
                                }

                                WebmasterLog::create([
                                    'user_name' => Auth::user()->name,
                                    'name' => 'Refresh Record',
                                    'status' => 'Success',
                                    'message' => $response1,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('googlewebmaster.index');
    }

    public function connectAccount(Request $request, $id): RedirectResponse
    {
        $GoogleClientAccount = GoogleClientAccount::find($id);
        $google_redirect_url = route('googlewebmaster.get-access-token');
        Cache::forever('google_client_account_id', $id);
        $this->client = new \Google_Client;
        $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
        $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
        $this->client->setRedirectUri($google_redirect_url);
        $this->client->setAccessType('offline');
        $this->client->setScopes([
            'https://www.googleapis.com/auth/webmasters',
            'https://www.googleapis.com/auth/webmasters.readonly',
        ]);
        $this->client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        $authUrl = $this->client->createAuthUrl();
        if ($authUrl) {

            WebmasterLog::create([
                'user_name' => Auth::user()->name,
                'name' => 'Connect Account',
                'status' => 'Success',
                'message' => $authUrl,
            ]);
        } else {
            WebmasterLog::create([
                'user_name' => Auth::user()->name,
                'name' => 'Connect Account',
                'status' => 'Error',
                'message' => json_encode($this->client),
            ]);
        }
    }

    public function disconnectAccount(Request $request, $id): RedirectResponse
    {
        $mail_acc = GoogleClientAccountMail::find($id);
        $GoogleClientAccount = GoogleClientAccount::find($mail_acc->google_client_account_id);
        $this->client = new \Google_Client;
        $this->client->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
        $this->client->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
        $this->client->refreshToken($mail_acc->GOOGLE_CLIENT_REFRESH_TOKEN);
        $access_token = $this->client->getAccessToken();

        if ($access_token) {
            $this->client->revokeToken($access_token['access_token']);
        }
        WebmasterLog::create([
            'user_name' => Auth::user()->name,
            'name' => 'Disconnect Account',
            'status' => 'Success',
            'message' => $access_token || 'Already Revoked',
        ]);

        $mail_acc->delete();

        return redirect()->route('googlewebmaster.index')->with('success', 'Account disconnected successfully!');
    }

    /*
     * This functions deletes the sites from google webmaster
     * */
    public function deleteSiteFromWebmaster(Request $request): JsonResponse
    {
        if (! empty($request->id)) {
            $delete = false;
            $site = GoogleWebMasters::find($request->id);
            if ($site) {
                $websiter = urlencode(utf8_encode($site->sites));
                $url_for_sites = 'https://searchconsole.googleapis.com/webmasters/v3/sites/'.$websiter;

                $google_acc = GoogleClientAccountMail::with('google_client_account')->get();

                foreach ($google_acc as $google_ac) {
                    if ($google_ac['GOOGLE_CLIENT_REFRESH_TOKEN'] == null) {
                        continue;
                    }
                    $this->client = new \Google_Client;
                    $this->client->setClientId($google_ac->google_client_account->GOOGLE_CLIENT_ID);
                    $this->client->setClientSecret($google_ac->google_client_account->GOOGLE_CLIENT_SECRET);
                    $this->client->refreshToken($google_ac->GOOGLE_CLIENT_REFRESH_TOKEN);

                    $token = $this->client->getAccessToken();
                    $request->session()->put('token', $token);
                    if ($token) {
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $curl = curl_init();
                        //replace website name with code coming form site list
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url_for_sites,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'DELETE',
                            CURLOPT_HTTPHEADER => [
                                'Accept: application/json',
                                'Content-length: 0',
                                'authorization: Bearer '.$token['access_token'],
                            ],
                        ]);
                        $response = curl_exec($curl);
                        $response = json_decode($response); //response deocde

                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);
                        LogRequest::log($startTime, $url_for_sites, 'DELETE', json_encode([]), $response, $httpcode, GoogleWebMasterController::class, 'deleteSiteFromWebmaster');

                        if (! empty($response)) {
                            WebmasterLog::create([
                                'user_name' => Auth::user()->name,
                                'name' => 'Delete Site',
                                'status' => 'Error',
                                'message' => isset($response->error->message) ? $response->error->message : 'Error',
                            ]);

                            return response()->json(['code' => 400, 'message' => 'Invalid Token']);
                        } else {
                            $delete = true;
                            break;
                        }
                    } else {
                        WebmasterLog::create([
                            'user_name' => Auth::user()->name,
                            'name' => 'Delete Site',
                            'status' => 'Error',
                            'message' => 'GOOGLE_CLIENT_ACCESS_TOKEN not found.',
                        ]);

                        return response()->json(['code' => 400, 'message' => 'GOOGLE_CLIENT_ACCESS_TOKEN not found']);
                    }
                }
                if ($delete) {
                    $site->delete();
                    WebmasterLog::create([
                        'user_name' => Auth::user()->name,
                        'name' => 'Delete Site',
                        'status' => 'Success',
                        'message' => 'Site Deleted Successfully',
                    ]);

                    return response()->json(['code' => 200, 'message' => 'Site Deleted successfully']);
                }
            } else {
                WebmasterLog::create([
                    'user_name' => Auth::user()->name,
                    'name' => 'Delete Site',
                    'status' => 'Success',
                    'message' => 'No Data Found',
                ]);

                return response()->json(['code' => 400, 'message' => 'No record found']);
            }
        }
    }
}
