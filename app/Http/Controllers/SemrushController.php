<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class SemrushController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        //
        $title = 'Domain Report';

        return view('semrush.domain_report', compact('title'));
    }

    public function keyword_report(): View
    {
        $title = 'Keyword Report';

        return view('semrush.keyword_report', compact('title'));
    }

    public function url_report(): View
    {
        $title = 'URL Report';

        return view('semrush.url_report', compact('title'));
    }

    public function backlink_reffring_report(): View
    {
        $title = 'Backlink & Reffring Domain';

        return view('semrush.backlink_reffring_report', compact('title'));
    }

    public function publisher_display_ad(): View
    {
        $title = 'Publisher Display Ad';

        return view('semrush.publisher_display_ad', compact('title'));
    }

    public function traffic_analitics_report(): View
    {
        $title = 'Traffic analitics Report';

        return view('semrush.traffic_analitics_report', compact('title'));
    }

    public function competitor_analysis(): View
    {
        $title = 'Competitor analyasis';

        return view('semrush.competitor_analysis', compact('title'));
    }

    public function manageSemrushAccounts(): View
    {
        return view('semrush.manage-accounts');
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
