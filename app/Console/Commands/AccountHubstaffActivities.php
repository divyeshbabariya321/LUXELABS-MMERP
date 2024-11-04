<?php

namespace App\Console\Commands;

use App\DeveloperTask;
use App\Helpers\LogHelper;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;
use App\User;
use App\UserRate;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccountHubstaffActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accounts for the hubstaff activity in terms of payments';

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
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);
            DB::beginTransaction();

            $firstUnaccountedActivity = HubstaffActivity::orderBy('starts_at')->first();
            if (! $firstUnaccountedActivity) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'No found any first unaccounted activity']);

                return 0;
            }

            $today = strtotime('today+00:00');
            $firstUnaccountActivityTime = strtotime($firstUnaccountedActivity->starts_at.' UTC');

            if ($firstUnaccountActivityTime < $today) {
                $this->processActivities($firstUnaccountedActivity, $today);
            }

            DB::commit();
            echo PHP_EOL.'=====DONE===='.PHP_EOL;
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
            DB::rollBack();
            echo PHP_EOL.'=====FAILED===='.PHP_EOL;
        }

        return 1; // Adjust return value as needed
    }

    private function processActivities($firstUnaccountedActivity, $today): void
    {
        $start = $firstUnaccountedActivity->starts_at; // inclusive
        $endTime = strtotime($start) + (1 * 24 * 60 * 60);
        $end = date('Y-m-d', $endTime).' 23:59:59'; // exclusive

        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Processing activities']);

        $userRatesForStartOfDayYesterday = UserRate::latestRatesBeforeTime($end);
        $rateChangesForYesterday = UserRate::rateChangesForDate($start, $end);
        $activities = HubstaffActivity::getActivitiesBetween($start, $end);

        $accountingEntries = $this->prepareAccountingEntries($activities, $userRatesForStartOfDayYesterday, $rateChangesForYesterday, $start, $end);
        $this->updateHubstaffPaymentAccounts($accountingEntries, $start, $end);
    }

    private function prepareAccountingEntries($activities, $userRatesForStartOfDayYesterday, $rateChangesForYesterday, $start, $end): array
    {
        $userId = $activities->pluck('system_user_id')->unique()->filter(fn ($id) => $id > 0)->toArray();
        $users = User::whereIn('id', $userId)->get();

        $accountingEntries = [];
        foreach ($users as $user) {
            $accountingEntry = $this->initializeAccountingEntry($user, $end);
            $rates = $this->getRates($user, $userRatesForStartOfDayYesterday, $rateChangesForYesterday, $start, $end);
            $userActivities = $activities->filter(fn ($activity) => $activity->system_user_id === $user->id);

            if (count($rates) === 0) {
                $this->handleNoRates($userActivities, $accountingEntry);
            } else {
                $this->calculateEarnings($userActivities, $accountingEntry, $rates);
            }

            $accountingEntries[] = $accountingEntry;
        }

        return $accountingEntries;
    }

    private function initializeAccountingEntry($user, $end): array
    {
        return [
            'user' => $user->id,
            'accountedTime' => $end,
            'activityIds' => [],
            'amount' => 0,
            'hrs' => 0,
            'tasks' => [],
        ];
    }

    private function getRates($user, $userRatesForStartOfDayYesterday, $rateChangesForYesterday, $start, $end): array
    {
        $rates = [];
        $individualRatesStartOfDayYesterday = $userRatesForStartOfDayYesterday->first(fn ($rate) => $rate->user_id == $user->id);

        if ($individualRatesStartOfDayYesterday) {
            $rates[] = [
                'start_date' => $start,
                'rate' => $individualRatesStartOfDayYesterday->hourly_rate,
                'currency' => $individualRatesStartOfDayYesterday->currency,
            ];
        }

        $rateChangesYesterdayForUser = $rateChangesForYesterday->filter(fn ($rate) => $rate->user_id == $user->id);
        foreach ($rateChangesYesterdayForUser as $rate) {
            $rates[] = [
                'start_date' => $rate->start_date,
                'rate' => $rate->hourly_rate,
                'currency' => $rate->currency,
            ];
        }

        usort($rates, fn ($a, $b) => strtotime($a['start_date']) - strtotime($b['start_date']));

        if (count($rates) > 0) {
            $lastEntry = end($rates);
            $rates[] = [
                'start_date' => $end,
                'rate' => $lastEntry['rate'],
                'currency' => $lastEntry['currency'],
            ];
        }

        return $rates;
    }

    private function handleNoRates($userActivities, &$accountingEntry): void
    {
        $accountingEntry['activityIds'] = $userActivities->pluck('id')->toArray();
    }

    private function calculateEarnings($userActivities, &$accountingEntry, $rates): void
    {
        foreach ($userActivities as $activity) {
            $accountingEntry['activityIds'][] = $activity->id;

            if (empty($accountingEntry['tasks'][$activity->task_id])) {
                $accountingEntry['tasks'][$activity->task_id] = $activity->tracked / 60;
            } else {
                $accountingEntry['tasks'][$activity->task_id] += $activity->tracked / 60;
            }

            for ($i = 0; $i < count($rates) - 1; $i++) {
                $startRate = $rates[$i];
                $endRate = $rates[$i + 1];

                if ($activity->starts_at >= $startRate['start_date'] && $activity->starts_at < $endRate['start_date']) {
                    $earnings = $activity->tracked * ($startRate['rate'] / 60 / 60);
                    $accountingEntry['amount'] += $earnings;
                    $accountingEntry['hrs'] += (float) $activity->tracked / 60 / 60;
                    break;
                }
            }
        }
    }

    private function updateHubstaffPaymentAccounts(array $accountingEntries, string $start, string $end): void
    {
        foreach ($accountingEntries as $entry) {
            $paymentAccount = new HubstaffPaymentAccount;
            $paymentAccount->user_id = $entry['user'];
            $paymentAccount->accounted_at = $entry['accountedTime'];
            $paymentAccount->amount = $entry['amount'];
            $paymentAccount->hrs = $entry['hrs'];
            $paymentAccount->billing_start = $start;
            $paymentAccount->billing_end = $end;
            $paymentAccount->rate = $entry['hrs'] > 0 ? (float) $entry['amount'] / $entry['hrs'] : 0;
            $paymentAccount->payment_currency = 'INR';
            $paymentAccount->total_payout = $entry['amount'] * 68;
            $paymentAccount->ex_rate = 68;
            $paymentAccount->save();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved hubstaff payment account detail by ID:'.$paymentAccount->id]);

            foreach ($entry['activityIds'] as $activityId) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Update hubstaff activity detail by ID:'.$activityId]);
                HubstaffActivity::where('id', $activityId)->update(['hubstaff_payment_account_id' => $paymentAccount->id]);
            }

            $this->updateDeveloperTasks($entry['tasks']);
            $this->updateUnaccountedActivities($entry['activityIds']);
        }
    }

    private function updateDeveloperTasks(array $tasks): void
    {
        foreach ($tasks as $taskId => $task) {

            $developerTask = DeveloperTask::where('hubstaff_task_id', $taskId)->first();

            if ($developerTask) {
                $developerTask->estimate_minutes += $task;
                $developerTask->save();
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Update developer task estimation by ID:'.$developerTask->id]);
            }
        }
    }

    private function updateUnaccountedActivities(array $activityIds): void
    {
        foreach ($activityIds as $activityId) {
            $activity = HubstaffActivity::find($activityId);
            if ($activity) {
                $activity->hubstaff_payment_account_id = -1;
                $activity->save();
            }
        }
    }
}
