<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $courier = Courier::all();

        return view('courier.index', compact('courier'));
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
    public function store(StoreCourierRequest $request): RedirectResponse
    {

        $courier       = new Courier();
        $courier->name = $request->get('name');
        $courier->save();

        return redirect()->back()->with('message', 'Courier added successfully!');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(KeywordToCategory $keywordToCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(KeywordToCategory $keywordToCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KeywordToCategory $keywordToCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\KeywordToCategory $keywordToCategory
     * @param mixed                  $id
     */
    public function destroy($id): RedirectResponse
    {
        $keyword = Courier::find($id);

        if ($keyword) {
            $keyword->delete();
        }

        return redirect()->back()->with('message', 'Courier deleted successfully!');
    }
}
