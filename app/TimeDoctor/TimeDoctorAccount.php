<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_doctor_email',
        'time_doctor_password',
        'project_id',
        'organization_id',
        'auth_token',
        'company_id',
    ];
}
