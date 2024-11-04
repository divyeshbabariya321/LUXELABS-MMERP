<?php

namespace App\Http\Controllers;

use App\BuildProcessErrorLog;
use App\BuildProcessHistory;
use App\BuildProcessStatusHistories;
use App\Github\GithubOrganization;
use App\Github\GithubRepository;
use App\Helpers\GithubTrait;
use App\Http\Requests\ProjectTypeStoreProjectRequest;
use App\Http\Requests\ServerenvStoreProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectServerenv;
use App\Models\ProjectType;
use App\StoreWebsite;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use GithubTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $projects = Project::latest();

        if ($request->keyword) {
            $projects = $projects->where('name', 'LIKE', '%'.$request->keyword.'%');
        }

        if ($searchStoreWebsite = $request->store_websites_search) {
            $projects->whereHas('storeWebsites', function ($query) use ($searchStoreWebsite) {
                $query->whereIn('store_website_id', $searchStoreWebsite);
            });
        }

        $projects = $projects->paginate(10);

        $store_websites = StoreWebsite::get()->pluck('title', 'id');
        $serverenvs = ProjectServerenv::get()->pluck('name', 'id');
        $projecttype = ProjectType::get()->pluck('name', 'id');
        $repositories = GithubRepository::All();
        $organizations = GithubOrganization::All();

        return view('project.index', compact('projects', 'store_websites', 'repositories', 'organizations', 'serverenvs', 'projecttype'));
    }

    public function getGithubRepos(): JsonResponse
    {
        if ($build_organization = request('build_organization')) {
            $repositories = GithubRepository::where('github_organization_id', $build_organization)->orderByDesc('created_at')->get()->pluck('name', 'id')->toArray();
            if ($repositories) {
                $options = ['<option value="" >--  Select a Repository --</option>'];
                foreach ($repositories as $key => $value) {
                    $options[] = '<option value="'.$key.'" '.($key == request('selected') ? 'selected' : '').' >'.$value.'</option>';
                }
            } else {
                $options = ['<option value="" >No records found.</option>'];
            }
        } else {
            $options = ['<option value="" >Please Select a Organizations</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }

    public function getGithubBranches()
    {
        if ($build_repository = request('build_repository')) {
            Log::info('Project getGithubBranches');
            $allBranchNames = [];
            try {
                $repository = GithubRepository::find($build_repository);
                $organization = $repository->organization;

                $githubClient = $this->connectGithubClient($organization->username, $organization->token);

                $url = 'https://api.github.com/repositories/'.$build_repository.'/branches';
                $headResponse = $githubClient->head($url);
                $linkHeader = $headResponse->getHeader('Link');

                $totalPages = 1;
                if (count($linkHeader) > 0) {
                    $lastLink = null;
                    $links = explode(',', $linkHeader[0]);
                    foreach ($links as $link) {
                        if (strpos($link, 'rel="last"') !== false) {
                            $lastLink = $link;
                            break;
                        }
                    }
                    $linkWithAngularBrackets = explode(';', $lastLink)[0];
                    $linkWithAngularBrackets = str_replace('<', '', $linkWithAngularBrackets);
                    $linkWithPageNumber = str_replace('>', '', $linkWithAngularBrackets);
                    $pageNumberString = explode('?', $linkWithPageNumber)[1];
                    $totalPages = explode('=', $pageNumberString)[1];
                    $totalPages = intval($totalPages);
                }
                Log::info('totalPages : '.$totalPages);

                $page = 1;
                while ($page <= $totalPages) {
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
                if (! empty($allBranchNames)) {
                    $options = ['<option value="" >--  Select a Branch --</option>'];
                    foreach ($allBranchNames as $value) {
                        $options[] = '<option value="'.$value.'">'.$value.'</option>';
                    }
                } else {
                    $options = ['<option value="" >No records found.</option>'];
                }
            } catch (Exception $e) {
                Log::info('Error : '.$e->getMessage());
                $options = ['<option value="" >Please Select a Repository</option>'];
            }
        } else {
            $options = ['<option value="" >Please Select a Repository</option>'];
        }

        return response()->json(['data' => implode('', $options)]);
    }

    public function pullRequestsBuildProcess(Request $request): JsonResponse
    {
        $repository_id = $request->build_process_repository;
        $branch_name = $request->build_process_branch;
        $projects = $request->projects;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
        } else {
            $user_id = 6;
        }
        if ($repository_id == '') {
            BuildProcessErrorLog::log([
                'project_id' => '',
                'error_message' => 'Repository data can not be empty!',
                'error_code' => '500',
                'github_organization_id' => '',
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => '',
                'user_id' => $user_id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Repository data can not be empty!']);
        }
        $repositoryData = GithubRepository::find($repository_id);
        if (! $repositoryData) {
            BuildProcessErrorLog::log([
                'project_id' => '',
                'error_message' => 'Repository data not found!',
                'error_code' => '500',
                'github_organization_id' => '',
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => '',
                'user_id' => $user_id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Repository data not found!']);
        }
        if ($branch_name == '') {
            BuildProcessErrorLog::log([
                'project_id' => '',
                'error_message' => 'Branch data can not be empty!',
                'error_code' => '500',
                'github_organization_id' => '',
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name,
                'user_id' => $user_id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Branch data can not be empty!']);
        }
        $initiate_from = 'Pull Requests Page';
        if ($request->has('project_type') && $request->project_type != '') {
            $initiate_from = 'Call Back URL';
            $project_type = $request->project_type;
            $projects = Project::where('project_type', $project_type)->get()->pluck('id')->toArray();
        }
        $build_pr = '';
        if ($request->has('build_pr') && $request->build_pr != '') {
            $build_pr = $request->build_pr;
        }
        if (empty($projects)) {
            BuildProcessErrorLog::log([
                'project_id' => '',
                'error_message' => 'Please select projects for build process!',
                'error_code' => '500',
                'github_organization_id' => '',
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name,
                'user_id' => $user_id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Please select projects for build process!']);
        }

        $repository = $repositoryData->name;
        $organization = $repositoryData->github_organization_id;
        foreach ($projects as $proj) {
            $project = Project::find($proj);
            if ($project) {
                $job_name = $project->job_name;
                $serverenv = $project->serverenv;
                if ($job_name == '' || $serverenv == '') {
                    $record = [
                        'store_website_id' => $proj,
                        'created_by' => $user_id,
                        'text' => 'Job name and serverenv can not be empty!',
                        'build_name' => '',
                        'build_number' => '',
                        'status' => 'ABORTED',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'build_pr' => $build_pr,
                        'initiate_from' => $initiate_from,
                        'command' => '',
                    ];
                    BuildProcessHistory::create($record);

                    BuildProcessErrorLog::log([
                        'project_id' => $proj,
                        'error_message' => 'Job name and serverenv can not be empty!',
                        'error_code' => '500',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'user_id' => $user_id,
                    ]);

                    continue;
                }

                $jobName = $job_name;
                $verbosity = 'high';

                try {
                    $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $job = $jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity]);
                    if ($jenkins->getJob($jobName)) {
                        $job = $jenkins->getJob($jobName);
                        $lastBuild = $job->getLastBuild();
                        $latestBuildNumber = $latestBuildResult = '';
                        if ($lastBuild) {
                            $latestBuildNumber = $lastBuild->getNumber();
                            $latestBuildResult = $lastBuild->getResult();
                        }

                        $buildDetail = 'Build Name: '.$jobName.'<br> Build Repository: '.$repository.'<br> Branch Name: '.$branch_name;

                        $record = [
                            'store_website_id' => $proj,
                            'created_by' => $user_id,
                            'text' => $buildDetail,
                            'build_name' => $jobName,
                            'build_number' => $latestBuildNumber,
                            'status' => $latestBuildResult,
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'build_pr' => $build_pr,
                            'initiate_from' => $initiate_from,
                            'command' => '',
                        ];

                        BuildProcessHistory::create($record);
                    } else {
                        $record = [
                            'store_website_id' => $proj,
                            'created_by' => $user_id,
                            'text' => 'Please try again, Jenkins job not created',
                            'build_name' => '',
                            'build_number' => '',
                            'status' => 'ABORTED',
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'command' => '',
                        ];
                        BuildProcessHistory::create($record);
                        BuildProcessErrorLog::log([
                            'project_id' => $proj,
                            'error_message' => 'Jenkins job not created',
                            'error_code' => '500',
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'build_pr' => $build_pr,
                            'initiate_from' => $initiate_from,
                            'user_id' => $user_id,
                        ]);
                    }
                } catch (Exception $e) {
                    $record = [
                        'store_website_id' => $proj,
                        'created_by' => $user_id,
                        'text' => $e->getMessage(),
                        'build_name' => '',
                        'build_number' => '',
                        'status' => 'ABORTED',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'command' => '',
                    ];
                    BuildProcessHistory::create($record);
                    BuildProcessErrorLog::log([
                        'user_id' => $user_id,
                        'project_id' => $proj,
                        'error_message' => $e->getMessage(),
                        'error_code' => '500',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'build_pr' => $build_pr,
                        'initiate_from' => $initiate_from,
                    ]);
                }
            } else {
                $record = [
                    'store_website_id' => $proj,
                    'created_by' => $user_id,
                    'text' => 'Project Data not found',
                    'build_name' => '',
                    'build_number' => '',
                    'status' => 'ABORTED',
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name,
                    'build_pr' => $build_pr,
                    'initiate_from' => $initiate_from,
                    'command' => '',
                ];

                BuildProcessHistory::create($record);

                BuildProcessErrorLog::log([
                    'project_id' => $proj,
                    'error_message' => 'Project Data not found',
                    'error_code' => '500',
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name,
                    'user_id' => $user_id,
                ]);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Process builed complete successfully. Please check builed process logs for more details']);
    }

    public function buildProcess(Request $request): JsonResponse
    {
        $repository_id = $repository = $request->repository;
        $branch_name = $request->branch_name;
        $job_name = $request->job_name;
        $organization = $request->organization;
        $projectId = $request->project_id;
        $initiate_from = $request->initiate_from;

        if ($repository == '') {
            BuildProcessErrorLog::log([
                'project_id' => $projectId,
                'error_message' => 'Please select repository',
                'error_code' => '500',
                'github_organization_id' => '',
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => '',
                'user_id' => Auth::user()->id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Please select repository']);
        }
        if ($branch_name == '') {
            BuildProcessErrorLog::log([
                'project_id' => $projectId,
                'error_message' => 'Please select Branch',
                'error_code' => '500',
                'github_organization_id' => $organization,
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Please select Branch']);
        }
        if ($job_name == '') {
            BuildProcessErrorLog::log([
                'project_id' => '',
                'error_message' => 'Please Enter Job Name',
                'error_code' => '500',
                'github_organization_id' => $organization,
                'github_repository_id' => $repository_id,
                'github_branch_state_name' => $branch_name,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json(['code' => 500, 'message' => 'Please Enter Job Name']);
        }

        if (! empty($request->project_id)) {
            $project = Project::find($request->project_id);

            if ($project) {
                $repositoryData = GithubRepository::find($request->repository);

                $repository = $request->repository;
                if ($repositoryData) {
                    $repository = $repositoryData->name;
                }

                $jobName = $request->job_name;
                $branch_name = $request->branch_name;
                $serverenv = $project->serverenv;
                $verbosity = 'high';
                try {
                    $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $launchJobStatus = $jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity]);
                    if ($launchJobStatus) {
                        $job = $jenkins->getJob($jobName);

                        $buildDetail = 'Build Name: '.$jobName.'<br> Build Repository: '.$repository.'<br> Branch Name: '.$branch_name.'<br> Server Env: '.$serverenv.'<br> Verbosity : '.$verbosity;
                        $latestBuildNumber = $latestBuildResult = '';

                        $job_api_url = "{$jenkins->getUrl()}/job/{$job_name}/api/json";
                        $job_info = json_decode(file_get_contents($job_api_url), true);

                        // Check if the job has any build in the queue
                        if ($job_info && $job_info['inQueue']) {
                            $latestBuildNumber = $job_info['nextBuildNumber'];
                            $latestBuildResult = 'WAITING';
                            $command = 'jenkins job in the queue';
                        } else {
                            $lastBuild = $job->getLastBuild();
                            if ($lastBuild) {
                                $latestBuildNumber = $lastBuild->getNumber();
                                $latestBuildResult = $lastBuild->getResult();
                                $command = $lastBuild->getUrl().'/'.$lastBuild->getBuiltOn();
                            }
                        }

                        $record = [
                            'store_website_id' => $request->project_id,
                            'created_by' => auth()->user()->id,
                            'text' => $buildDetail,
                            'build_name' => $jobName,
                            'build_number' => $latestBuildNumber,
                            'status' => $latestBuildResult,
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'initiate_from' => $initiate_from,
                            'command' => $command,
                        ];

                        BuildProcessHistory::create($record);

                        return response()->json(['code' => 200, 'message' => 'Process builed complete successfully.']);
                    } else {
                        $error_message = 'Jenkins job not created for '.
                            'Build Name: '.$jobName.'<br>'.
                            'Build Repository: '.$repository.'<br>'.
                            'Branch Name: '.$branch_name;

                        BuildProcessErrorLog::log([
                            'project_id' => $request->project_id,
                            'error_message' => $error_message,
                            'error_code' => '500',
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'user_id' => Auth::user()->id,
                        ]);

                        return response()->json(['code' => 500, 'message' => 'Please try again, Jenkins job not created']);
                    }
                } catch (Exception $e) {
                    BuildProcessErrorLog::log([
                        'project_id' => $request->project_id,
                        'error_message' => $e->getMessage(),
                        'error_code' => '500',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'user_id' => Auth::user()->id,
                    ]);

                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
            }
        }

        return response()->json(['code' => 500, 'message' => 'Project Data is not available.']);
    }

    public function buildProcessStatusLogs(Request $request): JsonResponse
    {
        $histories = BuildProcessStatusHistories::where('build_process_history_id', $request->id)->orderByDesc('id')->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history',
            'status_name' => 'success',
        ], 200);
    }

    public function buildProcessErrorLogs(Request $request): View
    {
        $buildProcessErrorLogs = BuildProcessErrorLog::with('project');

        if ($request->search_branch) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->where('github_branch_state_name', 'LIKE', '%'.$request->search_branch.'%');
        }
        if ($request->error_code) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->where('error_code', 'LIKE', '%'.$request->error_code.'%');
        }
        if ($request->error_msg) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->where('error_message', 'LIKE', '%'.$request->error_msg.'%');
        }
        if ($request->project_search) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->WhereIn('project_id', $request->project_search);
        }
        if ($request->orgs) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->WhereIn('github_organization_id', $request->orgs);
        }
        if ($request->repos) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->WhereIn('github_repository_id', $request->repos);
        }
        if ($request->usersIds) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->WhereIn('user_id', $request->usersIds);
        }
        if ($request->date) {
            $buildProcessErrorLogs = $buildProcessErrorLogs->where('created_at', 'LIKE', '%'.$request->date.'%');
        }

        $buildProcessErrorLogs = $buildProcessErrorLogs->orderByDesc('id')->paginate(10);

        $projects = Project::all();
        $organizations = GithubOrganization::All();
        $repositories = GithubRepository::All();
        $users = User::All();

        return view('project.build-process-error-logs', compact('buildProcessErrorLogs', 'projects', 'organizations', 'repositories', 'users'));
    }

    // New concept in page
    public function buildProcessLogs(Request $request, $id = null): View
    {
        $responseLogs = BuildProcessHistory::with('project')->leftJoin('users as u', 'u.id', '=', 'build_process_histories.created_by')->select('build_process_histories.*', 'u.name as usersname');
        $repo_names = GithubRepository::select('name', 'id')->get();
        $organizations = GithubOrganization::select('name', 'id')->get();
        $projects = Project::select('name', 'id')->get();
        $users = User::select('name', 'id')->get();

        $store_websites = StoreWebsite::get()->pluck('title', 'id');
        $serverenvs = ProjectServerenv::get()->pluck('name', 'id');
        $projecttype = ProjectType::get()->pluck('name', 'id');

        $reqproject = $request->projects ?? [];
        $reqorganizations = $request->organizations ?? [];
        $reqrepoids = $request->repo_ids ?? [];
        $requsers = $request->users ?? [];
        $reqstatus = $request->status ?? [];

        if ($id) {
            $responseValue = $responseLogs->where('store_website_id', $id)->get();
            $reqproject = $responseValue->pluck('store_website_id')->toArray();
            $reqstatus = $responseValue->pluck('status')->toArray();
            $requsers = $responseValue->pluck('created_by')->toArray();
            $reqorganizations = $responseValue->pluck('github_organization_id')->toArray();
            $reqrepoids = $responseValue->pluck('github_repository_id')->toArray();
        }

        if ($request->has('branch') && $request->branch != '') {
            $responseLogs->where('github_branch_state_name', $request->branch);
        }

        if ($request->has('buildby') && $request->buildby != '') {
            $responseLogs->where('created_by', $request->buildby);
        }

        if ($request->projects && $request->projects != '') {
            $responseLogs = $responseLogs->WhereIn('build_process_histories.store_website_id', $request->projects);
        }

        if ($request->organizations && $request->organizations != '') {
            $responseLogs = $responseLogs->WhereIn('build_process_histories.github_organization_id', $request->organizations);
        }

        if ($request->repo_ids && $request->repo_ids != '') {
            $responseLogs = $responseLogs->WhereIn('build_process_histories.github_repository_id', $request->repo_ids);
        }

        if ($request->users && $request->users != '') {
            $responseLogs = $responseLogs->WhereIn('build_process_histories.created_by', $request->users);
        }

        if ($request->status && $request->status != '') {
            $responseLogs = $responseLogs->WhereIn('build_process_histories.status', $request->status);
        }
        if ($request->search_build_number && $request->search_build_number != '') {
            $responseLogs = $responseLogs->where('build_process_histories.build_number', 'LIKE', '%'.$request->search_build_number.'%');
        }
        if ($request->search_build_name && $request->search_build_name != '') {
            $responseLogs = $responseLogs->where('build_process_histories.build_name', 'LIKE', '%'.$request->search_build_name.'%');
        }
        if ($request->search_branch_name && $request->search_branch_name != '') {
            $responseLogs = $responseLogs->where('build_process_histories.github_branch_state_name', 'LIKE', '%'.$request->search_branch_name.'%');
        }

        $keyword = $request->keyword;
        if (! empty($keyword)) {
            $responseLogs->where(function ($q) use ($keyword) {
                $q->orWhere('build_process_histories.build_number', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('build_process_histories.build_name', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('build_process_histories.status', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('build_process_histories.build_pr', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('build_process_histories.initiate_from', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('build_process_histories.text', 'LIKE', '%'.$keyword.'%');
            });
        }

        $responseLogs = $responseLogs->orderByDesc('id')->paginate(10);

        foreach ($responseLogs as $responseLog) {
            $job_name = $responseLog->build_name;
            $build_number = $responseLog->build_number;
            $project_id = $responseLog->store_website_id;
            if ($responseLog->status != 'SUCCESS' && $responseLog->status != 'ABORTED') {
                try {
                    $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $job = $jenkins->getJob($job_name);
                    if ($job) {
                        $build = $job->getJenkinsBuild($build_number);
                        if ($build) {
                            $build_status = $build->getResult();

                            if ($responseLog->status != $build_status) {
                                $console_output_url = "{$jenkins->getUrl()}/job/{$job_name}/$build_number/consoleText";
                                $console_output = file_get_contents($console_output_url);

                                $record = [
                                    'project_id' => $project_id,
                                    'build_process_history_id' => $responseLog->id,
                                    'build_number' => $build_number,
                                    'old_status' => $responseLog->status,
                                    'status' => $build_status,
                                ];

                                BuildProcessStatusHistories::create($record);

                                $responseLog->status = $build_status;
                                $responseLog->text = $console_output;
                                $responseLog->save();
                            }
                        }
                    }
                } catch (Exception $e) {
                    return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);
                } catch (\RuntimeException $e) {
                    return response()->json(['code' => 400, 'message' => 'API error'.$e->getMessage()]);
                }
            }
        }

        return view('project.build-process-logs', compact('responseLogs', 'repo_names', 'organizations', 'projects', 'users', 'reqproject', 'reqorganizations', 'reqrepoids', 'requsers', 'reqstatus', 'store_websites', 'serverenvs', 'projecttype'));
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save Project
        $project = new Project;
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
        $project->project_type = $data['project_type'];
        $project->serverenv = $data['serverenv'];
        $project->save();

        $project->storeWebsites()->attach($data['store_website_id']);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project created successfully!',
            ]
        );
    }

    public function destroy($id): RedirectResponse
    {
        $project = Project::find($id);
        $project->storeWebsites()->detach();
        $project->delete();

        return redirect()->route('project.index')
            ->with('success', 'Project deleted successfully');
    }

    public function edit(Request $request, $id): JsonResponse
    {
        $project = Project::with('storeWebsites')->where('id', $id)->first();

        if ($project) {
            return response()->json(['code' => 200, 'data' => $project]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function update(UpdateProjectRequest $request, $id): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save Project
        $project = Project::find($data['id']);
        $project->name = $data['name'];
        $project->job_name = $data['job_name'];
        $project->project_type = $data['project_type'];
        $project->serverenv = $data['serverenv'];
        $project->save();

        $project->storeWebsites()->detach();
        $project->storeWebsites()->attach($data['store_website_id']);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project updated successfully!',
            ]
        );
    }

    public function serverenvStore(ServerenvStoreProjectRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save Project server env
        $projectServerenv = new ProjectServerenv;
        $projectServerenv->name = $data['name'];
        $projectServerenv->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project server env created successfully!',
            ]
        );
    }

    public function projectTypeStore(ProjectTypeStoreProjectRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save Project server env
        $ProjectType = new ProjectType;
        $ProjectType->name = $data['name'];
        $ProjectType->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Project type created successfully!',
            ]
        );
    }

    public function buildMultipleProcess(Request $request): JsonResponse
    {
        $user_id = Auth::user()->id;
        $selectedIdsString = $request->input('selectedIds');
        $projectIds = explode(',', $selectedIdsString);
        $responseResults = [];
        $metapackage_branch = '';

        if ($request->metapackage_branch == 'master' && $request->repository == 353671452) {
            $metapackage_branch = 'master';
        } else {
            $metapackage_branch = '';
        }

        foreach ($projectIds as $projectId) {
            $project = Project::find($projectId);
            $job_name = $project->job_name;
            $repository_id = $repository = $request->repository;
            $branch_name = $request->branch_name;
            $organization = $request->organization;
            $initiate_from = $request->initiate_from;

            if ($repository == '') {
                BuildProcessErrorLog::log([
                    'project_id' => $projectId,
                    'error_message' => 'Please select repository',
                    'error_code' => '500',
                    'github_organization_id' => '',
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => '',
                    'user_id' => $user_id,
                ]);

                return response()->json(['code' => 500, 'message' => 'Please select repository']);
            }
            if ($branch_name == '') {
                BuildProcessErrorLog::log([
                    'project_id' => $projectId,
                    'error_message' => 'Please select Branch',
                    'error_code' => '500',
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name,
                    'user_id' => $user_id,
                ]);

                return response()->json(['code' => 500, 'message' => 'Please select Branch']);
            }
            if ($job_name == '') {
                BuildProcessErrorLog::log([
                    'project_id' => '',
                    'error_message' => 'Please Enter Job Name',
                    'error_code' => '500',
                    'github_organization_id' => $organization,
                    'github_repository_id' => $repository_id,
                    'github_branch_state_name' => $branch_name,
                    'user_id' => $user_id,
                ]);

                return response()->json(['code' => 500, 'message' => 'Please Enter Job Name']);
            }

            if (! empty($projectId)) {
                $repositoryData = GithubRepository::find($request->repository);

                $repository = $request->repository;
                if ($repositoryData) {
                    $repository = $repositoryData->name;
                }

                $jobName = $project->job_name;
                $branch_name = $request->branch_name;
                $serverenv = $project->serverenv;
                $verbosity = 'high';

                try {
                    $jenkins = new \JenkinsKhan\Jenkins('https://apibuild:11286d3dbdb6345298c8b6811e016d8b1e@deploy.theluxuryunlimited.com');
                    $launchJobStatus = $jenkins->launchJob($jobName, ['branch_name' => $branch_name, 'repository' => $repository, 'serverenv' => $serverenv, 'verbosity' => $verbosity, 'metapackage_branch' => $metapackage_branch]);
                    if ($launchJobStatus) {
                        $job = $jenkins->getJob($jobName);

                        $buildDetail = 'Build Name: '.$jobName.'<br> Build Repository: '.$repository.'<br> Branch Name: '.$branch_name;
                        $latestBuildNumber = $latestBuildResult = '';

                        $job_api_url = "{$jenkins->getUrl()}/job/{$jobName}/api/json";
                        $job_info = json_decode(file_get_contents($job_api_url), true);
                        // Check if the job has any build in the queue
                        if ($job_info && $job_info['inQueue']) {
                            $latestBuildNumber = $job_info['nextBuildNumber'];
                            $latestBuildResult = 'WAITING';
                        } else {
                            $lastBuild = $job->getLastBuild();
                            if ($lastBuild) {
                                $latestBuildNumber = $lastBuild->getNumber();
                                $latestBuildResult = $lastBuild->getResult();
                            }
                        }

                        $record = [
                            'store_website_id' => $projectId,
                            'created_by' => auth()->user()->id,
                            'text' => $buildDetail,
                            'build_name' => $jobName,
                            'build_number' => $latestBuildNumber,
                            'status' => $latestBuildResult,
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'initiate_from' => $initiate_from,
                        ];

                        BuildProcessHistory::create($record);

                        $responseResults[] = [
                            'project_id' => $projectId,
                            'code' => 200,
                            'message' => 'Process built successfully.',
                        ];
                    } else {
                        BuildProcessErrorLog::log([
                            'project_id' => $projectId,
                            'error_message' => 'Jenkins job not created',
                            'error_code' => '500',
                            'github_organization_id' => $organization,
                            'github_repository_id' => $repository_id,
                            'github_branch_state_name' => $branch_name,
                            'user_id' => $user_id,
                        ]);

                        return response()->json(['code' => 500, 'message' => 'Please try again, Jenkins job not created']);
                    }
                } catch (Exception $e) {
                    BuildProcessErrorLog::log([
                        'project_id' => $projectId,
                        'error_message' => $e->getMessage(),
                        'error_code' => '500',
                        'github_organization_id' => $organization,
                        'github_repository_id' => $repository_id,
                        'github_branch_state_name' => $branch_name,
                        'user_id' => $user_id,
                    ]);

                    return response()->json(['code' => 500, 'message' => $e->getMessage()]);
                }
            }
        }

        return response()->json($responseResults);
    }
}
