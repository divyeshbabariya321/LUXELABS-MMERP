<?php

namespace App\Http\Controllers;
use App\DeveloperTask;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use App\User;
use DataTables;
use App\CronJob;
use App\CronActivity;
use App\ScheduleQuery;
use Studio\Totem\Task;
use App\CronJobErroLog;
use Studio\Totem\Totem;
use App\DeveloperModule;
use function storage_path;
use Illuminate\Http\Request;
use App\Models\Crontask;
use Illuminate\Support\Facades\DB;
use Studio\Totem\Http\Requests\TaskRequest;

class TasksController extends Controller
{
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('totem.tasks.all');
    }

    public function index(Request $request)
    {
        $userCronIds = CronActivity::where('assign_to_id', Auth::User()->id)->pluck('cron_id')->all();
        $tablePrefix = !empty(config("totem.table_prefix")) ? config("totem.table_prefix") : "cron";

        if ($request->ajax()) {
            $tasks = Task::with('frequencies');
            $tasks->select($tablePrefix . 'tasks.*',
                DB::raw('IFNULL((select (sum(duration)/count(id)) from ' . $tablePrefix . 'task_results where ' . $tablePrefix . 'task_results.task_id=' . $tablePrefix . 'tasks.id), 0) as runtime_avg'),
                DB::raw('(select name from developer_modules where developer_modules.id=' . $tablePrefix . 'tasks.developer_module_id) as module_name'),
                DB::raw('(select ran_at from ' . $tablePrefix . 'task_results where ' . $tablePrefix . 'task_results.task_id=' . $tablePrefix . 'tasks.id order by id desc limit 1) as last_ran_at')
            );
            if (! (auth()->user()->isAdmin() || auth()->user()->isCronManager())) {
                $tasks->whereIn('id', $userCronIds);
            }

            return Datatables::of($tasks)
                ->addIndexColumn()
                ->addColumn('module', function ($task) {
                    return $task->developer_module_id ? DeveloperModule::find($task->developer_module_id)->name : '';
                })
                ->orderColumn('module', function ($query, $order) {
                    $query->orderBy('module_name', $order);
                })
                ->addColumn('averageRuntime', function ($task) {
                    return number_format($task->averageRuntime / 1000, 2) . ' seconds';
                })
                ->orderColumn('averageRuntime', function ($query, $order) {
                    $query->orderBy('runtime_avg', $order);
                })
                ->addColumn('last_run', function ($task) {
                    return $task->lastResult ? $task->lastResult->ran_at->toDateTimeString() : 'N/A';
                })
                ->orderColumn('last_run', function ($query, $order) {
                    $query->orderBy('last_ran_at', $order);
                })
                ->addColumn('frequency', function ($task) {
                    return $task->frequencies && count($task->frequencies) > 0 ? implode(', ', $task->frequencies->pluck('label')->toArray()) : '';
                })
                ->addColumn('upcoming', function ($task) {
                    return $task->upcoming;
                })
                ->addColumn('action', function ($task) {
                    $btn = '';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image view-task" href="#" data-id="' . $task->id . '" title="view task" data-expression="' . $task->getCronExpression() . '"><img src="/images/view.png" style="cursor: pointer; width: 0px;"></a>';
                    if (auth()->user()->isAdmin()) {
                        $btn .= '<a style="padding:1px;" class="btn d-inline btn-image edit-task" href="#" data-id="' . $task->id . '" title="edit task"><img src="/images/edit.png" style="cursor: pointer; width: 0px;"></a>';
                        $btn .= '<a style="padding:1px;" class="btn d-inline btn-image delete-tasks" href="#" data-id="' . $task->id . '" title="delete task"><img src="/images/delete.png" style="cursor: pointer; width: 0px;"></a>';
                    }
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image execute-task" href="#" data-id="' . $task->id . '" title="execute Task"><img src="/images/send.png" style="cursor: pointer; width: 0px;"></a>';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image execution-history" href="#" data-id="' . $task->id . '" title="task execution history"><i class="fa fa-globe" aria-hidden="true"></i></a>';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image task-history" href="#" data-id="' . $task->id . '" title="Task History">T</a>';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image command-execution-error" href="#" data-id="' . $task->id . '"  title="Cron Run error History"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></a>';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image command-schedule" href="#" data-id="' . $task->command . '" title="See Cron query and description"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a>';
                    $btn .= '<a style="padding:1px;" class="btn d-inline btn-image show-cron-history" href="#" data-id="' . $task->id . '" title="Show cron assign history"><img src="/images/history.png"  style="cursor: pointer; width: 0px;"></a>';
                    if (auth()->user()->isAdmin() || auth()->user()->isCronManager()) {
                        $btn .= '<a style="padding:1px;" class="btn d-inline btn-image assign-user" href="#" assing-id="' . $task->users_ids . '" task-id="' . $task->id . '" title="Assign user"><i class="fa fa-universal-access" aria-hidden="true"></i></a>';
                    }

                    return $btn;
                })
                ->addColumn('checkbox', function ($task) {
                    $btn = '';
                    if (auth()->user()->isAdmin() || auth()->user()->isCronManager()) {
                        $btn .= '<input style="height:15px;" type="checkbox" data-id="' . $task->id . '" class="checkBoxClass" id="checkbox' . $task->id . '"/></td>';
                    }

                    return $btn;
                })
                ->addColumn('enable_disable', function ($task) {
                    $btn = '';
                    if (auth()->user()->isAdmin() || auth()->user()->isCronManager()) {
                        $btn .= '<label class="switch">
                                        <input class="active-task" data-id="' . $task->id . '" data-active="' . $task->is_active . '" ' . ($task->is_active ? 'checked' : '') . ' type="checkbox">
                                        <span class="slider round"></span>
                                    </label>';
                    }

                    return $btn;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('search')['value']) {
                        $query->where(function ($q) use ($request) {
                            $q->where('description', 'like', '%' . $request->get('search')['value'] . '%')
                                ->orWhere('id', 'like', '%' . $request->get('search')['value'] . '%');
                        });
                    }
                    if ($request->filter_frequency != '') {
                        $query->whereHas('frequencies', function ($query) use ($request) {
                            $query->where('interval', $request->filter_frequency);
                        });
                    }
                    if ($request->is_active != '') {
                        $query->where('is_active', $request->is_active);
                    }
                    if ($request->description != '') {
                        $query->where('description', $request->description);
                    }
                })
                ->rawColumns(['checkbox', 'action', 'enable_disable'])
                ->make(true);
        }

        $columns = [
            ['data'=>'checkbox', 'name'=>'checkbox'],
            ['data'=> 'id', 'name'=>'id'],
            ['data'=> 'description', 'name'=>'description'],
            ['data'=> 'module', 'name'=>'module'],
            ['data'=> 'averageRuntime', 'name'=>'averageRuntime'],
            ['data'=> 'last_run', 'name'=>'last_run'],
            ['data'=> 'upcoming', 'name'=>'upcoming'],
            ['data'=> 'frequency', 'name'=>'frequency'],
            ['data'=> 'action', 'name'=>'action'],
            ['data'=> 'enable_disable', 'name'=>'enable_disable'],
        ];

        $tasks = Task::orderBy('description');
        $nonSortableColumnIndex = [0, 8, 9];

        if (! (auth()->user()->isAdmin() || auth()->user()->isCronManager())) {
            $tasks->whereIn('id', $userCronIds);
            $columns = array_filter($columns, function ($data) {
                return ! in_array($data['name'], ['checkbox', 'enable_disable']);
            });
            $nonSortableColumnIndex = [0,7];
        }
        $tasks = $tasks->pluck('description', 'id');

        return view('totem.tasks.index_new', [
            'tasks'            => $tasks,
            'task'             => null,
            'queries'          => ScheduleQuery::all(),
            'users'            => User::all(),
            'developer_module' => DeveloperModule::all(),
            'commands'         => Totem::getCommands(),
            'timezones'        => timezone_identifiers_list(),
            'frequencies'      => Totem::frequencies(),
            'total_tasks'      => Task::count(),
            'columns'          => array_values($columns),
            'nonSortableColumnIndex' => $nonSortableColumnIndex
        ])->with('i', (request()->input('page', 1) - 1) * 50);
    }

    public function executionHistory($task): JsonResponse
    {
        $taskResults = [];
        $assigned    = CronActivity::where('assign_to_id', Auth::User()->id)->where('cron_id', $task->id)->first();

        if (auth()->user()->isAdmin() || auth()->user()->isCronManager() || $assigned) {
            $taskResults = $task->results()->latest()->take(10)->get();
        }

        return response()->json([
            'task' => $taskResults,
        ]);
    }

    public function create(): View
    {
        return view('totem::tasks.form', [
            'task'        => new Task,
            'commands'    => Totem::getCommands(),
            'timezones'   => timezone_identifiers_list(),
            'frequencies' => Totem::frequencies(),
        ]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        Task::create($request->only([
            'description',
            'command',
            'parameters',
            'timezone',
            'developer_module_id',
            'expression',
            'notification_email_address',
            'notification_phone_number',
            'notification_slack_webhook',
            'dont_overlap',
            'run_in_maintenance',
            'run_on_one_server',
            'auto_cleanup_num',
            'auto_cleanup_type',
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Task Created Successfully.',
        ]);
    }

    public function view(Task $task): JsonResponse
    {
        return response()->json([
            'task'    => Task::find($task->id),
            'results' => $task->results->count() > 0 ? number_format($task->results->sum('duration') / (1000 * $task->results->count()), 2) : '0',
        ]);
    }

    public function edit(Task $task): JsonResponse
    {
        return response()->json([
            'task'        => $task,
            'commands'    => Totem::getCommands(),
            'timezones'   => timezone_identifiers_list(),
            'frequencies' => Totem::frequencies(),
        ]);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $task = Task::where('id', $task->id)->update($request->only([
            'description',
            'command',
            'parameters',
            'timezone',
            'developer_module_id',
            'expression',
            'notification_email_address',
            'notification_phone_number',
            'notification_slack_webhook',
            'dont_overlap',
            'run_in_maintenance',
            'run_on_one_server',
            'auto_cleanup_num',
            'auto_cleanup_type',
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Task Updated Successfully.',
        ]);
    }

    public function destroy($task, Request $request): JsonResponse
    {
        if ($task) {
            $task->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Task Deleted Successfully.',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Task Not Found.',
            ]);
        }
    }

    public function status($task, Request $request): JsonResponse
    {
        if ($task) {
            if ($request->active == 1) {
                Crontask::where('id', $task->id)->update([
                    'is_active' => 0,
                ]);
                $msg = 'Task Deactivated Successfully.';
            } else {
                $x = Crontask::where('id', $task->id)->update([
                    'is_active' => 1,
                ]);
                $msg = 'Task Activated Successfully.';
            }

            return response()->json([
                'status'  => true,
                'message' => $msg,
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Task Not Found.',
            ]);
        }
    }

    public function execute(Task $task): Response
    {
        File::put(storage_path('tasks.json'), Task::all()->toJson());

        return response()
            ->download(storage_path('tasks.json'), 'tasks.json')
            ->deleteFileAfterSend(true);
    }

    public function developmentTask(Request $request, $task): View
    {
        $findTasks = DeveloperTask::where('subject', 'like', '%' . strtoupper($task->command) . '%')->latest()->get();

        return view('totem.tasks.partials.development-task-list', compact('findTasks'));
    }

    public function totemCommandError(Request $request, $task)
    {
        $tortem    = CronJob::where('id', '=', $task->id)->first();
        $cronError = CronJobErroLog::where('signature', '=', $tortem->signature)->get();

        return response()->json([
            'data'    => $cronError,
            'message' => 'Listed successfully!!!',
        ]);
    }

    public function queryCommand(Request $request, $name)
    {
        $query = ScheduleQuery::where('schedule_name', '=', $name)->get()->toArray();

        return $query;
    }

    public function cronHistory(Request $request, $name)
    {
        $query = CronActivity::where('cron_id', '=', $name)->get()->map(function (CronActivity $cronActivity) {
            return [
                'assign_by_name' => $cronActivity->assignBy->name,
                'assign_to_name' => $cronActivity->assignTo->name,
            ];
        });

        return $query;
    }

    public function enableDisableCron(Request $request): JsonResponse
    {
        if ($request->get('ids')) {
            Crontask::whereIn('id', $request->get('ids'))->update([
                'is_active' => $request->get('active'),
            ]);
            $msg = $request->get('active') ? 'Task enabled Successfully' : 'Task disabled Successfully';

            return response()->json([
                'status'  => true,
                'message' => $msg,
            ]);
        }
    }

    public function assignUsers(Request $request): JsonResponse
    {
        foreach ($request->get('users_id') as $userId) {
            $cron               = new CronActivity();
            $cron->assign_by_id = Auth::user()->id;
            $cron->cron_id      = $request->get('task-id');
            $cron->assign_to_id = $userId;
            $cron->save();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Cron assign succesfully',
        ]);
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $crons    = Crontask::get()->toArray();
        $cron_ids = [];
        foreach ($crons as $cron) {
            $cron_ids[] = $cron->id;
        }
        foreach ($request->get('users_id') as $userId) {
            foreach ($cron_ids as $cron_id) {
                $cron               = new CronActivity();
                $cron->assign_by_id = Auth::user()->id;
                $cron->cron_id      = $cron_id;
                $cron->assign_to_id = $userId;
                $cron->save();
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Cron assign succesfully',
        ]);
    }
}
