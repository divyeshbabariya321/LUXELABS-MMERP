<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorLog;
use App\TimeDoctor\TimeDoctorMember;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class RefreshTimeDoctorUsers extends Command
{
    public $TIME_DOCTOR_USER_ID;

    public $TIME_DOCTOR_AUTH_TOKEN;

    public $TIME_DOCTOR_COMPANY_ID;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:refresh_users {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Time Doctor Users';

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
            $this->refreshUserList();
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshUserList()
    {
        $timedoctor = Timedoctor::getInstance();
        try {
            $this->timedoctor = $timedoctor->authenticate(false, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetUser();
        } catch (Exception $e) {
            $this->timedoctor = $timedoctor->authenticate(true, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->startGetUser();
        }
    }

    private function startGetUser()
    {
        try {
            $members = $this->timedoctor->getMemberList($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN);
            if (! empty($members)) {
                $record = count($members->data);
                foreach ($members->data as $member) {
                    echo $member->id.' Record started';
                    echo PHP_EOL;
                    $memeberExist = TimeDoctorMember::where('time_doctor_user_id', $member->id)->first();
                    if (! $memeberExist) {
                        if (! empty($member->email)) {
                            $userExist = User::where('email', $member->email)->first();
                            TimeDoctorMember::create([
                                'time_doctor_user_id' => $member->id,
                                'email' => $member->email,
                                'time_doctor_account_id' => $this->TIME_DOCTOR_USER_ID,
                                'user_id' => ($userExist) ? $userExist->id : null,
                            ]);
                        }
                    } else {
                        $memeberExist->time_doctor_account_id = $this->TIME_DOCTOR_USER_ID;
                        $memeberExist->save();
                    }

                    echo $member->id.' Record ended';
                    echo PHP_EOL;
                    echo 'Total Record Left :'.$record--;
                    echo PHP_EOL;
                }
            }
        } catch (Exception $e) {
            $responseCode = $e->getCode();

            if ($e instanceof \GuzzleHttp\Exception\RequestException) {
                // Capture the URI, request, and response
                $uri = $e->getRequest()->getUri();
                $requestContent = $e->getRequest()->getBody()->getContents();
                $responseContent = $e->getResponse()->getBody()->getContents();

                TimeDoctorLog::create([
                    'url' => $uri->__toString(),
                    'payload' => $requestContent,
                    'response' => $responseContent,
                    'user_id' => Auth::user()->id,
                    'response_code' => $responseCode,
                ]);
            }

            return ['code' => $responseCode, 'data' => [], 'message' => $e->getMessage()];
        }
    }
}
