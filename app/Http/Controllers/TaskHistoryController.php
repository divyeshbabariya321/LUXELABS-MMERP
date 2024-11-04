<?php

namespace App\Http\Controllers;

use App\Models\Tasks\TaskDueDateHistoryLog;
use App\Models\Tasks\TaskDueDateHistoryLogApprovals;
use App\Models\Tasks\TaskHistoryForCost;
use App\Models\Tasks\TaskHistoryForStartDate;
use App\Models\Tasks\TaskHistoryForStartDateApprovals;

class TaskHistoryController extends Controller
{
    public function historyStartDate()
    {
        $list = TaskHistoryForStartDate::with('updatedBy')->where('task_id', request('id'))->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">#</th>
                <th width="5%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td><input type="radio" name="radio_for_approve" value="'.$single->id.'" '.($single->approved ? 'checked' : '').' style="height:auto;" /></td>
                    <td>'.$single->id.'</td>
                    <td>'.($single->updatedBy ? $single->updatedBy->name : '-').'</td>
                    <td>'.$single->old_value.'</td>
                    <td>'.$single->new_value.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="6">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function historyDueDate()
    {
        $list = TaskDueDateHistoryLog::with(['users'])->where('task_id', request('id'))->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">#</th>
                <th width="5%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td><input type="radio" name="radio_for_approve" value="'.$single->id.'" '.($single->approved ? 'checked' : '').' style="height:auto;" /></td>
                    <td>'.$single->id.'</td>
                    <td>'.($single->users ? $single->users->name : '-').'</td>
                    <td>'.$single->old_due_date.'</td>
                    <td>'.$single->new_due_date.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="6">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function historyCost()
    {
        $list = TaskHistoryForCost::with('updatedBy')->where('task_id', request('id'))->orderByDesc('id')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>'.$single->id.'</td>
                    <td>'.($single->updatedBy ? $single->updatedBy->name : '-').'</td>
                    <td>'.$single->old_value.'</td>
                    <td>'.$single->new_value.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function approve()
    {
        $id = request('radio_for_approve');
        $type = request('type');
        if ($type == 'start_date') {
            TaskHistoryForStartDate::approved($id);
        } elseif ($type == 'due_date') {
            TaskDueDateHistoryLog::approved($id);
        }

        return respJson(200, 'Approved successfully.');
    }

    public function approveHistory()
    {
        $type = request('type');
        $taskId = request('id');
        if ($type == 'start_date') {
            $q = TaskHistoryForStartDateApprovals::from('task_history_for_start_date_approvals as t1');
            $q->with(['approvedBy']);
            $q->leftJoin('task_history_for_start_date as t2', function ($join) {
                $join->on('t1.parent_id', '=', 't2.id');
            });
            $q->where('t2.task_id', $taskId);
            $q->select([
                't1.*',
                't2.new_value AS value',
            ]);
            $q->orderByDesc('id');
            $list = $q->get();
        } elseif ($type == 'due_date') {
            $q = TaskDueDateHistoryLogApprovals::from('task_due_date_history_logs_approvals as t1');
            $q->with(['approvedBy']);
            $q->leftJoin('task_due_date_history_logs as t2', function ($join) {
                $join->on('t1.parent_id', '=', 't2.id');
            });
            $q->where('t2.task_id', $taskId);
            $q->select([
                't1.*',
                't2.new_due_date AS value',
            ]);
            $q->orderByDesc('id');
            $list = $q->get();
        }

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="15%">Parent ID</th>
                <th width="30%">Update By</th>
                <th width="30%" style="word-break: break-all;">Approved Value</th>
                <th width="25%">Created at</th>
            </tr>
        </thead>';
        if (isset($list) && $list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>'.$single->parent_id.'</td>
                    <td>'.($single->approvedByName() ?: '-').'</td>
                    <td>'.$single->value.'</td>
                    <td>'.$single->created_at.'</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="4">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';

        return respJson(200, '', ['data' => implode('', $html)]);
    }
}
