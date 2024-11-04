<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TaskRemark extends Model
{
    protected $fillable = ['task_id', 'task_type', 'updated_by', 'remark', 'create_at', 'updated_at'];

    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
