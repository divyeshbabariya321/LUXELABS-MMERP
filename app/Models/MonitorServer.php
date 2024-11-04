<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitorServer extends Model
{
    use HasFactory;

    protected $primaryKey = 'server_id';

    public $fillable = [
        'ip',
        'port',
        'request_method',
        'label',
        'type',
        'pattern',
        'pattern_online',
        'post_field',
        'redirect_check',
        'allow_http_status',
        'header_name',
        'header_value',
        'status',
        'error',
        'rtime',
        'last_online',
        'last_offline',
        'last_offline_duration',
        'last_check',
        'active',
        'email',
        'sms',
        'discord',
        'pushover',
        'webhook',
        'telegram',
        'jabber',
        'warning_threshold',
        'warning_threshold_counter',
        'ssl_cert_expiry_days',
        'ssl_cert_expired_time',
        'timeout',
        'website_username',
        'website_password',
        'last_error',
        'last_error_output',
        'last_output',
    ];

    /**
     * Get the monitorServersUptimes for the monitor server.
     */
    public function monitorServersUptimes(): HasMany
    {
        return $this->hasMany(MonitorServersUptime::class);
    }

    /**
     * The monitorUsers that belong to the monitorServer.
     */
    public function monitorUsers(): BelongsToMany
    {
        return $this->belongsToMany(MonitorUser::class, 'monitor_users_servers', 'server_id', 'user_id');
    }
}
