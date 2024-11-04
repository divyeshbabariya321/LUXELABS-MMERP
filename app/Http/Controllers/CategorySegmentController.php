<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCategorySegmentRequest;
use App\Http\Requests\StoreCategorySegmentRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Setting;
use App\CategorySegment;
use Illuminate\Http\Request;

class CategorySegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $category_segments = CategorySegment::query();

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $category_segments = $category_segments->where('name', 'like', '%' . $keyword . '%');
        }

        $category_segments = $category_segments->paginate(Setting::get('pagination'));

        return view('category-segment.index', compact('category_segments'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['name']   = '';
        $data['status'] = '';
        $data['modify'] = 0;

        return view('category-segment.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategorySegmentRequest $request): RedirectResponse
    {
        $category_segment         = new CategorySegment();
        $category_segment->name   = $request->name;
        $category_segment->status = $request->status;
        $category_segment->save();

        return redirect()->route('category-segment.index')->with('success', 'Category Segment created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $category_segment = CategorySegment::find($id);
        $data             = $category_segment->toArray();
        $data['modify']   = 1;

        return view('category-segment.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategorySegmentRequest $request, int $id): RedirectResponse
    {
        $category_segment         = CategorySegment::find($id);
        $category_segment->name   = $request->name;
        $category_segment->status = $request->status;
        $category_segment->save();

        return redirect()->route('category-segment.index')->with('success', 'Category Segment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $category_segment = CategorySegment::find($id);
        if ($category_segment) {
            $category_segment->delete();
        }

        return redirect()->route('category-segment.index')->with('success', 'Category Segment deleted successfully!');
    }
}
