<?php

namespace App\Console\Commands;

use App\Host;
use App\HostItem;
use App\LogRequest;
use App\ZabbixHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ZabbixHostItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:zabbixhostitems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store all host items';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info($this->signature.' started.');
        $auth_key = $this->login_api();
        $get_hosts = Host::get();
        if (! is_null($get_hosts)) {
            $historyRows = [];
            foreach ($get_hosts as $host) {
                try {
                    try {
                        $item_interrupt = $this->item_api($auth_key, $host->hostid, 'Interrupts per second');
                        if (! empty($item_interrupt)) {
                            HostItem::where('hostid', $host->hostid)->update(['interrupts_per_second' => $item_interrupt->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'interrupts_per_second' => $item_interrupt->lastvalue, 'item_id' => $item_interrupt->itemid];
                            Log::info('Interrupts per second: '.json_encode($item_interrupt));
                        }
                    } catch (Throwable $e) {

                        Log::error('Interrupts per second error: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_free_inode = $this->item_api($auth_key, $host->hostid, 'Free inodes in');
                        if (! empty($item_free_inode)) {
                            HostItem::where('hostid', $host->hostid)->update(['free_inode_in' => $item_free_inode->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'free_inode_in' => $item_free_inode->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('Free inodes in: '.json_encode($item_free_inode));
                    } catch (Throwable $e) {
                        Log::error('Free inodes in: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_space_utilization = $this->item_api($auth_key, $host->hostid, 'Space Utilization');
                        if (! empty($item_space_utilization)) {
                            HostItem::where('hostid', $host->hostid)->update(['space_utilization' => $item_space_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'space_utilization' => $item_space_utilization->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('Space Utilization: '.json_encode($item_space_utilization));

                    } catch (Throwable $e) {
                        Log::error('Space Utilization: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_total_space = $this->item_api($auth_key, $host->hostid, 'Total Space');
                        if (! empty($item_total_space)) {
                            HostItem::where('hostid', $host->hostid)->update(['total_space' => $item_total_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'total_space' => $item_total_space->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('Space Utilization: '.json_encode($item_space_utilization));

                    } catch (Throwable $e) {
                        Log::error('Total Space: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_used_space = $this->item_api($auth_key, $host->hostid, 'Used Space');
                        if (! empty($item_used_space)) {
                            HostItem::where('hostid', $host->hostid)->update(['used_space' => $item_used_space->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'used_space' => $item_used_space->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('Space Utilization: '.json_encode($item_space_utilization));

                    } catch (Throwable $e) {
                        Log::error('Used Space: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_available_memory = $this->item_api($auth_key, $host->hostid, 'Available Memory');
                        if (! empty($item_available_memory)) {
                            HostItem::where('hostid', $host->hostid)->update(['available_memory' => $item_available_memory->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'available_memory' => $item_available_memory->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('Available Memory: '.json_encode($item_available_memory));

                    } catch (Throwable $e) {
                        Log::error('Available Memory: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_available_memory_in = $this->item_api($auth_key, $host->hostid, 'Available Memory in');
                        if (! empty($item_available_memory_in)) {
                            $historyRows[] = ['host_id' => $host->hostid, 'available_memory_in' => $item_available_memory_in->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                    } catch (Throwable $e) {
                        Log::error('Available Memory in: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_cpu_idle_time = $this->item_api($auth_key, $host->hostid, 'CPU Idle Time');
                        if (! empty($item_cpu_idle_time)) {
                            HostItem::where('hostid', $host->hostid)->update(['cpu_idle_time' => $item_cpu_idle_time->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'cpu_idle_time' => $item_cpu_idle_time->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('CPU Idle Time: '.json_encode($item_cpu_idle_time));

                    } catch (Throwable $e) {
                        Log::error('CPU Idle Time: '.substr($e->getMessage(), 0, 1000));
                    }

                    try {
                        $item_cpu_utilization = $this->item_api($auth_key, $host->hostid, 'CPU Utilizatio');
                        if (! empty($item_cpu_utilization)) {
                            HostItem::where('hostid', $host->hostid)->update(['cpu_utilization' => $item_cpu_utilization->lastvalue, 'item_id' => $item_interrupt->itemid]);
                            $historyRows[] = ['host_id' => $host->hostid, 'cpu_utilization' => $item_cpu_utilization->lastvalue, 'item_id' => $item_interrupt->itemid];
                        }
                        Log::info('CPU Utilizatio: '.json_encode($item_cpu_utilization));

                    } catch (Throwable $e) {
                        Log::error('CPU Utilizatio: '.substr($e->getMessage(), 0, 1000));
                    }
                } catch (Throwable $e) {
                    Log::error('CPU Utilizatio: '.substr($e->getMessage(), 0, 1000));
                }
            }
            if (count($historyRows)) {
                ZabbixHistory::Insert($historyRows);
            }
        }
    }

    public function login_api()
    {
        //Get API ENDPOINT response
        $url = config('settings.zabbix_host').'/api_jsonrpc.php';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
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
        LogRequest::log($startTime, $url, 'POST', json_encode($datas), json_decode($result), $httpcode, ZabbixHostItems::class, 'login_api');

        $results = json_decode($result);

        try {
            return isset($results[0]->result)
            ? $results[0]->result
            : tap(0, fn () => Log::channel('general')->info(Carbon::now().$results[0]->error->data));

        } catch (Throwable $throwable) {

            return 0;
        }
    }

    public function item_api($auth_key, $hostid, $name)
    {
        try {
            //Get API ENDPOINT response
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $url = config('settings.zabbix_host').'/api_jsonrpc.php';
            $curl = curl_init($url);
            $data = [
                'jsonrpc' => '2.0',
                'method' => 'item.get',
                'params' => [
                    'hostids' => $hostid,
                    'limit' => 1,
                    'search' => [
                        'name' => $name,
                    ],
                    'sortfield' => 'name',
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
            curl_close($curl);
            LogRequest::log($startTime, $url, 'POST', json_encode($datas), json_decode($result), $httpcode, ZabbixHostItems::class, 'item_api');

            $results = json_decode($result);

            return $results[0]->result[0];
        } catch (Throwable $e) {
            return 0;
        }
    }
}
