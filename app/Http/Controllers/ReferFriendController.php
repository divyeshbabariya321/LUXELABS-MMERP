<?php

namespace App\Http\Controllers;
use App\LogReferalCoupon;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\ReferFriend;
use Illuminate\Http\Request;

class ReferFriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = ReferFriend::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('referrer_email', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_first_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_last_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referrer_phone', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_first_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_last_name', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_email', 'LIKE', '%' . $request->term . '%')
                ->orWhere('referee_phone', 'LIKE', '%' . $request->term . '%')
                ->orWhere('website', 'LIKE', '%' . $request->term . '%')
                ->orWhere('status', 'LIKE', '%' . $request->term . '%');
        }

        if ($request->for_date) {
            $query = $query->whereDate('created_at', $request->for_date);
        }

        $data = $query->orderByDesc('id')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('referfriend.partials.list-referral', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('referfriend.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $ReferFriend = ReferFriend::find($id);
        $ReferFriend->delete();

        return redirect()->route('referfriend.list')
            ->with('success', 'Referral deleted successfully');
    }

    /*
    * logAjax : Return log of refere friend api
    */
    public function logAjax(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $log = LogReferalCoupon::where('refer_friend_id', $request->get('id'))->get()->toArray();
            if ($log) {
                return response()->json([
                    'data' => $log,
                ], 200);
            }

            return response()->json([
                'data' => [],
            ], 200);
        }
    }
}
