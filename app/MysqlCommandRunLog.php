<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class MysqlCommandRunLog extends Model
{
    protected $fillable = [
        'user_id',
        'website_ids',
        'server_ip',
        'command',
        'response',
        'job_id',
        'status',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_ids', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
