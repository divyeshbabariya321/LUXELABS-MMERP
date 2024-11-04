<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Task;
use App\ZabbixStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZabbixWebhookData extends Model
{
    use HasFactory;

    protected $table = 'zabbix_webhook_data';

    protected $appends = ['short_message', 'short_operational_data'];

    public $fillable = [
        'subject',
        'message',
        'event_start',
        'event_name',
        'host',
        'severity',
        'operational_data',
        'event_id',
        'zabbix_status_id',
        'remarks',
        'zabbix_task_id',
    ];

    public function getShortMessageAttribute()
    {
        return strlen($this->message) > 15 ? substr($this->message, 0, 15) . '...' : $this->message;
    }

    public function getShortOperationalDataAttribute()
    {
        return strlen($this->operational_data) > 15 ? substr($this->operational_data, 0, 15) . '...' : $this->operational_data;
    }

    // This is belongsTo tasks table not a zabbix_tasks table
    public function zabbixTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'zabbix_task_id');
    }

    public function zabbixStatusColour(): BelongsTo
    {
        return $this->belongsTo(ZabbixStatus::class, 'zabbix_status_id');
    }
}
