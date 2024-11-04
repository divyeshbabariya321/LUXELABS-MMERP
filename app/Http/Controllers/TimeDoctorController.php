<?php

namespace App\Http\Controllers;
use App\Setting;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\User;
use stdClass;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\TimeDoctor\TimeDoctorLog;
use App\TimeDoctor\TimeDoctorTask;
use Illuminate\Support\Facades\DB;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorProject;
use App\Models\TimeDoctorDueDateHistory;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\Models\TimeDoctorAccountRemarkHistory;

class TimeDoctorController extends Controller
{
    public $timedoctor;

    public function __construct()
    {
        $this->timedoctor = Timedoctor::getInstance();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
        $members = TimeDoctorMember::query();
        $users   = User::all('id', 'name');

        if (isset($request->time_doctor_user_id) && $request->time_doctor_user_id != '') {
            $members->where('time_doctor_user_id', 'like', "%$request->time_doctor_user_id%");
        }
        if (isset($request->time_doctor_email) && $request->time_doctor_email != '') {
            $members->where('email', 'like', "%$request->time_doctor_email%");
        }
        if (isset($request->time_doctor_account_id) && count($request->time_doctor_account_id) > 0) {
            $members->whereIn('time_doctor_account_id', $request->time_doctor_account_id);
        }
        if (isset($request->time_doctor_user) && count($request->time_doctor_user) > 0) {
            $members->whereIn('user_id', $request->time_doctor_user);
        }

        $members = $members->get();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('time-doctor.users-list', ['members' => $members, 'users' => $users])->render(),
            ], 200);
        }

        $accountList = TimeDoctorAccount::select('id', 'time_doctor_email')->where('auth_token', '!=', '')->get();

        return view(
            'time-doctor.users',
            [
                'members'     => $members,
                'users'       => $users,
                'accountList' => $accountList,
            ]
        );
    }

    public function getProjects(Request $request)
    {
        $projects = TimeDoctorProject::query();

        if (isset($request->time_doctor_project_id) && $request->time_doctor_project_id != '') {
            $projects->where('time_doctor_project_id', 'like', "%$request->time_doctor_project_id%");
        }
        if (isset($request->time_doctor_company_id) && $request->time_doctor_company_id != '') {
            $projects->where('time_doctor_company_id', 'like', "%$request->time_doctor_company_id%");
        }
        if (isset($request->time_doctor_project_name) && $request->time_doctor_project_name != '') {
            $projects->where('time_doctor_project_name', 'like', "%$request->time_doctor_project_name%");
        }

        if (isset($request->time_doctor_account) && count($request->time_doctor_account) > 0) {
            $projects->whereIn('time_doctor_account_id', $request->time_doctor_account);
        }

        $projects = $projects->get();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('time-doctor.list', ['projects' => $projects])->render(),
            ], 200);
        }

        $accountList = TimeDoctorAccount::select('id', 'time_doctor_email')->where('auth_token', '!=', '')->get();

        return view(
            'time-doctor.projects',
            [
                'projects'    => $projects,
                'accountList' => $accountList,
            ]
        );
    }

    public function getTasks(Request $request)
    {
        $tasks = TimeDoctorTask::query();

        if (isset($request->time_doctor_task_id) && $request->time_doctor_task_id != '') {
            $tasks->where('time_doctor_task_id', 'like', "%$request->time_doctor_task_id%");
        }

        if (isset($request->time_doctor_account_id) && count($request->time_doctor_account_id) > 0) {
            $tasks->whereIn('time_doctor_account_id', $request->time_doctor_account_id);
        }

        if (isset($request->summery) && $request->summery != '') {
            $tasks->where('summery', 'like', "%$request->summery%");
        }

        $tasks = $tasks->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('time-doctor.tasks-list', ['tasks' => $tasks])->render(),
            ], 200);
        }
        $accountList = TimeDoctorAccount::select('id', 'time_doctor_email')->where('auth_token', '!=', '')->get();

        return view(
            'time-doctor.tasks',
            [
                'tasks'       => $tasks,
                'accountList' => $accountList,
            ]
        );
    }

    public function saveUserAccount(Request $request): JsonResponse
    {
        try {
            $time_doctor_acount                       = new TimeDoctorAccount();
            $time_doctor_acount->time_doctor_email    = $request->email;
            $time_doctor_acount->time_doctor_password = $request->password;
            if ($time_doctor_acount->save()) {
                return response()->json(['code' => 200, 'data' => [], 'message' => 'TimeDoctor Account Added successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function getAuthTokens(Request $request): JsonResponse
    {
        try {
            $getToken = $this->timedoctor->generateAuthToken($request->id);
            if ($getToken) {
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Auth token generated successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function displayUserAccountList(Request $request)
    {
        $timeDoctorAccounts = TimeDoctorAccount::all();
        $html               = '';
        $i                  = 1;
        foreach ($timeDoctorAccounts as $account) {
            $html .= '<tr>';
            $html .= '<td>' . $i++ . '</td>';
            $html .= '<td>' . $account->time_doctor_email . '</td>';
            $html .= '<td>' . $account->time_doctor_password . '</td>';
            $html .= '<td>' . $account->created_at . '</td>';
            if ($account->auth_token == '') {
                $html .= "<td><button type='button' class='btn btn-secondary get_token' data-id=" . $account->id . '>Get Token</button></td>';
            } else {
                $html .= "<td style='vertical-align:middle;'>" . $account->auth_token . '</td>';
            }
            $html .= '</tr>';
        }

        return $html;
    }

    public function refreshUsersById(Request $request): RedirectResponse
    {
        Artisan::call('timedoctor:refresh_users', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function refreshProjectsById(Request $request): RedirectResponse
    {
        Artisan::call('timedoctor:refresh_projects', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function saveProject(Request $request): JsonResponse
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->time_doctor_account);
            $companyId          = $time_doctor_acount->company_id;
            $accessToken        = $time_doctor_acount->auth_token;
            $createProject      = $this->timedoctor->createProject($companyId, $accessToken, $request->all());
            if ($createProject) {
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function refreshTasksById(Request $request): RedirectResponse
    {
        Artisan::call('timedoctor:refresh_tasks', ['id' => $request->time_doctor_account]);

        return redirect()->back();
    }

    public function saveTask(Request $request): JsonResponse
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->time_doctor_account);
            $companyId          = $time_doctor_acount->company_id;
            $accessToken        = $time_doctor_acount->auth_token;
            $createTask         = $this->timedoctor->createTask($companyId, $accessToken, $request->all());
            if ($createTask) {
                return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function getTasksById(Request $request): JsonResponse
    {
        try {
            $time_doctortask = TimeDoctorTask::find($request->taskId);
            if ($time_doctortask) {
                $res['name']        = $time_doctortask->summery;
                $res['description'] = $time_doctortask->description;
                $res['project_id']  = $time_doctortask->time_doctor_project_id;

                return response()->json(['code' => 200, 'data' => $res, 'message' => 'Time doctor task data fetched successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function updateTasksById(Request $request): JsonResponse
    {
        try {
            $time_doctor_task = TimeDoctorTask::find($request->time_doctor_task_id);
            if ($time_doctor_task) {
                $res['taskId']          = $time_doctor_task->time_doctor_task_id;
                $res['taskName']        = $request->edit_time_doctor_task_name;
                $res['taskDescription'] = $request->edit_time_doctor_task_description;
                $res['taskProject']     = $time_doctor_task->time_doctor_project_id;
                $companyId              = $time_doctor_task->account->company_id;
                $accessToken            = $time_doctor_task->account->auth_token;
                $updateTask             = $this->timedoctor->updateTask($companyId, $accessToken, $res);
                if ($updateTask) {
                    $time_doctor_task->summery     = $request->edit_time_doctor_task_name;
                    $time_doctor_task->description = $request->edit_time_doctor_task_description;
                    $time_doctor_task->save();

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
                } else {
                    return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function getProjectsById(Request $request): JsonResponse
    {
        try {
            $time_doctor_project = TimeDoctorProject::find($request->projectId);
            if ($time_doctor_project) {
                $res['name']        = $time_doctor_project->time_doctor_project_name;
                $res['description'] = $time_doctor_project->time_doctor_project_description;

                return response()->json(['code' => 200, 'data' => $res, 'message' => 'Time doctor project data fetched successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Program not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function updateProjectById(Request $request): JsonResponse
    {
        try {
            $time_doctor_project = TimeDoctorProject::find($request->time_doctor_program_id);
            if ($time_doctor_project) {
                $res['projectId']          = $time_doctor_project->time_doctor_project_id;
                $res['projectName']        = $request->edit_time_doctor_project_name;
                $res['projectDescription'] = $request->edit_time_doctor_project_description;
                $companyId                 = $time_doctor_project->account_detail->company_id;
                $accessToken               = $time_doctor_project->account_detail->auth_token;
                $updateProject             = $this->timedoctor->updateProject($companyId, $accessToken, $res);
                if ($updateProject) {
                    $time_doctor_project->time_doctor_project_name        = $request->edit_time_doctor_project_name;
                    $time_doctor_project->time_doctor_project_description = $request->edit_time_doctor_project_description;
                    $time_doctor_project->save();

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor project created successfully']);
                } else {
                    return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Task not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function userTreckTime(Request $request): View
    {
        return view('time-doctor.track-users');
    }

    public function linkUser(Request $request): JsonResponse
    {
        $bodyContent     = $request->getContent();
        $jsonDecodedBody = json_decode($bodyContent);

        $userId           = $jsonDecodedBody->user_id;
        $timeDoctorUserId = $jsonDecodedBody->time_doctor_user_id;

        if (! $userId || ! $timeDoctorUserId) {
            return response()->json(
                [
                    'error' => 'Missing parameters',
                ],
                400
            );
        }

        TimeDoctorMember::linkUser($timeDoctorUserId, $userId);

        return response()->json([
            'message' => 'link success',
        ]);
    }

    public function sendInvitations(Request $request): View
    {
        $members = TimeDoctorAccount::where('auth_token', '!=', '')->whereNotNull('company_id')->get();
        $users   = User::all('id', 'name', 'email');

        return view(
            'time-doctor.create-accounts',
            [
                'title'   => 'Create TimeDoctor Account',
                'members' => $members,
                'users'   => $users,
            ]
        );
    }

    public function sendSingleInvitation(Request $request): JsonResponse
    {
        try {
            $data = [
                'email' => $request->email,
                'name'  => $request->name,
            ];
            $userId             = $request->userId;
            $time_doctor_acount = TimeDoctorAccount::find($request->tda);
            if ($time_doctor_acount) {
                $companyId      = $time_doctor_acount->company_id;
                $accessToken    = $time_doctor_acount->auth_token;
                $inviteResponse = $this->timedoctor->sendSingleInvitation($companyId, $accessToken, $data);
                switch ($inviteResponse['code']) {
                    case '401':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctor Account user\'s Token ID is invalid or access is denied.']);
                        break;
                    case '403':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctor Account user don\'t have permission to perform this action']);
                        break;
                    case '409':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Given email is invalid.']);
                        break;
                    case '500':
                    case '422':
                    case '404':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                        break;
                    default:
                        $lastRow = TimeDoctorAccount::create([
                            'time_doctor_email' => $request->email,
                        ]);
                        TimeDoctorMember::create([
                            'time_doctor_user_id'    => $inviteResponse['data']['time_doctor_user_id'],
                            'time_doctor_account_id' => $lastRow->id,
                            'email'                  => $request->email,
                            'user_id'                => $userId,
                        ]);

                        return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor account created successfully']);
                        break;
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function sendBulkInvitation(Request $request): JsonResponse
    {
        try {
            $time_doctor_acount = TimeDoctorAccount::find($request->tda);
            if ($time_doctor_acount) {
                $companyId    = $time_doctor_acount->company_id;
                $accessToken  = $time_doctor_acount->auth_token;
                $users        = User::whereIn('id', $request->ids)->get();
                $payloadUsers = [];
                foreach ($users as $u) {
                    $obj        = new stdClass;
                    $obj->email = $u->email;
                    $obj->name  = $u->name;

                    array_push($payloadUsers, $obj);
                }

                $bulkInvitePayload = [
                    'users'       => $payloadUsers,
                    'noSendEmail' => 'false',
                ];

                $bulkInviteResponse = $this->timedoctor->sendBulkInvitation($companyId, $accessToken, $bulkInvitePayload);
                switch ($bulkInviteResponse['code']) {
                    case '401':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctor Account user\'s Token ID is invalid or access is denied.']);
                        break;
                    case '403':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Time Doctor Account user don\'t have permission to perform this action']);
                        break;
                    case '409':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Given email is invalid.']);
                        break;
                    case '500':
                    case '422':
                    case '404':
                        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
                        break;
                    default:
                        $response = $bulkInviteResponse['data']['response']->data;
                        foreach ($users as $u) {
                            $userId              = $u->id;
                            $email               = $u->email;
                            $time_doctor_user_id = '';
                            foreach ($response as $key => $val) {
                                foreach ($val as $k => $v) {
                                    if ($k == $email && $v->status == 'sent') {
                                        $time_doctor_user_id = $v->userId;
                                    }
                                }
                            }

                            if ($time_doctor_user_id != '') {
                                $lastRow = TimeDoctorAccount::create([
                                    'time_doctor_email' => $email,
                                ]);
                                TimeDoctorMember::create([
                                    'time_doctor_user_id'    => $time_doctor_user_id,
                                    'time_doctor_account_id' => $lastRow->id,
                                    'email'                  => $email,
                                    'user_id'                => $userId,
                                ]);
                            }
                        }

                        return response()->json(['code' => 200, 'data' => [], 'message' => 'Time doctor account created successfully']);
                        break;
                }
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function will retrive the log which are logged while ceating the account
     */
    public function taskCreationLogs(Request $request): View
    {
        try {
            $responseCode = TimeDoctorLog::distinct()->get('response_code')->pluck('response_code');
            $filterUsers  = TimeDoctorLog::with('user')->distinct()->get('user_id')->pluck('user.name', 'user.id');

            $developerTask = TimeDoctorLog::distinct()->whereNotNull('dev_task_id')->get('dev_task_id')->pluck('dev_task_id');
            $generalTask   = TimeDoctorLog::distinct()->whereNotNull('task_id')->get('task_id')->pluck('task_id');

            return view('time-doctor.task-creation-logs', compact('responseCode', 'filterUsers', 'generalTask', 'developerTask'));
        } catch (Exception $e) {
            return view('time-doctor.task-creation-logs');
        }
    }

    public function listTaskCreationLogs(Request $request): JsonResponse
    {
        try {
            $logs = TimeDoctorLog::query()->with(['user']);

            if (! auth()->user()->isAdmin()) {
                $logs->where('user_id', auth()->user()->id);
            }

            if ($request->search_url) {
                $logs->where('url', 'like', "%$request->search_url%");
            }

            if (isset($request->response_code) && ! empty($request->response_code)) {
                $logs->whereIn('response_code', $request->response_code);
            }

            if (isset($request->search_users) && ! empty($request->search_users)) {
                $logs->whereIn('user_id', $request->search_users);
            }

            $dev_task     = [];
            $general_task = [];
            if (isset($request->search_tasks) && ! empty($request->search_tasks)) {
                foreach ($request->search_tasks as $key => $task) {
                    if (str_contains($task, 'DEVTASK-')) {
                        array_push($dev_task, trim($task, 'DEVTASK-'));
                    } else {
                        array_push($general_task, trim($task, 'TASK-'));
                    }
                }
            }

            $logs->where(function ($query) use ($dev_task, $general_task) {
                if (! empty($dev_task)) {
                    $query->orWhereIn('dev_task_id', $dev_task);
                }

                if (! empty($general_task)) {
                    $query->orWhereIn('task_id', $general_task);
                }
            });

            $logs = $logs->paginate(20);

            return response()->json([
                'tbody'      => view('time-doctor.task-creation-logs-list', compact('logs'))->render(),
                'pagination' => $logs->links()->render(),
            ]);
        } catch (Exception $e) {
            $logs = [];

            return response()->json([
                'tbody'      => view('time-doctor.task-creation-logs-list', compact('logs'))->render(),
                'pagination' => '',
            ]);
        }
    }

    public function getTimerAlerts(Request $request): JsonResponse
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        $logs = TimeDoctorLog::query()->with(['user']);

        if (! auth()->user()->isAdmin()) {
            $logs->where('user_id', auth()->user()->id);
        }

        $currentLogs = $logs->where(DB::raw('DATE(created_at)'), $currentDate)->get();
        return response()->json([
            'tbody' => view('partials.modals.timer-alerts-modal-html', compact('currentLogs'))->render(),
            'count' => $currentLogs->count(),
        ]);
    }

    public function listUserAccountList(Request $request): View
    {
        $timeDoctorAccounts       = new TimeDoctorAccount();
        $timeDoctorAccountsEmails = TimeDoctorAccount::distinct('time_doctor_email')->pluck('time_doctor_email');

        $reqAccountsEmail = $request->time_doctor_account_id;

        if ($request->time_doctor_account_id) {
            $timeDoctorAccounts = $timeDoctorAccounts->WhereIn('time_doctor_email', $request->time_doctor_account_id);
        }
        if ($request->date) {
            $timeDoctorAccounts = $timeDoctorAccounts->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        if ($request->search_password) {
            $timeDoctorAccounts = $timeDoctorAccounts->where('time_doctor_password', 'LIKE', '%' . $request->search_password . '%');
        }

        $timeDoctorAccounts = $timeDoctorAccounts->latest()->paginate(Setting::get('pagination', 25));

        return view('time-doctor.user-account-list', compact('timeDoctorAccounts', 'timeDoctorAccountsEmails', 'reqAccountsEmail'));
    }

    public function listRemarkStore(Request $request): JsonResponse
    {
        $oldRemark                   = null;
        $timeDoctorAccounts          = TimeDoctorAccount::find($request->member_id);
        $oldRemark                   = $timeDoctorAccounts->remarks;
        $timeDoctorAccounts->remarks = $request->remark;
        $timeDoctorAccounts->save();

        $timeRemark                         = new TimeDoctorAccountRemarkHistory();
        $timeRemark->time_doctor_account_id = $request->member_id;
        $timeRemark->user_id                = Auth::user()->id;
        $timeRemark->old_remark             = $oldRemark;
        $timeRemark->new_remark             = $request->remark;
        $timeRemark->save();

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Reamrk Added successfully']);
    }

    public function getRemarkStore(Request $request): JsonResponse
    {
        $remarks = TimeDoctorAccountRemarkHistory::with(['user'])->where('time_doctor_account_id', $request->member_id)->latest()->get();

        return response()->json([
            'status'      => true,
            'data'        => $remarks,
            'message'     => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateValidate(Request $request): JsonResponse
    {
        $timeDoctorAccount           = TimeDoctorAccount::find($request->id);
        $beforeDate                  = $timeDoctorAccount->created_at;
        $timeDoctorAccount->validate = 1;
        $timeDoctorAccount->due_date = $request->dueDate;
        $timeDoctorAccount->save();

        $history                         = new TimeDoctorDueDateHistory();
        $history->time_doctor_account_id = $request->id;
        $history->before_date            = $beforeDate->addDays(15);
        $history->after_date             = $request->dueDate;
        $history->user_id                = Auth::user()->id;
        $history->save();

        return response()->json([
            'status'      => true,
            'data'        => $timeDoctorAccount,
            'message'     => 'Validate Updated successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getduedateHistory(Request $request): JsonResponse
    {
        $history = TimeDoctorDueDateHistory::with(['user'])->where('time_doctor_account_id', $request->member_id)->latest()->get();

        return response()->json([
            'status'      => true,
            'data'        => $history,
            'message'     => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }
}
