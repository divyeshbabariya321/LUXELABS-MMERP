<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Task;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TaskHistoryForStartDate extends Model
{
    public $table = 'task_history_for_start_date';

    public $fillable = [
        'task_id',
        'task_type',
        'updated_by',
        'old_value',
        'new_value',
        'approved',
    ];

    public function updatedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public static function historySave($taskId, $old, $new, $approved)
    {
        $single = self::create([
            'task_id'    => $taskId,
            'task_type'  => 'TASK',
            'updated_by' => loginId(),
            'old_value'  => $old,
            'new_value'  => $new,
            'approved'   => $approved ? 1 : 0,
        ]);
        if ($approved) {
            TaskHistoryForStartDateApprovals::create([
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
        Task::where('id', $single->task_id)->update(['start_date' => $single->new_value]);

        TaskHistoryForStartDateApprovals::create([
            'parent_id'   => $single->id,
            'approved_by' => loginId(),
        ]);
    }
}
