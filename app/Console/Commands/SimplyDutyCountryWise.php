<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJobReport;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffActivityNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimplyDutyCountryWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simpy-duty:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simply Duty calculate country wise';

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

        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $checkDate = date('Y-m-d', strtotime('2020-06-17'));

        // check daily hubstaff  level from activities
        $activities = HubstaffActivity::join('hubstaff_members as hm', 'hm.hubstaff_user_id', 'hubstaff_activities.user_id')
            ->join('users as u', 'u.id', 'hm.user_id')
            ->whereDate('starts_at', $checkDate)
            ->whereNotNull('hm.user_id')
            ->groupBy('hubstaff_activities.user_id')
            ->select([
                DB::raw('sum(hubstaff_activities.tracked) as total_track'),
                DB::raw('sum(hubstaff_activities.overall) as total_spent'),
                'hm.*',
                'hm.user_id as erp_user_id',
                'u.name as user_name',
                'u.phone as phone_number',
            ])->get();

        if (! $activities->isEmpty()) {
            foreach ($activities as $act) {
                $actualPercentage = (float) ($act->total_spent * 100) / $act->total_track;
                // start to add report
                $hubsaffReport = [];
                if ($act->min_activity_percentage > 0 && ($act->min_activity_percentage > $actualPercentage)) {
                    $userMessage = 'Your Daily activity for date '.$checkDate.' is lower then '.$act->min_activity_percentage;
                    ChatMessage::sendWithChatApi($act->phone_number, null, $userMessage);
                    $hubsaffReport[] = $act->user_name.' : Daily activity for date '.$checkDate.' is lower then '.$act->min_activity_percentage;
                }

                $hsn = new HubstaffActivityNotification;
                $hsn->fill([
                    'user_id' => $act->erp_user_id,
                    'start_date' => $checkDate,
                    'end_date' => $checkDate,
                    'min_percentage' => (float) $act->min_activity_percentage,
                    'actual_percentage' => (float) ($act->total_spent * 100) / $act->total_track,
                ]);
                $hsn->save();
            }

            $message = implode(PHP_EOL, $hubsaffReport);
            ChatMessage::sendWithChatApi('971502609192', null, $message);
        }

        $report->update(['end_time' => Carbon::now()]);
    }
}
