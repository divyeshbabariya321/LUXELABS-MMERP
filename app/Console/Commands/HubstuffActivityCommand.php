<?php

namespace App\Console\Commands;

use App\CashFlow;
use App\Http\Controllers\HubstaffActivitiesController;
use App\HubstaffActivityByPaymentFrequency;
use App\Loggers\HubstuffCommandLog;
use App\Loggers\HubstuffCommandLogMessage;
use App\PayentMailData;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HubstuffActivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HubstuffActivity:Command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tasks_controller = new HubstaffActivitiesController;
        $users = User::where('payment_frequency', '!=', '')->get();

        $HubstuffCommandLog = $this->initializeCommandLog(count($users));
        $HubstuffCommandLog_id = $HubstuffCommandLog->id;

        $weekly = $biweekly = $fornightly = $monthly = 0;


        foreach ($users as $user) {
            $payment_frequency = $user->payment_frequency;
            $last_mail_sent = $user->last_mail_sent_payment;
            $today = Carbon::now()->toDateTimeString();

            $HubstuffCommandLogMessage = $this->logUserCommand($HubstuffCommandLog_id, $user);

            [$from, $to, $get_activity] = $this->processPaymentFrequency(
                $user,
                $payment_frequency,
                $last_mail_sent,
                $today,
                $HubstuffCommandLogMessage
            );

            $HubstuffCommandLogMessage->start_date = $from;
            $HubstuffCommandLogMessage->end_date = $to;
            $HubstuffCommandLogMessage->save();

            if ($get_activity) {
                $req = $this->prepareRequestData($user, $HubstuffCommandLogMessage->id, $from, $to);
                $res = $tasks_controller->getActivityUsers(new Request, $req, 'HubstuffActivityCommand');

                $this->handleActivityResponse($res, $user, $payment_frequency, $today);
            }

            ${$payment_frequency}++;
        }

        $this->updateFrequencyCounts($HubstuffCommandLog_id, $weekly, $biweekly, $fornightly, $monthly);
    }

    private function initializeCommandLog(int $userCount): HubstuffCommandLog
    {
        $HubstuffCommandLog = new HubstuffCommandLog;
        $HubstuffCommandLog->messages = 'Total user for payment frequency is:'.$userCount;
        $HubstuffCommandLog->date = Carbon::now()->toDateTimeString();
        $HubstuffCommandLog->day = Carbon::now()->dayOfWeek;
        $HubstuffCommandLog->userCount = $userCount;
        $HubstuffCommandLog->save();

        return $HubstuffCommandLog;
    }

    private function logUserCommand(int $HubstuffCommandLog_id, User $user): HubstuffCommandLogMessage
    {
        $HubstuffCommandLogMessage = new HubstuffCommandLogMessage;
        $HubstuffCommandLogMessage->hubstuff_command_log_id = $HubstuffCommandLog_id;
        $HubstuffCommandLogMessage->user_id = $user->id;
        $HubstuffCommandLogMessage->frequency = $user->payment_frequency;
        $HubstuffCommandLogMessage->save();

        return $HubstuffCommandLogMessage;
    }

    private function processPaymentFrequency(User $user, string $payment_frequency, ?string $last_mail_sent, string $today, HubstuffCommandLogMessage $logMessage): array
    {
        $from = Carbon::createFromFormat('Y-m-d H:s:i', $today);
        $to = Carbon::now()->startOfMonth();
        $get_activity = false;

        switch ($payment_frequency) {
            case 'weekly':
                $logMessage->message = 'Go to weekly condition';
                if (Carbon::now()->dayOfWeek == Carbon::FRIDAY) {
                    $get_activity = true;
                }
                break;
            case 'biweekly':
                $logMessage->message = 'Go to biweekly condition';
                if (Carbon::now()->dayOfWeek == Carbon::MONDAY || Carbon::now()->dayOfWeek == Carbon::FRIDAY) {
                    $get_activity = true;
                }
                break;
            case 'fornightly':
                $logMessage->message = 'Go to fornightly condition';
                if (Carbon::now()->format('d') == '1' || Carbon::now()->format('d') == '16') {
                    $get_activity = true;
                }
                break;
            case 'monthly':
                $logMessage->message = 'Go to MONTHLY condition';
                if (Carbon::now()->format('d') == '1') {
                    $get_activity = true;
                }
                break;
        }

        return [$from, $to, $get_activity];
    }

    private function prepareRequestData(User $user, int $HubstuffCommandLogMessage_id, string $from, string $to): Request
    {
        $req = new Request;
        $req->request->add([
            'activity_command' => true,
            'user' => $user,
            'user_id' => $user->id,
            'HubstuffCommandLogMessage_id' => $HubstuffCommandLogMessage_id,
            'start_date' => $from,
            'end_date' => $to,
        ]);

        return $req;
    }

    private function handleActivityResponse($res, User $user, string $payment_frequency, string $today)
    {
        if ($res) {
            $path = $res['file_data'];
            Auth::logout($user);
            $user->last_mail_sent_payment = $today;
            $user->save();

            $hubstaff_activity = new HubstaffActivityByPaymentFrequency;
            $hubstaff_activity->user_id = $user->id;
            $hubstaff_activity->activity_excel_file = $path;
            $hubstaff_activity->type = $payment_frequency;
            $hubstaff_activity->payment_receipt_ids = isset($res['receipt_ids']) ? json_encode($res['receipt_ids']) : '';
            $hubstaff_activity->save();

            $this->createCashFlow($hubstaff_activity, $user, $payment_frequency);
        }
    }

    private function createCashFlow(HubstaffActivityByPaymentFrequency $activity, User $user, string $frequency)
    {
        $cashflow = new CashFlow;
        $cashflow->date = $activity->created_at;
        $cashflow->user_id = $user->id;
        $cashflow->cash_flow_able_id = $activity->id;
        $cashflow->cash_flow_able_type = HubstaffActivityByPaymentFrequency::class;
        $cashflow->description = "$frequency Frequency Payment";
        $cashflow->type = 'pending';
        $cashflow->status = 1;

        $paymentData = PayentMailData::where('user_id', $user->id)->orderByDesc('id')->first();
        $cashflow->amount = $paymentData->total_balance ?? 0;
        $cashflow->save();
    }

    private function updateFrequencyCounts(int $HubstuffCommandLog_id, int $weekly, int $biweekly, int $fornightly, int $monthly)
    {
        $HubstuffCommandLog = HubstuffCommandLog::find($HubstuffCommandLog_id);
        $HubstuffCommandLog->weekly = $weekly;
        $HubstuffCommandLog->biweekly = $biweekly;
        $HubstuffCommandLog->fornightly = $fornightly;
        $HubstuffCommandLog->monthly = $monthly;
        $HubstuffCommandLog->save();
    }
}
