<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\Models\MonitStatus;
use App\Models\MonitUnitCommandRunLogs;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MonitStatusController extends Controller
{
    public function listMonitStatus(Request $request): View
    {
        $assetsmanager = AssetsManager::where('monit_api_url', '!=', '')->get();
        if (! empty($assetsmanager)) {
            foreach ($assetsmanager as $value) {
                // URL of the XML data source
                $url = $value->monit_api_url.'_status?format=xml';

                // Your username and password for authentication
                $username = $value->monit_api_username;
                $password = $value->monit_api_password;

                // Initialize cURL session
                $ch = curl_init($url);

                // Set cURL options for authentication
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

                // Execute the cURL request
                $response = curl_exec($ch);

                // Check for cURL errors
                if (! curl_errno($ch)) {

                    // Parse and process the XML data
                    $xmlString = str_replace('&quot;', '&amp;quot;', $response);
                    $xml = simplexml_load_string($xmlString);

                    if (! empty($xml)) {
                        $json = json_encode($xml);
                        $xmlArray = json_decode($json, true);

                        MonitStatus::where('monit_api_id', $xmlArray['server']['id'])->delete();

                        foreach ($xmlArray['service'] as $valueXaml) {
                            $id = '';
                            if (! empty($xmlArray['server']['id'])) {
                                $id = $xmlArray['server']['id'];
                            }

                            $service_name = '';
                            if (! empty($valueXaml['name'])) {
                                $service_name = $valueXaml['name'];
                            }

                            $uptime = '';

                            if (! empty($valueXaml['uptime'])) {
                                $uptime = $valueXaml['uptime'];

                                if (is_numeric(trim($uptime))) {
                                    $seconds = $uptime;

                                    // Calculate days, hours, and minutes
                                    $days = floor($seconds / 86400); // 1 day = 24 hours * 60 minutes * 60 seconds
                                    $seconds %= 86400; // Remaining seconds after calculating days
                                    $hours = floor($seconds / 3600); // 1 hour = 60 minutes * 60 seconds
                                    $seconds %= 3600; // Remaining seconds after calculating hours
                                    $minutes = floor($seconds / 60); // 1 minute = 60 seconds

                                    // Format the result
                                    $uptime = $days.'d '.$hours.'h '.$minutes.'m';
                                }
                            }

                            $status = $valueXaml['status'];

                            $memory = '';
                            if (! empty($valueXaml['memory'])) {
                                $memory = json_encode($valueXaml['memory']);
                            }

                            $ip = '';
                            if (! empty($value['ip'])) {
                                $ip = $value['ip'];
                            }

                            MonitStatus::create(['service_name' => $service_name, 'status' => $status, 'uptime' => $uptime, 'memory' => json_encode($memory), 'url' => $url, 'username' => $username, 'password' => $password, 'xmlid' => $id.'-'.strtolower($service_name), 'ip' => $ip, 'monit_api_id' => $id, 'asset_management_id' => $value->id]);
                        }
                    }
                }

                // Close cURL session
                curl_close($ch);
            }
        }

        $monitStatus = MonitStatus::with('assetsManager');
        if ($request->service_name) {
            $monitStatus = $monitStatus->where('service_name', 'LIKE', '%'.$request->service_name.'%');
        }

        if ($request->search_memory) {
            $monitStatus = $monitStatus->where('memory', 'LIKE', '%'.$request->search_memory.'%');
        }

        if ($request->date) {
            $monitStatus = $monitStatus->where('created_at', 'LIKE', '%'.$request->date.'%');
        }

        if ($request->search_status) {
            $monitStatus = $monitStatus->where('status', 'LIKE', '%'.$request->search_status.'%');
        }

        if ($request->search_uptime) {
            $monitStatus = $monitStatus->where('uptime', 'LIKE', '%'.$request->search_uptime.'%');
        }

        $monitStatus = $monitStatus->latest()->orderBy('status')->get();

        return view('monit-status.monit-status-list', compact('monitStatus'));
    }

    public function runCommand(Request $request): JsonResponse
    {
        try {
            $url = 'http://s10.theluxuryunlimited.com:5000/execute';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $ipAddress = $request->server_ip; // Valid IP address as a string

            // Ensure it's a string before use
            $ipString = (string) $ipAddress;

            $parameters = [
                'command' => $request->command,
                'server' => $ipString,
                'dir' => '/home/prod-1-1/current/',
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $result = curl_exec($ch);

            curl_getinfo($ch, CURLINFO_HTTP_CODE);
            Log::info('API result: '.$result);
            Log::info('API Error Number: '.curl_errno($ch));

            curl_close($ch);

            MonitUnitCommandRunLogs::create(['created_by' => auth()->user()->id, 'xmlid' => $request->id, 'request_data' => json_encode($parameters), 'response_data' => json_decode($result)]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function monitApiHistory($id): JsonResponse
    {
        $datas = MonitUnitCommandRunLogs::with('user')
            ->where('xmlid', $id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
