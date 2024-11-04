<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Helpers\OrderHelper;
use App\Http\Requests\StatusStoreOrderReportRequest;
use App\Http\Requests\StoreOrderReportRequest;
use App\Order;
use App\OrderReport;
use App\OrderStatus;
use App\ReturnExchange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderReportRequest $request): RedirectResponse
    {

        $report = new OrderReport;

        $report->status_id = $request->status_id;
        $report->user_id = Auth::id();

        if ($request->order_id) {
            $report->order_id = $request->order_id;
        } else {
            $report->customer_id = $request->customer_id;
        }

        $report->completion_date = $request->completion_date;

        $report->save();

        return redirect()->back()->with('message', 'Order action was created successfully');
    }

    public function statusStore(StatusStoreOrderReportRequest $request): RedirectResponse
    {

        $status = new OrderStatus;

        $status->status = $request->status;

        $status->save();

        return redirect()->back()->with('message', 'Order status was created successfully');
    }

    public function orderRefundStatusMessage(Request $request): View
    {
        $orders = Order::join('customers', 'orders.customer_id', 'customers.id')
            ->select('orders.id', 'orders.is_flag', 'customer_id', 'orders.created_at as date', DB::raw("'order' as type"), 'customers.phone', 'customers.name', 'customers.email', 'order_status_id', 'estimated_delivery_date');

        if ($request->order_id && $request->order_id != null) {
            $orders->where('orders.id', $request->order_id);
        }
        if ($request->customer_name && $request->customer_name != null) {
            $orders->where('customers.name', 'LIKE', '%'.$request->customer_name.'%');
        }

        if ($request->flt_order_status && $request->flt_order_status != null) {
            $orders->where('order_status_id', $request->flt_order_status);
        }
        if ($request->flt_estimate_date && $request->flt_estimate_date != null) {
            $orders->where('estimated_delivery_date', 'LIKE', $request->flt_estimate_date);
        }

        $order_n_refunds = ReturnExchange::join('customers', 'return_exchanges.customer_id', 'customers.id')
            ->select('return_exchanges.id', 'is_flagged as is_flag', 'customer_id', 'return_exchanges.created_at as date', DB::raw("'refund' as type"), 'customers.phone', 'customers.name', 'customers.email', DB::raw("'' as order_status_id"), DB::raw('return_exchanges.est_completion_date as estimated_delivery_date'));
        if ($request->order_id && $request->order_id != null) {
            $order_n_refunds->where('return_exchanges.id', $request->order_id);
        }
        if ($request->customer_name && $request->customer_name != null) {
            $order_n_refunds->where('customers.name', 'LIKE', '%'.$request->customer_name.'%');
        }
        $order_n_refunds = $order_n_refunds->union($orders)->orderByDesc('date')->get();
        $order_n_refunds = $order_n_refunds->map(function ($item, $key) {
            $item->chatMessage = ChatMessage::where('chat_messages.order_id', '=', $item->id)->where('chat_messages.message', '!=', '')->orderBy('created_at', 'DESC')->first();

            return $item;
        });
        $orderStatusList = OrderStatus::all();
        $order_status_list = OrderHelper::getStatus();

        return view('orders.status-history', compact('order_n_refunds', 'order_status_list', 'orderStatusList'));
    }

    public function setFlag(Request $request): JsonResponse
    {
        $return_exchanges = Order::find($request->id);

        if ($return_exchanges->is_flag == 0) {
            $return_exchanges->is_flag = 1;
        } else {
            $return_exchanges->is_flag = 0;
        }

        $return_exchanges->save();

        return response()->json(['is_flagged' => $return_exchanges->is_flag]);
    }

    public function lastCommunicated($type = 'any')
    {
        $q = $this->chatMessage()->whereNotIn('status', ['7', '8', '9', '10']);

        if (in_array($type, ['unread', 'unapproved', 'chatbot_unapproved'])) {
            if ($type == 'unread') {
                $type = 0;
            } elseif ($type == 'chatbot_unapproved') {
                $type = 11;
            } else {
                $type = 1;
            }
            $q = $q->where('chat_messages.status', $type);
        } elseif ($type == 'last_communicated') {
            $q = $q->where('chat_messages.message', '!=', '')->where(function ($q) {
                $q->where('group_id', '<', 0)->orWhere('group_id', '')->orWhereNull('group_id');
            });
        } elseif ($type == 'last_received') {
            $q = $q->where('chat_messages.number', '=', $this->phone)->where('chat_messages.message', '!=', '');
        }

        return $q->orderByDesc('created_at')->first();
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }
}
