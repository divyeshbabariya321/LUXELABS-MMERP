<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreZabbixTaskRequest;
use App\Models\ZabbixTaskAssigneeHistory;
use App\Models\ZabbixWebhookData;
use App\Task;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ZabbixTaskController extends Controller
{
    public function store(StoreZabbixTaskRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Create task directly in tasks table instead of zabbix_tasks(this is not need now) table.
        $task = Task::where('task_subject', $data['task_name'])->where('assign_to', $data['assign_to'])->first();
        if (! $task) {
            $data['assign_from'] = Auth::id();
            $data['is_statutory'] = 0;
            $data['task_details'] = $data['task_name'];
            $data['task_subject'] = $data['task_name'];

            $task = Task::create($data);

            if ($data['assign_to']) {
                $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
            }
        }

        // Assign Zabbix Task Id to selected zabbix webhook datas
        $zabbixWebhookDatas = ZabbixWebhookData::whereIn('id', $data['zabbix_webhook_data_ids']);
        $zabbixWebhookDatas->update(['zabbix_task_id' => $task->id]);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Zabbix task has been created!',
            ]
        );
    }

    public function getAssigneeHistories($zabbix_task): JsonResponse
    {
        $assigneeHistories = ZabbixTaskAssigneeHistory::with(['user', 'newAssignee'])->where('zabbix_task_id', $zabbix_task)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $assigneeHistories,
            'message' => 'Assignee Histories get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
