<?php

namespace App;

use App\Http\Controllers\TaskModuleController;
use App\Http\Controllers\WhatsAppController;
use App\Hubstaff\HubstaffActivity;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Hubstaff\HubstaffMember;
use App\Models\Tasks\TaskDueDateHistoryLog;
use App\Models\Tasks\TaskHistoryForStartDate;
use App\Models\TaskStartEndHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;

class Task extends Model
{
    use Mediable;

    /**
     * @var string
     *
     * @SWG\Property(property="category",type="string")
     * @SWG\Property(property="task_details",type="string")
     * @SWG\Property(property="task_subject",type="string")
     * @SWG\Property(property="completion_date",type="datetime")
     * @SWG\Property(property="assign_from",type="datetime")
     * @SWG\Property(property="assign_to",type="datetime")
     * @SWG\Property(property="is_statutory",type="boolean")
     * @SWG\Property(property="sending_time",type="string")
     * @SWG\Property(property="recurring_type",type="string")
     * @SWG\Property(property="statutory_id",type="integer")
     * @SWG\Property(property="model_type",type="string")
     * @SWG\Property(property="model_id",type="integer")
     * @SWG\Property(property="general_category_id",type="integer")

     * @SWG\Property(property="cost",type="string")
     * @SWG\Property(property="is_milestone",type="boolean")
     * @SWG\Property(property="no_of_milestone",type="string")
     * @SWG\Property(property="milestone_completed",type="string")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="hubstaff_task_id",type="integer")
     * @SWG\Property(property="master_user_id",type="integer")
     * @SWG\Property(property="lead_hubstaff_task_id",type="integer")
     * @SWG\Property(property="due_date",type="datetime")
     * @SWG\Property(property="site_developement_id",type="integer")
     * @SWG\Property(property="priority_no",type="integer")
     */
    use SoftDeletes;

    protected $fillable = [
        'category',
        'task_details',
        'task_subject',
        'start_date',
        'completion_date',
        'assign_from',
        'assign_to',
        'is_statutory',
        'actual_start_date',
        'is_completed',
        'sending_time',
        'recurring_type',
        'statutory_id',
        'model_type',
        'model_id',
        'general_category_id',
        'cost',
        'is_milestone',
        'no_of_milestone',
        'milestone_completed',
        'customer_id',
        'hubstaff_task_id',
        'master_user_id',
        'lead_hubstaff_task_id',
        'due_date',
        'site_developement_id',
        'priority_no',
        'frequency',
        'message',
        'reminder_from',
        'reminder_last_reply',
        'last_send_reminder',
        'parent_task_id',
        'task_bug_ids',
        'last_date_time_reminder',
        'is_flow_task',
        'user_feedback_cat_id',
        'user_feedback_vendor_id',
        'parent_review_task_id',
        'time_doctor_task_id',
        'lead_time_doctor_task_id',
        'manually_assign',
        'slotTaskRemarks',
        'task_start',
        'm_start_date',
        'm_end_date',
    ];

    const TASK_TYPES = [
        'Developer Task',
        'Regular Task',
    ];

    const TASK_STATUS_FILTER = [
        'DONE' => 1,
        'DISCUSSING' => 2,
        'IN_PROGRESS' => 3,
        'ISSUE' => 4,
        'PLANNED' => 5,
        'DISCUSS_WITH_LEAD' => 6,
        'NOTE' => 7,
        'LEAD_RESPONSE_NEEDED' => 8,
        'ERRORS_IN_TASK' => 9,
        'IN_REVIEW' => 10,
        'PRIORITY' => 11,
        'PRIORITY_2' => 12,
        'HIGH_PRIORITY' => 13,
        'REVIEW_ESTIMATED_TIME' => 14,
        'USER_COMPLETE' => 15,
        'USER_COMPLETE_2' => 16,
        'USER_ESTIMATED' => 17,
        'DECLINE' => 18,
        'REOPEN' => 19,
        'APPROVED' => 20,
    ];

    const TASK_STATUS_DONE = 1;

    const TASK_STATUS_DISCUSSING = 2;

    const TASK_STATUS_IN_PROGRESS = 3;

    const TASK_STATUS_ISSUE = 4;

    const TASK_STATUS_PLANNED = 5;

    const TASK_STATUS_DISCUSS_WITH_LEAD = 6;

    const TASK_STATUS_NOTE = 7;

    const TASK_STATUS_LEAD_RESPONSE_NEEDED = 8;

    const TASK_STATUS_ERRORS_IN_TASK = 9;

    const TASK_STATUS_IN_REVIEW = 10;

    const TASK_STATUS_PRIORITY = 11;

    const TASK_STATUS_PRIORITY_2 = 12;

    const TASK_STATUS_HIGH_PRIORITY = 13;

    const TASK_STATUS_REVIEW_ESTIMATED_TIME = 14;

    const TASK_STATUS_USER_COMPLETE = 15;

    const TASK_STATUS_USER_COMPLETE_2 = 16;

    const TASK_STATUS_USER_ESTIMATED = 17;

    const TASK_STATUS_DECLINE = 18;

    const TASK_STATUS_REOPEN = 19;

    const TASK_STATUS_APPROVED = 20;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                // Check the assinged user in any team ?
                if ($model->assign_to > 0 && (empty($model->master_user_id) || empty($model->second_master_user_id))) {
                    $teamUser = TeamUser::where('user_id', $model->assign_to)->first();
                    if ($teamUser) {
                        $team = $teamUser->team;
                        if ($team) {
                            $model->master_user_id = $team->user_id;

                            if (strlen($team->second_lead_id) > 0 && $team->second_lead_id > 0) {
                                $model->second_master_user_id = $team->second_lead_id;
                            }
                        }
                    } else {

                        $isTeamLeader = Team::where('user_id', $model->assign_to)

                            ->orWhere('second_lead_id', $model->assign_to)->first();
                        if ($isTeamLeader) {
                            $model->master_user_id = $model->assign_to;
                        }
                    }
                }

            } catch (Exception $e) {

                //
            }
        });
    }

    public static function hasremark($id)
    {
        $task = Task::find($id)->remark;

        return ! empty($task);
    }

    // getting remarks
    public static function getremarks($taskid)
    {
        $results = DB::select('select * from remarks where taskid = :taskid order by created_at DESC', ['taskid' => $taskid]);

        return json_decode(json_encode($results), true);
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'task')->latest();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'task-note')->latest();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users', 'task_id', 'user_id')->where('type', User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to', 'id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'task_users', 'task_id', 'user_id')->where('type', Contact::class);
    }

    public function whatsappgroup(): HasOne
    {
        return $this->hasOne(WhatsAppGroup::class);
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        if ($needBroadCast) {
            return $this->hasMany(ChatMessage::class, 'task_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(ChatMessage::class, 'task_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function allMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'task_id', 'id')->orderByDesc('id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function timeSpent(): HasOne
    {
        return $this->hasOne(
            HubstaffActivity::class,
            'task_id',
            'hubstaff_task_id'
        )
            ->selectRaw('task_id, SUM(tracked) as tracked')
            ->groupBy('task_id');
    }

    public function taskStatus(): HasOne
    {
        return $this->hasOne(
            'App\taskStatus',
            'id',
            'status'
        );
    }

    public function taskStatusAlter(): HasOne
    {
        return $this->hasOne(TaskStatus::class, 'id', 'status');
    }

    public function developerTasksHistory(): HasMany
    {
        return $this->hasMany(DeveloperTaskHistory::class, 'developer_task_id')->orderByDesc('id', 'DESC');
    }

    public function taskHistoryForStartDate(): HasMany
    {
        return $this->hasMany(TaskHistoryForStartDate::class, 'task_id')->orderByDesc('id', 'DESC');
    }

    public function taskDueDateHistoryLogs(): HasMany
    {
        return $this->hasMany(TaskDueDateHistoryLog::class, 'task_id')->orderByDesc('id', 'DESC');
    }

    public function createTaskFromSortcuts($request)
    {
        $created = 0;
        $message = '';
        $assignedUserId = 0;

        if (isset($request['task_asssigned_from'])) {
            $data['assign_from'] = $request['task_asssigned_from'];
        } else {
            $data['assign_from'] = Auth::id();
        }

        $data['status'] = 3;
        $task = 0;
        $taskType = $request['task_type'];

        if (isset($request['parent_task_id'])) {
            $data['parent_task_id'] = $request['parent_task_id'];
        }

        if ($taskType != '4' || $taskType != '5' || $taskType != '6') {
            if (isset($data['is_flow_task'])) {
            } else {
                $data['is_flow_task'] = 1;
            }
            if ($request['task_asssigned_to']) {
                $data['assign_to'] = $request['task_asssigned_to'];
            } else {
                $data['assign_to'] = $request['assign_to_contacts'];
            }
            //discussion task

            $data['is_statutory'] = $request['task_type'];
            $data['task_details'] = $request['task_detail'];
            $data['task_subject'] = $request['task_subject'];
            $data['customer_id'] = $request['customer_id'];
            $data['site_developement_id'] = $request['site_id'];
            $data['cost'] = $request['cost'];
            if ($request['category_id'] != null) {
                $data['category'] = $request['category_id'];
            }
            $task = Task::create($data);
            $created = 1;
            $assignedUserId = $task->assign_to;
            $message = ($task->is_statutory != 1) ? '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details : $task->task_subject.'. '.$task->task_details;

            $params = [
                'number' => null,
                'user_id' => $data['assign_from'],
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
                'model' => Task::class,
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
        }

        if ($created) {
            // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
            $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

            $assignedUser = HubstaffMember::where('user_id', $assignedUserId)->first();

            $hubstaffUserId = null;
            $hubstaffTaskId = null;
            if ($assignedUser) {
                $hubstaffUserId = $assignedUser->hubstaff_user_id;
            }
            $taskSummery = substr($message, 0, 200);
            if ($hubstaffUserId) {
                $hubstaffTaskId = app(TaskModuleController::class)->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            }

            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();
            }
            if ($hubstaffTaskId) {
                $hubtask = new HubstaffTask;
                $hubtask->hubstaff_task_id = $hubstaffTaskId;
                $hubtask->project_id = $hubstaff_project_id;
                $hubtask->hubstaff_project_id = $hubstaff_project_id;
                $hubtask->summary = $message;
                $hubtask->save();
            }
        }

        return $task;
    }

    public function site_development(): BelongsTo
    {
        return $this->belongsTo(SiteDevelopment::class, 'site_developement_id', 'id');
    }

    public function updateStartDate($new)
    {
        $old = $this->start_date;

        if (isset($this->due_date) && $this->due_date != '0000-00-00 00:00:00' && isset($new)) {
            $newStartDate = Carbon::parse($new);
            $estimateDate = Carbon::parse($this->due_date);
            if ($newStartDate->gte($estimateDate)) {
                throw new Exception('Estimate start date time must be less then Estimate end date time.');
            }
        }

        $count = TaskHistoryForStartDate::where('task_id', $this->id)->count();
        if ($count) {
            TaskHistoryForStartDate::historySave($this->id, $old, $new, 0);
        } else {
            TaskHistoryForStartDate::historySave($this->id, $old, $new, 1);
        }
        $this->start_date = $new;
        $this->save();
    }

    public function updateDueDate($new)
    {
        $old = $this->due_date;

        if (isset($this->start_date) && $this->start_date != '0000-00-00 00:00:00' && isset($new)) {
            $startDate = Carbon::parse($this->start_date);
            $newEstimateDate = Carbon::parse($new);
            if ($newEstimateDate->lte($startDate)) {
                throw new Exception('Estimate end date time must be greater then Estimate start date time.');
            }
        }

        $count = TaskDueDateHistoryLog::where('task_id', $this->id)->count();
        if ($count) {
            TaskDueDateHistoryLog::historySave($this->id, $old, $new, 0);
        } else {
            TaskDueDateHistoryLog::historySave($this->id, $old, $new, 1);
        }
        $this->due_date = $new;
        $this->save();
    }

    public static function getMessagePrefix($obj)
    {
        return '#TASK-'.$obj->id.'-'.$obj->task_subject.' => ';
    }

    /* Common function to get tasks filtered and for Task & Activity module */
    public static function getSearchedTasks($type, $request)
    {
        $term = $request->term ?? '';
        $selected_user = $request->selected_user ?? '';
        $paginate = 50;
        $page = $request->get('page', 1);
        $offSet = ($page * $paginate) - $paginate;

        $chatSubQuery = ChatMessage::select(
            'chat_messages.id as message_id',
            'chat_messages.task_id',
            'chat_messages.message',
            'chat_messages.is_audio',
            'chat_messages.status as message_status',
            'chat_messages.sent as message_type',
            'chat_messages.created_at as message_created_at',
            'chat_messages.is_reminder as message_is_reminder',
            'chat_messages.user_id as message_user_id'
        )
            ->join('chat_messages_quick_datas', 'chat_messages_quick_datas.last_communicated_message_id', '=', 'chat_messages.id')
            ->whereNotIn('chat_messages.status', [7, 8, 9])
            ->where('chat_messages_quick_datas.model', '=', Task::class);

        $qb = self::select(
            'tasks.*',
            'task_status_data.name as task_status_name',
            'task_status_data.task_color as task_status_color',
            'assign_from_user.name as assign_from_username',
            'assign_to_user.name as assign_to_username',
            'message_id',
            'task_id',
            'message',
            'message_status',
            'message_type',
            'message_created_at',
            'message_is_reminder',
            'message_user_id'
        )
            ->leftJoinSub($chatSubQuery, 'chat_messages', function ($join) {
                $join->on('chat_messages.task_id', '=', 'tasks.id');
            })
            ->leftJoin('task_statuses as task_status_data', 'tasks.status', '=', 'task_status_data.id')
            ->leftJoin('users as assign_from_user', 'tasks.assign_from', '=', 'assign_from_user.id')
            ->leftJoin('users as assign_to_user', 'tasks.assign_to', '=', 'assign_to_user.id')
            ->leftJoin('task_categories', 'tasks.category', '=', 'task_categories.id')
            ->whereNull('tasks.deleted_at')
            ->whereNotNull('tasks.id');

        if ($type != 'statutory_not_completed_list') {
            $qb->where('is_statutory', '=', $request->get('is_statutory_query','!= 3'));
        }

        if ($term != '') {
            $qb->where(function ($query) use ($term) {
                $query->where('tasks.id', 'LIKE', '%'.$term.'%')
                    ->orWhere('task_categories.title', 'LIKE', '%'.$term.'%')
                    ->orWhere('tasks.task_subject', 'LIKE', '%'.$term.'%')
                    ->orWhere('tasks.task_details', 'LIKE', '%'.$term.'%')
                    ->orWhere('assign_from_user.name', 'LIKE', '%'.$term.'%')
                    ->orWhere('users.name', 'LIKE', '%'.$term.'%')
                    ->orWhereIn('tasks.id', function ($subquery) use ($term) {
                        $subquery->select('task_id')
                            ->from('task_users')
                            ->whereIn('task_users.user_id', function ($sq2) use ($term) {
                                $sq2->select('id')
                                    ->from('users')
                                    ->where('name', 'LIKE', '%'.$term.'%');
                            });
                    });
            });
        }

        // Filter by assigned user
        if ($selected_user != '') {
            $qb->where('assign_to', $selected_user);
        }

        // Sorting logic
        if ($request->sort_by == 1) {
            $qb->orderByDesc('tasks.created_at');
        } elseif ($request->sort_by == 2) {
            $qb->orderBy('tasks.created_at');
        }

        // Optimize conditions for pending tasks
        if ($type == 'pending') {
            $qb->where('is_statutory', '!=', 1);
            $qb->whereIn('tasks.status', TaskStatus::pluck('id')->toArray());

            if ($term != '') {
                $qb->where('tasks.id', '=', $term);
            }

            $qb->orderByDesc('tasks.is_flagged')
                ->orderByDesc('chat_messages.message_created_at')
                ->offset($offSet)
                ->limit($paginate);

            return $qb->get();
        } elseif (in_array($type, ['pending_list', 'completed_list', 'statutory_not_completed_list'])) {
            $qb->selectRaw('customers.name AS customer_name')
                ->leftJoin('customers', 'tasks.customer_id', '=', 'customers.id');

            if ($request->filter_status) {
                $qb->whereIn('tasks.status', $request->filter_status);
            } else {
                $qb->whereNotIn('tasks.status', [1]);
            }

            // Handle multiple users filter
            $userIds = array_filter(explode(',', $request->input('selected_user', Auth::id())));

            $qb->where(function ($query) use ($userIds, $request) {
                $query->whereIn('tasks.assign_to', $userIds)
                    ->orWhere('tasks.master_user_id', $request->search_master_user_id)
                    ->orWhere('tasks.second_master_user_id', $request->search_second_master_user_id)
                    ->orWhereIn('tasks.id', function ($subquery) use ($userIds) {
                        $subquery->select('task_id')
                            ->from('task_users')
                            ->whereIn('task_users.user_id', function ($sq2) use ($userIds) {
                                $sq2->select('id')
                                    ->from('users')
                                    ->whereIn('user_id', $userIds)
                                    ->where('type', 'LIKE', '%User%');
                            });
                    });
            });

            if ($request->ajax() && ! $request->flag_filter) {
                $qb->where('tasks.is_flagged', 0);
            }

            // Additional filters
            if ($request->category != '' && $request->category != 1) {
                $qb->where('tasks.category', $request->category);
            }

            if ($type != 'statutory_not_completed_list') {
                $qb->where('is_statutory', '!=', 1);
            } elseif ($type == 'statutory_not_completed_list') {
                $qb->where('is_statutory', '=', 1);
                $qb->whereNull('is_verified');
            }

            if ($type === 'completed_list' || $type == 'statutory_not_completed_list') {
                if ($type == 'completed_list') {
                    $qb->whereNotNull('is_verified');
                }

                $qb->selectRaw('message_created_at as last_communicated_at');
                $qb->orderByDesc('last_communicated_at');
            } elseif ($type === 'pending_list') {
                if ($request->filter_by == 1) {
                    $qb->whereNull('is_completed');
                }elseif ($request->filter_by == 2) {
                    $qb->whereNotNull('is_completed');
                } elseif ($request->filter_by != 1) {
                    $qb->whereNull('is_verified');
                }

                $qb->orderByDesc('tasks.is_flagged');
                $qb->orderByDesc('message_created_at');
            }
            $qb->offset($offSet)->limit($paginate);

            return $qb->get();
        }
    }

    public static function getDeveloperTasksHistory($id)
    {
        return self::with([
            'developerTasksHistory',
            'taskHistoryForStartDate',
            'taskDueDateHistoryLogs',
        ])->where('tasks.id', $id)->first();
    }

    public function taskStartEndHistories(): HasMany
    {
        return $this->hasMany(TaskStartEndHistory::class, 'task_id', 'id');
    }

    public function developerTaskHistories(): HasMany
    {
        return $this->hasMany(DeveloperTaskHistory::class, 'developer_task_id', 'id');
    }
}
