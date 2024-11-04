<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class TaskSubject extends Model
{

    protected $fillable = [
        'task_category_id', 'task_subcategory_id', 'name', 'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaskSubCategory::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(TaskHistories::class, 'task_subject_id', 'id');
    }
}
