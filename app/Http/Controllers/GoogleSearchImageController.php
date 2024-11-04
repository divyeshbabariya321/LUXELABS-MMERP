<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\GoogleSearchImage;
use App\GoogleSearchRelatedImage;
use App\GoogleServer;
use App\Helpers\StatusHelper;
use App\Http\Requests\CropGoogleSearchImageRequest;
use App\Http\Requests\SearchImageOnGoogleGoogleSearchImageRequest;
use App\LogGoogleCse;
use App\LogRequest;
use App\Mediables;
use App\Product;
use App\ProductLocation;
use App\QuickSellGroup;
use App\ScrapeQueues;
use App\Services\Search\TinEye;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use seo2websites\GoogleVision\GoogleVisionHelper;

class GoogleSearchImageController extends Controller
{
    /**
     * Display a Google Search Image
     */
    public function index(Request $request): View
    {
        $data = [];
        $term = $request->input('term');
        $quickProduct = request('quick_product', ! empty($request->all()) ? false : 'true');
        $request->request->add(['quick_product' => $quickProduct]);
        $data['term'] = $term;

        $productQuery = (new Product)->newQuery()->latest();
        if (isset($request->status_id) && is_array($request->status_id) && count($request->status_id) > 0) {
            $productQuery = $productQuery->whereIn('status_id', $request->status_id);
            $data['status_id'] = $request->status_id;
        }

        if ($request->brand) {
            if ($request->brand[0] != null) {
                $productQuery = $productQuery->whereIn('brand', $request->brand);
                $data['brand'] = $request->brand[0];
            }
        }

        if ($request->color) {
            if ($request->color[0] != null) {
                $productQuery = $productQuery->whereIn('color', $request->color);
                $data['color'] = $request->color[0];
            }
        }

        if (isset($request->category) && $request->category[0] != 1) {
            $is_parent = Category::isParent($request->category[0]);
            $category_children = [];

            if ($is_parent) {
                $childs = Category::find($request->category[0])->childs()->get();

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
                array_push($category_children, $request->category[0]);
            }

            $productQuery = $productQuery->whereIn('category', $category_children);

            $data['category'] = $request->category[0];
        }

        if (isset($request->price) && $request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[0];
            $max = $exploded[1];

            if ($min != '0' || $max != '10000000') {
                $productQuery = $productQuery->whereBetween('price_inr_special', [$min, $max]);
            }

            $data['price'][0] = $min;
            $data['price'][1] = $max;
        }

        if ($request->location) {
            if ($request->location[0] != null) {
                $productQuery = $productQuery->whereIn('location', $request->location);
                $data['location'] = $request->location[0];
            }
        }

        if ($request->no_locations) {
            $productQuery = $productQuery->whereNull('location');
        }

        if (trim($term) != '') {
            $productQuery = $productQuery->where(function ($query) use ($term) {
                $query->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%");

                if ($term == -1) {
                    $query->orWhere('isApproved', -1);
                }

                if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                    $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                if (Category::where('title', 'LIKE', "%$term%")->first()) {
                    $category_id = Category::where('title', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('category', $category_id);
                }
            });
        }

        $data['quick_product'] = false;
        if ($quickProduct === 'true') {
            $data['quick_product'] = true;
            $productQuery = $productQuery->where('quick_product', 1);
        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', '%'.$request->get('shoe_size').'%');
        }

        if (! empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $productQuery = $productQuery->whereRaw('(id in (select product_id from product_quicksell_groups where quicksell_group_id in ('.implode(',', $request->quick_sell_groups).') ))');
        }

        // Get all product IDs
        $productIdsSystem = $productQuery->pluck('id')->toArray();
        $countSystem = $productQuery->count();

        $data['products'] = $productQuery->join('mediables', function ($query) {
            $query->on('mediables.mediable_id', 'products.id')->where('mediable_type', Product::class);
        })
            ->groupBy('products.id')
            ->paginate(Setting::get('pagination'));
        $data['locations'] = ProductLocation::pluck('name');
        $data['quick_sell_groups'] = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();

        $data['all_products_system'] = $productIdsSystem;
        $data['count_system'] = $countSystem;
        $data['media_tags'] = config('constants.media_tags');

        return view('google_search_image.index', $data);
    }

    public function crop(CropGoogleSearchImageRequest $request)
    {

        $product_id = $request->get('product_id');
        $product = Product::where('id', $product_id)->first();
        if ($product) {
            $media = $product->media()->first();

            $data['image'] = '';

            if ($media) {
                $data['image'] = getMediaUrl($media);
                $data['media_id'] = $media->id;
                $data['product_id'] = $product_id;
            }
            if (! empty($data['image'])) {
                return view('google_search_image.crop', $data);
            }
        }

        return redirect()->back()->with('message', 'Image Not found!!');
    }

    public function searchImageOnGoogle(SearchImageOnGoogleGoogleSearchImageRequest $request): JsonResponse
    {

        $product_id = $request->get('product_id');
        $product = Product::where('id', $product_id)->first();
        if ($product) {
            $media = $product->media()->first();

            if ($media) {
                $path = $media->getAbsolutePath();
                $url = getMediaUrl($media);

                $img = \Image::make($media->getAbsolutePath());
                $imageWidth = $img->width();
                $imageHeight = $img->height();
                $width = $request->get('width', null);
                $height = $request->get('height', null);
                $x = $request->get('x', null);
                $y = $request->get('y', null);
                if ($height != null && $width != null && $x != null && $y != null) {
                    //Checking if width and height are same
                    if ($imageWidth != $width[0] || $imageHeight != $height[0]) {
                        $img->crop($width[0], $height[0], $x[0], $y[0]);

                        if (! is_dir(public_path().'/tmp_images')) {
                            mkdir(public_path().'/tmp_images', 0777, true);
                        }
                        $path = public_path().'/tmp_images/crop_'.$media->getBasenameAttribute();
                        $url = '/tmp_images/crop_'.$media->getBasenameAttribute();
                        $img->save($path);
                    }
                }
            }
        }

        if ($path) {
            // Set empty array for product image
            $productImage = [];

            // Try TinEye
            $tinEye = new TinEye;
            $results = $tinEye->searchByImage($path, true);

            // Does TinEye have results? Otherwise try Google Vision
            if (isset($results['pages']) && count($results['pages']) > 0) {
                $productImage[$url] = $results;

            } else {
                $productImage[$url] = GoogleVisionHelper::getImageDetails($path);
            }

            $product = Product::where('id', $product_id)->first();

            foreach ($productImage as $key => $z) {
                if ($z) {
                    $file = asset($key);
                    $file_name = Str::random(10).rand(1000, 9999).Str::random(4).'.jpg';
                    Storage::disk('s3')->put('search_crop_images/'.$file_name, file_get_contents($file));

                    $search_img = new GoogleSearchImage;
                    $search_img->user_id = Auth::id();
                    $search_img->product_id = $product_id;
                    $search_img->crop_image = 'search_crop_images/'.$file_name;
                    $search_img->save();

                    for ($i = 0; $i < count($z['pages']); $i++) {
                        $google_img = new GoogleSearchRelatedImage;
                        $google_img->google_search_image_id = $search_img->id;
                        $google_img->google_image = $z['pages_media'][$i];
                        $google_img->image_url = $z['pages'][$i];
                        $google_img->save();
                    }
                } else {
                    return response()->json(['status' => false, 'message' => 'Image Not Found']);
                }
            }

            return response()->json(['status' => true, 'message' => 'Search Successfully']);

            // Return view
        } else {
            return response()->json(['status' => false, 'message' => 'Please Select Products']);
        }
    }

    public function details(Request $request): View
    {
        $url = $request->get('url');
        $product_id = $request->get('product_id');
        $productImage = [];
        if (! empty($url)) {
            $productImage[$url] = GoogleVisionHelper::getImageDetails($url);
            if (! empty($productImage)) {
                $product = Product::where('id', $product_id)->first();

                return view('google_search_image.details', compact(['productImage', 'product_id', 'product']));
            }
        }

        abort(403, 'Sorry , it looks like there is no result from the request.');
    }

    public function product(Request $request): View
    {
        if ($request->isMethod('post')) {
            $images = $request->post('images', []);
            $productId = $request->post('product_id', 0);

            $product = Product::where('id', $productId)->first();

            if ($product) {
                $imagesSave = false;
                if (! empty($images)) {
                    foreach ($images as $image) {
                        $file = @file_get_contents($image);
                        if (! empty($file)) {
                            $media = MediaUploader::fromString($file)
                                ->toDirectory('product'.DIRECTORY_SEPARATOR.floor($product->id / config('constants.image_per_folder')))
                                ->useFilename(md5(date('Y-m-d H:i:s')))
                                ->upload();
                            $product->attachMedia($media, config('constants.media_tags'));
                            $imagesSave = true;
                        }
                    }
                }

                $product->status_id = 22;
                if ($imagesSave) {
                    StatusHelper::updateStatus($product, StatusHelper::$pendingVerificationGoogleTextSearch);
                    $product->status_id = StatusHelper::$pendingVerificationGoogleTextSearch;
                }

                $product->save();
            }
        }

        $revise = $request->get('revise', 0);

        $products = Product::where('products.stock', '>', 0);

        if ($revise == 1) {
            $products->where('status_id', StatusHelper::$manualImageUpload);
        } else {
            $products->where('status_id', StatusHelper::$unableToScrapeImages);
        }

        if ($request->has('supplier')) {
            $products = $products->join('product_suppliers as ps', 'ps.product_id', 'products.id');
            $products = $products->where('ps.supplier_id', $request->get('supplier'));
        }

        $productCount = $products->count();

        $product = $products->select(['products.*'])->orderByDesc('products.id')->first();

        $supplierList = Product::where('status_id', '14')
            ->where('products.stock', '>', 0)
            ->join('product_suppliers as ps', 'ps.product_id', 'products.id')
            ->join('suppliers as s', 's.id', 'ps.supplier_id')
            ->groupBy('s.id')
            ->select([DB::raw('count(*) as supplier_count'), 's.supplier', 's.id'])
            ->get()->toArray();

        $skippedSuppliers = Product::where('status_id', '22')
            ->where('products.stock', '>', 0)
            ->join('product_suppliers as ps', 'ps.product_id', 'products.id')
            ->join('suppliers as s', 's.id', 'ps.supplier_id')
            ->groupBy('s.id')
            ->select([DB::raw('count(*) as supplier_count'), 's.supplier', 's.id'])
            ->get()->toArray();

        $excelimporter = config('constants.excelimporter');
        $googleServer = GoogleServer::all();

        return view('google_search_image.product', compact(['product', 'productCount', 'supplierList', 'skippedSuppliers', 'excelimporter', 'googleServer']));
    }

    public function queue(Request $request): RedirectResponse
    {
        // Update product status
        $product = Product::findOrFail($request->product_id);

        // Update product
        $product->status_id = StatusHelper::$queuedForGoogleImageSearch;
        $product->save();

        // Create queue item
        $scrapeQueue = new ScrapeQueues;
        $scrapeQueue->product_id = (int) $request->product_id;
        $scrapeQueue->url = $request->url;
        $scrapeQueue->save();

        return redirect()->to('/google-search-image')->with('message', 'Product is queued');
    }

    public function getImageForMultipleProduct(Request $request): JsonResponse
    {
        $product = Product::findOrFail($request->id);
        $product->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
        $product->save();

        $media = $product->media()->first();

        if ($media) {
            $count = 0;
            $urls = GoogleVisionHelper::getImageDetails(getMediaUrl($media));

            if (isset($urls['pages'])) {
                foreach ($urls['pages'] as $url) {
                    if (stristr($url, '.gucci.') || stristr($url, '.farfetch.')) {
                        // Create queue item
                        $scrapeQueue = new ScrapeQueues;
                        $scrapeQueue->product_id = (int) $product->id;
                        $scrapeQueue->url = $url;
                        $scrapeQueue->save();
                        $count++;
                        break;
                    }
                }
            }
            if ($count == 0) {
                $product->status_id = StatusHelper::$googleImageSearchFailed;
                $product->save();
            } else {
                StatusHelper::updateStatus($product, StatusHelper::$queuedForGoogleImageSearch);
            }
        }

        return response()->json(['success' => 'true'], 200);
    }

    public function cropImageSequence(Request $request): JsonResponse
    {
        $id = $request->id;
        $sequence = $request->sequence;

        //Updating Product Sequence
        $product = Product::findOrFail($id);

        //Getting Product Media
        $media = $product->media()->first();

        if ($media) {

            $img = \Image::make($media->getAbsolutePath());
            $imageWidth = $img->width();
            $imageHeight = $img->height();
            if ($sequence == 8) {
                $newWidth = (int) ($imageWidth / 4);
                $newHeight = (int) ($imageHeight / 2);

                $count = 0;
                $axisCount = 0;
                for ($i = 0; $i < $sequence; $i++) {
                    $img = \Image::make($media->getAbsolutePath());
                    if ($count < 4) {
                        $x = $newWidth * $i;
                        $y = 0;
                        $img->crop($newWidth, $newHeight, $x, $y);

                        if (! is_dir(public_path().'/tmp_images')) {
                            mkdir(public_path().'/tmp_images', 0777, true);
                        }
                        $path = public_path().'/tmp_images/crop_'.$i.$media->getBasenameAttribute();
                        $url = '/tmp_images/crop_'.$i.$media->getBasenameAttribute();
                        $img->save($path);

                        //Product save
                        $newProduct = new Product;
                        $newProduct->name = $product->name;
                        $newProduct->sku = '-'.$product->sku;
                        $newProduct->size = $product->size;
                        $newProduct->brand = $product->brand;
                        $newProduct->color = $product->color;
                        $newProduct->supplier = $product->supplier;
                        $newProduct->price = $product->price;
                        $newProduct->quick_product = 1;
                        $newProduct->price_inr = $product->price_inr;
                        $newProduct->price_inr_special = $product->price_inr_special;
                        $newProduct->save();
                        //Attach Media To Post
                        $newMedia = MediaUploader::fromSource($path)
                            ->toDirectory('product/'.floor($newProduct->id / config('constants.image_per_folder')))
                            ->upload();
                        $newProduct->attachMedia($newMedia, config('constants.media_tags'));

                        //Updating New Product Status
                        $newProduct->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
                        $newProduct->save();

                        $mediaUrlCount = 0;
                        if (isset($newUrls['pages'])) {
                            foreach ($newUrls['pages'] as $url) {
                                if (stristr($url, '.gucci.') || stristr($url, '.farfetch.')) {
                                    // Create queue item
                                    $scrapeQueue = new ScrapeQueues;
                                    $scrapeQueue->product_id = (int) $newProduct->id;
                                    $scrapeQueue->url = $url;
                                    $scrapeQueue->save();
                                    $mediaUrlCount++;
                                    break;
                                }
                            }
                        }
                        //If Page Is Not Found
                        if ($mediaUrlCount == 0) {
                            $newProduct->status_id = StatusHelper::$googleImageSearchFailed;
                            $newProduct->save();
                        } else {
                            StatusHelper::updateStatus($newProduct, StatusHelper::$queuedForGoogleImageSearch);
                        }
                        $count++;
                    } else {
                        $x = $newWidth * $axisCount;
                        $y = $newHeight;
                        $img->crop($newWidth, $newHeight, $x, $y);

                        if (! is_dir(public_path().'/tmp_images')) {
                            mkdir(public_path().'/tmp_images', 0777, true);
                        }
                        $path = public_path().'/tmp_images/crop_'.$i.$media->getBasenameAttribute();
                        $url = '/tmp_images/crop_'.$i.$media->getBasenameAttribute();
                        $img->save($path);

                        //Product save
                        $newProduct = new Product;
                        $newProduct->name = $product->name;
                        $newProduct->sku = '-'.$product->sku;
                        $newProduct->size = $product->size;
                        $newProduct->brand = $product->brand;
                        $newProduct->color = $product->color;
                        $newProduct->supplier = $product->supplier;
                        $newProduct->price = $product->price;
                        $newProduct->quick_product = 1;
                        $newProduct->price_inr = $product->price_inr;
                        $newProduct->price_inr_special = $product->price_inr_special;
                        $newProduct->save();

                        //Attach Media To Post
                        $newMedia = MediaUploader::fromSource($path)
                            ->toDirectory('product/'.floor($newProduct->id / config('constants.image_per_folder')))
                            ->upload();
                        $newProduct->attachMedia($newMedia, config('constants.media_tags'));

                        //Updating New Product Status
                        $newProduct->status_id = StatusHelper::$isBeingScrapedWithGoogleImageSearch;
                        $newProduct->save();

                        //Process Image For Google Search
                        $newUrls = GoogleVisionHelper::getImageDetails(getMediaUrl($newMedia));

                        $mediaUrlCount = 0;
                        if (isset($urls['pages'])) {
                            foreach ($urls['pages'] as $url) {
                                if (stristr($url, '.gucci.') || stristr($url, '.farfetch.')) {
                                    // Create queue item
                                    $scrapeQueue = new ScrapeQueues;
                                    $scrapeQueue->product_id = (int) $newProduct->id;
                                    $scrapeQueue->url = $url;
                                    $scrapeQueue->save();
                                    $mediaUrlCount++;
                                    break;
                                }
                            }
                        }
                        //If Page Is Not Found
                        if ($mediaUrlCount == 0) {
                            $newProduct->status_id = StatusHelper::$googleImageSearchFailed;
                            $newProduct->save();
                        } else {
                            StatusHelper::updateStatus($newProduct, StatusHelper::$queuedForGoogleImageSearch);
                        }

                        $axisCount++;
                    }
                }
                //Delete Old Product
                $product->deleted_at = now();
                $product->save();
            }
        }

        return response()->json(['success' => 'true'], 200);
    }

    public function updateProductStatus(Request $request): JsonResponse
    {
        $id = $request->id;
        $type = $request->type;

        $product = Product::find($id);

        $statusMap = [
            'approve' => StatusHelper::$AI,
            'reject' => StatusHelper::$googleImageSearchManuallyRejected,
            'textapprove' => StatusHelper::$AI,
            'textreject' => StatusHelper::$googleTextSearchManuallyRejected,
        ];

        $product->status_id = isset($statusMap[$type]) ? $statusMap[$type] : $statusMap['textreject'];
        $product->save();

        return response()->json(['success' => 'true'], 200);
    }

    public function nultipeImageProduct(Request $request): View
    {
        $data = [];
        $term = $request->input('term');

        if (request('status_id') != null) {
            $statusId = $request->status_id;
        } else {
            if (empty($request->all()) || isset($request->page)) {
                $statusId = [StatusHelper::$unableToScrapeImages];
            }
        }

        $data['term'] = $term;

        $productQuery = (new Product)->newQuery()->latest();

        if (! isset($statusId)) {
            $statusId = null;
        }

        if ($statusId != null) {
            $productQuery = $productQuery->whereIn('status_id', $statusId);
            $data['status_id'] = $statusId;
        }

        if ($request->brand[0] != null) {
            $productQuery = $productQuery->whereIn('brand', $request->brand);
            $data['brand'] = $request->brand[0];
        }

        if ($request->supplier[0] != null) {
            $productQuery = $productQuery->whereIn('supplier', $request->supplier);
            $data['supplier'] = $request->supplier[0];
        }

        if ($request->color[0] != null) {
            $productQuery = $productQuery->whereIn('color', $request->color);
            $data['color'] = $request->color[0];
        }

        if (isset($request->category) && $request->category[0] != 1) {
            $is_parent = Category::isParent($request->category[0]);
            $category_children = [];

            if ($is_parent) {
                $childs = Category::find($request->category[0])->childs()->get();

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
                array_push($category_children, $request->category[0]);
            }

            $productQuery = $productQuery->whereIn('category', $category_children);

            $data['category'] = $request->category[0];
        }

        if (isset($request->price) && $request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[0];
            $max = $exploded[1];

            if ($min != '0' || $max != '10000000') {
                $productQuery = $productQuery->whereBetween('price_inr_special', [$min, $max]);
            }

            $data['price'][0] = $min;
            $data['price'][1] = $max;
        }

        if ($request->location[0] != null) {
            $productQuery = $productQuery->whereIn('location', $request->location);
            $data['location'] = $request->location[0];
        }

        if ($request->no_locations) {
            $productQuery = $productQuery->whereNull('location');
        }

        if (trim($term) != '') {
            $productQuery = $productQuery->where(function ($query) use ($term) {
                $query->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%");

                if ($term == -1) {
                    $query->orWhere('isApproved', -1);
                }

                if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                    $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                if (Category::where('title', 'LIKE', "%$term%")->first()) {
                    $category_id = Category::where('title', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('category', $category_id);
                }
            });
        }

        if ($request->no_locations === 'true') {
            $productQuery = $productQuery->where('quick_product', 1);
        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', '%'.$request->get('shoe_size').'%');
        }

        if (! empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $productQuery = $productQuery->whereRaw('(id in (select product_id from product_quicksell_groups where quicksell_group_id in ('.implode(',', $request->quick_sell_groups).') ))');
        }

        // Get all product IDs
        $productIdsSystem = $productQuery->pluck('id')->toArray();
        $countSystem = $productQuery->count();

        if (isset($statusId)) {
            $data['products'] = $productQuery
                ->groupBy('products.id')
                ->paginate(Setting::get('pagination'));
        } else {
            $data['products'] = $productQuery->join('mediables', function ($query) {
                $query->on('mediables.mediable_id', 'products.id')->where('mediable_type', Product::class);
            })
                ->groupBy('products.id')
                ->paginate(Setting::get('pagination'));
        }

        $data['locations'] = ProductLocation::pluck('name');
        $data['quick_sell_groups'] = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();

        $data['all_products_system'] = $productIdsSystem;
        $data['count_system'] = $countSystem;

        //getting top url
        $logs = LogGoogleCse::groupBy('image_url')->get();
        $logArray = [];
        foreach ($logs as $log) {
            $url = $log->image_url;
            $website = explode('/', $url);
            $website = $website[2]; //assuming that the url starts with https:// or https://
            if (! in_array($website, $logArray)) {
                array_push($logArray, $website);
            }
        }
        $counter = 0;
        foreach ($logArray as $log) {
            $count = LogGoogleCse::where('image_url', 'like', '%'.$log.'%')->count();
            $finalArray[] = [$log => $count];
            if ($counter == 20) {
                break;
            }
            $counter++;
        }
        if (isset($finalArray)) {
            $data['top_url'] = $finalArray;
        } else {
            $data['top_url'] = [];
        }
        $data['google_text_search'] = config('constants.google_text_search');
        $data['media_tags'] = config('constants.media_tags');

        $data['brands'] = Brand::all();

        return view('google_search_image.multiple-image-text', $data);
    }

    public function multipleImageStore(Request $request): JsonResponse
    {
        $id = $request->id;
        $product = Product::find($id);

        if ($product->brands != null) {
            $brand = $product->brands->name;
            if ($product->brands->googleServer != null) {
                $key = $product->brands->googleServer->key;
            } else {
                $key = null;
            }
        } else {
            $key = null;
            $brand = '';
        }

        $googleServer = config('env.GOOGLE_CUSTOM_SEARCH');

        //Replace Google Server Key
        if ($key != null) {
            $re = '/([?&]cx)=([^#&]*)/';
            preg_match($re, $googleServer, $match);
            $googleServer = str_replace($match[2], $key, $googleServer);
        }

        //Array Of Multiple Product Detail Search
        $keywords = [
            implode(',', array_filter([$brand, $product->name, $product->color, $product->sku])),
            implode(',', array_filter([$product->name, $product->color, $product->sku])),
            implode(',', array_filter([$product->name, $product->sku])),
            $product->name,
            $product->sku,
        ];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        //Looping Through Keywords
        foreach ($keywords as $keyword) {
            $link = $googleServer.'&q='.urlencode($keyword).'&searchType=image&imgSize=large';

            $handle = curl_init();

            // Set the url
            curl_setopt($handle, CURLOPT_URL, $link);
            // Set the result output to be a string.
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($handle);
            $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $link, 'POST', json_encode([]), json_decode($output), $httpcode, GoogleSearchImageController::class, 'multipleImageStore');

            curl_close($handle);

            $list = json_decode($output);

            if ($list == null) {
                continue;
            }

            if (isset($list->searchInformation)) {
                if ($list->searchInformation->totalResults == 0) {
                    continue;
                }
            }

            if (! isset($list->items)) {
                continue;
            }
            $links = $list->items;

            //here save log

            $count = 0;
            foreach ($links as $link) {
                $image = $link->link;

                $jpg = \Image::make($image)->encode('jpg');
                $filename = substr($image, strrpos($image, '/'));
                $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', $filename);
                $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($product->id / 10000).'/'.$product->id)->useFilename($filename)->upload();
                $product->attachMedia($media, config('constants.google_text_search'));

                $responseString = 'Link: '.$link->link.'\n Display Link: '.$link->displayLink.'\n Title : '.$link->title.'\n Image Details: '.$link->image->contextLink.' Height:'.$link->image->height.' Width : '.$link->image->width.'\n ThumbnailLink '.$link->image->thumbnailLink;

                $log = new LogGoogleCse;
                $log->image_url = $image;
                $log->keyword = $keyword;
                $log->response = $responseString;
                $log->save();

                $count++;
            }
            $parameter['searchImage'] = [
                'product_id' => $product->id,
                'image_url' => $product->crop_image,

            ];

            //If Page Is Not Found
            if ($count == 0) {
                $product->status_id = StatusHelper::$googleTextSearchFailed;
                $product->save();
            } else {
                StatusHelper::updateStatus($product, StatusHelper::$pendingVerificationGoogleTextSearch);
                break;
            }
        }

        return response()->json(['success' => 'true'], 200);
    }

    public function approveProduct(Request $request): View
    {
        $data = [];
        $term = $request->input('term');

        if (request('status_id') != null) {
            $statusId = $request->status_id;
        } else {
            if (empty($request->all()) || isset($request->page)) {
                $statusId = [StatusHelper::$pendingVerificationGoogleTextSearch];
            }
        }

        $data['term'] = $term;

        $productQuery = (new Product)->newQuery()->latest();

        if (! isset($statusId)) {
            $statusId = null;
        }

        if ($statusId != null) {
            $productQuery = $productQuery->whereIn('status_id', $statusId);
            $data['status_id'] = $statusId;
        }

        if ($request->brand[0] != null) {
            $productQuery = $productQuery->whereIn('brand', $request->brand);
            $data['brand'] = $request->brand[0];
        }

        if ($request->supplier[0] != null) {
            $productQuery = $productQuery->whereIn('supplier', $request->supplier);
            $data['supplier'] = $request->supplier[0];
        }

        if ($request->color[0] != null) {
            $productQuery = $productQuery->whereIn('color', $request->color);
            $data['color'] = $request->color[0];
        }

        if (isset($request->category) && $request->category[0] != 1) {
            $is_parent = Category::isParent($request->category[0]);
            $category_children = [];

            if ($is_parent) {
                $childs = Category::find($request->category[0])->childs()->get();

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
                array_push($category_children, $request->category[0]);
            }

            $productQuery = $productQuery->whereIn('category', $category_children);

            $data['category'] = $request->category[0];
        }

        if (isset($request->price) && $request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[0];
            $max = $exploded[1];

            if ($min != '0' || $max != '10000000') {
                $productQuery = $productQuery->whereBetween('price_inr_special', [$min, $max]);
            }

            $data['price'][0] = $min;
            $data['price'][1] = $max;
        }

        if ($request->location[0] != null) {
            $productQuery = $productQuery->whereIn('location', $request->location);
            $data['location'] = $request->location[0];
        }

        if ($request->no_locations) {
            $productQuery = $productQuery->whereNull('location');
        }

        if (trim($term) != '') {
            $productQuery = $productQuery->where(function ($query) use ($term) {
                $query->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%");

                if ($term == -1) {
                    $query->orWhere('isApproved', -1);
                }

                if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                    $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                if (Category::where('title', 'LIKE', "%$term%")->first()) {
                    $category_id = Category::where('title', 'LIKE', "%$term%")->first()->id;
                    $query->orWhere('category', $category_id);
                }
            });
        }

        if ($request->no_locations === 'true') {
            $productQuery = $productQuery->where('quick_product', 1);
        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', '%'.$request->get('shoe_size').'%');
        }

        if (! empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $productQuery = $productQuery->whereRaw('(id in (select product_id from product_quicksell_groups where quicksell_group_id in ('.implode(',', $request->quick_sell_groups).') ))');
        }

        // Get all product IDs
        $productIdsSystem = $productQuery->pluck('id')->toArray();
        $countSystem = $productQuery->count();

        if (isset($statusId)) {
            $data['products'] = $productQuery
                ->groupBy('products.id')
                ->paginate(Setting::get('pagination'));
        } else {
            $data['products'] = $productQuery->join('mediables', function ($query) {
                $query->on('mediables.mediable_id', 'products.id')->where('mediable_type', Product::class);
            })
                ->groupBy('products.id')
                ->paginate(Setting::get('pagination'));
        }

        $data['locations'] = ProductLocation::pluck('name');
        $data['quick_sell_groups'] = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();

        $data['all_products_system'] = $productIdsSystem;
        $data['count_system'] = $countSystem;
        $data['google_text_search'] = config('constants.google_text_search');

        return view('google_search_image.approve', $data);
    }

    public function approveTextGoogleImagesToProduct(Request $request): JsonResponse
    {
        $product_id = $request->id;
        $images = $request->selected;

        //Changed Selected Images For Product
        foreach ($images as $image) {
            Mediables::where('tag', config('constants.google_text_search'))->where('mediable_type', Product::class)->where('media_id', $image)
                ->limit(1)
                ->update(['tag' => config('constants.media_tags')[0]]);
        }

        //Change Product Status
        $product = Product::find($product_id);
        $product->status_id = StatusHelper::$AI;
        $product->save();

        return response()->json(['success' => 'true'], 200);
    }

    public function rejectProducts(Request $request): JsonResponse
    {
        $product = Product::find($request->id);
        $product->status_id = StatusHelper::$googleTextSearchManuallyRejected;
        $product->update();

        $results = $product->media()->get();
        $results->each(function ($media) {
            $media->delete();
        });

        return response()->json(['success' => 'true'], 200);
    }

    public function getProductFromImage(Request $request): Response
    {
        if (! is_dir(public_path().'/tmp_images')) {
            mkdir(public_path().'/tmp_images', 0777, true);
        }
        $path = public_path().'/tmp_images/crop_'.$request->file->getClientOriginalName();
        $url = '/tmp_images/crop_'.$request->file->getClientOriginalName();

        request()->file->move($path, $request->file->getClientOriginalName());

        //Product save
        $product = new Product;
        $product->sku = '';
        $product->price = 0;
        $product->quick_product = 1;
        $product->price_inr = 0;
        $product->price_inr_special = 0;
        $product->save();

        //Attach Media To Post
        $media = MediaUploader::fromSource($path)
            ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
            ->upload();
        $product->attachMedia($media, config('constants.media_tags'));

        if ($path) {
            $urls = GoogleVisionHelper::getImageDetails($path);
            $count = 0;
            if (isset($urls['pages'])) {
                foreach ($urls['pages'] as $url) {
                    if (stristr($url, '.gucci.') || stristr($url, '.farfetch.')) {
                        //Create New Product

                        // Create queue item
                        $scrapeQueue = new ScrapeQueues;
                        $scrapeQueue->product_id = $product->id;
                        $scrapeQueue->url = $url;
                        $scrapeQueue->save();
                        $count++;
                        break;
                    }
                }
                if ($count == 0) {
                    $product->status_id = StatusHelper::$googleImageSearchFailed;
                    $product->save();

                    return response('failed');
                } else {
                    StatusHelper::updateStatus($product, StatusHelper::$queuedForGoogleImageSearch);

                    return response('success');
                }
            } else {
                $product->status_id = StatusHelper::$googleImageSearchFailed;
                $product->save();

                return response('failed');
            }
        }
    }

    public function getProductFromText(Request $request)
    {
        $keyword = $request->keyword;
        $braand = $request->brand;
        $brand = Brand::where('name', $braand)->first();
        if ($brand == null && $brand == '') {
            $brandId = '';
        } else {
            $brandId = $brand->id;
        }
        $sku = $request->sku;
        if ($sku == null && $sku == '') {
            $sku = '';
        }
        $title = $request->title;
        if ($title == null && $title == '') {
            $title = '';
        }

        //Product save
        $product = new Product;
        $product->name = $title;
        $product->sku = $sku;
        $product->brand = $brandId;
        $product->price = 0;
        $product->quick_product = 1;
        $product->price_inr = 0;
        $product->price_inr_special = 0;
        $product->save();

        if ($product->brands != null) {
            if ($product->brands->googleServer != null) {
                $key = $product->brands->googleServer->key;
            } else {
                $key = null;
            }
        } else {
            $key = null;
            $brand = '';
        }

        $googleServer = config('env.GOOGLE_CUSTOM_SEARCH');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $parameter['searchImage'] = [
            'product_id' => $product->id,
            'image_url' => $product->crop_image,

        ];

        //Replace Google Server Key
        if ($key != null) {
            $re = '/([?&]cx)=([^#&]*)/';
            preg_match($re, $googleServer, $match);
            $googleServer = str_replace($match[2], $key, $googleServer);
        }

        $link = $googleServer.'&q='.urlencode($keyword).'&searchType=image&imgSize=large';

        $handle = curl_init();

        // Set the url
        curl_setopt($handle, CURLOPT_URL, $link);
        // Set the result output to be a string.
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($handle);
        $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $link, 'POST', json_encode($parameter), json_decode($output), $httpcode, GoogleSearchImageController::class, 'multipleImageStore');
        curl_close($handle);

        $list = json_decode($output);

        if ($list == null) {
            return false;
        }

        if (isset($list->searchInformation)) {
            if ($list->searchInformation->totalResults == 0) {
                return false;
            }
        }

        if (! isset($list->items)) {
            return false;
        }
        $links = $list->items;

        //here save log
        $count = 0;
        foreach ($links as $link) {
            $image = $link->link;

            $jpg = \Image::make($image)->encode('jpg');
            $filename = substr($image, strrpos($image, '/'));
            $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', $filename);
            $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($product->id / 10000).'/'.$product->id)->useFilename($filename)->upload();
            $product->attachMedia($media, config('constants.google_text_search'));

            $responseString = 'Link: '.$link->link.'\n Display Link: '.$link->displayLink.'\n Title : '.$link->title.'\n Image Details: '.$link->image->contextLink.' Height:'.$link->image->height.' Width : '.$link->image->width.'\n ThumbnailLink '.$link->image->thumbnailLink;

            $log = new LogGoogleCse;
            $log->image_url = $image;
            $log->keyword = $keyword;
            $log->response = $responseString;
            $log->save();
            $count++;
        }

        //If Page Is Not Found
        if ($count == 0) {
            $product->status_id = StatusHelper::$googleTextSearchFailed;
            $product->save();

            return response('error');
        } else {
            StatusHelper::updateStatus($product, StatusHelper::$pendingVerificationGoogleTextSearch);

            return response('success');
        }
    }

    public function searchImageList(): View
    {
        $data['title'] = 'Google Search Images';
        $image_search = GoogleSearchImage::where('user_id', Auth::id())
            ->leftjoin('products as p', 'p.id', '=', 'google_search_images.product_id')
            ->select('google_search_images.*', 'p.name as product_name')
            ->paginate(30);
        $data['image_search'] = $image_search;

        return view('google_search_image.search_image_list', $data);
    }
}
