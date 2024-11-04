<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoCronRunLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'command_id',
        'user_id',
        'website_ids',
        'command_name',
        'server_ip',
        'working_directory',
        'response',
        'job_id',
        'request',
    ];
}
