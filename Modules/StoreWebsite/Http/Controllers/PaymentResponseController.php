<?php

namespace Modules\StoreWebsite\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\StoreWebsite;
use App\PaymentResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title    = 'Payment Responses';
        $websites = StoreWebsite::all();

        return view('storewebsite::payment-responses.index', compact(['title', 'websites']));
    }

    /**
     * Fetch records of the resource.
     *
     * @param \App\StoreWebsite $url,$token
     */
    public function records(Request $request): JsonResponse
    {
        $records    = PaymentResponse::with('website')->orderByDesc('created_at');
        $keyword    = $request->input('amount');
        $website_id = $request->input('store_website_id');
        $card_type  = $request->input('card_type');
        $date       = $request->input('date');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('base_shipping_captured', 'LIKE', "%$keyword%")
                    ->orWhere('shipping_captured', 'LIKE', "%$keyword%")->orWhere('amount_refunded', 'LIKE', "%$keyword%")->orWhere('base_amount_paid', 'LIKE', "%$keyword%")->orWhere('amount_canceled', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_authorized', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_paid_online', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_refunded_online', 'LIKE', "%$keyword%")
                    ->orWhere('base_shipping_amount', 'LIKE', "%$keyword%")
                    ->orWhere('shipping_amount', 'LIKE', "%$keyword%")
                    ->orWhere('amount_paid', 'LIKE', "%$keyword%")->orWhere('amount_authorized', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_ordered', 'LIKE', "%$keyword%")
                    ->orWhere('base_shipping_refunded', 'LIKE', "%$keyword%")
                    ->orWhere('shipping_refunded', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_refunded', 'LIKE', "%$keyword%")
                    ->orWhere('amount_ordered', 'LIKE', "%$keyword%")
                    ->orWhere('base_amount_canceled', 'LIKE', "%$keyword%")
                    ->orWhere('quote_payment_id', 'LIKE', "%$keyword%")
                    ->orWhere('cc_exp_month', 'LIKE', "%$keyword%")
                    ->orWhere('cc_ss_start_year', 'LIKE', "%$keyword%")
                    ->orWhere('cc_secure_verify', 'LIKE', "%$keyword%")
                    ->orWhere('cc_approval', 'LIKE', "%$keyword%")->orWhere('cc_last_4', 'LIKE', "%$keyword%")
                    ->orWhere('cc_type', 'LIKE', "%$keyword%")
                    ->orWhere('cc_exp_year', 'LIKE', "%$keyword%")
                    ->orWhere('cc_status', 'LIKE', "%$keyword%");
            });
        }
        if (! empty($website_id)) {
            $records = $records->where(function ($q) use ($website_id) {
                $q->where('website_id', '=', $website_id);
            });
        }
        if (! empty($card_type)) {
            $records = $records->where(function ($q) use ($card_type) {
                $q->where('cc_type', 'LIKE', "%$card_type%");
            });
        }
        if (! empty($date)) {
            $records = $records->where(function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            });
        }
        $records = $records->get();

        return response()->json(['code' => 200, 'data' => $records, 'total' => count($records)]);
    }
}
