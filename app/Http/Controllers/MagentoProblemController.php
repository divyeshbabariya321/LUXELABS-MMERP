<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\Github\GithubRepository;
use App\Models\MagentoProblem;
use App\Models\MagentoProblemStatus;
use App\Models\MagentoProblemStatusHistory;
use App\Models\MagentoProblemUserHistory;
use App\Setting;
use App\Task;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MagentoProblemController extends Controller
{
    public function index(Request $request): View
    {
        $magentoProblems = MagentoProblem::select('error_body', 'created_at', 'updated_at', 'source', 'test', 'severity', 'type', 'status', 'user_id', DB::raw('MAX(id) AS id'))->orderByDesc('id');

        if ($request->search_source) {
            $magentoProblems = $magentoProblems->where('source', 'LIKE', '%'.$request->search_source.'%');
        }
        if ($request->search_test) {
            $magentoProblems = $magentoProblems->Where('test', 'LIKE', '%'.$request->search_test.'%');
        }
        if ($request->search_severity) {
            $magentoProblems = $magentoProblems->Where('severity', 'LIKE', '%'.$request->search_severity.'%');
        }
        if ($request->error_body) {
            $magentoProblems = $magentoProblems->Where('error_body', 'LIKE', '%'.$request->error_body.'%');
        }
        if ($request->date) {
            $magentoProblems = $magentoProblems->where('created_at', 'LIKE', '%'.$request->date.'%');
        }
        if ($request->has('status')) {
            if ($request->status == 'open') {
                $magentoProblems = $magentoProblems->where('status', 1);
            } elseif ($request->status == 'closed') {
                $magentoProblems = $magentoProblems->where('status', 0);
            }
        }
        if ($request->type) {
            $magentoProblems = $magentoProblems->where('type', 'LIKE', '%'.$request->type.'%');
        }

        $magentoProblems = $magentoProblems->groupBy('error_body');

        $magentoProblems = $magentoProblems->latest()->paginate(Setting::get('pagination', 10));

        $magento_statuses = MagentoProblemStatus::get();

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();

        $githubRepositories = GithubRepository::all();

        return view('magento-problems.index', compact('magentoProblems', 'magento_statuses', 'allUsers', 'githubRepositories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'error_body' => 'required',
            'source' => 'required',
            'test' => 'required',
            'severity' => 'required',
            'type' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $decodedErrorMessage = base64_decode($request->input('error_body'));

        try {
            $magentoProblem = new MagentoProblem;

            $magentoProblem->source = $request->input('source');
            $magentoProblem->test = $request->input('test');
            $magentoProblem->severity = $request->input('severity') ?? '';
            $magentoProblem->type = $request->input('type') ?? '';
            $magentoProblem->error_body = $decodedErrorMessage;
            $magentoProblem->status = $request->input('status');
            $magentoProblem->save();

            return response()->json(['message' => 'Magento Problem Stored Successfully'], 200);
        } catch (Exception $e) {
            Log::channel('magento_problem_error')->error($e->getMessage());

            return response()->json(['message' => 'An error occurred. Please check the logs.'], 500);
        }
    }

    public function magentoProblemStatusCreate(Request $request): JsonResponse
    {
        try {
            $status = new MagentoProblemStatus;
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
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

    public function updateStatus(Request $request): JsonResponse
    {
        $magentoProblemId = $request->input('magentoProblemId');
        $selectedStatus = $request->input('selectedStatus');

        $MagentoProblem = MagentoProblem::find($magentoProblemId);
        $history = new MagentoProblemStatusHistory;
        $history->magento_problem_id = $magentoProblemId;
        $history->old_value = $MagentoProblem->status;
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        $MagentoProblem->status = $selectedStatus;
        $MagentoProblem->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function magentoproblemsStatusHistories($id): JsonResponse
    {
        $datas = MagentoProblemStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('magento_problem_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateUser(Request $request): JsonResponse
    {
        $magentoProblemId = $request->input('magentoProblemId');
        $selectedUser = $request->input('selectedUser');

        $MagentoProblem = MagentoProblem::find($magentoProblemId);
        $history = new MagentoProblemUserHistory;
        $history->magento_problem_id = $magentoProblemId;
        $history->old_value = $MagentoProblem->user_id;
        $history->new_value = $selectedUser;
        $history->user_id = Auth::user()->id;
        $history->save();

        $MagentoProblem->user_id = $selectedUser;
        $MagentoProblem->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function magentoproblemsUserHistories($id): JsonResponse
    {
        $datas = MagentoProblemUserHistory::with(['user', 'newValue', 'oldValue'])
            ->where('magento_problem_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
