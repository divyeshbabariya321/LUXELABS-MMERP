<?php

namespace App\Http\Controllers;
use App\StoreWebsiteProductPriceHistory;
use App\StoreWebsiteProductPrice;
use App\StoreWebsite;
use App\Product;

use App\Activity;
use App\Brand;
use App\BrandLogo;
use App\BrandWithLogo;
use App\Category;
use App\CategorySegment;
use App\CategorySegmentDiscount;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UnMergeBrandBrandRequest;
use App\Jobs\CreateHashTags;
use App\ScrapedProducts;
use App\Setting;
use App\StoreWebsiteBrand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Exception;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::leftJoin('store_website_brands as swb', 'swb.brand_id', 'brands.id')
            ->leftJoin('store_websites as sw', 'sw.id', 'swb.store_website_id')
            ->select(['brands.*', DB::raw('group_concat(sw.id) as selling_on'), DB::raw('LOWER(trim(brands.name)) as lower_brand')])
            ->groupBy('brands.id')
            ->orderBy('lower_brand')->whereNull('brands.deleted_at');

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $brands = $brands->where('name', 'like', '%'.$keyword.'%');
        }

        $brands = $brands->paginate(Setting::get('pagination'));

        $category_segments = CategorySegment::where('status', 1)->get();
        $category_segment_discounts = CategorySegmentDiscount::whereIn('brand_id', $brands->pluck('id'))
            ->whereIn('category_segment_id', $category_segments->pluck('id'))
            ->get();

        $storeWebsite = StoreWebsite::all()->pluck('website', 'id')->toArray();
        $attachedBrands = StoreWebsiteBrand::groupBy('store_website_id')->select(
            [DB::raw('count(brand_id) as total_brand'), 'store_website_id']
        )->get()->toArray();

        return view('brand.index', compact('brands', 'storeWebsite', 'attachedBrands', 'category_segments', 'category_segment_discounts'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function scrap_brand(Request $request): View
    {
        // Set dates
        $keyWord = $request->get('term', '');
        $dev = $request->get('dev', '');
        $devCheckboxs = $request->get('devCheckboxs');

        $brands = Brand::leftJoin('products as p', 'p.brand', 'brands.id')
            ->select(['brands.*', DB::raw('LOWER(trim(brands.name)) as lower_brand'), DB::raw('COUNT(p.id) as total_products')])
            ->groupBy('brands.id')
            ->orderByDesc('total_products')->with('singleBrandTask')->whereNull('brands.deleted_at');

        if ($devCheckboxs) {
            $brands->whereHas('brandTask', function ($q) use ($devCheckboxs) {
                $q->whereIn('assigned_to', $devCheckboxs);
            });
        }
        $keyword = request('keyword');
        if (! empty($keyWord)) {
            $brands->where(function ($q) use ($keyWord) {
                $q->where('brands.name', 'like', "%{$keyWord}%");
            });
        }

        $brands = $brands->paginate(Setting::get('pagination'));

        //Developers

        $allbrands = Brand::leftJoin('products as p', 'p.brand', 'brands.id')
            ->select(['brands.*', DB::raw('LOWER(trim(brands.name)) as lower_brand'), DB::raw('COUNT(p.id) as total_products')])
            ->groupBy('brands.id')
            ->orderByDesc('total_products')->with('singleBrandTask')->whereNull('brands.deleted_at');

        $alldevs = [];
        $developers = $allbrands->get();
        if ($developers) {
            foreach ($developers as $_developer) {
                if ($_developer->singleBrandTask) {
                    $alldevs[! empty($_developer->singleBrandTask->assignedUser) ? $_developer->singleBrandTask->assignedUser->id : ''] = ! empty($_developer->singleBrandTask->assignedUser) ? $_developer->singleBrandTask->assignedUser->name : '';
                }
            }
        }

        $filters = $request->all();

        return view('brand.scrap_brand', compact('brands', 'filters', 'alldevs', 'dev'));
    }

    public function create(): View
    {
        $data['name'] = '';
        $data['euro_to_inr'] = '';
        $data['deduction_percentage'] = '';
        $data['magento_id'] = '';
        $data['brand_segment'] = '';
        $data['category_segments'] = CategorySegment::where('status', 1)->get();
        $data['amount'] = '';
        $data['modify'] = 0;

        return view('brand.form', $data);
    }

    public function edit(Brand $brand): View
    {
        $data = $brand->toArray();
        $data['category_segments'] = CategorySegment::where('status', 1)->get();
        $brand_id = $brand->id;
        $data['category_segment_discounts'] = CategorySegmentDiscount::where('brand_id', $brand_id)
            ->whereIn('category_segment_id', $data['category_segments']->pluck('id'))
            ->get();
        $category_segment_discount = CategorySegmentDiscount::where('brand_id', $brand->id)->first();
        if ($category_segment_discount) {
            $data['category_segment_id'] = $category_segment_discount->id;
            $data['amount'] = $category_segment_discount->amount;
        } else {
            $data['category_segment_id'] = '';
            $data['amount'] = '';
        }
        $data['modify'] = 1;

        return view('brand.form', $data);
    }

    public function store(StoreBrandRequest $request, Brand $brand): RedirectResponse
    {
        $euro_to_inr = $request->euro_to_inr;
        $deduction_percentage = $request->deduction_percentage;
        $brand_segment = $request->brand_segment;
        $magento_id = $request->magento_id;
        $amount = $request->amount;
        $category_segment_id = $request->category_segment_id;
        if ($euro_to_inr === null) {
            $euro_to_inr = 0.0;
        }
        if ($deduction_percentage === null) {
            $deduction_percentage = 0;
        }
        if ($brand_segment === null) {
            $brand_segment = '';
        }
        if ($magento_id === null) {
            $magento_id = 0;
        }
        if ($amount === null) {
            $amount = 0;
        }
        if ($category_segment_id === null) {
            $category_segment_id = 0;
        }

        $data = [
            'name' => $request->name,
            'euro_to_inr' => $euro_to_inr,
            'deduction_percentage' => $deduction_percentage,
            'sales_discount' => $request->sales_discount,
            'apply_b2b_discount_above' => $request->apply_b2b_discount_above,
            'b2b_sales_discount' => $request->b2b_sales_discount,
            'magento_id' => $magento_id,
            'brand_segment' => $brand_segment,
            'sku_strip_last' => $request->sku_strip_last,
            'sku_add' => $request->sku_add,
            'references' => $request->references,
        ];

        $brand = $brand->create($data);

        CategorySegmentDiscount::insert([
            ['brand_id' => $brand->id, 'category_segment_id' => $category_segment_id, 'amount' => $amount, 'amount_type' => 'percentage', 'created_at' => now(), 'updated_at' => now()],
        ]);

        /*Generate keyword for Current Brand Only*/
        $this->generateHashTagKeywords([]);

        return redirect()->route('brand.index')->with('success', 'Brand added successfully');
    }

    public function generateHashTagKeywords($brand_id_array)
    {
        $category_postfix_string_list = Category::getCategoryHierarchyString(4);
        /* Initialize queue for add hashtags */
        if (count($brand_id_array) > 0) {
            $brandList = Brand::where('is_hashtag_generated', 0)->whereIn('id', $brand_id_array)->pluck('name', 'id')->chunk(1000)->toArray();
        } else {
            $brandList = Brand::where('is_hashtag_generated', 0)->pluck('name', 'id')->chunk(100)->toArray();
        }

        foreach ($brandList as $chunk) {
            CreateHashTags::dispatch(['data' => $chunk, 'user_id' => Auth::user()->id, 'category_postfix_string_list' => $category_postfix_string_list, 'type' => 'brand'])->onQueue('generategooglescraperkeywords');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *                                       Function for fetch brand list using AJAX request
     */
    public function show(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $search_key = $request->get('search', '');
            $brand_list = Brand::where('name', 'LIKE', '%'.$search_key.'%')->take(20)->get();

            return response()->json(['success' => true, 'data' => $brand_list]);
        }

        return redirect()->route('brand.index');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->scrapedProducts()->delete();
        $brand->products()->delete();
        $brand->delete();

        return redirect()->route('brand.index')->with('success', 'Brand Deleted successfully');
    }

    public static function getBrandName($id)
    {
        $brand = new Brand;
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->name : '';
    }

    public static function getBrandIds($term)
    {
        $brand = Brand::where('name', '=', $term)->first();

        return $brand ? $brand->id : 0;
    }

    public static function getEuroToInr($id)
    {
        $brand = new Brand;
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->euro_to_inr : 0;
    }

    public static function getDeductionPercentage($id)
    {
        $brand = new Brand;
        $brand_instance = $brand->find($id);

        return $brand_instance ? $brand_instance->deduction_percentage : 0;
    }

    /**
     * @SWG\Get(
     *   path="/brands",
     *   tags={"Scraper"},
     *   summary="List all brands and reference for scraper",
     *   operationId="scraper-get-brands-reference",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function brandReference()
    {
        $brands = Brand::select('name', 'references')->get();
        $referenceArray = []; // Got undifined referenceArray error so assigne array here
        foreach ($brands as $brand) {
            $referenceArray[] = $brand->name;
            if (! empty($brand->references)) {
                $references = explode(';', $brand->references);
                if (is_array($references)) {
                    foreach ($references as $reference) {
                        if ($reference != null && $reference != '') {
                            $referenceArray[] = $reference;
                        }
                    }
                }
            }
        }

        return json_encode($referenceArray);
    }

    public function attachWebsite(Request $request): JsonResponse
    {
        $website = $request->get('website');
        $brandId = $request->get('brand_id');

        if (! empty($website) && ! empty($brandId)) {
            if (is_array($website)) {
                StoreWebsiteBrand::where('brand_id', $brandId)->whereNotIn('store_website_id', $website)->delete();
                foreach ($website as $key => $web) {
                    $sbrands = StoreWebsiteBrand::where('brand_id', $brandId)
                        ->where('store_website_id', $web)
                        ->first();

                    if (! $sbrands) {
                        $sbrands = new StoreWebsiteBrand;
                        $sbrands->brand_id = $brandId;
                        $sbrands->store_website_id = $web;
                        $sbrands->save();
                    }
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Website attached successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => [], 'message' => 'There is no website selected']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Oops, something went wrong']);
    }

    public function createRemoteId(Request $request, $id): JsonResponse
    {
        $brand = Brand::where('id', $id)->first();

        if (! empty($brand)) {
            if ($brand->magento_id == '' || $brand->magento_id <= 0) {
                $brand->magento_id = 10000 + $brand->id;
                $brand->save();

                return response()->json(['code' => 200, 'data' => $brand, 'message' => 'Remote id created successfully']);
            } else {
                return response()->json(['code' => 500, 'data' => $brand, 'message' => 'Remote id already exist']);
            }
        }

        return response()->json(['code' => 500, 'data' => $brand, 'message' => 'Brand not found']);
    }

    public function changeSegment(Request $request): JsonResponse
    {
        $id = $request->get('brand_id', 0);
        $brand = Brand::where('id', $id)->first();
        $segment = $request->get('segment');

        if ($brand) {
            $brand->brand_segment = $segment;
            $brand->status = 0;
            $brand->save();

            return response()->json(['code' => 200, 'data' => []]);
        }

        return response()->json(['code' => 500, 'data' => []]);
    }

    public function changeNextStep(Request $request): JsonResponse
    {
        $id = $request->get('brand_id', 0);
        $brand = Brand::where('id', $id)->first();
        $next_step = $request->get('next_step');

        if ($brand) {
            $brand->next_step = $next_step;
            $brand->status = 0;
            $brand->save();

            return response()->json(['code' => 200, 'data' => []]);
        }

        return response()->json(['code' => 500, 'data' => []]);
    }

    public function mergeBrand(Request $request): JsonResponse
    {
        if ($request->from_brand && $request->to_brand) {
            $fromBrand = Brand::find($request->from_brand);
            $toBrand = Brand::find($request->to_brand);

            if ($fromBrand && $toBrand) {
                $product = Product::where('brand', $fromBrand->id)->get();
                if (! $product->isEmpty()) {
                    foreach ($product as $p) {
                        $p->brand = $toBrand->id;
                        $p->save();
                    }
                }

                // now store the all brands
                $freferenceBrand = explode(',', $fromBrand->references);
                $treferenceBrand = explode(',', $toBrand->references);

                $mReference = array_merge($freferenceBrand, $treferenceBrand);
                $toBrand->references = implode(',', array_unique($mReference));
                $toBrand->save();
                $fromBrand->delete();

                return response()->json(['code' => 200, 'data' => []]);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Please check valid brand exist']);
    }

    public function unMergeBrand(UnMergeBrandBrandRequest $request): JsonResponse
    {

        $fromBrand = Brand::find($request->from_brand_id);

        if ($fromBrand) {
            // now store the all brands
            $freferenceBrand = explode(',', $fromBrand->references);

            if (($key = array_search($request->brand_name, $freferenceBrand)) !== false) {
                unset($freferenceBrand[$key]);
            }

            $fromBrand->references = implode(',', $freferenceBrand);
            $fromBrand->save();

            $brand_count = Brand::where('name', '=', $request->brand_name)->count();
            if ($brand_count == 0) {
                $oldBrand = Brand::where('name', '=', $request->brand_name)->onlyTrashed()->latest()->first();
                if ($oldBrand) {
                    $oldBrand->references = null;
                    $oldBrand->deleted_at = null;
                    $oldBrand->save();
                    $scrapedProducts = ScrapedProducts::where('brand_id', $oldBrand->id)->get();
                    foreach ($scrapedProducts as $scrapedProduct) {
                        $product = Product::where('id', $scrapedProduct->product_id)->first();
                        if ($product) {
                            $product->brand = $oldBrand->id;
                            $product->save();
                        }
                    }
                } else {
                    $newBrand = new Brand;
                    $newBrand->name = $request->brand_name;
                    $newBrand->euro_to_inr = 0;
                    $newBrand->deduction_percentage = 0;
                    $newBrand->magento_id = 0;
                    $newBrand->save();
                }
            } else {
                return response()->json(['message' => 'Brand unmerged successfully'], 200);
            }

            return response()->json(['message' => 'Brand unmerged successfully', 'data' => []], 200);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Please check valid brand exist']);
    }

    public function storeCategorySegmentDiscount(Request $request)
    {
        $ps = StoreWebsiteProductPrice::join('products', 'store_website_product_prices.product_id', 'products.id')
            ->select(
                'store_website_product_prices.id',
                'store_website_product_prices.duty_price',
                'store_website_product_prices.product_id',
                'store_website_product_prices.store_website_id',
                'websites.code'
            )
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->leftJoin('category_segment_discounts', 'store_website_product_prices.web_store_id', 'websites.id')
            ->where('category_segment_discounts.brand_id', $request->brand_id)->where('category_segment_discounts.category_segment_id', $request->category_segment_id)
            ->get();
        if ($ps) {
            foreach ($ps as $p) {
                StoreWebsiteProductPrice::where('id', $p->id)->update(['status' => 0]);
            }
        }
        $category_segment = CategorySegmentDiscount::where('brand_id', $request->brand_id)->where('category_segment_id', $request->category_segment_id)->first();
        if ($category_segment) {
            return $catSegment = CategorySegmentDiscount::where('brand_id', $request->brand_id)->where('category_segment_id', $request->category_segment_id)->update([
                'amount' => $request->amount,
                'amount_type' => 'percentage',
                'updated_at' => now(),
            ]);
        } else {
            return $catSegment = CategorySegmentDiscount::insert([
                ['brand_id' => $request->brand_id, 'category_segment_id' => $request->category_segment_id, 'amount' => $request->amount, 'amount_type' => 'percentage', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function activites(Request $request, $id): View
    {
        $activites = Activity::where('subject_id', $id)->where('subject_type', 'Brand')->get();

        return view()->make('brand.activities', compact('activites'));
    }

    public function priority(Request $request): JsonResponse
    {
        $brand = Brand::find($request->id);
        $brand->priority = $request->priority;
        if ($brand->save()) {
            return response()->json(['message' => 'Brand priority updated'], 200);
        }
    }

    public function fetchNewBrands(Request $request): JsonResponse
    {
        $path = public_path('brands');
        $files = File::allFiles($path);
        if ($request->hasfile('files')) {
            foreach ($request->file('files') as $files) {
                $image_name = $files->getClientOriginalName();
                $brand_name = strtoupper(pathinfo($image_name, PATHINFO_FILENAME));
                $brand_found = Brand::where('name', $brand_name)->get();
                if (! $brand_found->isEmpty()) {
                    $media = MediaUploader::fromSource($files)
                        ->toDirectory('brands')
                        ->upload();
                    Brand::where('id', $brand_found[0]->id)->update(['brand_image' => config('env.APP_URL').'/brands/'.$image_name]);
                }
            }

            return response()->json(['code' => 200, 'success' => 'Brand images updated']);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields']);
        }
    }

    //START - Purpose : Fetch data - DEVTASK-4278
    public function fetchlogos(Request $request): View
    {
        try {
            $brand_data = Brand::leftjoin('brand_with_logos', 'brands.id', 'brand_with_logos.brand_id')
                ->leftjoin('brand_logos', 'brand_with_logos.brand_logo_image_id', 'brand_logos.id')
                ->select('brands.id as brands_id', 'brands.name as brands_name', 'brand_logos.logo_image_name as brand_logos_image')
                ->orderBy('brands.name');

            if ($request->brand_name) {
                $search = '%'.$request->brand_name.'%';
                $brand_data = $brand_data->where('brands.name', 'like', $search);
            }
            $brand_data = $brand_data->paginate(Setting::get('pagination'));
            $data = Brand::all();

            return view('brand.brand_logo', compact('brand_data', 'data'))->with('i', (request()->input('page', 1) - 1) * 10);
        } catch (Exception $e) {
            return response()->json(['error' => 'Opps! Something went wrong, Please try again.'], 400);
        }
    }

    public function uploadlogo(Request $request): JsonResponse
    {
        try {
            $files = $request->file('file');
            $fileNameArray = [];
            foreach ($files as $key => $file) {
                $fileName = $file->hashName();
                $fileNameArray[] = $fileName;

                $params['logo_image_name'] = $fileName;
                $params['user_id'] = Auth::id();

                BrandLogo::create($params);

                $file->storeAs('brand_logo', $fileName, 's3');
            }

            return response()->json(['code' => 200, 'msg' => 'files uploaded successfully', 'data' => $fileNameArray]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data'=>[]]);
        }
    }

    public function get_all_images(Request $request): JsonResponse
    {
        try {
            $brand_data = BrandLogo::leftjoin('brand_with_logos', 'brand_logos.id', 'brand_with_logos.brand_logo_image_id')
                ->select('brand_logos.id as brand_logos_id', 'brand_logos.logo_image_name as brand_logo_image_name', 'brand_with_logos.id as brand_with_logos_id', 'brand_with_logos.brand_logo_image_id as brand_with_logos_brand_logo_image_id', 'brand_with_logos.brand_id as brand_with_logos_brand_id')
                ->where('brand_logos.logo_image_name', 'like', '%'.$request->brand_name.'%')
                ->get();

            return response()->json(['code' => 200, 'brand_logo_image' => $brand_data]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data'=>[]]);
        }
    }

    public function set_logo_with_brand(Request $request): JsonResponse
    {
        try {
            $brand_id = $request->logo_id;
            $logo_image_id = $request->logo_image_id;

            $brand_logo_data = BrandWithLogo::updateOrCreate(
                [
                    'brand_id' => $brand_id,
                ],
                [
                    'brand_id' => $brand_id,
                    'brand_logo_image_id' => $logo_image_id,
                    'user_id' => Auth::id(),
                ]
            );

            $brand_logo_image = BrandLogo::where('id', $brand_logo_data->brand_logo_image_id)->select('logo_image_name')->first();

            return response()->json(['code' => 200, 'message' => 'Logo Set Sucessfully for this Brand.', 'brand_logo_image' => $brand_logo_image->logo_image_name]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data'=>[]]);
        }
    }

    public function remove_logo(Request $request): JsonResponse
    {
        try {
            $brand_id = $request->brand_id;

            $record = BrandWithLogo::where('brand_id', $brand_id);
            $record->delete();

            return response()->json(['code' => 200, 'message' => 'Logo has been Removed Sucessfully.']);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'error' => 'Opps! Something went wrong, Please try again.', 'data'=>[]]);
        }
    }

    public function assignDefaultValue(Request $request): JsonResponse
    {
        $category_segments = $request->category_segments;
        $brand_segment = $request->brand_segment;
        $segments = CategorySegment::where('id', $category_segments)->get();
        $brands = Brand::where('brand_segment', $brand_segment)->get();
        if (! $brands->isEmpty()) {
            foreach ($brands as $b) {
                if (! $segments->isEmpty()) {
                    foreach ($segments as $segment) {
                        $catDiscount = CategorySegmentDiscount::where('brand_id', $b->id)->where('category_segment_id', $segment->id)->first();
                        if ($catDiscount) {
                            $catDiscount->amount = $request->value;
                            $catDiscount->save();
                        } else {
                            CategorySegmentDiscount::create([
                                'brand_id' => $b->id,
                                'category_segment_id' => $segment->id,
                                'amount' => $request->value,
                                'amount_type' => 'percentage',
                            ]);
                        }

                        $this->update_store_website_product_prices($b->id, $segment->id, $request->value);
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Default segment discount assigned']);
    }

    public function approve(Request $request): JsonResponse
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);
        for ($i = 0; $i < count($ids); $i++) {
            if ($ids[$i] > 0) {
                Brand::where('id', $ids[$i])->update(['status' => 1]);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Approved Successfully']);
    }

    public function update_store_website_product_prices($brand, $segment, $amount)
    {
        $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.segment_discount')
            ->join('products', 'store_website_product_prices.product_id', 'products.id')
            ->join('categories', 'products.category', 'categories.id')
            ->join('category_segments', 'categories.category_segment_id', 'category_segments.id')
            ->where('products.brand', $brand)
            ->where('categories.category_segment_id', $segment)
            ->get();

        if ($ps) {
            foreach ($ps as $p) {
                StoreWebsiteProductPrice::where('id', $p->id)->update(['segment_discount' => $amount, 'status' => 0]);
                $note = 'Segment Discount Changed from '.$p->segment_discount.' To '.$amount;
                StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id' => $p->id, 'updated_by' => Auth::id(), 'notes' => $note, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    //END - DEVTASK-4278
}
