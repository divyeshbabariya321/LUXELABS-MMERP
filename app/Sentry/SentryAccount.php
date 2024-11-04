<?php

namespace App\Sentry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentryAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sentry_token',
        'sentry_organization',
        'sentry_project',
    ];
}
