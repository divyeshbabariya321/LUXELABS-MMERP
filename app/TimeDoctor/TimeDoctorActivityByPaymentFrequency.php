<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeDoctorActivityByPaymentFrequency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'activity_excel_file',
        'start_date',
        'end_date',
        'frequency_type',
        'payment_receipt_ids',
    ];
}
