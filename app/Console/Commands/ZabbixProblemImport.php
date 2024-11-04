<?php

namespace App\Console\Commands;

use App\Host;
use App\LogRequest;
use App\Problem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ZabbixProblemImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zabbix:problem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get problems';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $auth_key = $this->login_api();
        if ($auth_key != '') {
            $problems = $this->problem_api($auth_key);
            foreach ($problems as $val) {
                foreach ($val as $problem) {
                    $check_if_exists = Problem::where('eventid', $problem->eventid)->first();
                    $host = Host::where('hostid', $problem->host_id)->first();
                    $array = [
                        'objectid' => $problem->objectid,
                        'name' => $problem->name,
                        'hostname' => $host->host,
                        'datetime' => $problem->clock,
                        'recovery_time' => $problem->r_clock,
                        'severity' => $problem->severity,
                        'time_duration' => $problem->ns,
                        'acknowledged' => (bool) $problem->acknowledged,
                    ];
                    $check_if_exists
                    ? Problem::where('eventid', $problem->eventid)->update($array)
                    : Problem::create(array_merge($array, ['eventid' => $problem->eventid]));
                }
            }
        }
    }

    public function login_api()
    {
        //Get API ENDPOINT response
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = config('settings.zabbix_host').'/api_jsonrpc.php';
        $curl = curl_init($url);
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => [
                'username' => config('settings.zabbix_username'),
                'password' => config('settings.zabbix_password'),
            ],
            'id' => 1,
        ];
        $datas = json_encode([$data]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        LogRequest::log($startTime, $url, 'POST', json_encode($datas), json_decode($result), $httpcode, ZabbixProblemImport::class, 'login_api');

        $results = json_decode($result);

        try {
            if (isset($results[0]->result)) {
                return $results[0]->result;
            } else {
                Log::channel('general')->info(Carbon::now().$results[0]->error->data);

                return 0;
            }
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function problem_api($auth_key)
    {
        //Get API ENDPOINT response
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = config('settings.zabbix_host').'/api_jsonrpc.php';
        $hostIds = Host::pluck('hostid');
        $errorArray = [];
        foreach ($hostIds as $val) {
            $curl = curl_init($url);
            $data = [
                'jsonrpc' => '2.0',
                'method' => 'problem.get',
                'params' => [
                    'hostids' => $val,
                ],
                'auth' => $auth_key,
                'id' => 1,
            ];
            $datas = json_encode([$data]);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $result = json_decode($result); //response decode

            LogRequest::log($startTime, $url, 'POST', json_encode($datas), $result, $httpcode, ZabbixProblemImport::class, 'login_api');

            if (isset($result) && is_array($result)) {
                foreach ($result as $error) {
                    foreach ($error->result as $pushcode) {
                        $pushcode->host_id = $val;
                    }
                }
                curl_close($curl);
                array_push($errorArray, $result[0]->result);
            }
        }

        return $errorArray;
    }
}
