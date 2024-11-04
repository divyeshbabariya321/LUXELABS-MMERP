<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorActivityNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'time_doctor_user_id',
        'total_track',
        'start_date',
        'end_date',
        'min_percentage',
        'actual_percentage',
        'reason',
        'status',
        'client_remarks',

    ];
}
