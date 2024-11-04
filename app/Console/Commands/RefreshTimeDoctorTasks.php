<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorProject;
use App\TimeDoctor\TimeDoctorTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RefreshTimeDoctorTasks extends Command
{
    public $TIME_DOCTOR_USER_ID;

    public $TIME_DOCTOR_AUTH_TOKEN;

    public $TIME_DOCTOR_COMPANY_ID;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:refresh_tasks {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Time Doctor Tasks';

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
        $time_doctor_account = TimeDoctorAccount::find($this->argument('id'));
        $this->TIME_DOCTOR_USER_ID = $time_doctor_account->id;
        $this->TIME_DOCTOR_AUTH_TOKEN = $time_doctor_account->auth_token;
        $this->TIME_DOCTOR_COMPANY_ID = $time_doctor_account->company_id;
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $this->refreshTaskList();
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshTaskList()
    {
        $timedoctor = Timedoctor::getInstance();
        try {
            $this->timedoctor = $timedoctor->authenticate(false, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetTaskList();
        } catch (Exception $e) {
            $this->timedoctor = $timedoctor->authenticate(true, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetTaskList();
        }
    }

    private function startGetTaskList()
    {
        try {
            $tasks = $this->timedoctor->getTaskList($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN);
            if (! empty($tasks)) {
                $record = count($tasks->data);
                foreach ($tasks->data as $task) {
                    echo $task->id.' Record started';
                    echo PHP_EOL;
                    $taskExist = TimeDoctorTask::where('time_doctor_task_id', $task->id)->first();
                    if (! $taskExist) {
                        if (! empty($task->name)) {
                            if (isset($task->project) && isset($task->project->id)) {
                                $project = TimeDoctorProject::where('time_doctor_project_id', $task->project->id)->first();
                                TimeDoctorTask::create([
                                    'time_doctor_task_id' => $task->id,
                                    'project_id' => $project ? $project->id : 1,
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

                    echo $task->id.' Record ended';
                    echo PHP_EOL;
                    echo 'Total Record Left :'.$record--;
                    echo PHP_EOL;
                }
            }
        } catch (Exception $e) {
            dd($e);
        }
    }
}
