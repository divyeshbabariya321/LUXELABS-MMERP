<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorAccount;
class TimeDoctorProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_doctor_project_id',
        'time_doctor_account_id',
        'time_doctor_company_id',
        'time_doctor_project_name',
        'time_doctor_project_description',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(TimeDoctorMember::class, 'time_doctor_account_id');
    }

    public function account_detail(): BelongsTo
    {
        return $this->belongsTo(TimeDoctorAccount::class, 'time_doctor_account_id');
    }
}
