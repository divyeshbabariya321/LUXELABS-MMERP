<?php

namespace App\Http\Controllers;

use App\BugStatus;
use App\BugStatusHistory;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugUserHistory;
use App\ChatMessage;
use App\Http\Requests\TestCaseStoreRequest;
use App\Http\Requests\TestCaseUpdateRequest;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\TestCase;
use App\TestCaseHistory;
use App\TestCaseStatus;
use App\TestCaseStatusHistory;
use App\TestCaseUserHistory;
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

class TestCaseController extends Controller
{
    public $filterWebsites;

    public $users;

    public function __construct()
    {
        $this->filterWebsites = StoreWebsite::orderBy('website')->pluck('title', 'id')->toArray();
        $this->users = User::pluck('name', 'id')->toArray();
    }

    public function index(Request $request): View
    {
        $title = 'Test Cases';
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $testCaseStatuses = TestCaseStatus::get();
        $filterWebsites = $this->filterWebsites;
        $users = $this->users;

        return view('test-cases.index', [
            'title' => $title,
            'filterCategories' => $filterCategories,
            'filterWebsites' => $filterWebsites,
            'users' => $users,
            'testCaseStatuses' => $testCaseStatuses,
        ]);
    }

    public function create(): View
    {
        $testCaseStatuses = TestCaseStatus::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();

        return view('test-cases.create', compact('testCaseStatuses', 'users', 'filterCategories', 'filterWebsites'));
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
        $records = TestCaseStatus::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function store(TestCaseStoreRequest $request): RedirectResponse
    {
        $id = $request->get('id', 0);

        $records = TestCase::find($id);

        if (! $records) {
            $records = new TestCase();
        }
        $test['created_by'] = Auth::user()->id;
        $records->fill($test);
        $records->save();
        $test['test_case_id'] = $records->id;
        $params = ChatMessage::create([
            'user_id' => Auth::user()->id,
            'test_case_id' => $records->id,
            'sent_to_user_id' => ($request->assign_to != Auth::user()->id) ? $request->assign_to : Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $request->name,
        ]);
        $testCaseHistory = TestCaseHistory::create($test);

        $usertestHistory = [
            'test_case_id' => $records->id,
            'new_user' => $request->assign_to,
            'updated_by' => Auth::user()->id,
        ];
        $userteststatusHistory = [
            'test_case_id' => $records->id,
            'new_status' => $request->test_status_id,
            'updated_by' => Auth::user()->id,
        ];
        TestCaseHistory::create($usertestHistory);
        TestCaseStatusHistory::create($userteststatusHistory);

        return redirect()->route('test-cases.index');
    }

    public function usertestHistory($id)
    {
        $testcaseusers = TestCaseUserHistory::where('test_case_id', $id)->get();
        $testcaseusers = $testcaseusers->map(function ($testcaseuser) {
            $testcaseuser->new_user = User::where('id', $testcaseuser->new_user)->value('name');
            $testcaseuser->old_user = User::where('id', $testcaseuser->old_user)->value('name');
            $testcaseuser->updated_by = User::where('id', $testcaseuser->updated_by)->value('name');
            $testcaseuser->created_at_date = $testcaseuser->created_at;

            return $testcaseuser;
        });

        return response()->json(['code' => 200, 'data' => $testcaseusers]);
    }

    public function userteststatusHistory($id)
    {
        $testcasestatus = TestCaseStatusHistory::where('test_case_id', $id)->get();
        $testcasestatus = $testcasestatus->map(function ($testcaseuserstatus) {
            $testcaseuserstatus->new_status = TestCaseStatus::where('id', $testcaseuserstatus->new_status)->value('name');
            $testcaseuserstatus->old_status = TestCaseStatus::where('id', $testcaseuserstatus->old_status)->value('name');
            $testcaseuserstatus->updated_by = User::where('id', $testcaseuserstatus->updated_by)->value('name');
            $testcaseuserstatus->created_at_date = $testcaseuserstatus->created_at;

            return $testcaseuserstatus;
        });

        return response()->json(['code' => 200, 'data' => $testcasestatus]);
    }

    public function records(Request $request)
    {
        $filterWebsites = $this->filterWebsites;
        $users = $this->users;
        if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Lead Tester')) {
            $records = TestCase::orderByDesc('id');
        } else {
            $records = TestCase::where('assign_to', Auth::user()->id)->orderByDesc('id');
        }

        if ($keyword = request('name')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('suite')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('suite', 'LIKE', "%$keyword%");
            });
        }

        if ($keyword = request('test_case_status')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('test_status_id', $keyword);
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
        if ($keyword = request('precondition')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('precondition', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('website')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('assign_to_user')) {
            $records = $records->whereIn('assign_to', $keyword);
        }

        // #DEVTASK-24253 - filter for the resource who has created
        if ($keyword = request('created_by')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('created_by', '=', "$keyword");
            });
        }

        if ($keyword = request('test_status')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->orWhereIn('test_status_id', $keyword);
            });
        }
        if ($keyword = request('date')) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->whereDate('created_at', $keyword);
            });
        }

        $records = $records->get();
        $records = $records->map(function ($testCase) use ($users, $filterWebsites) {
            $testCase->created_by = $users[$testCase->created_by];
            $testCase->created_at_date = \Carbon\Carbon::parse($testCase->created_at)->format('d-m-Y');
            $testCase->website = $filterWebsites[$testCase->website] ?? '';
            $testCase->step_to_reproduce_short = Str::limit($testCase->step_to_reproduce, 5, '..');

            return $testCase;
        });

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }

    public function edit($id): JsonResponse
    {
        $testCase = TestCase::findorFail($id);
        $testCaseStatuses = TestCaseStatus::get();
        $users = User::get();
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('website')->toArray();
        if ($testCase) {
            return response()->json([
                'code' => 200,
                'data' => $testCase,
                'testCaseStatuses' => $testCaseStatuses,
                'filterCategories' => $filterCategories,
                'users' => $users,
                'filterWebsites' => $filterWebsites, ]
            );
        }

        return response()->json(['code' => 500, 'error' => 'Wrong bug tracking id!']);
    }

    public function update(TestCaseUpdateRequest $request): RedirectResponse
    {
        $data = $request->except('_token', 'id');
        $testCase = TestCase::where('id', $request->id)->first();

        $data['created_by'] = Auth::user()->id;
        $data['updated_by'] = Auth::user()->id;
        $params = ChatMessage::create([
            'user_id' => Auth::user()->id,
            'test_case_id' => $testCase->id,
            'sent_to_user_id' => ($request->assign_to != Auth::user()->id) ? $request->assign_to : Auth::user()->id,
            'approved' => '1',
            'status' => '2',
            'message' => $request->name,
        ]);
        $testCase->update($data);
        $data['test_case_id'] = $request->id;
        TestCaseHistory::create($data);

        return redirect()->route('test-cases.index')->with('success', 'You have successfully updated a Bug Tracker!');
    }

    public function testCaseHistory($id)
    {
        $testCaseHistory = TestCaseHistory::where('test_case_id', $id)->orderByDesc('id')->get();
        $testCaseHistory = $testCaseHistory->map(function ($testCase) {
            $testCase->assign_to = User::where('id', $testCase->assign_to)->value('name');
            $testCase->updated_by = User::where('id', $testCase->updated_by)->value('name');
            $testCase->test_status_id = TestCaseStatus::where('id', $testCase->test_status_id)->value('name');

            return $testCase;
        });

        return response()->json(['code' => 200, 'data' => $testCaseHistory]);
    }

    public function destroy(TestCase $testCase, Request $request): JsonResponse
    {
        try {
            $testCase = TestCase::where('id', '=', $request->id)->delete();
            $testCaseHistory = TestCaseHistory::where('test_case_id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $testCase, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Test Case Request Delete Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->BugErrorLog($request->id ?? '', 'Bug Tracker Request Delete Error', $msg, 'bug_tracker');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function deleteTestCases(TestCase $testCase, Request $request): JsonResponse
    {
        try {
            TestCase::whereIn('id', $request->data)->delete();

            return response()->json(['code' => 200, 'data' => '', 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $id = $request->id;
        $user = Auth::user();
        $test = TestCase::find($request->id);

        $taskdata = $request->message;

        $userid = Auth::id();

        if ($user) {
            $params = ChatMessage::create([
                'user_id' => $userid,
                'erp_user' => $userid,
                'test_case_id' => $test->id,
                'sent_to_user_id' => ($test->assign_to != $user->id) ? $test->assign_to : $test->created_by,
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

    public function assignUser(Request $request): JsonResponse
    {
        $testCase = TestCase::where('id', $request->id)->first();
        $record = [
            'old_user' => $testCase->assign_to,
            'new_user' => $request->user_id,
            'test_case_id' => $testCase->id,
            'updated_by' => Auth::user()->id,
        ];
        $testCase->assign_to = $request->user_id;
        $testCase->save();
        $data = [
            'test_case_id' => $testCase->id,
            'name' => $testCase->name,
            'step_to_reproduce' => $testCase->step_to_reproduce,
            'suite' => $testCase->suite,
            'precondition' => $testCase->precondition,
            'assign_to' => $testCase->assign_to,
            'expected_result' => $testCase->expected_result,
            'test_status_id' => $testCase->test_status_id,
            'module_id' => $testCase->module_id,
            'created_by' => $testCase->created_by,
            'updated_by' => Auth::user()->id,
        ];
        TestCaseHistory::create($data);
        TestCaseUserHistory::create($record);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function statusUser(Request $request): JsonResponse
    {
        $testCase = TestCase::where('id', $request->id)->first();
        $record = [
            'old_status' => $testCase->test_status_id,
            'new_status' => $request->status_id,
            'test_case_id' => $testCase->id,
            'updated_by' => Auth::user()->id,
        ];
        $testCase->test_status_id = $request->status_id;
        $testCase->save();

        $data = [
            'test_case_id' => $testCase->id,
            'name' => $testCase->name,
            'step_to_reproduce' => $testCase->step_to_reproduce,
            'suite' => $testCase->suite,
            'precondition' => $testCase->precondition,
            'assign_to' => $testCase->assign_to,
            'expected_result' => $testCase->expected_result,
            'test_status_id' => $testCase->test_status_id,
            'module_id' => $testCase->module_id,
            'created_by' => $testCase->created_by,
            'updated_by' => Auth::user()->id,
        ];
        TestCaseHistory::create($data);
        TestCaseStatusHistory::create($record);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function sendTestCases(Request $request): JsonResponse
    {
        if ($request->website) {
            $testCases = TestCase::where('website', $request->website)->get();
            $bugStatus = BugStatus::where('name', 'In Test')->first();
            if (count($testCases) > 0) {
                foreach ($testCases as $testCase) {
                    $bugTracking = new BugTracker();
                    $bugTracking->module_id = $testCase->module_id;
                    $bugTracking->step_to_reproduce = $testCase->step_to_reproduce;
                    $bugTracking->expected_result = $testCase->expected_result;
                    $bugTracking->test_case_id = $testCase->id;
                    $bugTracking->website = $request->bug_website;
                    $bugTracking->created_by = Auth::user()->id;
                    $bugTracking->assign_to = $request->assign_to_test_case;
                    $bugTracking->bug_status_id = $bugStatus->id;
                    $bugTracking->save();
                    $params = ChatMessage::create([
                        'user_id' => Auth::user()->id,
                        'bug_id' => $bugTracking->id,
                        'sent_to_user_id' => $request->assign_to_test_case,
                        'approved' => '1',
                        'status' => '2',
                        'message' => $testCase->name,
                    ]);
                    $bugTrackingHistory = new BugTrackerHistory();
                    $bugTrackingHistory->bug_id = $bugTracking->id;
                    $bugTrackingHistory->module_id = $testCase->module_id;
                    $bugTrackingHistory->expected_result = $testCase->expected_result;
                    $bugTrackingHistory->test_case_id = $testCase->id;
                    $bugTrackingHistory->step_to_reproduce = $testCase->step_to_reproduce;
                    $bugTrackingHistory->website = $request->bug_website;
                    $bugTrackingHistory->assign_to = $request->assign_to_test_case;
                    $bugTrackingHistory->created_by = Auth::user()->id;
                    $bugTrackingHistory->bug_status_id = $bugStatus->id;
                    $bugTrackingHistory->save();
                    $statusHistory = [
                        'bug_id' => $bugTracking->id,
                        'new_status' => $bugStatus->id,
                        'updated_by' => Auth::user()->id,
                    ];
                    BugStatusHistory::create($statusHistory);
                    $record = [
                        'new_user' => $request->assign_to_test_case,
                        'bug_id' => $bugTracking->id,
                        'updated_by' => Auth::user()->id,
                    ];
                    BugUserHistory::create($record);
                }

                return response()->json(['code' => 200, 'message' => 'Test Cases Added Successfully']);
            } else {
                return response()->json(['code' => 500, 'error' => 'No Record Found']);
            }
        } else {
            return response()->json(['code' => 500, 'error' => 'website is required']);
        }
    }

    public function testCasesByModule($module_id): JsonResponse
    {
        $testCases = TestCase::where('module_id', $module_id)->select('id', 'name')->get();

        return response()->json(['code' => 200, 'testCases' => $testCases]);
    }

    public function show($id): JsonResponse
    {
        $testCase = TestCase::findorFail($id);

        return response()->json(['code' => 200, 'testCase' => $testCase]);
    }
}
