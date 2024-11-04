<?php

namespace App\Github;
use App\Github\GithubRepository;
use App\Github\GithubOrganization;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GithubRepositoryLabel extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repository_id',
        'label_name',
        'label_color',
        'message',
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
