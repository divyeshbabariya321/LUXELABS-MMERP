<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierStatusRequest;
use App\Http\Requests\UpdateSupplierStatusRequest;
use App\SupplierStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $supplierstatus = SupplierStatus::orderByDesc('id')->paginate(10);

        return view('supplier-status.index', compact('supplierstatus'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('supplier-status.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierStatusRequest $request): RedirectResponse
    {

        SupplierStatus::create(['name' => $request->input('name')]);

        return redirect()->route('supplier-status.index')
            ->with('success', 'Supplier Status created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $status = SupplierStatus::find($id);

        return view('supplier-status.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierStatusRequest $request, int $id): RedirectResponse
    {

        $department = SupplierStatus::find($id);
        $department->name = $request->input('name');
        $department->save();

        return redirect()->route('supplier-status.index')
            ->with('success', 'Supplier Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        SupplierStatus::where('id', $id)->delete();

        return redirect()->route('supplier-status.index')
            ->with('success', 'Supplier Status deleted successfully');
    }
}
