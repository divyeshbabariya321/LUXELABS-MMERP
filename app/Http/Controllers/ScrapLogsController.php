<?php

namespace App\Http\Controllers;

use App\DatabaseLog;
use App\LogMessageStatus;
use App\Models\ScrapperLogStatus;
use App\Scraper;
use App\ScrapLog;
use App\ScrapRemark;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\DataTables;

class ScrapLogsController extends Controller
{
    public function index(Request $Request): View
    {
        $name = '';

        $servers = Scraper::select('server_id')->whereNotNull('server_id')->groupBy('server_id')->get();

        return view('scrap-logs.index', compact('name', 'servers'));
    }

    public function getScrapLogsStatus(Request $request)
    {
        $scrapLogsStatus = LogMessageStatus::query()->orderByDesc('id');

        return DataTables::of($scrapLogsStatus)
            ->addColumn('action', function ($scrapLogStatus) {
                if (! empty($scrapLogStatus->status)) {
                    return $scrapLogStatus->status;
                } else {
                    return '<select name="status" id="log_status_'.$scrapLogStatus->id.'" onchange="saveStatus('.$scrapLogStatus->id.')">
                            <option value="">Select Status</option>
                            <option value="success">Success</option>
                            <option value="error">Error</option>
                        </select>';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function updateLogStatus(Request $request)
    {
        $input = $request->input();
        LogMessageStatus::where('id', $input['id'])->update(['status' => $input['log_status']]);

        return 'success';
    }

    public function filter($searchVal, $dateVal, Request $request): JsonResponse
    {
        if (! empty($request->get('month'))) {
            $month = $request->get('month');
        } else {
            $month = Carbon::now()->format('M');
        }

        if (! empty($request->get('year'))) {
            $month .= $request->get('year');
        } else {
            $month .= Carbon::now()->format('y');
        }

        $serverArray = [];

        $servers = Scraper::select('server_id')->whereNotNull('server_id')->groupBy('server_id')->get();

        if ($request->server_id !== null) {
            $servers = Scraper::select('server_id')->where('server_id', $request->server_id)->groupBy('server_id')->get();
        }
        foreach ($servers as $server) {
            $serverArray[] = $server['server_id'];
        }

        $searchVal = $searchVal != 'null' ? $searchVal : '';
        $dateVal = $dateVal != 'null' ? $dateVal : '';
        $file_list = [];

        $files = File::allFiles(config('env.SCRAP_LOGS_FOLDER'));

        $date = strlen($dateVal) == 1 ? "0$dateVal" : $dateVal;

        $lines = [];

        foreach ($files as $val) {
            $day_of_file = explode('-', $val->getFilename());
            $day_of_file = str_replace('.log', '', $day_of_file);

            if (((end($day_of_file) == $date) || (isset($day_of_file[1]) and strtolower($day_of_file[1]) == strtolower($date.$month))) && (Str::contains($val->getFilename(), $searchVal) || empty($searchVal))) {

                if (! in_array($val->getRelativepath(), $serverArray)) {
                    continue;
                }

                $file_path_new = config('settings.scrap_logs_folder').'/'.$val->getRelativepath().'/'.$val->getFilename();

                $file = file($file_path_new);

                $log_msg = '';
                for ($i = max(0, count($file) - 3); $i < count($file); $i++) {
                    $log_msg .= $file[$i];
                }

                $file_path_info = pathinfo($val->getFilename());
                $file_name_str = $file_path_info['filename'];
                $file_name_ss = $val->getFilename();

                $lines[] = "=============== $file_name_ss log started from here ===============";

                for ($i = max(0, count($file) - 10); $i < count($file); $i++) {
                    $lines[] = $file[$i];
                }

                $lines[] = "=============== $file_name_ss log ended from here ===============";

                if ($log_msg == '') {
                    $log_msg = 'Log data not found.';
                }
                $logStatus = LogMessageStatus::firstOrCreate(['log_message' => $log_msg], ['log_message' => $log_msg]);

                array_push($file_list, [
                    'filename' => $file_name_ss,
                    'foldername' => $val->getRelativepath(),
                    'log_msg' => $log_msg,
                    'status' => $logStatus['status'],
                    'scraper_id' => $file_name_str,
                ]
                );
            }
        }

        //config
        if (strtolower(request('download')) == 'yes') {
            $nameF = 'scraper-log-temp-file.txt';
            $namefile = storage_path().'/logs/'.$nameF;
            $content = implode("\n", $lines);

            //save file
            $file = fopen($namefile, 'w') || exit('Unable to open file!');
            fwrite($file, $content);
            fclose($file);

            //header download
            header('Content-Disposition: attachment; filename="'.$nameF.'"');
            header('Content-Type: application/force-download');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Type: text/plain');

            echo $content;
            exit;
        }

        return response()->json(['file_list' => $file_list]);
    }

    public function filtertosavelogdb()
    {
        $searchVal = '';
        $dateVal = '';
        $files = File::allFiles(config('env.SCRAP_LOGS_FOLDER'));

        $date = empty($dateVal) ? Carbon::now()->format('d') : sprintf('%02d', $dateVal);
        if ($date == 01) {
            $date = 32;
        }

        foreach ($files as $val) {
            $day_of_file = explode('-', $val->getFilename());
            if (Str::contains(end($day_of_file), sprintf('%02d', $date - 1)) && (Str::contains($val->getFilename(), $searchVal) || empty($searchVal))) {
                $file_path_new = config('env.SCRAP_LOGS_FOLDER').'/'.$val->getRelativepath().'/'.$val->getFilename();

                $file = file($file_path_new);
                $log_msg = '';
                for ($i = max(0, count($file) - 3); $i < count($file); $i++) {
                    $log_msg .= $file[$i];
                }
                if ($log_msg == '') {
                    $log_msg = 'Log data not found.';
                }
                $file_path_info = pathinfo($val->getFilename());

                $search_scraper = substr($file_path_info['filename'], 0, -3);
                $search_scraper = str_replace('-', '_', $search_scraper);
                $scrapers_info = Scraper::select('id')
                    ->where('scraper_name', 'like', $search_scraper)
                    ->get();

                if (count($scrapers_info) > 0) {
                    $scrap_logs_info = ScrapLogs::select('id', 'scraper_id')
                        ->where('scraper_id', '=', $scrapers_info[0]->id)
                        ->get();
                    $scrapers_id = $scrapers_info[0]->id;
                } else {
                    $scrapers_id = 0;
                }

                if (count($scrap_logs_info) == 0) {
                    $file_list_data = [
                        'scraper_id' => $scrapers_id,
                        'folder_name' => $val->getRelativepath(),
                        'file_name' => $val->getFilename(),
                        'log_messages' => $log_msg,
                        'created_date' => date('Y-m-d H:i:s'),
                        'updated_date' => date('Y-m-d H:i:s'),
                    ];
                    ScrapLogs::insert($file_list_data);
                }
            }
        }
    }

    public function logdata()
    {
        return ScrapRemark::select('scraper_name', 'remark', DB::raw('count(*) as log_count'), DB::raw("group_concat(scraper_name SEPARATOR ' ') as scraper_name"))
            ->where('scrap_field', 'last_line_error')
            ->whereDate('created_at', date('Y-m-d'))
            ->groupBy('remark')
            ->get();
    }

    public function loghistory($filename)
    {
        $day_of_file = explode('-', $filename);
        $day_of_file = str_replace('.log', '', $day_of_file);

        $fileLogs = [];

        $scraper = Scraper::where('scraper_name', $day_of_file[0])->first();
        if ($scraper) {
            $toDate = date('Y-m-d', strtotime('+1 day'));
            $fromDate = date('Y-m-d', strtotime('-7 days'));
            $fileLogs = ScrapLog::where('scraper_id', $scraper->id)->whereBetween('created_at', [$fromDate, $toDate])->get();
        }

        return $fileLogs;
    }

    public function fetchlog(): JsonResponse
    {
        $file_list = [];
        $scrap_logs_info = ScrapLogs::select('*')
            ->get();
        foreach ($scrap_logs_info as $row_log) {
            array_push($file_list, [
                'filename' => $row_log->file_name,
                'foldername' => $row_log->folder_name,
                'log_msg' => $row_log->log_messages,
                'scraper_id' => $row_log->scraper_id,
            ]
            );
        }

        return response()->json(['file_list' => $file_list]);
    }

    public function history(Request $request)
    {
        $day_of_file = explode('-', $request->filename);
        $day_of_file = str_replace('.log', '', $day_of_file);

        $cdate = Carbon::now()->subDays(7);

        $last7days = ScrapRemark::where('scraper_name', 'like', $day_of_file[0])->where('created_at', '>=', $cdate)->get();

        return $last7days;
    }

    public function fileView($filename, $foldername): BinaryFileResponse
    {
        $path = config('env.SCRAP_LOGS_FOLDER').'/'.$foldername.'/'.$filename;

        return response()->file($path);
    }

    public function indexByName($name): View
    {
        $name = strtolower(str_replace(' ', '', $name));

        return view('scrap-logs.index', compact('name'));
    }

    public function databaseLog(Request $request): View
    {
        $search = '';
        $databaseLogs = DatabaseLog::orderByDesc('created_at');
        $logBtn = SlowLogsEnableDisable::orderByDesc('id')->first();
        if ($request->search) {
            $databaseLogs = $databaseLogs->where('log_message', 'Like', '%'.$search.'%')->paginate(25);
        } else {
            $databaseLogs = $databaseLogs->paginate(25);
        }

        return view('scrap-logs.database-log', compact('databaseLogs', 'search', 'logBtn'));
    }

    public function enableMysqlAccess(Request $request): RedirectResponse
    {
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql-slowlogs.sh -f enable';
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        if ($result == '') {
            $result = 'Not any response';
        } else {
            $result = is_array($result) ? json_encode($result, true) : $result;
        }
        SlowLogsEnableDisable::create([
            'user_id' => Auth::user()->id ?? '',
            'response' => $result,
            'type' => 'Enable',
        ]);

        return redirect()->back()->with('success', 'Slow Logs enable successfully.');
    }

    public function disableMysqlAccess(Request $request): RedirectResponse
    {
        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'mysql-slowlogs.sh -f disable';
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        if ($result == '') {
            $result = 'Not any response';
        } else {
            $result = is_array($result) ? json_encode($result, true) : $result;
        }
        SlowLogsEnableDisable::create([
            'user_id' => Auth::user()->id ?? '',
            'response' => $result,
            'type' => 'Disable',
        ]);

        return redirect()->back()->with('success', 'Slow Logs disable successfully.');
    }

    public function disableEnableHistory(Request $request): JsonResponse
    {
        try {
            $data = SlowLogsEnableDisable::select('slow_logs_enable_disables.*', 'users.name AS userName')
                ->leftJoin('users', 'slow_logs_enable_disables.user_id', 'users.id')
                ->orderByDesc('slow_logs_enable_disables.id')
                ->get();

            return response()->json(['code' => 200, 'data' => $data, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function databaseTruncate(Request $request): JsonResponse
    {
        try {
            $data = DatabaseLog::query()->truncate();

            return response()->json(['code' => 200, 'data' => $data, 'message' => 'Datalog Truncated successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $data = [
            'text' => strtolower($request->errortext),
            'status' => strtolower($request->errorstatus),
        ];

        ScrapperLogStatus::insert($data);

        return redirect()->back()->with('success', 'New status created successfully.');
    }
}
