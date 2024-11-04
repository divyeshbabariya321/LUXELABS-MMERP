<?php

namespace App\Http\Controllers;

use App\CustomerCategory;
use App\Http\Requests\StoreCustomerCategoryRequest;
use App\Http\Requests\UpdateCustomerCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = CustomerCategory::all();

        return view('customers.category_messages.category.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerCategoryRequest $request): RedirectResponse
    {

        $category = new CustomerCategory;
        $category->name = $request->get('name');
        $category->message = $request->get('message');
        $category->save();

        return redirect()->back()->with('message', 'Category added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerCategory  $customerCategory
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customerCategory = CustomerCategory::find($id);

        if (! $customerCategory) {
            return redirect()->back()->with('message', 'The requested category is not available!');
        }

        return view('customers.category_messages.category.edit', compact('customerCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\CustomerCategory  $customerCategory
     * @param  mixed  $id
     */
    public function update(UpdateCustomerCategoryRequest $request, $id): RedirectResponse
    {
        $category = CustomerCategory::find($id);

        if (! $category) {
            return redirect()->back()->with('message', 'The requested category is not available!');
        }

        $category->name = $request->get('name');
        $category->message = $request->get('message');
        $category->save();

        return redirect()->back()->with('message', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerCategory  $customerCategory
     * @param  mixed  $id
     */
    public function destroy($id): RedirectResponse
    {
        $category = CustomerCategory::find($id);

        if (! $category) {
            return redirect()->back()->with('message', 'The requested category is not available!');
        }

        $category->delete();

        return redirect()->action([CustomerCategoryController::class, 'index'])->with('message', 'Category deleted successfully!');
    }
}
