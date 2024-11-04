<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\AssetsManager;
use App\LogRequest;
use App\Models\DataTableColumn;
use App\StoreWebsite;
use App\StoreWebsiteEnvironment;
use App\StoreWebsiteEnvironmentHistory;
use App\StoreWebsiteEnvironmentHistoryStatus;
use App\User;
use App\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StoreWebsiteEnvironmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $title = 'Store Website Environment ';

        $storeWebsites = StoreWebsite::all()->pluck('title', 'id');

        $paths = StoreWebsiteEnvironment::pluck('path', 'path');

        return view('storewebsite::environment.environment', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'paths' => $paths,
        ]);
    }

    public function matrix(Request $request): View
    {
        $title = 'Store Website Environment ';

        $storeWebsites = StoreWebsite::all()->pluck('title', 'id');

        $paths = StoreWebsiteEnvironment::pluck('path', 'path');

        $historyStatuses = StoreWebsiteEnvironmentHistoryStatus::all();

        $env_paths = StoreWebsiteEnvironment::groupBy('path');
        $env_store_websites = StoreWebsiteEnvironment::leftJoin('store_websites as sw', 'sw.id', 'store_website_environments.store_website_id')->select(['store_website_environments.store_website_id', 'sw.title as store_website_name', 'store_website_environments.path']);
        if ($request->store_websites) {
            $env_paths->whereIn('store_website_id', $request->store_websites);
            $env_store_websites->whereIn('store_website_id', $request->store_websites);
        }
        if ($request->paths) {
            //$env_paths->where('path', $request->paths);
            //$env_store_websites->where('path', $request->paths);

            $env_paths->whereIn('path', $request->paths);
            $env_store_websites->whereIn('path', $request->paths);
        }

        //$env_paths = $env_paths->pluck('path', 'id');
        $env_paths = $env_paths->paginate(25);
        $env_store_websites = $env_store_websites->groupBy('store_website_id')->pluck('store_website_name', 'store_website_id');

        $environments = StoreWebsiteEnvironment::with('latestStoreWebsiteEnvironmentHistory')->select('id', 'store_website_id', 'path', 'value')->get()->toArray();

        $result = [];
        $defaultHistoryStatus = StoreWebsiteEnvironmentHistoryStatus::where('name', 'LIKE', '%Default%')->select('color')->first();
        array_walk($environments, function ($value, $key) use (&$result, $defaultHistoryStatus) {
            $value['status_color'] = '';
            // Default color.
            if ($defaultHistoryStatus) {
                $value['status_color'] = $defaultHistoryStatus->color;
            }
            // If history exists then overwrite default color.
            if (isset($value['latest_store_website_environment_history']) && isset($value['latest_store_website_environment_history']['status'])) {
                $historyStatus = StoreWebsiteEnvironmentHistoryStatus::where('name', $value['latest_store_website_environment_history']['status'])->select('color')->first();
                if ($historyStatus) {
                    $value['status_color'] = $historyStatus->color;
                }
            }

            $result[$value['store_website_id']][] = $value;
        });

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'store-website-environment')->first();

        $dynamicColumnsToShowse = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowse = json_decode($hideColumns, true);
        }

        return view('storewebsite::environment.environment-matrix', [
            'title' => $title,
            'storeWebsites' => $storeWebsites,
            'paths' => $paths,
            'env_paths' => $env_paths,
            'env_store_websites' => $env_store_websites,
            'environments' => $result,
            'historyStatuses' => $historyStatuses,
            'dynamicColumnsToShowse' => $dynamicColumnsToShowse,
        ]);
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'store-website-environment')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'store-website-environment';
            $column->column_name = json_encode($request->column_se);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'store-website-environment';
            $column->column_name = json_encode($request->column_se);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function records(Request $request): JsonResponse
    {
        $environments = StoreWebsiteEnvironment::leftJoin('store_websites as sw', 'sw.id', 'store_website_environments.store_website_id');

        if ($request->store_website_id != null) {
            $environments = $environments->whereIn('store_website_environments.store_website_id', $request->store_website_id);
        }
        if ($request->paths != null) {
            $environments = $environments->whereIn('store_website_environments.path', $request->paths);
        }

        $environments = $environments->orderByDesc('store_website_environments.id')->select(['store_website_environments.*', 'sw.title as store_website_name'])->paginate();

        $items = $environments->items();

        $recItems = [];
        foreach ($items as $item) {
            $attributes = $item->getAttributes();
            $recItems[] = $attributes;
        }

        return response()->json(['code' => 200, 'pageUrl' => $request->page_url, 'data' => $recItems, 'total' => $environments->total(),
            'pagination' => (string) $environments->links(),
        ]);
    }

    /**
     * records Page
     *
     * @param  Request  $request  [description]
     */
    // Old Concept - Not need
    // public function updateValue(Request $request)
    // {
    //     $post = $request->all();
    //     $id = $request->get('id', 0);

    //     $params = [
    //         'value'           => 'required',
    //         'command'           => 'required',
    //     ];
    //     $validator = Validator::make($post, $params);

    //     if ($validator->fails()) {
    //         $outputString = '';
    //         $messages = $validator->errors()->getMessages();
    //         foreach ($messages as $k => $errr) {
    //             foreach ($errr as $er) {
    //                 $outputString .= "$k : " . $er . '<br>';
    //             }
    //         }

    //         return response()->json(['code' => 500, 'error' => $outputString]);
    //     }

    //     $records = StoreWebsiteEnvironment::find($id);

    //     if (! $records) {
    //         return response()->json(['code' => 500, 'error' => 'Store Website Environment data not found!']);
    //     }
    //     $environment_id=$records->id;
    //     $old_value=$records->value;
    //     $new_value=$request->value;
    //     $updated_by=$request->user_id;
    //     $cmd=$request->command;
    //     $store_website_id=$records->store_website_id;
    //     $path=$records->path;
    //     //$records->save();

    //     Log::info("Start Environment Pushed");
    //     $storeWebsite=StoreWebsite::where('id', $store_website_id)->first();
    //     $cwd='';
    //     $assetsmanager = new AssetsManager;
    //     if($storeWebsite){
    //         $cwd=$storeWebsite->working_directory;
    //         $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
    //     }

    //     if($assetsmanager && $assetsmanager->client_id!='')
    //     {

    //         $client_id=$assetsmanager->client_id;
    //         $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
    //         $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");

    //         $startTime = date('Y-m-d H:i:s', LARAVEL_START);

    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL,$url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_POST, 1);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    //         $parameters = [
    //             'command' => $cmd,
    //             'cwd' => $cwd,
    //             'is_sudo' => true,
    //             'timeout_sec' => 300,
    //         ];
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

    //         $headers = [];
    //         $headers[] = 'Authorization: Basic '.$key;
    //         $headers[] = 'Content-Type: application/json';
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         $result = curl_exec($ch);

    //         $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //         LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($result),
    //         $httpcode,\Modules\StoreWebsite\Http\Controllers\StoreWebsiteEnvironmentController::class, 'update');

    //         Log::info("API result: ".$result);
    //         Log::info("API Error Number: ".curl_errno($ch));
    //         if (curl_errno($ch)) {
    //             Log::info("API Error: ".curl_error($ch));

    //             StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => curl_error($ch)]);

    //             return response()->json(['code' => 500, 'error' =>curl_error($ch)]);
    //         }

    //         $response = json_decode($result);

    //         curl_close($ch);

    //         if(isset($response->errors)){
    //             $message='';
    //             foreach($response->errors as $error){
    //                 $message.=" ".$error->code.":".$error->title.":".$error->detail;
    //             }

    //             StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => $message]);
    //             Log::info($message);
    //             return response()->json(['code' => 500, 'error' => $message]);

    //         }else{
    //             if(isset($response->data) && isset($response->data->jid) ){
    //                 $job_id=$response->data->jid;
    //                 $status="Success";

    //                 StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Success", 'response' => 'Success', 'job_id' => $job_id]);
    //                 $records->value=$new_value;
    //                 $records->save();
    //                 Log::info("Job Id:".$job_id);
    //                 return response()->json(['code' => 200, 'data' => $records,'message'=>'Request pushed on website successfully']);
    //             }else{
    //                 StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' =>"Job Id not found in response"]);

    //                 Log::info("Job Id not found in response!");
    //                 return response()->json(['code' => 500, 'error' => 'Job Id not found in response!']);
    //             }
    //         }
    //     }else{

    //         StoreWebsiteEnvironmentHistory::create(['environment_id' => $environment_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'key' => $path, 'old_value' => $old_value, 'new_value' => $new_value, 'command' => $cmd, 'status' => "Error", 'response' => "Assets Manager & Client id not found the Store Website!"]);

    //         Log::info("Assets Manager & Client id not found the Store Website!");

    //         return response()->json(['code' => 500, 'error' => 'Assets Manager & Client id not found the Store Website!']);
    //     }

    //     Log::info("End Environment Pushed");

    //     return response()->json(['code' => 200, 'data' => $records,'message'=>'update']);
    // }

    // New Concept
    public function updateValue(Request $request): JsonResponse
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        $params = [
            'value' => 'required',
        ];

        $validator = Validator::make($post, $params);
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

        $records = StoreWebsiteEnvironment::find($id);
        if (! $records) {
            return response()->json(['code' => 500, 'error' => 'Store Website Environment data not found!']);
        }

        $store_website_id = $records->store_website_id;
        $storeWebsite = StoreWebsite::where('id', $store_website_id)->first();
        if (! $storeWebsite) {
            return response()->json(['code' => 500, 'error' => 'Store Website not found!']);
        }

        Log::info('Start Environment Pushed');
        $environment_id = $records->id;
        $old_value = $records->value;
        $new_value = $request->value;
        $updated_by = $request->user_id;
        $path = $records->path;
        $encodeValue = base64_encode($new_value);
        $cwd = $storeWebsite->working_directory;
        $title = $storeWebsite->title;
        $serverIP = $storeWebsite->server_ip;
        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

        $cmd = "bash $scriptsPath"."magento-env-update.sh -w \"$title\" -s \"$serverIP\" -d \"$cwd\" -p \"$path\" -v \"$encodeValue\" 2>&1";
        $output = [];
        exec($cmd, $output, $statusCode);
        Log::info('Command Status'.$statusCode);
        Log::info('Command OutPut'.print_r($output, true));

        if ($statusCode == 0) {
            // Assuming the output is stored in $output variable as an array
            $result = json_decode($output[0], true); // Decode the JSON string into an associative array

            // Check if the JSON decoding was successful and if it contains the necessary keys
            if (is_array($result) && isset($result['status'])) {
                $status = $result['status'];
                // Handle the values accordingly
                if ($status === 'true') {
                    $statusText = 'Success';
                    $response = $output[0]; // This is json string
                } else {
                    $statusText = 'Error';
                    $response = $output[0]; // This is json string
                }
            } else {
                // Invalid output format
                $statusText = 'Error';
                $response = 'Invalid output format';
            }
        } else {
            $statusText = 'Error';
            $response = 'Command Executed Failed';
        }

        StoreWebsiteEnvironmentHistory::create([
            'environment_id' => $environment_id,
            'store_website_id' => $store_website_id,
            'updated_by' => $updated_by,
            'key' => $path,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'command' => $cmd,
            'status' => $statusText,
            'response' => $response,
        ]);
        Log::info('End Environment Pushed');

        return response()->json(['code' => 200, 'data' => $records, 'message' => 'update']);
    }

    /**
     * records Page
     *
     * @param  Request  $request  [description]
     */
    public function store(Request $request): JsonResponse
    {
        $post = $request->all();
        $id = $request->get('id', 0);

        $params = [
            'path' => 'required',
            'store_website_id' => 'required',
            'command' => 'required',
        ];
        if (empty($id)) {
            $params['value'] = 'required';
        }
        $validator = Validator::make($post, $params);

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

        $records = StoreWebsiteEnvironment::find($id);

        if (! $records) {
            $records = new StoreWebsiteEnvironment;
        }
        $records->fill($post);
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $StoreWebsiteEnvironment = StoreWebsiteEnvironment::where('id', $id)->first();

        if ($StoreWebsiteEnvironment) {
            return response()->json(['code' => 200, 'data' => $StoreWebsiteEnvironment]);
        }

        return response()->json(['code' => 500, 'error' => 'Data Not Found']);
    }

    public function history(Request $request, $id): JsonResponse
    {
        $histories = StoreWebsiteEnvironmentHistory::select('store_website_environment_histories.*', 'u.name AS userName')->leftJoin('users AS u', 'u.id', 'store_website_environment_histories.updated_by')->where('environment_id', $id)->latest()->get();

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function storeEnvironmentHistoryStatus(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = StoreWebsiteEnvironmentHistoryStatus::updateOrCreate([
            'name' => $input['name'],
        ], [
            'color' => $input['color'],
        ]);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function updateEnvironmentHistoryStatus(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $cronStatus = StoreWebsiteEnvironmentHistoryStatus::find($key);
            $cronStatus->color = $value;
            $cronStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
}
