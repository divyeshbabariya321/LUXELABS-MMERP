<?php

namespace App;
use App\taskStatus;
use App\User;
use App\Remark;
use App\Hubstaff\HubstaffActivity;
use App\Customer;
use App\Contact;
use App\ChatMessage;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Mediable;

class Learning extends Model
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
        'learning_user',
        'learning_vendor',
        'learning_subject',
        'learning_module',
        'learning_submodule',
        'learning_assignment',
        'learning_duedate',
        'learning_status',
        'currency',
    ];

    const TASK_TYPES = [
        'Other Task',
        'Statutory Task',
        'Calendar Task',
        'Discussion Task',
        'Developer Task',
        'Developer Issue',
    ];

    public static function hasremark($id)
    {
        $task = Task::find($id);

        return ! empty($task->remark);
    }

    // getting remarks
    public static function getremarks($taskid)
    {
        $results = DB::select('select * from remarks where taskid = :taskid order by created_at DESC', ['taskid' => $taskid]);

        return json_decode(json_encode($results), true);
    }

    public function learningUser(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'learning_user');
    }

    public function learningVendor(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'learning_vendor');
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
            return $this->hasMany(ChatMessage::class, 'learning_id')->whereIn('status', ['7', '8', '9', '10'])->latest(); //Purpose - Replace from task_id to learning_id - DEVTASK-4020
        }

        return $this->hasMany(ChatMessage::class, 'learning_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest(); //Purpose - Replace from task_id to learning_id - DEVTASK-4020
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
}
