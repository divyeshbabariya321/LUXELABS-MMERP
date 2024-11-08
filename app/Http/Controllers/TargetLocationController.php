<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTargetLocationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\TargetLocation;
use App\InstagramUsersList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $locations = TargetLocation::all();

        return view('instagram.location.index', compact('locations'));
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
    public function store(StoreTargetLocationRequest $request): RedirectResponse
    {

        $location              = new TargetLocation();
        $location->country     = $request->get('country');
        $location->region      = $request->get('region');
        $polyY                 = explode(',', $request->get('lat'));
        $polyX                 = explode(',', $request->get('lng'));
        $location->region_data = [$polyX, $polyY];

        $location->save();

        return redirect()->back()->with('message', 'Location added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TargetLocation $targetLocation): View
    {
        return view('instagram.location.show', compact('targetLocation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\TargetLocation $targetLocation
     * @param mixed               $review
     */
    public function edit($review): View
    {
        $stats = InstagramUsersList::select(DB::raw('COUNT(`instagram_users_lists`.`id`) AS count, `target_locations`.`id` as location_id, `target_locations`.`country`, `target_locations`.`region`'))
            ->leftJoin('target_locations', 'instagram_users_lists.location_id', '=', 'target_locations.id')
            ->groupBy('location_id')->get()->toArray();

        $data   = [];
        $labels = [];
        foreach ($stats as $stat) {
            $data[]   = $stat->count;
            $labels[] = "\"$stat->country ($stat->region)\"";
        }

        $data   = implode(', ', $data);
        $labels = implode(', ', $labels);

        return view('instagram.location.report', compact('data', 'labels', 'stats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TargetLocation $targetLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TargetLocation $targetLocation)
    {
        //
    }
}
