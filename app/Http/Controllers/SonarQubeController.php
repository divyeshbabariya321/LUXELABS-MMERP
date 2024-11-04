<?php

namespace App\Http\Controllers;
use App\Github\GithubRepository;

use App\DeveloperTask;
use App\Models\SonarQube;
use App\Task;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SonarQubeController extends Controller
{
    public function createProject(Request $request): JsonResponse
    {
        $url = config('settings.sonarqube_url').'api/projects/create';

        try {
            $response = Http::withBasicAuth(config('settings.sonarqube_username'), config('settings.sonarqube_password'))
                ->post($url, [
                    'project' => $request->project,
                    'name' => $request->name,
                ]);

            if ($response->status() == 200) {
                return response()->json(['code' => 200, 'data' => $response, 'message' => 'Project created successfully!']);
            } else {
                return response()->json(['code' => 400, 'data' => $response, 'message' => 'Project Name Already exixts!']);
            }
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            $response = $exception->response;
            if ($response->status() === 400 && strpos($response->body(), 'Project already exists')) {
                return response()->json(['code' => 400, 'message' => 'Project with the same name already exists.']);
            }

            return response()->json(['code' => 500, 'message' => 'An error occurred while creating the project.']);
        }
    }

    public function searchProject(Request $request): JsonResponse
    {
        $url = config('settings.sonarqube_url').'api/projects/search';

        $response = Http::withBasicAuth(config('settings.sonarqube_username'), config('settings.sonarqube_password'))
            ->get($url, [
                'project' => 'brands-labels',
            ]);

        $responseData = $response->json();

        $html = view('sonarCube.project-list-modal-html')->with('projects', $responseData)->render();

        return response()->json(['code' => 200, 'data' => $responseData, 'html' => $html, 'message' => 'Content render']);
    }

    public function searchIssues(Request $request): View
    {
        $search = request('search', '');

        $issues = new SonarQube;

        if (! empty($search)) {
            $issues = $issues->where(function ($q) use ($search) {
                $q->where('severity', 'LIKE', '%'.$search.'%')
                    ->orWhere('component', 'LIKE', '%'.$search.'%')
                    ->orWhere('project', 'LIKE', '%'.$search.'%')
                    ->orWhere('message', 'LIKE', '%'.$search.'%')
                    ->orWhere('author', 'LIKE', '%'.$search.'%')
                    ->orWhere('status', 'LIKE', '%'.$search.'%');
            });
        }

        if (isset($request->severity) && count($request->severity) > 0) {
            $issues = $issues->whereIn('severity', $request->severity);
        }
        if (isset($request->author) && count($request->author) > 0) {
            $issues = $issues->whereIn('author', $request->author);
        }
        if (isset($request->project) && count($request->project) > 0) {
            $issues = $issues->whereIn('project', $request->project);
        }

        $issues = $issues->orderByDesc('id')->paginate(100);

        //Filter Dropdown properties - S
        $issuesFilterSeverity = SonarQube::getFilterSeverity();
        $issuesFilterAuthor = SonarQube::getFilterAuthor();
        $issuesFilterProject = SonarQube::getFilterProject();
        $issuesFilterStatus = SonarQube::getFilterStatus();
        //Filter Dropdown properties - E

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();
        $gitHubRepository = GithubRepository::all();

        return view('sonarCube.index', compact('issues', 'allUsers', 'issuesFilterSeverity', 'issuesFilterAuthor', 'issuesFilterProject', 'issuesFilterStatus', 'gitHubRepository'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function searchUserTokens(Request $request): JsonResponse
    {
        $url = config('settings.sonarqube_url').'api/user_tokens/search';

        $response = Http::withBasicAuth(config('settings.sonarqube_username'), config('settings.sonarqube_password'))
            ->get($url);

        $responseData = $response->json();

        $html = view('sonarCube.project-user-list-modal-html')->with('projects', $responseData)->render();

        return response()->json(['code' => 200, 'data' => $responseData, 'html' => $html, 'message' => 'Content render']);
    }

    public function taskCount($site_developement_id): JsonResponse
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }
}
