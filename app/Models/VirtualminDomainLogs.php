<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainLogs extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'created_by', 'command', 'job_id', 'status', 'response'];
}
