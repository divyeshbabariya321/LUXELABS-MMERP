<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\DeveloperComment;
use App\DeveloperCost;
use App\DeveloperLanguage;
use App\DeveloperModule;
use App\DeveloperTask;
use App\DeveloperTaskComment;
use App\DeveloperTaskDocument;
use App\DeveloperTaskHistory;
use App\DeveoperTaskPullRequestMerge;
use App\ErpPriority;
use App\Exports\DeveloperTaskExcelExport;
use App\Github\GithubOrganization;
use App\Github\GithubRepository;
use App\GoogleScreencast;
use App\Helpers;
use App\Helpers\HubstaffTrait;
use App\Helpers\MessageHelper;
use App\Http\Requests\CommentStoreDevelopmentRequest;
use App\Http\Requests\CostStoreDevelopmentRequest;
use App\Http\Requests\IssueAssignDevelopmentRequest;
use App\Http\Requests\ModuleAssignDevelopmentRequest;
use App\Http\Requests\ModuleStoreDevelopmentRequest;
use App\Http\Requests\ScrapperMonitoringCreateRequest;
use App\Http\Requests\StatusStoreDevelopmentRequest;
use App\Http\Requests\StoreDevelopmentRequest;
use App\Http\Requests\TaskCommentDevelopmentRequest;
use App\Http\Requests\UpdateDevelopmentRequest;
use App\Http\Requests\UpdateScrapperDevelopmentRequest;
use App\Http\Requests\UpdateScrapperRemarksDevelopmentRequest;
use App\Http\Requests\UploadFileDevelopmentRequest;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffProject;
use App\Hubstaff\HubstaffTask;
use App\HubstaffHistory;
use App\Issue;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\LogChatMessage;
use App\MeetingAndOtherTime;
use App\Models\DataTableColumn;
use App\Models\DeveloperTasks\DeveloperTasksHistoryApprovals;
use App\Models\DeveloperTaskStartEndHistory;
use App\Models\DeveloperTaskStatusChecklist;
use App\Models\DeveloperTaskStatusChecklistRemarks;
use App\Models\ScrapperLogs;
use App\Models\ScrapperMonitoring;
use App\Models\ScrapperValues;
use App\Models\ScrapperValuesHistory;
use App\Models\ScrapperValuesRemarksHistory;
use App\PaymentReceipt;
use App\PushNotification;
use App\ReplyCategory;
use App\Setting;
use App\Task;
use App\TaskAttachment;
use App\TaskMessage;
use App\TasksHistory;
use App\TaskStatus;
use App\TaskTypes;
use App\TaskUserHistory;
use App\Team;
use App\TeamUser;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorProject;
use App\TimeDoctor\TimeDoctorTask;
use App\User;
use App\UserAvaibility;
use App\UserRate;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use HubstaffTrait;

    private $githubClient;

    public function __construct()
    {
        $this->githubClient = new Client([
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClientObj = new Client([
            'auth' => [$userName, $token],
        ]);

        return $githubClientObj;
    }

    public function taskListByUserId(Request $request): JsonResponse
    {
        $user_id = $request->get('user_id', 0);
        $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
            ->leftJoin('erp_priorities', function ($query) use ($user_id) {
                $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                $query->where('erp_priorities.user_id', $user_id);
            })
            ->where('status', '!=', 'Done');
        // if admin the can assign new task
        if (auth()->user()->isAdmin()) {
            $issues = $issues->whereIn('developer_tasks.id', $request->get('selected_issue', []));
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }
        $issues = $issues->orderBy('erp_priorities.id')->get();
        foreach ($issues as &$value) {
            $value->module = $value->developerModule->name;
            $value->created_by = User::where('id', $value->created_by)->value('name');
        }
        unset($value);

        return response()->json($issues);
    }

    public function setTaskPriority(Request $request): JsonResponse
    {
        $priority = $request->get('priority', null);
        $user_id = $request->get('user_id', 0);
        //delete old priority
        ErpPriority::where('user_id', $user_id)->where('model_type', '=', DeveloperTask::class)->delete();

        if (! empty($priority)) {
            foreach ((array) $priority as $model_id) {
                ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class,
                    'user_id' => $user_id,
                ]);
            }
            $developerTask = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                ->join('erp_priorities', function ($query) use ($user_id) {
                    $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                    $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                    $query->where('erp_priorities.user_id', '=', $user_id);
                })
                ->where('is_resolved', '0')
                ->orderBy('erp_priorities.id')
                ->get();
            $message = '';
            $i = 1;
            foreach ($developerTask as $value) {
                $message .= $i.' : #Task-'.$value->id.'-'.$value->subject."\n";
                $i++;
            }
            if (! empty($message)) {
                $requestData = new Request;
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = '';
                if (! empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes')."\n";
                }
                $string .= "Task Priority is : \n".$message;

                $params['message'] = $string;
                $params['status'] = 2;
                $requestData->request->add($params);
                app(WhatsAppController::class)->sendMessage($requestData, 'priority');
            }
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function index(Request $request): \Illuminate\View\View
    {
        // Set required data
        $user = $request->user ?? Auth::id();
        $start = $request->range_start ? "$request->range_start 00:00" : '2018-01-01 00:00';
        $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->endOfWeek();
        // Set initial variables
        $progressTasks = new DeveloperTask;
        $plannedTasks = new DeveloperTask;
        $completedTasks = new DeveloperTask;
        // For non-admins get tasks assigned to the user
        if (! Auth::user()->hasRole('Admin')) {
            $progressTasks = DeveloperTask::where('user_id', Auth::id());
            $plannedTasks = DeveloperTask::where('user_id', Auth::id());
            $completedTasks = DeveloperTask::where('user_id', Auth::id());
        }
        // Get tasks for specific user if you are admin
        if (Auth::user()->hasRole('Admin') && (int) $request->user > 0) {
            $progressTasks = DeveloperTask::where('user_id', $user);
            $plannedTasks = DeveloperTask::where('user_id', $user);
            $completedTasks = DeveloperTask::where('user_id', $user);
        }
        // Filter by date/
        if ($request->get('range_start') != '') {
            $progressTasks = $progressTasks->whereBetween('created_at', [$start, $end]);
            $plannedTasks = $plannedTasks->whereBetween('created_at', [$start, $end]);
            $completedTasks = $completedTasks->whereBetween('created_at', [$start, $end]);
        }
        // Filter by ID
        if ($request->get('id')) {
            $progressTasks = $progressTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $plannedTasks = $plannedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $completedTasks = $completedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
        }
        // Get all data with user and messages
        $plannedTasks = $plannedTasks->where('status', 'Planned')->orderBy('created_at')->with(['user', 'messages', 'timeSpent'])->get();
        $completedTasks = $completedTasks->where('status', 'Done')->orderBy('created_at')->with(['user', 'messages', 'timeSpent'])->get();
        $progressTasks = $progressTasks->where('status', 'In Progress')->orderBy('created_at')->with(['user', 'messages', 'timeSpent'])->get();

        // Get all modules
        $modules = DeveloperModule::all();
        // Get all developers
        $users = Helpers::getUserArray(User::role('Developer')->get());
        // Get all task types
        $tasksTypes = TaskTypes::all();
        // Create empty array for module names
        $moduleNames = [];
        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[$module->id] = $module->name;
        }
        $times = [];

        $mediaTags = config('constants.media_tags'); // Use config variable

        return view('development.index', [
            'times' => $times,
            'users' => $users,
            'modules' => $modules,
            'user' => $user,
            'start' => $start,
            'end' => $end,
            'moduleNames' => $moduleNames,
            'completedTasks' => $completedTasks,
            'plannedTasks' => $plannedTasks,
            'progressTasks' => $progressTasks,
            'tasksTypes' => $tasksTypes,
            'title' => 'Dev',
            'mediaTags' => $mediaTags,
        ]);
    }

    public function moveTaskToProgress(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $date = $request->get('date');
        $task->status = 'In Progress';
        $hour = $request->get('hour') ?? '00';
        $minutes = $request->get('mimutes') ?? '00';
        $task->estimate_time = $date.' '."$hour:$minutes:00 ";
        $task->start_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function completeTask(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Done';
        $task->end_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function relistTask(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Planned';
        $task->end_time = null;
        $task->start_time = null;
        $task->estimate_time = null;
        $task->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateAssignee(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->get('task_id'));

        $old_assignee = $task->user_id;
        $task->user_id = $request->get('user_id');
        $task->save();
        $task_history = new TasksHistory;
        $task_history->date_time = date('Y-m-d H:i:s');
        $task_history->task_id = $request->get('task_id');
        $task_history->user_id = Auth::id();
        $task_history->old_assignee = $old_assignee;
        $task_history->new_assignee = $request->get('user_id');
        $task_history->save();

        return response()->json([
            'success',
        ]);
    }

    public function issueTaskIndex(Request $request)
    {
        $type = $request->tasktype ? $request->tasktype : 'all';
        $users = User::orderBy('name')->pluck('name', 'id');
        $usersForExport = [];

        if (Auth::user()->hasRole('Admin')) {
            $usersForExport = User::select('name', 'id')->get();
        } else {
            $usersForExport = User::select('name', 'id')->where('id', '=', Auth::user()->id)->get();
        }
        $auth_user = auth()->user();
        $title = 'Task List';

        $issues = DeveloperTask::with([
            'timeSpent',
            'developerTaskHistory',
            'assignedUser',
            'masterUser',
            'timeSpent',
            'leadtimeSpent',
            'testertimeSpent',
            'messages.taskUser',
            'messages.user',
            'dthWithMinuteEstimate',
            'tester',
        ]);

        $issues->when($type == 'issue', fn ($q) => $q->where('task_type_id', '3'));
        $issues->when(! empty($request->estimate_date), function (Builder $query) use ($request) {
            $estimate_date = date('Y-m-d', strtotime($request->estimate_date));

            return $query->where('estimate_date', $estimate_date);
        });

        $issues->when($type == 'devtask', fn ($q) => $q->where('task_type_id', '1'));
        $issues->when((int) $request->get('submitted_by') > 0, fn (Builder $query) => $query->where('developer_tasks.created_by', $request->get('submitted_by')));
        $issues->when((int) $request->get('responsible_user') > 0, fn (Builder $query) => $query->where('developer_tasks.responsible_user_id', $request->get('responsible_user')));
        $issues->when((int) $request->get('corrected_by') > 0, fn (Builder $query) => $query->where('developer_tasks.user_id', $request->get('corrected_by')));
        $issues->when((int) $request->get('assigned_to') > 0, fn (Builder $query) => $query->where('developer_tasks.assigned_to', $request->get('assigned_to')));
        $issues->when((int) $request->get('master_user_id') > 0, fn (Builder $query) => $query->where('developer_tasks.master_user_id', $request->get('master_user_id')));
        $issues->when((int) $request->get('team_lead_id') > 0, fn (Builder $query) => $query->where('developer_tasks.team_lead_id', $request->get('team_lead_id')));
        $issues->when((int) $request->get('tester_id') > 0, fn ($q) => $q->where('developer_tasks.tester_id', $request->get('tester_id')));
        $issues->when($request->get('module'), fn ($q) => $q->where('module_id', $request->get('module')));
        $issues->when(! empty($request->get('task_status', [])), fn ($q) => $q->whereIn('developer_tasks.status', $request->get('task_status')));
        $issues->when(! empty($request->get('repo_id')), fn ($q) => $q->where('developer_tasks.repository_id', $request->get('repo_id')));

        if (isset($request->is_estimated)) {
            if ($request->get('is_estimated') == 'null') {
                $issues = $issues->notEstimated();
            }
            if ($request->get('is_estimated') == 'not_approved') {
                $issues = $issues->adminNotApproved();
            }
        }

        $whereCondition = '';
        if ($request->get('subject') != '') {
            $whereCondition = ' and message like  "%'.$request->get('subject').'%"';
            $issues = $issues->where(function (Builder $query) use ($request) {
                $query->whereLike(['developer_tasks.id', 'developer_tasks.subject', 'developer_tasks.task', 'chat_messages.message'], $request->get('subject'));
            });
        }
        $issues = $issues->leftJoin(
            DB::raw('(SELECT MAX(id) as  max_id, issue_id, message
            FROM `chat_messages` where issue_id > 0
             '.$whereCondition.' GROUP BY issue_id )
             m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');

        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');

        $issues->when($request->get('last_communicated', 'off') == 'on', fn ($q) => $q->orderByDesc('chat_messages.id'));

        $issues = $issues->select(
            'developer_tasks.id',
            'developer_tasks.user_id',
            'developer_tasks.subject',
            'developer_tasks.task',
            'developer_tasks.status',
            'developer_tasks.assigned_to',
            'developer_tasks.created_by',
            'developer_tasks.master_user_id',
            'developer_tasks.responsible_user_id',
            'developer_tasks.team_lead_id',
            'developer_tasks.tester_id',
            'developer_tasks.repository_id',
            'developer_tasks.created_at',
            'chat_messages.message',
            'chat_messages.is_audio',
            'chat_messages.user_id AS message_user_id',
            'chat_messages.is_reminder AS message_is_reminder',
            'chat_messages.status as message_status',
            'chat_messages.sent_to_user_id'
        );

        // Set variables with modules and users
        $modules = Cache::remember('DeveloperModule::orderBy::name', 60 * 60 * 24 * 1, function () {
            return DeveloperModule::orderBy('name')->get();
        });

        $statusList = Cache::remember('task_status_select_name', 60 * 60 * 24 * 7, function () {
            return TaskStatus::select('name')->pluck('name', 'name')->toArray();
        });

        if (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) use ($auth_user) {
                $query->where('developer_tasks.assigned_to', $auth_user->id)
                    ->orWhere('developer_tasks.master_user_id', $auth_user->id)
                    ->orWhere('developer_tasks.tester_id', $auth_user->id)
                    ->orWhere('developer_tasks.team_lead_id', $auth_user->id);
            });
        }

        $plannedTasks = DeveloperTask::where('developer_tasks.status', 'Planned')
            ->groupBy('developer_tasks.assigned_to')
            ->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])
            ->pluck('total_product', 'assigned_to')->toArray();

        $inProgressTasks = DeveloperTask::where('developer_tasks.status', 'In Progress')
            ->groupBy('developer_tasks.assigned_to')
            ->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])
            ->pluck('total_product', 'assigned_to')->toArray();

        $usersCount = array_values(array_filter(array_keys($plannedTasks)));
        $userModel = empty($usersCount) ? [] : $users->whereIn('id', $usersCount)->pluck('name', 'id')->toArray();
        $countPlanned = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countPlanned[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // category filter start count
        $countInProgress = [];
        $usersCount = array_values(array_filter(array_keys($inProgressTasks)));
        $userModel = empty($usersCount) ? [] : $users->whereIn('id', $usersCount)->pluck('name', 'id')->toArray();
        if (! empty($inProgressTasks) && ! empty($userModel)) {
            foreach ($inProgressTasks as $key => $count) {
                $countInProgress[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderByDesc('developer_tasks.created_at');
        } elseif ($request->order == 'latest_task_first') {
            $issues = $issues->orderByDesc('developer_tasks.id');
        } else {
            $issues = $issues->orderByDesc('chat_messages.id');
        }

        if ($request->download == 2) {
            $issues = $issues->get();
            $tasks_csv = [];
            foreach ($issues as $value) {
                $task_csv = [];
                $task_csv['ID'] = $value->id;
                $task_csv['Subject'] = $value->subject;
                $task_csv['Communication'] = $value->message;
                $task_csv['Developer'] = ($value->assignedUser) ? $value->assignedUser->name : 'Unassigned';
                $tasks_csv[] = $task_csv;
            }
            $this->outputCsv('downaload-task-summaries.csv', $tasks_csv);
        } else {
            $issues = $issues->paginate(50);
        }

        $priority = ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        $respositories = Cache::remember('GithubRepository::all()', 60 * 60 * 24 * 7, function () {
            return GithubRepository::all();
        });

        $checkList = [];
        $checkList = Cache::remember('DeveloperTaskStatusChecklist::all()', 60 * 60 * 24 * 7, function () {
            $checkListArray = DeveloperTaskStatusChecklist::select('id', 'name', 'task_status')->get()->toArray();
            foreach ($checkListArray as $list) {
                $checkList[$list['task_status']][] = $list;
            }
        });
        $datatableModel = DataTableColumn::select('column_name', 'user_id', 'section_name')->where('user_id', auth()->user()->id)->where('section_name', 'development-list')->first();

        $dynamicColumnsToShowDl = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowDl = json_decode($hideColumns, true);
        }
        $isReviwerLikeAdmin = auth()->user()->isReviwerLikeAdmin();
        $userID = Auth::user()->id;
        foreach ($issues as $key => $issue) {
            if ($isReviwerLikeAdmin) {
                $issue->view = 'development.partials.admin-row-view';
            } elseif (in_array($userID, [$issue->created_by, $issue->master_user_id, $issue->assigned_to])) {
                $issue->view = 'development.partials.developer-row-view';
                $issue->isTimeShow = false;
                $issue->developerTime = null;
                if (in_array($userID, [$issue->assigned_to, $issue->master_user_id, $issue->tester_id])) {
                    $issue->isTimeShow = true;
                    $issue->developerTime = MeetingAndOtherTime::where('model', DeveloperTask::class)
                        ->where('model_id', $issue->id)
                        ->where('user_id', $userID)
                        ->where('approve', 1)
                        ->sum('time');
                }
                $issue->time_history = DeveloperTaskHistory::where('developer_task_id', $issue->id)->where('attribute', 'estimation_minute')->where('model', DeveloperTask::class)->first();
            }
        }
        if (request()->ajax()) {
            return view('development.partials.load-more', compact('issues', 'users', 'modules', 'request', 'title', 'type', 'countPlanned', 'countInProgress', 'statusList', 'priority', 'dynamicColumnsToShowDl'));
        }

        $reply_categories = ReplyCategory::select('id', 'name')
            ->with('approval_leads', 'sub_categories')
            ->where('parent_id', 0)
            ->where('id', 44)
            ->orderBy('name')->get();

        return view('development.issue', [
            'issues' => $issues,
            'users' => $users,
            'checkList' => $checkList,
            'modules' => $modules,
            'request' => $request,
            'title' => $title,
            'type' => $type,
            'priority' => $priority,
            'countPlanned' => $countPlanned,
            'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            'respositories' => $respositories,
            'dynamicColumnsToShowDl' => $dynamicColumnsToShowDl,
            'reply_categories' => $reply_categories,
            'usersForExport' => $usersForExport,
        ]);
    }

    public function dlColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'development-list')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'development-list';
            $column->column_name = json_encode($request->column_dl);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'development-list';
            $column->column_name = json_encode($request->column_dl);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function dsColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'development-summarylist')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'development-summarylist';
            $column->column_name = json_encode($request->column_ds);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'development-summarylist';
            $column->column_name = json_encode($request->column_ds);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function scrappingTaskIndex(Request $request): \Illuminate\View\View
    {
        $inputs = $request->input();
        $users = User::query();

        $issues = DeveloperTask::with('assignedUser');
        $issues = $issues->where('developer_tasks.task_type_id', '1')->whereNotNull('scraper_id')->where('scraper_id', '<>', 0);

        $issues = $issues->select('developer_tasks.*');

        if (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id)
                    ->orWhere('developer_tasks.tester_id', auth()->user()->id)
                    ->orWhere('developer_tasks.team_lead_id', auth()->user()->id);
            });
        }

        if (@$inputs['module']) {
            $issues->where('module_id', $inputs['module']);
        }

        if (@$inputs['subject']) {
            $issues->where('subject', 'like', '%'.$inputs['subject'].'%');
        }

        if (@$inputs['task']) {
            $issues->where('task', 'like', '%'.$inputs['task'].'%');
        }

        if (@$inputs['user_id']) {
            $issues->where('assigned_to', $inputs['user_id']);
            $users = User::where('id', $request->user_id)->select(['id', 'name'])->first();
        }

        if (@$inputs['status']) {
            $issues->where('status', $inputs['status']);
        }

        $issues = $issues->orderByDesc('id')->groupBy('developer_tasks.id');
        $issues = $issues->paginate(50);

        $modules = DeveloperModule::all()->pluck('name', 'id');

        return view('development.scrapper', [
            'issues' => $issues,
            'modules' => $modules,
            'inputs' => $inputs,
            'title' => 'Scrapping Issues List',
            'users' => $users,

        ]);
    }

    public function loadAllTasks(Request $request)
    {
        $dataTaskType = $request->dataTaskType;
        switch ($dataTaskType) {
            case 'devtask':
                return $this->loadAllDevTasks($request);
                break;
            case 'task':
                return $this->loadAllNormalTasks($request);
                break;
        }
    }

    private function loadAllDevTasks($request)
    {
        $issuesQuery = DeveloperTask::with(['assignedUser'])
            ->with(['taskStartEndHistories' => function ($query) {
                $query->select('task_id',
                    DB::raw('SUM(TIMESTAMPDIFF(MINUTE, start_date, end_date)) as tracked_time'),
                    DB::raw('MIN(start_date) as first_start_date'),
                    DB::raw('MAX(end_date) as last_end_date'),
                )
                    ->orderByDesc('created_at')
                    ->groupBy('task_id');
            }])
            ->with(['developerTaskHistories' => function ($query) {
                $query->select('developer_task_id', DB::raw('SUM(new_value) as approved_time'))
                    ->where('is_approved', 1)
                    ->orderByDesc('created_at')
                    ->groupBy('developer_task_id');
            }])
            ->orderByDesc('id');

        if ($request->startDate && $request->endDate) {
            $issuesQuery = $issuesQuery->whereBetween('developer_tasks.created_at', [$request->startDate, $request->endDate]);
        }

        if ($request->startDateTracked && $request->endDateTracked) {
            $issuesQuery = $issuesQuery->whereBetween('developer_tasks.m_start_date', [$request->startDateTracked, $request->endDateTracked]);
        }

        if ($request->assigned_to) {
            $issuesQuery = $issuesQuery->where('developer_tasks.assigned_to', $request->assigned_to);
        }

        return $issuesQuery;
    }

    private function loadAllNormalTasks($request)
    {
        $issuesQuery = Task::with(['assignedTo', 'taskStatusAlter'])
            ->with(['taskStartEndHistories' => function ($query) {
                $query->select('task_id',
                    DB::raw('SUM(TIMESTAMPDIFF(MINUTE, start_date, end_date)) as tracked_time'),
                    DB::raw('MIN(start_date) as first_start_date'),
                    DB::raw('MAX(end_date) as last_end_date'),
                )
                    ->orderByDesc('created_at')
                    ->groupBy('task_id');
            }])
            ->with(['developerTaskHistories' => function ($query) {
                $query->select('developer_task_id', DB::raw('SUM(new_value) as approved_time'))
                    ->where('is_approved', 1)
                    ->orderByDesc('created_at')
                    ->groupBy('developer_task_id');
            }])
            ->orderByDesc('id');

        if ($request->startDate && $request->endDate) {
            $issuesQuery = $issuesQuery->whereBetween('tasks.created_at', [$request->startDate, $request->endDate]);
        }

        if ($request->startDateTracked && $request->endDateTracked) {
            $issuesQuery = $issuesQuery->whereBetween('tasks.m_start_date', [$request->startDateTracked, $request->endDateTracked]);
        }

        if ($request->assigned_to) {
            $issuesQuery = $issuesQuery->where('tasks.assign_to', $request->assigned_to);
        }

        return $issuesQuery;
    }

    public function getTasksCsvNeededFormat($issues)
    {
        $tasks_csv = [];

        $users = User::query()->pluck('name', 'id');
        foreach ($issues as $value) {
            // dd($value);
            $task_csv = [];
            $task_csv['id'] = $value->id;
            $task_csv['Subject'] = $value->subject ?? $value->task_subject;
            $task_csv['Assigned To'] = ((! empty($users[$value->assigned_to]) || ! empty($users[$value->assign_to])) ?
                                            ($users[$value->assigned_to] ?? $users[$value->assign_to]) :
                                            'Unassigned');
            $task_csv['Approved Time'] = (isset($value->developerTaskHistories->first()->approved_time) && ! empty($value->developerTaskHistories->first()->approved_time)) ?
                                            $value->developerTaskHistories->first()->approved_time : 0;
            $task_csv['Status'] = ((isset($value->taskStatusAlter) && ! empty($value->taskStatusAlter)) ? $value->taskStatusAlter->name : ((is_string($value->status)) ? $value->status : '-'));

            $task_csv['Tracked Time'] = ((isset($value->taskStartEndHistories->first()->tracked_time) && ! empty($value->taskStartEndHistories->first()->tracked_time)) ?
                                            $value->taskStartEndHistories->first()->tracked_time : 0);
            $task_csv['Tracking Start'] = ((isset($value->taskStartEndHistories->first()->first_start_date) && ! empty($value->taskStartEndHistories->first()->first_start_date)) ?
                                            $value->taskStartEndHistories->first()->first_start_date : '-');
            $task_csv['Tracking End'] = ((isset($value->taskStartEndHistories->first()->last_end_date) && ! empty($value->taskStartEndHistories->first()->last_end_date)) ?
                                            $value->taskStartEndHistories->first()->last_end_date : '-');
            $task_csv['Difference'] = ($task_csv['Tracked Time'] - $task_csv['Approved Time'] > 0 ? $task_csv['Tracked Time'] - $task_csv['Approved Time'] : '+'.abs($task_csv['Tracked Time'] - $task_csv['Approved Time'])) ?? 0;
            array_push($tasks_csv, $task_csv);
        }

        return $tasks_csv;
    }

    public function viewAllTasks(Request $request): \Illuminate\View\View
    {
        $issues = $this->loadAllTasks($request)->paginate(10);
        $tasks_csv = $this->getTasksCsvNeededFormat($issues);

        if ($request->ajax()) {
            return view('development.ajax.all-tasks-ajax', [
                'issues' => $issues,
                'tasks_csv' => $tasks_csv,
                'startDate' => $request->get('startDate', null),
                'endDate' => $request->get('endDate', null),
            ]);
        }

        return view('development.all-tasks', [
            'issues' => $issues,
            'tasks_csv' => $tasks_csv,
            'startDate' => $request->get('startDate', null),
            'endDate' => $request->get('endDate', null),
        ]);
    }

    public function exportTask(Request $request)
    {
        $issues = $this->loadAllTasks($request)->get();
        $tasks_csv = $this->getTasksCsvNeededFormat($issues);

        $this->outputCsv('download-task-summaries.csv', $tasks_csv);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new DeveloperTaskExcelExport($request), 'tasks.xlsx');
    }

    private function outputCsv($fileName, $assocDataArray)
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename='.$fileName);
        $fp = fopen('php://output', 'w');
        if (isset($assocDataArray['0'])) {
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray as $values) {
                fputcsv($fp, $values);
            }
        }
        fclose($fp);
        exit();
    }

    public function summaryList(Request $request): \Illuminate\View\View
    {
        // Load issues
        $type = $request->tasktype ? $request->tasktype : 'all';

        $title = 'Task List';

        $issues = DeveloperTask::with(['timeSpent', 'assignedUser', 'masterUser']);
        if ($type == 'issue') {
            $issues = $issues->where('developer_tasks.task_type_id', '3');
        }
        if ($type == 'devtask') {
            $issues = $issues->where('developer_tasks.task_type_id', '1');
        }
        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('developer_tasks.created_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('developer_tasks.responsible_user_id', $request->get('responsible_user'));
        }

        if ((int) $request->get('corrected_by') > 0) {
            $issues = $issues->where('developer_tasks.user_id', $request->get('corrected_by'));
        }

        if ((int) $request->get('assigned_to') > 0) {
            $issues = $issues->whereIn('developer_tasks.assigned_to', $request->get('assigned_to'));
        }
        if ((int) $request->get('lead') > 0) {
            $issues = $issues->whereIn('developer_tasks.master_user_id', $request->get('lead'));
        }
        if ($request->get('module')) {
            $issues = $issues->where('developer_tasks.module_id', $request->get('module'));
        }
        if (! empty($request->get('task_status', []))) {
            $issues = $issues->whereIn('developer_tasks.status', $request->get('task_status'));
        } else {
            $issues = $issues->where('developer_tasks.status', 'In Progress');
        }

        if (! empty($request->get('module_id', []))) {
            $issues = $issues->whereIn('developer_tasks.module_id', $request->get('module_id'));
        }

        $whereCondition = '';
        if ($request->get('subject') != '') {
            $subject = explode(',', $request->get('subject'));
            $whereCondition .= ' and message like  "%'.$request->get('subject').'%"';
            $issues = $issues->where(function ($query) use ($subject) {
                $query->whereIn('developer_tasks.id', $subject)
                    ->orWhere(function ($query) use ($subject) {
                        foreach ($subject as $termSubject) {
                            $query->orWhere('subject', 'like', "%$termSubject%")->orWhere('task', 'like', "%$termSubject%")->orWhere('chat_messages.message', 'LIKE', "%$termSubject%");
                        }
                    });
            });
        }

        $issues = $issues->leftJoin(DB::raw('(SELECT MAX(id)
 as  max_id, issue_id, message   FROM `chat_messages` where issue_id > 0 '.$whereCondition.' GROUP BY issue_id ) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');
        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');

        if ($request->get('last_communicated', 'off') == 'on') {
            $issues = $issues->orderByDesc('chat_messages.id');
        }
        if ($request->get('unread_messages', 'off') == 'unread') {
            $issues = $issues->where('chat_messages.sent_to_user_id', Auth::user()->id);
        }

        $issues = $issues->select('developer_tasks.*', 'chat_messages.message', 'chat_messages.sent_to_user_id');

        // Set variables with modules and users
        $modules = DeveloperModule::select('id', 'name')->orderBy('name')->get();

        $users = Helpers::getUserArray(User::orderBy('name')->get());

        $statusList = TaskStatus::select('name')->orderBy('name')->pluck('name', 'name')->toArray();

        $statusList = array_merge([
            '' => 'Select Status',
        ], $statusList);

        if (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id);
            });
        }

        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'Planned')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));
        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();

        $countPlanned = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countPlanned[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }
        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'In Progress')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));

        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();
        $countInProgress = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countInProgress[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderBy('priority')->orderByDesc('created_at')->with('communications');
        } elseif ($request->order == 'latest_task_first') {
            $issues = $issues->orderByDesc('developer_tasks.id');
        } else {
            $issues = $issues->orderByDesc('chat_messages.id');
        }

        $issues = $issues->with('communications');

        $issues = $issues->paginate(Setting::get('pagination'));
        $priority = ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        //Get all searchable user list
        $userslist = null;
        if ((int) $request->get('assigned_to') > 0) {
            $userslist = User::whereIn('id', $request->get('assigned_to'))->get();
        }

        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name')->get()->toArray();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'development-summarylist')->first();

        $dynamicColumnsToShowDs = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowDs = json_decode($hideColumns, true);
        }

        $reply_categories = ReplyCategory::with([
            'approval_leads' => function ($query) {
                $query->select('id', 'category_id', 'reply')
                    ->where('model', 'Approval Lead')
                    ->orderBy('reply');
            },
            'sub_categories' => function ($query) {
                $query->select('id', 'parent_id', 'name')->orderBy('name');
            }
        ])
        ->select('id', 'name')
        ->where('parent_id', 0)
        ->where('id', 44)
        ->orderBy('name')
        ->get();
        $taskStatusColors = TaskStatus::pluck('task_color', 'name')->toArray();

        $issueIds = $issues->pluck('id')->toArray();
        $currentUserId = auth()->user()->id;

        $developerTimes = MeetingAndOtherTime::where('model', DeveloperTask::class)
            ->whereIn('model_id', $issueIds)
            ->where('user_id', $currentUserId)
            ->where('approve', 1)
            ->select('model_id', DB::raw('SUM(time)
 as total_time'))
            ->groupBy('model_id')
            ->pluck('total_time', 'model_id');

        $timeHistories = DeveloperTaskHistory::whereIn('developer_task_id', $issueIds)
            ->where('attribute', 'estimation_minute')
            ->where('model', DeveloperTask::class)
            ->get()
            ->keyBy('developer_task_id');

        foreach ($issues as $key => $issue) {
            if (auth()->user()->isReviwerLikeAdmin()) {
                $issue->task_color = $taskStatusColors[$issue->status] ?? null;
                $issue->view = 'development.partials.summarydata';
            } elseif (in_array($currentUserId, [$issue->created_by, $issue->master_user_id, $issue->assigned_to])) {
                $issue->view = 'development.partials.developer-row-view-s';
                $issue->isTimeShow = false;
                $issue->developerTime = null;

                if (in_array($currentUserId, [$issue->assigned_to, $issue->master_user_id, $issue->tester_id])) {
                    $issue->isTimeShow = true;
                    $issue->developerTime = $developerTimes[$issue->id] ?? null; 
                }

                $issue->time_history = $timeHistories[$issue->id] ?? null;
            }
        }

        if (request()->ajax()) {
            return view('development.partials.summarydatas', [
                'issues' => $issues,
                'users' => $users,
                'modules' => $modules,
                'request' => $request,
                'title' => $title,
                'type' => $type,
                'priority' => $priority,
                'countPlanned' => $countPlanned,
                'countInProgress' => $countInProgress,
                'statusList' => $statusList,
                'userslist' => $userslist,
                'dynamicColumnsToShowDs' => $dynamicColumnsToShowDs,
                'reply_categories' => $reply_categories,
            ]);
        }

        return view('development.summarylist', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $title,
            'type' => $type,
            'priority' => $priority,
            'countPlanned' => $countPlanned,
            'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            'userslist' => $userslist,
            'time_doctor_projects' => $time_doctor_projects,
            'dynamicColumnsToShowDs' => $dynamicColumnsToShowDs,
            'reply_categories' => $reply_categories,
        ]);
    }

    public function summaryListDev(Request $request): \Illuminate\View\View
    {
        // Load issues
        $type = $request->tasktype ? $request->tasktype : 'all';

        $title = 'Task List';

        $issues = DeveloperTask::with('timeSpent');
        if ($type == 'issue') {
            $issues = $issues->where('developer_tasks.task_type_id', '3');
        }
        if ($type == 'devtask') {
            $issues = $issues->where('developer_tasks.task_type_id', '1');
        }
        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('developer_tasks.created_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('developer_tasks.responsible_user_id', $request->get('responsible_user'));
        }

        if ((int) $request->get('corrected_by') > 0) {
            $issues = $issues->where('developer_tasks.user_id', $request->get('corrected_by'));
        }

        if ((int) $request->get('assigned_to') > 0) {
            $issues = $issues->whereIn('developer_tasks.assigned_to', $request->get('assigned_to'));
        }
        if ((int) $request->get('lead') > 0) {
            $issues = $issues->whereIn('developer_tasks.master_user_id', $request->get('lead'));
        }
        if ($request->get('module')) {
            $issues = $issues->where('developer_tasks.module_id', $request->get('module'));
        }
        if (! empty($request->get('task_status', []))) {
            $issues = $issues->whereIn('developer_tasks.status', $request->get('task_status'));
        } else {
            $issues = $issues->where('developer_tasks.status', 'In Progress');
        }

        $whereCondition = '';
        if ($request->get('subject') != '') {
            $subject = explode(',', $request->get('subject'));
            $whereCondition .= ' and message like  "%'.$request->get('subject').'%"';
            $issues = $issues->where(function ($query) use ($subject) {
                $query->whereIn('developer_tasks.id', $subject)
                    ->orWhere(function ($query) use ($subject) {
                        foreach ($subject as $termSubject) {
                            $query->orWhere('subject', 'like', "%$termSubject%")->orWhere('task', 'like', "%$termSubject%")->orWhere('chat_messages.message', 'LIKE', "%$termSubject%");
                        }
                    });
            });
        }

        $issues = $issues->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, issue_id, message   FROM `chat_messages` where issue_id > 0 '.$whereCondition.' GROUP BY issue_id ) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');
        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');

        if ($request->get('last_communicated', 'off') == 'on') {
            $issues = $issues->orderByDesc('chat_messages.id');
        }
        if ($request->get('unread_messages', 'off') == 'unread') {
            $issues = $issues->where('chat_messages.sent_to_user_id', Auth::user()->id);
        }

        $issues = $issues->select('developer_tasks.*', 'chat_messages.message', 'chat_messages.sent_to_user_id');

        // Set variables with modules and users
        $modules = DeveloperModule::orderBy('name')->get();

        $users = Helpers::getUserArray(User::orderBy('name')->get());

        $statusList = TaskStatus::select('name')->orderBy('name')->pluck('name', 'name')->toArray();

        $statusList = array_merge([
            '' => 'Select Status',
        ], $statusList);

        if (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id);
            });
        }

        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'Planned')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));
        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();

        $countPlanned = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countPlanned[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }
        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'In Progress')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));

        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();
        $countInProgress = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countInProgress[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderBy('priority')->orderByDesc('created_at')->with('communications');
        } elseif ($request->order == 'latest_task_first') {
            $issues = $issues->orderByDesc('developer_tasks.id');
        } else {
            $issues = $issues->orderByDesc('chat_messages.id');
        }

        $issues = $issues->with('communications');

        $issues = $issues->paginate(Setting::get('pagination'));
        $priority = ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        //Get all searchable user list
        $userslist = null;
        if ((int) $request->get('assigned_to') > 0) {
            $userslist = User::whereIn('id', $request->get('assigned_to'))->get();
        }

        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name')->get()->toArray();

        $reply_categories = ReplyCategory::select('id', 'name')
            ->with('approval_leads', 'sub_categories')
            ->where('parent_id', 0)
            ->where('id', 44)
            ->orderBy('name')->get();
        foreach ($issues as $key => $issue) {
            if (auth()->user()->isReviwerLikeAdmin()) {
                $issue->task_color = TaskStatus::where('name', $issue->status)->value('task_color');
                $issue->view = 'development.partials.summarydata';
            } elseif (in_array(auth()->user()->id, [$issue->created_by, $issue->master_user_id, $issue->assigned_to])) {
                $issue->view = 'development.partials.developer-row-view-s';
                $issue->isTimeShow = false;
                $issue->developerTime = null;
                if (in_array(auth()->user()->id, [$issue->assigned_to, $issue->master_user_id, $issue->tester_id])) {
                    $issue->isTimeShow = true;
                    $issue->developerTime = MeetingAndOtherTime::where('model', DeveloperTask::class)
                        ->where('model_id', $issue->id)
                        ->where('user_id', auth()->user()->id)
                        ->where('approve', 1)
                        ->sum('time');
                }
                $issue->time_history = DeveloperTaskHistory::where('developer_task_id', $issue->id)->where('attribute', 'estimation_minute')->where('model', DeveloperTask::class)->first();
            }
        }
        if (request()->ajax()) {
            return view('development.partials.summarydatas', [
                'issues' => $issues,
                'users' => $users,
                'modules' => $modules,
                'request' => $request,
                'title' => $title,
                'type' => $type,
                'priority' => $priority,
                'countPlanned' => $countPlanned,
                'countInProgress' => $countInProgress,
                'statusList' => $statusList,
                'userslist' => $userslist,
                'reply_categories' => $reply_categories,
            ]);
        }

        return view('development.summarylistdev', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $title,
            'type' => $type,
            'priority' => $priority,
            'countPlanned' => $countPlanned,
            'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            'userslist' => $userslist,
            'time_doctor_projects' => $time_doctor_projects,
            'reply_categories' => $reply_categories,
        ]);
    }

    public function searchDevTask(Request $request): \Illuminate\View\View
    {
        $type = $request->tasktype ? $request->tasktype : 'all';

        $title = 'Task List';

        $issues = DeveloperTask::with('timeSpent');

        $whereCondition = '';
        if ($request->get('subject') != '') {
            $whereCondition = ' and message like  "%'.$request->get('subject').'%"';
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $task_id = explode(',', $subject);
                if (count($task_id) == 1) {
                    $query->where('developer_tasks.id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%")->orWhere('task', 'LIKE', "%$subject%")
                        ->orwhere('chat_messages.message', 'LIKE', "%$subject%");
                } else {
                    $query->whereIn('developer_tasks.id', $task_id)->orWhere('subject', 'LIKE', "%$subject%")->orWhere('task', 'LIKE', "%$subject%")
                        ->orwhere('chat_messages.message', 'LIKE', "%$subject%");
                }
            });
        }

        if ($request->input('selected_user') != '') {
            $userid = $request->input('selected_user');
            $issues = $issues->where('developer_tasks.assigned_to', $userid);
        }

        $issues = $issues->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, issue_id, message   FROM `chat_messages` where issue_id > 0 '.$whereCondition.' GROUP BY issue_id ) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');
        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');
        if ($request->get('last_communicated', 'off') == 'on') {
            $issues = $issues->orderByDesc('chat_messages.id');
        }
        if ($request->get('unread_messages', 'off') == 'unread') {
            $issues = $issues->where('chat_messages.sent_to_user_id', Auth::user()->id);
        }
        $issues = $issues->select('developer_tasks.*', 'chat_messages.message', 'chat_messages.sent_to_user_id');
        // Set variables with modules and users
        $modules = DeveloperModule::orderBy('name')->get();
        $users = Helpers::getUserArray(User::orderBy('name')->get());
        $statusList = TaskStatus::select('name')->orderBy('name')->pluck('name', 'name')->toArray();
        $statusList = array_merge([
            '' => 'Select Status',
        ], $statusList);

        // Hide resolved
        if (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id);
            });
        }

        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'Planned')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));
        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();

        $countPlanned = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countPlanned[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }
        // category filter start count
        $issuesGroups = clone $issues;
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'In Progress')->groupBy('developer_tasks.assigned_to')->select([DB::raw('count(developer_tasks.id) as total_product'), 'developer_tasks.assigned_to'])->pluck('total_product', 'assigned_to')->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));

        $userModel = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();
        $countInProgress = [];
        if (! empty($issuesGroups) && ! empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countInProgress[] = [
                    'id' => $key,
                    'name' => ! empty($userModel[$key]) ? $userModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderBy('priority')->orderByDesc('created_at')->with('communications');
        } elseif ($request->order == 'latest_task_first') {
            $issues = $issues->orderByDesc('developer_tasks.id');
        } else {
            $issues = $issues->orderByDesc('chat_messages.id');
        }

        $issues = $issues->with('communications');

        $issues = $issues->paginate(Setting::get('pagination'));

        foreach ($issues as $key => $issue) {
            $issue->task_color = TaskStatus::where('name', $issue->status)->value('task_color');
        }

        $priority = ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        return view('development.partials.menu-summarydata', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $title,
            'type' => $type,
            'priority' => $priority,
            'countPlanned' => $countPlanned,
            'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            // 'languages' => $languages
        ]);
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = TaskStatus::find($key);
            $bugstatus->task_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function automaticTasks(Request $request)
    {
        $users = Helpers::getUserArray(User::orderBy('name')->get());
        $title = 'Automatic Task List';

        $task = Task::leftJoin('site_developments', 'site_developments.id', 'tasks.site_developement_id')
            ->leftJoin('store_websites', 'store_websites.id', 'site_developments.website_id')
            ->with('timeSpent')->where('is_flow_task', '1');

        $devCheckboxs = $request->get('devCheckboxs');
        $dev = [];

        if (isset($request->term) && ! empty($request->term)) {
            $task = $task->where(function ($query) use ($request) {
                $term = $request->get('term');
                $query->where('tasks.id', 'LIKE', "%$term%")
                    ->orWhere('store_websites.website', 'LIKE', "%$term%")
                    ->orWhere('tasks.parent_task_id', 'LIKE', "%$term%")
                    ->orWhere('tasks.task_subject', 'LIKE', "%$term%")
                    ->orWhere('tasks.task_details', 'LIKE', "%$term%")
                    ->orwhere('chat_messages.message', 'LIKE', "%$term%");
            });
        }

        if (isset($request->assigned_to) && ! empty($request->assigned_to)) {
            $task = $task->where('tasks.assign_to', $request->assigned_to);
        }

        if (isset($request->task_status) && ! empty($request->task_status)) {
            $task = $task->where('tasks.status', $request->task_status);
        }

        $task = $task->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, task_id, message  FROM `chat_messages` where task_id > 0 GROUP BY task_id ) m_max'), 'm_max.task_id', '=', 'tasks.id');
        $task = $task->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');
        $task = $task->select('tasks.*', 'chat_messages.message', 'store_websites.website', 'store_websites.title as website_title');

        if ($devCheckboxs) {
            $count = 1;
            foreach ($request->get('devCheckboxs') as $devCheckbox) {
                if ($count == 1) {
                    $task = $task->where('tasks.assign_to', $devCheckbox);
                } else {
                    $task = $task->orWhere('tasks.assign_to', $devCheckbox);
                }
                $count++;
                $dev[$devCheckbox] = 1;
            }
        }

        if (! auth()->user()->isReviwerLikeAdmin()) {
            if (count($dev) == 0) {
                $task = $task->where(function ($query) {
                    $query->where('tasks.assign_to', auth()->user()->id)
                        ->orWhere('tasks.master_user_id', auth()->user()->id);
                });
            }
        }

        $tasks = $task->paginate(50);

        $task_statuses = TaskStatus::all();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('task-module.partials.flagsummarydata', compact('users', 'request', 'title', 'task_statuses', 'tasks', 'dev'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $tasks->render(),
                'count' => $tasks->total(),
            ], 200);
        }

        return view('task-module.automatictask', [
            'users' => $users,
            'request' => $request,
            'title' => $title,
            'task_statuses' => $task_statuses,
            'tasks' => $tasks,
            'dev' => $dev,
            'count' => $tasks->total(),
        ]);
    }

    public function flagtask(Request $request)
    {
        $users = Helpers::getUserArray(User::orderBy('name')->get());
        $statusList = TaskStatus::select('name')->orderBy('name')->pluck('name', 'name')->toArray();

        $isTeamLeader = Team::where('user_id', auth()->user()->id)->first();

        $model_team = Team::where('user_id', auth()->user()->id)->get()->toArray();
        $team_members_array[] = auth()->user()->id;
        $team_id_array = [];
        $team_members_array_unique_ids = '';
        if (count($model_team) > 0) {
            for ($k = 0; $k < count($model_team); $k++) {
                $team_id_array[] = $model_team[$k]->id;
            }
            $model_user_model = TeamUser::whereIn('team_id', $team_id_array)->get()->toArray();
            for ($m = 0; $m < count($model_user_model); $m++) {
                $team_members_array[] = $model_user_model[$m]->user_id;
            }
        }

        $team_members_array_unique = array_unique($team_members_array);
        $team_members_array_unique_ids = implode(',', $team_members_array_unique);

        $task_statuses = TaskStatus::all();
        $taskStatusArray = $task_statuses->pluck('id', 'name')->toArray();
        $modules = DeveloperModule::orderBy('name')->get();

        $type = $request->tasktype ? $request->tasktype : 'all';

        $title = 'Flag Task List';

        $issues = DeveloperTask::with(['timeSpent', 'leadtimeSpent', 'testertimeSpent', 'assignedUser', 'taskStatus']); // ->where('is_flagged', '1')
        $issues->whereNotIn('developer_tasks.status', [DeveloperTask::DEV_TASK_STATUS_DONE, DeveloperTask::DEV_TASK_STATUS_IN_REVIEW]);
        $issues->whereRaw('developer_tasks.assigned_to IN (SELECT id FROM users WHERE is_task_planned = 1)');

        $task = Task::with(['timeSpent', 'taskStatus']); // ->where('is_flagged', '1')
        $task->whereNotIn('tasks.status', [
            Task::TASK_STATUS_DONE,
            Task::TASK_STATUS_USER_COMPLETE,
            Task::TASK_STATUS_USER_COMPLETE_2,
        ]);
        $task->whereRaw('tasks.assign_to IN (SELECT id FROM users WHERE is_task_planned = 1)');

        if (Auth::user()->hasRole('Admin')) {
            $task->whereRaw('tasks.assign_to IN (SELECT id FROM users WHERE is_task_planned = 1)');
        } elseif ($isTeamLeader) {
            $task->whereRaw('tasks.assign_to IN (SELECT id FROM users WHERE is_task_planned = 1 AND id IN ('.$team_members_array_unique_ids.'))');
        } else {
            $login_user_id = auth()->user()->id;
            $task->whereRaw('tasks.assign_to IN (SELECT id FROM users WHERE is_task_planned = 1 AND id IN ('.$login_user_id.'))');
        }

        if ($type == 'issue') {
            $issues = $issues->where('developer_tasks.task_type_id', '3');
        }

        if ($type == 'devtask') {
            $issues = $issues->where('developer_tasks.task_type_id', '1');
        }
        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('developer_tasks.created_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('developer_tasks.responsible_user_id', $request->get('responsible_user'));
        }

        if ((int) $request->get('corrected_by') > 0) {
            $issues = $issues->where('developer_tasks.user_id', $request->get('corrected_by'));
            $task = $task->where('tasks.assign_from', $request->get('corrected_by'));
        }

        if ($s = request('assigned_to')) {
            if ($s[0] != '') {
                $issues = $issues->whereIn('developer_tasks.assigned_to', $s);
                $task = $task->whereIn('tasks.assign_to', $s);
            }
        }
        if ((int) $request->get('empty_estimated_time') > 0) {
            $issues = $issues->where('developer_tasks.estimate_time', null);
            $issues = $issues->where('developer_tasks.estimate_date', null);
            $task = $task->where('tasks.approximate', 0);
            $task = $task->where('tasks.due_date', null);
        }
        if ((int) $request->get('time_is_overdue') > 0) {
            $issues = $issues->where('developer_tasks.estimate_date', '>', date('Y-m-d'))->where('developer_tasks.status', '!=', 'Done');

            $task = $task->where('tasks.due_date', '>', date('Y-m-d'))->where('tasks.status', '!=', 3);
        }
        if ($s = request('module_id', [])) {
            if ($s[0] != '') {
                $issues = $issues->whereIn('developer_tasks.module_id', $s);
            }
        }
        if (! empty($request->get('task_status', []))) {
            $issues = $issues->whereIn('developer_tasks.status', $request->get('task_status'));

            $requestStatusArray = [];
            foreach ($request->get('task_status') as $status) {
                $requestStatusArray[] = $taskStatusArray[$status];
            }

            $task = $task->whereIn('tasks.status', $requestStatusArray);
        }
        $whereCondition = $whereTaskCondition = '';
        if ($request->get('subject') != '') {
            $whereCondition = ' and message like  "%'.$request->get('subject').'%"';
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('developer_tasks.id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%")->orWhere('task', 'LIKE', "%$subject%")
                    ->orwhere('chat_messages.message', 'LIKE', "%$subject%");
            });

            $whereTaskCondition = ' and message like  "%'.$request->get('subject').'%"';
            $task = $task->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('tasks.id', 'LIKE', "%$subject%")->orWhere('task_subject', 'LIKE', "%$subject%")->orWhere('task_details', 'LIKE', "%$subject%")
                    ->orwhere('chat_messages.message', 'LIKE', "%$subject%");
            });
        }

        $issues = $issues->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, issue_id, message  FROM `chat_messages` where issue_id > 0 '.$whereCondition.' GROUP BY issue_id ) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');
        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');

        if ($request->get('last_communicated', 'off') == 'on') {
            $issues = $issues->orderByDesc('chat_messages.id');
        }

        $issues = $issues->select('developer_tasks.*', 'chat_messages.message');

        $task = $task->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, task_id, message  FROM `chat_messages` where task_id > 0 '.$whereTaskCondition.' GROUP BY task_id ) m_max'), 'm_max.task_id', '=', 'tasks.id');
        $task = $task->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');
        $task = $task->select('tasks.*', 'chat_messages.message');

        if ($isTeamLeader && ! Auth::user()->hasRole('Admin')) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id);
            });
            $task = $task->where(function ($query) use ($team_members_array_unique) {
                $query->whereIn('tasks.assign_to', $team_members_array_unique)
                    ->orWhere('tasks.master_user_id', auth()->user()->id);
            });
        } elseif (! auth()->user()->isReviwerLikeAdmin()) {
            $issues = $issues->where(function ($query) {
                $query->where('developer_tasks.assigned_to', auth()->user()->id)
                    ->orWhere('developer_tasks.master_user_id', auth()->user()->id);
            });
            $task = $task->where(function ($query) {
                $query->where('tasks.assign_to', auth()->user()->id)
                    ->orWhere('tasks.master_user_id', auth()->user()->id);
            });
        }

        if ($request->delivery_date && $request->delivery_date != '') {
            $delivery_date = Carbon::parse($request->delivery_date)->toDateString();
            $issues->whereDate('due_date', $delivery_date);
            $task->whereDate('due_date', $delivery_date);
        }

        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderBy('priority')->orderByDesc('created_at');
            $task = $task->orderBy('priority_no')->orderByDesc('created_at');
        } elseif ($request->order == 'latest_task_first') {
            $issues = $issues->orderByDesc('developer_tasks.id');
            $task = $task->orderByDesc('tasks.id');
        } elseif ($request->order == 'oldest_first') {
            $issues = $issues->orderBy('developer_tasks.id');
            $task = $task->orderBy('tasks.id');
        } else {
            $issues = $issues->orderByDesc('chat_messages.id');
            $task = $task->orderByDesc('chat_messages.id');
        }

        $paginateLimit = Setting::get('pagination') ?: 15;

        $issues = $issues->paginate($paginateLimit);

        $tasks = $task->paginate($paginateLimit);

        $priority = ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();
        if ($request->ajax()) {
            $data = '';
            $isReviwerLikeAdmin = auth()->user()->isReviwerLikeAdmin();
            $userID = Auth::user()->id;
            foreach ($issues as $issue) {
                if ($isReviwerLikeAdmin) {
                    $data .= view('development.partials.flagsummarydata', compact('issue', 'users', 'statusList', 'task_statuses'));
                } elseif ($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID) {
                    $data .= view('development.partials.flagdeveloper-row-view', compact('issue', 'users', 'statusList', 'task_statuses'));
                }
            }
            foreach ($tasks as $issue) {
                if ($isReviwerLikeAdmin) {
                    $data .= view('task-module.partials.flagsummarydata2', compact('issue', 'users', 'statusList', 'task_statuses'));
                } elseif ($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID) {
                    $data .= view('task-module.partials.flagdeveloper-row-view', compact('issue', 'users', 'statusList', 'task_statuses'));
                }
            }

            return $data;
        }

        $taskMessage = TaskMessage::where('message_type', 'date_time_reminder_message')->first();

        return view('development.flagtask', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $title,
            'type' => $type,
            'priority' => $priority,
            'tasks' => $tasks,
            'taskMessage' => $taskMessage,
            // 'countPlanned' => $countPlanned,
            //'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            // 'languages' => $languages,
            'task_statuses' => $task_statuses,
            'isTeamLeader' => $isTeamLeader,
        ]);
    }

    public function gettasktimemessage(request $request): JsonResponse
    {
        $id = $request->input('id');
        $html = '';
        $chatmessages = ChatMessage::where('task_id', $id)->where('task_time_reminder', 1)->orwhere('developer_task_id', $id)->get();
        $i = 1;
        if (count($chatmessages) > 0) {
            foreach ($chatmessages as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->message.'</td>';
                $html .= '<td>'.$history->created_at.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function getlogtasktimemessage(request $request): JsonResponse
    {
        $id = $request->input('id');
        $html = '';
        $chatmessages = LogChatMessage::where('task_id', $id)->where('task_time_reminder', 1)->get();
        $i = 1;
        if (count($chatmessages) > 0) {
            foreach ($chatmessages as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->log_case_id.'</td>';
                $html .= '<td>'.$history->message.'</td>';
                $html .= '<td>'.$history->log_msg.'</td>';
                $html .= '<td>'.$history->created_at.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function saveTaskMessage(Request $request): JsonResponse
    {
        $input = $request->input();
        TaskMessage::updateOrCreate(['id' => $input['id']], $input);

        return response()->json([
            'success',
        ]);
    }

    public function saveTaskTimeMessage(Request $request): JsonResponse
    {
        $est_time_message['message'] = $request->est_time_message;
        $est_date_message['message'] = $request->est_date_message;
        $overdue_time_date_message['message'] = $request->overdue_time_date_message;

        TaskMessage::updateOrCreate(['message_type' => 'est_time_message', 'frequency' => $request->frequency], $est_time_message);
        TaskMessage::updateOrCreate(['message_type' => 'est_date_message', 'frequency' => $request->frequency], $est_date_message);
        TaskMessage::updateOrCreate(['message_type' => 'overdue_time_date_message', 'frequency' => $request->frequency], $overdue_time_date_message);

        return response()->json(['success']);
    }

    public function summaryList1(Request $request): \Illuminate\View\View
    {
        $modules = DeveloperModule::all();
        print_r($modules);

        $statusList = TaskStatus::select('name')->pluck('name', 'name')->toArray();

        $statusList = array_merge([
            '' => 'Select Status',
        ], $statusList);

        return view('development.summarylist', compact('modules', 'statusList'));
    }

    public function issueIndex(Request $request): \Illuminate\View\View
    {
        $issues = new Issue;

        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('submitted_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }
        if ((int) $request->get('assigned_to') > 0) {
            $issues = $issues->where('assigned_to', $request->get('assigned_to'));
        }
        if ((int) $request->get('corrected_by') > 0) {
            $issues = $issues->where('user_id', $request->get('corrected_by'));
        }
        if ($request->get('module')) {
            $issues = $issues->where('module', $request->get('module'));
        }
        if ($request->get('subject') != '') {
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%");
            });
        }
        $modules = DeveloperModule::all();
        $users = Helpers::getUserArray(User::all());
        // Hide resolved
        if ((int) $request->show_resolved !== 1) {
            $issues = $issues->where('is_resolved', 0);
        }
        // Sort
        if ($request->order == 'create') {
            $issues = $issues->orderByDesc('created_at')->with('communications')->get();
        } else {
            $issues = $issues->orderBy('priority')->orderByDesc('created_at')->with('communications')->get();
        }
        $priority = ErpPriority::where('model_type', '=', Issue::class)->pluck('model_id')->toArray();

        $isReviwerLikeAdmin = auth()->user()->isReviwerLikeAdmin();
        $userID = Auth::user()->id;
        foreach ($issues as $key => $issue) {
            if ($isReviwerLikeAdmin) {
                $issue->view = 'development.partials.admin-row-view';
            } elseif (in_array($userID, [$issue->created_by, $issue->master_user_id, $issue->assigned_to])) {
                $issue->view = 'development.partials.developer-row-view';
                $issue->isTimeShow = false;
                $issue->developerTime = null;
                if (in_array($userID, [$issue->assigned_to, $issue->master_user_id, $issue->tester_id])) {
                    $issue->isTimeShow = true;
                    $issue->developerTime = MeetingAndOtherTime::where('model', DeveloperTask::class)
                        ->where('model_id', $issue->id)
                        ->where('user_id', $userID)
                        ->where('approve', 1)
                        ->sum('time');
                }
                $issue->time_history = DeveloperTaskHistory::where('developer_task_id', $issue->id)->where('attribute', 'estimation_minute')->where('model', DeveloperTask::class)->first();
            }
        }

        return view('development.issue', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => 'Issue',
            'priority' => $priority,
        ]);
    }

    public function listByUserId(Request $request): JsonResponse
    {
        $user_id = $request->get('user_id', 0);
        $selected_issue = $request->get('selected_issue', []);
        $issues = DeveloperTask::select('developer_tasks.*')
            ->leftJoin('erp_priorities', function ($query) use ($user_id) {
                $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                $query->where('erp_priorities.user_id', $user_id);
            })
            ->where('status', '!=', 'Done');
        if (auth()->user()->isAdmin()) {
            $issues = $issues->where(function ($q) use ($selected_issue, $user_id) {
                $user_id = is_null($user_id) ? 0 : $user_id;
                if ($user_id != 0) {
                    $q->where('developer_tasks.assigned_to', $user_id)
                        ->orWhere('developer_tasks.master_user_id', $user_id)
                        ->orWhere('developer_tasks.team_lead_id', $user_id)
                        ->orWhere('developer_tasks.tester_id', $user_id);
                }
                $q->whereIn('developer_tasks.id', $selected_issue)->orWhere('erp_priorities.user_id', $user_id);
            });
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->orderBy('erp_priorities.id')->get();
        foreach ($issues as &$value) {
            $value->module = $value->developerModule->name;
            $value->created_by = User::where('id', $value->created_by)->value('name');
        }
        unset($value);
        $viewData = view('development.partials.taskpriority', compact('issues'))->render();

        return response()->json([
            'html' => $viewData,

        ], 200);
    }

    public function setPriority(Request $request): JsonResponse
    {
        $priority = $request->get('priority', null);
        $user_id = $request->get('user_id', 0);
        //delete old priority
        ErpPriority::where('user_id', $user_id)->where('model_type', '=', DeveloperTask::class)->delete();

        if (! empty($priority)) {
            foreach ((array) $priority as $model_id) {
                ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class,
                    'user_id' => $user_id,
                ]);
            }

            $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by', 'developer_tasks.task_type_id')
                ->join('erp_priorities', function ($query) use ($user_id) {
                    $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                    $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                    $query->where('erp_priorities.user_id', '=', $user_id);
                })
                ->where('is_resolved', '0')
                ->orderBy('erp_priorities.id')
                ->get();
            $message = '';
            $i = 1;
            foreach ($issues as $value) {
                $mode = ($value->task_type_id == 3) ? '#ISSUE-' : '#TASK-';
                $message .= $i.' : '.$mode.$value->id.'-'.$value->subject."\n";
                $i++;
            }
            if (! empty($message)) {
                $requestData = new Request;
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = '';
                if (! empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes')."\n";
                }
                $string .= "Issue Priority is : \n".$message;

                $params['message'] = $string;
                $params['status'] = 2;
                $requestData->request->add($params);
                app(WhatsAppController::class)->sendMessage($requestData, 'priority');
            }
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function issueCreate(): \Illuminate\View\View
    {
        return view('development.issue-create');
    }

    private function createHubstaffTask(string $taskSummary, ?int $hubstaffUserId, int $projectId, bool $shouldRetry = true)
    {
        $tokens = $this->getTokens();
        $url = 'https://api.hubstaff.com/v2/projects/'.$projectId.'/tasks';
        $httpClient = new Client;
        try {
            $body = [
                'summary' => $taskSummary,
            ];

            if ($hubstaffUserId) {
                $body['assignee_id'] = $hubstaffUserId;
            } else {
                $body['assignee_id'] = config('env.HUBSTAFF_DEFAULT_ASSIGNEE_ID');
            }

            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer '.$tokens->access_token,
                        'Content-Type' => 'application/json',
                    ],

                    RequestOptions::BODY => json_encode($body),
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return $parsedResponse->task->id;
        } catch (ClientException $e) {
            if ($e->getCode() == 401) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->createHubstaffTask(
                        $taskSummary,
                        $hubstaffUserId,
                        $projectId,
                        false
                    );
                }
            }
        }

        return false;
    }

    public function timeDoctorActions($type, $task, $projectId, $accountId, $assignTo)
    {
        $project_data = [];
        $project_data['time_doctor_project'] = $projectId;
        $project_data['time_doctor_task_name'] = $task['subject'] ?? '';
        $project_data['time_doctor_task_description'] = $task['task'] ?? '';

        if ($type == 'DEVTASK') {
            $message = '#DEVTASK-'.$task->id.' => '.$task->subject;
            $projectId = '#DEVTASK-'.$task->id;
        } elseif ($type == 'TASK') {
            $message = '#TASK-'.$task->id.' => '.$task->task_subject.'. '.$task->task_details;
            $projectId = '#TASK-'.$task->id;
        } else {
            return false;
        }

        $assignUsersData = TimeDoctorAccount::find($accountId);
        $timedoctor = Timedoctor::getInstance();
        $companyId = $assignUsersData->company_id;
        $accessToken = $assignUsersData->auth_token;

        if (config('app.env') === 'production') {
            $timeDoctorTaskId = '';
            $timeDoctorTaskResponse = $timedoctor->createGeneralTask($companyId, $accessToken, $project_data, $task->id, $type);
            if (! empty($timeDoctorTaskResponse['data'])) {
                $timeDoctorTaskId = $timeDoctorTaskResponse['data']['id'];
            }

            if ($timeDoctorTaskId && $timeDoctorTaskId != '') {
                $task->time_doctor_task_id = $timeDoctorTaskId;
                $task->save();
                $time_doctor_task = new TimeDoctorTask;
                $time_doctor_task->time_doctor_task_id = $timeDoctorTaskId;
                $time_doctor_task->project_id = $projectId;
                $time_doctor_task->time_doctor_project_id = $projectId;
                $time_doctor_task->summery = $message;
                $time_doctor_task->save();
            }

            return $timeDoctorTaskResponse;
        } else {
            return false;
        }
    }

    /**
     * return branch name or false
     *
     * @param  mixed  $repositoryId
     * @param  mixed  $taskId
     * @param  mixed  $taskTitle
     * @param  mixed  $branchName
     */
    private function createBranchOnGithub($repositoryId, $taskId, $taskTitle, $branchName = 'master')
    {
        $newBranchName = 'DEVTASK-'.$taskId;

        $githubRepository = GithubRepository::find($repositoryId);
        $organization = $githubRepository->organization;

        if (empty($organization)) {
            return false;
        }

        $githubClientObj = $this->connectGithubClient($organization->username, $organization->token);

        // get the master branch SHA
        $url = 'https://api.github.com/repositories/'.$repositoryId.'/branches/'.$branchName;
        try {
            $response = $githubClientObj->get($url);
            $masterSha = json_decode($response->getBody()->getContents())->commit->sha;
        } catch (Exception $e) {
            return false;
        }

        // create a branch
        $url = 'https://api.github.com/repositories/'.$repositoryId.'/git/refs';
        try {
            $this->githubClient->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'ref' => 'refs/heads/'.$newBranchName,
                        'sha' => $masterSha,
                    ]),
                ]
            );

            return $newBranchName;
        } catch (Exception $e) {
            if ($e instanceof ClientException && $e->getResponse()->getStatusCode() == 422) {
                // branch already exists
                return $newBranchName;
            }

            return false;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDevelopmentRequest $request)
    {

        $data = $request->except('_token');
        $data['hubstaff_project'] = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');
        $data['user_id'] = request('user_id', loginId());
        $data['created_by'] = Auth::id();
        $data['priority'] = 0;
        $data['hubstaff_task_id'] = 0;
        $data['repository_id'] = request('repository_id');

        $slotAvailable = $this->userSchedulesLoadData($request->get('assigned_to'));

        if (! empty($slotAvailable)) {
            $data['status'] = 'Planned';
            $data['start_date'] = $slotAvailable['st'];
            $data['estimate_date'] = $slotAvailable['en'];
        }

        $task = $this->developerTaskCreate($data);

        if (request('need_review_task')) {
            $data['parent_review_task_id'] = $task->id;
        }
        if ($request->ajax()) {
            return response()->json(['task' => $task]);
        }

        return redirect()->to(url('development/summarylist'))->with('success', 'You have successfully added task!');
    }

    public function developerTaskCreate($data)
    {
        $loggedUser = request()->user();

        $data['created_by'] = loginId();

        if ($data['parent_review_task_id'] ?? 0) {
            $data['subject'] = $data['subject'].' - #REVIEW_TASK';
            $data['task'] = $data['task'].' - #REVIEW_TASK';
        }
        $task = DeveloperTask::create($data);

        // Check the assinged user in any team ?
        if ($task->assigned_to > 0 && empty($task->team_lead_id)) {
            $teamUser = TeamUser::where('user_id', $task->assigned_to)->first();
            if ($teamUser) {
                $team = $teamUser->team;
                if ($team) {
                    if (strlen($team->user_id) > 0 && $team->user_id > 0) {
                        $task->team_lead_id = $team->user_id;
                        $task->save();
                    } elseif (strlen($team->second_lead_id) > 0 && $team->second_lead_id > 0) {
                        $task->team_lead_id = $team->second_lead_id;
                        $task->save();
                    }
                }
            } else {
                $isTeamLeader = Team::where('user_id', $task->assigned_to)
                    ->orWhere('second_lead_id', $task->assigned_to)->first();

                if ($isTeamLeader) {
                    $task->team_lead_id = $task->assigned_to;
                    $task->save();
                }
            }
        }

        // CREATE GITHUB REPOSITORY BRANCH
        $newBranchName = $this->createBranchOnGithub(
            $task->repository_id,
            $task->id,
            $task->subject
        );

        // UPDATE TASK WITH BRANCH NAME
        if ($newBranchName) {
            $task->github_branch_name = $newBranchName;
            $task->save();
        }

        // SEND MESSAGE
        if (is_string($newBranchName)) {
            $message = $task->task.PHP_EOL.'A new branch '.$newBranchName." has been created. Please pull the current code and run 'git checkout ".$newBranchName."' to work in that branch.";
        } else {
            $message = $task->task;
        }
        $requestData = new Request;
        $requestData->setMethod('POST');
        $requestData->request->add(['issue_id' => $task->id, 'message' => $message, 'status' => 1]);
        app(WhatsAppController::class)->sendMessage($requestData, 'issue');

        MessageHelper::sendEmailOrWebhookNotification([
            $task->user_id,
            $task->assigned_to,
            $task->master_user_id,
            $task->responsible_user_id,
            $task->team_lead_id,
            $task->tester_id,
        ], ' [ '.$loggedUser->name.' ] - '.$message);

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID') ?: 0;

        $hubstaffUserId = null;
        if ($assignedUser = HubstaffMember::where('user_id', $task->assigned_to)->first()) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }

        $summary = substr($task->task, 0, 200);
        if ($data['task_type_id'] == 1) {
            $taskSummery = '#DEVTASK-'.$task->id.' => '.$summary;
        } else {
            $taskSummery = '#TASK-'.$task->id.' => '.$summary;
        }

        if (isset($data['task_for']) && $data['task_for'] == 'time_doctor') {
            $this->timeDoctorActions('DEVTASK', $task, $data['time_doctor_project'], $data['time_doctor_account'], $data['assigned_to']);
        } else {
            $hubstaffTaskId = '';
            if (config('settings.production')) {
                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            } else {
                $hubstaff_project_id = '#TASK-3';
                $hubstaffTaskId = 34543; //for local system
            }

            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();

                $task = new HubstaffTask;
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->project_id = $hubstaff_project_id;
                $task->hubstaff_project_id = $hubstaff_project_id;
                $task->summary = $task->task;
                $task->save();
            }
        }

        return $task;
    }

    public function issueStore(Request $request)
    {
        $data = $request->except('_token');
        $module = $request->get('module');

        if ($request->response == 1) {
            $reference = md5(strtolower($request->reference));
            //Check if reference exist
            $existReference = DeveloperTask::where('reference', $reference)->first();
            if ($existReference != null || $existReference != '') {
                return redirect()->back()->withErrors(['Issue Already Created!']);
            }
        }

        if (! isset($reference)) {
            $reference = null;
        }

        if (is_string($module)) {
            $module = DeveloperModule::where('name', 'like', $module)->first();
        } else {
            $module = DeveloperModule::find($module);
        }

        if (! $module) {
            $module = new DeveloperModule;
            $module->name = $request->get('module');
            $module->save();
            $data['module'] = $module->id;
        }
        $userId = Auth::id();
        $userId = ! empty($userId) ? $userId : $request->get('assigned_to', 0);
        $task = new DeveloperTask;
        $task->priority = $request->input('priority');
        $task->subject = $request->input('subject');
        $task->task = $request->input('issue');
        $task->responsible_user_id = 0;
        $task->assigned_to = $request->get('assigned_to', 0);
        $task->module_id = $module->id;
        $task->user_id = 0;
        $task->assigned_by = $userId;
        $task->created_by = $userId;
        $task->reference = $reference;
        $task->status = $request->get('status', 'Issue');
        $task->task_type_id = $request->get('task_type_id', 3);
        $task->scraper_id = $request->input('scraper_id', null);
        $task->brand_id = $request->input('brand_id', null);
        $task->save();

        $repo = GithubRepository::where('name', 'erp')->first();

        if ($repo) {
            $this->createBranchOnGithub($repo->id, $task->id, $task->subject);
        }

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('issue/'.floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }
        $requestData = new Request;
        $requestData->setMethod('POST');
        $requestData->request->add(['issue_id' => $task->id, 'message' => $request->input('issue'), 'status' => 1]);

        // commenting code as its not working
        // app(WhatsAppController::class)->sendMessage($requestData, 'issue');

        // return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function moduleStore(ModuleStoreDevelopmentRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        DeveloperModule::create($data);

        return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function statusStore(StatusStoreDevelopmentRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        TaskStatus::create($data);

        return redirect()->back()->with('success', 'You have successfully created a status!');
    }

    public function commentStore(CommentStoreDevelopmentRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        DeveloperComment::create($data);

        return redirect()->back()->with('success', 'You have successfully wrote a comment!');
    }

    public function costStore(CostStoreDevelopmentRequest $request): RedirectResponse
    {
        $data = $request->except('_token');
        DeveloperCost::create($data);

        return redirect()->back()->with('success', 'You have successfully added payment!');
    }

    public function awaitingResponse(Request $request, $id): \Illuminate\Http\Response
    {
        $comment = DeveloperComment::find($id);
        $comment->status = 1;
        $comment->save();

        return response('success');
    }

    public function issueAssign(IssueAssignDevelopmentRequest $request, $id): RedirectResponse
    {
        $issue = Issue::find($id);
        $task = new DeveloperTask;
        $task->priority = $issue->priority;
        $task->task = $issue->issue;
        $task->user_id = $request->user_id;
        $task->status = 'Planned';
        $task->save();
        foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
            $task->attachMedia($image, config('constants.media_tags'));
        }
        $issue->user_id = $request->user_id;
        $issue->save();
        $issue->delete();

        return redirect()->back()->with('success', 'You have successfully assigned the issue!');
    }

    public function moduleAssign(ModuleAssignDevelopmentRequest $request, $id): RedirectResponse
    {
        $module = DeveloperTask::find($id);
        $module->user_id = $request->user_id;
        $module->module = 0;
        $module->save();

        return redirect()->route('development.index')->with('success', 'You have successfully assigned the module!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDevelopmentRequest $request, int $id): RedirectResponse
    {
        $data = $request->except('_token');
        $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

        $task = DeveloperTask::find($id);
        $task->update($data);
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('developertask/'.floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
    }

    public function updateCost(Request $request): \Illuminate\Http\Response
    {
        $task = DeveloperTask::find($request->id);
        if ($task->user_id == Auth::id()) {
            $task->cost = $request->cost;
            $task->save();
        }

        return response('success');
    }

    public function updateStatus(Request $request, $id): \Illuminate\Http\Response
    {
        $task = DeveloperTask::find($id);
        $task->status = $request->status;
        if ($request->status == 'In Progress') {
            $task->start_time = Carbon::now();
        }
        if ($request->status == 'Done') {
            $task->end_time = Carbon::now();
        }
        $task->save();

        return response('success');
    }

    public function updateTask(Request $request, $id): \Illuminate\Http\Response
    {
        $task = DeveloperTask::find($id);
        $task->task = $request->task;
        $task->save();

        return response('success');
    }

    public function updatePriority(Request $request, $id): JsonResponse
    {
        $task = DeveloperTask::find($id);
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            'priority' => $task->priority,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->completed = 1;
        $task->save();
        $notifications = PushNotification::where('model_type', DeveloperTask::class)->where('model_id', $task->id)->where('isread', 0)->get();
        foreach ($notifications as $notification) {
            $notification->isread = 1;
            $notification->save();
        }
        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('development.index')->with('success', 'You have successfully verified the task!');
    }

    public function verifyView(Request $request): RedirectResponse
    {
        $task = DeveloperTask::find($request->id);
        PushNotification::where('model_type', DeveloperTask::class)->where('model_id', $request->id)->delete();
        if ($request->tab) {
            return redirect(url("/development#task_$request->id"));
        } else {
            return redirect(url("/development?user=$request->user#task_$task->id"));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $task = DeveloperTask::find($id);
        $task->development_details()->delete();
        $task->delete();
        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id): RedirectResponse
    {
        DeveloperTask::find($id)->delete();

        return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }

    public function moduleDestroy($id): RedirectResponse
    {
        $module = DeveloperModule::find($id);
        foreach ($module->tasks as $task) {
            $task->module_id = '';
            $task->save();
        }
        $module->delete();

        return redirect()->route('development.index')->with('success', 'You have successfully archived the module!');
    }

    public function assignUser(Request $request): JsonResponse
    {
        $issue = DeveloperTask::find($request->get('issue_id'));

        $slotAvailable = $this->userSchedulesLoadData($request->get('assigned_to'));

        if (! empty($slotAvailable)) {
            $issue->status = 'Planned';
            $issue->start_date = $slotAvailable['st'];
            $issue->estimate_date = $slotAvailable['en'];
        }

        $user = User::find($request->get('assigned_to'));

        if (! $user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found',
            ], 500);
        }

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $request->get('assigned_to'))->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }

        $summary = substr($issue->task, 0, 200);
        if ($issue->task_type_id == 1) {
            $taskSummery = '#DEVTASK-'.$issue->id.' => '.$summary;
        } else {
            $taskSummery = '#TASK-'.$issue->id.' => '.$summary;
        }
        if ($hubstaffUserId) {
            $hubstaffTaskId = $this->createHubstaffTask(
                $taskSummery,
                $hubstaffUserId,
                $hubstaff_project_id
            );
            if ($hubstaffTaskId) {
                $issue->hubstaff_task_id = $hubstaffTaskId;
                $issue->save();

                $task = new HubstaffTask;
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->project_id = $hubstaff_project_id;
                $task->hubstaff_project_id = $hubstaff_project_id;
                $task->summary = $taskSummery;
                $task->save();
            }
        }

        $old_id = $issue->assigned_to;
        if (! $old_id) {
            $old_id = 0;
        }
        $issue->assigned_to = $request->get('assigned_to');
        $issue->save();

        $taskUser = new TaskUserHistory;
        $taskUser->model = DeveloperTask::class;
        $taskUser->model_id = $issue->id;
        $taskUser->old_id = $old_id;
        $taskUser->new_id = $request->get('assigned_to');
        $taskUser->user_type = 'developer';
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function userSchedulesLoadData($user_id)
    {
        $isPrint = ! request()->ajax();

        $stDate = date('Y-m-d');
        $enDate = date('Y-m-d', strtotime(' + 30 days'));
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

            $q->where('users.id', $user_id);

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

            if ($count) {
                $filterDatesOnly = array_column($filterDates, 'date');

                $userIds = [];

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
                if ($userIds) {
                    $tasksInProgress = $this->typeWiseTasks('IN_PROGRESS', [
                        'userIds' => $userIds,
                    ]);
                    $tasksPlanned = $this->typeWiseTasks('PLANNED', [
                        'userIds' => $userIds,
                    ]);

                    if ($tasksInProgress) {
                        foreach ($tasksInProgress as $task) {
                            $task->st_date = date('Y-m-d H:i:00', strtotime($task->st_date));

                            if (! isset($task->en_date)) {
                                $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date.' + '.$task->est_minutes.'minutes'));
                            }

                            $tasksArr[$task->assigned_to][$task->status2][] = [
                                'id' => $task->id,
                                'typeId' => $task->type.'-'.$task->id,
                                'stDate' => $task->st_date,
                                'enDate' => $task->en_date,
                                'status' => $task->status,
                                'status2' => $task->status2,
                                'mins' => $task->est_minutes,
                                'manually_assign' => $task->manually_assign,
                            ];
                        }
                    }
                    if ($tasksPlanned) {
                        foreach ($tasksPlanned as $task) {
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
                            ];
                        }
                    }
                }
                if ($isPrint) {
                    _p($tasksArr);
                }

                // Arrange tasks on users slots
                foreach ($userArr as $k1 => $user) {
                    $userTasksArr = isset($tasksArr[$user['id']]) && count($tasksArr[$user['id']]) ? $tasksArr[$user['id']] : [];
                    if ($user['uaId'] && isset($user['availableSlots']) && count($user['availableSlots'])) {
                        foreach ($user['availableSlots'] as $date => $slots) {
                            foreach ($slots as $k2 => $slot) {
                                if ($slot['type'] == 'AVL' || $slot['slot_type'] == 'AVL') {
                                    $res = $this->slotIncreaseAndShift($slot, $userTasksArr);

                                    $slot['taskIds'] = $res['taskIds'] ?? [];
                                    $slot['userTasks'] = $res['userTasks'] ?? [];
                                }
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
                            foreach ($slots as $slot) {
                                if (in_array($slot['type'], ['AVL', 'SMALL-LUNCH', 'LUNCH-START', 'LUNCH-END']) && $slot['slot_type'] != 'PAST') {
                                    $ut_array = [];
                                    $ut_arrayManually = [];

                                    if (! empty($slot['userTasks'])) {
                                        foreach ($slot['userTasks'] as $ut) {
                                            if ($ut['manually_assign'] == 1) {
                                                $ut_arrayManually[] = $ut['typeId'];
                                            } else {
                                                $ut_array[] = $ut['typeId'];
                                            }
                                        }
                                    } else {
                                        if ($slot['type'] == 'AVL') {
                                            return $slot;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function typeWiseTasks($type, $wh = [])
    {
        $userIds = $wh['userIds'] ?? [0];
        $taskStatuses = [0];
        $devTaskStatuses = ['none'];

        if ($type == 'IN_PROGRESS') {
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
                    task_subject AS title, 
                    start_date AS st_date, 
                    due_date AS en_date, 
                    COALESCE(approximate, 0) AS est_minutes, 
                    status,
                    (
                        CASE
                            WHEN status = '".Task::TASK_STATUS_IN_PROGRESS."' THEN 'IN_PROGRESS'
                            WHEN status = '".Task::TASK_STATUS_PLANNED."' THEN 'PLANNED'
                        END
                    ) AS status2
                FROM 
                    tasks 
                WHERE 
                1
                AND (
                    ( status = '".Task::TASK_STATUS_IN_PROGRESS."' AND start_date IS NOT NULL )
                    OR 
                    ( status != '".Task::TASK_STATUS_IN_PROGRESS."' )
                )
                AND deleted_at IS NULL
                AND assign_to IN (".implode(',', $userIds).") 
                AND status IN ('".implode("','", $taskStatuses)."') 
            )
            UNION
            (
                SELECT 
                    id, 
                    'DT' AS type, 
                    assigned_to AS assigned_to, 
                    manually_assign, 
                    subject AS title, 
                    start_date AS st_date, 
                    estimate_date AS en_date, 
                    COALESCE(estimate_minutes, 0) AS est_minutes, 
                    status,
                    (
                        CASE
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS."' THEN 'IN_PROGRESS'
                            WHEN status = '".DeveloperTask::DEV_TASK_STATUS_PLANNED."' THEN 'PLANNED'
                        END
                    ) AS status2
                FROM developer_tasks
                WHERE 1
                AND (
                    ( status = '".DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS."' AND start_date IS NOT NULL )
                    OR 
                    ( status != '".DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS."' )
                )
                AND deleted_at IS NULL
                AND assigned_to IN (".implode(',', $userIds).")
                AND status IN ('".implode("','", $devTaskStatuses)."')
            )
        ) AS listdata
        ORDER BY listdata.st_date ASC";

        $tasks = DB::select($sql, []);

        return $tasks;
    }

    public function slotIncreaseAndShift($slot, $tasks)
    {
        // IN_PROGRESS, PLANNED

        $taskIds = [];
        $userTasks = [];

        if ($tasks) {
            if ($list = ($tasks['IN_PROGRESS'] ?? [])) {
                foreach ($list as $task) {
                    $SlotStart = Carbon::parse($slot['st']);
                    $SlotEnd = Carbon::parse($slot['en']);
                    $TaskStart = Carbon::parse($task['stDate']);
                    $TaskEnd = Carbon::parse($task['enDate']);

                    if (
                        $TaskStart->between($SlotStart, $SlotEnd) ||  // Task starts within the slot
                        $TaskEnd->between($SlotStart, $SlotEnd) ||    // Task ends within the slot
                        ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd)) // Task spans the entire slot
                    ) {
                        $userTasks[] = $task;
                    }
                }
                $list = array_values($list);
                $tasks['IN_PROGRESS'] = $list;
            }

            if ($list = ($tasks['PLANNED'] ?? [])) {
                foreach ($list as $k => $task) {
                    $SlotStart = Carbon::parse($slot['st']);
                    $SlotEnd = Carbon::parse($slot['en']);
                    $TaskStart = Carbon::parse($task['stDate']);
                    $TaskEnd = Carbon::parse($task['enDate']);

                    if (
                        ($TaskStart->gte($SlotStart) && $TaskStart->lte($SlotEnd)) ||
                        ($TaskEnd->gte($SlotStart) && $TaskEnd->lte($SlotEnd)) ||
                        ($TaskStart->lte($SlotStart) && $TaskEnd->gte($SlotEnd))
                    ) {
                        $userTasks[] = $task;
                    }
                }
                $list = array_values($list);
                $tasks['PLANNED'] = $list;
            }
        }

        return [
            'taskIds' => $taskIds ?? [],
            'userTasks' => $userTasks ?? [],
        ];
    }

    public function assignMasterUser(Request $request): JsonResponse
    {
        $masterUserId = $request->get('master_user_id');
        $issue = DeveloperTask::find($request->get('issue_id'));

        $old_hubstaff_id = $issue->lead_hubstaff_task_id;

        $user = User::find($masterUserId);

        if (! $user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found',
            ], 500);
        }
        $old_id = $issue->master_user_id;
        if (! $old_id) {
            $old_id = 0;
        }
        $issue->master_user_id = $masterUserId;

        $issue->save();

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $masterUserId)->first();
        $hubstaffUserId = null;

        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }

        $summary = substr($issue->task, 0, 200);
        if ($issue->task_type_id == 1) {
            $taskSummery = '#DEVTASK-'.$issue->id.' => '.$summary;
        } else {
            $taskSummery = '#TASK-'.$issue->id.' => '.$summary;
        }
        if ($hubstaffUserId) {
            $hubstaffTaskId = $this->createHubstaffTask(
                $taskSummery,
                $hubstaffUserId,
                $hubstaff_project_id
            );

            if ($hubstaffTaskId) {
                $issue->lead_hubstaff_task_id = $hubstaffTaskId;
                $issue->save();

                $task = new HubstaffTask;
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->project_id = $hubstaff_project_id;
                $task->hubstaff_project_id = $hubstaff_project_id;
                $task->summary = $taskSummery;
                $task->save();
            }
        }

        $taskUser = new TaskUserHistory;
        $taskUser->model = DeveloperTask::class;
        $taskUser->model_id = $issue->id;
        $taskUser->old_id = $old_id;
        $taskUser->new_id = $masterUserId;
        $taskUser->user_type = 'leaddeveloper';
        $taskUser->master_user_hubstaff_task_id = $old_hubstaff_id;
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function assignTeamlead(Request $request): JsonResponse
    {
        $team_lead_id = $request->get('team_lead_id');
        $issue = DeveloperTask::find($request->get('issue_id'));

        $user = User::find($team_lead_id);

        if (! $user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found',
            ], 500);
        }

        $isMember = $user->teams()->first();
        if ($isMember) {
            return response()->json([
                'message' => 'This user is already a team member',
            ], 500);
        } else {
            $isLeader = Team::where('user_id', $team_lead_id)->first();
            if (! $isLeader) {
                $team = new Team;
                $team->name = $request->name;
                $team->user_id = $team_lead_id;
                $team->save();
            }
            $issue->team_lead_id = $team_lead_id;
            $issue->save();
        }

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function assignTester(Request $request): JsonResponse
    {
        $tester_id = $request->get('tester_id');
        $issue = DeveloperTask::find($request->get('issue_id'));

        $user = User::find($tester_id);

        if (! $user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found',
            ], 500);
        }
        $old_id = $issue->tester_id;
        if (! $old_id) {
            $old_id = 0;
        }
        $issue->tester_id = $tester_id;
        $issue->save();

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $tester_id)->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }

        $summary = substr($issue->task, 0, 200);
        if ($issue->task_type_id == 1) {
            $taskSummery = '#DEVTASK-'.$issue->id.' => '.$summary;
        } else {
            $taskSummery = '#TASK-'.$issue->id.' => '.$summary;
        }
        if ($hubstaffUserId) {
            $hubstaffTaskId = $this->createHubstaffTask(
                $taskSummery,
                $hubstaffUserId,
                $hubstaff_project_id
            );
            if ($hubstaffTaskId) {
                $issue->tester_hubstaff_task_id = $hubstaffTaskId;
                $issue->save();

                $task = new HubstaffTask;
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->project_id = $hubstaff_project_id;
                $task->hubstaff_project_id = $hubstaff_project_id;
                $task->summary = $taskSummery;
                $task->save();
            }
        }

        $taskUser = new TaskUserHistory;
        $taskUser->model = DeveloperTask::class;
        $taskUser->model_id = $issue->id;
        $taskUser->old_id = $old_id;
        $taskUser->new_id = $tester_id;
        $taskUser->user_type = 'tester';
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function assignResponsibleUser(Request $request): JsonResponse
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        $issue->assigned_by = \Auth::id();
        $issue->responsible_user_id = $request->get('responsible_user_id');
        $issue->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function saveMilestone(Request $request): JsonResponse
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        if (! $issue->is_milestone) {
            return response()->json([
                'message' => 'Milestone not set',
            ], 500);
        }
        $total = $request->total;
        if ($issue->milestone_completed) {
            if ($total <= $issue->milestone_completed) {
                return response()->json([
                    'message' => 'Milestone no can\'t be reduced',
                ], 500);
            }
        }

        if ($total > $issue->no_of_milestone) {
            return response()->json([
                'message' => 'Estimated milestone exceeded',
            ], 500);
        }
        if (! $issue->cost || $issue->cost == '') {
            return response()->json([
                'message' => 'Please provide cost first',
            ], 500);
        }

        $newCompleted = $total - $issue->milestone_completed;
        $individualPrice = $issue->cost / $issue->no_of_milestone;
        $totalCost = $individualPrice * $newCompleted;

        $issue->milestone_completed = $total;
        $issue->save();
        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date('Y-m-d');
        $payment_receipt->worked_minutes = $issue->estimate_minutes;
        $payment_receipt->rate_estimated = $totalCost;
        $payment_receipt->status = 'Pending';
        $payment_receipt->developer_task_id = $issue->id;
        $payment_receipt->user_id = $issue->assigned_to;
        $payment_receipt->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function resolveIssue(Request $request): JsonResponse
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        if ($issue->is_resolved == 1) {
            return response()->json([
                'message' => 'DONE Status can not change further.',
            ], 500);
        }
        if (strtolower($request->get('is_resolved')) == 'done') {
            if (Auth::user()->isAdmin()) {
                $old_status = $issue->status;
                $issue->status = $request->get('is_resolved');
                $assigned_to = User::find($issue->assigned_to);
                if (! $assigned_to) {
                    return response()->json([
                        'message' => 'Please assign the task.',
                    ], 500);
                }
                $team_user = TeamUser::where('user_id', $issue->assigned_to)->first();
                if ($team_user) {
                    $team_lead = Team::where('id', $team_user->team_id)->first();
                    if ($team_lead) {
                        $dev_task_user = User::find($team_lead->user_id);
                        $is_team = 1;
                    }
                }
                if (empty($dev_task_user)) {
                    $dev_task_user = $assigned_to;
                }
                if ($dev_task_user && $dev_task_user->fixed_price_user_or_job == 0) {
                    return response()->json([
                        'message' => 'Please provide salary payment method for '.$dev_task_user->name.' .',
                    ], 500);
                }
                if ($dev_task_user && $dev_task_user->fixed_price_user_or_job == 1) {
                    $userRate = UserRate::getRateForUser($dev_task_user->id);
                    // Fixed price task.
                    if ($issue->cost == null) {
                        return response()->json([
                            'message' => 'Please provide cost for fixed price task.',
                        ], 500);
                    }

                    if (! $issue->is_milestone) {
                        $payment_receipt = new PaymentReceipt;
                        $payment_receipt->date = date('Y-m-d');
                        $payment_receipt->worked_minutes = $issue->estimate_minutes;
                        $payment_receipt->rate_estimated = $issue->cost;
                        $payment_receipt->status = 'Pending';
                        $payment_receipt->currency = ($userRate->currency ?? 'USD');
                        $payment_receipt->developer_task_id = $issue->id;
                        $payment_receipt->user_id = $dev_task_user->id;
                        $payment_receipt->by_command = 3;
                        $payment_receipt->save();
                    }
                } elseif ($dev_task_user && $dev_task_user->fixed_price_user_or_job == 2) {
                    $userRate = UserRate::getRateForUser($dev_task_user->id);

                    if ($userRate && $userRate->hourly_rate !== null) {
                        if ($issue->estimate_minutes) {
                            if ($issue->ApprovedDeveloperTaskHistory) {
                                $rate_estimated = ($issue->estimate_minutes) * ($userRate->hourly_rate) / 60;
                            } else {
                                return response()->json([
                                    'message' => 'Estimated time is not approved.',
                                ], 500);
                            }
                        } else {
                            return response()->json([
                                'message' => 'Estimated time is not exist.',
                            ], 500);
                        }
                    } else {
                        return response()->json([
                            'message' => 'Please provide hourly rate for '.$dev_task_user->name.' .',
                        ], 500);
                    }
                    $payment_receipt = new PaymentReceipt;
                    $payment_receipt->date = date('Y-m-d');
                    $payment_receipt->worked_minutes = $issue->estimate_minutes;
                    $payment_receipt->rate_estimated = $rate_estimated;
                    $payment_receipt->status = 'Pending';
                    $payment_receipt->currency = ($userRate->currency ?? 'USD');
                    $payment_receipt->developer_task_id = $issue->id;
                    $payment_receipt->user_id = $dev_task_user->id;
                    $payment_receipt->by_command = 2;
                    $payment_receipt->save();
                }
                $issue->responsible_user_id = $issue->assigned_to;
                $issue->is_resolved = 1;
                $issue->save();

                DeveloperTaskHistory::create([
                    'developer_task_id' => $issue->id,
                    'model' => DeveloperTask::class,
                    'attribute' => 'task_status',
                    'old_value' => $old_status,
                    'new_value' => $request->is_resolved,
                    'user_id' => Auth::id(),
                ]);
            } else {
                return response()->json([
                    'message' => 'Only admin can change status to DONE.',
                ], 500);
            }
        } else {
            $old_status = $issue->status;

            DeveloperTaskHistory::create([
                'developer_task_id' => $issue->id,
                'model' => DeveloperTask::class,
                'attribute' => 'task_status',
                'old_value' => $old_status,
                'new_value' => $request->is_resolved,
                'user_id' => Auth::id(),
            ]);

            $issue->status = $request->get('is_resolved');

            if ($issue->status == DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS) {
                if ($issue->actual_start_date == null || $issue->actual_start_date == '0000-00-00 00:00:00') {
                    $issue->actual_start_date = date('Y-m-d H:i:s');
                }
            }
            if ($issue->status == DeveloperTask::DEV_TASK_STATUS_DONE) {
                $issue->actual_end_date = date('Y-m-d H:i:s');
            }
            if ($issue->status == DeveloperTask::DEV_TASK_STATUS_USER_COMPLETE) {
                if (isset($request->checklist)) {
                    $statusMsg = 'Status has been updated : From '.$old_status.' To '.DeveloperTask::DEV_TASK_STATUS_USER_COMPLETE."\n";
                    $msg = '';
                    foreach ($request->checklist as $key => $list) {
                        $checkList = DeveloperTaskStatusChecklist::find($key);
                        if (! empty($checkList)) {
                            DeveloperTaskStatusChecklistRemarks::create([
                                'user_id' => Auth::id(),
                                'task_id' => $issue->id,
                                'developer_task_status_checklist_id' => $key,
                                'remark' => $list,
                            ]);
                            $msg .= $checkList['name'].' => '.$list."\n";
                        }
                    }

                    $message = ! empty($msg) ? $statusMsg.$msg : '';

                    if (! empty($message)) {
                        ChatMessage::create([
                            'user_id' => Auth::user()->id,
                            'developer_task_id' => $issue->id,
                            'sent_to_user_id' => $issue->user_id,
                            'message' => $message,
                            'status' => 2,
                            'approved' => 1,
                        ]);
                    }
                }
            }

            $issue->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function saveEstimateTime(Request $request): JsonResponse
    {
        $issue = DeveloperTaskHistory::where(['developer_task_id' => $request->get('issue_id'), 'attribute' => 'estimation_minute', 'user_id' => Auth::user()->id])->orderByDesc('id')->first();
        if ($issue->count() > 0) {
            $task_history = new DeveloperTaskHistory;
            $task_history->developer_task_id = $request->get('issue_id');
            $task_history->attribute = 'estimation_minute';
            $task_history->old_value = $issue->new_value;
            $task_history->new_value = $request->get('estimate_time');
            $task_history->user_id = Auth::user()->id();
            $task_history->developer_task_id = $request->name;
            $task_history->model = DeveloperTask::class;
            $result = $task_history->save();
        } else {
            $task_history = new DeveloperTaskHistory;
            $task_history->developer_task_id = $request->get('issue_id');
            $task_history->attribute = 'estimation_minute';
            $task_history->old_value = 0;
            $task_history->new_value = $request->get('estimate_time');
            $task_history->user_id = Auth::user()->id();
            $task_history->developer_task_id = $request->name;
            $task_history->model = DeveloperTask::class;
            $result = $task_history->save();
        }

        return response()->json([
            'status' => 'success', 'result' => $result,
        ]);
    }

    public function approveTimeHistory(Request $request): JsonResponse
    {
        if (Auth::user()->isAdmin) {
            if (! $request->approve_time || $request->approve_time == '' || ! $request->developer_task_id || $request->developer_task_id == '') {
                return response()->json([
                    'message' => 'Select one time first',
                ], 500);
            }
            DeveloperTaskHistory::where('developer_task_id', $request->developer_task_id)->where('attribute', 'estimation_minute')->where('model', DeveloperTask::class)->update(['is_approved' => 0]);
            $history = DeveloperTaskHistory::find($request->approve_time);
            $history->is_approved = 1;
            $history->save();

            if ($history) {
                if ($history->old_value == null) {
                    $old_val = '';
                } else {
                    $old_val = $history->old_value;
                }

                $param = [
                    'developer_task_id' => $history->developer_task_id,
                    'old_value' => $old_val,
                    'new_value' => $history->new_value,
                    'user_id' => \Auth::id(),
                ];
            }

            $task = DeveloperTask::find($request->developer_task_id);
            $task->status = DeveloperTask::DEV_TASK_STATUS_APPROVED;
            $task->save();

            $time = $history->new_value !== null ? $history->new_value : $history->old_value;
            $msg = 'TIME APPROVED FOR TASK '.'#DEVTASK-'.$task->id.'-'.$task->subject.' - '.$time.' MINS';

            $user = User::find($request->user_id);
            $admin = Auth::user();
            $master_user = User::find($task->master_user_id);
            $team_lead = User::find($task->team_lead_id);
            $tester = User::find($task->tester_id);

            if ($user) {
                if ($admin->phone) {
                    $chat = ChatMessage::create([
                        'number' => $admin->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id,
                    ]);
                } elseif ($user->phone) {
                    $chat = ChatMessage::create([
                        'number' => $user->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id,
                    ]);
                } elseif ($master_user && $master_user->phone) {
                    $chat = ChatMessage::create([
                        'number' => $master_user->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id,
                    ]);
                } elseif ($team_lead && $team_lead->phone) {
                    $chat = ChatMessage::create([
                        'number' => $team_lead->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id,
                    ]);
                } elseif ($tester && $tester->phone) {
                    $chat = ChatMessage::create([
                        'number' => $tester->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id,
                    ]);
                }
                if (isset($chat)) {
                    if ($admin->phone) {
                        app(WhatsAppController::class)->sendWithThirdApi($admin->phone, $admin->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($user->phone) {
                        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($master_user && $master_user->phone) {
                        app(WhatsAppController::class)->sendWithThirdApi($master_user->phone, $master_user->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($team_lead && $team_lead->phone) {
                        app(WhatsAppController::class)->sendWithThirdApi($team_lead->phone, $team_lead->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($tester && $tester->phone) {
                        app(WhatsAppController::class)->sendWithThirdApi($tester->phone, $tester->whatsapp_number, $msg, false, $chat->id);
                    }
                }
            }
        } else {
            return response()->json([
                'message' => 'Only admin can approve',
            ], 500);
        }
    }

    public function sendRemindMessage(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = DeveloperTask::find($request->id);
                $msg = 'PLS ADD ESTIMATED TIME FOR TASK  '.'#DEVTASK-'.$task->id.'-'.$task->subject;
                $chat = ChatMessage::create([
                    'number' => $receiver_user_phone,
                    'user_id' => $user->id,
                    'customer_id' => $user->id,
                    'message' => $msg,
                    'status' => 0,
                    'developer_task_id' => $request->id,
                ]);

                app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->assigned_to, $task->team_lead_id, $task->tester_id], $msg);
            }
        }

        return response()->json([
            'message' => 'Remind message sent successfully',
        ]);
    }

    public function sendReviseMessage(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = DeveloperTask::find($request->id);
                $msg = 'TIME NOT APPROVED REVISE THE ESTIMATED TIME FOR TASK '.'#DEVTASK-'.$task->id.'-'.$task->subject;
                $chat = ChatMessage::create([
                    'number' => $receiver_user_phone,
                    'user_id' => $user->id,
                    'customer_id' => $user->id,
                    'message' => $msg,
                    'status' => 0,
                    'developer_task_id' => $request->id,
                ]);
                app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->assigned_to, $task->team_lead_id, $task->tester_id], $msg);
            }
        }

        return response()->json([
            'message' => 'Revise message sent successfully',
        ]);
    }

    public function savePriorityNo(Request $request): JsonResponse
    {
        $issue = DeveloperTask::find($request->get('issue_id'));

        if ($issue) {
            $issue->priority_no = $request->get('priority');
            $issue->save();
        }

        return response()->json(['status' => 'success']);
    }

    public function updateValues(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->get('id'));
        $type = $request->get('type');
        $value = $request->get('value');
        if ($type == 'start_date') {
            $task->start_date = $value;
        } else {
            if ($type == 'end_date') {
                $task->end_date = $value;
            } else {
                if ($type == 'estimate_date') {
                    $task->estimate_date = $value;
                } else {
                    if ($type == 'cost') {
                        $task->cost = $value;
                    } else {
                        if ($type == 'module') {
                            $task->module_id = $value;
                        }
                    }
                }
            }
        }
        $task->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function overview(Request $request): \Illuminate\View\View
    {
        // Get status
        $status = $request->get('status');
        if (empty($status)) {
            $status = 'In Progress';
        }
        $task_type = 1;
        $taskTypes = TaskTypes::all();
        $users = Helpers::getUsersByRoleName('Developer');
        if (! empty($request->get('task_type'))) {
            $task_type = $request->get('task_type');
        }
        if (! empty($request->get('task_status'))) {
            $status = $request->get('task_status');
        }
        if (! empty($request->get('task_type')) && ! empty($request->get('task_status'))) {
            $status = $request->get('task_status');
            $task_type = $request->get('task_type');
        }

        return view('development.overview', [
            'taskTypes' => $taskTypes,
            'users' => $users,
            'status' => $status,
            'task_type' => $task_type,
        ]);
    }

    public function taskDetail($taskId): \Illuminate\View\View
    {
        // Get tasks
        $task = DeveloperTask::where('developer_tasks.id', $taskId)
            ->select('developer_tasks.*', 'task_types.name as task_type', 'users.name as username', 'u.name as reporter')
            ->leftjoin('task_types', 'task_types.id', '=', 'developer_tasks.task_type_id')
            ->leftjoin('users', 'users.id', '=', 'developer_tasks.user_id')
            ->leftjoin('users AS u', 'u.id', '=', 'developer_tasks.created_by')
            ->first();
        // Get subtasks
        $subtasks = DeveloperTask::where('developer_tasks.parent_id', $taskId)->get();
        // Get comments
        $comments = DeveloperTaskComment::where('task_id', $taskId)
            ->join('users', 'users.id', '=', 'developer_task_comments.user_id')
            ->get();
        //Get Attachments
        $attachments = TaskAttachment::where('task_id', $taskId)->get();
        $developers = Helpers::getUserArray(User::role('Developer')->get());

        // Return view
        return view('development.task_detail', [
            'task' => $task,
            'subtasks' => $subtasks,
            'comments' => $comments,
            'developers' => $developers,
            'attachments' => $attachments,
        ]);
    }

    public function taskComment(TaskCommentDevelopmentRequest $request)
    {
        $response = [];
        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        $created = DeveloperTaskComment::create($data);
        if ($created) {
            $response['status'] = 'ok';
            $response['msg'] = 'Comment stored successfully';
            echo json_encode($response);
        } else {
            $response['status'] = 'error';
            $response['msg'] = 'Error';
        }
    }

    public function changeTaskStatus(Request $request): JsonResponse
    {
        if (! empty($request->input('task_id'))) {
            $task = DeveloperTask::find($request->input('task_id'));
            $task->status = $request->input('status');
            $task->save();

            return response()->json(['success']);
        }
    }

    public function makeDirectory($path, $mode = 0777, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        } else {
            return mkdir($path, $mode, $recursive);
        }
    }

    public function uploadAttachDocuments(Request $request): RedirectResponse
    {
        $task_id = $request->input('task_id');
        $task = DeveloperTask::find($task_id);
        if ($request->hasfile('attached_document')) {
            foreach ($request->file('attached_document') as $image) {
                $new_id = floor($task_id / 1000);

                $dirname = public_path().'/uploads/developer-task/'.$new_id;
                if (file_exists($dirname)) {
                    $dirname2 = public_path().'/uploads/developer-task/'.$new_id.'/'.$task_id;
                    if (file_exists($dirname2) == false) {
                        mkdir($dirname2, 0777);
                    }
                } else {
                    mkdir($dirname, 0777);
                }
                $media = MediaUploader::fromSource($image)->toDirectory("developer-task/$new_id/$task_id")->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }
        if (! empty($request->file('attached_document'))) {
            foreach ($request->file('attached_document') as $file) {
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('images/task_files/'), $name);
                $task_attachment = new TaskAttachment;
                $task_attachment->task_id = $task_id;
                $task_attachment->name = $name;
                $task_attachment->save();
            }

            return redirect(url("/development/task-detail/$task_id"));
        } else {
            return redirect(url("/development/task-detail/$task_id"));
        }
    }

    public function downloadFile(Request $request): BinaryFileResponse
    {
        $file_name = $request->input('file_name');
        $file = public_path().'/images/task_files/'.$file_name;
        $ext = substr($file_name, strrpos($file_name, '.') + 1);
        $headers = [];
        if ($ext == 'pdf') {
            $headers = [
                'Content-Type: application/pdf',
            ];
        }

        return Response::download($file, $file_name, $headers);
    }

    public function openNewTaskPopup(Request $request)
    {
        $status = 'ok';
        // Get all developers
        if (config('app.env')) {
            $userlst = User::role('Developer')->orderBy('name')->get(); // Production
        } else {
            $userlst = User::orderBy('name')->get(); // Local system
        }
        $users = Helpers::getUserArray($userlst);
        // Get all task types
        $tasksTypes = TaskTypes::all();
        $moduleNames = [];

        // Get all modules
        $modules = DeveloperModule::orderBy('name')->get();

        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[$module->id] = $module->name;
        }

        // this is the ID for erp
        $defaultRepositoryId = 231925646;

        $githubOrganizations = GithubOrganization::get();

        $statusList = TaskStatus::orderBy('name')
            ->select('name')
            ->pluck('name', 'name')
            ->toArray();

        $statusList = array_merge([
            '' => 'Select Status',
        ], $statusList);

        //Get hubstaff projects
        $projects = HubstaffProject::all();

        $html = view('development.ajax.add_new_task', compact('users', 'tasksTypes', 'modules', 'moduleNames', 'githubOrganizations', 'defaultRepositoryId', 'projects', 'statusList'))->render();

        return json_encode(compact('html', 'status'));
    }

    public function saveLanguage(Request $request): JsonResponse
    {
        $language = $request->get('language');

        if (! empty(trim($language))) {
            if (! is_numeric($language)) {
                $languageModal = DeveloperLanguage::updateOrCreate(
                    ['name' => $language],
                    ['name' => $language]
                );
            }

            $issue = DeveloperTask::find($request->get('issue_id'));
            $issue->language = isset($languageModal->id) ? $languageModal->id : $language;
            $issue->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $id = $request->get('developer_task_id', 0);
        $subject = $request->get('subject', null);

        $loggedUser = $request->user();

        if ($id > 0 && ! empty($subject)) {
            $devTask = DeveloperTask::find($id);

            if (! empty($devTask)) {
                $devDocuments = new DeveloperTaskDocument;
                $devDocuments->fill(request()->all());
                $devDocuments->created_by = \Auth::id();
                $devDocuments->save();

                if ($request->hasfile('files')) {
                    foreach ($request->file('files') as $files) {
                        $media = MediaUploader::fromSource($files)
                            ->toDirectory('developertask/'.floor($devTask->id / config('constants.image_per_folder')))
                            ->upload();
                        $devDocuments->attachMedia($media, config('constants.media_tags'));
                    }

                    $message = '[ '.$loggedUser->name.' ] - #DEVTASK-'.$devTask->id.' - '.$devTask->subject." \n\n".'New attchment(s) called '.$subject.' has been added. Please check and give your comment or fix it if any issue.';

                    MessageHelper::sendEmailOrWebhookNotification([$devTask->assigned_to, $devTask->team_lead_id, $devTask->tester_id], $message);
                }

                return response()->json(['code' => 200, 'success' => 'Done!']);
            }

            return response()->json(['code' => 500, 'error' => 'Oops, There is no record in database']);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields']);
        }
    }

    public function getDocument(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);

        if ($id > 0) {
            $devDocuments = DeveloperTaskDocument::where('developer_task_id', $id)->latest()->get();

            $mediaTags = config('constants.media_tags'); // Use config variable

            $html = view('development.ajax.document-list', compact('devDocuments', 'mediaTags'))->render();

            return response()->json(['code' => 200, 'data' => $html]);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, id is required field']);
        }
    }

    /**
     * changeModule on  development/list/devtask
     *
     * @ajax Request
     */
    public function changeModule(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $task_module = DeveloperTask::find($request->get('issue_id'));
            if ($task_module) {
                $task_module->module_id = $request->get('module_id');
                if ($task_module->save()) {
                    $message = ['message' => 'success', 'status' => '200'];
                } else {
                    $message = ['message' => 'Error', 'status' => '400'];
                }
            } else {
                $message = ['message' => 'Error', 'status' => '400'];
            }
        } else {
            $message = ['message' => 'Error', 'status' => '400'];
        }

        return response()->json($message);
    }

    public function getTimeHistory(Request $request)
    {
        $id = $request->id;
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')
            ->where('developer_task_id', $id)
            ->where('model', DeveloperTask::class)
            ->where('attribute', 'estimation_minute')
            ->select('developer_tasks_history.*', 'users.name')
            ->orderByDesc('id')
            ->get();

        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function getTimeHistoryApproved(Request $request)
    {
        $id = $request->id;

        $task_module = HubstaffHistory::join('users', 'users.id', 'hubstaff_historys.user_id')->where('developer_task_id', $id)->select('hubstaff_historys.*', 'users.name')->get();

        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function getStatusHistory(Request $request)
    {
        $id = $request->id;
        $type = DeveloperTask::class;
        if (isset($request->type) && $request->type == 'task') {
            $type = Task::class;
        }
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')->where('developer_task_id', $id)->where('model', $type)->where('attribute', 'task_status')->select('developer_tasks_history.*', 'users.name')->get();
        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function getTrackedHistory(Request $request): JsonResponse
    {
        $id = $request->id;
        $type = $request->type;
        if ($type == 'lead') {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at,sum(hubstaff_activities.tracked) as total_tracked,developer_tasks.master_user_id,users.name FROM `hubstaff_activities`  join developer_tasks on developer_tasks.lead_hubstaff_task_id = hubstaff_activities.task_id join users on users.id = developer_tasks.master_user_id where developer_tasks.id = ' . $id . ' group by task_id,starts_at'));

            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'developer_tasks.master_user_id',
                'users.name'
            )
                ->join('developer_tasks', 'developer_tasks.lead_hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'developer_tasks.master_user_id')
                ->where('developer_tasks.id', $id)
                ->groupBy('task_id', 'starts_at')
                ->get();
        } elseif ($type == 'tester') {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at,sum(hubstaff_activities.tracked) as total_tracked,developer_tasks.tester_id,users.name FROM `hubstaff_activities`  join developer_tasks on developer_tasks.tester_hubstaff_task_id = hubstaff_activities.task_id join users on users.id = developer_tasks.tester_id where developer_tasks.id = ' . $id . ' group by task_id,starts_at'));

            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'developer_tasks.tester_id',
                'users.name'
            )
                ->join('developer_tasks', 'developer_tasks.tester_hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'developer_tasks.tester_id')
                ->where('developer_tasks.id', $id)
                ->groupBy('task_id', 'starts_at')
                ->get();
        } else {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at,sum(hubstaff_activities.tracked) as total_tracked,developer_tasks.assigned_to,users.name FROM `hubstaff_activities`  join developer_tasks on developer_tasks.hubstaff_task_id = hubstaff_activities.task_id join users on users.id = developer_tasks.assigned_to where developer_tasks.id = ' . $id . ' group by task_id,starts_at'));

            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'developer_tasks.assigned_to',
                'users.name'
            )
                ->join('developer_tasks', 'developer_tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'developer_tasks.assigned_to')
                ->where('developer_tasks.id', $id)
                ->groupBy('task_id', 'starts_at')
                ->get();
        }

        return response()->json(['histories' => $task_histories]);
    }

    public function createHubstaffManualTask(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->id);
        if ($task) {
            if ($request->task_for == 'hubstaff') {
                if ($request->type == 'developer') {
                    $user_id = $task->assigned_to;
                } elseif ($request->type == 'tester') {
                    $user_id = $task->tester_id;
                } else {
                    $user_id = $task->master_user_id;
                }
                $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

                $assignedUser = HubstaffMember::where('user_id', $user_id)->first();

                $hubstaffUserId = null;
                if ($assignedUser) {
                    $hubstaffUserId = $assignedUser->hubstaff_user_id;
                }
                $taskSummery = '#DEVTASK-'.$task->id.' => '.$task->subject;
                if ($hubstaffUserId) {
                    $hubstaffTaskId = $this->createHubstaffTask(
                        $taskSummery,
                        $hubstaffUserId,
                        $hubstaff_project_id
                    );
                } else {
                    return response()->json([
                        'message' => 'Hubstaff member not found',
                    ], 500);
                }
                if ($hubstaffTaskId) {
                    if ($request->type == 'developer') {
                        $task->hubstaff_task_id = $hubstaffTaskId;
                    } elseif ($request->type == 'tester') {
                        $task->tester_hubstaff_task_id = $hubstaffTaskId;
                    } else {
                        $task->lead_hubstaff_task_id = $hubstaffTaskId;
                    }
                    $task->save();
                } else {
                    return response()->json([
                        'message' => 'Hubstaff task not created',
                    ], 500);
                }
                if ($hubstaffTaskId) {
                    $task = new HubstaffTask;
                    $task->hubstaff_task_id = $hubstaffTaskId;
                    $task->project_id = $hubstaff_project_id;
                    $task->hubstaff_project_id = $hubstaff_project_id;
                    $task->summary = $taskSummery;
                    $task->save();
                }
            } else {
                $timeDoctorTaskResponse = $this->timeDoctorActions('DEVTASK', $task, $request->time_doctor_project, $request->time_doctor_account, $request->assigned_to);
                $errorMessages = config('constants.TIME_DOCTOR_API_RESPONSE_MESSAGE');
                if (! $timeDoctorTaskResponse) {
                    return response()->json(['message' => 'Unable to create task'], 500);
                }
                if ($timeDoctorTaskResponse['code'] != '200') {
                    $message = match ($timeDoctorTaskResponse['code']) {
                        '401' => $errorMessages['401'],
                        '403' => $errorMessages['403'],
                        '409' => $errorMessages['409'],
                        '422' => $errorMessages['422'],
                        '500', '404' => $errorMessages['404'],
                        default => 'Time doctor task created successfully',
                    };

                    return response()->json([
                        'message' => $message,
                    ], $timeDoctorTaskResponse['code']);
                } else {
                    return response()->json([
                        'message' => 'Successful',
                    ]);
                }
            }

            return response()->json([
                'message' => 'Successful',
            ]);
        } else {
            return response()->json([
                'message' => 'Task not found',
            ], 500);
        }
    }

    public function deleteBulkTasks(Request $request): JsonResponse
    {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                DeveloperTask::where('id', $t)->delete();
            }
        }

        return response()->json(['message' => 'Successful']);
    }

    public function getMeetingTimings(Request $request): JsonResponse
    {
        $query = MeetingAndOtherTime::join('users', 'users.id', 'meeting_and_other_times.user_id')->where('model', DeveloperTask::class)->where('model_id', $request->issue_id);
        $issue = DeveloperTask::find($request->issue_id);
        if ($request->type == 'admin') {
            $query = $query;
        } elseif ($request->type == 'developer') {
            $query = $query->where('user_id', $issue->assigned_to);
        } elseif ($request->type == 'lead') {
            $query = $query->where('user_id', $issue->master_user_id);
        } elseif ($request->type == 'tester') {
            $query = $query->where('user_id', $issue->tester_id);
        } else {
            return response()->json(['message' => 'Unauthorized access'], 500);
        }
        if ($request->timing_type && $request->timing_type != '') {
            $query = $query->where('type', $request->timing_type);
        }

        $timings = $query->select('meeting_and_other_times.*', 'users.name')->get();

        $developerTime = MeetingAndOtherTime::where('model', DeveloperTask::class)->where('model_id', $request->issue_id)->where('user_id', $issue->assigned_to)->where('approve', 1)->sum('time');

        $master_devTime = MeetingAndOtherTime::where('model', DeveloperTask::class)->where('model_id', $request->issue_id)->where('user_id', $issue->master_user_id)->where('approve', 1)->sum('time');

        $testerTime = MeetingAndOtherTime::where('model', DeveloperTask::class)->where('model_id', $request->issue_id)->where('user_id', $issue->tester_id)->where('approve', 1)->sum('time');

        return response()->json(['timings' => $timings, 'issue_id' => $request->issue_id, 'developerTime' => $developerTime, 'master_devTime' => $master_devTime, 'testerTime' => $testerTime], 200);
    }

    public function storeMeetingTime(Request $request): JsonResponse
    {
        if (! $request->task_id || $request->task_id == '' || ! $request->time || $request->time == '' || ! $request->user_type || $request->user_type == '' || ! $request->timing_type || $request->timing_type == '') {
            return response()->json(['message' => 'Incomplete data'], 500);
        }
        $query = MeetingAndOtherTime::where('model', DeveloperTask::class)->where('model_id', $request->task_id)->where('type', $request->timing_type);
        $issue = DeveloperTask::find($request->task_id);
        if ($request->user_type == 'developer') {
            $query = $query->where('user_id', $issue->assigned_to);
            $user_id = $issue->assigned_to;
        } elseif ($request->user_type == 'lead') {
            $query = $query->where('user_id', $issue->master_user_id);
            $user_id = $issue->master_user_id;
        } elseif ($request->user_type == 'tester') {
            $query = $query->where('user_id', $issue->tester_id);
            $user_id = $issue->tester_id;
        } else {
            return response()->json(['message' => 'Unauthorized access'], 500);
        }
        $time = $query->orderByDesc('id')->first();
        $oldValue = 0;
        if ($time) {
            $oldValue = $time->time;
        }
        $time = new MeetingAndOtherTime;
        $time->model = DeveloperTask::class;
        $time->model_id = $request->task_id;
        $time->user_id = $user_id;
        $time->time = $request->time;
        $time->old_time = $oldValue;
        $time->type = $request->timing_type;
        $time->note = $request->note;
        $time->updated_by = Auth::user()->name;
        $time->save();

        return response()->json(['message' => 'Successful'], 200);
    }

    public function approveMeetingHistory($task_id, Request $request): JsonResponse
    {
        if (Auth::user()->isAdmin) {
            if (! $request->approve_time || $request->approve_time == '') {
                return response()->json([
                    'message' => 'Select one time first',
                ], 500);
            }
            $time = MeetingAndOtherTime::find($request->approve_time);

            MeetingAndOtherTime::where('model', DeveloperTask::class)->where('model_id', $time->model_id)->where('type', $time->type)->where('user_id', $time->user_id)->update(['approve' => 0]);
            $time->approve = 1;
            $time->save();

            return response()->json([
                'message' => 'Success',
            ], 200);
        }
    }

    public function getUserHistory(Request $request): JsonResponse
    {
        $users = TaskUserHistory::where('model', DeveloperTask::class)->where('model_id', $request->id)->get();
        foreach ($users as $u) {
            $old_name = null;
            $new_name = null;
            if ($u->old_id) {
                $old_name = User::find($u->old_id)->name;
            }
            if ($u->new_id) {
                $new_name = User::find($u->new_id)->name;
            }
            $u->new_name = $new_name;
            $u->old_name = $old_name;
        }

        return response()->json([
            'users' => $users,
        ], 200);
    }

    public function getPullHistory(Request $request): JsonResponse
    {
        $pullrequests = DeveoperTaskPullRequestMerge::where('task_id', $request->id)->get();
        foreach ($pullrequests as $u) {
            $u->user_id = User::find($u->user_id)->name;
        }

        return response()->json([
            'pullrequests' => $pullrequests,
        ], 200);
    }

    public function getLeadTimeHistory(Request $request)
    {
        $id = $request->id;
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')->where('developer_task_id', $id)->where('attribute', 'lead_estimation_minute')->select('developer_tasks_history.*', 'users.name')->get();
        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function updateDevelopmentReminder(Request $request): JsonResponse
    {
        // this is the changes related to developer task
        $task = DeveloperTask::find($request->get('development_id'));
        $task->frequency = $request->get('frequency');
        $task->reminder_message = $request->get('message');
        $task->reminder_from = $request->get('reminder_from', '0000-00-00 00:00');
        $task->reminder_last_reply = $request->get('reminder_last_reply', 0);
        $task->last_send_reminder = date('Y-m-d H:i:s');
        $task->save();

        $message = $request->get('message');
        if ($task->assignedUser?->phone) {
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(['issue_id' => $task->id, 'message' => $message, 'status' => 1]);
            app(WhatsAppController::class)->sendMessage($requestData, 'issue');
        }

        return response()->json([
            'success',
        ]);
    }

    public function changeUser(Request $request): \Illuminate\View\View
    {
        $title = 'Change User';
        $user = $request->user;
        $issues = DeveloperTask::with('timeSpent', 'developerTaskHistory', 'assignedUser', 'masterUser', 'timeSpent', 'leadtimeSpent', 'testertimeSpent', 'messages.taskUser', 'messages.user', 'tester');

        if (Auth::user()->hasRole('Admin') && isset($user) && (int) count($request->user) > 0) {
            $issues = $issues->whereIn('assigned_to', $user);
        }

        $issues = $issues->where('developer_tasks.task_type_id', '1')->whereNotNull('scraper_id');
        $usrlst = User::orderBy('name')->get();
        $users = Helpers::getUserArray($usrlst);
        $issues = $issues->select('developer_tasks.*');
        $issues = $issues->paginate(20);

        return view('development.change_user', [
            'users' => $users,
            'user' => (@$user) ? implode(',', $user) : '',
            'userIds' => (@$user) ? @$user : [],
            'title' => $title,
            'issues' => $issues,
        ]);
    }

    public function changeUserStore(Request $request): RedirectResponse
    {
        if ($request->assign_user_id) {
            $final = [];
            $tasks = DeveloperTask::select()->whereIn('id', explode(',', $request->task_ids))->whereIn('assigned_to', explode(',', $request->assign_user_id))->where('status', 'In Progress')->where('task_type_id', '1')->where('scraper_id', '>', 0)->get();
            if ($tasks) {
                foreach ($tasks as $_task) {
                    $data['priority'] = $_task->priority;
                    $data['subject'] = $_task->subject;
                    $data['task'] = $_task->task;
                    $data['responsible_user_id'] = $_task->responsible_user_id;
                    $data['assigned_to'] = $request->change_user_id;
                    $data['module_id'] = $_task->module_id;
                    $data['user_id'] = $_task->user_id;
                    $data['assigned_by'] = $_task->assigned_by;
                    $data['created_by'] = $_task->created_by;
                    $data['reference'] = $_task->reference;
                    $data['status'] = $_task->status;
                    $data['task_type_id'] = $_task->task_type_id;
                    $data['scraper_id'] = $_task->scraper_id;
                    $data['brand_id'] = $_task->brand_id;
                    $data['parent_id'] = $_task->parent_id;
                    $data['hubstaff_task_id'] = $_task->hubstaff_task_id;
                    $data['estimate_date'] = $_task->estimate_date;
                    $final[] = $data;
                }
            }

            DeveloperTask::insert($final);

            return redirect()->back()->with('success', 'You have successfully change user for the task!');
        }

        return redirect()->back();
    }

    public function getDateHistory(Request $request)
    {
        $id = $request->id;
        $type = DeveloperTask::class;
        if (isset($request->type) && $request->type == 'task') {
            $type = Task::class;
        }
        $task_module = DeveloperTaskHistory::query()
            ->join('users', 'users.id', 'developer_tasks_history.user_id')
            ->where('developer_task_id', $id)
            ->where('model', $type)
            ->where('attribute', 'estimate_date')
            ->select('developer_tasks_history.*', 'users.name')
            ->orderByDesc('developer_tasks_history.id')
            ->get();
        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function taskGet()
    {
        try {
            $errors = reqValidate(request()->all(), [
                'id' => 'required',
            ], []);
            if ($errors) {
                return respJson(400, $errors[0]);
            }

            $single = DeveloperTask::find(request('id'));
            if (! $single) {
                return respJson(404, 'No task found.');
            }

            return respJson(200, '', [
                'data' => $single,
                'user' => $single->assignedUser ?? null,
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function actionStartDateUpdate()
    {
        if ($new = request('value')) {
            try {
                if ($single = DeveloperTask::find(request('id'))) {
                    $params['message'] = 'Estimated Start Datetime: '.$new;
                    $params['user_id'] = Auth::user()->id;
                    $params['developer_task_id'] = $single->id;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $params['sent_to_user_id'] = $single->user_id;
                    ChatMessage::create($params);
                    $single->estimate_date = request('estimatedEndDateTime'); // Assign for validation purpose in below function.
                    $single->updateStartDate($new);

                    return respJson(200, 'Successfully updated.');
                }
            } catch (Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Start date is required.');
    }

    public function saveEstimateDate(Request $request)
    {
        if ($new = request('value')) {
            try {
                if ($single = DeveloperTask::find(request('id'))) {
                    $params['message'] = 'Estimated Start Datetime: '.$new;
                    $params['user_id'] = Auth::user()->id;
                    $params['developer_task_id'] = $single->id;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    $params['sent_to_user_id'] = $single->user_id;
                    ChatMessage::create($params);
                    $single->updateEstimateDate($new);

                    return respJson(200, 'Successfully updated.');
                }
            } catch (Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Estimate date is required.');
    }

    public function saveEstimateDueDate(Request $request)
    {
        if ($new = request('value')) {
            if ($single = DeveloperTask::find(request('id'))) {
                $params['message'] = 'Estimated End Datetime: '.$new;
                $params['user_id'] = Auth::user()->id;
                $params['developer_task_id'] = $single->id;
                $params['approved'] = 1;
                $params['status'] = 2;
                $params['sent_to_user_id'] = $single->user_id;
                ChatMessage::create($params);
                $single->updateEstimateDueDate($new);

                return respJson(200, 'Successfully updated.');
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Due date is required.');
    }

    public function saveAmount(Request $request)
    {
        if ($new = request('value')) {
            if ($single = DeveloperTask::find(request('id'))) {
                $old = $single->cost;

                $single->cost = $new;
                $single->save();
                $params['message'] = 'New Cost: '.$new;
                $params['user_id'] = Auth::user()->id;
                $params['developer_task_id'] = $single->id;
                $params['approved'] = 1;
                $params['status'] = 2;
                $params['sent_to_user_id'] = $single->user_id;
                ChatMessage::create($params);
                DeveloperTaskHistory::create([
                    'developer_task_id' => $single->id,
                    'model' => DeveloperTask::class,
                    'attribute' => 'cost',
                    'old_value' => $old,
                    'new_value' => $new,
                    'user_id' => loginId(),
                ]);

                return respJson(200, 'Successfully updated.');
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Cost is required.');
    }

    public function saveEstimateMinutes(Request $request)
    {
        $new = request('estimate_minutes');
        $remark = request('remark');

        if ($issue = DeveloperTask::find(request('issue_id'))) {
            $old = $issue->estimate_minutes;
            $start_date = $issue->start_date;
            $estimate_date = $issue->estimate_date;

            $issue->estimate_minutes = $new;
            $issue->start_date = $start_date;
            $issue->estimate_date = $estimate_date;

            $issue->status = DeveloperTask::DEV_TASK_STATUS_USER_ESTIMATED;
            $issue->save();
            $params['message'] = 'Estimated Time: '.$new.' Mins, Remark:'.$remark;
            $params['user_id'] = Auth::user()->id;
            $params['developer_task_id'] = $issue->id;
            $params['approved'] = 1;
            $params['status'] = 2;
            $params['sent_to_user_id'] = $issue->user_id;
            ChatMessage::create($params);

            DeveloperTaskHistory::create([
                'developer_task_id' => $issue->id,
                'model' => DeveloperTask::class,
                'attribute' => 'estimation_minute',
                'old_value' => $old,
                'new_value' => $new,
                'remark' => $remark ?: null,
                'user_id' => loginId(),
            ]);

            if (Auth::user()->isAdmin()) {
                $user = User::find($issue->user_id);
                $msg = 'TIME ESTIMATED BY ADMIN FOR TASK '.'#DEVTASK-'.$issue->id.'-'.$issue->subject.' '.$new.' MINS';
            } else {
                $user = User::find($issue->master_user_id);
                $msg = 'TIME ESTIMATED BY USER FOR TASK '.'#DEVTASK-'.$issue->id.'-'.$issue->subject.' '.$new.' MINS';
            }

            if ($user) {
                $receiver_user_phone = $user->phone;
                if ($receiver_user_phone) {
                    $chat = ChatMessage::create([
                        'number' => $receiver_user_phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $issue->id,
                    ]);
                    app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);
                    MessageHelper::sendEmailOrWebhookNotification([$issue->assigned_to, $issue->team_lead_id, $issue->tester_id], $msg);
                }
            }

            return respJson(200, 'Successfully updated.');
        }

        return respJson(404, 'Record not found.');
    }

    public function saveLeadEstimateTime(Request $request)
    {
        $issue = DeveloperTask::find(request('issue_id'));

        DeveloperTaskHistory::create([
            'developer_task_id' => $issue->id,
            'model' => DeveloperTask::class,
            'attribute' => 'lead_estimation_minute',
            'old_value' => $issue->lead_estimate_time,
            'new_value' => request('lead_estimate_time'),
            'remark' => request('remark') ?: null,
            'user_id' => loginId(),
        ]);
        $issue->lead_estimate_time = request('lead_estimate_time');
        $issue->save();

        return respJson(200, 'Successfully updated.');
    }

    public function approveLeadTimeHistory(Request $request)
    {
        if (isAdmin()) {
            if (
                ! $request->approve_time
                || $request->approve_time == ''
                || ! $request->lead_developer_task_id
                || $request->lead_developer_task_id == ''
            ) {
                return respJson(400, 'Select one time first.');
            }

            DeveloperTaskHistory::where('developer_task_id', $request->lead_developer_task_id)
                ->where('attribute', 'estimation_minute')
                ->update(['is_approved' => 0]);

            $history = DeveloperTaskHistory::find($request->approve_time);
            $history->is_approved = 1;
            $history->save();

            return respJson(200, 'Successfully updated.');
        }

        return respJson(403, 'Only admin can approve.');
    }

    public function historySimpleData($key, $id)
    {
        $list = DeveloperTaskHistory::with('user')
            ->where('model', DeveloperTask::class)
            ->where('attribute', $key)
            ->where('developer_task_id', $id)->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';

        $needApprovals = ['start_date', 'estimate_date'];

        if (in_array($key, $needApprovals)) {
            $html[] = '<thead>
            <tr>
                <th width="5%">#</th>
                <th width="5%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        } else {
            $html[] = '<thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        }

        if ($list->count()) {
            foreach ($list as $single) {
                if (in_array($key, $needApprovals)) {
                    $html[] = '<tr>
                        <td><input type="radio" name="radio_for_approve" value="'.$single->id.'" '.($single->is_approved ? 'checked' : '').' style="height:auto;" /></td>
                        <td>'.$single->id.'</td>
                        <td>'.($single->user ? $single->user->name : '-').'</td>
                        <td>'.$single->old_value.'</td>
                        <td>'.$single->new_value.'</td>
                        <td>'.$single->created_at.'</td>
                    </tr>';
                } else {
                    $html[] = '<tr>
                        <td>'.$single->id.'</td>
                        <td>'.($single->user ? $single->user->name : '-').'</td>
                        <td>'.$single->old_value.'</td>
                        <td>'.$single->new_value.'</td>
                        <td>'.$single->created_at.'</td>
                    </tr>';
                }
            }
        } else {
            if (in_array($key, $needApprovals)) {
                $html[] = '<tr>
                    <td colspan="6">No records found.</td>
                </tr>';
            } else {
                $html[] = '<tr>
                    <td colspan="5">No records found.</td>
                </tr>';
            }
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function historyStartDate()
    {
        return $this->historySimpleData('start_date', request('id'));
    }

    public function historyEstimateDate()
    {
        return $this->historySimpleData('estimate_date', request('id'));
    }

    public function historyCost()
    {
        return $this->historySimpleData('cost', request('id'));
    }

    public function historyApproveSubmit()
    {
        $id = request('radio_for_approve');
        $type = request('type');
        if ($type == 'start_date' || $type == 'estimate_date') {
            DeveloperTaskHistory::approved($id, $type);
        }

        return respJson(200, 'Approved successfully.');
    }

    public function historyApproveList()
    {
        $type = request('type');
        $taskId = request('id');
        if ($type == 'start_date' || $type == 'estimate_date') {
            $q = DeveloperTasksHistoryApprovals::from('developer_tasks_history_approvals as t1');
            $q->with(['approvedBy']);
            $q->leftJoin('developer_tasks_history as t2', function ($join) {
                $join->on('t1.parent_id', '=', 't2.id');
            });
            $q->where('t2.model', DeveloperTask::class);
            $q->where('t2.attribute', $type);
            $q->where('t2.developer_task_id', $taskId);
            $q->select([
                't1.*',
                't2.new_value AS value',
            ]);
            $q->orderByDesc('id');
            $list = $q->get();
        }

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="15%">Parent ID</th>
                <th width="30%">Update By</th>
                <th width="30%" style="word-break: break-all;">Approved Value</th>
                <th width="25%">Created at</th>
            </tr>
        </thead>';
        if (isset($list) && $list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>'.$single->parent_id.'</td>
                    <td>'.($single->approvedByName() ?: '-').'</td>
                    <td>'.$single->value.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="4">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }

    /**
     * function to show the user wise development task's statuses counts.
     *
     * @param  int  $id
     */
    public function developmentTaskSummary(Request $request): \Illuminate\View\View
    {
        $getTaskStatus = TaskStatus::orderBy('name')->groupBy('name')->get();
        $getTaskStatusIds = TaskStatus::select(DB::raw('group_concat(name) as name'))->first();
        $arrTaskStatusNames = explode(',', $getTaskStatusIds['name']);

        $userListWithStatuesCnt = User::select('developer_tasks.id', 'developer_tasks.user_id', 'users.id as userid', 'users.name', 'developer_tasks.status', DB::raw('(SELECT developer_tasks.created_at from developer_tasks where developer_tasks.user_id = users.id order by developer_tasks.created_at DESC limit 1) AS created_date'), 'users.name', DB::raw('count(developer_tasks.id) statusCnt'));
        $userListWithStatuesCnt = $userListWithStatuesCnt->join('developer_tasks', 'developer_tasks.user_id', 'users.id')->where('users.is_task_planned', 1);

        // Code for filter
        //Get all searchable user list
        $userslist = $statuslist = null;
        $filterUserIds = $request->get('users_filter');
        $filterStatusIds = $request->get('status_filter');

        //Get all searchable status list
        if ((int) $filterUserIds > 0 && (int) $filterStatusIds > 0) {
            $searchableStatus = TaskStatus::WhereIn('id', $filterStatusIds)->get();
            $userListWithStatuesCnt = $userListWithStatuesCnt->WhereIn('developer_tasks.user_id', $filterUserIds)->where(function ($query) use ($searchableStatus) {
                foreach ($searchableStatus as $searchTerm) {
                    $query->orWhere('developer_tasks.status', 'like', "%$searchTerm->name%");
                }
            });
            $statuslist = TaskStatus::WhereIn('id', $filterStatusIds)->get();
            $userslist = User::whereIn('id', $filterUserIds)->get();
        } elseif ((int) $filterUserIds > 0) {
            $userListWithStatuesCnt = $userListWithStatuesCnt->WhereIn('users.id', $filterUserIds);
            $userslist = User::whereIn('id', $request->get('users_filter'))->get();
        } elseif ((int) $filterStatusIds > 0) {
            $searchableStatus = TaskStatus::WhereIn('id', $filterStatusIds)->get();
            $userListWithStatuesCnt = $userListWithStatuesCnt->where(function ($query) use ($searchableStatus) {
                foreach ($searchableStatus as $searchTerm) {
                    $query->orWhere('developer_tasks.status', 'like', "%$searchTerm->name%");
                }
            });
            $statuslist = TaskStatus::WhereIn('id', $filterStatusIds)->get();
        }

        $userListWithStatuesCnt = $userListWithStatuesCnt->groupBy('users.id', 'developer_tasks.user_id', 'developer_tasks.status')
            ->orderByDesc('created_date')->orderBy('developer_tasks.status')
            ->get();

        $arrStatusCount = [];
        $arrUserNameId = [];
        foreach ($userListWithStatuesCnt as $value) {
            $status = $value['status'];
            $arrStatusCount[$value['userid']][$status] = $value['statusCnt'];
            $arrUserNameId[$value['userid']]['name'] = $value['name'];
            $arrUserNameId[$value['userid']]['userid'] = $value['userid'];
            foreach ($arrTaskStatusNames as $key => $arrTaskStatusNamevalue) {
                if (! array_key_exists($arrTaskStatusNamevalue, $arrStatusCount[$value['userid']])) {
                    $arrStatusCount[$value['userid']][$arrTaskStatusNamevalue] = 0;
                }
            }
            isset($arrStatusCount[$value['userid']]) ? ksort($arrStatusCount[$value['userid']]) : '';
        }

        return view('development.devtasksummary', compact('userListWithStatuesCnt', 'getTaskStatus', 'arrUserNameId', 'arrStatusCount', 'userslist', 'statuslist'));
    }

    /**
     * function to show all the task list based on specific status and user
     */
    public function developmentTaskList(Request $request): JsonResponse
    {
        $taskDetails = DeveloperTask::where('status', $request->taskStatusId)->where('user_id', $request->userId)->get();

        return response()->json(['data' => $taskDetails]);
    }

    /**
     * Function to get user's name - it's use for lazy loading of users data
     */
    public function usersList(Request $request): JsonResponse
    {
        $users = User::orderBy('name');
        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }
        $users = $users->paginate(30);
        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $result['items'][] = [
                'id' => $user->id,
                'text' => $user->name,
            ];
        }

        return response()->json($result);
    }

    /**
     * Upload a task file to google drive
     */
    public function uploadFile(UploadFileDevelopmentRequest $request): RedirectResponse
    {

        $data = $request->all();

        try {
            foreach ($data['file'] as $file) {
                DB::transaction(function () use ($file, $data) {
                    $googleScreencast = new GoogleScreencast;
                    $googleScreencast->file_name = $file->getClientOriginalName();
                    $googleScreencast->extension = $file->extension();
                    $googleScreencast->user_id = Auth::id();

                    $googleScreencast->read = '';
                    $googleScreencast->write = '';

                    if ($data['task_type'] == 'DEVTASK') {
                        $googleScreencast->developer_task_id = $data['task_id'];
                    } elseif ($data['task_type'] == 'TASK') {
                        $googleScreencast->belongable_id = $data['task_id'];
                        $googleScreencast->belongable_type = Task::class;
                    }

                    $googleScreencast->remarks = $data['remarks'];
                    $googleScreencast->file_creation_date = $data['file_creation_date'];

                    $googleScreencast->save();
                    UploadGoogleDriveScreencast::dispatchSync($googleScreencast, $file);
                });
            }

            return redirect()->back()->with('success', 'File is Uploaded to Google Drive.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    /**
     * This function will return a list of files which are uploaded under uicheck class
     */
    public function getUploadedFilesList(Request $request): JsonResponse
    {
        try {
            $result = [];
            if (isset($request->task_id) && isset($request->task_type)) {
                if ($request->task_type == 'DEVTASK') {
                    $result = GoogleScreencast::where('developer_task_id', $request->task_id)->orderByDesc('id')->get();
                } elseif ($request->task_type == 'TASK') {
                    $result = GoogleScreencast::where('belongable_type', Task::class)->where('belongable_id', $request->task_id)->orderByDesc('id')->get();
                } else {
                    throw new Exception('Something went wrong.');
                }

                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('development.partials.google-drive-list', compact('result'))->render(),
                ]);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => view('development.partials.google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    /**
     * Show the hostory for task an dev task
     */
    public function showTaskEstimateTime(Request $request)
    {
        try {
            $developerTaskID = DeveloperTaskHistory::where([
                'model' => DeveloperTask::class,
                'attribute' => 'estimation_minute',
            ])->orderByDesc('id')->limit(10)->groupBy('developer_task_id')->select('developer_task_id', DB::raw('max(id) as id'))->get()->pluck('id')->toArray();

            $developerTaskHistory = DeveloperTaskHistory::join('developer_tasks', 'developer_tasks.id', 'developer_tasks_history.developer_task_id')
                ->whereIn('developer_tasks_history.id', $developerTaskID)
                ->where(function ($query) use ($request) {
                    if (isset($request->task_id)) {
                        if (str_contains($request->task_id, 'DEVTASK')) {
                            $query = $query->where('developer_tasks.id', trim($request->task_id, 'DEVTASK-'));
                        }
                    }

                    return $query;
                })
                ->select('developer_tasks.*', 'developer_tasks_history.*', 'developer_tasks.id as task_id')->get();

            $t_developerTaskID = DeveloperTaskHistory::where([
                'model' => Task::class,
                'attribute' => 'estimation_minute',
            ])->orderByDesc('id')->limit(10)->groupBy('developer_task_id')->select('developer_task_id', DB::raw('max(id) as id'))->get()->pluck('id')->toArray();

            $developerTaskHistory = DeveloperTaskHistory::join('tasks', 'tasks.id', 'developer_tasks_history.developer_task_id')
                ->whereIn('developer_tasks_history.id', $t_developerTaskID)
                ->where(function ($query) use ($request) {
                    if (isset($request->task_id)) {
                        if (! str_contains($request->task_id, 'DEVTASK')) {
                            $query = $query->where('tasks.id', trim($request->task_id, 'TASK-'));
                        }
                    }

                    return $query;
                })
                ->select('tasks.*', 'developer_tasks_history.*', 'tasks.id as task_id')->get();

            if (isset($request->task_id)) {
                if (str_contains($request->task_id, 'DEVTASK')) {
                    $developerTaskHistory = [];
                }
                if (! str_contains($request->task_id, 'DEVTASK')) {
                    $developerTaskHistory = [];
                }
            }
            $taskId = isset($request->task_id) ? $request->task_id : '';
            $d_taskList = DeveloperTask::select('id')->orderByDesc('id')->pluck('id');
            $g_taskList = Task::select('id')->orderByDesc('id')->pluck('id');

            return view('development.partials.estimate-list', compact('developerTaskHistory', 't_developerTaskHistory', 'd_taskList', 'g_taskList', 'taskId'));
        } catch (Exception $e) {
            dd($e);

            return '';
        }
    }

    public function showTaskEstimateTimeAlert(Request $request)
    {
        try {
            $developerTaskID = DeveloperTaskHistory::where([
                'model' => DeveloperTask::class,
                'attribute' => 'estimation_minute',
            ])->orderByDesc('id')->limit(10)->groupBy('developer_task_id')->select('developer_task_id', DB::raw('max(id) as id'))->get()->pluck('id')->toArray();

            $developerTaskHistory = DeveloperTaskHistory::join('developer_tasks', 'developer_tasks.id', 'developer_tasks_history.developer_task_id')
                ->whereIn('developer_tasks_history.id', $developerTaskID)
                ->where(function ($query) use ($request) {
                    if (isset($request->task_id)) {
                        if (str_contains($request->task_id, 'DEVTASK')) {
                            $query = $query->where('developer_tasks.id', trim($request->task_id, 'DEVTASK-'));
                        }
                    }

                    return $query;
                })
                ->where('developer_tasks_history.is_approved', 0)
                ->select('developer_tasks.*', 'developer_tasks_history.*', 'developer_tasks.id as task_id')->count();

            $t_developerTaskID = DeveloperTaskHistory::where([
                'model' => Task::class,
                'attribute' => 'estimation_minute',
            ])->orderByDesc('id')->limit(10)->groupBy('developer_task_id')->select('developer_task_id', DB::raw('max(id) as id'))->get()->pluck('id')->toArray();

            $developerTaskHistory = DeveloperTaskHistory::join('tasks', 'tasks.id', 'developer_tasks_history.developer_task_id')
                ->whereIn('developer_tasks_history.id', $t_developerTaskID)
                ->where(function ($query) use ($request) {
                    if (isset($request->task_id)) {
                        if (! str_contains($request->task_id, 'DEVTASK')) {
                            $query = $query->where('tasks.id', trim($request->task_id, 'TASK-'));
                        }
                    }

                    return $query;
                })
                ->where('developer_tasks_history.is_approved', 0)
                ->select('tasks.*', 'developer_tasks_history.*', 'tasks.id as task_id')->count();

            $totalUnApproved = $developerTaskHistory + $developerTaskHistory;

            return response()->json([
                'code' => 200,
                'count' => $totalUnApproved,
            ]);
        } catch (Exception $e) {
            dd($e);

            return '';
        }
    }

    public function addScrapper(Request $request): JsonResponse
    {
        try {
            $this->validate(
                $request, [
                    'task_id' => 'required',
                    'task_type' => 'required',
                    'scrapper_values' => 'required',
                ]
            );
            $returnData = [];
            if (! empty($returnData)) {
                return response()->json(
                    [
                        'code' => 500,
                        'data' => [],
                        'message' => implode('</br> ', $returnData)."</br> above key's value is missing on your json data!",
                    ]
                );
            }

            $column = new ScrapperValues;
            $column->task_id = $request->task_id;
            $column->task_type = $request->task_type;
            $column->scrapper_values = $request->scrapper_values;
            $column->added_by = auth()->user()->id;
            $column->save();

            ScrapperLogs::create([
                'scrapper_id' => $column->id,
                'task_id' => $request->task_id,
                'task_type' => $request->task_type,
                'log' => 'Scrapper Added',
                'created_by' => auth()->user()->id,
            ]);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your scrapper value has been added!',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 500,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function getScrapperLogsByTaskId($id): JsonResponse
    {
        $data = ScrapperLogs::with('user')->where('task_id', $id)->get();

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function taskScrapper($task_id): JsonResponse
    {
        $ScrapperValues = ScrapperValues::where('task_id', $task_id)->orderByDesc('id')->first();

        $ScrapperValuesHistory = [];
        $ScrapperValuesRemarksHistory = [];
        $returnData = [];
        $id = 0;
        if (! empty($ScrapperValues)) {
            $jsonString = $ScrapperValues['scrapper_values'];
            $phpArray = json_decode($jsonString, true);
            if (! empty($phpArray)) {
                if (! empty($phpArray)) {
                    $ScrapperValuesHistory = ScrapperValuesHistory::where('task_id', $task_id)->get();
                    $ScrapperValuesRemarksHistory = ScrapperValuesRemarksHistory::where('task_id', $task_id)->get();

                    foreach ($phpArray as $key_json => $value_json) {
                        $returnData[$key_json] = $value_json;
                    }
                }
            }

            $id = $ScrapperValues->id;
        }

        return response()->json(['code' => 200, 'values' => $returnData, 'task_id' => $task_id, 'ScrapperValuesHistory' => $ScrapperValuesHistory, 'ScrapperValuesRemarksHistory' => $ScrapperValuesRemarksHistory, 'id' => $id]);
    }

    // Added function to get scrapper by scrapper id on listing page DEVTASK-24690
    public function viewScrapper($id): JsonResponse
    {
        $ScrapperValues = ScrapperValues::where('id', $id)->first();

        $task_id = $ScrapperValues->task_id;
        $ScrapperValuesHistory = [];
        $ScrapperValuesRemarksHistory = [];
        $returnData = [];
        $id = 0;
        if (! empty($ScrapperValues)) {
            $jsonString = $ScrapperValues['scrapper_values'];
            $phpArray = json_decode($jsonString, true);
            if (! empty($phpArray)) {
                if (! empty($phpArray)) {
                    $ScrapperValuesHistory = ScrapperValuesHistory::where('task_id', $task_id)->get();
                    $ScrapperValuesRemarksHistory = ScrapperValuesRemarksHistory::where('task_id', $task_id)->get();

                    foreach ($phpArray as $key_json => $value_json) {
                        $returnData[$key_json] = $value_json;
                    }
                }
            }

            $id = $ScrapperValues->id;
        }

        return response()->json(['code' => 200, 'values' => $returnData, 'task_id' => $task_id, 'ScrapperValuesHistory' => $ScrapperValuesHistory, 'ScrapperValuesRemarksHistory' => $ScrapperValuesRemarksHistory, 'id' => $id]);
    }

    public function UpdateScrapper(UpdateScrapperDevelopmentRequest $request): JsonResponse
    {

        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;

        ScrapperValuesHistory::updateOrCreate(
            ['task_id' => $request->task_id, 'column_name' => $request->column_name], $input
        );

        if ($request->status == 'Unapprove') {
            if (! empty($request->remarks)) {
                ScrapperValuesRemarksHistory::updateOrCreate(
                    ['task_id' => $request->task_id, 'column_name' => $request->column_name], $input
                );
            }

            $task = DeveloperTask::find($request->task_id);
            $task->status = 'Scrapper Data Unapproved';
            $task->save();
        } else {
            $task = DeveloperTask::find($request->task_id);
            $task->status = 'Scrapper Data Approved';
            $task->save();
        }

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Your scrapper status has been updated!',
            ]
        );
    }

    public function UpdateScrapperRemarks(UpdateScrapperRemarksDevelopmentRequest $request): JsonResponse
    {

        $input = $request->all();
        $input['updated_by'] = auth()->user()->id;

        ScrapperValuesRemarksHistory::updateOrCreate(
            ['task_id' => $request->task_id, 'column_name' => $request->column_name], $input
        );

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Your scrapper status has been updated!',
            ]
        );
    }

    public function devScrappingTaskIndex(Request $request): \Illuminate\View\View
    {
        $inputs = $request->input();

        $records = ScrapperValues::with('tasks', 'scrappervalueshistory', 'scrappervaluesremarkshistory');

        $keywords = request('keywords');
        if (! empty($keywords)) {
            $records = $records->where(function ($q) use ($keywords) {
                $q->where('scrapper_values', 'LIKE', "%$keywords%")
                    ->orWhere('task_id', 'LIKE', "%$keywords%");
            });
        }

        $records = $records->select('task_id', 'scrapper_values.scrapper_values', 'scrapper_values.created_at', DB::raw('MAX(id) AS max_id')) // Select only necessary columns and use an alias for MAX(id)
            ->groupBy('task_id')
            ->orderByDesc('max_id') // Order by the alias of MAX(id)
            ->paginate(50);

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'development-scrapper-listing')->first();

        $dynamicColumnsToShowscrapper = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowscrapper = json_decode($hideColumns, true);
        }

        return view('development.scrapperlist', [
            'records' => $records,
            'inputs' => $inputs,
            'dynamicColumnsToShowscrapper' => $dynamicColumnsToShowscrapper,
        ]);
    }

    public function scrapperColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'development-scrapper-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'development-scrapper-listing';
            $column->column_name = json_encode($request->column_scrapper);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'development-scrapper-listing';
            $column->column_name = json_encode($request->column_scrapper);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function developmentScrapperData($id): JsonResponse
    {
        $ScrapperValues = ScrapperValues::findorFail($id);

        $properties = [];
        $jsonString = $ScrapperValues['scrapper_values'];
        $phpArray = json_decode($jsonString, true);
        if (! empty($phpArray)) {
            foreach ($phpArray as $key_json => $value_json) {
                if ($key_json == 'properties') {
                    $properties[] = $value_json;
                }
            }
        }

        $html = '';
        if (! empty($properties)) {
            $html = '<table class="table table-bordered table-striped">
                    <tbody class="text-center task_queue_list">';
            foreach ($properties as $value) {
                $keys = array_keys($value);

                if (! empty($keys)) {
                    foreach ($keys as $value_k) {
                        $html .= '<tr>';
                        $html .= '<th>'.ucwords(str_replace('_', ' ', $value_k)).'</th>';

                        if (gettype($value[$value_k]) == 'array') {
                            $html .= '<td>'.implode(', ', $value[$value_k]).'</td>';
                        } else {
                            $html .= '<td>'.$value[$value_k].'</td>';
                        }
                        $html .= '</tr>';
                    }
                }
            }

            $html .= '</tbody>';
            $html .= '</table>';
        }

        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Data get successfully',
        ], 200);
    }

    public function developmentScrapperImagesData($id): JsonResponse
    {
        $ScrapperValues = ScrapperValues::findorFail($id);

        $images = [];
        $jsonString = $ScrapperValues['scrapper_values'];
        $phpArray = json_decode($jsonString, true);
        if (! empty($phpArray)) {
            foreach ($phpArray as $key_json => $value_json) {
                if ($key_json == 'images') {
                    $images[] = $value_json;
                }
            }
        }

        $html = '';
        if (! empty($images)) {
            $html = '<div class="row">
                    <div class="col-lg-12">';
            foreach ($images as $value) {
                if (! empty($value)) {
                    foreach ($value as $value_k) {
                        $html .= '<div class="col-lg-1">';
                        $html .= '<img src="'.$value_k.'">';
                        $html .= '</div>';
                    }
                }
            }

            $html .= '</div>';
            $html .= '</div>';
        }

        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Data get successfully',
        ], 200);
    }

    public function developmentGetScrapperData(Request $request): JsonResponse
    {
        $ScrapperValuesHistory = ScrapperValuesHistory::where('task_id', $request->task_id)->where('column_name', $request->column_name)->first();

        $ScrapperValuesRemarksHistory = [];
        if (! empty($ScrapperValuesHistory)) {
            if ($ScrapperValuesHistory['status'] == 'Unapprove') {
                $ScrapperValuesRemarksHistory = ScrapperValuesRemarksHistory::where('task_id', $request->task_id)->where('column_name', $request->column_name)->first();
            }
        }

        return response()->json([
            'status' => true,
            'ScrapperValuesHistory' => $ScrapperValuesHistory,
            'ScrapperValuesRemarksHistory' => $ScrapperValuesRemarksHistory,
            'message' => 'Data get successfully',
        ], 200);
    }

    public function devScrappingTaskHistoryIndex(Request $request): JsonResponse
    {
        $ScrapperValues = ScrapperValues::where('task_id', $request->task_id)->where('id', '!=', $request->id)->orderByDesc('id')->get();
        $returnData = [];
        if (! empty($ScrapperValues)) {
            foreach ($ScrapperValues as $key => $value) {
                $jsonString = $value['scrapper_values'];
                $phpArray = json_decode($jsonString, true);
                if (! empty($phpArray)) {
                    foreach ($phpArray as $key_json => $value_json) {
                        $returnData[$key][$key_json] = $value_json;
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'values' => $returnData, 'task_id' => $request->task_id]);
    }

    public function devScrappingTaskHistory($id): \Illuminate\View\View
    {
        $recordsSingle = ScrapperValues::where('id', $id)->first();

        $records = ScrapperValues::with('tasks')->where('task_id', $recordsSingle['task_id'])->orderByDesc('id');

        $keywords = request('keywords');
        if (! empty($keywords)) {
            $records = $records->where(function ($q) use ($keywords) {
                $q->where('scrapper_values', 'LIKE', "%$keywords%")
                    ->orWhere('task_id', 'LIKE', "%$keywords%");
            });
        }
        $records = $records->paginate(50);

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'development-scrapper-listing')->first();

        $dynamicColumnsToShowscrapper = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowscrapper = json_decode($hideColumns, true);
        }

        $ScrapperValuesHistory = ScrapperValuesHistory::where('task_id', $recordsSingle['task_id'])->get();
        $ScrapperValuesRemarksHistory = ScrapperValuesRemarksHistory::where('task_id', $recordsSingle['task_id'])->get();

        return view('development.scrapperlisthistroy', [
            'records' => $records,
            'dynamicColumnsToShowscrapper' => $dynamicColumnsToShowscrapper,
            'ScrapperValuesHistory' => $ScrapperValuesHistory,
            'ScrapperValuesRemarksHistory' => $ScrapperValuesRemarksHistory,
        ]);
    }

    public function developmentUpdateAllScrapperStatusData(Request $request): JsonResponse
    {
        $recordsScrapper = ScrapperValues::where('id', $request->scrapper_id)->first();

        if (! empty($recordsScrapper)) {
            if ($request->type == 1) {
                $jsonString = $recordsScrapper['scrapper_values'];
                $phpArray = json_decode($jsonString, true);
                if (! empty($phpArray)) {
                    foreach ($phpArray as $key_json => $value_json) {
                        if ($key_json == 'properties') {
                            if (! empty($value_json)) {
                                foreach ($value_json as $key => $value) {
                                    $ScrapperValuesHistory = ScrapperValuesHistory::where('column_name', $key)->where('task_id', $recordsScrapper['task_id'])->first();

                                    if (empty($ScrapperValuesHistory)) {
                                        $ScrapperValuesHistoryNew = new ScrapperValuesHistory;
                                        $ScrapperValuesHistoryNew->status = 'Approve';
                                        $ScrapperValuesHistoryNew->column_name = $key;
                                        $ScrapperValuesHistoryNew->updated_by = auth()->user()->id;
                                        $ScrapperValuesHistoryNew->task_id = $recordsScrapper['task_id'];
                                        $ScrapperValuesHistoryNew->save();
                                    } else {
                                        $ScrapperValuesHistory->status = 'Approve';
                                        $ScrapperValuesHistory->updated_by = auth()->user()->id;
                                        $ScrapperValuesHistory->save();
                                    }

                                    ScrapperValuesRemarksHistory::where('column_name', $key)->where('task_id', $recordsScrapper['task_id'])->delete();
                                }
                            }
                        } else {
                            $ScrapperValuesHistory = ScrapperValuesHistory::where('column_name', $key_json)->where('task_id', $recordsScrapper['task_id'])->first();

                            if (empty($ScrapperValuesHistory)) {
                                $ScrapperValuesHistoryNew = new ScrapperValuesHistory;
                                $ScrapperValuesHistoryNew->status = 'Approve';
                                $ScrapperValuesHistoryNew->column_name = $key_json;
                                $ScrapperValuesHistoryNew->updated_by = auth()->user()->id;
                                $ScrapperValuesHistoryNew->task_id = $recordsScrapper['task_id'];
                                $ScrapperValuesHistoryNew->save();
                            } else {
                                $ScrapperValuesHistory->status = 'Approve';
                                $ScrapperValuesHistory->updated_by = auth()->user()->id;
                                $ScrapperValuesHistory->save();
                            }

                            ScrapperValuesRemarksHistory::where('column_name', $key_json)->where('task_id', $recordsScrapper['task_id'])->delete();
                        }
                    }

                    $task = DeveloperTask::find($recordsScrapper['task_id']);
                    $task->status = 'Scrapper Data Approved';
                    $task->save();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Scrapper values status updated.',
                ], 200);
            } else {
                ScrapperValuesHistory::where('task_id', $recordsScrapper['task_id'])->delete();
                ScrapperValuesRemarksHistory::where('task_id', $recordsScrapper['task_id'])->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Scrapper values status updated.',
                ], 200);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Scrapper values status updated.',
        ], 200);
    }

    public function startTimeHistory(Request $request): JsonResponse
    {
        $task = DeveloperTask::find($request->developer_task_id);

        if ($request->task_type == 1) {
            $input['m_start_date'] = Carbon::now();
            $input['task_start'] = 1;
            $input['status'] = 'In Progress';

            $history = new DeveloperTaskStartEndHistory;
            $history->user_id = auth()->user()->id;
            $history->task_id = $request->developer_task_id;
            $history->start_date = Carbon::now();
            $history->save();
        } elseif ($request->task_type == 2) {
            $input['m_end_date'] = Carbon::now();
            $input['task_start'] = 2;

            $history = DeveloperTaskStartEndHistory::where('task_id', $request->developer_task_id)->orderByDesc('id')->first();

            if (! empty($history)) {
                $history->end_date = Carbon::now();
                $history->save();
            }
        }

        $task->update($input);

        return response()->json(['msg' => 'success']);
    }

    public function getTimeHistoryStartEnd(Request $request): JsonResponse
    {
        $id = $request->id;

        $task_histories = DeveloperTaskStartEndHistory::where('task_id', $id)->orderByDesc('id')->get();

        return response()->json(['histories' => $task_histories]);
    }

    public function scrapperMonitoring(Request $request): \Illuminate\View\View
    {
        $tasks = DeveloperTask::select('id')->orderByDesc('id')->get();
        $users = User::role('Developer')->select('id', 'name')->get();

        $data = ScrapperMonitoring::with([
            'user',
            'task' => function ($query) {
                $query->with(['developerTaskHistories' => function ($innerQuery) {
                    $innerQuery->orderByDesc('created_at');
                }]);
            },
        ])
            ->when(($request->has('scrapper_name') && $request->scrapper_name != ''), function ($query) use ($request) {
                $query->where('scrapper_name', 'LIKE', '%'.$request->scrapper_name.'%');
            })
            ->when(($request->has('task_id') && $request->task_id != ''), function ($query) use ($request) {
                $query->where('task_id', $request->task_id);
            })
            ->when(($request->has('user_id') && $request->user_id != ''), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })
            ->when(($request->has('need_proxy') && $request->need_proxy != ''), function ($query) use ($request) {
                $query->where('need_proxy', $request->need_proxy);
            })
            ->when(($request->has('aws_moved') && $request->aws_moved != ''), function ($query) use ($request) {
                $query->where('move_to_aws', $request->aws_moved);
            })
            ->orderByDesc('created_at')
            ->paginate(10);
        if (request()->ajax()) {
            return view('development.scrapper.partials.table-data', compact('data'));
        }

        $inputsData = $request->all();

        return view('development.scrapper.monitoring', compact('tasks', 'users', 'data', 'inputsData'));
    }

    public function storeScrapperMonitoring(ScrapperMonitoringCreateRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            if (! auth()->user()->isAdmin()) {
                $validatedData['user_id'] = auth()->user()->id;
            }

            ScrapperMonitoring::create($validatedData);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Record created successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    // Add this function to handle common column visibilty feature. DEVTASK-24789
    public function globalColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', $request->section_name)->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = $request->section_name;
            $column->column_name = json_encode($request->column_scrapper);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = $request->section_name;
            $column->column_name = json_encode($request->column_scrapper);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }
}
