<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Github\GitHubBranchState;
use App\Github\GithubOrganization;
use App\Helpers\GithubTrait;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class LoadBranchState extends Command
{
    use GithubTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:load_branch_state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets state of all branch for all repositories';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $organizations = GithubOrganization::get();

            foreach ($organizations as $organization) {
                $organizationId = $organization->name;
                $userName = $organization->username;
                $token = $organization->token;

                $repositoryIds = $this->getAllRepositoriesIds($organizationId, $userName, $token);

                $repoBranches = [];
                foreach ($repositoryIds as $repoId) {
                    $this->info($repoId);
                    $branchNames = $this->getBranchNamesOfRepository($userName, $token, $repoId);
                    if (count($branchNames) > 0) {
                        $repoBranches[$repoId] = $branchNames;
                    }
                }

                $comparisons = [];
                foreach ($repoBranches as $repoId => $branches) {
                    foreach ($branches as $branch) {
                        $this->info($branch);
                        $comparison = $this->compareRepoBranches($userName, $token, $repoId, $branch);
                        $filters = [
                            'state' => 'all',
                            'head' => $organizationId.':'.$branch,
                        ];
                        $pullRequests = $this->pullRequests($userName, $token, $repoId, $filters);
                        if (! empty($pullRequests) && count($pullRequests) > 0) {
                            $pullRequest[$repoId][$branch] = $pullRequests[0];
                        }
                        $comparisons[$repoId][$branch] = $comparison;
                    }
                }

                foreach ($comparisons as $repoId => $branches) {
                    $branchNames = [];
                    foreach ($branches as $branchName => $comparison) {
                        $this->info($repoId.' : '.$branchName);
                        GithubBranchState::updateOrCreate(
                            [
                                'repository_id' => $repoId,
                                'branch_name' => $branchName,
                            ],
                            [
                                'github_organization_id' => $organization->id,
                                'repository_id' => $repoId,
                                'branch_name' => $branchName,
                                'ahead_by' => $comparison['ahead_by'],
                                'behind_by' => $comparison['behind_by'],
                                'status' => ! empty($pullRequest[$repoId]) && ! empty($pullRequest[$repoId][$branchName]) ? $pullRequest[$repoId][$branchName]['state'] : '',
                                'last_commit_author_username' => $comparison['last_commit_author_username'],
                                'last_commit_time' => $comparison['last_commit_time'],
                            ]
                        );
                        $branchNames[] = $branchName;
                    }
                    GithubBranchState::where('repository_id', $repoId)
                        ->whereNotIn('branch_name', $branchNames)
                        ->delete();
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function connectGithubClient($userName, $token)
    {
        $githubClient = new Client([
            'auth' => [$userName, $token],
        ]);

        return $githubClient;
    }

    private function getAllRepositoriesIds($organizationId, $userName, $token)
    {
        $repositories = [];

        try {
            $url = 'https://api.github.com/orgs/'.$organizationId.'/repos';

            $githubClient = $this->connectGithubClient($userName, $token);

            $response = $githubClient->get($url);

            $repositories = json_decode($response->getBody()->getContents());

            return array_map(
                function ($repository) {
                    return $repository->id;
                },
                $repositories
            );
        } catch (Exception $e) {
            //
        }

        return $repositories;
    }

    private function getBranchNamesOfRepository($userName, $token, int $repoId)
    {
        $allBranchNames = [];
        try {
            $url = 'https://api.github.com/repositories/'.$repoId.'/branches';

            $githubClient = $this->connectGithubClient($userName, $token);

            $headResponse = $githubClient->head($url);

            $linkHeader = $headResponse->getHeader('Link');

            /**
             * <https://api.github.com/repositories/231925646/branches?page=4>; rel="prev", <https://api.github.com/repositories/231925646/branches?page=4>; rel="last", <https://api.github.com/repositories/231925646/branches?page=1>; rel="first"
             */
            $totalPages = 1;
            $this->info($url);
            $this->info(json_encode($linkHeader));
            if (count($linkHeader) > 0) {
                $lastLink = null;
                $links = explode(',', $linkHeader[0]);
                foreach ($links as $link) {
                    if (strpos($link, 'rel="last"') !== false) {
                        $lastLink = $link;
                        break;
                    }
                }

                //<https://api.github.com/repositories/231925646/branches?page=4>; rel="last"
                $linkWithAngularBrackets = explode(';', $lastLink)[0];
                //<https://api.github.com/repositories/231925646/branches?page=4>
                $linkWithAngularBrackets = str_replace('<', '', $linkWithAngularBrackets);
                //https://api.github.com/repositories/231925646/branches?page=4>
                $linkWithPageNumber = str_replace('>', '', $linkWithAngularBrackets);
                //https://api.github.com/repositories/231925646/branches?page=4
                $pageNumberString = explode('?', $linkWithPageNumber)[1];
                //page=4
                $totalPages = explode('=', $pageNumberString)[1];

                $totalPages = intval($totalPages);
            }
            $this->info('totalPages: '.$totalPages);
            $page = 1;
            while ($page <= $totalPages) {
                $this->info('page: '.$page);

                $response = $githubClient->get($url.'?page='.$page);

                $branches = json_decode($response->getBody()->getContents());

                $branchNames = array_map(
                    function ($branch) {
                        return $branch->name;
                    },
                    $branches
                );

                $allBranchNames = array_merge(
                    $allBranchNames,
                    array_filter($branchNames, function ($name) {
                        return $name != 'master';
                    })
                );

                $page++;
            }
        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
        $this->info(json_encode($allBranchNames));

        return $allBranchNames;
    }
}
