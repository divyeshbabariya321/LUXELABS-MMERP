<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class TaskSubCategory extends Model
{

    protected $fillable = [
        'task_category_id', 'name',
    ];

    public function task_category(): BelongsTo
    {
        return $this->belongsTo(TaskCategories::class);
    }

    public function task_subject(): HasMany
    {
        return $this->hasMany(TaskSubject::class, 'task_subcategory_id', 'id');
    }
}
