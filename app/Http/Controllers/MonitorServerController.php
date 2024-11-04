<?php

namespace App\Http\Controllers;

use App\Models\MonitorLog;
use App\Models\MonitorServer;
use App\Models\MonitorServersUptime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitorServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->get('keyword');

        $monitorServers = MonitorServer::latest();

        if (! empty($keyword)) {
            $monitorServers = $monitorServers->where(function ($q) use ($keyword) {
                $q->orWhere('monitor_servers.ip', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.label', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.type', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.status', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.error', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.rtime', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.last_online', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.last_offline', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('monitor_servers.last_offline_duration', 'LIKE', '%'.$keyword.'%');
            });
        }

        $monitorServers = $monitorServers->paginate(25);

        return view('monitor-server.index', compact('monitorServers'));
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = 10; // Number of records per page

        $monitorServers = MonitorServer::latest();
        $monitorServers = $monitorServers->where('status', 'Off');
        $monitorServers = $monitorServers->paginate($perPage);

        return response()->json($monitorServers);
    }

    public function getServerUptimes(Request $request, $id): JsonResponse
    {
        $data = MonitorServersUptime::where('server_id', $id)->orderby('created_at', 'desc')->paginate(20);
        $paginateHtml = $data->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $data, 'message' => 'Server uptimes found']);
    }

    public function getServerUsers(Request $request, $id): JsonResponse
    {
        $monitorServer = MonitorServer::findOrFail($id);
        $serverUsers = $monitorServer->monitorUsers()->paginate(20);
        $paginateHtml = $serverUsers->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $serverUsers, 'message' => 'Server users found']);
    }

    public function getServerHistory(Request $request, $id): JsonResponse
    {
        $logHistory = MonitorLog::where('server_id', $id)->paginate(25);
        $paginateHtml = $logHistory->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $logHistory, 'message' => 'Server history found']);
    }

    public function logHistoryTruncate(): RedirectResponse
    {
        MonitorLog::truncate();
        MonitorServersUptime::truncate();

        return redirect()->route('monitor-server.index')->withSuccess('data Removed succesfully!');
    }
}
