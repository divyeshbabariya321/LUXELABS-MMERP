<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;

class TimeDoctorTaskEfficiency extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'admin_input',
        'user_input',
        'date',
        'time',
    ];
}
