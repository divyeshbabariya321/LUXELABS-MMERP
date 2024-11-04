<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeywordRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Keywords;
use Illuminate\Http\Request;

class KeywordsController extends Controller
{
    public function index(): View
    {
        $keywords = Keywords::all();

        return view('instagram.keywords.index', compact('keywords'));
    }

    public function store(StoreKeywordRequest $request): RedirectResponse
    {

        $k       = new Keywords();
        $k->text = $request->get('name');
        $k->save();

        return redirect()->back()->with('message', 'Keyword added successfully!');
    }

    public function destroy($id): RedirectResponse
    {
        $k = Keywords::findOrFail($id);
        $k->delete();

        return redirect()->back()->with('message', 'Keyword deleted successfully!');
    }
}
