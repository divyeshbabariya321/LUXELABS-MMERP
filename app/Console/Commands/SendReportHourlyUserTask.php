<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\HubstaffTrait;
use App\Http\Controllers\WhatsAppController;
use App\Hubstaff\HubstaffActivity;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReportHourlyUserTask extends Command
{
    use HubstaffTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:send_report_hourly_user_task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends hubstaff report to whatsapp based every hour if user not select task ';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $users = HubstaffActivity::select('hubstaff_activities.user_id', 'hubstaff_members.hubstaff_user_id', 'users.*')
                ->leftJoin('hubstaff_members', 'hubstaff_activities.user_id', 'hubstaff_members.hubstaff_user_id')
                ->leftJoin('users', 'hubstaff_members.user_id', 'users.id')
                ->where('task_id', 0)
                ->whereDate('starts_at', date('Y-m-d'))
                ->groupBy('user_id')
                ->orderByDesc('id')->get();
            Log::info('Hubstaff task not select Total user : '.count($users));
            foreach ($users as $user) {
                if ($user->whatsapp_number) {
                    app(WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, 'Please select task on hubstaff', true);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            Log::error('Hubstaff task not select Total user : '.$e->getMessage());
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
