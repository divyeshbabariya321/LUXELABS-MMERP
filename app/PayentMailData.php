<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayentMailData extends Model
{
    protected $table = 'payment_mail_records';

    protected $fillable = [
        'user_id', 'file_path', 'payment_date', 'start_date', 'end_date', 'total_amount', 'total_amount_paid', 'total_balance', 'command_execution',
    ];
}
