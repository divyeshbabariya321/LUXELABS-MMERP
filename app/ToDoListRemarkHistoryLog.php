<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ToDoListRemarkHistoryLog extends Model
{
    protected $table = 'todolist_remark_history_logs';

    protected $fillable = ['id', 'todo_list_id', 'remark', 'old_remark', 'created_at', 'updated_at', 'user_id'];

    public function username(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
