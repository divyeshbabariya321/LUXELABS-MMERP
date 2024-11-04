<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeTransferLog extends Model
{
    protected $table = 'wetransfers_logs';

    protected $fillable = [
        'link',
        'log_description',
    ];
}
