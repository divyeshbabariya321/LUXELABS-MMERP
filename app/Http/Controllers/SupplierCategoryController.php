<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierCategoryRequest;
use App\Http\Requests\UpdateSupplierCategoryRequest;
use App\SupplierCategory;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $suppliercategory = SupplierCategory::orderByDesc('id')->paginate(10);

        return view('supplier-category.index', compact('suppliercategory'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('supplier-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierCategoryRequest $request): RedirectResponse
    {

        SupplierCategory::create(['name' => $request->input('name')]);

        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $category = SupplierCategory::find($id);

        return view('supplier-category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierCategoryRequest $request, int $id): RedirectResponse
    {

        $department = SupplierCategory::find($id);
        $department->name = $request->input('name');
        $department->save();

        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        SupplierCategory::where('id', $id)->delete();

        return redirect()->route('supplier-category.index')
            ->with('success', 'Supplier Category deleted successfully');
    }

    public function usersPermission(Request $request): View
    {
        $users = User::where('is_active', 1)->orderBy('name')->with('supplierCategoryPermission')->get();
        $categories = SupplierCategory::orderBy('name')->get();

        return view('suppliers.supplier-category-permission.index', compact('users', 'categories'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function updatePermission(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $category_id = $request->supplier_category_id;
        $check = $request->check;
        $user = User::findorfail($user_id);
        //ADD PERMISSION
        if ($check == 1) {
            $user->supplierCategoryPermission()->attach($category_id);
            $message = 'Permission added Successfully';
        }
        //REMOVE PERMISSION
        if ($check == 0) {
            $user->supplierCategoryPermission()->detach($category_id);
            $message = 'Permission removed Successfully';
        }

        $data = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($data);
    }
}
