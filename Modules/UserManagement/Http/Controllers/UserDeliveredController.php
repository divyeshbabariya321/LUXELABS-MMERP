<?php

namespace Modules\UserManagement\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Task;
use App\User;
use App\TaskStatus;
use App\UserAvaibility;
use App\Hubstaff\HubstaffActivity;
use App\Http\Controllers\Controller;

class UserDeliveredController extends Controller
{
    public function index(): View
    {
        return view('usermanagement::user-delivered.index', [
            'title'       => 'User Delivered',
            'table'       => 'listUserDelivered',
            'urlLoadData' => route('user-management.user-delivered.load-data'),
            'listUsers'   => User::dropdown([
                'is_active' => 1,
            ]),
        ]);
    }

    public function loadData()
    {
        try {
            $count = 0;
            $data  = [];

            $isPrint = ! request()->ajax();

            $stDate = request('srchDateFrom');
            $enDate = request('srchDateTo');
            if ($stDate && $enDate) {
                $filterDates     = dateRangeArr($stDate, $enDate);
                $filterDatesOnly = array_column($filterDates, 'date');

                // if ($isPrint) {
                //     _p($filterDatesOnly);
                //     _p($filterDates);
                // }

                $q = UserAvaibility::from('user_avaibilities as ua');
                $q->leftJoin('users as u', 'ua.user_id', '=', 'u.id');
                if (! isAdmin()) {
                    $q->where('u.id', loginId());
                }
                if ($srch = request('srchUser')) {
                    $q->where('u.id', $srch);
                }
                $q->where(function ($query) use ($stDate, $enDate) {
                    $query->whereRaw(" ('" . $stDate . "' BETWEEN ua.from AND ua.to) ")
                        ->orWhereRaw(" ('" . $enDate . "' BETWEEN ua.from AND ua.to) ");
                    // $query->where('ua.from', '<=', $stDate)->orWhere('ua.to', '<=', $enDate);
                });
                // if (request('is_active')) {
                //     $q->where('users.is_active', request('is_active') == 1 ? 1 : 0);
                // }
                $q->select([
                    'u.id',
                    'u.name',
                    DB::raw('ua.date AS uaDays'),
                    DB::raw('ua.from AS uaFrom'),
                    DB::raw('ua.to AS uaTo'),
                    DB::raw('ua.start_time AS uaStTime'),
                    DB::raw('ua.end_time AS uaEnTime'),
                    DB::raw('ua.lunch_time AS uaLunchTime'),
                ]);
                $q->orderByDesc('ua.id');
                $userAvaibilities = $q->get();

                $count = $userAvaibilities->count();

                // if ($isPrint) {
                //     _p($userAvaibilities->toArray());
                // }

                if ($count) {
                    $userIds = [];

                    // Prepare user's data
                    $userArr = [];
                    foreach ($userAvaibilities as $single) {
                        $userIds[] = $single->id;

                        $single->uaStTime    = date('H:i:00', strtotime($single->uaStTime));
                        $single->uaEnTime    = date('H:i:00', strtotime($single->uaEnTime));
                        $single->uaLunchTime = date('H:i:00', strtotime($single->uaLunchTime));

                        $single->uaDays = UserAvaibility::getAvailableDays($single->uaDays);
                        $availableDates = UserAvaibility::getAvailableDates($single->uaFrom, $single->uaTo, $single->uaDays, $filterDatesOnly);
                        $availableSlots = UserAvaibility::dateWiseHourlySlots($availableDates, $single->uaStTime, $single->uaEnTime, $single->uaLunchTime);

                        foreach ($availableSlots as $date => $slots) {
                            if (! $slots || isset($userArr[$single->id]['dates'][$date])) {
                                continue;
                            }
                            $userArr[$single->id]['id']           = $single->id;
                            $userArr[$single->id]['name']         = $single->name;
                            $userArr[$single->id]['lunch']        = substr($single->uaLunchTime, 0, 5);
                            $userArr[$single->id]['dates'][$date] = [
                                'stTime' => $slots[0]['st'],
                                'enTime' => $slots[count($slots) - 1]['en'],
                            ];
                        }
                    }

                    // Get Tasks & Developer Tasks -- Arrange with End time & Mins
                    if ($userIds) {
                        $tasks = $this->getTaskList([
                            'userIds' => $userIds,
                            'stDate'  => $stDate,
                            'enDate'  => $enDate,
                        ]);

                        if ($tasks) {
                            foreach ($tasks as $task) {
                                $tempDate      = date('Y-m-d', strtotime($task->st_date));
                                $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date . ' + ' . $task->est_minutes . 'minutes'));

                                if (! isset($userArr[$task->assigned_to]['dates'][$tempDate])) {
                                    continue;
                                }

                                if (! isset($userArr[$task->assigned_to]['dates'][$tempDate]['planned_tasks'])) {
                                    $userArr[$task->assigned_to]['dates'][$tempDate]['planned_tasks'] = [];
                                }
                                $userArr[$task->assigned_to]['dates'][$tempDate]['planned_tasks'][] = [
                                    'id'      => $task->id,
                                    'typeId'  => $task->type . '-' . $task->id,
                                    'subject' => $task->title,
                                    'stDate'  => $task->st_date,
                                    'enDate'  => $task->en_date,
                                    'status'  => $task->status,
                                    'status2' => TaskStatus::printName($task->status),
                                    'mins'    => $task->est_minutes,
                                    'stTime'  => date('H:i', strtotime($task->st_date)),
                                    'enTime'  => date('H:i', strtotime($task->en_date)),
                                ];
                            }
                        }
                    }

                    // Get HubStaff Data
                    if ($userIds) {
                        $hubstaffData = $this->getHubstaffData([
                            'userIds' => $userIds,
                            'stDate'  => $stDate,
                            'enDate'  => $enDate,
                        ]);
                        if ($isPrint) {
                            _p($hubstaffData);
                        }
                        if ($hubstaffData) {
                            foreach ($hubstaffData as $userId => $user) {
                                if (isset($user['dates']) && $user['dates']) {
                                    foreach ($user['dates'] as $date => $rows) {
                                        if (! isset($userArr[$userId]['dates'][$date])) {
                                            continue;
                                        }
                                        if (! isset($userArr[$userId]['dates'][$date]['actual_tasks'])) {
                                            $userArr[$userId]['dates'][$date]['actual_tasks'] = [];
                                        }
                                        $userArr[$userId]['dates'][$date]['actual_tasks'] = array_merge_recursive($userArr[$userId]['dates'][$date]['actual_tasks'], $rows);
                                    }
                                }
                            }
                        }
                    }

                    if ($isPrint) {
                        _p($userArr);
                    }

                    // Arange for datatable
                    foreach ($userArr as $user) {
                        foreach ($user['dates'] as $date => $dateRow) {
                            $planned_tasks = $dateRow['planned_tasks'] ?? [];
                            if ($planned_tasks) {
                                $temp = [];
                                foreach ($planned_tasks as $task) {
                                    $temp[] = '<div class="div-slot" title="' . $task['subject'] . ' (' . $task['status2'] . ')" >' . $task['typeId'] . ' (' . $task['stTime'] . ' - ' . $task['enTime'] . ')' . '</div>';
                                }
                                $planned_tasks = $temp;
                            }

                            $actualTasks = [];
                            $temp        = $dateRow['actual_tasks'] ?? [];
                            if ($temp) {
                                $trackedTotal       = 0;
                                $trackedWithTask    = 0;
                                $trackedWithoutTask = 0;
                                $taskHours          = [];

                                foreach ($temp as $row) {
                                    $trackedTotal += $row['hub_tracked'];
                                    if ($row['hub_task_id']) {
                                        $trackedWithTask += $row['hub_tracked'];
                                        if ($row['task_type']) {
                                            $taskHours[] = '<div class="div-slot" title="' . $row['task_title'] . ' ' . ($row['task_status2'] ? '(' . $row['task_status2'] . ')' : '') . '" >' .
                                                $row['task_type'] . ': ' . printNum($row['hub_tracked'] / 60) .
                                                '</div>';
                                        }
                                    } else {
                                        $trackedWithoutTask += $row['hub_tracked'];
                                    }
                                }
                                $actualTasks[] = implode(' / ', [
                                    printNum($trackedTotal / 60),
                                    printNum($trackedWithTask / 60),
                                    printNum($trackedWithoutTask / 60),
                                ]);
                                if ($taskHours) {
                                    $actualTasks[] = '<br />';
                                    $actualTasks[] = implode('', $taskHours);
                                }
                            }

                            $data[] = [
                                'name'         => $user['name'],
                                'date'         => $date,
                                'availability' => date('H:i', strtotime($dateRow['stTime'])) . ' - ' . date('H:i', strtotime($dateRow['enTime'])),
                                'lunch'        => ($user['lunch'] ?: '-'),
                                'planned'      => $planned_tasks ? implode('', $planned_tasks) : '-',
                                'actual'       => $actualTasks ? implode('', $actualTasks) : '-',
                            ];
                        }
                    }
                }

                return respJson(200, '', [
                    'draw'            => request('draw'),
                    'recordsTotal'    => $count,
                    'recordsFiltered' => $count,
                    'data'            => $data,
                ]);
            } else {
                return respJson(400, 'From and To Date is required.');
            }
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function getTaskList($wh = [])
    {
        $userIds = $wh['userIds'] ?? [0];
        $stDate  = $wh['stDate'] ?? null;
        $enDate  = $wh['enDate'] ?? null;

        $stDate = $stDate . ' 00:00:00';
        $enDate = $enDate . ' 23:59:59';

        $sql = "SELECT
            listdata.*
            FROM (
            (
                SELECT 
                    id, 
                    'T' AS type, 
                    assign_to AS assigned_to, 
                    task_subject AS title, 
                    start_date AS st_date, 
                    COALESCE(approximate, 0) AS est_minutes,
                    status
                FROM 
                    tasks 
                WHERE 
                1
                AND start_date IS NOT NULL
                AND start_date BETWEEN ? AND ?
                AND deleted_at IS NULL
                AND assign_to IN (" . implode(',', $userIds) . ") 
            )
            UNION
            (
                SELECT 
                    id, 
                    'DT' AS type, 
                    assigned_to AS assigned_to, 
                    subject AS title, 
                    start_date AS st_date, 
                    COALESCE(estimate_minutes, 0) AS est_minutes,
                    status
                FROM developer_tasks
                WHERE 1
                AND start_date IS NOT NULL
                AND start_date BETWEEN ? AND ?
                AND deleted_at IS NULL
                AND assigned_to IN (" . implode(',', $userIds) . ')
            )
        ) AS listdata
        ORDER BY listdata.st_date ASC';

        $tasks = DB::select($sql, [
            $stDate,
            $enDate,
            $stDate,
            $enDate,
        ]);

        return $tasks;
    }

    public function getHubstaffData($wh = [])
    {
        $userIds = $wh['userIds'] ?? [0];
        $stDate  = $wh['stDate'] ?? null;
        $enDate  = $wh['enDate'] ?? null;
        $stDate  = $stDate . ' 00:00:00';
        $enDate  = $enDate . ' 23:59:59';

        $query = HubstaffActivity::query();
        $query->leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id');
        $query->leftJoin('users', 'users.id', '=', 'hubstaff_members.user_id');
        $query->leftJoin('tasks', function ($join) {
            $join->on('tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')->where('hubstaff_activities.task_id', '>', 0);
        });
        $query->leftJoin('developer_tasks', function ($join) {
            $join->on('developer_tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')->where('hubstaff_activities.task_id', '>', 0);
        });
        // $query->leftJoin(
        //     DB::raw("(SELECT date, user_id, MAX(created_at) AS created_at FROM hubstaff_activity_summaries GROUP BY date, user_id) hub_summary"),
        //     function ($join) {
        //         $join->on('hub_summary.date', '=', DB::raw("DATE(hubstaff_activities.starts_at)"));
        //         $join->on('hub_summary.user_id', '=', 'hubstaff_members.user_id');
        //     }
        // );
        // $query->leftJoin('hubstaff_activity_summaries', function ($join) {
        //     $join->on('hubstaff_activity_summaries.date', '=', 'hub_summary.date');
        //     $join->on('hubstaff_activity_summaries.user_id', '=', 'hub_summary.user_id');
        //     $join->on('hubstaff_activity_summaries.created_at', '=', 'hub_summary.created_at');
        // });
        $query->where('hubstaff_activities.starts_at', '>=', $stDate);
        $query->where('hubstaff_activities.starts_at', '<=', $enDate);
        if ($userIds) {
            $query = $query->whereIn('hubstaff_members.user_id', $userIds);
        }

        // if (Auth::user()->isAdmin()) {
        //     $users = User::orderBy('name')->pluck('name', 'id')->toArray();
        // } else {
        //     $members = Team::join('team_user', 'team_user.team_id', 'teams.id')->where('teams.user_id', Auth::user()->id)->distinct()->pluck('team_user.user_id');
        //     if (!count($members)) {
        //         $members = [Auth::user()->id];
        //     } else {
        //         $members[] = Auth::user()->id;
        //     }
        //     $query = $query->whereIn('hubstaff_members.user_id', $members);
        //     $users = User::whereIn('id', [Auth::user()->id])->pluck('name', 'id')->toArray();
        // }

        $query->groupBy(
            DB::raw('DATE(hubstaff_activities.starts_at)'),
            'hubstaff_activities.user_id',
            'hubstaff_activities.task_id'
        );
        $query->orderByDesc('hubstaff_activities.starts_at');
        // $query->groupBy(DB::raw("DATE(hubstaff_activities.starts_at)"), "hubstaff_activities.user_id");

        $query->select([
            DB::raw('COALESCE(hubstaff_members.user_id, 0) AS userId'),
            'users.name as userName',

            DB::raw('DATE(hubstaff_activities.starts_at) AS hub_date'),
            DB::raw('COALESCE(hubstaff_activities.user_id, 0) AS hub_user_id'),
            DB::raw('COALESCE(hubstaff_activities.task_id, 0) AS hub_task_id'),
            DB::raw('SUM(COALESCE(hubstaff_activities.tracked, 0)) AS hub_tracked'),
            DB::raw('SUM(COALESCE(hubstaff_activities.overall, 0)) AS hub_overall'),

            DB::raw('COALESCE(tasks.id, 0) AS task_table_id'),
            DB::raw("CONCAT('T-', COALESCE(tasks.id, 0)) AS task_type_id"),
            DB::raw("COALESCE(tasks.task_subject, '') AS task_title"),
            DB::raw("COALESCE(tasks.status, '') AS task_status"),
            DB::raw('COALESCE(developer_tasks.id, 0) AS developer_task_table_id'),
            DB::raw("CONCAT('DT-', COALESCE(developer_tasks.id, 0)) AS developer_task_type_id"),
            DB::raw("COALESCE(developer_tasks.subject, '') AS developer_task_title"),
            DB::raw("COALESCE(developer_tasks.status, '') AS developer_task_status"),
            // DB::raw("COALESCE(hubstaff_activity_summaries.accepted, 0) AS approved_hours"),
            // DB::raw("(SUM(COALESCE(hubstaff_activities.tracked, 0)) - COALESCE(hubstaff_activity_summaries.accepted, 0)) AS difference_hours")
        ]);

        $list = $query->get();
        // _p($list->toArray());

        $data = [];
        foreach ($list as $activity) {
            $data[$activity->userId]['id']                           = $activity->userId;
            $data[$activity->userId]['name']                         = $activity->userName;
            $data[$activity->userId]['hub_user_id']                  = $activity->hub_user_id;
            $data[$activity->userId]['dates'][$activity->hub_date][] = [
                'hub_task_id' => $activity->hub_task_id,
                'hub_tracked' => $activity->hub_tracked,
                'hub_overall' => $activity->hub_overall,

                'task_type'    => ($activity->task_table_id ? $activity->task_type_id : ($activity->developer_task_table_id ? $activity->developer_task_type_id : '')) ?: 'HB-' . $activity->hub_task_id,
                'task_title'   => ($activity->task_table_id ? $activity->task_title : ($activity->developer_task_table_id ? $activity->developer_task_title : '')) ?: 'Hubstaff Task ID: ' . $activity->hub_task_id,
                'task_status2' => $activity->task_table_id ? TaskStatus::printName($activity->task_status) : ($activity->developer_task_table_id ? TaskStatus::printName($activity->developer_task_status) : ''),

                // 'task_table_id' => $activity->task_table_id,
                // 'task_title' => $activity->task_title,
                // 'task_status' => $activity->task_status,
                // 'developer_task_table_id' => $activity->developer_task_table_id,
                // 'developer_task_title' => $activity->developer_task_title,
                // 'developer_task_status' => $activity->developer_task_status,
            ];
        }

        return $data;
    }
}
