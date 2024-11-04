<?php

namespace App\Http\Controllers;

use App\BugStatus;
use App\BugStatusHistory;
use App\BugTracker;
use App\BugTrackerHistory;
use App\BugUserHistory;
use App\ChatMessage;
use App\Http\Requests\TestCaseAssignmentRequest;
use App\Http\Requests\TestCaseChangeStatusRequest;
use App\Http\Requests\TestCaseCommandRequest;
use App\Http\Requests\TestCaseStatusRequest;
use App\SiteDevelopmentCategory;
use App\StoreWebsite;
use App\TestCase;
use App\TestCaseHistory;
use App\TestCaseStatus;
use App\TestCaseStatusHistory;
use App\TestCaseUserHistory;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Exception;

class TestCaseAutomationController extends Controller
{
    public function index(Request $request): View
    {
        $title = 'Test Cases Automation';
        $filterCategories = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();
        $testCaseStatuses = TestCaseStatus::get();
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('title', 'id')->toArray();
        $users = User::pluck('name', 'id')->toArray();

        $test_cases = $this->getAllRecords($request);

        $browsers = config('constants.BROWSERS');
        $testList = config('constants.TEST_LIST');
        $testFlow = config('constants.TEST_FLOW_LIST');

        return view('test-cases-automation.index', [
            'title' => $title,
            'filterCategories' => $filterCategories,
            'filterWebsites' => $filterWebsites,
            'users' => $users,
            'testCaseStatuses' => $testCaseStatuses,
            'test_cases' => $test_cases,
            'browsers' => $browsers,
            'testList' => $testList,
            'testFlow' => $testFlow,
        ]);
    }

    public function updateStatus(TestCaseStatusRequest $request): JsonResponse
    {
        $data = $request->except('_token');
        $records = TestCaseStatus::create($data);

        return response()->json(['code' => 200, 'data' => $records]);
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

    public function getAllRecords(Request $request)
    {
        $filterWebsites = StoreWebsite::orderBy('website')->pluck('title', 'id')->toArray();
        $users = User::pluck('name', 'id')->toArray();
        if (Auth::user()->hasRole(config('constants.ADMIN_ROLE')) || Auth::user()->hasRole(config('constants.LEAD_TESTER_ROLE'))) {
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

        $records->groupBy(['website']);

        $records = $records->paginate(20);
        $items = collect($records->items());
        $items->map(function ($testCase) use ($users, $filterWebsites) {
            $testCase->website_url = $testCase->websites->website ?? '';

            $testCase->created_by = (array_key_exists($testCase->created_by, $users)) ? $users[$testCase->created_by] : null;
            $testCase->created_at_date = \Carbon\Carbon::parse($testCase->created_at)->format('d-m-Y');
            $testCase->website = $filterWebsites[$testCase->website] ?? '';

            $testCase->step_to_reproduce_short = Str::limit($testCase->step_to_reproduce, 5, '..');

            return $testCase;
        });

        return $records;
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

    public function assignUser(TestCaseAssignmentRequest $request): JsonResponse
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

    public function statusUser(TestCaseChangeStatusRequest $request): JsonResponse
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

    public function addTestCaseCommand(TestCaseCommandRequest $request)
    {
        try {
            $testCase = TestCase::find($request->test_case_id);
            Log::info('submitRequest : addTestCaseCommand');
            if ($testCase) {

                $command = 'mvn -f automation-test-setup/automationtests/pom.xml test -DsiteToUse='.$request->site_to_use.' -DsiteName='.$request->website_name.' -Denvironment=o2t -Dbrowser='.$request->browser.' -Dheadless='.$request->headless.' -Dtest='.$request->test.' -DtestFlow='.$request->test_flow;

                $testCase->command = $command;
                $testCase->save();

                return redirect()->route('test-case-automation.index')->with('success', 'Command successfully saved');
            } else {
                return redirect()->back()->with('error', 'Record not found');
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function runTestCaseCommand(Request $request, $id)
    {
        try {
            Log::info('Initialized Test Case Command...');

            $testCase = TestCase::find($id);

            Log::info('submitRequest : runTestCaseCommand');
            if ($testCase) {

                Log::info('Started executing command ');

                $output = [];
                $returnCode = '';
                // $output = system($testCase->command, $returnCode);
                exec($testCase->command, $output, $returnCode);

                Log::info('Test Case Command Output-->'.json_encode($output));

                Log::info('Test Case Command Return code-->'.$returnCode);

                Log::info('Test Case Command Root Path-->'.dirname(__DIR__, 4));

                if ($returnCode === 1) {

                    $testFlow = explode('-DtestFlow=', $testCase->command);

                    $htmlFilePath = '';
                    if (! empty($testFlow) && ! empty($testFlow[1])) {
                        $htmlFilePath = env('APP_URL').'automation-test-setup/automationtests/src/test/resources/reports/'.$testFlow[1].'/Spark.html';
                        $testCase->html_file_path = $htmlFilePath;
                    }

                    Log::info('Test Case Command Report File Path-->'.$htmlFilePath);

                    $testCase->request = '';
                    $testCase->response = $returnCode;
                    $testCase->save();

                    Log::info('Successfully executed command.');

                    Log::info('Command run successfully');

                    return response()->json(['status' => 'success', 'code' => $returnCode, 'message' => 'Command run successfully']);
                } else {
                    Log::info('Failed to execute command');

                    return response()->json(['status' => 'fail', 'code' => $returnCode, 'message' => 'Failed to execute command']);
                }
            }
        } catch (Exception $e) {

            Log::info('command error'.$e->getMessage());

            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

    }

    public function deleteTestCaseCommand(Request $request, $id): JsonResponse
    {
        try {
            $testCase = TestCase::find($id);
            if (! $testCase) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => false,
                        'message' => 'Test Case not found',
                    ]
                );
            }
            if (empty($testCase->command)) {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => false,
                        'message' => 'Command not found',
                    ]
                );
            }

            $testCase->command = null;
            $testCase->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Command deleted successfully',
                ]
            );

        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => 404,
                    'status' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

    }

    public function viewResult($id = '')
    {
        if (empty($id)) {
            return redirect()->back()->withErrors('No record found');
        }

        $testCase = TestCase::find($id);
        Log::info('viewResult : Test Case Automation');
        // $testFlow = explode('-DtestFlow=', $testCase->command);
        // $testFlowDirectory = $testFlow[1];
        $testFlowDirectory = $testCase->html_file_path;

        return view('test-cases-automation.view-result', compact('testFlowDirectory'));
    }
}
