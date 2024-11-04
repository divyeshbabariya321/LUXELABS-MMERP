<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\LogRequest;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use App\MagentoMulitipleCommand;
use App\Models\MagentoCronList;
use App\Models\MagentoCronRunLog;
use App\Models\MagentoMultipleCron;
use App\MysqlCommandRunLog;
use App\Setting;
use App\StoreWebsite;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MagentoCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $limit = Setting::get('pagination') ?? config('site.pagination.limit');
            $magentoCommand = MagentoCommand::orderByDesc('created_at')->paginate($limit)->appends(request()->except(['page']));
            $magentoCommandListArray = MagentoCommand::whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command_type', 'command_name')->toArray();
            $allMagentoCommandListArray = MagentoCommand::select(
                DB::raw("CONCAT(COALESCE(`command_name`,''),' (',COALESCE(`command_type`,''),')') AS command"), 'id', 'command_type')->whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command', 'id')->toArray();

            $assetsmanager = AssetsManager::all();
            $websites = StoreWebsite::all();
            $users = User::all();

            return view('magento-command.index', compact('magentoCommand', 'websites', 'users', 'magentoCommandListArray', 'assetsmanager', 'allMagentoCommandListArray'));
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    public function index_command()
    {
        try {
            $limit = Setting::get('pagination') ?? config('site.pagination.limit');
            $magentoCommand = MagentoCronList::orderByDesc('created_at')->paginate($limit)->appends(request()->except(['page']));

            $allMagentoCommandListArray = MagentoCronList::get()->pluck('cron_name', 'id')->toArray();

            return view('magento-command.index_command', compact('magentoCommand', 'allMagentoCommandListArray'));
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    public function storecommand(Request $request): JsonResponse
    {
        try {
            if (isset($request->id) && $request->id > 0) {
                $mCom = MagentoCronList::where('id', $request->id)->first();
            } else {
                $mCom = new MagentoCronList;
            }

            if (! empty($request->cron_name) && ! empty($request->frequency)) {
                $mCom->cron_name = $request->cron_name;
                $mCom->frequency = $request->frequency;
                $mCom->save();
            }

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getMagentoCommand(Request $request): JsonResponse
    {
        $magentoCommand = MagentoCommand::whereNotNull('id')->orderByDesc('id');
        $magentoCommandListArray = MagentoCommand::whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command_type', 'command_name')->toArray();
        if (! empty($request->website)) {
            $magentoCommand->whereIn('website_ids', $request->website);
        }
        if (! empty($request->command_name)) {
            $magentoCommand->whereIn('command_name', $request->command_name);
        }
        if (! empty($request->user_id)) {
            $magentoCommand->whereIn('user_id', $request->user_id);
        }
        $limit = Setting::get('pagination') ?? config('site.pagination.limit');
        $magentoCommand = $magentoCommand->paginate($limit);
        $users = User::all();
        $websites = StoreWebsite::all();
        $allMagentoCommandListArray = MagentoCommand::select(
            DB::raw("CONCAT(COALESCE(`command_name`,''),' (',COALESCE(`command_type`,''),')') AS command"), 'id', 'command_type')->whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command', 'id')->toArray();
        $assetsmanager = AssetsManager::all();

        $html = view('partials.modals.magento-commands-modal-html')->with(['magentoCommand' => $magentoCommand, 'websites' => $websites, 'users' => $users, 'magentoCommandListArray' => $magentoCommandListArray, 'assetsmanager' => $assetsmanager, 'allMagentoCommandListArray' => $allMagentoCommandListArray])->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Content render']);
    }

    public function search(Request $request): View
    {
        $magentoCommand = MagentoCommand::whereNotNull('id');
        $magentoCommandListArray = MagentoCommand::whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command_type', 'command_name')->toArray();
        if (! empty($request->website)) {
            $magentoCommand->whereIn('website_ids', $request->website);
        }
        if (! empty($request->command_name)) {
            $magentoCommand->whereIn('command_name', $request->command_name);
        }
        if (! empty($request->user_id)) {
            $magentoCommand->whereIn('user_id', $request->user_id);
        }
        $limit = Setting::get('pagination') ?? config('site.pagination.limit');
        $magentoCommand = $magentoCommand->paginate($limit);
        $users = User::all();
        $websites = StoreWebsite::all();
        $allMagentoCommandListArray = MagentoCommand::select(
            DB::raw("CONCAT(COALESCE(`command_name`,''),' (',COALESCE(`command_type`,''),')') AS command"), 'id', 'command_type')->whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command', 'id')->toArray();
        $assetsmanager = AssetsManager::all();

        return view('magento-command.index', compact('magentoCommandListArray', 'magentoCommand', 'websites', 'users', 'assetsmanager', 'allMagentoCommandListArray'));
    }

    public function searchcron(Request $request): View
    {
        $magentoCommand = MagentoCronList::whereNotNull('id');
        if (! empty($request->command_name)) {
            $magentoCommand->whereIn('id', $request->command_name);
        }
        $limit = Setting::get('pagination') ?? config('site.pagination.limit');
        $magentoCommand = $magentoCommand->paginate($limit);

        $allMagentoCommandListArray = MagentoCronList::get()->pluck('cron_name', 'id')->toArray();

        return view('magento-command.index_command', compact('magentoCommand', 'allMagentoCommandListArray'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userPermissions = array_filter((! empty($request->user_permission) ? $request->user_permission : []));

            if (isset($request->id) && $request->id > 0) {
                $mCom = MagentoCommand::where('id', $request->id)->first();

                if (! empty($mCom->user_permission)) {
                    $editUserPermissions = explode(',', $mCom->user_permission);

                    $userPermissions = array_unique(array_merge($userPermissions, $editUserPermissions));
                }
            } else {
                $mCom = new MagentoCommand;
                $type = 'Created';
                $loginUserId = Auth::user()->id ?? '';

                if (strlen($loginUserId) > 0 && ! in_array($loginUserId, $userPermissions)) {
                    array_push($userPermissions, $loginUserId);
                }
            }

            $mCom->user_id = Auth::user()->id ?? '';
            $mCom->website_ids = isset($request->websites_ids) ? implode(',', $request->websites_ids) : $mCom->websites_ids;
            $mCom->command_name = $request->command_name;
            $mCom->command_type = $request->command_type;
            $mCom->working_directory = $request->working_directory;
            $mCom->assets_manager_id = $request->assets_manager_id;
            $mCom->user_permission = implode(',', array_filter($userPermissions));
            $mCom->save();

            if (! empty($request->command_name) && ! empty($request->websites_ids) && ! empty($request->working_directory)) {
                $requestData['command'] = $request->command_type;

                $storeWebsiteData = StoreWebsite::where('id', $request->websites_ids)->first();

                if (! empty($storeWebsiteData)) {
                    $requestData['server'] = $storeWebsiteData->server_ip;
                    $requestData['dir'] = $request->working_directory;
                }

                Log::info('magento command request data'.print_r($requestData, true));

                if (! empty($requestData['command']) && ! empty($requestData['server']) && ! empty($requestData['dir']) && ! empty($request->command_name) && ! empty($request->command_type)) {
                    $requestJson = json_encode($requestData);

                    // Initialize cURL session
                    $ch = curl_init();

                    // Set cURL options for a POST request
                    curl_setopt($ch, CURLOPT_URL, 'https://s10.theluxuryunlimited.com:5000/execute');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestJson);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Content-Length: '.strlen($requestJson),
                    ]);

                    // Execute cURL session and store the response in a variable
                    $response = curl_exec($ch);

                    // Check for cURL errors
                    if (curl_errno($ch)) {
                        echo 'Curl error: '.curl_error($ch);
                    }

                    // Close cURL session
                    curl_close($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    $responseData = json_decode($response);

                    if ($responseData->success == 1) {
                        $status = 'Success';
                    }

                    Log::info('Test response'.print_r($response, true));

                    MagentoCommandRunLog::create([
                        'command_id' => $mCom->id,
                        'user_id' => Auth::user()->id ?? '',
                        'website_ids' => $request->websites_ids[0],
                        'server_ip' => $storeWebsiteData->server_ip,
                        'request' => json_encode($requestData),
                        'response' => $response,
                        'command_name' => $request->command_name,
                        'command_type' => $request->command_type,
                        'job_id' => $httpcode,
                    ]
                    );
                }
            }

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     */
    public function runCommand(Request $request): JsonResponse
    {
        try {
            Artisan::call('command:MagentoCreatRunCommand', ['id' => $request->id]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function magentoCommandLogs()
    {
        try {
            $limit = Setting::get('pagination') ?? config('site.pagination.limit');
            $magentoCommandRunLog = MagentoCommandRunLog::with(['website', 'user', 'command'])->latest()->paginate($limit)->appends(request()->except(['page']));

            return view('magento-command.magento-command-logs', compact('magentoCommandRunLog'));
        } catch (Exception $e) {
            $e->getMessage();

            return redirect()->back()->withErrors('Something went wrong!');
        }
    }

    public function runOnMultipleWebsite(Request $request): JsonResponse
    {
        $command_id = $request->command_id;
        $websites_ids = $request->websites_ids;

        MagentoMulitipleCommand::create([
            'website_ids' => json_encode($websites_ids),
            'command_id' => $command_id,
            'user_id' => Auth::user()->id,

        ]);

        try {
            Artisan::call('command:MagentoRunCommandOnMultipleWebsite', ['id' => $command_id, 'websites_ids' => $websites_ids]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully! Please check the command\'s preview response for more information']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function runMagentoCommand(Request $request): JsonResponse
    {
        $command_id = $request->id;
        $websites_ids = $request->websites_ids;

        MagentoMultipleCron::create([
            'website_ids' => json_encode($websites_ids),
            'command_id' => $command_id,
            'user_id' => Auth::user()->id,

        ]);

        try {
            Artisan::call('command:MagentoRunCronOnMultipleWebsite', ['id' => $command_id, 'websites_ids' => $websites_ids]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully! Please check the command\'s preview response for more information']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function runMySqlQuery(Request $request): JsonResponse
    {
        if (! isset($request->websites_ids) && empty($request->websites_ids)) {
            return response()->json(['code' => 500, 'message' => 'Please Select Websites']);
        }
        if (! isset($request->command) && $request->command == '') {
            return response()->json(['code' => 500, 'message' => 'Please Entr MySql Query']);
        }

        $command = $request->command;
        $websites_ids = $request->websites_ids;
        $cmd = 'mysql -u root -e "'.$command.'"';
        try {
            Log::info('Start Run Mysql Query');
            $isError = 0;
            foreach ($websites_ids as $websites_id) {
                if ($websites_id == 'ERP') {
                    Log::info('Start Rum Mysql Query for website_id: ERP');
                    $job_id = '';
                    $assetsmanager = AssetsManager::where('name', 'like', '%ERP%')->first();
                    if ($assetsmanager && $assetsmanager->client_id != '') {
                        Log::info('client_id: '.$assetsmanager->client_id);
                        $client_id = $assetsmanager->client_id;
                        $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/'.$client_id.'/scripts';
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                            'script' => base64_encode($cmd),
                            'is_sudo' => true,
                        ]));

                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        Log::info('API result: '.$result);
                        if (curl_errno($ch)) {
                            Log::info('API Error: '.curl_error($ch));
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => curl_error($ch),
                                    'status' => 'Error',
                                ]
                            );
                        }
                        Log::info('API Response: '.$result);
                        $response = json_decode($result);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode(['script' => base64_encode($cmd), 'is_sudo' => true]), json_decode($response), $httpcode, MagentoCommandController::class, 'commandHistoryLog');

                        curl_close($ch);

                        if (isset($response->errors)) {
                            $message = '';
                            foreach ($response->errors as $error) {
                                $message .= ' '.$error->code.':'.$error->title.':'.$error->detail;
                            }
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => $message,
                                    'status' => 'Error',
                                ]
                            );
                        }

                        if (isset($response->data) && isset($response->data->jid)) {
                            $job_id = $response->data->jid;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => $result,
                                    'job_id' => $job_id,
                                    'status' => 'Success',
                                ]
                            );
                        } else {
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => 'ERP',
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => 'Job Id not found in response',
                                    'status' => 'Error',
                                ]
                            );
                        }
                    } else {
                        $isError = 1;
                        MysqlCommandRunLog::create(
                            [
                                'user_id' => Auth::user()->id ?? '',
                                'website_ids' => 'ERP',
                                'server_ip' => '',
                                'command' => $cmd,
                                'response' => 'Assets Manager & Client id not found for this website!',
                                'status' => 'Error',
                            ]
                        );
                    }

                    Log::info('End Rum Mysql Query for website_id: ERP');
                } else {
                    Log::info('Start Rum Mysql Query for website_id:'.$websites_id);
                    $websites = StoreWebsite::where('id', $websites_id)->first();
                    if (! $websites) {
                        $isError = 1;
                        MysqlCommandRunLog::create(
                            [
                                'user_id' => Auth::user()->id ?? '',
                                'website_ids' => $websites_id,
                                'server_ip' => '',
                                'command' => $cmd,
                                'response' => 'The website is not found!',
                                'status' => 'Error',
                            ]
                        );
                    }
                    $assetsmanager = AssetsManager::where('id', $websites->assets_manager_id)->first();
                    if ($assetsmanager && $assetsmanager->client_id != '') {
                        Log::info('client_id: '.$assetsmanager->client_id);
                        $client_id = $assetsmanager->client_id;
                        $url = getenv('MAGENTO_COMMAND_API_URL');
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                            'command' => $cmd,
                            'dir' => $websites->working_directory,
                            'is_sudo' => true,
                            'server' => $website->server_ip,
                        ]));

                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        Log::info('API result: '.$result);
                        if (curl_errno($ch)) {
                            Log::info('API Error: '.curl_error($ch));
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => $websites_id,
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => curl_error($ch),
                                    'status' => 'Error',
                                ]
                            );
                        }
                        Log::info('API Response: '.$result);
                        $response = json_decode($result); //response decoded
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $parameters = [];
                        LogRequest::log($startTime, $url, 'POST', json_encode(['script' => base64_encode($cmd),
                            'is_sudo' => true]),
                            $response, $httpcode, MagentoCommandController::class, 'runMySqlQuery');

                        curl_close($ch);

                        if (isset($response->errors)) {
                            $message = '';
                            foreach ($response->errors as $error) {
                                $message .= ' '.$error->code.':'.$error->title.':'.$error->detail;
                            }
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => $websites_id,
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => $message,
                                    'status' => 'Error',
                                ]
                            );
                        }

                        if (isset($response->data) && isset($response->data->jid)) {
                            $job_id = $response->data->jid;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => $websites_id,
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => $result,
                                    'job_id' => $job_id,
                                    'status' => 'Success',
                                ]
                            );
                        } else {
                            $isError = 1;
                            MysqlCommandRunLog::create(
                                [
                                    'user_id' => Auth::user()->id ?? '',
                                    'website_ids' => $websites_id,
                                    'server_ip' => $assetsmanager->ip,
                                    'command' => $cmd,
                                    'response' => 'Job Id not found in response',
                                    'status' => 'Error',
                                ]
                            );
                        }
                    } else {
                        $isError = 1;
                        MysqlCommandRunLog::create(
                            [
                                'user_id' => Auth::user()->id ?? '',
                                'website_ids' => $websites_id,
                                'server_ip' => '',
                                'command' => $cmd,
                                'response' => 'Assets Manager & Client id not found for this website!',
                                'status' => 'Error',
                            ]
                        );
                    }
                    Log::info('End Rum Mysql Query for website_id:'.$websites_id);
                }
            }
            Log::info('End Run Mysql Query');
            if ($isError) {
                return response()->json(['code' => 500, 'message' => 'MySql Query Run Failed! Please check the command\'s preview response for more information']);
            }

            return response()->json(['code' => 200, 'message' => 'MySql Query Run successfully! Please check the command\'s preview response for more information']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::info('Run Mysql Query Error '.$msg);

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function mySqlQueryLogs()
    {
        try {
            $limit = Setting::get('pagination') ?? config('site.pagination.limit');
            $mysqlCommandRunLog = MysqlCommandRunLog::with(['website', 'user'])->latest()->paginate($limit)->appends(request()->except(['page']));

            $assetsmanager = AssetsManager::all();
            $websites = StoreWebsite::all();
            $users = User::all();
            foreach ($mysqlCommandRunLog as $logs) {
                if ($logs->website_ids != '' && $logs->job_id != '') {
                    if ($logs->website_ids == 'ERP') {
                        $assetsmanager = AssetsManager::where('name', 'like', '%ERP%')->first();
                    } else {
                        $storeWebsite = StoreWebsite::where('id', $logs->website_ids)->first();
                        $assetsmanager = new AssetsManager;
                        if ($storeWebsite) {
                            $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                        }
                    }

                    if ($assetsmanager && $assetsmanager->client_id != '') {
                        $client_id = $assetsmanager->client_id;
                        $job_id = $logs->job_id;
                        $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/'.$client_id.'/commands/'.$job_id;
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, Controllers\MagentoCommandController::class, 'mySqlQueryLogs');

                        $response = json_decode($result);
                        Log::info('API Response: '.$result);
                        if (isset($response->data) && isset($response->data->result)) {
                            $logs->status = $response->data->status;
                            $result = $response->data->result;
                            $message = '';
                            if (isset($result->stdout) && $result->stdout != '') {
                                $message .= 'Output: '.$result->stdout;
                            }
                            if (isset($result->stderr) && $result->stderr != '') {
                                $message .= 'Error: '.$result->stderr;
                            }
                            if (isset($result->summary) && $result->summary != '') {
                                $message .= 'summary: '.$result->summary;
                            }
                            if ($message != '') {
                                $logs->response = $message;
                            }
                        }

                        curl_close($ch);
                    }
                }
            }

            return view('magento-command.mysql-query-logs', compact('mysqlCommandRunLog', 'websites', 'users'));
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     */
    public function edit(Request $request): JsonResponse
    {
        try {
            $magentoCom = MagentoCommand::find($request->id);
            $websites = StoreWebsite::all();
            $ops = '';
            foreach ($websites as $website) {
                $selected = '';
                if ($magentoCom->website_ids == $website->id) {
                    $selected = 'selected';
                }
                $ops .= '<option value="'.$website->id.'" '.$selected.'>'.$website->name.'</option>';
            }

            return response()->json(['code' => 200, 'data' => $magentoCom, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function editcommand(Request $request): JsonResponse
    {
        try {
            $magentoCom = MagentoCronList::find($request->id);
            $websites = StoreWebsite::all();
            $ops = '';
            foreach ($websites as $website) {
                $selected = '';
                if ($magentoCom->website_ids == $website->id) {
                    $selected = 'selected';
                }
                $ops .= '<option value="'.$website->id.'" '.$selected.'>'.$website->name.'</option>';
            }

            return response()->json(['code' => 200, 'data' => $magentoCom, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function commandHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = MagentoCommandRunLog::select('magento_command_run_logs.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'magento_command_run_logs.user_id')
                ->where('command_id', '=', $request->id)->orderByDesc('id')->get();

            foreach ($postHis as $logs) {
                $logs->status = '';
                if ($logs->website_ids != '' && $logs->job_id != '') {
                    $storeWebsite = StoreWebsite::where('id', $logs->website_ids)->first();
                    $assetsmanager = new AssetsManager;
                    if ($storeWebsite) {
                        $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                    }

                    if ($assetsmanager && $assetsmanager->client_id != '') {
                        $client_id = $assetsmanager->client_id;
                        $job_id = $logs->job_id;
                        $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/'.$client_id.'/commands/'.$job_id;
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, MagentoCommandController::class, 'commandHistoryLog');
                        $response = json_decode($result);
                        Log::info('API Response: '.$result);
                        if (isset($response->data) && isset($response->data->result)) {
                            $logs->status = $response->data->status;
                            $result = $response->data->result;
                            $message = '';
                            if (isset($result->stdout) && $result->stdout != '') {
                                $message .= 'Output: '.$result->stdout;
                            }
                            if (isset($result->stderr) && $result->stderr != '') {
                                $message .= 'Error: '.$result->stderr;
                            }
                            if (isset($result->summary) && $result->summary != '') {
                                $message .= 'summary: '.$result->summary;
                            }
                            if ($message != '') {
                                $logs->response = $message;
                            }
                        }

                        curl_close($ch);
                    }
                }
            }

            $html = view('partials.command_history_rows', ['postHis' => $postHis])->render();

            return response()->json(['code' => 200, 'html' => $html, 'data' => $postHis, 'message' => 'Listed successfully!!!']);

        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Opps! Something went wrong, Please try again.']);
        }
    }

    public function cronHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = MagentoCronRunLog::select('magento_cron_run_logs.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'magento_cron_run_logs.user_id')
                ->where('command_id', '=', $request->id)->orderByDesc('id')->get();

            foreach ($postHis as $logs) {
                $logs->status = '';
                if ($logs->website_ids != '' && $logs->job_id != '') {
                    $storeWebsite = StoreWebsite::where('id', $logs->website_ids)->first();
                    $assetsmanager = new AssetsManager;
                    if ($storeWebsite) {
                        $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                    }

                    if ($assetsmanager && $assetsmanager->client_id != '') {
                        $client_id = $assetsmanager->client_id;
                        $job_id = $logs->job_id;
                        $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/'.$client_id.'/commands/'.$job_id;
                        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($result), $httpcode, MagentoCommandController::class, 'cronHistoryLog');

                        $response = json_decode($result);
                        Log::info('API Response: '.$result);
                        if (isset($response->data) && isset($response->data->result)) {
                            $logs->status = $response->data->status;
                            $result = $response->data->result;
                            $message = '';
                            if (isset($result->stdout) && $result->stdout != '') {
                                $message .= 'Output: '.$result->stdout;
                            }
                            if (isset($result->stderr) && $result->stderr != '') {
                                $message .= 'Error: '.$result->stderr;
                            }
                            if (isset($result->summary) && $result->summary != '') {
                                $message .= 'summary: '.$result->summary;
                            }
                            if ($message != '') {
                                $logs->response = $message;
                            }
                        }

                        curl_close($ch);
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $postman = MagentoCommand::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $postman, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function deletecommand(Request $request): JsonResponse
    {
        try {
            $postman = MagentoCronList::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $postman, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function userPermission(Request $request): JsonResponse
    {
        try {
            $magentoCommands = MagentoCommand::where('website_ids', 'like', '%,'.$request->persmission_website.',%')
                ->orWhere('website_ids', 'like', '%,'.$request->persmission_website.'%')
                ->orWhere('website_ids', 'like', '%'.$request->persmission_website.',%')
                ->orWhere('website_ids', $request->persmission_website)
                ->get();

            foreach ($magentoCommands as $magentoCommand) {
                $userPermissions = array_filter(explode(',', $magentoCommand->user_permission));

                if (! in_array($request->persmission_user, $userPermissions)) {
                    array_push($userPermissions, $request->persmission_user);
                }
                $userPermissionFormatted = implode(',', array_filter($userPermissions));

                MagentoCommand::where('id', '=', $magentoCommand->id)->update(
                    [
                        'user_permission' => $userPermissionFormatted,
                    ]
                );
            }

            return response()->json(['code' => 200, 'message' => 'Permission Updated successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Magento Command User permission Error => '.json_decode($e).' #id #'.$request->id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getMulitipleCommands(): View
    {
        $mulitipleCommands = MagentoMulitipleCommand::with(['website', 'user', 'command'])->paginate(25);
        $magentoCommands = MagentoCommand::get();
        $websitesArray = StoreWebsite::get()->pluck('website', 'id')->toArray();

        return view('magento-command.multiple-command-list', compact('mulitipleCommands', 'magentoCommands', 'websitesArray'));
    }
}
