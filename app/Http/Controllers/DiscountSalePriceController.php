<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use App\StoreWebsite;
use App\StoreWebsiteSalesPrice;
use App\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscountSalePriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $discountsaleprice = StoreWebsiteSalesPrice::select('store_website_sales_prices.*', 'suppliers.supplier')
            ->leftJoin('suppliers', 'store_website_sales_prices.supplier_id', 'suppliers.id');

        if ($request->type != '') {
            $discountsaleprice->where('type', $request->type);
        }
        if ($request->type_id != '') {
            $discountsaleprice->where('type_id', $request->type_id);
        }
        if ($request->supplier != '') {
            $discountsaleprice->where('supplier_id', $request->supplier);
        }
        $discountsaleprice = $discountsaleprice->get();
        $discountsaleprice->each(function ($price) {
            switch ($price->type) {
                case 'brand':
                    $brand = Brand::find($price->type_id);
                    $price->brand_name = $brand ? $brand->name : null;
                    break;
                case 'product':
                    $product = Product::find($price->type_id);
                    $price->product_name = $product ? $product->name : null;
                    break;
                case 'category':
                    $category = Category::find($price->type_id);
                    $price->category_title = $category ? $category->title : null;
                    break;
                case 'store_website':
                    $store_website = StoreWebsite::find($price->type_id);
                    $price->store_website_title = $store_website ? $store_website->title : null;
                    break;
                default:
                    $price->default_name = null;
            }
        });
        $supplier = Supplier::get();
        if ($request->ajax()) {
            return view('discountsaleprice.index_page', [
                'discountsaleprice' => $discountsaleprice,
                'supplier' => $supplier,
            ]);
        } else {
            return view('discountsaleprice.index', [
                'discountsaleprice' => $discountsaleprice,
                'supplier' => $supplier,
            ]);
        }
    }

    public function type(Request $request)
    {
        $type = $request->type;
        $select = "<select class='form-control' name='type_id' required id='type_id'>";

        if ($type == 'brand') {
            $model_type = Brand::class;
            $rs = $model_type::get();
            foreach ($rs as $r) {
                $select .= "<option value='".$r->id."'>".$r->name.'</option>';
            }
        }
        if ($type == 'category') {
            $model_type = Category::class;
            $rs = $model_type::all();
            foreach ($rs as $r) {
                $select .= "<option value='".$r->id."'>".$r->title.'</option>';
            }
        }

        if ($type == 'product') {
            $model_type = Product::class;
            $rs = $model_type::get();
            foreach ($rs as $r) {
                $select .= "<option value='".$r->id."'>".$r->name.'</option>';
            }
        }

        if ($type == 'store_website') {
            $model_type = StoreWebsite::class;
            $rs = $model_type::get();
            foreach ($rs as $r) {
                $select .= "<option value='".$r->id."'>".$r->title.'</option>';
            }
        }
        $select .= '</select>';
        echo $select;
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', 'file']);
        $data['created_by'] = Auth::id();

        $id = $request->id;
        if ($id > 0) {
            StoreWebsiteSalesPrice::where('id', $id)->update($data);

            return redirect()->to('discount-sale-price')->withSuccess('You have successfully updated a record!');
        } else {
            StoreWebsiteSalesPrice::insert($data);

            return redirect()->to('discount-sale-price')->withSuccess('You have successfully added a record!');
        }
    }

    public function delete($id): RedirectResponse
    {
        StoreWebsiteSalesPrice::where('id', $id)->delete();

        return redirect()->to('discount-sale-price')->withSuccess('You have successfully deleted a record!');
    }
}
