<?php

namespace App\Http\Controllers;
use App\Helpers\OrderHelper;

use App\Http\Requests\UpdateRefundRequest;
use App\Http\Requests\StoreRefundRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Order;
use App\Refund;
use App\Setting;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\RefundDispatched;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $refunds = Refund::paginate(Setting::get('pagination'));

        return view('refund.index', [
            'refunds' => $refunds,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers    = Customer::all();
        $orders       = Order::all();
        $orders_array = [];

        foreach ($orders as $key => $order) {
            $orders_array[$key]['id']          = $order->id;
            $orders_array[$key]['order_id']    = $order->order_id;
            $orders_array[$key]['customer_id'] = $order->customer_id;
        }

        return view('refund.create', [
            'customers'    => $customers,
            'orders_array' => $orders_array,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRefundRequest $request): RedirectResponse
    {

        $data                  = $request->except('_token');
        $data['date_of_issue'] = Carbon::parse($request->date_of_request)->addDays(10);

        Refund::create($data);

        return redirect()->route('refund.index')->with('success', 'You have successfully added refund!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $refund       = Refund::find($id);
        $customers    = Customer::all();
        $orders       = Order::all();
        $orders_array = [];

        foreach ($orders as $key => $order) {
            $orders_array[$key]['id']          = $order->id;
            $orders_array[$key]['order_id']    = $order->order_id;
            $orders_array[$key]['customer_id'] = $order->customer_id;
        }

        return view('refund.show', [
            'refund'       => $refund,
            'customers'    => $customers,
            'orders_array' => $orders_array,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRefundRequest $request, int $id): RedirectResponse
    {

        $order = Order::find($request->order_id);

        $data = $request->except('_token', '_method');
        if (! $request->dispatched) {
            $data['dispatch_date'] = null;
            $data['awb']           = '';
        } else {
            $order->order_status    = 'Refund Dispatched';
            $order->order_status_id = OrderHelper::$refundDispatched;
            $refund                 = Refund::find($id);
            event(new RefundDispatched($refund));
        }

        if ($request->credited) {
            $data['credited'] = 1;

            $order->order_status    = 'Refund Credited';
            $order->order_status_id = OrderHelper::$refundCredited;
        }

        $order->save();

        $data['date_of_issue'] = Carbon::parse($request->date_of_request)->addDays(10);

        $refund = Refund::find($id);
        $refund->update($data);

        if ($request->credited) {
            $refund->order->delete();
            $refund->delete();
        }

        return redirect()->route('refund.index')->with('success', 'You have successfully added refund!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        Refund::find($id)->delete();

        return redirect()->route('refund.index')->with('success', 'You have successfully deleted refund!');
    }
}
