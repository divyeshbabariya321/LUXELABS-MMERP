<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageNotesCategoryRequest;
use App\Http\Requests\UpdatePageNotesCategoryRequest;
use App\PageNotes;
use App\PageNotesCategories;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageNotesCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $data;

    public function index(Request $request): View
    {
        $pageNotesCategories = PageNotesCategories::paginate(15);

        return view('page-notes-categories.index', ['pageNotesCategories' => $pageNotesCategories]);
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
    public function store(StorePageNotesCategoryRequest $request): RedirectResponse
    {
        PageNotesCategories::create([
            'name' => $request->get('name'),
        ]);

        return redirect()->back()->withSuccess('Page Notes Category Successfully stored.');
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
     */
    public function update(int $id, UpdatePageNotesCategoryRequest $request): RedirectResponse
    {
        $pageNotesCategories = PageNotesCategories::where('id', $id)->first();
        if ($pageNotesCategories) {
            $pageNotesCategories->fill([
                'name' => $request->get('name'),
            ])->save();

            return redirect()->back()->withSuccess('Page Notes Category Successfully updated.');
        }

        return redirect()->back()->withSuccess('Page Notes Category Successfully not updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $pageNotesCategories = PageNotesCategories::where('id', $id)->first();
        try {
            $pageNotes = PageNotes::where('category_id', $id)->count();
            if (! $pageNotes) {
                $pageNotesCategories->delete();
            } else {
                return redirect()->back()->withErrors('Couldn\'t delete data , category is using in page notes!!');
            }
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t delete data');
        }

        return redirect()->back()->withSuccess('You have successfully deleted page notes category');
    }
}
