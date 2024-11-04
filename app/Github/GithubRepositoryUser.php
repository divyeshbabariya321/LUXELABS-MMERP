<?php

namespace App\Github;
use App\Github\GithubUser;
use App\Github\GithubRepository;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class GithubRepositoryUser extends Model
{
    protected $fillable = [
        'id',
        'github_organization_id',
        'github_repositories_id',
        'github_users_id',
        'rights',
    ];

    public function githubUser(): HasOne
    {
        return $this->hasOne(GithubUser::class, 'id', 'github_users_id');
    }

    public function githubRepository(): HasOne
    {
        return $this->hasOne(GithubRepository::class, 'id', 'github_repositories_id');
    }
}
