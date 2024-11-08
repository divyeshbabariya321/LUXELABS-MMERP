<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingPaymentRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\User;
use App\ListingPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ListingHistory;

class ListingPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *                                   Get the listing history
     */
    public function index(Request $request): View
    {
        // simply get stats for listing rejected or approved by user
        $histories = ListingHistory::selectRaw('
                  user_id,
                  DATE(`created_at`) as date,
                  SUM(case when action = "LISTING_APPROVAL" then 1 Else 0 End) as attribute_approved,
                  SUM(case when action = "LISTING_REJECTED" then 1 Else 0 End) as attribute_rejected
            ')
            ->whereIn('action', ['LISTING_APPROVAL', 'LISTING_REJECTED'])
            ->groupBy(DB::raw('`user_id`, DATE(`created_at`)'));

        if ($request->get('user_id') > 0) {
            $histories = $histories->where('user_id', $request->get('user_id'));
        }

        $histories = $histories->get();

        $users = User::pluck('name', 'id')->toArray();

        return view('products.listing_payment', compact('histories', 'users', 'request'));
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
     *
     * @return \Illuminate\Http\Response
     *                                   Create the entry for the paid amount
     */
    public function store(StoreListingPaymentRequest $request): RedirectResponse
    {

        $amt              = new ListingPayments();
        $amt->paid_at     = $request->get('date');
        $amt->amount      = $request->get('amount');
        $amt->user_id     = $request->get('user_id');
        $amt->product_ids = [];
        $amt->remarks     = 'Paid till ' . $request->get('date');
        $amt->save();

        return redirect()->back()->with('success', 'Amount paid successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ListingPayments $listingPayments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ListingPayments $listingPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ListingPayments $listingPayments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListingPayments $listingPayments)
    {
        //
    }
}
