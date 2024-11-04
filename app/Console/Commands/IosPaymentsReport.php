<?php

namespace App\Console\Commands;

use App\AppPaymentReport;
use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use Exception;
use Illuminate\Console\Command;

class IosPaymentsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosPaymentsReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payments report using Appfigure which sync with Appstore connect check and store DB every day';

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
                $url = "https://api.appfigures.com/v2/reports/payments?group_by=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value";
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
                $res = json_decode($result, true); //response decoded
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), $res, $httpcode, IosPaymentsReport::class, 'handle');
                curl_close($curl);

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api was called.']);

                if ($res) {
                    $r = new AppPaymentReport;
                    $r->product_id = $array_app_name[$i].' ['.$product_id.']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;

                    $r->revenue = $res['apple:ios']['revenue'];
                    $r->converted_revenue = $res['apple:ios']['converted_revenue'];
                    $r->financial_revenue = $res['apple:ios']['financial_revenue'];
                    $r->estimated_revenue = $res['apple:ios']['estimated_revenue'];
                    $r->storefront = $res['apple:ios']['storefront'];
                    $r->store = $res['apple:ios']['store'];

                    $r->save();
                }

                $i += 1;
            }

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'App payment report was added.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);

            return $this->info('Payments Report added');
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
