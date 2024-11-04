<?php

namespace App\Http\Controllers\Github;

use App\Github\GithubGroup;
use App\Github\GithubGroupMember;
use App\Github\GithubOrganization;
use App\Github\GithubRepository;
use App\Github\GithubRepositoryGroup;
use App\Github\GithubUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Github\AddRepositoryGroupRequest;
use App\Http\Requests\Github\AddUserGroupRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class GroupController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
            'auth' => [$userName, $token],
        ]);

        return $githubClient;
    }

    public function listGroups(): View
    {
        $groups = GithubGroup::with('users')->get();

        return view('github.groups', ['groups' => $groups]);
    }

    public function groupDetails(): View
    {
        $groupId = Route::current()->parameter('groupId');
        $group = GithubGroup::find($groupId);
        $repositories = $group->repositories;
        $users = $group->users;

        $githubOrganizations = GithubOrganization::get();

        return view(
            'github.group_details',
            [
                'group' => $group,
                'repositories' => $repositories,
                'users' => $users,
                'githubOrganizations' => $githubOrganizations,
            ]
        );
    }

    public function addRepositoryForm($groupId)
    {
        $group = GithubGroup::find($groupId);
        $existingRepositories = $group->repositories;

        $repositoryIds = $existingRepositories->map(function ($repository) {
            return $repository->id;
        });

        $githubOrganizations = GithubOrganization::with(['repos' => function ($query) use ($repositoryIds) {
            $query->whereNotIn('id', $repositoryIds);
        }])->get();

        return view(
            'github.group_add_repository',
            [
                'group' => $group,
                'githubOrganizations' => $githubOrganizations,
            ]
        );
    }

    public function addRepository(AddRepositoryGroupRequest $request): RedirectResponse
    {
        $organizationId = $request->organizationId;
        $repoId = $request->repoId;
        $groupId = $request->group_id;
        $permission = $request->permission;

        $this->callApiToAddRepository($organizationId, $repoId, $groupId, $permission);

        return redirect()->back();
    }

    private function callApiToAddRepository($organizationId, $repoId, $groupId, $permission)
    {
        $organization = GithubOrganization::find($organizationId);
        $repository = GithubRepository::find($repoId);

        // https://api.github.com/organizations/:org_id/team/:team_id/repos/:owner/:repo
        $url = 'https://api.github.com/organizations/'.$organization->name.'/team/'.$groupId.'/repos/'.$organization->name.'/'.$repository->name;
        $url = 'https://api.github.com/organizations/'.$organization->name.'/team/'.$groupId.'/repos/'.$organization->name.'/'.$repository->name;

        try {
            $githubClient = $this->connectGithubClient($organization->username, $organization->token);

            $githubClient->put($url);

            return true;
        } catch (ClientException $e) {
            //throw $e;
        }

        return false;
    }

    public function addUserForm($groupId)
    {
        $group = GithubGroup::find($groupId);
        $existingUsers = $group->users;

        $userIds = $existingUsers->map(function ($repository) {
            return $repository->id;
        });

        $users = GithubUser::whereNotIn('id', $userIds)->get(['username']);

        $userSelect = [];
        foreach ($users as $user) {
            $userSelect[$user->username] = $user->username;
        }

        $githubOrganizations = GithubOrganization::get();

        return view(
            'github.group_add_user',
            [
                'group' => $group,
                'users' => $userSelect,
                'githubOrganizations' => $githubOrganizations,
            ]
        );
    }

    public function addUser(AddUserGroupRequest $request): RedirectResponse
    {
        $organizationId = $request->organizationId;
        $groupId = $request->group_id;
        $role = $request->role;
        $username = $request->username;

        $this->addUserToGroup($organizationId, $groupId, $username, $role);

        return redirect()->back();
    }

    private function addUserToGroup($organizationId, $groupId, $username, $role)
    {
        $organization = GithubOrganization::find($organizationId);

        // https://api.github.com/organizations/:org_id/team/:team_id/memberships/:username
        // $url = "https://api.github.com/organizations/" . getenv('GITHUB_ORG_ID') . "/team/" . $groupId . "/memberships/". $username;
        $url = 'https://api.github.com/organizations/'.$organization->name.'/team/'.$groupId.'/memberships/'.$username;

        try {
            $githubClient = $this->connectGithubClient($organization->username, $organization->token);

            $githubClient->put(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        'role' => $role,
                    ]),
                ]
            );

            return true;
        } catch (ClientException $e) {
            //throw $e;
        }

        return false;
    }

    public function removeUsersFromGroup(): RedirectResponse
    {
        $groupId = Route::current()->parameter('groupId');
        $userId = Route::current()->parameter('userId');
        $organizationId = Route::current()->parameter('organizationId');

        $organization = GithubOrganization::find($organizationId);
        $githubUser = GithubUser::find($userId);

        //https://api.github.com/teams/:team_id/memberships/:username
        $url = 'https://api.github.com/teams/'.$groupId.'/memberships/'.$githubUser->username;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $githubClient->delete($url);

        GithubGroupMember::where('github_groups_id', $groupId)->where('github_users_id', $userId)->delete();

        return redirect()->back();
    }

    public function removeRepositoryFromGroup(): RedirectResponse
    {
        $groupId = Route::current()->parameter('groupId');
        $repoId = Route::current()->parameter('repoId');

        $repo = GithubRepository::find($repoId);
        $organization = $repo->organization;

        //https://api.github.com/teams/:team_id/repos/:owner/:repo
        // $url = 'https://api.github.com/teams/' . $groupId . '/repos/' . getenv('GITHUB_ORG_ID') . '/' . $repo->name;
        $url = 'https://api.github.com/teams/'.$groupId.'/repos/'.$organization->name.'/'.$repo->name;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $githubClient->delete($url);

        GithubRepositoryGroup::where('github_repositories_id', $repoId)->where('github_groups_id', $groupId)->delete();

        return redirect()->back();
    }
}
