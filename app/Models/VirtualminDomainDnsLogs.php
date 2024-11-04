<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainDnsLogs extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'dns_type', 'created_by', 'command', 'job_id', 'status', 'response'];
}
