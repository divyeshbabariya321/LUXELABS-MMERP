<?php

namespace App\Http\Controllers;

use App\Http\Requests\WhitelistIPIpLogRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\IpLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IpLogController extends Controller
{
    public function getIPLogs(Request $request): View
    {
        $logs = new IpLog();

        if ($request->search_ip) {
            $logs = $logs->where('ip', 'LIKE', '%' . $request->search_ip . '%');
        }
        if ($request->search_message) {
            $logs = $logs->where('message', 'LIKE', '%' . $request->search_message . '%');
        }
        if ($request->email_ids) {
            $logs = $logs->WhereIn('email', $request->email_ids);
        }
        if ($request->status) {
            $logs = $logs->where('status', 'LIKE', '%' . $request->status . '%');
        }
        if ($request->date) {
            $logs = $logs->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        $logs = $logs->latest()->paginate(25);

        return view('IpLogs.ip-log-list', compact('logs'));
    }

    public function whitelistIP(WhitelistIPIpLogRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $serverName = $validatedData['server_name'];
        $ipAddress  = $validatedData['ip_address'];
        $comment    = $validatedData['comment'];

        $command = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'webaccess-firewall.sh ' . '-s "' . $serverName . '" -i "' . $ipAddress . '" -c "' . $comment . '"';

        $allOutput   = [];
        $allOutput[] = $command;
        $result      = exec($command, $allOutput);

        Log::info('Command result: ' . $result);

        if ($result == '') {
            $errorMessage = 'No response';
            Log::error($errorMessage);
            $result = $errorMessage . 'Command run Fail Response';
        } elseif ($result == 0) {
            $result = 'Command run success Response ' . $result;
        } elseif ($result == 1) {
            $errorMessage = 'Command run Fail Response ' . $result;
            Log::error($errorMessage);
            $result = $errorMessage;
        } else {
            $result = is_array($result) ? json_encode($result, true) : $result;
        }

        Log::info(print_r($result, true));

        return response()->json(['message' => $result, 'code' => 200]);
    }
}
