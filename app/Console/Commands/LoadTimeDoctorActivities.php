<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorActivity;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorProject;
use App\TimeDoctor\TimeDoctorTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class LoadTimeDoctorActivities extends Command
{
    public $TIME_DOCTOR_USER_ID;

    public $TIME_DOCTOR_AUTH_TOKEN;

    public $TIME_DOCTOR_COMPANY_ID;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:load_time_doctor_activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load activities for users per task from TimeDoctor';

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
        ini_set('max_execution_time', 0);
        $time_doctor_members = TimeDoctorMember::groupBy('user_id')->get();
        $time_doctor_accounts = TimeDoctorAccount::where('auth_token', '!=', '')->get();
        Timedoctor::getInstance();

        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            foreach ($time_doctor_accounts as $account) {
                $this->TIME_DOCTOR_AUTH_TOKEN = $account->auth_token;
                $this->TIME_DOCTOR_COMPANY_ID = $account->company_id;
                $this->refreshActivityList();
                $this->startGetTaskList();
            }

            foreach ($time_doctor_members as $member) {
                if (($member->account_detail) && $member->account_detail->auth_token != '') {
                    $this->TIME_DOCTOR_USER_ID = $member->user_id;
                    $this->TIME_DOCTOR_AUTH_TOKEN = $member->account_detail->auth_token;
                    $this->TIME_DOCTOR_COMPANY_ID = $member->account_detail->company_id;
                    $this->refreshActivityList();
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshActivityList()
    {
        $timedoctor = Timedoctor::getInstance();
        try {
            $this->timedoctor = $timedoctor->authenticate(false, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->getActivitiesBetween();
        } catch (Exception $e) {
            $this->timedoctor = $timedoctor->authenticate(true, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->getActivitiesBetween();
        }
    }

    private function getActivitiesBetween()
    {
        try {
            $activities = $this->timedoctor->getActivityListCommand($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN, $this->TIME_DOCTOR_USER_ID);
            foreach ($activities as $activity) {
                TimeDoctorActivity::create([
                    'user_id' => $activity['user_id'],
                    'task_id' => is_null($activity['task_id']) ? 0 : $activity['task_id'],
                    'starts_at' => $activity['starts_at'],
                    'tracked' => $activity['tracked'],
                    'project_id' => $activity['project'],
                ]);
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());

            return false;
        }
    }

    private function startGetTaskList()
    {
        $tasks = $this->timedoctor->getTaskList($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN);
        if (! empty($tasks)) {
            foreach ($tasks->data as $task) {
                $taskExist = TimeDoctorTask::where('time_doctor_task_id', $task->id)->first();
                if (! $taskExist) {
                    if (! empty($task->name)) {
                        if (isset($task->project)) {
                            $project = TimeDoctorProject::where('time_doctor_project_id', $task->project->id)->first();
                            TimeDoctorTask::create([
                                'time_doctor_task_id' => $task->id,
                                'project_id' => $project->id,
                                'time_doctor_project_id' => $task->project->id,
                                'time_doctor_company_id' => $this->TIME_DOCTOR_COMPANY_ID,
                                'summery' => $task->name,
                                'description' => (isset($task->description) && $task->description != '') ? $task->description : '',
                                'time_doctor_account_id' => $this->TIME_DOCTOR_USER_ID,
                            ]);
                        }
                    }
                } else {
                    $taskExist->summery = $task->name;
                    $taskExist->description = (isset($task->description) && $task->description != '') ? $task->description : '';
                    $taskExist->time_doctor_account_id = $this->TIME_DOCTOR_USER_ID;
                    $taskExist->save();
                }
            }
        }
    }
}
