<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttributeReplacementRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\AttributeReplacement;
use Illuminate\Support\Facades\Auth;

class AttributeReplacementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $replacements = AttributeReplacement::orderBy('field_identifier')->get();

        return view('products.attr_replacements.index', compact('replacements'));
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
    public function store(StoreAttributeReplacementRequest $request): RedirectResponse
    {

        $r                   = new AttributeReplacement();
        $r->action_to_peform = 'REPLACE';
        $r->field_identifier = $request->get('field_identifier');
        $r->first_term       = $request->get('first_term');
        $r->replacement_term = $request->get('replacement_term');
        $r->remarks          = $request->get('remarks');
        $r->save();

        return redirect()->back()->with('message', 'Added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\AttributeReplacement $attributeReplacement
     * @param mixed                     $id
     */
    public function show($id, Request $request): JsonResponse
    {
        $r = AttributeReplacement::find($id);
        if (! $r) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        $r->authorized_by = Auth::id();
        $r->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(AttributeReplacement $attributeReplacement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttributeReplacement $attributeReplacement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttributeReplacement $attributeReplacement)
    {
        //
    }
}
