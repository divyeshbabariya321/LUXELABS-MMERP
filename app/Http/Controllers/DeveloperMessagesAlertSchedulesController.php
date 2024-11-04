<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\DeveloperMessagesAlertSchedules;

class DeveloperMessagesAlertSchedulesController extends Controller
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
    public function store(Request $request): RedirectResponse
    {
        $schedile = DeveloperMessagesAlertSchedules::first();

        if (! $schedile) {
            $schedile = new DeveloperMessagesAlertSchedules();
        }

        $schedile->time = $request->get('times');
        $schedile->save();

        return redirect()->back()->with('message', 'Alert schedules updated successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DeveloperMessagesAlertSchedules $developerMessagesAlertSchedules)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(DeveloperMessagesAlertSchedules $developerMessagesAlertSchedules)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeveloperMessagesAlertSchedules $developerMessagesAlertSchedules)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeveloperMessagesAlertSchedules $developerMessagesAlertSchedules)
    {
        //
    }
}
