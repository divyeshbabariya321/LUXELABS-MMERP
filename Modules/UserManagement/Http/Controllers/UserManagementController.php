<?php

namespace Modules\UserManagement\Http\Controllers;

use App\ApiKey;
use App\AssetsManager;
use App\ChatMessage;
use App\ColdLeads;
use App\Customer;
use App\DeveloperTask;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsAppController;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;
use App\Marketing\WhatsappConfig;
use App\Models\UserDatabaseLog;
use App\Models\UserScheduleRequest;
use App\Models\UserScheduleStatus;
use App\Payment;
use App\PaymentMethod;
use App\Permission;
use App\PermissionRequest;
use App\Reply;
use App\Role;
use App\StoreWebsite;
use App\Task;
use App\TaskStatus;
use App\Team;
use App\TeamUser;
use App\User;
use App\UserAvaibility;
use App\UserAvaibilityHistory;
use App\UserDatabase;
use App\UserDatabaseTable;
use App\UserFeedbackCategory;
use App\UserFeedbackCategorySopHistory;
use App\UserFeedbackCategorySopHistoryComment;
use App\UserFeedbackStatus;
use App\UserFeedbackStatusUpdate;
use App\UserPemfileHistory;
use App\UserPemfileHistoryLog;
use App\UserRate;
use App\UserSysyemIp;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PragmaRX\Tracker\Vendor\Laravel\Models\Session;

use function GuzzleHttp\json_encode;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req): View
    {

        $title = 'User management';
        $permissionRequest = PermissionRequest::count();
        $statusList = TaskStatus::select('name', 'id')->get();

        // $shell_list = shell_exec("bash " . getenv('DEPLOYMENT_SCRIPTS_PATH'). "/webaccess-firewall.sh -f list");
        // $final_array = [];
        // if($shell_list != ''){
        //     $lines=explode(PHP_EOL,$shell_list);
        //     $final_array = [];
        //     foreach($lines as $line){
        //         $values = [];
        //         $values=explode(' ',$line);
        //         array_push($final_array,$values);
        //     }
        // }

        // if(!empty($final_array))
        // {
        //     foreach(array_reverse($final_array) as $values){

        //         $index   = $values[0]??0;
        //         $ip      = $values[1]??0;
        //         $comment = $values[2]??0;

        //         $where = ['ip' => $ip];

        //         $insert = [
        //             'index_txt'       => $index??'-',
        //             'ip'              => $ip??'-',
        //             'notes'           => $comment??'-',
        //             // 'user_id'         => Auth::id(),
        //             // 'other_user_name' => $comment,
        //         ];
        //         $userips = UserSysyemIp::updateOrCreate($where,$insert);
        //     }
        // }

        // $usersystemips = UserSysyemIp::with('user')->get();
        $usersystemips = [];

        // $userlist = User::orderBy('name')->where('is_active',1)->get();
        $userlist = [];

        // $user = new feedback_table;
        // $user->catagory=$req->input('catagory');
        // //     // $user->adminrespose=$req->input('adminrespose');
        // //     // $user->userrespose=$req->input('userrespose');
        // //     // $user->status=$req->input('status');
        // //     // $user->histry=$req->input('histry');
        // $user->save();
        // $whatsapp = DB::select('SELECT number FROM whatsapp_configs WHERE status = 1');

        $whatsapp = WhatsappConfig::select('number')
            ->where('status', 1)
            ->get();

        // _p($whatsapp); exit;
        $servers = AssetsManager::whereHas('category', function ($q) {
            $q->where('cat_name', '=', 'Servers');
        })->get();

        $userLists = User::orderBy('name')->where('is_active', 1)->pluck('name', 'id');

        return view('usermanagement::index', compact('title', 'permissionRequest', 'statusList', 'usersystemips', 'userlist', 'whatsapp', 'servers', 'userLists'));
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = UserScheduleStatus::find($key);
            $bugstatus->color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function getUserList(Request $request): JsonResponse
    {
        $userlist = User::where('is_active', 1)->orderBy('name')->pluck('name', 'id');

        $usersystemips = UserSysyemIp::with('user')->get();

        $shell_list = shell_exec('bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f list');
        $final_array = [];
        if ($shell_list != '') {
            $lines = explode(PHP_EOL, $shell_list);
            $final_array = [];
            foreach ($lines as $line) {
                $values = [];
                $values = explode(' ', $line);
                array_push($final_array, $values);
            }
        }

        if (! empty($final_array)) {
            foreach (array_reverse($final_array) as $values) {
                $index = $values[0] ?? 0;
                $ip = $values[1] ?? 0;
                $comment = $values[2] ?? 0;
                $where = ['ip' => $ip];
                $insert = [
                    'index_txt' => $index ?? '-',
                    'ip' => $ip ?? '-',
                    'notes' => $comment ?? '-',
                    // 'user_id'         => Auth::id(),
                    // 'other_user_name' => $comment,
                ];
                $userips = UserSysyemIp::updateOrCreate($where, $insert);
            }
        }
        $html = view('partials.user_list', ['usersystemips' => $usersystemips])->render();

        return response()->json(['code' => 200, 'data' => $userlist, 'usersystemips' => $usersystemips]);
    }

    public function cat_name(Request $req): View
    {
        $title = 'User management';
        $permissionRequest = PermissionRequest::count();
        $statusList = TaskStatus::select('name', 'id')->get()->toArray();

        $usersystemips = UserSysyemIp::with('user')->get();

        $userlist = User::orderBy('name')->where('is_active', 1)->get();

        // $user = new feedback_table;
        // $user->catagory=$req->input('catagory');
        //     // $user->adminrespose=$req->input('adminrespose');
        //     // $user->userrespose=$req->input('userrespose');
        //     // $user->status=$req->input('status');
        //     // $user->histry=$req->input('histry');
        // $user->save();

        return view('usermanagement::index', compact('title', 'permissionRequest', 'statusList', 'usersystemips', 'userlist'));
    }

    public function permissionRequest(Request $request): JsonResponse
    {
        $history = PermissionRequest::leftjoin('users', 'permission_request.user_id', 'users.id')
            ->orderByDesc('permission_request.id')
            ->when($request->has('user_id'), fn ($q) => $q->where('permission_request.user_id', $request->user_id))
            ->get();

        return response()->json(['code' => 200, 'data' => $history]);
    }

    public function deletePermissionRequest(Request $request): JsonResponse
    {
        $history = PermissionRequest::query()->delete();

        return response()->json(['code' => 200, 'data' => 'Permission Deleted Successfully']);
    }

    public function todayTaskHistory(Request $request): JsonResponse
    {
        $date = "'%".date('Y-m-d')."%'";
        // $history = HubstaffActivity::select('users.name','developer_tasks.subject','developer_tasks.id as devtaskId','hubstaff_activities.starts_at' ,DB::raw("SUM(tracked) as day_tracked"))
        //           ->join('hubstaff_members','hubstaff_activities.user_id','hubstaff_members.hubstaff_user_id')
        //           ->join('users','hubstaff_members.user_id','users.id')
        //           ->join('developer_tasks','hubstaff_activities.task_id','developer_tasks.hubstaff_task_id')
        //           ->whereDate('hubstaff_activities.starts_at',date('Y-m-d'))
        //           ->groupBy('hubstaff_activities.starts_at','hubstaff_activities.user_id');
        //           if( !empty( $request->id ) ){
        //             $history->where('users.id',$request->id);
        //           }
        // $history =  $history->orderBy("hubstaff_activities.id","desc")->get();
        // $history = DB::select('SELECT users.name, developer_tasks.subject, developer_tasks.id as devtaskId,tasks.id as task_id,tasks.task_subject as task_subject,  hubstaff_activities.starts_at, SUM(tracked) as day_tracked
        //           FROM `users`
        //           JOIN hubstaff_members ON hubstaff_members.user_id=users.id
        //           JOIN hubstaff_activities ON hubstaff_members.hubstaff_user_id=hubstaff_activities.user_id
        //           LEFT JOIN developer_tasks ON hubstaff_activities.task_id=developer_tasks.hubstaff_task_id
        //           LEFT JOIN tasks ON hubstaff_activities.task_id=tasks.hubstaff_task_id
        //           WHERE ( (`hubstaff_activities`.`starts_at` LIKE ' . $date . ') AND (developer_tasks.id is NOT NULL or tasks.id is not null) and hubstaff_activities.task_id > 0)
        //             GROUP by hubstaff_activities.task_id
        //             order by day_tracked desc ');
        $history = User::select(
            'users.name',
            'developer_tasks.subject',
            'developer_tasks.id as devtaskId',
            'tasks.id as task_id',
            'tasks.task_subject as task_subject',
            'hubstaff_activities.starts_at',
            DB::raw('SUM(tracked) as day_tracked')
        )
            ->join('hubstaff_members', 'hubstaff_members.user_id', '=', 'users.id')
            ->join('hubstaff_activities', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
            ->leftJoin('developer_tasks', 'hubstaff_activities.task_id', '=', 'developer_tasks.hubstaff_task_id')
            ->leftJoin('tasks', 'hubstaff_activities.task_id', '=', 'tasks.hubstaff_task_id')
            ->where(function ($query) use ($date) {
                $query->where('hubstaff_activities.starts_at', 'LIKE', $date)
                    ->whereNotNull('developer_tasks.id')
                    ->orWhereNotNull('tasks.id');
            })
            ->where('hubstaff_activities.task_id', '>', 0)
            ->groupBy('hubstaff_activities.task_id')
            ->orderByDesc('day_tracked')
            ->get();

        // WHERE (`hubstaff_activities`.`starts_at` LIKE " . $date . "  OR `hubstaff_activities`.`starts_at` is NULL ) group by users.id order by day_tracked desc ");

        //purpose : Add AND developer_tasks.id is NOT NULL in where condition ,  Remove group by users.id , Add left join task Table Old Query is Comment - DEVTASK-4256
        $filterList = [];
        if (! empty($history)) {
            foreach ($history as $key => $value) {
                $filterList[] = [
                    'user_name' => $value->name,
                    'devtaskId' => empty($value->devtaskId) ? $value->task_id : $value->devtaskId,
                    'task' => empty($value->devtaskId) ? $value->task_subject : $value->subject,
                    'date' => $value->starts_at,
                    'tracked' => number_format($value->day_tracked / 60, 2, '.', ','),
                ];
            }
        }

        return response()->json(['code' => empty($filterList) ? 500 : 200, 'data' => [$filterList]]);
    }

    public function taskActivity(Request $request): JsonResponse
    {
        $history = HubstaffActivity::select('users.name', 'developer_tasks.subject', 'hubstaff_activities.starts_at', DB::raw('SUM(tracked) as day_tracked'))
            ->leftjoin('hubstaff_members', 'hubstaff_activities.user_id', 'hubstaff_members.hubstaff_user_id')
            ->leftjoin('users', 'hubstaff_members.user_id', 'users.id')
            ->leftjoin('developer_tasks', 'hubstaff_activities.task_id', 'developer_tasks.hubstaff_task_id')
            ->whereBetween('hubstaff_activities.starts_at', [date('Y-m-d', strtotime('-7 days')), date('Y-m-d', strtotime('+1 days'))])
            ->groupBy('hubstaff_activities.starts_at', 'hubstaff_activities.user_id')
            ->where('users.id', $request->id)
            ->orderByDesc('hubstaff_activities.id')->get();
        $filterList = [];
        foreach ($history as $key => $value) {
            $filterList = [
                'user_name' => $value->name,
                'task' => $value->subject,
                'date' => $value->starts_at,
                'tracked' => number_format($value->day_tracked / 60, 2, '.', ','),
            ];
        }

        return response()->json(['code' => empty($filterList) ? 500 : 200, 'data' => [$filterList]]);
    }

    public function modifiyPermission(Request $request): JsonResponse
    {
        if ($request->type == 'accept') {
            $user = User::findorfail($request->user);
            $user->permissions()->attach($request->permission);
            PermissionRequest::where('user_id', $request->user)->where('permission_id', $request->permission)->delete();

            // user need to send message
            //ChatMessage::sendWithChatApi($act->phone_number, null, $userMessage);
            // $permission     = Permission::find($request->permission);
            // $permissionName = '';
            // if ($permission) {
            //     $permissionName = $permission->name;
            // }

            // $params            = [];
            // $params['user_id'] = $user->id;
            // $params['message'] = 'Your permission request has been approved for the permission :' . $permissionName;
            // send chat message
            // $chat_message = ChatMessage::create($params);
            // send
            // app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);

            return response()->json(['code' => 200, 'data' => 'Permission added Successfully']);
        }
        if ($request->type == 'reject') {
            PermissionRequest::where('user_id', $request->user)->where('permission_id', $request->permission)->delete();

            return response()->json(['code' => 200, 'data' => 'Requets reject successfully']);
        }

        return response()->json(['code' => 500, 'data' => 'Something went wrong!']);
    }

    public function records(Request $request): JsonResponse
    {
        $user = new User;
        $isWhitelist = $request->is_whitelisted == 1 ? 1 : 0;
        if (! Auth::user()->isAdmin()) {
            $user = $user->where('users.id', Auth::user()->id);
        }

        if ($request->is_active == 1) {
            $user = $user->where('users.is_active', 1);
        }
        if ($request->is_active == 2) {
            $user = $user->where('users.is_active', 0);
        }
        if ($request->keyword != null) {
            $user = $user->where(function ($q) use ($request) {
                $q->where('users.email', 'like', '%'.$request->keyword.'%')
                    ->orWhere('users.name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('users.phone', 'like', '%'.$request->keyword.'%');
            });
        }
        if ($request->is_whitelisted) {
            $user = $user->where('users.is_whitelisted', $isWhitelist);
        }

        $user = $user->select(['users.*', 'hubstaff_activities.starts_at'])
            ->leftJoin('hubstaff_members', 'hubstaff_members.user_id', '=', 'users.id')
            ->leftJoin('hubstaff_activities', 'hubstaff_activities.user_id', '=', 'hubstaff_members.hubstaff_user_id')
            ->groupBy('users.id')
            ->orderByDesc('hubstaff_activities.starts_at')
            // ->orderBy('is_active','DESC')
            ->paginate(12);

        $items = [];
        $replies = null;
        if (! $user->isEmpty()) {
            foreach ($user as $u) {
                $currentRate = $u->latestRate;

                $team = Team::with(['users' => function ($query) use ($u) {
                    $query->where('user_id', $u->id);
                }])->first();

                $user_in_team = 0;

                if ($team) {
                    $u['team_leads'] = $team->users->count();
                    $u['team_members'] = $team->users->toArray();
                    $u['team'] = $team;
                    $user_in_team = 1;
                }

                // $taskList = DB::select('
                // select * from (
                //     (SELECT tasks.id as task_id,tasks.task_subject as subject, tasks.task_details as details, tasks.approximate as approximate_time, tasks.due_date,tasks.deleted_at,tasks.assign_to as assign_to,tasks.status as status_falg,chat_messages.message as last_message, chat_messages.created_at as orderBytime, tasks.is_verified as cond, "TASK" as type,tasks.created_at as created_at,tasks.priority_no,tasks.is_flagged as has_flag  FROM tasks
                //     LEFT JOIN
                //     (SELECT MAX(id) AS max_id,
                //     task_id,
                //     message,
                //     created_at
                //     FROM chat_messages
                //     WHERE task_id > 0
                //     GROUP BY task_id) m_max ON m_max.task_id = tasks.id
                //     LEFT JOIN task_statuses ON task_statuses.id = tasks.status
                //              LEFT JOIN chat_messages ON chat_messages.id = m_max.max_id
                //              WHERE tasks.deleted_at IS NULL and tasks.is_statutory != 1 and tasks.is_verified is null and tasks.assign_to = ' . $u->id . ')

                //              union

                //              (
                //                  select developer_tasks.id as task_id, developer_tasks.subject as subject, developer_tasks.task as details, developer_tasks.estimate_minutes as approximate_time, developer_tasks.due_date as due_date,developer_tasks.deleted_at, developer_tasks.assigned_to as assign_to,developer_tasks.status as status_falg, chat_messages.message as last_message, chat_messages.created_at as orderBytime,"d" as cond, "DEVTASK" as type,developer_tasks.created_at as created_at,developer_tasks.priority_no,developer_tasks.is_flagged as has_flag from developer_tasks left join (SELECT MAX(id) as  max_id, issue_id, message,created_at  FROM  chat_messages where issue_id > 0  GROUP BY issue_id ) m_max on  m_max.issue_id = developer_tasks.id left join chat_messages on chat_messages.id = m_max.max_id where developer_tasks.status != "Done" and developer_tasks.deleted_at is null and developer_tasks.assigned_to = ' . $u->id . '

                //                  )
                //                  ) as c order by priority_no desc
                //                  ');

                $taskList = Task::select(
                    'tasks.id as task_id',
                    'tasks.task_subject as subject',
                    'tasks.task_details as details',
                    'tasks.approximate as approximate_time',
                    'tasks.due_date',
                    'tasks.deleted_at',
                    'tasks.assign_to as assign_to',
                    'tasks.status as status_falg',
                    'chat_messages.message as last_message',
                    'chat_messages.created_at as orderBytime',
                    'tasks.is_verified as cond',
                    DB::raw('"TASK" as type'),
                    'tasks.created_at as created_at',
                    'tasks.priority_no',
                    'tasks.is_flagged as has_flag'
                )
                    ->leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.status')
                    ->leftJoin('chat_messages', function ($join) {
                        $join->on('chat_messages.id', '=', DB::raw('(SELECT MAX(id) FROM chat_messages WHERE task_id = tasks.id)'));
                    })
                    ->whereNull('tasks.deleted_at')
                    ->where('tasks.is_statutory', '!=', 1)
                    ->whereNull('tasks.is_verified')
                    ->where('tasks.assign_to', $u->id);

                $developerTaskList = DeveloperTask::select(
                    'developer_tasks.id as task_id',
                    'developer_tasks.subject as subject',
                    'developer_tasks.task as details',
                    'developer_tasks.estimate_minutes as approximate_time',
                    'developer_tasks.due_date as due_date',
                    'developer_tasks.deleted_at',
                    'developer_tasks.assigned_to as assign_to',
                    'developer_tasks.status as status_falg',
                    'chat_messages.message as last_message',
                    'chat_messages.created_at as orderBytime',
                    DB::raw('"d" as cond'),
                    DB::raw('"DEVTASK" as type'),
                    'developer_tasks.created_at as created_at',
                    'developer_tasks.priority_no',
                    'developer_tasks.is_flagged as has_flag'
                )
                    ->leftJoin(DB::raw('(SELECT MAX(id) as max_id, issue_id FROM chat_messages WHERE issue_id > 0 GROUP BY issue_id) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id')
                    ->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id')
                    ->where('developer_tasks.status', '!=', 'Done')
                    ->whereNull('developer_tasks.deleted_at')
                    ->where('developer_tasks.assigned_to', $u->id);

                $unionQuery = $taskList->union($developerTaskList);

                // $taskList = DB::table(DB::raw("({$unionQuery->toSql()}) as c"))
                //     ->mergeBindings($unionQuery)
                //     ->orderByDesc('priority_no')
                //     ->get();

                $taskList = DB::query()
                    ->fromSub($unionQuery, 'c')
                    ->orderByDesc('priority_no')
                    ->get();

                $pending_tasks = 0;
                $total_tasks = count($taskList);
                foreach ($taskList as $t) {
                    if ($t->has_flag != 1) {
                        $pending_tasks++;
                    }
                }

                $no_time_estimate = DeveloperTask::whereNull('estimate_minutes')->where('assigned_to', $u->id)->count();
                $overdue_task = DeveloperTask::where('estimate_date', '>', date('Y-m-d'))->where('status', '!=', 'Done')->where('assigned_to', $u->id)->count();

                // $total_tasks = Task::where('is_statutory', 0)
                // ->Where('assign_to', $u->id)->count();
                $u['pending_tasks'] = $pending_tasks;
                $u['total_tasks'] = $total_tasks;
                $u['no_time_estimate'] = $no_time_estimate;
                $u['overdue_task'] = $overdue_task;

                $isMember = $u->teams()->first();
                if ($isMember) {
                    $user_in_team = 1;
                }

                $u['user_in_team'] = $user_in_team;
                $u['hourly_rate'] = ($currentRate) ? $currentRate->hourly_rate : 0;
                $u['currency'] = ($currentRate) ? $currentRate->currency : 'USD';

                $u['yesterday_hrs'] = $u->yesterdayHrs();
                $u['isAdmin'] = $u->isAdmin();
                $u['is_online'] = $u->isOnline();

                if ($u->approve_login == date('Y-m-d')) {
                    $u['already_approved'] = true;
                } else {
                    $u['already_approved'] = false;
                }

                $online_now = $u->lastOnline();
                if ($online_now) {
                    $u['online_now'] = \Carbon\Carbon::parse($online_now)->format('d-m H:i');
                } else {
                    $u['online_now'] = null;
                }

                $lastPaid = $u->payments()->orderByDesc('id')->first();
                if ($lastPaid) {
                    $lastPaidOn = $lastPaid->paid_upto;
                } else {
                    $query = HubstaffPaymentAccount::where('user_id', $u->id)->first();
                    if (! $query) {
                        $lastPaidOn = date('Y-m-d');
                    } else {
                        $lastPaidOn = date('Y-m-d', strtotime($query->billing_start.'-1 days'));
                    }
                }
                if ($lastPaidOn) {
                    $u['previousDue'] = $u->previousDue($lastPaidOn);
                } else {
                    $u['previousDue'] = '';
                }

                $u['lastPaidOn'] = $lastPaidOn;

                if ($u->payment_frequency == 'fornightly') {
                    $u['nextDue'] = date('Y-m-d', strtotime($lastPaidOn.'+1 days'));
                }
                if ($u->payment_frequency == 'weekly') {
                    $u['nextDue'] = date('Y-m-d', strtotime($lastPaidOn.'+7 days'));
                }
                if ($u->payment_frequency == 'biweekly') {
                    $u['nextDue'] = date('Y-m-d', strtotime($lastPaidOn.'+14 days'));
                }
                if ($u->payment_frequency == 'monthly') {
                    $u['nextDue'] = date('Y-m-d', strtotime($lastPaidOn.'+30 days'));
                }
                $items[] = $u;
            }

            $replies = Reply::where('model', 'User')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();
        }

        $isAdmin = Auth::user()->isAdmin();

        return response()->json([
            'code' => 200,
            'data' => $items,
            'replies' => $replies,
            'isAdmin' => $isAdmin,
            'pagination' => (string) $user->links(),
            'total' => $user->total(),
            'page' => $user->currentPage(),
        ]);
    }

    public function GetUserDetails($id): JsonResponse
    {
        $user = User::where('id', $id)->first();

        return response()->json([
            'code' => 200,
            'data' => $user,
        ]);
    }

    public function getPendingandAvalHour($id): JsonResponse
    {
        $u = [];
        $tasks_time = Task::where('assign_to', $id)->where('is_verified', null)->select(DB::raw('SUM(approximate) as approximate_time'));
        $devTasks_time = DeveloperTask::where('assigned_to', $id)->where('status', '!=', 'Done')->select(DB::raw('SUM(estimate_minutes) as approximate_time'));

        $task_times = ($devTasks_time)->union($tasks_time)->get();
        $pending_tasks = 0;
        foreach ($task_times as $key => $task_time) {
            $pending_tasks += $task_time['approximate_time'];
        }
        $u['total_pending_hours'] = intdiv($pending_tasks, 60).':'.($pending_tasks % 60);
        $today = date('Y-m-d');

        /** get total availablity hours */
        $avaibility = UserAvaibility::where('user_id', $id)->where('date', '>=', $today)->get();
        $avaibility_hour = 0;
        foreach ($avaibility as $aval_time) {
            $from = $this->getTimeFormat($aval_time['from']);
            $to = $this->getTimeFormat($aval_time['to']);
            $avaibility_hour += round((strtotime($to) - strtotime($from)) / 3600, 1);
        }
        $avaibility_hour = $this->getTimeFormat($avaibility_hour);
        $u['total_avaibility_hour'] = $avaibility_hour;

        /** get today availablity hours */
        $today_avaibility = UserAvaibility::where('user_id', $id)->where('date', '=', $today)->get();
        $today_avaibility_hour = 0;
        foreach ($today_avaibility as $aval_time) {
            $from = $this->getTimeFormat($aval_time['from']);
            $to = $this->getTimeFormat($aval_time['to']);
            $today_avaibility_hour += round((strtotime($to) - strtotime($from)) / 3600, 1);
        }
        $today_avaibility_hour = $this->getTimeFormat($today_avaibility_hour);
        $u['today_avaibility_hour'] = $today_avaibility_hour;

        return response()->json([
            'code' => 200,
            'data' => $u,
        ]);
    }

    public function getTimeFormat($time)
    {
        $time = explode('.', $time);
        if (strlen($time[0]) <= 1) {
            $from_time = '0'.$time[0].':00:00';
        } else {
            $from_time = $time[0].':00:00';
        }

        return $from_time;
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::orderBy('name')->pluck('name', 'id')->all();
        $permission = Permission::orderBy('name')->pluck('name', 'id')->all();

        $users = User::all();
        $userRole = $user->roles->pluck('name', 'id')->all();
        $userPermission = $user->permissions->pluck('name', 'id')->all();
        $agent_roles = ['sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others'];
        $user_agent_roles = explode(',', $user->agent_role);
        $api_keys = ApiKey::select('number')->get();
        $customers_all = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->whereRaw("customers.id NOT IN (SELECT customer_id FROM user_customers WHERE user_id != $id)")->get()->toArray();

        $userRate = UserRate::getRateForUser($user->id);
        // return response()->json([
        //     "code"       => 200,
        //     "user"       => $user,
        //     "users"       => $users,
        //     "agent_roles" => $agent_roles,
        //     "api_keys"    => $api_keys,
        //     "customers_all" => $customers_all,
        //     "userRate" => $userRate,
        // ]);

        return view('usermanagement::templates.edit-user', compact('user', 'userRole', 'users', 'roles', 'agent_roles', 'user_agent_roles', 'api_keys', 'customers_all', 'permission', 'userPermission', 'userRate'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'sometimes|nullable|integer|unique:users,phone,'.$id,
            'password' => 'same:confirm-password',
        ]);
        $input = $request->all();
        $hourly_rate = $input['hourly_rate'];
        $currency = $input['currency'];

        unset($input['hourly_rate']);
        unset($input['currency']);
        $input['name'] = str_replace(' ', '_', $input['name']);
        if (isset($input['agent_role'])) {
            $input['agent_role'] = implode(',', $input['agent_role']);
        } else {
            $input['agent_role'] = '';
        }

        if (! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }
        //return $input;
        $user = User::find($id);
        $user->update($input);
        if ($request->customer) {
            if ($request->customer[0] != '') {
                $user->customers()->sync($request->customer);
            }
        }

        $user->listing_approval_rate = $request->get('listing_approval_rate') ?? '0';
        $user->listing_rejection_rate = $request->get('listing_rejection_rate') ?? '0';
        $user->save();

        $userRate = new UserRate;
        $userRate->start_date = Carbon::now();
        $userRate->hourly_rate = $hourly_rate;
        $userRate->currency = $currency;
        $userRate->user_id = $user->id;
        $userRate->save();

        return redirect()->back()
            ->with('success', 'User updated successfully');
    }

    public function activate(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if ($user->is_active == 1) {
            $user->is_active = 0;
        } else {
            $user->is_active = 1;
        }

        $user->save();

        return response()->json([
            'code' => 200,
            'message' => 'User sucessfully updated',
            'page' => $request->get('page'),
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (Auth::id() != $id) {
            return redirect()->route('user-management.index')->withWarning("You don't have access to this page!");
        }

        $users_array = Helpers::getUserArray(User::all());
        $roles = Role::pluck('name', 'name')->all();
        $users = User::all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $agent_roles = ['sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others'];
        $user_agent_roles = explode(',', $user->agent_role);
        $api_keys = ApiKey::select('number')->get();

        $pending_tasks = Task::where('is_statutory', 0)
            ->whereNull('is_completed')
            ->where(function ($query) use ($id) {
                return $query->orWhere('assign_from', $id)
                    ->orWhere('assign_to', $id);
            })->get();

        // dd($pending_tasks);
        // return response()->json(["code" => 200, "user" => $user]);

        return view(
            'usermanagement::templates.show',
            [
                'user' => $user,
                'users_array' => $users_array,
                'roles' => $roles,
                'users' => $users,
                'userRole' => $userRole,
                'agent_roles' => $agent_roles,
                'user_agent_roles' => $user_agent_roles,
                'api_keys' => $api_keys,
                'pending_tasks' => $pending_tasks,
            ]
        );
    }

    public function usertrack($id): View
    {
        $user = User::find($id);
        $actions = $user->actions()->orderByDesc('created_at')->get();

        $tracks = Session::where('user_id', $id)->orderByDesc('created_at')->get();

        $routeActions = [
            'users.index' => 'Viewed Users Page',
            'users.show' => 'Viewed A User',
            'customer.index' => 'Viewed Customer Page',
            'customer.show' => 'Viewed A Customer Page',
            'cold-leads.index' => 'Viewed Cold Leads Page',
            'home' => 'Landed Homepage',
            'purchase.index' => 'Viewed Purchase Page',
        ];

        $models = [
            'users.show' => new User,
            'customer.show' => new Customer,
            'cold-leads.show' => new ColdLeads,
        ];

        return view(
            'usermanagement::templates.track',
            [
                'actions' => $actions,
                'tracks' => $tracks,
                'routeActions' => $routeActions,
                'models' => $models,
            ]
        );
    }

    public function userPayments($id, Request $request)
    {
        $params = $request->all();

        $date = new DateTime;

        if (isset($params['year']) && isset($params['week'])) {
            $year = $params['year'];
            $week = $params['week'];
        } else {
            $week = $date->format('W');
            $year = $date->format('Y');
        }
        $result = getStartAndEndDate($week, $year);
        $start = $result['week_start'];
        $end = $result['week_end'];

        $user = User::join('hubstaff_payment_accounts as hpa', 'hpa.user_id', 'users.id')->where('users.id', $id)->with(['currentRate'])->first();
        $usersRatesThisWeek = UserRate::ratesForWeek($week, $year);

        $usersRatesPreviousWeek = UserRate::latestRatesForWeek($week - 1, $year);

        $activitiesForWeek = HubstaffActivity::getActivitiesForWeek($week, $year);

        $paymentsDone = Payment::getConsidatedUserPayments();

        $amountToBePaid = HubstaffPaymentAccount::getConsidatedUserAmountToBePaid();

        $now = now();
        $paymentMethods = [];
        if ($user) {
            $user->secondsTracked = 0;
            $user->currency = '-';
            $user->total = 0;

            $userPaymentsDone = 0;

            $userPaymentsDoneModel = $paymentsDone->first(function ($value) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($userPaymentsDoneModel) {
                $userPaymentsDone = $userPaymentsDoneModel->paid;
            }

            $userPaymentsToBeDone = 0;
            $userAmountToBePaidModel = $amountToBePaid->first(function ($value) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($userAmountToBePaidModel) {
                $userPaymentsToBeDone = $userAmountToBePaidModel->amount;
            }

            $user->balance = $userPaymentsToBeDone - $userPaymentsDone;

            //echo $user->id. ' '.$userPaymentsToBeDone. ' '. $userPaymentsDone. PHP_EOL;

            $invidualRatesPreviousWeek = $usersRatesPreviousWeek->first(function ($value, $key) use ($user) {
                return $value->user_id == $user->id;
            });

            $weekRates = [];

            if ($invidualRatesPreviousWeek) {
                $weekRates[] = [
                    'start_date' => $start,
                    'rate' => $invidualRatesPreviousWeek->hourly_rate,
                    'currency' => $invidualRatesPreviousWeek->currency,
                ];
            }

            $rates = $usersRatesThisWeek->filter(function ($value, $key) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($rates) {
                foreach ($rates as $rate) {
                    $weekRates[] = [
                        'start_date' => $rate->start_date,
                        'rate' => $rate->hourly_rate,
                        'currency' => $rate->currency,
                    ];
                }
            }

            usort($weekRates, function ($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });

            if (count($weekRates) > 0) {
                $lastEntry = $weekRates[count($weekRates) - 1];

                $weekRates[] = [
                    'start_date' => $end,
                    'rate' => $lastEntry['rate'],
                    'currency' => $lastEntry['currency'],
                ];

                $user->currency = $lastEntry['currency'];
            }

            $activities = $activitiesForWeek->filter(function ($value, $key) use ($user) {
                return $value->system_user_id === $user->id;
            });

            $user->trackedActivitiesForWeek = $activities;

            foreach ($activities as $activity) {
                $user->secondsTracked += $activity->tracked;
                $i = 0;
                while ($i < count($weekRates) - 1) {
                    $start = $weekRates[$i];
                    $end = $weekRates[$i + 1];

                    if ($activity->starts_at >= $start['start_date'] && $activity->start_time < $end['start_date']) {
                        // the activity needs calculation for the start rate and hence do it
                        $earnings = $activity->tracked * ($start['rate'] / 60 / 60);
                        $activity->rate = $start['rate'];
                        $activity->earnings = $earnings;
                        $user->total += $earnings;
                        break;
                    }
                    $i++;
                }
            }

            //exit;

            foreach (PaymentMethod::all() as $paymentMethod) {
                $paymentMethods[$paymentMethod->id] = $paymentMethod->name;
            }
        }

        return view(
            'usermanagement::templates.payments',
            [
                'user' => $user,
                'id' => $id,
                'selectedYear' => $year,
                'selectedWeek' => $week,
                'paymentMethods' => $paymentMethods,
            ]
        );
    }

    public function getRoles($id): JsonResponse
    {
        $user = User::find($id);
        $roles = Role::orderBy('name')->pluck('name', 'id')->all();
        $userRole = $user->roles->pluck('name', 'id')->all();

        return response()->json([
            'code' => 200,
            'user' => $user,
            'userRole' => $userRole,
            'roles' => $roles,
        ]);
    }

    public function submitRoles($id, Request $request): JsonResponse
    {
        $user = User::find($id);
        if (Auth::user()->hasRole('Admin')) {
            $user->roles()->sync($request->input('roles'));

            return response()->json([
                'code' => 200,
                'message' => 'Role updated successfully',
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Unauthorized access',
        ]);
    }

    public function getPermission($id): JsonResponse
    {
        $user = User::find($id);
        //$permission = Permission::orderBy('name', 'asc')->pluck('name', 'id')->all();

        $permission = Permission::leftJoin('permission_user', function ($join) use ($user) {
            $join->on('permissions.id', '=', 'permission_user.permission_id');
            $join->where('permission_user.user_id', '=', $user->id);
        })
            ->select('permissions.name', 'permissions.id', 'permission_user.user_id')
            ->orderByDesc('permission_user.user_id')
            ->get()->toArray();

        //->pluck('name','id');

        //->pluck('permissions.name', 'permissions.id');

        $userPermission = $user->permissions->pluck('name', 'id')->all();

        return response()->json([
            'code' => 200,
            'user' => $user,
            'userPermission' => $userPermission,
            'permissions' => $permission,
        ]);
    }

    public function submitPermission($id, Request $request): JsonResponse
    {
        $user = User::find($id);
        if (Auth::user()->hasRole('Admin')) {
            $user->permissions()->sync($request->input('permissions'));

            return response()->json([
                'code' => 200,
                'message' => 'Permission updated successfully',
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Unauthorized access',
        ]);
    }

    public function addNewPermission(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'route' => 'required|unique:roles,name',

        ]);
        $permission = new Permission;
        $permission->name = $request->name;
        $permission->route = $request->route;
        $permission->save();

        return response()->json([
            'code' => 200,
            'permission' => $permission,
        ]);
    }

    public function paymentInfo($id)
    {
        $user = User::find($id);
        $lastPaid = $user->payments()->orderByDesc('id')->first();
        if ($lastPaid) {
            $lastPaidOn = $lastPaid->paid_upto;
        } else {
            $query = HubstaffPaymentAccount::where('user_id', $id)->first();
            if (! $query) {
                return response()->json([
                    'code' => 500,
                    'message' => 'No data found',
                ]);
            }
            $lastPaidOn = date('Y-m-d', strtotime($query->billing_start.'-1 days'));
        }
        $pendingPyments = HubstaffPaymentAccount::where('user_id', $id)->where('billing_start', '>', $lastPaidOn)->get();
        if (! count($pendingPyments)) {
            return response()->json([
                'code' => 500,
                'message' => 'No data found',
            ]);
        }
        $pendingTerms = [];
        if ($user->payment_frequency == 'fornightly') {
            $totalPendingTerms = count($pendingPyments);
            $perPacket = 1;
        }
        if ($user->payment_frequency == 'weekly') {
            $totalPendingTerms = floor(count($pendingPyments) / 7);
            $perPacket = 7;
        }
        if ($user->payment_frequency == 'biweekly') {
            $totalPendingTerms = floor(count($pendingPyments) / 14);
            $perPacket = 14;
        }
        if ($user->payment_frequency == 'monthly') {
            $totalPendingTerms = floor(count($pendingPyments) / 30);
            $perPacket = 30;
        }
        $count = 0;
        $packetCount = 0;
        $totalAmount = 0;
        $totalAmountTobePaid = 0;
        foreach ($pendingPyments as $pending) {
            if ($count < $totalPendingTerms) {
                $totalAmount = $totalAmount + ($pending->hrs * $pending->rate);
                $totalAmountTobePaid = $totalAmountTobePaid + ($pending->hrs * $pending->rate * $pending->ex_rate);
                $packetCount = $packetCount + 1;
                if ($packetCount == $perPacket) {
                    $packetCount = 0;
                    $count = $count + 1;
                    $array = [
                        'totalAmount' => $totalAmount,
                        'lastPaidOn' => $pending->billing_start,
                        'currency' => $pending->currency,
                        'totalAmountTobePaid' => $totalAmountTobePaid,
                        'payment_currency' => $pending->payment_currency,
                    ];

                    $pendingTerms[] = $array;
                    $totalAmount = 0;
                    $totalAmountTobePaid = 0;
                }
            } else {
                break;
            }
        }
        $paymentMethods = PaymentMethod::all();

        return view('usermanagement::templates.add-payment', compact('user', 'pendingTerms', 'paymentMethods'));
    }

    public function addPaymentMethod(Request $request): View
    {
        $paymentMethods = new PaymentMethod;
        $paymentMethods->name = $request->name;
        $paymentMethods->save();
        $paymentMethods = PaymentMethod::all();

        return view('usermanagement::templates.new-payment-methods', compact('paymentMethods'));
    }

    public function savePayments($id, Request $request): RedirectResponse
    {
        $this->validate($request, [
            'currency' => 'required',
            'amounts' => 'required',
            'payment_method_id' => 'required',
        ]);

        $user = User::find($id);
        $lastPaid = $user->payments()->orderByDesc('id')->first();
        if ($lastPaid) {
            $lastPaidOn = $lastPaid->paid_upto;
        } else {
            $query = HubstaffPaymentAccount::where('user_id', $id)->first();
            $lastPaidOn = date('Y-m-d', strtotime($query->billing_start.'-1 days'));
        }
        $pendingPyments = HubstaffPaymentAccount::where('user_id', $id)->where('billing_start', '>', $lastPaidOn)->get();
        $pendingTerms = [];
        if ($user->payment_frequency == 'fornightly') {
            $perPacket = 1;
        }
        if ($user->payment_frequency == 'weekly') {
            $perPacket = 7;
        }
        if ($user->payment_frequency == 'biweekly') {
            $perPacket = 14;
        }
        if ($user->payment_frequency == 'monthly') {
            $perPacket = 30;
        }
        $count = 1;
        $totalPendingRaws = $perPacket * count($request->amounts);
        foreach ($pendingPyments as $pending) {
            if ($count == $totalPendingRaws) {
                $resetLastPaidOn = $pending->billing_start;
                break;
            }
            $count++;
        }
        $totalAmount = 0;
        foreach ($request->amounts as $amount) {
            $totalAmount = $totalAmount + $amount;
        }
        $payment = new Payment;
        $payment->payment_method_id = $request->payment_method_id;
        $payment->user_id = $id;
        $payment->note = $request->note;
        $payment->amount = $totalAmount;
        $payment->currency = $request->currency;
        $payment->paid_upto = $resetLastPaidOn;
        $payment->save();

        return redirect()->back()->with('success', 'Payment done successfully');
    }

    public function addReply(Request $request): JsonResponse
    {
        $reply = $request->get('reply');
        $autoReply = [];
        // add reply from here
        if (! empty($reply)) {
            $autoReply = Reply::updateOrCreate(
                ['reply' => $reply, 'model' => 'User', 'category_id' => 1],
                ['reply' => $reply]
            );
        }

        return response()->json(['code' => 200, 'data' => $autoReply]);
    }

    public function deleteReply(Request $request): JsonResponse
    {
        $id = $request->get('id');

        if ($id > 0) {
            $autoReply = Reply::where('id', $id)->first();
            if ($autoReply) {
                $autoReply->delete();
            }
        }

        return response()->json([
            'code' => 200, 'data' => Reply::where('model', 'User')
                ->whereNull('deleted_at')
                ->pluck('reply', 'id')
                ->toArray(),
        ]);
    }

    public function userTasks($id): JsonResponse
    {
        $user = User::find($id)->toArray();
        $taskList = DB::select('
                select * from (
                    (SELECT tasks.id as task_id,tasks.task_subject as subject, tasks.task_details as details, tasks.approximate as approximate_time, tasks.due_date,tasks.deleted_at,tasks.assign_to as assign_to,tasks.status as status_falg,chat_messages.message as last_message, chat_messages.created_at as orderBytime, tasks.is_verified as cond, "TASK" as type,tasks.created_at as created_at,tasks.priority_no,tasks.is_flagged as has_flag  FROM tasks
                              LEFT JOIN
                               (SELECT MAX(id) AS max_id,
                                       task_id,
                                       message,
                                       created_at
                                FROM chat_messages
                                WHERE task_id > 0
                                GROUP BY task_id) m_max ON m_max.task_id = tasks.id
                            LEFT JOIN task_statuses ON task_statuses.id = tasks.status
                             LEFT JOIN chat_messages ON chat_messages.id = m_max.max_id
                              WHERE tasks.deleted_at IS NULL and tasks.is_statutory != 1 and tasks.is_verified is null and tasks.assign_to = '.$id.') 
                    
                    union  
                    
                    (
                        select developer_tasks.id as task_id, developer_tasks.subject as subject, developer_tasks.task as details, developer_tasks.estimate_minutes as approximate_time, developer_tasks.due_date as due_date,developer_tasks.deleted_at, developer_tasks.assigned_to as assign_to,developer_tasks.status as status_falg, chat_messages.message as last_message, chat_messages.created_at as orderBytime,"d" as cond, "DEVTASK" as type,developer_tasks.created_at as created_at,developer_tasks.priority_no,developer_tasks.is_flagged as has_flag from developer_tasks left join (SELECT MAX(id) as  max_id, issue_id, message,created_at  FROM  chat_messages where issue_id > 0  GROUP BY issue_id ) m_max on  m_max.issue_id = developer_tasks.id left join chat_messages on chat_messages.id = m_max.max_id where developer_tasks.status != "Done" and developer_tasks.deleted_at is null and developer_tasks.assigned_to = '.$id.'
                        
                        ) 
                    ) as c order by has_flag desc
                ');

        // Subquery for tasks
        $tasksSubQuery = Task::leftJoin(DB::raw('(SELECT MAX(id) AS max_id, task_id, message, created_at 
                         FROM chat_messages 
                         WHERE task_id > 0 
                         GROUP BY task_id) m_max'), 'm_max.task_id', '=', 'tasks.id')
            ->leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.status')
            ->leftJoin('chat_messages', 'chat_messages.id', '=', DB::raw('m_max.max_id'))
            ->select(
                'tasks.id as task_id',
                'tasks.task_subject as subject',
                'tasks.task_details as details',
                'tasks.approximate as approximate_time',
                'tasks.due_date',
                'tasks.deleted_at',
                'tasks.assign_to as assign_to',
                'tasks.status as status_flag',
                'chat_messages.message as last_message',
                'chat_messages.created_at as orderByTime',
                'tasks.is_verified as cond',
                DB::raw('"TASK" as type'),
                'tasks.created_at as created_at',
                'tasks.priority_no',
                'tasks.is_flagged as has_flag'
            )
            ->whereNull('tasks.deleted_at')
            ->where('tasks.is_statutory', '!=', 1)
            ->whereNull('tasks.is_verified')
            ->where('tasks.assign_to', '=', $id);

        // Subquery for developer tasks
        $developerTasksSubQuery = DeveloperTask::leftJoin(DB::raw('(SELECT MAX(id) AS max_id, issue_id, message, created_at 
                         FROM chat_messages 
                         WHERE issue_id > 0 
                         GROUP BY issue_id) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id')
            ->leftJoin('chat_messages', 'chat_messages.id', '=', DB::raw('m_max.max_id'))
            ->select(
                'developer_tasks.id as task_id',
                'developer_tasks.subject as subject',
                'developer_tasks.task as details',
                'developer_tasks.estimate_minutes as approximate_time',
                'developer_tasks.due_date as due_date',
                'developer_tasks.deleted_at',
                'developer_tasks.assigned_to as assign_to',
                'developer_tasks.status as status_flag',
                'chat_messages.message as last_message',
                'chat_messages.created_at as orderByTime',
                DB::raw('"d" as cond'),
                DB::raw('"DEVTASK" as type'),
                'developer_tasks.created_at as created_at',
                'developer_tasks.priority_no',
                'developer_tasks.is_flagged as has_flag'
            )
            ->where('developer_tasks.status', '!=', 'Done')
            ->whereNull('developer_tasks.deleted_at')
            ->where('developer_tasks.assigned_to', '=', $id);

        // Union the two subqueries
        $taskList = $tasksSubQuery->union($developerTasksSubQuery)
            ->orderByDesc('has_flag')
            ->get();

        //  $tasks = Task::where('assign_to',$id)->where('is_verified',NULL)->select('id as task_id','task_subject as subject','task_details as details','approximate as approximate_time','due_date');
        //  $tasks = $tasks->addSelect(DB::raw("'TASK' as type"));
        //  $devTasks = DeveloperTask::where('assigned_to',$id)->where('status','!=','Done')->select('id as task_id','subject','task as details','estimate_minutes as approximate_time','due_date as due_date');
        //  $devTasks = $devTasks->addSelect(DB::raw("'DEVTASK' as type"));

        //  $taskList = $devTasks->union($tasks)->get();

        $u = [];
        $tasks_time = Task::where('assign_to', $id)->where('is_verified', null)->select(DB::raw('SUM(approximate) as approximate_time'));
        $devTasks_time = DeveloperTask::where('assigned_to', $id)->where('status', '!=', 'Done')->select(DB::raw('SUM(estimate_minutes) as approximate_time'));

        $task_times = ($devTasks_time)->union($tasks_time)->get();
        $pending_tasks = 0;
        foreach ($task_times as $key => $task_time) {
            $pending_tasks += $task_time['approximate_time'];
        }
        $u['total_pending_hours'] = intdiv($pending_tasks, 60).':'.($pending_tasks % 60);

        //$priority_tasks_time = Task::where('assign_to',$id)->where('is_verified',NULL)->where('is_flagged',1)->select(DB::raw("SUM(approximate) as approximate_time"))->first();
        $p_tasks_time = Task::where('assign_to', $id)->where('is_verified', null)->where('is_flagged', 1)->select(DB::raw('SUM(approximate) as approximate_time'));
        $p_devtasks_time = DeveloperTask::where('assigned_to', $id)->where('status', '!=', 'Done')->where('priority', '!=', 0)->select(DB::raw('SUM(estimate_minutes) as approximate_time'));
        $priority_tasks_time = ($p_devtasks_time)->union($p_tasks_time)->get();
        // SELECT
        /** get availablity hours */
        $user_avaibility = UserAvaibility::where('user_id', $id)->selectRaw('minute')->orderByDesc('id')->first();
        $available_minute = ! empty($user_avaibility) ? $user_avaibility->minute : 0;

        $totalPriority = ! empty($priority_tasks_time) ? $priority_tasks_time[0]->approximate_time : 0;
        $hours = 0;

        if ($available_minute != 0) {
            $available_minute = $available_minute - $totalPriority;
            $hours = floor($available_minute / 60); // Get the number of whole hours
            $available_minute = $available_minute % 60;
        }

        $u['total_priority_hours'] = intdiv($totalPriority, 60).':'.($totalPriority % 60);
        $u['total_available_time'] = sprintf('%d:%02d', $hours, $available_minute);
        $today = date('Y-m-d');

        /** get total availablity hours */
        $avaibility = UserAvaibility::where('user_id', $id)->where('date', '>=', $today)->get();
        $avaibility_hour = 0;
        foreach ($avaibility as $aval_time) {
            $from = $this->getTimeFormat($aval_time['from']);
            $to = $this->getTimeFormat($aval_time['to']);
            $avaibility_hour += round((strtotime($to) - strtotime($from)) / 3600, 1);
        }
        $avaibility_hour = $this->getTimeFormat($avaibility_hour);
        $u['total_avaibility_hour'] = $avaibility_hour;

        /** get today availablity hours */
        $today_avaibility = UserAvaibility::where('user_id', $id)->where('date', '=', $today)->get();
        $today_avaibility_hour = 0;
        foreach ($today_avaibility as $aval_time) {
            $from = $this->getTimeFormat($aval_time['from']);
            $to = $this->getTimeFormat($aval_time['to']);
            $today_avaibility_hour += round((strtotime($to) - strtotime($from)) / 3600, 1);
        }
        $today_avaibility_hour = $this->getTimeFormat($today_avaibility_hour);
        $u['today_avaibility_hour'] = $today_avaibility_hour;

        $statusList = TaskStatus::select('name', 'id')->get()->toArray();

        return response()->json([
            'code' => 200,
            'user' => $user,
            'statusList' => $statusList,
            'taskList' => $taskList,
            'userTiming' => $u,
        ]);
    }

    public function createTeam($id): JsonResponse
    {
        $user = User::find($id);
        $users = User::where('id', '!=', $id)->where('is_active', 1)->get()->pluck('name', 'id');

        return response()->json([
            'code' => 200,
            'user' => $user,
            'users' => $users,
        ]);
    }

    public function submitTeam($id, Request $request): JsonResponse
    {
        $user = User::find($id);
        $isLeader = Team::where('user_id', $id)->orWhere('second_lead_id', $request->second_lead)->first();
        if ($isLeader) {
            return response()->json([
                'code' => 500,
                'message' => 'This user is already a team leader',
            ]);
        }

        $isMember = $user->teams()->first();
        if ($isMember) {
            return response()->json([
                'code' => 500,
                'message' => 'This user is already a team member',
            ]);
        }
        $team = new Team;
        $team->name = $request->name;
        $team->user_id = $id;
        $team->second_lead_id = $request->second_lead;
        $team->save();

        $team_user = TeamUser::where('team_id', $team->id)->where('user_id', $team->second_lead)->first();
        if (! empty($team_user)) {
            $team_user->delete();
        }

        if (Auth::user()->hasRole('Admin')) {
            $members = $request->input('members');
            if ($members) {
                foreach ($members as $key => $mem) {
                    $u = User::find($mem);
                    if ($u) {
                        $isMember = $u->teams()->first();
                        $isLeader = Team::where('user_id', $mem)->first();
                        if (! $isMember && ! $isLeader) {
                            $team->users()->attach($mem);
                            $response[$key]['msg'] = $u->name.' added in team successfully';
                            $response[$key]['status'] = 'success';
                        } elseif ($isMember) {
                            $response[$key]['msg'] = $u->name.' is already team member';
                            $response[$key]['status'] = 'error';
                        } else {
                            $response[$key]['msg'] = $u->name.' is already team leader';
                            $response[$key]['status'] = 'error';
                        }
                    }
                }
            }

            return response()->json([
                'code' => 200,
                'data' => $response,
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Unauthorized access',
        ]);
    }

    public function getTeam($id): JsonResponse
    {
        $team = Team::where('user_id', $id)->first();
        $team->user;
        $team->members = $team->users()->where('users.id', '!=', $team->second_lead_id)->pluck('name', 'users.id');
        $totalMembers = $team->users()->count();

        $users = User::where('id', '!=', $id)->where('id', '!=', $team->second_lead_id)->where('is_active', 1)->get()->pluck('name', 'id');

        if (! empty($team->second_lead_id)) {
            $second_users = User::where('id', $team->second_lead_id)->first();
        }

        return response()->json([
            'code' => 200,
            'team' => $team,
            'users' => $users,
            'second_users' => ! empty($second_users->name) ? $second_users->name : '',
            'totalMembers' => $totalMembers,
        ]);
    }

    public function deleteTeam($id, Request $request): JsonResponse
    {
        $team = Team::find($id);
        if ($team) {
            $team->users()->detach();
            $team->delete();

            return response()->json([
                'code' => 200,
                'data' => 'Team deleted successfully',
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Unauthorized access',
            ]);
        }
    }

    public function editTeam($id, Request $request): JsonResponse
    {
        $team = Team::find($id);

        $team_user = TeamUser::where('team_id', $team->id)->where('user_id', $request->second_lead)->first();

        if (! empty($team_user)) {
            $team_user->delete();
        }

        if (Auth::user()->hasRole('Admin')) {
            $team->update(['name' => $request->name, 'second_lead_id' => $request->second_lead]);
            $members = $request->input('members');
            if ($members) {
                $team->users()->detach();
                foreach ($members as $key => $mem) {
                    $u = User::find($mem);
                    if ($u) {
                        $isMember = $u->teams()->first();
                        $isLeader = Team::where('user_id', $mem)->first();
                        if (! $isMember && ! $isLeader) {
                            $team->users()->attach($mem);
                            $response[$key]['msg'] = $u->name.' added in team successfully';
                            $response[$key]['status'] = 'success';
                        } elseif ($isMember) {
                            $response[$key]['msg'] = $u->name.' is already team member';
                            $response[$key]['status'] = 'error';
                        } else {
                            $response[$key]['msg'] = $u->name.' is already team leader';
                            $response[$key]['status'] = 'error';
                        }
                    }
                }
            }

            return response()->json([
                'code' => 200,
                'data' => $response,
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Unauthorized access',
        ]);
    }

    public function saveUserAvaibility(Request $request): JsonResponse
    {
        Log::info('Request:'.json_encode($request->all()));
        $rules = [
            'user_id' => 'required',
            'to' => 'required',
            'from' => 'required|lte:to',
            'day' => 'required',
            'status' => 'required',
            'availableDay' => 'required',
            'availableHour' => 'required',
            'startTime' => 'required',
            'endTime' => 'required',
            'lunchTime' => 'required',
            'note' => 'required_if:status,"0"',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $message = '';
            foreach ($errors->getMessages() as $field => $message) {
                $message = $message[0];
            }

            return response()->json([
                'code' => 500,
                'error' => $message,
            ]);
        }
        $nextDay = implode(', ', $request->day);
        // $nextDay = 'next '.$request->day;

        UserAvaibility::updateOrCreate([
            'user_id' => $request->user_id,
        ], [
            'date' => $nextDay,
            'user_id' => $request->user_id,
            'from' => $request->from,
            'to' => $request->to,
            'day' => $request->availableDay,
            'minute' => $request->availableHour,
            'status' => $request->status,
            'note' => trim($request->note),
            'start_time' => $request->startTime,
            'end_time' => $request->endTime,
            'lunch_time' => $request->lunchTime,
        ]);

        // $user_avaibility = new UserAvaibility;
        // $user_avaibility->date = $day;
        // $user_avaibility->from = $request->from;
        // $user_avaibility->user_id = $request->user_id;
        // $user_avaibility->to = $request->to;
        // $user_avaibility->day = $request->availableDay;
        // $user_avaibility->minute = $request->availableHour;
        // $user_avaibility->status = $request->status;
        // $user_avaibility->note = $note;
        // $user_avaibility->save();

        return response()->json([
            'code' => 200,
            'message' => 'Successful',
        ]);
    }

    /*
        Pawan added for userAvailibility view
    */
    public function userAvaibilityForView($id): JsonResponse
    {
        $avaibility = UserAvaibility::where('user_id', $id)->first();
        if ($avaibility) {
            $avaibility['weekday'] = explode(',', $avaibility['date']);
            $avaibility['date_from'] = date('Y-m-d', strtotime($avaibility['from']));
            $avaibility['date_to'] = date('Y-m-d', strtotime($avaibility['to']));
        }
        // $userhubstafftotal = HubstaffActivity::where('user_id',352204)->sum('tracked');

        $userhubstafftotal = HubstaffActivity::where('user_id', $id)->whereBetween('starts_at', [$avaibility['date_from'], $avaibility['date_to']])->sum('tracked');
        $avaibility['userhubstafftotal'] = $userhubstafftotal;

        // Log::info('HubStaff'.json_encode($userhubstafftotal));
        $avaibility['user_id'] = $id;
        $avaibility['start_time'] = date('H:i', strtotime($avaibility['start_time']));
        $avaibility['end_time'] = date('H:i', strtotime($avaibility['end_time']));
        $avaibility['lunch_time'] = date('H:i', strtotime($avaibility['lunch_time']));
        $avaibility['minute'] = date('H:i', strtotime($avaibility['minute']));

        //Log::info('avaibility:'.json_encode($avaibility));
        return response()->json(['code' => 200, 'data' => $avaibility]);
    }
    //end

    public function userAvaibilityForModal($id): JsonResponse
    {
        $avaibility = UserAvaibility::where('user_id', $id)->first();

        if ($avaibility) {
            $weekday = explode(',', $avaibility['date']);
            for ($i = 0; $i < count($weekday); $i++) {
                Log::info($i);
                if ($weekday[$i] == 'monday' || $weekday[$i] == ' monday') {
                    $avaibility['weekday_0'] = $weekday[$i];
                }
                if ($weekday[$i] == 'tuesday' || $weekday[$i] == ' tuesday') {
                    $avaibility['weekday_1'] = $weekday[$i];
                }
                if ($weekday[$i] == 'wednesday' || $weekday[$i] == ' wednesday') {
                    $avaibility['weekday_2'] = $weekday[$i];
                }
                if ($weekday[$i] == 'thursday' || $weekday[$i] == ' thursday') {
                    $avaibility['weekday_3'] = $weekday[$i];
                }
                if ($weekday[$i] == 'friday' || $weekday[$i] == ' friday') {
                    $avaibility['weekday_4'] = $weekday[$i];
                }
                if ($weekday[$i] == 'saturday' || $weekday[$i] == ' saturday') {
                    $avaibility['weekday_5'] = $weekday[$i];
                }
                // $avaibility['weekday_'.$i] = $weekday[$i];
            }
            // $avaibility['weekday'] = strtolower(date('l', strtotime($avaibility['date'])));
            $avaibility['weekday'] = explode(',', $avaibility['date']);
            Log::info('array:'.json_encode($avaibility['weekday']));
            Log::info('array:'.json_encode($avaibility));
        }

        $avaibility['user_id'] = $id;

        return response()->json(['code' => 200, 'data' => $avaibility]);
    }

    public function userAvaibility($id): JsonResponse
    {
        $user = User::find($id);
        $today = date('Y-m-d');
        $avaibility = UserAvaibility::where('user_id', $id)->where('date', '>=', $today)->get();
        foreach ($avaibility as $av) {
            $av->day = date('D', strtotime($av['date']));
        }
        $avaibility = $avaibility->toArray();

        return response()->json([
            'code' => 200,
            'user' => $user,
            'avaibility' => $avaibility,
        ]);
    }

    public function userAvaibilityUpdate($id, Request $request): JsonResponse
    {
        UserAvaibility::find($id)->update(['status' => $request->status, 'note' => $request->note]);

        return response()->json([
            'code' => 200,
            'user' => 'Success',
        ]);
    }

    public function approveUser($id): JsonResponse
    {
        $user = User::find($id);
        if ($user) {
            $user->update(['approve_login' => date('Y-m-d')]);

            $params = [];
            $params['user_id'] = $user->id;
            $params['message'] = "Your activity has been approved for today's date ".date('Y-m-d');
            // send chat message
            $chat_message = ChatMessage::create($params);
            // send
            app(WhatsAppController::class)
                ->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);

            return response()->json(['message' => 'Successfully approved', 'code' => 200]);
        }

        return response()->json(['message' => 'User not found', 'code' => 404]);
    }

    public function getDatabase(Request $request, $id): JsonResponse
    {
        $database = UserDatabase::where('user_id', $id)->where('database', 'mysql')->first();
        $tablesExisting = [];
        if ($database) {
            $tablesExisting = UserDatabaseTable::where('user_database_id', $database->id)->pluck('name', 'id')->toArray();
        }

        $user = User::find($id);

        $list = [];
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            foreach ($table as $t) {
                $list[] = ['table' => $t, 'checked' => in_array($t, $tablesExisting) ? true : false];
            }
        }
        $data = [
            'user_id' => $id,
            'database' => $database,
            'tables' => $list,
            'user_name' => ($database) ? $database->username : preg_replace('/\s+/', '_', strtolower($user->name)),
            'password' => ($database) ? $database->password : '',
            'tablesExisting' => $tablesExisting,
            'connection' => 'mysql',
        ];

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function createDatabaseUser(Request $request, $id): JsonResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $connection = $request->get('connection');

        if (empty($connection)) {
            return response()->json(['code' => 500, 'message' => 'Please select the database connection']);
        }

        if (empty($username)) {
            return response()->json(['code' => 500, 'message' => 'Enter username']);
        }

        if (empty($password) || strlen($password) <= 6) {
            return response()->json(['code' => 500, 'message' => 'Please enter password and more then 6 length']);
        }

        $connectionInformation = config("database.connections.$connection");
        if ($connection != 'mysql') {
            $storeWebsite = StoreWebsite::where('id', $connection)->first();
            if ($storeWebsite) {
                if ($storeWebsite->server_ip == '') {
                    return response()->json(['code' => '500',  'message' => 'Server ip  is not set!']);
                }
                if ($storeWebsite->database_name == '') {
                    return response()->json(['code' => '500',  'message' => 'Database name is not set!']);
                }
                if ($storeWebsite->mysql_username == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username is not set!']);
                }
                if ($storeWebsite->mysql_password == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username password is not set!']);
                }
                $connectionInformation['host'] = $storeWebsite->server_ip;
                $connectionInformation['database'] = $storeWebsite->database_name;
                $connectionInformation['username'] = $storeWebsite->mysql_username;
                $connectionInformation['password'] = $storeWebsite->mysql_password;
            } else {
                return response()->json(['code' => 500, 'message' => 'Site details is not found']);
            }
        }
        if (empty($connectionInformation)) {
            return response()->json(['code' => 500, 'message' => 'No , database connection is not available']);
        }

        $user = User::find($id);
        if ($user) {
            $database = UserDatabase::where('user_id', $user->id)->where('database', $connection)->first();
            if (! $database) {
                $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql_user.sh -f create -h '.$connectionInformation['host'].' -d '.$connectionInformation['database'].' -u '.$connectionInformation['username']." -p '".$connectionInformation['password']."' -n '".$username."' -s '".$password."' 2>&1";
                $allOutput = [];
                $allOutput[] = $cmd;
                $result = exec($cmd, $allOutput);
                Log::info(print_r($allOutput, true));
                UserDatabase::create([
                    'username' => $username,
                    'password' => $password,
                    'database' => $connection,
                    'user_id' => $id,
                ]);

                UserDatabaseLog::create([
                    'user_id' => $id,
                    'request_data' => $cmd,
                    'response_data' => json_encode($allOutput),
                    'updated_by' => Auth::id(),
                ]);

                $params = [];
                $params['user_id'] = $user->id;
                $params['message'] = 'We have created user with username : '.$username.' and password : '.$password.' , you can sing in here https://erp.theluxuryunlimited.com/7WZr3fgqVfRS5ZskKfv3km2ByrVRGqyDW9F/phpMyAdmin/.';
                // send chat message
                $chat_message = ChatMessage::create($params);
                // send
                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);

                return response()->json(['code' => 200, 'message' => 'User created successfully']);
            }

            return response()->json(['code' => 500, 'message' => 'User already created']);
        }

        return response()->json(['code' => 500, 'message' => 'User not found']);
    }

    public function assignDatabaseTable(Request $request, $id): JsonResponse
    {
        $connection = $request->get('connection');

        if (empty($connection)) {
            return response()->json(['code' => 500, 'message' => 'Please select the database connection']);
        }

        $connectionInformation = config("database.connections.$connection");
        if ($connection != 'mysql') {
            $storeWebsite = StoreWebsite::where('id', $connection)->first();
            if ($storeWebsite) {
                if ($storeWebsite->server_ip == '') {
                    return response()->json(['code' => '500',  'message' => 'Server ip  is not set!']);
                }
                if ($storeWebsite->database_name == '') {
                    return response()->json(['code' => '500',  'message' => 'Database name is not set!']);
                }
                if ($storeWebsite->mysql_username == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username is not set!']);
                }
                if ($storeWebsite->mysql_password == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username password is not set!']);
                }
                $connectionInformation['host'] = $storeWebsite->server_ip;
                $connectionInformation['database'] = $storeWebsite->database_name;
                $connectionInformation['username'] = $storeWebsite->mysql_username;
                $connectionInformation['password'] = $storeWebsite->mysql_password;
            } else {
                return response()->json(['code' => 500, 'message' => 'Site details is not found']);
            }
        }
        if (empty($connectionInformation)) {
            return response()->json(['code' => 500, 'message' => 'No , database connection is not available']);
        }

        $database = UserDatabase::where('user_id', $id)->where('database', $connection)->first();
        $user = User::find($id);
        $tables = ! empty($request->tables) ? $request->tables : [];
        $permissionType = $request->get('assign_permission', 'read');

        if ($database) {
            $tablesExisting = UserDatabaseTable::where('user_database_id', $database->id)->pluck('name', 'id')->toArray();
            if (! empty($tablesExisting)) {
                $deleteTables = [];
                foreach ($tablesExisting as $te) {
                    if (! in_array($te, $tables)) {
                        $deleteTables[] = $te;
                    }
                }
                if (! empty($deleteTables)) {
                    $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql_user.sh -f revoke -h '.$connectionInformation['host'].'  -u '.$connectionInformation['username']." -p '".$connectionInformation['password']."' -d ".$connectionInformation['database']." -n '".$database->username."' -t ".implode(',', $deleteTables).' 2>&1';
                    $allOutput = [];
                    $allOutput[] = $cmd;
                    $result = exec($cmd, $allOutput);
                    Log::info(print_r($allOutput, true));
                }
            }

            UserDatabaseTable::where('user_database_id', $database->id)->delete();

            if (! empty($tables)) {
                foreach ($tables as $t) {
                    UserDatabaseTable::create([
                        'user_database_id' => $database->id,
                        'name' => $t,
                    ]);
                }
            }

            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql_user.sh -f update  -h '.$connectionInformation['host'].'  -u '.$connectionInformation['username']." -p '".$connectionInformation['password']."' -d ".$connectionInformation['database']." -n '".$database->username."' -t ".implode(',', $tables)." -m '".$permissionType."' 2>&1";
            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            Log::info(print_r($allOutput, true));

            UserDatabaseLog::create([
                'user_id' => $user->id,
                'request_data' => $cmd,
                'response_data' => json_encode($allOutput),
                'updated_by' => Auth::id(),
            ]);

            $params = [];
            $params['user_id'] = $user->id;
            $params['message'] = 'Your request for given table ('.implode(',', $tables).')  has been approved , please verify at your end.';
            // send chat message
            $chat_message = ChatMessage::create($params);
            // send
            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);

            return response()->json(['code' => 200, 'message' => 'Table assigned successfully']);
        }

        return response()->json(['code' => 500, 'message' => 'Please create database user first']);
    }

    public function deleteDatabaseAccess(Request $request, $id): JsonResponse
    {
        $connection = $request->get('connection');

        if (empty($connection)) {
            return response()->json(['code' => 500, 'message' => 'Please select the database connection']);
        }

        $connectionInformation = config("database.connections.$connection");
        if ($connection != 'mysql') {
            $storeWebsite = StoreWebsite::where('id', $connection)->first();
            if ($storeWebsite) {
                if ($storeWebsite->server_ip == '') {
                    return response()->json(['code' => '500',  'message' => 'Server ip  is not set!']);
                }
                if ($storeWebsite->database_name == '') {
                    return response()->json(['code' => '500',  'message' => 'Database name is not set!']);
                }
                if ($storeWebsite->mysql_username == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username is not set!']);
                }
                if ($storeWebsite->mysql_password == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username password is not set!']);
                }
                $connectionInformation['host'] = $storeWebsite->server_ip;
                $connectionInformation['database'] = $storeWebsite->database_name;
                $connectionInformation['username'] = $storeWebsite->mysql_username;
                $connectionInformation['password'] = $storeWebsite->mysql_password;
            } else {
                return response()->json(['code' => 500, 'message' => 'Site details is not found']);
            }
        }
        if (empty($connectionInformation)) {
            return response()->json(['code' => 500, 'message' => 'No , database connection is not available']);
        }

        $database = UserDatabase::where('user_id', $id)->where('database', $connection)->first();
        if ($database) {
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql_user.sh -f delete -h '.$connectionInformation['host'].'  -u '.$connectionInformation['username']." -p '".$connectionInformation['password']."' -d ".$connectionInformation['database']."  -n '".$database->username."' 2>&1";
            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput);
            Log::info(print_r($allOutput, true));
            foreach ($database->userDatabaseTables as $dbtables) {
                $dbtables->delete();
            }
            $database->delete();

            UserDatabaseLog::create([
                'user_id' => $id,
                'request_data' => $cmd,
                'response_data' => json_encode($allOutput),
                'updated_by' => Auth::id(),
            ]);

            return response()->json(['code' => 200, 'message' => 'Database access has been removed']);
        }

        return response()->json(['code' => 500, 'message' => "Sorry we couldn't found the access for the given user"]);
    }

    public function chooseDatabase(Request $request, $id): JsonResponse
    {
        $connection = $request->get('connection');

        $database = UserDatabase::where('database', $connection)->where('user_id', $id)->first();
        $tablesExisting = [];
        if ($database) {
            $tablesExisting = UserDatabaseTable::where('user_database_id', $database->id)->pluck('name', 'id')->toArray();
        }

        $user = User::find($id);

        $list = [];
        $tables = [];
        if ($connection != 'mysql') {
            $storeWebsite = StoreWebsite::where('id', $connection)->first();
            if ($storeWebsite) {
                $server_ip = $storeWebsite->server_ip;
                $database_name = $storeWebsite->database_name;
                $mysql_username = $storeWebsite->mysql_username;
                $mysql_password = $storeWebsite->mysql_password;
                if ($server_ip == '') {
                    return response()->json(['code' => '500',  'message' => 'Server ip  is not set!']);
                }
                if ($database_name == '') {
                    return response()->json(['code' => '500',  'message' => 'Database name is not set!']);
                }
                if ($mysql_username == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username is not set!']);
                }
                if ($mysql_password == '') {
                    return response()->json(['code' => '500',  'message' => 'MySql Username password is not set!']);
                }

                //dd([$server_ip,$mysql_username,$mysql_password,$database_name]);
                //Creating a connection
                try {
                    $conn = new \mysqli($server_ip, $mysql_username, $mysql_password, $database_name);

                    if ($conn->connect_error) {
                        return response()->json(['code' => 500, 'message' => 'Connection failed:'.$conn->connect_error]);
                    } else {
                        if ($result = $conn->query('SHOW TABLES')) {
                            $tables = $result->fetch_all(MYSQLI_ASSOC);
                        }
                    }

                    //Closing the connection
                    $conn->close();
                } catch (Exception $e) {
                    return response()->json(['code' => '500',  'message' => $e->getMessage()]);
                }
            } else {
                return response()->json(['code' => 500, 'message' => 'Site details is not found']);
            }
        } else {
            $tables = DB::connection($connection)->select('SHOW TABLES');
        }

        if (! empty($tables)) {
            foreach ($tables as $table) {
                foreach ($table as $t) {
                    $list[] = ['table' => $t, 'checked' => in_array($t, $tablesExisting) ? true : false];
                }
            }
        }
        $data = [
            'user_id' => $id,
            'database' => $database,
            'tables' => $list,
            'user_name' => ($database) ? $database->username : preg_replace('/\s+/', '_', strtolower($user->name)),
            'password' => ($database) ? $database->password : '',
            'tablesExisting' => $tablesExisting,
            'connection' => $connection,
        ];

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        if ($request->type == 'TASK') {
            //issue_id
            $status = TaskStatus::where('name', $request->is_resolved)->select('id')->get();
            if ($status) {
                Task::where('id', $request->issue_id)
                    ->update(['status' => $status[0]->id]);

                return response()->json(['code' => 200, 'data' => 'Success']);
            }
        }
        if ($request->type == 'DEVTASK') {
            DeveloperTask::where('id', $request->issue_id)
                ->update(['status' => $request->is_resolved]);

            return response()->json(['code' => 200, 'data' => 'Success']);
        }

        return response()->json(['code' => 500, 'data' => 'Error']);
    }

    public function systemIps(Requests $request): JsonResponse
    {
        $shell_list = shell_exec('bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f list');

        return response()->json(['code' => 200, 'data' => $shell_list]);
    }

    public function downloadPemFile(Request $request, $id): RedirectResponse
    {
        $pemHistory = UserPemfileHistory::find($id);
        if (! $pemHistory) {
            return redirect()->back()->with('error', 'PEM File data not found!');
        }

        $server = AssetsManager::where('id', $pemHistory->server_id)->first();
        if (! $server) {
            return redirect()->back()->with('error', 'Server data not found!');
        }
        $server = $server->ip;
        $username = $pemHistory->username;
        $public_key = $pemHistory->public_key;
        if ($public_key == '') {
            return redirect()->back()->with('error', 'Public key is not found!');
        }

        $access_type = $pemHistory->access_type;
        $content = $pemHistory->pem_content;
        $nameF = $pemHistory->server_name.'.pem';

        //header download
        header('Content-Disposition: attachment; filename="'.$nameF.'"');
        header('Content-Type: application/force-download');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/x-pem-file');

        echo $content;
        exit;
    }

    public function userGenerateStorefile(Request $request): JsonResponse
    {
        $server_id = $request->get('for_server');
        $user = User::find($request->get('userid', 0));
        if (! $user) {
            return response()->json(['code' => 500, 'message' => 'User data Not found!']);
        }
        $public_key = $request->public_key;
        if ($public_key == '') {
            return response()->json(['code' => 500, 'message' => 'Please enter public key']);
        }
        $access_type = $request->get('access_type', 'sftp');
        $server = AssetsManager::where('id', $server_id)->first();
        if (! $server) {
            return response()->json(['code' => 500, 'message' => 'Server data Not found!']);
        }
        $user_role = $request->get('user_role');
        $username = str_replace(' ', '_', $user->name);

        $var_t_sftp = 'true';
        $var_b_ssh = 'false';
        if ($access_type == 'ssh') {
            $var_t_sftp = 'false';
            $var_b_ssh = 'true';
        }
        $server_ip = $server->ip;

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'pem-generate.sh -u '.$username.' -f add -s '.$server_ip.' -t '.$var_t_sftp.'  -b '.$var_b_ssh.'  -k "'.$public_key.'" -R '.$user_role.' 2>&1';
        Log::info('Generate Pem Files:');
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput, $return_var);

        Log::info(print_r($allOutput, true));

        $string = [];
        if (! empty($allOutput)) {
            $continuetoFill = false;
            foreach ($allOutput as $ao) {
                if (strpos($ao, 'PRIVATE KEY-----') !== false || $continuetoFill) {
                    $string[] = $ao;
                    $continuetoFill = true;
                }
            }
        }

        $content = implode("\n", $string);
        $content = $content."\n";

        $userPemfileHistory = UserPemfileHistory::create([
            'user_id' => $request->userid,
            'server_id' => $server->id,
            'server_name' => $server->name,
            'server_ip' => $server->ip,
            'username' => $username,
            'public_key' => $public_key,
            'access_type' => $access_type,
            'user_role' => $user_role,
            'pem_content' => $content,
            'action' => 'add',
            'created_by' => $request->user()->id,
        ]);

        (new UserPemfileHistoryLog)->saveLog($userPemfileHistory->id, $cmd, $allOutput, $return_var);

        return response()->json(['code' => 200, 'message' => 'PEM file generate sucessfully!']);
    }

    public function userPemfileHistoryListing(Request $request): JsonResponse
    {
        $history = UserPemfileHistory::with('user')->withTrashed()->where('user_id', $request->userid)->latest()->get();

        return response()->json(['code' => 200, 'data' => $history]);
    }

    public function disablePemFile(Request $request, $id): JsonResponse
    {
        $pemHistory = UserPemfileHistory::find($id);
        if ($pemHistory) {
            $server = AssetsManager::where('id', $pemHistory->server_id)->first();
            if (! $server) {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Server data not found!']);
            }
            $access_type = $pemHistory->access_type;
            $var_t_sftp = true;
            $var_b_ssh = false;
            if ($access_type == 'ssh') {
                $var_t_sftp = false;
                $var_b_ssh = true;
            }

            Log::info('Disable Pem Access:'.$id);
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'pem-generate.sh -u '.$pemHistory->username.' -f disable -s '.$server->id.' -t '.$var_t_sftp.'  -b '.$var_b_ssh.'  2>&1';

            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput, $return_var);
            Log::info(print_r($allOutput, true));
            $pemHistory->action = 'disable';
            $pemHistory->created_by = auth()->user()->id;
            $pemHistory->save();

            (new UserPemfileHistoryLog)->saveLog($pemHistory->id, $cmd, $allOutput, $return_var);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Pem access disable successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No request found']);
        }
    }

    public function deletePemFile(Request $request, $id): JsonResponse
    {
        $pemHistory = UserPemfileHistory::find($id);
        if ($pemHistory) {
            $server = AssetsManager::where('id', $pemHistory->server_id)->first();
            if (! $server) {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Server data not found!']);
            }
            $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'pem-generate.sh -u '.$pemHistory->username.' -f delete -s '.$server->id.' 2>&1';
            Log::info('Delete Pem Access:'.$id);
            $allOutput = [];
            $allOutput[] = $cmd;
            $result = exec($cmd, $allOutput, $return_var);
            Log::info(print_r($allOutput, true));
            $pemHistory->action = 'delete';
            $pemHistory->created_by = auth()->user()->id;
            $pemHistory->save();
            $pemHistory->delete();

            (new UserPemfileHistoryLog)->saveLog($pemHistory->id, $cmd, $allOutput, $return_var);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Pem access remove successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No request found']);
        }
    }

    public function addFeedbackCategory(Request $request)
    {
        $cat = UserFeedbackCategory::where('category', $request->category);
        if ($cat->count() != 0) {
            return response()->json(['message' => 'Category already exists']);
        }
        $category = new UserFeedbackCategory;
        $category->user_id = Auth::id();
        $category->category = $request->category;
        $category->save();
        $status = UserFeedbackStatus::get();

        return view('usermanagement::user-feedback-data', compact('category', 'status'));
        // return response()->json(["status" => true , 'category' => $category]);
    }

    public function addFeedbackStatus(Request $request)
    {
        $status = UserFeedbackStatus::where('status', $request->status);
        if ($status->count() === 0) {
            $feedback_status = new UserFeedbackStatus;
            $feedback_status->status = $request->status;
            $feedback_status->save();
            // return view('usermanagement::user-feedback-data',compact('status'));
        }
        $all_status = UserFeedbackStatus::get();

        return response()->json(['status' => true, 'feedback_status' => $all_status]);
    }

    public function addFeedbackTableData(Request $request): View
    {
        $status = UserFeedbackStatus::get();

        $user_id = $request->user_id;
        $category = UserFeedbackCategory::groupBy('category')->get();

        return view('usermanagement::user-feedback-table', compact('category', 'status', 'user_id'));
    }

    public function updateFeedbackStatus(Request $request): JsonResponse
    {
        $cat_id = $request->cat_id;
        $user_id = $request->user_id;
        $status_id = $request->status_id;
        $status = UserFeedbackStatusUpdate::where('user_feedback_category_id', $cat_id)->where('user_id', $user_id)->first();
        if (! $status) {
            $status = new UserFeedbackStatusUpdate;
        }
        $status->user_id = $user_id;
        $status->user_feedback_status_id = $status_id ? $status_id : 0;
        $status->user_feedback_category_id = $cat_id;
        $status->save();

        return response()->json(['message' => 'Status Update Successful']);
    }

    public function updateTaskPlanFlag(): JsonResponse
    {
        User::where('id', request('user_id'))->update([
            'is_task_planned' => request('is_task_planned') ? 0 : 1,
        ]);

        return response()->json([
            'message' => request('is_task_planned') ? 'User unflagged successfully.' : 'User flagged successfully.',
            'flag' => request('is_task_planned') ? 0 : 1,
        ]);
    }

    // User Schedules
    public function userSchedulesIndex(): View
    {
        $statusList = TaskStatus::pluck('name', 'name')->prepend('Please select', '');

        $status = UserScheduleStatus::all();

        $userType = 0; //user
        if (Auth::user()->isAdmin()) {
            $userType = 1; //admin
        }

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();

        return view('usermanagement::user-schedules.index', [
            'title' => 'User Schedules',
            'urlLoadData' => route('user-management.user-schedules.load-data'),
            'statusList' => $statusList,
            'userType' => $userType,
            'allUsers' => $allUsers,
            'status' => $status,
            'listUsers' => User::dropdown([
                'is_active' => 1,
            ]),

        ]);
    }

    public function taskCount($site_developement_id): JsonResponse
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        //print_r($taskStatistics);
        $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function taskCountAdmin($site_developement_id): JsonResponse
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('subject', 'LIKE', '%User Schedules Page - %')->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('subject', 'LIKE', '%User Schedules Page - %')->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();

        $merged = $taskStatistics;

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function addNewRequest(Request $request)
    {
        $UserScheduleRequest = new UserScheduleRequest;
        $UserScheduleRequest->user_id = $request->user_id;
        $UserScheduleRequest->request_date = $request->request_date;
        $UserScheduleRequest->request_status = 'requested';
        $UserScheduleRequest->save();

        return $UserScheduleRequest;
    }

    public function updateRequest(Request $request)
    {
        $UserScheduleRequested = UserScheduleRequest::where('user_id', '=', $request->user_id)->where('request_date', '=', $request->request_date)->orderByDesc('id')->first();

        $UserScheduleRequested->request_status = $request->request_status;
        $UserScheduleRequested->updated_by = Auth::id();
        $UserScheduleRequested->update();

        return $UserScheduleRequested;
    }

    public function userSchedulesLoadData()
    {
        try {
            $usertemp = 0;
            $count = 0;
            $data = [];

            $isPrint = ! request()->ajax();

            // _p(hourlySlots('2022-08-10 10:10:00', '2022-08-10 15:15:00', '12:05:00'));
            // exit;

            $stDate = request('srchDateFrom');
            $enDate = request('srchDateTo');
            if ($stDate && $enDate) {
                $filterDates = dateRangeArr($stDate, $enDate);
                $filterDatesNew = [];
                foreach ($filterDates as $row) {
                    $filterDatesNew[$row['date']] = $row;
                }

                $q = User::query();
                $q->leftJoin('user_avaibilities as ua', 'ua.user_id', '=', 'users.id');
                $q->where('users.is_task_planned', 1);
                $q->where('ua.is_latest', 1);
                if (! isAdmin()) {
                    $q->where('users.id', loginId());
                }
                if ($srch = request('srchUser')) {
                    $q->where('users.id', $srch);
                }
                if (request('is_active')) {
                    $q->where('users.is_active', request('is_active') == 1 ? 1 : 0);
                }
                $q->select([
                    'users.id',
                    'users.name',
                    DB::raw('ua.id AS uaId'),
                    DB::raw('ua.date AS uaDays'),
                    DB::raw('ua.from AS uaFrom'),
                    DB::raw('ua.to AS uaTo'),
                    DB::raw('ua.start_time AS uaStTime'),
                    DB::raw('ua.end_time AS uaEnTime'),
                    DB::raw('ua.lunch_time AS uaLunchTime'),
                    DB::raw('ua.lunch_time_from AS lunch_time_from'),
                    DB::raw('ua.lunch_time_to AS lunch_time_to'),
                ]);
                $users = $q->get();
                $count = $users->count();

                // _p( getHourlySlots('2022-08-11 22:05:00', '2022-08-12 02:45:00') );
                // exit;

                if ($count) {
                    $filterDatesOnly = array_column($filterDates, 'date');

                    $userIds = [];

                    // _p($users->toArray(), 1);

                    // Prepare user's data
                    $userArr = [];
                    foreach ($users as $single) {
                        $userIds[] = $single->id;
                        if ($single->uaId) {
                            $single->uaStTime = date('H:i:00', strtotime($single->uaStTime));
                            $single->uaEnTime = date('H:i:00', strtotime($single->uaEnTime));
                            $single->uaLunchTime = $single->uaLunchTime ? date('H:i:00', strtotime($single->uaLunchTime)) : '';

                            $single->uaDays = $single->uaDays ? explode(',', str_replace(' ', '', $single->uaDays)) : [];
                            $availableDates = UserAvaibility::getAvailableDates($single->uaFrom, $single->uaTo, $single->uaDays, $filterDatesOnly);
                            $availableSlots = UserAvaibility::dateWiseHourlySlotsV2($availableDates, $single->uaStTime, $single->uaEnTime, $single->uaLunchTime, $single);

                            $userArr[] = [
                                'id' => $single->id,
                                'name' => $single->name,
                                'uaLunchTime' => $single->uaLunchTime ? substr($single->uaLunchTime, 0, 5) : '',
                                'uaId' => $single->uaId,
                                'uaDays' => $single->uaDays,
                                'availableDays' => $single->uaDays,
                                'availableDates' => $availableDates,
                                'availableSlots' => $availableSlots,
                            ];
                        } else {
                            $userArr[] = [
                                'id' => $single->id,
                                'name' => $single->name,
                                'uaLunchTime' => null,
                                'uaId' => null,
                                'uaDays' => [],
                                'availableDays' => [],
                                'availableDates' => [],
                                'availableSlots' => [],
                            ];
                        }
                    }

                    // Get Tasks & Developer Tasks -- Arrange with End time & Mins
                    $tasksArr = [];
                    $usertasksArr = [];
                    $userdevtasksArr = [];
                    if ($userIds) {
                        $tasksInProgress = $this->typeWiseTasks('IN_PROGRESS', [
                            'userIds' => $userIds,
                        ], request('task_status'), request('task_type_filter'));

                        $tasksPlanned = $this->typeWiseTasks('PLANNED', [
                            'userIds' => $userIds,
                        ], request('task_status'), request('task_type_filter'));

                        if ($tasksInProgress) {
                            foreach ($tasksInProgress as $task) {
                                $task->st_date = date('Y-m-d H:i:00', strtotime($task->st_date));

                                if (! isset($task->en_date)) {
                                    $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date.' + '.$task->est_minutes.'minutes'));
                                }
                                // if ($task->en_date <= date('Y-m-d H:i:s')) {
                                //     $task->en_date = date('Y-m-d H:i:00', strtotime('+1 hour'));
                                //     $task->est_minutes = 60;
                                // } else {
                                //     // $task->est_minutes = ceil((strtotime($task->en_date) - $task->st_date) / 60);
                                // }

                                if (! in_array($task->id, $usertasksArr)) {
                                    $usertasksArr[] = $task->id;
                                    $tasksArr[$task->assigned_to][$task->status2][] = [
                                        'id' => $task->id,
                                        'typeId' => $task->type.'-'.$task->id,
                                        'stDate' => $task->st_date,
                                        'enDate' => $task->en_date,
                                        'status' => $task->status,
                                        'status2' => $task->status2,
                                        'mins' => $task->est_minutes,
                                        'manually_assign' => $task->manually_assign,
                                        'estimate_minutes' => $task->estimate_minutes,
                                        'slotTaskRemarks' => $task->slotTaskRemarks,
                                        'task_type' => 'tasks',
                                        'mt_start_date' => $task->mt_start_date,
                                        'mt_end_date' => $task->mt_end_date,
                                    ];
                                }
                            }
                        }
                        if ($tasksPlanned) {
                            foreach ($tasksPlanned as $task) {
                                if (! in_array($task->id, $usertasksArr)) {
                                    $usertasksArr[] = $task->id;

                                    $task->est_minutes = 20;
                                    $task->st_date = $task->st_date ?: date('Y-m-d H:i:00');
                                    $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date.' + '.$task->est_minutes.'minutes'));
                                    $tasksArr[$task->assigned_to][$task->status2][] = [
                                        'id' => $task->id,
                                        'typeId' => $task->type.'-'.$task->id,
                                        'stDate' => $task->st_date,
                                        'enDate' => $task->en_date,
                                        'status' => $task->status,
                                        'status2' => $task->status2,
                                        'mins' => $task->est_minutes,
                                        'manually_assign' => $task->manually_assign,
                                        'estimate_minutes' => $task->estimate_minutes,
                                        'slotTaskRemarks' => $task->slotTaskRemarks,
                                        'task_type' => 'dev_tasks',
                                        'mt_start_date' => $task->mt_start_date,
                                        'mt_end_date' => $task->mt_end_date,
                                    ];
                                }
                            }
                        }
                    }
                    if ($isPrint) {
                        _p($tasksArr);
                    }
                    // dd($tasksArr);

                    // Arrange tasks on users slots
                    foreach ($userArr as $k1 => $user) {
                        $userTasksArr = isset($tasksArr[$user['id']]) && count($tasksArr[$user['id']]) ? $tasksArr[$user['id']] : [];
                        if ($user['uaId'] && isset($user['availableSlots']) && count($user['availableSlots'])) {
                            foreach ($user['availableSlots'] as $date => $slots) {
                                foreach ($slots as $k2 => $slot) {
                                    if ($slot['type'] == 'PAST' || $slot['type'] == 'AVL' || $slot['slot_type'] == 'AVL') {
                                        $res = $this->slotIncreaseAndShift($slot, $userTasksArr);
                                        // dd($res, $userTasks);

                                        $userTasks = $res['userTasks'] ?? [];
                                        $slot['taskIds'] = $res['taskIds'] ?? [];
                                        $slot['userTasks'] = $res['userTasks'] ?? [];
                                    }
                                    // else if ($slotRow['type'] == 'LUNCH') {
                                    //     // $userTasks = $this->slotIncreaseAndShift($userTasks, $slotKey);
                                    // }
                                    $slots[$k2] = $slot;
                                }

                                $user['availableSlots'][$date] = $slots;
                            }
                        }
                        $userArr[$k1] = $user;
                    }

                    if ($isPrint) {
                        _p($userArr);
                    }

                    // Arange for datatable
                    foreach ($userArr as $user) {
                        if ($user['uaId'] && isset($user['availableSlots']) && count($user['availableSlots'])) {
                            foreach ($user['availableSlots'] as $date => $slots) {
                                $divSlots = [];
                                // dd($slots);
                                foreach ($slots as $slot) {
                                    $title = '';
                                    $class = '';
                                    $display = [
                                        date('H:i', strtotime($slot['st'])),
                                        ' - ',
                                        date('H:i', strtotime($slot['en'])),
                                    ];

                                    $displayManually = [
                                        date('H:i', strtotime($slot['st'])),
                                        ' - ',
                                        date('H:i', strtotime($slot['en'])),
                                    ];

                                    $displayManually = [];
                                    $displayText = [];
                                    $displayTextManually = [];
                                    $displayManuallyMove = [];
                                    $taskArray = [];
                                    $devtaskArray = [];
                                    $plusIconArray = [];
                                    $avaibilityGreenTask = [];
                                    $avaibilityYellowTask = [];
                                    $avaibilityOrangeTask = [];

                                    if (in_array($slot['type'], ['AVL', 'SMALL-LUNCH', 'LUNCH-START', 'LUNCH-END', 'PAST'])) {
                                        $ut_array = [];
                                        $ut_arrayManually = [];

                                        if ($slot['type'] == 'PAST') {
                                            $todaydate = date('Y-m-d');
                                            $pastdate = date('Y-m-d', strtotime(' -1 day'));
                                            $slotdate = date('Y-m-d', strtotime($slot['st']));

                                            if ($todaydate == $slotdate || $pastdate == $slotdate) {
                                                if (! empty($slot['userTasks'])) {
                                                    foreach ($slot['userTasks'] as $ut) {
                                                        if ($ut['manually_assign'] == 1) {
                                                            $taskContentButton = '';
                                                            if ($ut['slotTaskRemarks'] !== null) {
                                                                $taskContentButton .= '<a href="javascript:void(0)" class="slotTaskRemarks_class" data-remarks="'.$ut['slotTaskRemarks'].'" style="margin-right: 5px;"><i class="fa fa-commenting" aria-hidden="true"></i></a>';
                                                            }

                                                            if ($ut['estimate_minutes'] !== null) {
                                                                $taskContentButton .= '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                            }

                                                            $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                            array_push($ut_arrayManually, $ut['typeId'].' - '.$taskContentButton);
                                                        } else {
                                                            $taskContentButton = '';
                                                            if ($ut['estimate_minutes'] !== null) {
                                                                $taskContentButton = '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="color: green;margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                            }

                                                            $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                            array_push($ut_array, $ut['typeId'].' - '.$taskContentButton);
                                                        }

                                                        $position = strpos($ut['typeId'], 'DT-');

                                                        if ($position !== false) {
                                                            $devtaskArray[] = $ut['id'];
                                                        } else {
                                                            $taskArray[] = $ut['id'];
                                                        }

                                                        array_push($displayManuallyMove, $ut['typeId']);
                                                        // foreach ($ut as $t) {
                                                        //     dd($ut);
                                                        // }
                                                    }
                                                } else {
                                                    if ($todaydate == $slotdate) {
                                                        $avaibilityYellowTask[] = $slot['st'];
                                                    }

                                                    if ($pastdate == $slotdate) {
                                                        $avaibilityOrangeTask[] = $slot['st'];
                                                    }
                                                }
                                                // $generalTaskID = [];
                                                // if (isset($slot['taskIds'])) {
                                                //     $generalTaskID = array_keys($slot['taskIds']);
                                                // }
                                                $developerTaskID = $ut_array;
                                                if (! empty($developerTaskID)) {
                                                    $display[] = '</br><span style="color:green !important">'.implode(', </br>', $developerTaskID).'</span>';

                                                    $title = [];
                                                    foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                        $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                    }
                                                    $title = implode(PHP_EOL, $title);
                                                }

                                                $developerTaskIDManually = $ut_arrayManually;
                                                if (! empty($developerTaskIDManually)) {
                                                    $displayManually[] = implode('</br>', $developerTaskIDManually);

                                                    $title = [];
                                                    foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                        $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                    }
                                                    $title = implode(PHP_EOL, $title);
                                                }

                                                if ($slot['type'] == 'SMALL-LUNCH') {
                                                    $display[] = '<br>Lunch time ('.date('H:i', strtotime($slot['lunch_time']['from'])).'-'.date('H:i', strtotime($slot['lunch_time']['to'])).')';
                                                } elseif ($slot['type'] == 'LUNCH-START') {
                                                    $display[] = '<br>Lunch start at: '.date('H:i', strtotime($slot['lunch_time']['from']));
                                                } elseif ($slot['type'] == 'LUNCH-END') {
                                                    $display[] = '<br>Lunch end at: '.date('H:i', strtotime($slot['lunch_time']['to']));
                                                } else {
                                                    $class = 'text-secondary';
                                                    $display[] = '';
                                                    $plusIconArray[] = ' <a href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-slot="'.date('H:i', strtotime($slot['new_st'] ?? $slot['st'])).'" onclick="funSlotAssignModal(this);" ><i class="fa fa-plus" aria-hidden="true"></i></a>';
                                                }
                                            } else {
                                                if (! empty($slot['userTasks'])) {
                                                    foreach ($slot['userTasks'] as $ut) {
                                                        if ($ut['manually_assign'] == 1) {
                                                            $taskContentButton = '';
                                                            if ($ut['slotTaskRemarks'] !== null) {
                                                                $taskContentButton .= '<a href="javascript:void(0)" class="slotTaskRemarks_class" data-remarks="'.$ut['slotTaskRemarks'].'" style="margin-right: 5px;"><i class="fa fa-commenting" aria-hidden="true"></i></a>';
                                                            }

                                                            if ($ut['estimate_minutes'] !== null) {
                                                                $taskContentButton .= '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                            }

                                                            $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                            array_push($ut_arrayManually, $ut['typeId'].' - '.$taskContentButton);
                                                        } else {
                                                            $taskContentButton = '';
                                                            if ($ut['estimate_minutes'] !== null) {
                                                                $taskContentButton = '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="color: green;margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                            }

                                                            $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                            array_push($ut_array, $ut['typeId'].' - '.$taskContentButton);
                                                        }

                                                        $position = strpos($ut['typeId'], 'DT-');

                                                        if ($position !== false) {
                                                            $devtaskArray[] = $ut['id'];
                                                        } else {
                                                            $taskArray[] = $ut['id'];
                                                        }

                                                        array_push($displayManuallyMove, $ut['typeId']);
                                                        // foreach ($ut as $t) {
                                                        //     dd($ut);
                                                        // }
                                                    }
                                                } else {
                                                    $avaibilityOrangeTask[] = $slot['st'];
                                                }
                                                // $generalTaskID = [];
                                                // if (isset($slot['taskIds'])) {
                                                //     $generalTaskID = array_keys($slot['taskIds']);
                                                // }
                                                $developerTaskID = $ut_array;
                                                if (! empty($developerTaskID)) {
                                                    $display[] = '</br><span style="color:green !important">'.implode(', </br>', $developerTaskID).'</span>';

                                                    $title = [];
                                                    foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                        $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                    }
                                                    $title = implode(PHP_EOL, $title);
                                                }

                                                $developerTaskIDManually = $ut_arrayManually;
                                                if (! empty($developerTaskIDManually)) {
                                                    $displayManually[] = implode('</br>', $developerTaskIDManually);

                                                    $title = [];
                                                    foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                        $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                    }
                                                    $title = implode(PHP_EOL, $title);
                                                }

                                                if ($slot['type'] == 'SMALL-LUNCH') {
                                                    $display[] = '<br>Lunch time ('.date('H:i', strtotime($slot['lunch_time']['from'])).'-'.date('H:i', strtotime($slot['lunch_time']['to'])).')';
                                                } elseif ($slot['type'] == 'LUNCH-START') {
                                                    $display[] = '<br>Lunch start at: '.date('H:i', strtotime($slot['lunch_time']['from']));
                                                } elseif ($slot['type'] == 'LUNCH-END') {
                                                    $display[] = '<br>Lunch end at: '.date('H:i', strtotime($slot['lunch_time']['to']));
                                                } else {
                                                    $class = 'text-secondary';

                                                    $display[] = '<s> ('.$slot['slot_type'].')</s>';

                                                    if (! Auth::user()->isAdmin()) {
                                                        $UserScheduleRequested = UserScheduleRequest::where('user_id', '=', $user['id'])->where('request_date', '=', $date)->orderByDesc('id')->first();

                                                        if (! empty($UserScheduleRequested)) {
                                                            if ($UserScheduleRequested->request_status == 'accepted') {
                                                                $plusIconArray[] = ' <a href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-slot="'.date('H:i', strtotime($slot['new_st'] ?? $slot['st'])).'" onclick="funSlotAssignModal(this);"><i class="fa fa-plus" aria-hidden="true"></i></a>';
                                                            }
                                                        }
                                                    } else {
                                                        $plusIconArray[] = ' <a href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-slot="'.date('H:i', strtotime($slot['new_st'] ?? $slot['st'])).'" onclick="funSlotAssignModal(this);" ><i class="fa fa-plus" aria-hidden="true"></i></a>';
                                                    }
                                                }
                                            }
                                        } else {
                                            if (! empty($slot['userTasks'])) {
                                                foreach ($slot['userTasks'] as $ut) {
                                                    if ($ut['manually_assign'] == 1) {
                                                        $taskContentButton = '';
                                                        if ($ut['slotTaskRemarks'] !== null) {
                                                            $taskContentButton .= '<a href="javascript:void(0)" class="slotTaskRemarks_class" data-remarks="'.$ut['slotTaskRemarks'].'" style="margin-right: 5px;"><i class="fa fa-commenting" aria-hidden="true"></i></a>';
                                                        }

                                                        if ($ut['estimate_minutes'] !== null) {
                                                            $taskContentButton .= '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                        }

                                                        $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                        array_push($ut_arrayManually, $ut['typeId'].' - '.$taskContentButton);
                                                    } else {
                                                        $taskContentButton = '';
                                                        if ($ut['estimate_minutes'] !== null) {
                                                            $taskContentButton = '<a href="javascript:void(0)" class="estimate_minutes_class" data-time="'.$ut['estimate_minutes'].'" style="color: green;margin-right: 5px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
                                                        }

                                                        $taskContentButton .= '<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="'.$ut['id'].'" title="Load messages" style="display: contents;"><img src="/images/chat.png" alt="" style="cursor: nwse-resize; width: 0px;"></button>';

                                                        array_push($ut_array, $ut['typeId'].' - '.$taskContentButton);
                                                    }

                                                    $position = strpos($ut['typeId'], 'DT-');

                                                    if ($position !== false) {
                                                        $devtaskArray[] = $ut['id'];
                                                    } else {
                                                        $taskArray[] = $ut['id'];
                                                    }

                                                    array_push($displayManuallyMove, $ut['typeId']);
                                                    // foreach ($ut as $t) {
                                                    //     dd($ut);
                                                    // }
                                                }
                                            } else {
                                                if (in_array($slot['type'], ['AVL'])) {
                                                    $avaibilityGreenTask[] = $slot['st'];
                                                }
                                            }
                                            // $generalTaskID = [];
                                            // if (isset($slot['taskIds'])) {
                                            //     $generalTaskID = array_keys($slot['taskIds']);
                                            // }
                                            $developerTaskID = $ut_array;
                                            if (! empty($developerTaskID)) {
                                                $display[] = '</br><span style="color:green !important">'.implode(', </br>', $developerTaskID).'</span>';

                                                $title = [];
                                                foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                    $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                }
                                                $title = implode(PHP_EOL, $title);
                                            }

                                            $developerTaskIDManually = $ut_arrayManually;
                                            if (! empty($developerTaskIDManually)) {
                                                $displayManually[] = implode('</br>', $developerTaskIDManually);

                                                $title = [];
                                                foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                    $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                                }
                                                $title = implode(PHP_EOL, $title);
                                            }

                                            if ($slot['type'] == 'SMALL-LUNCH') {
                                                //$display[] = '<br>Lunch time (' . date('H:i', strtotime($slot['lunch_time']['from'])) . '-' . date('H:i', strtotime($slot['lunch_time']['to'])) . ')';

                                                $display[] = '<br>Lunch time';
                                            } elseif ($slot['type'] == 'LUNCH-START') {
                                                $display[] = '<br>Lunch start at: '.date('H:i', strtotime($slot['lunch_time']['from']));
                                            } elseif ($slot['type'] == 'LUNCH-END') {
                                                $display[] = '<br>Lunch end at: '.date('H:i', strtotime($slot['lunch_time']['to']));
                                            } else {
                                                $class = 'text-secondary';
                                                $display[] = '';
                                                $plusIconArray[] = ' <a href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-slot="'.date('H:i', strtotime($slot['new_st'] ?? $slot['st'])).'" onclick="funSlotAssignModal(this);" title="Add Task"><i class="fa fa-plus" aria-hidden="true"></i></a>';
                                            }
                                        }

                                        // $title
                                        $display = implode('', $display);

                                        $displayManually = implode('', $displayManually);

                                        $plusIcon = implode('', $plusIconArray);
                                    } elseif (in_array($slot['slot_type'], ['LUNCH'])) {
                                        $title = 'Not Available';
                                        $class = 'text-secondary';
                                        $display[] = ' ('.$slot['slot_type'].')';
                                        $display = '<s>'.implode('', $display).'</s>';
                                        $plusIcon = '';
                                    } /*elseif (in_array($slot['slot_type'], ['PAST'])) {

                                        $todaydate = date("Y-m-d");
                                        $taskdate = date("Y-m-d", strtotime($slot['st']));

                                        if($todaydate==$taskdate){
                                            $title = 'Not Available';
                                            $class = 'text-secondary';
                                            $display[] = '<s> (' . $slot['slot_type'] . ')</s>';

                                            $display[] = ' <a href="javascript:void(0);" data-user_id="' . $user['id'] . '" data-date="' . $date . '" data-slot="' . date('H:i', strtotime($slot['new_st'] ?? $slot['st'])) . '" onclick="funSlotAssignModal(this);" >(AVL)</a>';

                                            $display = implode('', $display);
                                        } else {
                                            $title = 'Not Available';
                                            $class = 'text-secondary';
                                            $display[] = ' (' . $slot['slot_type'] . ')';
                                            $display = '<s>' . implode('', $display) . '</s>';
                                        }
                                    }*/
                                    // elseif ($slot['type'] == "SMALL-LUNCH") {
                                    //     $title = 'LUNCH';
                                    //     $class = 'text-secondary';
                                    //     $display[] = ' ('.$slot['type'].')';
                                    //     $display = '<s>'.implode('', $display).'</s>';
                                    // }
                                    elseif ($slot['type'] == 'FULL-LUNCH') {
                                        $title = 'LUNCH';
                                        $class = 'text-secondary';
                                        $display[] = ' (LUNCH)';
                                        $display = '<s>'.implode('', $display).'</s>';
                                        $plusIcon = '';
                                    }
                                    // elseif ($slot['type'] == "LUNCH-START") {
                                    //     dd($slot);
                                    //     $title = 'LUNCH';
                                    //     $class = 'text-secondary';
                                    //     $display[] = ' ('.$slot['type'].')';
                                    //     $display = '<s>'.implode('', $display).'</s>';
                                    // }
                                    // elseif ($slot['type'] == "LUNCH-END") {
                                    //     $title = 'LUNCH';
                                    //     $class = 'text-secondary';
                                    //     $display[] = ' ('.$slot['type'].')';
                                    //     $display = '<s>'.implode('', $display).'</s>';
                                    // }

                                    //$divSlots[] = '<div class="div-slot ' . $class . '" title="' . $title . '" >' . $display . '</div><div class="div-slot ' . $class . '" title="' . $title . '" >' . $displayManually . '</div>';

                                    /*$green = '';
                                    $yellow = '';
                                    $orange = '';
                                    if(!empty($avaibilityGreenTask)){
                                        $green = 'green';
                                    }

                                    if(!empty($avaibilityYellowTask)){
                                        $yellow = 'yellow';
                                    }

                                    if(!empty($avaibilityOrangeTask)){
                                        $orange = 'orange';
                                    }*/

                                    $colorVar = '';
                                    if (! empty($avaibilityGreenTask)) {
                                        $colorVar = 'greenClass';
                                    }

                                    if (! empty($avaibilityYellowTask)) {
                                        $colorVar = 'yellowClass';
                                    }

                                    if (! empty($avaibilityOrangeTask)) {
                                        $colorVar = 'orangeClass';
                                    }

                                    $divSlotsVar = '<div class="'.$colorVar.'">';

                                    //$divSlotsVar .= '<div class="div-slot ' . $class . '" title="' . $title . '" >' . $display .' '.$green.' '.$yellow.' '.$orange.'</div>';

                                    $divSlotsVar .= '<div class="div-slot '.$class.'" title="'.$title.'" >'.$display.'</div>';

                                    if (! empty($displayManually)) {
                                        $divSlotsVar .= '<div class="div-slot '.$class.'" style="color:blue !important" title="'.$title.'" >'.$displayManually.'</div>';
                                    }

                                    $divSlotsVar .= '<div class="icon-div">';
                                    if (! empty($plusIcon)) {
                                        $divSlotsVar .= $plusIcon;
                                    }

                                    if (! empty($displayManuallyMove)) {
                                        if ($slot['type'] == 'AVL' || $slot['slot_type'] == 'AVL') {
                                            $divSlotsVar .= ' <a href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-slot="'.date('H:i', strtotime($slot['new_st'] ?? $slot['st'])).'" onclick="funSlotMoveModal(this);" data-tasks="'.implode(', ', $taskArray).'" data-dev_tasks="'.implode(', ', $devtaskArray).'" title="Move Task"><i class="fa fa-exchange" aria-hidden="true"></i></a>';
                                        }
                                    }
                                    $divSlotsVar .= '</div></div>';

                                    $divSlots[] = $divSlotsVar;
                                }
                                /*
                                $data[] = [
                                    'name' => $user['name'],
                                    'date' => $date,
                                    'slots' => implode('', $divSlots),
                                ];
                                */

                                $data[$usertemp]['name'] = $user['name'];

                                $pastdatesend = date('Y-m-d', strtotime(' -1 day'));

                                $dateContent = $date;
                                if ($pastdatesend > $date) {
                                    $UserScheduleRequested = UserScheduleRequest::where('user_id', '=', $user['id'])->where('request_date', '=', $date)->orderByDesc('id')->first();

                                    $requested = '';
                                    if (! empty($UserScheduleRequested)) {
                                        $requested = $UserScheduleRequested->request_status;
                                    }

                                    if (! Auth::user()->isAdmin()) {
                                        $dateContent .= '</br><a style="padding: 2px;" class="btn btn-primary" href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-requested="'.$requested.'" id="send-request-date" title="Send Request To Edit Task"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>';

                                        if ($requested == 'accepted') {
                                            $dateContent .= '</br><span style="color:green"> Request '.$requested.'. You can add task to the slot.</span>';
                                        }
                                    } else {
                                        if (! empty($requested)) {
                                            $dateContent .= '</br><a style="padding: 0px;" class="btn btn-success send-request-date-admin" href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-requested="'.$requested.'" data-status="accepted" class="" title="Accepet Request To Edit Task"><i class="fa fa-check" aria-hidden="true"></i></a>';

                                            $dateContent .= '<a style="padding: 0px 2px; margin-left: 5px;" class="btn btn-danger send-request-date-admin" href="javascript:void(0);" data-user_id="'.$user['id'].'" data-date="'.$date.'" data-requested="'.$requested.'" data-status="denied" title="Denied Request To Edit Task"><i class="fa fa-times" aria-hidden="true"></i></a>';

                                            if ($requested == 'accepted') {
                                                $dateContent .= '</br><span style="color:green"> Request '.$requested.'.</span>';
                                            } elseif ($requested == 'denied') {
                                                $dateContent .= '</br><span  style="color:red"> Request '.$requested.'.</span>';
                                            }
                                        }
                                    }
                                }
                                $data[$usertemp]['date'] = $dateContent;

                                for ($p = 0; $p < 9; $p++) {
                                    $varid = 'slots'.$p;
                                    if (isset($divSlots[$p])) {
                                        $str = str_replace('(AVL)', '<br>(AVL)', $divSlots[$p]);
                                        $str = str_replace('(LUNCH)', '<br>(LUNCH)', $divSlots[$p]);
                                        $str = str_replace('(PAST)', '<br>(PAST)', $divSlots[$p]);
                                        $data[$usertemp][$varid] = $str;
                                    } else {
                                        $data[$usertemp][$varid] = '';
                                    }
                                }
                                $usertemp = $usertemp + 1;
                            }
                        } else {
                            /*
                             $data[] = [
                                 'name' => $user['name'],
                                 'date' => '-',
                                 'slots' => 'Availability is not set for this user.',
                             ];
                             */

                            $data[$usertemp] = [
                                'name' => $user['name'],
                                'date' => '-',
                                'slots0' => 'Availability is not set for this user.',
                                'slots1' => '',
                                'slots2' => '',
                                'slots3' => '',
                                'slots4' => '',
                                'slots5' => '',
                                'slots6' => '',
                                'slots7' => '',
                                'slots8' => '',
                                /*'slots9' => '',
                                'slots10' => '',
                                'slots11' => '',
                                'slots12' => '',*/
                            ];
                            $usertemp = $usertemp + 1;
                        }
                    }
                }

                return respJson(200, '', [
                    'draw' => request('draw'),
                    'recordsTotal' => $count,
                    'recordsFiltered' => $count,
                    'data' => $data,
                ]);
            } else {
                return respJson(400, 'From and To Date is required.');
            }
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function slotIncreaseAndShift($slot, $tasks)
    {
        // IN_PROGRESS, PLANNED
        $checkDates = 0;

        $taskStatus = DeveloperTask::DEV_TASK_STATUS_FILTER;

        $taskIds = [];
        $userTasks = [];

        if ($tasks) {
            foreach ($taskStatus as $key => $value) {
                if ($key != 'DONE' && $key != 'USER_COMPLETE') {
                    if ($list = ($tasks[$key] ?? [])) {
                        foreach ($list as $k => $task) {
                            $SlotStart = Carbon::parse($slot['st']);
                            $SlotEnd = Carbon::parse($slot['en']);
                            $TaskStart = Carbon::parse($task['stDate']);
                            $TaskEnd = Carbon::parse($task['enDate']);

                            if (
                                ($TaskStart->gte($SlotStart) && $TaskStart->lte($SlotEnd)) ||
                                ($TaskEnd->gte($SlotStart) && $TaskEnd->lte($SlotEnd))
                            ) {
                                array_push($userTasks, $task);
                            } elseif ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd)) {
                                array_push($userTasks, $task);
                            }

                            if ($task['mt_start_date'] !== null && $task['mt_end_date'] !== null) {
                                $TaskStart = Carbon::parse($task['mt_start_date']);
                                $TaskEnd = Carbon::parse($task['mt_end_date']);

                                if (
                                    ($TaskStart->gte($SlotStart) && $TaskStart->lte($SlotEnd)) ||
                                    ($TaskEnd->gte($SlotStart) && $TaskEnd->lte($SlotEnd))
                                ) {
                                    array_push($userTasks, $task);
                                } elseif ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd)) {
                                    array_push($userTasks, $task);
                                }
                            } elseif ($task['mt_start_date'] !== null && $task['mt_end_date'] == null) {
                                $TaskStart = Carbon::parse($task['mt_start_date']);
                                $TaskEnd = Carbon::parse(Carbon::now());

                                if (
                                    ($TaskStart->gte($SlotStart) && $TaskStart->lte($SlotEnd)) ||
                                    ($TaskEnd->gte($SlotStart) && $TaskEnd->lte($SlotEnd))
                                ) {
                                    array_push($userTasks, $task);
                                } elseif ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd)) {
                                    array_push($userTasks, $task);
                                }
                            }
                        }
                        $list = array_values($list);
                        $tasks[$key] = $list;
                    }
                }
            }

            /*if ($list = ($tasks['PLANNED'] ?? [])) {
                foreach ($list as $k => $task) {
                    $SlotStart = Carbon::parse($slot['st']);
                    $SlotEnd = Carbon::parse($slot['en']);
                    $TaskStart = Carbon::parse($task['stDate']);
                    $TaskEnd = Carbon::parse($task['enDate']);

                    if (
                        ($TaskStart->gte($SlotStart) && $TaskStart->lte($SlotEnd)) ||
                        ($TaskEnd->gte($SlotStart) && $TaskEnd->lte($SlotEnd))
                    ) {
                        array_push($userTasks, $task);
                    } elseif ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd)) {
                        array_push($userTasks, $task);
                    }
                }
                $list = array_values($list);
                $tasks['PLANNED'] = $list;
            }*/
        }

        // print_r($userTasks);
        return [
            'taskIds' => $taskIds ?? [],
            'userTasks' => $userTasks ?? [],
        ];
    }

    public function typeWiseTasks($type, $wh, $task_status_value, $task_type_filter)
    {
        $userIds = $wh['userIds'] ?? [0];
        $taskStatuses = [];
        $devTaskStatuses = [];

        /*if ($type == 'IN_PROGRESS') {
            $taskStatuses = [
                Task::TASK_STATUS_IN_PROGRESS,
            ];
            $devTaskStatuses = [
                DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS,
            ];
        } elseif ($type == 'PLANNED') {
            $taskStatuses = [
                Task::TASK_STATUS_PLANNED,
            ];
            $devTaskStatuses = [
                DeveloperTask::DEV_TASK_STATUS_PLANNED,
            ];
        }*/

        if (! empty($task_status_value)) {
            $task_status_value_array = array_filter($task_status_value, fn ($ip_ids) => ! is_null($task_status_value));
            if (! in_array(null, $task_status_value_array)) {
                foreach ($task_status_value as $key => $value) {
                    $varTask_status = strtoupper(str_replace(' ', '_', $value));
                    $vardevTask_status = strtoupper(str_replace(' ', '_', $value));

                    $taskStatuses[] = Task::TASK_STATUS_FILTER[$varTask_status];
                    $taskStatuses[] = DeveloperTask::DEV_TASK_STATUS_FILTER[$vardevTask_status];
                }
            }
        }

        // start_date IS NOT NULL AND approximate > 0
        // start_date IS NOT NULL AND estimate_minutes > 0

        $sql = "SELECT
            listdata.*
            FROM (
            (
                SELECT 
                    id, 
                    'T' AS type, 
                    assign_to AS assigned_to, 
                    manually_assign, 
                    approximate as estimate_minutes,
                    slotTaskRemarks, 
                    task_subject AS title, 
                    start_date AS st_date, 
                    due_date AS en_date,
                    m_start_date AS mt_start_date, 
                    m_end_date AS mt_end_date, 
                    COALESCE(approximate, 0) AS est_minutes, 
                    status,
                    (
                        CASE
                            WHEN status = '".Task::TASK_STATUS_IN_PROGRESS."' THEN 'IN_PROGRESS'
                            WHEN status = '".Task::TASK_STATUS_PLANNED."' THEN 'PLANNED'
                            WHEN status = '".Task::TASK_STATUS_DISCUSSING."' THEN 'DISCUSSING'
                            WHEN status = '".Task::TASK_STATUS_ISSUE."' THEN 'ISSUE'
                            WHEN status = '".Task::TASK_STATUS_DISCUSS_WITH_LEAD."' THEN 'DISCUSS_WITH_LEAD'
                            WHEN status = '".Task::TASK_STATUS_NOTE."' THEN 'NOTE'
                            WHEN status = '".Task::TASK_STATUS_LEAD_RESPONSE_NEEDED."' THEN 'LEAD_RESPONSE_NEEDED'
                            WHEN status = '".Task::TASK_STATUS_ERRORS_IN_TASK."' THEN 'ERRORS_IN_TASK'
                            WHEN status = '".Task::TASK_STATUS_IN_REVIEW."' THEN 'IN_REVIEW'
                            WHEN status = '".Task::TASK_STATUS_PRIORITY."' THEN 'PRIORITY'
                            WHEN status = '".Task::TASK_STATUS_HIGH_PRIORITY."' THEN 'HIGH_PRIORITY'
                            WHEN status = '".Task::TASK_STATUS_REVIEW_ESTIMATED_TIME."' THEN 'REVIEW_ESTIMATED_TIME'
                            WHEN status = '".Task::TASK_STATUS_USER_ESTIMATED."' THEN 'USER_ESTIMATED'
                            WHEN status = '".Task::TASK_STATUS_DECLINE."' THEN 'DECLINE'
                            WHEN status = '".Task::TASK_STATUS_REOPEN."' THEN 'REOPEN'
                            WHEN status = '".Task::TASK_STATUS_APPROVED."' THEN 'APPROVED'
                        END
                    ) AS status2
                FROM 
                    tasks 
                WHERE 
                1
                AND deleted_at IS NULL
                AND (due_date IS NOT NULL OR m_start_date IS NOT NULL)
                AND assign_to IN (".implode(',', $userIds).')';

        if (! empty($taskStatuses)) {
            $sql .= "AND status IN ('".implode("','", $taskStatuses)."') ";
        }

        if (! empty($task_type_filter)) {
            if ($task_type_filter == 2) {
                $sql .= 'AND manually_assign IS NULL';
            } else {
                $sql .= 'AND manually_assign = 1';
            }
        }
        $sql .= ")
            UNION
            (
                SELECT 
                    id, 
                    'DT' AS type, 
                    assigned_to AS assigned_to, 
                    manually_assign, 
                    estimate_minutes,
                    slotTaskRemarks, 
                    subject AS title, 
                    start_date AS st_date, 
                    estimate_date AS en_date, 
                    m_start_date AS mt_start_date, 
                    m_end_date AS mt_end_date, 
                    COALESCE(estimate_minutes, 0) AS est_minutes, 
                    status,
                    (
                        CASE
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS."' THEN 'IN_PROGRESS'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_PLANNED."' THEN 'PLANNED'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_DISCUSSING."' THEN 'DISCUSSING'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_DISCUSS_WITH_LEAD."' THEN 'DISCUSS_WITH_LEAD'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_NOTE."' THEN 'NOTE'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_LEAD_RESPONSE_NEEDED."' THEN 'LEAD_RESPONSE_NEEDED'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_ERRORS_IN_TASK."' THEN 'ERRORS_IN_TASK'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_IN_REVIEW."' THEN 'IN_REVIEW'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_PRIORITY."' THEN 'PRIORITY'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_HIGH_PRIORITY."' THEN 'HIGH_PRIORITY'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_REVIEW_ESTIMATED_TIME."' THEN 'REVIEW_ESTIMATED_TIME'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_USER_ESTIMATED."' THEN 'USER_ESTIMATED'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_DECLINE."' THEN 'DECLINE'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_REOPEN."' THEN 'REOPEN'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_APPROVED."' THEN 'APPROVED'
                        END
                    ) AS status2
                FROM developer_tasks
                WHERE 1
                AND deleted_at IS NULL
                AND (estimate_date IS NOT NULL OR m_start_date IS NOT NULL)
                AND assigned_to IN (".implode(',', $userIds).')';

        if (! empty($devTaskStatuses)) {
            $sql .= "AND status IN ('".implode("','", $devTaskStatuses)."') ";
        }

        if (! empty($task_type_filter)) {
            if ($task_type_filter == 2) {
                $sql .= 'AND manually_assign IS NULL';
            } else {
                $sql .= 'AND manually_assign = 1';
            }
        }
        $sql .= ')
        ) AS listdata
        ORDER BY listdata.st_date ASC';

        //return $sql;

        $tasks = DB::select($sql, []);

        return $tasks;
    }

    public function plannedUserAndAvailability()
    {
        try {
            $q = User::query();
            $q->leftJoin('user_avaibilities AS ua', function ($join) {
                $join->on('ua.user_id', '=', 'users.id')
                    ->where('ua.is_latest', '=', 1);
            });
            $q->where('is_task_planned', 1);
            if (! isAdmin()) {
                $q->where('id', loginId());
            }
            $q->orderBy('name');
            $q->select([
                'users.*',
                'ua.from',
                'ua.to',
                'ua.start_time',
                'ua.end_time',
                'ua.date',
                'ua.created_at AS latest_updated',
            ]);
            $list = $q->get();

            $html = [];
            $html[] = '<table class="table table-bordered">';
            $html[] = '<thead>
                    <tr>
                        <th width="20%">Username</th>
                        <th width="15%" style="word-break: break-all;">From/To Date</th>
                        <th width="10%" style="word-break: break-all;">Start/End Time</th>
                        <th width="30%" style="word-break: break-all;">Available Days</th>
                        <th width="10%" style="word-break: break-all;">Lunch Time</th>
                        <th width="15%">Created at</th>
                    </tr>
                </thead>';
            if ($list->count()) {
                foreach ($list as $single) {
                    $html[] = '<tr>
                            <td>'.$single->name.'</td>
                            <td>'.$single->from.' - '.$single->to.'</td>
                            <td>'.$single->start_time.' - '.$single->end_time.'</td>
                            <td>'.(str_replace(',', ', ', $single->date) ?: '-').'</td>
                            <td>'.($single->lunch_time ?: '-').'</td>
                            <td>'.($single->latest_updated ?: '-').'</td>
                        </tr>';
                }
            } else {
                $html[] = '<tr>
                        <td colspan="5">No records found.</td>
                    </tr>';
            }
            $html[] = '</table>';

            return respJson(200, '', [
                'data' => implode('', $html),
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function userAvailabilitiesEdit(Request $request)
    {
        try {
            $avaibility = UserAvaibility::where('id', '=', $request->id)->first();

            return response()->json(['code' => 200, 'data' => $avaibility, 'message' => 'Edited successfully!!!']);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function userAvaibilityHistoryLog()
    {
        $q = UserAvaibilityHistory::query();
        $q->leftJoin('users', 'user_id', 'users.id');
        $q->select('user_avaibility_histories.*', 'users.name AS username');
        $q->where('user_avaibility_id', request('id'));
        $list = $q->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">ID</th>
                <th width="5%" style="word-break: break-all;">User</th>
                <th width="15%" style="word-break: break-all;">From/To Date</th>
                <th width="13%" style="word-break: break-all;">Start/End Time</th>
                <th width="25%" style="word-break: break-all;">Available Days</th>
                <th width="13%" style="word-break: break-all;">Lunch Time</th>
                <th width="13%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $lunch_time = ($single->lunch_time_from && $single->lunch_time_to) ? $single->lunch_time_from.' - '.$single->lunch_time_to : '-';
                $html[] = '<tr>
                    <td>'.$single->id.'</td>
                    <td>'.$single->username.'</td>
                    <td>'.$single->from.' - '.$single->to.'</td>
                    <td>'.$single->start_time.' - '.$single->end_time.'</td>
                    <td>'.(str_replace(',', ', ', $single->date) ?: '-').'</td>
                    <td>'.$lunch_time.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return implode('', $html);
    }

    public function deleteFeedbackCategory(Request $request): JsonResponse
    {
        try {
            $userFCH = UserFeedbackCategorySopHistory::where('category_id', $request->id);
            $getUserFCH = $userFCH->get();
            if (! empty($getUserFCH)) {
                foreach ($getUserFCH as $key => $val) {
                    $userFCHC = UserFeedbackCategorySopHistoryComment::where('sop_history_id', $val->id)->delete();
                }
                $userFCH->delete();
            }
            //DeveloperTask::where('user_feedback_cat_id', $request->id)->delete();
            //Task::where('user_feedback_cat_id', $request->id)->delete();
            UserFeedbackCategory::where('id', $request->id)->delete();

            return response()->json(['code' => '200', 'data' => [], 'message' => 'Data deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => '500',  'message' => $e->getMessage()]);
        }
    }

    public function whitelistBulkUpdate(Request $request)
    {
        //remove all users from whitelist
        if ($request->action == 2) {
            User::where('is_whitelisted', 1)->update(['is_whitelisted' => 0]);
        }
        //add or remove selected users from whitelist
        else {
            $whitelistValue = $request->action == 1 ? 1 : 0;
            User::whereIn('id', $request->users)->update(['is_whitelisted' => $whitelistValue]);
        }
    }

    public function userPemfileHistoryLogs(Request $request): JsonResponse
    {
        $historyLogs = UserPemfileHistoryLog::where('user_pemfile_history_id', $request->pemfileHistoryId)->latest()->get();

        return response()->json(['code' => 200, 'data' => $historyLogs]);
    }

    public function userAccessListing(Request $request): View
    {
        $userAccessLists = new UserPemfileHistory;
        $userAccessLists = $userAccessLists->withTrashed();

        if ($request->user_ids) {
            $userAccessLists = $userAccessLists->whereIn('user_id', $request->user_ids);
        }
        if ($request->s_ids) {
            $userAccessLists = $userAccessLists->WhereIn('server_name', $request->s_ids);
        }
        if ($request->search_event) {
            $userAccessLists = $userAccessLists->where('action', 'LIKE', '%'.$request->search_event.'%');
        }
        if ($request->date) {
            $userAccessLists = $userAccessLists->where('created_at', 'LIKE', '%'.$request->date.'%');
        }
        if ($request->search_username) {
            $userAccessLists = $userAccessLists->where('username', 'LIKE', '%'.$request->search_username.'%');
        }

        $userAccessLists = $userAccessLists->with('user')->latest()->paginate(25);

        return view('user-management.user-access-list', compact('userAccessLists'));
    }

    public function databaseLogs(Request $request): View
    {
        $title = 'Database Logs | User management';

        $UserDatabaseLog = UserDatabaseLog::orderByDesc('id');

        $UserDatabaseLog = $UserDatabaseLog->paginate(25);

        return view('user-management.index-database-logs', compact('title', 'UserDatabaseLog'))->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function userSchedulesReport(Request $request): View
    {
        $months = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];

        $currentYear = Carbon::now()->year;

        $years = range($currentYear - 5, $currentYear + 5);

        $dataMainArray = [];

        //return $request;

        if (! empty($request->user_id) && ! empty($request->month) && ! empty($request->year)) {
            $desiredMonth = $request->month; // September (example)
            $desiredYear = $request->year; // 2023 (example)

            // Create a Carbon instance for the specified month and year
            $startDate = Carbon::createFromDate($desiredYear, $desiredMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Loop through each day of the specified month

            $iii = 0;
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $dates = $date->format('Y-m-d'); // Add each date to the array

                $dataArray = [];

                $records = DeveloperTask::select('id', 'estimate_minutes', 'start_date', 'estimate_date')->whereDate('start_date', '=', $dates)->orWhereDate('m_start_date', '=', $dates)->where('assigned_to', Auth::user()->id)->get();

                if (count($records) > 0) {
                    foreach ($records as $key => $value) {
                        $records[$key]['task_type'] = 'DT';
                        $records[$key]['totalTime'] = $value->estimate_minutes;

                        if ($value->start_date !== null && $value->estimate_date !== null) {
                            $startDate = Carbon::parse($value->start_date);
                            $endDate = Carbon::parse($value->estimate_date);

                            $records[$key]['totalMinutes'] = $endDate->diffInMinutes($startDate);
                        }

                        $dataArray[$iii] = $records[$key];
                        $iii++;
                    }
                }

                $recordsTask = Task::select('id', 'approximate', 'start_date', 'due_date')->whereDate('start_date', '=', $dates)->orWhereDate('m_start_date', '=', $dates)->where('assign_to', Auth::user()->id)->get();

                if (count($recordsTask) > 0) {
                    foreach ($recordsTask as $keyTask => $valueTask) {
                        $recordsTask[$keyTask]['task_type'] = 'T';
                        $recordsTask[$keyTask]['totalTime'] = $valueTask->approximate;

                        if ($valueTask->start_date !== null && $valueTask->due_date !== null) {
                            $startDateTask = Carbon::parse($valueTask->start_date);
                            $endDateTask = Carbon::parse($valueTask->due_date);

                            $recordsTask[$keyTask]['totalMinutes'] = $endDateTask->diffInMinutes($startDateTask);
                        }

                        $dataArray[$iii] = $recordsTask[$keyTask];
                        $iii++;
                    }
                }

                if (! empty($dataArray)) {
                    $dataMainArray[$dates] = $dataArray;
                }
            }
        }

        return view('usermanagement::user-schedules.report', compact('months', 'years', 'dataMainArray'));
    }
}
