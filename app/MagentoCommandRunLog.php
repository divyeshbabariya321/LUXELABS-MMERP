<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagentoCommandRunLog extends Model
{
    protected $fillable = [
        'command_id',
        'user_id',
        'website_ids',
        'command_name',
        'server_ip',
        'command_type',
        'response',
        'job_id',
        'request',
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

    public function command(): BelongsTo
    {
        return $this->belongsTo(MagentoCommand::class, 'command_id', 'id');
    }

    public function getFormattedResponseAttribute()
    {
        $response = trim($this->response, '"');
        $response = str_replace('n', "\n", $response);

        return nl2br(e($response));
    }
}
