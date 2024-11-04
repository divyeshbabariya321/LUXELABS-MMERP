<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Task;

class StoreSocialContentMilestone extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="store_social_content_id",type="integer")
     * @SWG\Property(property="task_id",type="integer")
     * @SWG\Property(property="ono_of_content",type="string")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = [
        'task_id', 'ono_of_content', 'store_social_content_id', 'status',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }
}
