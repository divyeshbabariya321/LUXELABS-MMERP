<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TodoList extends Model
{

    use SoftDeletes;

    protected $fillable = ['id', 'user_id', 'title', 'status', 'todo_date', 'remark', 'created_at', 'updated_at', 'todo_category_id'];

    public function username(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(TodoCategory::class, 'id', 'todo_category_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(TodoStatus::class, 'status');
    }
}
