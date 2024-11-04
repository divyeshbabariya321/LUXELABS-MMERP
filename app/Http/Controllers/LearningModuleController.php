<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\ChatMessagesQuickData;
use App\Contact;
use App\Customer;
use App\DeveloperTask;
use App\DeveloperTaskHistory;
use App\DocumentRemark;
use App\ErpPriority;
use App\Helpers;
use App\Helpers\HubstaffTrait;
use App\Http\Requests\CreateStatusLearningModuleRequest;
use App\Http\Requests\MessageReminderLearningModuleRequest;
use App\Http\Requests\StoreLearningModuleRequest;
use App\Http\Requests\UpdateLearningModuleRequest;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffTask;
use App\Learning;
use App\LearningDueDateHistory;
use App\LearningModule;
use App\LearningStatusHistory;
use App\PaymentReceipt;
use App\Remark;
use App\SatutoryTask;
use App\ScheduledMessage;
use App\Setting;
use App\task;
use App\User;
use App\WhatsAppGroup;
use App\WhatsAppGroupNumber;
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
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use TaskStatus;

class LearningModuleController extends Controller
{
    use HubstaffTrait;

    public function __construct()
    {
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    public function index(Request $request)
    {
        if ($request->input('selected_user') == '') {
            $userid = Auth::id();
            $userquery = ' AND (assign_from = '.$userid.' OR  master_user_id = '.$userid.' OR  id IN (SELECT task_id FROM task_users WHERE user_id = '.$userid.' AND type LIKE "%User%")) ';
        } else {
            $userid = $request->input('selected_user');
            $userquery = ' AND (master_user_id = '.$userid.' OR  id IN (SELECT task_id FROM task_users WHERE user_id = '.$userid.' AND type LIKE "%User%")) ';
        }

        if (! $request->input('type') || $request->input('type') == '') {
            $type = 'pending';
        } else {
            $type = $request->input('type');
        }
        $activeCategories = LearningModule::where('is_active', 1)->pluck('id')->all();

        $categoryWhereClause = '';
        $category = '';
        $request->category = $request->category ? $request->category : 1;
        if ($request->category != '') {
            if ($request->category != 1) {
                $categoryWhereClause = "AND category = $request->category";
                $category = $request->category;
            } else {
                $category_condition = implode(',', $activeCategories);
                if ($category_condition != '' || $category_condition != null) {
                    $category_condition = '( '.$category_condition.' )';
                    $categoryWhereClause = 'AND category in '.$category_condition;
                } else {
                    $categoryWhereClause = '';
                }
            }
        }

        $term = $request->term ?? '';
        $searchWhereClause = '';

        if ($request->term != '') {
            $searchWhereClause = ' AND (id LIKE "%'.$term.'%" OR category IN (SELECT id FROM task_categories WHERE title LIKE "%'.$term.'%") OR task_subject LIKE "%'.$term.'%" OR task_details LIKE "%'.$term.'%" OR assign_from IN (SELECT id FROM users WHERE name LIKE "%'.$term.'%") OR id IN (SELECT task_id FROM task_users WHERE user_id IN (SELECT id FROM users WHERE name LIKE "%'.$term.'%")))';
        }
        $orderByClause = ' ORDER BY';
        if ($request->sort_by == 1) {
            $orderByClause .= ' learnings.created_at desc,';
        } elseif ($request->sort_by == 2) {
            $orderByClause .= ' learnings.created_at asc,';
        }
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
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;

            $orderByClause .= ' is_flagged DESC, message_created_at DESC';
            $isCompleteWhereClose = ' AND is_verified IS NULL ';

            if (! Auth::user()->isAdmin()) {
                $isCompleteWhereClose = ' AND is_verified IS NULL ';
            }
            if ($request->filter_by == 1) {
                $isCompleteWhereClose = ' AND is_completed IS NULL ';
            }
            if ($request->filter_by == 2) {
                $isCompleteWhereClose = ' AND is_completed IS NOT NULL AND is_verified IS NULL ';
            }

            // $data['task']['pending'] = DB::select('
            // SELECT learnings.*

            // FROM (
            //   SELECT * FROM learnings
            //   LEFT JOIN (
            // 	  SELECT
            // 	  chat_messages.id as message_id,
            // 	  chat_messages.task_id,
            // 	  chat_messages.message,
            // 	  chat_messages.status as message_status,
            // 	  chat_messages.sent as message_type,
            // 	  chat_messages.created_at as message_created_at,
            // 	  chat_messages.is_reminder AS message_is_reminder,
            // 	  chat_messages.user_id AS message_user_id
            // 	  FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="Task"
            //   ) as chat_messages  ON chat_messages.task_id = learnings.id
            // ) AS learnings
            // WHERE (id IS NOT NULL) AND is_statutory != 1 ' . $isCompleteWhereClose . $userquery . $categoryWhereClause . $searchWhereClause . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . '; ');

            $data['task']['pending'] = Learning::with('customer')->select(
                'learnings.*',
                'chat_messages.id as message_id',
                'chat_messages.task_id',
                'chat_messages.message',
                'chat_messages.status as message_status',
                'chat_messages.sent as message_type',
                'chat_messages.created_at as message_created_at',
                'chat_messages.is_reminder AS message_is_reminder',
                'chat_messages.user_id AS message_user_id'
            )
                ->leftJoin('chat_messages', function ($join) {
                    $join->on('chat_messages.task_id', '=', 'learnings.id')
                        ->whereNotIn('chat_messages.status', [7, 8, 9])
                        ->where('chat_messages_quick_datas.model', Task::class)
                        ->where('chat_messages_quick_datas.last_communicated_message_id', '=', 'chat_messages.id');
                })
                ->whereNotNull('learnings.id')
                ->where('learnings.is_statutory', '!=', 1)
                ->whereRaw($isCompleteWhereClose)
                ->whereRaw($userquery)
                ->whereRaw($categoryWhereClause)
                ->whereRaw($searchWhereClause)
                ->orderByRaw($orderByClause)
                ->limit($paginate)
                ->offset($offSet)
                ->get();

            foreach ($data['task']['pending'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['pending'] as $task) {
                $task->special_task = Task::find($task->id);
                $task->customer = Customer::find($task->customer_id);
                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'completed') {
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;
            $orderByClause .= ' last_communicated_at DESC';
            // $data['task']['completed'] = DB::select('
            //     SELECT *,
            // 	message_id,
            //     message,
            //     message_status,
            //     message_type,
            //     message_created_At as last_communicated_at
            //     FROM (
            //       SELECT * FROM learnings
            //      LEFT JOIN (
            // 		SELECT
            // 		chat_messages.id as message_id,
            // 		chat_messages.task_id,
            // 		chat_messages.message,
            // 		chat_messages.status as message_status,
            // 		chat_messages.sent as message_type,
            // 		chat_messages.created_at as message_created_at,
            // 		chat_messages.is_reminder AS message_is_reminder,
            // 		chat_messages.user_id AS message_user_id
            // 		FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\Task"
            //      ) AS chat_messages ON chat_messages.task_id = learnings.id
            //     ) AS learnings
            //     WHERE (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NOT NULL ' . $userquery . $categoryWhereClause . $searchWhereClause . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . ';');

            $data['task']['completed'] = Learning::with('customer')->select(
                'learnings.*',
                'chat_messages.id as message_id',
                'chat_messages.message',
                'chat_messages.status as message_status',
                'chat_messages.sent as message_type',
                'chat_messages.created_at as message_created_at',
                'chat_messages.is_reminder AS message_is_reminder',
                'chat_messages.user_id AS message_user_id'
            )
                ->leftJoin('chat_messages', function ($join) {
                    $join->on('chat_messages.task_id', '=', 'learnings.id')
                        ->whereNotIn('chat_messages.status', [7, 8, 9])
                        ->where('chat_messages_quick_datas.model', Task::class)
                        ->where('chat_messages_quick_datas.last_communicated_message_id', '=', 'chat_messages.id');
                })
                ->whereNotNull('learnings.id')
                ->where('learnings.is_statutory', '!=', 1)
                ->whereNotNull('learnings.is_verified')
                ->whereRaw($userquery)
                ->whereRaw($categoryWhereClause)
                ->whereRaw($searchWhereClause)
                ->orderByRaw($orderByClause)
                ->limit($paginate)
                ->offset($offSet)
                ->get();

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
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'statutory_not_completed') {
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;
            $orderByClause .= ' last_communicated_at DESC';
            // $data['task']['statutory_not_completed'] = DB::select('
            //        SELECT *,
            // 	   message_id,
            //        message,
            //        message_status,
            //        message_type,
            //        message_created_At as last_communicated_at

            //        FROM (
            //          SELECT * FROM learnings
            //          LEFT JOIN (
            // 				SELECT
            // 				chat_messages.id as message_id,
            // 				chat_messages.task_id,
            // 				chat_messages.message,
            // 				chat_messages.status as message_status,
            // 				chat_messages.sent as message_type,
            // 				chat_messages.created_at as message_created_at,
            // 				chat_messages.is_reminder AS message_is_reminder,
            // 				chat_messages.user_id AS message_user_id
            // 				FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\Task"
            //          ) AS chat_messages ON chat_messages.task_id = learnings.id

            //        ) AS learnings
            // 	   WHERE (id IS NOT NULL) AND is_statutory = 1 AND is_verified IS NULL ' . $userquery . $categoryWhereClause . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . ';');

            $data['task']['statutory_not_completed'] = Learning::select(
                'learnings.*',
                'chat_messages.id as message_id',
                'chat_messages.message',
                'chat_messages.status as message_status',
                'chat_messages.sent as message_type',
                'chat_messages.created_at as message_created_at',
                'chat_messages.is_reminder AS message_is_reminder',
                'chat_messages.user_id AS message_user_id'
            )
                ->leftJoin('chat_messages', function ($join) {
                    $join->on('chat_messages.task_id', '=', 'learnings.id')
                        ->whereNotIn('chat_messages.status', [7, 8, 9])
                        ->where('chat_messages_quick_datas.model', Task::class)
                        ->where('chat_messages_quick_datas.last_communicated_message_id', '=', 'chat_messages.id');
                })
                ->whereNotNull('learnings.id')
                ->where('learnings.is_statutory', 1)
                ->whereNull('learnings.is_verified')
                ->whereRaw($userquery)
                ->whereRaw($categoryWhereClause)
                ->orderByRaw($orderByClause)
                ->limit($paginate)
                ->offset($offSet)
                ->get();

            foreach ($data['task']['statutory_not_completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                $task->special_task = \Task::find($task->id);

                $search_suggestions[] = '#'.$task->id.' '.$task->task_subject.' '.$task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } else {
            //
        }

        $subjectList = Learning::select('learning_subject')->distinct()->pluck('learning_subject');

        $users = User::oldest()->get()->toArray();
        $data['users'] = $users;
        $data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

        //My code start
        $selected_user = $request->input('selected_user');
        $users = Helpers::getUserArray(User::orderby('name')->get());
        $task_categories = LearningModule::where('parent_id', 0)->get();
        $learning_module_dropdown = nestable(LearningModule::where('is_approved', 1)->where('parent_id', 0)->get()->toArray())->attr(['name' => 'learning_module', 'class' => 'form-control input-sm parent-module'])
            ->selected($request->category)
            ->renderAsDropdown();

        $learning_submodule_dropdown = LearningModule::where('is_approved', 1)->where('parent_id', '1')->get();

        $categories = [];
        foreach (LearningModule::all() as $category) {
            $categories[$category->id] = $category->title;
        }
        if (! empty($selected_user) && ! Helpers::getadminorsupervisor()) {
            return response()->json(['user not allowed'], 405);
        }
        //My code end
        $tasks_view = [];
        $priority = ErpPriority::where('model_type', '=', Learning::class)->pluck('model_id')->toArray();

        $openTask = Learning::join('users as u', 'u.id', 'learnings.assign_to')
            ->whereNull('learnings.is_completed')
            ->groupBy('learnings.assign_to')
            ->select(DB::raw('count(u.id) as total'), 'u.name as person')
            ->pluck('total', 'person');

        if ($request->is_statutory_query == 3) {
            $tasks_view = Learning::all();
            foreach ($tasks_view as $task) {
                $task->special_task = Task::find($task->id);
            }
            $title = 'Discussion learnings';
            $tasks = Task::where('is_statutory', 3)->where('task_subject', '!=', "''")->pluck('task_subject', 'id')->toArray();
        } else {
            $title = 'Learning & Activity';
        }

        $task_statuses = TaskStatus::all();

        if ($request->ajax()) {
            if ($type == 'pending') {
                return view('learning-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } elseif ($type == 'statutory_not_completed') {
                return view('learning-module.partials.statutory-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } elseif ($type == 'completed') {
                return view('learning-module.partials.completed-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } else {
                return view('learning-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            }
        }

        if ($request->is_statutory_query == 3) {
            return view('learning-module.discussion-tasks', compact('data', 'users', 'tasks', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'learning_module_dropdown', 'learning_submodule_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
        } else {
            $statusList = TaskStatus::orderBy('name')->pluck('name', 'id')->toArray();

            $learningsListing = Learning::query();

            if (! empty($request->get('user_id'))) {
                $learningsListing->where('learning_user', $request->get('user_id'));
            }

            if (! empty($request->get('subject'))) {
                $subject = $request->get('subject');
                $learningsListing->where('learning_subject', 'LIKE', "%$subject%");
            }

            if (! empty($request->get('task_status'))) {
                $learningsListing->whereIn('learning_status', $request->get('task_status'));
            }

            if (! empty($request->get('overduedate'))) {
                $learningsListing->whereDate('learning_duedate', '<', $request->get('overduedate'));
            }

            if (! empty($request->get('module'))) {
                $learningsListing->where('learning_module', $request->get('module'));
            }

            if (! empty($request->get('submodule'))) {
                $learningsListing->where('learning_submodule', $request->get('submodule'));
            }

            $learningsListing = $learningsListing->latest()->get();
            $usersForView = User::orderBy('name')->get();
            $providersForView = $usersForView;
            $modulesForView = LearningModule::where('parent_id', 0)->get();
            $statusesForView = TaskStatus::all();

            $submodulesForView = [];
            foreach ($learningsListing as $submodule) {
                $submodulesForView[$submodule->learning_module] = LearningModule::where('parent_id', $submodule->learning_module)->get();
            }

            $last_record_learning = Learning::with('learningUser')->latest()->first();
            $modules = LearningModule::where('parent_id', 0)->orderBy('title')->get();
            $submodules = LearningModule::where('parent_id', '!=', 0)->orderBy('title')->get();

            return view('learning-module.show', compact('data', 'usersForView', 'providersForView', 'modulesForView', 'statusesForView', 'submodulesForView', 'users', 'modules', 'submodules', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'learning_module_dropdown', 'learning_submodule_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'learningsListing', 'statusList', 'subjectList', 'last_record_learning'));
        }
    }

    public function updateCost(Request $request): JsonResponse
    {
        $task = Learning::find($request->task_id);

        if (Auth::user()->isAdmin()) {
            $task->cost = $request->cost;
            $task->save();

            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Not authorized user to update'], 500);
        }
    }

    public function saveMilestone(Request $request): JsonResponse
    {
        $task = Learning::find($request->task_id);
        if (! $task->is_milestone) {
            return response()->json([
                'message' => 'Milestone not found',
            ], 500);
        }
        $total = $request->total;
        if ($task->milestone_completed) {
            if ($total <= $task->milestone_completed) {
                return response()->json([
                    'message' => 'Milestone no can\'t be reduced',
                ], 500);
            }
        }

        if ($total > $task->no_of_milestone) {
            return response()->json([
                'message' => 'Estimated milestone exceeded',
            ], 500);
        }
        if (! $task->cost || $task->cost == '') {
            return response()->json([
                'message' => 'Please provide cost first',
            ], 500);
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

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateApproximate(Request $request): JsonResponse
    {
        $task = Learning::find($request->task_id);

        if (Auth::user()->id == $task->assign_to || Auth::user()->isAdmin()) {
            if ($task && $request->approximate) {
                DeveloperTaskHistory::create([
                    'developer_task_id' => $task->id,
                    'model' => Task::class,
                    'attribute' => 'estimation_minute',
                    'old_value' => $task->approximate,
                    'new_value' => $request->approximate,
                    'user_id' => auth()->id(),
                ]);
            }

            $task->approximate = $request->approximate;
            $task->save();

            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Unauthorized access'], 500);
        }
    }

    public function updatePriorityNo(Request $request): JsonResponse
    {
        $task = Learning::find($request->task_id);

        if (Auth::user()->id == $task->assign_to || Auth::user()->isAdmin()) {
            $task->priority_no = $request->priority;
            $task->save();

            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Unauthorized access'], 500);
        }
    }

    public function learningListByUserId(Request $request): JsonResponse
    {
        $user_id = $request->get('user_id', 0);
        $selected_issue = $request->get('selected_issue', []);

        $issues = Learning::select('learnings.id', 'learnings.task_subject', 'learnings.task_details', 'learnings.assign_from')
            ->leftJoin('erp_priorities', function ($query) {
                $query->on('erp_priorities.model_id', '=', 'learnings.id');
                $query->where('erp_priorities.model_type', '=', Learning::class);
            })->whereNull('is_verified');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->where(function ($q) use ($selected_issue, $user_id) {
                $user_id = is_null($user_id) ? 0 : $user_id;
                $q->whereIn('learnings.id', $selected_issue)->orWhere('erp_priorities.user_id', $user_id);
            });
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->groupBy('learnings.id')->orderBy('erp_priorities.id')->get();

        foreach ($issues as &$value) {
            $value->created_by = User::where('id', $value->assign_from)->value('name');
        }
        unset($value);

        return response()->json($issues);
    }

    public function setTaskPriority(Request $request): JsonResponse
    {
        $priority = $request->get('priority', null);
        $user_id = $request->get('user_id', 0);

        //delete old priority
        ErpPriority::where('user_id', $user_id)->where('model_type', '=', Learning::class)->delete();

        if (! empty($priority)) {
            foreach ((array) $priority as $model_id) {
                ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => Learning::class,
                    'user_id' => $user_id,
                ]);
            }

            $developerTask = Learning::select('learnings.id', 'learnings.task_subject', 'learnings.task_details', 'learnings.assign_from')
                ->join('erp_priorities', function ($query) use ($user_id) {
                    $user_id = is_null($user_id) ? 0 : $user_id;
                    $query->on('erp_priorities.model_id', '=', 'learnings.id');
                    $query->where('erp_priorities.model_type', '=', Learning::class);
                    $query->where('user_id', $user_id);
                })
                ->whereNull('is_verified')
                ->orderBy('erp_priorities.id')
                ->get();

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
                app(Controllers\WhatsAppController::class)->sendMessage($requestData, 'priority');
            }
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function store(StoreLearningModuleRequest $request)
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

        if ($request->task_type == 'note-task') {
            Learning::find($request->task_id);
        } else {
            if ($request->assign_to) {
                $data['assign_to'] = $request->assign_to[0];
            } else {
                $data['assign_to'] = $request->assign_to_contacts[0];
            }
        }

        $task = Learning::create($data);

        if ($request->is_statutory == 3) {
            foreach ($request->note as $note) {
                if ($note != null) {
                    Remark::create([
                        'taskid' => $task->id,
                        'remark' => $note,
                        'module_type' => 'task-note',
                    ]);
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
        ChatMessagesQuickData::updateOrCreate([
            'model' => Learning::class,
            'model_id' => $params['task_id'],
        ], [
            'last_communicated_message' => @$params['message'],
            'last_communicated_message_at' => $chat_message->created_at,
            'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
        ]);

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
            $taskSummery,
            $hubstaffUserId,
            $hubstaff_project_id
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
                $priority = ErpPriority::where('model_type', '=', Learning::class)->pluck('model_id')->toArray();

                if ($task->is_statutory == 1) {
                    $mode = 'learning-module.partials.statutory-row';
                } elseif ($task->is_statutory == 3) {
                    $mode = 'learning-module.partials.discussion-pending-raw';
                } else {
                    $mode = 'learning-module.partials.pending-row';
                }

                $view = (string) view($mode, compact('task', 'priority', 'users', 'task_statuses'));

                return response()->json(['code' => 200, 'statutory' => $task->is_statutory, 'raw' => $view]);
            }

            return response('success');
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }

    private function createHubstaffTask(string $taskSummary, ?int $hubstaffUserId, int $projectId, bool $shouldRetry = true)
    {
        $tokens = $this->getTokens();

        $url = 'https://api.hubstaff.com/v2/projects/'.$projectId.'/learnings';

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
                } else {
                    return response()->json('Opps! Something went wrong, Please try again.');
                }
            }
        }

        return false;
    }

    public function flag(Request $request): JsonResponse
    {
        $task = Learning::find($request->task_id);

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
        $task = Learning::find($id);
        $task->time_slot = $request->time_slot;
        $task->planned_at = $request->planned_at;
        $task->general_category_id = $request->get('general_category_id', null);
        $task->save();

        return response()->json([
            'task' => $task,
        ]);
    }

    public function loadView(Request $request): JsonResponse
    {
        $tasks = Learning::whereIn('id', $request->selected_tasks)->get();
        $users = Helpers::getUserArray(User::all());
        $view = view('learning-module.partials.learning-view', [
            'tasks_view' => $tasks,
            'users' => $users,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
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

    public function messageReminder(MessageReminderLearningModuleRequest $request): RedirectResponse
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
        $task = Learning::find($id);

        $task->is_statutory = 3;
        $task->save();

        return response('success');
    }

    public function updateSubject(Request $request, $id): Response
    {
        $task = Learning::find($id);
        $task->task_subject = $request->subject;
        $task->save();

        return response('success');
    }

    public function addNote(Request $request, $id): Response
    {
        Remark::create([
            'taskid' => $id,
            'remark' => $request->note,
            'module_type' => 'task-note',
        ]);

        return response('success');
    }

    public function addSubnote(Request $request, $id): Response
    {
        $remark = Remark::create([
            'taskid' => $id,
            'remark' => $request->note,
            'module_type' => 'task-note-subnote',
        ]);

        $id = $remark->id;

        return response(['success' => $id]);
    }

    public function updateCategory(Request $request, $id): Response
    {
        $task = Learning::find($id);
        $task->category = $request->category;
        $task->save();

        return response('success');
    }

    public function show($id)
    {
        $task = Learning::find($id);
        $chatMessages = ChatMessage::where('task_id', $id)->get();
        if ((! $task->users->contains(Auth::id()) && $task->is_private == 1) || ($task->assign_from != Auth::id() && $task->contacts()->count() > 0) || (! $task->users->contains(Auth::id()) && $task->assign_from != Auth::id() && Auth::id() != 6)) {
            return redirect()->back()->withErrors('This Learning is private!');
        }

        $users = User::all();
        $users_array = Helpers::getUserArray(User::all());
        $categories = LearningModule::attr(['title' => 'category', 'class' => 'form-control input-sm', 'placeholder' => 'Select a Category', 'id' => 'task_category'])
            ->selected($task->category)
            ->renderAsDropdown();

        if (request()->has('keyword')) {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->where('remark', 'like', '%'.request()->keyword.'%')->paginate(20);
        } else {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->paginate(20);
        }

        $hiddenRemarks = $task->notes()->where('is_hide', 1)->get();

        return view('learning-module.learning-show', [
            'task' => $task,
            'users' => $users,
            'users_array' => $users_array,
            'categories' => $categories,
            'taskNotes' => $taskNotes,
            'hiddenRemarks' => $hiddenRemarks,
            'chatMessages' => $chatMessages,
        ]);
    }

    public function update(UpdateLearningModuleRequest $request, $id): RedirectResponse
    {

        $task = Learning::find($id);
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
        $task = Learning::find($id);

        if ($task->is_private == 1) {
            $task->is_private = 0;
        } else {
            $task->is_private = 1;
        }

        $task->save();

        return response()->json([
            'task' => $task,
        ]);
    }

    public function isWatched(Request $request, $id): JsonResponse
    {
        $task = Learning::find($id);

        if ($task->is_watched == 1) {
            $task->is_watched = 0;
        } else {
            $task->is_watched = 1;
        }

        $task->save();

        return response()->json([
            'task' => $task,
        ]);
    }

    public function complete(Request $request, $taskid)
    {
        $task = Learning::find($taskid);
        if ($request->type == 'complete') {
            if (is_null($task->is_completed)) {
                $task->is_completed = date('Y-m-d H:i:s');
            } elseif (is_null($task->is_verified)) {
                if ($task->assignedTo) {
                    if ($task->assignedTo->fixed_price_user_or_job == 1) {
                        // Fixed price task.
                        if ($task->cost == null) {
                            if ($request->ajax()) {
                                return response()->json([
                                    'message' => 'Please provide cost for fixed price task.',
                                ], 500);
                            }

                            return redirect()->back()
                                ->with('error', 'Please provide cost for fixed price task.');
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
            return response()->json([
                'task' => $task,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task marked as completed.');
    }

    public function start(Request $request, $taskid)
    {
        $task = Learning::find($taskid);

        $task->actual_start_date = date('Y-m-d H:i:s');
        $task->save();

        if ($request->ajax()) {
            return response()->json([
                'task' => $task,
            ]);
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

        return redirect()->back()
            ->with('success', 'Statutory Task marked as completed.');
    }

    public function addRemark(Request $request): JsonResponse
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        if ($request->module_type == 'document') {
            DocumentRemark::create([
                'document_id' => $id,
                'remark' => $remark,
                'module_type' => $request->module_type,
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name,
            ]);
        } else {
            Remark::create([
                'taskid' => $id,
                'remark' => $remark,
                'module_type' => $request->module_type,
                'user_name' => $request->user_name ? $request->user_name : Auth::user()->name,
            ]);
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function list(Request $request): View
    {
        $pending_tasks = Learning::where('is_statutory', 0)->whereNull('is_completed')->where('assign_from', Auth::id());
        $completed_tasks = Learning::where('is_statutory', 0)->whereNotNull('is_completed')->where('assign_from', Auth::id());

        if (is_array($request->user) && $request->user[0] != null) {
            $pending_tasks = $pending_tasks->whereIn('assign_to', $request->user);
            $completed_tasks = $completed_tasks->whereIn('assign_to', $request->user);
        }

        if ($request->date != null) {
            $pending_tasks = $pending_tasks->where('created_at', 'LIKE', "%$request->date%");
            $completed_tasks = $completed_tasks->where('created_at', 'LIKE', "%$request->date%");
        }

        $pending_tasks = $pending_tasks->oldest()->paginate(Setting::get('pagination'));
        $completed_tasks = $completed_tasks->orderByDesc('is_completed')->paginate(Setting::get('pagination'), ['*'], 'completed-page');

        $users = Helpers::getUserArray(User::all());
        $user = $request->user ?? [];
        $date = $request->date ?? '';

        return view('learning-module.list', [
            'pending_tasks' => $pending_tasks,
            'completed_tasks' => $completed_tasks,
            'users' => $users,
            'user' => $user,
            'date' => $date,
        ]);
    }

    public function getremark(Request $request)
    {
        $id = $request->input('id');

        $task = Learning::find($id);

        echo $task->remark;
    }

    public function deleteTask(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $task = Learning::find($id);

        if ($task) {
            $task->remark = $request->input('comment');
            $task->save();

            $task->delete();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 200]);
        }
    }

    public function archiveTask($id)
    {
        $task = Learning::find($id);

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

        $tasks = (new Task)->newQuery()->withTrashed()->whereBetween('created_at', [$from, $to])->where('assign_from', '!=', 0)->where('assign_to', '!=', 0);

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

        return view('learning-module.export')->withTasks($tasks_csv);
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
        Learning::create($statutory_task);
    }

    public function getTaskRemark(Request $request): JsonResponse
    {
        $id = $request->input('id');

        if (is_null($request->module_type)) {
            $remark = Learning::getremarks($id);
        } else {
            $remark = Remark::where('module_type', $request->module_type)->where('taskid', $id)->get();
        }

        return response()->json($remark, 200);
    }

    public function addWhatsAppGroup(Request $request): JsonResponse
    {
        $whatsapp_number = '971562744570';
        $task = Learning::findorfail($request->id);

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
            $request->request->add(['group_id' => $group->id, 'user_id' => array_keys($assigned_from), 'task_id' => $task->id, 'whatsapp_number' => $whatsapp_number]);

            $this->addGroupParticipant(request());
        }

        //Add Assigned To Into Whats App Group
        if (array_keys($assigned_to)[0] != null) {
            $request->request->add(['group_id' => $group->id, 'user_id' => array_keys($assigned_to), 'task_id' => $task->id, 'whatsapp_number' => $whatsapp_number]);

            $this->addGroupParticipant(request());
        }

        return response()->json(['group_id' => $group->id]);
    }

    public function addGroupParticipant(Request $request): RedirectResponse
    {
        $whatsapp_number = '971562744570';
        //Now Add Participant In the Group

        foreach ($request->user_id as $key => $value) {
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
        $task = Learning::find($request->get('task_id', 0));

        if ($task) {
            return response()->json(['code' => 200, 'data' => $task]);
        }

        return response()->json(['code' => 500, 'message' => 'Sorry, no task found']);
    }

    public function saveNotes(Request $request): JsonResponse
    {
        $task = Learning::find($request->get('task_id', 0));

        if ($task) {
            if ($task->is_statutory == 3) {
                foreach ($request->note as $note) {
                    if ($note != null) {
                        Remark::create([
                            'taskid' => $task->id,
                            'remark' => $note,
                            'module_type' => 'task-note',
                        ]);
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => $task, 'message' => 'Note added!']);
        }

        return response()->json(['code' => 500, 'message' => 'Sorry, no task found']);
    }

    public function createLearningFromSortcut(Request $request): RedirectResponse
    {
        Learning::create([
            'learning_user' => $request->learning_user,
            'learning_vendor' => $request->learning_vendor,
            'learning_subject' => $request->learning_subject,
            'learning_module' => $request->learning_module,
            'learning_submodule' => $request->learning_submodule,
            'learning_assignment' => $request->learning_assignment,
            'learning_duedate' => $request->learning_duedate,
            'learning_status' => $request->learning_status,
        ]);

        return redirect()->route('learning.index');
    }

    public function getDiscussionSubjects(): JsonResponse
    {
        $discussion_subjects = Learning::where('is_statutory', 3)->where('is_verified', null)->pluck('task_subject', 'id')->toArray();

        return response()->json(['code' => 200, 'discussion_subjects' => $discussion_subjects]);
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
        $issue = Learning::find($request->get('issue_id'));

        $user = User::find($masterUserId);

        if (! $user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found',
            ], 500);
        }

        $issue->master_user_id = $masterUserId;

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
            $summary,
            $hubstaffUserId,
            $hubstaff_project_id
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

        return response()->json([
            'status' => 'success',
        ]);
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

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        if (! $request->learning_id || $request->learning_id == '') {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Select one learning']);
        }
        $documents = $request->input('document', []);
        $learning = Learning::find($request->learning_id);
        if (! empty($documents)) {
            $count = 0;
            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('learning-files/'.floor($learning->id / config('constants.image_per_folder')))
                    ->upload();
                $learning->attachMedia($media, config('constants.media_tags'));
                $count++;
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function previewTaskImage($id): View
    {
        $task = Learning::find($id);
        $records = [];
        if ($task) {
            $userList = User::pluck('name', 'id')->all();
            if ($task->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                    $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];
                    $explodeImage = explode('.', getMediaUrl($media));
                    $extension = end($explodeImage);

                    if (in_array($extension, $imageExtensions)) {
                        $isImage = true;
                    } else {
                        $isImage = false;
                    }
                    $records[] = [
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'task_id' => $task->id,
                        'isImage' => $isImage,
                        'userList' => $userList,
                        'created_at' => $media->created_at,
                    ];
                }
            }
        }

        $records = array_reverse($records);
        $title = 'Preview images';

        return view('learning-module.partials.preview-task-images', compact('title', 'records'));
    }

    public function approveTimeHistory(Request $request): JsonResponse
    {
        if (Auth::user()->isAdmin) {
            if (! $request->approve_time || $request->approve_time == '' || ! $request->developer_task_id || $request->developer_task_id == '') {
                return response()->json([
                    'message' => 'Select one time first',
                ], 500);
            }
            DeveloperTaskHistory::where('developer_task_id', $request->developer_task_id)->where('attribute', 'estimation_minute')->where('model', Task::class)->update(['is_approved' => 0]);
            $history = DeveloperTaskHistory::find($request->approve_time);
            $history->is_approved = 1;
            $history->save();

            return response()->json([
                'message' => 'Success',
            ], 200);
        }

        return response()->json([
            'message' => 'Only admin can approve',
        ], 500);
    }

    public function getTrackedHistory(Request $request): JsonResponse
    {
        $id = $request->id;
        $type = $request->type;
        if ($type == 'lead') {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,learnings.master_user_id,users.name FROM `hubstaff_activities`  join learnings on learnings.lead_hubstaff_task_id = hubstaff_activities.task_id join users on users.id = learnings.master_user_id where learnings.id = ' . $id . ' group by starts_at_date'));

            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at_date'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'learnings.master_user_id',
                'users.name'
            )
                ->join('learnings', 'learnings.lead_hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'learnings.master_user_id')
                ->where('learnings.id', $id)
                ->groupBy('starts_at_date')
                ->get();
        } else {
            // $task_histories = DB::select(DB::raw('SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,learnings.assign_to,users.name FROM `hubstaff_activities`  join learnings on learnings.hubstaff_task_id = hubstaff_activities.task_id join users on users.id = learnings.assign_to where learnings.id = ' . $id . ' group by starts_at_date'));

            $task_histories = HubstaffActivity::select(
                'hubstaff_activities.task_id',
                DB::raw('CAST(hubstaff_activities.starts_at AS DATE) AS starts_at_date'),
                DB::raw('SUM(hubstaff_activities.tracked) AS total_tracked'),
                'learnings.assign_to',
                'users.name'
            )
                ->join('learnings', 'learnings.hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->join('users', 'users.id', '=', 'learnings.assign_to')
                ->where('learnings.id', $id)
                ->groupBy('starts_at_date')
                ->get();

        }

        return response()->json(['histories' => $task_histories]);
    }

    public function updateTaskDueDate(Request $request): JsonResponse
    {
        if ($request->type == 'TASK') {
            $task = Learning::find($request->task_id);
            if ($request->date) {
                $task->update(['due_date' => $request->date]);
            }
        } else {
            if ($request->date) {
                DeveloperTask::where('id', $request->task_id)
                    ->update(['due_date' => $request->date]);
            }
        }

        return response()->json([
            'message' => 'Successfully updated',
        ], 200);
    }

    public function createHubstaffManualTask(Request $request): JsonResponse
    {
        $task = Learning::find($request->id);
        if ($task) {
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

            return response()->json([
                'message' => 'Successful',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Task not found',
            ], 500);
        }
    }

    public function getTaskCategories(): View
    {
        $categories = LearningModule::where('is_approved', 1)->get();

        return view('learning-module.partials.all-task-category', compact('categories'));
    }

    public function completeBulkTasks(Request $request): JsonResponse
    {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                $task = Learning::find($t);
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
                Learning::where('id', $t)->delete();
            }
        }

        return response()->json(['message' => 'Successful']);
    }

    public function getTimeHistory(Request $request)
    {
        $id = $request->id;
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')->where('developer_task_id', $id)->where('model', Task::class)->where('attribute', 'estimation_minute')->select('developer_tasks_history.*', 'users.name')->get();
        if ($task_module) {
            return $task_module;
        }

        return 'error';
    }

    public function sendDocument(Request $request): JsonResponse
    {
        if ($request->id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user = User::find($request->user_id);
            if ($user) {
                if ($media) {
                    ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        'Please find attached file',
                        getMediaUrl($media)
                    );

                    return response()->json(['message' => 'Document send succesfully'], 200);
                }
            } else {
                return response()->json(['message' => 'User  not available'], 500);
            }
        }

        return response()->json(['message' => 'Sorry required fields is missing like id , userid'], 500);
    }

    /* update task status
     */

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $task = Learning::find($request->task_id);

            $task->status = $request->status;

            $task->save();

            return response()->json([
                'status' => 'success', 'message' => 'The task status updated.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 'message' => 'The task status not updated.',
            ], 500);
        }
    }

    /* create new task status */

    public function createStatus(CreateStatusLearningModuleRequest $request): RedirectResponse
    {

        try {
            TaskStatus::create(['name' => $request->task_status]);

            return redirect()->back()->with('success', 'The task status created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function learningModuleUpdate(Request $request): JsonResponse
    {
        $id = $request->id;
        $learning = Learning::find($id);
        if ($request->user_id) {
            $learning->learning_user = $request->user_id;
            $learning->save();

            return response()->json(['message' => 'User Updated Successfully']);
        }

        if ($request->provider_id) {
            $learning->learning_vendor = $request->provider_id;
            $learning->save();

            return response()->json(['message' => 'provider Updated Successfully']);
        }

        if ($request->subject) {
            $learning->learning_subject = $request->subject;
            $learning->save();

            return response()->json(['message' => 'Subject Updated Successfully']);
        }

        if ($request->module_id) {
            $learning->learning_module = $request->module_id;
            $learning->learning_submodule = null;
            $learning->save();
            $submodule = LearningModule::where('parent_id', $learning->learning_module)->get();

            return response()->json(['message' => 'Module Updated Successfully', 'learning_id' => $learning->id, 'submodule' => $submodule]);
        }

        if ($request->submodule_id) {
            $learning->learning_submodule = $request->submodule_id;
            $learning->save();

            return response()->json(['message' => 'Submodule Updated Successfully']);
        }

        if ($request->assignment) {
            $learning->learning_assignment = $request->assignment;
            $learning->save();

            return response()->json(['message' => 'Assignment Updated Successfully']);
        }

        if ($request->status_id) {
            LearningStatusHistory::create([
                'learning_id' => $learning->id,
                'old_status' => $learning->learning_status ?? 0,
                'new_status' => $request->status_id,
                'update_by' => $request->user()->id,
            ]);

            $learning->learning_status = $request->status_id;
            $learning->save();
            $s = TaskStatus::where('name', 'completed')->first();
            if ($s) {
                if ($s->id == $request->status_id) {
                    $payment_receipt = new PaymentReceipt;
                    $payment_receipt->date = date('Y-m-d');
                    $payment_receipt->worked_minutes = 0;
                    $payment_receipt->rate_estimated = $learning->cost;
                    $payment_receipt->status = 'Pending';
                    $payment_receipt->task_id = $learning->id;
                    $payment_receipt->user_id = $learning->assign_to;
                    $payment_receipt->save();
                }
            }

            return response()->json(['message' => 'Status Updated Successfully']);
        }
    }

    public function getStatusHistory(Request $request)
    {
        $learningid = $request->learningid;

        $records = LearningStatusHistory::with('oldstatus', 'newstatus', 'user')
            ->where('learning_id', $learningid)
            ->latest()
            ->get();

        if ($records) {
            $response = [];
            foreach ($records as $row) {
                $response[] = [
                    'created_date' => $row->created_at->format('Y-m-d'),
                    'old_status' => $row->oldstatus?->name ?? '-',
                    'new_status' => $row->newstatus?->name ?? '-',
                    'update_by' => $row->user->name,
                ];
            }

            return $response;
        }

        return 'error';
    }

    public function saveDueDateUpdate(Request $request): JsonResponse
    {
        $learning = Learning::find($request->get('learningid'));
        $due_date = date('Y-m-d', strtotime($request->due_date));
        if ($learning && $request->due_date) {
            LearningDueDateHistory::create([
                'learning_id' => $learning->id,
                'old_duedate' => $learning->learning_duedate ?? 0,
                'new_duedate' => $due_date,
                'update_by' => $request->user()->id,
            ]);
        }

        $learning->learning_duedate = $due_date;
        $learning->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getDueDateHistory(Request $request)
    {
        $learningid = $request->learningid;

        $records = LearningDueDateHistory::with('user')
            ->where('learning_id', $learningid)
            ->latest()
            ->get();

        if ($records) {
            $response = [];
            foreach ($records as $row) {
                $response[] = [
                    'created_date' => $row->created_at->format('Y-m-d'),
                    'old_duedate' => $row->old_duedate ?? '-',
                    'new_duedate' => $row->new_duedate ?? '-',
                    'update_by' => $row->user->name,
                ];
            }

            return $response;
        }

        return 'error';
    }
}
