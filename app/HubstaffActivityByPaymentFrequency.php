<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivityByPaymentFrequency extends Model
{
    protected $fillable = [
        'user_id',
        'activity_excel_file',
        'start_date',
        'end_date',
        'frequency_type',
        'payment_receipt_ids',
    ];
}
