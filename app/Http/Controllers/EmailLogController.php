<?php

namespace App\Http\Controllers;

use App\Email;
use App\EmailLog;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index()
    {
        $data = EmailLog::latest()
            ->where('source', EmailLog::EMAIL_ALERT)
            ->paginate(10);

        if (request()->ajax()) {
            return response()->view('email-log.partials.email-alert-logs', compact('data'));
        }

        return view('email-log.index', compact('data'));
    }

    public function list(Request $request): View
    {
        $modelNames = $this->getAllModelNames();

        return view('email-log.list', [
            'modules' => $modelNames,
        ]);
    }

    public function getAllModelNames()
    {
        $models = [];
        // Get all PHP files in the app/Models directory
        $files = File::files(app_path());
        foreach ($files as $file) {
            // Extract the file name without extension
            $modelName = pathinfo($file, PATHINFO_FILENAME);
            // Check if the file is a PHP class and not a directory
            if (is_file($file) && class_exists("App\\$modelName")) {
                $models[] = $modelName;
            }
        }

        return $models;
    }

    public function remove(Request $request): JsonResponse
    {
        $emptyLog = $request->isEmptyLog;

        // Check if $emptyLog is true, then remove all records from the Email table
        if ($emptyLog) {
            Email::truncate(); // This will remove all records from the Email table

            return response()->json(['message' => 'All email logs removed successfully']);
        }

        $deleteLogIds = $request->deleteLogId;

        // Check if any IDs were selected
        if (! empty($deleteLogIds)) {
            Email::destroy($deleteLogIds);

            return response()->json(['message' => 'Selected logs removed successfully']);
        } else {
            return response()->json(['message' => 'No logs selected for removal'], 400);
        }
    }

    public function ajaxList(Request $request)
    {
        if ($request->ajax()) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $module = $request->input('module');
            $globalSearch = $request->input('global_search');
            $columns = ['id', 'created_at', 'message', 'model_type', 'from', 'to', 'action'];
            $columnIndex = $request->input('order.0.column');
            $columnSortOrder = $request->input('order.0.dir');

            $data_category = Email::query();

            // Apply filters
            $data_category->where(function ($query) use ($globalSearch) {
                if (isset($globalSearch) && ! empty($globalSearch)) {
                    $query->where('model_type', 'LIKE', '%'.'App\\'.$globalSearch.'%')
                        ->orWhere('from', 'LIKE', '%'.$globalSearch.'%')
                        ->orWhere('to', 'LIKE', '%'.$globalSearch.'%')
                        ->orWhere('message', 'LIKE', '%'.$globalSearch.'%');
                }
            });

            if (isset($fromDate) && ! empty($fromDate) && isset($toDate) && ! empty($toDate)) {
                $data_category->whereBetween('created_at', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);
            } elseif (isset($fromDate) && ! empty($fromDate)) {
                $data_category->where('created_at', '>=', $fromDate.' 00:00:00');
            } elseif (isset($toDate) && ! empty($toDate)) {
                $data_category->where('created_at', '<=', $toDate.' 23:59:59');
            }

            if (isset($module) && ! empty($module)) {
                $modelName = 'App\\'.$module;
                $data_category->where('model_type', $modelName);
            }

            // Order by clause
            $data_category->orderBy('created_at', $columnSortOrder);
            $data = $data_category->get();

            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return '<input type="checkbox" class="select-checkbox" style="text-align: center;" data-id="'.$row->id.'"  value="'.$row->id.'">';
                })
                ->addColumn('action', function ($row) {
                    return '';
                })
                ->rawColumns(['action', 'id', 'message'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function truncateEmailAlertLogs(): JsonResponse
    {
        EmailLog::where('source', EmailLog::EMAIL_ALERT)->delete();

        return response()->json(['code' => 200, 'message' => 'Truncated email alert logs']);
    }
}
