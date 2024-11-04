<?php

namespace App\Helpers;

use App\Github\GithubOrganization;
use App\Github\GithubRepository;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;

trait GithubTrait
{
    // Define the constant here
    private const GITHUB_API_REPOSITORIES_URL = 'https://api.github.com/repositories/';

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
            'auth' => [$userName, $token],
        ]);

        return $githubClient;
    }

    private function pullRequests($userName, $token, $repoId, $filters = [])
    {
        $addedFilters = ! empty($filters) ? Arr::query($filters) : '';
        $pullRequests = [];
        $url = self::GITHUB_API_REPOSITORIES_URL.$repoId.'/pulls?per_page=200';
        if (! empty($addedFilters)) {
            $url .= '&'.$addedFilters;
        }
        try {
            $githubClient = $this->connectGithubClient($userName, $token);
            $response = $githubClient->get($url);

            $decodedJson = json_decode($response->getBody()->getContents());
            foreach ($decodedJson as $pullRequest) {
                $pullRequests[] = [
                    'id' => $pullRequest->number,
                    'title' => $pullRequest->title,
                    'number' => $pullRequest->number,
                    'state' => $pullRequest->state,
                    'username' => $pullRequest->user->login,
                    'userId' => $pullRequest->user->id,
                    'updated_at' => $pullRequest->updated_at,
                    'source' => $pullRequest->head->ref,
                    'destination' => $pullRequest->base->ref,
                ];
            }
        } catch (Exception $e) {
            $this->info($e->getMessage());
        }

        return $pullRequests;
    }

    private function compareRepoBranches($userName, $token, int $repoId, string $branchName, string $base = 'master')
    {
        $githubClient = $this->connectGithubClient($userName, $token);
        //https://api.github.com/repositories/:repoId/compare/:diff

        try {
            $url = self::GITHUB_API_REPOSITORIES_URL.$repoId.'/compare/'.$base.'...'.$branchName;
            $response = $githubClient->get($url);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                // known error which happens in case there is more changes
                return [
                    'ahead_by' => 0,
                    'behind_by' => 0,
                    'last_commit_author_username' => null,
                    'last_commit_time' => null,
                ];
            }
        }

        $compare = json_decode($response->getBody()->getContents());

        $lastCommitAuthorUsername = null;
        $lastCommitTime = null;

        if (is_array($compare->commits) && count($compare->commits) > 0) {
            $index = count($compare->commits) - 1;

            try {
                $lastCommitAuthorUsername = $compare->commits[$index]->author->login;
            } catch (Exception $e) {
                // do nothing
                $lastCommitAuthorUsername = $compare->commits[$index]->commit->author->name;
            }
            $lastCommitTime = Carbon::parse($compare->commits[$index]->commit->author->date);
        } else {
            $lastCommitAuthorUsername = $compare->merge_base_commit->commit->author->name;
            $lastCommitTime = Carbon::parse($compare->merge_base_commit->commit->author->date);
        }

        return [
            'ahead_by' => $compare->ahead_by,
            'behind_by' => $compare->behind_by,
            'last_commit_author_username' => $lastCommitAuthorUsername,
            'last_commit_time' => $lastCommitTime,
        ];
    }

    private function inviteUser(string $email, $organizationId)
    {
        $organization = GithubOrganization::find($organizationId);

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        // /orgs/:org/invitations
        // $url = 'https://api.github.com/orgs/' . getenv('GITHUB_ORG_ID') . '/invitations';
        $url = 'https://api.github.com/orgs/'.$organization->name.'/invitations';

        try {
            $githubClient->post(
                $url,
                [
                    'json' => [
                        'email' => $email,
                    ],
                ]
            );

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getPullRequestDetail($userName, $token, string $repoId, string $number)
    {
        $url = self::GITHUB_API_REPOSITORIES_URL.$repoId.'/pulls/'.$number;

        try {
            $githubClient = $this->connectGithubClient($userName, $token);

            $response = $githubClient->get($url);

            $pullRequest = json_decode($response->getBody()->getContents());

            return [
                'id' => $pullRequest->number,
                'title' => $pullRequest->title,
                'number' => $pullRequest->number,
                'username' => $pullRequest->user->login,
                'userId' => $pullRequest->user->id,
                'updated_at' => $pullRequest->updated_at,
                'source' => $pullRequest->head->ref,
                'mergeable_state' => $pullRequest->mergeable_state,
                'destination' => $pullRequest->base->ref,
            ];
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'errors' => $e->getMessage(), 'message' => $e->getMessage()], 400);
        }
    }

    private function closePullRequest(string $repositoryId, string $pullNumber)
    {
        $repository = GithubRepository::find($repositoryId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = self::GITHUB_API_REPOSITORIES_URL.$repositoryId.'/pulls/'.$pullNumber;

        try {
            $githubClient->patch($url,
                [
                    'json' => [
                        'state' => 'closed',
                    ],
                ]);
            $data['status'] = true;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['error'] = $e->getMessage();
        }

        return $data;
    }

    private function deleteBranch(string $repositoryId, string $branchName)
    {
        $repository = GithubRepository::find($repositoryId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = self::GITHUB_API_REPOSITORIES_URL.$repositoryId.'/git/refs/heads/'.$branchName;

        try {
            $githubClient->delete($url);
            $data['status'] = true;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['error'] = $e->getMessage();
        }

        return $data;
    }

    private function getGithubActionRuns(string $repositoryId, $page = 1, $date = null, $status = null, $branch = null)
    {
        $repository = GithubRepository::find($repositoryId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = self::GITHUB_API_REPOSITORIES_URL.$repositoryId.'/actions/runs?page='.$page;
        if (! empty($date)) {
            $url .= "&created={$date}";
        }
        if (! empty($status)) {
            $url .= "&status={$status}";
        }
        if (! empty($branch)) {
            $url .= "&branch={$branch}";
        }

        try {
            $response = $githubClient->get($url);
            $githubAction = json_decode($response->getBody()->getContents());

            return $githubAction;
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'errors' => $e->getMessage(), 'message' => $e->getMessage()], 400);
        }
    }

    private function getGithubActionRunJobs(string $repositoryId, string $runId)
    {
        $repository = GithubRepository::find($repositoryId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = self::GITHUB_API_REPOSITORIES_URL.$repositoryId.'/actions/runs/'.$runId.'/jobs';

        try {
            $response = $githubClient->get($url);
            $githubAction = json_decode($response->getBody()->getContents());

            return $githubAction;
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'errors' => $e->getMessage(), 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * For Github Branches
     */
    public function getGithubBranches(string $repositoryId, array $inputs)
    {
        $repository = GithubRepository::find($repositoryId);
        $organization = $repository->organization;

        $githubClient = $this->connectGithubClient($organization->username, $organization->token);

        $url = self::GITHUB_API_REPOSITORIES_URL.$repositoryId.'/branches';

        try {
            $response = $githubClient->get($url);
            $githubAction = json_decode($response->getBody()->getContents());

            return $githubAction;
        } catch (Exception $e) {
            //
        }
    }

    private function rerunAction($repoId, $jobId)
    {
        $githubRepository = GithubRepository::with('organization')->find($repoId);

        $url = 'https://api.github.com/repos/'.$githubRepository->organization->name.'/'.$githubRepository->name.'/actions/runs/'.$jobId.'/rerun-failed-jobs';

        $githubClient = $this->connectGithubClient($githubRepository->organization->username, $githubRepository->organization->token);

        try {
            $githubClient->post($url);
            $data['status'] = true;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['error'] = $e->getMessage();
        }

        return $data;
    }
}
