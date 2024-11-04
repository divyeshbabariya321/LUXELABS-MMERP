<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\AssetsManager;
use App\BuildProcessHistory;
use App\ChatMessage;
use App\DownloadDatabaseEnvLogs;
use App\Github\GithubRepository;
use App\Jobs\DuplicateStoreWebsiteJob;
use App\Jobs\MagentoMediaSyncJob;
use App\Language;
use App\LogStoreWebsiteUser;
use App\MagentoDevScripUpdateLog;
use App\MagentoSettingUpdateResponseLog;
use App\Models\GoogleTranslateCsvData;
use App\Models\MagentoMediaSync;
use App\Models\StoreWebsiteAdminUrl;
use App\Models\StoreWebsiteApiTokenHistory;
use App\Models\StoreWebsiteBuilderApiKeyHistory;
use App\Models\StoreWebsiteCsvFile;
use App\Models\StoreWebsiteCsvPullHistory;
use App\Models\VarnishStats;
use App\Models\VarnishStatsLogs;
use App\Models\WebsiteStoreProject;
use App\Models\WebsiteStoreTag;
use App\ProductCancellationPolicie;
use App\Service;
use App\Services\CommonGoogleTranslateService;
use App\Services\KubernetesService;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\SocialStrategy;
use App\SocialStrategyRemark;
use App\SocialStrategySubject;
use App\StoreReIndexHistory;
use App\StoreViewCodeServerMap;
use App\StoreWebsite;
use App\StoreWebsiteAnalytic;
use App\StoreWebsiteAttributes;
use App\StoreWebsiteBrand;
use App\StoreWebsiteCategory;
use App\StoreWebsiteCategorySeo;
use App\StoreWebsiteColor;
use App\StoreWebsiteGoal;
use App\StoreWebsiteImage;
use App\StoreWebsitePage;
use App\StoreWebsiteProduct;
use App\StoreWebsiteProductAttribute;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteProductScreenshot;
use App\StoreWebsitesApiTokenLog;
use App\StoreWebsitesCountryShipping;
use App\StoreWebsiteSeoFormat;
use App\StoreWebsiteSize;
use App\StoreWebsiteTwilioNumber;
use App\StoreWebsiteUserHistory;
use App\StoreWebsiteUsers;
use App\Translations;
use App\User;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use seo2websites\MagentoHelper\MagentoHelperv2;

class StoreWebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WebsiteStoreTag $WebsiteStoreTag): View
    {
        $title = 'List | Store Website';
        $services = Service::get();

        $tags = $WebsiteStoreTag->get();

        $assetManager = AssetsManager::whereNotNull('ip');
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('website')->get();
        $storeCodes = StoreViewCodeServerMap::groupBy('server_id')->orderBy('server_id')->select('code', 'id', 'server_id')->get()->toArray();
        $projects = WebsiteStoreProject::orderBy('name')->get()->toArray();

        $storeWebsiteUsers = StoreWebsiteUsers::where('is_deleted', 0)->get();

        $podNames = [];
        $clusterFiles = '';
        $cloudPlateform = getCloudPlateform();

        return view('storewebsite::index', compact('title', 'services', 'assetManager', 'storeWebsites', 'storeCodes', 'tags', 'storeWebsiteUsers', 'projects', 'clusterFiles', 'podNames', 'cloudPlateform'));
    }

    public function builderApiKey(): View
    {
        $title = 'Builder Api Key | Store Website';
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('id')->get();

        return view('storewebsite::index-builder-api-key', compact('title', 'storeWebsites'));
    }

    public function updateBuilderApiKey(Request $request, $id): RedirectResponse
    {
        $website = StoreWebsite::findOrFail($id);
        $old = $website->builder_io_api_key;
        $website->builder_io_api_key = $request->input('builder_io_api_key');
        $website->save();

        // Maintain history in table
        if ($old != $request->input('builder_io_api_key')) {
            $history = new StoreWebsiteBuilderApiKeyHistory;
            $history->store_website_id = $website->id;
            $history->old = $old;
            $history->new = $request->input('builder_io_api_key');
            $history->updated_by = Auth::user()->id;
            $history->save();
        }

        return redirect()->back()->with('success', 'API key updated successfully');
    }

    public function builderApiKeyHistory($id): JsonResponse
    {
        $datas = StoreWebsiteBuilderApiKeyHistory::with(['user'])
            ->where('store_website_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getPodList(Request $request): JsonResponse
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }
        $projectId = (int) $request->input('project_id');
        $clusterFile = $this->getClusterFile($projectId);

        if ($clusterFile === null) {
            return response()->json([
                'status' => false,
                'data' => [
                    'podName' => [],
                    'clusterFile' => '',
                ],
                'message' => 'Sorry, No cluster file found for the selected project.',
                'status_name' => 'error',
            ], 422);
        }
        $kubernetesService = new KubernetesService($clusterFile);
        $podNames = $kubernetesService->getPodList();

        return response()->json([
            'status' => true,
            'data' => [
                'podName' => $podNames,
                'clusterFile' => $clusterFile,
            ],
            'message' => 'Data received',
            'status_name' => 'success',
        ], 200);
    }

    private function getClusterFile(int $projectId): ?string
    {
        $clusterFiles = [
            1 => 'magento-avoirchic-cluster-kubeconfig-cluster-1.yaml',
            2 => 'magento-avoirchic-cluster-kubeconfig-cluster-1.yaml',
            3 => 'magento-avoirchic-cluster-kubeconfig-cluster-2.yaml',
            4 => 'magento-avoirchic-cluster-kubeconfig-cluster-2.yaml',
            5 => 'magento-avoirchic-cluster-kubeconfig-cluster-2.yaml',
        ];

        return $clusterFiles[$projectId] ?? null;
    }

    public function apiToken(): View
    {
        $title = 'Api Token | Store Website';
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('id')->get();
        $storeWebsiteUsers = StoreWebsiteUsers::where('is_deleted', 0)->get();

        return view('storewebsite::index-api-token', compact('title', 'storeWebsites', 'storeWebsiteUsers'));
    }

    public function adminPassword(Request $request): View
    {
        $storeWebsiteUsers = StoreWebsiteUsers::where('is_deleted', 0);

        if ($keyword = request('storewebsiteid')) {
            $storeWebsiteUsers = $storeWebsiteUsers->where(function ($q) use ($keyword) {
                $q->whereIn('store_website_id', $keyword);
            });
        }

        $storeWebsiteUsers = $storeWebsiteUsers->get();

        $title = 'Admin Password | Store Website';
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('id')->get();
        //$storeWebsiteUsers = StoreWebsiteUsers::where('is_deleted', 0)->get();

        return view('storewebsite::index-admin-password', compact('title', 'storeWebsites', 'storeWebsiteUsers', 'request'));
    }

    public function adminUrls(Request $request): View
    {
        $title = 'Admin Urls | Store Website';
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->orderBy('id')->get();
        //$storeWebsiteAdminUrls = StoreWebsiteAdminUrl::with(['user'])->where('status', 1)->orderBy('id', 'DESC')->get();

        $storeWebsiteAdminUrls = StoreWebsiteAdminUrl::with(['user', 'storewebsite'])->where('status', 1)->orderByDesc('id');

        if ($keyword = request('searchstorewebsiteids')) {
            $storeWebsiteAdminUrls = $storeWebsiteAdminUrls->where(function ($q) use ($keyword) {
                $q->whereIn('store_website_admin_urls.store_website_id', $keyword);
            });
        }

        $storeWebsiteAdminUrls = $storeWebsiteAdminUrls->get();

        return view('storewebsite::index-admin-urls', compact('title', 'storeWebsites', 'storeWebsiteAdminUrls', 'request'));
    }

    public function getApiTokenLogs(Request $request): JsonResponse
    {
        $logs = StoreWebsitesApiTokenLog::with(['storeWebsite', 'StoreWebsiteUsers', 'user'])->where('store_website_id', $request->store_website_id)->orderByDesc('id')->get();
        //dd($logs);
        $data = '';
        if ($logs->isNotEmpty()) {
            foreach ($logs as $log) {
                $data .= '<tr>';
                $data .= '<td>';
                $data .= $log->id;
                $data .= '</td>';
                $data .= '<td>';
                if ($log->user) {
                    $data .= $log->user->name;
                }
                $data .= '</td>';
                $data .= '<td>';
                if ($log->storeWebsite) {
                    $data .= $log->storeWebsite->title;
                }
                $data .= '</td>';
                $data .= '<td>';
                if ($log->StoreWebsiteUsers) {
                    $data .= $log->StoreWebsiteUsers->first_name.' '.$log->StoreWebsiteUsers->last_name.' ('.$log->StoreWebsiteUsers->email.')';
                }
                $data .= '</td>';
                $data .= '<td>';
                $data .= $log->response;
                $data .= '</td>';
                $data .= '<td>';
                $data .= $log->status_code;
                $data .= '</td>';
                $data .= '<td>';
                $data .= $log->status;
                $data .= '</td>';
                $data .= '<td>';
                $data .= $log->created_at;
                $data .= '</td>';

                $data .= '</tr>';
            }
        } else {
            $data .= '<tr><td>No Data found!</td></tr>';
        }

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function testApiToken(Request $request): JsonResponse
    {
        if ($request->has('store_website_id') && $request->store_website_id == '') {
            return response()->json(['success' => false, 'message' => 'The request parameter store website is missing']);
        }
        $storeWebsite = StoreWebsite::where('id', $request->store_website_id)->first();
        if ($storeWebsite) {
            $magento_url = $storeWebsite->magento_url;
            $api_token = $storeWebsite->api_token;
            $storeWebsiteCode = $storeWebsite->storeCode;
            if (! empty($magento_url) && ! empty($storeWebsiteCode)) {
                $response = Http::withBody(json_encode([
                    'category' => [
                        'name' => 'Default Category',
                    ],
                ]), 'application/json')->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$api_token,
                ])->get(rtrim($magento_url, '/')."/{$storeWebsiteCode->code}/rest/V1/categories?fields=id,parent_id,name");

                if ($response->ok()) {
                    StoreWebsitesApiTokenLog::create([
                        'user_id' => Auth::id(),
                        'store_website_id' => $storeWebsite->id,
                        'response' => 'API Token Test:- '.$response->body(),
                        'status_code' => $response->status(),
                        'status' => 'Success',
                    ]);

                    return response()->json(['success' => true, 'message' => 'API Token Test Success!']);
                } else {
                    StoreWebsitesApiTokenLog::create([
                        'user_id' => Auth::id(),
                        'store_website_id' => $storeWebsite->id,
                        'response' => 'API Token Test:- '.$response->json('message'),
                        'status_code' => $response->status(),
                        'status' => 'Error',
                    ]);

                    return response()->json(['success' => false, 'message' => 'API Token Test failed. Please check logs for more details']);
                }
            }
            StoreWebsitesApiTokenLog::create([
                'user_id' => Auth::id(),
                'store_website_id' => $storeWebsite->id,
                'response' => 'The store website URL or Store Code is not found.',
                'status_code' => '404',
                'status' => 'Error',
            ]);

            return response()->json(['success' => false, 'message' => 'The store website URL  or Store Code is not found.']);
        }

        return response()->json(['success' => false, 'message' => 'The Store Website data not found']);
    }

    public function apiTokenGenerate(Request $request): JsonResponse
    {
        if ($request->has('store_website_id') && $request->store_website_id == '') {
            return response()->json(['success' => false, 'message' => 'The request parameter store_website_id is missing']);
        }
        if ($request->has('store_website_users_id') && $request->store_website_users_id == '') {
            return response()->json(['success' => false, 'message' => 'The request parameter store_website_users_id is missing']);
        }
        $storeWebsite = StoreWebsite::where('id', $request->store_website_id)->first();
        $oldWebsiteApi = $storeWebsite->api_token;
        $StoreWebsiteUser = StoreWebsiteUsers::where('id', $request->store_website_users_id)->first();
        if ($storeWebsite && $StoreWebsiteUser) {
            //            $storeWebsiteCode = $storeWebsite->storeCode;
            $magento_url = $storeWebsite->magento_url;
            if (! empty($magento_url)) {
                //            if (! empty($magento_url) && ! empty($storeWebsiteCode)) {
                //$url=$magento_url."/rest/V1/integration/admin/token";

                //                $token_response = Http::post(rtrim($magento_url, '/') . "/{$storeWebsiteCode->code}/rest/V1/integration/admin/token", [
                $token_response = Http::post(rtrim($magento_url, '/').'/rest/V1/integration/admin/token', [
                    'username' => $StoreWebsiteUser->username,
                    'password' => $StoreWebsiteUser->password,
                ]);

                if ($token_response->ok()) {
                    $generated_token = trim($token_response->body(), '"');
                    $storeWebsite->api_token = $generated_token;
                    $storeWebsite->save();

                    $storeWebsiteHistory = new StoreWebsiteApiTokenHistory;
                    $storeWebsiteHistory->store_websites_id = $storeWebsite->id;
                    $storeWebsiteHistory->old_api_token = $oldWebsiteApi;
                    $storeWebsiteHistory->new_api_token = $generated_token;
                    $storeWebsiteHistory->updatedBy = Auth::id();
                    $storeWebsiteHistory->save();

                    StoreWebsitesApiTokenLog::create([
                        'user_id' => Auth::id(),
                        'store_website_id' => $storeWebsite->id,
                        'store_website_users_id' => $StoreWebsiteUser->id,
                        'response' => 'API Token updated successfully!',
                        'status_code' => $token_response->status(),
                        'status' => 'Success',
                    ]);

                    return response()->json(['success' => true, 'message' => 'API Token updated successfully!', 'token' => $generated_token]);
                } else {
                    StoreWebsitesApiTokenLog::create([
                        'user_id' => Auth::id(),
                        'store_website_id' => $storeWebsite->id,
                        'store_website_users_id' => $StoreWebsiteUser->id,
                        'response' => $token_response->json('message'),
                        'status_code' => $token_response->status(),
                        'status' => 'Error',
                    ]);

                    return response()->json(['success' => false, 'message' => $token_response->json('message')]);
                }
            }
            StoreWebsitesApiTokenLog::create([
                'user_id' => Auth::id(),
                'store_website_id' => $storeWebsite->id,
                'store_website_users_id' => $StoreWebsiteUser->id,
                'response' => 'The store website URL or Store Code is not found.',
                'status_code' => '404',
                'status' => 'Error',
            ]);

            return response()->json(['success' => false, 'message' => 'The store website URL or Store Code is not found.']);
        }

        return response()->json(['success' => false, 'message' => 'The Store Website and User not found']);
    }

    public function apiTokenBulkGenerate(Request $request): JsonResponse
    {
        if ($request->has('ids') && empty($request->ids)) {
            return response()->json(['success' => false, 'message' => 'The request parameter ids missing']);
        }

        foreach ($request->ids as $storeWebsiteId) {
            $storeWebsite = StoreWebsite::where('id', $storeWebsiteId)->first();
            $oldWebsiteApi = $storeWebsite->api_token;
            $StoreWebsiteUser = StoreWebsiteUsers::where('store_website_id', $storeWebsiteId)->where('email', 'apiuser@theluxuryunlimited.com')->first();

            if ($storeWebsite && $StoreWebsiteUser) {
                //                $storeWebsiteCode = $storeWebsite->storeCode;
                $magento_url = $storeWebsite->magento_url;
                //                if (! empty($magento_url) && ! empty($storeWebsiteCode)) {
                if (! empty($magento_url)) {
                    $token_response = Http::post(rtrim($magento_url, '/').'/rest/V1/integration/admin/token', [
                        'username' => $StoreWebsiteUser->username,
                        'password' => $StoreWebsiteUser->password,
                    ]);

                    if ($token_response->ok()) {
                        $generated_token = trim($token_response->body(), '"');
                        $storeWebsite->api_token = $generated_token;
                        $storeWebsite->save();
                        $storeWebsiteHistory = new StoreWebsiteApiTokenHistory;
                        $storeWebsiteHistory->store_websites_id = $storeWebsite->id;
                        $storeWebsiteHistory->old_api_token = $oldWebsiteApi;
                        $storeWebsiteHistory->new_api_token = $generated_token;
                        $storeWebsiteHistory->updatedBy = Auth::id();
                        $storeWebsiteHistory->save();

                        StoreWebsitesApiTokenLog::create([
                            'user_id' => Auth::id(),
                            'store_website_id' => $storeWebsite->id,
                            'store_website_users_id' => $StoreWebsiteUser->id,
                            'response' => 'API Token updated successfully!',
                            'status_code' => $token_response->status(),
                            'status' => 'Success',
                        ]);
                        // return response()->json(['success' => true, 'message' =>'API Token updated successfully!','token'=>$generated_token ]);
                    } else {
                        StoreWebsitesApiTokenLog::create([
                            'user_id' => Auth::id(),
                            'store_website_id' => $storeWebsite->id,
                            'store_website_users_id' => $StoreWebsiteUser->id,
                            'response' => $token_response->json('message'),
                            'status_code' => $token_response->status(),
                            'status' => 'Error',
                        ]);
                        // return response()->json(['success' => false, 'message' => $token_response->json('message')]);
                    }
                } else {
                    StoreWebsitesApiTokenLog::create([
                        'user_id' => Auth::id(),
                        'store_website_id' => $storeWebsite->id,
                        'store_website_users_id' => $StoreWebsiteUser->id,
                        'response' => 'The store website URL or Store Code is not found.',
                        'status_code' => '404',
                        'status' => 'Error',
                    ]);
                    // return response()->json(['success' => false, 'message' => 'The store website URL or Store Code is not found.']);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Bulk API token generate completed, You can check logs individually.']);
    }

    public function logWebsiteUsers($id): View
    {
        $title = 'List | Store Website User Logs';
        $logstorewebsiteuser = LogStoreWebsiteUser::where('store_website_id', $id)->orderByDesc('id')->get();

        return view('storewebsite::log_store_website_users', compact('title', 'logstorewebsiteuser'));
    }

    public function cancellation(): View
    {
        $title = 'Cancellation Policy | Store Website';

        return view('storewebsite::cancellation', compact('title'));
    }

    /**
     * records Page
     *
     * @param  Request  $request  [description]
     */
    public function records(Request $request): JsonResponse
    {
        $records = StoreWebsite::whereNull('deleted_at')
            ->leftJoin('store_view_code_server_map as svcsm', 'svcsm.id', 'store_websites.store_code_id')
            ->select(['store_websites.*', 'svcsm.code as store_code_name', 'svcsm.id as store_code_id']);
        $keyword = request('keyword');
        $country = request('country');
        $mailing_service_id = request('mailing_service_id');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%")
                    ->orWhere('title', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($country)) {
            $records = $records->where(function ($q) use ($country) {
                $q->where('country_duty', 'LIKE', "%$country%");
            });
        }

        if (! empty($mailing_service_id)) {
            $records = $records->where(function ($q) use ($mailing_service_id) {
                $q->where('mailing_service_id', $mailing_service_id);
            });
        }

        $records = $records->orderBy('website')->get();

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function saveCancellation(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);
        $checkCacellation = ProductCancellationPolicie::find($id);
        if ($checkCacellation != null) {
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->update();
        } else {
            $checkCacellation = new ProductCancellationPolicie;
            $checkCacellation->store_website_id = $request->store_website_id;
            $checkCacellation->days_cancelation = $request->days_cancelation;
            $checkCacellation->days_refund = $request->days_refund;
            $checkCacellation->percentage = $request->percentage;
            $checkCacellation->save();
        }

        return response()->json(['code' => 200, 'data' => $checkCacellation]);
    }

    public function savelogwebsiteuser($log_case_id, $id, $username, $userEmail, $firstName, $lastName, $password, $website_mode, $msg)
    {
        $log = new LogStoreWebsiteUser;
        $log->log_case_id = $log_case_id;
        $log->store_website_id = $id;
        $log->username = $username;
        $log->username = $username;
        $log->useremail = $userEmail;
        $log->first_name = $firstName;
        $log->last_name = $lastName;
        $log->password = $password;
        $log->website_mode = $website_mode;
        $log->log_msg = $msg;
        $log->save();
    }

    /**
     * records Page
     *
     * @param  Request  $request  [description]
     */
    public function save(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'title' => 'required',
            'website' => 'required',
            'product_markup' => 'required',
            'cloud_platform' => 'required',
            'ai_assistant' => 'required',
            'cluster_file' => 'nullable|required_if:cloud_platform,do',
            'pod_name' => 'nullable|required_if:cloud_platform,do',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }
        $id = $request->get('id', 0);

        $records = StoreWebsite::find($id);

        if (! $records) {
            $records = new StoreWebsite;
        } else {
            if (! is_null($request->is_debug_true)) {
                if (! $request->server_ip) {
                    $outputString = 'Server IP is required to enable db logs';

                    return response()->json(['code' => 500, 'error' => $outputString]);
                }
                if ($records->is_debug_true !== $request->is_debug_true) {
                    $this->enableDBLog($request);
                }
            }
        }

        if ($request->key_file_path1 != 'undefined' && $request->key_file_path1 != '') {
            $keyPath = public_path('bigData');
            if (! file_exists($keyPath)) {
                mkdir($keyPath, 0777, true);
            }
            $file = $request->file('key_file_path1');
            $keyPathName = uniqid().strtotime(date('YmdHis')).'_'.trim($file->getClientOriginalName());
            $file->move($keyPath, $keyPathName);
            $post['key_file_path'] = $keyPathName;
        }
        if ($post['site_folder'] != '') {
            $post['site_folder'] = $post['site_folder'];
        }

        $post['aws_region'] = $request->aws_region;
        $post['aws_cluster'] = $request->aws_cluster;
        $post['aws_ecs_service_id'] = $request->aws_ecs_service_id;
        $post['aws_api_key'] = $request->aws_api_key;
        $post['aws_api_secret'] = $request->aws_api_secret;
        $post['aws_document_name'] = $request->aws_document_name;
        $post['cluster_file'] = $request->cluster_file;
        $post['pod_name'] = $request->pod_name;

        $records->fill($post);

        $records->save();

        if (isset($post['username'])) {
            $this->savelogwebsiteuser('#1', $post['id'], $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['website_mode'], 'For this Website '.$post['id'].' ,A new user has been created.');
        }
        if ($request->staging_username && $request->staging_password) {
            $message = 'Staging Username: '.$request->staging_username.', Staging Password is: '.$request->staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($request->mysql_username && $request->mysql_password) {
            $message = 'Mysql Username: '.$request->mysql_username.', Mysql Password is: '.$request->mysql_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($request->mysql_staging_username && $request->mysql_staging_password) {
            $message = 'Mysql Staging Username: '.$request->mysql_staging_username.', Mysql Staging Password is: '.$request->mysql_staging_password;
            $params['user_id'] = Auth::id();
            $params['message'] = $message;
            $chat_message = ChatMessage::create($params);
        }

        if ($id == 0) {
            $siteDevelopmentCategories = SiteDevelopmentCategory::all();
            foreach ($siteDevelopmentCategories as $develop) {
                $site = new SiteDevelopment;
                $site->site_development_category_id = $develop->id;
                $site->site_development_master_category_id = $develop->master_category_id;
                $site->website_id = $records->id;
                $site->save();
            }
        }

        return response()->json(['code' => 200, 'message' => 'Data successfully saved', 'data' => $records]);
    }

    /**
     * Creates store website from an existing store website and insert necessary data to the corresponding tables
     *
     * @param  Request  $request  [description]
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function saveDuplicateStore(Request $request): JsonResponse
    {
        //        set_time_limit(0);
        $numberOfDuplicates = $request->get('number') - 1;
        if ($numberOfDuplicates <= 0) {
            return response()->json(['code' => 500, 'error' => 'Number of duplicates must be 1 or more!']);
        }

        $storeWebsiteId = $request->get('id');
        $storeWebsite = StoreWebsite::find($storeWebsiteId);
        $existingDuplicateCount = StoreWebsite::where('parent_id', '=', $storeWebsiteId)->count();
        $numberOfDuplicates = $numberOfDuplicates + $existingDuplicateCount;
        $serverId = 1;
        $response = $this->updateStoreViewServer($storeWebsiteId, $serverId);
        if (! $response) {
            return response()->json(['code' => 500, 'error' => 'Something went wrong in update store view server!']);
        }

        if (! $storeWebsite) {
            return response()->json(['code' => 500, 'error' => 'No website found!']);
        }

        for ($i = $existingDuplicateCount + 1; $i <= $numberOfDuplicates; $i++) {
            $copyStoreWebsite = $storeWebsite->replicate();
            $title = $copyStoreWebsite->title;
            unset($copyStoreWebsite->id);
            unset($copyStoreWebsite->title);
            $copyStoreWebsite->title = $title.' '.$i;
            $copyStoreWebsite->parent_id = $storeWebsiteId;
            $copyStoreWebsite->save();

            Log::info($copyStoreWebsite->title.' step 1 created.');

            DuplicateStoreWebsiteJob::dispatch($storeWebsiteId, $copyStoreWebsite, $i)->onQueue('sololuxury');

            if ($i == $numberOfDuplicates) {
                return response()->json(['code' => 200, 'error' => 'Store website created successfully']);
            }
        }
    }

    /**
     * Function to update store view server mapping of a store website
     *
     * @param  mixed  $storeWebsiteId
     * @param  mixed  $serverId
     */
    public function updateStoreViewServer($storeWebsiteId, $serverId): JsonResponse
    {
        $servers = StoreViewCodeServerMap::where('server_id', '=', $serverId)->pluck('code')->toArray();
        $storeViews = WebsiteStoreView::whereIn('code', $servers)->get();
        $count = 0;
        foreach ($storeViews as $key => $view) {
            $storeView = WebsiteStoreView::find($view->id);
            if (! $storeView->websiteStore) {
                Log::error('Website store not found for '.$view->id.'!');
            } elseif (! $storeView->websiteStore->website) {
                Log::error('Website not found for '.$view->id.'!');
            } else {
                $websiteId = $view->websiteStore->website->id;
                $website = Website::find($websiteId);
                $website->store_website_id = $storeWebsiteId;
                $response = $website->save();
            }
            $count++;
        }

        if ($response && $count == $key + 1) {
            return true;
        } else {
            Log::error('Count is not equal to total store views');

            return false;
        }
    }

    public function saveUserInMagento(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'username' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'userEmail' => 'required',
            'password' => 'required',
            'websitemode' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsites = StoreWebsite::where('id', '=', $post['store_id'])->orWhere('parent_id', '=', $post['store_id'])->get();
        $count = 0;
        foreach ($storeWebsites as $key => $storeWebsite) {
            $this->savelogwebsiteuser('#2', $storeWebsite->id, $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['websitemode'], 'For this Website '.$storeWebsite->id.' ,A user has been updated.');

            $checkUserNameExist = '';
            if (! empty($post['store_website_userid'])) {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->where('id', '<>', $post['store_website_userid'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->where('id', '<>', $post['store_website_userid'])->first();
                }
            } else {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->first();
                }
            }

            if (! empty($checkUserExist)) {
                return response()->json(['code' => 500, 'error' => 'User Email already exist!']);
            }
            if (! empty($checkUserNameExist)) {
                return response()->json(['code' => 500, 'error' => 'Username already exist!']);
            }

            $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
            if (! $uppercase || strlen($post['password']) < 7) {
                return response()->json(['code' => 500, 'error' => 'Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters.']);
            }

            if (! empty($post['store_website_userid'])) {
                $getUser = StoreWebsiteUsers::where('id', $post['store_website_userid'])->first();
                $getUser->first_name = $post['firstName'];
                $getUser->last_name = $post['lastName'];
                $getUser->email = $post['userEmail'];
                $getUser->password = $post['password'];
                $getUser->website_mode = $post['websitemode'];
                $getUser->save();

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $getUser->store_website_id,
                    'store_website_user_id' => $getUser->id,
                    'model' => StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'updated',
                    'new_value' => 'updated',
                    'user_id' => Auth::id(),
                ]);

                if ($getUser->is_deleted == 0) {
                    $magentoHelper = new MagentoHelperv2;
                    $magentoHelper->updateMagentouser($storeWebsite, $post);
                }
            } else {
                $params['username'] = $post['username'];
                $params['first_name'] = $post['firstName'];
                $params['last_name'] = $post['lastName'];
                $params['email'] = $post['userEmail'];
                $params['password'] = $post['password'];
                $params['store_website_id'] = $storeWebsite->id;
                $params['website_mode'] = $post['websitemode'];

                $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

                if ($post['userEmail'] && $post['password']) {
                    $message = 'Email: '.$post['userEmail'].', Password is: '.$post['password'];
                    $params['user_id'] = Auth::id();
                    $params['message'] = $message;
                    ChatMessage::create($params);
                }

                $magentoHelper = new MagentoHelperv2;
                $magentoHelper->addMagentouser($storeWebsite, $post);

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $StoreWebsiteUsersid->store_website_id,
                    'store_website_user_id' => $StoreWebsiteUsersid->id,
                    'model' => StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'new_added',
                    'new_value' => 'new_added',
                    'user_id' => Auth::id(),
                ]);
            }
            $count++;
        }
        if ($count == $key + 1) {
            return response()->json(['code' => 200, 'messages' => 'User details saved Successfully']);
        } else {
            return response()->json(['code' => 500, 'messages' => 'Something went wrong']);
        }
    }

    public function saveUserInMagentoAdminPassword(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'username' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'userEmail' => 'required',
            'password' => 'required',
            'websitemode' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsites = StoreWebsite::where('id', '=', $post['store_id'])->orWhere('parent_id', '=', $post['store_id'])->get();
        $count = 0;
        foreach ($storeWebsites as $key => $storeWebsite) {
            $this->savelogwebsiteuser('#2', $storeWebsite->id, $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['websitemode'], 'For this Website '.$storeWebsite->id.' ,A user has been updated.');

            $checkUserNameExist = '';
            if (! empty($post['store_website_userid'])) {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->where('id', '<>', $post['store_website_userid'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->where('id', '<>', $post['store_website_userid'])->first();
                }
            } else {
                $checkUserExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->first();
                if (empty($checkUserExist)) {
                    $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->first();
                }
            }

            if (! empty($checkUserExist)) {
                return response()->json(['code' => 500, 'error' => 'User Email already exist!']);
            }
            if (! empty($checkUserNameExist)) {
                return response()->json(['code' => 500, 'error' => 'Username already exist!']);
            }

            $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
            if (! $uppercase || strlen($post['password']) < 7) {
                return response()->json(['code' => 500, 'error' => 'Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters.']);
            }

            if (! empty($post['store_website_userid'])) {
                $getUser = StoreWebsiteUsers::where('id', $post['store_website_userid'])->first();
                $getUser->first_name = $post['firstName'];
                $getUser->last_name = $post['lastName'];
                $getUser->email = $post['userEmail'];
                $getUser->password = $post['password'];
                $getUser->website_mode = $post['websitemode'];
                $getUser->save();

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $getUser->store_website_id,
                    'store_website_user_id' => $getUser->id,
                    'model' => StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'updated',
                    'new_value' => 'updated',
                    'user_id' => Auth::id(),
                ]);

                if ($getUser->is_deleted == 0) {
                    $magentoHelper = new MagentoHelperv2;
                    $magentoHelper->updateMagentouser($storeWebsite, $post, $getUser->id);
                }
            } else {
                $params['username'] = $post['username'];
                $params['first_name'] = $post['firstName'];
                $params['last_name'] = $post['lastName'];
                $params['email'] = $post['userEmail'];
                $params['password'] = $post['password'];
                $params['store_website_id'] = $storeWebsite->id;
                $params['website_mode'] = $post['websitemode'];

                $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

                if ($post['userEmail'] && $post['password']) {
                    $message = 'Email: '.$post['userEmail'].', Password is: '.$post['password'];
                    $params['user_id'] = Auth::id();
                    $params['message'] = $message;
                    ChatMessage::create($params);
                }

                $magentoHelper = new MagentoHelperv2;
                $magentoHelper->addMagentouser($storeWebsite, $post, $StoreWebsiteUsersid->id);

                StoreWebsiteUserHistory::create([
                    'store_website_id' => $StoreWebsiteUsersid->store_website_id,
                    'store_website_user_id' => $StoreWebsiteUsersid->id,
                    'model' => StoreWebsiteUsers::class,
                    'attribute' => 'username_password',
                    'old_value' => 'new_added',
                    'new_value' => 'new_added',
                    'user_id' => Auth::id(),
                ]);
            }
            $count++;
        }
        if ($count == $key + 1) {
            return response()->json(['code' => 200, 'messages' => 'User details saved Successfully']);
        } else {
            return response()->json(['code' => 500, 'messages' => 'Something went wrong']);
        }
    }

    public function deleteUserInMagento(Request $request): JsonResponse
    {
        $post = $request->all();
        $getUser = StoreWebsiteUsers::where('id', $post['store_website_userid'])->first();
        $username = $getUser->username;
        $getUser->is_deleted = 1;
        $getUser->save();

        $this->savelogwebsiteuser('#3', $getUser['store_website_id'], $getUser['username'], $getUser['email'], $getUser['first_name'], $getUser['last_name'], $getUser['password'], $getUser['website_mode'], 'For this Website '.$getUser['store_website_id'].' ,User has been Deleted.');

        $storeWebsite = StoreWebsite::find($getUser->store_website_id);

        $magentoHelper = new MagentoHelperv2;
        $result = $magentoHelper->deleteMagentouser($storeWebsite, $username);

        StoreWebsiteUserHistory::create([
            'store_website_id' => $getUser->store_website_id,
            'store_website_user_id' => $getUser->id,
            'model' => StoreWebsiteUsers::class,
            'attribute' => 'username_password',
            'old_value' => 'delete',
            'new_value' => 'delete',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['code' => 200, 'messages' => 'User Deleted Sucessfully']);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        $services = Service::get();
        //->where('is_deleted',0)

        $storewebsiteusers = StoreWebsiteUsers::where('store_website_id', $id)->get();
        if ($storeWebsite->cloud_platform == 'do') {
            $kubernates = new KubernetesService($storeWebsite->cluster_file);
            $podNames = $kubernates->getPodList();
        } else {
            $podNames = [];
        }

        $storewebsiteadminurl = StoreWebsiteAdminUrl::where('store_website_id', $id)->where('created_by', Auth::user()->id)->orderByDesc('id')->first();
        if ($storeWebsite) {
            return response()->json([
                'code' => 200,
                'data' => $storeWebsite,
                'userdata' => $storewebsiteusers,
                'last_adminurl' => $storewebsiteadminurl,
                'services' => $services,
                'totaluser' => count($storewebsiteusers),
                'podNames' => $podNames]
            );
        }

        return response()->json(['code' => 404, 'error' => 'Wrong site id!']);
    }

    public function editCancellation(Request $request, $id): JsonResponse
    {
        $storeWebsite = ProductCancellationPolicie::where('store_website_id', $id)->first();
        // $storewebsiteusers = StoreWebsiteUsers::where('store_website_id',$id)->where('is_deleted',0)->get();
        if ($storeWebsite) {
            return response()->json(['code' => 200, 'data' => $storeWebsite]);
        }

        return response()->json(['code' => 200, 'data' => ['store_website_id' => $id]]);
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();

        if ($storeWebsite) {
            $storeWebsite->delete();
            SiteDevelopment::where('website_id', '=', $id)->delete();
            Log::info('Deleted from SiteDevelopment with id '.$id);
            StoreWebsitesCountryShipping::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsitesCountryShipping with id '.$id);
            StoreWebsiteAnalytic::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteAnalytic with id '.$id);
            StoreWebsiteAttributes::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteAttributes with id '.$id);
            StoreWebsiteBrand::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteBrand with id '.$id);
            StoreWebsiteCategory::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteCategory with id '.$id);
            StoreWebsiteCategorySeo::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteCategorySeo with id '.$id);
            StoreWebsiteColor::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteColor with id '.$id);
            StoreWebsiteGoal::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteGoal with id '.$id);
            StoreWebsiteImage::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteImage with id '.$id);
            StoreWebsiteProduct::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteProduct with id '.$id);
            StoreWebsitePage::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsitePage with id '.$id);
            StoreWebsiteProductAttribute::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteProductAttribute with id '.$id);
            StoreWebsiteProductPrice::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteProductPrice with id '.$id);
            StoreWebsiteProductScreenshot::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteProductScreenshot with id '.$id);
            StoreWebsiteSeoFormat::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteSeoFormat with id '.$id);
            StoreWebsiteSize::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteSize with id '.$id);
            StoreWebsiteTwilioNumber::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteTwilioNumber with id '.$id);
            StoreWebsiteUsers::where('store_website_id', '=', $id)->delete();
            Log::info('Deleted from StoreWebsiteUsers with id '.$id);

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function updateSocialRemarks(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();

        if ($storeWebsite) {
            $facebook_remarks = $request->get('facebook_remarks');
            if (! empty($facebook_remarks)) {
                $storeWebsite->facebook_remarks = $facebook_remarks;
            }

            $instagram_remarks = $request->get('instagram_remarks');
            if (! empty($instagram_remarks)) {
                $storeWebsite->instagram_remarks = $instagram_remarks;
            }

            $storeWebsite->save();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function socialStrategy($id, Request $request)
    {
        $website = StoreWebsite::find($id);
        $subjects = SocialStrategySubject::orderByDesc('id');

        if ($request->k != null) {
            $subjects = $subjects->where('title', 'like', '%'.$request->k.'%');
        }

        $subjects = $subjects->paginate(Setting::get('pagination'));
        foreach ($subjects as $subject) {
            $subject->strategy = SocialStrategy::where('social_strategy_subject_id', $subject->id)->where('website_id', $id)->first();
        }
        $users = User::select('id', 'name')->get();

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::social-strategy.partials.data', compact('subjects', 'users', 'website'))->render(),
                'links' => (string) $subjects->render(),
            ], 200);
        }

        return view('storewebsite::social-strategy.index', compact('subjects', 'users', 'website'));
    }

    public function submitSubject(Request $request): JsonResponse
    {
        if ($request->text) {
            $subjectCheck = SocialStrategySubject::where('title', $request->text)->first();

            if (empty($subjectCheck)) {
                $subject = new SocialStrategySubject;
                $subject->title = $request->text;
                $subject->save();

                return response()->json(['code' => 200, 'messages' => 'Subject Saved Sucessfully']);
            } else {
                return response()->json(['code' => 500, 'messages' => 'Subject Already Exist']);
            }
        } else {
            return response()->json(['code' => 500, 'messages' => 'Please Enter Text']);
        }
    }

    public function submitStrategy($id, Request $request): JsonResponse
    {
        $store_strategy = SocialStrategy::where('social_strategy_subject_id', $request->subject)->where('website_id', $request->site)->first();

        if (! $store_strategy) {
            $store_strategy = new SocialStrategy;
        }
        if ($request->type == 'description') {
            $store_strategy->description = $request->text;
        }

        if ($request->type == 'execution') {
            $store_strategy->execution_id = $request->text;
        }

        if ($request->type == 'content') {
            $store_strategy->content_id = $request->text;
        }

        $store_strategy->social_strategy_subject_id = $request->subject;
        $store_strategy->website_id = $request->site;

        $store_strategy->save();

        return response()->json(['code' => 200, 'messages' => 'Social strategy Saved Sucessfully']);
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        $strategy = null;
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            if ($request->id) {
                $strategy = SocialStrategy::find($request->id);
            }

            if (! $strategy || $request->id == null) {
                $strategy = new SocialStrategy;
                $strategy->description = '';
                $strategy->website_id = $request->store_website_id;
                $strategy->social_strategy_subject_id = $request->site_development_subject_id;
                $strategy->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/'.floor($strategy->id / config('constants.image_per_folder')))
                    ->upload();
                $strategy->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function listDocuments(Request $request, $id): JsonResponse
    {
        $site = SocialStrategy::find($request->id);

        $userList = [];

        if ($site->execution_id) {
            $userList[$site->execution->id] = $site->execution->name;
        }

        if ($site->content_id) {
            $userList[$site->content->id] = $site->content->name;
        }

        $userList = array_filter($userList);
        // create the select box design html here
        $usrSelectBox = '';
        if (! empty($userList)) {
            $usrSelectBox = (string) html()->select('send_message_to', $userList, null)->class('form-control send-message-to-id');
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'site_id' => $site->id,
                        'user_list' => $usrSelectBox,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function deleteDocument(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();

                return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function sendDocument(Request $request): JsonResponse
    {
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user = User::find($request->user_id);
            if ($user) {
                if ($media) {
                    ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        'Please find attached file',
                        getMediaUrl($media)
                    );

                    return response()->json(['code' => 200, 'message' => 'Document send succesfully']);
                }
            } else {
                return response()->json(['code' => 200, 'message' => 'User or site is not available']);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Sorry required fields is missing like id, siteid , userid']);
    }

    public function remarks(Request $request, $id): JsonResponse
    {
        $response = SocialStrategyRemark::join('users as u', 'u.id', 'social_strategy_remarks.user_id')->where('social_strategy_id', $request->id)
            ->select(['social_strategy_remarks.*', DB::raw('u.name as created_by')])
            ->orderByDesc('social_strategy_remarks.created_at')
            ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function saveRemarks(Request $request, $id): JsonResponse
    {
        SocialStrategyRemark::create([
            'remarks' => $request->remark,
            'social_strategy_id' => $request->id,
            'user_id' => Auth::user()->id,
        ]);

        $response = SocialStrategyRemark::join('users as u', 'u.id', 'social_strategy_remarks.user_id')->where('social_strategy_id', $request->id)
            ->select(['social_strategy_remarks.*', DB::raw('u.name as created_by')])
            ->orderByDesc('social_strategy_remarks.created_at')
            ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function viewSubject(Request $request): JsonResponse
    {
        $subject = SocialStrategySubject::find($request->id);

        return response()->json(['code' => 200, 'data' => $subject]);
    }

    public function submitSubjectChange(Request $request, $id): JsonResponse
    {
        $subject = SocialStrategySubject::find($request->id);
        $subject->title = $request->subject_title;
        $subject->save();

        return response()->json(['code' => 200, 'message' => 'Successful']);
    }

    public function generateStorefile(Request $request)
    {
        $server = $request->get('for_server');

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'pem-generate.sh '.$server.' 2>&1';

        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        Log::info(print_r($allOutput, true));

        $string = [];
        if (! empty($allOutput)) {
            $continuetoFill = false;
            foreach ($allOutput as $ao) {
                if ($ao == '-----BEGIN RSA PRIVATE KEY-----' || $continuetoFill) {
                    $string[] = $ao;
                    $continuetoFill = true;
                }
            }
        }

        $content = implode("\n", $string);

        $nameF = $server.'.pem';

        //header download
        header('Content-Disposition: attachment; filename="'.$nameF.'"');
        header('Content-Type: application/force-download');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/x-pem-file');

        echo $content;
        exit;
    }

    public function magentoUserList(Request $request): JsonResponse
    {
        $users = StoreWebsiteUsers::where('is_deleted', 0)->get();

        return response()->json(['code' => 200, 'data' => $users]);
    }

    public function userHistoryList(Request $request): JsonResponse
    {
        $histories = StoreWebsiteUserHistory::with('websiteuser', 'storewebsite')
            ->where('store_website_id', $request->id)
            ->latest()
            ->get();

        $resultArray = [];

        foreach ($histories as $history) {
            $resultArray[] = [
                'date' => $history->created_at->format('Y-m-d H:i:s'),
                'website_mode' => $history->websiteuser->website_mode,
                'username' => $history->websiteuser->username,
                'first_name' => $history->websiteuser->first_name,
                'last_name' => $history->websiteuser->last_name,
                'action' => $history->new_value,
            ];
        }

        return response()->json(['code' => 200, 'data' => $resultArray]);
    }

    public function storeReindexHistory(Request $request): JsonResponse
    {
        $website = StoreWebsite::find($request->id);
        $date = Carbon::now()->subDays(7);
        $histories = StoreReIndexHistory::where('server_name', $website->title)->where('created_at', '>=', $date)
            ->latest()
            ->get();

        $resultArray = [];

        foreach ($histories as $history) {
            $resultArray[] = [
                'date' => $history->created_at->format('Y-m-d H:i:s'),
                'server_name' => $history->server_name,
                'username' => $history->username,
                'action' => $history->action,
            ];
        }

        return response()->json(['code' => 200, 'data' => $resultArray]);
    }

    /**
     * Build Process Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function buildProcess(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        if ($storeWebsite) {
            return response()->json([
                'code' => 200,
                'data' => $storeWebsite,
            ]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function buildProcessSave(Request $request): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'reference' => 'required',
            'repository' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }

        if (! empty($request->store_website_id)) {
            $StoreWebsite = StoreWebsite::find($request->store_website_id);

            if ($StoreWebsite != null) {
                $StoreWebsite->build_name = $request->repository;
                $StoreWebsite->repository = $request->repository;
                $StoreWebsite->reference = $request->reference;
                $StoreWebsite->update();

                if ($StoreWebsite) {
                    $jobName = $request->repository;
                    $repository = $request->repository;
                    $ref = $request->reference;
                    $staticdep = 1;

                    $jenkins = new \JenkinsKhan\Jenkins('http://apibuild:117ed14fbbe668b88696baa43d37c6fb48@build.theluxuryunlimited.com:8080');
                    $jenkins->launchJob($jobName, ['repository' => $repository, 'ref' => $ref, 'staticdep' => 0]);
                    if ($jenkins->getJob($jobName)) {
                        $job = $jenkins->getJob($jobName);
                        $builds = $job->getBuilds();
                        $buildDetail = 'Build Name: '.$jobName.'<br> Build Repository: '.$repository.'<br> Reference: '.$ref;
                        $record = ['store_website_id' => $request->store_website_id, 'created_by' => Auth::id(), 'text' => $buildDetail, 'build_name' => $jobName, 'build_number' => $builds[0]->getNumber()];
                        BuildProcessHistory::create($record);

                        return response()->json(['code' => 200, 'error' => 'Process builed complete successfully.']);
                    } else {
                        return response()->json(['code' => 500, 'error' => 'Please try again, Jenkins job not created']);
                    }
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }
    }

    /**
     * This function is use to add company website address.
     */
    public function addCompanyWebsiteAddress(Request $request, int $store_website_id): JsonResponse
    {
        $StoreWebsite = StoreWebsite::where('id', '=', $store_website_id)->first();
        if ($StoreWebsite != null) {
            return response()->json([
                'code' => 200,
                'data' => $StoreWebsite,
            ]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
    }

    public function magentoDevScriptUpdate(Request $request): JsonResponse
    {
        try {
            $run = Artisan::call('command:MagentoDevUpdateScript', ['id' => $request->id, 'folder_name' => $request->folder_name]);

            return response()->json(['code' => 200, 'message' => 'Magento Setting Updated successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getDownloadDbEnvLogs(Request $request, $store_website_id): JsonResponse
    {
        try {
            $perPage = 25;
            $responseLog = DownloadDatabaseEnvLogs::where('store_website_id', $store_website_id)
                ->orderByDesc('created_at')
                ->paginate($perPage);
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>'.$res->id.'</td>';
                    if (isset($res->user->name)) {
                        $html .= '<td>'.$res->user->name.'</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '<td>'.$res->type.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->cmd, 50, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->cmd.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit(json_encode($res->output), 100, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.json_encode($res->output).'</span>
                    </td>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    if ($res->download_url) {
                        $filename = basename($res->download_url);
                        $downloadRoute = route('store-website.downloadFile', $filename);

                        $html .= '<td><a href="'.$downloadRoute.'" class="btn btn-primary" download>Download</a></td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => '',
                    'pagination' => $responseLog,
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function getMagentoUpdateWebsiteSetting(Request $request, $store_website_id): JsonResponse
    {
        try {
            $responseLog = MagentoSettingUpdateResponseLog::where('website_id', '=', $store_website_id)->get();
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 100, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento setting updated successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function getFolderName(Request $request)
    {
        //$assetManager = AssetsManager::where('id', $request->id);
    }

    public function getMagentoDevScriptUpdatesLogs(Request $request, $store_website_id): JsonResponse
    {
        try {
            $responseLog = MagentoDevScripUpdateLog::where('store_website_id', '=', $store_website_id)->get();
            //dd($responseLog);
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    //dd($res->created_at);
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="website" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-website-'.$res->id.'">'.Str::limit($res->website, 15, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-website-'.$res->id.' hidden">'.$res->website.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="command" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-command-'.$res->id.'">'.Str::limit($res->command_name, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-command-'.$res->id.' hidden">'.$res->command_name.'</span>
                    </td>';

                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento setting updated successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    /**
     * This function is use to Update company's website address.
     */
    public function updateCompanyWebsiteAddress(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'website_address' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }

        if (! empty($request->store_website_id)) {
            $StoreWebsite = StoreWebsite::find($request->store_website_id);
            if ($StoreWebsite != null) {
                $StoreWebsite->website_address = $request->website_address;
                $StoreWebsite->update();

                return response()->json(['code' => 200, 'message' => 'Address has been saved']);
            }

            return response()->json(['code' => 500, 'error' => 'Please fill required fields.']);
        }
    }

    public function syncStageToMaster($storeWebId): JsonResponse
    {
        $websiteDetails = StoreWebsite::where('id', $storeWebId)->select('server_ip', 'repository_id')->first();
        if ($websiteDetails != null and $websiteDetails['server_ip'] != null and $websiteDetails['repository_id'] != null) {
            $repo = GithubRepository::where('id', $websiteDetails['repository_id'])->pluck('name')->first();
            if ($repo != null) {
                $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'sync-staticfiles.sh -r '.$repo.' -s '.$websiteDetails['server_ip'];
                $allOutput = [];
                $allOutput[] = $cmd;
                $result = exec($cmd, $allOutput); //Execute command
                Log::info(print_r(['Command Output', $allOutput], true));

                return response()->json(['code' => 200, 'message' => 'Command executed']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Repository Not found.']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Request has been failed.']);
        }
    }

    public function enableDBLog($website)
    {
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'magento-debug.sh --server '.$website->server_ip.' --debug '.($website->is_debug_true ? 'true' : 'false').' 2>&1';
        Log::info('[SatyamTest] '.$cmd);
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);
        Log::info(print_r($allOutput, true));

        return $result;
    }

    public function checkMagentoToken(Request $request): JsonResponse
    {
        $token = $request->id;
        $magentoHelper = new MagentoHelperv2;
        $result = $magentoHelper->checkToken($token, $request->url);
        if ($result) {
            return response()->json(['code' => 200, 'message' => 'Token is valid']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Token is invalid']);
        }
    }

    public function generateApiToken(Request $request): RedirectResponse
    {
        $storeId = current(array_filter($request->update_website_api_id));
        $oldStoreWebsite = StoreWebsite::find($storeId);
        $storeWebsiteHistory = new StoreWebsiteApiTokenHistory;
        $storeWebsiteHistory->store_websites_id = $oldStoreWebsite->id;
        $storeWebsiteHistory->old_api_token = $oldStoreWebsite->api_token;
        $storeWebsiteHistory->updatedBy = Auth::id();

        $apiTokens = $request->api_token;

        if ($request->api_token) {
            foreach ($apiTokens as $key => $apiToken) {
                StoreWebsite::where('id', $key)->update(['api_token' => $apiToken, 'server_ip' => $request->server_ip[$key]]);
            }
            $newStoreWebsite = StoreWebsite::find($storeId);
            $storeWebsiteHistory->new_api_token = $newStoreWebsite->api_token;
            $storeWebsiteHistory->save();

            session()->flash('msg', 'Api Token Updated Successfully.');

            return redirect()->back();
        } else {
            session()->flash('msg', 'Api Token is invalid.');

            return redirect()->back();
        }
    }

    public function getApiToken(Request $request): JsonResponse
    {
        $search = $request->search;
        $store_ids = $request->store_ids;

        $storeWebsites = StoreWebsite::whereNull('deleted_at');
        if ($search != null) {
            $storeWebsites = $storeWebsites->where('title', 'Like', '%'.$search.'%');
        }
        if ($store_ids != null) {
            $storeWebsites = $storeWebsites->whereIn('id', $store_ids);
        }
        $storeWebsites = $storeWebsites->get();

        return response()->json([
            'tbody' => view('storewebsite::api-token', compact('storeWebsites'))->render(),

        ], 200);
    }

    public function deleteStoreViews($serverId): JsonResponse
    {
        set_time_limit(0);
        $storeServers = StoreViewCodeServerMap::where('server_id', '=', $serverId)->pluck('id')->toArray();
        $storeWebsiteIds = StoreWebsite::WhereIn('store_code_id', $storeServers)->pluck('id')->toArray();

        if (count($storeWebsiteIds) == 0) {
            return response()->json(['code' => 500, 'message' => 'Store websites not found for the selected server']);
        }

        $serverCodes = StoreViewCodeServerMap::where('server_id', '=', $serverId)->get();
        if ($serverCodes->count() == 0) {
            return response()->json(['code' => 500, 'message' => 'No store coded found for the selected website']);
        }

        $serverCodesPartials = [];
        foreach ($serverCodes as $code) {
            $codeArray = explode('-', $code->code);
            $serverCodesPartials[] = $codeArray[0];
        }
        $serverCodesPartials = array_unique($serverCodesPartials);
        $noWebsiteIds = [];
        foreach ($storeWebsiteIds as $storeWebsiteId) {
            $websites = Website::whereIn('code', $serverCodesPartials)->where('store_website_id', '=', $storeWebsiteId)
                ->groupBy('code')->orderBy('id')->pluck('id');
            if ($websites->count() == 0) {
                $noWebsiteIds[] = $storeWebsiteId;
                Log::info('No websites found belongs to the store website id '.$storeWebsiteId);
            }
            $websitesToBeRemoved = Website::whereNotIn('id', $websites)->where('store_website_id', '=', $storeWebsiteId)->pluck('id');

            $websiteStoresToBeRemoved = WebsiteStore::whereIn('website_id', $websitesToBeRemoved)->pluck('id');
            WebsiteStoreView::whereIn('website_store_id', $websiteStoresToBeRemoved)->delete();
            Log::info('Store views belongs to  the following ids deleted: '.json_encode($websiteStoresToBeRemoved));
            WebsiteStore::whereIn('website_id', $websitesToBeRemoved)->delete();
            Log::info('Website stores belongs to  the following ids deleted: '.json_encode($websitesToBeRemoved));
            Website::whereIn('id', $websitesToBeRemoved)->where('store_website_id', '=', $storeWebsiteId)->delete();
            Log::info('Websites belongs to  the following ids deleted: '.json_encode($websitesToBeRemoved));
        }

        return response()->json(['code' => 200, 'message' => 'Website store views deleted successfully! The following store websites dont have websites: '.json_encode($noWebsiteIds)]);
    }

    public function copyWebsiteStoreViews($storeWebsiteId): JsonResponse
    {
        set_time_limit(0);
        $storeWebsiteIds = StoreWebsite::Where('parent_id', '=', $storeWebsiteId)->orWhere('id', '=', $storeWebsiteId)->get();
        if (count($storeWebsiteIds) == 1 && $storeWebsiteId == $storeWebsiteIds[0]->id) {
            if ($storeWebsiteIds[0]->parent_id) {
                $storeWebsiteIds = StoreWebsite::Where('parent_id', '=', $storeWebsiteIds[0]->parent_id)->orWhere('id', '=', $storeWebsiteIds[0]->parent_id)->get();
            } else {
                return response()->json(['code' => 500, 'message' => 'No store websites found in the series of selected store website']);
            }
        }
        $swIds = [];
        foreach ($storeWebsiteIds as $row) {
            if ($row->id != $storeWebsiteId) {
                $swIds[] = $row->id;
            }
        }
        Log::info('Source store website id: '.$storeWebsiteId);

        foreach ($swIds as $swId) {
            Log::info('Target store website id: '.$swId);
            $websitesToBeRemoved = Website::where('store_website_id', '=', $swId)->pluck('id');
            $websiteStoresToBeRemoved = WebsiteStore::whereIn('website_id', $websitesToBeRemoved)->pluck('id');
            WebsiteStoreView::whereIn('website_store_id', $websiteStoresToBeRemoved)->delete();
            Log::info('Deleted the following store views: '.json_encode($websiteStoresToBeRemoved));
            WebsiteStore::whereIn('website_id', $websitesToBeRemoved)->delete();
            Log::info('Deleted the following website store: '.json_encode($websitesToBeRemoved));
            Website::whereIn('id', $websitesToBeRemoved)->where('store_website_id', '=', $storeWebsiteId)->delete();
            Log::info('Deleted the following websites: '.json_encode($websitesToBeRemoved));
        }

        $websites = Website::where('store_website_id', '=', $storeWebsiteId)->get();

        foreach ($websites as $website) {
            Log::info('Copying started from website : '.$website->title);
            $websiteStore = WebsiteStore::where('website_id', '=', $website->id)->get();
            foreach ($swIds as $row) {
                $dataRow['name'] = $website->name;
                $dataRow['code'] = $website->code;
                $dataRow['platform_id'] = $website->platform_id;
                $dataRow['store_website_id'] = $row;
                $lastRow = Website::create($dataRow);

                foreach ($websiteStore as $wsRow) {
                    $websiteStoreViews = WebsiteStoreView::where('website_store_id', '=', $wsRow->id)->get();
                    $wsData['name'] = $wsRow->name;
                    $wsData['code'] = $wsRow->code;
                    $wsData['platform_id'] = $wsRow->platform_id;
                    $wsData['website_id'] = $lastRow->id;
                    $lastWsRow = WebsiteStore::create($wsData);

                    foreach ($websiteStoreViews as $websiteStoreView) {
                        $wsvData['name'] = $websiteStoreView->name;
                        $wsvData['code'] = $websiteStoreView->code;
                        $wsvData['platform_id'] = $websiteStoreView->platform_id;
                        $wsvData['website_store_id'] = $lastWsRow->id;
                        $wsvData['store_group_id'] = $websiteStoreView->store_group_id;
                        $wsvData['ref_theme_group_id'] = $websiteStoreView->ref_theme_group_id;
                        WebsiteStoreView::create($wsvData);
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Website store views copied successfully']);
    }

    public function list_tags(Request $request, WebsiteStoreTag $WebsiteStoreTag): JsonResponse
    {
        $list = $WebsiteStoreTag->all();
        if (! empty($list)) {
            return response()->json(['code' => 200, 'data' => $list, 'message' => 'List found']);
        }

        return response()->json(['code' => 400, 'message' => 'Tags Not found']);
    }

    /**
     * Create tags for multiple website and stores
     */
    public function create_tags(Request $request, WebsiteStoreTag $WebsiteStoreTag): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'tag' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 400, 'message' => $outputString]);
        }

        $insertArray = [
            'tags' => \Str::slug($data['tag']),
        ];
        //check and create the tags
        $WebsiteStoreTag->updateOrCreate($insertArray);

        return response()->json(['code' => 200, 'message' => 'Tags Added Successfully']);
    }

    public function attach_tags(Request $request, StoreWebsite $StoreWebsite): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'store_id' => 'required',
            'tag_attached' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 400, 'message' => $outputString]);
        }

        //attach the tag
        $StoreWebsite->where(['id' => $data['store_id']])->update(['tag_id' => $data['tag_attached']]);

        return response()->json(['code' => 200, 'message' => 'Tags Attach Successfully']);
    }

    public function attach_tags_store(StoreWebsite $StoreWebsite): JsonResponse
    {
        $list = $StoreWebsite->select('tag_id', 'website', 'title')->whereNotNull('tag_id')->with('tags')->get();
        if (! empty($list)) {
            return response()->json(['code' => 200, 'data' => $list, 'message' => 'List found']);
        }

        return response()->json(['code' => 400, 'message' => 'Tags Not found']);
    }

    /**
     * Create project
     */
    public function createProject(Request $request, WebsiteStoreProject $WebsiteStoreProject): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 400, 'message' => $outputString]);
        }

        $insertArray = [
            'name' => $data['name'],
        ];

        //check and create the tags
        $WebsiteStoreProject->updateOrCreate($insertArray);

        return response()->json(['code' => 200, 'message' => 'Project Added Successfully']);
    }

    public function generateAdminPassword(Request $request): RedirectResponse
    {
        $usernames = $request->username;

        if ($request->username) {
            foreach ($usernames as $key => $username) {
                if (Str::startsWith($key, 'edit:')) {
                    [$idd, $i] = $id = explode(':', $key);

                    // update
                    if ($request->store_website_id[$key]) {
                        StoreWebsiteUsers::where('id', $id[1])->update(
                            ['username' => $username, 'password' => $request->password[$key], 'store_website_id' => $request->store_website_id[$key]]
                        );
                    }
                } else {
                    // check
                    if ($request->store_website_id[$key]) {
                        $params['username'] = $username;
                        $params['password'] = $request->password[$key];
                        $params['store_website_id'] = $request->store_website_id[$key];

                        // new
                        $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);
                    }
                }
            }
            session()->flash('msg', 'Admin Password Updated Successfully.');

            return redirect()->back();
        } else {
            session()->flash('msg', 'Admin Password is invalid.');

            return redirect()->back();
        }
    }

    public function getAdminPassword(Request $request): JsonResponse
    {
        $search = $request->search;
        $storeWebsites = StoreWebsite::whereNull('deleted_at')->get();
        $storeWebsiteUsers = StoreWebsiteUsers::where('is_deleted', 0);
        if ($search != null) {
            $storeWebsiteUsers = $storeWebsiteUsers->where('username', 'Like', '%'.$search.'%')->orWhere('password', 'Like', '%'.$search.'%');
        }
        $storeWebsiteUsers = $storeWebsiteUsers->get();

        return response()->json([
            'tbody' => view('storewebsite::admin-password', compact('storeWebsites', 'storeWebsiteUsers'))->render(),

        ], 200);
    }

    public function flattenArray($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix.$key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey.'@@@'));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    public function downloadDbEnv(Request $request, $id, $type)
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        if (! $storeWebsite) {
            return response()->json(['status' => 'error', 'message' => 'Store Website data is not found!']);
        }
        if ($type != 'db' && $type != 'env') {
            return response()->json(['status' => 'error', 'message' => 'You can only download database or env data']);
        }
        if ($type == 'db' && $storeWebsite->database_name == '') {
            return response()->json(['status' => 'error', 'message' => 'Store Website database name is not found!']);
        }
        if ($storeWebsite->instance_number == '') {
            return response()->json(['status' => 'error', 'message' => 'Store Website instance number is not found!']);
        }

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'donwload-dev-db.sh -t '.$type.' -s '.$storeWebsite->server_ip.' -n '.$storeWebsite->instance_number.' 2>&1';
        $filename = 'env.php'; // because from command it returns env.php in url. So need to use same filename.
        if ($type == 'db') {
            $filename = $storeWebsite->database_name.'.sql';
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'donwload-dev-db.sh -t '.$type.' -s '.$storeWebsite->server_ip.' -n '.$storeWebsite->instance_number.' -d '.$storeWebsite->database_name.' 2>&1';
        }

        Log::info('Start Download DB/ENV');

        $result = exec($cmd, $output, $return_var);

        $downloadDatabaseEnvLogsenvLog = (new DownloadDatabaseEnvLogs)->saveLog($storeWebsite->id, auth()->user()->id, $type, $cmd, $output, $return_var);
        Log::info('command:'.$cmd);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);

        Log::info('End Download DB/ENV');
        if (! isset($output[0])) {
            return response()->json(['status' => 'error', 'message' => 'The response is not found!']);
        }
        $response = json_decode($output[0]);
        if (isset($response->status) && ($response->status == 'true' || $response->status)) {
            if (isset($response->url) && $response->url != '') {
                $path = $response->url;
            } else {
                $path = Storage::path('download_db');
                $path .= '/'.$filename;
            }
            if (file_exists($path)) {
                // return response()->download($path)->deleteFileAfterSend(true);
                $response = [
                    'status' => 'success',
                    'message' => 'Download successfully!',
                    'download_url' => $path, // Add the download URL to the response,
                    'filename' => $filename,
                ];

                // Update the log entry with the download_url
                DownloadDatabaseEnvLogs::where('id', $downloadDatabaseEnvLogsenvLog->id)->update(['download_url' => $path]);

                return response()->json($response);
            } else {
                return response()->json(['status' => 'error', 'message' => 'File Not found on server!']);
            }
            DownloadDatabaseEnvLogs::where('id', $downloadDatabaseEnvLogsenvLog->id)->update(['download_url' => $path]);
        } else {
            $message = 'Something Went Wrong! Please check Logs for more details';
            if (isset($response->message) && $response->message != '') {
                $message = $response->message;
            }

            return response()->json(['status' => 'error', 'message' => $message]);
        }

        return response()->json(['status' => 'error', 'message' => 'Download successfully!']);
    }

    public function downloadFile(Request $request, $fileName)
    {
        // Get the full path to the file you want to download
        $filePath = storage_path('app/download_db/'.$fileName);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Set the appropriate headers for the download response
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ];

            // Return the download response
            return response()->download($filePath, $fileName, $headers);
        } else {
            // If the file does not exist, return a 404 response
            abort(404);
        }
    }

    public function runFilePermissions(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        if (! $storeWebsite) {
            return response()->json(['code' => 500, 'message' => 'Store Website is not found!']);
        }
        $website = $storeWebsite->website;
        $server_ip = $storeWebsite->server_ip;
        $working_directory = $storeWebsite->working_directory;
        if ($server_ip == '') {
            return response()->json(['code' => 500, 'message' => 'Store Website server ip not found!']);
        }
        if ($working_directory == '') {
            return response()->json(['code' => 500, 'message' => 'Store Website working directory not found!']);
        }
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'file_permission.sh -w='.$website.' -s='.$server_ip.' -d='.$working_directory.' 2>&1';
        Log::info('Start run File Permissions');
        $result = exec($cmd, $output, $return_var);
        Log::info('command:'.$cmd);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);
        Log::info('End run File Permissions');

        return response()->json(['code' => 200, 'message' => 'Run Successfully']);
    }

    public function clearCloudflareCaches(Request $request, $id): JsonResponse
    {
        $storeWebsite = StoreWebsite::where('id', $id)->first();
        if (! $storeWebsite) {
            return response()->json(['code' => 500, 'message' => 'Store Website is not found!']);
        }
        $magento_url = $storeWebsite->magento_url;
        $domain_name = parse_url($magento_url, PHP_URL_HOST);
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'cloudflare_cache_clear.sh -d='.$domain_name.' 2>&1';
        Log::info('Start run Clear Cloudflare Caches');
        $result = exec($cmd, $output, $return_var);
        Log::info('command:'.$cmd);
        Log::info('output:'.print_r($output, true));
        Log::info('return_var:'.$return_var);
        Log::info('End run Clear Cloudflare Caches');

        return response()->json(['code' => 200, 'message' => 'Clear Cloudflare Caches Successfully']);
    }

    public function userPermission(Request $request): JsonResponse
    {
        $storeWebsite = StoreWebsite::find($request->store_website_id);
        $storeWebsite->users_id = $request->users_id;
        $storeWebsite->save();

        return response()->json(['code' => 200, 'message' => 'User permission updated successfully']);
    }

    public function apiTokenHistory($id): JsonResponse
    {
        $datas = StoreWebsiteApiTokenHistory::with(['user'])
            ->where('store_websites_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function adminUrlHistory($id): JsonResponse
    {
        $datas = StoreWebsiteAdminUrl::with('user', 'storewebsite')
            ->where('store_website_id', $id)
            ->where('status', 0)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function versionNumbers(): View
    {
        $storeWebsites = StoreWebsite::all();

        return view('storewebsite::version-number', compact('storeWebsites'));
    }

    // public function download($id)
    // {
    //     $fileName = MagentoCssVariableJobLog::find($id);

    //     $file_name = basename($fileName->csv_file_path);

    //     $filePath =   storage_path('app/public/magento-css-variable-csv/' . $file_name);

    //     if (file_exists($filePath)) {
    //         return Response::download($filePath);
    //     } else {
    //         abort(404, 'The file you are trying to download does not exist.');
    //     }
    // }

    public function StorewebsiteDownloadListing(Request $request): View
    {
        $perPage = 20;

        $storeWebsites = new StoreWebsite;

        if ($request->store_ids) {
            $storeWebsites = $storeWebsites->WhereIn('id', $request->store_ids);
        }

        $storeWebsites = $storeWebsites->latest()->paginate($perPage);

        $storeWebsitesDropdown = StoreWebsite::latest()->groupBy('title')->get();

        return view('storewebsite::store-website-csv-download-listing', compact('storeWebsites', 'storeWebsitesDropdown'));
    }

    public function mulitipleStorewebsiteDownload(Request $request)
    {
        $webIds = $request->input('website_ids');

        $webIdsArray = explode(',', $webIds);

        if (empty($webIdsArray)) {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'Data is missing']);
        }

        try {
            foreach ($webIdsArray as $webId) {
                $action = 'pull';

                return $this->csvFilePullCommand($webId, $action);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function runCsvSingleCommand(Request $request)
    {
        $action = 'pull';

        return $this->csvFilePullCommand($request->input('id'), $action);
    }

    public function runCsvSinglePushCommand(Request $request): JsonResponse
    {
        $storewebsite = StoreWebsite::find($request->input('id'));
        $action = 'push';
        $storeWebsiteId = $request->input('id');
        $serverName = $storewebsite->server_ip;
        $websiteName = $storewebsite->title;
        $websiteDirectory = $storewebsite->working_directory;
        $storeCode = 'gb-en';

        //get filename
        $fileName = basename($request->input('filename'));

        // Construct and execute the command
        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');
        $command = "bash $scriptsPath"."process_magento_csv.sh -a \"$action\" -s \"$serverName\" -w \"$websiteName\" -d \"$websiteDirectory\" -S \"$storeCode\" -f \"$fileName\" 2>&1";

        $allOutput = [];
        $allOutput[] = $command;
        $result = exec($command, $allOutput);

        //  $allOutput = [
        //     "bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/process_magento_csv.sh -a \"pull\" -s \"85.208.51.101\" -w \"Brands QA\" -d \"/home/brands-qa-1-1/current/\" -S \"gb-en\" -f \"Brands-QA-gb-en.csv\" 2>&1",
        //     '{"status":"success","message":"success","path":"/var/www/erp.theluxuryunlimited.com/storage/app/magento/lang/csv/Brands-QA-gb-en.csv"}'
        // ];

        $response = json_decode($allOutput[1], true);
        Log::info('command:'.$command);
        Log::info('output:'.print_r($allOutput, true));
        Log::info('response:'.print_r($response, true));

        if (is_array($response)) {
            $status = $response['status'];
            $message = $response['message'];
            $path = $response['path'];

            if ($status === 'success') {
                StoreWebsiteCsvFile::create([
                    'storewebsite_id' => $storeWebsiteId,
                    'status' => $status,
                    'message' => $message,
                    'action' => $action,
                    'path' => $path,
                    'user_id' => Auth::user()->id,
                    'command' => $command,
                    'csv_file_id' => $request->input('csvfileId'),
                ]);

                return response()->json(['status' => 'success', 'message' => $message, 'code' => 200]);
            } else {
                StoreWebsiteCsvFile::create([
                    'storewebsite_id' => $storeWebsiteId,
                    'status' => $status,
                    'message' => $message,
                    'action' => $action,
                    'path' => $path,
                    'command' => $command,
                    'user_id' => Auth::user()->id,
                    'csv_file_id' => $request->input('csvfileId'),
                ]);

                Log::info('command:'.$command);
                Log::info('output:'.print_r($allOutput, true));

                return response()->json(['message' => $message, 'code' => 500]);
            }
        } else {
            StoreWebsiteCsvFile::create([
                'storewebsite_id' => $storeWebsiteId,
                'status' => 'fail',
                'message' => 'Invalid JSON response',
                'action' => $action,
                'command' => $command,
                'user_id' => Auth::user()->id,
                'csv_file_id' => $request->input('csvfileId'),
            ]);

            Log::info('command:'.$command);
            Log::info('output:'.print_r($allOutput, true));

            return response()->json(['message' => 'Invalid JSON response', 'code' => 500]);
        }
    }

    public function csvFilePullCommand($id, $action)
    {
        $storewebsite = StoreWebsite::find($id);

        if (! $storewebsite) {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'Invalid website ID']);
        }

        // Retrieve required data for the operation
        $storeWebsiteId = $id;
        $serverName = $storewebsite->server_ip;
        $websiteName = $storewebsite->title;
        $websiteDirectory = $storewebsite->working_directory;
        $storeCode = 'gb-en';
        $fileName = str_replace(' ', '-', $storewebsite->title).'-gb-en.csv';
        $action = $action;

        $pullHistory = new StoreWebsiteCsvPullHistory;
        $pullHistory->user_id = Auth::user()->id;
        $pullHistory->store_website_id = $id;
        $pullHistory->save();

        $languageData = Language::where('status', 1)->where('locale', '!=', 'en')->get();

        $message = '';

        if (! $storewebsite) {
            $message = 'websiteId is required';
        }

        if (! $serverName) {
            $message = 'Serve name is required';
        }

        if (! $websiteName) {
            $message = 'Website name is required';
        }

        if (! $websiteDirectory) {
            $message = 'websiteDirectory is required';
        }

        if ($message !== '') {
            StoreWebsiteCsvFile::create([
                'storewebsite_id' => $storeWebsiteId,
                'status' => 'fail',
                'message' => $message,
                'action' => $action,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json(['code' => 500, 'data' => [], 'message' => $message]);
        }

        // Construct and execute the command
        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');
        // $command = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'process_magento_csv.sh'  . '-a ' . $action . '-s ' . $serverName . ' -w ' . $websiteName . ' -d ' . $websiteDirectory . ' -S ' . $storeCode . ' -f' . $fileName . '';
        $command = "bash $scriptsPath"."process_magento_csv.sh -a \"$action\" -s \"$serverName\" -w \"$websiteName\" -d \"$websiteDirectory\" -S \"$storeCode\" -f \"$fileName\" 2>&1";

        $allOutput = [];
        $allOutput[] = $command;
        $result = exec($command, $allOutput);
        // Below static code for testing purpose.
        $allOutput = [
            'bash /var/www/erp.theluxuryunlimited.com/deployment_scripts/process_magento_csv.sh -a "pull" -s "85.208.51.101" -w "Brands QA" -d "/home/brands-qa-1-1/current/" -S "gb-en" -f "Brands-QA-gb-en.csv" 2>&1',
            '{"status":"success","message":"success","path":"/var/www/erp.theluxuryunlimited.com/storage/app/magento/lang/csv/Brands-QA-gb-en.csv"}',
        ];

        $response = json_decode($allOutput[1], true);
        Log::info('command:'.$command);
        Log::info('output:'.print_r($allOutput, true));
        Log::info('response:'.print_r($response, true));

        if (is_array($response)) {
            $status = $response['status'];
            $message = $response['message'];
            $path = $response['path'];

            // $path = "/var/www/erp.local/storage/app/magento/lang/csv/Brands-QA-gb-en.csv";

            if ($status === 'success') {
                StoreWebsiteCsvFile::create([
                    'storewebsite_id' => $storeWebsiteId,
                    'status' => $status,
                    'message' => $message,
                    'action' => $action,
                    'path' => $path,
                    'user_id' => Auth::user()->id,
                ]);

                if (! file_exists($path)) {
                    try {
                        foreach ($languageData as $language) {
                            $new_file_path = str_replace('-en.cs', '-'.$language->locale.'.cs', $path);
                            $result = $this->translateFile($path, $language->locale, ',');
                            StoreWebsiteCsvFile::create([
                                'storewebsite_id' => $storeWebsiteId,
                                'status' => 'success',
                                'message' => 'csv file created',
                                'action' => $action,
                                'filename' => $new_file_path,
                                'user_id' => Auth::user()->id,
                                'command' => $command,
                            ]);

                            foreach ($result as $translationSet) {
                                try {
                                    $translationDataStored = new GoogleTranslateCsvData;
                                    $translationDataStored->key = $translationSet[0];
                                    $translationDataStored->value = $translationSet[1];
                                    $translationDataStored->standard_value = $translationSet[2];
                                    $translationDataStored->storewebsite_id = $storeWebsiteId;
                                    $translationDataStored->lang_id = $language->id;
                                    $translationDataStored->save();
                                } catch (Exception $e) {
                                    return 'Upload failed: '.$e->getMessage();
                                }
                            }
                        }
                    } catch (Exception $e) {
                        return response()->json(['message' => $e->getMessage(), 'code' => 500]);
                    }
                } else {
                    StoreWebsiteCsvFile::create([
                        'storewebsite_id' => $storeWebsiteId,
                        'status' => $status,
                        'message' => 'File not found',
                        'action' => $action,
                        'path' => $path,
                        'user_id' => Auth::user()->id,
                        'command' => $command,
                    ]);
                    throw new Exception('File not found');
                }

                return response()->json(['status' => 'success', 'message' => $message, 'code' => 200]);
            } else {
                StoreWebsiteCsvFile::create([
                    'storewebsite_id' => $storeWebsiteId,
                    'status' => $status,
                    'message' => $message,
                    'action' => $action,
                    'path' => $path,
                    'user_id' => Auth::user()->id,
                    'command' => $command,
                ]);

                Log::info('command:'.$command);
                Log::info('output:'.print_r($allOutput, true));

                return response()->json(['message' => $message, 'code' => 500]);
            }
        } else {
            StoreWebsiteCsvFile::create([
                'storewebsite_id' => $storeWebsiteId,
                'status' => 'fail',
                'message' => 'Invalid JSON response',
                'action' => $action,
                'user_id' => Auth::user()->id,
                'command' => $command,
            ]);

            Log::info('command:'.$command);
            Log::info('output:'.print_r($allOutput, true));

            return response()->json(['message' => 'Invalid JSON response', 'code' => 500]);
        }
    }

    public function translateFile($path, $language, $delimiter = ',')
    {
        //local testing purpose
        // $path = $path = storage_path('app/magento/lang/csv/Brands-QA-gb-en.csv');

        if (! file_exists($path) || ! is_readable($path)) {
            return false;
        }
        $newCsvData = [];
        $keywordToTranslate = [];

        $new_file_path = str_replace('-en.cs', '-'.$language.'.cs', $path);

        $duplicatePath = $new_file_path;

        if (copy($path, $duplicatePath)) {
            $copiedFilePath = $duplicatePath;
            if (($handle = fopen($copiedFilePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    // Check translation SEPARATE LINE exists or not
                    $checkTranslationTable = Translations::select('text')->where('to', $language)->where('text_original', $data[0])->first();
                    if ($checkTranslationTable) {
                        $data[] = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                    } else {
                        $keywordToTranslate[] = $data[0];
                        $data[] = $data[0];
                    }
                    $newCsvData[] = $data;
                }
                fclose($handle);
            }

            $translateKeyPair = [];
            if (isset($keywordToTranslate) && count($keywordToTranslate) > 0) {
                // Max 128 lines supports for translation per request
                $keywordToTranslateChunk = array_chunk($keywordToTranslate, 100);
                $translationString = [];
                foreach ($keywordToTranslateChunk as $key => $chunk) {
                    try {
                        $googleTranslate = new CommonGoogleTranslateService;
                        $result = $googleTranslate->translate($language, $chunk, true);
                    } catch (Exception $e) {
                        Log::channel('errorlog')->error($e);
                        throw new Exception($e->getMessage());
                    }
                    // $translationString = [...$translationString, ...$result];
                    array_push($translationString, ...$result);
                }

                $insertData = [];
                if (isset($translationString) && count($translationString) > 0) {
                    foreach ($translationString as $key => $value) {
                        $translateKeyPair[$value['input']] = $value['text'];
                        $insertData[] = [
                            'text_original' => $value['input'],
                            'text' => $value['text'],
                            'from' => 'en',
                            'to' => $language,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (! empty($insertData)) {
                    Translations::insert($insertData);
                }
            }

            //Update the csv with Translated data
            if (isset($newCsvData) && count($newCsvData) > 0) {
                for ($i = 0; $i < count($newCsvData); $i++) {
                    $last = array_pop($newCsvData[$i]);
                    array_push($newCsvData[$i], htmlspecialchars_decode($translateKeyPair[$last] ?? $last));
                }

                $handle = fopen($copiedFilePath, 'r+');
                foreach ($newCsvData as $line) {
                    fputcsv($handle, $line, $delimiter, $enclosure = '"');
                }
                fclose($handle);
            }

            return $newCsvData;
        } else {
            return false;
        }
    }

    public function dataViewPage($id): View
    {
        $googleTranslateDatas = GoogleTranslateCsvData::Where('storewebsite_id', $id)->latest()->get();

        return View('googlefiletranslator.googlefiletranlate-list', ['id' => $id, 'googleTranslateDatas' => $googleTranslateDatas]);
    }

    public function pushCsvFile($id, Request $request): View
    {
        $query = StoreWebsiteCsvFile::where('storewebsite_id', $id)
            ->whereNotNull('filename')
            ->where('filename', '!=', ''); // Check for non-empty values

        // Check if 'search_filename' is provided in the request
        if ($request->has('search_filename')) {
            $searchFilename = $request->input('search_filename');
            $query->where(function ($query) use ($searchFilename) {
                $query->where('filename', 'like', '%'.$searchFilename.'%');
            });
        }

        // Check if 'date' is provided in the request
        if ($request->has('date')) {
            $date = $request->input('date');
            $query->whereDate('created_at', $date);
        }

        $query->groupBy('filename');

        $filenames = $query->paginate(25);

        return View('googlefiletranslator.store-website-push-csv-list', ['filenames' => $filenames])->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function pullRequestHistoryShow($id): JsonResponse
    {
        $histories = StoreWebsiteCsvPullHistory::with(['storewebsite', 'user'])->where('store_website_id', $id)->where('user_id', Auth::user()->id)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function pullRequesLogShow(Request $request): JsonResponse
    {
        $action = $request->get('action');

        if ($action == 'pull') {
            $histories = StoreWebsiteCsvFile::with(['storewebsite', 'user'])->where('storewebsite_id', $request->id)->where('user_id', Auth::user()->id)->where('action', $request->action)->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get history status',
                'status_name' => 'success',
            ], 200);
        } else {
            $histories = StoreWebsiteCsvFile::with(['storewebsite', 'user'])->where('csv_file_id', $request->id)->where('user_id', Auth::user()->id)->where('action', $request->action)->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get history status',
                'status_name' => 'success',
            ], 200);
        }
    }

    public function csvFileTruncate(): RedirectResponse
    {
        StoreWebsiteCsvFile::truncate();

        GoogleTranslateCsvData::truncate();

        return redirect()->route('store-website.listing')->withSuccess('data Removed succesfully!');
    }

    public function deleteAdminPasswords(Request $request): JsonResponse
    {
        StoreWebsiteUsers::whereIn('id', $request->ids)->update(['is_deleted' => 1]);

        return response()->json([
            'status' => true,
            'message' => ' column visiblity Added Successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function generateRandomString($length)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = str_shuffle($characters);
        $randomString = substr($randomString, 0, $length);

        return $randomString;
    }

    public function createAdminUrl(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'store_dir' => 'required',
            'server_ip_address' => 'required',
            'store_website_id' => 'required',
            'admin_url' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        // New Script
        $title = $post['title'];
        $admin_url_var = $title.'_admin_'.$this->generateRandomString(6);
        $password = '';
        $store_dir = $post['store_dir'];
        $server_ip_address = $post['server_ip_address'];

        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

        $cmd = "bash $scriptsPath"."update-magento-admin-url.sh -d \"$store_dir\" -s \"$server_ip_address\" -u \"$admin_url_var\" -p \"$password\" 2>&1";

        // NEW Script
        $result = exec($cmd, $output, $return_var);

        //$result = '';

        StoreWebsiteAdminUrl::where('store_website_id', $post['store_website_id'])->update(['status' => 0]);

        $adminurl = new StoreWebsiteAdminUrl;
        $adminurl->created_by = Auth::user()->id;
        $adminurl->store_dir = $post['store_dir'];
        $adminurl->server_ip_address = $post['server_ip_address'];
        $adminurl->store_website_id = $post['store_website_id'];
        $adminurl->website_url = $post['admin_url'];

        if (substr($post['admin_url'], -1) == '/') {
            $adminurl->admin_url = $post['admin_url'].$admin_url_var;
        } else {
            $adminurl->admin_url = $post['admin_url'].'/'.$admin_url_var;
        }

        $adminurl->request_data = $cmd;
        $adminurl->response_data = json_encode($result);
        $adminurl->save();

        return response()->json(['code' => 200, 'message' => 'Admin url has been successfully generated.', 'data' => $adminurl]);
    }

    public function adminUrlBulkGenerate(Request $request): RedirectResponse
    {
        if ($request->has('storewebsiteids') && empty($request->storewebsiteids)) {
            return redirect()->back()->with('error', 'The request parameter ids missing');
        }

        $available = 0;
        $notavailable = 0;
        foreach ($request->storewebsiteids as $id) {
            $storeWebsite = StoreWebsite::where('id', $id)->first();

            if (! empty($storeWebsite['working_directory']) && ! empty($storeWebsite['server_ip']) && ! empty($storeWebsite['website']) && ! empty($storeWebsite['title'])) {
                StoreWebsiteAdminUrl::where('store_website_id', $id)->update(['status' => 0]);

                $admin_url_var = $storeWebsite['title'].'_admin_'.$this->generateRandomString(6);
                $password = '';
                $store_dir = $storeWebsite['working_directory'];
                $server_ip_address = $storeWebsite['server_ip'];

                $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

                $cmd = "bash $scriptsPath"."update-magento-admin-url.sh -d \"$store_dir\" -s \"$server_ip_address\" -u \"$admin_url_var\" -p \"$password\" 2>&1";

                // NEW Script
                $result = exec($cmd, $output, $return_var);

                //$result = '';

                $adminurl = new StoreWebsiteAdminUrl;
                $adminurl->created_by = Auth::user()->id;
                $adminurl->store_dir = $storeWebsite['working_directory'];
                $adminurl->server_ip_address = $storeWebsite['server_ip'];
                $adminurl->store_website_id = $storeWebsite['id'];
                //$adminurl->admin_url = $storeWebsite['website_url'].$admin_url_var;

                $adminurl->website_url = $storeWebsite['website'];

                if (substr($storeWebsite['website'], -1) == '/') {
                    $adminurl->admin_url = $storeWebsite['website'].$admin_url_var;
                } else {
                    $adminurl->admin_url = $storeWebsite['website'].'/'.$admin_url_var;
                }

                $adminurl->request_data = $cmd;
                $adminurl->response_data = json_encode($result);
                $adminurl->save();

                $available = 1;
            } else {
                $notavailable = 1;
            }
        }

        if ($notavailable == 1 && $available == 0) {
            return redirect()->back()->with('error', 'Store website not have directory or ip address or website url or title.');
        } elseif ($notavailable == 0 && $available == 1) {
            return redirect()->back()->with('success', 'Bulk Admin url generate completed, You can check logs individually.');
        } elseif ($notavailable == 1 && $available == 1) {
            return redirect()->back()->with('success', 'Bulk Admin url generate completed, You can check logs individually. and Some Store website not have directory or ip address or website url or title. so that Store Website Admin url not generated.');
        }

        //return response()->json(['success' => true, 'message' => 'Bulk Admin url generate completed, You can check logs individually.']);
    }

    public function magentoMediaSync(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'source_store_website_id' => 'required',
            'dest_store_website_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 400, 'message' => $outputString]);
        }

        $sourceStoreWebsites = StoreWebsite::whereNull('deleted_at')->where('id', $request->source_store_website_id)->first();
        $destStoreWebsites = StoreWebsite::whereNull('deleted_at')->where('id', $request->dest_store_website_id)->first();

        if (! empty($sourceStoreWebsites) && ! empty($destStoreWebsites)) {
            if (! empty($sourceStoreWebsites->server_ip) && ! empty($sourceStoreWebsites->working_directory) && ! empty($destStoreWebsites->server_ip) && ! empty($destStoreWebsites->working_directory)) {
                MagentoMediaSyncJob::dispatch($sourceStoreWebsites, $destStoreWebsites, $request->source_store_website_id, $request->dest_store_website_id, Auth::user()->id)->onQueue('magento_media_sync');

                return response()->json(['code' => 200, 'message' => 'Magento media sync Successfully.']);
            } else {
                return response()->json(['code' => 400, 'message' => 'Something went wrong. Please try again.']);
            }
        } else {
            return response()->json(['code' => 400, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function magentoMediaSyncLogs(Request $request): View
    {
        $title = 'Magento Media Sync | Store Website';

        $MagentoMediaSyncLogs = MagentoMediaSync::with(['user', 'sourcestorewebsite', 'deststorewebsite'])->orderByDesc('id');

        $MagentoMediaSyncLogs = $MagentoMediaSyncLogs->get();

        return view('storewebsite::index-magento-media-sync-logs', compact('title', 'MagentoMediaSyncLogs', 'request'));
    }

    public function varnishFrontendCron(Request $request): JsonResponse
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'varnish_store_website_id' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 400, 'message' => $outputString]);
        }

        $sourceStoreWebsites = StoreWebsite::whereNull('deleted_at')->where('id', $request->varnish_store_website_id)->first();
        $cwd = '';
        $assetsmanager = new AssetsManager;
        if ($sourceStoreWebsites) {
            $assetsmanager = AssetsManager::where('id', $sourceStoreWebsites->assets_manager_id)->first();
        }

        if (! empty($sourceStoreWebsites->server_ip) && ! empty($sourceStoreWebsites->title) && ! empty($assetsmanager->ip_name)) {
            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

            $cmd = "bash $scriptsPath"."varnish_get_details.sh -s \"$assetsmanager->ip_name\" -i \"$sourceStoreWebsites->server_ip\" -w \"$sourceStoreWebsites->title\" 2>&1";

            // NEW Script
            $result = exec($cmd, $output, $return_var);

            Log::info('store command:'.$cmd);
            Log::info('store output:'.print_r($output, true));
            Log::info('store return_var:'.$return_var);

            $useraccess = VarnishStats::create([
                'created_by' => Auth::user()->id,
                'store_website_id' => $request->varnish_store_website_id,
                'assets_manager_id' => $sourceStoreWebsites->assets_manager_id,
                'server_name' => $sourceStoreWebsites->title,
                'server_ip' => $sourceStoreWebsites->server_ip,
                'website_name' => $assetsmanager->ip_name,
                'request_data' => $cmd,
                'response_data' => json_encode($result),
            ]);
        } else {
            return response()->json(['code' => 400, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function varnishLogs(Request $request): View
    {
        $title = 'Varnish Logs | Store Website';

        $VarnishStatsLogs = VarnishStatsLogs::orderByDesc('id');

        $VarnishStatsLogs = $VarnishStatsLogs->paginate(25);

        return view('storewebsite::index-varnish-logs', compact('title', 'VarnishStatsLogs'))->with('i', ($request->input('page', 1) - 1) * 10);
    }
}
