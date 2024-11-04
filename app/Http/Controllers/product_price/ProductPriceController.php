<?php

namespace App\Http\Controllers\product_price;

use App\Brand;
use App\Category;
use App\CategorySegment;
use App\CategorySegmentDiscount;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Jobs\PushToMagento;
use App\Loggers\LogListMagento;
use App\PriceOverride;
use App\Product;
use App\ProductUpdateLog;
use App\Setting;
use App\SimplyDutyCountry;
use App\SimplyDutyCountryHistory;
use App\SimplyDutySegment;
use App\StoreWebsite;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteProductPriceHistory;
use App\Supplier;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ini_set('memory_limit', -1);

        $categoryIds = Category::pluck('id')->toArray();
        $categories = Category::whereNotIn('parent_id', $categoryIds)->where('parent_id', '>', 0)->select('id', 'title')->get()->toArray();

        $filter_data = $request->input();

        $skip = empty($request->page) ? 0 : $request->page;
        $category_segments = CategorySegment::where('status', 1)->get();

        $product_list = [];
        if (! empty($request->selectedId)) {
            $products = StoreWebsite::where('store_websites.is_published', 1)
                ->crossJoin('products')
                ->crossJoin('simply_duty_countries')
                ->leftJoin('brands as b', function ($q) {
                    $q->on('b.id', 'products.brand');
                })
                ->leftJoin('categories as c', function ($q) {
                    $q->on('c.id', 'products.category');
                })
                ->leftJoin('category_segments as cs', function ($q) {
                    $q->on('c.category_segment_id', 'cs.id');
                })
                ->leftJoin('scraped_products as sp', function ($q) {
                    $q->on('sp.product_id', 'products.id');
                })
                ->Join('product_suppliers as psu', function ($q) {
                    $q->on('psu.product_id', 'products.id');
                })
                ->select(DB::raw('
                    products.id as pid, 
                    products.name as product_name,
                    b.name as brand_name,
                    b.id as brand_id,
                    cs.name as category_segment,
                    b.brand_segment as brand_segment,
                    c.title as category_name,
                    products.category,
                    products.supplier,
                    products.sku,
                    products.size,
                    products.color,
                    products.suggested_color,
                    products.composition,
                    products.size_eu,
                    products.stock,
                    psu.size_system,
                    status_id,
                    sub_status_id,
                    products.created_at,
                    products.id as pid,
                    simply_duty_countries.country_code as product_country_code,
                    simply_duty_countries.country_name as product_country_name,
                    store_websites.id as store_websites_id,
                    store_websites.website as product_website,
                    products.brand'
                ));
            $products = $products->whereNull('products.deleted_at');

            if (isset($filter_data['country_code'])) {
                $products = $products->where('simply_duty_countries.country_code', $filter_data['country_code']);
            }

            if (isset($filter_data['supplier']) && is_array($filter_data['supplier']) && $filter_data['supplier'][0] != null) {
                $suppliers_list = implode(',', $filter_data['supplier']);
                $products = $products->whereRaw(DB::raw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))"));
            }

            if (isset($filter_data['brand_names']) && is_array($filter_data['brand_names']) && $filter_data['brand_names'][0] != null) {
                $products = $products->whereIn('brand_id', $filter_data['brand_names']);
            }

            if (isset($filter_data['websites']) && is_array($filter_data['websites']) && $filter_data['websites'][0] != null) {
                $products = $products->whereIn('store_websites.id', $filter_data['websites']);
            }

            if (isset($filter_data['term'])) {
                $term = $filter_data['term'];
                $products = $products->where(function ($q) use ($term) {
                    $q->where('products.name', 'LIKE', "%$term%")
                        ->orWhere('products.sku', 'LIKE', "%$term%")
                        ->orWhere('c.title', 'LIKE', "%$term%")
                        ->orWhere('b.name', 'LIKE', "%$term%")
                        ->orWhere('products.id', 'LIKE', "%$term%");
                });
            }

            $products = $products->where('products.id', $request->selectedId);

            $products = $products->orderByDesc('products.id'); // FOR LATEST PRODUCT

            $products = $products->skip($skip * 25)->limit(25)->get();
            $product_list = [];
            if (count($products)) {
                foreach ($products as $p) {
                    $category_segment_discount_array = [];
                    $product = Product::find($p->pid);
                    $dutyPrice = $product->getDuty($p->product_country_code);
                    $category_segment = $p->category_segment != null ? $p->category_segment : $p->brand_segment;

                    $price = $product->getPrice($p->store_websites_id, $p->product_country_code, null, true, $dutyPrice, null, null, null, isset($product->suppliers_info) ? $product->suppliers_info[0]->price : 0, $category_segment);
                    $ivaPercentage = Product::IVA_PERCENTAGE;
                    $productPrice = number_format($price['original_price'], 2, '.', '');

                    foreach ($category_segments as $key => $category_segment) {
                        $category_segment_discount = CategorySegmentDiscount::where('brand_id', $p['brand_id'])->where('category_segment_id', $category_segment->id)->first();
                        $category_segment_discount_array[$p['brand_id']][$category_segment->id] = $category_segment_discount;
                    }
                    $product_list[] = [
                        'storeWebsitesID' => $p->store_websites_id,
                        'getPrice' => $price,
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'brand' => isset($p->brand_name) ? $p->brand_name : '-',
                        'brand_id' => isset($p->brand) ? $p->brand : '0',
                        'segment' => $category_segment,
                        'website' => $p->product_website,
                        'eur_price' => $productPrice,
                        'seg_discount' => (float) $price['segment_discount'],
                        'segment_discount_per' => (float) $price['segment_discount_per'],
                        'iva' => Product::IVA_PERCENTAGE.'%',
                        'net_price' => $productPrice - (float) $price['segment_discount'] - ($productPrice) * (Product::IVA_PERCENTAGE) / 100,
                        'add_duty' => $product->getDuty($p->product_country_code).'%',
                        'add_profit' => number_format($price['promotion'], 2, '.', ''),
                        'add_profit_per' => (float) $price['promotion_per'],
                        'final_price' => number_format($price['total'], 2, '.', ''),
                        'country_code' => $p->product_country_code,
                        'country_name' => $p->product_country_name,
                        'category_segment_discount_array' => $category_segment_discount_array,
                    ];
                }
            }
        }
        $countryGroups = SimplyDutyCountry::getSelectList();
        $cCodes = $countryGroups;
        if (! empty($request->country_code)) {
            $cCodes = SimplyDutyCountry::where('country_code', $request->country_code)->pluck('country_name', 'country_code')->toArray(); //$request->country_code;
        }

        $suppliers = [];
        $brands = [];
        $websites = StoreWebsite::where('is_published', '1')->get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('title', 'id', 'website')->where('is_published', '1');

        if ($request->websites && ! empty($request->websites)) {
            $storeWebsites = $storeWebsites->whereIn('id', $request->websites);
        }
        $storeWebsites = $storeWebsites->get()->toArray();

        $selected_brands = null;
        if ($request->brand_names) {
            $selected_brands = Brand::select('id', 'name')->whereIn('id', $request->brand_names)->get();
        }

        $selected_suppliers = null;
        if ($request->supplier) {
            $selected_suppliers = Supplier::select('id', 'supplier')->whereIn('id', $request->supplier)->get();
        }

        $selected_websites = null;
        if ($request->websites) {
            $selected_websites = StoreWebsite::select('id', 'title')->whereIn('id', $request->websites)->get();
        }

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('product_price.index_ajax', compact('product_list', 'count', 'category_segments'))->render();

            return response()->json(['html' => $view, 'page' => $request->page, 'count' => $count]);
        }

        return view('product_price.index', compact('countryGroups', 'product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites', 'category_segments', 'categories'));
    }

    public function store_website_product_prices(Request $request)
    {
        ini_set('memory_limit', -1);
        $filter_data = $request->input();
        $skip = empty($request->page) ? 0 : $request->page;
        $products = StoreWebsiteProductPrice::join('products', 'store_website_product_prices.product_id', 'products.id')
            ->leftJoin('brands as b', function ($q) {
                $q->on('b.id', 'products.brand');
            })
            ->leftJoin('categories as c', function ($q) {
                $q->on('c.id', 'products.category');
            })
            ->leftJoin('category_segments as cs', function ($q) {
                $q->on('c.category_segment_id', 'cs.id');
            })
            ->leftJoin('scraped_products as sp', function ($q) {
                $q->on('sp.product_id', 'products.id');
            })
            ->leftJoin('product_suppliers as psu', function ($q) {
                $q->on('psu.product_id', 'products.id');
            })
            ->leftJoin('store_websites', 'store_websites.id', 'store_website_product_prices.store_website_id')
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->select(DB::raw('
                products.id as pid, 
                products.name as product_name,
                b.name as brand_name,
                b.id as brand_id,
                cs.name as category_segment,
                b.brand_segment as brand_segment,
                c.title as category_name,
                products.category,
                products.supplier,
                products.sku,
                products.size,
                products.color,
                products.suggested_color,
                products.composition,
                products.size_eu,
                products.stock,
                psu.size_system,
                status_id,
                sub_status_id,
                products.created_at,
                products.id as pid,
                store_websites.id as store_websites_id,
                store_websites.website as product_website,
                products.brand,
                store_website_product_prices.id as store_website_product_prices_id,
                store_website_product_prices.default_price,
                store_website_product_prices.segment_discount,
                store_website_product_prices.duty_price ,
                store_website_product_prices.override_price,
                store_website_product_prices.status,
                websites.name as countries'
            ));
        if (isset($filter_data['websites']) && is_array($filter_data['websites']) && $filter_data['websites'][0] != null) {
            $products = $products->whereIn('store_websites.id', $filter_data['websites']);
        }
        if (isset($filter_data['country_code'])) {
            $products = $products->where('websites.code', strtolower($filter_data['country_code']));
        }

        if (isset($filter_data['name'])) {
            $products = $products->where('products.sku', $filter_data['name']);
        }

        $products = $products->whereNull('products.deleted_at');

        $page = Paginator::resolveCurrentPage('page');

        $products = Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            'items' => $products->offset(($page - 1) * 20)->limit(20)->get(),
            'total' => 5000000,
            'perPage' => 20,
            'currentPage' => $page,
            'options' => [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        ]);
        $category_segments = CategorySegment::where('status', 1)->get();

        $product_list = [];
        foreach ($products as $p) {
            if (count($products)) {
                $category_segment_discount_array = [];
                $product = Product::find($p->pid);
                $dutyPrice = $product->getDuty($p->product_country_code);
                $category_segment = $p->category_segment != null ? $p->category_segment : $p->brand_segment;

                $price = $product->getPrice($p->store_websites_id, $p->product_country_code, null, true, $dutyPrice, null, null, null, isset($product->suppliers_info[0]) ? $product->suppliers_info[0]->price : 0, $category_segment);
                $ivaPercentage = Product::IVA_PERCENTAGE;
                $productPrice = number_format($price['original_price'], 2, '.', '');

                foreach ($category_segments as $key => $category_segment) {
                    $category_segment_discount = CategorySegmentDiscount::where('brand_id', $p['brand_id'])->where('category_segment_id', $category_segment->id)->first();
                    $category_segment_discount_array[$p['brand_id']][$category_segment->id] = $category_segment_discount;
                }

                $product_list[] = [
                    'storeWebsitesID' => $p->store_websites_id,
                    'getPrice' => $price,
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'brand' => isset($p->brand_name) ? $p->brand_name : '-',
                    'brand_id' => isset($p->brand) ? $p->brand : '0',
                    'segment' => $category_segment,
                    'website' => $p->product_website,
                    'eur_price' => $productPrice,
                    'seg_discount' => (float) $price['segment_discount'],
                    'segment_discount_per' => (float) $price['segment_discount_per'],
                    'iva' => Product::IVA_PERCENTAGE.'%',
                    'net_price' => $productPrice - (float) $price['segment_discount'] - ($productPrice) * (Product::IVA_PERCENTAGE) / 100,
                    'add_duty' => $product->getDuty($p->product_country_code).'%',
                    'add_profit' => number_format($price['promotion'], 2, '.', ''),
                    'add_profit_per' => (float) $price['promotion_per'],
                    'final_price' => number_format($price['total'], 2, '.', ''),
                    'country_code' => $p->product_country_code,
                    'country_name' => $p->product_country_name,
                    'default_price' => $p->default_price,
                    'segment_discount' => $p->segment_discount,
                    'duty_price' => $p->duty_price,
                    'override_price' => $p->override_price,
                    'store_website_product_prices_id' => $p->store_website_product_prices_id,
                    'status' => $p->status,
                    'default_duty' => $p->default_duty,
                    'countries' => $p->countries,
                    'category_segment_discount_array' => $category_segment_discount_array,
                ];
            }
        }
        $countryGroups = SimplyDutyCountry::getSelectList();
        $cCodes = $countryGroups;
        if (! empty($request->country_code)) {
            $cCodes = SimplyDutyCountry::where('country_code', $request->country_code)->pluck('country_name', 'country_code')->toArray(); //$request->country_code;
        }

        $suppliers = [];
        $brands = [];
        $websites = StoreWebsite::where('is_published', '1')->get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('title', 'id', 'website')->where('is_published', '1');
        if ($request->websites && ! empty($request->websites)) {
            $storeWebsites = $storeWebsites->whereIn('id', $request->websites);
        }
        $storeWebsites = $storeWebsites->get()->toArray();

        $selected_brands = null;
        if ($request->brand_names) {
            $selected_brands = Brand::select('id', 'name')->whereIn('id', $request->brand_names)->get();
        }

        $selected_suppliers = null;
        if ($request->supplier) {
            $selected_suppliers = Supplier::select('id', 'supplier')->whereIn('id', $request->supplier)->get();
        }

        $selected_websites = null;
        if ($request->websites) {
            $selected_websites = StoreWebsite::select('id', 'title')->whereIn('id', $request->websites)->get();
        }

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('product_price.index_ajax', compact('product_list', 'count', 'category_segments'))->render();

            return response()->json(['html' => $view, 'page' => $request->page, 'count' => $count]);
        }

        return view('product_price.store-website-product-prices', compact('countryGroups', 'product_list', 'suppliers', 'websites', 'brands', 'selected_suppliers', 'selected_brands', 'selected_websites', 'category_segments'));
    }

    public function update_product(Request $request): JsonResponse
    {
        $product_array = json_decode($request->product_array);
        $response_array = [];
        foreach ($product_array as $key => $p) {
            if ($request->route()->getName() == 'product.pricing.update.add_duty') {
                if ($p->row_id == $request->row_id) {
                    $duty = SimplyDutyCountry::where('country_code', $p->country_code)->first();
                    $duty->default_duty = str_replace('%', '', $request->add_duty);
                    $duty->save();
                    $add_duty = str_replace('%', '', $request->add_duty);
                }
            } elseif ($request->route()->getName() == 'product.pricing.update.add_profit') {
                if ($p->row_id == $request->row_id) {
                    $ref_product = Product::find($p->product_id);
                    $category_segment = @$ref_product->categories->categorySegmentId->name == null ? (@$ref_product->brands->brand_segment != null ? $ref_product->brands->brand_segment : null) : $ref_product->categories->categorySegmentId->name;
                    $result = $ref_product->getPrice($p->storewebsitesid, $p->country_code, null, true, $p->add_duty, null, $request->add_profit, null, isset($ref_product->suppliers_info) ? $ref_product->suppliers_info[0]->price : 0, $category_segment);
                    if ($result['status'] == false) {
                        return response()->json(['status' => false, 'message' => $result['field']]);
                    }
                }
            } else {
                if ($p->row_id == $request->row_id) {
                    $ref_product = Product::find($p->product_id);
                    $category_segment = @$ref_product->categories->categorySegmentId->name == null ? (@$ref_product->brands->brand_segment != null ? $ref_product->brands->brand_segment : null) : $ref_product->categories->categorySegmentId->name;
                    $ref_product->getPrice($p->storewebsitesid, $p->country_code, null, true, $p->add_duty, (int) str_replace('%', '', $request->seg_discount), null, null, isset($ref_product->suppliers_info) ? $ref_product->suppliers_info[0]->price : 0, $category_segment);
                }
            }

            $product = Product::find($p->product_id);
            $category_segment = @$product->categories->categorySegmentId->name == null ? (@$product->brands->brand_segment != null ? $product->brands->brand_segment : null) : $product->categories->categorySegmentId->name;
            $price = $product->getPrice($p->storewebsitesid, $p->country_code, null, true, empty($add_duty) ? $p->add_duty : $add_duty, null, null, 'checked_add_profit', isset($product->suppliers_info) ? $product->suppliers_info[0]->price : 0, $category_segment);
            $arr['status'] = $price['status'];
            $arr['row_id'] = $p->row_id;
            $arr['seg_discount'] = (float) $price['segment_discount'];
            $arr['segment_discount_per'] = (float) $price['segment_discount_per'];
            $arr['add_profit'] = number_format($price['promotion'], 2, '.', '');
            $arr['add_profit_per'] = number_format($price['promotion_per'], 2, '.', '');
            $arr['price'] = number_format($price['total'], 2, '.', '');
            $arr['add_duty'] = empty($add_duty) ? $p->add_duty : $add_duty.'%';
            $response_array[] = $arr;
        }
        CategorySegmentDiscount::where('id', $request->seg_id)->update(['amount' => $request->seg_discount]);

        return response()->json(['data' => $response_array, 'status' => true]);
    }

    public function genericPricingAll($request)
    {
        $product_price = 100;
        $final_price1 = 100;
        $final_price2 = 100;
        $ids = $request->all();

        $categoryIds = Category::pluck('id')->toArray();
        $categories = Category::whereNotIn('parent_id', $categoryIds)
            ->where('parent_id', '>', 0)
            ->select('id', 'title')
            ->orderBy('title')
            ->get()->toArray();

        $skip = empty($request->page) ? 0 : $request->page;

        ini_set('memory_limit', -1);
        $product_list = [];

        $countries = SimplyDutyCountry::leftJoin('simply_duty_segments', 'simply_duty_segments.id', '=', 'simply_duty_countries.segment_id')
            ->select('*', 'simply_duty_segments.segment as country_segment');

        if (isset($request->order) && isset($request->input)) {
            if ($request->input == 'csegment') {
                $countries = $countries->orderBy('country_code', $request->order);
            }
        }
        if ($request->country_segment != '') {
            $countries->where('simply_duty_segments.segment', $request->country_segment);
        }

        $countries = $countries->get()->toArray();

        $brands = StoreWebsite::where('store_websites.is_published', 1)
            ->crossJoin('products')
            ->leftJoin('brands', 'brands.id', 'products.brand')
            ->Join('categories', 'categories.id', 'products.category')
            ->leftJoin('category_segments as cs', function ($q) {
                $q->on('categories.category_segment_id', 'cs.id');
            })
            ->whereNotNull('brands.name')
            ->select('brands.id', 'brands.name', 'brands.brand_segment', 'products.category as catId',
                'store_websites.id as store_websites_id', 'store_websites.website as product_website',
                'categories.title as cate_title', 'cs.name as country_segment', 'products.id as pid')
            ->groupBy('categories.id', 'store_websites.id', 'brands.brand_segment')
            ->having(DB::raw('count(*)'), '>=', 1);

        $i = 0;

        if (isset($request->order) && isset($request->input)) {
            if ($request->input == 'category') {
                $brands->orderBy('cate_title', $request->order);
            }

            if ($request->input == 'website') {
                $brands->orderBy('product_website', $request->order);
            }

            if ($request->input == 'bsegment') {
                $brands->orderBy('brands.brand_segment', $request->order);
            }

            if ($request->input == 'csegment') {
                $brands->orderBy('cs.name', $request->order);
            }
        }
        $numcount = 5000;
        $brands = $brands
            ->skip($skip * Setting::get('pagination'))
            ->limit(Setting::get('pagination') ?? 10)
            ->get()->toArray();


        $countriesCount = count($countries);

        $category_segments = CategorySegment::where('status', 1)->get();
        if (count($countries) > 0) {
            foreach ($brands as $brand) {
                $country = $countries[$i];
                $catSegDisc1 = $catSegDisc2 = 0;
                $final_price = $final_price1 = $final_price2 = $product_price;

                foreach ($category_segments as $key => $category_segment) {
                    $price = $final_price;
                    $category_segment_discount = CategorySegmentDiscount::where('brand_id', $brand['id'])->where('category_segment_id', $category_segment->id)->first();
                    if ($category_segment_discount != null) {
                        if ($category_segment_discount->amount != '' && $category_segment_discount->amount_type == 'percentage') {
                            if ($category_segment_discount->amount != '' || $category_segment_discount->amount != 0) {
                                $catDisc = ($price * $category_segment_discount->amount) / 100;
                                $price = $price - $catDisc;
                            }
                        } elseif ($category_segment_discount->amount_type == 'amount') {
                            if ($category_segment_discount->amount != '' || $category_segment_discount->amount != 0) {
                                $price = $price - $category_segment_discount->amount;
                            }
                        }
                    }

                    if ($key == 0) {
                        $catSegDisc1 = $category_segment_discount;
                        $final_price1 = $price;
                    } elseif ($key == 1) {
                        $catSegDisc2 = $category_segment_discount;
                        $final_price2 = $price;
                    }
                }


                if (Product::IVA_PERCENTAGE != 0) {
                    $IVA = Product::IVA_PERCENTAGE;
                    $lessIva = ($final_price1 * $IVA) / 100;
                    $final_price1 = $final_price1 - $lessIva;

                    $lessIva = ($final_price2 * $IVA) / 100;
                    $final_price2 = $final_price2 - $lessIva;
                }

                if ($country['default_duty'] != '' || $country['default_duty'] != 0) {
                    $dutyDisc = ($final_price1 * $country['default_duty']) / 100;
                    $final_price1 = $final_price1 + $dutyDisc;

                    $dutyDisc = ($final_price2 * $country['default_duty']) / 100;
                    $final_price2 = $final_price2 + $dutyDisc;
                }

                $product = Product::find($brand['pid']);
                $product->price = 100;
                if ($product == null) {
                    continue;
                }
                $dutyPrice = $product->getDuty($country['country_code']);
                $category_segment = $brand['brand_segment'];
                $price = $product->getPrice($brand['store_websites_id'], $country['country_code'], null, true, $dutyPrice, null, null, null, 100, $category_segment);

                $cost1 = $final_price1;
                $cost2 = $final_price2;
                $profit = 0;
                $profit_per = 0;
                if (isset($price['promotion'])) {
                    $profit = number_format($price['promotion'], 2, '.', '');
                }
                if (isset($price['promotion_per'])) {
                    $profit_per = $price['promotion_per'];
                }

                if ($profit) {
                    $profitCost = ($final_price1 * $profit_per) / 100;
                    $final_price1 = $final_price1 + $profitCost;

                    $profitCost = ($final_price2 * $profit_per) / 100;
                    $final_price2 = $final_price2 + $profitCost;
                }

                $country['dutySegment'] = isset($brand['country_segment']) ? $brand['country_segment'] : $brand['brand_segment']; //$country['country_code'];

                $categoryDetail = Category::where('id', $brand['catId'])->select('id', 'title')->first();


                $product_list[$categoryDetail->id.'_'.$brand['store_websites_id'].'_'.$brand['brand_segment']] = [
                    'catId' => $categoryDetail ? $categoryDetail->id : '',
                    'categoryName' => $categoryDetail ? $categoryDetail->title : '',
                    'product' => 'Product For Brand',
                    'brandId' => $brand['id'],
                    'brandName' => $brand['name'],
                    'brandSegment' => $brand['brand_segment'],
                    'store_websites_id' => $brand['store_websites_id'],
                    'product_website' => $brand['product_website'],
                    'country' => $country,
                    'product_price' => 100,
                    'less_IVA' => Product::IVA_PERCENTAGE.'%',
                    'final_price1' => number_format($final_price1, 2, '.', ''),
                    'add_profit' => number_format($profit, 2, '.', ''),
                    'add_profit_per' => (float) $profit_per,
                    'final_price2' => number_format($final_price2, 2, '.', ''),
                    'cost1' => $cost1,
                    'cost2' => $cost2,
                    'cate_segment_discount' => isset($category_segment_discount->amount) ? $category_segment_discount->amount : 0,

                    'cate_segment_discount_type' => isset($category_segment_discount->amount_type) ? $category_segment_discount->amount_type : 0,
                ];

                if ($i < $countriesCount - 1) {
                    $i++;
                } else {
                    $i = 0;
                }

                $product_price = 100;
            }
        }

        return ['product_list' => $product_list, 'category_segments' => $category_segments, 'categories' => $categories, 'numcount' => $numcount];
    }

    public function genericPricing(Request $request)
    {
        $product_price = 100;
        $final_price = 100;
        $ids = $request->all();
        $cats = StoreWebsite::select('id', 'title')->get();
        $country_segments = SimplyDutySegment::pluck('segment')->toArray();

        $categoryIds = Category::pluck('id')->toArray();
        $categories = Category::whereNotIn('parent_id', $categoryIds)->where('parent_id', '>', 0)->select('id', 'title')->orderBy('title')->get()->toArray();

        $cat_id = isset($ids['id']) ? $ids['id'] : $categories[0]['id'];

        ini_set('memory_limit', -1);
        $product_list = [];

        $countries = SimplyDutyCountry::leftJoin('simply_duty_segments', 'simply_duty_segments.id', '=', 'simply_duty_countries.segment_id')
            ->select('*', 'simply_duty_segments.segment as country_segment');

        if (isset($request->order) && isset($request->input)) {
            if ($request->input == 'csegment') {
                $countries = $countries->orderBy('country_code', $request->order);
            }
        }
        if ($request->country_segment != '') {
            $countries->where('simply_duty_segments.segment', $request->country_segment);
        }
        $countries = $countries->get()->toArray();

        $categoryDetail = Category::where('id', $cat_id)->select('id', 'title')->first();

        $skip = empty($request->page) ? 0 : $request->page;

        $countriesCount = count($countries);
        $category_segments = CategorySegment::where('status', 1)->get();

        $numcount = 10;
        $product_list = [];
        if (! empty($request->selectedId)) {
            $brands = StoreWebsite::where('store_websites.is_published', 1)
                ->crossJoin('products')
                ->leftJoin('brands', 'brands.id', 'products.brand')
                ->Join('categories', 'categories.id', 'products.category')
                ->leftJoin('category_segments as cs', function ($q) {
                    $q->on('categories.category_segment_id', 'cs.id');
                })
                ->whereNotNull('brands.name')
                ->select('brands.id', 'brands.name', 'brands.brand_segment', 'products.category as catId',
                    'store_websites.id as store_websites_id', 'store_websites.website as product_website',
                    'categories.title as cate_title', 'cs.name as country_segment', 'products.id as pid')
                ->groupBy('categories.id', 'store_websites.id', 'brands.brand_segment')
                ->having(DB::raw('count(*)'), '>=', 1);

            if ($request->id != '') {
                $brands->where('products.category', $cat_id);
            }

            $brands = $brands->where('products.id', $request->selectedId);

            if ($request->brand_segment != '') {
                $brands->where('brands.brand_segment', $request->brand_segment);
            }

            if ($request->website != '') {
                $brands->where('store_websites.id', $request->website);
            }
            if (isset($request->order) && isset($request->input)) {
                if ($request->input == 'category') {
                    $brands->orderBy('cate_title', $request->order);
                }

                if ($request->input == 'website') {
                    $brands->orderBy('product_website', $request->order);
                }

                if ($request->input == 'bsegment') {
                    $brands->orderBy('brands.brand_segment', $request->order);
                }

                if ($request->input == 'csegment') {
                    $brands->orderBy('cs.name', $request->order);
                }
            }

            $brands = $brands
                ->skip($skip * Setting::get('pagination'))
                ->limit(Setting::get('pagination') ?? 10)
                ->get()->toArray();
            $i = 0;

            if ($countriesCount > 0) {
                foreach ($brands as $brand) {
                    $catSegDisc1 = $catSegDisc2 = 0;
                    $final_price1 = $final_price2 = $product_price;
                    $country = $countries[$i];
                    $category_segment_discount_array = [];

                    foreach ($category_segments as $key => $category_segment) {
                        $price = $final_price;
                        $category_segment_discount = CategorySegmentDiscount::where('brand_id', $brand['id'])->where('category_segment_id', $category_segment->id)->first();
                        if ($category_segment_discount != null) {
                            if ($category_segment_discount->amount != '' && $category_segment_discount->amount_type == 'percentage') {
                                if ($category_segment_discount->amount != '' || $category_segment_discount->amount != 0) {
                                    $catDisc = ($price * $category_segment_discount->amount) / 100;
                                    $price = $price - $catDisc;
                                }
                            } elseif ($category_segment_discount->amount_type == 'amount') {
                                if ($category_segment_discount->amount != '' || $category_segment_discount->amount != 0) {
                                    $price = $price - $category_segment_discount->amount;
                                }
                            }
                        }

                        if ($key == 0) {
                            $catSegDisc1 = $category_segment_discount;
                            $final_price1 = $price;
                        } elseif ($key == 1) {
                            $catSegDisc2 = $category_segment_discount;
                            $final_price2 = $price;
                        }
                        $category_segment_discount_array[$brand['id']][$category_segment->id] = $category_segment_discount;
                    }

                    if (Product::IVA_PERCENTAGE != 0) {
                        $IVA = Product::IVA_PERCENTAGE;
                        $lessIva = ($final_price1 * $IVA) / 100;
                        $final_price1 = $final_price1 - $lessIva;

                        $lessIva = ($final_price2 * $IVA) / 100;
                        $final_price2 = $final_price2 - $lessIva;
                    }

                    if ($country['default_duty'] != '' || $country['default_duty'] != 0) {
                        $dutyDisc = ($final_price1 * $country['default_duty']) / 100;
                        $final_price1 = $final_price1 + $dutyDisc;

                        $dutyDisc = ($final_price2 * $country['default_duty']) / 100;
                        $final_price2 = $final_price2 + $dutyDisc;
                    }

                    $country['dutySegment'] = isset($brand['country_segment']) ? $brand['country_segment'] : $brand['brand_segment'];
                    $product = Product::find($brand['pid']);
                    $dutyPrice = $product->getDuty($country['country_code']);
                    $category_segment = isset($brand['country_segment']) ? $brand['country_segment'] : $brand['brand_segment'];
                    $price = $product->getPrice($brand['store_websites_id'], $country['country_code'], null, true, $dutyPrice, null, null, null, isset($product->suppliers_info[0]) ? $product->suppliers_info[0]->price : 0, $category_segment);

                    $cost1 = $final_price1;
                    $cost2 = $final_price2;
                    $profit = 0;
                    $profit_per = 0;

                    if (isset($price['promotion'])) {
                        $profit = number_format($price['promotion'], 2, '.', '');
                    }
                    if (isset($price['promotion_per'])) {
                        $profit_per = $price['promotion_per'];
                    }

                    if ($profit) {
                        $profitCost = ($final_price1 * $profit_per) / 100;
                        $final_price1 = $final_price1 + $profitCost;

                        $profitCost = ($final_price2 * $profit_per) / 100;
                        $final_price2 = $final_price2 + $profitCost;
                    }

                    $product_list[$categoryDetail->id.'_'.$brand['store_websites_id'].'_'.$brand['brand_segment']] = [
                        'catId' => $categoryDetail->id,
                        'categoryName' => $categoryDetail->title,
                        'product' => 'Product For Brand',
                        'brandId' => $brand['id'],
                        'brandName' => $brand['name'],
                        'brandSegment' => $brand['brand_segment'],
                        'store_websites_id' => $brand['store_websites_id'],
                        'product_website' => $brand['product_website'],
                        'country' => $country,
                        'product_price' => 100,
                        'less_IVA' => Product::IVA_PERCENTAGE.'%',
                        'cost1' => $cost1,
                        'cost2' => $cost2,
                        'final_price1' => number_format($final_price1, 2, '.', ''),
                        'add_profit' => number_format($profit, 2, '.', ''),
                        'add_profit_per' => (float) $profit_per,
                        'final_price2' => number_format($final_price2, 2, '.', ''),
                        'cate_segment_discount' => isset($category_segment_discount->amount) ? $category_segment_discount->amount : 0,
                        'cate_segment_discount_type' => isset($category_segment_discount->amount_type) ? $category_segment_discount->amount_type : 0,
                        'category_segment_discount_array' => $category_segment_discount_array,
                    ];

                    if ($i < $countriesCount - 1) {
                        $i++;
                    } else {
                        $i = 0;
                    }

                    $product_price = 100;
                }
            }
        }

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('product_price.generic_price_ajax', compact('product_list', 'category_segments'))->render();

            return response()->json(['html' => $view, 'page' => $request->page, 'count' => $count]);
        }

        return view('product_price.generic_price', compact('product_list', 'category_segments', 'country_segments', 'categories', 'cats', 'numcount'));
    }

    public function updateProduct(Request $request)
    {
        if (isset($request->segmentId1)) {
            $catSegDiscount = CategorySegmentDiscount::where(['category_segment_id' => $request->segmentId1, 'brand_id' => $request->brandId])->first();
            if ($request->segmentprice1 != null && $request->segmentprice1 > 0) {
                if ($catSegDiscount == null) {
                    CategorySegmentDiscount::create(['category_segment_id' => $request->segmentId1, 'amount' => $request->segmentprice1, 'brand_id' => $request->brandId]);
                } else {
                    $catSegDiscount->update(['amount' => $request->segmentprice1]);
                }
            }
        }

        if (isset($request->segmentId2)) {
            $catSegDiscount = CategorySegmentDiscount::where(['category_segment_id' => $request->segmentId2, 'brand_id' => $request->brandId])->first();
            if ($request->segmentprice2 != null && $request->segmentprice2 > 0) {
                if ($catSegDiscount == null) {
                    CategorySegmentDiscount::create(['category_segment_id' => $request->segmentId2, 'amount' => $request->segmentprice2, 'brand_id' => $request->brandId]);
                } else {
                    $catSegDiscount->update(['amount' => $request->segmentprice2]);
                }
            }
        }
        if (isset($request->default_duty)) {
            $duty = SimplyDutyCountry::find($request->countryId);
            $duty->default_duty = $request->default_duty;

            $data = [
                'simply_duty_countries_id' => $duty->id,
                'old_segment' => $duty->segment_id,
                'new_segment' => $duty->segment_id,
                'old_duty' => $duty->default_duty,
                'new_duty' => $request->input('duty'),
                'updated_by' => Auth::user()->id,

            ];
            $duty->status = 0;
            SimplyDutyCountryHistory::insert($data);
            $dutyPricesProducts = 0;
            if ($duty->save()) {
                $amount = $request->input('duty');


                $code = $duty->country_code;
                $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.duty_price',

                    'store_website_product_prices.product_id', 'store_website_product_prices.store_website_id', 'websites.code')
                    ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
                    ->where('websites.code', strtolower($code))
                    ->get(); //dd($ps);
                if ($ps) {
                    foreach ($ps as $p) {
                        StoreWebsiteProductPrice::where('id', $p->id)->update(['duty_price' => $amount, 'status' => 0]);
                        $note = 'Country Duty changed  from '.$p->duty_price.' To '.$amount;
                        StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id' => $p->id, 'updated_by' => Auth::id(), 'notes' => $note, 'created_at' => date('Y-m-d H:i:s')]);
                    }
                }
                $dutyPricesProducts = count($ps);
            }

            if ($request->add_profit > 0) {

                $priceRecords = null;
                $brand = $request->brand_segment;
                $category = $request->input('catId');
                $country = $request->input('country_code');
                $priceModal = PriceOverride::where('store_website_id', $request->websiteId);
                $updated_add_profit = $request->add_profit;
                if (! empty($brand) && ! empty($category) && ! empty($country)) {
                    $priceRecords = $priceModal->where('country_code', $country)->where('brand_segment', $brand)->where('category_id', $category)->first();
                }

                if (! $priceRecords) {


                    $priceModal = PriceOverride::where('store_website_id', $request->websiteId);

                    $priceRecords = $priceModal->where(function ($q) use ($brand, $category, $country) {
                        $q->orWhere(function ($q) use ($brand, $category) {
                            $q->where('brand_segment', $brand)->where('category_id', $category);
                        })->orWhere(function ($q) use ($brand, $country) {
                            $q->where('brand_segment', $brand)->where('country_code', $country);
                        })->orWhere(function ($q) use ($country, $category) {
                            $q->where('country_code', $country)->where('category_id', $category);
                        });
                    })->first();
                }

                if (! $priceRecords) {


                    $priceModal = PriceOverride::where('store_website_id', $request->websiteId);


                    $priceRecords = $priceModal->where('brand_segment', $brand)->first();
                }

                if (! $priceRecords) {


                    $priceModal = PriceOverride::where('store_website_id', $request->websiteId);

                    $priceRecords = $priceModal->where('category_id', $category)->first();
                }

                if (! $priceRecords) {

                    $priceModal = PriceOverride::where('store_website_id', $request->websiteId);
                    $priceRecords = $priceModal->where('country_code', $country)->first();
                }
                $updated = 0;
                if ($priceRecords) {
                    if ($updated_add_profit && $priceRecords->type == 'PERCENTAGE') {
                        $updated_add_profit_row = PriceOverride::where('id', $priceRecords->id)->update(
                            [
                                'calculated' => $updated_add_profit >= 0 ? '+' : '-',
                                'value' => $updated_add_profit,
                            ]
                        );
                        $updated = 1;
                    }
                }
                if ($updated == 0) {
                    PriceOverride::create(['store_website_id' => $request->websiteId, 'brand_id' => $request->input('brandId'), 'category_id' => $request->input('catId'),
                        'country_code' => $request->input('country_code'), 'type' => 'PERCENTAGE', 'calculated' => '+', 'value' => $request->add_profit, ]);
                }
            }
            $ps = Product::select('products.id', 'store_website_product_prices.store_website_id')
                ->leftJoin('store_website_product_prices', 'store_website_product_prices.product_id', '=', 'products.id')
                ->where('store_website_id', $request->input('websiteId'))
                ->where(function ($query) use ($request) {
                    $query->where('brand', $request->input('brandId'))
                        ->orWhere('category', $request->input('catId'));
                })->select('products.id as product_id', 'products.name as product_name', 'store_website_product_prices.store_website_id')->groupBy('products.id')->get();

            foreach ($ps as $p) {
                ProductUpdateLog::create(['store_website_id' => $p->store_website_id, 'created_by' => Auth::id(), 'product_id' => $p->product_id, 'log' => $p->product_name.' updated.']);
            }
            foreach ($ps as $p) {
                $this->pushToMagento($p->product_id, $p->store_website_id);
            }
        }

        return ['status' => true, 'count' => count($ps) + $dutyPricesProducts];
    }

    public function updateProductPrice(Request $request)
    {
        if ($request->route()->getName() == 'updateDutyPrice') {
            $duty = SimplyDutyCountry::find($request->countryId);
            $duty->default_duty = $request->dutyPrice;
            $duty->save();
        } elseif ($request->route()->getName() == 'updateSegmentPrice') {
            $catSegDiscount = CategorySegmentDiscount::where(['category_segment_id' => $request->segmentId, 'brand_id' => $request->brandId])->first();
            if ($catSegDiscount == null) {
                CategorySegmentDiscount::create(['category_segment_id' => $request->segmentId, 'amount' => $request->price, 'brand_id' => $request->brandId]);
            } else {
                $catSegDiscount->update(['amount' => $request->price]);
            }
        }

        return json_encode(['status' => true]);
    }

    public function approve(Request $request): JsonResponse
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);
        for ($i = 0; $i < count($ids); $i++) {
            if ($ids[$i] > 0) {
                StoreWebsiteProductPrice::where('id', $ids[$i])->update(['status' => 1]);
            }
        }

        return response()->json(['code' => 200, 'message' => 'Approved Successfully']);
    }

    public function storewebsiteproductpriceshistory(Request $request)
    {

        $id = $request->id;
        $history = StoreWebsiteProductPriceHistory::where('sw_product_prices_id', $id)->orderByDesc('created_at')->get();
        $html = "<table class='table table-bordered table-striped'> <thead><tr><th>Date</th><th>Notes</th></thead> <tbody>";

        foreach ($history as $h) {
            $html .= '<tr><td>'.$h->created_at.'</td>';
            $html .= '<td>'.$h->notes.'</td></tr>';
        }
        $html .= ' </tbody> </table>';

        echo $html;
    }

    public function update_store_website_product_prices($code, $amount)
    {
        $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.duty_price',
            'store_website_product_prices.product_id', 'store_website_product_prices.store_website_id', 'websites.code')
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->where('websites.code', strtolower($code))
            ->get();
        if ($ps) {
            foreach ($ps as $p) {

                StoreWebsiteProductPrice::where('id', $p->id)->update(['duty_price' => $amount, 'status' => 0]);
                $note = 'Country Duty changed  from '.$p->duty_price.' To '.$amount;

                $this->pushToMagento($p->product_id, $p->store_website_id);
                StoreWebsiteProductPriceHistory::insert(['sw_product_prices_id' => $p->id, 'updated_by' => Auth::id(), 'notes' => $note, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    public function update_store_website_product_segment($code, $segmentDiscount)
    {
        $ps = StoreWebsiteProductPrice::select('store_website_product_prices.id', 'store_website_product_prices.duty_price', 'websites.code')
            ->leftJoin('websites', 'store_website_product_prices.web_store_id', 'websites.id')
            ->where('websites.code', strtolower($code))
            ->get(); //dd($ps);
        if ($ps) {
            foreach ($ps as $p) {
                StoreWebsiteProductPrice::where('id', $p->id)->update(['segment_discount' => $segmentDiscount, 'status' => 0, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    public function pushToMagento($productId, $websiteId)
    {
        $product = Product::find($productId);

        if ($product) {
            $website = StoreWebsite::where('id', $websiteId)->first();
            if ($website == null) {
                Log::channel('productUpdates')->info('Product started '.$product->id.' No website found');
                $msg = 'No website found for  Brand: '.$product->brand.' and Category: '.$product->category;
                echo $msg;
                exit;
            } else {
                $i = 1;

                if ($website) {
                    // testing
                    Log::channel('productUpdates')->info('Product started website found For website'.$website->website);
                    $log = LogListMagento::log($product->id, 'Start push to magento for product id '.$product->id, 'info', $website->id);
                    //currently we have 3 queues assigned for this task.
                    if ($i > 3) {
                        $i = 1;
                    }
                    $log->queue = Helpers::createQueueName($website->title);
                    $log->save();
                    PushToMagento::dispatch($product, $website, $log)->onQueue($log->queue);
                    $i++;
                }
            }
        }
    }

    public function productUpdateLogs(Request $request): View
    {
        $productLogs = ProductUpdateLog::leftJoin('users', 'users.id', '=', 'product_update_logs.created_by')
            ->select('product_update_logs.*', 'users.name as product_updated_by')->orderByDesc('product_update_logs.id')->paginate(20);

        if ($request->ajax()) {
            return view('logging.partials.product_update_logs', compact('productLogs'));
        }

        return view('logging.product_update_logs', compact('productLogs'));
    }

    public function store_website_product_skus(Request $request)
    {
        if (! empty($request['term'])) {
            $dataDropdown = Product::where('sku', 'LIKE', '%'.$request['term'].'%')->pluck('sku', 'id')->toArray();
            echo json_encode($dataDropdown);
        }
    }

    public function getProductAutocomplete(Request $request): JsonResponse
    {
        $input = $_GET['term'];

        $products = [];
        if (! empty($input)) {
            $products = Product::where('id', 'like', '%'.$input.'%')->orWhere('sku', 'like', '%'.$input.'%')->pluck('sku', 'id');
        }

        return response()->json($products);
    }
}
