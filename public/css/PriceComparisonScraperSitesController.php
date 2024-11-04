<?php

namespace seo2websites\PriceComparisonScraper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class PriceComparisonScraperSitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all the sites
        $sites = PriceComparisonScraperSites::all();

        // load the view and pass the nerds
        return view('PriceComparisonScraper::sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // load the create form
        $details = [];

        return view('PriceComparisonScraper::sites.create', compact('details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create new site
        $input = $request->input();
        PriceComparisonScraperSites::updateOrCreate(['id' => $input['id']], $input);

        // Redirect to index
        return redirect()->action('\seo2websites\PriceComparisonScraper\PriceComparisonScraperSitesController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\PriceComparisonScraperSites $priceComparisonScraperSites
     * @param mixed                            $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $details = PriceComparisonScraperSites::find($id);
        // load the create form
        return view('PriceComparisonScraper::sites.create', compact('details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\PriceComparisonScraperSites $priceComparisonScraperSites
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PriceComparisonScraperSites $priceComparisonScraperSites)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\PriceComparisonScraperSites $priceComparisonScraperSites
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PriceComparisonScraperSites $priceComparisonScraperSites)
    {
        //
    }
}
