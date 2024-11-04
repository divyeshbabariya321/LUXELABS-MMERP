<?php

namespace App\Http\Controllers;
use App\Setting;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\GitHubAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class GitHubActionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $githubAction = new GitHubAction();

            $githubAction->github_actor         = $request->input('GITHUB_ACTOR') ?? '';
            $githubAction->github_api_url       = $request->input('GITHUB_API_URL') ?? '';
            $githubAction->github_base_ref      = $request->input('GITHUB_BASE_REF') ?? '';
            $githubAction->github_event_name    = $request->input('GITHUB_EVENT_NAME') ?? '';
            $githubAction->github_job           = $request->input('GITHUB_JOB') ?? '';
            $githubAction->github_ref           = $request->input('GITHUB_REF') ?? '';
            $githubAction->github_ref_name      = $request->input('GITHUB_REF_NAME') ?? '';
            $githubAction->github_ref_type      = $request->input('GITHUB_REF_TYPE') ?? '';
            $githubAction->github_repository    = $request->input('GITHUB_REPOSITORY') ?? '';
            $githubAction->github_repository_id = $request->input('GITHUB_REPOSITORY_ID') ?? '';
            $githubAction->github_run_attempt   = $request->input('GITHUB_RUN_ATTEMPT') ?? '';
            $githubAction->github_run_id        = $request->input('GITHUB_RUN_ID') ?? '';
            $githubAction->github_workflow      = $request->input('GITHUB_WORKFLOW') ?? '';
            $githubAction->runner_name          = $request->input('RUNNER_NAME') ?? '';
            $githubAction->save();

            return response()->json(['message' => 'GitHub Action Stored Successfully'], 200);
        } catch (Exception $e) {
            Log::channel('github_error')->error($e->getMessage());

            return response()->json(['message' => 'An error occurred. Please check the logs.'], 500);
        }
    }

    public function index(Request $request): View
    {
        $gitactions = new GitHubAction();

        $apiUrls = GitHubAction::distinct('github_api_url')->pluck('github_api_url');
        $refUrls = GitHubAction::distinct('github_ref')->pluck('github_ref');
        $repos   = GitHubAction::distinct('github_repository')->pluck('github_repository');

        if ($request->search_event) {
            $gitactions = $gitactions->where('github_event_name', 'LIKE', '%' . $request->search_error . '%');
        }
        if ($request->api_url) {
            $gitactions = $gitactions->Where('github_api_url', $request->api_url);
        }
        if ($request->ref_url) {
            $gitactions = $gitactions->Where('github_ref', $request->ref_url);
        }
        if ($request->repo) {
            $gitactions = $gitactions->Where('github_repository', $request->repo);
        }
        if ($request->date) {
            $gitactions = $gitactions->where('created_at', 'LIKE', '%' . $request->date . '%');
        }
        if ($request->search_job) {
            $gitactions = $gitactions->where('github_job', 'LIKE', '%' . $request->search_job . '%');
        }
        if ($request->search_ref_name) {
            $gitactions = $gitactions->where('github_ref_name', 'LIKE', '%' . $request->search_ref_name . '%');
        }
        if ($request->search_ref_type) {
            $gitactions = $gitactions->where('github_ref_type', 'LIKE', '%' . $request->search_ref_type . '%');
        }
        if ($request->search_actor) {
            $gitactions = $gitactions->where('github_actor', 'LIKE', '%' . $request->search_actor . '%');
        }
        if ($request->search_runner) {
            $gitactions = $gitactions->where('runner_name', 'LIKE', '%' . $request->search_runner . '%');
        }

        $gitactions = $gitactions->latest()->paginate(Setting::get('pagination', 10));

        return view('git-actions.git-action-list', compact('gitactions', 'apiUrls', 'repos', 'refUrls'));
    }
}
