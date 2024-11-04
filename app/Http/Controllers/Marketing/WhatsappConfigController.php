<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\EditWhatsappConfigRequest;
use App\Http\Requests\Marketing\StoreWhatsappConfigRequest;
use App\ImQueue;
use App\LogRequest;
use App\Marketing\WhatsappBusinessAccounts;
use App\Marketing\WhatsappConfig;
use App\Notification;
use App\Services\Whatsapp\ChatApi\ChatApi;
use App\Setting;
use App\StoreWebsite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class WhatsappConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $query = WhatsappConfig::query();

            //Added store data to put dropdown in form  to add store website id to whatsapp config table
            $storeData = StoreWebsite::all()->toArray();

            //global search term
            if (request('term') != null) {
                $query->where('number', 'LIKE', "%{$request->term}%")
                    ->orWhere('username', 'LIKE', "%{$request->term}%")
                    ->orWhere('password', 'LIKE', "%{$request->term}%")
                    ->orWhere('provider', 'LIKE', "%{$request->term}%");
            }

            if (request('date') != null) {
                $query->whereDate('created_at', request('website'));
            }

            //if number is not null
            if (request('number') != null) {
                $query->where('number', 'LIKE', '%'.request('number').'%');
            }

            //If username is not null
            if (request('username') != null) {
                $query->where('username', 'LIKE', '%'.request('username').'%');
            }

            //if provider with is not null
            if (request('provider') != null) {
                $query->where('provider', 'LIKE', '%'.request('provider').'%');
            }

            //if provider with is not null
            if (request('customer_support') != null) {
                $query->where('is_customer_support', request('customer_support'));
            }

            $whatsAppConfigs = $query->orderByDesc('id')->paginate(Setting::get('pagination'));
        } else {
            $whatsAppConfigs = WhatsappConfig::latest()->paginate(Setting::get('pagination'));
        }
        $businessAccounts = WhatsappBusinessAccounts::all();

        //Fetch Store Details

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('marketing.whatsapp-configs.partials.data', compact('whatsAppConfigs', 'storeData', 'businessAccounts'))->render(),
                'links' => (string) $whatsAppConfigs->render(),
            ], 200);
        }

        return view('marketing.whatsapp-configs.index', [
            'whatsAppConfigs' => $whatsAppConfigs,
            'storeData' => $storeData,
            'businessAccounts' => $businessAccounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWhatsappConfigRequest $request): RedirectResponse
    {
        //dd($request);
        $requestData = $request->all();
        $defaultFor = implode(',', isset($requestData['default_for']) ? $requestData['default_for'] : []);

        $data = $request->except('_token', 'default_for');
        $data['is_customer_support'] = $request->customer_support;
        $data['default_for'] = $defaultFor;
        WhatsappConfig::create($data);

        Artisan::call('config:clear');

        return redirect()->back()->withSuccess('You have successfully stored Whats App Config');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(WhatsappConfig $whatsAppConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WhatsappConfig  $whatsAppConfig
     */
    public function edit(EditWhatsappConfigRequest $request): RedirectResponse
    {
        $config = WhatsappConfig::findorfail($request->id);

        $requestData = $request->all();

        $defaultFor = implode(',', isset($requestData['default_for']) ? $requestData['default_for'] : []);

        $data = $request->except('_token', 'id', 'default_for');
        $data['is_customer_support'] = $request->customer_support;
        $data['default_for'] = $defaultFor;

        $config->update($data);

        Artisan::call('config:clear');

        return redirect()->back()->withSuccess('You have successfully changed Whats App Config');
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WhatsappConfig $whatsAppConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WhatsappConfig  $whatsAppConfig
     */
    public function destroy(Request $request): JsonResponse
    {
        $config = WhatsappConfig::findorfail($request->id);
        $config->delete();

        Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Config Deleted',
        ]);
    }

    /**
     * Show history page
     *
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history($id, Request $request): View
    {
        $term = $request->term;
        $date = $request->date;
        $config = WhatsappConfig::find($id);
        $number = $config->number;
        $provider = $config->provider;

        if ($config->provider === 'py-whatsapp') {
            $data = ImQueue::whereNotNull('sent_at')->where('number_from', $config->number)->orderByDesc('sent_at');
            if (request('term') != null) {
                $data = $data->where('number_to', 'LIKE', "%{$request->term}%");
                $data = $data->orWhere('text', 'LIKE', "%{$request->term}%");
                $data = $data->orWhere('priority', 'LIKE', "%{$request->term}%");
            }
            if (request('date') != null) {
                $data = $data->whereDate('send_after', request('date'));
            }
            $data = $data->get();
        } elseif ($config->provider === 'Chat-API') {
            $data = ChatApi::chatHistory($config->number);
        }

        return view('marketing.whatsapp-configs.history', compact('data', 'id', 'term', 'date', 'number', 'provider'));
    }

    /**
     * Show queue page
     *
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function queue($id, Request $request): View
    {
        $term = $request->term;
        $date = $request->date;
        $config = WhatsappConfig::find($id);
        $number = $config->number;
        $provider = $config->provider;
        if ($config->provider === 'py-whatsapp') {
            $data = ImQueue::whereNull('sent_at')->with('marketingMessageTypes')->where('number_from', $config->number)->orderByDesc('created_at');
            if (request('term') != null) {
                $data = $data->where('number_to', 'LIKE', "%{$request->term}%");
                $data = $data->orWhere('text', 'LIKE', "%{$request->term}%");
                $data = $data->orWhere('priority', 'LIKE', "%{$request->term}%");
            }
            if (request('date') != null) {
                $data = $data->whereDate('send_after', request('date'));
            }
            $data = $data->get();
        } elseif ($config->provider === 'Chat-API') {
            $data = ChatApi::chatQueue($config->number);
        }

        return view('marketing.whatsapp-configs.queue', compact('data', 'id', 'term', 'date', 'number', 'provider'));
    }

    /**
     * Delete all queues from Chat-Api
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function clearMessagesQueue($id): RedirectResponse
    {
        $config = WhatsappConfig::find($id);
        ChatApi::deleteQueues($config->number);

        return redirect()->to('/marketing/whatsapp-config');
    }

    /**
     * Delete single queue
     */
    public function destroyQueue(Request $request): JsonResponse
    {
        $config = ImQueue::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Config Deleted',
        ]);
    }

    /**
     * Delete all queues from Whatsapp
     */
    public function destroyQueueAll(Request $request): JsonResponse
    {
        ImQueue::where('number_from', $request->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Configs Deleted',
        ]);
    }

    public function getBarcode(Request $request): JsonResponse
    {
        $id = $request->id;

        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();

        $url = 'http://136.244.118.102:81/get-barcode';

        if ($whatsappConfig->is_use_own == 1) {
            $url = 'http://167.86.89.241:81/get-barcode?instanceId='.$whatsappConfig->instance_id;
        }

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($output), $httpcode, WhatsappConfigController::class, 'getBarcode');

        $barcode = $output;

        if ($barcode) {
            if ($barcode == 'No Barcode Available') {
                return response()->json(['nobarcode' => true]);
            }
            $content = base64_decode($barcode);

            $media = MediaUploader::fromString($content)->toDirectory('/barcode')->useFilename('barcode-'.Str::random(4))->upload();

            return response()->json(['success' => true, 'media' => getMediaUrl($media)]);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function getScreen(Request $request): JsonResponse
    {
        $id = $request->id;

        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($whatsappConfig) {
            $ch = curl_init();

            if ($whatsappConfig->is_use_own == 1) {
                $url = 'http://167.86.89.241:81/get-screen?instanceId='.$whatsappConfig->instance_id;
            } else {
                $url = config('settings.whatsapp_barcode_ip').$whatsappConfig->username.'/get-screen';
            }

            // set url
            curl_setopt($ch, CURLOPT_URL, $url);
            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $output contains the output string
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // close curl resource to free up system resources
            curl_close($ch);

            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($output), $httpcode, WhatsappConfigController::class, 'getScreen');
            if ($whatsappConfig->is_use_own = 1) {
                $content = base64_decode($output);
            } else {
                $barcode = json_decode($output);
                if ($barcode->barcode == 'No Screen Available') {
                    return response()->json(['nobarcode' => true]);
                }
                $content = base64_decode($barcode->barcode);
            }

            $media = MediaUploader::fromString($content)->toDirectory('/barcode')->useFilename('screen'.uniqid(true))->upload();

            return response()->json(['success' => true, 'media' => getMediaUrl($media)]);
        }
    }

    public function deleteChromeData(Request $request): JsonResponse
    {
        $id = $request->id;

        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();

        $url = config('settings.whatsapp_barcode_ip').':'.$whatsappConfig->username.'/delete-chrome-data';

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // close curl resource to free up system resources
        curl_close($ch);

        $barcode = json_decode($output); //response decoded

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $barcode, $httpcode, WhatsappConfigController::class, 'deleteChromeData');

        if ($barcode) {
            if ($barcode->barcode == 'Directory Deleted') {
                return response()->json(['nobarcode' => true]);
            }

            return response()->json(['success' => true, 'media' => 'Directory Can not be Deleted']);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function restartScript(Request $request): JsonResponse
    {
        $id = $request->id;

        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $ch = curl_init();

        $url = config('settings.whatsapp_barcode_ip').$whatsappConfig->username.'/restart-script';

        if ($whatsappConfig->is_use_own == 1) {
            $url = 'http://167.86.89.241:81/restart?instanceId='.$whatsappConfig->instance_id;
        }

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // close curl resource to free up system resources
        curl_close($ch);

        $response = json_decode($output); //response decoded

        LogRequest::log($startTime, $url, 'POST', json_encode([]), $response, $httpcode, WhatsappConfigController::class, 'restartScript');

        if ($response) {
            if ($response->barcode == 'Process Killed') {
                return response()->json(['nobarcode' => true]);
            }

            return response()->json(['success' => true, 'media' => 'No Process Found']);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function blockedNumber(): JsonResponse
    {
        $whatsappNumbers = WhatsappConfig::where('status', 2)->get();

        foreach ($whatsappNumbers as $whatsappNumber) {
            $queues = ImQueue::where('number_from', $whatsappNumber->number)->whereNotNull('sent_at')->orderByDesc('sent_at')->get();

            //Making DND for last 30 numbers
            $maxCount = 30;
            $count = 0;
            //Making 30 customer numbers to DND
            foreach ($queues as $queue) {
                $customer = Customer::where('phone', $queue->number_to)->first();
                if ($count == $maxCount) {
                    break;
                }
                if (! empty($customer)) {
                    $customer->do_not_disturb = 1;
                    $customer->phone = '-'.$customer->phone;
                    $customer->update();
                    $count++;
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Last 30 Customer disabled']);
    }

    public function checkInstanceAuthentication()
    {
        //get all providers
        $allWhatsappInstances = WhatsappConfig::select()->where(['provider' => 'Chat-API'])->get();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        try {
            foreach ($allWhatsappInstances as $instanceDetails) {
                $instanceId = $instanceDetails->instance_id;
                $sentTo = 6;
                if ($instanceId) {
                    $curl = curl_init();
                    $url = "https://api.chat-api.com/instance$instanceId/status?token";

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 300,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'content-type: application/json',
                        ],
                    ]);

                    $response = curl_exec($curl);
                    if (curl_errno($curl)) {
                        curl_error($curl);
                    } else {
                        $resInArr = json_decode($response, true);
                        if (isset($resInArr) && isset($resInArr['accountStatus']) && $resInArr['accountStatus'] != 'authenticated') {
                            Notification::create([
                                'role' => 'Whatsapp Config Proivders Authentication',
                                'message' => 'Current Status : '.$resInArr['accountStatus'],
                                'product_id' => '',
                                'user_id' => $instanceDetails->id,
                                'sale_id' => '',
                                'task_id' => '',
                                'sent_to' => $sentTo,
                            ]);
                        }
                    }
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, WhatsappConfigController::class, 'checkInstanceAuthentication');
                }
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function logoutScript(Request $request): JsonResponse
    {
        $id = $request->id;
        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        if ($whatsappConfig->is_use_own == 1) {
            $url = 'http://167.86.89.241:83/logout?instanceId='.$whatsappConfig->instance_id;
            curl_setopt($ch, CURLOPT_URL, $url);
            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $output contains the output string
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // close curl resource to free up system resources
            curl_close($ch);
            $response = json_decode($output); //response deocde

            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $httpcode, WhatsappConfigController::class, 'logoutScript');

            if ($response) {
                return response()->json(['success' => true, 'message' => 'Logout Script called']);
            } else {
                return response()->json(['error' => true]);
            }
        }

        return response()->json(['error' => true]);
    }

    public function getStatusInfo(Request $request): JsonResponse
    {
        $id = $request->id;
        $whatsappConfig = WhatsappConfig::find($id);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        if ($whatsappConfig->is_use_own == 1) {
            $url = 'http://167.86.89.241:81/get-status?instanceId='.$whatsappConfig->instance_id;
            curl_setopt($ch, CURLOPT_URL, $url);
            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $output contains the output string
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // close curl resource to free up system resources
            curl_close($ch);
            $response = json_decode($output); //response deocded

            LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $httpcode, WhatsappConfigController::class, 'getStatusInfo');
            if (! empty($output)) {
                return response()->json(['success' => true, 'message' => $output]);
            } else {
                return response()->json(['error' => true]);
            }
        }

        return response()->json(['error' => true]);
    }
}
