<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Colors;
use App\Helpers\StatusHelper;
use App\Http\Requests\StoreProductSelectionRequest;
use App\Image;
use App\Product;
use App\ProductLocation;
use App\Setting;
use App\Stage;
use App\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class ProductSelectionController extends Controller
{
    public function index(): View
    {
        $products = Product::where('stock', '>=', 1)->latest()
            ->withMedia(config('constants.media_tags'))
            ->with('suppliers')
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])
            ->paginate(Setting::get('pagination'));

        $roletype = 'Selection';

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple'])
            ->selected(1)
            ->renderAsDropdown();
        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors)->all();
        $stage = new Stage;
        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'roletype', 'category_selection', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function manualImageUpload(Request $request): View
    {
        $products = Product::where('status_id', '=', StatusHelper::$manualImageUpload)
            ->latest()
            ->withMedia(config('constants.media_tags'));

        $term = $request->input('term');
        if (trim($term) != '') {
            $products = $products->where(function ($query) use ($term) {
                $query->orWhere('sku', 'LIKE', '%'.$term.'%')
                    ->orWhere('id', 'LIKE', '%'.$term.'%');
                if ($term == -1) {
                    $query = $query->orWhere('isApproved', -1);
                }

                $brand = Brand::where('name', 'LIKE', '%'.$term.'%')->first();
                if ($brand) {
                    $query = $query->orWhere('brand', '=', $brand->id);
                }

                $category = Category::where('name', 'LIKE', '%'.$term.'%')->first();
                if ($category) {
                    $query = $query->orWhere('category', '=', $category->id);
                }
            });
        }

        if ($request->get('category') != null && $request->get('category') != 1) {
            $category_children = [];
            $category = $request->get('category');
            $is_parent = Category::isParent($category);

            if ($is_parent) {
                $childs = Category::find($category)->childs()->get();

                foreach ($childs as $child) {
                    $is_parent = Category::isParent($child->id);

                    if ($is_parent) {
                        $children = Category::find($child->id)->childs()->get();

                        foreach ($children as $chili) {
                            array_push($category_children, $chili->id);
                        }
                    } else {
                        array_push($category_children, $child->id);
                    }
                }
            } else {
                array_push($category_children, $category);
            }

            $products = $products->whereIn('category', $category_children);
        }

        if ($request->get('brand')) {
            $products = $products->where('brand', $request->get('brand'));
        }

        if ($request->get('color')) {
            $products = $products->where('color', $request->get('color'));
        }

        if ($request->get('supplier')) {
            $products = $products->whereRaw('products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id = '.$request->get('supplier').')');
        }

        if ($request->get('size')) {
            $products = $products->whereNotNull('size')->where(function ($query) use ($request) {
                $query->where('size', $request->get('size'))->orWhere('size', 'LIKE', '%'.$request->get('size').',')->orWhere('size', 'LIKE', '%,'.$request->get('size').',%');
            });
        }

        $products = $products->paginate(Setting::get('pagination'));

        $stage = new Stage;

        $category_selection = Category::attr(['name' => 'category', 'class' => 'form-control select-multiple'])
            ->selected(request()->get('category', 1))
            ->renderAsDropdown();

        $media_tags = config('constants.media_tags');
        $suppliers = DB::select('
                        SELECT id, supplier
                        FROM suppliers

                        INNER JOIN (
                            SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
                            ) as product_suppliers
                        ON suppliers.id = product_suppliers.supplier_id
                ');
        $brands = Brand::getAll();

        return view('productselection.manual-image-upload', compact('products', 'stage', 'category_selection', 'media_tags', 'suppliers', 'brands'));
    }

    public function sList(): View
    {
        $productselection = Product::latest()->withMedia(config('constants.media_tags'))->paginate(Setting::get('pagination'));
        $media_tags = config('constants.media_tags');

        return view('productselection.list', compact('productselection', 'media_tags'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        $locations = ProductLocation::pluck('name', 'name');
        $suppliers = Supplier::select(['id', 'supplier'])->get();

        return view('productselection.create', [
            'locations' => $locations,
            'suppliers' => $suppliers,
        ]);
    }

    public function show(Product $productselection): View
    {
        return view('productselection.show', compact('productselection'));
    }

    public function store(StoreProductSelectionRequest $request, Stage $stage): RedirectResponse
    {

        $productselection = new Product;
        $productselection->sku = $request->input('sku');
        $productselection->size = $request->input('size');
        $productselection->size_eu = $request->input('size_eu');
        $productselection->price = $request->input('price');
        $productselection->supplier_link = $request->input('supplier_link');
        $productselection->location = $request->input('location');
        $productselection->brand = $request->input('brand');
        $productselection->last_selector = Auth::id();

        $productselection->stage = $stage->get('Selection');
        $productselection->stock = 1;

        if (! empty($productselection->brand) && ! empty($productselection->price)) {
            $productselection->price_inr = $this->euroToInr($productselection->price, $productselection->brand);
            $productselection->price_inr_special = $this->calculateSpecialDiscount($productselection->price_inr, $productselection->brand);
        } else {
            $productselection->price_inr_special = $request->price_inr_special;
        }

        $productselection->save();

        if ($request->supplier) {
            $productselection->suppliers()->attach($request->supplier);
        }

        $productselection->detachMediaTags(config('constants.media_tags'));
        $media = MediaUploader::fromSource($request->file('image'))
            ->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
            ->upload();
        $productselection->attachMedia($media, config('constants.media_tags'));

        NotificaitonContoller::store('has selected', ['Searchers'], $productselection->id);

        ActivityConroller::create($productselection->id, 'selection', 'create');

        return redirect()->route('productselection.index')
            ->with('success', 'Selection created successfully.');
    }

    public function edit(Product $productselection)
    {
        if ($productselection->isApproved == 1) {
            return redirect()->route('products.show', $productselection->id);
        }

        $locations = ProductLocation::pluck('name', 'name');
        $suppliers = Supplier::select(['id', 'supplier'])->get();
        $media_tags = config('constants.media_tags');
        if (request()->get('open_from')) {
            return view('productselection.edit-from', compact('productselection', 'locations', 'suppliers', 'media_tags'));
        }

        return view('productselection.edit', compact('productselection', 'locations', 'suppliers', 'media_tags'));
    }

    public function update(Request $request, Product $productselection): RedirectResponse
    {
        $validations = [
            'sku' => 'required|unique:products,sku,'.$productselection->id,
        ];

        if ($request->input('oldImage') != 0) {
            $validations['image'] = 'required | mimes:jpeg,bmp,png,jpg';
        }

        $this->validate($request, $validations);

        $productselection->sku = $request->input('sku');
        $productselection->size = $request->input('size');
        $productselection->size_eu = $request->input('size_eu');
        $productselection->price = $request->input('price');
        $productselection->status_id = $request->input('status_id');
        $productselection->supplier_link = $request->input('supplier_link');
        $productselection->location = $request->input('location');
        $productselection->brand = $request->input('brand');
        $productselection->last_selector = Auth::id();

        if (! empty($productselection->brand) && ! empty($productselection->price)) {
            $productselection->price_inr = $this->euroToInr($productselection->price, $productselection->brand);
            $productselection->price_inr_special = $this->calculateSpecialDiscount($productselection->price_inr, $productselection->brand);
        } else {
            $productselection->price_inr_special = $request->price_inr_special;
        }

        if ($request->oldImage > 0) {
            self::replaceImage($request, $productselection);
        } elseif ($request->oldImage == -1) {
            $media = MediaUploader::fromSource($request->file('image'))
                ->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
                ->upload();
            $productselection->attachMedia($media, config('constants.media_tags'));
        }

        $productselection->save();

        if ($request->supplier) {
            $productselection->suppliers()->detach();
            $productselection->suppliers()->attach($request->supplier);
        }

        NotificaitonContoller::store('has updated', ['Searchers'], $productselection->id);

        return redirect()->back()
            ->with('success', 'Selection updated successfully');
    }

    public function replaceImage($request, $productselection)
    {
        if ($request->input('oldImage') != 0) {
            $results = Media::where('id', $request->input('oldImage'))->get();

            $results->each(function ($media) {
                Image::trashImage($media->basename);
                $media->delete();
            });

            if (! empty($request->file('image'))) {
                $media = MediaUploader::fromSource($request->file('image'))
                    ->toDirectory('product/'.floor($productselection->id / config('constants.image_per_folder')))
                    ->upload();
                $productselection->attachMedia($media, config('constants.media_tags'));
            }
        }
    }

    public function euroToInr($price, $brand)
    {
        $euro_to_inr = BrandController::getEuroToInr($brand);

        if (! empty($euro_to_inr)) {
            $inr = $euro_to_inr * $price;
        } else {
            $inr = Setting::get('euro_to_inr') * $price;
        }

        return round($inr, -3);
    }

    public function calculateSpecialDiscount($price, $brand)
    {
        $dis_per = BrandController::getDeductionPercentage($brand);

        $dis_price = $price - ($price * $dis_per) / 100;

        return round($dis_price, -3);
    }

    public function emailTplSet(Request $request)
    {
        $product_ids = $request->input('product_ids');
        $product_ids = explode(',', $product_ids);

        $productData = product::whereIn('id', $product_ids)->withMedia(config('constants.media_tags'))->get();

        dd($productData);
    }
}
