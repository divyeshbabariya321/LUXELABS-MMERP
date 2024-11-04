<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChangeDescriptionRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\DescriptionChange;
use Illuminate\Http\Request;

class ChangeDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $matchedArray = [];
        $descriptions = DescriptionChange::query();

        $listdescriptions = ['' => '-- Select --'] + DescriptionChange::where('replace_with', '!=', '')->groupBy('replace_with')->pluck('replace_with', 'replace_with')->toArray();

        $descriptions = $descriptions->orderByDesc('id')->paginate(50);

        return view('description.index', compact('descriptions', 'listdescriptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChangeDescriptionRequest $request): RedirectResponse
    {

        $ifExist = DescriptionChange::where('keyword', $request->keyword)->first();

        if ($ifExist) {
            return redirect()->back();
        }

        $c = DescriptionChange::create($request->all());

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Compositions $compositions
     */
    public function destroy(Request $request): RedirectResponse
    {
        $id = $request->description_id;
        $c  = DescriptionChange::find($id);
        $c->delete();

        return redirect()->back();
    }
}
