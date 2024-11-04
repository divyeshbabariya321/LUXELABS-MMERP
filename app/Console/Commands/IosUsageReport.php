<?php

namespace App\Console\Commands;

use App\AppUsageReport;
use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use Exception;
use Illuminate\Console\Command;

class IosUsageReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosUsageReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Usage using Appfigure which sync with Appstore connect check and store DB every day';

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $username = config('settings.appfigure_user_email');
            $password = config('settings.appfigure_user_pass');
            $key = base64_encode($username.':'.$password);

            $group_by = 'network';
            $start_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
            $end_date = date('Y-m-d');
            $product_id = config('settings.appfigure_product_id');
            $ckey = config('settings.appfigure_client_key');
            $array_app_name = explode(',', config('settings.appfigure_app_name'));
            $i = 0;
            $array_app = explode(',', config('settings.appfigure_product_id'));
            foreach ($array_app as $app_value) {
                //Usage Report
                $curl = curl_init();
                $url = "https://api.appfigures.com/v2/reports/usage?group_by=' . $group_by . '&start_date=2019-01-01&end_date=' . $end_date . '&products=' . $app_value,";
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'X-Client-Key:'.$ckey,
                        'Authorization: Basic '.$key,
                    ],
                ]);

                $result = curl_exec($curl);
                $res = json_decode($result, true); //response decode
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), $res, $httpcode, IosUsageReport::class, 'handle');
                curl_close($curl);

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api call finished.']);
                print_r($res);

                if ($res) {
                    $r = new AppUsageReport;
                    $r->product_id = $array_app_name[$i].' ['.$product_id.']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;
                    $r->crashes = $res['apple:analytics']['crashes'];
                    $r->sessions = $res['apple:analytics']['sessions'];
                    $r->app_store_views = $res['apple:analytics']['app_store_views'];
                    $r->unique_app_store_views = $res['apple:analytics']['unique_app_store_views'];
                    $r->daily_active_devices = $res['apple:analytics']['daily_active_devices'];
                    $r->monthly_active_devices = $res['apple:analytics']['monthly_active_devices'];
                    $r->paying_users = $res['apple:analytics']['paying_users'];
                    $r->impressions = $res['apple:analytics']['impressions'];
                    $r->unique_impressions = $res['apple:analytics']['unique_impressions'];
                    $r->uninstalls = $res['apple:analytics']['uninstalls'];
                    $r->avg_daily_active_devices = $res['apple:analytics']['avg_daily_active_devices'];
                    $r->avg_optin_rate = $res['apple:analytics']['avg_optin_rate'];
                    $r->storefront = $res['apple:analytics']['storefront'];
                    $r->store = $res['apple:analytics']['store'];
                    $r->save();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'App user report was added.']);

                    return $this->info('Usage Report added');
                } else {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'App user report was not generated.']);

                    return $this->info('Usage Report not generated');
                }

                $i += 1;
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron job was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
