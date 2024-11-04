<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\TimeDoctor\TimeDoctorAccount;
class TimeDoctorTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'time_doctor_task_id',
        'project_id',
        'time_doctor_project_id',
        'summery',
        'description',
        'time_doctor_company_id',
        'time_doctor_account_id',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(TimeDoctorAccount::class, 'time_doctor_account_id');
    }
}
