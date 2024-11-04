<?php

namespace App\Http\Controllers;

use App\OrderStatus;
use App\PurchaseStatus;
use App\ReadOnly\ShippingStatus;
use App\ReturnExchangeStatus;
use App\StatusMapping;
use App\StatusMappingHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StatusMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $data['orderStatuses'] = OrderStatus::pluck('status', 'id')->all();
        $data['purchaseStatuses'] = PurchaseStatus::pluck('name', 'id')->all();
        $data['shippingStatuses'] = (new ShippingStatus)->all();
        $data['returnExchangeStatuses'] = ReturnExchangeStatus::pluck('status_name', 'id')->all();

        $data['statusMappings'] = StatusMapping::latest()->get();

        return view('status-mappings.index', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $statusMapping = StatusMapping::where('order_status_id', $input['orderStatusId'])->first();

        if ($statusMapping) {
            return response()->json([
                'status' => false,
                'message' => 'Order status mapping already created',
                'status_name' => 'error',
            ], 500);
        } else {
            $statusMapping = new StatusMapping;
            $statusMapping->order_status_id = $input['orderStatusId'];
            $statusMapping->save();

            if ($statusMapping) {
                return response()->json([
                    'status' => true,
                    'message' => 'Mapping created successfully',
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
    }

    public function update(Request $request, $id)
    {
        $input = $request->except(['_token']);

        $statusMapping = StatusMapping::where('id', $id)->first();

        if (! $statusMapping) {
            return response()->json([
                'status' => false,
                'message' => 'Record not exists',
                'status_name' => 'error',
            ], 500);
        } else {
            if ($input['statusType'] == StatusMappingHistory::STATUS_TYPE_PURCHASE) {
                $purchaseStatusMapping = StatusMapping::where('purchase_status_id', $input['purchaseStatusId'])->first();
                if ($purchaseStatusMapping) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This purchase status already mapped with other Order status, Please choose different one',
                        'status_name' => 'error',
                    ], 500);
                }

                $oldStatusId = $statusMapping->purchase_status_id;
                $newStatusId = $input['purchaseStatusId'];
                $statusMapping->purchase_status_id = $newStatusId;
            } elseif ($input['statusType'] == StatusMappingHistory::STATUS_TYPE_SHIPPING) {
                $shippingStatusMapping = StatusMapping::where('shipping_status_id', $input['shippingStatusId'])->first();
                if ($shippingStatusMapping) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This shipping status already mapped with other Order status, Please choose different one',
                        'status_name' => 'error',
                    ], 500);
                }

                $oldStatusId = $statusMapping->shipping_status_id;
                $newStatusId = $input['shippingStatusId'];
                $statusMapping->shipping_status_id = $newStatusId;
            } elseif ($input['statusType'] == StatusMappingHistory::STATUS_TYPE_RETURN_EXCHANGE) {
                $returnExchangeStatusMapping = StatusMapping::where('return_exchange_status_id', $input['returnExchangeStatusId'])->first();
                if ($returnExchangeStatusMapping) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This return exchange status already mapped with other Order status, Please choose different one',
                        'status_name' => 'error',
                    ], 500);
                }

                $oldStatusId = $statusMapping->return_exchange_status_id;
                $newStatusId = $input['returnExchangeStatusId'];
                $statusMapping->return_exchange_status_id = $newStatusId;
            }

            $statusMapping->save();

            $lastUpdatedUser = $this->saveStatusMappingHistory($statusMapping, $oldStatusId, $newStatusId, $input['statusType']);

            if ($statusMapping) {
                return response()->json([
                    'status' => true,
                    'message' => 'Mapping updated successfully',
                    'status_name' => 'success',
                    'data' => [
                        'lastUpdatedUser' => $lastUpdatedUser,
                    ],
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'something error occurred',
                    'status_name' => 'error',
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $statusMapping = StatusMapping::where('id', $id)->first();

        if ($statusMapping) {
            $oldPurchaseStatusId = $statusMapping->purchase_status_id;
            $oldShippingStatusId = $statusMapping->shipping_status_id;
            $oldReturnExchangeStatusId = $statusMapping->return_exchange_status_id;

            $statusMapping->purchase_status_id = '';
            $statusMapping->shipping_status_id = '';
            $statusMapping->return_exchange_status_id = '';
            $statusMapping->save();

            $this->saveStatusMappingHistory($statusMapping, $oldPurchaseStatusId, '', StatusMappingHistory::STATUS_TYPE_PURCHASE);
            $this->saveStatusMappingHistory($statusMapping, $oldShippingStatusId, '', StatusMappingHistory::STATUS_TYPE_SHIPPING);
            $lastUpdatedUser = $this->saveStatusMappingHistory($statusMapping, $oldReturnExchangeStatusId, '', StatusMappingHistory::STATUS_TYPE_RETURN_EXCHANGE);

            return response()->json([
                'status' => true,
                'message' => 'Mapping Deleted successfully',
                'status_name' => 'success',
                'data' => [
                    'lastUpdatedUser' => $lastUpdatedUser,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    protected function saveStatusMappingHistory($statusMapping, $oldStatusId, $newStatusId, $statusType)
    {
        $history = new StatusMappingHistory;
        $history->status_mapping_id = $statusMapping->id;
        $history->old_status_id = $oldStatusId;
        $history->new_status_id = $newStatusId;
        $history->status_type = $statusType;
        $history->user_id = Auth::user()->id;
        $history->save();

        return $history->user->name;
    }
}
