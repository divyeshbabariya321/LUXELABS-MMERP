<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Host;
use Exception;
use App\Problem;
use Carbon\Carbon;
use App\ZabbixHistory;
use App\Zabbix\ZabbixApi;
use Illuminate\Http\Request;
use App\Models\Zabbix\Trigger;
use App\Models\Zabbix\Host as HostZabbix;

class ZabbixController extends Controller
{
    public function __construct(
        private Trigger $trigger,
        private ZabbixApi $zabbix
    ) {
    }

    public function index(Request $request)
    {
        $templates = $this->trigger->getAllTemplates();
        if ($request->ajax()) {
            $query = Host::with('items');

            return datatables()->eloquent($query)->toJson();
        }

        return view('zabbix.index', compact('templates'));
    }

    public function detail(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');

            if ($id === null) {
                throw new Exception('ID is required param.');
            }

            $host = Host::find($id);

            if ($host === null) {
                throw new Exception('Host not found.');
            }

            $hostZabbix = new HostZabbix();

            $hostInterfaceZabbix         = $hostZabbix->getById($host->hostid);
            $hostZbx                     = $this->zabbix->call('host.get', ['hostids' => $host->hostid]);
            $hostInterfaceZabbix['name'] = $hostZbx[0]['host'];
            $hostInterfaceZabbix['id']   = $host->id;

            return response()->json(['code' => 200, 'data' => $hostInterfaceZabbix]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $host = Host::find($data['id']);

            if ($host === null) {
                throw new Exception('Host not found.');
            }

            $this->zabbix->call('host.delete', [$host->hostid]);

            $host->delete();

            return response()->json(['code' => 200, 'message' => 'Deleted successful.']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function save(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $isCreate = false;

            $host = Host::find($data['id']);

            if ($host === null) {
                $isCreate = true;
            }

            $hostZabbix = new HostZabbix();

            if ($isCreate) {
                $hostArray['host']       = $data['name'];
                $hostArray['interfaces'] = [
                    'type'  => 1,
                    'main'  => 1,
                    'useip' => 1,
                    'ip'    => $data['ip'],
                    'dns'   => '',
                    'port'  => $data['port'],
                ];

                if (! empty($data['template_ids'])) {
                    foreach ($data['template_ids'] as $id) {
                        $hostArray['templates'][]['templateid'] = $id;
                    }
                }

                $hostArray['groups'] = [
                    'groupid' => 1,
                ];

                $hostZabbix->save($hostArray);
            } else {
                $hostZabbixArray = $hostZabbix->getById($host->hostid);

                $hostArray['host']   = $data['name'];
                $hostArray['hostid'] = $hostZabbixArray['hostid'];
                $hostArray['groups'] = [
                    'groupid' => 1,
                ];

                $hostArray['interfaces'] = [
                    'main'        => 1,
                    'ip'          => $data['ip'],
                    'port'        => $data['port'],
                    'interfaceid' => $hostZabbixArray['interfaceid'],
                ];

                if (! empty($data['template_ids'])) {
                    $hostArray['templates_clear'] = [];
                    foreach ($data['template_ids'] as $id) {
                        $hostArray['templates'][]['templateid'] = $id;
                    }
                }

                $hostZabbix->save($hostArray, 'update');
            }

            return response()->json(['code' => 200, 'message' => 'Successful saved.']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function problems(Request $request): View
    {
        $search_data = Problem::orderBy('id')->get();

        $problems = Problem::orderBy('id');

        if (! empty($request->host_name)) {
            $problems->where('hostname', $request->host_name);
        }
        if (! empty($request->problem)) {
            $problems->where('name', $request->problem);
        }
        if (! empty($request->event_id)) {
            $problems->where('eventid', $request->event_id);
        }
        if (! empty($request->object_id)) {
            $problems->where('objectid', $request->object_id);
        }
        $problems = $problems->paginate(25)->appends(request()->except(['page']));

        $totalentries = count($problems);

        return view('zabbix.problem', compact('problems', 'totalentries', 'search_data'));
    }

    public function history(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $dueDate = Carbon::now()->subDays(2);
            $query   = ZabbixHistory::whereDate('created_at', '>', $dueDate)->where('host_id', $request->hostid)->orderByDesc('created_at')->get();
            foreach ($query as $val) {
                $host            = Host::where('hostid', $val->host_id)->first();
                $val['hostname'] = $host->name;
            }

            return response()->json(['status' => 200, 'data' => $query]);
        }
    }
}
