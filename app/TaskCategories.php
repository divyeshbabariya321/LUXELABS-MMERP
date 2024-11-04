<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class TaskCategories extends Model
{
    protected $table = 'task_category';

    protected $fillable = [
        'name',
    ];

    public function subcategory(): HasMany
    {
        return $this->hasMany(TaskSubCategory::class, 'task_category_id', 'id');
    }
}
