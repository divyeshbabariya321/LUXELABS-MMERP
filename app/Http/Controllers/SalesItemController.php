<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\SalesItem;
use Illuminate\Http\Request;

class SalesItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $items = SalesItem::distinct()->get(['supplier']);

        return view('scrap.sale.index', compact('items'));
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
     * @param \App\SalesItem $salesItem
     * @param mixed          $supplier
     */
    public function show($supplier): View
    {
        $products = SalesItem::where('supplier', $supplier)->paginate(25);
        $title    = $supplier;

        return view('scrap.sale.show', compact('products', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesItem $salesItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesItem $salesItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesItem $salesItem)
    {
        //
    }
}
