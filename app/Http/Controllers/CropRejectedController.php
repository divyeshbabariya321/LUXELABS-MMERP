<?php

namespace App\Http\Controllers;

use DataTables;
use App\RejectedImages;
use Illuminate\Http\Request;

class CropRejectedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RejectedImages::query();
            $query->select('rejected_images.*');
            $query->with(['product', 'store_website', 'user']);
            $query->where('rejected_images.status', 0);

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    $product_name = (isset($row->product)) ? $row->product->name : '';

                    return $product_name;
                })
                ->addColumn('product_sku', function ($row) {
                    $product_sku = (isset($row->product)) ? $row->product->sku : '';

                    return $product_sku;
                })
                ->addColumn('store_website_title', function ($row) {
                    $store_website_title = (isset($row->store_website)) ? $row->store_website->title : '';

                    return $store_website_title;
                })
                ->addColumn('rejected_by', function ($row) {
                    $rejected_by = (isset($row->user)) ? $row->user->name : '';

                    return $rejected_by;
                })
                ->make(true);
        }

        return view('crop-rejected.index');
    }
}
