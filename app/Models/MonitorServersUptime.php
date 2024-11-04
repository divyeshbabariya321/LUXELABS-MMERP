<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitorServersUptime extends Model
{
    use HasFactory;

    protected $table = 'monitor_servers_uptime';

    public $fillable = [
        'server_id',
        'date',
        'status',
        'latency',
    ];

    /**
     * Get the monitorServer that owns the monitorServersUptime.
     */
    public function monitorServer(): BelongsTo
    {
        return $this->belongsTo(MonitorServer::class);
    }
}
