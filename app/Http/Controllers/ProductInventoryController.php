<?php

namespace App\Http\Controllers;

use App\Brand;
use App\c;
use App\Category;
use App\CategorySegment;
use App\CategorySegmentDiscount;
use App\ChatMessage;
use App\ColorReference;
use App\Colors;
use App\Courier;
use App\Customer;
use App\Exports\ReportExport;
use App\Helpers;
use App\Helpers\OrderHelper;
use App\Helpers\ProductHelper;
use App\Helpers\StatusHelper;
use App\Http\Requests\ExportExcelProductInventoryRequest;
use App\Http\Requests\ImportProductInventoryRequest;
use App\Http\Requests\MappingExcelProductInventoryRequest;
use App\Http\Requests\StockProductInventoryRequest;
use App\Imports\InventoryImport;
use App\Instruction;
use App\InventoryHistory;
use App\InventoryStatusHistory;
use App\InventoryStatusHistoryView;
use App\Jobs\UpdateFromSizeManager;
use App\Mediables;
use App\Models\DataTableColumn;
use App\Models\ScrapedProductMissingLog;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ProductDiscountExcelFile;
use App\ProductDispatch;
use App\ProductLocation;
use App\ProductLocationHistory;
use App\ProductSizes;
use App\ProductSupplier;
use App\RejectedImages;
use App\ReplyCategory;
use App\ScrapedProducts;
use App\Setting;
use App\Stage;
use App\Supplier;
use App\SupplierBrandDiscount;
use App\SupplierDiscountLogHistory;
use App\SystemSize;
use App\SystemSizeManager;
use App\SystemSizeRelation;
use App\User;
use Carbon\Carbon;
use DataTables;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductInventoryController extends Controller
{
    public function index(Stage $stage): View
    {
        $products = Product::latest()
            ->where('stock', '>=', 1)
            ->whereNull('dnf')
            ->select(['id', 'name', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at', 'category', 'color']);

        $products_count = $products->count();
        $products = $products->paginate(Setting::get('pagination'));

        $roletype = 'Inventory';

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control'])
            ->selected(1)
            ->renderAsDropdown();

        $categoryAll = Category::where('parent_id', 0)->get();
        foreach ($categoryAll as $category) {
            $categoryArray[] = ['id' => $category->id, 'value' => $category->title];
            $childs = Category::where('parent_id', $category->id)->get();
            foreach ($childs as $child) {
                $categoryArray[] = ['id' => $child->id, 'value' => $category->title.' '.$child->title];
                $grandChilds = Category::where('parent_id', $child->id)->get();
                if ($grandChilds != null) {
                    foreach ($grandChilds as $grandChild) {
                        $categoryArray[] = ['id' => $grandChild->id, 'value' => $category->title.' '.$child->title.' '.$grandChild->title];
                    }
                }
            }
        }

        $sampleColors = ColorReference::select('erp_color')->groupBy('erp_color')->get();

        $categoryArray = [];

        $attach_image_tag = config('constants.attach_image_tag');

        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors)->all();
        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'products_count', 'roletype', 'category_selection', 'categoryArray', 'sampleColors', 'attach_image_tag', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    // Function to flatten the 3-level array into a 2-level array
    public function flattenCategories($array, &$result = [])
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = 0;
                // Recursive call to flatten the next level
                $this->flattenCategories($value, $result);
            } else {
                // Assign the value to the flattened array
                $result[$key] = $value;
            }
        }
    }

    public function list(Request $request, Stage $stage)
    {
        $category_tree = [];

        foreach (Category::all() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent->parent_id != 0) {
                    if (isset($category_tree[$parent->parent_id][$parent->id]) && is_array($category_tree[$parent->parent_id][$parent->id])) {
                        // Make sure the third level exists before assignment
                        $category_tree[$parent->parent_id][$parent->id][$category->id] = 0; // Change 0 to the desired value
                    } else {
                        // If the third level doesn't exist, initialize it as an empty array before assignment
                        $category_tree[$parent->parent_id][$parent->id] = [$category->id => 0]; // Change 0 to the desired value
                    }
                } else {
                    $category_tree[$parent->id][$category->id] = 0;
                }
            }
        }

        // Flatten the $category_tree array into a 2-level array
        // Loop through each category and flatten its subcategories
        foreach ($category_tree as $category_id => $subcategories) {
            // Create a temporary array to store the flattened subcategories
            $flattened_subcategories = [];
            $this->flattenCategories($subcategories, $flattened_subcategories);

            // Replace the first level keys with the flattened subcategories
            $category_tree[$category_id] = $flattened_subcategories;
        }

        $brands_array = Brand::getAll();
        $products_brands = Product::latest()
            ->where('stage', '>=', $stage->get('Approver'))
            ->whereNull('dnf')
            ->where('stock', '>=', 1)->get()
            ->groupBy([function ($query) use ($brands_array) {
                if (isset($brands_array[$query->brand])) {
                    return $brands_array[$query->brand];
                }

                return 'Unknown Brand';
            }, 'supplier', 'category']);

        $inventory_data = [];

        foreach ($products_brands as $brand_name => $suppliers) {
            foreach ($suppliers as $supplier_name => $categories) {
                $inventory_data[$brand_name][$supplier_name] = $category_tree;

                foreach ($categories as $category_id => $products) {
                    $category = Category::find($category_id);
                    if ($category !== null && $category->parent_id != 0) {
                        $parent = $category->parent;
                        if (isset($parent->parent_id) && $parent->parent_id != 0) {
                            @$inventory_data[$brand_name][$supplier_name][$parent->parent_id][$parent->id] += count($products);
                        } else {
                            @$inventory_data[$brand_name][$supplier_name][$parent->id][$category->id] += count($products);
                        }
                    }
                }
            }
        }

        $categories_array = [];
        $categories = Category::all();

        foreach ($categories as $category) {
            $categories_array[$category->id] = $category->title;
        }

        return view('products.list', compact('inventory_data', 'categories_array'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function edit(Product $productlister): RedirectResponse
    {
        return redirect()->route('products.show', $productlister->id);
    }

    public function stock(Product $product, StockProductInventoryRequest $request, Stage $stage): RedirectResponse
    {

        $result = $this->magentoSoapUpdateStock($product, $request->input('stock'));
        $product->stock = $request->input('stock');
        $product->stage = $stage->get('Inventory');
        $product->save();

        if ($result) {
            ActivityConroller::create($product->id, 'inventory', 'create');

            return redirect()->back()->with('success', 'Product inventory has been updated');
        }

        return redirect()->back()->with('error', 'Error Occured while uploading stock');
    }

    public function instock(Request $request): View|BinaryFileResponse
    {
        $data = [];
        $term = $request->input('term');
        $data['term'] = $term;

        $productQuery = Product::latest()->with(['brands', 'product_category']);

        if (isset($request->brand) && $request->brand[0] != null) {
            $productQuery = $productQuery->whereIn('brand', $request->brand);
            $data['brand'] = $request->brand[0];
        }
        if (isset($request->color) && is_array($request->color) && $request->color[0] != null) {
            $productQuery = $productQuery->whereIn('color', $request->color);
            $data['color'] = $request->color;
        }

        if (! empty($request->category) && $request->category[0] != 1) {
            $category = Category::with('childs.childLevelSencond')->find($request->category[0]);
            $category_children = [];
            if ($category->childs->count()) {
                $childs = $category->childs;
                foreach ($childs as $child) {
                    if ($child->childLevelSencond->count()) {
                        $grandChilds = $child->childLevelSencond;
                        foreach ($grandChilds as $grandChild) {
                            $category_children[] = $grandChild->id;
                        }
                    } else {
                        $category_children[] = $child->id;
                    }
                }
            } else {
                $category_children[] = $category->id;
            }
            $productQuery->whereIn('category', $category_children);
            $data['category'] = $request->category[0];
        }

        if (isset($request->location) && $request->location[0] != null) {
            $productQuery->whereIn('location', $request->location);

            $data['location'] = $request->location;
        }

        if (isset($request->no_locations) && $request->no_locations) {
            $productQuery->whereNull('location');

            $data['no_locations'] = true;
        }

        $productQuery->when(! empty($term), function ($e) use ($term) {
            $e->where(function ($q) use ($term) {
                $q->where('sku', 'LIKE', "%$term%")
                    ->orWhereHas('brands', function ($a) use ($term) {
                        $a->where('name', 'LIKE', "%$term%");
                    })->orwhereHas('product_category', function ($q) use ($term) {
                        $q->where('title', 'LIKE', "%$term%");
                    })
                    ->orWhere(function ($q) use ($term) {
                        $arr_id = Product::STOCK_STATUS;
                        $key = array_search(ucwords($term), $arr_id);
                        $q->where('stock_status', $key);
                    });
            });
        });

        $selected_brand = null;
        if ($request->brand) {
            $selected_brand = Brand::select('id', 'name')->whereIn('id', $request->brand)->get();
        }
        $data['selected_brand'] = $selected_brand;

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]', 'class' => 'form-control'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        $stockStatus = $request->get('stock_status', '');
        if (! empty($stockStatus)) {
            $productQuery->where('stock_status', $stockStatus);
        }

        if ($request->get('shoe_size', false)) {
            $productQuery->where('products.size', 'like', '%'.$request->get('shoe_size').'%');
        }

        $productQuery->where(function ($query) {
            $query->where('purchase_status', '!=', 'Delivered')->orWhereNull('purchase_status');
        });

        if ($request->get('in_pdf') === 'on') {
            $data['products'] = $productQuery->whereRaw("(products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 11) OR (location IS NOT NULL AND location != ''))")->get();
        } else {
            $data['products'] = $productQuery->whereRaw("(products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id = 11) OR (location IS NOT NULL AND location != ''))")->paginate(Setting::get('pagination'));
        }

        $data['date'] = $request->date ? $request->date : '';
        $data['type'] = $request->type ? $request->type : '';
        $data['customer_id'] = $request->customer_id ? $request->customer_id : '';
        $data['locations'] = (new ProductLocation)->pluck('name')->toArray() + ['In-Transit' => 'In-Transit'];

        $data['new_category_selection'] = Category::attr(['name' => 'category', 'class' => 'form-control', 'id' => 'product-category'])
            ->renderAsDropdown();

        $data['category_tree'] = [];
        $data['categories_array'] = [];

        foreach (Category::with('parent')->get() as $category) {
            if ($category->parent_id != 0) {
                $parent = $category->parent;
                if ($parent) {
                    if ($parent->parent_id != 0) {
                        @$data['category_tree'][$parent->parent_id][$parent->id][$category->id];
                    } else {
                        $data['category_tree'][$parent->id][$category->id] = $category->id;
                    }
                }
            }

            $data['categories_array'][$category->id] = $category->parent_id;
        }

        $data['media_tags'] = config('constants.media_tags');
        $data['attach_image_tag'] = config('constants.attach_image_tag');

        if ($request->get('in_pdf') === 'on') {
            set_time_limit(0);
            $html = view('instock.instock_pdf', $data);

            $pdf = new Dompdf;
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream('instock.pdf');

            // return;
        }

        return view('instock.index', $data);
    }

    public function inDelivered(Request $request)
    {
        $data = [];
        $term = $request->input('term');
        $data['term'] = $term;

        $productQuery = (new Product)->newQuery()->latest();
        if ($request->brand[0] != null) {
            $productQuery = $productQuery->whereIn('brand', $request->brand);
            $data['brand'] = $request->brand[0];
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

        if (trim($term) != '') {
            $productQuery = $productQuery->where(function ($query) use ($term) {
                $query->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('id', 'LIKE', "%$term%");
            });

            if ($term == -1) {
                $productQuery = $productQuery->where(function ($query) {
                    return $query->orWhere('isApproved', -1);
                });
            }

            if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                $productQuery = $productQuery->where(function ($query) use ($brand_id) {
                    return $query->orWhere('brand', 'LIKE', "%$brand_id%");
                });
            }

            $category = Category::where('title', 'LIKE', "%$term%")->first();
            if ($category) {
                $productQuery = $productQuery->where(function ($query) use ($term) {
                    return $query->orWhere('category', CategoryController::getCategoryIdByName($term));
                });
            }
        }

        $selected_categories = $request->category ? $request->category : 1;

        $data['category_selection'] = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])
            ->selected($selected_categories)
            ->renderAsDropdown();

        if ($request->get('shoe_size', false)) {
            $productQuery = $productQuery->where('products.size', 'like', '%'.$request->get('shoe_size').'%');
        }

        $data['products'] = $productQuery->where('products.purchase_status', '=', 'Delivered')->paginate(Setting::get('pagination'));
        $data['media_tags'] = config('constants.media_tags');

        return view('indelivered.index', $data);
    }

    public function magentoSoapUpdateStock($product, $stockQty)
    {
        $options = [
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
        ];
        $proxy = new \SoapClient(config('magentoapi.url'), $options);
        $sessionId = $proxy->login(config('
		api.user'), config('magentoapi.password'));

        $sku = $product->sku.$product->color;
        $result = false;

        if (! empty($product->size)) {
            $sizes_array = explode(',', $product->size);

            foreach ($sizes_array as $size) {
                $error_message = '';

                try {
                    $result = $proxy->catalogInventoryStockItemUpdate($sessionId, $sku.'-'.$size, [
                        'qty' => $stockQty,
                        'is_in_stock' => $stockQty ? 1 : 0,
                    ]);
                } catch (Exception $e) {
                    $error_message = $e->getMessage();
                }

                if ($error_message == 'Product not exists.') {
                    $product->isUploaded = 0;
                    $product->isFinal = 0;
                    $product->save();
                }
            }

            $error_message = '';
            try {
                $result = $proxy->catalogInventoryStockItemUpdate($sessionId, $sku, [
                    'is_in_stock' => $stockQty ? 1 : 0,
                ]);
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }

            if ($error_message == 'Product not exists.') {
                $product->isUploaded = 0;
                $product->isFinal = 0;
                $product->save();
            }
        } else {
            $error_message = '';

            try {
                $result = $proxy->catalogInventoryStockItemUpdate($sessionId, $sku, [
                    'qty' => $stockQty,
                    'is_in_stock' => $stockQty ? 1 : 0,
                ]);
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }

            if ($error_message == 'Product not exists.') {
                $product->isUploaded = 0;
                $product->isFinal = 0;
                $product->save();
            }
        }

        return $result;
    }

    public function import(ImportProductInventoryRequest $request): RedirectResponse
    {

        $array = (new InventoryImport)->toArray($request->file('file'));

        $new_array = [];
        $brands_array = Helpers::getUserArray(Brand::all());

        foreach ($array[0] as $key => $item) {
            $new_array[$item['modellovariante']][] = $item;
        }

        foreach ($new_array as $sku => $items) {
            $formatted_sku = str_replace(' ', '', $sku);

            if ($product = Product::where('sku', $formatted_sku)->first()) {
                if (in_array($items[0]['brand'], $brands_array)) {
                    if (count($items) > 1) {
                        $sizes = '';
                        $product->stock = 1;
                        $product->import_date = Carbon::now();
                        $product->status = 3; // Import Update status

                        foreach ($items as $key => $item) {
                            $size = str_replace('½', '.5', $item['taglia']);

                            if ($key == 0) {
                                $sizes .= $size;
                            } else {
                                $sizes .= ','.$size;
                            }
                        }

                        if (! preg_match('/UNI/', $sizes)) {
                            $product->size = $sizes;
                        }

                        $product->save();
                    } else {
                        $product->stock = 1;
                        $product->import_date = Carbon::now();
                        $product->status = 3; // Import Update status

                        foreach ($items as $key => $item) {
                            $size = str_replace('½', '.5', $item['taglia']);
                        }

                        if (! preg_match('/UNI/', $size)) {
                            $product->size = $size;
                        }

                        $product->save();
                    }
                }
            } else {
                if (in_array($items[0]['brand'], $brands_array)) {
                    if (count($items) > 1) {
                        $sizes = '';
                        $product = new Product;
                        $product->sku = $formatted_sku;
                        $product->brand = array_search($items[0]['brand'], $brands_array);
                        $product->stage = 3;
                        $product->stock = 1;
                        $product->import_date = Carbon::now();
                        $product->status = 2; // Import Create status

                        foreach ($items as $key => $item) {
                            $size = str_replace('½', '.5', $item['taglia']);

                            if ($key == 0) {
                                $sizes .= $size;
                            } else {
                                $sizes .= ','.$size;
                            }
                        }

                        if (! preg_match('/UNI/', $sizes)) {
                            $product->size = $sizes;
                        }

                        $product->save();
                    } else {
                        $product = new Product;
                        $product->sku = $formatted_sku;
                        $product->brand = array_search($items[0]['brand'], $brands_array);
                        $product->stage = 3;
                        $product->stock = 1;
                        $product->import_date = Carbon::now();
                        $product->status = 2; // Import Create status

                        foreach ($items as $key => $item) {
                            $size = str_replace('½', '.5', $item['taglia']);
                        }

                        if (! preg_match('/UNI/', $size)) {
                            $product->size = $sizes;
                        }

                        $product->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'You have successfully imported Inventory');
    }

    public function instructionCreate(): View
    {
        $productId = request()->get('product_id', 0);

        $users = User::all()->pluck('name', 'id');
        $product = Product::where('id', $productId)->first();

        $locations = ProductLocation::all()->pluck('name', 'name');
        $couriers = Courier::all()->pluck('name', 'name');
        $order = [];
        if ($product) {
            $order = OrderProduct::where('product_id', $product->id)
                ->join('orders as o', 'o.id', 'order_products.order_id')
                ->select(['o.id', DB::raw("concat(o.id,' => ',o.client_name) as client_name")])->pluck('client_name', 'id');
        }

        $reply_categories = ReplyCategory::whereHas('product_dispatch')->get();

        return view('instock.instruction_create', compact(['productId', 'users', 'order', 'locations', 'couriers', 'reply_categories']));
    }

    public function instruction(): JsonResponse
    {
        $params = request()->all();

        // validate incoming request

        $validator = Validator::make($params, [
            'product_id' => 'required',
            'location_name' => 'required',
            'instruction_type' => 'required',
            'instruction_message' => 'required',
            'courier_name' => 'required',
            'courier_details' => 'required',
            'date_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'errors' => $validator->messages()]);
        }

        // start to store first location as per the request

        $product = Product::where('id', $params['product_id'])->first();
        $instruction = new Instruction;

        if ($params['instruction_type'] == 'dispatch') {
            $orderId = request()->get('order_id', 0);
            if ($orderId > 0) {
                $order = Order::where('id', $params['order_id'])->first();
                if ($order) {
                    $instruction->customer_id = $order->customer_id;
                    $order->order_status = 'Delivered';
                    $order->order_status_id = OrderHelper::$delivered;
                    $order->save();
                }
            } else {
                $instruction->customer_id = request()->get('customer_id', 0);
            }

            $customer = ($instruction->customer) ? $instruction->customer->name : '';

            $assign_to = request()->get('assign_to', 0);

            if ($assign_to > 0) {
                $user = User::where('id', $assign_to)->first();
            }
            // if customer object found then send message
            if (! empty($user)) {
                $extraString = '';

                // check if any date time set
                if (! empty($params['date_time'])) {
                    $extraString = ' on '.$params['date_time'];
                }

                // set for pending amount
                if (! empty($params['pending_amount'])) {
                    $extraString .= ' and '.$params['pending_amount'].' to be collected';
                }
                // send message
                $messageData = implode("\n", [
                    "{$product->name} to be delivered to {$customer} {$extraString}",
                    $params['courier_name'],
                    $params['courier_details'],
                ]);

                $params['approved'] = 1;
                $params['message'] = $messageData;
                $params['status'] = 2;
                $params['user_id'] = $user->id;

                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $messageData);
                $chat_message = ChatMessage::create($params);
                if ($product->hasMedia(config('constants.media_tags'))) {
                    foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                        app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, null, getMediaUrl($image));
                        $chat_message->attachMedia($image, config('constants.media_tags'));
                    }
                }
            }
        } elseif ($params['instruction_type'] == 'location') {
            if ($product) {
                $product->location = 'In-Transit'; //$params["location_name"];
                $product->save();

                $params['location_name'] = 'In-Transit - '.$params['location_name'];

                $user = User::where('id', $params['assign_to'])->first();
                if ($user) {
                    // send location message
                    $pendingAmount = (! empty($params['pending_amount'])) ? ' and Pending amount : '.$params['pending_amount'] : '';
                    $messageData = implode("\n", [
                        "Pls. Despatch {$product->name} to ".$params['location_name'].$pendingAmount,
                        $params['instruction_message'],
                        $params['courier_name'],
                        $params['courier_details'],
                    ]);

                    $params['approved'] = 1;
                    $params['message'] = $messageData;
                    $params['status'] = 2;
                    $params['user_id'] = $user->id;

                    app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $messageData);
                    $chat_message = ChatMessage::create($params);
                    if ($product->hasMedia(config('constants.media_tags'))) {
                        foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, null, getMediaUrl($image));
                            $chat_message->attachMedia($image, config('constants.media_tags'));
                        }
                    }
                }
            }
        }

        $instruction->category_id = 7;
        $instruction->instruction = $params['instruction_message'];
        $instruction->assigned_from = Auth::user()->id;
        $instruction->assigned_to = $params['assign_to'];
        $instruction->product_id = $params['product_id'];
        $instruction->order_id = isset($params['order_id']) ? $params['order_id'] : null;
        $instruction->save();

        $productHistory = new ProductLocationHistory;

        $productHistory->fill($params);
        $productHistory->created_by = Auth::user()->id;
        $productHistory->instruction_message = $params['instruction_message'];
        $productHistory->save();

        return response()->json(['code' => 1, 'message' => 'Done']);
    }

    public function locationHistory(): View
    {
        $productId = request()->get('product_id', 0);
        $locations = (new ProductLocation)->pluck('name')->toArray();
        $product = Product::where('id', $productId)->First();
        $history = ProductLocationHistory::where('product_id', $productId)
            ->orderByDesc('date_time')
            ->get();

        return view('instock.history_list', compact(['history', 'locations', 'product']));
    }

    public function dispatchCreate(): View
    {
        $productId = request()->get('product_id', 0);

        return view('instock.dispatch_create', compact(['productId', 'users', 'order']));
    }

    public function dispatchStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'modeof_shipment' => 'required',
            'delivery_person' => 'required',
            'awb' => 'required',
            'eta' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'errors' => $validator->messages()]);
        }

        $productDispatch = new ProductDispatch;
        $productDispatch->fill($request->all());
        $productDispatch->save();

        $uploaded_images = [];

        if ($request->hasFile('file')) {
            try {
                foreach ($request->file('file') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('dispatch-images')->upload();
                    array_push($uploaded_images, $media);
                    $productDispatch->attachMedia($media, config('constants.media_tags'));
                }
            } catch (Exception $exception) {
                //
            }
        }

        if ($request->get('product_id') > 0) {

            $product = Product::where('id', $request->get('product_id'))->first();

            $product->purchase_status = 'Delivered';
            $product->location = null;
            $product->save();
            $instruction = Instruction::where('product_id', $request->get('product_id'))->where('customer_id', '>', '0')->orderByDesc('id')->first();
            if ($instruction) {
                $customer = Customer::where('id', $instruction->customer_id)->first();

                // if customer object found then send message
                if (! empty($customer)) {
                    $params = [];
                    $messageData = implode("\n", [
                        "We have Despatched your {$product->name} by {$productDispatch->delivery_person}",
                        "AWB : {$request->awb}",
                        "Mode Of Shipment  : {$request->modeof_shipment}",
                    ]);

                    $params['approved'] = 1;
                    $params['message'] = $messageData;
                    $params['status'] = 2;
                    $params['customer_id'] = $customer->id;

                    $chat_message = ChatMessage::create($params);

                    // if product has image then send message with image otherwise send with photo
                    if ($productDispatch->hasMedia(config('constants.media_tags'))) {
                        foreach ($productDispatch->getMedia(config('constants.media_tags')) as $image) {
                            $url = createProductTextImage($image->getAbsolutePath(), 'product-dispatch', $messageData, '000000', '15', false);
                            if (! empty($url)) {
                                app(WhatsAppController::class)->sendWithThirdApi($customer->phone, $customer->whatsapp_number, null, $url);
                            }
                            $chat_message->attachMedia($image, config('constants.media_tags'));
                        }
                    } else {
                        app(WhatsAppController::class)->sendWithThirdApi($customer->phone, $customer->whatsapp_number, $messageData);
                    }
                }
            }
        }

        return response()->json(['code' => 1, 'message' => 'Done']);
    }

    public function locationChange(Request $request): JsonResponse
    {
        $product = Product::where('id', $request->get('product_id', 0))->first();

        if ($product) {
            $product->location = $request->get('location', $product->location);
            $product->save();

            $productHistory = new ProductLocationHistory;
            $params = [

                'location_name' => $product->location,
                'product_id' => $product->id,
                'date_time' => date('Y-m-d H:i:s'),
            ];
            $productHistory->fill($params);
            $productHistory->created_by = Auth::user()->id;
            $productHistory->save();
        }

        return response()->json(['code' => 1, 'productHistory' => $productHistory, 'userName' => $productHistory->user->name]);
    }

    public function updateField(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $fieldName = $request->get('field_name', '');
        $fieldValue = $request->get('field_value', '');

        if ($id > 0 && ! empty($fieldValue) && ! empty($fieldName)) {
            $product = Product::where('id', $id)->first();
            if ($product) {
                $product->$fieldName = $fieldValue;
                $product->save();

                return response()->json(['code' => 200, 'message' => $fieldName.' updated successfully']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'Oops, Required field is missing']);
    }

    public function inventoryList(Request $request)
    {
        ini_set('memory_limit', -1);
        $filter_data = $request->input();
        $inventory_data = Product::getProducts($filter_data);

        // started to update status request
        if ($request->get('update_status', false) == true) {
            foreach ($inventory_data as $upd) {
                $nups = $request->get('status_id_update', 0);
                if ($nups) {
                    $upd->status_id = $nups;
                    if ($nups != StatusHelper::$requestForExternalScraper) {
                        $upd->sub_status_id = null;
                    }
                    $upd->save();
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Request has been updated successfully']);
        }
        // end for update request status

        $query = Product::selectRaw('
				   sum(CASE WHEN products.category = "" OR products.category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN products.color = "" OR products.color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN products.composition = "" OR products.composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN products.name = "" OR products.name IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN products.short_description = "" OR products.short_description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN products.price = "" OR products.price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN products.size = "" OR products.size IS NULL AND products.measurement_size_type IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       sum(CASE WHEN products.measurement_size_type = "" OR products.measurement_size_type AND products.size = "" OR products.size IS NULL THEN 1 ELSE 0 END) AS missing_measurement,
			       `products`.`supplier`
				')
            ->where('products.supplier', '<>', '');
        $query = $query->groupBy('products.supplier')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $reportData = $query->get();

        $scrapped_query = ScrapedProducts::selectRaw('
				   sum(CASE WHEN category = ""
			           OR category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN color = ""
			           OR color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN composition = ""
			           OR composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN title = ""
			           OR title IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN description = ""
			           OR description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN price = ""
			           OR price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN size = ""
			           OR size IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       supplier,
			       website
				')
            ->where('website', '<>', '');
        $scrapped_query = $scrapped_query->groupBy('website')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $scrappedReportData = $scrapped_query->get();
        $inventory_data_count = $inventory_data->total();

        $status_list = StatusHelper::getStatus();

        foreach ($inventory_data as $product) {
            $product['medias'] = Mediables::getMediasFromProductId($product['id']);
            $product_history = $product->productstatushistory;

            foreach ($product_history as $each) {
                $each['old_status'] = isset($status_list[$each['old_status']]) ? $status_list[$each['old_status']] : 0;
                $each['new_status'] = isset($status_list[$each['new_status']]) ? $status_list[$each['new_status']] : 0;
            }
            $product['status_history'] = $product_history;
        }

        //for filter

        $sku = [];
        $pname = [];
        $arr = cache()->remember('product', 60, function () {
            return Product::select('name', 'sku')->get();
        });
        foreach ($arr as $a) {
            $sku[$a->sku] = $a->sku;
            $pname[$a->name] = $a->name;
        }

        // $brands = cache()->remember('brands', 60, function () {
        //     return Brand::select('id', 'name')->get();
        // });
        // foreach ($brands as $brand) {
        //     $brandsArray[$brand->id] = $brand->name;
        // }
        // dd($brandsArray);
        $selected_brand = null;
        $selected_brand = cache()->remember('brands', 60, function () {
            return Brand::select('id', 'name')->get();
        });
        // $selected_brand = Brand::select('id', 'name')->get();
        $selected_supplier = null;

        $selected_supplier = cache()->remember('supplier', 60, function () {
            return Supplier::select('id', 'supplier')->get();
        });

        $selected_categories = null;
        $selected_categories = cache()->remember('categories', 60, function () {
            return Category::select('id', 'title')->get();
        });
        // $selected_categories = Category::select('id', 'title')->get();

        $products_names = $pname;
        $products_sku = $sku;

        asort($products_names);
        asort($products_sku);
        $products_categories = [];
        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'inventory-list')->first();

        $dynamicColumnsToShowPi = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPi = json_decode($hideColumns, true);
        }

        if (request()->ajax()) {
            return view('product-inventory.inventory-list-partials.load-more', compact('inventory_data', 'dynamicColumnsToShowPi'));
        }

        return view('product-inventory.inventory-list', compact('inventory_data', 'products_categories', 'products_sku', 'status_list', 'inventory_data_count', 'reportData', 'scrappedReportData', 'selected_brand', 'selected_supplier', 'selected_categories', 'dynamicColumnsToShowPi'));
    }

    public function columnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'postman-listing')->first();
        $result = null;

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'inventory-list';
            $column->column_name = json_encode($request->column_pi);
            $result = $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'inventory-list';
            $column->column_name = json_encode($request->column_pi);
            $column->user_id = auth()->user()->id;
            $result = $column->save();
        }

        if (request()->ajax()) {
            return response()->json(['code' => 200, 'data' => $result, 'message' => 'Column Visibility Updated Successfully!']);
        }

        return redirect()->back()->with('success', 'Column Visibility Updated Successfully!');
    }

    public function inventoryListNew(Request $request): View
    {
        $selected_brand = null;

        $term = '';
        $inventory_data = Product::select('products.id', 'products.sku', 'products.created_at', 'products.name', 'products.supplier', 'brands.name');

        $inventory_data->join('store_website_product_attributes as swp', 'swp.product_id', 'products.id');
        if ($request->start_date != '') {
            $inventory_data->whereDate('products.created_at', '>=', $request->start_date);
        }
        if ($request->end_date != '') {
            $inventory_data->whereDate('products.created_at', '<=', $request->end_date);
        }
        $inventory_data = $inventory_data->leftJoin('brands as b', function ($q) {
            $q->on('b.id', 'products.brand');
        });
        // $inventory_data = $inventory_data->leftJoin('categories as c', function ($q) {
        //     $q->on('c.id', 'products.category');
        // });
        if (isset($request->brand_names)) {
            $inventory_data = $inventory_data->whereIn('brand', $request->brand_names);
            $selected_brand = Brand::select('id', 'name')->whereIn('id', $request->brand_names)->get();
        }

        if (isset($request->term)) {
            $term = $request->term;
            $inventory_data = $inventory_data->where(function ($q) use ($term) {
                $q->where('products.name', 'LIKE', "%$term%")
                    ->orWhere('products.sku', 'LIKE', "%$term%")
                    ->orWhere('c.title', 'LIKE', "%$term%")
                    ->orWhere('b.name', 'LIKE', "%$term%")
                    ->orWhere('products.id', 'LIKE', "%$term%");
            });
        }

        $inventory_data = $inventory_data->select('products.*', 'b.name as brand_name');
        $inventory_data = $inventory_data->orderByDesc('swp.created_at')->paginate(20);

        $inventory_data_count = $inventory_data->total();

        $totalProduct = Supplier::join('scrapers as sc', 'sc.supplier_id', 'suppliers.id')
            ->join('scraped_products as sp', 'sp.website', 'sc.scraper_name')
            ->join('products as p', 'p.id', 'sp.product_id')
            ->where('suppliers.supplier_status_id', 1)
            ->select(DB::raw('count(distinct p.id) as total'))->first();

        $totalProduct = ($totalProduct) ? $totalProduct->total : 0;

        $noofProductInStock = Product::where('stock', '>', 0)->count();
        $productUpdated = InventoryStatusHistory::whereDate('date', '=', date('Y-m-d'))->select(DB::raw('count(distinct product_id) as total'))->first();
        $productUpdated = ($productUpdated) ? $productUpdated->total : 0;

        $history = InventoryHistory::orderByDesc('date')->limit(7)->get();

        if (request()->ajax()) {
            return view('product-inventory.inventory-list-partials.load-more-new', compact('inventory_data', 'noofProductInStock', 'productUpdated', 'totalProduct', 'selected_brand', 'term'));
        }

        return view('product-inventory.inventory-list-new', compact('inventory_data', 'inventory_data_count', 'noofProductInStock', 'productUpdated', 'totalProduct', 'history', 'selected_brand', 'term'));
    }

    public function downloadReport()
    {
        $query = Product::selectRaw('
				   sum(CASE WHEN products.category = ""
			           OR products.category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN products.color = ""
			           OR products.color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN products.composition = ""
			           OR products.composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN products.name = ""
			           OR products.name IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN products.short_description = ""
			           OR products.short_description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN products.price = ""
			           OR products.price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN products.size = ""
			           OR products.size IS NULL AND products.measurement_size_type IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       sum(CASE WHEN products.measurement_size_type = ""
			           OR products.measurement_size_type AND products.size = "" OR products.size IS NULL THEN 1 ELSE 0 END) AS missing_measurement,
			       `products`.`supplier`
				')
            ->where('products.supplier', '<>', '');
        $query = $query->groupBy('products.supplier')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $reportDatas = $query->get();

        return \Excel::download(new ReportExport($reportDatas), 'exports.xls');
    }

    public function downloadScrapReport()
    {
        $query = ScrapedProducts::selectRaw('
				   sum(CASE WHEN category = ""
			           OR category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN color = ""
			           OR color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN composition = ""
			           OR composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN title = ""
			           OR title IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN description = ""
			           OR description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN price = ""
			           OR price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN size = ""
			           OR size IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       supplier
				')
            ->where('supplier', '<>', '');
        $query = $query->groupBy('supplier')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $reportDatas = $query->get();

        return \Excel::download(new ReportExport($reportDatas), 'exports.xls');
    }

    public function inventoryHistory($id): JsonResponse
    {
        $inventory_history = InventoryStatusHistory::getInventoryHistoryFromProductId($id);

        foreach ($inventory_history as $each) {
            $supplier = Supplier::find($each['supplier_id']);
            if ($supplier) {
                $each['supplier'] = $supplier->supplier;
            } else {
                $each['supplier'] = '';
            }
        }

        return response()->json(['data' => $inventory_history]);
    }

    public function getSuppliers($id): JsonResponse
    {
        $suppliers = Product::with(['suppliers_info', 'suppliers_info.supplier'])->find($id);

        return response()->json(['data' => $suppliers->suppliers_info]);
    }

    public function getProductImages($id): JsonResponse
    {
        $product = Product::find($id);
        $urls = [];
        if ($product) {
            $medias = $product->getMedia(config('constants.attach_image_tag'));
            foreach ($medias as $media) {
                $urls[] = getMediaUrl($media);
            }
        }

        return response()->json(['urls' => $urls]);
    }

    public function getProductRejectedImages($id): JsonResponse
    {
        $product = Product::find($id);
        if ($product) {

            $medias = RejectedImages::getRejectedMediasFromProductId($id);
            $site_medias = $medias->groupBy('title');
            if ($site_medias->count()) {
                $view = view('product-inventory.inventory-list-partials.rejected-images', ['site_medias' => $site_medias]);
                $html = $view->render();
            } else {
                $html = '<h1>No rejected media found</h1>';
            }
        } else {
            $html = '<h1>No product found</h1>';
        }

        return response()->json(['html' => $html]);
    }

    public function changeSizeSystem(Request $request): JsonResponse
    {
        $product_ids = $request->get('product_ids');
        $size_system = $request->get('size_system');
        $messages = [];
        $errorMessages = [];
        if (! empty($size_system) && ! empty($product_ids)) {
            $products = Product::whereIn('id', $product_ids)->get();
            if (! $products->isEmpty()) {
                foreach ($products as $product) {
                    $productSupplier = ProductSupplier::where('product_id', $product->id)->where('supplier_id', $product->supplier_id)->first();
                    if ($productSupplier) {
                        $productSupplier->size_system = $size_system;

                        $allSize = explode(',', $product->size);
                        $euSize = ProductHelper::getEuSize($product, $allSize, $productSupplier->size_system);
                        $product->size_eu = implode(',', $euSize);

                        if (empty($euSize)) {
                            $product->status_id = StatusHelper::$unknownSize;
                            $errorMessages[] = "$product->sku has issue with size";
                        } else {
                            $messages[] = "$product->sku updated successfully";
                            foreach ($euSize as $es) {
                                ProductSizes::updateOrCreate([
                                    'product_id' => $product->id, 'supplier_id' => $product->supplier_id, 'size' => $es,
                                ], [
                                    'product_id' => $product->id, 'quantity' => 1, 'supplier_id' => $product->supplier_id, 'size' => $es,
                                ]);
                            }
                        }
                        $productSupplier->save();
                        $product->save();
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => implode('</br>', $messages), 'error_messages' => implode('</br>', $errorMessages)]);
    }

    public function changeErpSize(Request $request): JsonResponse
    {
        $sizes = $request->sizes;
        $erpSizes = $request->erp_size;
        $sizeSystemStr = $request->size_system;
        $categoryId = $request->category_id;

        if (! empty($sizes) && ! empty($erpSizes) && ! empty($sizeSystemStr)) {
            /// check first size system exist or not
            $sizeSystem = SystemSize::where('name', $sizeSystemStr)->first();

            if (! $sizeSystem) {

                $sizeSystem = new SystemSize;

                $sizeSystem->name = $sizeSystem;
                $sizeSystem->save();
            }

            // check size exist or not
            if (! empty($erpSizes)) {
                foreach ($erpSizes as $k => $epSize) {
                    $existSize = SystemSizeManager::where('category_id', $categoryId)->where('erp_size', $epSize)->first();

                    if (! $existSize) {

                        $existSize = new SystemSizeManager;
                        $existSize->category_id = $categoryId;
                        $existSize->erp_size = $epSize;
                        $existSize->status = 1;
                        $existSize->save();
                    }

                    if (isset($sizes[$k])) {
                        $checkMainSize = SystemSizeRelation::where('system_size_manager_id', $sizeSystem->id)
                            ->where('system_size', $existSize->id)
                            ->where('size', $sizes[$k])
                            ->first();

                        if (! $checkMainSize) {

                            $checkMainSize = new SystemSizeRelation;
                            $checkMainSize->system_size_manager_id = $existSize->id;
                            $checkMainSize->system_size = $sizeSystem->id;
                            $checkMainSize->size = $sizes[$k];
                            $checkMainSize->save();
                        }
                    }
                }

                UpdateFromSizeManager::dispatch([
                    'category_id' => $categoryId,
                    'size_system' => $sizeSystemStr,
                ])->onQueue('mageone');
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Your request has been send to the jobs']);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $product_ids = $request->get('product_ids');
        $product_status = $request->get('product_status');

        $messages = [];
        $errorMessages = [];
        if (! empty($product_status) && ! empty($product_ids)) {
            $products = Product::whereIn('id', $product_ids)->get();
            if (! $products->isEmpty()) {
                foreach ($products as $product) {
                    if ($product->status_id != $product_status) {
                        $product->status_id = $product_status;
                        $product->save();
                        $messages[] = "$product->name updated successfully";
                    }
                }
            } else {
                $messages[] = 'Something went wrong. Please try again later.';
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => implode('</br>', $messages), 'error_messages' => implode('</br>', $errorMessages)]);
    }

    public function supplierProductSummary(Request $request, int $supplier_id): View
    {
        $inventory = InventoryStatusHistory::whereDate('created_at', '>', Carbon::now()->subDays(7))->where('supplier_id', $supplier_id)->orderByDesc('in_stock');

        if ($request->search) {
            $inventory->where('product_id', 'like', '%'.$request->search)->orWhereHas('product', function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            });
        }

        $total_rows = $inventory->count();

        $inventory = $inventory->paginate(Setting::get('pagination'));

        $allHistory = [];

        foreach ($inventory as $history) {
            $row = ['id' => $history->id, 'product_name' => $history->product->name ?? '', 'supplier_name' => $history->supplier->supplier ?? '', 'product_id' => $history->product_id, 'brand_name' => $history->product->brands->name ?? ''];

            $dates = InventoryStatusHistory::whereDate('created_at', '>', Carbon::now()->subDays(7))->where('supplier_id', $history->supplier_id)->where('product_id', $history->product_id)->get();

            $row['dates'] = $dates;

            $allHistory[] = (object) $row;
        }

        return view('product-inventory.supplier-inventory-history', compact('allHistory', 'inventory', 'total_rows', 'request'));
    }

    public function supplierProductHistory(Request $request)
    {
        $total_rows = 25;
        $supplier_droupdown = Supplier::select('id', 'supplier')->get();
        $suppliers = Supplier::query();
        if ($request->supplier) {
            $suppliers = $suppliers->where('id', $request->supplier);
        }
        $suppliers = $suppliers->paginate($total_rows);
        $columnData = [];
        $start_date = new \DateTime(date('Y-m-d', strtotime('-7 days')));
        $end_date = new \DateTime(date('Y-m-d'));
        $interval = new \DateInterval('P1D');
        $range = new \DatePeriod($start_date, $interval, $end_date);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('product-inventory.partials.supplier-product-history-data', compact('total_rows', 'suppliers', 'range'))->render(),
            ], 200);
        }

        return view('product-inventory.supplier-product-history', compact('range', 'supplier_droupdown', 'total_rows', 'suppliers', 'columnData'));
    }

    public static function getLastScrappedOn($supplier_id)
    {
        $data = (array) InventoryStatusHistory::where('supplier_id', '=', $supplier_id)->latest('date')->first();

        if (isset($data['date'])) {
            return $data['date'];
        }

        return '';
    }

    public function supplierProductHistoryWithView(Request $request)
    {

        $suppliers = Supplier::pluck('supplier', 'id')->toArray();

        $selectedDate = Carbon::now()->subDays(7);
        $dataToInsert = [];
        for ($date = $selectedDate; $date < Carbon::now(); Carbon::parse($date)->addDays(1)) {
            $inventoryHistoryView = InventoryStatusHistory::where('inventory_status_histories.created_at', 'like', $date.'%')->first();
            if ($inventoryHistoryView == null) {
                $inventory = InventoryStatusHistory::leftjoin('scrapers', 'scrapers.supplier_id', '=', 'inventory_status_histories.supplier_id')->select('inventory_status_histories.created_at', 'inventory_status_histories.supplier_id', 'scrapers.last_completed_at', DB::raw('count(distinct product_id) as product_count_count'))
                    ->whereDate('inventory_status_histories.created_at', '=', $selectedDate)
                    ->where('in_stock', '>', 0)
                    ->groupBy('inventory_status_histories.supplier_id');

                if ($request->supplier && $request->supplier != '') {
                    $inventory = $inventory->where('inventory_status_histories.supplier_id', $request->supplier);
                }

                $inventory = $inventory->orderByDesc('product_count_count')->paginate(2); //dd($inventory);
                $total_rows = $inventory->total();
                $allHistory = [];
                $date = date('Y-m-d', strtotime(date('Y-m-d').' -6 day'));
                $extraDates = $date;
                $columnData = [];
                for ($i = 1; $i < 8; $i++) {
                    $columnData[] = $extraDates;
                    $extraDates = date('Y-m-d', strtotime($extraDates.' +1 day'));
                }

                foreach ($inventory as $row) {
                    $newRow = [];
                    $newRow['supplier_name'] = '';
                    if (isset($suppliers[$row->supplier_id])) {
                        $newRow['supplier_name'] = $suppliers[$row->supplier_id];
                    }
                    $brandCount = InventoryStatusHistory::join('products as p', 'p.id', 'inventory_status_histories.product_id')
                        ->whereDate('inventory_status_histories.created_at', '>=', $selectedDate)
                        ->where('inventory_status_histories.supplier_id', $row->supplier_id)
                        ->groupBy('p.brand')
                        ->select(DB::raw('count(p.brand) as total'))
                        ->get()
                        ->count();

                    $newRow['brands'] = $brandCount;
                    $newRow['products'] = $row->product_count_count;
                    $newRow['supplier_id'] = $row->supplier_id;
                    $newRow['last_scrapped_on'] = $row->last_completed_at;

                    foreach ($columnData as $c) {
                        $totalProduct = InventoryStatusHistory::whereDate('created_at', $c)
                            ->where('supplier_id', $row->supplier_id)
                            ->select(DB::raw('count(distinct product_id) as total_product'))->first();
                        $newRow['dates'][$c] = ($totalProduct) ? $totalProduct->total_product : 0;

                        $dataToInsert[] = ['supplier_id' => $row->supplier_id, 'supplier_name' => $newRow['supplier_name'], 'last_scrapped_on' => $newRow['last_scrapped_on'], 'products' => $newRow['products'], 'brands' => $newRow['brands'], 'date' => $c, 'count' => $newRow['dates'][$c]];
                    }

                    array_push($allHistory, $newRow);
                }
            }
        }

        dd($dataToInsert);
        if (count($dataToInsert) > 0) {
            InventoryStatusHistoryView::insert($dataToInsert);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('product-inventory.partials.supplier-product-history-data', compact('allHistory', 'inventory', 'total_rows', 'request', 'columnData'))->render(),
            ], 200);
        }

        return view('product-inventory.supplier-product-history', compact('allHistory', 'inventory', 'total_rows', 'suppliers', 'request', 'columnData'));
    }

    public function supplierProductHistoryCopy(Request $request)
    {
        $suppliers = Supplier::pluck('supplier', 'id')->toArray();
        dd($suppliers);

        $inventory = InventoryStatusHistory::leftjoin('scrapers', 'scrapers.supplier_id', '=', 'inventory_status_histories.supplier_id')->select('inventory_status_histories.created_at', 'inventory_status_histories.supplier_id', 'scrapers.last_completed_at', DB::raw('count(distinct product_id) as product_count_count', 'GROUP_CONCAT(product_id) as brand_products'))
            ->whereDate('inventory_status_histories.created_at', '>=', Carbon::now()->subDays(7))
            ->where('in_stock', '>', 0)
            ->groupBy('inventory_status_histories.supplier_id');

        if ($request->supplier) {
            $inventory = $inventory->where('inventory_status_histories.supplier_id', $request->supplier);
        }

        $inventory = $inventory->orderByDesc('product_count_count')->paginate(1); //dd($inventory);
        $total_rows = $inventory->total();
        $allHistory = [];
        $date = date('Y-m-d', strtotime(date('Y-m-d').' -6 day'));
        $extraDates = $date;
        $columnData = [];
        for ($i = 1; $i < 8; $i++) {
            $columnData[] = $extraDates;
            $extraDates = date('Y-m-d', strtotime($extraDates.' +1 day'));
        }

        foreach ($inventory as $row) {
            $newRow = [];
            $newRow['supplier_name'] = '';
            if (isset($suppliers[$row->supplier_id])) {
                $newRow['supplier_name'] = $suppliers[$row->supplier_id];
            }

            $brandCount = InventoryStatusHistory::join('products as p', 'p.id', 'inventory_status_histories.product_id')->whereDate('inventory_status_histories.created_at', '>', Carbon::now()->subDays(7))->where('inventory_status_histories.supplier_id', $row->supplier_id)
                ->where('in_stock', '>', 0)
                ->groupBy('p.brand')
                ->select(DB::raw('count(p.brand) as total'))
                ->get()
                ->count();

            $newRow['brands'] = $brandCount;
            $newRow['products'] = $row->product_count_count;
            $newRow['supplier_id'] = $row->supplier_id;
            $newRow['last_scrapped_on'] = $row->last_completed_at;

            foreach ($columnData as $c) {

                $totalProduct = InventoryStatusHistory::whereDate('created_at', $c)->where('supplier_id', $row->supplier_id)->select(DB::raw('count(distinct product_id) as total_product'))->first();

                $newRow['dates'][$c] = ($totalProduct) ? $totalProduct->total_product : 0;
            }
            array_push($allHistory, $newRow);
        }
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('product-inventory.partials.supplier-product-history-data', compact('allHistory', 'inventory', 'total_rows', 'request', 'columnData'))->render(),
            ], 200);
        }

        return view('product-inventory.supplier-product-history', compact('allHistory', 'inventory', 'total_rows', 'suppliers', 'request', 'columnData'));
    }

    public function supplierProductHistoryBrand(Request $request): View
    {
        $inventory = InventoryStatusHistory::join('products as p', 'p.id', 'inventory_status_histories.product_id')
            ->leftjoin('brands as b', 'b.id', 'p.brand')
            ->whereDate('inventory_status_histories.created_at', '>', Carbon::now()->subDays(7))->where('inventory_status_histories.supplier_id', $request->supplier_id)
            ->where('in_stock', '>', 0)
            ->groupBy('p.brand')
            ->select([DB::raw('count(distinct p.id) as total'), 'p.brand', 'b.name'])
            ->orderByDesc('total')
            ->get();

        return view('product-inventory.brand-history', compact('inventory'));
    }

    public function mergeScrapBrand(Request $request): RedirectResponse
    {
        $scraperBrand = $request->get('scraper_brand');
        $originalBrand = $request->get('product_brand');

        if (! empty($scraperBrand) && ! empty($originalBrand)) {
            DB::statement('update products join scraped_products as sp on sp.sku = products.sku 
						join brands as b1 on b1.id = products.brand
						join brands as b2 on b2.id = sp.brand_id
						set products.brand = sp.brand_id , products.last_brand = products.brand
						where b1.name = ? and b2.name = ?', [$originalBrand, $scraperBrand]);
        } else {
            return redirect()->back()->with('error', 'Please enter product brand and scraper brand');
        }

        return redirect()->back()->with('message', 'Product(s) updated successfully');
    }

    public function supplierDiscountFiles(Request $request): View
    {
        $suppliers = Supplier::all();

        $rows = SupplierBrandDiscount::with('supplier', 'brand');

        if ($request->supplier) {
            $rows = $rows->where('supplier_id', $request->supplier);
        }

        if ($request->brands) {
            $rows = $rows->where('brand_id', $request->brands);
        }

        $rows = $rows->paginate(30);

        $brand_data = SupplierBrandDiscount::distinct()->get(['brand_id']);
        $excel_data = ProductDiscountExcelFile::join('users', 'users.id', 'product_discount_excel_files.user_id')->select('product_discount_excel_files.*', 'users.name')->get();

        return view('product-inventory.discount-files', compact('suppliers', 'rows', 'brand_data', 'request', 'excel_data'));
    }

    public function download_excel(Request $request): BinaryFileResponse
    {
        $file = $request->filename;

        return response()->download(public_path('/product_discount_file/'.$file));
    }

    public function discountlogHistory(Request $request)
    {
        $id = $request->id;
        $header = $request->header;

        $discount_log = SupplierDiscountLogHistory::join('users', 'users.id', 'supplier_discount_log_history.user_id')->where('supplier_brand_discounts_id', $id)->where('header_name', $header)->select('supplier_discount_log_history.*', 'users.name')->get();

        if ($discount_log) {
            return $discount_log;
        }

        return 'error';
    }

    public function exportExcel(ExportExcelProductInventoryRequest $request): RedirectResponse
    {

        $file = $request->file('excel');

        if ($file->getClientOriginalExtension() == 'xlsx') {
            $reader = new Xlsx;
        } else {
            if ($file->getClientOriginalExtension() == 'xls') {
                $reader = new Xls;
            }
        }

        try {
            $ogfilename = $file->getClientOriginalName();

            $fileName_array = rtrim($ogfilename, '.xlsx');
            $fileName = ($fileName_array).'_'.time().'.'.$file->extension();

            $params_file['excel_name'] = $fileName;
            $params_file['user_id'] = Auth::user()->id;

            $spreadsheet = $reader->load($file->getPathname());

            $rows = $spreadsheet->getActiveSheet()->toArray();

            // -----------------------------------------------------------------------------Brand-----------------------------------------------------------------------

            if ($rows[1][0] == 'Brand') {
                foreach ($rows as $key => $row) {
                    if ($key == 0 || $key == 1) {
                        continue;
                    }
                    $brand_name = trim($row[0]);

                    $brand = Brand::where('name', 'like', '%'.$brand_name.'%')->first();

                    if (! $brand) {
                        $params_brand = [
                            'name' => $brand_name,
                        ];
                        $brand = Brand::create($params_brand);
                    }

                    $discount = new SupplierBrandDiscount;
                    $exist_row = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[1])->where('category', $row[2])->first();
                    if ($row[4] != '') {
                        $segments = CategorySegment::where('status', 1)->get();
                        if (! $segments->isEmpty()) {
                            foreach ($segments as $segment) {
                                $csd = CategorySegmentDiscount::where('brand_id', $brand->id)->where('category_segment_id', $segment->id)->first();
                                if ($csd) {
                                    $csd->amount = $row[4];
                                    $csd->save();
                                } else {

                                    CategorySegmentDiscount::create([
                                        'brand_id' => $brand->id,
                                        'category_segment_id' => $segment->id,
                                        'amount' => $row[4],
                                        'amount_type' => 'percentage',
                                    ]);
                                }
                            }
                        }
                    }

                    if ($exist_row) {
                        if ($exist_row->condition_from_retail != $row[4]) {
                            $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[1])->where('category', $row[2])->where('condition_from_retail', $exist_row->condition_from_retail)->update(['condition_from_retail' => $row[4]]);

                            $params['supplier_brand_discounts_id'] = $exist_row->id;
                            $params['header_name'] = 'condition_from_retail';
                            $params['old_value'] = $exist_row->condition_from_retail;
                            $params['new_value'] = $row[4];
                            $params['user_id'] = Auth::user()->id;

                            SupplierDiscountLogHistory::create($params);
                        }

                        if ($exist_row->condition_from_retail_exceptions != $row[5]) {
                            // $updaterow5 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[1])->where('category', $row[2])->where('condition_from_retail', $row[4])->where('condition_from_retail_exceptions', $exist_row->condition_from_retail_exceptions)->update(['condition_from_retail_exceptions' => $row[5]]);

                            $params['supplier_brand_discounts_id'] = $exist_row->id;
                            $params['header_name'] = 'condition_from_retail_exceptions';
                            $params['old_value'] = $exist_row->condition_from_retail_exceptions;
                            $params['new_value'] = $row[5];
                            $params['user_id'] = Auth::user()->id;

                            SupplierDiscountLogHistory::create($params);
                        }
                    } else {
                        $discount->supplier_id = $request->supplier;
                        $discount->brand_id = $brand->id;
                        $discount->gender = $row[1];
                        $discount->category = $row[2];
                        $discount->exceptions = $row[3];
                        $discount->condition_from_retail = $row[4];
                        $discount->condition_from_retail_exceptions = $row[5];
                        $discount->save();

                        if ($row[4] != null) {
                            $params['supplier_brand_discounts_id'] = $discount->id;
                            $params['header_name'] = 'condition_from_retail';
                            $params['old_value'] = '-';
                            $params['new_value'] = $row[4];
                            $params['user_id'] = Auth::user()->id;
                            $log_history = SupplierDiscountLogHistory::create($params);
                        }

                        if ($row[5] != null) {
                            $params['supplier_brand_discounts_id'] = $discount->id;
                            $params['header_name'] = 'condition_from_retail_exceptions';
                            $params['old_value'] = '-';
                            $params['new_value'] = $row[5];
                            $params['user_id'] = Auth::user()->id;
                            $log_history1 = SupplierDiscountLogHistory::create($params);
                        }
                    }
                }

                $file->move(public_path('product_discount_file'), $fileName);
                ProductDiscountExcelFile::create($params_file);

                return redirect()->back()->with('success', 'Excel Imported Successfully!');
            }
            // ------------------------------------------------------------------ SS21---------------------------------------------------------------------------
            if ($rows[0][1] == 'SS21') {
                $array1 = $array2 = [];
                $first_row = $rows[0][1];
                foreach ($rows as $key => $row) {
                    if ($row[1] == 'SS21' || $row[1] == 'ST' || $key == 2) {
                        continue;
                    }

                    $array1[] = [$row[1], $row[2]];
                    $array2[] = [$row[4], $row[5]];
                }

                $categories = [];
                $cat = [];
                foreach ($array1 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                $cat = [];
                foreach ($array2 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                foreach ($categories as $cats) {
                    if (isset($cats[0])) {
                        array_unshift($cats, []);
                    }
                    $condition_from_retail = null;
                    foreach ($cats as $key => $cat) {
                        if ($key == 1) {
                            $category = trim($cat[0]);

                            $gender = strpos($category, 'WOMAN') !== false ? 'WOMAN' : (strpos($category, 'MAN') !== false ? 'MAN' : '');
                            $category = str_replace(' + ACC', '', $category);

                            continue;
                        } elseif ($key == 2) {
                            $gen_price = $cat[0];
                            if ($first_row == 'SS21') {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST', '', $gen_price));
                                $generic_price = str_replace('+', '', $generic_price);
                            } else {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST+', '', $gen_price));
                                $generic_price = trim(str_replace('GENERIC PRICE: COST +', '', $generic_price));
                            }

                            continue;
                        } elseif ($key == 3) {
                            $exceptions = $cat[0];
                            $condition_from_retail_exceptions = trim(str_replace('EXCEPTIONS', '', $exceptions));
                            $condition_from_retail_exceptions = str_replace('+', '', $condition_from_retail_exceptions);

                            continue;
                        } elseif ($key == 0) {
                            continue;
                        } else {
                            $brand_name = $cat[0];

                            $condition_from_retail = $cat[1] !== null ? str_replace('C+', '', $cat[1]) : $condition_from_retail;

                            $brand = Brand::where('name', $brand_name)->first();

                            if (! $brand) {
                                $params_brand = [
                                    'name' => $brand_name,
                                ];
                                $brand = Brand::create($params_brand);
                            }

                            $discount = new SupplierBrandDiscount;

                            $exist_row = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->first();

                            if ($condition_from_retail != '') {
                                $segments = CategorySegment::where('status', 1)->get();
                                if (! $segments->isEmpty()) {
                                    foreach ($segments as $segment) {
                                        $csd = CategorySegmentDiscount::where('brand_id', $brand->id)->where('category_segment_id', $segment->id)->first();
                                        if ($csd) {
                                            $csd->amount = $condition_from_retail;
                                            $csd->save();
                                        } else {

                                            CategorySegmentDiscount::create([
                                                'brand_id' => $brand->id,
                                                'category_segment_id' => $segment->id,
                                                'amount' => $condition_from_retail,
                                                'amount_type' => 'percentage',
                                            ]);
                                        }
                                    }
                                }
                            }

                            if ($exist_row) {
                                if ($exist_row->condition_from_retail != $condition_from_retail) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier);

                                    if (isset($gender) && $gender != '') {
                                        $updaterow4 = $updaterow4->where('gender', $gender);
                                    }

                                    $updaterow4 = $updaterow4->where('category', $category)->where('condition_from_retail', $exist_row->condition_from_retail)->update(['condition_from_retail' => $condition_from_retail]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = $exist_row->condition_from_retail;
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                $generic_price_data = (isset($generic_price) && $generic_price != '' ? $generic_price : (isset($brand->deduction_percentage) ? $brand->deduction_percentage.'%' : '0%'));

                                if ($exist_row->generic_price != $generic_price_data) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('generic_price', $exist_row->generic_price)->update(['generic_price' => $generic_price_data]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = $exist_row->generic_price;
                                    $params['new_value'] = $generic_price_data;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($exist_row->condition_from_retail_exceptions != $condition_from_retail_exceptions) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('condition_from_retail_exceptions', $exist_row->condition_from_retail_exceptions)->update(['condition_from_retail_exceptions' => $condition_from_retail_exceptions]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'condition_from_retail_exceptions';
                                    $params['old_value'] = $exist_row->condition_from_retail_exceptions;
                                    $params['new_value'] = $condition_from_retail_exceptions;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            } else {
                                $generic_price_data = (isset($generic_price) && $generic_price != '' ? $generic_price : (isset($brand->deduction_percentage) ? $brand->deduction_percentage.'%' : ''));

                                $discount->supplier_id = $request->supplier;
                                $discount->brand_id = $brand->id;
                                $discount->gender = $gender;
                                $discount->category = $category;
                                $discount->generic_price = $generic_price_data;
                                $discount->condition_from_retail = $condition_from_retail;
                                $discount->condition_from_retail_exceptions = $condition_from_retail_exceptions;
                                $discount->save();

                                if ($condition_from_retail != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;

                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);

                                }

                                if ($generic_price != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;

                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $generic_price_data;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);

                                }

                                if ($condition_from_retail_exceptions != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;

                                    $params['header_name'] = 'condition_from_retail_exceptions';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $condition_from_retail_exceptions;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            }
                        }
                    }
                }

                $file->move(public_path('product_discount_file'), $fileName);
                $excel_log = ProductDiscountExcelFile::create($params_file);

                return redirect()->back()->with('success', 'Excel Imported Successfully!');
            }
            // -------------------------------------------------------------------------FW21-----------------------------------------------------------------------
            if ($rows[0][1] == 'FW21') {
                $array1 = $array2 = [];
                $first_time1 = 1;
                $first_row = $rows[0][1];
                foreach ($rows as $key => $row) {
                    if ($row[1] == 'FW21' || $row[1] == 'ST' || $key == 2) {
                        continue;
                    }

                    $array1[] = [$row[1], $row[2]];
                    $array2[] = [$row[4], $row[5]];
                }

                $categories = [];
                $cat = [];
                foreach ($array1 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                $cat = [];
                foreach ($array2 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                $total = 1;
                foreach ($categories as $key_ => $cats) {
                    if (isset($cats[0])) {
                        array_unshift($cats, []);
                    }
                    $condition_from_retail = null;
                    foreach ($cats as $key => $cat) {
                        if ($key == 1) {
                            $category = trim($cat[0]);
                            $gender = strpos($category, 'WOMAN') !== false ? 'WOMAN' : (strpos($category, 'MAN') !== false ? 'MAN' : '');
                            $category = str_replace(' + ACC', '', $category);

                            continue;
                        } elseif ($key == 2) {
                            $gen_price = $cat[0];
                            if ($first_row == 'FW21') {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST', '', $gen_price));
                                $generic_price = str_replace('+', '', $generic_price);
                            } else {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST+', '', $gen_price));
                                $generic_price = trim(str_replace('GENERIC PRICE: COST +', '', $generic_price));
                            }

                            continue;
                        } elseif ($key == 3) {
                            $exceptions = $cat[0];
                            $condition_from_retail_exceptions = trim(str_replace('EXCEPTIONS', '', $exceptions));
                            $condition_from_retail_exceptions = str_replace('+', '', $condition_from_retail_exceptions);

                            continue;
                        } elseif ($key == 0) {
                            continue;
                        } else {
                            $brand_name = $cat[0];

                            $condition_from_retail = $cat[1] !== null ? str_replace('C+', '', $cat[1]) : $condition_from_retail;
                            $brand = Brand::where('name', $brand_name)->first();

                            if (! $brand) {
                                $params_brand = [
                                    'name' => $brand_name,
                                ];
                                $brand = Brand::create($params_brand);
                            }

                            if ($condition_from_retail != '') {
                                $segments = CategorySegment::where('status', 1)->get();
                                if (! $segments->isEmpty()) {
                                    foreach ($segments as $segment) {
                                        $csd = CategorySegmentDiscount::where('brand_id', $brand->id)->where('category_segment_id', $segment->id)->first();
                                        if ($csd) {
                                            $csd->amount = $condition_from_retail;
                                            $csd->save();
                                        } else {

                                            CategorySegmentDiscount::create([
                                                'brand_id' => $brand->id,
                                                'category_segment_id' => $segment->id,
                                                'amount' => $condition_from_retail,
                                                'amount_type' => 'percentage',
                                            ]);
                                        }
                                    }
                                }
                            }

                            $discount = new SupplierBrandDiscount;

                            $exist_row = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->first();

                            if ($exist_row) {
                                if ($exist_row->condition_from_retail != $condition_from_retail) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier);

                                    if (isset($gender) && $gender != '') {
                                        $updaterow4 = $updaterow4->where('gender', $gender);
                                    }

                                    $updaterow4 = $updaterow4->where('gender', $gender)->where('category', $category)->where('condition_from_retail', $exist_row->condition_from_retail)->update(['condition_from_retail' => $condition_from_retail]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = $exist_row->condition_from_retail;
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                $generic_price_data = (isset($generic_price) && $generic_price != '' ? $generic_price : (isset($brand->deduction_percentage) ? $brand->deduction_percentage.'%' : ''));

                                if ($exist_row->generic_price != $generic_price_data) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('generic_price', $exist_row->generic_price)->update(['generic_price' => $generic_price_data]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = $exist_row->generic_price;
                                    $params['new_value'] = $generic_price_data;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($exist_row->condition_from_retail_exceptions != $condition_from_retail_exceptions) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('condition_from_retail_exceptions', $exist_row->condition_from_retail_exceptions)->update(['condition_from_retail_exceptions' => $condition_from_retail_exceptions]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'condition_from_retail_exceptions';
                                    $params['old_value'] = $exist_row->condition_from_retail_exceptions;
                                    $params['new_value'] = $condition_from_retail_exceptions;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            } else {
                                $generic_price_data = (isset($generic_price) && $generic_price != '' ? $generic_price : (isset($brand->deduction_percentage) ? $brand->deduction_percentage.'%' : ''));

                                $discount->supplier_id = $request->supplier;
                                $discount->brand_id = $brand->id;
                                $discount->gender = $gender;
                                $discount->category = $category;
                                $discount->generic_price = $generic_price_data;
                                $discount->condition_from_retail = $condition_from_retail;
                                $discount->condition_from_retail_exceptions = $condition_from_retail_exceptions;
                                $discount->save();

                                if ($condition_from_retail != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;
                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($generic_price != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;
                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $generic_price_data;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($condition_from_retail_exceptions != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;
                                    $params['header_name'] = 'condition_from_retail_exceptions';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $condition_from_retail_exceptions;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            }
                        }
                    }
                }

                $file->move(public_path('product_discount_file'), $fileName);
                $excel_log = ProductDiscountExcelFile::create($params_file);

                return redirect()->back()->with('success', 'Excel Imported Successfully!');
            }

            if ($rows[0][1] == 'FW20') {
                $array1 = $array2 = [];
                $first_time1 = 1;
                $first_row = $rows[0][1];
                foreach ($rows as $key => $row) {
                    if ($row[1] == 'FW20' || $row[1] == 'ST' || $key == 2) {
                        continue;
                    }

                    $row_1 = (isset($row[1]) && $row[1] != null ? $row[1] : '-');
                    $row_2 = (isset($row[2]) && $row[2] != null ? $row[2] : '-');

                    $row_4 = (isset($row[4]) && $row[4] != null ? $row[4] : '-');
                    $row_5 = (isset($row[5]) && $row[5] != null ? $row[5] : '-');

                    $array1[] = [$row_1, $row_2];
                    $array2[] = [$row_4, $row_5];
                }
                $categories = [];
                $cat = [];
                foreach ($array1 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                $cat = [];
                foreach ($array2 as $key => $row) {
                    if ($row[0] == null && $row[1] == null) {
                        if ($cat[0][0] == null && $cat[0][1] == null) {
                            unset($cat[0]);
                        }
                        $categories[] = $cat;
                        $cat = [];
                    }
                    $cat[] = $row;
                }
                if ($cat[0][0] == null && $cat[0][1] == null) {
                    unset($cat[0]);
                }
                $categories[] = $cat;
                $total = 1;
                foreach ($categories as $key_ => $cats) {
                    if (isset($cats[0])) {
                        array_unshift($cats, []);
                    }
                    foreach ($cats as $key => $cat) {
                        if ($key == 1) {
                            $category = trim($cat[0]);
                            $gender = strpos($category, 'WOMAN') !== false ? 'WOMAN' : 'MAN';
                            $category = str_replace(' + ACC', '', $category);

                            continue;
                        } elseif ($key == 2) {
                            $gen_price = $cat[0];

                            if ($first_row == 'FW20') {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST', '', $gen_price));
                                $generic_price = str_replace('+', '', $generic_price);
                            } else {
                                $generic_price = trim(str_replace('GENERIC PRICE: COST +', '', $gen_price));
                                $generic_price = trim(str_replace('GENERIC PRICE: COST+', '', $generic_price));
                            }

                            continue;
                        } elseif ($key == 3 || $key == 0) {
                            continue;
                        } else {
                            $brand_name = trim($cat[0]);
                            $condition_from_retail = $cat[1] !== null ? str_replace('C+', '', $cat[1]) : $condition_from_retail;

                            $brand = Brand::where('name', 'like', '%'.$brand_name.'%')->first();

                            if (! $brand) {
                                $params_brand = [
                                    'name' => $brand_name,
                                ];
                                $brand = Brand::create($params_brand);
                            }
                            if ($condition_from_retail != '') {
                                $segments = CategorySegment::where('status', 1)->get();
                                if (! $segments->isEmpty()) {
                                    foreach ($segments as $segment) {
                                        $csd = CategorySegmentDiscount::where('brand_id', $brand->id)->where('category_segment_id', $segment->id)->first();
                                        if ($csd) {
                                            $csd->amount = $condition_from_retail;
                                            $csd->save();
                                        } else {

                                            CategorySegmentDiscount::create([
                                                'brand_id' => $brand->id,
                                                'category_segment_id' => $segment->id,
                                                'amount' => $condition_from_retail,
                                                'amount_type' => 'percentage',
                                            ]);
                                        }
                                    }
                                }
                            }
                            $discount = new SupplierBrandDiscount;

                            $exist_row = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->first();

                            if ($exist_row) {
                                if ($exist_row->condition_from_retail != $condition_from_retail) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('condition_from_retail', $exist_row->condition_from_retail)->update(['condition_from_retail' => $condition_from_retail]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = $exist_row->condition_from_retail;
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($exist_row->generic_price != $generic_price) {
                                    $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $gender)->where('category', $category)->where('generic_price', $exist_row->generic_price)->update(['generic_price' => $generic_price]);

                                    $params['supplier_brand_discounts_id'] = $exist_row->id;
                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = $exist_row->generic_price;
                                    $params['new_value'] = $generic_price;
                                    $params['user_id'] = Auth::user()->id;

                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            } else {
                                $discount->supplier_id = $request->supplier;
                                $discount->brand_id = $brand->id;
                                $discount->gender = $gender;
                                $discount->category = $category;
                                $discount->generic_price = $generic_price;
                                $discount->condition_from_retail = $condition_from_retail;
                                $discount->save();

                                if ($condition_from_retail != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;
                                    $params['header_name'] = 'condition_from_retail';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $condition_from_retail;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }

                                if ($generic_price != null) {
                                    $params['supplier_brand_discounts_id'] = $discount->id;
                                    $params['header_name'] = 'generic_price';
                                    $params['old_value'] = '-';
                                    $params['new_value'] = $generic_price;
                                    $params['user_id'] = Auth::user()->id;
                                    $log_history = SupplierDiscountLogHistory::create($params);
                                }
                            }
                        }
                    }
                }

                $file->move(public_path('product_discount_file'), $fileName);
                $excel_log = ProductDiscountExcelFile::create($params_file);

                return redirect()->back()->with('success', 'Excel Imported Successfully!');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please check your file!');
        }

        return redirect()->back()->with('error', 'Something went wrong, please check your file!');
    }

    public function mapping_excel(MappingExcelProductInventoryRequest $request): JsonResponse
    {

        $file = $request->file('excel');

        if ($file->getClientOriginalExtension() == 'xlsx') {
            $reader = new Xlsx;
        } else {
            if ($file->getClientOriginalExtension() == 'xls') {
                $reader = new Xls;
            }
        }

        try {
            $ogfilename = $file->getClientOriginalName();
            $fileName_array = rtrim($ogfilename, '.xlsx');
            $fileName = ($fileName_array).'_'.time().'.'.$file->extension();
            $params_file['excel_name'] = $fileName;
            $params_file['user_id'] = Auth::user()->id;

            $spreadsheet = $reader->load($file->getPathname());

            $rows = $spreadsheet->getActiveSheet()->toArray();
            $i = 0;
            foreach ($rows as $row) {
                if ($row[$i] != '' && $row[$i + 1] != '' && $row[$i + 2] != '') {
                    $data = $row;
                    $column_index = $i;
                    break;
                }
                $i++;
            }

            return response()->json(['code' => 200, 'message' => 'Header Data Get Successfully , Please do Mapping', 'header_data' => $data, 'column_index' => $column_index]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Something went wrong, please check your file!']);
        }

        return response()->json(['code' => 400, 'message' => 'Something went wrong, please check your file!']);
    }

    public function export_mapping_excel(Request $request): JsonResponse
    {
        $file = $request->file;

        if ($file->getClientOriginalExtension() == 'xlsx') {
            $reader = new Xlsx;
        } else {
            if ($file->getClientOriginalExtension() == 'xls') {
                $reader = new Xls;
            }
        }

        try {
            $brand_index = $request->brand_dropdown;
            $gender_index = $request->gender_dropdown;
            $category_index = $request->category_dropdown;
            $exceptions_index = $request->exceptions_dropdown;
            $generice_price_index = $request->generice_price_dropdown;
            $condition_from_retail_index = $request->condition_from_retail_dropdown;
            $condition_from_exceptions_index = $request->condition_from_exceptions_dropdown;
            $column_index = $request->column_index;

            $ogfilename = $file->getClientOriginalName();

            $fileName_array = rtrim($ogfilename, '.xlsx');
            $fileName = ($fileName_array).'_'.time().'.'.$file->extension();

            $params_file['excel_name'] = $fileName;
            $params_file['user_id'] = Auth::user()->id;

            $spreadsheet = $reader->load($file->getPathname());

            $rows = $spreadsheet->getActiveSheet()->toArray();

            foreach ($rows as $key => $row) {
                if ($key <= $column_index) {
                    continue;
                }

                $brand_name = trim($row[$brand_index]);

                if ($brand_name != '') {
                    $brand = Brand::where('name', 'like', '%'.$brand_name.'%')->first();
                } else {
                    $brand = '';
                }

                if (! $brand && $brand_name != '') {
                    $params_brand = [
                        'name' => $brand_name,
                    ];
                    $brand = Brand::create($params_brand);
                }

                if ($brand) {
                    if ($row[$condition_from_retail_index] != '') {
                        $segments = CategorySegment::where('status', 1)->get();
                        if (! $segments->isEmpty()) {
                            foreach ($segments as $segment) {
                                $csd = CategorySegmentDiscount::where('brand_id', $brand->id)->where('category_segment_id', $segment->id)->first();
                                if ($csd) {
                                    $csd->amount = $row[$condition_from_retail_index];
                                    $csd->save();
                                } else {
                                    CategorySegmentDiscount::create([
                                        'brand_id' => $brand->id,
                                        'category_segment_id' => $segment->id,
                                        'amount' => $row[$condition_from_retail_index],
                                        'amount_type' => 'percentage',
                                    ]);
                                }
                            }
                        }
                    }
                    $discount = new SupplierBrandDiscount;

                    $exist_row = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[$gender_index])->where('category', $row[$category_index])->first();

                    if ($exist_row) {
                        if ($generice_price_index != null && $exist_row->generic_price != $row[$generice_price_index]) {
                            // $updaterow3 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[$gender_index])->where('category', $row[$category_index])->where('generic_price', $exist_row->generic_price)->update(['generic_price' => $row[$generice_price_index]]);

                            $params['supplier_brand_discounts_id'] = $exist_row->id;
                            $params['header_name'] = 'generic_price';
                            $params['old_value'] = $exist_row->generic_price;
                            $params['new_value'] = $row[$generice_price_index];
                            $params['user_id'] = Auth::user()->id;

                            SupplierDiscountLogHistory::create($params);
                        }

                        if ($condition_from_retail_index != null && $exist_row->condition_from_retail != $row[$condition_from_retail_index]) {
                            // $updaterow4 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[$gender_index])->where('category', $row[$category_index])->where('condition_from_retail', $exist_row->condition_from_retail)->update(['condition_from_retail' => $row[$condition_from_retail_index]]);

                            $params['supplier_brand_discounts_id'] = $exist_row->id;
                            $params['header_name'] = 'condition_from_retail';
                            $params['old_value'] = $exist_row->condition_from_retail;
                            $params['new_value'] = $row[$condition_from_retail_index];
                            $params['user_id'] = Auth::user()->id;

                            SupplierDiscountLogHistory::create($params);
                        }

                        if ($condition_from_exceptions_index != null && $exist_row->condition_from_retail_exceptions != $row[$condition_from_exceptions_index]) {
                            // $updaterow5 = SupplierBrandDiscount::where('brand_id', $brand->id)->where('supplier_id', $request->supplier)->where('gender', $row[$gender_index])->where('category', $row[$category_index])->where('condition_from_retail', $row[$condition_from_retail_index])->where('condition_from_retail_exceptions', $exist_row->condition_from_retail_exceptions)->update(['condition_from_retail_exceptions' => $row[$condition_from_exceptions_index]]);

                            $params['supplier_brand_discounts_id'] = $exist_row->id;
                            $params['header_name'] = 'condition_from_retail_exceptions';
                            $params['old_value'] = $exist_row->condition_from_retail_exceptions;
                            $params['new_value'] = $row[$condition_from_exceptions_index];
                            $params['user_id'] = Auth::user()->id;

                            SupplierDiscountLogHistory::create($params);
                        }
                    } else {
                        $discount->supplier_id = $request->supplier;
                        $discount->brand_id = $brand->id;
                        $discount->gender = $row[$gender_index];
                        $discount->category = $row[$category_index];
                        $discount->generic_price = ($generice_price_index != null ? $row[$generice_price_index] : null);
                        $discount->exceptions = ($exceptions_index != null ? $row[$exceptions_index] : null);
                        $discount->condition_from_retail = ($condition_from_retail_index != null ? $row[$condition_from_retail_index] : null);
                        $discount->condition_from_retail_exceptions = ($condition_from_exceptions_index ? $row[$condition_from_exceptions_index] : null);
                        $discount->save();

                        if ($generice_price_index != null && $row[$generice_price_index] != null) {
                            $params['supplier_brand_discounts_id'] = $discount->id;
                            $params['header_name'] = 'generic_price';
                            $params['old_value'] = '-';
                            $params['new_value'] = $row[$generice_price_index];
                            $params['user_id'] = Auth::user()->id;
                            SupplierDiscountLogHistory::create($params);
                        }

                        if ($condition_from_retail_index != null && $row[$condition_from_retail_index] != null) {
                            $params['supplier_brand_discounts_id'] = $discount->id;
                            $params['header_name'] = 'condition_from_retail';
                            $params['old_value'] = '-';
                            $params['new_value'] = $row[$condition_from_retail_index];
                            $params['user_id'] = Auth::user()->id;
                            SupplierDiscountLogHistory::create($params);
                        }

                        if ($condition_from_exceptions_index != null && $row[$condition_from_exceptions_index] != null) {
                            $params['supplier_brand_discounts_id'] = $discount->id;
                            $params['header_name'] = 'condition_from_retail_exceptions';
                            $params['old_value'] = '-';
                            $params['new_value'] = $row[$condition_from_exceptions_index];
                            $params['user_id'] = Auth::user()->id;
                            SupplierDiscountLogHistory::create($params);
                        }
                    }
                }
            }

            $file->move(public_path('product_discount_file'), $fileName);
            ProductDiscountExcelFile::create($params_file);

            return response()->json(['code' => 200, 'message' => 'Excel Imported Successfully!']);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Something went wrong, please check your file!']);
        }

        return response()->json(['code' => 400, 'message' => 'Something went wrong, please check your file!']);
    }

    public function updategenericprice(Request $request): JsonResponse
    {
        $generic_price_data = $request->generic_price_data;
        $id = $request->generic_id;

        $brand_disc = SupplierBrandDiscount::find($id);

        $brand_disc_history = new SupplierDiscountLogHistory;
        $brand_disc_history->supplier_brand_discounts_id = $id;
        $brand_disc_history->header_name = 'generic_price';
        $brand_disc_history->old_value = $brand_disc->generic_price;
        $brand_disc_history->new_value = $generic_price_data;
        $brand_disc_history->user_id = Auth::id();

        $brand_disc_history->save();

        $brand_disc->generic_price = $generic_price_data;
        $brand_disc->save();

        return response()->json([
            'brand_disc' => $brand_disc,
        ]);
    }

    public function conditionprice(Request $request): JsonResponse
    {
        $condition_from_retail_data = $request->condition_from_retail_data;
        $id = $request->condition_id;

        $condition_disc = SupplierBrandDiscount::find($id);

        $condition_disc_history = new SupplierDiscountLogHistory;
        $condition_disc_history->supplier_brand_discounts_id = $id;
        $condition_disc_history->header_name = 'condition_from_retail';
        $condition_disc_history->old_value = $condition_disc->condition_from_retail;
        $condition_disc_history->new_value = $condition_from_retail_data;
        $condition_disc_history->user_id = Auth::id();

        $condition_disc_history->save();

        $condition_disc->condition_from_retail = $condition_from_retail_data;
        $condition_disc->save();

        return response()->json([
            'condition_disc' => $condition_disc,
        ]);
    }

    public function exceptionsprice(Request $request): JsonResponse
    {
        $condition_from_retail_exceptions_data = $request->condition_from_retail_exceptions_data;
        $id = $request->condition_exceptions_id;

        $exceptions_discount = SupplierBrandDiscount::find($id);

        $exceptions_discount_his = new SupplierDiscountLogHistory;
        $exceptions_discount_his->supplier_brand_discounts_id = $id;
        $exceptions_discount_his->header_name = 'condition_from_retail_exceptions';
        $exceptions_discount_his->old_value = $exceptions_discount->condition_from_retail_exceptions;
        $exceptions_discount_his->new_value = $condition_from_retail_exceptions_data;
        $exceptions_discount_his->user_id = Auth::id();

        $exceptions_discount_his->save();

        $exceptions_discount->condition_from_retail_exceptions = $condition_from_retail_exceptions_data;
        $exceptions_discount->save();

        return response()->json([
            'exceptions_discount' => $exceptions_discount,
        ]);
    }

    public function scrapelog(Request $request): View
    {
        // Get results

        $logs = ScrapedProductMissingLog::query();

        $logs = $logs->paginate(Setting::get('pagination'));
        $total_count = $logs->total();
        // Show results
        if ($request->ajax()) {
            return view('products.scrape_log_ajax', compact('logs', 'total_count'));
        } else {
            return view('products.scrape_log', compact('logs', 'total_count'));
        }
    }

    // Inventory sold out products list
    public function getStockwithZeroQuantity(Request $request)
    {
        if ($request->ajax()) {
            $products = InventoryStatusHistory::query();
            $products->with('product', 'supplier');

            if (isset($request->id) && ! empty($request->id)) {
                $products = $products->where('product_id', $request->id);
            }
            if (isset($request->name) && ! empty($request->name)) {
                $products->select('inventory_status_histories.*')->leftjoin('products as p1', 'p1.id', 'inventory_status_histories.product_id')->
                where('p1.name', $request->name);
            }

            if (isset($request->sku) && ! empty($request->sku)) {
                $products->select('inventory_status_histories.*')->leftjoin('products as p2', 'p2.id', 'inventory_status_histories.product_id')->
                where('p2.sku', $request->sku);
            }

            $products->where('in_stock', '>=', 1)
                ->groupBy('product_id')
                ->orderByDesc('created_at');

            return Datatables::of($products)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    $product = $row->product ? $row->product->name : 'N/A';

                    return $product;
                })
                ->addColumn('sku', function ($row) {
                    $product = $row->product ? $row->product->sku : 'N/A';

                    return $product;
                })
                ->addColumn('in_stock', function ($row) {
                    $product = $row->product ? $row->product->in_stock : 'N/A';

                    return $product;
                })
                ->addColumn('prev_in_stock', function ($row) {
                    $product = $row->product ? $row->product->prev_in_stock : 'N/A';

                    return $product;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" data-id="'.$row->id.'" data-product-id="'.$row->product_id.'" class="get-product-log-detail btn btn-warning btn-sm"><i class="fa fa-list fa-sm"></i></a>&nbsp;';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'product_name', 'sku', 'in_stock', 'prev_in_stock'])
                ->make(true);
        }

        return view('product-inventory.out-of-stock');
    }

    // Inventory sold out product history list
    public function outOfStockProductLog(Request $request): JsonResponse
    {
        $product = $request->product;
        if ($product) {
            $productsLog = InventoryStatusHistory::with('product', 'supplier')->where(['in_stock' => 1, 'product_id' => $request->product])->get();
            $productName = $productsLog[0]->product ? $productsLog[0]->product->name : 'N/A';
            $productSku = $productsLog[0]->product ? $productsLog[0]->product->sku : 'N/A';
            $response = (string) view('product-inventory.out-of-stock-product-log', compact('productsLog'));

            return response()->json(['success' => true, 'msg' => 'Product logs found successfully.', 'data' => $response, 'productName' => $productName, 'productSku' => $productSku]);
        } else {
            return response()->json(['success' => false, 'msg' => 'No product history found']);
        }
    }
}
