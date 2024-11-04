<?php

namespace App\Console\Commands;

use App\AppRatingsReport;
use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use Exception;
use Illuminate\Console\Command;

class IosRatingsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosRatingsReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ratings report using Appfigure which sync with Appstore connect check and store DB every day';

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
                $url = "https://api.appfigures.com/v2/reports/ratings?group_by=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value,";
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
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $res = json_decode($result, true); //response decode
                curl_close($curl);

                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($result), $httpcode, IosRatingsReport::class, 'handle');
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api was called.']);

                if ($res) {
                    $r = new AppRatingsReport;
                    $r->product_id = $array_app_name[$i].' ['.$product_id.']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;

                    $r->breakdown = implode(',', $res['apple:ios']['breakdown']);
                    $r->new = implode(',', $res['apple:ios']['new']);
                    $r->average = $res['apple:ios']['average'];
                    $r->total = $res['apple:ios']['total'];
                    $r->new_average = $res['apple:ios']['new_average'];
                    $r->new_total = $res['apple:ios']['new_total'];
                    $r->positive = $res['apple:ios']['positive'];
                    $r->negative = $res['apple:ios']['negative'];
                    $r->neutral = $res['apple:ios']['neutral'];
                    $r->new_positive = $res['apple:ios']['new_positive'];
                    $r->new_negative = $res['apple:ios']['new_negative'];
                    $r->new_neutral = $res['apple:ios']['new_neutral'];
                    $r->storefront = $res['apple:ios']['storefront'];
                    $r->store = $res['apple:ios']['store'];
                    $r->save();
                }
                $i += 1;
            }

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'App ratings report was added.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);

            return $this->info('Ratings Report added');
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
