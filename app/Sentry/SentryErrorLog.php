<?php

namespace App\Sentry;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Sentry\SentryAccount;

class SentryErrorLog extends Model
{
    protected $fillable = [
        'error_id',
        'error_title',
        'issue_type',
        'issue_category',
        'is_unhandled',
        'first_seen',
        'last_seen',
        'project_id',
        'total_events',
        'total_user',
        'device_name',
        'os',
        'os_name',
        'release_version',
        'status_id',
    ];

    public function sentry_project(): BelongsTo
    {
        return $this->belongsTo(SentryAccount::class, 'project_id');
    }
}
