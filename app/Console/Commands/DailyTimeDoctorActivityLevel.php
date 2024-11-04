<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\TimeDoctor\TimeDoctorActivity;
use App\TimeDoctor\TimeDoctorActivityNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyTimeDoctorActivityLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:daily-activity-level-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Daily TimeDoctor Activity level check';

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
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $checkDate = date('Y-m-d');

            // check daily time doctor level from activities
            $activities = TimeDoctorActivity::join('time_doctor_members as hm', 'hm.time_doctor_user_id', 'time_doctor_activities.user_id')
                ->join('users as u', 'u.id', 'hm.user_id')
                ->whereDate('starts_at', $checkDate)
                ->whereNotNull('hm.user_id')
                ->groupBy('time_doctor_activities.user_id')
                ->select([
                    DB::raw('sum(time_doctor_activities.tracked) as total_track'),
                    DB::raw('sum(time_doctor_activities.overall) as total_spent'),
                    'hm.*',
                    'hm.user_id as erp_user_id',
                    'u.name as user_name',
                    'u.phone as phone_number',
                ])->get();

            if (! $activities->isEmpty()) {
                foreach ($activities as $act) {
                    $hsn = new TimeDoctorActivityNotification;
                    $hsn->fill([
                        'user_id' => $act->erp_user_id,
                        'time_doctor_user_id' => $act->time_doctor_user_id,
                        'total_track' => $act->total_track,
                        'start_date' => $checkDate,
                        'end_date' => $checkDate,
                        'min_percentage' => (float) $act->min_activity_percentage,
                        'actual_percentage' => (float) ($act->total_spent * 100) / $act->total_track,
                    ]);
                    $hsn->save();
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
