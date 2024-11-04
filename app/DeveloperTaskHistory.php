<?php

namespace App;
use App\User;
use App\DeveloperTask;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeveloperTasks\DeveloperTasksHistoryApprovals;

class DeveloperTaskHistory extends Model
{
    /**
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="developer_task_id",type="integer")
     * @SWG\Property(property="attribute",type="string")
     * @SWG\Property(property="old_value",type="string")
     * @SWG\Property(property="new_value",type="string")
     * @SWG\Property(property="model",type="string")
     * @SWG\Property(property="is_approved",type="boolean")
     */
    protected $table = 'developer_tasks_history';

    protected $fillable = [
        'user_id', 'developer_task_id', 'attribute', 'old_value', 'new_value', 'model', 'is_approved', 'remark',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function historySave($taskId, $type, $old, $new, $approved)
    {
        $single = self::create([
            'developer_task_id' => $taskId,
            'model'             => DeveloperTask::class,
            'attribute'         => $type,
            'old_value'         => $old,
            'new_value'         => $new,
            'user_id'           => loginId(),
            'is_approved'       => $approved ? 1 : 0,
        ]);
        if ($approved) {
            DeveloperTasksHistoryApprovals::create([
                'parent_id'   => $single->id,
                'approved_by' => loginId(),
            ]);
        }
    }

    public static function approved($id, $type)
    {
        $single = self::find($id);
        self::where('model', DeveloperTask::class)->where('attribute', $type)->where('developer_task_id', $single->developer_task_id)->update(['is_approved' => 0]);
        self::where('id', $single->id)->update(['is_approved' => 1]);

        DeveloperTask::where('id', $single->developer_task_id)->update([$type => $single->new_value]);
        DeveloperTasksHistoryApprovals::create([
            'parent_id'   => $single->id,
            'approved_by' => loginId(),
        ]);
    }
}
