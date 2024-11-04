<?php

namespace App\Console\Commands;

use App\AppSubscriptionReport;
use App\CronJob;
use App\Helpers\LogHelper;
use App\LogRequest;
use Exception;
use Illuminate\Console\Command;

class IosSubscriptionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'IosSubscriptionReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscription report using Appfigure which sync with Appstore connect check and store DB every day';

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
                $url = "https://api.appfigures.com/v2/reports/subscriptions?group_by=' . $group_by . '&start_date=' . $start_date . '&end_date=' . $end_date . '&products=' . $app_value";
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
                $res = json_decode($result, true);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($result), $httpcode, IosSubscriptionReport::class, 'handle');
                curl_close($curl);

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'CURL api call completed.']);

                if ($res) {
                    $r = new AppSubscriptionReport;
                    $r->product_id = $array_app_name[$i].' ['.$product_id.']';
                    $r->group_by = $group_by;
                    $r->start_date = $start_date;
                    $r->end_date = $end_date;

                    $r->active_subscriptions = $res['apple:ios']['active_subscriptions'];
                    $r->active_free_trials = $res['apple:ios']['active_free_trials'];
                    $r->new_subscriptions = $res['apple:ios']['new_subscriptions'];
                    $r->cancelled_subscriptions = $res['apple:ios']['cancelled_subscriptions'];
                    $r->new_trials = $res['apple:ios']['new_trials'];
                    $r->trial_conversion_rate = $res['apple:ios']['trial_conversion_rate'];
                    $r->mrr = $res['apple:ios']['mrr'];
                    $r->actual_revenue = $res['apple:ios']['actual_revenue'];
                    $r->renewals = $res['apple:ios']['renewals'];
                    $r->first_year_subscribers = $res['apple:ios']['first_year_subscribers'];
                    $r->non_first_year_subscribers = $res['apple:ios']['non_first_year_subscribers'];
                    $r->reactivations = $res['apple:ios']['reactivations'];
                    $r->transitions_out = $res['apple:ios']['transitions_out'];
                    $r->trial_cancellations = $res['apple:ios']['trial_cancellations'];
                    $r->transitions_in = $res['apple:ios']['transitions_in'];
                    $r->activations = $res['apple:ios']['activations'];
                    $r->cancellations = $res['apple:ios']['cancellations'];
                    $r->trial_conversions = $res['apple:ios']['trial_conversions'];
                    $r->churn = $res['apple:ios']['churn'];
                    $r->gross_revenue = $res['apple:ios']['gross_revenue'];
                    $r->gross_mrr = $res['apple:ios']['gross_mrr'];
                    $r->active_grace = $res['apple:ios']['active_grace'];
                    $r->new_grace = $res['apple:ios']['new_grace'];
                    $r->grace_drop_off = $res['apple:ios']['grace_drop_off'];
                    $r->grace_recovery = $res['apple:ios']['grace_recovery'];
                    $r->new_trial_grace = $res['apple:ios']['new_trial_grace'];
                    $r->trial_grace_drop_off = $res['apple:ios']['trial_grace_drop_off'];
                    $r->trial_grace_recovery = $res['apple:ios']['trial_grace_recovery'];
                    $r->active_trials = $res['apple:ios']['active_trials'];
                    $r->active_discounted_subscriptions = $res['apple:ios']['active_discounted_subscriptions'];
                    $r->all_active_subscriptions = $res['apple:ios']['all_active_subscriptions'];
                    $r->paying_subscriptions = $res['apple:ios']['paying_subscriptions'];
                    $r->all_subscribers = $res['apple:ios']['all_subscribers'];
                    $r->storefront = $res['apple:ios']['storefront'];
                    $r->store = $res['apple:ios']['store'];
                    $r->save();
                }

                $i += 1;
            }

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'App subscription report added.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);

            return $this->info('Subscription Report added');
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
