<?php

namespace App\Http\Controllers;
use App\Supplier;
use App\Colors;
use App\Brand;

use App\Http\Requests\RejectProductSupervisorRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Stage;
use App\Product;
use App\Setting;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSupervisorController extends Controller
{
    public function index(Stage $stage): View
    {
        $products = Product::where('stock', '>=', 1)->latest()
            ->whereNull('dnf')
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
            ->paginate(Setting::get('pagination'));

        $roletype = 'Supervisor';

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple'])
            ->selected(1)
            ->renderAsDropdown();
        $attach_image_tag = config('constants.attach_image_tag');
        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors())->all();
        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'roletype', 'category_selection', 'attach_image_tag', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit(Product $productsupervisor): RedirectResponse
    {
        return redirect()->route('products.show', $productsupervisor->id);
    }

    public function approve(Product $product, Stage $stage): RedirectResponse
    {
        $product->isApproved = 1;
        $product->stage      = $stage->get('Supervisor');
        $product->save();

        NotificaitonContoller::store('has Approved', ['ImageCropers'], $product->id);
        ActivityConroller::create($product->id, 'supervisor', 'create');

        return redirect()->back()->with('success', 'Product has been approved');
    }

    public function reject(Product $product, RejectProductSupervisorRequest $request): RedirectResponse
    {

        $role   = $request->input('role');
        $reason = $request->input('reason');

        $product->rejected_note = $reason;
        $product->isApproved    = -1;
        $product->save();

        NotificaitonContoller::store('has Rejected due to ' . $reason, [$role], $product->id);
        ActivityConroller::create($product->id, 'supervisor', 'reject');

        return redirect()->back()->with('rejected', 'Product has been rejected');
    }
}
