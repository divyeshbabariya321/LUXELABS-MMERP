<?php

namespace App\Github;
use App\Github\GithubRepository;
use App\Github\GithubOrganization;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GithubPrActivity extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'pull_number',
        'activity_id',
        'user',
        'event',
        'label_name',
        'label_color',
        'comment_text',
        'activity_created_at',
        'event_header',
        'body',
        'description',
        'created_at',
        'updated_at',
    ];

    public function githubOrganization(): BelongsTo
    {
        return $this->belongsTo(GithubOrganization::class);
    }

    public function githubRepository(): BelongsTo
    {
        return $this->belongsTo(GithubRepository::class);
    }
}
