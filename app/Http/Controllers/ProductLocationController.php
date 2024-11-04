<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductLocationRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\ProductLocation;
use Illuminate\Http\Request;

class ProductLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $productLocation = ProductLocation::all();

        return view('product-location.index', compact('productLocation'));
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
    public function store(StoreProductLocationRequest $request): RedirectResponse
    {

        $productLocation       = new ProductLocation();
        $productLocation->name = $request->get('name');
        $productLocation->save();

        return redirect()->back()->with('message', 'Location added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     */
    public function destroy($id): RedirectResponse
    {
        $productLocation = ProductLocation::find($id);

        if ($productLocation) {
            $productLocation->delete();
        }

        return redirect()->back()->with('message', 'Location deleted successfully!');
    }
}
