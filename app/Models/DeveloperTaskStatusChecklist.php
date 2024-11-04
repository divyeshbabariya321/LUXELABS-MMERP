<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperTaskStatusChecklist extends Model
{
    protected $fillable = [
        'task_status',
        'name',
    ];
}
