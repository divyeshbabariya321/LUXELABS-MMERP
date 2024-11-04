<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\MagentoCronData;
use App\StoreWebsite;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchMagentoCronData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetchMagentoCronData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will fetch data from all magento websites';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $website = StoreWebsite::whereNotNull('magento_url')->get()->pluck('magento_url', 'id')->toArray();

        $date = Carbon::yesterday()->format('Y-n-j');
        $cronstatus = $this->cronStatus();
        foreach ($website as $storeId => $web) {
            foreach ($cronstatus as $status) {
                $web = strtolower($web);
                $url = parse_url($web);
                if (! isset($url['scheme'])) {
                    $web = 'https://'.$web;
                }

                $api = "$web/default/rest/all/V1/cronbystatus/$status/createdat/$date";
                $data = json_decode($this->getDataApi($api), true);
                if (! empty($data)) {
                    foreach ($data as $da) {
                        if (isset($da['status']) && ($da['status'] == true && $da['Message'] == 'Cron data returned successfully')) {
                            $insert = [
                                'store_website_id' => $storeId,
                                'website' => $web,
                                'cronstatus' => $da['cronstatus'],
                                'cron_id' => $da['cron_id'],
                                'job_code' => $da['job_code'],
                                'cron_message' => $da['cron_message'],
                                'cron_created_at' => $da['created_at'],
                                'cron_scheduled_at' => $da['scheduled_at'],
                                'cron_executed_at' => $da['executed_at'],
                                'cron_finished_at' => $da['finished_at'],
                            ];
                            MagentoCronData::updateOrCreate($insert);
                        } else {
                            $this->info('Log Not available at '.$api);
                        }
                    }
                }
            }
        }

        return 'successfully';
    }

    public function cronStatus()
    {
        return ['missed', 'error', 'success', 'pending', 'running'];
    }

    public function getDataApi($url)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($server_output), $httpcode, 'getDataApi', FetchMagentoCronData::class);

        return $server_output;
    }
}
