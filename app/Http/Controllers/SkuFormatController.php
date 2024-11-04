<?php

namespace App\Http\Controllers;
use App\SkuFormatHistory;

use App\Http\Requests\UpdateSkuFormatRequest;
use App\Http\Requests\StoreSkuFormatRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Brand;
use DataTables;
use App\Category;
use App\SkuFormat;
use Illuminate\Http\Request;

class SkuFormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories         = Category::orderBy('title')->get();
        $brands             = Brand::orderBy('name')->get();
        $skus               = SkuFormat::all();
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2', 'id' => 'category'])->renderAsDropdown();

        return view('sku-format.index', compact('categories', 'brands', 'skus', 'category_selection'));
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
    public function store(StoreSkuFormatRequest $request): RedirectResponse
    {

        $sku               = new SkuFormat();
        $sku->category_id  = $request->category_id;
        $sku->brand_id     = $request->brand_id;
        $sku->sku_examples = $request->sku_examples;
        $sku->sku_format   = ($request->sku_format == null) ? '' : $request->sku_format;
        $sku->save();

        SkuFormatHistory::create([
            'sku_format_id' => $sku->id,
            'sku_format'    => $request->sku_format,
            'user_id'       => Auth::user()->id,
        ]);

        return redirect()->back()->withSuccess('You have successfully saved SKU');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SkuFormat $skuFormat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SkuFormat $skuFormat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\SkuFormat $skuFormat
     */
    public function update(UpdateSkuFormatRequest $request): JsonResponse
    {

        $sku               = SkuFormat::findorfail($request->id);
        $oldFormat         = $sku->sku_format;
        $sku->category_id  = $request->category_id;
        $sku->brand_id     = $request->brand_id;
        $sku->sku_examples = $request->sku_examples;
        $sku->sku_format   = ($request->sku_format == null) ? '' : $request->sku_format;
        $sku->update();

        SkuFormatHistory::create([
            'sku_format_id'  => $sku->id,
            'old_sku_format' => $oldFormat,
            'sku_format'     => $sku->sku_format,
            'user_id'        => Auth::user()->id,
        ]);

        return response()->json(['success' => 'success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SkuFormat $skuFormat)
    {
        //
    }

    public function getData(Request $request)
    {
        if (! empty($request->from_date)) {
            $skulogs = SkuFormat::select(['brand_id', 'category_id', 'sku_examples', 'sku_format'])->whereBetween('created_at', [$request->from_date, $request->to_date])->get();

            return Datatables::of($skulogs)
                ->addColumn('category', function ($skulogs) {
                    return '<h6>' . $skulogs->category->name . '</h6>';
                })
                ->addColumn('brand', function ($skulogs) {
                    return $skulogs->brand->name;
                })
                ->addColumn('actions', function ($skulogs) {
                    return '<button class=btn btn-default" onclick="editSKU(' . $skulogs->id . ')">Edit</button>';
                })
                ->rawColumns(['category'])
                ->rawColumns(['brand'])
                ->rawColumns(['actions'])
                ->make(true);
        } else {
            $skulogs = SkuFormat::select(['id', 'brand_id', 'category_id', 'sku_examples', 'sku_format']);

            return Datatables::of($skulogs)
                ->addColumn('category', function ($skulogs) {
                    return $skulogs->category->title;
                })
                ->addColumn('brand', function ($skulogs) {
                    return $skulogs->brand->name;
                })
                ->addColumn('actions', function ($skulogs) {
                    return '<button class=btn btn-default" onclick="editSKU(' . $skulogs->id . ')">Edit</button><button class=btn btn-default" onclick="showHistory(' . $skulogs->id . ')">History</button>';
                })
                ->rawColumns(['category'])
                ->rawColumns(['brand'])
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $history = SkuFormatHistory::where('sku_format_id', $request->id)->join('users as u', 'u.id', 'sku_format_histories.user_id')
            ->orderByDesc('sku_format_histories.created_at')
            ->select(['sku_format_histories.*', 'u.name as user_name'])
            ->get();

        return response()->json(['code' => 200, 'data' => $history]);
    }
}
