<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorTaskNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'notes',
        'date',
    ];
}
