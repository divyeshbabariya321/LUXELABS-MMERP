<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorProject;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RefreshTimeDoctorProjects extends Command
{
    public $TIME_DOCTOR_USER_ID;

    public $TIME_DOCTOR_AUTH_TOKEN;

    public $TIME_DOCTOR_COMPANY_ID;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:refresh_projects {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Time Doctor Projects';

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
            $this->refreshProjectList();
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshProjectList()
    {
        $timedoctor = Timedoctor::getInstance();
        try {
            $this->timedoctor = $timedoctor->authenticate(false, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetProject();
        } catch (Exception $e) {
            $this->timedoctor = $timedoctor->authenticate(true, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetProject();
        }
    }

    private function startGetProject()
    {
        try {
            $projects = $this->timedoctor->getProjectList($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN);
            if (! empty($projects)) {
                $record = count($projects->data);
                foreach ($projects->data as $project) {
                    echo $project->id.' Record started';
                    echo PHP_EOL;
                    $projectExist = TimeDoctorProject::where('time_doctor_project_id', $project->id)->first();
                    if (! $projectExist) {
                        if (! empty($project->name)) {
                            TimeDoctorProject::create([
                                'time_doctor_project_id' => $project->id,
                                'time_doctor_account_id' => $this->TIME_DOCTOR_USER_ID,
                                'time_doctor_company_id' => $this->TIME_DOCTOR_COMPANY_ID,
                                'time_doctor_project_name' => $project->name,
                                'time_doctor_project_description' => (isset($project->description) && $project->description != '') ? $project->description : '',
                            ]);
                        }
                    } else {
                        $projectExist->time_doctor_project_name = $project->name;
                        $projectExist->time_doctor_project_description = (isset($project->description) && $project->description != '') ? $project->description : '';
                        $projectExist->time_doctor_account_id = $this->TIME_DOCTOR_USER_ID;
                        $projectExist->save();
                    }

                    echo $project->id.' Record ended';
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
