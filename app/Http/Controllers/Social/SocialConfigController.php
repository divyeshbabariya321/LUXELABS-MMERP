<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\AdStoreSocialConfigRequest;
use App\Http\Requests\SocialConfig\EditRequest;
use App\Http\Requests\SocialConfig\StoreRequest;
use App\Language;
use App\Models\DataTableColumn;
use App\Models\SocialAdAccount;
use App\Setting;
use App\Social\SocialConfig;
use App\StoreWebsite;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class SocialConfigController extends Controller
{
    protected string $fb_base_url;

    public function __construct()
    {
        $this->fb_base_url = 'https://graph.facebook.com/'.config('facebook.config.default_graph_version').'/';
    }

    /**
     * Social config page results
     *
     * @return array|Application|Factory|View|JsonResponse
     */
    public function index(Request $request)
    {
        $query = SocialConfig::with('storeWebsite'); // Eager load the related storeWebsite to avoid N+1 queries

        if ($this->shouldApplyBasicFilter($request)) {
            // No additional conditions are applied
        } else {
            // Apply filters based on the request
            $this->applyAdvancedFilters($query, $request);
        }

        $socialConfigs = $query->orderByDesc('id')->paginate(Setting::get('pagination'));

        if (! $request->ajax()) {
            $additionalData = $this->getAdditionalData($request);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.configs.partials.data', compact('socialConfigs'))->render(),
                'links' => (string) $socialConfigs->links(),
            ]);
        }

        $datatableModel = DataTableColumn::select('column_name')
            ->where('user_id', auth()->user()->id)
            ->where('section_name', 'development-section-social-config')
            ->first();

        $dynamicColumnsToShow = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShow = json_decode($hideColumns, true);
        }

        return view('social.configs.index', array_merge(compact('socialConfigs', 'dynamicColumnsToShow'), $additionalData ?? []));
    }

    protected function shouldApplyBasicFilter(Request $request)
    {
        return $request->number || $request->username || $request->provider ||
            ($request->customer_support || $request->term || $request->date) &&
            $request->customer_support == 0;
    }

    protected function applyAdvancedFilters($query, Request $request)
    {
        if ($request->store_website_id) {
            $query->whereIn('store_website_id', $request->store_website_id);
        }

        if ($request->user_name) {
            $query->whereIn('user_name', $request->user_name);
        }

        if ($request->platform) {
            $query->whereIn('platform', $request->platform);
        }

        if ($request->ad_account) {
            $query->whereIn('ad_account_id', $request->ad_account);
        }
    }

    /**
     * Data that is sent to the index blade on all the conditions
     */
    protected function getAdditionalData(Request $request): array
    {
        return [
            'websites' => StoreWebsite::select('id', 'title')->get(),
            'user_names' => SocialConfig::select('user_name')->distinct()->get(),
            'platforms' => SocialConfig::select('platform')->distinct()->get(),
            'ad_accounts' => SocialAdAccount::where('status', 1)->get()->toArray(),
            'languages' => Language::get(),
            'selected_website' => $request->store_website_id,
            'selected_user_name' => $request->user_name,
            'selected_platform' => $request->platform,
            'selected_ad_account' => $request->ad_account,
        ];
    }

    public function adStore(AdStoreSocialConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $url = $this->fb_base_url.'oauth/access_token?grant_type=fb_exchange_token&client_id='.$request['api_key']
            .'&client_secret='.$request['api_secret'].'&fb_exchange_token='.$request['page_token'];
        $http = Http::get($url);
        $response = $http->json();

        // To fix encrypted api secret key store errorm and to add api_key to ad_account DEVTASK-24765
        $data['page_token'] = $response['access_token'];

        SocialAdAccount::create($data);

        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    public function getNeverExpiringToken(array $data): string|bool
    {
        $url = $this->fb_base_url.'oauth/access_token?grant_type=fb_exchange_token&client_id='.$data['api_key']
            .'&client_secret='.$data['api_secret'].'&fb_exchange_token='.$data['page_token'];
        $http = Http::get($url);
        $response = $http->json();
        if (isset($response['error'])) {
            return false;
        }
        $long_lived_token = $response['access_token'];
        $permanent_token_url = $this->fb_base_url.$data['page_id'].'?fields=access_token&access_token='.$long_lived_token;
        $httpPT = Http::get($permanent_token_url);
        $ptResponse = $httpPT->json();
        if (! isset($ptResponse['access_token'])) {
            return $long_lived_token;
        }

        return $ptResponse['access_token'];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['page_language'] = $request->page_language;

        // $neverExpiringToken    = $this->getNeverExpiringToken($data);
        // if (! $neverExpiringToken) {
        //     return redirect()->back()->withError('Unable to refactor the token. Kindly validate it');
        // }
        // $data['page_token'] = $neverExpiringToken;

        SocialConfig::create($data);

        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    /**
     * To get the Facebook User Id- Account ID
     *
     * @return int
     */
    public function getAccountID($token)
    {
        $client = new Client;
        try {
            $response = $client->get('https://graph.facebook.com/v19.0/me', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);
            $body = $response->getBody()->getContents();
            $responseData = json_decode($body, true);
            return $responseData['id'];           
        
        } catch (Exception $e) {

            $errorMessage = $e->getMessage();

            return redirect()->back()->withError($errorMessage);
        }
    }

    /**
     * To get the associated Account IDs/Page IDs to account
     *
     * @return array
     */
    public function getPageIDs($token)
    {
        $client = new Client;
        try {
            $response = $client->get('https://graph.facebook.com/v19.0/me/accounts', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $responseData = json_decode($body, true);
            $data = $responseData['data'];
            $pageIds = [];
            foreach ($data as $pageData) {
                $pageIds[] = $pageData['id'];
            }
           return $pageIds;
        
        } catch (Exception $e) {

            $errorMessage = $e->getMessage();

            return redirect()->back()->withError($errorMessage);
        }
    }

    /**
     * To set edit page view and handle callback from fb.
     *
     * @return array
     */
    public function update(Request $request, $configId)
    {
        $queryParameters = $request->query();
        $config = SocialConfig::findorfail($configId);
        $redirect_url = url()->current();

        if (isset($queryParameters['access_token'])) {
            $account_id = $this->getAccountID($queryParameters['access_token']);
            $pageIdArray = $this->getPageIDs($queryParameters['access_token']);
            $pageids = implode(',', $pageIdArray);
            $data = [];
            $data['id'] = $configId;
            $data['page_id'] = $pageids;
            $data['account_id'] = $account_id;
            $data['status'] = 1;
            $data['page_token'] = $queryParameters['long_lived_token'];
            $config->update($data);

            return redirect()->to($redirect_url)->withSuccess('You have successfully connected to facebook.');
        }

        if (! $request->ajax()) {
            $additionalData = $this->getAdditionalData($request);
        }
        $socialConfig = SocialConfig::where('id', $configId)->first();

        return view('social.configs.edit', compact('socialConfig', 'redirect_url'), $additionalData ?? []);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditRequest $request): RedirectResponse
    {
        $config = SocialConfig::findorfail($request->id);
        $data = $request->validated();

        $neverExpiringToken = $this->getNeverExpiringToken($data);
        if (! $neverExpiringToken) {
            return redirect()->back()->withError('Unable to refactor the token. Kindly validate it');
        }

        if (isset($request->adsmanager)) {
            $data['ads_manager'] = $request->adsmanager;
        }
        $data['page_language'] = $request->page_language;
        $data['page_token'] = $neverExpiringToken;

        $config->update($data);

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): Response
    {
        $config = SocialConfig::findorfail($request->id);
        $config->delete();

        return response()->json(['message' => 'Config Deleted']);
    }
}
