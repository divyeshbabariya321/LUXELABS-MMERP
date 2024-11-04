<?php

namespace App\Library\TimeDoctor\Src;

use Illuminate\Support\Facades\Auth;
use App\TimeDoctor\TimeDoctorLog;
use App\TimeDoctor\TimeDoctorMember;
use Illuminate\Support\Facades\Http;
use App\TimeDoctor\TimeDoctorAccount;
use Exception;

class Timedoctor
{
    protected static ?Timedoctor $instance = null;

    protected string $base_url = 'https://api2.timedoctor.com/api/1.0/';

    /**
     * @var mixed|string
     */
    private mixed $accessToken;

    public static function getInstance(): ?Timedoctor
    {
        if (is_null(self::$instance)) {
            self::$instance = new Timedoctor();
        }

        return self::$instance;
    }

    public function generateAuthToken($account_id): bool
    {
        $getTimeDoctorAccount = TimeDoctorAccount::find($account_id);
        $url                  = $this->base_url . 'authorization/login';
        try {
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'email'       => $getTimeDoctorAccount->time_doctor_email,
                'password'    => $getTimeDoctorAccount->time_doctor_password,
                'permissions' => 'write',
            ]);
            $parsedResponse                   = $http->json();
            $getTimeDoctorAccount->auth_token = $parsedResponse->data->token;
            $getTimeDoctorAccount->company_id = $parsedResponse->data->companies[0]->id;

            return $getTimeDoctorAccount->save();
        } catch (Exception $e) {
            return false;
        }
    }

    public function authenticate($generate = true, $access_token = ''): static
    {
        $this->accessToken = $access_token;

        return $this;
    }

    public function getMemberList($company_id, $access_token)
    {
        $url      = $this->base_url . 'users?company=' . $company_id . '&token=' . $access_token;
        $http     = Http::get($url);
        $response = $http->json();

        TimeDoctorLog::create([
            'url'           => $url,
            'response'      => $http->body(),
            'user_id'       => Auth::user()->id,
            'response_code' => $http->status(),
        ]);

        return $response;
    }

    public function getProjectList($company_id, $access_token)
    {
        $url  = $this->base_url . 'projects?company=' . $company_id . '&token=' . $access_token;
        $http = Http::get($url);

        return $http->json();
    }

    public function createProject($company_id, $access_token, $project_data): bool
    {
        try {
            $url  = $this->base_url . 'projects?company=' . $company_id . '&token=' . $access_token;
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'name'        => $project_data['time_doctor_project_name'],
                'description' => $project_data['time_doctor_project_description'],
            ]);

            return $http->status() == 200;
        } catch (Exception) {
            return false;
        }
    }

    public function getTaskList($company_id, $access_token)
    {
        $url  = $this->base_url . 'tasks?company=' . $company_id . '&token=' . $access_token;
        $http = Http::get($url);

        return $http->json();
    }

    public function createTask($company_id, $access_token, $project_data): bool
    {
        try {
            [$url, $response, $parsedResponse] = $this->createBaseTask($company_id, $access_token, $project_data);

            return $response->status() == 200;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createGeneralTask($company_id, $access_token, $project_data, $task_id, $type): array
    {
        try {
            [$url, $response, $parsedResponse] = $this->createBaseTask($company_id, $access_token, $project_data);
            $responseCode                      = $response->status();
            TimeDoctorLog::create([
                'url'     => $url,
                'payload' => json_encode([
                    'project'     => ['id' => $project_data['time_doctor_project'], 'weight' => 0],
                    'name'        => $project_data['time_doctor_task_name'],
                    'description' => $project_data['time_doctor_task_description'],
                ]),
                'response'      => $response->body(),
                'user_id'       => Auth::user()->id,
                'response_code' => $responseCode,
                'dev_task_id'   => $type == 'DEVTASK' ? $task_id : null,
                'task_id'       => $type == 'TASK' ? $task_id : null,
            ]);

            return ['code' => $responseCode, 'data' => ['id' => $parsedResponse->data->id], 'message' => $response->getReasonPhrase()];
        } catch (Exception $e) {
            $responseCode = $e->getCode();
            TimeDoctorLog::create([
                'url'     => $url ?? '',
                'payload' => json_encode([
                    'project'     => ['id' => $project_data['time_doctor_project'], 'weight' => 0],
                    'name'        => $project_data['time_doctor_task_name'],
                    'description' => $project_data['time_doctor_task_description'],
                ]),
                'response'      => $e->getMessage(),
                'user_id'       => Auth::user()->id,
                'response_code' => $responseCode,
                'dev_task_id'   => $type == 'DEVTASK' ? $task_id : null,
                'task_id'       => $type == 'TASK' ? $task_id : null,
            ]);

            return ['code' => $responseCode, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function updateTask($company_id, $access_token, $project_data): bool
    {
        try {
            $url  = $this->base_url . 'tasks/' . $project_data['taskId'] . '?company=' . $company_id . '&token=' . $access_token;
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'project'     => ['id' => $project_data['taskProject'], 'weight' => 0],
                'name'        => $project_data['taskName'],
                'description' => $project_data['taskDescription'],
            ]);

            return $http->status() == 200;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateProject($company_id, $access_token, $project_data): bool
    {
        try {
            $url  = $this->base_url . 'projects/' . $project_data['projectId'] . '?company=' . $company_id . '&token=' . $access_token;
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'name'        => $project_data['projectName'],
                'description' => $project_data['projectDescription'],
            ]);

            return $http->status() == 200;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getActivityListOld($company_id, $access_token, $user_id, $start = '', $end = ''): array
    {
        $members        = TimeDoctorMember::where('time_doctor_account_id', $user_id)->select('time_doctor_user_id')->get();
        $memberId       = implode(',', array_column($members->toArray(), 'time_doctor_user_id'));
        $end            = date('Y-m-d', strtotime($end . ' +1 day'));
        $url            = $this->base_url . 'activity/worklog?company=' . $company_id . '&user=' . $memberId . '&from=' . $start . '&to=' . $end . '&token=' . $access_token;
        $http           = Http::get($url);
        $parsedResponse = $http->json();
        $activities     = [];

        foreach ($parsedResponse->data as $activity_data) {
            foreach ($activity_data as $activity) {
                $res = [
                    'user_id'   => $activity->userId,
                    'task_id'   => $activity->taskId,
                    'starts_at' => $activity->start,
                    'tracked'   => $activity->time,
                    'project'   => $activity->projectId,
                ];
                $activities[] = $res;
            }
        }

        return $activities;
    }

    public function getActivityList($company_id, $access_token, $user_id, $start = '', $end = ''): array
    {
        $members    = TimeDoctorMember::where('user_id', $user_id)->get();
        $activities = [];
        foreach ($members as $member) {
            $end        = date('Y-m-d', strtotime($end . ' +1 day'));
            $activities = $this->getArr($member, $start, $end, $activities);
        }

        return $activities;
    }

    public function getActivityListCommand($company_id, $access_token, $user_id): array
    {
        $members    = TimeDoctorMember::where('user_id', $user_id)->get();
        $start      = date('Y-m-d');
        $end        = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $activities = [];
        foreach ($members as $member) {
            $activities = $this->getArr($member, $start, $end, $activities);
        }

        return $activities;
    }

    public function sendSingleInvitation($company_id, $access_token, $data = []): array
    {
        try {
            $url  = 'https://api2.timedoctor.com/api/1.1/invitations?company=' . $company_id . '&token=' . $access_token;
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'email'       => $data['email'] ?? '',
                'name'        => $data['name'] ?? '',
                'role'        => $data['role'] ?? '',
                'employeeId'  => $data['employeeId'] ?? '',
                'noSendEmail' => $data['noSendEmail'] ?? 'false',
            ]);
            $parsedResponse = $http->json();

            return ['code' => $http->status(), 'data' => ['time_doctor_user_id' => $parsedResponse->data->userId], 'message' => $http->reason()];
        } catch (Exception $e) {
            return ['code' => $e->getCode(), 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function sendBulkInvitation($company_id, $access_token, $data): array
    {
        try {
            $url  = $this->base_url . 'invitations/bulk?company=' . $company_id . '&token=' . $access_token;
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);
            $parsedResponse = $http->json();

            return ['code' => $http->status(), 'data' => ['response' => $parsedResponse], 'message' => $http->reason()];
        } catch (Exception $e) {
            return ['code' => $e->getCode(), 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function getArr(mixed $member, string $start, string $end, array $activities): array
    {
        $url            = $this->base_url . 'activity/worklog?company=' . $member->account_detail->company_id . '&user=' . $member->time_doctor_user_id . '&from=' . $start . '&to=' . $end . '&token=' . $member->account_detail->auth_token;
        $http           = Http::get($url);
        $parsedResponse = $http->json();

        foreach ($parsedResponse->data as $activity_data) {
            foreach ($activity_data as $activity) {
                $res = [
                    'user_id'   => $activity->userId,
                    'task_id'   => $activity->taskId,
                    'starts_at' => $activity->start,
                    'tracked'   => $activity->time,
                    'project'   => $activity->projectId,
                ];
                $activities[] = $res;
            }
        }

        return $activities;
    }

    public function createBaseTask($company_id, $access_token, $project_data): array
    {
        $url  = $this->base_url . 'tasks?company=' . $company_id . '&token=' . $access_token;
        $http = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'project'     => ['id' => $project_data['time_doctor_project'], 'weight' => 0],
            'name'        => $project_data['time_doctor_task_name'],
            'description' => $project_data['time_doctor_task_description'],
        ]);

        $parsedResponse = $http->json();

        return [$url, $http, $parsedResponse];
    }
}
