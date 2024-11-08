<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\ListingHistory;
use Illuminate\Http\Request;

class ListingHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'Listing History';

        return view('listing-history.index', compact(['title']));
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
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ListingHistory $listingHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ListingHistory $listingHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ListingHistory $listingHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListingHistory $listingHistory)
    {
        //
    }

    public function records(Request $request): JsonResponse
    {
        $history = ListingHistory::leftJoin('products as p', 'p.id', 'listing_histories.product_id')
            ->leftJoin('users as u', 'u.id', 'listing_histories.user_id')
            ->orderByDesc('listing_histories.created_at')
            ->select(['listing_histories.*', 'u.name as user_name', 'p.name as product_name']);

        if ($request->created_by != null) {
            $history = $history->where('user_id', $request->created_by);
        }

        if ($request->keyword != null) {
            $history = $history->where(function ($q) use ($request) {
                $q->where('listing_histories.action', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->created_at != null) {
            $history = $history->whereDate('listing_histories.created_at', $request->created_at);
        }

        $updated_history = clone $history;
        $updated_history = $updated_history->groupBy('u.id');
        $updated_history = $updated_history->select(['u.name as user_name', DB::raw('count(listing_histories.product_id) as total_updated')]);
        $updated_history = $updated_history->get()->toArray();

        $history = $history->paginate(25);

        return response()->json([
            'code'            => 200,
            'data'            => $history->items(),
            'total'           => $history->total(),
            'pagination'      => (string) $history->render(),
            'updated_history' => $updated_history,
        ]);
    }
}
