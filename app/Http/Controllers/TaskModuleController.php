<?php

namespace App\Http\Controllers;

use App\BugTracker;
use App\ChatMessage;
use App\ChatMessagesQuickData;
use App\Contact;
use App\DeveloperTask;
use App\DeveloperTaskHistory;
use App\DocumentRemark;
use App\ErpPriority;
use App\Exports\AllTasksExcelExport;
use App\GoogleScreencast;
use App\Helpers;
use App\Helpers\HubstaffTrait;
use App\Helpers\MessageHelper;
use App\Http\Requests\CreateStatusTaskModuleRequest;
use App\Http\Requests\CreateTaskFromSortcutTaskModuleRequest;
use App\Http\Requests\MessageReminderTaskModuleRequest;
use App\Http\Requests\StoreTaskModuleRequest;
use App\Http\Requests\TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest;
use App\Http\Requests\UpdateTaskModuleRequest;
use App\Http\Requests\UploadFileTaskModuleRequest;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffTask;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\LogChatMessage;
use App\Models\DataTableColumn;
use App\Models\TaskHubstaffCreateLog;
use App\Models\Tasks\TaskHistoryForCost;
use App\Models\Tasks\TaskHistoryForStartDate;
use App\Models\TaskStartEndHistory;
use App\Models\TaskUser;
use App\PaymentReceipt;
use App\Remark;
use App\ReplyCategory;
use App\RoleUser;
use App\SatutoryTask;
use App\ScheduledMessage;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\Sop;
use App\StoreWebsite;
use App\Task;
use App\TaskCategory;
use App\TaskRemark;
use App\TaskStatus;
use App\TaskUserHistory;
use App\Team;
use App\TeamUser;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorTask;
use App\User;
use App\UserAvaibility;
use App\UserEvent\UserEvent;
use App\UserRate;
use App\WhatsAppGroup;
use App\WhatsAppGroupNumber;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;
use Throwable;

class TaskModuleController extends Controller
{
    use HubstaffTrait;

    private $githubClient;

    public function __construct()
    {
        $this->githubClient = new Client(
            [
                'auth' => [
                    config('env.GITHUB_USERNAME'),
                    config('env.GITHUB_TOKEN'),
                ],
            ]
        );
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    public function index(Request $request)
    {
        // Set default type and category
        $type = $request->input('type', 'pending');
        $category = $request->category == 1 ? '' : $request->category ?? '';
        $term = $request->term ?? '';
        $data['task'] = [];
        $data['task']['pending'] = [];
        $data['task']['statutory_not_completed'] = [];
        $data['task']['completed'] = [];

        // Initialize arrays for search suggestions
        $search_term_suggestions = [];
        $search_suggestions = [];
        $assign_from_arr = [0];
        $assign_to_arr = [0];

        if ($type == 'pending') {
            // Get Pending tasks via model
            $data['task']['pending'] = Task::getSearchedTasks('pending_list', $request);

            foreach ($data['task']['pending'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['pending'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'completed') {
            // Get Completed tasks via model
            $data['task']['completed'] = Task::getSearchedTasks('completed_list', $request);

            foreach ($data['task']['completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['completed'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'statutory_not_completed') {
            // Get Statutory tasks via model
            $data['task']['statutory_not_completed'] = Task::getSearchedTasks('statutory_not_completed_list', $request);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        }

        $usersOrderByName = User::select('id', 'name', 'email')->whereNull('deleted_at')->orderBy('name')->get();
        $data['users'] = $usersOrderByName->toArray();
        $data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

        // Lead user process starts
        $model_team = Team::where('user_id', auth()->user()->id)->pluck('id')->toArray(); // Get team IDs directly
        $isTeamLeader = ! empty($model_team);

        $team_members_array = [auth()->user()->id];
        $team_id_array = $model_team;

        // Lead user process ends

        $selected_user = $request->input('selected_user');

        if ($isTeamLeader && ! Auth::user()->hasRole('Admin')) {
            $usrlst = [];

            $team_members = TeamUser::whereIn('team_id', $team_id_array)->pluck('user_id')->unique()->toArray();

            $team_members_array = array_merge($team_members_array, $team_members);

            $usrlst = $usersOrderByName->filter(function ($user) use ($team_members_array) {
                return in_array($user->id, $team_members_array);
            });

        } else {
            $usrlst = $usersOrderByName;
        }

        $users = Helpers::getUserArray($usrlst->toArray());
        $usersForExport = [];

        if (Auth::user()->hasRole('Admin')) {
            $usersForExport = User::select('name', 'id')->get();
        } else {
            $usersForExport = User::select('name', 'id')->where('id', '=', Auth::user()->id)->get();
        }

        $all_task_categories = TaskCategory::all();

        // Set default selected category for Admin
        $selected_category = $request->category ?: (Auth::user()->hasRole('Admin') ? 1 : null);

        $task_categories = $approved_categories = $categories = [];

        foreach ($all_task_categories as $category) {
            // task category with parent_id == 0
            if ($category->parent_id == 0) {
                $task_categories[] = $category;
            }

            // Map category id to titles
            $categories[$category->id] = $category->title;

            // approved category list for dropdown
            if ($category->is_approved == 1) {
                $approved_categories[] = $category->toArray();
            }
        }

        // Generate the task categories dropdown
        $task_categories_dropdown = nestable($approved_categories)->attr([
            'name' => 'category',
            'class' => 'form-control input-sm',
        ])->selected($selected_category)->renderAsDropdown();

        if (! empty($selected_user) && ! Helpers::getadminorsupervisor()) {
            return response()->json(['user not allowed'], 405);
        }

        $tasks_view = [];
        $priority = ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

        $openTask = Task::join('users as u', 'u.id', 'tasks.assign_to')->whereNull('tasks.is_completed')->groupBy('tasks.assign_to')->select(DB::raw('count(u.id) as total'), 'u.name as person')->pluck('total', 'person');

        if ($request->is_statutory_query == 3) {
            $title = 'Discussion tasks';
        } else {
            $title = 'Task & Activity';
        }

        $task_statuses = TaskStatus::all();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'task-listing')->first();

        $dynamicColumnsToShowTask = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowTask = json_decode($hideColumns, true);
        }

        $reply_categories = ReplyCategory::select('id', 'name')
            ->with('approval_leads', 'sub_categories')
            ->where('parent_id', 0)
            ->where('id', 44)
            ->orderBy('name')->get();

        if ($request->ajax()) {
            if ($type == 'pending') {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories'));
            } elseif ($type == 'statutory_not_completed') {
                return view('task-module.partials.statutory-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories'));
            } elseif ($type == 'completed') {
                return view('task-module.partials.completed-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories'));
            } else {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories'));
            }
        }

        if ($request->is_statutory_query == 3) {
            $task = Task::where('is_statutory', 3)->where('task_subject', '!=', "''")->get()->pluck('task_subject', 'id')->toArray();

            return view('task-module.discussion-tasks', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories', 'task'));
        } else {
            $taskStatusData = TaskStatus::get();

            $statuseslist = $taskStatusData->pluck('name', 'id')->toArray();
            $selectStatusList = $taskStatusData->pluck('id')->toArray();
            $taskstatus = $taskStatusData;

            return view('task-module.show', compact('taskstatus', 'data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'statuseslist', 'selectStatusList', 'isTeamLeader', 'dynamicColumnsToShowTask', 'reply_categories', 'usersForExport'));
        }
    }

    public function taskColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'task-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'task-listing';
            $column->column_name = json_encode($request->column_task);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'task-listing';
            $column->column_name = json_encode($request->column_task);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function indexModules(Request $request)
    {
        if (! $request->input('type') || $request->input('type') == '') {
            $type = 'pending';
        } else {
            $type = $request->input('type');
        }

        $category = '';
        if ($request->category != '') {
            $category = $request->category;
            if ($request->category == 1) {
                $category = '';
            }
        }

        $term = $request->term ?? '';
        $data['task'] = [];

        $search_term_suggestions = [];
        $search_suggestions = [];
        $assign_from_arr = [0];
        $special_task_arr = [0];
        $assign_to_arr = [0];
        $data['task']['pending'] = [];
        $data['task']['statutory_not_completed'] = [];
        $data['task']['completed'] = [];

        if ($type == 'pending') {
            // Get Pending tasks via model
            $data['task']['pending'] = Task::getSearchedTasks('pending_list', $request);

            foreach ($data['task']['pending'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['pending'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'completed') {
            // Get Completed tasks via model
            $data['task']['completed'] = Task::getSearchedTasks('completed_list', $request);

            foreach ($data['task']['completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['completed'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'statutory_not_completed') {
            // Get Statutory tasks via model
            $data['task']['statutory_not_completed'] = Task::getSearchedTasks('statutory_not_completed_list', $request);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = $task->assign_from_username;
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = $task->assign_to_username;
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        }

        $usersOrderByName = User::orderBy('name')->get();
        $data['users'] = $usersOrderByName->toArray();
        $data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

        // Lead user process starts
        $model_team = Team::where('user_id', auth()->user()->id)->get()->toArray();
        $team_members_array[] = auth()->user()->id;
        $team_id_array = [];
        $isTeamLeader = null;
        if (count($model_team) > 0) {
            $isTeamLeader = $model_team[0];
        }
        // Lead user process ends

        $selected_user = $request->input('selected_user');

        if ($isTeamLeader && ! Auth::user()->hasRole('Admin')) {
            $usrlst = [];

            for ($k = 0; $k < count($model_team); $k++) {
                $team_id_array[] = $model_team[$k]->id;
            }

            $model_user_model = TeamUser::whereIn('team_id', $team_id_array)->get()->toArray();
            for ($m = 0; $m < count($model_user_model); $m++) {
                $team_members_array[] = $model_user_model[$m]->user_id;
            }

            foreach ($usersOrderByName as $user) {
                if (in_array($user->id, $team_members_array)) {
                    $usrlst[] = $user;
                }
            }
        } else {
            $usrlst = $usersOrderByName;
        }

        $users = Helpers::getUserArray($usrlst);

        $all_task_categories = TaskCategory::all();
        if (Auth::user()->hasRole('Admin')) {
            if (empty($request->category)) {
                $selected_category = 1;
            }
        }
        $categories = $approved_categories = $task_categories = [];
        foreach ($all_task_categories as $category) {
            if ($category->parent_id == 0) {
                $task_categories[] = $category;
            }

            $categories[$category->id] = $category->title;

            if ($category->is_approved == 1) {
                $approved_categories[] = $category->toArray();
            }
        }

        $selected_category = $request->category;

        if (Auth::user()->hasRole('Admin')) {
            if (empty($request->category)) {
                $selected_category = 1;
            }
        }

        $task_categories_dropdown = nestable($approved_categories)->attr(
            [
                'name' => 'category',
                'class' => 'form-control input-sm',
            ]
        )->selected($selected_category)->renderAsDropdown();

        if (! empty($selected_user) && ! Helpers::getadminorsupervisor()) {
            return response()->json(['user not allowed'], 405);
        }

        $tasks_view = [];
        $priority = ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

        $openTask = Task::join('users as u', 'u.id', 'tasks.assign_to')->whereNull('tasks.is_completed')->groupBy('tasks.assign_to')->select(DB::raw('count(u.id) as total'), 'u.name as person')->pluck('total', 'person');

        if ($request->is_statutory_query == 3) {
            $title = 'Discussion tasks';
        } else {
            $title = 'Task & Activity';
        }

        $task_statuses = TaskStatus::all();

        if ($request->ajax()) {
            if ($type == 'pending') {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader'));
            } elseif ($type == 'statutory_not_completed') {
                return view('task-module.partials.statutory-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader'));
            } elseif ($type == 'completed') {
                return view('task-module.partials.completed-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader'));
            } else {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader'));
            }
        }

        if ($request->is_statutory_query == 3) {
            $task = Task::where('is_statutory', 3)->where('task_subject', '!=', "''")->get()->pluck('task_subject', 'id')->toArray();

            return view('task-module.discussion-tasks', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader', 'task'));
        } else {
            $taskStatusData = TaskStatus::get();

            $statuseslist = $taskStatusData->pluck('name', 'id')->toArray();
            $selectStatusList = $taskStatusData->pluck('id')->toArray();
            $taskstatus = $taskStatusData;

            return view('task-module.show-modules', compact('taskstatus', 'data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'statuseslist', 'selectStatusList', 'isTeamLeader'));
        }
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

    public function saveMilestone(Request $request): JsonResponse
    {
        $task = Task::find($request->task_id);
        if (! $task->is_milestone) {
            return response()->json(
                [
                    'message' => 'Milestone not found',
                ], 500
            );
        }
        $total = $request->total;
        if ($task->milestone_completed) {
            if ($total <= $task->milestone_completed) {
                return response()->json(
                    [
                        'message' => 'Milestone no can\'t be reduced',
                    ], 500
                );
            }
        }

        if ($total > $task->no_of_milestone) {
            return response()->json(
                [
                    'message' => 'Estimated milestone exceeded',
                ], 500
            );
        }
        if (! $task->cost || $task->cost == '') {
            return response()->json(
                [
                    'message' => 'Please provide cost first',
                ], 500
            );
        }

        $newCompleted = $total - $task->milestone_completed;
        $individualPrice = $task->cost / $task->no_of_milestone;
        $totalCost = $individualPrice * $newCompleted;

        $task->milestone_completed = $total;
        $task->save();
        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date('Y-m-d');
        $payment_receipt->worked_minutes = $task->approximate;
        $payment_receipt->rate_estimated = $totalCost;
        $payment_receipt->status = 'Pending';
        $payment_receipt->task_id = $task->id;
        $payment_receipt->user_id = $task->assign_to;
        $payment_receipt->save();

        return response()->json(
            [
                'status' => 'success',
            ]
        );
    }

    public function updatePriorityNo(Request $request): JsonResponse
    {
        $task = Task::find($request->task_id);

        if (Auth::user()->id == $task->assign_to || Auth::user()->isAdmin()) {
            $task->priority_no = $request->priority;
            $task->save();

            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Unauthorized access'], 500);
        }
    }

    public function taskListByUserId(Request $request): JsonResponse
    {
        $user_id = $request->get('user_id', 0);
        $selected_issue = $request->get('selected_issue', []);
        $issues = Task::select('tasks.*')->leftJoin(
            'erp_priorities', function ($query) {
                $query->on('erp_priorities.model_id', '=', 'tasks.id');
                $query->where('erp_priorities.model_type', '=', Task::class);
            }
        )->whereNull('is_verified');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->where(
                function ($q) use ($selected_issue, $user_id) {
                    if (count($selected_issue) != 0 && count($selected_issue) != 1) {
                        $q->whereIn('tasks.id', $selected_issue);
                    }

                    $user_id = is_null($user_id) ? 0 : $user_id;

                    if ($user_id != 0) {
                        $q->where('tasks.assign_to', $user_id)->orWhere('tasks.master_user_id', $user_id);
                    }
                }
            );
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->groupBy('tasks.id')->orderBy('erp_priorities.id')->get();

        foreach ($issues as &$value) {
            $value->created_by = User::where('id', $value->assign_from)->value('name');
            $value->created_at = Carbon::parse($value->created_at)->format('d-m-y H:i:s');
        }
        unset($value);
        $viewData = view('task-module.taskpriority', compact('issues'))->render();

        return response()->json(
            [
                'html' => $viewData,

            ], 200
        );
    }

    public function setTaskPriority(Request $request): JsonResponse
    {
        $priority = $request->get('priority', null);
        $user_id = $request->get('user_id', 0);

        //delete old priority
        ErpPriority::where('user_id', $user_id)->where('model_type', '=', Task::class)->delete();

        if (! empty($priority)) {
            foreach ((array) $priority as $model_id) {
                ErpPriority::create(
                    [
                        'model_id' => $model_id,
                        'model_type' => Task::class,
                        'user_id' => $user_id,
                    ]
                );
            }

            $developerTask = Task::select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.assign_from')->join(
                'erp_priorities', function ($query) use ($user_id) {
                    $user_id = is_null($user_id) ? 0 : $user_id;
                    $query->on('erp_priorities.model_id', '=', 'tasks.id');
                    $query->where('erp_priorities.model_type', '=', Task::class);
                    $query->where('user_id', $user_id);
                }
            )->whereNull('is_verified')->orderBy('erp_priorities.id')->get();

            $message = '';
            $i = 1;

            foreach ($developerTask as $value) {
                $message .= $i.' : #Task-'.$value->id.'-'.$value->task_subject."\n";
                $i++;
            }

            if (! empty($message)) {
                $requestData = new Request;
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $user_id;

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

        return response()->json(
            [
                'status' => 'success',
            ]
        );
    }

    public function store(StoreTaskModuleRequest $request)
    {
        dd('We are not using this function anymore, If you reach here, that means that we have to change this.');
        $data = $request->except('_token');
        $data['assign_from'] = Auth::id();

        if ($request->task_type == 'quick_task') {
            $data['is_statutory'] = 0;
            $data['category'] = 6;
            $data['model_type'] = $request->model_type;
            $data['model_id'] = $request->model_id;
        }

        if ($request->task_type != 'note-task') {
            if ($request->assign_to) {
                $data['assign_to'] = $request->assign_to[0];
            } else {
                $data['assign_to'] = $request->assign_to_contacts[0];
            }
        }

        if (! empty($data['status'])) {
            $data['status'] = 3;
        }

        $task = Task::create($data);

        if ($request->is_statutory == 3) {
            foreach ($request->note as $note) {
                if ($note != null) {
                    Remark::create(
                        [
                            'taskid' => $task->id,
                            'remark' => $note,
                            'module_type' => 'task-note',
                        ]
                    );
                }
            }
        }

        if ($request->task_type != 'note-task') {
            if ($request->assign_to) {
                foreach ($request->assign_to as $user_id) {
                    $task->users()->attach([$user_id => ['type' => User::class]]);
                }
            }

            if ($request->assign_to_contacts) {
                foreach ($request->assign_to_contacts as $contact_id) {
                    $task->users()->attach([$contact_id => ['type' => Contact::class]]);
                }
            }
        }

        if ($task->is_statutory != 1) {
            $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
        } else {
            $message = $task->task_subject.'. '.$task->task_details;
        }

        $params = [
            'number' => null,
            'user_id' => Auth::id(),
            'approved' => 1,
            'status' => 2,
            'task_id' => $task->id,
            'message' => $message,
        ];
        if (count($task->users) > 0) {
            if ($task->assign_from == Auth::id()) {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $user->id;
                    } else {
                        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                    }
                }
            } else {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $task->assign_from;
                    } else {
                        if ($user->id != Auth::id()) {
                            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                }
            }
        }

        if (count($task->contacts) > 0) {
            foreach ($task->contacts as $key => $contact) {
                if ($key == 0) {
                    $params['contact_id'] = $task->assign_to;
                } else {
                    app(WhatsAppController::class)->sendWithThirdApi($contact->phone, null, $params['message']);
                }
            }
        }

        $chat_message = ChatMessage::create($params);
        ChatMessagesQuickData::updateOrCreate(
            [
                'model' => Task::class,
                'model_id' => $params['task_id'],
            ], [
                'last_communicated_message' => @$params['message'],
                'last_communicated_message_at' => $chat_message->created_at,
                'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
            ]
        );

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('task', $myRequest);

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $request->input('assign_to'))->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        $taskSummery = substr($message, 0, 200);

        $hubstaffTaskId = $this->createHubstaffTask(
            $taskSummery, $hubstaffUserId, $hubstaff_project_id
        );

        if ($hubstaffTaskId) {
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->save();
        }
        if ($hubstaffUserId) {
            $task = new HubstaffTask;
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }

        $task_statuses = TaskStatus::all();

        if ($request->ajax()) {
            $hasRender = request('has_render', false);

            if (! empty($hasRender)) {
                $users = Helpers::getUserArray(User::all());
                $priority = ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

                if ($task->is_statutory == 1) {
                    $mode = 'task-module.partials.statutory-row';
                } elseif ($task->is_statutory == 3) {
                    $mode = 'task-module.partials.discussion-pending-raw';
                } else {
                    $mode = 'task-module.partials.pending-row';
                }

                $view = (string) view($mode, compact('task', 'priority', 'users', 'task_statuses'));

                return response()->json(
                    [
                        'code' => 200,
                        'statutory' => $task->is_statutory,
                        'raw' => $view,
                    ]
                );
            }

            return response('success');
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    public function createHubstaffTask(string $taskSummary, ?int $hubstaffUserId, int $projectId, bool $shouldRetry = true)
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
                $url, [
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
                        $taskSummary, $hubstaffUserId, $projectId, false
                    );
                }
            }
        }

        return false;
    }

    public function flag(Request $request): JsonResponse
    {
        if ($request->task_type == 'DEVTASK') {
            $task = DeveloperTask::find($request->task_id);
        } else {
            $task = Task::find($request->task_id);
        }

        if ($task->is_flagged == 0) {
            $task->is_flagged = 1;
        } else {
            $task->is_flagged = 0;
        }

        $task->save();

        return response()->json(['is_flagged' => $task->is_flagged]);
    }

    public function remarkFlag(Request $request): JsonResponse
    {
        $remark = Remark::find($request->remark_id);

        if ($remark->is_flagged == 0) {
            $remark->is_flagged = 1;
        } else {
            $remark->is_flagged = 0;
        }

        $remark->save();

        return response()->json(['is_flagged' => $remark->is_flagged]);
    }

    public function plan(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        $task = Task::find($id);
        $task->time_slot = $request->time_slot;
        $task->planned_at = $request->planned_at;
        $task->general_category_id = $request->get('general_category_id', null);
        $task->save();

        // Save the data in user event
        $schedultDate = Carbon::parse($request->planned_at);
        $timeSlotArr = explode('-', $request->time_slot);
        $c_start_at = Carbon::parse("$request->planned_at ".$timeSlotArr[0]);
        $c_end_at = Carbon::parse("$request->planned_at ".$timeSlotArr[1]);
        // Delete old event of plan task task
        UserEvent::where('subject', 'LIKE', "%Planned task $task->id%")->delete();

        $userEvent = new UserEvent;
        $userEvent->user_id = $user->id;
        $userEvent->description = trim($timeSlotArr[0]).'-'.trim($timeSlotArr[1]).', '.$schedultDate->format('l').', '.$schedultDate->toDateString();
        $userEvent->subject = "Planned task $task->id ($task->task_subject)";
        $userEvent->date = $schedultDate;
        $userEvent->start = $c_start_at->toDateTime();
        $userEvent->end = $c_end_at->toDateTime();
        $userEvent->save();
        $time_slot = $request->time_slot;
        $html = view('task-row', compact('task', 'time_slot'))->render();

        return response()->json(
            [
                'html' => $html,
                'task' => $task,
            ]
        );
    }

    public function loadView(Request $request): JsonResponse
    {
        $tasks = Task::whereIn('id', $request->selected_tasks)->get();
        $users = Helpers::getUserArray(User::all());
        $view = view(
            'task-module.partials.task-view', [
                'tasks_view' => $tasks,
                'users' => $users,
            ]
        )->render();

        return response()->json(
            [
                'view' => $view,
            ]
        );
    }

    public function assignMessages(Request $request): RedirectResponse
    {
        $messages_ids = json_decode($request->selected_messages, true);

        foreach ($messages_ids as $message_id) {
            $message = ChatMessage::find($message_id);
            $message->task_id = $request->task_id;
            $message->save();
        }

        return redirect()->back()->withSuccess('You have successfully assign messages');
    }

    public function messageReminder(MessageReminderTaskModuleRequest $request): RedirectResponse
    {

        $message = ChatMessage::find($request->message_id);

        $additional_params = [
            'user_id' => $message->user_id,
            'task_id' => $message->task_id,
            'erp_user' => $message->erp_user,
            'contact_id' => $message->contact_id,
        ];

        $params = [
            'user_id' => Auth::id(),
            'message' => 'Reminder - '.$message->message,
            'type' => 'task',
            'data' => json_encode($additional_params),
            'sending_time' => $request->reminder_date,
        ];

        ScheduledMessage::create($params);

        return redirect()->back()->withSuccess('You have successfully set a reminder!');
    }

    public function convertTask(Request $request, $id): Response
    {
        $task = Task::find($id);

        $task->is_statutory = 3;
        $task->save();

        return response('success');
    }

    public function updateSubject(Request $request, $id): Response
    {
        $task = Task::find($id);
        $task->task_subject = $request->subject;
        $task->save();

        return response('success');
    }

    public function addNote(Request $request, $id): Response
    {
        Remark::create(
            [
                'taskid' => $id,
                'remark' => $request->note,
                'module_type' => 'task-note',
            ]
        );

        return response('success');
    }

    public function addSubnote(Request $request, $id): Response
    {
        $remark = Remark::create(
            [
                'taskid' => $id,
                'remark' => $request->note,
                'module_type' => 'task-note-subnote',
            ]
        );

        $id = $remark->id;

        return response(['success' => $id]);
    }

    public function updateCategory(Request $request, $id): Response
    {
        $task = Task::find($id);
        $task->category = $request->category;
        $task->save();

        return response('success');
    }

    public function show($id)
    {
        $task = Task::find($id);

        if (! $task) {
            abort(404, 'Task is not exist');
        }

        $chatMessages = ChatMessage::where('task_id', $id)->get();
        if ((! $task->users->contains(Auth::id()) && $task->is_private == 1) || ($task->assign_from != Auth::id() && $task->contacts()->count() > 0) || (! $task->users->contains(Auth::id()) && $task->assign_from != Auth::id() && Auth::id() != 6)) {
            return redirect()->back()->withErrors('This task is private!');
        }

        $users = User::all();
        $users_array = Helpers::getUserArray(User::all());
        $categories = TaskCategory::attr(
            [
                'title' => 'category',
                'class' => 'form-control input-sm',
                'placeholder' => 'Select a Category',
                'id' => 'task_category',
            ]
        )->selected($task->category)->renderAsDropdown();

        if (request()->has('keyword')) {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->where('remark', 'like', '%'.request()->keyword.'%')->paginate(20);
        } else {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->paginate(20);
        }

        $hiddenRemarks = $task->notes()->where('is_hide', 1)->get();

        return view(
            'task-module.task-show', [
                'task' => $task,
                'users' => $users,
                'users_array' => $users_array,
                'categories' => $categories,
                'taskNotes' => $taskNotes,
                'hiddenRemarks' => $hiddenRemarks,
                'chatMessages' => $chatMessages,
            ]
        );
    }

    public function searchTask(Request $request)
    {
        if ($request->input('selected_user') == '') {
            $userid = Auth::id();

            $searchMasterUserId = $userid;
            if ($request->search_master_user_id != '') {
                $searchMasterUserId = $request->search_master_user_id;
            }

            $searchSecondMasterUserId = $userid;
            if ($request->search_second_master_user_id != '') {
                $searchSecondMasterUserId = $request->search_second_master_user_id;
            }

        } else {
            $userid = $request->input('selected_user');

            $userqueryInner = '';

            if ($request->search_master_user_id != '') {
                $searchMasterUserId = $request->search_master_user_id;

                $userqueryInner .= ' OR master_user_id = '.$searchMasterUserId;
            }

            if ($request->search_second_master_user_id != '') {
                $searchSecondMasterUserId = $request->search_second_master_user_id;

                $userqueryInner .= ' OR  second_master_user_id = '.$searchSecondMasterUserId;
            }

        }

        if (! $request->input('type') || $request->input('type') == '') {
            $type = 'pending';
        } else {
            $type = $request->input('type');
        }

        $term = $request->term ?? '';
        $data['task'] = [];

        $search_term_suggestions = [];
        $search_suggestions = [];
        $assign_from_arr = [0];
        $special_task_arr = [0];
        $assign_to_arr = [0];
        $data['task']['pending'] = [];
        $data['task']['statutory_not_completed'] = [];
        $data['task']['completed'] = [];

        if ($type == 'pending') {
            // Get Pending tasks via model
            $data['task']['pending'] = Task::getSearchedTasks('pending', $request);

            foreach ($data['task']['pending'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['pending'] as $task) {
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    if ($task->assign_from_username) {
                        $search_term_suggestions[] = $task->assign_from_username;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    if ($task->assign_to_username) {
                        $search_term_suggestions[] = $task->assign_to_username;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        }
        //task pending backup
        $usersOrderByName = User::orderBy('name')->get();
        $data['users'] = $usersOrderByName->toArray();
        $data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

        // Lead user process starts
        $model_team = Team::where('user_id', auth()->user()->id)->get()->toArray();
        $team_members_array[] = auth()->user()->id;
        $team_id_array = [];
        $isTeamLeader = null;
        if (count($model_team) > 0) {
            $isTeamLeader = $model_team[0];
        }
        // Lead user process ends

        $selected_user = $request->input('selected_user');

        if ($isTeamLeader && ! Auth::user()->hasRole('Admin')) {
            $usrlst = [];

            for ($k = 0; $k < count($model_team); $k++) {
                $team_id_array[] = $model_team[$k]->id;
            }
            $model_user_model = TeamUser::whereIn('team_id', $team_id_array)->get()->toArray();
            for ($m = 0; $m < count($model_user_model); $m++) {
                $team_members_array[] = $model_user_model[$m]->user_id;
            }

            foreach ($usersOrderByName as $user) {
                if (in_array($user->id, $team_members_array)) {
                    $usrlst[] = $user;
                }
            }
        } else {
            $usrlst = $usersOrderByName;
        }

        $users = Helpers::getUserArray($usrlst);
        $all_task_categories = TaskCategory::all();
        $selected_category = $request->category;
        if (Auth::user()->hasRole('Admin')) {
            if (empty($request->category)) {
                $selected_category = 1;
            }
        }
        $categories = $approved_categories = [];
        foreach ($all_task_categories as $category) {
            $categories[$category->id] = $category->title;

            if ($category->is_approved == 1) {
                $approved_categories[] = $category->toArray();
            }
        }

        $task_categories_dropdown = nestable($approved_categories)->attr(
            [
                'name' => 'category',
                'class' => 'form-control input-sm',
            ]
        )->selected($selected_category)->renderAsDropdown();

        if (! empty($selected_user) && ! Helpers::getadminorsupervisor()) {
            return response()->json(['user not allowed'], 405);
        }
        $tasks_view = [];
        $priority = ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

        $openTask = Task::join('users as u', 'u.id', 'tasks.assign_to')->whereNull('tasks.is_completed')->groupBy('tasks.assign_to')->select(DB::raw('count(u.id) as total'), 'u.name as person')->pluck('total', 'person');

        if ($request->is_statutory_query == 3) {
            $title = 'Discussion tasks';
        } else {
            $title = 'Task & Activity';
        }

        $task_statuses = TaskStatus::all();

        return view('task-module.partials.menu-search-task-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'isTeamLeader'));
    }

    public function update(UpdateTaskModuleRequest $request, $id): RedirectResponse
    {

        $task = Task::find($id);
        $task->users()->detach();
        $task->contacts()->detach();

        if ($request->assign_to) {
            foreach ($request->assign_to as $user_id) {
                $task->users()->attach([$user_id => ['type' => User::class]]);
            }

            $task->assign_to = $request->assign_to[0];
        }

        if ($request->assign_to_contacts) {
            foreach ($request->assign_to_contacts as $contact_id) {
                $task->users()->attach([$contact_id => ['type' => Contact::class]]);
            }

            $task->assign_to = $request->assign_to_contacts[0];
        }

        if ($request->sending_time) {
            $task->sending_time = $request->sending_time;
        }

        $task->save();

        return redirect()->route('task.show', $id)->withSuccess('You have successfully reassigned users!');
    }

    public function makePrivate(Request $request, $id): JsonResponse
    {
        $task = Task::find($id);

        if ($task->is_private == 1) {
            $task->is_private = 0;
        } else {
            $task->is_private = 1;
        }

        $task->save();

        return response()->json(
            [
                'task' => $task,
            ]
        );
    }

    public function isWatched(Request $request, $id): JsonResponse
    {
        $task = Task::find($id);

        if ($task->is_watched == 1) {
            $task->is_watched = 0;
        } else {
            $task->is_watched = 1;
        }

        $task->save();

        return response()->json(
            [
                'task' => $task,
            ]
        );
    }

    public function complete(Request $request, $taskid)
    {
        $task = Task::find($taskid);
        if ($request->type == 'complete') {
            if (is_null($task->is_completed)) {
                $task->is_completed = date('Y-m-d H:i:s');
            } elseif (is_null($task->is_verified)) {
                if ($task->assignedTo) {
                    if ($task->assignedTo->fixed_price_user_or_job == 1) {
                        // Fixed price task.
                        if ($task->cost == null) {
                            if ($request->ajax()) {
                                return response()->json(
                                    [
                                        'message' => 'Please provide cost for fixed price task.',
                                    ], 500
                                );
                            }

                            return redirect()->back()->with('error', 'Please provide cost for fixed price task.');
                        }
                        if (! $task->is_milestone) {
                            $payment_receipt = new PaymentReceipt;
                            $payment_receipt->date = date('Y-m-d');
                            $payment_receipt->worked_minutes = $task->approximate;
                            $payment_receipt->rate_estimated = $task->cost;
                            $payment_receipt->status = 'Pending';
                            $payment_receipt->task_id = $task->id;
                            $payment_receipt->user_id = $task->assign_to;
                            $payment_receipt->save();
                        }
                    }
                }
                $task->is_verified = date('Y-m-d H:i:s');
            }
        } elseif ($request->type == 'clear') {
            $task->is_completed = null;
            $task->is_verified = null;
        }
        $task->save();

        if ($request->ajax()) {
            return response()->json(
                [
                    'task' => $task,
                ]
            );
        }

        return redirect()->back()->with('success', 'Task marked as completed.');
    }

    public function start(Request $request, $taskid)
    {
        $task = Task::find($taskid);

        $task->actual_start_date = date('Y-m-d H:i:s');
        $task->save();

        if ($request->ajax()) {
            return response()->json(
                [
                    'task' => $task,
                ]
            );
        }

        return redirect()->back()->with('success', 'Task started.');
    }

    public function statutoryComplete($taskid): RedirectResponse
    {
        $task = SatutoryTask::find($taskid);
        $task->completion_date = date('Y-m-d H:i:s');

        if ($task->assign_to == Auth::id()) {
            $task->save();
        }

        return redirect()->back()->with('success', 'Statutory Task marked as completed.');
    }

    public function addRemark(Request $request): JsonResponse
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        if ($request->module_type == 'document') {
            DocumentRemark::create(
                [
                    'document_id' => $id,
                    'remark' => $remark,
                    'module_type' => $request->module_type,
                    'user_name' => $request->user_name ? $request->user_name : Auth::user()->name,
                ]
            );
        } else {
            Remark::create(
                [
                    'taskid' => $id,
                    'remark' => $remark,
                    'module_type' => $request->module_type,
                    'user_name' => $request->user_name ? $request->user_name : Auth::user()->name,
                ]
            );
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function list(Request $request)
    {
        $pending_tasks = Task::where('is_statutory', 0)->whereNull('is_completed');
        $developer_tasks = DeveloperTask::orderByDesc('id');

        if (! Auth::user()->hasRole('Admin')) {
            $pending_tasks = $pending_tasks->where('assign_to', Auth::id());
            $developer_tasks = $developer_tasks->where('assigned_to', Auth::id());
        }
        if ($request->term && $request->term != null) {
            $pending_tasks = $pending_tasks->where('id', 'LIKE', "%$request->term%");
            $developer_tasks = $developer_tasks->where('id', 'LIKE', "%$request->term%");
        }

        if ($request->task_subject && $request->task_subject != null) {
            $pending_tasks = $pending_tasks->where('task_subject', 'LIKE', "%$request->task_subject%");
            $developer_tasks = $developer_tasks->where('subject', 'LIKE', "%$request->task_subject%");
        }

        if (is_array($request->user) && $request->user[0] != null) {
            $pending_tasks = $pending_tasks->whereIn('assign_to', $request->user);
            $developer_tasks = $developer_tasks->whereIn('assigned_to', $request->user);
        }

        if ($request->get('is-filter-created', null) === 'on' && $request->get('start_date', null) && $request->get('end_date', null)) {
            // dd($request->start_date,$request->get('end_date', null));
            $pending_tasks = $pending_tasks->where('tasks.created_at', '>=', $request->start_date);
            $developer_tasks = $developer_tasks->where('developer_tasks.created_at', '>=', $request->start_date);

            $pending_tasks = $pending_tasks->where('tasks.created_at', '<=', $request->end_date);
            $developer_tasks = $developer_tasks->where('developer_tasks.created_at', '<=', $request->end_date);
        }

        if ($request->get('is-filter-tracked', null) === 'on' && $request->get('start_date_tracked', null) && $request->get('end_date_tracked', null)) {
            $pending_tasks = $pending_tasks->where('tasks.m_start_date', '>=', $request->start_date_tracked);
            $developer_tasks = $developer_tasks->where('developer_tasks.m_start_date', '>=', $request->start_date_tracked);

            $pending_tasks = $pending_tasks->where('tasks.m_start_date', '<=', $request->end_date_tracked);
            $developer_tasks = $developer_tasks->where('developer_tasks.m_start_date', '<=', $request->end_date_tracked);
        }

        $users = Helpers::getUserArray(User::all());
        $user = $request->user ?? [];
        $date = $request->date ?? '';
        $taskstatus = TaskStatus::get();
        $isTeamLeader = Team::where('user_id', auth()->user()->id)->first();

        if ($request->get('export_excel', null)) {
            return $this->exportExcel($pending_tasks, $developer_tasks);
        }

        $pending_tasks = $pending_tasks->orderByDesc('id')->latest()->paginate(Setting::get('pagination'));
        $developer_tasks = $developer_tasks->latest()->paginate(Setting::get('pagination'));

        return view(
            'task-module.list', [
                'pending_tasks' => $pending_tasks,
                'taskstatus' => $taskstatus,
                'isTeamLeader' => $isTeamLeader,
                'users' => $users,
                'user' => $user,
                'date' => $date,
                'developer_tasks' => $developer_tasks,
                'start_date' => $request->get('start_date', ''),
                'end_date' => $request->get('end_date', ''),
            ]
        );
    }

    public function exportExcel($pendingTasks, $developerTasks)
    {
        return Excel::download(new AllTasksExcelExport($pendingTasks, $developerTasks), 'all_tasks.xlsx');
    }

    public function getremark(Request $request)
    {
        $id = $request->input('id');

        $task = Task::find($id);

        echo $task->remark;
    }

    public function deleteTask(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $task = Task::find($id);

        if ($task) {
            $task->remark = $request->input('comment');
            $task->save();

            $task->delete();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 200]);
        }
    }

    public function archiveTask($id, Request $request)
    {
        $task = Task::find($id);

        $task->delete();

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->to('/');
    }

    public function archiveTaskRemark($id): Response
    {
        $task = Remark::find($id);
        $remark = $task->remark;
        $task->delete_at = now();
        $task->update();

        return response(['success' => $remark]);
    }

    public function deleteStatutoryTask(Request $request): RedirectResponse
    {
        $id = $request->input('id');
        $task = SatutoryTask::find($id);
        $task->delete();

        return redirect()->back();
    }

    public function exportTask(Request $request): View
    {
        $users = $request->input('selected_user');
        $from = $request->input('range_start').' 00:00:00.000000';
        $to = $request->input('range_end').' 23:59:59.000000';

        $tasks = (new Task)->newQuery()->withTrashed()->whereBetween(
            'created_at', [
                $from,
                $to,
            ]
        )->where('assign_from', '!=', 0)->where('assign_to', '!=', 0);

        if (! empty($users)) {
            $tasks = $tasks->whereIn('assign_to', $users);
        }

        $tasks_list = $tasks->get()->toArray();
        $tasks_csv = [];
        $userList = Helpers::getUserArray(User::all());

        for ($i = 0; $i < count($tasks_list); $i++) {
            $task_csv = [];
            $task_csv['id'] = $tasks_list[$i]['id'];
            $task_csv['SrNo'] = $i + 1;
            $task_csv['assign_from'] = $userList[$tasks_list[$i]['assign_from']];
            $task_csv['assign_to'] = $userList[$tasks_list[$i]['assign_to']];
            $task_csv['type'] = $tasks_list[$i]['is_statutory'] == 1 ? 'Statutory' : 'Other';
            $task_csv['task_subject'] = $tasks_list[$i]['task_subject'];
            $task_csv['task_details'] = $tasks_list[$i]['task_details'];
            $task_csv['completion_date'] = $tasks_list[$i]['completion_date'];
            $task_csv['remark'] = $tasks_list[$i]['remark'];
            $task_csv['completed_on'] = $tasks_list[$i]['is_completed'];
            $task_csv['created_on'] = $tasks_list[$i]['created_at'];

            array_push($tasks_csv, $task_csv);
        }

        return view('task-module.export')->withTasks($tasks_csv);
    }

    public function outputCsv($fileName, $assocDataArray)
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename='.$fileName);
        if (isset($assocDataArray['0'])) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray as $values) {
                fputcsv($fp, $values);
            }
            fclose($fp);
        }
    }

    public static function getClasses($task)
    {
        $classes = ' ';
        $classes .= ' '.((empty($task) && $task->assign_from == Auth::user()->id) ? 'mytask' : '').' ';
        $classes .= ' '.((empty($task) && time() > strtotime($task->completion_date.' 23:59:59')) ? 'isOverdue' : '').' ';

        $task_status = empty($task) ? Helpers::statusClass($task->assign_status) : '';

        $classes .= $task_status;

        return $classes;
    }

    public function recurringTask()
    {
        $statutory_tasks = SatutoryTask::all()->toArray();

        foreach ($statutory_tasks as $statutory_task) {
            switch ($statutory_task['recurring_type']) {
                case 'EveryDay':
                    self::createTasksFromSatutary($statutory_task);
                    break;

                case 'EveryWeek':
                    if ($statutory_task['recurring_day'] == date('D')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;

                case 'EveryMonth':
                    if ($statutory_task['recurring_day'] == date('d')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;

                case 'EveryYear':
                    $dayNdate = date('d-n', strtotime($statutory_task['recurring_day']));
                    if ($dayNdate == date('d-n')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;
            }
        }
    }

    public static function createTasksFromSatutary($statutory_task)
    {
        $statutory_task['is_statutory'] = 1;
        $statutory_task['statutory_id'] = $statutory_task['id'];
        Task::create($statutory_task);
    }

    public function getTaskRemark(Request $request): JsonResponse
    {
        $id = $request->input('id');

        if (is_null($request->module_type)) {
            $remark = Task::getremarks($id);
        } else {
            $remark = Remark::where('module_type', $request->module_type)->where('taskid', $id)->get();
        }

        return response()->json($remark, 200);
    }

    public function addWhatsAppGroup(Request $request): JsonResponse
    {
        $whatsapp_number = '971562744570';
        $task = Task::findorfail($request->id);

        // Yogesh Sir Number
        $admin_number = User::findorfail(6);
        $assigned_from = Helpers::getUserArray(User::where('id', $task->assign_from)->get());
        $assigned_to = Helpers::getUserArray(User::where('id', $task->assign_to)->get());
        $task_id = $task->id;

        //Check if task id is present in Whats App Group
        $group = WhatsAppGroup::where('task_id', $task_id)->first();

        if ($group == null) {
            //First Create Group Using Admin id
            $phone = $admin_number->phone;
            $result = app(WhatsAppController::class)->createGroup($task_id, '', $phone, '', $whatsapp_number);
            if (isset($result['chatId']) && $result['chatId'] != null) {
                $chatId = $result['chatId'];
                //Create Group
                $group = new WhatsAppGroup;
                $group->task_id = $task_id;
                $group->group_id = $chatId;
                $group->save();
                //Save Whats App Group With Reference To Group ID
                $group_number = new WhatsAppGroupNumber;
                $group_number->group_id = $group->id;
                $group_number->user_id = $admin_number->id;
                $group_number->save();
                //Chat Message
                $params['task_id'] = $task_id;
                $params['group_id'] = $group->id;
                ChatMessage::create($params);
            } else {
                $group = new WhatsAppGroup;
                $group->task_id = $task_id;
                $group->group_id = null;
                $group->save();

                $group_number = new WhatsAppGroupNumber;
                $group_number->group_id = $group->id;
                $group_number->user_id = $admin_number->id;
                $group_number->save();

                $params['task_id'] = $task_id;
                $params['group_id'] = $group->id;
                $params['error_status'] = 1;
                ChatMessage::create($params);
            }
        }

        //iF assigned from is different from Yogesh Sir
        if ($admin_number->id != array_keys($assigned_from)[0]) {
            $request->request->add(
                [
                    'group_id' => $group->id,
                    'user_id' => array_keys($assigned_from),
                    'task_id' => $task->id,
                    'whatsapp_number' => $whatsapp_number,
                ]
            );

            $this->addGroupParticipant(request());
        }

        //Add Assigned To Into Whats App Group
        if (array_keys($assigned_to)[0] != null) {
            $request->request->add(
                [
                    'group_id' => $group->id,
                    'user_id' => array_keys($assigned_to),
                    'task_id' => $task->id,
                    'whatsapp_number' => $whatsapp_number,
                ]
            );

            $this->addGroupParticipant(request());
        }

        return response()->json(['group_id' => $group->id]);
    }

    public function addGroupParticipant(Request $request): RedirectResponse
    {
        $whatsapp_number = '971562744570';
        //Now Add Participant In the Group

        foreach ($request->user_id as $value) {
            $check = WhatsAppGroupNumber::where('group_id', $request->group_id)->where('user_id', $value)->first();
            if ($check == null) {
                $user = User::findorfail($value);
                $group = WhatsAppGroup::where('task_id', $request->task_id)->first();
                $phone = $user->phone;
                $result = app(WhatsAppController::class)->createGroup('', $group->group_id, $phone, '', $whatsapp_number);
                if (isset($result['add']) && $result['add'] != null) {
                    $task_id = $request->task_id;

                    $group_number = new WhatsAppGroupNumber;
                    $group_number->group_id = $request->group_id;
                    $group_number->user_id = $user->id;
                    $group_number->save();
                    $params['user_id'] = $user->id;
                    $params['task_id'] = $task_id;
                    $params['group_id'] = $request->group_id;
                    ChatMessage::create($params);
                } else {
                    $task_id = $request->task_id;

                    $group_number = new WhatsAppGroupNumber;
                    $group_number->group_id = $request->group_id;
                    $group_number->user_id = $user->id;
                    $group_number->save();
                    $params['user_id'] = $user->id;
                    $params['task_id'] = $task_id;
                    $params['group_id'] = $request->group_id;
                    $params['error_status'] = 1;
                    ChatMessage::create($params);
                }
            }
        }

        return redirect()->back()->with('message', 'Participants Added To Group');
    }

    public function getDetails(Request $request): JsonResponse
    {
        $task = Task::where('id', $request->get('task_id', 0))->first();

        if ($task) {
            return response()->json(
                [
                    'code' => 200,
                    'data' => $task,
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'message' => 'Sorry, no task found',
            ]
        );
    }

    public function saveNotes(Request $request): JsonResponse
    {
        $task = Task::where('id', $request->get('task_id', 0))->first();

        if ($task) {
            if ($task->is_statutory == 3) {
                foreach ($request->note as $note) {
                    if ($note != null) {
                        Remark::create(
                            [
                                'taskid' => $task->id,
                                'remark' => $note,
                                'module_type' => 'task-note',
                            ]
                        );
                    }
                }
            }

            return response()->json(
                [
                    'code' => 200,
                    'data' => $task,
                    'message' => 'Note added!',
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'message' => 'Sorry, no task found',
            ]
        );
    }

    public function getWebsiteList(Request $request): JsonResponse
    {
        if ($request->id[0] == 'all') {
            $websiteData = StoreWebsite::all();
        } else {
            $websiteData = StoreWebsite::whereIn('id', $request->id)->get();
        }
        $websiteCheckbox = '';
        foreach ($websiteData as $website) {
            $websiteCheckbox .= '<div class="col-4 py-1"><div style="float: left;height: auto;margin-right: 6px;"><input style="height:13px;" type="checkbox" name="website_name['.$website->id.']" value="'.$website->title.' - '.$request->cat_title.'"/></div> <div class=""  style="float: left;height: auto;margin-right: 6px;overflow-wrap: anywhere;width: 80%;">'.$website->website.'</div></div>';
        }

        return response()->json(
            [
                'code' => 200,
                'data' => $websiteCheckbox,
                'message' => 'List of website!',
            ]
        );
    }

    public function createMultipleTaskFromSortcutBugtrack(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $bug_list_ids = explode(',', $request->task_bug_ids);
            $model_bug_tracker = BugTracker::whereIn('id', $bug_list_ids)->get()->toArray();
            $bug_tracker_array = [];
            $postfix_task_bug = '';
            $model_name = 0;
            for ($p = 0; $p < count($model_bug_tracker); $p++) {
                $bug_primary_id = $model_bug_tracker[$p]['id'];
                $bug_tracker_array[$bug_primary_id] = $model_bug_tracker[$p];
                $model_name = $model_bug_tracker[0]['module_id'];
            }

            foreach ($bug_tracker_array as $bug_key => $bug_tracker_ele) {
                $postfix_task_bug .= '<br/>'.$bug_key.' :Step to Reproduce : <br/>'.$bug_tracker_ele['step_to_reproduce'];
                $postfix_task_bug .= '<br/>Expected Result : <br/>'.$bug_tracker_ele['expected_result'];
                $postfix_task_bug .= '<br/>Remark : <br/>'.$bug_tracker_ele['remark'];
            }

            $model_site_dev_category = SiteDevelopmentCategory::where('title', $model_name)->get()->toArray();
            $site_development_module_id = 0;
            if (count($model_site_dev_category) > 0 && $model_site_dev_category[0]['id'] > 0) {
                $site_development_module_id = $model_site_dev_category[0]['id'];
            }

            $website_multiple_arrays = array_keys($request->website_name);

            for ($m = 0; $m < count($website_multiple_arrays); $m++) {
                $data_site['site_development_category_id'] = $site_development_module_id;
                $data_site['bug_id'] = $request->site_id;
                $data_site['website_id'] = $website_multiple_arrays[$m];
                $data_site['created_at'] = date('Y-m-d H:i:s');
                $data_site['site_development_master_category_id'] = 4;

                $site_devlopment_exist = SiteDevelopment::where('bug_id', $request->site_id)->where('website_id', $website_multiple_arrays[$m])->get()->toArray();
                $site_developement_primary_id = 0;
                if (count($site_devlopment_exist) == 0) {
                    $res_site_dev = SiteDevelopment::create($data_site);
                    $site_developement_primary_id = $res_site_dev->id;
                } else {
                    if (isset($site_devlopment_exist[0]['id']) && $site_devlopment_exist[0]['id'] > 0) {
                        $site_developement_primary_id = $site_devlopment_exist[0]['id'];
                    }
                }
            }

            $site_dev_category_id = SiteDevelopment::where('id', $site_developement_primary_id)->select('site_development_category_id')->first();
            if (is_array($request->website_name)) {
                $sub_array = [];
                foreach ($request->website_name as $website) {
                    $sub_array[] = $website;
                }
                $site_developement_id = SiteDevelopment::select('id')->where(
                    [
                        'site_development_category_id' => $site_dev_category_id->site_development_category_id,
                        'website_id' => $website_multiple_arrays[0], //$key
                    ]
                )->first();
                if (isset($site_developement_id->id)) {
                    $website = implode(',', $sub_array);
                    $request->task_subject = $website;
                    $taskType = $request->task_type;
                    $data = $request->except('_token');
                    $data['site_id'] = 0;
                    $data['bug_id'] = $request->site_id;
                    $data['task_subject'] = $website;
                    $data['task_bug_ids'] = $request->task_bug_ids;
                    $data['task_detail'] .= $postfix_task_bug;
                    if ($taskType == '4' || $taskType == '5' || $taskType == '6') {
                        $data = [];
                        if (is_array($request->task_asssigned_to)) {
                            $data['assigned_to'] = $request->task_asssigned_to[0];
                        } else {
                            $data['assigned_to'] = $request->task_asssigned_to;
                        }
                        $data['user_id'] = loginId();
                        $data['subject'] = $website;
                        $data['task'] = $data['task_detail'];
                        $data['task_type_id'] = 1;
                        $data['cost'] = $request->get('cost', 0);
                        $data['status'] = DeveloperTask::DEV_TASK_STATUS_PLANNED;
                        $data['created_by'] = loginId();
                        if ($taskType == 5 || $taskType == 6) {
                            $data['task_type_id'] = 3;
                        }

                        $data['subject'] = $website;
                        $data['task_type'] = $taskType;
                        $data['task'] = $data['task_detail'];
                        $data['task_type_id'] = 1;
                        $data['user_feedback_cat_id'] = $request->get('user_feedback_cat_id');
                        $data['site_developement_id'] = 0;
                        $data['cost'] = $request->get('cost', 0);
                        $data['status'] = 'In Progress';
                        $data['created_by'] = Auth::id();

                        $task = $this->taskCreateMaster($data);

                        if ($task) {
                            if (count($bug_list_ids) > 0) {
                                $task_asssigned_user_to = $data['assigned_to'];
                                for ($k = 0; $k < count($bug_list_ids); $k++) {
                                    $bug_tacker_id = $bug_list_ids[$k];
                                    $bug_tracking = BugTracker::find($bug_tacker_id);
                                    $bug_tracking->bug_status_id = 6;
                                    $bug_tracking->assign_to = $task_asssigned_user_to;
                                    $bug_tracking->updated_at = date('Y-m-d H:i:s');
                                    $bug_tracking->updated_by = Auth::user()->name;
                                    $bug_tracking->save();
                                }
                            }
                        }

                        if (request('need_review_task')) {
                            $data['parent_review_task_id'] = $task->id;
                            $this->developerTaskCreate($data);
                        }
                    } else {
                        $data['site_developement_id'] = 0;
                        $data['task_subject'] = $website;
                        $data['task_type'] = $taskType;
                        $data['assign_from'] = loginId();
                        $data['status'] = 5;
                        $data['customer_id'] = $data['customer_id'] ?? null;
                        $data['cost'] = $data['cost'] ?? null;

                        $task = $this->taskCreateMaster($data);

                        if ($task) {
                            if (count($bug_list_ids) > 0) {
                                if (is_array($request->task_asssigned_to)) {
                                    $data['assigned_to'] = $request->task_asssigned_to[0];
                                } else {
                                    $data['assigned_to'] = $request->task_asssigned_to;
                                }
                                $task_asssigned_user_to = $data['assigned_to'];
                                for ($k = 0; $k < count($bug_list_ids); $k++) {
                                    $bug_tacker_id = $bug_list_ids[$k];
                                    $bug_tracking = BugTracker::find($bug_tacker_id);
                                    $bug_tracking->bug_status_id = 6;
                                    $bug_tracking->assign_to = $task_asssigned_user_to;
                                    $bug_tracking->updated_at = date('Y-m-d H:i:s');
                                    $bug_tracking->updated_by = Auth::user()->name;
                                    $bug_tracking->save();
                                }
                            }
                        }

                        if (request('need_review_task')) {
                            $data['parent_review_task_id'] = $task->id;
                            $reviewTask = $this->taskCreateMaster($data);
                        }
                    }
                }
            } else {
                $this->createTaskFromSortcut($request);

                if (count($bug_list_ids) > 0) {
                    if (is_array($request->task_asssigned_to)) {
                        $task_asssigned_user_to = $request->task_asssigned_to[0];
                    } else {
                        $task_asssigned_user_to = $request->task_asssigned_to;
                    }
                    for ($k = 0; $k < count($bug_list_ids); $k++) {
                        $bug_tacker_id = $bug_list_ids[$k];
                        $bug_tracking = BugTracker::find($bug_tacker_id);
                        $bug_tracking->bug_status_id = 6;
                        if ($task_asssigned_user_to > 0) {
                            $bug_tracking->assign_to = $task_asssigned_user_to;
                        }
                        $bug_tracking->updated_at = date('Y-m-d H:i:s');
                        $bug_tracking->updated_by = Auth::user()->name;
                        $bug_tracking->save();
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Your quick task has been created!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function createMultipleTaskFromSortcut(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $site_dev_category_id = SiteDevelopment::where('id', $request->site_id)->select('site_development_category_id')->first();
            if (is_array($request->website_name)) {
                foreach ($request->website_name as $key => $website) {
                    $site_developement_id = SiteDevelopment::select('id')->where(
                        [
                            'site_development_category_id' => $site_dev_category_id->site_development_category_id,
                            'website_id' => $key,
                        ]
                    )->first();
                    if (isset($site_developement_id->id)) {
                        $request->task_subject = $website;
                        $taskType = request('task_type');
                        $data = $request->except('_token');
                        $data['site_id'] = $site_developement_id->id;
                        $data['task_subject'] = $website;
                        if ($taskType == '4' || $taskType == '5' || $taskType == '6') {
                            $data = [];
                            if (is_array($request->task_asssigned_to)) {
                                $data['assigned_to'] = $request->task_asssigned_to[0];
                            } else {
                                $data['assigned_to'] = $request->task_asssigned_to;
                            }
                            $data['user_id'] = loginId();
                            $data['subject'] = $website;
                            $data['task'] = $request->get('task_detail');
                            $data['task_type_id'] = 1;
                            $data['site_developement_id'] = $request->get('site_id');
                            $data['cost'] = $request->get('cost', 0);
                            $data['status'] = DeveloperTask::DEV_TASK_STATUS_PLANNED;
                            $data['created_by'] = loginId();
                            if ($taskType == 5 || $taskType == 6) {
                                $data['task_type_id'] = 3;
                            }

                            $data['subject'] = $website;
                            $data['task'] = $request->get('task_detail');
                            $data['task_type_id'] = 1;
                            $data['user_feedback_cat_id'] = $request->get('user_feedback_cat_id');
                            $data['site_developement_id'] = $request->get('site_id');
                            $data['cost'] = $request->get('cost', 0);
                            $data['status'] = 'In Progress';
                            $data['created_by'] = Auth::id();

                            $task = $this->developerTaskCreate($data);

                            if (request('need_review_task')) {
                                $data['parent_review_task_id'] = $task->id;
                                $this->developerTaskCreate($data);
                            }
                        } else {
                            $data['site_developement_id'] = $site_developement_id->id;
                            $data['task_subject'] = $website;
                            $data['task_type'] = $data['task_type'] ?? null;
                            $data['assign_from'] = loginId();
                            $data['status'] = 5; // Planned - As per DEVTASK-22162
                            $data['customer_id'] = $data['customer_id'] ?? null;
                            $data['cost'] = $data['cost'] ?? null;

                            $task = $this->taskCreateMaster($data);

                            if (request('need_review_task')) {
                                $data['parent_review_task_id'] = $task->id;
                                $this->taskCreateMaster($data);
                            }
                        }
                    }
                }
            } else {
                $this->createTaskFromSortcut($request);
            }

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutPostman(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutDevOops(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {

            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutUserSchedules(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromScriptDocument(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {

            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutMagentoProblems(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutWebsiteLogs(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutSentry(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createMultipleTaskFromSortcutSonar(TaskModuleCreateMultipleTaskFromSortcutBugtrackRequest $request): JsonResponse
    {
        try {
            $this->createTaskFromSortcut($request);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Your quick task has been created!',
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

    public function createTaskFromSortcut(CreateTaskFromSortcutTaskModuleRequest $request): JsonResponse
    {

        $taskType = request('task_type');
        $data = $request->except('_token');
        $quick_task = request('quick_task');

        if ($taskType == '4' || $taskType == '5' || $taskType == '6' || ($taskType == '0' && $quick_task == '1')) {
            $data = [];
            if (is_array($request->task_asssigned_to)) {
                $data['assigned_to'] = $request->task_asssigned_to[0];
            } else {
                $data['assigned_to'] = $request->task_asssigned_to;
            }
            $data['user_id'] = loginId();
            $data['subject'] = $request->get('task_subject');
            $data['task'] = $request->get('task_detail');
            $data['task_type_id'] = 1;
            $data['site_developement_id'] = $request->get('site_id');
            $data['cost'] = $request->get('cost', 0);
            $data['status'] = DeveloperTask::DEV_TASK_STATUS_PLANNED;
            $data['created_by'] = loginId();
            if ($taskType == 5 || $taskType == 6) {
                $data['task_type_id'] = 3;
            }

            $data['subject'] = $request->get('task_subject');
            $data['task'] = $request->get('task_detail');
            $data['task_type_id'] = 1;
            $data['user_feedback_cat_id'] = $request->get('user_feedback_vendor_id') ?? 0;
            $data['user_feedback_vendor_id'] = $request->get('user_feedback_cat_id') ?? 0;
            $data['site_developement_id'] = $request->get('site_id');
            $data['cost'] = $request->get('cost', 0);
            $data['status'] = 'In Progress';
            $data['created_by'] = Auth::id();

            $task = $this->developerTaskCreate($data);

            if (request('need_review_task')) {
                $data['parent_review_task_id'] = $task->id;
                $this->developerTaskCreate($data);
            }
        } else {
            $data['task_type'] = $data['task_type'] ?? null;
            $data['assign_from'] = loginId();
            $data['status'] = 5; // Planned - As per DEVTASK-22162
            $data['customer_id'] = $data['customer_id'] ?? null;
            $data['cost'] = $data['cost'] ?? null;

            $task = $this->taskCreateMaster($data);

            if (request('need_review_task')) {
                $data['parent_review_task_id'] = $task->id;
                $this->taskCreateMaster($data);
            }
        }

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Your quick task has been created!',
            ]
        );
    }

    public function taskCreateMaster($data)
    {
        if ($data['task_type'] ?? null) {
            $data['is_statutory'] = $data['task_type'];
        }
        if ($data['task_detail'] ?? null) {
            $data['task_details'] = $data['task_detail'];
        }
        if ($data['site_id'] ?? null) {
            $data['site_developement_id'] = $data['site_id'];
        }
        if ($data['category_id'] ?? null) {
            $data['category'] = $data['category_id'];
        }

        if ($temp = $data['task_asssigned_to'] ?? null) {
            $data['assign_to'] = is_array($temp) ? $temp[0] : $temp;
        }

        if ($data['task_type'] != 'note-task') {
            if (! isset($data['assign_to'])) {
                if ($temp = $data['assign_to_contacts'] ?? null) {
                    $data['assign_to'] = is_array($temp) ? $temp[0] : $temp;
                }
            }
        }

        if ($data['task_type'] == 'quick_task') {
            $data['is_statutory'] = 0;
            $data['category'] = 6;
        }

        if ($data['parent_review_task_id'] ?? 0) {
            $data['task_subject'] = $data['task_subject'].' - #REVIEW_TASK';
            $data['task_details'] = $data['task_details'].' - #REVIEW_TASK';
        }

        $newCreated = 0;

        // Discussion task
        if ($data['task_type'] == 3) {
            $task = Task::find($data['task_subject']);
            if (! $task) {
                $task = Task::create($data);
                $newCreated = 1;
            }

            $remarks = $task->task_subject;
            $exist = Remark::where('taskid', $task->id)->where('remark', $remarks)->where('module_type', 'task-note')->first();
            if (! $exist) {
                Remark::create(
                    [
                        'taskid' => $task->id,
                        'remark' => $remarks,
                        'module_type' => 'task-note',
                    ]
                );
            }
            if ($data['note'] ?? []) {
                $data['note'] = is_array($data['note']) ? $data['note'] : [$data['note']];
                foreach ($data['note'] as $note) {
                    if (trim($note)) {
                        Remark::create(
                            [
                                'taskid' => $task->id,
                                'remark' => $note,
                                'module_type' => 'task-note',
                            ]
                        );
                    }
                }
            }
        } else {
            $task = Task::create($data);
            $newCreated = 1;
        }

        if ($newCreated) {
            if (isset($data['task_for']) && $data['task_for'] == 'time_doctor') {
                $this->timeDoctorActions('TASK', $task, $data['time_doctor_project'], $data['time_doctor_account'], $data['assign_to']);
            } else {
                $this->hubstaffActions('TASK', $task);
            }
        }

        if ($task->is_statutory != 1) {
            $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
        } else {
            $message = $task->task_subject.'. '.$task->task_details;
        }

        if ($data['task_type'] != 'note-task') {
            if ($temp = $data['task_asssigned_to'] ?? null) {
                if (is_array($temp)) {
                    foreach ($temp as $user_id) {
                        $task->users()->attach([$user_id => ['type' => User::class]]);
                    }
                } else {
                    $task->users()->attach([$temp => ['type' => User::class]]);
                }
            }

            if ($temp = $data['assign_to_contacts'] ?? null) {
                if (is_array($temp)) {
                    foreach ($temp as $contact_id) {
                        $task->users()->attach([$contact_id => ['type' => Contact::class]]);
                    }
                } else {
                    $task->users()->attach([$temp => ['type' => Contact::class]]);
                }
            }
        }

        $params = [
            'number' => null,
            'user_id' => loginId(),
            'approved' => 1,
            'status' => 2,
            'task_id' => $task->id,
            'message' => $message,
        ];

        if (count($task->users) > 0) {
            if ($task->assign_from == Auth::id()) {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $user->id;
                    } else {
                        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                    }
                }
            } else {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $task->assign_from;
                    } else {
                        if ($user->id != Auth::id()) {
                            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                }
            }
        }

        if (count($task->contacts) > 0) {
            foreach ($task->contacts as $key => $contact) {
                if ($key == 0) {
                    $params['contact_id'] = $task->assign_to;
                } else {
                    app(WhatsAppController::class)->sendWithThirdApi($contact->phone, null, $params['message']);
                }
            }
        }

        $chat_message = ChatMessage::create($params);
        ChatMessagesQuickData::updateOrCreate(
            [
                'model' => Task::class,
                'model_id' => $params['task_id'],
            ], [
                'last_communicated_message' => @$params['message'],
                'last_communicated_message_at' => $chat_message->created_at,
                'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
            ]
        );

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('task', $myRequest);

        return $task;
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
                    $task->team_lead_id = $team->user_id;
                    $task->save();
                }
            } else {
                $isTeamLeader = Team::where('user_id', $task->assigned_to)->first();
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
            $this->timeDoctorActions('DEVTASK', $task, $data['time_doctor_project'], $data['assigned_to']);
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

    public function hubstaffActions($type, $task)
    {
        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        if ($type == 'DEVTASK') {
            $message = '#DEVTASK-'.$task->id.' => '.$task->subject;
            $assignedToId = $task->assigned_to;
        } elseif ($type == 'TASK') {
            $message = '#TASK-'.$task->id.' => '.$task->task_subject.'. '.$task->task_details;
            $assignedToId = $task->assign_to;
        } else {
            return false;
        }

        if ($assignedUser = HubstaffMember::where('user_id', $assignedToId)->first()) {
            $taskSummary = substr($message, 0, 200);
            $hubstaffTaskId = $this->createHubstaffTask(
                $taskSummary, $assignedUser->hubstaff_user_id, $hubstaff_project_id
            );

            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();

                $hubtask = new HubstaffTask;
                $hubtask->hubstaff_task_id = $hubstaffTaskId;
                $hubtask->project_id = $hubstaff_project_id;
                $hubtask->hubstaff_project_id = $hubstaff_project_id;
                $hubtask->summary = $message;
                $hubtask->save();

                return true;
            }
        }

        return false;
    }

    public function timeDoctorActions($type, $task, $projectId, $accountId, $assignTo)
    {
        $check_entry = 0;
        $project_data = [];
        $project_data['time_doctor_project'] = $projectId;
        $project_data['time_doctor_task_name'] = $task['task_subject'];
        $project_data['time_doctor_task_description'] = $task['task_details'];

        if ($type == 'DEVTASK') {
            $message = '#DEVTASK-'.$task->id.' => '.$task->subject;
        } elseif ($type == 'TASK') {
            $message = '#TASK-'.$task->id.' => '.$task->task_subject.'. '.$task->task_details;
        } else {
            return false;
        }

        $assignUsersData = TimeDoctorAccount::find($accountId);
        if ($assignUsersData && $assignUsersData->company_id && $assignUsersData->auth_token) {
            $timedoctor = Timedoctor::getInstance();
            $companyId = $assignUsersData->company_id;
            $accessToken = $assignUsersData->auth_token;

            $timeDoctorTaskResponse = $timedoctor->createGeneralTask($companyId, $accessToken, $project_data, $task->id, $type);
            $errorMessages = config('constants.TIME_DOCTOR_API_RESPONSE_MESSAGE');
            switch ($timeDoctorTaskResponse['code']) {
                case '401':
                    return ['code' => 500, 'data' => [], 'message' => $errorMessages['401']];
                    break;
                case '403':
                    return ['code' => 500, 'data' => [], 'message' => $errorMessages['403']];
                    break;
                case '409':
                    return ['code' => 500, 'data' => [], 'message' => $errorMessages['409']];
                    break;
                case '422':
                    return ['code' => 500, 'data' => [], 'message' => $errorMessages['422']];
                    break;
                case '500':
                case '404':
                    return ['code' => 500, 'data' => [], 'message' => $errorMessages['404']];
                    break;
                default:
                    $timeDoctorTaskId = $timeDoctorTaskResponse['data']['id'];
                    if ($timeDoctorTaskId) {
                        $task->time_doctor_task_id = $timeDoctorTaskId;
                        $task->save();

                        $time_doctor_task = new TimeDoctorTask;
                        $time_doctor_task->time_doctor_task_id = $timeDoctorTaskId;
                        $time_doctor_task->project_id = $projectId;
                        $time_doctor_task->time_doctor_project_id = $projectId;
                        $time_doctor_task->summery = $message;
                        $time_doctor_task->save();
                    }

                    return ['code' => 200, 'data' => [], 'message' => 'Time doctor task created successfully'];
                    break;
            }
        } else {
            return false;
        }

        return false;
    }

    //START - Purpose : Set Remined , Revise - DEVTASK-4354
    public function sendRemindMessage(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = Task::find($request->id);
                $msg = 'PLS ADD ESTIMATED TIME FOR TASK  '.'#TASK-'.$task->id.'-'.$task->subject;
                $chat = ChatMessage::create(
                    [
                        'number' => $receiver_user_phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->id,
                    ]
                );

                app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->user_id], $msg);
            }
        }

        return response()->json(
            [
                'message' => 'Remind message sent successfully',
            ]
        );
    }

    public function sendReviseMessage(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = Task::find($request->id);
                $msg = 'TIME NOT APPROVED REVISE THE ESTIMATED TIME FOR TASK '.'#TASK-'.$task->id.'-'.$task->subject;
                $chat = ChatMessage::create(
                    [
                        'number' => $receiver_user_phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->id,
                    ]
                );
                app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->assigned_to], $msg);
            }
        }

        return response()->json(
            [
                'message' => 'Revise message sent successfully',
            ]
        );
    }

    //END - DEVTASK-4354

    public function getDiscussionSubjects(): JsonResponse
    {
        $discussion_subjects = Task::where('is_statutory', 3)->where('is_verified', null)->pluck('task_subject', 'id')->toArray();

        return response()->json(
            [
                'code' => 200,
                'discussion_subjects' => $discussion_subjects,
            ]
        );
    }

    /***
     * Delete task note
     */
    public function deleteTaskNote(Request $request): Response
    {
        Remark::whereId($request->note_id)->delete();
        session()->flash('success', 'Deleted successfully.');

        return response(['success' => 'Deleted']);
    }

    /**
     * Hide task note from list
     */
    public function hideTaskRemark(Request $request): Response
    {
        Remark::whereId($request->note_id)->update(['is_hide' => 1]);
        session()->flash('success', 'Hide successfully.');

        return response(['success' => 'Hidden']);
    }

    public function assignMasterUser(Request $request): JsonResponse
    {
        $masterUserId = $request->get('master_user_id');
        $issue = Task::find($request->get('issue_id'));

        $user = User::find($masterUserId);

        if (! $user) {
            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'user not found',
                ], 500
            );
        }

        if ($request->get('lead') == '1') {
            $old_id = $issue->master_user_id;
            if (! $old_id) {
                $old_id = 0;
            }
            $issue->master_user_id = $masterUserId;
            $task_type = 'leaddeveloper';
        } else {
            $old_id = $issue->second_master_user_id;
            if (! $old_id) {
                $old_id = 0;
            }
            $issue->second_master_user_id = $masterUserId;
            $task_type = 'second_leaddeveloper';
        }

        $issue->save();

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $masterUserId)->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        $message = '#'.$issue->id.'. '.$issue->task_subject.'. '.$issue->task_details;
        $summary = substr($message, 0, 200);
        $hubstaffTaskId = $this->createHubstaffTask(
            $summary, $hubstaffUserId, $hubstaff_project_id
        );
        if ($hubstaffTaskId) {
            $issue->lead_hubstaff_task_id = $hubstaffTaskId;
            $issue->save();
        }
        if ($hubstaffTaskId) {
            $task = new HubstaffTask;
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }
        $taskUser = new TaskUserHistory;
        $taskUser->model = Task::class;
        $taskUser->model_id = $issue->id;
        $taskUser->old_id = ($old_id == '') ? 0 : $old_id;
        $taskUser->new_id = $masterUserId;
        $taskUser->user_type = $task_type;
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();

        return response()->json(
            [
                'status' => 'success',
            ]
        );
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json(
            [
                'name' => $name,
                'original_name' => $file->getClientOriginalName(),
            ]
        );
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        $loggedUser = $request->user();

        if (! $request->task_id || $request->task_id == '') {
            return response()->json(
                [
                    'code' => 500,
                    'data' => [],
                    'message' => 'Select one task',
                ]
            );
        }

        $documents = $request->input('document', []) ? $request->input('document', []) : $request->document;

        $task = Task::find($request->task_id);
        if (! empty($documents)) {
            $count = count([$documents]);

            $message = '['.$loggedUser->name.'] - #ISSUE-'.$task->id.' - '.$task->task_subject."\n\n ".$count.' new attchment'.($count > 1 ? 's' : '');

            foreach ($documents as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)->toDirectory('task-files/'.floor($task->id / config('constants.image_per_folder')))->upload();
                $task->attachMedia($media, config('constants.media_tags'));

                if (! empty($media->filename)) {
                    Media::where('filename', $media->filename)->update(['user_id' => Auth::id()]);
                }

                $message .= "\n".$file;
            }

            $message .= "\nhas been added. \n Please check it and add your comment if any.";

            MessageHelper::sendEmailOrWebhookNotification($task->users->pluck('id')->toArray(), $message);

            return response()->json(
                [
                    'code' => 200,
                    'data' => [],
                    'message' => 'Done!',
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 500,
                    'data' => [],
                    'message' => 'No documents for upload',
                ]
            );
        }
    }

    public function previewTaskImage($id): View
    {
        $task = Task::find($id);

        $records = [];
        if ($task) {
            $userList = User::pluck('name', 'id')->all();
            $task = Task::find($id);
            $userName = '';
            $mediaDetail = [];
            if ($task->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                    $imageExtensions = [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png',
                        'bmp',
                        'svg',
                        'svgz',
                        'cgm',
                        'djv',
                        'djvu',
                        'ico',
                        'ief',
                        'jpe',
                        'pbm',
                        'pgm',
                        'pnm',
                        'ppm',
                        'ras',
                        'rgb',
                        'tif',
                        'tiff',
                        'wbmp',
                        'xbm',
                        'xpm',
                        'xwd',
                    ];
                    $explodeImage = explode('.', getMediaUrl($media));
                    $extension = end($explodeImage);

                    if (in_array($extension, $imageExtensions)) {
                        $isImage = true;
                    } else {
                        $isImage = false;
                    }

                    $mediaDetail = Media::where('id', $media->id)->first();
                    if ($mediaDetail) {
                        $userName = User::where('id', $mediaDetail->user_id)->pluck('name')->first();
                    } else {
                        $userName = '';
                    }

                    $records[] = [
                        'media_id' => $id,
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'task_id' => $task->id,
                        'isImage' => $isImage,
                        'userList' => $userList,
                        'userName' => $userName,
                        'created_at' => $media->created_at,
                    ];
                }
            }
        }

        $records = array_reverse($records);
        $title = 'Preview images';

        return view('task-module.partials.preview-task-images', compact('title', 'records'));
    }

    public function SendTask(Request $request): JsonResponse
    {
        $id = $request->id;

        if ($request->type == 'TASK') {
            $task = Task::find($request->taskdata);
            $user = User::find($task->assign_to);
        } else {
            $task = DeveloperTask::find($request->taskdata);
            $user = User::find($task->user_id);
        }
        $taskdata = $request->taskdata;

        $media = Media::find($request->id);


        $userid = Auth::id();
        $msg = getMediaUrl($media);
        if ($user && $user->phone) {
            if ($request->type == 'TASK') {
                $params = ChatMessage::create(
                    [
                        'id' => $id,
                        'user_id' => $user->id,
                        'task_id' => $taskdata,

                        'sent_to_user_id' => $userid,

                        'erp_user' => $task->assign_to,
                        'contact_id' => $task->assign_to,
                        'message' => getMediaUrl($media),

                    ]
                );
            } else {
                $params = ChatMessage::create(
                    [
                        'id' => $id,
                        'user_id' => $userid,
                        'task_id' => $request->task_id,
                        'developer_task_id' => $task->id,
                        'sent_to_user_id' => $user->id,
                        'issue_id' => $task->id,
                        'erp_user' => $task->assign_to,
                        'contact_id' => $task->assign_to,
                        'message' => getMediaUrl($media),

                    ]
                );
            }

            if ($params) {
                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                return response()->json(
                    [
                        'message' => 'Successfully Send File',
                    ], 200
                );
            }

            return response()->json(
                [
                    'message' => 'Something Was Wrong',
                ], 500
            );
        }
    }

    public function sendDocument(Request $request): JsonResponse
    {
        if ($request->id != null && $request->user_id != null) {
            $media = Media::find($request->id);
            $user = User::find($request->user_id);
            $id = $request->id;
            Task::find($request->user_id);
            $task_id = $request->doc_id;
            $userid = Auth::id();
            $msg = getMediaUrl($media);
            if ($user && $user->phone) {
                $params = ChatMessage::create(
                    [
                        'id' => $id,
                        // 'user_id' => $user->id,
                        'user_id' => $userid,
                        'task_id' => $task_id,
                        'erp_user' => $user->id,
                        'sent_to_user_id' => $userid,
                        'message' => getMediaUrl($media),

                    ]
                );

                if ($params) {
                    app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                    return response()->json(
                        [
                            'message' => 'Successfully Send Document',
                        ], 200
                    );
                }

                return response()->json(
                    [
                        'message' => 'Something Was Wrong',
                    ], 500
                );
            }
        }

        return response()->json(['message' => 'Sorry required fields is missing like User, Phone'], 500);
    }

    public function SendTaskSOP(Request $request): JsonResponse
    {
        $media = Media::find($request->id);

        $task = Task::find($request->task_id);
        $username = User::find($task->assign_to);


        Sop::create(
            [
                'name' => $username->name,
                'content' => getMediaUrl($media),

            ]
        );

        return response()->json(['message' => 'Data Added Successfully']);
    }

    public function approveTimeHistory(Request $request): JsonResponse
    {
        if (Auth::user()->isAdmin) {
            if (! $request->approve_time || $request->approve_time == '' || ! $request->developer_task_id || $request->developer_task_id == '') {
                return response()->json(
                    [
                        'message' => 'Select one time first',
                    ], 500
                );
            }
            DeveloperTaskHistory::where('developer_task_id', $request->developer_task_id)->where('attribute', 'estimation_minute')->where('model', Task::class)->update(['is_approved' => 0]);
            $history = DeveloperTaskHistory::find($request->approve_time);
            $history->is_approved = 1;
            $history->save();

            $task = Task::find($request->developer_task_id);
            $task->status = Task::TASK_STATUS_APPROVED;
            $task->save();

            $time = $history->new_value !== null ? $history->new_value : $history->old_value;
            $msg = 'TIME APPROVED FOR TASK '.'#DEVTASK-'.$task->id.'-'.$task->subject.' - '.$time.' MINS';

            $user = User::find($request->user_id);
            $admin = Auth::user();
            $master_user = User::find($task->master_user_id);

            if ($user) {
                if ($admin->phone) {
                    $chat = ChatMessage::create(
                        [
                            'number' => $admin->phone,
                            'user_id' => $user->id,
                            'customer_id' => $user->id,
                            'message' => $msg,
                            'status' => 0,
                            'developer_task_id' => $request->developer_task_id,
                        ]
                    );
                } elseif ($user->phone) {
                    $chat = ChatMessage::create(
                        [
                            'number' => $user->phone,
                            'user_id' => $user->id,
                            'customer_id' => $user->id,
                            'message' => $msg,
                            'status' => 0,
                            'developer_task_id' => $request->developer_task_id,
                        ]
                    );
                } elseif ($master_user && $master_user->phone) {
                    $chat = ChatMessage::create(
                        [
                            'number' => $master_user->phone,
                            'user_id' => $user->id,
                            'customer_id' => $user->id,
                            'message' => $msg,
                            'status' => 0,
                            'developer_task_id' => $request->developer_task_id,
                        ]
                    );
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
                }
            }

            return response()->json(
                [
                    'message' => 'Success',
                ], 200
            );
        }

        return response()->json(
            [
                'message' => 'Only admin can approve',
            ], 500
        );
    }

    public function getTrackedHistory(Request $request): JsonResponse
    {
        $id = $request->id;
        $type = $request->type;
        if ($type == 'lead') {
            //     $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,tasks.master_user_id,users.name FROM `hubstaff_activities`  join tasks on tasks.lead_hubstaff_task_id = hubstaff_activities.task_id join users on users.id = tasks.master_user_id where tasks.id = ' . $id . ' group by starts_at_date'));
            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at_date'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'tasks.master_user_id',
                'users.name'
            )
                ->join('tasks', 'tasks.lead_hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'tasks.master_user_id')
                ->where('tasks.id', $id)
                ->groupBy(DB::raw('CAST(hubstaff_activities.starts_at AS DATE)'))
                ->get();

        } else {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,tasks.assign_to,users.name FROM `hubstaff_activities`  join tasks on tasks.hubstaff_task_id = hubstaff_activities.task_id join users on users.id = tasks.assign_to where tasks.id = ' . $id . ' group by starts_at_date'));
            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at_date'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'tasks.assign_to',
                'users.name'
            )
                ->join('tasks', 'tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'tasks.assign_to')
                ->where('tasks.id', $id)
                ->groupBy(DB::raw('CAST(hubstaff_activities.starts_at AS DATE)'))
                ->get();

        }

        return response()->json(['histories' => $task_histories]);
    }

    public function taskCreateGetRemark(Request $request): JsonResponse
    {
        try {
            $msg = '';
            if ($request->remark != '') {
                TaskRemark::create(
                    [
                        'task_id' => $request->task_id,
                        'task_type' => $request->type,
                        'updated_by' => Auth::id(),
                        'remark' => $request->remark,
                    ]
                );
                $msg = ' Created and ';
            }
            $taskRemarkData = TaskRemark::where(
                [
                    [
                        'task_id',
                        '=',
                        $request->task_id,
                    ],
                    [
                        'task_type',
                        '=',
                        $request->type,
                    ],
                ]
            )->get();
            $html = '';
            foreach ($taskRemarkData as $taskRemark) {
                $html .= '<tr>';
                $html .= '<td>'.$taskRemark->id.'</td>';
                $html .= '<td>'.$taskRemark->users->name.'</td>';
                $html .= '<td>'.$taskRemark->remark.'</td>';
                $html .= '<td>'.$taskRemark->created_at.'</td>';
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='".$taskRemark->remark."'></i></td>";
            }

            return response()->json(
                [
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Remark '.$msg.' listed Successfully',
                ]
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 500,
                    'data' => '',
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    public function devgetTaskRemark(Request $request): JsonResponse
    {
        if ($request->status != 'view') {
            if ($request->remark != '') {
                $remark = TaskRemark::create(
                    [
                        'task_id' => $request->task_id,
                        'task_type' => $request->type,
                        'updated_by' => Auth::id(),
                        'remark' => $request->remark,
                    ]
                );
            }
        } else {
            $remark = TaskRemark::where([['task_id', '=', $request->task_id], ['task_type', '=', $request->type]])->get();
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function createHubstaffManualTask(Request $request): JsonResponse
    {
        $task = Task::find($request->id);

        if ($task) {
            if ($request->task_for_modal == 'hubstaff') {
                if ($request->type == 'developer') {
                    $user_id = $task->assign_to;
                } else {
                    $user_id = $task->master_user_id;
                }
                $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

                $assignedUser = HubstaffMember::where('user_id', $user_id)->first();

                $hubstaffUserId = null;
                if ($assignedUser) {
                    $hubstaffUserId = $assignedUser->hubstaff_user_id;
                }
                $taskSummery = '#'.$task->id.'. '.$task->task_subject;
                if ($hubstaffUserId) {
                    $hubstaffTaskId = $this->createHubstaffTask(
                        $taskSummery, $hubstaffUserId, $hubstaff_project_id
                    );
                } else {
                    $log = new TaskHubstaffCreateLog;
                    $log->task_id = $request->id;
                    $log->error_message = 'Hubstaff member not found';
                    $log->user_id = Auth::id();
                    $log->save();

                    return response()->json(
                        [
                            'message' => 'Hubstaff member not found',
                        ], 500
                    );
                }
                if ($hubstaffTaskId) {
                    if ($request->type == 'developer') {
                        $task->hubstaff_task_id = $hubstaffTaskId;
                    } else {
                        $task->lead_hubstaff_task_id = $hubstaffTaskId;
                    }
                    $task->save();
                } else {
                    $log = new TaskHubstaffCreateLog;
                    $log->task_id = $request->id;
                    $log->error_message = 'Hubstaff task not create';
                    $log->user_id = Auth::id();
                    $log->save();

                    return response()->json(
                        [
                            'message' => 'Hubstaff task not created',
                        ], 500
                    );
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
                try {
                    $timeDoctorTaskResponse = $this->timeDoctorActions('TASK', $task, $request->time_doctor_project, $request->time_doctor_account, $request->assigned_to);

                    $log = new TaskHubstaffCreateLog;
                    $log->task_id = $request->id;
                    $log->error_message = $timeDoctorTaskResponse['message'] ?? '';
                    $log->user_id = Auth::id();
                    $log->save();

                    return response()->json([
                        'message' => $timeDoctorTaskResponse['message'],
                    ], $timeDoctorTaskResponse['code']);
                } catch (Exception $e) {
                    return response()->json([
                        'message' => $e->getMessage(),
                    ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            return response()->json(
                [
                    'message' => 'Successful',
                ], 200
            );
        } else {
            $log = new TaskHubstaffCreateLog;
            $log->task_id = $request->id;
            $log->error_message = 'Task not found';
            $log->user_id = Auth::id();
            $log->save();

            return response()->json(
                [
                    'message' => 'Task not found',
                ], 500
            );
        }
    }

    public function getTaskCategories(): View
    {
        $categories = TaskCategory::where('is_approved', 1)->get();

        return view('task-module.partials.all-task-category', compact('categories'));
    }

    public function completeBulkTasks(Request $request): JsonResponse
    {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                $task = Task::find($t);
                $task->is_completed = date('Y-m-d H:i:s');
                $task->is_verified = date('Y-m-d H:i:s');
                if ($task->assignedTo) {
                    if ($task->assignedTo->fixed_price_user_or_job == 1) {
                        // Fixed price task.
                        continue;
                    }
                }
                $task->save();
            }
        }

        return response()->json(['message' => 'Successful']);
    }

    public function deleteBulkTasks(Request $request): JsonResponse
    {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                Task::where('id', $t)->delete();
            }
        }

        return response()->json(['message' => 'Successful']);
    }

    public function getTimeHistory(Request $request)
    {
        $id = $request->id;
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')->where('developer_task_id', $id)->where('model', Task::class)->where('attribute', 'estimation_minute')->select('developer_tasks_history.*', 'users.name')->orderByDesc('developer_tasks_history.id')->get();
        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    /* update task status
    */

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $task = Task::find($request->task_id);
            $old_status = $task->status;

            $task->status = $request->status;

            if (request('status') == Task::TASK_STATUS_IN_PROGRESS) {
                if ($task->actual_start_date == null || $task->actual_start_date == '0000-00-00 00:00:00') {
                    $task->actual_start_date = date('Y-m-d H:i:s');
                }
            }
            if (request('status') == Task::TASK_STATUS_DONE) {
                $task->actual_end_date = date('Y-m-d H:i:s');
            }

            $task->save();
            DeveloperTaskHistory::create(
                [
                    'developer_task_id' => $request->task_id,
                    'model' => Task::class,
                    'attribute' => 'task_status',
                    'old_value' => $old_status,
                    'new_value' => $task->status,
                    'user_id' => Auth::id(),
                ]
            );

            if ($task->status == 1) {
                $task_user = User::find($task->assign_to);
                if (! $task_user) {
                    return response()->json(
                        [
                            'message' => 'Please assign the task.',
                        ], 500
                    );
                }
                $team_user = TeamUser::where('user_id', $task->assign_to)->first();
                if ($team_user) {
                    $team_lead = Team::where('id', $team_user->team_id)->first();
                    if ($team_lead) {
                        $task_user_for_payment = User::find($team_lead->user_id);
                    }
                }
                if (empty($task_user_for_payment)) {
                    $task_user_for_payment = $task_user;
                }
                if ($task_user_for_payment->fixed_price_user_or_job == 0) {
                    return response()->json(
                        [
                            'message' => 'Please provide salary payment method for user.',
                        ], 500
                    );
                }
                if (! empty($task_user_for_payment)) {
                    if ($task_user_for_payment->fixed_price_user_or_job == 1) {
                        if ($task->cost == null) {
                            return response()->json(
                                [
                                    'message' => 'Please provide cost for fixed price task.',
                                ], 500
                            );
                        }
                        $rate_estimated = $task->cost ?? 0;
                    } elseif ($task_user_for_payment->fixed_price_user_or_job == 2) {
                        $userRate = UserRate::getRateForUser($task_user_for_payment->id);
                        if ($userRate && $userRate->hourly_rate !== null) {
                            $rate_estimated = $task->approximate * ($userRate->hourly_rate ?? 0) / 60;
                        } else {
                            return response()->json(
                                [
                                    'message' => 'Please provide hourly rate of user.',
                                ], 500
                            );
                        }
                    }
                    PaymentReceipt::create(
                        [
                            'status' => 'Pending',
                            'rate_estimated' => $rate_estimated,
                            'date' => date('Y-m-d'),
                            'currency' => '',
                            'user_id' => $task_user_for_payment->id,
                            'by_command' => 4,
                            'task_id' => $task->id,
                        ]
                    );

                    if ($task->status == 1) {
                        if ($task->task_bug_ids != '') {
                            $task_details_info = explode(',', $task->task_bug_ids);
                            if (count($task_details_info) > 0) {
                                $admin_user_id = 0;
                                $customer_role_users = RoleUser::where(['role_id' => 1])->with('user')->get()->toArray();
                                if (count($customer_role_users) > 0) {
                                    for ($m = 0; $m < count($customer_role_users); $m++) {
                                        if (isset($customer_role_users[$m]['user']['id']) && $customer_role_users[$m]['user']['id'] > 0) {
                                            $admin_user_id = $customer_role_users[$m]['user']['id'];
                                            $m = count($customer_role_users);
                                        }
                                    }
                                }

                                for ($k = 0; $k < count($task_details_info); $k++) {
                                    $bug_tacker_id = $task_details_info[$k];
                                    $bug_tracking = BugTracker::find($bug_tacker_id);
                                    if ($task->status == 3) { // In progress
                                        $bug_tracking->bug_status_id = 5;
                                    } elseif ($task->status == 1) { // complete
                                        $bug_tracking->bug_status_id = 6;
                                        if ($admin_user_id > 0) {
                                            $bug_tracking->assign_to = $admin_user_id;
                                        }
                                    } elseif ($task->status == 2) { // Discussing
                                        $bug_tracking->bug_status_id = 7;
                                        if ($admin_user_id > 0) {
                                            $bug_tracking->assign_to = $admin_user_id;
                                        }
                                    }

                                    $bug_tracking->updated_at = date('Y-m-d H:i:s');
                                    $bug_tracking->updated_by = Auth::user()->name;
                                    $bug_tracking->save();
                                }
                            }
                        }
                    }
                }
            }

            if ($task->status == 3 || $task->status == 2 || $task->status == 6 || $task->status == 15 || $task->status == 16) {
                if ($task->task_bug_ids != '') {
                    $task_details_info = explode(',', $task->task_bug_ids);
                    if (count($task_details_info) > 0) {
                        $admin_user_id = 0;
                        $customer_role_users = RoleUser::where(['role_id' => 1])->with('user')->get()->toArray();
                        if (count($customer_role_users) > 0) {
                            for ($m = 0; $m < count($customer_role_users); $m++) {
                                if (isset($customer_role_users[$m]['user']['id']) && $customer_role_users[$m]['user']['id'] > 0) {
                                    $admin_user_id = $customer_role_users[$m]['user']['id'];
                                    $m = count($customer_role_users);
                                }
                            }
                        }

                        for ($k = 0; $k < count($task_details_info); $k++) {
                            $bug_tacker_id = $task_details_info[$k];
                            $bug_tracking = BugTracker::find($bug_tacker_id);
                            if ($task->status == 3) { // In progress
                                $bug_tracking->bug_status_id = 6;
                            } elseif ($task->status == 15 || $task->status == 16) { // complete
                                $bug_tracking->bug_status_id = 7;
                                $bug_tracking->assign_to = $bug_tracking->created_by;
                            } elseif ($task->status == 2) { // Discussing
                                $bug_tracking->bug_status_id = 8;
                                if ($admin_user_id > 0) {
                                    $bug_tracking->assign_to = $admin_user_id;
                                }
                            } elseif ($task->status == 6) { // Discuss with Lead
                                $bug_tracking->bug_status_id = 10;
                                if ($admin_user_id > 0) {
                                    $bug_tracking->assign_to = $admin_user_id;
                                }
                            }

                            $bug_tracking->updated_at = date('Y-m-d H:i:s');
                            $bug_tracking->updated_by = Auth::user()->name;
                            $bug_tracking->save();
                        }
                    }
                }
            }

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'The task status updated.',
                ], 200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'The task status not updated.',
                ], 500
            );
        }
    }

    /* create new task status */

    public function createStatus(CreateStatusTaskModuleRequest $request): RedirectResponse
    {

        try {
            TaskStatus::create(['name' => $request->task_status]);

            return redirect()->back()->with('success', 'The task status created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateTaskReminder(Request $request): JsonResponse
    {
        $task = Task::find($request->get('task_id'));
        $task->frequency = $request->get('frequency');
        $task->reminder_message = $request->get('message');
        $task->reminder_from = $request->get('reminder_from', '0000-00-00 00:00');
        $task->reminder_last_reply = $request->get('reminder_last_reply', 0);
        $task->last_send_reminder = date('Y-m-d H:i:s');
        $task->save();

        $message = $request->get('message');
        if ($task->assignedTo?->phone) {
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(
                [
                    'task_id' => $task->id,
                    'message' => $message,
                    'status' => 1,
                ]
            );
            app(WhatsAppController::class)->sendMessage($requestData, 'task');
        }

        return response()->json(
            [
                'success',
            ]
        );
    }

    public function sendBrodCast(Request $request): JsonResponse
    {
        $taskIds = $request->selected_tasks;

        if (! empty($taskIds)) {
            foreach ($taskIds as $tid) {
                // started to send message
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add(
                    [
                        'task_id' => $tid,
                        'message' => $request->message,
                        'status' => 1,
                    ]
                );
                app(WhatsAppController::class)->sendMessage($requestData, 'task');
            }

            return response()->json(
                [
                    'code' => 200,
                    'message' => 'Message has been sent to all selected task',
                ]
            );
        }

        return response()->json(
            [
                'code' => 500,
                'message' => 'Please select atleast one task',
            ]
        );
    }

    public function CommunicationTaskStatus(Request $request): JsonResponse
    {
        $task = Task::find($request->get('task_id'));

        if ($task->communication_status == 0) {
            $status = 1;
        }
        if ($task->communication_status == 1) {
            $status = 0;
        }

        $updatetask = Task::find($request->get('task_id'));
        $updatetask->communication_status = $status;
        $updatetask->update();

        return response()->json(
            [
                'status' => 'success',
                'communication_status' => $status,
            ]
        );
    }

    public function recurringHistory(request $request): JsonResponse
    {
        $task_id = $request->input('task_id');
        $html = '';
        $chatData = LogChatMessage::where('task_id', $task_id)->where('task_time_reminder', 0)->orderByDesc('id')->get();
        $i = 1;
        if (count($chatData) > 0) {
            foreach ($chatData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->log_case_id.'</td>';
                $html .= '<td>'.$history->message.'</td>';
                $html .= '<td>'.$history->log_msg.'</td>';
                $html .= '<td>'.$history->created_at.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(
                [
                    'html' => $html,
                    'success' => true,
                ], 200
            );
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(
            [
                'html' => $html,
                'success' => true,
            ], 200
        );
    }

    public function AssignTaskToUser(Request $request): JsonResponse
    {
        $task = Task::find($request->get('issue_id'));
        $old_id = $task->assign_to;
        if (! $old_id) {
            $old_id = 0;
        } else {
            // DB::delete(
            //     'delete from task_users where task_id = ? AND user_id = ? AND type = ?', [
            //         $task->id,
            //         $old_id,
            //         User::class,
            //     ]
            // );
            TaskUser::where('task_id', $task->id)
                ->where('user_id', $old_id)
                ->where('type', User::class)
                ->delete();
        }
        $task->assign_to = $request->get('user_id');

        $slotAvailable = $this->userSchedulesLoadData($request->get('user_id'));

        if (! empty($slotAvailable)) {
            $task->start_date = $slotAvailable['st'];
            $task->due_date = $slotAvailable['en'];
        }

        $task->save();

        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $request->input('user_id'))->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        if ($task->is_statutory != 1) {
            $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
        } else {
            $message = $task->task_subject.'. '.$task->task_details;
        }

        $taskSummery = substr($message, 0, 200);

        $hubstaffTaskId = $this->createHubstaffTask(
            $taskSummery, $hubstaffUserId, $hubstaff_project_id
        );

        if ($hubstaffTaskId) {
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->save();
        }
        if ($hubstaffUserId) {
            $task = new HubstaffTask;
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }

        $taskUser = new TaskUserHistory;
        $taskUser->model = Task::class;
        $taskUser->model_id = $task->id;
        $taskUser->old_id = $old_id;
        $taskUser->new_id = $request->get('user_id');
        $taskUser->user_type = 'developer';
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();

        $values = [
            'task_id' => $request->get('issue_id'),
            'user_id' => $request->get('user_id'),
            'type' => User::class,
        ];
        TaskUser::insert($values);

        return response()->json(['status' => 'success']);
    }

    public function dropdownSlotWise()
    {
        try {
            $options = $this->userSchedulesLoadDataDropDown(request('userId'));

            $return = [];
            if (count($options)) {
                foreach ($options as $k => $v) {
                    $return[] = '<option value="'.$v.'">'.$v.'</option>';
                }
            }

            return response()->json(
                [
                    'list' => $return ? implode('', $return) : null,
                ]
            );
        } catch (Throwable $th) {
            return respException($th);
        }
    }

    public function userSchedulesLoadDataDropDown($user_id)
    {
        $usertemp = 0;
        $count = 0;
        $data = [];

        $isPrint = ! request()->ajax();

        $stDate = date('Y-m-d');
        $enDate = date('Y-m-d', strtotime(' + 5 days'));

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
                                    $userTasks = $res['userTasks'] ?? [];
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
                            $divSlots = [];
                            foreach ($slots as $slot) {
                                $title = '';
                                $display = [
                                    date('H:i', strtotime($slot['st'])),
                                    ' - ',
                                    date('H:i', strtotime($slot['en'])),
                                ];

                                if (in_array($slot['type'], ['AVL']) && $slot['slot_type'] != 'PAST') {
                                    $ut_array = [];
                                    $ut_arrayManually = [];

                                    if (! empty($slot['userTasks'])) {
                                        foreach ($slot['userTasks'] as $ut) {
                                            if ($ut['manually_assign'] == 1) {
                                                array_push($ut_arrayManually, $ut['typeId']);
                                            } else {
                                                array_push($ut_array, $ut['typeId']);
                                            }
                                        }
                                    }

                                    $developerTaskID = $ut_array;
                                    if (! empty($developerTaskID)) {
                                        $display[] = ' ('.implode(', ', $developerTaskID).')';

                                        $title = [];
                                        foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                            $title[] = $taskId.' - ('.$taskRow['status2'].')';
                                        }
                                        $title = implode(PHP_EOL, $title);
                                    }

                                    // $title
                                    $display = implode('', $display);

                                    $divSlots[] = $display;
                                }
                            }

                            for ($p = 0; $p < 13; $p++) {
                                $varid = 'slots'.$p;
                                if (isset($divSlots[$p])) {
                                    $str = str_replace('(PAST)', '<br>(PAST)', $divSlots[$p]);

                                    $data[] = $date.' ('.$str.')';
                                }
                            }

                            $usertemp = $usertemp + 1;
                        }
                    }
                }
            }

            return $data;
        }
    }

    public function userSchedulesLoadData($user_id)
    {
        $count = 0;

        $isPrint = ! request()->ajax();

        $stDate = $start_date = date('Y-m-d');
        $enDate = $start_date = date('Y-m-d', strtotime(' + 30 days'));
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

                                    $userTasks = $res['userTasks'] ?? [];
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
                            $divSlots = [];
                            foreach ($slots as $slot) {
                                $title = '';
                                $class = '';
                                $display = [
                                    date('H:i', strtotime($slot['st'])),
                                    ' - ',
                                    date('H:i', strtotime($slot['en'])),
                                ];


                                if (in_array($slot['type'], ['AVL', 'SMALL-LUNCH', 'LUNCH-START', 'LUNCH-END']) && $slot['slot_type'] != 'PAST') {
                                    $ut_array = [];
                                    $ut_arrayManually = [];

                                    if (! empty($slot['userTasks'])) {
                                        foreach ($slot['userTasks'] as $ut) {
                                            if ($ut['manually_assign'] == 1) {
                                                array_push($ut_arrayManually, $ut['typeId']);
                                            } else {
                                                array_push($ut_array, $ut['typeId']);
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
                        array_push($userTasks, $task);
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
                        array_push($userTasks, $task);
                    }
                }
                $list = array_values($list);
                $tasks['PLANNED'] = $list;
            }
        }

        // print_r($userTasks);
        return [
            'taskIds' => $taskIds ?? [],
            'userTasks' => $userTasks ?? [],
        ];
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

        // get the master branch SHA
        // https://api.github.com/repositories/:repoId/branches/master
        $url = 'https://api.github.com/repositories/'.$repositoryId.'/branches/'.$branchName;
        try {
            $response = $this->githubClient->get($url);
            $masterSha = json_decode($response->getBody()->getContents())->commit->sha;
        } catch (Exception $e) {
            return false;
        }

        // create a branch
        // https://api.github.com/repositories/:repo/git/refs
        $url = 'https://api.github.com/repositories/'.$repositoryId.'/git/refs';
        try {
            $this->githubClient->post(
                $url, [
                    RequestOptions::BODY => json_encode(
                        [
                            'ref' => 'refs/heads/'.$newBranchName,
                            'sha' => $masterSha,
                        ]
                    ),
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

    public function getUserHistory(Request $request): JsonResponse
    {
        if (isset($request->type)) {
            if ($request->type == 'developer') {
                $users = TaskUserHistory::where('model', DeveloperTask::class)->where('model_id', $request->id)->get();
            } else {
                $users = TaskUserHistory::where('model', Task::class)->where('model_id', $request->id)->get();
            }
        } else {
            $users = TaskUserHistory::where('model', Task::class)->where('model_id', $request->id)->get();
        }
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
        $html = view('partials.user_history', ['users' => $users])->render();

        return response()->json(
            [
                'html' => $html,
                'users' => $users,
                'html' => $html,
            ], 200
        );
    }

    public function getSiteDevelopmentTask(Request $request): JsonResponse
    {
        $site_developement_id = SiteDevelopment::where('website_id', $request->site_id)->pluck('id');
        $merged = [];
        if (! empty($site_developement_id)) {
            $taskStatistics['Devtask'] = DeveloperTask::whereIn('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

            $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->whereIn('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
            $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
            $taskStatistics = $query->get();
            $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->whereIn('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
            $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
            $othertaskStatistics = $query1->get();
            $merged = $othertaskStatistics->merge($taskStatistics);
        }

        return response()->json(
            [
                'code' => 200,
                'taskStatistics' => $merged,
            ]
        );
    }

    /*
    * AssignMultipleTaskToUser : Assign multiple task to user
    * DEVTASK-21672
    */
    public function AssignMultipleTaskToUser(Request $request): RedirectResponse
    {
        $tasks = $request->get('taskIDs');
        if (count($tasks) > 0) {
            foreach ($tasks as $tsk) {
                $task = Task::find($tsk);
                $old_id = $task->assign_to;
                if (! $old_id) {
                    $old_id = 0;
                } else {
                    // DB::delete(
                    //     'delete from task_users where task_id = ? AND user_id = ? AND type = ?', [
                    //         $task->id,
                    //         $old_id,
                    //         User::class,
                    //     ]
                    // );

                    TaskUser::where('task_id', $task->id)
                        ->where('user_id', $old_id)
                        ->where('type', User::class)
                        ->delete();
                }
                $task->assign_to = $request->get('user_assigned_to');
                $task->save();
                $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

                $assignedUser = HubstaffMember::where('user_id', $request->input('user_assigned_to'))->first();

                $hubstaffUserId = null;
                if ($assignedUser) {
                    $hubstaffUserId = $assignedUser->hubstaff_user_id;
                }
                if ($task->is_statutory != 1) {
                    $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
                } else {
                    $message = $task->task_subject.'. '.$task->task_details;
                }

                $taskSummery = substr($message, 0, 200);

                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery, $hubstaffUserId, $hubstaff_project_id
                );

                if ($hubstaffTaskId) {
                    $task->hubstaff_task_id = $hubstaffTaskId;
                    $task->save();
                }
                if ($hubstaffUserId) {
                    $task = new HubstaffTask;
                    $task->hubstaff_task_id = $hubstaffTaskId;
                    $task->project_id = $hubstaff_project_id;
                    $task->hubstaff_project_id = $hubstaff_project_id;
                    $task->summary = $message;
                    $task->save();
                }

                $taskUser = new TaskUserHistory;
                $taskUser->model = Task::class;
                $taskUser->model_id = $task->id;
                $taskUser->old_id = $old_id;
                $taskUser->new_id = $request->get('user_assigned_to');
                $taskUser->user_type = 'developer';
                $taskUser->updated_by = Auth::user()->name;
                $taskUser->save();

                $values = [
                    'task_id' => $task->id,
                    'user_id' => $request->get('user_assigned_to'),
                    'type' => User::class,
                ];
                TaskUser::insert($values);
            }
        }

        return redirect()->to('/development/automatic/tasks')->withSuccess('You have successfully assigned task!');
    }

    public function dropdownUserWise()
    {
        try {
            $dataArr = [];
            if ($userId = request('userId')) {
                $dTasks = DeveloperTask::where('assigned_to', $userId)->whereNotIn(
                    'status', [
                        DeveloperTask::DEV_TASK_STATUS_APPROVED,
                        DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS,
                        DeveloperTask::DEV_TASK_STATUS_REOPEN,
                        DeveloperTask::DEV_TASK_STATUS_PLANNED,
                    ]
                )->orderByDesc('id')->get();
                foreach ($dTasks as $key => $dTask) {
                    $dataArr['Developer Tasks']['DT-'.$dTask->id] = '(DT-'.$dTask->id.') - '.$dTask->subject;
                }

                $tasks = Task::where('assign_to', $userId)->whereNotIn(
                    'status', [
                        Task::TASK_STATUS_APPROVED,
                        Task::TASK_STATUS_IN_PROGRESS,
                        Task::TASK_STATUS_REOPEN,
                        Task::TASK_STATUS_PLANNED,
                    ]
                )->orderByDesc('id')->get();
                foreach ($tasks as $key => $task) {
                    $dataArr['Tasks']['T-'.$task->id] = '(T-'.$task->id.') - '.$task->task_subject;
                }
            }

            return response()->json(
                [
                    'list' => $dataArr ? makeDropdown($dataArr) : null,
                ]
            );
        } catch (Throwable $th) {
            return respException($th);
        }
    }

    public function slotMove()
    {
        try {
            if (! empty(request('tasks'))) {
                $tasks = explode(',', request('tasks'));
            }

            if (! empty(request('dev_tasks'))) {
                $dev_tasks = explode(',', request('dev_tasks'));
            }

            $phrase = request('taskTime');
            $healthy = ['(', ')'];
            $yummy = ['', ''];

            $newPhrase = str_replace($healthy, $yummy, $phrase);

            $taskTime = explode(' ', $newPhrase);

            if (! empty($tasks)) {
                foreach ($tasks as $key => $value) {
                    $task = Task::find($value);

                    if ($task) {
                        $task->start_date = $taskTime[0].' '.$taskTime[1];
                        $task->due_date = $taskTime[0].' '.$taskTime[2];

                        $task->save();
                    }
                }
            }

            if (! empty($dev_tasks)) {
                foreach ($dev_tasks as $key => $value) {
                    $task = DeveloperTask::find($value);

                    if ($task) {
                        $task->start_date = $taskTime[0].' '.$taskTime[1];
                        $task->estimate_date = $taskTime[0].' '.$taskTime[3];

                        $task->save();
                    }
                }
            }

            return respJson(200, 'Time slot updated successfully.');
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function slotAssign()
    {
        try {
            $newValue = request('date').' '.request('slot').':00';
            if ($id = isDeveloperTaskId(request('taskId'))) {
                if ($single = DeveloperTask::find($id)) {
                    if (! empty($single->estimate_date) || ! empty($single->start_date)) {
                        throw new Exception('You already have updated your estimate date.');
                    }
                    if (empty($single->estimate_minutes) || $single->estimate_minutes == null || $single->estimate_minutes == '') {
                        throw new Exception('Update your estimate time first.');
                    }

                    $oldValue = $single->start_date;
                    if ($oldValue == $newValue) {
                        return respJson(400, 'No change in time slot.');
                    }

                    $single->slotTaskRemarks = request('slotTaskRemarks');
                    $single->status = 'Planned';
                    $single->manually_assign = 1;
                    $single->start_date = $newValue;
                    $single->estimate_date = date('Y-m-d H:i:00', strtotime($single->start_date." +$single->estimate_minutes minute"));

                    $single->save();
                    $single->updateHistory('start_date', $oldValue, $newValue);

                    return respJson(200, 'Time slot updated successfully.');
                }
            } elseif ($id = isRegularTaskId(request('taskId'))) {
                if ($single = Task::find($id)) {
                    if (! empty($single->due_date) || ! empty($single->start_date)) {
                        throw new Exception('You already have updated your estimate date.');
                    }

                    if (empty($single->approximate) || $single->approximate == null || $single->approximate == '' || $single->approximate == 0) {
                        throw new Exception('Update your estimate time first.');
                    }

                    $single->slotTaskRemarks = request('slotTaskRemarks');
                    $single->manually_assign = 1;
                    $oldValue = $single->start_date;

                    $single->start_date = $newValue;
                    $single->due_date = date('Y-m-d H:i:00', strtotime($single->start_date." +$single->approximate minute"));

                    $single->save();

                    TaskHistoryForStartDate::historySave($single->id, $oldValue, $newValue, 0);

                    return respJson(200, 'Time slot updated successfully.');
                }
            }

            return respJson(404, 'No task found.');
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function taskGet()
    {
        try {
            $errors = reqValidate(
                request()->all(), [
                    'id' => 'required',
                ], []
            );
            if ($errors) {
                return respJson(400, $errors[0]);
            }
            $subquery = DB::raw('SELECT remark FROM developer_tasks_history WHERE developer_task_id=tasks.id ORDER BY id DESC');
            $single = Task::where('tasks.id', request('id'))->select(
                'tasks.*', DB::raw('(SELECT remark FROM developer_tasks_history WHERE developer_task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_remark'), DB::raw('(SELECT new_value FROM task_history_for_start_date WHERE task_id=tasks.id ORDER BY id DESC LIMIT 1) as task_start_date'), DB::raw("(SELECT new_due_date FROM task_due_date_history_logs WHERE task_id=tasks.id AND task_type='TASK' ORDER BY id DESC LIMIT 1) as task_new_due_date")
            )->first();
            if (! $single) {
                return respJson(404, 'No task found.');
            }

            return respJson(
                200, '', [
                    'data' => $single,
                    'user' => $single->assignedTo ?? null,
                ]
            );
        } catch (Throwable $th) {
            return respException($th);
        }
    }

    public function taskUpdateStartDate()
    {
        if ($new = request('value')) {
            try {
                if ($task = Task::find(request('task_id'))) {
                    if ($task->assign_to == Auth::user()->id) {
                        $params['message'] = 'Estimated Start Datetime: '.$new;
                        $params['user_id'] = Auth::user()->id;
                        $params['task_id'] = $task->id;
                        $params['approved'] = 1;
                        $params['status'] = 2;
                        ChatMessage::create($params);
                    }
                    $task->updateStartDate($new);

                    return respJson(200, 'Successfully updated.');
                }
            } catch (Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Start date is required.');
    }

    public function taskUpdateDueDate()
    {
        if ($new = request('value')) {
            try {
                if ($task = Task::find(request('task_id'))) {
                    if ($task->assign_to == Auth::user()->id) {
                        $params['message'] = 'Estimated End Datetime: '.$new;
                        $params['user_id'] = Auth::user()->id;
                        $params['task_id'] = $task->id;
                        $params['approved'] = 1;
                        $params['status'] = 2;
                        ChatMessage::create($params);
                    }
                    $task->updateDueDate($new);

                    return respJson(200, 'Successfully updated.');
                }
            } catch (Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Due date is required.');
    }

    public function updateCost()
    {
        if (! isAdmin()) {
            return respJson(403, 'Not authorized for users to update cost.');
        }
        $new = request('cost');
        if (is_numeric($new)) {
            if ($task = Task::find(request('task_id'))) {
                $oldValue = $task->cost;
                if ($task->assign_to == Auth::user()->id) {
                    $params['message'] = 'New Cost: '.$new;
                    $params['user_id'] = Auth::user()->id;
                    $params['task_id'] = $task->id;
                    $params['approved'] = 1;
                    $params['status'] = 2;
                    ChatMessage::create($params);
                }
                $task->update(['cost' => $new]);
                TaskHistoryForCost::create(
                    [
                        'task_id' => $task->id,
                        'old_value' => $oldValue,
                        'new_value' => $new,
                        'updated_by' => Auth::id(),
                    ]
                );

                return respJson(200, 'Successfully updated.');
            }

            return respJson(404, 'No task found.');
        }

        return respJson(400, 'Cost must be numeric.');
    }

    public function updateApproximate()
    {
        $task_id = request('task_id');
        $approximate = request('approximate');
        $remark = request('remark');

        if (! is_numeric($approximate)) {
            return respJson(400, 'Estimated time must be numeric.');
        }
        if ($task = Task::find($task_id)) {
            if (! isAdmin() && $task->assign_to != loginId()) {
                return respJson(403, 'Unauthorized access.');
            }
            if ($task->assign_to == Auth::user()->id) {
                $params['message'] = 'Estimated Time: [In Minutes] '.$approximate.',  '.'Remark: '.$remark;
                $params['user_id'] = Auth::user()->id;
                $params['task_id'] = $task_id;
                $params['approved'] = 1;
                $params['status'] = 2;
                ChatMessage::create($params);
            }

            DeveloperTaskHistory::create(
                [
                    'developer_task_id' => $task->id,
                    'model' => Task::class,
                    'attribute' => 'estimation_minute',
                    'old_value' => $task->approximate,
                    'remark' => $remark ?: null,
                    'new_value' => $approximate,
                    'user_id' => auth()->id(),
                ]
            );

            if (! isAdmin()) {
                $task->status = Task::TASK_STATUS_USER_ESTIMATED;
            }
            $task->approximate = $approximate;
            $task->save();

            if (Auth::user()->isAdmin()) {
                $user = User::find($task->assign_to);
                $msg = 'TIME ESTIMATED BY ADMIN FOR TASK '.'#DEVTASK-'.$task->id.'-'.$task->subject.' '.$approximate.' MINS';
            } else {
                $user = User::find($task->master_user_id);
                $msg = 'TIME ESTIMATED BY USER FOR TASK '.'#DEVTASK-'.$task->id.'-'.$task->subject.' '.$approximate.' MINS';
            }
            if ($user) {
                if ($receiver_user_phone = $user->phone) {
                    $chat = ChatMessage::create(
                        [
                            'number' => $receiver_user_phone,
                            'user_id' => $user->id,
                            'customer_id' => $user->id,
                            'message' => $msg,
                            'status' => 0,
                            'developer_task_id' => $task_id,
                        ]
                    );
                    app(WhatsAppController::class)->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);
                }
            }

            return respJson(200, 'Estimation updated successfully.');
        }

        return respJson(404, 'No task found.');
    }

    /**
     * Upload a task file to google drive
     */
    public function uploadFile(UploadFileTaskModuleRequest $request): RedirectResponse
    {

        $data = $request->all();
        try {
            foreach ($data['file'] as $file) {
                DB::transaction(function () use ($file, $data) {
                    $task = Task::find($data['task_id']);
                    $googleScreencast = new GoogleScreencast;

                    $googleScreencast->file_name = $file->getClientOriginalName();
                    $googleScreencast->file_name .= " (TASK-$task->id ".($task->task_subject ?? '-').')';

                    $googleScreencast->extension = $file->extension();
                    $googleScreencast->user_id = Auth::id();

                    $googleScreencast->read = '';
                    $googleScreencast->write = '';

                    $googleScreencast->remarks = $data['remarks'];
                    $googleScreencast->file_creation_date = $data['file_creation_date'];

                    $googleScreencast->belongable_id = $data['task_id'];
                    $googleScreencast->belongable_type = Task::class;
                    $googleScreencast->save();
                    UploadGoogleDriveScreencast::dispatchSync($googleScreencast, $file);
                });
            }

            return redirect()->back()->with('success', 'File is Uploaded to Google Drive.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

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
            if (isset($request->task_id)) {
                $result = GoogleScreencast::where('belongable_type', Task::class)->where('belongable_id', $request->task_id)->orderByDesc('id')->with('user')->get();
                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('task-module.google-drive-list', compact('result'))->render(),
                ]);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'data' => view('task-module.google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    public function taskModuleListLogHistory(Request $request): JsonResponse
    {
        $logs = TaskHubstaffCreateLog::with(['user', 'task'])
            ->where('task_id', $request->id)->get();

        return response()->json([
            'status' => true,
            'data' => $logs,
            'message' => 'Successfully get Logs history status',
            'status_name' => 'success',
        ], 200);
    }

    public function deleteDevTask(Request $request): JsonResponse
    {
        $id = $request->input('id');
        if ($request->tasktype == 'Devtask') {
            $task = DeveloperTask::find($id);
        } elseif ($request->tasktype == 'Othertask') {
            $task = Task::find($id);
        }

        if ($task) {
            $task->delete();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 200]);
        }
    }

    public function startTimeHistory(Request $request): JsonResponse
    {
        $task = Task::find($request->developer_task_id);

        if ($request->task_type == 1) {
            $input['m_start_date'] = Carbon::now();
            $input['task_start'] = 1;
            $input['status'] = 5;

            $history = new TaskStartEndHistory;
            $history->user_id = auth()->user()->id;
            $history->task_id = $request->developer_task_id;
            $history->start_date = Carbon::now();
            $history->save();
        } elseif ($request->task_type == 2) {
            $input['m_end_date'] = Carbon::now();
            $input['task_start'] = 3;

            $history = TaskStartEndHistory::where('task_id', $request->developer_task_id)->orderByDesc('id')->first();

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

        $task_histories = TaskStartEndHistory::where('task_id', $id)->orderByDesc('id')->get();

        return response()->json(['histories' => $task_histories]);
    }
}
