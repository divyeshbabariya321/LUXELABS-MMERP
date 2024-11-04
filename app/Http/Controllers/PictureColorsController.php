<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\PictureColors;
use Illuminate\Http\Request;

class PictureColorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pictures = PictureColors::all();

        return view('test2', compact('pictures'));
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
    public function show(PictureColors $pictureColors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(PictureColors $pictureColors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PictureColors $pictureColors)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(PictureColors $pictureColors)
    {
        //
    }
}
