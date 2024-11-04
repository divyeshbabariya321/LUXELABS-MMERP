<?php

namespace App\Github;
use App\Github\GithubOrganization;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GithubPrErrorLog extends Model
{
    protected $fillable = [
        'id',
        'type',
        'log',
        'github_organization_id',
        'github_repository_id',
        'pull_number',
        'created_at',
        'updated_at',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(GithubOrganization::class, 'github_organization_id', 'id');
    }

    const TYPE_PR_REVIEW_COMMENTS = 'pr-review-comments';

    const TYPE_PR_ACTIVITY_TIMELINE = 'pr-activity-timeline';

    public function getShortLogAttribute()
    {
        return strlen($this->log) > 35 ? substr($this->log, 0, 35) . '...' : $this->log;
    }
}
