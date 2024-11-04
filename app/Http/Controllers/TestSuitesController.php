<?php

namespace App\Http\Controllers;

use App\BugEnvironment;
use App\BugSeverity;
use App\BugStatus;
use App\BugType;
use App\ChatMessage;
use App\Http\Requests\TestSuitesStoreRequest;
use App\Http\Requests\UpdateTestSuiteRequest;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\TestSuites;
use App\TestSuitesHistory;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Exception;

class TestSuitesController extends Controller
{
    public function index(Request $request): View
    {
        $title = 'Test Suites';

        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->get();

        return view('test-suites.index', [
            'title' => $title,
            'bugTypes' => $bugTypes,
            'bugEnvironments' => $bugEnvironments,
            'bugSeveritys' => $bugSeveritys,
            'bugStatuses' => $bugStatuses,
            'filterCategories' => $filterCategories,
            'users' => $users,
            'filterWebsites' => $filterWebsites,
        ]);
    }

    public function records(Request $request)
    {
        $records = TestSuites::orderByDesc('id');

        if ($keyword = request('name')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('test_cases')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('test_cases', 'LIKE', "%$keyword%");
            });
        }

        if ($keyword = request('bug_enviornment')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_environment_id', $keyword);
            });
        }

        if ($keyword = request('bug_status')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('bug_status_id', $keyword);
            });
        }
        if ($keyword = request('module_id')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('module_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('step_to_reproduce')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('step_to_reproduce', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('test_cases_search')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('test_cases', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('url')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('url', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('website')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('date')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->whereDate('created_at', $keyword);
            });
        }
        $records = $records->get();
        $records = $records->map(function ($bug) {
            $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name');
            $bug->created_by = User::where('id', $bug->created_by)->value('name');
            $bug->created_at_date = \Carbon\Carbon::parse($bug->created_at)->format('d-m-Y  H:i');
            $bug->bug_history = TestSuitesHistory::where('test_suites_id', $bug->id)->get();
            $bug->website = StoreWebsite::where('id', $bug->website)->value('title');
            $bug->name_short = Str::limit($bug->name, 5, '..');
            $bug->test_cases_short = Str::limit($bug->test_cases, 5, '..');
            $bug->step_to_reproduce_short = Str::limit($bug->step_to_reproduce, 5, '..');
            $bug->url_short = Str::limit($bug->url, 5, '..');

            return $bug;
        });

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function create(): View
    {
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();

        return view('test-suites.create', compact('bugStatuses', 'bugTypes', 'bugEnvironments', 'bugSeveritys', 'users', 'filterCategories', 'filterWebsites'));
    }

    public function edit($id): JsonResponse
    {
        $TestSuites = TestSuites::findorFail($id);
        $bugStatuses = BugStatus::get();
        $bugEnvironments = BugEnvironment::get();
        $bugSeveritys = BugSeverity::get();
        $bugTypes = BugType::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();
        if ($TestSuites) {
            return response()->json([
                'code' => 200,
                'data' => $TestSuites,
                'bugTypes' => $bugTypes,
                'bugEnvironments' => $bugEnvironments,
                'bugSeveritys' => $bugSeveritys,
                'bugStatuses' => $bugStatuses,
                'filterCategories' => $filterCategories,
                'users' => $users,
                'filterWebsites' => $filterWebsites, ]
            );
        }

        return response()->json(['code' => 500, 'error' => 'Wrong Test Suites id!']);
    }

    public function status(Request $request): JsonResponse
    {
        $status = $request->all();
        $validator = Validator::make($status, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugStatus::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function environment(Request $request): JsonResponse
    {
        $environment = $request->all();
        $validator = Validator::make($environment, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugEnvironment::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function type(Request $request): JsonResponse
    {
        $type = $request->all();
        $validator = Validator::make($type, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugType::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function severity(Request $request): JsonResponse
    {
        $severity = $request->all();
        $validator = Validator::make($severity, [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Name is required']);
        }
        $data = $request->except('_token');
        $records = BugSeverity::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function store(TestSuitesStoreRequest $request): RedirectResponse
    {
        $id = $request->get('id', 0);

        $records = TestSuites::find($id);

        if (! $records) {
            $records = new TestSuites();
        }
        $bug['created_by'] = Auth::user()->id;
        $bug['name'] = str_replace("\n", '<br/>', $bug['name']);
        $bug['test_cases'] = str_replace("\n", '<br/>', $bug['test_cases']);
        $bug['step_to_reproduce'] = str_replace("\n", '<br/>', $bug['step_to_reproduce']);
        $records->fill($bug);

        $records->save();

        $params = ChatMessage::create([
            'user_id' => Auth::user()->id,
            'test_suites_id' => $records->id,
            'sent_to_user_id' => ($records->assign_to != Auth::user()->id) ? $records->assign_to : Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $records->remark,
        ]);
        $bug['test_suites_id'] = $records->id;
        $bug['updated_by'] = Auth::user()->id;
        $TestSuitesHistory = TestSuitesHistory::create($bug);

        return redirect()->back();
    }

    public function destroy(TestSuites $TestSuites, Request $request): JsonResponse
    {
        try {
            $bug = TestSuites::where('id', '=', $request->id)->delete();
            $TestSuitesHistory = TestSuitesHistory::where('test_suites_id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $bug, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Test Suites Request Delete Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->BugErrorLog($request->id ?? '', 'Test Suites Request Delete Error', $msg, 'bug_tracker');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function update(UpdateTestSuiteRequest $request): RedirectResponse
    {

        $data = $request->except('_token', 'id');
        $bug = TestSuites::where('id', $request->id)->first();

        $data['created_by'] = Auth::user()->id;
        $bug['updated_by'] = Auth::user()->id;

        $params = ChatMessage::create([
            'user_id' => Auth::user()->id,
            'test_suites_id' => $bug->id,
            'sent_to_user_id' => ($request->assign_to != Auth::user()->id) ? $request->assign_to : Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $request->remark,
        ]);
        $data['name'] = str_replace("\n", '<br/>', $request->name);
        $data['test_cases'] = str_replace("\n", '<br/>', $request->test_cases);
        $data['step_to_reproduce'] = str_replace("\n", '<br/>', $request->step_to_reproduce);

        $bug->update($data);
        $data['test_suites_id'] = $request->id;

        TestSuitesHistory::create($data);

        return redirect()->route('test-suites.index')->with('success', 'You have successfully updated a Test Suites!');
    }

    public function bugHistory($id)
    {
        $bugHistory = TestSuitesHistory::where('test_suites_id', $id)->get();
        $bugHistory = $bugHistory->map(function ($bug) {
            $bug->bug_environment_id = BugEnvironment::where('id', $bug->bug_environment_id)->value('name').' '.$bug->bug_environment_ver;
            $bug->assign_to = User::where('id', $bug->assign_to)->value('name');
            $bug->updated_by = User::where('id', $bug->updated_by)->value('name');
            $bug->bug_status_id = BugStatus::where('id', $bug->bug_status_id)->value('name');
            $bug->bug_history = TestSuitesHistory::where('test_suites_id', $bug->id)->get();

            return $bug;
        });

        return response()->json(['code' => 200, 'data' => $bugHistory]);
    }

    public function assignUser(Request $request): JsonResponse
    {
        $TestSuites = TestSuites::where('id', $request->id)->first();
        $TestSuites->assign_to = $request->user_id;
        $TestSuites->save();
        $data = [
            'name' => $TestSuites->name,
            'test_cases' => $TestSuites->test_cases,
            'step_to_reproduce' => $TestSuites->step_to_reproduce,
            'url' => $TestSuites->url,
            'bug_environment_id' => $TestSuites->bug_environment_id,
            'assign_to' => $TestSuites->assign_to,
            'bug_status_id' => $TestSuites->bug_status_id,
            'module_id' => $TestSuites->module_id,
            'remark' => $TestSuites->remark,
            'website' => $TestSuites->website,
            'test_suites_id' => $TestSuites->id,
            'updated_by' => Auth::user()->id,
        ];
        TestSuitesHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function severityUser(Request $request): JsonResponse
    {
        $TestSuites = TestSuites::where('id', $request->id)->first();
        $TestSuites->bug_severity_id = $request->severity_id;
        $TestSuites->save();
        $data = [
            'step_to_reproduce' => $TestSuites->step_to_reproduce,
            'url' => $TestSuites->url,
            'name' => $TestSuites->name,
            'test_cases' => $TestSuites->test_cases,
            'bug_environment_id' => $TestSuites->bug_environment_id,
            'assign_to' => $TestSuites->assign_to,
            'bug_status_id' => $TestSuites->bug_status_id,
            'module_id' => $TestSuites->module_id,
            'remark' => $TestSuites->remark,
            'website' => $TestSuites->website,
            'test_suites_id' => $TestSuites->id,
            'updated_by' => Auth::user()->id,
        ];
        TestSuitesHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function statusUser(Request $request): JsonResponse
    {
        $TestSuites = TestSuites::where('id', $request->id)->first();
        $TestSuites->bug_status_id = $request->status_id;
        $TestSuites->save();

        $data = [
            'name' => $TestSuites->name,
            'test_cases' => $TestSuites->test_cases,
            'step_to_reproduce' => $TestSuites->step_to_reproduce,
            'url' => $TestSuites->url,
            'bug_environment_id' => $TestSuites->bug_environment_id,
            'assign_to' => $TestSuites->assign_to,
            'bug_status_id' => $TestSuites->bug_status_id,
            'module_id' => $TestSuites->module_id,
            'remark' => $TestSuites->remark,
            'website' => $TestSuites->website,
            'test_suites_id' => $TestSuites->id,
            'updated_by' => Auth::user()->id,
        ];
        TestSuitesHistory::create($data);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $id = $request->id;
        $user = Auth::user();
        $task = TestSuites::find($request->id);

        $taskdata = $request->message;

        $userid = Auth::id();

        if ($user) {
            $params = ChatMessage::create([
                'user_id' => $userid,
                'erp_user' => $userid,
                'test_suites_id' => $task->id,
                'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                'sent_to_user_id' => ($task->assign_to != $user->id) ? $task->assign_to : $task->created_by,
                'approved' => '1',
                'status' => '2',
                'message' => $taskdata,
            ]);

            if ($params) {
                return response()->json(['code' => 200, 'message' => 'Successfully Send File']);
            }

            return response()->json([
                'message' => 'Something Was Wrong',
            ], 500);

        }
    }

    public function communicationData($id)
    {
        $messages = ChatMessage::where('test_suites_id', $id)->orderByDesc('id')->get();
        $messages = $messages->map(function ($message) {
            $message->user_name = 'From '.User::where('id', $message->user_id)->value('name').' to '.User::where('id', $message->send_to_user_id)->value('name').' '.\Carbon\Carbon::parse($message->created_at)->format('Y-m-d H:i A');

            return $message;
        });

        return response()->json(['code' => 200, 'data' => $messages]);
    }
}
