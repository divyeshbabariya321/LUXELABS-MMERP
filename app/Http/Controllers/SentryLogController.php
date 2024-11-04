<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\Models\SantryStatusHistory;
use App\Models\SentyStatus;
use App\Sentry\SentryAccount;
use App\Sentry\SentryErrorLog;
use App\Task;
use App\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SentryLogController extends Controller
{
    public function index(Request $request): View
    {
        $sentry_logs = SentryErrorLog::orderByDesc('id');

        if ($request->project_list) {
            $sentry_logs = SentryErrorLog::where('project_id', $request->project_list)->get();
        }

        if ($request->keyword) {
            if ($keyword = $request->keyword) {
                $sentry_logs = $sentry_logs->where(
                    function ($q) use ($keyword) {
                        $q->where('error_title', 'LIKE', "%$keyword%");
                        $q->orWhere('error_id', 'LIKE', "%$keyword%");
                        $q->orWhere('device_name', 'LIKE', "%$keyword%");
                        $q->orWhere('os', 'LIKE', "%$keyword%");
                        $q->orWhere('os_name', 'LIKE', "%$keyword%");
                        $q->orWhere('release_version', 'LIKE', "%$keyword%");
                    }
                );
            }
        }
        $sentry_logs = $sentry_logs->get();

        $project_list = SentryAccount::get();
        $sentryLogsData = [];
        $projects = [];
        foreach ($sentry_logs as $error_log) {
            $res['id'] = $error_log->error_id;
            $res['title'] = $error_log->error_title;
            $res['issue_type'] = $error_log->issue_type;
            $res['issue_category'] = $error_log->issue_category;
            $res['is_unhandled'] = $error_log->is_unhandled;
            $res['project'] = $error_log->sentry_project->sentry_project;
            $res['total_events'] = $error_log->total_events;
            $res['total_user'] = $error_log->total_user;
            $res['device_name'] = $error_log->device_name;
            $res['os'] = $error_log->os;
            $res['os_name'] = $error_log->os_name;
            $res['release_version'] = $error_log->release_version;
            $res['first_seen'] = $error_log->first_seen;
            $res['last_seen'] = $error_log->last_seen;
            $res['status_id'] = $error_log->status_id;
            $res['unique_id'] = $error_log->id;
            $sentryLogsData[] = $res;
        }
        foreach ($project_list as $project) {
            $data['id'] = $project->id;
            $data['name'] = $project->sentry_project;
            $projects[] = $data;
        }

        $status = SentyStatus::all();

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();
        $githubRepository = \App\Github\GithubRepository::all();
        $sentyStatus = SentyStatus::all();

        return view('sentry-log.index', compact('sentryLogsData', 'projects', 'status', 'allUsers', 'githubRepository', 'sentyStatus'));
    }

    public function getSentryLogData(Request $request)
    {
        $url = 'https://sentry.io/api/0/projects/'.config('settings.sentry_organization').'/'.config('settings.sentry_project').'/issues/';
        $httpClient = new Client;

        $response = $httpClient->get(
            $url,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.config('settings.sentry_token'),
                ],
            ]
        );
        $responseJson = json_decode($response->getBody()->getContents());

        $sentryLogsData = [];
        $totalRecods = 0;
        for ($i = 0; $i < 100; $i++) {
            foreach ($responseJson as $error_log) {
                $res['id'] = $error_log->id;
                $res['title'] = $error_log->title;
                $res['issue_type'] = $error_log->issueType;
                $res['issue_category'] = $error_log->issueCategory;
                $res['is_unhandled'] = $error_log->isUnhandled;
                $res['first_seen'] = $error_log->firstSeen;
                $res['last_seen'] = $error_log->lastSeen;
                $sentryLogsData[] = $res;
                $totalRecods++;
            }
        }

        foreach ($sentryLogsData as $error_log) {
            $sub_array = [];
            $sub_array[] = $error_log['id'];
            $sub_array[] = $error_log['title'];
            $sub_array[] = $error_log['issue_type'];
            $sub_array[] = $error_log['issue_category'];
            $sub_array[] = $error_log['is_unhandled'];
            $sub_array[] = $error_log['first_seen'];
            $sub_array[] = $error_log['last_seen'];
            $data[] = $sub_array;
        }

        if (! empty($data)) {
            $output = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecods,
                'recordsFiltered' => $totalRecods,
                'data' => $data,
            ];
        } else {
            $output = [
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ];
        }

        return json_encode($output);
    }

    public function saveUserAccount(Request $request): JsonResponse
    {
        try {
            $sentry_acount = new SentryAccount;
            $sentry_acount->sentry_project = $request->project;
            $sentry_acount->sentry_organization = $request->organization;
            $sentry_acount->sentry_token = $request->token;

            if ($sentry_acount->save()) {
                $url = 'https://sentry.io/api/0/projects/'.$sentry_acount->sentry_organization.'/'.$sentry_acount->sentry_project.'/issues/';
                $httpClient = new Client;

                $response = $httpClient->get(
                    $url,
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => 'Bearer '.$sentry_acount->sentry_token,
                        ],
                    ]
                );
                $responseJson = json_decode($response->getBody()->getContents());

                foreach ($responseJson as $error_log) {
                    SentryErrorLog::create([
                        'error_id' => $error_log->id,
                        'error_title' => $error_log->title,
                        'issue_type' => $error_log->issueType,
                        'issue_category' => $error_log->issueCategory,
                        'is_unhandled' => ($error_log->isUnhandled == 'false') ? 0 : 1,
                        'first_seen' => date('d-m-y H:i:s', strtotime($error_log->firstSeen)),
                        'last_seen' => date('d-m-y H:i:s', strtotime($error_log->lastSeen)),
                        'project_id' => $sentry_acount->id,
                    ]);
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Sentry Account Added successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
        }
    }

    public function displayUserAccountList(Request $request)
    {
        $sentryAccounts = SentryAccount::all();
        $html = '';
        $i = 1;
        foreach ($sentryAccounts as $account) {
            $html .= '<tr>';
            $html .= '<td>'.$i++.'</td>';
            $html .= '<td>'.$account->sentry_organization.'</td>';
            $html .= '<td>'.$account->sentry_project.'</td>';
            $html .= "<td style='vertical-align:middle;'>".$account->sentry_token.'</td>';
            $html .= '</tr>';
        }

        return $html;
    }

    public function refreshLogs(): RedirectResponse
    {
        Artisan::call('sentry:load_error_logs');

        return redirect()->back();
    }

    public function sentryStatusCreate(Request $request): JsonResponse
    {
        try {
            $status = new SentyStatus;
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = SentyStatus::find($key);
            $bugstatus->senty_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
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
        $SantryLogId = $request->input('SantryLogId');
        $selectedStatus = $request->input('selectedStatus');

        $SentryErrorLog = SentryErrorLog::find($SantryLogId);
        $history = new SantryStatusHistory;
        $history->santry_log_id = $SantryLogId;
        $history->old_value = $SentryErrorLog->status_id;
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        $SentryErrorLog->status_id = $selectedStatus;
        $SentryErrorLog->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function sentryStatusHistories($id): JsonResponse
    {
        $datas = SantryStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('santry_log_id', $id)
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
