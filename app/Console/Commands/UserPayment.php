<?php

namespace App\Console\Commands;

use App\Hubstaff\HubstaffActivity;
use App\PaymentReceipt;
use App\TimeDoctor\TimeDoctorActivity;
use App\User;
use App\UserRate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make payment request for users';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $users = User::whereIn('fixed_price_user_or_job', [2, 3])->get();
            $firstEntryInActivity = HubstaffActivity::orderBy('starts_at')->first();

            if ($firstEntryInActivity) {
                $bigining = date('Y-m-d', strtotime($firstEntryInActivity->starts_at));
            } else {
                $bigining = date('Y-m-d');
            }
            Log::info('Users found - '.$users->count().' on Date - '.$bigining);
            foreach ($users as $user) {
                $start = $bigining;
                $end = date('Y-m-d');
                echo PHP_EOL."=====Checking $start - $end for $user->id ====".PHP_EOL;

                Log::info("=====Checking $start - $end for $user->id ====");

                $activityrecords = HubstaffActivity::getTrackedActivitiesBetween($start, $end, $user->id);
                echo PHP_EOL.'===== Result found '.count($activityrecords).' ===='.PHP_EOL;

                Log::info('User ID - '.$user->id);

                $total = 0;
                $minutes = 0;
                $startsAt = null;
                Log::info('Activity Records found - '.count($activityrecords));
                foreach ($activityrecords as $record) {
                    $latestRatesOnDate = UserRate::latestRatesOnDate($record->starts_at, $user->id);
                    if ($record->tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                        $total = $total + ($record->tracked / 60) / 60 * $latestRatesOnDate->hourly_rate;
                        $minutes = $minutes + $record->tracked / 60;
                        $record->paid = 1;
                        $record->save();
                        $startsAt = $record->starts_at;
                    }
                }

                Log::info('Total count - '.$total);

                if ($total > 0) {
                    $total = number_format($total, 2);
                    $paymentReceipt = new PaymentReceipt;
                    $paymentReceipt->worked_minutes = $minutes;
                    $paymentReceipt->status = 'Pending';
                    $paymentReceipt->rate_estimated = $total;
                    $paymentReceipt->date = $startsAt;
                    $paymentReceipt->user_id = $user->id;
                    /*$paymentReceipt->billing_start_date = isset($billingStartDate) ? $billingStartDate : null;
                    $paymentReceipt->billing_end_date = isset($billingEndDate) ? $billingEndDate : $end;*/
                    $paymentReceipt->currency = ''; //we need to change this.
                    if ($user->billing_frequency_day > 0) {
                        $paymentReceipt->billing_due_date = date('Y-m-d', strtotime($startsAt.' +'.$user->billing_frequency_day));
                    }
                    $paymentReceipt->saveWithoutEvents();

                    Log::info('Paymemt Receipt Added - '.$paymentReceipt->id);
                }
            }
            DB::commit();

            echo PHP_EOL.'===== Checking for Time dctor activity ===='.PHP_EOL;
            // Cron for time doctor

            DB::beginTransaction();
            $users = User::whereIn('fixed_price_user_or_job', [2, 3])->get();
            $firstEntryInActivity = TimeDoctorActivity::orderBy('starts_at')->first();

            if ($firstEntryInActivity) {
                $bigining = date('Y-m-d', strtotime($firstEntryInActivity->starts_at));
            } else {
                $bigining = date('Y-m-d');
            }
            Log::info('Users found - '.$users->count().' on Date - '.$bigining);
            foreach ($users as $user) {
                $lastPayment = PaymentReceipt::where('user_id', $user->id)->orderByDesc('date')->first();
                $start = $bigining;
                $end = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 days'));
                echo PHP_EOL."=====Checking $start - $end for $user->id ====".PHP_EOL;

                Log::info("=====Checking $start - $end for $user->id ====");

                $activityrecords = TimeDoctorActivity::getTrackedActivitiesBetween($start, $end, $user->id);
                echo PHP_EOL.'===== Result found '.count($activityrecords).' ===='.PHP_EOL;

                Log::info('User ID - '.$user->id);

                $total = 0;
                $minutes = 0;
                $startsAt = null;
                Log::info('Activity Records found - '.count($activityrecords));
                foreach ($activityrecords as $record) {
                    $latestRatesOnDate = UserRate::latestRatesOnDate($record->starts_at, $user->id);
                    if ($record->tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                        $total = $total + ($record->tracked / 60) / 60 * $latestRatesOnDate->hourly_rate;
                        $minutes = $minutes + $record->tracked / 60;
                        $record->paid = 1;
                        $record->save();
                        $startsAt = $record->starts_at;
                    }
                }

                Log::info('Total count - '.$total);

                if ($total > 0) {
                    $total = number_format($total, 2);
                    $paymentReceipt = new PaymentReceipt;
                    $paymentReceipt->worked_minutes = $minutes;
                    $paymentReceipt->status = 'Pending';
                    $paymentReceipt->rate_estimated = $total;
                    $paymentReceipt->date = $startsAt;
                    $paymentReceipt->user_id = $user->id;
                    $paymentReceipt->currency = '';
                    if ($user->billing_frequency_day > 0) {
                        $paymentReceipt->billing_due_date = date('Y-m-d', strtotime($startsAt.' +'.$user->billing_frequency_day));
                    }
                    $paymentReceipt->saveWithoutEvents();

                    Log::info('Paymemt Receipt Added - '.$paymentReceipt->id);
                }
            }
            DB::commit();
            echo PHP_EOL.'=====DONE===='.PHP_EOL;
        } catch (Exception $e) {
            Log::error($e);
            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL.'=====FAILED===='.PHP_EOL;
        }
    }
}
