<?php

namespace App\Http\Controllers;

use App\Exports\MagentoProductCommonError;
use App\MagentoLogHistory;
use App\ProductPushErrorLog;
use App\PushToMagentoCondition;
use App\StoreWebsite;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class MagentoProductPushErrors extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'List | Magento Log Errors';

        $websites = StoreWebsite::get();

        return view('magento-product-error.index', compact('title', 'websites'));
    }

    public function records(Request $request): JsonResponse
    {
        $keyword = $request->get('keyword');

        if ($request->website !== '' && $request->website !== 'all') {
            $records = ProductPushErrorLog::whereHas('store_website', function ($q) use ($request) {
                $q->where('id', $request->website);
            });
        } else {
            $records = ProductPushErrorLog::with('store_website');
        }

        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('message', 'LIKE', "%$keyword%");
            });
        }
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('message', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($request->response_status)) {
            $response_status = $request->response_status;
            $records = $records->where(function ($q) use ($response_status) {
                $q->where('response_status', 'LIKE', $response_status);
            });
        }

        if (! empty($request->log_date)) {
            $log_date = date('Y-m-d', strtotime($request->log_date));
            $records = $records->whereBetween('created_at', [$log_date.' 00:00:00', $log_date.' 23:59:59']);
        }

        $records = $records->latest()->paginate(50);

        $recorsArray = [];
        $conditions = PushToMagentoCondition::pluck('condition', 'id')->toArray();

        foreach ($records as $row) {
            $condition = '';
            if ($row->condition_id != null && isset($conditions[$row->condition_id])) {
                $condition = $conditions[$row->condition_id];
            }
            $recorsArray[] = [
                'product_id' => '<a class="show-product-information" data-id="'.$row->product_id.'" href="/products/'.$row->product_id.'" target="__blank">'.$row->product_id.'</a>',
                'updated_at' => $row->created_at->format('d-m-y H:i:s'),
                'store_website' => ($row->store_website) ? $row->store_website->title : '-',
                'message' => Str::limit(strip_tags($row->message), 30,
                    '<a data-logid='.$row->id.' class="message_load">...</a>'),
                'request_data' => Str::limit($row->request_data, 30,
                    '<a data-logid='.$row->id.' class="request_data_load">...</a>'),
                'condition_id' => $condition,
                'response_data' => Str::limit($row->response_data, 30,
                    '<a data-logid='.$row->id.' class="response_data_load">...</a>'),
                'response_status' => ' <div style="display:flex;"><select class="form-control globalSelect2" name="error_status" id="error_status" data-log_id="'.$row->id.'">
                <option value="" ></option>
                <option value="success" '.($row->response_status == 'success' ? 'selected' : '').'>Success</option>
                <option value="error" '.($row->response_status == 'error' ? 'selected' : '').'>Error</option>
                <option value="php" '.($row->response_status === 'php' ? 'selected' : '').'>Php</option>
                <option value="magento" '.($row->response_status == 'magento' ? 'selected' : '').'>Magento</option>
                <option value="message" '.($row->response_status == 'message' ? 'selected' : '').'>Message</option>
                <option value="translation_not_found" '.($row->response_status == 'translation_not_found' ? 'selected' : '').'>Translation not found</option>
                </select> <button style="padding-left:5px !important;" type="button" class="btn btn-xs show-logs-history" title="Show Logs History" data-id="'.$row->id.'">
                <i class="fa fa-info-circle"></i>
            </button></div>',
            ];
        }

        return response()->json([
            'code' => 200,
            'data' => $recorsArray,
            'pagination' => (string) $records->links(),
            'total' => $records->total(),
            'page' => $records->currentPage(),
        ]);
    }

    public function getLoadDataValue(Request $request): JsonResponse
    {
        $records = ProductPushErrorLog::where('id', $request->id)->first();

        $fulltextvalue = $records[$request->field];

        return response()->json(['code' => 200, 'data' => $fulltextvalue]);
    }

    public function groupErrorMessage(Request $request)
    {
        $records = ProductPushErrorLog::where('response_status', 'error')
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->latest('count')
            ->groupBy('message')
            ->select(DB::raw('*,COUNT(message) AS count'))
            ->get();

        $recordsArr = [];

        foreach ($records as $row) {
            if (strpos($row->message, 'Failed readiness') !== false) {
                if (array_key_exists('Failed_readiness', $recordsArr)) {
                    $recordsArr['Failed_readiness']['count'] = $recordsArr['Failed_readiness']['count'] + 1;
                    $recordsArr['Failed_readiness']['message'] = 'Failed readiness';
                } else {
                    $recordsArr['Failed_readiness'] = [
                        'count' => 1,
                        'message' => $row->message,

                    ];
                }
            } else {
                $recordsArr[] = [
                    'count' => $row->count,
                    'message' => $row->message,

                ];
            }
        }

        usort($recordsArr, function ($a, $b) {
            return $a['count'] - $b['count'];
        });

        rsort($recordsArr);

        $filename = 'Today Report Magento Errors.xlsx';

        return Excel::download(new MagentoProductCommonError($recordsArr), $filename);
    }

    //START - Purpose : Open modal and get data - DEVTASK-20123
    public function groupErrorMessageReport(Request $request)
    {
        $records = ProductPushErrorLog::latest('count')
            ->groupBy('message')
            ->select(DB::raw('*,COUNT(message) AS count'));

        if (isset($request->startDate) && isset($request->endDate)) {
            $records->whereDate('created_at', '>=', date($request->startDate))
                ->whereDate('created_at', '<=', date($request->endDate));
        } else {
            $records->whereDate('created_at', Carbon::now()->format('Y-m-d'));
        }

        if (! empty($request->websites)) {
            $records->whereIn('store_website_id', explode(',', $request->websites));
        }

        $records = $records->get();

        $recordsArr = [];
        foreach ($records as $row) {
            if (strpos($row->message, 'Failed readiness') !== false) {
                if (array_key_exists('Failed_readiness_'.$row->response_status, $recordsArr)) {
                    $recordsArr['Failed_readiness_'.$row->response_status]['count'] = $recordsArr['Failed_readiness_'.$row->response_status]['count'] + 1;
                    $recordsArr['Failed_readiness_'.$row->response_status]['message'] = 'Failed readiness';
                } else {
                    $recordsArr['Failed_readiness_'.$row->response_status] = [
                        'count' => 1,
                        'message' => $row->message,
                        'status' => $row->response_status,
                    ];
                }
            } else {
                $recordsArr[] = [
                    'count' => $row->count,
                    'message' => $row->message,
                    'status' => $row->response_status,
                ];
            }
        }

        usort($recordsArr, function ($a, $b) {
            return $a['count'] - $b['count'];
        });

        rsort($recordsArr);

        return response()->json(['code' => 200, 'data' => $recordsArr]);
    }

    //END - DEVTASK-20123

    public function getHistory(Request $request, $id)
    {
        $log_module = MagentoLogHistory::join('users', 'users.id', 'magento_log_history.user_id')->where('log_id', $id)->select('magento_log_history.*', 'users.name')->get();

        if ($log_module) {
            return $log_module;
        }

        return 'error';
    }

    public function changeStatus(Request $request, $id): JsonResponse
    {
        $log = ProductPushErrorLog::where('id', $id)->first();
        $logged_user = $request->user();

        $newtime = strtotime($log->created_at);
        $created_at = date('Y-m-d', $newtime);

        if ($log) {
            $log_data = ProductPushErrorLog::whereDate('created_at', $created_at)->Where('message', 'like', $log->message)->Where('store_website_id', $log->store_website_id)->get();

            foreach ($log_data as $value) {
                ProductPushErrorLog::where('id', $value->id)->update(['response_status' => $request->type]);

                $old_value = $value->response_status;
                MagentoLogHistory::create([
                    'log_id' => $value->id,
                    'user_id' => $logged_user->id,
                    'old_value' => $old_value,
                    'new_value' => $request->type,
                ]);
            }
        }

        return response()->json(true);
    }
}
