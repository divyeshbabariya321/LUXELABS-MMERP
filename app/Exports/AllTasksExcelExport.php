<?php

namespace App\Exports;
use App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AllTasksExcelExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $developerTasks;

    protected $pendingTasks;

    public function __construct($pendingTasks, $developerTasks)
    {
        $this->developerTasks = $developerTasks;
        $this->pendingTasks   = $pendingTasks;
    }

    public function collection()
    {
        $pendingTasks = $this->pendingTasks
            ->leftJoin('categories', 'categories.id', '=', 'tasks.category')
            ->leftJoin('users', 'users.id', '=', 'tasks.assign_to')
            ->leftJoin('users as masters', 'masters.id', '=', 'tasks.master_user_id')
            ->leftJoin('users as second_masters', 'second_masters.id', '=', 'tasks.second_master_user_id')
            ->leftJoin('task_statuses', 'task_statuses.id', '=', 'tasks.status')
            ->select('tasks.id', 'tasks.created_at', 'categories.title', 'tasks.task_subject', 'users.name as username', 'masters.name as master_name', 'second_masters.name as second_master_name', 'task_statuses.name as task_name')
            ->get();

        $developerTasks = $this->developerTasks
            ->leftJoin('users', 'users.id', '=', 'developer_tasks.assigned_to')
            ->leftJoin('users as masters', 'masters.id', '=', 'developer_tasks.master_user_id')
            ->leftJoin('users as second_masters', 'second_masters.id', '=', 'developer_tasks.team_lead_id')
            ->leftJoin('task_statuses', 'task_statuses.id', '=', 'developer_tasks.status')
            ->select('developer_tasks.id', 'developer_tasks.created_at', 'developer_tasks.subject', 'users.name as username', 'masters.name as master_name', 'second_masters.name as second_master_name', 'task_statuses.name as task_name')
            ->get();

        $developerTasks = $developerTasks->map(function ($item, $key) {
            $arr['id']                 = $item['id'];
            $arr['created_at']         = $item['created_at'];
            $arr['title']              = '';
            $arr['subject']            = $item['subject'];
            $arr['username']           = $item['username'];
            $arr['master_name']        = $item['master_name'];
            $arr['second_master_name'] = $item['second_master_name'];
            $arr['task_name']          = $item['task_name'];

            return $arr;
        });

        $tasks = collect($pendingTasks)->concat(collect($developerTasks));

        return $tasks;
    }

    public function headings(): array
    {
        return [
            'Id',
            'Date',
            'Category',
            'Task',
            'Assigned To',
            'Lead',
            'Lead 2',
            'Status',
        ];
    }
}
