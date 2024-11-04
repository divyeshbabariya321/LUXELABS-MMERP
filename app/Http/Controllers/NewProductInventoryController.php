<?php

namespace App\Http\Controllers;
use App\LandingPageProduct;
use App\Http\Controllers\ScrapController;
use App\Helpers\StatusHelper;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Stage;
use App\Product;
use App\Category;
use App\UpteamLog;
use App\ColorReference;
use Illuminate\Http\Request;
use App\Library\Product\ProductSearch;
use App\Services\Scrap\GoogleImageScraper;
use Carbon\Carbon;
use App\Supplier;
use App\Scraper;
use Illuminate\Support\Facades\DB;

class NewProductInventoryController extends Controller
{
    public function __construct(private GoogleImageScraper $googleImageScraper)
    {
    }

    public function index(Stage $stage): View
    {

        if (!request()->ajax()) {
            $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])->selected(request('category'))->renderAsDropdown();
            // $suppliersDropList  = \Illuminate\Support\Facades\DB::select('SELECT id, supplier FROM suppliers INNER JOIN (
            //                             SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
            //                             ) as product_suppliers
            //                         ON suppliers.id = product_suppliers.supplier_id');
            // $suppliersDropList = collect($suppliersDropList)->pluck('supplier', 'id')->toArray();

            $suppliersDropList = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->Join('product_suppliers', 'suppliers.id', '=', 'product_suppliers.supplier_id')
            ->groupBy('suppliers.id')
            ->pluck('supplier', 'id');

            // $scrapperDropList  = \Illuminate\Support\Facades\DB::select('SELECT id, scraper_name FROM scrapers INNER JOIN (
            //     SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
            //     ) as product_suppliers
            // ON scrapers.supplier_id = product_suppliers.supplier_id');

            // $scrapperDropList = collect($scrapperDropList)->pluck('scraper_name', 'id')->toArray();
            $scrapperDropList = Scraper::select('scrapers.id', 'scrapers.scraper_name')
                ->leftJoin('product_suppliers', 'scrapers.supplier_id', '=', 'product_suppliers.supplier_id')
                ->groupBy('scrapers.id')
                ->pluck('scraper_name', 'id');
            $typeList         = [
                'scraped'  => 'Scraped',
                'imported' => 'Imported',
                'uploaded' => 'Uploaded',
            ];
        }

        $params = request()->all();

        $productsQuery = (new ProductSearch($params))
            ->getQuery();
        $productCount = $productsQuery->count();

        $products = $productsQuery->with('scraped_products')->paginate(24);

        // $productCount = (new ProductSearch($params))->getQuery()->count();
        $items        = [];
        // foreach ($products->items() as $product) {
        //     $date               = date('Y-m-d', strtotime($product->created_at));
        //     $referencesCategory = '';
        //     $referencesColor    = '';
        //     if (isset($product->scraped_products)) {
        //         // starting to see that howmany category we going to update
        //         if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['category']) != null) {
        //             $category = $product->scraped_products->properties['category'];
        //             if (is_array($category)) {
        //                 $referencesCategory = implode(' > ', $category);
        //             }
        //         }

        //         if (isset($product->scraped_products->properties) && isset($product->scraped_products->properties['color']) != null) {
        //             $referencesColor = $product->scraped_products->properties['color'];
        //         }
        //     }
        //     $product->reference_category = $referencesCategory;
        //     $product->reference_color    = $referencesColor;

        //     $supplier_list = '';
        //     foreach ($product->suppliers as $key => $supplier) {
        //         $supplier_list .= $supplier->supplier;
        //     }

        //     $product->supplier_list = $supplier_list;

        //     if (isset($items[$date])) {
        //         $items[$date][] = $product;
        //     } else {
        //         $items[$date] = [$product];
        //     }
        // }
        $products->each(function ($product) use (&$items) {
            // Extract date using Carbon
            $date = Carbon::parse($product->created_at)->format('Y-m-d');
        
            // Extract references
            $referencesCategory = $product->scraped_products->properties['category'] ?? '';
            $referencesCategory = is_array($referencesCategory) ? implode(' > ', $referencesCategory) : '';
        
            $referencesColor = $product->scraped_products->properties['color'] ?? '';
        
            // Combine supplier names into a single string
            $supplier_list = implode('', $product->suppliers->pluck('supplier')->toArray());
        
            // Assign references and supplier list to product properties
            $product->reference_category = $referencesCategory;
            $product->reference_color = $referencesColor;
            $product->supplier_list = $supplier_list;
        
            // Group products by date
            if (!isset($items[$date])) {
                $items[$date] = [];
            }
        
            $items[$date][] = $product;
        });
        // move to the function
        $categoryAll = Category::with('childs.childLevelSencond')
            ->where('title', 'NOT LIKE', '%Unknown Category%')
            ->where('magento_id', '!=', '0')
            ->get();

        $categoryArray = [];
        $categoryAll->each(function ($category) use (&$categoryArray) {
            $categoryArray[] = ['id' => $category->id, 'value' => $category->title];
        
            // Loop through child categories
            $category->childs->each(function ($child) use ($category, &$categoryArray) {
                $categoryArray[] = ['id' => $child->id, 'value' => $category->title . ' > ' . $child->title];
        
                // Loop through grandchild categories
                if ($child->childLevelSencond != null) {
                    $child->childLevelSencond->each(function ($grandChild) use ($category, $child, &$categoryArray) {
                        $categoryArray[] = ['id' => $grandChild->id, 'value' => $category->title . ' > ' . $child->title . ' > ' . $grandChild->title];
                    });
                }
            });
        });
        $categoryArray = collect($categoryArray)->pluck('value', 'id')->toArray();

        $sampleColors  = ColorReference::select('erp_color')->groupBy('erp_color')->pluck('erp_color', 'erp_color');
        if (request()->ajax()) {
            return view('product-inventory.partials.load-more', compact('products', 'productCount', 'items', 'categoryArray', 'sampleColors'));
        }

        $attach_image_tag = config('constants.attach_image_tag');
        return view('product-inventory.index', compact('category_selection', 'productCount', 'suppliersDropList', 'typeList', 'products', 'items', 'categoryArray', 'sampleColors', 'scrapperDropList', 'attach_image_tag'));
    }

    public function autoSuggestSku(Request $request): JsonResponse
    {
        $term = $request->input('term');

        // Adjusted the 'like' clause to match terms that start with the provided input
        $autosuggestions = Product::where('sku', 'like', $term . '%')->paginate(10)->pluck('sku');

        return response()->json($autosuggestions);
    }

    public function upteamLogs(Request $request): View
    {
        if (($request->upteam_log && $request->upteam_log != null) && ($request->from_date != '' && $request->to_date != '')) {
            $logs = UpteamLog::where('log_description', 'LIKE', '%' . $request->upteam_log . '%')->whereBetween('created_at', [$request->from_date, $request->to_date])->orderByDesc('id')->paginate(30);
        } elseif ($request->upteam_log && $request->upteam_log != '') {
            $logs = UpteamLog::where('log_description', 'LIKE', '%' . $request->upteam_log . '%')->orderByDesc('id')->paginate(30);
        } elseif ($request->from_date != '' && $request->to_date != '') {
            $logs = UpteamLog::whereBetween('created_at', [$request->from_date, $request->to_date])->orderByDesc('id')->paginate(30);
        } else {
            $logs = UpteamLog::orderByDesc('id')->paginate(30);
        }

        return view('product-inventory.upteam_logs', compact('logs'));
    }

    public function pushInStore(Request $request): JsonResponse
    {
        if (! empty($request->product_ids)) {
            if (is_array($request->product_ids)) {
                foreach ($request->product_ids as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        // check status if not cropped then send to the cropper first
                        if ($product->status_id != StatusHelper::$finalApproval) {
                            $product->scrap_priority = 1;
                        } else {
                            $product->scrap_priority = 0;
                        }
                        // save product
                        $product->save();
                        LandingPageProduct::updateOrCreate(
                            ['product_id' => $productId],
                            ['product_id' => $productId, 'name' => $product->name, 'description' => $product->description, 'price' => $product->price]
                        );
                    }
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Product updated Successfully']);
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'No product ids found']);
    }

    public function fetchImgGoogle(Request $request): RedirectResponse
    {
        if (empty($request->get('name'))) {
            return redirect()->back()->with('error', 'Product name is required');
        }

        $q  = $request->get('name');
        $id = $request->get('id');

        $googleData = $this->googleImageScraper->scrapGoogleImages($q, 'lifestyle', 10);

        if ($googleData) {
            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add([
                'data'       => $googleData,
                'product_id' => $id,
            ]);
            app(ScrapController::class)->downloadImages($requestData);

            return redirect()->back()->with('message', 'Images has been saved on lifestyle grid');
        }

        return redirect()->back()->with('error', 'No any images found on google');
    }
}
