<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZabbixHistory extends Model
{
    protected $table = 'zabbix_history';

    protected $fillable = ['host_id', 'free_inode_in', 'space_utilization', 'total_space', 'used_space', 'available_memory', 'available_memory_in', 'cpu_idle_time', 'cpu_utilization', 'interrupts_per_second'];
}
