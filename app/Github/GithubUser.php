<?php

namespace App\Github;
use App\User;
use App\Github\GithubRepositoryUser;
use App\Github\GithubRepository;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class GithubUser extends Model
{
    protected $fillable = [
        'id',
        'username',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function platformUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function repositories(): HasManyThrough
    {
        return $this->hasManyThrough(
            GithubRepository::class,
            GithubRepositoryUser::class,
            'github_users_id',
            'id',
            'id',
            'github_repositories_id'
        );
    }

    public static function getUserDetails($userId)
    {
        $userDetails = self::leftJoin('github_repository_users', 'github_users.id', '=', 'github_repository_users.github_users_id')
            ->leftJoin('github_repositories', 'github_repositories.id', '=', 'github_repository_users.github_repositories_id')
            ->where('github_users.id', '=', $userId)
            ->get();

        $user = [
            'id'       => $userDetails[0]->github_users_id,
            'username' => $userDetails[0]->username,
        ];

        $repositories = $userDetails->map(function ($repository) {
            return [
                'id'     => $repository->github_repositories_id,
                'name'   => $repository->name,
                'rights' => $repository->rights,
            ];
        });

        return [
            'user'         => $user,
            'repositories' => $repositories,
        ];
    }
}
