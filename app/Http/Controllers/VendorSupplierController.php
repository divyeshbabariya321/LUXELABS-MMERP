<?php

namespace App\Http\Controllers;
use App\VendorCategory;
use App\SupplierStatus;
use App\SupplierCategory;

use Illuminate\View\View;

class VendorSupplierController extends Controller
{
    public function index(): View
    {
        return view('vendor-supplier.index');
    }

    public function vendorForm(): View
    {
        $vendorCategory = VendorCategory::all()->pluck('title', 'id')->toArray();

        return view('vendor-supplier.vendor-form', compact('vendorCategory'));
    }

    public function supplierForm(): View
    {
        $suppliercategory = SupplierCategory::pluck('name', 'id')->toArray();
        $supplierstatus = SupplierStatus::pluck('name', 'id')->toArray();

        return view('vendor-supplier.supplier-form', compact(['suppliercategory', 'supplierstatus']));
    }
}
