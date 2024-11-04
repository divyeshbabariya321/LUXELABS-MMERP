<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\ChatMessage;
use App\ColorReference;
use App\Colors;
use App\CropImageGetRequest;
use App\CropImageHttpRequestResponse;
use App\CroppedImageReference;
use App\Customer;
use App\CustomerReview;
use App\DescriptionChange;
use App\ErpLeads;
use App\Helpers;
use App\Helpers\ProductHelper;
use App\Helpers\QueryHelper;
use App\Helpers\StatusHelper;
use App\HsCode;
use App\HsCodeGroup;
use App\HsCodeGroupsCategoriesComposition;
use App\HsCodeSetting;
use App\Http\Requests\CreateTemplateProductRequest;
use App\Http\Requests\Products\ProductTranslationRequest;
use App\Http\Requests\StoreProductRequest;
use App\Jobs\AttachImagesSend;
use App\Jobs\Flow2ConditionCheckProductOnly;
use App\Jobs\Flow2PushProductOnlyJob;
use App\Jobs\ImageApprovalPushProductOnlyJob;
use App\Jobs\PushProductOnlyJob;
use App\Jobs\PushToMagento;
use App\Jobs\SendMessageToCustomer;
use App\Jobs\TestPushProductOnlyJob;
use App\Jobs\UpdateScrapedCategory;
use App\Jobs\UpdateScrapedColor;
use App\Language;
use App\ListingHistory;
use App\Loggers\LogListMagento;
use App\Loggers\LogScraper;
use App\LogRequest;
use App\Mediables;
use App\MessagingGroup;
use App\MessagingGroupCustomer;
use App\Models\DataTableColumn;
use App\Models\ProductListingFinalStatus;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Product_translation;
use App\ProductCategoryHistory;
use App\ProductColorHistory;
use App\ProductLocation;
use App\ProductPushErrorLog;
use App\ProductQuicksellGroup;
use App\ProductStatus;
use App\ProductStatusHistory;
use App\ProductSuggestedLog;
use App\ProductSupplier;
use App\ProductTemplate;
use App\ProductTranslationHistory;
use App\ProductVerifyingUser;
use App\PushToMagentoCondition;
use App\QuickSellGroup;
use App\RejectedImages;
use App\Sale;
use App\ScrapedProducts;
use App\scraperImags;
use App\Setting;
use App\SimplyDutyCategory;
use App\SimplyDutyCountry;
use App\SiteCroppedImages;
use App\Sizes;
use App\Sop;
use App\Stage;
use App\Status;
use App\Stock;
use App\StoreWebsite;
use App\StoreWebsiteProduct;
use App\StoreWebsiteProductAttribute;
use App\SuggestedProduct;
use App\SuggestedProductList;
use App\Supplier;
use App\Task;
use App\Template;
use App\TranslationLanguage;
use App\User;
use App\UserProduct;
use App\UserProductFeedback;
use App\WebsiteProduct;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;
use Qoraiche\MailEclipse\MailEclipse;
use seo2websites\MagentoHelper\MagentoHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('permission:product-list', ['only' => ['show']]);
        $this->middleware('permission:product-lister', ['only' => ['listing']]);
        $this->middleware('permission:product-lister', ['only' => ['listing']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);

        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->archived == 'true') {
            $products = Product::onlyTrashed()->latest()->select(['id', 'sku', 'name']);
        } else {
            $products = Product::latest()->select(['id', 'sku', 'name']);
        }
        $term = $request->term;
        $archived = $request->archived;

        if (! empty($term)) {
            $products = $products->where(function ($query) use ($term) {
                return $query
                    ->orWhere('id', 'like', '%'.$term.'%')
                    ->orWhere('name', 'like', '%'.$term.'%')
                    ->orWhere('sku', 'like', '%'.$term.'%');
            });
        }

        $products = $products->paginate(Setting::get('pagination'));
        $websiteList = ProductHelper::storeWebsite();

        $mediaTags = config('constants.media_tags');

        return view('products.index', compact('products', 'term', 'archived', 'websiteList', 'mediaTags'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function customerReviews(Request $request): View
    {
        $reviews = CustomerReview::with('storeWebsite')->latest();
        $email = '';
        $name = '';
        $store = '';
        if (! empty($request->email)) {
            $email = $request->email;
            $reviews->where('email', 'LIKE', '%'.$request->email.'%');
        }
        if (! empty($request->name)) {
            $name = $request->name;
            $reviews->where('name', 'LIKE', '%'.$request->name.'%');
        }
        if (! empty($request->store)) {
            $store = $request->store;
            $reviews->whereHas('storeWebsite', function ($q) use ($request) {
                $q->where('website', 'LIKE', '%'.$request->store.'%');
            });
        }

        $reviews = $reviews->paginate(15);

        return view('products.reviews', compact('reviews', 'email', 'name', 'store'));
    }

    public function deleteReview(Request $request): JsonResponse
    {
        CustomerReview::where('id', $request->id)->delete();

        return response()->json(['code' => 200, 'message' => 'Review deleted successfully']);
    }

    public function approveReview(Request $request)
    {
        ini_set('memory_limit', '-1');

        $data = ['platform_id' => $request->platform_id, 'status' => 1];
        $data = json_encode($data);
        $url = $request->base_url.'/testimonial/index/statusupdate';
        $token = $request->token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'accept: application/json', 'Authorization: Bearer '.$token]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_error($ch);
        Log::channel('approveReview')->info(json_encode([$url, $token, $data, $result, 'approveReview']));
        $response = json_decode($result);

        if ($response) {
            CustomerReview::where(['platform_id' => $request->platform_id])->update(['status' => 1, 'push' => 1]);
        }

        Log::info(print_r([$url, $token, $data, $result], true));
    }

    public function approvedListing(Request $request, $pageType = ''): View
    {
        if (! Setting::has('auto_push_product')) {
            $auto_push_product = Setting::add('auto_push_product', 0, 'int');
        } else {
            $auto_push_product = Setting::get('auto_push_product');
        }
        $cropped = $request->cropped;
        $colors = (new Colors)->all();
        $categories = Category::with('parent')->get();
        $category_tree = [];
        $categories_array = [];
        $categories_paths_array = [];
        $siteCroppedImages = [];
        $brands = Brand::getAll();
        $storeWebsites = StoreWebsite::get();

        // $suppliers = DB::select('
        //         SELECT id, supplier
        //         FROM suppliers

        //         INNER JOIN (
        //             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        //             ) as product_suppliers
        //         ON suppliers.id = product_suppliers.supplier_id
        // ');

        $suppliers = Supplier::join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) as product_suppliers'),
            'suppliers.id', '=', 'product_suppliers.supplier_id')
            ->select('suppliers.id', 'suppliers.supplier')
            ->get();

        foreach ($categories as $category) {
            $categoryPath = $category->title;

            if ($category->parent_id != 0) {
                $parent = $category->parent;

                if ($parent !== null) {
                    $categoryPath = $parent->title.' > '.$categoryPath;
                }

                if ($parent->parent_id != 0) {
                    if (! isset($category_tree[$parent->parent_id])) {
                        $category_tree[$parent->parent_id] = [];
                    }
                    $category_tree[$parent->parent_id][$parent->id] = $category->id;
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
            $categories_paths_array[$category->id] = $categoryPath;
        }
        if (auth()->user()->isReviwerLikeAdmin('final_listing')) {
            $newProducts = Product::query()->with('categories.parent', 'cropApprover', 'cropOrderer', 'approver', 'log_scraper_vs_ai', 'croppedImages', 'brands', 'landingPageProduct');
        } else {
            $newProducts = Product::query()->with('categories.parent', 'cropApprover', 'cropOrderer', 'approver', 'log_scraper_vs_ai', 'croppedImages', 'brands', 'landingPageProduct')->where('assigned_to', auth()->user()->id);
        }

        if ($request->get('status_id') != null) {
            $statusList = is_array($request->get('status_id')) ? $request->get('status_id') : [$request->get('status_id')];
            $newProducts = $newProducts->whereIn('status_id', $statusList); //dd($newProducts->limit(10)->get());
        } else {
            if ($request->get('submit_for_approval') == 'on') {
                $newProducts = $newProducts->where('status_id', StatusHelper::$submitForApproval);
            } else {
                $newProducts = $newProducts->where('status_id', StatusHelper::$finalApproval);
            }
        }
        // Run through query helper
        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if (is_array($request->brand) && $request->brand[0] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if (is_array($request->color) && $request->color[0] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if (is_array($request->category) && $request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
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
            }

            $newProducts = $newProducts->whereIn('products.category', $category_children);
            $category = $request->category[0];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }

        if ($request->crop_status == 'Not Matched') {
            $newProducts = $newProducts->whereDoesntHave('croppedImages');
        }
        if ($request->crop_status == 'Matched') {
            $newProducts = $newProducts->whereHas('croppedImages');
        }

        if (trim($term) != '') {
            $newProducts->where(function ($query) use ($term) {
                $query->where('products.short_description', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.color', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.name', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.sku', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.id', 'LIKE', '%'.$term.'%')
                    ->orWhereHas('brands', function ($q) use ($term) {
                        $q->where('name', 'LIKE', '%'.$term.'%');
                    })
                    ->orWhereHas('product_category', function ($q) use ($term) {
                        $q->where('title', 'LIKE', '%'.$term.'%');
                    });
            });
        }

        if ($request->get('user_id') > 0 && $request->get('submit_for_image_approval') == 'on') {
            $newProducts = $newProducts->Join('log_list_magentos as llm', function ($join) {
                $join->on('llm.product_id', 'products.id');
            });
            $newProducts = $newProducts->where('llm.user_id', $request->get('user_id'));
            $newProducts = $newProducts->addSelect('llm.user_id as last_approve_user');
        }
        if ($request->get('user_id') > 0 && $request->get('rejected_image_approval') == 'on') {
            $newProducts = $newProducts->leftJoin('rejected_images as ri', function ($join) use ($request) {
                $join->on('ri.product_id', 'products.id');

                $join->where('ri.user_id', $request->get('user_id'));
            });
            $newProducts = $newProducts->addSelect('rejected_images.user_id as rejected_user_id', 'rejected_images.created_at as rejected_date');
        }
        if ($request->get('user_id') > 0 && $request->get('rejected_image_approval') != 'on' && $request->get('submit_for_image_approval') != 'on') {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }

        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();
        $newProducts = $newProducts->leftJoin('product_verifying_users as pvu', function ($join) {
            $join->on('pvu.product_id', 'products.id');
            $join->where('pvu.user_id', '!=', auth()->user()->id);
        });

        if ($request->without_title != null) {
            $newProducts = $newProducts->where('products.name', '');
        }

        if ($request->without_size != null) {
            $newProducts = $newProducts->where('products.size', '');
        }

        if ($request->without_composition != null) {
            $newProducts = $newProducts->where('products.composition', '');
        }

        if ($request->without_stock != null) {
            $newProducts = $newProducts->where('products.stock', 0);
        }

        if (! auth()->user()->isAdmin()) {
            //
        }
        $newProducts = $newProducts->where('isUploaded', 0);

        if ($request->crop_start_date != null && $request->crop_end_date != null) {
            $startDate = $request->crop_start_date;
            $endDate = $request->crop_end_date;
            $newProducts = $newProducts->leftJoin('cropped_image_references as cri', function ($join) use ($startDate, $endDate) {
                $join->on('cri.product_id', 'products.id');
                $join->whereDate('cri.created_at', '>=', $startDate)->whereDate('cri.created_at', '<=', $endDate);
            });

            $newProducts = $newProducts->whereNotNull('cri.product_id');
            $newProducts = $newProducts->groupBy('products.id');
        }

        if ($request->store_website_id > 0) {
            $storeWebsiteID = $request->store_website_id;
            $newProducts = $newProducts->join('store_website_categories as swc', function ($join) use ($storeWebsiteID) {
                $join->on('swc.category_id', 'products.category');
                $join->where('swc.store_website_id', $storeWebsiteID)->where('swc.remote_id', '>', 0);
            });

            $newProducts = $newProducts->join('store_website_brands as swb', function ($join) use ($storeWebsiteID) {
                $join->on('swb.brand_id', 'products.brand');
                $join->where('swb.store_website_id', $storeWebsiteID)->where('swb.magento_value', '>', 0);
            });

            $newProducts = $newProducts->groupBy('products.id');
        }

        $newProducts = $newProducts->select(['products.*'])->paginate(10);

        if (! auth()->user()->isAdmin()) {
            if (! $newProducts->isEmpty()) {
                $i = 1;
                foreach ($newProducts as $product) {
                    $productVerify = ProductVerifyingUser::firstOrNew([
                        'product_id' => $product->id,
                    ]);
                    $productVerify->product_id = $product->id;
                    $productVerify->user_id = auth()->user()->id;
                    $productVerify->save();
                    $i++;
                    // if more then 15 records then break
                    if ($i > 25) {
                        break;
                    }
                }
            }
        }

        // checking here for the product which is cropped

        if (count($newProducts) > 0) {
            $productIds = $newProducts->pluck('id')->toArray();

            $siteCroppedImages = SiteCroppedImages::select('product_id', DB::raw('group_concat(site_cropped_images.website_id) as website_ids'))->whereIn('product_id', $productIds)->groupBy('product_id')->pluck('website_ids', 'product_id')->toArray();
        }

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'products-listing-final')->first();

        $dynamicColumnsToShowPlf = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPlf = json_decode($hideColumns, true);
        }

        $statusProductsListingFinal = ProductListingFinalStatus::all();
        $categoryAll = Category::with('childs.childLevelSencond')->where('parent_id', 0)->get();

        if ($request->ajax()) {
            // view path for images
            $viewpath = ($pageType == 'images') ? 'products.final_listing_image_ajax' : 'products.final_listing_ajax';

            return view($viewpath, [
                'users_list' => $users->pluck('name', 'id'),
                'products' => $newProducts,
                'products_count' => $newProducts->total(),
                'colors' => $colors,
                'brands' => $brands,
                'suppliers' => $suppliers,
                'categories' => $categories,
                'category_tree' => $category_tree,
                'categories_array' => $categories_array,
                'term' => $term,
                'brand' => $brand,
                'category' => $category,
                'color' => $color,
                'supplier' => $supplier,
                'type' => $type,
                'users' => $users,
                'assigned_to_users' => $assigned_to_users,
                'cropped' => $cropped,
                'category_array' => $category_array,
                'selected_categories' => $selected_categories,
                'store_websites' => $storeWebsites,
                'type' => $pageType,
                'auto_push_product' => $auto_push_product,
                'user_id' => ($request->get('user_id') > 0) ? $request->get('user_id') : '',
                'request' => $request->all(),
                'categories_paths_array' => $categories_paths_array,
                'siteCroppedImages' => $siteCroppedImages,
                'dynamicColumnsToShowPlf' => $dynamicColumnsToShowPlf,
                'statusProductsListingFinal' => $statusProductsListingFinal,
                'categoryAll' => $categoryAll,
            ]);
        }

        $viewpath = 'products.final_listing';

        return view($viewpath, [
            'users_list' => $users->pluck('name', 'id'),
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            'user_id' => ($request->get('user_id') > 0) ? $request->get('user_id') : '',
            // 'category_selection' => $category_selection,
            // 'category_search'    => $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
            'cropped' => $cropped,
            //            'left_for_users'  => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'store_websites' => $storeWebsites,
            'pageType' => $pageType,
            'auto_push_product' => $auto_push_product,
            //'store_website_count' => StoreWebsite::count(),
            'categories_paths_array' => $categories_paths_array,
            'siteCroppedImages' => $siteCroppedImages,
            'dynamicColumnsToShowPlf' => $dynamicColumnsToShowPlf,
            'statusProductsListingFinal' => $statusProductsListingFinal,
            'categoryAll' => $categoryAll,
        ]);
    }

    public function plfColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'products-listing-final')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'products-listing-final';
            $column->column_name = json_encode($request->column_plf);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'products-listing-final';
            $column->column_name = json_encode($request->column_plf);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = ProductListingFinalStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function getFinalApporvalImages(Request $request): View
    {
        $cropped = $request->cropped;
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        // $suppliers = DB::select('
        //         SELECT id, supplier
        //         FROM suppliers

        //         INNER JOIN (
        //             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        //             ) as product_suppliers
        //         ON suppliers.id = product_suppliers.supplier_id
        // ');

        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) as product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    if (! isset($category_tree[$parent->parent_id])) {
                        $category_tree[$parent->parent_id] = [];
                    }
                    $category_tree[$parent->parent_id][$parent->id] = $category->id;
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        if (auth()->user()->isReviwerLikeAdmin('final_listing')) {
            $newProducts = Product::query();
        } else {
            $newProducts = Product::query()->where('assigned_to', auth()->user()->id);
        }

        if ($request->get('status_id') != null) {
            $statusList = is_array($request->get('status_id')) ? $request->get('status_id') : [$request->get('status_id')];
            $newProducts = $newProducts->whereIn('status_id', $statusList);
        } else {
            if ($request->get('submit_for_approval') == 'on') {
                $newProducts = $newProducts->where('status_id', StatusHelper::$submitForApproval);
            } else {
                $newProducts = $newProducts->where('status_id', StatusHelper::$finalApproval);
            }
        }

        // Run through query helper
        $newProducts = QueryHelper::approvedListingOrder($newProducts);
        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if (is_array($request->brand) && $request->brand[0] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if (is_array($request->color) && $request->color[0] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if (is_array($request->category) && $request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
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
            }

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[0];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }

        if ($request->crop_status == 'Not Matched') {
            $newProducts = $newProducts->whereDoesntHave('croppedImages');
        }
        if ($request->crop_status == 'Matched') {
            $newProducts = $newProducts->whereHas('croppedImages');
        }

        if (trim($term) != '') {
            $newProducts->where(function ($query) use ($term) {
                $query->where('short_description', 'LIKE', '%'.$term.'%')
                    ->orWhere('color', 'LIKE', '%'.$term.'%')
                    ->orWhere('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.sku', 'LIKE', '%'.$term.'%')
                    ->orWhere('products.id', 'LIKE', '%'.$term.'%')
                    ->orWhereHas('brands', function ($q) use ($term) {
                        $q->where('name', 'LIKE', '%'.$term.'%');
                    })
                    ->orWhereHas('product_category', function ($q) use ($term) {
                        $q->where('title', 'LIKE', '%'.$term.'%');
                    });
            });
        }

        if ($request->get('user_id') > 0) {
            if ($request->get('submit_for_image_approval') == 'on') {
                $newProducts = $newProducts->leftJoin('log_list_magentos as llm', function ($join) use ($request) {
                    $join->on('llm.product_id', 'products.id')
                        ->on('llm.id', '=', DB::raw('(SELECT max(id) from log_list_magentos WHERE log_list_magentos.project_id = projects.id)'));
                    $join->where('llm.user_id', $request->get('user_id'));
                });
            } else {
                $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
            }
        }

        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = $newProducts->leftJoin('product_verifying_users as pvu', function ($join) {
            $join->on('pvu.product_id', 'products.id');
            $join->where('pvu.user_id', '!=', auth()->user()->id);
        });

        if ($request->without_title != null) {
            $newProducts = $newProducts->where('products.name', '');
        }

        if ($request->without_size != null) {
            $newProducts = $newProducts->where('products.size', '');
        }

        if ($request->without_composition != null) {
            $newProducts = $newProducts->where('products.composition', '');
        }

        if (! auth()->user()->isAdmin()) {
            $newProducts = $newProducts->whereNull('pvu.product_id');
        }

        $newProducts = $newProducts->select(['products.*'])->paginate(20);
        if (! auth()->user()->isAdmin()) {
            if (! $newProducts->isEmpty()) {
                $i = 1;
                foreach ($newProducts as $product) {
                    $productVerify = ProductVerifyingUser::firstOrNew([
                        'product_id' => $product->id,
                    ]);
                    $productVerify->product_id = $product->id;
                    $productVerify->user_id = auth()->user()->id;
                    $productVerify->save();
                    $i++;
                    // if more then 15 records then break
                    if ($i > 25) {
                        break;
                    }
                }
            }
        }
        if (! Setting::has('auto_push_product')) {
            $auto_push_product = Setting::add('auto_push_product', 0, 'int');
        } else {
            $auto_push_product = Setting::get('auto_push_product');
        }
        if ($request->ajax()) {
            return view('products.final_listing_ajax', [
                'products' => $newProducts,
                'products_count' => $newProducts->total(),
                'colors' => $colors,
                'brands' => $brands,
                'suppliers' => $suppliers,
                'categories' => $categories,
                'category_tree' => $category_tree,
                'categories_array' => $categories_array,
                'term' => $term,
                'brand' => $brand,
                'category' => $category,
                'color' => $color,
                'supplier' => $supplier,
                'type' => $type,
                'users' => $users,
                'assigned_to_users' => $assigned_to_users,
                'cropped' => $cropped,
                'category_array' => $category_array,
                'selected_categories' => $selected_categories,
                'store_websites' => StoreWebsite::all(),
                'auto_push_product' => $auto_push_product,
            ]);
        }

        return view('products.final_approval_images', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection' => $category_selection,
            // 'category_search'    => $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
            'cropped' => $cropped,
            //            'left_for_users'  => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'store_websites' => StoreWebsite::all(),
            'auto_push_product' => $auto_push_product,
            //'store_website_count' => StoreWebsite::count(),
        ]);
    }

    public function approvedListingCropConfirmation(Request $request): View
    {
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        // $suppliers = DB::select('
        //         SELECT id, supplier
        //         FROM suppliers

        //         INNER JOIN (
        //             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        //             ) as product_suppliers
        //         ON suppliers.id = product_suppliers.supplier_id
        // ');

        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[$parent->parent_id][$parent->id][$category->id];
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        // Prioritize suppliers
        $newProducts = Product::where('status_id', StatusHelper::$cropApprovalConfirmation)->where('stock', '!=', 0);

        $newProducts = QueryHelper::approvedListingOrder($newProducts);

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if ($request->brand[0] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if ($request->color[0] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if ($request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
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
            }

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[0];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }
        //
        if (trim($term) != '') {
            $newProducts = $newProducts->where(function ($query) use ($term) {
                $query->where('id', 'LIKE', "%$term%")->orWhere('sku', 'LIKE', "%$term%");
            });
        }

        if ($request->get('user_id') > 0) {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }

        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = QueryHelper::approvedListingOrder($newProducts);

        $newProducts = $newProducts->with(['media', 'brands', 'log_scraper_vs_ai'])->paginate(50);
        $media_tags = config('constants.media_tags');

        return view('products.final_crop_confirmation', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection' => $category_selection,
            // 'category_search'    => $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
            //            'cropped' => $cropped,
            //            'left_for_users'  => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'media_tags' => $media_tags,
        ]);
    }

    public function approvedMagento(Request $request): View
    {
        // Get queue count
        $queueSize = Queue::size('listMagento');

        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        // $suppliers = DB::select('
        //         SELECT id, supplier
        //         FROM suppliers

        //         INNER JOIN (
        //             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        //             ) as product_suppliers
        //         ON suppliers.id = product_suppliers.supplier_id
        // ');

        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    if (isset($category_tree[$parent->parent_id]) && isset($category_tree[$parent->parent_id][$parent->id])) {
                        @$category_tree[$parent->parent_id][$parent->id][$category->id];
                    }
                } else {
                    @$category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        $newProducts = Product::where('isUploaded', 1)->orderByDesc('listing_approved_at');

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        if (isset($request->brand[0]) && $request->brand[0] != null) {
            $newProducts = $newProducts->whereIn('brand', $request->get('brand'));
        }

        if (isset($request->color[0]) && $request->color[0] != null) {
            $newProducts = $newProducts->whereIn('color', $request->get('color'));
        }
        if (isset($request->category) && $request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
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
            }

            $newProducts = $newProducts->whereIn('category', $category_children);
            $category = $request->category[0];
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $newProducts = $newProducts->where('isFinal', 0)->where('isUploaded', 0);
            } else {
                if ($request->type == 'Listed') {
                    $newProducts = $newProducts->where('isUploaded', 1);
                } else {
                    if ($request->type == 'Approved') {
                        $newProducts = $newProducts->where('is_approved', 1);
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $newProducts = $newProducts->where('is_image_processed', 1);
                        }
                    }
                }
            }

            $type = $request->get('type');
        }
        if (trim($term) != '') {
            $newProducts = $newProducts->where(function ($query) use ($term) {
                $query->where('id', 'LIKE', "%$term%")->orWhere('sku', 'LIKE', "%$term%");
            });
        }

        if ($request->get('user_id') > 0) {
            $newProducts = $newProducts->where('approved_by', $request->get('user_id'));
        }

        $selected_categories = $request->category ? $request->category : [1];
        $category_array = Category::renderAsArray();
        $users = User::all();

        $newProducts = $newProducts->with(['media', 'brands'])->paginate(50);
        $media_tags = config('constants.media_tags');

        return view('products.in_magento', [
            'products' => $newProducts,
            'products_count' => $newProducts->total(),
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            // 'category_selection' => $category_selection,
            // 'category_search'    => $category_search,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'users' => $users,
            'assigned_to_users' => $assigned_to_users,
            //            'cropped' => $cropped,
            //            'left_for_users'  => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'queueSize' => $queueSize,
            'media_tags' => $media_tags,
        ]);
    }

    public function showListigByUsers(Request $request): View
    {
        $whereFirst = '';
        if ($request->get('date')) {
            $whereFirst = ' AND DATE(created_at) = "'.$request->get('date').'"';
        }
        $users = UserProduct::groupBy(['user_id'])
            ->select(DB::raw('
            user_id,
            COUNT(product_id) as total_assigned,
            (SELECT COUNT(DISTINCT(listing_histories.product_id)) FROM listing_histories WHERE listing_histories.user_id = user_products.user_id AND action IN ("LISTING_APPROVAL", "LISTING_REJECTED") '.$whereFirst.') as total_acted'));

        if ($request->get('date')) {
            $users = $users->whereRaw('DATE(created_at) = "'.$request->get('date').'"');
        }

        $users = $users->with('user')->get();

        return view('products.assigned_products', compact('users'));
    }

    public function listing(Request $request, Stage $stage): View
    {
        $colors = (new Colors)->all();
        $categories = Category::all();
        $category_tree = [];
        $categories_array = [];
        $brands = Brand::getAll();

        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $isInArray = in_array($category->id, $category_tree[$parent->parent_id] ?? []);
                    if (! $isInArray) {
                        $category_tree[$parent->parent_id][] = $category->id;
                    }
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        $term = $request->input('term');
        $brand = '';
        $category = '';
        $color = '';
        $supplier = [];
        $type = '';
        $assigned_to_users = '';

        $brandWhereClause = '';
        $colorWhereClause = '';
        $categoryWhereClause = '';
        $supplierWhereClause = '';
        $typeWhereClause = '';
        $termWhereClause = '';
        $croppedWhereClause = '';
        $stockWhereClause = ' AND stock >= 1';

        $userWhereClause = '';
        if (is_array($request->brand) && $request->brand[0] != null) {
            $brands_list = implode(',', $request->brand);

            $brand = $request->brand[0];
            $brandWhereClause = " AND brand IN ($brands_list)";
        }

        if (is_array($request->color) && $request->color[0] != null) {
            $colors_list = implode(',', $request->color);

            $color = $request->color[0];
            $colorWhereClause = " AND color IN ($colors_list)";
        }
        if (is_array($request->category) && $request->category[0] != null && $request->category[0] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
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
            }

            $category_list = implode(',', $category_children);

            $category = $request->category[0];
            $categoryWhereClause = " AND category IN ($category_list)";
        }
        if (is_array($request->supplier) && $request->supplier[0] != null) {
            $suppliers_list = implode(',', $request->supplier);
            $supplier = $request->supplier;
            $supplierWhereClause = " AND products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))";
        }
        if ($request->type != '') {
            if ($request->type == 'Not Listed') {
                $typeWhereClause = ' AND isFinal = 0 AND isUploaded = 0';
            } else {
                if ($request->type == 'Listed') {
                    $typeWhereClause = ' AND isUploaded = 1';
                } else {
                    if ($request->type == 'Approved') {
                        $typeWhereClause = ' AND is_approved = 1 AND last_imagecropper IS NULL';
                    } else {
                        if ($request->type == 'Image Cropped') {
                            $typeWhereClause = ' AND is_approved = 1 AND last_imagecropper IS NOT NULL';
                        }
                    }
                }
            }

            $type = $request->type;
        }
        if (trim($term) != '') {
            $termWhereClause = ' AND (sku LIKE "%'.$term.'%" OR id LIKE "%'.$term.'%")';
        }

        $cropped = $request->cropped == 'on' ? 'on' : '';
        if ($request->get('cropped') == 'on') {
            $croppedWhereClause = ' AND is_crop_approved = 1';
        }

        if ($request->users == 'on') {
            $users_products = User::role('Products Lister')->pluck('id');
            $users = [];
            foreach ($users_products as $user) {
                $users[] = $user;
            }
            $users_list = implode(',', $users);

            $userWhereClause = " AND products.id IN (SELECT product_id FROM user_products WHERE user_id IN ($users_list))";
            $stockWhereClause = '';
            $assigned_to_users = 'on';
        }

        $left_for_users = '';
        if ($request->left_products == 'on') {
            $userWhereClause = ' AND products.id NOT IN (SELECT product_id FROM user_products)';
            $stockWhereClause = ' AND stock >= 1 AND is_crop_approved = 1 AND is_crop_ordered = 1 AND is_image_processed = 1 AND isUploaded = 0 AND isFinal = 0';
            $left_for_users = 'on';
        }

        $messages = UserProductFeedback::where('action', 'LISTING_APPROVAL_REJECTED')->where('user_id', Auth::id())->with('product')->get();

        // if (Auth::user()->hasRole('Products Lister')) {
        //     $sql = '
        //                                     SELECT *, user_products.user_id as product_user_id,
        //                                     (SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at
        //                                     FROM products

        //                                     LEFT JOIN (
        //                                         SELECT user_id, product_id FROM user_products
        //                                         ) as user_products
        //                                     ON products.id = user_products.product_id

        //                                     LEFT JOIN (
        //                                         SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid
        //                                         ) AS remarks
        //                                     ON products.id = remarks.taskid

        //                                     WHERE stock>=1 AND is_approved = 0 AND is_listing_rejected = 0 AND is_crop_approved = 1 AND is_crop_ordered = 1 ' . $brandWhereClause . $colorWhereClause . $categoryWhereClause . $supplierWhereClause . $typeWhereClause . $termWhereClause . $croppedWhereClause . $stockWhereClause . ' AND id IN (SELECT product_id FROM user_products WHERE user_id = ' . Auth::id() . ')
        //                                      AND id NOT IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 60)
        //                                     ORDER BY listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, created_at DESC
        //         ';
        // } else {
        //     $sql = '
        //         SELECT *, user_products.user_id as product_user_id,
        //         (SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at
        //         FROM products

        //         LEFT JOIN (
        //             SELECT user_id, product_id FROM user_products
        //             ) as user_products
        //         ON products.id = user_products.product_id

        //         LEFT JOIN (
        //             SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid
        //             ) AS remarks
        //         ON products.id = remarks.taskid
        //         WHERE stock>=1 AND is_approved = 0 AND is_listing_rejected = 0  AND is_crop_approved = 1 AND is_crop_ordered = 1  ' . $stockWhereClause . $brandWhereClause . $colorWhereClause . $categoryWhereClause . $supplierWhereClause . $typeWhereClause . $termWhereClause . $croppedWhereClause . $userWhereClause . '
        //         ORDER BY listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, products.updated_at DESC
        //         ';
        // }
        // $new_products = DB::select($sql);

        if (Auth::user()->hasRole('Products Lister')) {
            $new_products = Product::select(
                'products.*',
                'user_products.user_id as product_user_id',
                DB::raw('(SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at')
            )
                ->leftJoin('user_products', 'products.id', '=', 'user_products.product_id')
                ->leftJoin(DB::raw('(SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid) AS remarks'), 'products.id', '=', 'remarks.taskid')
                ->where('stock', '>=', 1)
                ->where('is_approved', 0)
                ->where('is_listing_rejected', 0)
                ->where('is_crop_approved', 1)
                ->where('is_crop_ordered', 1)
                ->whereRaw($brandWhereClause)
                ->whereRaw($colorWhereClause)
                ->whereRaw($categoryWhereClause)
                ->whereRaw($supplierWhereClause)
                ->whereRaw($typeWhereClause)
                ->whereRaw($termWhereClause)
                ->whereRaw($croppedWhereClause)
                ->whereRaw($stockWhereClause)
                ->whereIn('id', function ($query) {
                    $query->select('product_id')
                        ->from('user_products')
                        ->where('user_id', Auth::id());
                })
                ->whereNotIn('id', function ($query) {
                    $query->select('product_id')
                        ->from('product_suppliers')
                        ->where('supplier_id', 60);
                })
                ->orderByRaw('listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, created_at DESC')
                ->get();
        } else {
            $new_products = Product::select(
                'products.*',
                'user_products.user_id as product_user_id',
                DB::raw('(SELECT mm1.created_at FROM remarks mm1 WHERE mm1.id = remark_id) AS remark_created_at')
            )
                ->leftJoin('user_products', 'products.id', '=', 'user_products.product_id')
                ->leftJoin(DB::raw('(SELECT MAX(id) AS remark_id, taskid FROM remarks WHERE module_type = "productlistings" GROUP BY taskid) AS remarks'), 'products.id', '=', 'remarks.taskid')
                ->where('stock', '>=', 1)
                ->where('is_approved', 0)
                ->where('is_listing_rejected', 0)
                ->where('is_crop_approved', 1)
                ->where('is_crop_ordered', 1)
                ->whereRaw($stockWhereClause)
                ->whereRaw($brandWhereClause)
                ->whereRaw($colorWhereClause)
                ->whereRaw($categoryWhereClause)
                ->whereRaw($supplierWhereClause)
                ->whereRaw($typeWhereClause)
                ->whereRaw($termWhereClause)
                ->whereRaw($croppedWhereClause)
                ->whereRaw($userWhereClause)
                ->orderByRaw('listing_approved_at DESC, category, is_crop_ordered DESC, remark_created_at DESC, products.updated_at DESC')
                ->get();
        }

        $products_count = count($new_products);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

        $new_products = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $selected_categories = $request->category ? $request->category : [1];

        $category_array = Category::renderAsArray();

        $userStats = [];
        $userStats['approved'] = ListingHistory::where('action', 'LISTING_APPROVAL')->where('user_id', Auth::user()->id)->count();
        $userStats['rejected'] = ListingHistory::where('action', 'LISTING_REJECTED')->where('user_id', Auth::user()->id)->count();

        $media_tags = config('constants.media_tags');

        return view('products.listing', [
            'products' => $new_products,
            'products_count' => $products_count,
            'colors' => $colors,
            'brands' => $brands,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'category_tree' => $category_tree,
            'categories_array' => $categories_array,
            'term' => $term,
            'brand' => $brand,
            'category' => $category,
            'color' => $color,
            'supplier' => $supplier,
            'type' => $type,
            'assigned_to_users' => $assigned_to_users,
            'cropped' => $cropped,
            'left_for_users' => $left_for_users,
            'category_array' => $category_array,
            'selected_categories' => $selected_categories,
            'messages' => $messages,
            'userStatus' => $userStats,
            'media_tags' => $media_tags,
        ]);
    }

    public function magentoConditionsCheck(Request $request)
    {
        if ($request->ajax()) {
            $fieldName = $request->get('filedname');
            $value = $request->get('value');

            $products = Product::query();

            $products = $products->where(function ($query) {
                $query->where('status_id', StatusHelper::$productConditionsChecked);
            });

            $products = $products->where('is_conditions_checked', 1);
            $products = $products->leftJoin('product_verifying_users as pvu', function ($join) {
                $join->on('pvu.product_id', 'products.id');
                $join->where('pvu.user_id', '!=', auth()->user()->id);
            });

            $products = $products->join('log_list_magentos as LLM', 'products.id', '=', 'LLM.product_id');
            $products = $products->leftJoin('store_websites as SW', 'LLM.store_website_id', '=', 'SW.id');
            $products = $products->leftJoin('categories as c', 'c.id', '=', 'products.category');

            $products = $products->leftJoin('status as s', function ($join) {
                $join->on('products.status_id', 's.id');
            });

            if ($request->get('id') != '') {
                $products = $products->where('products.id', $request->get('id'));
            }
            if ($request->get('name') != '') {
                $products = $products->where('products.name', $request->get('name'));
            }
            if ($request->get('title') != '') {
                $products = $products->where('SW.title', $request->get('title'));
            }
            if ($request->get('color') != '') {
                $products = $products->where('products.color', $request->get('color'));
            }
            if ($request->get('compositon') != '') {
                $products = $products->where('products.composition', $request->get('compositon'));
            }
            if ($request->get('status') != '') {
                $products = $products->where('products.status', $request->get('status'));
            }
            if ($request->get('price') != '') {
                $products = $products->where('products.price_usd', $request->get('price'));
                $products = $products->orWhere('products.price_usd_special', $request->get('price'));
            }

            $products = $products->where('isUploaded', 0);

            if (isset($fieldName)) {
                if ($fieldName === 'title') {
                    $products = $products->where("SW.$fieldName", 'LIKE', "%$value%");
                }
                if ($fieldName === 'category') {
                    $products = $products->where("categories.$fieldName", 'LIKE', "%$value%");
                } else {
                    $products = $products->where("products.$fieldName", 'LIKE', "%$value%");
                }
            }
            $products = $products->orderByDesc('llm_id');
            $products = $products->select(['products.*', 's.name as product_status', 'LLM.id as llm_id', 'LLM.message as llm_message', 'SW.title as sw_title', 'SW.id as sw_id']);
            $products = $products->paginate(20);
            $productsCount = $products->total();
            $imageCropperRole = auth()->user()->hasRole('ImageCropers');
            $categoryArray = Category::renderAsArray();
            $colors = (new Colors)->all();
            if (! Setting::has('auto_push_product')) {
                $auto_push_product = Setting::add('auto_push_product', 0, 'int');
            } else {
                $auto_push_product = Setting::get('auto_push_product');
            }
            $users = User::all();

            $view = (string) view('products.magento_conditions_check.list', compact('products', 'imageCropperRole', 'categoryArray', 'colors', 'auto_push_product', 'users', 'productsCount'));
            $return['view'] = $view;
            $return['productsCount'] = $productsCount;

            return response()->json(['status' => 200, 'data' => $return]);
        } else {
            return view('products.magento_conditions_check.index');
        }
    }

    public function autocompleteForFilter(Request $request): JsonResponse
    {
        $search = $request->get('filedname');
        $value = $request->get('value');

        if (auth()->user()->isReviwerLikeAdmin('final_listing')) {
            $products = Product::query();
        } else {
            $products = Product::query()->where('assigned_to', auth()->user()->id);
        }
        $products = $products->where(function ($query) {
            $query->where('status_id', StatusHelper::$finalApproval);
            $query->orWhere('status_id', StatusHelper::$productConditionsChecked);
        });

        $products = $products->where('is_conditions_checked', 1);
        $products = $products->where('is_push_attempted', 0);

        $products = $products->join('log_list_magentos as LLM', 'products.id', '=', 'LLM.product_id');
        $products = $products->leftJoin('store_websites as SW', 'LLM.store_website_id', '=', 'SW.id');
        $products = $products->leftJoin('categories as c', 'c.id', '=', 'products.category');

        $products = $products->leftJoin('status as s', function ($join) {
            $join->on('products.status_id', 's.id');
        });

        $products = $products->where('isUploaded', 0);
        $products = $products->orderByDesc('llm_id');
        $products = $products->select(['products.*', 's.name as product_status', 'LLM.id as llm_id', 'LLM.message as llm_message', 'SW.title as sw_title', 'c.title as category_title']);

        if ($search == 'title') {
            $products = $products->where("SW.$search", 'LIKE', "%$value%");
        }
        if ($search == 'category') {
            $products = $products->where('c.title', 'LIKE', "%$value%");
        } else {
            $products = $products->where("products.$search", 'LIKE', "%$value%");
        }

        $products = $products->groupBy('LLM.product_id', 'LLM.store_website_id');
        $products = $products->select(['products.*', 'LLM.id as llm_id', 'LLM.message as llm_message', 'SW.id as sw_id', 'SW.title as sw_title'])->get()->toArray();
        auth()->user()->hasRole('ImageCropers');
        Category::renderAsArray();
        (new Colors)->all();
        if (! Setting::has('auto_push_product')) {
            Setting::add('auto_push_product', 0, 'int');
        } else {
            Setting::get('auto_push_product');
        }

        return response()->json(['status' => 200, 'data' => array_unique(array_column($products, $search))]);
    }

    public function magentoPushStatusForMagentoCheck(Request $request): View
    {
        if ($request->ajax()) {
            $value = $request->get('value');
            $search = $request->get('fieldname');

            $products = Product::query();
            $products = $products->where(function ($query) {
                $query->where('status_id', StatusHelper::$pushToMagento);
                $query->orWhere('status_id', StatusHelper::$inMagento);
            });
            $products = $products->where('is_push_attempted', 1);
            $products = $products->leftJoin('product_verifying_users as pvu', function ($join) {
                $join->on('pvu.product_id', 'products.id');
                $join->where('pvu.user_id', '!=', auth()->user()->id);
            });

            $products = $products->leftJoin('status as s', function ($join) {
                $join->on('products.status_id', 's.id');
            });

            $products = $products->where('isUploaded', 1);
            $products = $products->leftJoin('categories as c', 'c.id', '=', 'products.category');

            if ($request->get('id') != '') {
                $products = $products->where('products.id', $request->get('id'));
            }
            if ($request->get('name') != '') {
                $products = $products->where('products.name', $request->get('name'));
            }
            if ($request->get('title') != '') {
                $products = $products->where('products.name', $request->get('title'));
            }
            if ($request->get('color') != '') {
                $products = $products->where('products.color', $request->get('color'));
            }
            if ($request->get('composition') != '') {
                $composition = $request->get('compositon');
                $products = $products->where('products.composition', 'LIKE', "%$composition%");
            }
            if ($request->get('status') != '') {
                $products = $products->where('products.status', $request->get('status'));
            }
            if ($request->get('price') != '') {
                $products = $products->where('products.price_usd', $request->get('price'));
                $products = $products->orWhere('products.price_usd_special', $request->get('price'));
                $products = $products->orWhere('products.price', $request->get('price'));
            }

            if (isset($search)) {
                if ($search === 'title' || $search === 'name') {
                    $products = $products->where('products.name', 'LIKE', "%$value%");
                }
                if ($search === 'category') {
                    $products = $products->where('categories.title', 'LIKE', "%$value%");
                } else {
                    $products = $products->where("products.$search", 'LIKE', "%$value%");
                }
            }

            $products = $products->select(['products.*', 's.name as product_status'])->paginate(10);
            $productsCount = $products->total();
            $imageCropperRole = auth()->user()->hasRole('ImageCropers');
            $categoryArray = Category::renderAsArray();
            $colors = (new Colors)->all();
            if (! Setting::has('auto_push_product')) {
                $auto_push_product = Setting::add('auto_push_product', 0, 'int');
            } else {
                $auto_push_product = Setting::get('auto_push_product');
            }
            $users = User::all();

            return view('products.magento_push_status.list', compact('products', 'imageCropperRole', 'categoryArray', 'colors', 'auto_push_product', 'users', 'productsCount'));
        } else {
            return view('products.magento_push_status.index');
        }
    }

    public function autocompleteSearchPushStatus(Request $request): JsonResponse
    {
        if (auth()->user()->isReviwerLikeAdmin('final_listing')) {
            $products = Product::query();
        } else {
            $products = Product::query()->where('assigned_to', auth()->user()->id);
        }
        $search = $request->get('filedname');
        $products = $products->where(function ($query) {
            $query->where('status_id', StatusHelper::$pushToMagento);
            $query->orWhere('status_id', StatusHelper::$inMagento);
        });
        $products = $products->where('is_conditions_checked', 1);
        $products = $products->where('is_push_attempted', 1);

        $products = $products->join('log_list_magentos as LLM', 'products.id', '=', 'LLM.product_id');
        $products = $products->leftJoin('store_websites as SW', 'LLM.store_website_id', '=', 'SW.id');

        $products = $products->where('isUploaded', 0);
        $products = $products->leftJoin('categories as c', 'c.id', '=', 'products.category');
        $searchValue = $request->get('search_value');

        if (isset($search)) {
            if ($search === 'title' || $search === 'name') {
                $products = $products->where('products.name', 'LIKE', "%$searchValue%");
            }
            if ($search === 'category') {
                $products = $products->where('c.title', 'LIKE', "%$searchValue%");
            } else {
                $products = $products->where("products.$search", 'LIKE', "%$searchValue%");
            }
        }

        $products = $products->select(['products.*', 's.name as product_status']);
        $products = $products->get()->toArray();
        

        return response()->json(['status' => 200, 'data' => array_unique(array_column($products, $search))]);
    }

    public function magentoConditionsCheckLogs($pId, $swId): JsonResponse
    {
        $logs = ProductPushErrorLog::where('product_id', '=', $pId)->where('store_website_id', '=', $swId)->orderByDesc('id')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    public function getLogListMagentoDetail($llm_id): JsonResponse
    {
        $logs = LogListMagento::where('id', $llm_id)->first();
        if (isset($logs) && ! empty($logs)) {
            return response()->json(['code' => 200, 'data' => $logs]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'msg' => 'Log details not found.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, Sizes $sizes): View
    {
        $data = [];

        $data['dnf'] = $product->dnf;
        $data['id'] = $product->id;
        $data['name'] = $product->name;
        $data['short_description'] = $product->short_description;
        $data['activities'] = $product->activities;
        $data['scraped'] = $product->scraped_products;

        $data['measurement_size_type'] = $product->measurement_size_type;
        $data['lmeasurement'] = $product->lmeasurement;
        $data['hmeasurement'] = $product->hmeasurement;
        $data['dmeasurement'] = $product->dmeasurement;

        $data['size'] = $product->size;
        $data['size_value'] = $product->size_value;
        $data['sizes_array'] = $sizes->all();

        $data['composition'] = $product->composition;
        $data['sku'] = $product->sku;
        $data['made_in'] = $product->made_in;
        $data['brand'] = $product->brand;
        $data['color'] = $product->color;
        $data['price'] = $product->price;
        $data['status'] = $product->status_id;
        $data['euro_to_inr'] = $product->euro_to_inr;
        $data['price_inr'] = $product->price_inr;
        $data['price_inr_special'] = $product->price_inr_special;

        $data['isApproved'] = $product->isApproved;
        $data['rejected_note'] = $product->rejected_note;
        $data['isUploaded'] = $product->isUploaded;
        $data['isFinal'] = $product->isFinal;
        $data['stock'] = $product->stock;
        $data['reason'] = $product->rejected_note;

        $data['product_link'] = $product->product_link;
        $data['supplier'] = $product->supplier;
        $data['supplier_link'] = $product->supplier_link;
        $data['description_link'] = $product->description_link;
        $data['location'] = $product->location;

        $data['suppliers'] = '';
        $data['more_suppliers'] = [];

        foreach ($product->suppliers as $key => $supplier) {
            if ($key == 0) {
                $data['suppliers'] .= $supplier->supplier;
            } else {
                $data['suppliers'] .= ", $supplier->supplier";
            }
        }

        // $data['more_suppliers'] = DB::select('SELECT sp.url as link,s.supplier as name
        //                     FROM `scraped_products` sp
        //                     JOIN scrapers sc on sc.scraper_name=sp.website
        //                     JOIN suppliers s ON s.id=sc.supplier_id
        //                     WHERE last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY) and sp.sku = :sku', ['sku' => $product->sku]);
        $data['more_suppliers'] = ScrapedProducts::select('scraped_products.url as link', 's.supplier as name')
            ->join('scrapers AS sc', 'sc.scraper_name', '=', 'scraped_products.website')
            ->join('suppliers AS s', 's.id', '=', 'sc.supplier_id')
            ->where('last_inventory_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)'))
            ->where('scraped_products.sku', '=', $product->sku)
            ->get();

        $data['images'] = $product->getMedia(config('constants.media_tags'));

        $data['categories'] = $product->category ? CategoryController::getCategoryTree($product->category) : '';

        $data['has_reference'] = ScrapedProducts::where('sku', $product->sku)->first() ? true : false;

        $data['product'] = $product;

        return view('partials.show', $data);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $selected_products = json_decode($request->selected_products, true);
        $category = $request->category[0];

        foreach ($selected_products as $id) {
            $product = Product::find($id);
            $product->category = $category;
            $product->save();

            $lh = new ListingHistory;
            $lh->user_id = Auth::user()->id;
            $lh->product_id = $id;
            $lh->content = ['Category updated', $category];
            $lh->save();
        }

        return redirect()->back()->withSuccess('You have successfully bulk updated products!');
    }

    public function updateName(Request $request, $id): Response
    {
        $product = Product::find($id);
        $product->name = $request->name;
        $product->save();

        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Name updated', $request->get('name')];
        $lh->save();

        return response('success');
    }

    public function updateDescription(Request $request, $id): Response
    {
        $product = Product::find($id);
        $product->short_description = $request->description;
        $product->save();

        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Description updated', $request->get('description')];
        $lh->save();

        return response('success');
    }

    public function updateComposition(Request $request, $id): Response
    {
        $product = Product::find($id);
        $product->composition = $request->composition;
        $product->save();

        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Composition updated', $request->get('composition')];
        $lh->save();

        return response('success');
    }

    public function updateColor(Request $request, $id): Response
    {
        $product = Product::find($id);

        if ($product) {
            $productColHis = new ProductColorHistory;
            $productColHis->user_id = Auth::user()->id;
            $productColHis->color = $request->color;
            $productColHis->old_color = $product->color;
            $productColHis->product_id = $product->id;
            $productColHis->save();
        }

        $originalColor = $product->color;
        $product->color = $request->color;
        $product->save();

        ProductStatus::pushRecord($product->id, 'MANUAL_COLOR');

        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Color updated', $request->get('color')];
        $lh->save();

        if (! $originalColor) {
            return response('success');
        }

        $color = (new Colors)->getID($originalColor);
        if ($color) {
            return response('success');
        }

        $colorReference = ColorReference::where('original_color', $originalColor)->first();
        if ($colorReference) {
            return response('success');
        }

        $colorReference = new ColorReference;
        $colorReference->original_color = $originalColor;
        $colorReference->brand_id = $product->brand;
        $colorReference->erp_color = $request->get('color');
        $colorReference->save();

        return response('success');
    }

    public function updateCategory(Request $request, $id): Response
    {
        $product = Product::find($id);

        if ($product) {
            $productCatHis = new ProductCategoryHistory;
            $productCatHis->user_id = Auth::user()->id;
            $productCatHis->category_id = $request->category;
            $productCatHis->old_category_id = $product->category;
            $productCatHis->product_id = $product->id;
            $productCatHis->save();

            ProductStatus::pushRecord($product->id, 'MANUAL_CATEGORY');
        }
        $product->category = $request->category;
        $product->save();
        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Category updated', $request->get('category')];
        $lh->save();

        return response('success');
    }

    public function updateSize(Request $request, $id): Response
    {
        $product = Product::find($id);
        $product->size = is_array($request->size) && count($request->size) > 0 ? implode(',', $request->size) : '';
        $product->lmeasurement = $request->lmeasurement;
        $product->hmeasurement = $request->hmeasurement;
        $product->dmeasurement = $request->dmeasurement;
        $product->save();

        $lh = new ListingHistory;
        $lh->user_id = Auth::user()->id;
        $lh->product_id = $id;
        $lh->content = ['Sizes updated', $request->get('lmeasurement').' X '.$request->get('hmeasurement').' X '.$request->get('dmeasurement')];
        $lh->save();

        return response('success');
    }

    public function updatePrice(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $product->price = $request->price;

        if (! empty($product->brand)) {
            $product->price_inr = $this->euroToInr($product->price, $product->brand);
            $product->price_inr_special = $this->calculateSpecialDiscount($product->price_inr_special, $product->brand);
        }

        $product->save();

        $l = new ListingHistory;
        $l->user_id = Auth::user()->id;
        $l->product_id = $id;
        $l->content = ['Price updated', $product->price];

        return response()->json([
            'price_inr' => $product->price_inr,
            'price_inr_special' => $product->price_inr_special,
        ]);
    }

    public function quickDownload($id): BinaryFileResponse
    {
        $product = Product::find($id);

        $products_array = [];

        if ($product->hasMedia(config('constants.media_tags'))) {
            foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                $path = public_path('uploads').'/'.$image->filename.'.'.$image->extension;
                array_push($products_array, $path);
            }
        }

        return response()->download(public_path("$product->sku.zip"))->deleteFileAfterSend();
    }

    public function quickUpload(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $image_url = '';

        if ($request->hasFile('images')) {
            $product->detachMediaTags(config('constants.media_tags'));

            foreach ($request->file('images') as $key => $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
                    ->upload();
                $product->attachMedia($media, config('constants.media_tags'));

                if ($key == 0) {
                    $image_url = getMediaUrl($media);
                }
            }

            $product->last_imagecropper = Auth::id();
            $product->save();
        }

        return response()->json([
            'image_url' => $image_url,
            'last_imagecropper' => $product->last_imagecropper,
        ]);
    }

    public function calculateSpecialDiscount($price, $brand)
    {
        $dis_per = BrandController::getDeductionPercentage($brand);
        $dis_price = $price - ($price * $dis_per) / 100;

        return round($dis_price, -3);
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

    public function listMagento(Request $request, $id): JsonResponse
    {
        try {
            // code...
            // Get product by ID
            $product = Product::find($id);
            ImageApprovalPushProductOnlyJob::dispatch($product)->onQueue('imageapprovalpushproductonly');

            return response()->json([
                'result' => 'queuedForDispatch',
                'status' => 'listed',
            ]);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            $logId = LogListMagento::log($id, $msg, 'info');
            ProductPushErrorLog::log('', $id, $msg, 'php', $logId->store_website_id, '', '', $logId->id);
            $this->updateLogUserId($logId);

            // Return error response by default
            return response()->json([
                'result' => 'productNotFound',
                'status' => 'error',
            ]);
        }
    }

    public function pushProductTest(Request $request): RedirectResponse
    {
        try {
            $products = ProductHelper::getProducts(StatusHelper::$finalApproval, 1);
            $product = $products->first();
            TestPushProductOnlyJob::dispatchSync($product);

            return Redirect::Back()->with('success', 'Push product test initiated for this product #'.$product->id.'. You can check the logs on <a href="'.route('list.magento.logging').'">Log List Magento</a> page.');
        } catch (Exception $e) {
            $msg = $e->getMessage();

            $logId = LogListMagento::log($product->id, $msg, 'info');
            ProductPushErrorLog::log('', $product->id, $msg, 'php', $logId->store_website_id, '', '', $logId->id);
            $this->updateLogUserId($logId);

            return Redirect::Back()->with('error', 'Push product test failed for this product #'.$product->id.'. You can check the logs on <a href="'.route('list.magento.logging').'">Log List Magento</a> page.');
        }
    }

    public function multilistMagento(Request $request): JsonResponse
    {
        $data = $request->data;

        foreach ($data as $id) {
            try {
                //code...
                // Get product by ID
                $mode = $request->get('mode', 'product-push');
                $product = Product::find($id);
                //check for hscode
                $product->hsCode($product->category, $product->composition);
                // If we have a product, push it to Magento
                if ($product !== null) {
                    //website search
                    $websiteArrays = ProductHelper::getStoreWebsiteName($product->id);
                    if (count($websiteArrays) == 0) {
                        Log::info('Product started '.$product->id.' No website found');
                        $msg = 'No website found for  Brand: '.$product->brand.' and Category: '.$product->category;
                        $logId = LogListMagento::log($product->id, 'No website found '.$product->id, 'info');
                        ProductPushErrorLog::log('', $product->id, $msg, 'error', $logId->store_website_id, '', '', $logId->id);
                        $this->updateLogUserId($logId);
                    } else {
                        $i = 1;
                        foreach ($websiteArrays as $websiteArray) {
                            $website = StoreWebsite::find($websiteArray);
                            if ($website) {
                                Log::info('Product started website found For website'.$website->website);
                                $log = LogListMagento::log($product->id, 'Start push to magento for product id '.$product->id, 'info', $website->id, 'waiting');
                                //currently we have 3 queues assigned for this task.
                                $log->sync_status = 'waiting';
                                $log->queue = Helpers::createQueueName($website->title);
                                $log->save();
                                PushToMagento::dispatch($product, $website, $log, $mode)->onQueue($log->queue);
                                $i++;
                            }
                        }
                    }

                    // Update the product, so it doesn't show up in final listing
                    $product->isUploaded = 1;
                    $product->save();

                    // Return response
                    return response()->json([
                        'result' => 'queuedForDispatch',
                        'status' => 'listed',
                    ]);
                }
            } catch (Exception $e) {
                //throw $th;
                $msg = $e->getMessage();

                $logId = LogListMagento::log($id, $msg, 'info');
                ProductPushErrorLog::log('', $id, $msg, 'php', $logId->store_website_id, '', '', $logId->id);
                $this->updateLogUserId($logId);
            }
        }

        return response()->json([
            'result' => 'queuedForDispatch',
            'status' => 'listed',
        ]);
    }

    public function updateLogUserId($logId)
    {
        $updateLogUser = LogListMagento::find($logId->id);
        if ($updateLogUser) {
            $updateLogUser->user_id = Auth::id();
            $updateLogUser->save();
        }
    }

    public function unlistMagento(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $magentoHelper = new MagentoHelper;
        $result = $magentoHelper->magentoUnlistProduct($product);

        return response()->json([
            'result' => $result,
            'status' => 'unlisted',
        ]);
    }

    public function approveMagento(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $magentoHelper = new MagentoHelper;
        $result = $magentoHelper->magentoUpdateStatus($product);

        return response()->json([
            'result' => $result,
            'status' => 'approved',
        ]);
    }

    public function updateMagento(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $magentoHelper = new MagentoHelper;
        $result = $magentoHelper->magentoProductUpdate($product);

        return response()->json([
            'result' => $result[1],
            'status' => 'updated',
        ]);
    }

    public function updateMagentoProduct(Request $request): RedirectResponse
    {
        $product = Product::find($request->update_product_id);

        //////      Update Local Product    //////
        $product->name = $request->name;
        $product->price = $request->price;
        $product->price_eur_special = $request->price_eur_special;
        $product->price_eur_discounted = $request->price_eur_discounted;
        $product->price_inr = $request->price_inr;
        $product->price_inr_special = $request->price_inr_special;
        $product->price_inr_discounted = $request->price_inr_discounted;
        $product->measurement_size_type = $request->measurement_size_type;
        $product->lmeasurement = $request->lmeasurement;
        $product->hmeasurement = $request->hmeasurement;
        $product->dmeasurement = $request->dmeasurement;
        $product->composition = $request->composition;
        $product->size = $request->size;
        $product->short_description = $request->short_description;
        $product->made_in = $request->made_in;
        $product->brand = $request->brand;
        $product->category = $request->category;
        $product->supplier = $request->supplier;
        $product->supplier_link = $request->supplier_link;
        $product->product_link = $request->product_link;
        $product->updated_at = time();

        if ($product->update()) {
            if ($product->status_id == 12) {
                ///////     Update Magento Product  //////
                $options = [
                    'trace' => true,
                    'connection_timeout' => 120,
                    'wsdl_cache' => WSDL_CACHE_NONE,
                ];

                $proxy = new \SoapClient(config('magentoapi.url'), $options);
                $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

                $sku = $product->sku.$product->color;
                try {
                    $magento_product = json_decode(json_encode($proxy->catalogProductInfo($sessionId, $sku)), true);
                    if ($magento_product) {
                        if (! empty($product->size)) {
                            $associated_skus = [];
                            $new_variations = 0;
                            $sizes_array = explode(',', $product->size);
                            $categories = CategoryController::getCategoryTreeMagentoIds($product->category);

                            //////      Add new Variations  //////
                            foreach ($sizes_array as $size) {
                                $error_message = '';

                                try {
                                    $simple_product = json_decode(json_encode($proxy->catalogProductInfo($sessionId, $sku.'-'.$size)), true);
                                } catch (Exception $e) {
                                    $error_message = $e->getMessage();
                                }

                                if ($error_message == 'Product not exists.') {
                                    // CREATE VARIATION
                                    $productData = [
                                        'categories' => $categories,
                                        'name' => $product->name,
                                        'description' => '<p></p>',
                                        'short_description' => $product->short_description,
                                        'website_ids' => [1],
                                        // Id or code of website
                                        'status' => $magento_product['status'],
                                        // 1 = Enabled, 2 = Disabled
                                        'visibility' => 1,
                                        // 1 = Not visible, 2 = Catalog, 3 = Search, 4 = Catalog/Search
                                        'tax_class_id' => 2,
                                        // Default VAT
                                        'weight' => 0,
                                        'stock_data' => [
                                            'use_config_manage_stock' => 1,
                                            'manage_stock' => 1,
                                        ],
                                        'price' => $product->price_eur_special,
                                        // Same price than configurable product, no price change
                                        'special_price' => $product->price_eur_discounted,
                                        'additional_attributes' => [
                                            'single_data' => [
                                                ['key' => 'msrp', 'value' => $product->price],
                                                ['key' => 'composition', 'value' => $product->composition],
                                                ['key' => 'color', 'value' => $product->color],
                                                ['key' => 'sizes', 'value' => $size],
                                                ['key' => 'country_of_manufacture', 'value' => $product->made_in],
                                                ['key' => 'brands', 'value' => BrandController::getBrandName($product->brand)],
                                            ],
                                        ],
                                    ];
                                    // Creation of product simple
                                    $proxy->catalogProductCreate($sessionId, 'simple', 14, $sku.'-'.$size, $productData);
                                    $new_variations = 1;
                                } else {
                                    // SIMPLE PRODUCT EXISTS
                                    $simple_product['status'];
                                    // 1 = Enabled, 2 = Disabled
                                }
                                $associated_skus[] = $sku.'-'.$size;
                            }

                            if ($new_variations == 1) {
                                // IF THERE WAS NEW VARIATION CREATED, UPDATED THE MAIN PRODUCT
                                /**
                                 * Configurable product
                                 */
                                $productData = [
                                    'associated_skus' => $associated_skus,
                                ];
                                // Creation of configurable product
                                $proxy->catalogProductUpdate($sessionId, $sku, $productData);
                            }
                            $messages = 'Product updated successfully';

                            return Redirect::Back()
                                ->with('success', $messages);
                        } else {
                            $messages[] = 'Sorry! No sizes found for magento update';

                            return Redirect::Back()
                                ->withErrors($messages);
                        }
                    } else {
                        $messages[] = 'Sorry! Product not found in magento';

                        return Redirect::Back()
                            ->withErrors($messages);
                    }
                } catch (Exception $e) {
                    $messages[] = $e->getMessage();

                    return Redirect::Back()
                        ->withErrors($messages);
                }
            } else {
                $messages = 'Product updated successfuly';

                return Redirect::Back()
                    ->with('success', $messages);
            }
        } else {
            $messages[] = 'Sorry! Please try again';

            return Redirect::Back()
                ->withErrors($messages);
        }

        return Redirect::Back();
    }

    public function approveProduct(Request $request, $id = null): JsonResponse
    {
        if ($id !== null) {
            $product = Product::find($id);

            $product->is_approved = 1;
            $product->approved_by = Auth::user()->id;
            $product->listing_approved_at = Carbon::now()->toDateTimeString();
            $product->save();

            $l = new ListingHistory;
            $l->user_id = Auth::user()->id;
            $l->product_id = $product->id;
            $l->action = 'LISTING_APPROVAL';
            $l->content = ['action' => 'LISTING_APPROVAL', 'message' => 'Listing approved!'];
            $l->save();

            // once product approved the remove from the edititing list
            $productVUser = ProductVerifyingUser::where('product_id', $id)->first();
            if ($productVUser) {
                $productVUser->delete();
            }

            ActivityConroller::create($product->id, 'productlister', 'create');
        } else {
            $ids = $request->ids;
            $products = Product::whereIn('id', explode(',', $ids))->get();
            foreach ($products as $product) {
                $product->is_approved = 1;
                $product->approved_by = Auth::user()->id;
                $product->listing_approved_at = Carbon::now()->toDateTimeString();
                $product->save();

                $l = new ListingHistory;
                $l->user_id = Auth::user()->id;
                $l->product_id = $product->id;
                $l->action = 'LISTING_APPROVAL';
                $l->content = ['action' => 'LISTING_APPROVAL', 'message' => 'Listing approved!'];
                $l->save();

                // once product approved the remove from the edititing list
                $productVUser = ProductVerifyingUser::where('product_id', $id)->first();
                if ($productVUser) {
                    $productVUser->delete();
                }
            }

            // once product approved the remove from the edititing list
            $productVUser = ProductVerifyingUser::where('product_id', $id)->first();
            if ($productVUser) {
                $productVUser->delete();
            }

            ActivityConroller::create($product->id, 'productlister', 'create');
        }

        return response()->json([
            'result' => true,
            'status' => 'is_approved',
            'success' => 'Products Approved successfully',
        ]);
    }

    public function submitForApproval(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        $product->status = StatusHelper::$submitForApproval;
        $product->save();

        $l = new ListingHistory;
        $l->user_id = Auth::user()->id;
        $l->product_id = $product->id;
        $l->action = 'SUBMIT_FOR_APPROVAL';
        $l->content = ['action' => 'SUBMIT_FOR_APPROVAL', 'message' => 'User has submitted for approval!'];
        $l->save();

        return response()->json([
            'result' => true,
            'status' => 'submit_for_approval',
        ]);
    }

    public function archive($id): RedirectResponse
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->back()
            ->with('success', 'Product archived successfully');
    }

    public function restore($id): RedirectResponse
    {
        $product = Product::withTrashed()->find($id);
        $product->restore();

        return redirect()->back()
            ->with('success', 'Product restored successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $product = Product::find($id);
        $product->forceDelete();

        return redirect()->back()
            ->with('success', 'Product deleted successfully');
    }

    public function originalCategory($id): JsonResponse
    {
        $product = Product::find($id);
        $referencesCategory = '';

        if (isset($product->scraped_products)) {
            // starting to see that howmany category we going to update
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
                $category = $product->scraped_products->properties['category'];
                if (is_array($category)) {
                    $referencesCategory = implode(' > ', $category);
                }
            }

            $scrapedProductSkuArray = [];

            if (! empty($referencesCategory)) {
                $productSupplier = $product->supplier;
                $supplier = Supplier::where('supplier', $productSupplier)->first();
                if ($supplier && $supplier->scraper) {
                    $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();
                    foreach ($scrapedProducts as $scrapedProduct) {
                        $products = $scrapedProduct->properties['category'];
                        if (is_array($products)) {
                            $list = implode(' > ', $products);
                            if (strtolower($referencesCategory) == strtolower($list)) {
                                $scrapedProductSkuArray[] = $scrapedProduct->sku;
                            }
                        }
                    }
                }
            }

            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
                return response()->json(['success', $referencesCategory, count($scrapedProductSkuArray)]);
            } else {
                return response()->json(['message', 'Category Is Not Present']);
            }
        } else {
            return response()->json(['message', 'Category Is Not Present']);
        }
    }

    public function changeAllCategoryForAllSupplierProducts(Request $request, $id): JsonResponse
    {
        UpdateScrapedCategory::dispatch([
            'product_id' => $id,
            'category_id' => $request->category,
            'user_id' => Auth::user()->id,
        ])->onQueue('supplier_products');

        return response()->json(['success', 'Product category has been sent for the update']);
    }

    public function attachProducts($model_type, $model_id, $type, $customer_id, Request $request): View
    {
        $roletype = $request->input('roletype') ?? 'Sale';
        $products = Product::where('stock', '>=', 1)
            ->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'isApproved', 'stage', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(Setting::get('pagination'));

        $doSelection = true;
        $customer_id = $customer_id ?? null;

        if ($type == 'images') {
            $attachImages = true;
        } else {
            $attachImages = false;
        }

        if ($model_type == 'broadcast-images') {
            $attachImages = true;
            $doSelection = false;
        }

        if (Order::find($model_id)) {
            $selected_products = self::getSelectedProducts($model_type, $model_id);
        } else {
            $selected_products = [];
        }

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])
            ->selected(1)
            ->renderAsDropdown();

        $attach_image_tag = config('constants.attach_image_tag');

        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors)->all();
        $stage = new Stage;

        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'roletype', 'model_id', 'selected_products', 'doSelection', 'model_type', 'category_selection', 'attachImages', 'customer_id', 'attach_image_tag', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'));
    }

    public function attachImages(Request $request, $model_type, $model_id = null, $status = null, $assigned_user = null)
    {
        if ($model_type == 'customer') {
            $customerId = $model_id;
        } else {
            $customerId = null;
        }
        $roletype = $request->input('roletype') ?? 'Sale';
        $term = $request->input('term');
        if ($request->total_images) {
            $perPageLimit = $request->total_images;
        } else {
            $perPageLimit = $request->get('per_page');
        }

        if (Order::find($model_id)) {
            $selected_products = self::getSelectedProducts($model_type, $model_id);
        } else {
            $selected_products = [];
        }
        if (empty($perPageLimit)) {
            $perPageLimit = Setting::get('pagination');
        }

        $products = (new Product)->newQuery()->latest();
        $products->where('has_mediables', 1);

        if (isset($request->brand[0])) {
            if ($request->brand[0] != null) {
                $products = $products->whereIn('brand', $request->brand);
            }
        }

        if (isset($request->color[0])) {
            if ($request->color[0] != null) {
                $products = $products->whereIn('color', $request->color);
            }
        }

        if (isset($request->category[0])) {
            if ($request->category[0] != null && $request->category[0] != 1) {
                $category_children = [];

                foreach ($request->category as $category) {
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
                }

                $products = $products->whereIn('category', $category_children);
            }
        }

        if ($request->price_min != null && $request->price_min != 0) {
            $products = $products->where('price_inr_special', '>=', $request->price_min);
        }

        if ($request->price_max != null) {
            $products = $products->where('price_inr_special', '<=', $request->price_max);
        }

        if ($request->discounted_percentage_min != null && $request->discounted_percentage_min != 0) {
            $products = $products->where('discounted_percentage', '>=', $request->discounted_percentage_min);
        }

        if ($request->discounted_percentage_max != null) {
            $products = $products->where('discounted_percentage', '<=', $request->discounted_percentage_max);
        }

        if (isset($request->supplier[0])) {
            if ($request->supplier[0] != null) {
                $suppliers_list = implode(',', $request->supplier);

                $products = $products->whereRaw("products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
            }
        }

        if (trim($request->size) != '') {
            $products = $products->whereNotNull('size')->where(function ($query) use ($request) {
                $query->where('size', $request->size)->orWhere('size', 'LIKE', "%$request->size,")->orWhere('size', 'LIKE', "%,$request->size,%");
            });
        }

        if (isset($request->location[0])) {
            if ($request->location[0] != null) {
                $products = $products->whereIn('location', $request->location);
            }
        }

        if (isset($request->type[0])) {
            if ($request->type[0] != null && is_array($request->type)) {
                if (count($request->type) > 1) {
                    $products = $products->where(function ($query) {
                        $query->where('is_scraped', 1)->orWhere('status', 2);
                    });
                } else {
                    if ($request->type[0] == 'scraped') {
                        $products = $products->where('is_scraped', 1);
                    } elseif ($request->type[0] == 'imported') {
                        $products = $products->where('status', 2);
                    } else {
                        $products = $products->where('isUploaded', 1);
                    }
                }
            }
        }

        if ($request->date != '') {
            if (isset($products)) {
                if ($request->type[0] != null && $request->type[0] == 'uploaded') {
                    $products = $products->where('is_uploaded_date', 'LIKE', "%$request->date%");
                } else {
                    $products = $products->where('created_at', 'LIKE', "%$request->date%");
                }
            }
        }

        if (trim($term) != '') {
            $products = $products->where(function ($query) use ($term) {
                $query->where('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%")
                    ->orWhere('name', 'LIKE', "%$term%")
                    ->orWhere('short_description', 'LIKE', "%$term%");
                if ($term == -1) {
                    $query = $query->orWhere('isApproved', -1);
                }

                $brand_id = Brand::where('name', 'LIKE', "%$term%")->value('id');
                if ($brand_id) {
                    $query = $query->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                $category_id = Category::where('title', 'LIKE', "%$term%")->value('id');
                if ($category_id) {
                    $query = $query->orWhere('category', $category_id);
                }
            });
            if ($roletype != 'Selection' && $roletype != 'Searcher') {
                $products = $products->whereNull('dnf');
            }
        }

        if (isset($request->ids[0])) {
            if ($request->ids[0] != null) {
                $products = $products->whereIn('id', $request->ids);
            }
        }

        if ($request->quick_product === 'true') {
            $products = $products->where('quick_product', 1);
        }

        // assing product to varaible so can use as per condition for join table media
        if ($request->quick_product !== 'true') {
            $products = $products->whereRaw("(stock > 0 OR (supplier ='In-Stock'))");
        }

        if ($request->drafted_product == 'on') {
            $products = $products->whereRaw('quick_product = 1');
        }

        // if source is attach_media for search then check product has image exist or not
        if ($request->get('unsupported', null) != '') {
            $products = $products->join('mediables', function ($query) {
                $query->on('mediables.mediable_id', 'products.id')->where('mediable_type', Product::class);
            });

            $mediaIds = Media::where('aggregate_type', 'image')->join('mediables', function ($query) {
                $query->on('mediables.media_id', 'media.id')->where('mediables.mediable_type', Product::class);

            })->whereNotIn('extension', config('constants.gd_supported_files'))->select('id')->pluck('id')->toArray();

            $products = $products->whereIn('mediables.media_id', $mediaIds);
            $products = $products->groupBy('products.id');
        }

        if (! empty($request->quick_sell_groups) && is_array($request->quick_sell_groups)) {
            $products = $products->whereRaw('(id in (select product_id from product_quicksell_groups where quicksell_group_id in ('.implode(',', $request->quick_sell_groups).') ))');
        }

        // brand filter count start
        $brandGroups = clone $products;
        $brandGroups = $brandGroups->groupBy('brand')->select([DB::raw('count(id) as total_product'), 'brand'])->pluck('total_product', 'brand')->toArray();
        $brandIds = array_values(array_filter(array_keys($brandGroups)));

        $brandsModel = Brand::whereIn('id', $brandIds)->pluck('name', 'id')->toArray();

        $countBrands = [];
        if (! empty($brandGroups) && ! empty($brandsModel)) {
            foreach ($brandGroups as $key => $count) {
                $countBrands[] = [
                    'id' => $key,
                    'name' => ! empty($brandsModel[$key]) ? $brandsModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }
        if ($request->category) {
            try {
                $filtered_category = $request->category;
            } catch (Exception $e) {
                $filtered_category = [1];
            }
        } else {
            $filtered_category = [1];
        }

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple-cat-list input-lg select-multiple', 'multiple' => true, 'data-placeholder' => 'Select Category..'])
            ->selected($filtered_category)
            ->renderAsDropdown();

        // category filter start count
        $categoryGroups = clone $products;
        $categoryGroups = $categoryGroups->groupBy('category')->select([DB::raw('count(id) as total_product'), 'category'])->pluck('total_product', 'category')->toArray();
        $categoryIds = array_values(array_filter(array_keys($categoryGroups)));

        $categoryModel = Category::whereIn('id', $categoryIds)->pluck('title', 'id')->toArray();
        $countCategory = [];
        if (! empty($categoryGroups) && ! empty($categoryModel)) {
            foreach ($categoryGroups as $key => $count) {
                $countCategory[] = [
                    'id' => $key,
                    'name' => ! empty($categoryModel[$key]) ? $categoryModel[$key] : 'N/A',
                    'count' => $count,
                ];
            }
        }

        // suppliers filter start count/
        $suppliersGroups = clone $products;
        $all_product_ids = $suppliersGroups->pluck('id')->toArray();
        $countSuppliers = [];
        if (! empty($all_product_ids)) {
            $suppliersGroups = Product::leftJoin('product_suppliers', 'product_id', '=', 'products.id')
                ->where('products.id', $all_product_ids)
                ->groupBy('product_suppliers.supplier_id')
                ->select([DB::raw('count(products.id) as total_product'), 'product_suppliers.supplier_id'])
                ->pluck('total_product', 'supplier_id')
                ->toArray();
            $suppliersIds = array_values(array_filter(array_keys($suppliersGroups)));
            $suppliersModel = Supplier::whereIn('id', $suppliersIds)->pluck('supplier', 'id')->toArray();

            if (! empty($suppliersGroups)) {
                foreach ($suppliersGroups as $key => $count) {
                    $countSuppliers[] = [
                        'id' => $key,
                        'name' => ! empty($suppliersModel[$key]) ? $suppliersModel[$key] : 'N/A',
                        'count' => $count,
                    ];
                }
            }
        }

        // select fields..
        $products = $products->select(['products.id', 'name', 'short_description', 'color', 'sku', 'products.category', 'products.size', 'price_eur_special', 'price_inr_special', 'supplier', 'purchase_status', 'products.created_at']);

        if ($request->get('is_on_sale') == 'on') {
            $products = $products->where('is_on_sale', 1);
        }

        if ($request->has('limit')) {
            $perPageLimit = ($request->get('limit') == 'all') ? $products->get()->count() : $request->get('limit');
        }

        $categoryAll = Category::with('childs.childLevelSencond')->where('parent_id', 0)->get();

        foreach ($categoryAll as $category) {
            $categoryArray[] = ['id' => $category->id, 'value' => $category->title];
            foreach ($category->childs as $child) {
                $categoryArray[] = ['id' => $child->id, 'value' => $category->title.' '.$child->title];
                if ($child->childLevelSencond != null) {
                    foreach ($child->childLevelSencond as $grandChild) {
                        $categoryArray[] = ['id' => $grandChild->id, 'value' => $category->title.' '.$child->title.' '.$grandChild->title];
                    }
                }
            }
        }

        if ($request->total_images) {
            $products = $products->limit($request->total_images)->get();
            $products = new LengthAwarePaginator($products, count($products), $request->total_images, 1, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]);
        } else {
            $products = $products->paginate($perPageLimit);
        }

        $brand = $request->brand;
        $products_count = $products->total();
        $all_product_ids = [];
        $from = request('from', '');
        if ($request->submit_type == 'send-to-approval') {
            $products_ids_cloned = clone $products;
            $product_ids = $products_ids_cloned->pluck('id');
            $inserted = 0;
            if (count($product_ids) > 0 && $customerId) {
                $json_brands = json_encode($request->brand);
                $json_categories = json_encode($request->category);
                $json_supplier = json_encode($request->supplier);
                $json_color = json_encode($request->color);
                $json_location = json_encode($request->location);
                $size = $request->size;
                $suggestedProducts = new SuggestedProduct;
                $suggestedProducts->customer_id = $customerId;
                if ($json_brands != 'null' && $json_brands != '') {
                    $suggestedProducts->brands = $json_brands;
                }
                if ($json_categories != 'null' && $json_categories != '') {
                    $suggestedProducts->categories = $json_categories;
                }
                if ($json_color != 'null' && $json_color != '') {
                    $suggestedProducts->color = $json_color;
                }
                if ($json_supplier != 'null' && $json_supplier != '') {
                    $suggestedProducts->supplier = $json_supplier;
                }
                if ($json_location != 'null' && $json_location != '') {
                    $suggestedProducts->location = $json_location;
                }
                $suggestedProducts->size = $size;
                $suggestedProducts->total = $perPageLimit;
                $suggestedProducts->save();
                $suggestedProductId = $suggestedProducts->id;

                $data_to_insert = [];
                foreach ($product_ids as $id) {
                    $exists = SuggestedProductList::where('customer_id', $customerId)->where('product_id', $id)->where('date', date('Y-m-d'))->first();
                    if (! $exists) {
                        $pr = Product::find($id);
                        if ($pr->hasMedia(config('constants.attach_image_tag'))) {
                            $data_to_insert[] = [
                                'suggested_products_id' => $suggestedProductId,
                                'customer_id' => $customerId,
                                'product_id' => $id,
                                'date' => date('Y-m-d'),
                            ];
                        }
                        $category_brand_count = ErpLeads::where('category_id', $pr->category)->where('brand_id', $pr->brand)->count();
                        if ($category_brand_count === 0) {
                            $erp_lead = new ErpLeads;
                            $erp_lead->lead_status_id = 1;
                            $erp_lead->customer_id = $customerId;
                            $erp_lead->product_id = $id;
                            $erp_lead->store_website_id = 15;
                            $erp_lead->category_id = $pr->category;
                            $erp_lead->brand_id = $pr->brand;
                            $erp_lead->type = 'attach-images-for-product';
                            $erp_lead->min_price = ! empty($request->price_min) ? $request->price_min : 0;
                            $erp_lead->max_price = ! empty($request->price_max) ? $request->price_max : 0;
                            $erp_lead->save();
                        }
                    }
                }
                $inserted = count($data_to_insert);
                if ($inserted > 0) {
                    SuggestedProductList::insert($data_to_insert);
                }
            }

            //
            if ($request->need_to_send_message == 1) {
                ChatMessage::create([
                    'message' => "Total product found '".count($product_ids)."' for the keyword message : {$request->keyword_matched}",
                    'customer_id' => $model_id,
                    'status' => 2,
                    'approved' => 1,
                ]);

                return ['total_product' => count($product_ids)];
            }
            $msg = $inserted.' Products attached successfully';

            return response()->json(['code' => 200, 'message' => $msg]);
        }

        $mailEclipseTpl = mailEclipse::getTemplates()->where('template_dynamic', false);
        $rViewMail = [];
        if (! empty($mailEclipseTpl)) {
            foreach ($mailEclipseTpl as $mTpl) {
                $rViewMail[$mTpl->template_slug] = $mTpl->template_name.' ['.$mTpl->template_description.']';
            }
        }

        $customer = null;
        if ($customerId) {
            $customer = Customer::find($customerId);
        }
        $brands = Brand::where('magento_id', '>', 0)->pluck('name', 'id');
        $colors = (new Colors)->all();
        $categories = Category::all();
        if ($request->ajax()) {
            $html = view('partials.image-load', [
                'products' => $products,
                'all_product_ids' => $all_product_ids,
                'selected_products' => $request->selected_products ? json_decode($request->selected_products) : [],
                'model_type' => $model_type,
                'countBrands' => $countBrands,
                'countCategory' => $countCategory,
                'countSuppliers' => $countSuppliers,
                'customerId' => $customerId,
                'categoryArray' => $categoryArray,
                'rViewMail' => $rViewMail,
                'customer' => $customer,
                'brands' => $brands,
                'colors' => $colors,
                'categories' => $categories,
            ])->render();

            if (! empty($from) && $from == 'attach-image') {
                return $html;
            }

            return response()->json(['html' => $html, 'products_count' => $products_count]);
        }

        $message_body = $request->message ? $request->message : '';
        $sending_time = $request->sending_time ?? '';

        $locations = ProductLocation::pluck('name', 'name');
        $suppliers = Supplier::select(['id', 'supplier'])->whereIn('id', ProductSupplier::selectRaw('DISTINCT(`supplier_id`) as suppliers')->pluck('suppliers')->toArray())->get();

        $quick_sell_groups = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();
        Product::getPendingProductsCount($roletype);

        return view('partials.image-grid', compact(
            'products',
            'products_count',
            'roletype',
            'model_id',
            'selected_products',
            'model_type',
            'status',
            'assigned_user',
            'category_selection',
            'brand',
            'filtered_category',
            'message_body',
            'sending_time',
            'locations',
            'suppliers',
            'all_product_ids',
            'quick_sell_groups',
            'countBrands',
            'countCategory',
            'countSuppliers',
            'customerId',
            // 'categoryArray',
            'term',
            'rViewMail',
            'customer',
            'brands',
            'colors'
        ));
    }

    public function attachProductToModel($model_type, $model_id, $product_id)
    {
        switch ($model_type) {
            case 'order':
                $action = OrderController::attachProduct($model_id, $product_id);

                break;

            case 'sale':
                $action = SaleController::attachProduct($model_id, $product_id);
                break;
            case 'stock':
                $stock = Stock::find($model_id);
                $product = Product::find($product_id);

                $stock->products()->attach($product);
                $action = 'Attached';
                break;
        }

        return ['msg' => 'success', 'action' => $action];
    }

    public static function getSelectedProducts($model_type, $model_id)
    {
        $selected_products = [];

        switch ($model_type) {
            case 'order':
                $order = Order::find($model_id);
                if (! empty($order)) {
                    $selected_products = $order->order_product()->with('product')->get()->pluck('product.id')->toArray();
                }
                break;

            case 'sale':
                $sale = Sale::find($model_id);
                if (! empty($sale)) {
                    $selected_products = json_decode($sale->selected_product, true) ?? [];
                }
                break;

            default:
                $selected_products = [];
        }

        return $selected_products;
    }

    public function store(StoreProductRequest $request)
    {

        $product = new Product;

        $product->name = $request->name;
        $product->sku = $request->sku;
        $size = ! is_array($request->size) ? [$request->size] : $request->size;
        $product->size = implode(',', $size);
        $product->brand = $request->brand;
        $product->color = $request->color;
        $product->supplier = $request->supplier;
        $product->location = $request->location;
        $product->category = $request->category ?? 1;
        if ($request->price) {
            $product->price = $request->price;
        }
        if ($request->price_inr_special) {
            $product->price_inr_special = $request->price_inr_special;
        }
        $product->stock = 1;

        $brand = Brand::find($request->brand);

        if ($request->price) {
            if (isset($request->brand) && ! empty($brand->euro_to_inr)) {
                $product->price_inr = $brand->euro_to_inr * $product->price;
            } else {
                $product->price_inr = Setting::get('euro_to_inr') * $product->price;
            }

            $deduction_percentage = $brand && $brand->deduction_percentage ? $brand->deduction_percentage : 1;
            $product->price_inr = round($product->price_inr, -3);
            $product->price_inr_special = $product->price_inr - ($product->price_inr * $deduction_percentage) / 100;

            $product->price_inr_special = round($product->price_inr_special, -3);
        } elseif ($request->price_inr_special) {
            if (isset($request->brand) && ! empty($brand->euro_to_inr)) {
                $product->price = $request->price_inr_special / $brand->euro_to_inr;
            } else {
                $product->price = $request->price_inr_special / Setting::get('euro_to_inr');
            }
            $product->price_inr = $request->price_inr_special;
        }

        $product->save();

        if ($request->supplier == 'In-stock') {
            $product->suppliers()->attach(11); // In-stock ID
        }

        if ($request->hasFile('image')) {
            $product->detachMediaTags(config('constants.media_tags'));
            $media = MediaUploader::fromSource($request->get('is_image_url') ? $request->get('image') : $request->file('image'))
                ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')).'/'.$product->id)
                ->upload();
            $product->attachMedia($media, config('constants.media_tags'));
        }

        $product_image = $product->getMedia(config('constants.media_tags'))->first() ? getMediaUrl($product->getMedia(config('constants.media_tags'))->first()) : '';

        if ($request->order_id) {
            $order_product = new OrderProduct;

            $order_product->order_id = $request->order_id;
            $order_product->sku = $request->sku;
            $order_product->product_price = $product->price_inr_special;
            $order_product->size = $request->size;
            $order_product->color = $request->color;
            $order_product->qty = $request->quantity;
            $order_product->product_id = $product->id;
            $order_product->save();

            return response(['product' => $product, 'order' => $order_product, 'quantity' => $request->quantity, 'product_image' => $product_image]);
        } elseif ($request->stock_id) {
            $stock = Stock::find($request->stock_id);
            $stock->products()->attach($product);

            return response(['product' => $product, 'product_image' => $product_image]);
        }

        if ($request->ajax()) {
            return response()->json(['msg' => 'success']);
        }

        return redirect()->back()->with('success', 'You have successfully uploaded product!');
    }

    /**
     * @SWG\Get(
     *   path="/crop",
     *   tags={"Scraper"},
     *   summary="Return images array where the product status = auto crop",
     *   operationId="scraper-get-product-img",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=false,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="supplier_id",
     *          in="path",
     *          required=false,
     *          type="string"
     *      ),
     * )
     */
    public function giveImage(Request $request): JsonResponse
    {
        Log::info('crop_image_start_time: '.date('Y-m-d H:i:s'));
        $productId = request('product_id', null);
        $supplierId = request('supplier_id', null);
        if ($productId != null) {
            $product = Product::where('id', $productId)->first();
            if ($product) {
                //set initial pending status for isBeingCropped
                $scrap_status_data = [
                    'product_id' => $product->id,
                    'old_status' => $product->status_id,
                    'new_status' => StatusHelper::$isBeingCropped,
                    'pending_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                ProductStatusHistory::addStatusToProduct($scrap_status_data);

                //set initial pending status for pending products with category(attributeRejectCategory)
                $scrap_status_data = [
                    'product_id' => $product->id,
                    'old_status' => $product->status_id,
                    'new_status' => StatusHelper::$attributeRejectCategory,
                    'pending_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                ProductStatusHistory::addStatusToProduct($scrap_status_data);
            }
            Log::info('product_start_time_if_block: '.date('Y-m-d H:i:s'));
            $product = Product::where('id', $productId)->where('category', '>', 3)->first();
            Log::info('product_end_time_if_block: '.date('Y-m-d H:i:s'));
        } elseif ($supplierId != null) {
            Log::info('product_supplier_start_time: '.date('Y-m-d H:i:s'));
            $product = Product::join('product_suppliers as ps', 'ps.product_id', 'products.id')
                ->where('ps.supplier_id', $supplierId)
                ->where('products.status_id', StatusHelper::$autoCrop)
                ->where('products.category', '>', 3)
                ->where('products.stock', '>=', 1)
                ->orderByDesc('products.scrap_priority')
                ->select('products.*')
                ->first();
            Log::info('product_supplier_end_time: '.date('Y-m-d H:i:s'));
        } else {
            Log::info('product_image_start_time_else_block: '.date('Y-m-d H:i:s'));
            // Get next product
            $product = Product::select('id', 'status_id', 'category', 'lmeasurement', 'hmeasurement', 'dmeasurement')
                ->where('status_id', StatusHelper::$autoCrop)
                ->where('category', '>', 3)
                ->where('stock', '>=', 1)
                ->whereExists(function ($query) {
                    $query->selectRaw(1)
                        ->from('product_suppliers')
                        ->whereColumn('products.id', 'product_suppliers.product_id')
                        ->whereExists(function ($subquery) {
                            // Removed the suppliers column priority not null check, as the default value is NULL and if suppliers priority not set
                            // then system won't provide record to py team for further process this endpoint API.
                            // @todo: Commented old code for history and will require to remove after 10 day.
                            /*
                            $subquery->selectRaw(1)
                                    ->from('suppliers')
                                    ->whereColumn('product_suppliers.supplier_id', 'suppliers.id')
                                    ->whereNotNull('priority')
                                    ->whereNull('suppliers.deleted_at');
                            */
                            $subquery->selectRaw(1)
                                ->from('suppliers')
                                ->whereColumn('product_suppliers.supplier_id', 'suppliers.id')
                                ->whereNull('suppliers.deleted_at');
                        });
                })
                ->whereExists(function ($query) {
                    $query->selectRaw(1)
                        ->from('media')
                        ->join('mediables', 'media.id', '=', 'mediables.media_id')
                        ->whereColumn('products.id', 'mediables.mediable_id')
                        ->where('mediables.mediable_type', Product::class)
                        ->where('tag', 'original');
                })
                ->whereNull('deleted_at')
                ->orderByDesc('scrap_priority')
                ->orderByRaw('CASE 
                                WHEN brand IN (4, 13, 15, 18, 20, 21, 24, 25, 27, 30, 32, 144, 145) 
                                    AND category IN (11, 39, 5, 41, 14, 42, 60, 17, 31, 63) 
                                    AND supplier IN (
                                        "G & B Negozionline",
                                        "Tory Burch",
                                        "Wise Boutique",
                                        "Biffi Boutique (S.P.A.)",
                                        "MARIA STORE",
                                        "Lino Ricci Lei",
                                        "Al Duca d\'Aosta",
                                        "Tiziana Fausti",
                                        "Leam"
                                    ) THEN 0 ELSE 1 END')
                ->orderByDesc('is_on_sale')
                ->orderByDesc('listing_approved_at')
                ->first();

            if (! empty($product)) {
                $product->priority = $product->suppliers_info->first()->supplier->priority ?? 5;
            }
            Log::info('product_image_end_time_else_block: '.date('Y-m-d H:i:s'));

            unset($product->priority);
        }

        if (! $product) {
            // Return JSON
            return response()->json([
                'status' => 'no_product',
            ]);
        }

        $debug = request('debug', false);
        if (empty($debug)) {
            $product->status_id = StatusHelper::$isBeingCropped;
            $product->save();
        }

        Log::info('mediables_start_time: '.date('Y-m-d H:i:s'));

        $mediables = Mediables::select('media_id')->where('mediable_id', $product->id)->where('mediable_type', Product::class)->where('tag', 'original')->get();
        Log::info('mediables_end_time: '.date('Y-m-d H:i:s'));
        //deleting old images
        Log::info('old_image_start_time: '.date('Y-m-d H:i:s'));
        $oldImages = Mediables::select('media_id')->where('mediable_id', $product->id)->where('mediable_type', Product::class)->where('tag', '!=', 'original')->get();
        Log::info('old_image_end_time: '.date('Y-m-d H:i:s'));
        //old scraped products
        if ($oldImages) {
            foreach ($oldImages as $img) {
                $media = Media::where('id', $img->media_id)->first();
                if ($media) {
                    $image_path = $media->getAbsolutePath();
                    if (File::exists($image_path)) {
                        File::delete($image_path);
                    }
                    $media->delete();
                }
            }
        }

        foreach ($mediables as $mediable) {
            $mediableArray[] = $mediable->media_id;
        }

        if (! isset($mediableArray)) {
            return response()->json([
                'status' => 'no_product',
            ]);
        }

        Log::info('media_start_time: '.date('Y-m-d H:i:s'));
        $images = Media::select('id', 'filename', 'extension', 'mime_type', 'disk', 'directory')->whereIn('id', $mediableArray)->get();

        foreach ($images as $image) {
            // py team is facing issue and require to share the disk value as storage in place of local & uploads received.
            if (isset($image['disk']) && ! empty($image['disk']) && in_array($image['disk'], ['local', 'uploads'])) {
                $image['disk'] = 'storage';
            }
            $output['media_id'] = $image->id;
            $image->setAttribute('pivot', $output);
        }
        Log::info('media_end_time: '.date('Y-m-d H:i:s'));

        // Get category
        $category = $product->product_category;

        // Get other information related to category
        $cat = $category->title;
        $parent = '';
        $child = '';
        try {
            if ($cat != 'Select Category') {
                if ($category->isParent($category->id)) {
                    $parent = $cat;
                    $child = $cat;
                } else {
                    $parent = $category->parent()->first()->title;
                    $child = $cat;
                }
            }
        } catch (\ErrorException $e) {
            //
        }

        Log::info('website_array_start_time: '.date('Y-m-d H:i:s'));
        //Getting Website Color
        $websiteArrays = ProductHelper::getStoreWebsiteNameByTag($product->id);
        if (count($websiteArrays) == 0) {
            $colors = [];
        } else {
            foreach ($websiteArrays as $websiteArray) {
                $website = $websiteArray;
                if ($website) {
                    $isCropped = SiteCroppedImages::where('website_id', $websiteArray->id)
                        ->where('product_id', $product->id)->exists();
                    if (! $isCropped) {
                        [$r, $g, $b] = sscanf($website->cropper_color, '#%02x%02x%02x');
                        if (! empty($r) && ! empty($g) && ! empty($b)) {
                            $hexcode = '('.$r.','.$g.','.$b.')';
                            $colors[] = [
                                'code' => $hexcode,
                                'color' => $website->cropper_color_name,
                                'size' => $website->cropping_size,
                                'store' => $website->title,
                                'logo_color' => $website->logo_color,
                                'logo_border_color' => $website->logo_border_color,
                                'text_color' => $website->text_color,
                                'border_color' => ['color' => $website->border_color, 'thickness' => $website->border_thickness],
                            ];
                        }
                    }
                }
            }
        }
        Log::info('website_array_end_time: '.date('Y-m-d H:i:s'));
        if (! isset($colors)) {
            $colors = [];
        }
        if ($parent == null && $parent == '') {
            // Set new status
            $product->status_id = StatusHelper::$attributeRejectCategory;
            $product->save();

            Log::info('crop_image_end_time: '.date('Y-m-d H:i:s'));

            // Return JSON
            return response()->json([
                'status' => 'no_product',
            ]);
        } else {
            // Set new status
            $debug = request('debug', false);
            if (empty($debug)) {
                $product->status_id = StatusHelper::$isBeingCropped;
                $product->save();
            }

            $category_text = '';
            if ($child == 'Unknown Category') {
                $category_text = $parent;
            } else {
                $category_text = $parent.' '.$child;
            }
            $res = [
                'product_id' => $product->id,
                'image_urls' => $images,
                'l_measurement' => $product->lmeasurement,
                'h_measurement' => $product->hmeasurement,
                'd_measurement' => $product->dmeasurement,
                'category' => $category_text,
                'colors' => $colors,
            ];

            $http = CropImageGetRequest::create([
                'product_id' => $product->id,
                'request' => json_encode($request->all()),
                'response' => json_encode($res),
            ]);

            $res['token'] = $http->id;

            Log::info('crop_image_end_time: '.date('Y-m-d H:i:s'));

            // Return product
            return response()->json($res);
        }
    }

    /**
     * @SWG\Post(
     *   path="/link/image-crop",
     *   tags={"Crop"},
     *   summary="Save cropped image for product",
     *   operationId="crop-save-product-img",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="file",
     *          in="formData",
     *          required=true,
     *          type="file"
     *      ),
     *      @SWG\Parameter(
     *          name="color",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="media_id",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="filename",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="time",
     *          in="formData",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function saveImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'file' => 'required',
            'color' => 'required',
            'media_id' => 'required',
            'filename' => 'required',
            'time' => 'required',
            'instance_id' => 'required',
            'image_score' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $req = $request->all();

        $req['file'] = $request->file;

        $httpHistory = CropImageHttpRequestResponse::create([
            'crop_image_get_request_id' => $request->token,
            'request' => json_encode($req),
        ]);
        try {
            // Find the product or fail
            $product = Product::find($request->get('product_id'));

            if (! $product) {
                $res = [
                    'status' => 'error',
                    'message' => 'Unknown product with ID:'.$request->get('product_id'),
                ];

                $httpHistory->update(['response' => json_encode($res)]);

                return response()->json($res);
            }

            //sets initial status pending for finalApproval in product status histroy
            $data = [
                'product_id' => $product->id,
                'old_status' => $product->status_id,
                'new_status' => StatusHelper::$finalApproval,
                'pending_status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            ProductStatusHistory::addStatusToProduct($data);

            // Check if we have a file
            if ($request->hasFile('file')) {
                $image = $request->file('file');

                //Get the last image of the product.
                $allMediaIds = [];
                $pMedia = $product->getMedia(config('constants.media_original_tag'));
                if (! $pMedia->isEmpty()) {
                    foreach ($pMedia as $m) {
                        $allMediaIds[] = $m->id;
                    }
                }

                $productMediacount = count($allMediaIds);

                $media = MediaUploader::fromSource($image)
                    ->useFilename('CROPPED_'.time().'_'.rand(555, 455545))
                    ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')).'/'.$product->id)
                    ->upload();
                $colorName = null;
                if ($request->get('color')) {
                    $colorCode = str_replace(['(', ')'], '', $request->get('color'));
                    $rgbarr = explode(',', $colorCode, 3);
                    $hex = sprintf('#%02x%02x%02x', $rgbarr[0], $rgbarr[1], $rgbarr[2]);
                    $colorName = $hex;
                    $tag = 'gallery_'.$hex;

                    // check the store website count is existed with the total image
                    $storeWebCount = $product->getMedia($tag)->count();
                    if ($productMediacount <= $storeWebCount) {
                        $store_website_detail = StoreWebsite::where('cropper_color', 'LIKE', '%'.$request->get('color'))->first();
                        if ($store_website_detail !== null) {
                            $store_websites = StoreWebsite::where('tag_id', $store_website_detail->tag_id)->get();
                            foreach ($store_websites as $sw_data) {
                                if (isset($req['store']) && $req['store'] == $sw_data->title) {
                                    $exist = SiteCroppedImages::where('website_id', $sw_data->id)
                                        ->where('product_id', $product->id)->exists();
                                    if (! $exist) {
                                        SiteCroppedImages::create([
                                            'website_id' => $sw_data->id,
                                            'product_id' => $product->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $tag = config('constants.media_gallery_tag');
                }

                $product->attachMedia($media, $tag);
                $product->crop_count = $product->crop_count + 1;
                $product->save();

                $imageReference = new CroppedImageReference;
                $imageReference->original_media_id = $request->get('media_id');
                $imageReference->new_media_id = $media->id;
                $imageReference->original_media_name = $request->get('filename');
                $imageReference->new_media_name = $media->filename.'.'.$media->extension;
                $imageReference->speed = $request->get('time');
                $imageReference->product_id = $product->id;
                $imageReference->color = $colorName;
                /**
                 * Code to capturing image_score and store it within cropped_image_references table.
                 */
                $imageScore = (is_numeric($request->get('image_score'))) ? $request->get('image_score') : null;
                if (isset($imageScore) && ! empty($imageScore) && is_numeric($imageScore)) {
                    $imageReference->image_score = $imageScore;
                }
                $imageReference->instance_id = $request->get('instance_id');
                $imageReference->save();

                $httpHistory->update(['cropped_image_reference_id' => $imageReference->id]);

                //CHeck number of products in Crop Reference Grid
                $cropCount = CroppedImageReference::where('product_id', $product->id)
                    ->whereIn('original_media_id', $allMediaIds)
                    ->count();

                //check website count using Product
                $websiteArrays = ProductHelper::getStoreWebsiteName($product->id);

                if (count($websiteArrays) == 0) {
                    $multi = 1;
                } else {
                    $multi = count($websiteArrays);
                }

                $productMediacount = ($productMediacount * $multi);

                if ($productMediacount <= $cropCount) {
                    $product->cropped_at = Carbon::now()->toDateTimeString();
                    //check final approval
                    if ($product->checkPriceRange()) {
                        $product->status_id = StatusHelper::$finalApproval;
                    } else {
                        $product->status_id = StatusHelper::$priceCheck;
                    }

                    $product->scrap_priority = 0;
                    $product->save();
                } else {
                    $product->cropped_at = Carbon::now()->toDateTimeString();
                    $product->save();
                }

                // get the status as per crop
                if ($product->category > 0) {
                    $category = Category::find($product->category);
                    if (! empty($category) && $category->status_after_autocrop > 0) {
                        StatusHelper::updateStatus($product, $category->status_after_autocrop);
                    }
                }
            } else {
                $product->status_id = StatusHelper::$cropSkipped;
                $product->save();
            }

            $res = [
                'status' => 'success',
            ];

            $httpHistory->update(['response' => json_encode($res)]);

            return response()->json($res);
        } catch (Exception $e) {
            $res = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'line_no' => $e->getLine(),
                'file' => $e->getFile(),
            ];

            $httpHistory->update(['response' => json_encode($res)]);

            return response()->json($res);
        }
    }

    public function rejectedListingStatistics(): View
    {
        $products = Product::where('is_listing_rejected', 1)->groupBy(['listing_remark', 'supplier'])->selectRaw('COUNT(*) as total_count, supplier, listing_remark')->orderByDesc('total_count')->get();

        return view('products.rejected_stats', compact('products'));
    }

    public function addListingRemarkToProduct(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        $remark = $request->get('remark');

        $product = Product::find($productId);
        if ($product) {
            $product->listing_remark = $remark;
            $product->is_listing_rejected = $request->get('rejected');
            $product->listing_rejected_by = Auth::user()->id;
            $product->is_approved = 0;
            $product->listing_rejected_on = date('Y-m-d');
            $product->save();
        }

        if ($request->get('senior') && $product) {
            $s = new UserProductFeedback;
            $s->user_id = $product->approved_by;
            $s->senior_user_id = Auth::user()->id;
            $s->action = 'LISTING_APPROVAL_REJECTED';
            $s->content = ['action' => 'LISTING_APPROVAL_REJECTED', 'previous_action' => 'LISTING_APPROVAL', 'current_action' => 'LISTING_REJECTED', 'message' => 'Your listing has been rejected because of : '.$remark];
            $s->message = "Your listing approval has been discarded by the Admin because of this issue: $remark. Please make sure you check these details before approving any future product.";
            $s->product_id = $product->id;
            $s->save();
        }

        if ($request->get('rejected') && $product) {
            $l = new ListingHistory;
            $l->action = 'LISTING_REJECTED';
            $l->content = ['action' => 'LISTING_REJECTED', 'page' => 'LISTING'];
            $l->user_id = Auth::user()->id;
            $l->product_id = $product->id;
            $l->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function showAutoRejectedProducts(): View
    {
        $totalRemaining = Product::where('stock', '>=', 1)->where('is_listing_rejected_automatically', 1)->count();
        $totalDone = Product::where('stock', '>=', 1)->where('was_auto_rejected', 1)->count();

        return view('products.auto_rejected_stats', compact('totalDone', 'totalRemaining'));
    }

    public function affiliateProducts(Request $request): View
    {
        $colors = (new Colors)->all();
        $category_tree = [];
        $brands = Brand::all();
        $brand = null;
        $price = null;
        $color = [];
        $products = Product::where('is_without_image', 0);

        if ($request->get('sku')) {
            $products = $products->where(function ($query) use ($request) {
                $sku = $request->get('sku');
                $query->where('sku', $sku)
                    ->orWhere('name', 'LIKE', "%$sku%")
                    ->orWhere('short_description', 'LIKE', "%$sku%");
            });
        }

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    $category_tree[$parent->parent_id][$parent->id][$category->id];
                } else {
                    $category_tree[$parent->id][$category->id] = $category->id;
                }
            }

            $categories_array[$category->id] = $category->parent_id;
        }

        if ($request->get('brand') > 0) {
            $brand = $request->get('brand');
            $products = $products->where('brand', $brand);
        }

        $selected_categories = $request->get('category') ?: [1];
        if ($request->get('category')[0] != null && $request->get('category')[0] != 1) {
            $category_children = [];

            foreach ($request->get('category') as $category) {
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
            }

            $products = $products->whereIn('category', $category_children);
        }

        if ($request->color[0] != null) {
            $products = $products->whereIn('color', $request->color);
            $color = $request->color;
        }

        if ($request->get('price')[0] !== null) {
            $price = $request->get('price');
            $price = explode(',', $price);
            $products = $products->whereBetween('price_inr_special', [$price[0], $price[1]]);
        }

        $category_array = Category::renderAsArray();

        $products = $products->paginate(20);

        $c = $color;

        $media_tags = config('constants.media_tags');

        return view('products.affiliate', compact('products', 'request', 'brands', 'categories_array', 'category_array', 'selected_categories', 'brand', 'colors', 'c', 'price', 'media_tags'));
    }

    public function showRejectedListedProducts(Request $request): View
    {
        $products = new Product;
        $products = $products->where('stock', '>=', 1);
        $reason = '';
        $supplier = [];
        $selected_categories = [];

        if ($request->get('reason') !== '') {
            $reason = $request->get('reason');
            $products = $products->where('listing_remark', 'LIKE', "%$reason%");
        }

        if ($request->get('date') !== '') {
            $date = $request->get('date');
            $products = $products->where('listing_rejected_on', 'LIKE', "%$date%");
        }

        if ($request->get('id') !== '') {
            $id = $request->get('id');
            $products = $products->where('id', $id)->orWhere('sku', 'LIKE', "%$id%");
        }

        if ($request->get('user_id') > 0) {
            $products = $products->where('listing_rejected_by', $request->get('user_id'));
        }

        if ($request->get('type') === 'accepted') {
            $products = $products->where('is_listing_rejected', 0)->where('listing_remark', '!=', '');
        } else {
            $products = $products->where('is_listing_rejected', 1);
        }

        // $suppliers = DB::select('
        //         SELECT id, supplier
        //         FROM suppliers

        //         INNER JOIN (
        //             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
        //             ) as product_suppliers
        //         ON suppliers.id = product_suppliers.supplier_id
        // ');
        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
            })
            ->get();

        if ($request && $request->supplier) {
            if ($request->supplier[0] != null) {
                $supplier = $request->get('supplier');
                $products = $products->whereIn('id', ProductSupplier::whereIn('supplier_id', $supplier)->pluck('product_id'));
            }
        }

        if ($request && $request->category) {
            if ($request->category[0] != null && $request->category[0] != 1) {
                $category_children = [];
                foreach ($request->category as $category) {
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
                }
                $products = $products->whereIn('category', $category_children);
                $selected_categories = [$request->get('category')[0]];
            }
        }
        $users = User::all();

        $category_array = Category::renderAsArray();

        $products = $products->with('log_scraper_vs_ai')->where('stock', '>=', 1)->where('is_listing_rejected', 1)->orderByDesc('listing_rejected_on')->orderByDesc('updated_at')->paginate(25);

        $rejectedListingSummary = Product::where('stock', '>=', 1)->selectRaw('DISTINCT(listing_remark) as remark, COUNT(listing_remark) as issue_count')->where('is_listing_rejected', 1)->groupBy('listing_remark')->orderByDesc('issue_count')->get();

        return view('products.rejected_listings', compact('products', 'reason', 'category_array', 'selected_categories', 'suppliers', 'supplier', 'request', 'users', 'rejectedListingSummary'));
    }

    public function updateProductListingStats(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        if ($product) {
            $product->is_corrected = $request->get('is_corrected');
            $product->is_script_corrected = $request->get('is_script_corrected');
            $product->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function deleteOutOfStockProducts(): RedirectResponse
    {
        Product::where('stock', 0)->delete();

        return redirect()->back()->with('success', 'Productsssss deleted successfully');
    }

    public function deleteProduct(Request $request): JsonResponse
    {
        if ($request->has('product_id')) {
            $product = Product::find($request->get('product_id'));

            if ($product) {
                $product->forceDelete();
            }
        } else {
            $ids = $request->ids;
            $delete_products = Product::whereIn('id', explode(',', $ids))->get();
            foreach ($delete_products as $delete_product) {
                $delete_product->deleted_at = date('Y-m-d H:i:s');
                $delete_product->save();
            }
        }

        return response()->json([
            'status' => 'Products Deleted successfully',
            'success' => 'Products Deleted successfully',
        ]);
    }

    public function relistProduct(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));

        if ($product) {
            $product->is_listing_rejected = $request->get('rejected');
            $product->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function productMultiDescription(Request $request): View
    {
        $products = ScrapedProducts::selectRaw('scraped_products.sku, COUNT(*) as count, scraped_products.product_id')
            ->where('scraped_products.sku', '!=', '')
            ->groupBy('scraped_products.sku')
            ->orderByDesc('count')
            ->paginate(25);

        return view('products.multidescription', compact('products'));
    }

    public function productMultiDescriptionCheck(Request $request): JsonResponse
    {
        $sku = $request->input('sku');
        $productCount = Product::where('sku', $sku)->count();

        return response()->json(['result' => $productCount]);
    }

    public function productMultiDescriptionSku(Request $request): View
    {
        $sku = $request->id;
        $products = ScrapedProducts::selectRaw('scraped_products.id as sid, scraped_products.sort_order as sort_order, scraped_products.description, scraped_products.brand_id, scraped_products.website as website, products.name as pname, brands.name as bname')
            ->join('products', 'scraped_products.product_id', '=', 'products.id')
            ->join('brands', 'scraped_products.brand_id', '=', 'brands.id')
            ->where('scraped_products.sku', $sku)
            ->get();

        return view('products.skumultidescription', compact('products', 'sku'));
    }

    public function productMultiDescriptionUpdate(Request $request): JsonResponse
    {
        $updates = $request->productData;
        $sku = $request->sku;
        $condition = $request->condition;
        foreach ($updates as $update) {
            $productId = $update['id'];
            $sortOrder = $update['value'];
            ScrapedProducts::where('id', $productId)->where('sku', $sku)->update(['sort_order' => $sortOrder]);
            if ($condition == 1 && $sortOrder == 1) {
                $getdescription = ScrapedProducts::where('id', $productId)->where('sku', $sku)->first();
                Product::where('sku', $sku)->update(['short_description' => $getdescription->description]);
            }
        }

        return response()->json(['message' => 'Sort orders updated successfully']);
    }

    public function productSizeLog(Request $request): View
    {
        $query = ProductSupplier::with('supplier', 'product')
            ->where(function ($query) {
                $query->whereNotNull('size')->orWhere('size', '!=', '');
            });

        if ($request->has('product_id') && $request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->supplier) {
            $query->whereIn('product_suppliers.supplier_id', $request->supplier); // Specify the table for the column 'supplier_id'
        }

        if ($request->has('sku') && $request->filled('sku')) {
            $query->whereHas('product', function ($query) use ($request) {
                $query->where('sku', $request->input('sku'));
            });
        }

        // // Add the groupBy clause here
        $supplier = Supplier::select('id', 'supplier')->get();
        $products_count = $query->count();
        $products = $query->paginate(50);

        return view('products.size', compact('products', 'products_count', 'request', 'supplier'));
    }

    public function productDescriptionHistory(Request $request): View
    {
        $id = $request->id;

        $query = LogScraper::where('sku', $id)
            ->leftJoin('brands as b', 'b.id', 'log_scraper.brand')
            ->leftJoin('categories as c', 'c.id', 'log_scraper.category')
            ->select([
                'log_scraper.*',
                'b.name as brand_name',
                'c.title as category_name']);
        $products = $query->orderByDesc('updated_at')->get();

        return view('products.partials.history', compact('products'));
    }

    public function productDescription(Request $request): View
    {
        $query = ProductSupplier::with('supplier', 'product')
            ->select(['product_suppliers.*', 'scrapers.id as scraper_id', 'scrapers.last_started_at as last_started_at'])
            ->join('scrapers', 'scrapers.supplier_id', 'product_suppliers.supplier_id');

        $supplier = Supplier::select('id', 'supplier')->get();

        if ($request->supplier) {
            $query->whereIn('product_suppliers.supplier_id', $request->supplier); // Specify the table for the column 'supplier_id'
        }
        if ($request->colors) {
            $query->whereIn('product_suppliers.color', $request->colors); // Specify the table for the column 'supplier_id'
        }
        if ($request->sizeSystem) {
            $query->whereIn('product_suppliers.size_system', $request->sizeSystem); // Specify the table for the column 'supplier_id'
        }

        $products_count = $query->count();
        $products = $query->orderByDesc('product_id')->paginate(50);

        return view('products.description', compact('products', 'products_count', 'request', 'supplier'));
    }

    public function productDescriptionUpdate(Request $request): JsonResponse
    {
        $ids = $request->ids;
        $from = $request->from;
        $to = $request->to;
        DescriptionChange::create([
            'keyword' => $from,
            'replace_with' => $to,
        ]);
        foreach ($ids as $id) {
            $prod = ProductSupplier::where('product_id', $id)->first();
            $description = str_replace($from, $to, $prod->description);
            $prod->description = $description;
            $prod->save();
        }

        return response()->json([
            'code' => 200,
            'message' => 'Your request has been update successfully',
        ]);
    }

    public function productScrapLog(Request $request)
    {
        $products = Product::orderByDesc('updated_at');

        if ($request->get('product_id') != '') {
            $products = $products->where('id', $request->get('product_id'));
        }
        if ($request->get('sku') != '') {
            $products = $products->where('sku', $request->get('sku'));
        }

        $products_count = $products->count();

        $products = $products->paginate(50);

        $products->getCollection()->transform(function ($getproduct) {
            $getproduct->total_count = $getproduct->productstatushistory->count();
            $history_log = [];
            $productstatushistory = $getproduct->productstatushistory->toArray();
            foreach ($productstatushistory as $history) {
                $history['created_at'] = Carbon::parse($history['created_at'])->format('H:i');
                $history_log[$history['new_status']] = $history;
            }
            $getproduct->alllog_status = $history_log;

            return $getproduct;
        });

        $status = StatusHelper::getStatus();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'products-status-history')->first();

        $dynamicColumnsToShowp = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowp = json_decode($hideColumns, true);
        }

        return view('products.statuslog', compact('products', 'request', 'status', 'products_count', 'request', 'dynamicColumnsToShowp'));
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'products-status-history')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'products-status-history';
            $column->column_name = json_encode($request->column_p);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'products-status-history';
            $column->column_name = json_encode($request->column_p);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function productStats(Request $request): View
    {
        $products = Product::orderByDesc('updated_at');

        if ($request->get('status') != '') {
            $status = $request->get('status') == 'approved' ? 1 : 0;
            $products = $products->where('is_approved', $status);
        }
        if ($request->has('user_id') >= 1) {
            $products = $products->where(function ($query) use ($request) {
                $query->where('approved_by', $request->get('user_id'))
                    ->orWhere('crop_approved_by', $request->get('user_id'))
                    ->orWhere('listing_rejected_by', $request->get('user_id'))
                    ->orWhere('crop_rejected_by', $request->get('user_id'))
                    ->orWhere('crop_ordered_by', $request->get('user_id'));
            });
        }
        $sku = '';

        if ($request->get('sku') != '') {
            $sku = $request->get('sku');
            $products = $products->where('sku', 'LIKE', "%$sku%");
        }

        if ($request->get('range_start') != '') {
            $products = $products->where(function ($query) use ($request) {
                $query->where('crop_approved_at', '>=', $request->get('range_start'))
                    ->orWhere('listing_approved_at', '>=', $request->get('range_start'))
                    ->orWhere('listing_rejected_on', '>=', $request->get('range_start'))
                    ->orWhere('crop_ordered_at', '>=', $request->get('range_start'))
                    ->orWhere('crop_rejected_at', '>=', $request->get('range_start'));
            });
        }
        if ($request->get('range_end') != '') {
            $products = $products->where(function ($query) use ($request) {
                $query->where('crop_approved_at', '<=', $request->get('range_end'))
                    ->orWhere('listing_approved_at', '<=', $request->get('range_end'))
                    ->orWhere('listing_rejected_on', '<=', $request->get('range_end'))
                    ->orWhere('crop_ordered_at', '<=', $request->get('range_end'))
                    ->orWhere('crop_rejected_at', '<=', $request->get('range_end'));
            });
        }

        $products = $products->paginate(50);
        $users = User::all();

        return view('products.stats', compact('products', 'sku', 'users', 'request'));
    }

    public function showSOP(Request $request): View
    {
        $sopType = $request->get('type');
        $sop = Sop::where('name', $sopType)->first();

        if (! $sop) {
            $sop = new Sop;
            $sop->name = $request->name;
            $sop->content = $request->content;

            $sop->save();
        }

        return view('products.sop', compact('sop'));
    }

    public function getSupplierScrappingInfo(Request $request): View
    {
        return View('scrap.supplier-info');
    }

    public function deleteImage(): JsonResponse
    {
        $productId = request('product_id', 0);
        $mediaId = request('media_id', 0);

        $cond = Db::table('mediables')->where([
            'media_id' => $mediaId,
            'mediable_id' => $productId,
            //"tag" => $mediaType,
            'mediable_type' => Product::class,
        ])->delete();

        if ($cond) {
            return response()->json(['code' => 1, 'data' => []]);
        }

        return response()->json(['code' => 0, 'data' => [], 'message' => 'No media found']);
    }

    public function sendMessageSelectedCustomer(Request $request)
    {
        $params = request()->all();
        $params['user_id'] = Auth::id();
        $params['status'] = ChatMessage::CHAT_AUTO_BROADCAST;

        $token = request('customer_token', '');

        if (! empty($token)) {
            $customerIds = json_decode(session($token));
            if (empty($customerIds)) {
                $customerIds = [];
            }
        }
        // if customer is not available then choose what it is before
        if (empty($customerIds)) {
            $customerIds = $request->get('customers_id', '');
            $customerIds = explode(',', $customerIds);
        }

        $params['customer_ids'] = $customerIds;

        $groupId = ChatMessage::max('group_id');
        $params['group_id'] = ($groupId > 0) ? $groupId + 1 : 1;
        $params['is_queue'] = request('is_queue', 0);

        SendMessageToCustomer::dispatch($params)->onQueue('customer_message');

        if ($request->ajax()) {
            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'error']);
        }
    }

    public function assignGroupSelectedCustomer(Request $request): JsonResponse
    {
        $customerIDs = explode(',', $request->get('customers_id'));
        if (! empty($customerIDs)) {
            foreach ($customerIDs as $cid) {
                $customerExist = MessagingGroupCustomer::where(['message_group_id' => $request->sms_group_id, 'customer_id' => $cid])->first();
                if ($customerExist == null) {
                    MessagingGroupCustomer::create(['message_group_id' => $request->sms_group_id, 'customer_id' => $cid]);
                }
            }
        }

        return response()->json(['msg' => 'success']);
    }

    public function createGroupSelectedCustomer(Request $request)
    {
        $params = request()->all();
        $params['user_id'] = Auth::id();

        $token = request('customer_token', '');

        if (! empty($token)) {
            $customerIds = json_decode(session($token));
            if (empty($customerIds)) {
                $customerIds = [];
            }
        }
        // if customer is not available then choose what it is before
        if (empty($customerIds)) {
            $customerIds = $request->get('customers_id', '');
            $customerIds = explode(',', $customerIds);
        }

        $params['customer_ids'] = $customerIds;

        MessagingGroup::create([
            'name' => $request->name,
            'store_website_id' => $request->store_website_id,
            'service_id' => $request->service_id,
        ]);

        $customers = Customer::whereIn('id', $params['customer_ids'])->update(['store_website_id' => $params['store_website_id']]);

        if ($customers) {
            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'error']);
        }
    }

    /**
     * This function is use for create suggested product log
     *
     * @param type [array] inputArray
     * @param  Request  $request  Request
     * @param  mixed  $log
     * @param  mixed  $type
     * @param  mixed  $parentId
     * @return void;
     */
    public function createSuggestedProductLog($log = '', $type = '', $parentId = '')
    {
        try {
            ProductSuggestedLog::create([
                'parent_id' => $parentId,
                'log' => $log,
                'type' => $type,
            ]);
        } catch (Exception $e) {
            ProductSuggestedLog::create(['parent_id' => $parentId, 'log' => $e->getMessage(), 'type' => 'not catch']);
        }
    }

    public function queueCustomerAttachImages(Request $request)
    {
        $prodSugId = isset($request->hidden_suggestedproductid) ? $request->hidden_suggestedproductid : '';
        try {
            // This condition is use for send now no whatsapp
            if ($request->is_queue == 2) {
                return $this->sendNowCustomerAttachImages($request);
            } else {
                $data['_token'] = $request->_token;
                $data['send_pdf'] = $request->send_pdf;
                $data['pdf_file_name'] = ! empty($request->pdf_file_name) ? $request->pdf_file_name : '';
                $data['images'] = $request->images;
                $data['image'] = $request->image;
                $data['screenshot_path'] = $request->screenshot_path;
                $data['message'] = $request->message;
                $data['customer_id'] = $request->customer_id;
                $data['status'] = $request->status;
                $data['type'] = $request->type;
                AttachImagesSend::dispatch($data)->onQueue('customer_message');

                $json = request()->get('json', false);

                if ($json) {
                    $this->createSuggestedProductLog('Message Send later Queue', 'Send later Queue', $prodSugId);

                    return response()->json(['code' => 200, 'message' => 'Message Send later Queue']);
                }
                if ($request->get('return_url')) {
                    return redirect()->to($request->get('return_url'));
                }

                return redirect()->route('get.customer.post.show', $prodSugId)->withSuccess('Message Send For Queue');
            }
        } catch (Exception $e) {
            ProductSuggestedLog::create(['parent_id' => $prodSugId, 'log' => $e->getMessage(), 'type' => 'not catch']);
        }
    }

    /**
     * This function is use for send now image on whatsapp
     *
     * @return type JsonResponse
     */
    public function sendNowCustomerAttachImages(Request $request): type
    {
        $prodSugId = isset($request->hidden_suggestedproductid) ? $request->hidden_suggestedproductid : '';
        try {
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add([
                '_token' => $request->_token,
                'send_pdf' => $request->send_pdf,
                'pdf_file_name' => $request->pdf_file_name,
                'images' => $request->images,
                'image' => $request->image,
                'screenshot_path' => $request->screenshot_path,
                'message' => $request->message,
                'customer_id' => $request->customer_id,
                'status' => $request->status,
                'type' => $request->type,
            ]);
            app(WhatsAppController::class)->sendMessage($requestData, 'customer');

            $json = request()->get('json', false);
            if ($json) {
                $this->createSuggestedProductLog('Message Send Now on whatsapp', 'Send Now', $prodSugId);

                return response()->json(['code' => 200, 'message' => 'Message Send Now on whatsapp']);
            }
            if ($request->get('return_url')) {
                return redirect()->to($request->get('return_url'));
            }

            return redirect()->route('get.customer.post.show', $prodSugId)->withSuccess('Message Send Now on whatsapp');
        } catch (Exception $e) {
            ProductSuggestedLog::create(['parent_id' => $prodSugId, 'log' => $e->getMessage(), 'type' => 'not catch']);
        }
    }

    public function cropImage(Request $request): JsonResponse
    {
        $img = $request->img;
        $style = $request->style;
        $style = explode(' ', $style);
        $name = str_replace(['scale(', ')'], '', $style[4]);
        $newHeight = (($name * 3.333333) * 1000);
        [$width, $height] = getimagesize($img);
        $thumb = imagecreatetruecolor($newHeight, $newHeight);
        try {
            $source = imagecreatefromjpeg($img);
        } catch (Exception $e) {
            $source = imagecreatefrompng($img);
        }

        // Resize
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newHeight, $newHeight, $width, $height);

        $thumbWidth = imagesx($thumb);
        $thumbHeight = imagesy($thumb);

        $canvasImage = imagecreatetruecolor(1000, 1000); // Creates a black image

        // Fill it with white (optional)
        $gray = imagecolorallocate($canvasImage, 227, 227, 227);
        imagefill($canvasImage, 0, 0, $gray);

        imagecopy($canvasImage, $thumb, (1000 - $thumbWidth) / 2, (1000 - $thumbHeight) / 2, 0, 0, $thumbWidth, $thumbHeight);
        $url = config('env.APP_URL');
        $path = str_replace($url, '', $img);

        imagejpeg($canvasImage, public_path().'/'.$path);

        return response()->json(['success' => 'success', 200]);
    }

    public function hsCodeIndex(Request $request): View
    {
        if ($request->category || $request->keyword) {
            $products = Product::select('composition', 'category')->where('composition', 'LIKE', '%'.request('keyword').'%')->where('category', $request->category[0])->groupBy('composition')->get();

            foreach ($products as $product) {
                if ($product->category != null) {
                    $categoryTree = CategoryController::getCategoryTree($product->category);
                    if (is_array($categoryTree)) {
                        $childCategory = implode(' > ', $categoryTree);
                    }

                    $cat = Category::findOrFail($request->category[0]);
                    $parentCategory = $cat->title;

                    if ($product->composition != null) {
                        if ($request->group == 'on') {
                            $composition = strip_tags($product->composition);
                            $compositions[] = str_replace(['&nbsp;', '/span>'], ' ', $composition);
                        } else {
                            if ($product->isGroupExist($product->category, $product->composition, $parentCategory, $childCategory)) {
                                $composition = strip_tags($product->composition);
                                $compositions[] = str_replace(['&nbsp;', '/span>'], ' ', $composition);
                            }
                        }
                    }
                }
            }
            if (! isset($compositions)) {
                $compositions = [];
                $childCategory = '';
                $parentCategory = '';
            }
            $keyword = $request->keyword;
            $groupSelected = $request->group;
        } else {
            $keyword = '';
            $compositions = [];
            $childCategory = '';
            $parentCategory = '';
            $groupSelected = '';
        }
        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2', 'id' => 'category_value'])
            ->selected($selected_categories)
            ->renderAsDropdown();
        $hscodes = SimplyDutyCategory::all();
        $categories = Category::all();
        $groups = HsCodeGroup::all();
        $cate = HsCodeGroupsCategoriesComposition::groupBy('category_id')->pluck('category_id')->toArray();
        $pendingCategory = Category::all()->except($cate);
        $pendingCategoryCount = $pendingCategory->count();
        $setting = HsCodeSetting::first();
        $countries = SimplyDutyCountry::all();

        return view('products.hscode', compact('keyword', 'compositions', 'childCategory', 'parentCategory', 'category_selection', 'hscodes', 'categories', 'groups', 'groupSelected', 'pendingCategoryCount', 'setting', 'countries'));
    }

    public function saveGroupHsCode(Request $request): JsonResponse
    {
        $name = $request->name;
        $compositions = $request->compositions;
        $key = HsCodeSetting::first();
        if ($key == null) {
            return response()->json(['Please Update the Hscode Setting']);
        }
        $api = $key->key;
        $fromCountry = $key->from_country;
        $destinationCountry = $key->destination_country;
        if ($api == null || $fromCountry == null || $destinationCountry == null) {
            return response()->json(['Please Update the Hscode Setting']);
        }
        $category = Category::select('id', 'title')->where('id', $request->category)->first();
        $categoryId = $category->id;

        if ($request->composition) {
            $hscodeSearchString = str_replace(['&gt;', '>'], '', $name.' '.$category->title.' '.$request->composition);
        } else {
            $hscodeSearchString = str_replace(['&gt;', '>'], '', $name);
        }

        $hscode = HsCode::where('description', $hscodeSearchString)->first();

        if ($hscode != null) {
            return response()->json(['error' => 'HsCode Already exist']);
        }

        $hscodeSearchString = urlencode($hscodeSearchString);

        $searchString = 'https://www.api.simplyduty.com/api/classification/get-hscode?APIKey='.$api.'&fullDescription='.$hscodeSearchString.'&originCountry='.$fromCountry.'&destinationCountry='.$destinationCountry.'&getduty=false';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $searchString);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $searchString, 'POST', json_encode([]), json_decode($output), $httpcode, ProductController::class, 'saveGroupHsCode');

        // close curl resource to free up system resources
        curl_close($ch);

        $categories = json_decode($output);

        if (! isset($categories->HSCode)) {
            return response()->json(['error' => 'Something is wrong with the API. Please check the balance.']);
        } else {
            if ($categories->HSCode != null) {
                $hscode = new HsCode;
                $hscode->code = $categories->HSCode;
                $hscode->description = urldecode($hscodeSearchString);
                $hscode->save();

                if ($request->existing_group != null) {
                    $group = HsCodeGroup::find($request->existing_group);
                } else {
                    $group = new HsCodeGroup;
                    $group->hs_code_id = $hscode->id;
                    $group->name = $name.' > '.$category->title;
                    $group->composition = $request->composition;
                    $group->save();
                }

                $id = $group->id;
                if ($request->compositions) {
                    foreach ($compositions as $composition) {
                        $comp = new HsCodeGroupsCategoriesComposition;
                        $comp->hs_code_group_id = $id;
                        $comp->category_id = $categoryId;
                        $comp->composition = $composition;
                        $comp->save();
                    }
                }
            }
        }

        return response()->json(['Hscode Generated successfully'], 200);
    }

    public function editGroup(Request $request): JsonResponse
    {
        $group = HsCodeGroup::find($request->id);
        $group->hs_code_id = $request->hscode;
        $group->name = $request->name;
        $group->composition = $request->composition;
        $group->save();

        return response()->json(['success' => 'success'], 200);
    }

    public function productTranslation(Request $request)
    {
        $term = $request->term;
        $language = $request->language;
        $is_rejected = $request->input('is_rejected', '0');
        $query = new Product_translation;
        if (! empty($term)) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->term.'%');
            });
        }
        if (! empty($language)) {
            $query = $query->where(function ($q) use ($request) {
                $q->Where('locale', 'LIKE', '%'.$request->language.'%');
            });
        }

        if ($request->has('is_rejected')) {
            $query = $query->where(function ($q) use ($is_rejected) {
                $q->Where('is_rejected', $is_rejected);
            });
        }

        $product_translations = $query->orderByDesc('product_id')->paginate(10)->appends(request()->except(['page'])); //catch 2

        $product_translation_history = ProductTranslationHistory::get();

        $languages = TranslationLanguage::get();

        $all_languages = Language::get();
        $media_gallery_tag = config('constants.media_gallery_tag');

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('products.translations.product-search', compact('product_translations', 'term', 'language', 'is_rejected'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $product_translations->render(),
            ], 200);
        }

        return view('products.translations.product-list', compact('product_translations', 'term', 'language', 'languages', 'all_languages', 'product_translation_history', 'media_gallery_tag'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function translationLanguage(ProductTranslationRequest $request): JsonResponse
    {
        $this->validate($request, [
            'locale' => 'sometimes|nullable|string|max:255',
            'code' => 'required',
        ]);

        $data = $request->except('_token');
        Language::create($data);

        return response()->json([
            'message' => 'Successfully updated the data',
        ]);
    }

    public function productTranslationRejection(Request $request): JsonResponse
    {
        $product_translation = Product_translation::find($request->product_translation_id);
        $product_translation->is_rejected = $request->value;
        $product_translation->save();
        $product_translation_history = new ProductTranslationHistory;
        $product_translation_history->is_rejected = $request->value;
        $product_translation_history->user_id = Auth::user()->id;
        $product_translation_history->product_translation_id = $request->product_translation_id;
        $product_translation_history->save();

        return response()->json([
            'message' => 'Rejected Successfully',
            'value' => $request->value,
        ]);
    }

    public function viewProductTranslation($id): View
    {
        $locales = Product_translation::groupBy('locale')->pluck('locale');
        $languages = Language::get();
        $sites = StoreWebsite::get();
        $product_translation = Product_translation::find($id);

        return view('products.translations.view-or-edit', [
            'product_translation' => $product_translation,
            'locales' => $locales,
            'sites' => $sites,
            'languages' => $languages,
        ]);
    }

    public function getProductTranslationDetails($id, $locale): JsonResponse
    {
        $product_translation = Product_translation::where('product_id', $id)->where('locale', $locale)->first();

        return response()->json([
            'product_translation' => $product_translation,
        ]);
    }

    public function editProductTranslation($id, Request $request): JsonResponse
    {
        Product_translation::where('id', $id)->update(['locale' => $request->language, 'title' => $request->title, 'description' => $request->description, 'site_id' => $request->site_id]);
        ProductTranslationHistory::insert([
            'user_id' => Auth::user()->id,
            'product_translation_id' => $id,
            'locale' => $request->language,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Successfully updated the data',
        ]);
    }

    public function published(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $website = $request->get('website', []);

        WebsiteProduct::where('product_id', $id)->delete();

        if (! empty($website)) {
            foreach ($website as $web) {
                $website = new WebsiteProduct;
                $website->product_id = $id;
                $website->store_website_id = $web;
                $website->save();
            }
        }

        return response()->json(['code' => 200]);
    }

    public function originalColor($id): JsonResponse
    {
        $product = Product::find($id);
        $referencesColor = '';
        if (isset($product->scraped_products)) {
            // starting to see that howmany color we going to update
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['colors']) != null) {
                $color = $product->scraped_products->properties['colors'];
                if (is_array($color)) {
                    $referencesColor = implode(' > ', $color);
                } else {
                    $referencesColor = $color;
                }
            }

            // starting to see that howmany color we going to update
            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
                $color = $product->scraped_products->properties['color'];
                if (is_array($color)) {
                    $referencesColor = implode(' > ', $color);
                } else {
                    $referencesColor = $color;
                }
            }

            $scrapedProductSkuArray = [];

            if (! empty($referencesColor)) {
                $productSupplier = $product->supplier;
                $supplier = Supplier::where('supplier', $productSupplier)->first();
                if ($supplier && $supplier->scraper) {
                    $scrapedProducts = ScrapedProducts::where('website', $supplier->scraper->scraper_name)->get();
                    foreach ($scrapedProducts as $scrapedProduct) {
                        if (isset($scrapedProduct->properties['color'])) {
                            $products = $scrapedProduct->properties['color'];
                            if (! empty($products)) {
                                $scrapedProductSkuArray[] = $scrapedProduct->sku;
                            }
                        }

                        if (isset($scrapedProduct->properties['colors'])) {
                            $products = $scrapedProduct->properties['colors'];
                            if (! empty($products)) {
                                $scrapedProductSkuArray[] = $scrapedProduct->sku;
                            }
                        }
                    }
                }
            }

            if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['colors']) != null) {
                return response()->json(['success', $referencesColor, count($scrapedProductSkuArray)]);
            } else {
                return response()->json(['message', 'Color Is Not Present']);
            }
        } else {
            return response()->json(['message', 'Color Is Not Present']);
        }
    }

    public function changeAllColorForAllSupplierProducts(Request $request, $id): JsonResponse
    {
        UpdateScrapedColor::dispatch([
            'product_id' => $id,
            'color' => $request->color,
            'user_id' => Auth::user()->id,
        ])->onQueue('supplier_products');

        return response()->json(['success', 'Product color has been sent for the update']);
    }

    public function storeWebsiteDescription(Request $request): JsonResponse
    {
        $websites = $request->store_wesites;
        if (is_array($websites) && $request->product_id != null && $request->description != null) {
            foreach ($websites as $website) {
                $storeWebsitePA = StoreWebsiteProductAttribute::where('product_id', $request->product_id)->where('store_website_id', $website)->first();
                if (! $storeWebsitePA) {
                    $storeWebsitePA = new StoreWebsiteProductAttribute;
                    $storeWebsitePA->product_id = $request->product_id;
                    $storeWebsitePA->store_website_id = $website;
                }
                $storeWebsitePA->store_website_id = $website;
                $storeWebsitePA->description = $request->description;
                $storeWebsitePA->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Store website description stored successfully']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Required field is missing']);
    }

    public function changeAutoPushValue(Request $request): JsonResponse
    {
        if (Setting::get('auto_push_product') == 0) {
            $val = 1;
        } else {
            $val = 0;
        }
        $settings = Setting::set('auto_push_product', $val, 'int');

        return response()->json(['code' => 200, 'data' => $settings, 'message' => 'Status changed']);
    }

    public function pushProduct(Request $request): JsonResponse
    {
        $limit = $request->get('no_of_product', config('constants.no_of_product'));
        // Mode($mode) defines the whether it's a condition check or product push.
        $mode = $request->get('mode', config('constants.mode'));
        $products = ProductHelper::getProducts(StatusHelper::$finalApproval, $limit);
        Log::info('Product push star time: '.date('Y-m-d H:i:s'));
        $no_of_product = count($products);
        foreach ($products as $key => $product) {
            $details = [];
            $details['product_index'] = ($key) + 1;
            $details['no_of_product'] = $no_of_product;

            PushProductOnlyJob::dispatch($product, $details)->onQueue('pushproductonly');
        }
        Log::info('Product push end time: '.date('Y-m-d H:i:s'));

        if ($mode == 'conditions-check') {
            return response()->json(['code' => 200, 'message' => 'Conditions checked completed successfully!']);
        } elseif ($mode == 'product-push') {
            return response()->json(['code' => 200, 'message' => 'Push product successfully!']);
        }
    }

    public function processProductsConditionsCheck(Request $request): JsonResponse
    {
        $limit = $request->get('no_of_product', config('constants.no_of_product'));
        // Mode($mode) defines the whether it's a condition check or product push.
        $mode = $request->get('mode', config('constants.mode'));
        // Gets all products with final approval status
        $products = ProductHelper::getProducts(StatusHelper::$finalApproval, $limit);

        $no_of_product = count($products);
        foreach ($products as $key => $product) {
            $details = [];
            $details['product_index'] = ($key) + 1;
            $details['no_of_product'] = $no_of_product;
            Flow2ConditionCheckProductOnly::dispatch($product, $details)->onQueue('conditioncheckonly');
        }

        if ($mode == 'conditions-check') {
            return response()->json(['code' => 200, 'message' => 'Conditions checked completed successfully!']);
        } elseif ($mode == 'product-push') {
            return response()->json(['code' => 200, 'message' => 'Push product successfully!']);
        }
    }

    public function pushProductsToMagento(Request $request): JsonResponse
    {
        $limit = $request->get('no_of_product', config('constants.no_of_product'));
        $products = ProductHelper::getProducts(StatusHelper::$productConditionsChecked, $limit);
        if ($products->count() == 0) {
            return response()->json(['code' => 500, 'message' => 'No products found!']);
        }

        $no_of_product = count($products);
        foreach ($products as $key => $product) {
            $details = [];
            $details['product_index'] = ($key) + 1;
            $details['no_of_product'] = $no_of_product;

            Flow2PushProductOnlyJob::dispatch($product, $details)->onQueue('pushproductflow2only');
        }

        return response()->json(['code' => 200, 'message' => 'Product pushed to magento successfully!']);
    }

    public function pushToMagentoConditions(Request $request): View
    {
        $drConditions = PushToMagentoCondition::all();
        if (($request->condition && $request->condition != null) && ($request->magento_description && $request->magento_description != null)) {
            $conditions = PushToMagentoCondition::where('condition', $request->condition)->where('description', 'LIKE', '%'.$request->magento_description.'%')->get();
        } elseif ($request->magento_description && $request->magento_description != null) {
            $conditions = PushToMagentoCondition::where('description', 'LIKE', '%'.$request->magento_description.'%')->get();
        } elseif ($request->condition && $request->condition != null) {
            $conditions = PushToMagentoCondition::where('condition', $request->condition)->get();
        } else {
            $conditions = PushToMagentoCondition::all();
        }

        return view('products.conditions', compact('conditions', 'drConditions'));
    }

    public function updateConditionStatus(Request $request)
    {
        $input = $request->input();
        PushToMagentoCondition::where('id', $input['id'])->update(['status' => $input['status']]);

        return 'Status Updated';
    }

    public function updateConditionUpteamStatus(Request $request)
    {
        $input = $request->input();
        PushToMagentoCondition::where('id', $input['id'])->update(['upteam_status' => $input['upteam_status']]);

        return 'Upteam Status Updated';
    }

    public function getPreListProducts(): View
    {
        $newProducts = Product::where('status_id', StatusHelper::$finalApproval);
        $newProducts = QueryHelper::approvedListingOrderFinalApproval($newProducts, true);

        $newProducts = $newProducts->where('isUploaded', 0);

        $newProducts = $newProducts->select(DB::raw('products.brand,products.category,products.assigned_to,count(*) as total'))
            ->groupBy('products.brand', 'products.category', 'products.assigned_to')->paginate(50);
        foreach ($newProducts as $product) {
            if ($product->brand) {
                $brand = Brand::find($product->brand);
                if ($brand) {
                    $product->brandName = $brand->name;
                } else {
                    $product->brandName = '';
                }
            } else {
                $product->brandName = '';
            }
            if ($product->category) {
                $category = Category::find($product->category);
                if ($category) {
                    $product->categoryName = $category->title;
                } else {
                    $product->categoryName = '';
                }
            } else {
                $product->categoryName = '';
            }
            if ($product->assigned_to) {
                $product->assignTo = User::find($product->assigned_to)->name;
            } else {
                $product->assignTo = '';
            }
        }
        $users = User::all()->pluck('name', 'id')->toArray();

        return view('products.assign-products', compact('newProducts', 'users'));
    }

    public function assignProduct(Request $request): JsonResponse
    {
        $category = $request->category;
        $brand = $request->brand;
        $assigned_to = $request->assigned_to;
        if (! $assigned_to) {
            return response()->json(['message' => 'Select one user'], 500);
        }
        $products = Product::where('products.status_id', StatusHelper::$finalApproval)->where('products.category', $category)->where('products.brand', $brand);

        $products = QueryHelper::approvedListingOrderFinalApproval($products, true);
        $products = $products->where('products.isUploaded', 0);
        $products = $products->select('products.*')->get();

        foreach ($products as $product) {
            $product->update(['assigned_to' => $assigned_to]);
        }

        $data['assign_from'] = Auth::id();
        $data['is_statutory'] = 2;
        $data['task_details'] = 'Final Approval Assignment';
        $data['task_subject'] = 'Final Approval Assignment';
        $data['assign_to'] = $assigned_to;

        $task = Task::create($data);
        if (! empty($task)) {
            $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
        }

        if ($task->is_statutory != 1) {
            $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
        } else {
            $message = $task->task_subject.'. '.$task->task_details;
        }

        $params = [
            'number' => null,
            'user_id' => Auth::id(),
            'approved' => 1,
            'status' => 2,
            'task_id' => $task->id,
            'message' => $message,
        ];
        $user = User::find($assigned_to);
        $params['erp_user'] = $assigned_to;
        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);
        app(WhatsAppController::class)->approveMessage('task', $myRequest);

        $username = $user->name;

        return response()->json(['message' => 'Successful', 'user' => $username]);
    }

    public function assignProductNoWise(Request $request): RedirectResponse
    {
        $no_of_product_assign = $request->get('no_of_product_assign', 0);
        $assigned_to = $request->assigned_to;
        if (! $assigned_to) {
            return redirect()->back()->withErrors('Select one user');
        }
        $products = Product::where('products.status_id', StatusHelper::$finalApproval);

        $products = QueryHelper::approvedListingOrderFinalApproval($products, true);
        $products = $products->where('products.isUploaded', 0);

        if ($no_of_product_assign > 0) {
            $products = $products->limit($no_of_product_assign);
        } else {
            $products = $products->limit(0);
        }

        $products = $products->select('products.*')->get();

        foreach ($products as $product) {
            $product->update(['assigned_to' => $assigned_to]);
        }

        $data['assign_from'] = Auth::id();
        $data['is_statutory'] = 2;
        $data['task_details'] = 'Final Approval Assignment';
        $data['task_subject'] = 'Final Approval Assignment';
        $data['assign_to'] = $assigned_to;

        $task = Task::create($data);
        if (! empty($task)) {
            $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
        }

        if ($task->is_statutory != 1) {
            $message = '#'.$task->id.'. '.$task->task_subject.'. '.$task->task_details;
        } else {
            $message = $task->task_subject.'. '.$task->task_details;
        }

        $params = [
            'number' => null,
            'user_id' => Auth::id(),
            'approved' => 1,
            'status' => 2,
            'task_id' => $task->id,
            'message' => $message,
        ];

        $user = User::find($assigned_to);
        $params['erp_user'] = $assigned_to;
        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request;
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);
        app(WhatsAppController::class)->approveMessage('task', $myRequest);

        return redirect()->back()->withSuccess('Product assigned to person successfully');
    }

    public function draftedProducts(Request $request): View
    {
        Log::info('action started');
        $products = Product::where('quick_product', 1)
            ->leftJoin('brands as b', 'b.id', 'products.brand')
            ->leftJoin('categories as c', 'c.id', 'products.category')
            ->select([
                'products.id',
                'products.name as product_name',
                'b.name as brand_name',
                'c.title as category_name',
                'products.supplier',
                'products.status_id',
                'products.created_at',
                'products.supplier_link',
                'products.composition',
                'products.size',
                'products.lmeasurement',
                'products.hmeasurement',
                'products.dmeasurement',
                'products.color',
            ]);

        if ($request->category != null && $request->category != 1) {
            $products = $products->where('products.category', $request->category);
        }

        if ($request->brand_id != null) {
            $products = $products->where('products.brand', $request->brand_id);
        }

        if ($request->supplier_id != null) {
            $products = $products->where('products.supplier', $request->supplier_id);
        }

        if ($request->status_id != null) {
            $products = $products->where('products.status_id', $request->status_id);
        }

        $products = $products->orderByDesc('products.created_at')->paginate()->appends(request()->except(['page']));

        Log::info('Page Displayed here');

        $attachImageTag = config('constants.attach_image_tag');

        return view('drafted-supplier-product.index', compact('products', 'attachImageTag'));
    }

    public function editDraftedProduct(Request $request): View
    {
        $product = Product::where('id', $request->id)->first();

        return view('drafted-supplier-product.edit-modal', ['product' => $product]);
    }

    public function deleteDraftedProducts(Request $request): JsonResponse
    {
        $productIds = $request->products;
        if (! empty($productIds)) {
            $products = Product::whereIn('id', $productIds)->get();
            if (! $products->isEmpty()) {
                foreach ($products as $product) {
                    $product->delete();
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Successfully deleted!']);
    }

    public function editDraftedProducts(Request $request): JsonResponse
    {
        $draftedProduct = Product::where('id', $request->id)->first();

        if ($draftedProduct) {
            $draftedProduct->fill($request->all());
            $draftedProduct->save();

            return response()->json(['code' => 200, 'data' => $draftedProduct, 'message' => 'Successfully edited!']);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong row id!']);
    }

    public function updateApprovedBy(Request $request, $product_id): JsonResponse
    {
        $product = Product::find($product_id);

        if ($product) {
            $product->update([
                'is_approved' => 1,
                'approved_by' => $request->user_id,
            ]);
        }

        return response()->json([
            'code' => 200,
        ]);
    }

    public function createTemplate(CreateTemplateProductRequest $request): RedirectResponse
    {
        $template = new ProductTemplate;
        $template->template_no = $request->template_no;
        $template->text = $request->text;
        $template->background_color = $request->background;
        $template->template_status = 'python';

        if ($template->save()) {
            if (! empty($request->get('product_media_id')) && is_array($request->get('product_media_id'))) {
                foreach ($request->get('product_media_id') as $mediaid) {
                    $media = Media::find($mediaid);
                    $template->attachMedia($media, ['template-image-attach']);
                    $template->save();
                    $imagesArray[] = getMediaUrl($media);
                }
            }

            return redirect()->back()->with('success', 'Template has been created successfully');
        }

        return redirect()->back()->with('error', 'Something went wrong, Please try again!');
    }

    /**
     * This funcrtion is use for delete product suggested
     *
     * @param  mixed  $ids
     */
    public function deleteSuggestedProduct(Request $request, $ids = ''): JsonResponse
    {
        try {
            $idArr = explode(',', $request->ids);
            $sugProd = SuggestedProduct::whereIn('id', $idArr)->delete();
            if ($sugProd != 0) {
                return response()->json(['code' => 200, 'message' => 'Successfully Deleted']);
            }

            return response()->json(['code' => 500, 'message' => 'Please select any record']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This funcrtion is use for get product suggested log
     */
    public function getSuggestedProductLog(Request $request): JsonResponse
    {
        try {
            $sugProd = ProductSuggestedLog::where('parent_id', $request->id)->get();
            if ($sugProd->toArray()) {
                $html = '';
                foreach ($sugProd as $sugProdData) {
                    $html .= '<tr>';
                    $html .= '<td>'.$sugProdData->id.'</td>';
                    $html .= '<td>'.$sugProdData->log.'</td>';
                    $html .= '</tr>';
                }

                return response()->json(['code' => 200, 'data' => $html, 'message' => 'Log listed Successfully']);
            }

            return response()->json(['code' => 500, 'message' => 'Log not found']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function attachedImageGrid($model_type, $model_id, $status, $assigned_user, Request $request)
    {
        $model_type = 'customer';
        if ($model_type == 'customer') {
            $customerId = $model_id;
        } else {
            $customerId = null;
        }
        if ($request->customer_id) {
            $explode = explode('/', $request->customer_id);
            if (count($explode) > 1) {
                $customerId = $explode[1];
            }
        }

        if ($request->category) {
            try {
                $filtered_category = $request->category;
            } catch (Exception $e) {
                $filtered_category = [];
            }
        } else {
            $filtered_category = [];
        }
        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple-cat-list input-lg select-multiple', 'multiple' => true, 'data-placeholder' => 'Select Category..'])
            ->selected($filtered_category)
            ->renderAsDropdown();

        //DB::enableQueryLog();
        $roletype = $request->input('roletype') ?? 'Sale';
        $term = $request->input('term');
        if ($request->total_images) {
            $perPageLimit = $request->total_images;
        } else {
            $perPageLimit = $request->get('per_page');
        }

        if (empty($perPageLimit)) {
            $perPageLimit = Setting::get('pagination');
        }
        $suggestedProducts = SuggestedProduct::with('customer')->leftJoin('suggested_product_lists as spl', 'spl.suggested_products_id', 'suggested_products.id');
        $suggestedProducts = $suggestedProducts->leftJoin('products as p', 'spl.product_id', 'p.id');
        $suggestedProducts = $suggestedProducts->leftJoin('customers as c', 'c.id', 'suggested_products.customer_id');
        if ($customerId) {
            $suggestedProducts = $suggestedProducts->where('suggested_products.customer_id', $customerId);
        }

        if ($request->category != null) {
            $suggestedProducts = $suggestedProducts->whereIn('p.category', $request->category);
        }

        if ($request->brand != null) {
            $suggestedProducts = $suggestedProducts->whereIn('p.brand', $request->brand);
        }

        if ($request->platform != null) {
            $suggestedProducts = $suggestedProducts->where('suggested_products.platform', $request->platform);
        }

        if (! empty($term)) {
            $suggestedProducts = $suggestedProducts->where(function ($q) use ($term) {
                $q->orWhere('p.sku', 'LIKE', '%'.$term.'%')->orWhere('p.id', 'LIKE', '%'.$term.'%');
            });
        }

        $loggedInUser = auth()->user();
        $isInCustomerService = $loggedInUser->isInCustomerService();
        if ($isInCustomerService) {
            $suggestedProducts = $suggestedProducts->where('c.user_id', $loggedInUser->id);
        }

        // $perPageLimit
        $suggestedProducts = $suggestedProducts->select(DB::raw('suggested_products.*, max(suggested_products.created_at) as created_at'))->orderByDesc('suggested_products.created_at')->groupBy('suggested_products.id')->paginate($perPageLimit);

        foreach ($suggestedProducts as $suggestion) {
            $last_attached = SuggestedProductList::where('suggested_products_id', $suggestion->id)->orderByDesc('date')->first();
            if ($last_attached) {
                $suggestion->last_attached = $last_attached->date;
            } else {
                $suggestion->last_attached = $suggestion->created_at;
            }
            $brandIds = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')->where('suggested_product_lists.customer_id', $suggestion->customer_id)->where('suggested_products_id', $suggestion->id)->groupBy('products.brand')->pluck('products.brand');
            if (count($brandIds) > 0) {
                $suggestion->brdNames = Brand::whereIn('id', $brandIds)->get();
            } else {
                $suggestion->brdNames = [];
            }

            $catIds = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')->where('suggested_product_lists.customer_id', $suggestion->customer_id)->where('suggested_products_id', $suggestion->id)->groupBy('products.category')->pluck('products.category');
            if (count($catIds) > 0) {
                $suggestion->catNames = Category::whereIn('id', $catIds)->get();
            } else {
                $suggestion->catNames = [];
            }
        }

        $templateArr = Template::all();

        $all_product_ids = [];
        $model_type = 'customer';
        $countBrands = 0;
        $countCategory = 0;
        $countSuppliers = 0;
        $categoryArray = [];
        $from = '';
        $products_count = 0;
        $selected_products = [];
        $brand = $request->brand;
        $customerName = '';
        if ($customerId) {
            $customerName = Customer::find($customerId)->name;
        }
        $brands = Brand::pluck('name', 'id');
        if ($request->ajax()) {
            $html = view('partials.attached-image-load', [
                'suggestedProducts' => $suggestedProducts,
                'all_product_ids' => $all_product_ids,
                'brand' => $brand,
                'selected_products' => $request->selected_products ? json_decode($request->selected_products) : [],
                'model_type' => $model_type,
                'countBrands' => $countBrands,
                'countCategory' => $countCategory,
                'countSuppliers' => $countSuppliers,
                'customerId' => $customerId,
                'categoryArray' => $categoryArray,
                'customerName' => $customerName,
                'brands' => $brands,
            ])->render();
            if (! empty($from) && $from == 'attach-image') {
                return $html;
            }

            $selected_products = $request->selected_products ? json_decode($request->selected_products) : [];

            return view('partials.attached-image-load', compact(
                'suggestedProducts', 'all_product_ids', 'brand', 'selected_products', 'model_type', 'countBrands', 'countCategory', 'countSuppliers', 'customerId', 'categoryArray', 'customerName', 'brands'));
        }

        $message_body = $request->message ? $request->message : '';
        $sending_time = $request->sending_time ?? '';

        $locations = ProductLocation::pluck('name', 'name');
        $suppliers = Supplier::select(['id', 'supplier'])->whereIn('id', ProductSupplier::selectRaw('DISTINCT(`supplier_id`) as suppliers')->pluck('suppliers')->toArray())->get();

        $quick_sell_groups = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();
        $customers = Customer::pluck('name', 'id');
        $pendingProductsCount = 0;
        if ($roletype != 'Selection' && $roletype != 'Sale') {
            $pendingProductsCount = Product::getPendingProductsCount($roletype);
        }

        return view('partials.attached-image-grid', compact(
            'suggestedProducts', 'templateArr', 'products_count', 'roletype', 'model_id', 'selected_products', 'model_type', 'status', 'assigned_user', 'category_selection', 'brand', 'filtered_category', 'message_body', 'sending_time', 'locations', 'suppliers', 'all_product_ids', 'quick_sell_groups', 'countBrands', 'countCategory', 'countSuppliers', 'customerId', 'categoryArray', 'term', 'customers', 'customerName', 'pendingProductsCount', 'brands'
        ));
    }

    public function crop_rejected_status(Request $request): JsonResponse
    {
        if ($request->status == 'reject') {
            $lastPriorityScrap = Product::orderByDesc('scrap_priority')->first();
            if ($lastPriorityScrap) {
                if ($lastPriorityScrap->scrap_priority) {
                    $lastPriority = $lastPriorityScrap->scrap_priority + 1;
                } else {
                    $lastPriority = 1;
                }
            } else {
                $lastPriority = 1;
            }

            Product::where('id', $request->product_id)->update(['status_id' => StatusHelper::$autoCrop, 'scrap_priority' => $lastPriority]);
            SiteCroppedImages::where('product_id', $request->product_id)->where('website_id', $request->site_id)->delete();
        }
        RejectedImages::updateOrCreate(
            ['website_id' => $request->site_id, 'product_id' => $request->product_id, 'user_id' => auth()->user()->id],
            ['status' => $request->status == 'approve' ? 1 : 0]
        );

        return response()->json(['code' => 200, 'message' => 'Successfully rejected']);
    }

    public function all_crop_rejected_status(Request $request): JsonResponse
    {
        if ($request->status == 'reject') {
            $lastPriorityScrap = Product::orderByDesc('scrap_priority')->first();
            if ($lastPriorityScrap) {
                if ($lastPriorityScrap->scrap_priority) {
                    $lastPriority = $lastPriorityScrap->scrap_priority + 1;
                } else {
                    $lastPriority = 1;
                }
            } else {
                $lastPriority = 1;
            }
            $sites = SiteCroppedImages::where('product_id', $request->product_id)->get();
            foreach ($sites as $site) {
                RejectedImages::updateOrCreate(
                    ['website_id' => $site->website_id, 'product_id' => $request->product_id, 'user_id' => auth()->user()->id],
                    ['status' => $request->status == 'approve' ? 1 : 0]
                );
            }

            Product::where('id', $request->product_id)->update(['status_id' => StatusHelper::$autoCrop, 'scrap_priority' => $lastPriority]);
            SiteCroppedImages::where('product_id', $request->product_id)->delete();
        }

        return response()->json(['code' => 200, 'message' => 'Successfully rejected']);
    }

    public function attachMoreProducts($suggested_products_id): JsonResponse
    {
        $lastSuggestion = SuggestedProduct::where('id', $suggested_products_id)->orderByDesc('created_at')->first();
        $customerId = $lastSuggestion->customer_id;
        $brands = [];
        $categories = [];
        $term = '';
        $limit = 10;
        if ($lastSuggestion) {
            if ($lastSuggestion->brands) {
                $brands = json_decode($lastSuggestion->brands);
            }
            if ($lastSuggestion->categories) {
                $categories = json_decode($lastSuggestion->categories);
            }
            $term = $lastSuggestion->keyword;
            $limit = $lastSuggestion->total;
        }
        $remove_ids = SuggestedProductList::where('suggested_products_id', $suggested_products_id)->pluck('product_id as id');

        $products = (new Product)->newQuery()->latest();

        if (count($brands) > 0) {
            $products = $products->whereIn('brand', $brands);
        }

        if (count($categories) > 0) {
            $category_children = [];

            foreach ($categories as $category) {
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

                    array_push($category_children, $category);
                } else {
                    array_push($category_children, $category);
                }
            }

            $products = $products->whereIn('category', $category_children);
        }

        if (trim($term) != '') {
            $products = $products->where(function ($query) use ($term) {
                $query->where('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%")
                    ->orWhere('name', 'LIKE', "%$term%")
                    ->orWhere('short_description', 'LIKE', "%$term%");
                if ($term == -1) {
                    $query = $query->orWhere('isApproved', -1);
                }

                $brand_id = Brand::where('name', 'LIKE', "%$term%")->value('id');
                if ($brand_id) {
                    $query = $query->orWhere('brand', 'LIKE', "%$brand_id%");
                }

                $category_id = Category::where('title', 'LIKE', "%$term%")->value('id');
                if ($category_id) {
                    $query = $query->orWhere('category', $category_id);
                }
            });
        }
        if (count($remove_ids) > 0) {
            $products = $products->whereNotIn('products.id', $remove_ids);
        }

        // select fields..
        $products = $products->select(['products.id', 'name', 'short_description', 'color', 'sku', 'products.category', 'products.size', 'price_eur_special', 'price_inr_special', 'supplier', 'purchase_status', 'products.created_at']);

        $products = $products->paginate($limit);

        if (count($products) > 0) {
            $data_to_insert = [];
            foreach ($products as $product) {
                $exists = SuggestedProductList::where('suggested_products_id', $suggested_products_id)->where('customer_id', $customerId)->where('product_id', $product->id)->where('date', date('Y-m-d'))->first();
                if (! $exists) {
                    $pr = Product::find($product->id);
                    if ($pr->hasMedia(config('constants.attach_image_tag'))) {
                        $data_to_insert[] = [
                            'suggested_products_id' => $suggested_products_id,
                            'customer_id' => $customerId,
                            'product_id' => $product->id,
                            'date' => date('Y-m-d'),
                        ];
                    }
                }
            }
            if (count($data_to_insert) > 0) {
                SuggestedProductList::insert($data_to_insert);
            }
        }
        $url = '/attached-images-grid/customer?customer_id='.$customerId;

        return response()->json(['message' => 'Successfull', 'url' => $url, 'code' => 200]);
    }

    public function suggestedProducts($model_type, $model_id, $status, $assigned_user, Request $request)
    {
        $model_type = 'customer';
        $customerId = null;
        if ($request->customer_id) {
            $explode = explode('/', $request->customer_id);
            if (count($explode) > 1) {
                $customerId = $explode[1];
            }
        }
        $roletype = $request->input('roletype') ?? 'Sale';
        $term = $request->input('term');
        if ($request->total_images) {
            $perPageLimit = $request->total_images;
        } else {
            $perPageLimit = $request->get('per_page');
        }
        if (empty($perPageLimit)) {
            $perPageLimit = Setting::get('pagination');
        }
        $suggestedProducts = SuggestedProduct::join('suggested_product_lists', 'suggested_products.customer_id', 'suggested_product_lists.customer_id')->where('suggested_product_lists.chat_message_id', '!=', null);
        if ($customerId) {
            $suggestedProducts = $suggestedProducts->where('suggested_products.customer_id', $customerId);
        }

        $suggestedProducts = $suggestedProducts->select(DB::raw('suggested_products.*, suggested_products.created_at as created_at'))->orderByDesc('created_at')->groupBy('suggested_products.id')->paginate($perPageLimit);

        foreach ($suggestedProducts as $suggestion) {
            $suggestion->last_attached = SuggestedProduct::where('customer_id', $suggestion->customer_id)->orderByDesc('created_at')->first()->created_at;

            $brandIds = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')->where('suggested_product_lists.customer_id', $suggestion->customer_id)->where('suggested_products_id', $suggestion->id)->groupBy('products.brand')->pluck('products.brand');
            if (count($brandIds) > 0) {
                $suggestion->brdNames = Brand::whereIn('id', $brandIds)->get();
            } else {
                $suggestion->brdNames = [];
            }

            $catIds = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')->where('suggested_product_lists.customer_id', $suggestion->customer_id)->where('suggested_products_id', $suggestion->id)->groupBy('products.category')->pluck('products.category');
            if (count($catIds) > 0) {
                $suggestion->catNames = Category::whereIn('id', $catIds)->get();
            } else {
                $suggestion->catNames = [];
            }
        }

        if ($request->category) {
            try {
                $filtered_category = $request->category;
            } catch (Exception $e) {
                $filtered_category = [1];
            }
        } else {
            $filtered_category = [1];
        }

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple-cat-list input-lg select-multiple', 'multiple' => true, 'data-placeholder' => 'Select Category..'])
            ->selected($filtered_category)
            ->renderAsDropdown();

        $all_product_ids = [];
        $model_type = 'customer';
        $countBrands = 0;
        $countCategory = 0;
        $countSuppliers = 0;
        $categoryArray = [];
        $from = '';
        $products_count = 0;
        $selected_products = [];
        $brand = $request->brand;
        $pendingProductsCount = 0;
        if ($roletype != 'Selection' && $roletype != 'Sale') {
            $pendingProductsCount = Product::getPendingProductsCount($roletype);
        }
        $brands = Brand::where('magento_id', '>', 0)->pluck('name', 'id');
        $brand_segments = Brand::all();
        $statusData = (new Status)->all();
        if ($request->ajax()) {
            $html = view('partials.suggested-image-load', [
                'suggestedProducts' => $suggestedProducts,
                'all_product_ids' => $all_product_ids,
                'selected_products' => $request->selected_products ? json_decode($request->selected_products) : [],
                'model_type' => $model_type,
                'countBrands' => $countBrands,
                'countCategory' => $countCategory,
                'countSuppliers' => $countSuppliers,
                'customerId' => $customerId,
                'categoryArray' => $categoryArray,
                'brand' => $brand,
                'pendingProductsCount' => $pendingProductsCount,
                'brands' => $brands,
                'brand_segments' => $brand_segments,
                'statusData' => $statusData,
            ])->render();

            if (! empty($from) && $from == 'attach-image') {
                return $html;
            }

            $selected_products = $request->selected_products ? json_decode($request->selected_products) : [];

            return view('partials.suggested-image-load', compact(
                'suggestedProducts', 'all_product_ids', 'selected_products', 'model_type', 'countBrands', 'countCategory', 'countSuppliers', 'customerId', 'categoryArray', 'brand', 'pendingProductsCount', 'brands', 'brand_segments', 'statusData'
            ));
        }

        $message_body = $request->message ? $request->message : '';
        $sending_time = $request->sending_time ?? '';

        $locations = ProductLocation::pluck('name', 'name');
        $suppliers = Supplier::select(['id', 'supplier'])->whereIn('id', ProductSupplier::selectRaw('DISTINCT(`supplier_id`) as suppliers')->pluck('suppliers')->toArray())->get();

        $quick_sell_groups = QuickSellGroup::select('id', 'name')->orderByDesc('id')->get();

        $customers = Customer::pluck('name', 'id');

        return view('partials.suggested-image-grid', compact(
            'suggestedProducts', 'products_count', 'roletype', 'model_id', 'selected_products', 'model_type', 'status', 'assigned_user', 'category_selection', 'brand', 'filtered_category', 'message_body', 'sending_time', 'locations', 'suppliers', 'all_product_ids', 'quick_sell_groups', 'countBrands', 'countCategory', 'countSuppliers', 'customerId', 'categoryArray', 'term', 'customers'
        ));
    }

    public function removeProducts($suggested_products_id, Request $request): JsonResponse
    {
        foreach ($products as $product_list_id) {
            $suggested = SuggestedProductList::find($product_list_id);
            if ($suggested) {
                if ($suggested->chat_message_id) {
                    $suggested->remove_attachment = 1;
                    $suggested->save();
                } else {
                    $suggested->delete();
                }
            }
        }
        $remains = SuggestedProductList::where('suggested_products_id', $suggested_products_id)->count();
        if (! $remains) {
            SuggestedProduct::where('id', $suggested_products_id)->delete();
        }

        return response()->json(['code' => 200, 'message' => 'Successfull']);
    }

    public function removeSingleProduct($customer_id, Request $request): JsonResponse
    {
        $suggested = SuggestedProductList::find($request->product_id);
        if ($suggested) {
            if ($suggested->chat_message_id) {
                $suggested->remove_attachment = 1;
                $suggested->save();
            } else {
                $suggested->delete();

                $remains = SuggestedProductList::where('customer_id', $customer_id)->count();
                if (! $remains) {
                    SuggestedProduct::where('customer_id', $customer_id)->delete();
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Successfull']);
    }

    public function forwardProducts(Request $request): JsonResponse
    {
        $customerId = 0;
        if ($request->customer_id) {
            $explode = explode('/', $request->customer_id);
            if (count($explode) > 1) {
                $customerId = $explode[1];
            }
        }

        $forward_suggestedproductid = $request->forward_suggestedproductid;

        if (! $customerId) {
            $msg = ' Customer not found';

            return response()->json(['code' => 500, 'message' => $msg]);
        }

        $suggestedProducts = false;
        if ($forward_suggestedproductid) {
            $suggestedProducts = SuggestedProduct::where('customer_id', $customerId)->where('id', $forward_suggestedproductid)->orderByDesc('created_at')->first();
        }

        $products = json_decode($request->products, true);
        $total = count($products);

        if ($suggestedProducts) {
            $suggestedProducts->touch();
            $new_suggestedproductid = $suggestedProducts->id;
        } else {
            $suggestedProducts = new SuggestedProduct;
            $suggestedProducts->customer_id = $customerId;
            $suggestedProducts->total = $total;
            $suggestedProducts->save();
            $new_suggestedproductid = $suggestedProducts->id;
        }

        $listIds = json_decode($request->products, true);
        $data_to_insert = [];
        $inserted = 0;

        if (! empty($listIds) && is_array($listIds)) {
            foreach ($listIds as $listedImage) {
                $productList = SuggestedProductList::find($listedImage);
                $product = Product::find($productList->product_id);
                $imageDetails = $product->getMedia(config('constants.attach_image_tag'))->first();
                $image_key = $imageDetails->getKey();
                $media = Media::find($image_key);
                if ($media) {
                    $mediable = Mediables::where('media_id', $media->id)->where('mediable_type', Product::class)->first();
                    if ($mediable) {
                        $exists = SuggestedProductList::where('suggested_products_id', $new_suggestedproductid)->where('customer_id', $customerId)->where('product_id', $mediable->mediable_id)->where('date', date('Y-m-d'))->first();
                        if (! $exists) {
                            $pr = Product::find($mediable->mediable_id);
                            if ($pr->hasMedia(config('constants.attach_image_tag'))) {
                                $data_to_insert[] = [
                                    'suggested_products_id' => $new_suggestedproductid,
                                    'customer_id' => $customerId,
                                    'product_id' => $mediable->mediable_id,
                                    'date' => date('Y-m-d'),
                                ];
                            }
                        }
                    }
                }
            }

            $inserted = count($data_to_insert);
            if ($inserted > 0) {
                SuggestedProductList::insert($data_to_insert);
            }

            if ($request->type == 'forward') {
                $data['_token'] = $request->_token;
                $data['send_pdf'] = 0;
                $data['pdf_file_name'] = '';
                $data['images'] = $request->products;
                $data['image'] = null;
                $data['screenshot_path'] = null;
                $data['message'] = null;
                $data['customer_id'] = $customerId;
                $data['status'] = 2;
                $data['type'] = 'customer-attach';
                AttachImagesSend::dispatch($data)->onQueue('customer_message');
            }
        }
        $msg = $inserted.' Products added successfully';

        return response()->json(['code' => 200, 'message' => $msg]);
    }

    public function resendProducts($suggestedproductid, Request $request): JsonResponse
    {
        $suggestedProducts = SuggestedProduct::where('id', $suggestedproductid)->orderByDesc('created_at')->first();
        $customer_id = $suggestedProducts->customer_id;
        $suggestedProducts->touch();

        $data['_token'] = $request->_token;
        $data['send_pdf'] = 0;
        $data['pdf_file_name'] = '';
        $data['images'] = $request->products;
        $data['image'] = null;
        $data['screenshot_path'] = null;
        $data['message'] = null;
        $data['customer_id'] = $customer_id;
        $data['status'] = 2;
        $data['type'] = 'customer-attach';
        AttachImagesSend::dispatch($data)->onQueue('customer_message');
        $msg = ' Images Resend successfully';

        return response()->json(['code' => 200, 'message' => $msg]);
    }

    public function getCustomerProducts($type, $suggested_products_id, $customer_id, Request $request): View
    {
        $term = null;
        //$suggested_products_id=3;
        $suggestedProductsLists = SuggestedProductList::with('getMedia')->where('suggested_products_id', $suggested_products_id)->where('customer_id', $customer_id)->where('remove_attachment', 0)
            ->orderByDesc('date')->whereNotNull('media_id')->get();

        if ($type == 'attach') {
            $productsLists = SuggestedProductList::where('suggested_products_id', $suggested_products_id)->where('customer_id', $customer_id)->whereNull('media_id')->where('remove_attachment', 0)
                ->select('suggested_product_lists.*')->orderByDesc('date')->get()->unique('date');
        } else {
            $productsLists = SuggestedProductList::where('customer_id', $customer_id)->whereNull('media_id')->where('chat_message_id', '!=', null)
                ->select('suggested_product_lists.*')->orderByDesc('date')->get()->unique('date');
        }
        $customer = Customer::find($customer_id);

        foreach ($productsLists as $suggestion) {
            if ($type == 'attach') {
                $products = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')
                    ->where('suggested_product_lists.customer_id', $customer_id)
                    ->where('suggested_product_lists.suggested_products_id', $suggested_products_id)
                    ->where('remove_attachment', 0)
                    ->where('suggested_product_lists.date', $suggestion->date);
            } else {
                $products = SuggestedProductList::join('products', 'suggested_product_lists.product_id', 'products.id')
                    ->where('suggested_product_lists.customer_id', $customer_id)
                    ->where('suggested_product_lists.suggested_products_id', $suggested_products_id)
                    ->where('chat_message_id', '!=', null)
                    ->where('suggested_product_lists.date', $suggestion->date);
            }

            if (isset($request->brand[0])) {
                if ($request->brand[0] != null) {
                    $products = $products->whereIn('products.brand', $request->brand);
                }
            }

            if (isset($request->category[0])) {
                if ($request->category[0] != null && $request->category[0] != 1) {
                    $category_children = [];

                    foreach ($request->category as $category) {
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
                    }
                    $products = $products->whereIn('category', $category_children);
                }
            }

            if (trim($term) != '') {
                $products = $products->where(function ($query) use ($term) {
                    $query->where('sku', 'LIKE', "%$term%")
                        ->orWhere('products.id', 'LIKE', "%$term%")
                        ->orWhere('name', 'LIKE', "%$term%")
                        ->orWhere('short_description', 'LIKE', "%$term%");
                    if ($term == -1) {
                        $query = $query->orWhere('isApproved', -1);
                    }

                    $brand_id = Brand::where('name', 'LIKE', "%$term%")->value('id');
                    if ($brand_id) {
                        $query = $query->orWhere('brand', 'LIKE', "%$brand_id%");
                    }

                    $category_id = Category::where('title', 'LIKE', "%$term%")->value('id');
                    if ($category_id) {
                        $query = $query->orWhere('category', $category_id);
                    }
                });
            }
            $suggestion->products = $products->select('products.*', 'suggested_product_lists.created_at as sort', 'suggested_product_lists.id as suggested_product_list_id')->orderBy('sort')->get();
        }
        $selected_products = [];
        $model_type = 'customer';
        if ($type == 'attach') {
            return view('partials.attached-image-products', compact('productsLists', 'customer_id', 'selected_products', 'model_type', 'suggested_products_id', 'customer', 'suggestedProductsLists'));
        } else {
            return view('partials.suggested-image-products', compact('productsLists', 'customer_id', 'selected_products', 'model_type', 'suggested_products_id', 'customer', 'suggestedProductsLists'));
        }
    }

    public function addDraftProductsToQuickSell(Request $request): JsonResponse
    {
        $post = $request->all();

        $group = QuickSellGroup::orderByDesc('id')->first();
        if ($group != null) {
            $group_create = new QuickSellGroup;
            $incrementId = ($group->group + 1);
            $group_create->group = $incrementId;
            $group_create->name = $post['groupName'];
            $group_create->save();
            $group_id = $group_create->group;
        } else {
            $group = new QuickSellGroup;
            $group->group = 1;
            $group->name = $post['groupName'];
            $group->save();
            $group_id = $group->group;
        }
        foreach ($request->products as $id) {
            $group = new ProductQuicksellGroup;
            $group->product_id = $id;
            $group->quicksell_group_id = $group_id;
            $group->save();
        }
        $msg = 'Products are added into group successfully';

        return response()->json(['code' => 200, 'message' => $msg]);
    }

    public function test(Request $request): JsonResponse
    {
        $product = Product::where('id', $request->productid)->first();
        if ($product !== null) {
            $image = $product->getMedia(config('constants.attach_image_tag'))->first();
            $data = [
                'title' => $product->name,
                'url' => getMediaUrl($image),
                'amount' => $product->price,
                'description' => $product->short_description,
            ];
        } else {
            $data = [
                'status' => false,

            ];
        }

        return response()->json($data);
    }

    public function add_product_def_cust($product_id, Request $request): JsonResponse
    {
        $product = Product::find($product_id);

        $def_cust_id = config('env.DEFAULT_CUST_ID');

        $customers = Customer::find($def_cust_id);

        $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $nextId = 0;
        if (! empty($statement)) {
            $nextId = $statement[0]->Auto_increment;
        }

        $order_id = 'OFF-'.date('Ym').'-'.$nextId;

        $order_data = [
            'customer_id' => $def_cust_id,
            'order_id' => $order_id,
            'order_date' => date('Y-m-d'),
            'client_name' => $customers->name,
        ];
        $order = Order::create($order_data);

        $order_id = $order->id;

        $orderproduct_data = [
            'order_id' => $order_id,
            'sku' => $product->sku,
            'product_id' => $product->id,
            'product_price' => $product->price,
        ];
        OrderProduct::create($orderproduct_data);

        return response()->json(['code' => 200, 'message' => 'Purchase Products Added successfully']);
    }

    public function sendLeadPrice(Request $request): JsonResponse
    {
        if (empty($request->customer_id) && empty($request->product_id)) {
            return response()->json(['code' => 500, 'message' => 'Please check product id and customer id exist']);
        }

        $customer = Customer::find($request->customer_id);

        if ($customer && ! empty($request->product_id)) {
            app(CustomerController::class)->dispatchBroadSendPrice($customer, array_unique([$request->product_id]), true);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Lead price created']);
    }

    public function getWebsites(Request $request): JsonResponse
    {
        $productId = $request->get('product_id');
        if ($productId > 0) {
            $websites = ProductHelper::getStoreWebsiteName($productId);
            $websitesList = StoreWebsite::whereIn('id', $websites)->get();
            if (! $websitesList->isEmpty()) {
                return response()->json(['code' => 200, 'data' => $websitesList]);
            } else {
                return response()->json(['code' => 200, 'data' => []]);
            }
        } else {
            return response()->json(['code' => 200, 'data' => []]);
        }
    }

    public function getTranslationProduct(Request $request, $id): View
    {
        $translation = Product_translation::where('product_id', $id)->get();

        return view('products.partials.translation-product', compact('translation'));
    }

    public function changeimageorder(Request $request): JsonResponse
    {
        if (! empty($request->mid) && ! empty($request->pid) && ! empty($request->val)) {
            Mediables::where('mediable_type', Product::class)->where('mediable_id', $request->pid)->where('media_id', $request->mid)->update(['order' => $request->val]);

            return response()->json(['code' => 200, 'data' => [], 'message' => 'order update successfully']);
        } else {
            return response()->json(['code' => 0, 'data' => [], 'message' => 'error']);
        }
    }

    public function pushproductlist(Request $request): View
    {
        $products = StoreWebsiteProduct::join('products', 'store_website_products.product_id', 'products.id');
        $products->join('store_websites', 'store_website_products.store_website_id', 'store_websites.id');
        $products->leftJoin('categories', 'products.category', 'categories.id');
        $products->leftJoin('brands', 'products.brand', 'brands.id');
        $products->select('store_website_products.*', 'products.name as product_name', 'store_websites.title as store_website_name', 'store_websites.magento_url as store_website_url', 'categories.title as category', 'brands.name as brand');
        if ($request->website != '') {
            $products->where('store_website_id', $request->website);
        }
        if ($request->category != '') {
            $products->where('products.category', $request->category);
        }
        if ($request->brand != '') {
            $products->where('products.brand', $request->brand);
        }
        $products->orderByDesc('created_at');
        $products = $products->paginate(Setting::get('pagination'));
        $websiteList = StoreWebsite::get();
        $categoryList = Category::all();
        $brandList = Brand::get();

        return view('products.pushproductlist', compact('products', 'categoryList', 'brandList', 'websiteList'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function changeCategory(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        if ($product) {
            $product->category = $request->category_id;
            $product->save();

            return response()->json(['code' => 200, 'message' => 'category updated successfully']);
        } else {
            return response()->json(['code' => 500, 'message' => 'category is unable to update']);
        }
    }

    public function approvedScrapperImages(Request $request, $pageType = '')
    {
        $all_store_websites = StoreWebsite::where('website_source', 'magento')->pluck('title', 'id')->toArray();

        $images = new scraperImags;
        if (! empty($request->store_website_id)) {
            $images = $images->whereIn('store_website', $request->store_website_id);
        }

        $checking = 0;
        if (! empty($request->si_status)) {
            if ($request->si_status == 1) {
                $images = $images->where('si_status', 1);
            } elseif ($request->si_status == 2) {
                $images = $images->where('si_status', 2);
            } elseif ($request->si_status == 3) {
                $images = $images->where('si_status', 3);
            } elseif ($request->si_status == 4) {
                $images = $images->where('manually_approve_flag', 1);
            } else {
                $images = $images->where('si_status', 1);
                $checking = 1;
            }
        } else {
            $images = $images->where('si_status', 1);
            $checking = 1;
        }

        if (! empty($request->url)) {
            $term = $request->url;
            $images = $images->where(function ($query) use ($term) {
                return $query->orWhere('url', 'like', '%'.$term.'%');
            });
        }
        $images = $images->orderByDesc('id');
        $images = $images->paginate(60);

        if ($request->ajax()) {
            if ($checking == 1) {
                if (! empty($images[0]->id)) {
                    scraperImags::where('id', '>', $images[0]->id)->where('si_status', 1)->where('manually_approve_flag', 0)->update(['si_status' => 2]);
                }
            }

            $viewpath = 'products.scrapper_listing_image_ajax';

            return view($viewpath, [
                'checking' => $checking,
                'products' => $images,
                'products_count' => $images->total(),
            ]);
        }

        $viewpath = 'products.scrapper_listing';

        return view($viewpath, [
            'checking' => $checking,
            'products' => $images,
            'all_store_websites' => $all_store_websites,
            'products_count' => $images->total(),
        ]);
    }

    public function getProductSupplierList(Request $request): JsonResponse
    {
        $datas = Product::with('suppliers_name')->select('id', 'supplier')->where('id', $request->product_id)->first();

        $suppliers = [];
        $supplier = '';
        if (! empty($datas)) {
            if (! empty($datas['suppliers_name'])) {
                $suppliers = $datas['suppliers_name'];
            }

            if (! empty($datas['supplier'])) {
                $supplier = $datas['supplier'];
            }
        }

        return response()->json([
            'status' => true,
            'data' => $suppliers,
            'supplier' => $supplier,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function approvedScrapperImagesCompare(Request $request, $pageType = '', $id = ''): View
    {
        if (! empty($id)) {
            $image = scraperImags::find($id);

            if (! empty($image)) {
                $images = new scraperImags;
                $images = $images->where('url', $image->url);

                $images = $images->orderByDesc('id');
                $images = $images->paginate(60);

                if ($request->ajax()) {
                    $viewpath = 'products.scrapper_listing_image_ajax_compare';

                    return view($viewpath, [
                        'products' => $images,
                        'products_count' => $images->total(),
                    ]);
                }

                $viewpath = 'products.scrapper_listing_compare';

                return view($viewpath, [
                    'products' => $images,
                    'products_count' => $images->total(),
                ]);
            }
        }
    }

    public function truncateScrapperImagesMedia(Request $request): JsonResponse
    {
        $scrapperImages = scraperImags::pluck('img_url');

        if (! empty($scrapperImages)) {
            foreach ($scrapperImages as $value) {
                $path = public_path().'/scrappersImages/';
                if (file_exists($path.$value)) {
                    unlink($path.$value);
                }
            }
        }

        DB::statement('TRUNCATE TABLE scraper_imags');

        return response()->json([
            'status' => true,
            'message' => ' Your selected batabase tables has been truncate successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $translation = Product_translation::where('id', $id)->first();
        if ($translation) {
            $original = Product_translation::where('product_id', $translation->product_id)->where('locale', 'en')->first();
            if ($original) {
                $titleScore = $descScore = $compositionScore = $colorScore = $countryOfManufactureScore = $dimensionScore = 0;
                if (! empty($translation->title)) {
                    $titleScore = app('translation-lambda-helper')->getTranslateScore($original->title, $translation->title);
                }
                if (! empty($translation->description)) {
                    $descScore = app('translation-lambda-helper')->getTranslateScore($original->description, $translation->description);
                }
                if (! empty($translation->composition)) {
                    $compositionScore = app('translation-lambda-helper')->getTranslateScore($original->composition, $translation->composition);
                }
                if (! empty($translation->color)) {
                    $colorScore = app('translation-lambda-helper')->getTranslateScore($original->color, $translation->color);
                }
                if (! empty($translation->country_of_manufacture)) {
                    $countryOfManufactureScore = app('translation-lambda-helper')->getTranslateScore($original->country_of_manufacture, $translation->country_of_manufacture);
                }
                if (! empty($translation->dimension)) {
                    $dimensionScore = app('translation-lambda-helper')->getTranslateScore($original->dimension, $translation->dimension);
                }

                $translation->title_score = $titleScore;
                $translation->description_score = $descScore;
                $translation->composition_score = $compositionScore;
                $translation->color_score = $colorScore;
                $translation->country_of_manufacture_score = $countryOfManufactureScore;
                $translation->dimension_score = $dimensionScore;
                $translation->save();

                return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong comment id!']);
        }
    }
}
