<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Setting;
use App\BingSite;
use App\LogRequest;
use App\BingWebmasterLog;
use App\BingClientAccount;
use App\BingSearchAnalytics;
use Illuminate\Http\Request;
use App\BingClientAccountMail;
use Spatie\Activitylog\Models\Activity;

class BingWebMasterController extends Controller
{
    private $bingToken;

    public function index(Request $request): View
    {
        $getSites       = BingSite::paginate(Setting::get('pagination'), ['*'], 'crawls_per_page');
        $sites          = BingSite::select('id', 'site_url')->get();
        $logs           = Activity::where('log_name', 'bing_sites')->orWhere('log_name', 'bing_search_analytics')->latest()->paginate(Setting::get('pagination'), ['*'], 'logs_per_page');
        $webmaster_logs = BingWebmasterLog::latest()->paginate(Setting::get('pagination'), ['*'], 'webmaster_logs_per_page');

        $SearchAnalytics = new BingSearchAnalytics;
        $SearchAnalytics = $SearchAnalytics->orderByDesc('id');
        if ($request->site) {
            $SearchAnalytics = $SearchAnalytics->where('site_id', $request->site);
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
        $sitesData = $SearchAnalytics->paginate(Setting::get('pagination'));

        return view('bing-web-master/index', compact('getSites', 'sitesData', 'sites', 'request', 'logs', 'webmaster_logs'));
    }

    /*
     * This functions adds the client accounts for bing webmaster
     * */
    public function addAccount(Request $request): RedirectResponse
    {
        BingClientAccount::create($request->all());
        $this->createLog(json_encode($request->all()), 'Add Account');

        return redirect()->route('bingwebmaster.index')->with('success', 'Bing client account added successfully!');
    }

    /*
     * This functions gets the client accounts
     * */
    public function getAccounts(): JsonResponse
    {
        $bing_client_accounts = BingClientAccount::with('mails')->orderByDesc('id')->get();
        $this->createLog('Total Account ' . count($bing_client_accounts), 'Show Account');

        return response()->json(['code' => 200, 'data' => $bing_client_accounts]);
    }

    /*
     * This helper functions to create logs for bing error / success
     * */
    public function createLog($message, $name = 'Bing Log', $status = 'Success')
    {
        BingWebmasterLog::create([
            'user_name' => Auth::user()->name,
            'name'      => $name,
            'status'    => $status,
            'message'   => $message,
        ]);
    }

    /*
     * This functions Generates a authorization URL to get the access token to pull data from bing
     * */
    public function connectAccount(Request $request, $id): RedirectResponse
    {
        $bing_client_account = BingClientAccount::find($id);
        $bing_redirect_url   = route('bingwebmaster.get-access-token');
        Cache::forever('bing_client_account_id', $id);
        $authUrl = "https://www.bing.com/webmasters/OAuth/authorize?response_type=code&client_id=$bing_client_account->bing_client_id&redirect_uri=$bing_redirect_url&scope=webmaster.manage";
        $this->createLog($authUrl, 'Attempt To Connect Account');

        return redirect()->to($authUrl);
    }

    /*
     * This functions deletes the connected account for the bing webmaster.
     * */
    public function disconnectAccount(Request $request, $id): RedirectResponse
    {
        $mail_acc = BingClientAccountMail::find($id);
        $this->createLog($mail_acc->bing_client_access_token || 'Already Revoked', 'Disconnect Account');
        $mail_acc->delete();

        return redirect()->route('bingwebmaster.index')->with('success', 'Account disconnected successfully!');
    }

    /*
     * This functions is the callback url for the bing authorization url which generates the access token.
     * */
    public function bingLogin(Request $request): RedirectResponse
    {
        $bing_redirect_url   = route('bingwebmaster.get-access-token');
        $id                  = Cache::get('bing_client_account_id');
        $bing_client_account = BingClientAccount::find($id);
        $params              = 'code=' . $request->get('code') . "&client_id=$bing_client_account->bing_client_id&client_secret=$bing_client_account->bing_client_secret&redirect_uri=$bing_redirect_url&grant_type=authorization_code";
        $response            = $this->getAccessTokenFromBing($params);
        if (! isset($response->access_token)) {
            $this->createLog(json_encode($response), 'Failed To Connect Account');

            return redirect()->route('bingwebmaster.index')->with('Error', 'Failed to connect account!');
        }
        BingClientAccountMail::where('bing_client_account_id', $id)->delete();
        $mail_acc                           = new BingClientAccountMail();
        $mail_acc->bing_account             = 'Bing Account User';
        $mail_acc->bing_client_account_id   = $id;
        $mail_acc->bing_client_access_token = $response->access_token;
        if (! empty($response->refresh_token)) {
            $mail_acc->bing_client_refresh_token = $response->refresh_token;
        }
        $mail_acc->expires_in = $response->expires_in;
        $mail_acc->save();

        return redirect()->route('bingwebmaster.index')->with('success', 'Account connected successfully!');
    }

    /*
     * This functions fetches all the bing website records, Matrix, Crawls & indexes for the site and stores into the DB.
     * */
    public function allRecords(Request $request): RedirectResponse
    {
        $bing_client_accounts = BingClientAccount::get();
        foreach ($bing_client_accounts as $bing_client_account) {
            $refresh_token = BingClientAccountMail::where('bing_client_account_id', $bing_client_account->id)->first();

            if (! $refresh_token || $refresh_token['bing_client_refresh_token'] == null) {
                continue;
            }
            $this->bingToken = $this->getAccessToken($refresh_token->id);
            if (empty($this->bingToken)) {
                continue;
            }
            $sites = $this->getDataFromBing('GetUserSites');
            if (count($sites->d) > 0) {
                foreach ($sites->d as $site) {
                    $site_role = $this->getDataFromBing("GetSiteRoles?siteUrl=$site->Url");
                    if ($siteRow = BingSite::whereSiteUrl($site->Url)->first()) {
                        $siteRow->role = config('constants.bing_site_role_enum')[$site_role->d[0]->Role];
                        $siteRow->save();
                    } else {
                        BingSite::create(['site_url' => $site->Url, 'role' => config('constants.bing_site_role_enum')[$site_role->d[0]->Role]]);
                    }
                }
                $this->updateSites($sites->d);
            }

            return redirect()->route('bingwebmaster.index')->with('success', 'Records Updated successfully!');
        }
    }

    public function updateSites($sites)
    {
        foreach ($sites as $site) {
            $siteData  = BingSite::whereSiteUrl($site->Url)->first();
            $site_date = $this->getDataFromBing("GetQueryStats?siteUrl=$site->Url");
            $matrix    = isset($site_date->d[0]) ? $site_date->d[0] : null;
            $record    = [
                'clicks'     => $matrix ? $matrix->Clicks : 0,
                'impression' => $matrix ? $matrix->Impressions : 0,
                'site_id'    => $siteData->id,
                'ctr'        => 0,
                'position'   => $matrix ? $matrix->AvgImpressionPosition : 0,
                'query'      => $matrix ? $matrix->Query : '',
                'page'       => $site->Url,
                'date'       => $matrix ? date('Y-m-d', strtotime($matrix->Date)) : date('Y-m-d'),
            ];

            $crawl_stats              = $this->getDataFromBing("GetCrawlStats?siteUrl=$site->Url");
            $crawl_stats_matrix       = isset($crawl_stats->d[0]) ? $crawl_stats->d[0] : null;
            $record['crawl_requests'] = $crawl_stats_matrix ? $crawl_stats_matrix->CrawledPages : 0;
            $record['crawl_errors']   = $crawl_stats_matrix ? $crawl_stats_matrix->CrawlErrors : 0;
            $record['index_pages']    = $crawl_stats_matrix ? $crawl_stats_matrix->InIndex : 0;

            $query = parse_url($site->Url)['host'];
            if ($record['query']) {
                $query = $record['query'];
            }
            $keyword_stats        = $this->getDataFromBing('GetKeywordStats?q=' . $query);
            $keyword_stats_matrix = isset($keyword_stats->d[0]) ? $keyword_stats->d[0] : null;
            $record['keywords']   = $keyword_stats_matrix ? $keyword_stats_matrix->Query : '';

            $page_stats        = $this->getDataFromBing("GetLinkCounts?siteUrl=$site->Url");
            $page_stats_matrix = isset($page_stats->d) ? $page_stats->d : null;
            $record['pages']   = 0;
            if (isset($page_stats_matrix)) {
                foreach ($page_stats_matrix->Links as $link) {
                    $record['pages'] = $record['pages'] + $link->Count;
                }
            }

            if ($record['impression'] != 0) {
                $record['ctr'] = ($record['clicks'] / $record['impression']) * 100;
            }

            $rowData = new BingSearchAnalytics;

            foreach ($record as $col => $val) {
                $rowData = $rowData->where($col, $val);
            }
            $record['crawl_information'] = $crawl_stats_matrix ? json_encode($crawl_stats_matrix) : 0;
            if (! $rowData->first()) {
                BingSearchAnalytics::create($record);
            }
        }
    }

    public function getAccessToken($id)
    {
        $account = BingClientAccountMail::with('bing_client_account')->where('id', $id)->first();
        $params  = [
            'client_id'     => $account->bing_client_account->bing_client_id,
            'client_secret' => $account->bing_client_account->bing_client_secret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $account->bing_client_refresh_token,
        ];
        $params    = 'client_id=' . $params['client_id'] . '&client_secret=' . $params['client_secret'] . '&grant_type=' . $params['grant_type'] . '&refresh_token=' . $params['refresh_token'];
        $url       = 'https://www.bing.com/webmasters/oauth/token';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl      = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($params), json_decode($response), $httpcode, BingWebMasterController::class, 'BinggetAccessToken');
        curl_close($curl);

        if (isset($error_msg)) {
            activity('bing_sites')->log($error_msg);
        }

        if (isset($response->error->message)) {
            activity('bing_sites')->log($response->error->message);
        }
        if (! isset($response->access_token)) {
            $this->createLog(json_encode($response), 'Failed to Fetch the access token from refresh token');

            return null;
        }
        $account->bing_client_access_token  = $response->access_token;
        $account->expires_in                = $response->expires_in;
        $account->bing_client_refresh_token = $response->refresh_token;
        $account->save();

        return $response->access_token;
    }

    /*
     * This functions deletes the sites from bing webmaster
     * */
    public function deleteSiteFromWebmaster(Request $request): JsonResponse
    {
        $delete = false;
        $site   = BingSite::find($request->id);
        if ($site) {
            $bing_acc = BingClientAccountMail::with('bing_client_account')->get();
            foreach ($bing_acc as $bing_ac) {
                if ($bing_ac['bing_client_refresh_token'] == null) {
                    continue;
                }
                $this->bingToken = $this->getAccessToken($bing_ac->id);
                if (! empty($this->bingToken)) {
                    $response = $this->getDataFromBing('RemoveSite', ['siteUrl' => $site->site_url], 'POST');
                    if (isset($response->d) && $response->d == null) {
                        $delete = true;
                        break;
                    } else {
                        $this->createLog(isset($response->error->message) ? $response->error->message : 'Error', 'Delete Site', 'Error');

                        return response()->json(['code' => 400, 'message' => 'Bing Access token not found']);
                    }
                } else {
                    $this->createLog('Bing Access token not found', 'Delete Site', 'Error');

                    return response()->json(['code' => 400, 'message' => 'Bing Access token not found']);
                }
            }
            if ($delete) {
                $site->delete();
                $this->createLog('Site Deleted Successfully', 'Delete Site');

                return response()->json(['code' => 200, 'message' => 'Site Deleted successfully']);
            }
        } else {
            $this->createLog('No Data Found', 'Delete Site');

            return response()->json(['code' => 400, 'message' => 'No record found']);
        }
    }

    /*
     * Helper curl function to get data from bing
     * */
    private function getAccessTokenFromBing($params = [])
    {
        $url       = 'https://www.bing.com/webmasters/oauth/token';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl      = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response); //response decoded

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($params), $response, $httpcode, BingWebMasterController::class, 'BinggetAccessTokenFromBing');

        if (isset($error_msg)) {
            activity('bing_sites')->log($error_msg);
        }

        if (isset($response->error->message)) {
            activity('bing_sites')->log($response->error->message);
        }

        return $response;
    }

    /*
     * Helper curl function to get data from bing
     * */
    private function getDataFromBing($method, $params = [], $request_type = 'GET')
    {
        $url       = 'https://ssl.bing.com/webmaster/api.svc/json/' . $method;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $curl = curl_init();
        //replace website name with code coming form site list
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_CUSTOMREQUEST  => $request_type,
            CURLOPT_HTTPHEADER     => [
                'authorization: Bearer ' . $this->bingToken,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = json_decode($response); //response decoded

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);
        LogRequest::log($startTime, $url, $request_type, json_encode($params), $response, $httpcode, BingWebMasterController::class, 'BinggetDataFromBing');

        if (isset($error_msg)) {
            activity('bing_sites')->log($error_msg);
        }

        if (isset($response->error->message)) {
            activity('bing_sites')->log($response->error->message);
        }

        return $response;
    }
}
