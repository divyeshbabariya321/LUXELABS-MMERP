<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\RedisQueue;
class RedisQueueCommandExecutionLog extends Model
{
    protected $table = 'redis_queue_command_execution_log';

    protected $fillable = [
        'user_id', 'redis_queue_id', 'command', 'server_ip', 'response',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(RedisQueue::class, 'redis_queue_id');
    }
}
