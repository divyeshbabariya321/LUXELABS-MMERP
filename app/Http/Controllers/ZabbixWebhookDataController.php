<?php

namespace App\Http\Controllers;

use App\Models\ZabbixWebhookData;
use App\ZabbixStatus;
use App\ZabbixWebhookDataRemarkHistory;
use App\ZabbixWebhookDataStatusHistory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ZabbixWebhookDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keyword = $request->get('keyword');
        $eventStart = $request->get('event_start');

        $zabbixWebhookDatas = ZabbixWebhookData::latest();

        if (! empty($keyword)) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->where(function ($q) use ($keyword) {
                $q->orWhere('subject', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('message', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('event_name', 'LIKE', '%'.$keyword.'%');
            });
        }

        if ($eventStart) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->whereDate('event_start', $eventStart);
        }

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate(10);

        $zabbixStatuses = ZabbixStatus::pluck('name', 'id')->toArray();
        $getZabbixStatuses = ZabbixStatus::all();

        return view('zabbix-webhook-data.index', compact('zabbixWebhookDatas', 'zabbixStatuses', 'getZabbixStatuses'));
    }

    public function storeZabbixStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:zabbix_statuses,name',
            'color' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'status_name' => 'error',
            ], 422);
        }

        $input = $request->except(['_token']);

        $data = ZabbixStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function getRemarks($zabbix_webhook_data): JsonResponse
    {
        $remarks = ZabbixWebhookDataRemarkHistory::with(['user'])->where('zabbix_webhook_data_id', $zabbix_webhook_data)->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function storeRemark(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);
        $input['user_id'] = Auth::user()->id;

        $zabbixWebhookDataRemarkHistory = ZabbixWebhookDataRemarkHistory::create($input);

        if ($zabbixWebhookDataRemarkHistory) {
            $update = ZabbixWebhookData::where('id', $request->zabbix_webhook_data_id)->update(['remarks' => $request->remarks]);

            return response()->json([
                'status' => true,
                'message' => 'Remark added successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark added unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $zaabbixWebhookData = ZabbixWebhookData::findOrFail($request->zabbix_webhook_data);
            $old_status = $zaabbixWebhookData->zabbix_status_id;

            $zaabbixWebhookData->zabbix_status_id = $request->status;

            $zaabbixWebhookData->save();

            $statusColour = ZabbixStatus::find($zaabbixWebhookData->zabbix_status_id);
            $statusColour = $statusColour->color;

            $history = new ZabbixWebhookDataStatusHistory;
            $history->zabbix_webhook_data_id = $zaabbixWebhookData->id;
            $history->old_status_id = $old_status;
            $history->new_status_id = $request->status;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Status updated successfully',
                    'colourCode' => $statusColour,
                ], 200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Status not updated.',
                ], 500
            );
        }
    }

    public function issuesSummary(Request $request): JsonResponse
    {
        $perPage = 10; // Number of records per page

        $zabbixWebhookDatas = ZabbixWebhookData::latest();
        $zabbixWebhookDatas = $zabbixWebhookDatas->where('severity', 'high');

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate($perPage);

        return response()->json($zabbixWebhookDatas);
    }

    public function StatusColorUpdate(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = ZabbixStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
}
