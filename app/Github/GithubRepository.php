<?php

namespace App\Github;
use App\Github\GithubUser;
use App\Github\GithubOrganization;
use App\Github\GithubBranchState;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\GithubToken;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GithubRepository extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'name',
        'html',
        'webhook',
        'token_key',
        'github_type',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            GithubUser::class,
            'github_repository_users',
            'github_repositories_id',
            'github_users_id'
        )
            ->withPivot(['id', 'rights']);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(
            GithubBranchState::class,
            'repository_id',
            'id'
        )->orderByDesc('last_commit_time');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(GithubOrganization::class, 'github_organization_id', 'id');
    }

    public function github_tokens(): HasOne
    {
        return $this->hasOne(GithubToken::class,'github_repositories_id','id');
    }
}
