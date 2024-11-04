<?php

namespace App\Github;
use App\Github\GithubUser;
use App\Github\GithubRepository;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class GithubGroup extends Model
{
    protected $fillable = [
        'id',
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            GithubUser::class,
            'github_group_members',
            'github_groups_id',
            'github_users_id'
        );
    }

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(
            GithubRepository::class,
            'github_repository_groups',
            'github_groups_id',
            'github_repositories_id'
        )->withPivot(['rights']);
    }

    public static function getGroupDetails($groupId)
    {
        $group = GithubGroup::find($groupId);

        $repositories = GithubGroup::join('github_repository_groups', 'github_groups.id', '=', 'github_repository_groups.github_groups_id')
            ->join('github_repositories', 'github_repositories.id', '=' . 'github_repository_groups.github_repositories_id')
            ->where('github_groups.id', '=', $groupId)
            ->get();

        $users = $group->users;

        return [
            'group'        => $group,
            'repositories' => $repositories,
            'users'        => $users,
        ];
    }
}
