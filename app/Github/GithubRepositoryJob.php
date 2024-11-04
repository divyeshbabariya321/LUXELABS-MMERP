<?php

namespace App\Github;
use App\Github\GithubRepository;
use App\Github\GithubOrganization;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GithubRepositoryJob extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'job_name',
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
