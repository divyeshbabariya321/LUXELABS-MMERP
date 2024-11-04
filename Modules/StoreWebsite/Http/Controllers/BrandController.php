<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\Brand;
use App\Category;
use App\Models\DataTableColumn;
use App\Product;
use App\PushBrandsLog;
use App\ReconsileBrandsLog;
use App\StoreWebsite;
use App\StoreWebsiteBrand;
use App\StoreWebsiteBrandHistory;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use seo2websites\MagentoHelper\MagentoHelper;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  mixed  $id
     */
    public function index(Request $request, $id)
    {
        $title = 'Attached Brand | Store Website';

        if ($request->ajax()) {
            // send response into the json
            $brands = Brand::getBrands()->pluck('name', 'id')->toArray();

            $storeWebsite = StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                ->where('store_website_id', $id)
                ->select(['store_website_brands.*', 'b.name'])
                ->get();

            return response()->json([
                'code' => 200,
                'store_website_id' => $id,
                'data' => $storeWebsite,
                'brands' => $brands,
            ]);
        }

        return view('storewebsite::index', compact('title'));
    }

    /**
     * store cateogories
     */
    public function store(Request $request): JsonResponse
    {
        $storeWebsiteId = $request->get('store_website_id');
        $post = $request->all();

        $validator = Validator::make($post, [
            'store_website_id' => 'required',
            'markup' => 'required',
            'brand_id' => 'unique:store_website_brands,brand_id,NULL,id,store_website_id,'.$storeWebsiteId.'|required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsiteBrand = new StoreWebsiteBrand;
        $storeWebsiteBrand->fill($post);
        $storeWebsiteBrand->save();

        return response()->json(['code' => 200, 'data' => $storeWebsiteBrand]);
    }

    public function delete(Request $request, $id, $store_brand_id): JsonResponse
    {
        $storeBrand = StoreWebsiteBrand::where('store_website_id', $id)->where('id', $store_brand_id)->first();
        if ($storeBrand) {
            $storeBrand->delete();
        }

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function createPushBrandsLog($request, $error_type, $error): JsonResponse
    {
        try {
            $recLog = new PushBrandsLog;
            $recLog->store_webite_id = $request->store_website_id;
            $recLog->user_id = (auth()->user()) ? auth()->user()->id : 6;
            $recLog->error_type = $error_type;
            $recLog->error = $error;
            $recLog->save();
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function list(Request $request)
    {
        $title = 'Store Brand';

        if ($request->get('push') == 1) {
            // brand push changes

            $brands = Product::join('brands as b', 'b.id', 'products.brand')->groupBy('b.id')->select(['b.*'])->get();

            $webLimit = explode(',', $request->get('store_website_id'));

            $storeWebsites = StoreWebsite::whereIn('id', $webLimit)->where('api_token', '!=', '')->where('website_source', 'magento')->get();

            if (! $brands->isEmpty()) {
                foreach ($brands as $brand) {
                    if (! $storeWebsites->isEmpty()) {
                        $this->createPushBrandsLog($request, 'Website', 'Website id is  found ID is : '.$request->get('store_website_id'));
                        foreach ($storeWebsites as $storeWeb) {
                            $magentoBrandId = MagentoHelper::addBrand($brand, $storeWeb);
                            if (! empty($magentoBrandId)) {
                                $brandStore = StoreWebsiteBrand::where('brand_id', $brand->id)->where('store_website_id', $storeWeb->id)->first();
                                if (! $brandStore) {
                                    $brandStore = new StoreWebsiteBrand;
                                    $brandStore->brand_id = $brand->id;
                                    $brandStore->store_website_id = $storeWeb->id;
                                }
                                $brandStore->magento_value = $magentoBrandId;
                                $brandStore->save();
                            } else {
                                $this->createPushBrandsLog($request, 'magentoBrand adding Error', "magentoBrandId is not found fir brand : '.$brand.'  website : ".$storeWeb);
                            }
                        }
                    } else {
                        $this->createPushBrandsLog($request, 'Website Error', 'Website id is not found ID is : '.$request->get('store_website_id'));
                    }
                }
            } else {
                $this->createPushBrandsLog($request, 'Brands Error', 'Brands id i not found');
            }

            return redirect()->back()->with('success', 'Brand Request finished successfully');
        }
        $storeWebsite = StoreWebsite::pluck('title', 'id')->toArray();

        $query = Brand::leftJoin('products', 'products.brand', '=', 'brands.id')->groupBy('brands.id')->select('brands.*', DB::raw('count(products.id) as counts'));

        // $query = $query->whereNull("brands.deleted_at");

        if ($request->keyword != null) {
            $query->where('brands.name', 'like', '%'.$request->keyword.'%');
        }

        if ($request->category_id != null) {
            $query->where('products.category', $request->category_id);
        }

        if ($request->has('no-inventory')) {
            $query->having('counts', '=', 0);
        } else {
            $query->having('counts', '>', 0);
        }

        $query->orderBy('brands.name');

        // print_r($brands->toArray());
        // exit;

        $appliedQ = StoreWebsiteBrand::all();

        $apppliedResult = [];
        $apppliedResultCount = [];

        if (! $appliedQ->isEmpty()) {
            foreach ($appliedQ as $raw) {
                $apppliedResult[$raw->brand_id][] = $raw->store_website_id;
                $apppliedResultCount[$raw->store_website_id][] = $raw->brand_id;
            }
        }
        $brandsCount = $query->get()->toArray();
        $brandsCountIds = [];
        foreach ($brandsCount as $brandCount) {
            array_push($brandsCountIds, $brandCount['id']);
        }
        foreach ($apppliedResultCount as $k => $v) {
            $diff = array_diff($v, $brandsCountIds);
            $result = (array_diff($v, $diff));
            $apppliedResultCount[$k] = $result;
        }

        $limit = 30;

        if ($request->ajax() && $request->pagination == null) {
            $brands = $query->limit($limit)->offset(($request->page - 1) * $limit)->get();

            return response()->json([
                'tbody' => view('storewebsite::brand.partials.brand_data', compact('brands', 'storeWebsite', 'apppliedResult', 'apppliedResultCount'))->render(),
                'count' => count($brands),
            ], 200);
        } else {
            $brands = $query->limit($limit)->get();
        }
        $categories = Category::join('products', 'products.category', '=', 'categories.id')->orderBy('categories.title')->pluck('categories.title', 'categories.id');

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'store-website_brand')->first();

        $dynamicColumnsToShow = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShow = json_decode($hideColumns, true);
        }

        return view('storewebsite::brand.index', compact(['title', 'brands', 'storeWebsite', 'apppliedResult', 'categories', 'apppliedResultCount', 'dynamicColumnsToShow']));
    }

    public function pushBrandsLog(Request $request): JsonResponse
    {
        try {
            $data = PushBrandsLog::select('push_brands_logs.*', 'sw.website AS websiteName')->leftJoin('store_websites as sw', 'sw.id', 'push_brands_logs.store_website_id')->get();

            return response()->json(['code' => 200, 'data' => $data, 'message' => 'Push brand log listed successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function pushToStore(Request $request)
    {
        $user = auth()->user();
        if ($request->brand != null && $request->store != null) {
            try {
                $brandStore = StoreWebsiteBrand::where('brand_id', $request->brand)->where('store_website_id', $request->store)->first();
                $websites = StoreWebsite::where('parent_id', '=', $request->store)->orWhere('id', '=', $request->store)->get();
                if (count($websites) > 0) {
                    if (class_exists('\\seo2websites\\MagentoHelper\\MagentoHelper')) {
                        $response = null;
                        $brand = Brand::find($request->brand);
                        $storeWebsites = [];
                        foreach ($websites as $key => $website) {
                            $storeWebsites[] = $website->id;
                            if (! $brandStore) {
                                $magentoBrandId = MagentoHelper::addBrand($brand, $website);
                                if ($magentoBrandId) {
                                    $brandStore = new StoreWebsiteBrand;
                                    $brandStore->brand_id = $request->brand;
                                    if (isset($magentoBrandId)) {
                                        $brandStore->magento_value = $magentoBrandId;
                                    }
                                    $brandStore->store_website_id = $website->id;
                                    $brandStore->save();
                                    $message = "{$website->title} assigned to {$brand->name} brand.";
                                    $this->createWebsiteBrandHistory($request->brand, $website->id, 'assign', $user->id, $message);
                                    $response = response()->json(['code' => 200, 'message' => 'Brand is pushed to store successfully.', 'storeWebsites' => $storeWebsites]);
                                } else {
                                    $message = "{$website->title} assigned to {$brand->name} brand failed.";
                                    $this->createWebsiteBrandHistory($request->brand, $website->id, 'error', $user->id, $message);

                                    return response()->json(['code' => 500, 'message' => 'Brand is not pushed to store,please check history log.']);
                                }
                            } else {
                                $bID = $brandStore->magento_value;
                                if ($bID) {
                                    $status = MagentoHelper::deleteBrand($bID, $website);
                                    if ($status) {
                                        $brandStore->delete();
                                        $message = "{$brand->name} removed from {$website->title} store.";
                                        $this->createWebsiteBrandHistory($request->brand, $website->id, 'remove', $user->id, $message);
                                        $response = response()->json(['code' => 200, 'message' => 'Brand is removed from store successfully.', 'storeWebsites' => $storeWebsites]);
                                    } else {
                                        $message = "{$brand->name} is not removed from {$website->title} store.";
                                        $this->createWebsiteBrandHistory($request->brand, $website->id, 'error', $user->id, $message);

                                        return response()->json(['code' => 500, 'message' => 'Brand is not removed from store,please check history log.']);
                                    }
                                }
                            }
                        }
                        if ($key + 1 == (count($websites))) {
                            return $response;
                        }
                    } else {
                        return response()->json(['code' => 500, 'message' => 'MagentoHelper class not found!']);
                    }
                } else {
                    return response()->json(['code' => 500, 'message' => 'No store websites found!']);
                }
            } catch (Exception $e) {
                $this->createWebsiteBrandHistory($request->brand, $request->store, 'error', $user->id, $e->getMessage());

                return response()->json(['code' => 500, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['code' => 500, 'message' => 'Store and brand not found']);
    }

    public function createWebsiteBrandHistory($brand_id, $store_website_id, $type, $created_by, $message)
    {
        StoreWebsiteBrandHistory::create([
            'brand_id' => $brand_id,
            'store_website_id' => $store_website_id,
            'type' => $type,
            'created_by' => $created_by,
            'message' => $message,
        ]);
    }

    /**
     * run artisan command
     */
    public function refreshMinMaxPrice(): JsonResponse
    {
        try {
            Artisan::call('brand:maxminprice');

            return response()->json('Console Commnad Ran', 200);
        } catch (Exception $e) {
            return response()->json('Cannot call artisan command', 200);
        }
    }

    public function history(Request $request)
    {
        if ($request->brand != null && $request->store != null) {
            $StoreWebsiteBrandHistories = StoreWebsiteBrandHistory::leftJoin('users as u', 'u.id', 'store_website_brand_histories.created_by')
                ->where('brand_id', $request->brand)
                ->where('store_website_id', $request->store)
                ->select(['store_website_brand_histories.*', 'u.name as user_name'])
                ->get();

            return view('storewebsite::brand.history', compact(['StoreWebsiteBrandHistories']));
        }

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function liveBrands(Request $request): View
    {
        $heading = 'Live Brands';
        $storeWebsite = StoreWebsite::find($request->store_website_id);
        if ($storeWebsite && $storeWebsite->magento_url) {
            $client = new Client;
            $response = $client->request('GET', $storeWebsite->magento_url.'/rest/V1/brands/list', [
                'form_params' => [

                ],
            ]);
            $brands = (string) $response->getBody()->getContents();
            $brands = json_decode($brands, true);
            $mangetoIds = [];

            if (! empty($brands)) {
                foreach ($brands as $brand) {
                    $mangetoIds[] = $brand['attribute_id'];
                }
            }

            $availableBrands = StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                ->where('store_website_id', $storeWebsite->id)
                ->whereIn('magento_value', $mangetoIds)
                ->groupBy('store_website_brands.magento_value')
                ->select(['b.*'])
                ->get();
            //$missingBrands   = StoreWebsiteBrand::join("brands as b","b.id","store_website_brands.brand_id")->whereNotIn("magento_value",$mangetoIds)->get();

            $total = $availableBrands->count();

            return view('storewebsite::brand.live-compare', compact('availableBrands', 'total', 'heading'));
        } else {
            throw new Exception('Magento URL missing');
        }
    }

    public function missingBrands(Request $request): View
    {
        $heading = 'Missing Brands';
        $storeWebsite = StoreWebsite::find($request->store_website_id);
        if ($storeWebsite && $storeWebsite->magento_url) {
            $client = new Client;
            $response = $client->request('GET', $storeWebsite->magento_url.'/rest/V1/brands/list', [
                'form_params' => [

                ],
            ]);
            $brands = (string) $response->getBody()->getContents();
            $brands = json_decode($brands, true);
            $mangetoIds = [];

            if (! empty($brands)) {
                foreach ($brands as $brand) {
                    $mangetoIds[] = $brand['attribute_id'];
                }
            }

            $availableBrands = StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                ->where('store_website_id', $storeWebsite->id)
                ->whereNotIn('magento_value', $mangetoIds)
                ->groupBy('store_website_brands.magento_value')
                ->select(['b.*'])
                ->get();

            $total = $availableBrands->count();

            return view('storewebsite::brand.live-compare', compact('availableBrands', 'heading'));
        } else {
            throw new Exception('Magento URL missing');
        }
    }

    public function reconsileBrandsLog($request, $error_type, $error): JsonResponse
    {
        try {
            $recLog = new ReconsileBrandsLog;
            $recLog->store_webite_id = $request->store_website_id;
            $recLog->user_id = (auth()->user()) ? auth()->user()->id : 6;
            $recLog->error_type = $error_type;
            $recLog->error = $error;
            $recLog->save();
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function reconsileBrands(Request $request): JsonResponse
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            $storeWebsite = StoreWebsite::find($request->store_website_id);
            if ($storeWebsite) {
                $client = new Client;
                $response = $client->request('GET', $storeWebsite->magento_url.'/rest/V1/brands/list', [
                    'form_params' => [
                    ],
                ]);

                $brands = (string) $response->getBody()->getContents();
                $brands = json_decode($brands, true);
                $mangetoIds = [];
                if (! empty($brands)) {
                    foreach ($brands as $brand) {
                        $mangetoIds[] = $brand['attribute_id'];
                    }
                } else {
                    $this->reconsileBrandsLog($request, 'Brand error', 'brand id not found');
                }

                $rightBrand = StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                    ->where('store_website_id', $storeWebsite->id)
                    ->where('magento_value', '>', 0)
                    ->groupBy('store_website_brands.magento_value')
                    ->select(['store_website_brands.*', 'b.deleted_at'])
                    ->get();

                $assingedBrands = [];
                $noneedTodelete = [];
                if (! $rightBrand->isEmpty()) {
                    foreach ($rightBrand as $rb) {
                        //if(is_null($rb->deleted_at)) {
                        $noneedTodelete[] = $rb->magento_value;
                        //}
                        if ($rb->magento_value > 0) {
                            $assingedBrands[$rb->magento_value] = $rb;
                        }
                    }
                } else {
                    $this->reconsileBrandsLog($request, 'magento_value error', 'magento_value id not found');
                }

                $needDeleteRequest = [];
                if (! empty($mangetoIds)) {
                    foreach ($mangetoIds as $nrei) {
                        if (! in_array($nrei, $noneedTodelete)) {
                            $needDeleteRequest[] = $nrei;
                        }
                    }
                }

                Log::info(print_r(['Delete request IDS', $needDeleteRequest], true));

                // go for delete brands
                $userId = (auth()->user()) ? auth()->user()->id : 6;
                if (! empty($needDeleteRequest)) {
                    foreach ($needDeleteRequest as $ndr) {
                        Log::info('Request started for '.$ndr);
                        try {
                            Log::info('Brand started for delete '.$ndr);
                            $status = MagentoHelper::deleteBrand($ndr, $storeWebsite);
                        } catch (Exception $e) {
                            $this->reconsileBrandsLog($request, 'Delete brand', $e->getMessage());
                            Log::info("Brand delete has error with id $ndr =>".$e->getMessage());
                        }
                        Log::info('Brand check for delete '.$ndr);
                        if (isset($assingedBrands[$ndr])) {
                            Log::info('Brand find for delete '.$ndr);
                            $brandStore = $assingedBrands[$ndr];
                            $brandStore->delete();
                            StoreWebsiteBrandHistory::create([
                                'brand_id' => $brandStore->brand_id,
                                'store_website_id' => $brandStore->store_website_id,
                                'type' => 'remove',
                                'created_by' => $userId,
                                'message' => "{$brandStore->name} removed from {$storeWebsite->title} store.",
                            ]);
                        } else {
                            $this->reconsileBrandsLog($request, 'Delete brand', 'Brand check for delete '.$ndr);
                        }
                    }
                } else {
                    $this->reconsileBrandsLog($request, 'Delete brand', 'Delete Brand not found');
                }

                // this we need to push
                $availableBrands = StoreWebsiteBrand::join('brands as b', 'b.id', 'store_website_brands.brand_id')
                    ->where('store_website_id', $storeWebsite->id)
                    ->whereNotIn('magento_value', $mangetoIds)
                    ->groupBy('store_website_brands.magento_value')
                    ->select(['b.*'])
                    ->get();

                if (! $availableBrands->isEmpty()) {
                    foreach ($availableBrands as $avb) {
                        $magentoBrandId = MagentoHelper::addBrand($avb, $storeWebsite);
                        if (! empty($magentoBrandId)) {
                            $brandStore = StoreWebsiteBrand::where('brand_id', $avb->id)->where('store_website_id', $storeWebsite->id)->first();
                            if (! $brandStore) {
                                $brandStore = new StoreWebsiteBrand;
                                $brandStore->brand_id = $avb->id;
                                $brandStore->store_website_id = $storeWebsite->id;
                            }
                            $brandStore->magento_value = $magentoBrandId;
                            $brandStore->save();
                        }
                    }
                }
            }
            $this->reconsileBrandsLog($request, 'Reconsile Successfully', 'Reconsile request has been finished successfully');

            return response()->json(['code' => 200, 'message' => 'Reconsile request has been finished successfully']);
        } catch (Exception $e) {
            $this->reconsileBrandsLog($request, 'Catch Error', $e->getMessage());

            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function reconsileBrandsHistoryLog(Request $request): JsonResponse
    {
        try {
            $data = ReconsileBrandsLog::select('reconsile_brands_log.*', 'sw.website AS websiteName')->leftJoin('store_websites as sw', 'sw.id', 'reconsile_brands_log.store_website_id')->get();

            return response()->json(['code' => 200, 'data' => $data, 'message' => 'Reconsile log listed successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function columnVisbilityUpdate(Request $request): JsonResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'store-website_brand')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'store-website_brand';
            $column->column_name = json_encode($request->columns);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'store-website_brand';
            $column->column_name = json_encode($request->columns);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return response()->json([
            'status' => true,
            'message' => ' column visiblity Added Successfully',
            'status_name' => 'success',
        ], 200);
    }
}
