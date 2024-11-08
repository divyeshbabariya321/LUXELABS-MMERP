<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\User;
use App\DeveloperTask;
use App\DeveloperModule;

class NewDevTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $statuses = [
            'Done'        => 'Done',
            'Planned'     => 'Planned',
            'In Progress' => 'In Progress',
            'Complete'    => 'Complete',
            'Documented'  => 'Documented',
            'Checked'     => 'Checked',
            'Implemented' => 'Implemented',
            'Paid'        => 'Paid',
        ];
        $modules = DeveloperModule::pluck('name', 'id')->toArray();
        $users   = User::pluck('name', 'id')->toArray();
        if (! empty($_GET['search_term'])) {
            $search_term = $_GET['search_term'];
            $dev_task    = DeveloperTask::where('task', 'like', '%' . $search_term . '%')->paginate(60);
        } elseif (! empty($_GET['module'])) {
            $module   = $_GET['module'];
            $dev_task = DeveloperTask::where('module_id', $module)->paginate(60);
        } elseif (! empty($_GET['user'])) {
            $user     = $_GET['user'];
            $dev_task = DeveloperTask::where('user_id', $user)->paginate(60);
        } elseif (! empty($_GET['status'])) {
            $status   = $_GET['status'];
            $dev_task = DeveloperTask::where('status', '=', $status)->paginate(60);
        } else {
            $dev_task = DeveloperTask::paginate(60);
        }

        return view('new_dev_task_planner.index', compact(
            'dev_task', 'modules', 'users', 'statuses'
        ));
    }
}
