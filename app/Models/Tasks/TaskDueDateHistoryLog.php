<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Task;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TaskDueDateHistoryLog extends Model
{
    protected $fillable = ['task_id', 'task_type', 'updated_by', 'old_due_date', 'new_due_date', 'approved'];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public static function historySave($taskId, $old, $new, $approved)
    {
        $single = self::create([
            'task_id'      => $taskId,
            'task_type'    => 'TASK',
            'updated_by'   => loginId(),
            'old_due_date' => $old,
            'new_due_date' => $new,
            'approved'     => $approved ? 1 : 0,
        ]);
        if ($approved) {
            TaskDueDateHistoryLogApprovals::create([
                'parent_id'   => $single->id,
                'approved_by' => loginId(),
            ]);
        }
    }

    public static function approved($id)
    {
        $single = self::find($id);
        self::where('task_id', $single->task_id)->update(['approved' => 0]);
        self::where('id', $single->id)->update(['approved' => 1]);
        Task::where('id', $single->task_id)->update(['due_date' => $single->new_due_date]);
        TaskDueDateHistoryLogApprovals::create([
            'parent_id'   => $single->id,
            'approved_by' => loginId(),
        ]);
    }

    public function task()
    {
        return belongsTo(Task::class);
    }
}
