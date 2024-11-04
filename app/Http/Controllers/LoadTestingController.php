<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoadTestingRequest;
use App\Jobs\runJMeterTest;
use App\Models\JmeterResultApdex;
use App\Models\JmeterResultError;
use App\Models\JmeterResultStatistic;
use App\Models\JmeterResultTop5Error;
use App\Models\loadTesting;
use App\Setting;
use App\Traits\JMeter;
use App\Traits\JMeterHtml;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoadTestingController extends Controller
{
    use JMeter, JMeterHtml;

    public function index(Request $request): View
    {
        Log::info('index : Get all load Testing Record');
        $templateArr = loadTesting::all();
        $loadTestJSON = loadTesting::get()->toArray();

        return view('load-testing.index', compact('templateArr', 'loadTestJSON'));
    }

    public function create(CreateLoadTestingRequest $request): JsonResponse
    {
        try {
            Log::info('create : Get load Testing Record');
            LoadTesting::create($request->all());

            return response()->json(['code' => 1, 'message' => 'Load Testing Data Created Successfully!']);
        } catch (\Exeception $e) {
            Log::error('Failed to create file Error :'.$e->getMessage());

            return response()->json(['code' => 0, 'message' => 'Failed to create base64 code of JML file']);
        }
    }

    public function response(): JsonResponse
    {
        try {
            $records = LoadTesting::paginate(Setting::get('pagination'));
            Log::info('Response : Get load Testing Record ');

            return response()->json([
                'code' => 1,
                'result' => $records,
                'pagination' => (string) $records->links(),
            ]);
        } catch (\Exeception $e) {
            Log::error('Failed to create file Error :'.$e->getMessage());

            return response()->json(['code' => 0, 'message' => 'Failed to create base64 code of JML file']);
        }
    }

    public function submitRequest($loadTestingId): JsonResponse
    {

        try {
            $getLoadTesting = loadTesting::find($loadTestingId);
            Log::info('submitRequest : Get load Testing Record');
            $getNewFilePath = $this->updateNumThreads($getLoadTesting);
            Log::info('submitRequest : Update number threads');
            if ($getNewFilePath->original['success']) {
                $getLoadTesting->status = 1;
                $getLoadTesting->jmx_file_path = $getNewFilePath->original['file_path']; //$getNewFilePath->original['base64_content'];
                $getLoadTesting->save();
                Log::info('Record updated with base64 code and status changes to in pnprocess');
                $jmeterApiResponse = $this->sendDataToJmeterApi($loadTestingId);
                if ($jmeterApiResponse->original['success']) {
                    Log::info('submitRequest :Load Testing proccessed!');

                    return response()->json(['code' => 1,  'message' => 'Load Testing proccessed!']);
                } else {
                    Log::info('submitRequest : Failed To Process Load Test!');

                    return response()->json(['code' => 0, 'message' => 'Failed To Process Load Test!']);
                }
            } else {
                return response()->json(['code' => 0, 'message' => $getNewFilePath->original['message']]);
            }
        } catch (\Exeception $e) {
            Log::error('Failed to create file Error :'.$e->getMessage());

            return response()->json(['code' => 0, 'message' => 'Failed to create base64 code of JML file']);
        }

    }

    public function updateNumThreads($getLoadTesting): JsonResponse
    {
        try {
            $templatePath = public_path('jmx-sample-files/sample.jmx');
            // Load the JMeter test plan template
            $template = file_get_contents($templatePath);

            // Replace placeholder values with the retrieved values
            $template = str_replace('LT_NUM_THREADS', $getLoadTesting->no_of_virtual_user, $template);
            $template = str_replace('LT_RAMP_TIME', $getLoadTesting->ramp_time, $template);
            $template = str_replace('LT_DURATION', $getLoadTesting->duration, $template);
            $template = str_replace('LT_INITIAL_DELAY', $getLoadTesting->delay, $template);
            $template = str_replace('LT_LOOP_COUNT', $getLoadTesting->loop_count, $template);
            $template = str_replace('LT_DOMAIN_NAME', $getLoadTesting->domain_name, $template);
            $template = str_replace('LT_PORTOCOL', $getLoadTesting->protocols, $template);
            $template = str_replace('LT_PATH', $getLoadTesting->path, $template);
            $template = str_replace('LT_REQ_METHOD', $getLoadTesting->request_method, $template);

            $base64_content = base64_encode($template);

            $domainName = str_replace('.', '-', $getLoadTesting->domain_name);
            $fileName = $domainName.'-'.time().'.jmx';
            // Store the CSV file
            $disk = 'public';
            $directory = 'jmx/';
            $path = public_path($directory);

            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            Storage::disk($disk)->put($directory.$fileName, $template);

            Log::info('Jmeter base64 code generated successfully');

            return response()->json(['success' => 1, 'message' => 'JMeter test plan updated successfully', 'file_path' => $path.$fileName, 'base64_content' => $base64_content]);
        } catch (\Exeception $e) {
            Log::error('Failed to create base64 code of JML file Error :'.$e->getMessage());

            return response()->json(['success' => 0, 'message' => 'Failed to create base64 code of JML file']);
        }

    }

    public function viewResult($getLoadTestingId): View
    {
        $getLoadTesting = loadTesting::find($getLoadTestingId);
        Log::info('viewResult : Get load Testing Record');
        $checkJmterTestResultStoredDb = JmeterResultApdex::where('load_testing_id', $getLoadTesting->id)->exists();
        Log::info('viewResult : Get Jmeter Result Apdex');
        if (! $checkJmterTestResultStoredDb) {
            Log::info('viewResult : Get store Jmeter Result');
            $this->storeJmeterResult($getLoadTesting);
        }

        return view('load-testing.view-result', compact('getLoadTesting'));
    }

    public function sendDataToJmeterApi($getLoadTesting): JsonResponse
    {
        try {
            $getLoadTesting = loadTesting::find($getLoadTesting);
            Log::info('sendDataToJmeterApi : Get load Testing Record');
            $base64_content = base64_encode(file_get_contents(storage_path('app/public/jmx/'.basename($getLoadTesting->jmx_file_path))));
            Log::info('Base64_decode :: '.$base64_content);

            $testId = $getLoadTesting->id;

            $response = $this->runJmeterTest($testId);
            // if($response){
            Log::info('store Jmeter api response:');
            // $getLoadTesting->jmeter_api_request = json_encode(array('TEST_ID'=>$testId));
            $getLoadTesting->jmeter_api_response = $response;
            $getLoadTesting->save();
            // }

            Log::info('Jmeter api response:');

            return response()->json(['success' => 1, 'message' => 'JMeter load testing data sent', 'response' => $response]);
        } catch (\Exeception $e) {
            Log::error('Failed to call jmeter api :'.$e->getMessage());

            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function runJmeterTest($loadTestingId): int
    {
        try {
            Log::info('Initialized JMeter command');
            // $loadTestingId = $this->argument('loadTestingId');
            Log::info('command run new');
            $getLoadTestingRow = loadTesting::find($loadTestingId);
            $domainName = str_replace('.', '-', $getLoadTestingRow->domain_name);
            $fileName = $domainName.'-'.time();

            $saveResult = storage_path('app/public/jmx/'.$fileName.'.jtl');
            Log::info($saveResult);
            $jmxFile = storage_path('app/public/jmx/'.basename($getLoadTestingRow->jmx_file_path));
            Log::info('Started executing command ');
            // $data = $this->runJMeterTest($jmxFile,$saveResult);
            Log::info('jmeter call req --> jmeter -n -t '.$jmxFile.' -l '.$saveResult);
            // Build your JMeter command
            $jmeterCommand = 'jmeter -n -t '.$jmxFile.' -l '.$saveResult;
            // Execute the command
            $output = [];
            exec($jmeterCommand, $output, $returnCode);
            Log::info('jmeter return code-->'.$returnCode);
            if ($returnCode === 0) {
                Log::info('successfully executed jmeter command.');
                $getLoadTestingRow->status = 2;
                $getLoadTestingRow->jtl_file_path = $saveResult;
                $getLoadTestingRow->save();
                $htmlFile = storage_path('app/public/jmx/'.$fileName.'.html');
                $this->generateJMeterHtml($saveResult, $htmlFile);
                if ($returnCode === 0) {
                    $getLoadTestingRow->status = 4;
                    $getLoadTestingRow->save();
                }
                Log::info('command run successfully - ');

                return 1;
            } else {
                $getLoadTestingRow->status = 3;
                $getLoadTestingRow->save();
                Log::info('Failed to execute JMeter command for.');

                return 0;
            }
            //return $data;
        } catch (Exception $e) {
            Log::info('command error'.$e->getMessage());
            throw new Exception($e->getMessage());
        }

    }

    public function storeJmeterResult($getLoadTesting)
    {
        $filename = basename($getLoadTesting->jmx_file_path);
        $getFileContent = storage_path('app/public/jmx/'.$filename); //env('S3_JMETER_BUCKET_URL').$getLoadTesting->jmx_file_path;

        $getFileContent = Str::replace('index.html', 'content/js/dashboard.js', $getFileContent);

        $getFileContent = file_get_contents($getFileContent);
        $pattern = '/createTable\(\$.*?function/s';
        try {
            preg_match_all($pattern, $getFileContent, $matches);
            if (isset($matches[0]) && count($matches[0]) > 0) {
                foreach ($matches[0] as $match) {

                    $tableName = Str::after($match, '$("#');
                    $tableName = Str::before($tableName, '")');
                    $jsonString = Str::between($match, ', "items": [{"data":', '],').']';

                    $data = json_decode($jsonString, true);

                    $jsonString = Str::between($match, ', "overall": {"data":', '],').']';
                    $jsonString = Str::before($jsonString, '],').']';
                    $overAllData = json_decode($jsonString, true);
                    $tableName = Str::replace('#', '', $tableName);
                    switch ($tableName) {
                        case 'apdexTable':
                            if (isset($overAllData) && count($overAllData) > 0) {
                                $addData =
                                [
                                    'load_testing_id' => $getLoadTesting->id,
                                    'apdex' => $overAllData[0],
                                    'toleration_threshold' => $overAllData[1],
                                    'frustration_threshold' => $overAllData[2],
                                    'label' => $overAllData[3],
                                ];
                                Log::info('store Jmeter data:', $addData);
                                JmeterResultApdex::create($addData);
                            }
                            $addData =
                            [
                                'load_testing_id' => $getLoadTesting->id,
                                'apdex' => $data[0],
                                'toleration_threshold' => $data[1],
                                'frustration_threshold' => $data[2],
                                'label' => $data[3],
                            ];
                            Log::info('store Jmeter data:', $addData);
                            JmeterResultApdex::create($addData);

                            break;
                        case 'statisticsTable':
                            if (isset($overAllData) && count($overAllData) > 0) {
                                $addData =
                                [
                                    'load_testing_id' => $getLoadTesting->id,
                                    'label' => $overAllData[0],
                                    'samples' => $overAllData[1],
                                    'fail' => $overAllData[2],
                                    'error' => $overAllData[3],
                                    'avg' => $overAllData[4],
                                    'min' => $overAllData[5],
                                    'max' => $overAllData[6],
                                    'median' => $overAllData[7],
                                    '90th_pct' => $overAllData[8],
                                    '95th_pct' => $overAllData[9],
                                    '99th_pct' => $overAllData[10],
                                    'transactions' => $overAllData[11],
                                    'received' => $overAllData[12],
                                    'sent' => $overAllData[13],
                                ];
                                Log::info('store Jmeter Result Statistic data:', $addData);
                                JmeterResultStatistic::create($addData);
                            }
                            $addData =
                            [
                                'load_testing_id' => $getLoadTesting->id,
                                'label' => $data[0],
                                'samples' => $data[1],
                                'fail' => $data[2],
                                'error' => $data[3],
                                'avg' => $data[4],
                                'min' => $data[5],
                                'max' => $data[6],
                                'median' => $data[7],
                                '90th_pct' => $data[8],
                                '95th_pct' => $data[9],
                                '99th_pct' => $data[10],
                                'transactions' => $data[11],
                                'received' => $data[12],
                                'sent' => $data[13],
                            ];
                            Log::info('store Jmeter Result Statistic data:', $addData);
                            JmeterResultStatistic::create($addData);
                            break;
                        case 'errorsTable':
                            $addData =
                            [
                                'load_testing_id' => $getLoadTesting->id,
                                'type_of_error' => $data[0],
                                'no_of_error' => $data[1],
                                'percentage_of_error' => $data[2],
                                'percentage_in_all_samples' => $data[3],
                            ];
                            Log::info('store Jmeter error data:', $addData);
                            JmeterResultError::create($addData);
                            break;
                        case 'top5ErrorsBySamplerTable':
                            if (isset($overAllData) && count($overAllData) > 0) {
                                $addData =
                                [
                                    'load_testing_id' => $getLoadTesting->id,
                                    'samples' => $overAllData[0],
                                    'errors_1' => $overAllData[1],
                                    'error_1' => $overAllData[2],
                                    'errors_2' => $overAllData[3],
                                    'error_2' => $overAllData[4],
                                    'errors_3' => $overAllData[5],
                                    'error_3' => $overAllData[6],
                                    'errors_4' => $overAllData[7],
                                    'error_4' => $overAllData[8],
                                    'errors_5' => $overAllData[9],
                                    'error_5' => $overAllData[10],
                                    'errors_6' => $overAllData[11],
                                ];
                                Log::info('store Jmeter Result Top 5 Error:', $addData);
                                JmeterResultTop5Error::create($addData);

                            }
                            $addData =
                            [
                                'load_testing_id' => $getLoadTesting->id,
                                'samples' => $data[0],
                                'errors_1' => $data[1],
                                'error_1' => $data[2],
                                'errors_2' => $data[3],
                                'error_2' => $data[4],
                                'errors_3' => $data[5],
                                'error_3' => $data[6],
                                'errors_4' => $data[7],
                                'error_4' => $data[8],
                                'errors_5' => $data[9],
                                'error_5' => $data[10],
                                'errors_6' => $data[11],
                            ];
                            Log::info('store Jmeter Result Top 5 Error:', $addData);
                            JmeterResultTop5Error::create($addData);
                            break;
                        default:
                            // code...
                            break;
                    }

                }

                return true;
            } else {
                Log::error('Failed to store jmeter data ');

                return false;
            }
        } catch (\Exeception $e) {
            Log::error('Failed to store jmeter data :'.$e->getMessage());

            return false;
        }
    }

    public function filterResult(Request $request): View
    {
        Log::info('filterResult : Get load Testing Record');
        $getRecords = loadTesting::join('jmeter_result_apdexes', 'load_testings.id', '=', 'jmeter_result_apdexes.load_testing_id')
            ->join('jmeter_result_errors', 'load_testings.id', '=', 'jmeter_result_errors.load_testing_id')
            ->join('jmeter_result_statistics', 'load_testings.id', '=', 'jmeter_result_statistics.load_testing_id')
            ->join('jmeter_result_top5_errors', 'load_testings.id', '=', 'jmeter_result_top5_errors.load_testing_id')
            ->when(($request->has('domain_name') && $request->domain_name != ''), function ($query) use ($request) {
                $query->where('domain_name', 'LIKE', '%'.$request->domain_name.'%');
            })
            ->when(($request->has('apdex') && $request->apdex != ''), function ($query) use ($request) {
                $query->where('apdex', $request->apdex);
            })
            ->when(($request->has('toleration_threshold') && $request->toleration_threshold != ''), function ($query) use ($request) {
                $query->where('toleration_threshold', $request->toleration_threshold);
            })
            ->when(($request->has('frustration_threshold') && $request->frustration_threshold != ''), function ($query) use ($request) {
                $query->where('frustration_threshold', $request->frustration_threshold);
            })
            ->when(($request->has('label') && $request->label != ''), function ($query) use ($request) {
                $query->where('label', $request->label);
            })
            ->when(($request->has('samples') && $request->samples != ''), function ($query) use ($request) {
                $query->where('samples', $request->samples);
            })
            ->when(($request->has('fail') && $request->fail != ''), function ($query) use ($request) {
                $query->where('fail', $request->fail);
            })
            ->when(($request->has('average') && $request->average != ''), function ($query) use ($request) {
                $query->where('avg', $request->average);
            })
            ->groupBy('load_testings.id')
            ->orderByDesc('load_testings.id')
            ->paginate(10);
        $inputsData = $request->all();

        return view('load-testing.result-list', compact('getRecords', 'inputsData'));
    }

    public function getJmeterResponse(Request $request, $id)
    {
        try {
            $getLoadTesting = loadTesting::where('id', $id)->pluck('jmeter_api_response');

            return response()->json(['success' => 1, 'response' => $getLoadTesting]);
        } catch (\Exeception $e) {
            \Log::error('Failed to store jmeter data :'.$e->getMessage());

            return false;
        }
    }
}
