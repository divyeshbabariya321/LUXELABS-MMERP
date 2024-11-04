<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Brand;
use App\Category;
use App\ChatMessage;
use App\Comment;
use App\CommunicationHistory;
use App\Customer;
use App\Email;
use App\Events\ProformaConfirmed;
use App\Exports\PurchasesExport;
use App\File;
use App\Helpers;
use App\Helpers\OrderHelper;
use App\Http\Requests\EmailResendPurchaseRequest;
use App\Http\Requests\EmailSendPurchaseRequest;
use App\Http\Requests\ProductCreateReplacePurchaseRequest;
use App\Http\Requests\SendEmailBulkPurchaseRequest;
use App\Http\Requests\StorePurchaseRequest;
use App\Jobs\SendEmail;
use App\LogExcelImport;
use App\Mails\Manual\ForwardEmail;
use App\Mails\Manual\PurchaseEmail;
use App\Mails\Manual\PurchaseExport;
use App\Mails\Manual\ReplyToEmail;
use App\Message;
use App\Models\PurchaseProductSupplier;
use App\Order;
use App\OrderProduct;
use App\PrivateView;
use App\Product;
use App\ProductSupplier;
use App\Purchase;
use App\PurchaseDiscount;
use App\PurchaseProduct;
use App\ReadOnly\OrderStatus as OrderStatus;
use App\ReadOnly\PurchaseStatus;
use App\ReadOnly\SupplierList;
use App\Remark;
use App\Reply;
use App\ReplyCategory;
use App\ScrapedProducts;
use App\Setting;
use App\StatusChange;
use App\Supplier;
use App\Task;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Webklex\PHPIMAP\ClientManager;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->input('term');

        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }

        switch ($request->input('sortby')) {
            case 'date':
                $sortby = 'created_at';
                break;
            case 'purchase_handler':
                $sortby = 'purchase_handler';
                break;
            case 'supplier':
                $sortby = 'supplier';
                break;
            case 'status':
                $sortby = 'status';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            default:
                $sortby = 'created_at';
        }

        $purchases = (new Purchase)->newQuery()->with([
            'orderProducts' => function ($query) {
                $query->with([
                    'Order' => function ($q) {
                        $q->with('customer');
                    },
                ]);
                $query->with(['Product']);
            },
            'Products' => function ($query) {
                $query->with([
                    'orderproducts' => function ($quer) {
                        $quer->with([
                            'Order' => function ($q) {
                                $q->with('customer');
                            },
                        ]);
                    },
                ]);
            },
            'purchase_supplier',
        ]);

        if (! empty($term)) {
            $purchases = $purchases
                ->orWhere('id', 'like', '%'.$term.'%')
                ->orWhere('purchase_handler', Helpers::getUserIdByName($term))
                ->orWhere('supplier', 'like', '%'.$term.'%')
                ->orWhere('status', 'like', '%'.$term.'%')
                ->orWhereHas('Products', function ($query) use ($term) {
                    $query->where('sku', 'LIKE', "%$term%");
                });
        }

        if ($sortby != 'communication') {
            $purchases = $purchases->orderBy($sortby, $orderby);
        }

        $users = Helpers::getUserArray(User::all());

        $purchases_array = $purchases->select(['id', 'purchase_handler', 'supplier', 'supplier_id', 'status', 'created_at'])->get()->toArray();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($purchases_array, $perPage * ($currentPage - 1), $perPage);

        $purchases_array = new LengthAwarePaginator($currentItems, count($purchases_array), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $purchase_data = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
        ];
        $purchase_products = Product::with('orderproducts')->whereHas('purchases')->get();

        foreach ($purchase_products as $product) {
            if (count($product->orderproducts) > 0) {
                if ($product->orderproducts[0]->purchase_status != 'In Transit from Italy to Dubai' && $product->orderproducts[0]->purchase_status != 'Shipment Received in Dubai' && $product->orderproducts[0]->purchase_status != 'Shipment in Transit from Dubai to India' && $product->orderproducts[0]->purchase_status != 'Shipment Received in India') {
                    $purchase_data['0'] += 1;
                }

                if ($product->orderproducts[0]->purchase_status == 'In Transit from Italy to Dubai') {
                    $purchase_data['1'] += 1;
                }

                if ($product->orderproducts[0]->purchase_status == 'Shipment Received in Dubai') {
                    $purchase_data['2'] += 1;
                }

                if ($product->orderproducts[0]->purchase_status == 'Shipment in Transit from Dubai to India') {
                    $purchase_data['3'] += 1;
                }

                if ($product->orderproducts[0]->purchase_status == 'Shipment Received in India') {
                    $purchase_data['4'] += 1;
                }
            } else {
                $purchase_data['0'] += 1;
            }
        }

        $suppliers = Supplier::select(['id', 'supplier'])->get();
        $agents = Agent::where('model_type', Supplier::class)->get();
        $agents_array = [];

        foreach ($agents as $agent) {
            $agents_array[$agent->model_id][$agent->id] = $agent->name.' - '.$agent->email;
        }

        $media_tags = config('constants.media_tags');
        if ($request->ajax()) {
            $html = view('purchase.purchase-item', ['purchases_array' => $purchases_array, 'orderby' => $orderby, 'users' => $users, 'media_tags' => $media_tags])->render();

            return response()->json(['html' => $html]);
        }

        return view('purchase.index', compact('purchases_array', 'term', 'orderby', 'users', 'suppliers', 'agents_array', 'purchase_data', 'media_tags'));
    }

    public function purchaseGrid(Request $request, $page = null)
    {
        $purchases = Db::select('select p.sku,p.id,pp.order_product_id from purchase_products as pp join products as p on p.id = pp.product_id');

        $not_include_products = [];
        $includedPurchases = [];
        foreach ((array) $purchases as $product) {
            if ($product->order_product_id > 0) {
                $not_include_products[] = $product->order_product_id;
                $includedPurchases[] = $product->id;
            }
        }

        $skuNeed = Db::select('select p.id from order_products as op join products as p on p.id = op.product_id left join purchase_products as pp on pp.order_product_id = op.id  where pp.order_product_id is null group by op.sku');
        $skuNeed = collect($skuNeed)->pluck('id')->toArray();

        $ignoreSku = array_diff($includedPurchases, $skuNeed);
        $customerId = request()->get('customer_id', 0);

        if ($request->status[0] != null && $request->supplier[0] == null && $request->brand[0] == null) {
            $status = $request->status;
            $orders = OrderProduct::join('orders as o', 'o.id', 'order_products.order_id')
                ->join('products as p', 'p.id', 'order_products.product_id')
                ->whereIn('o.order_status', $status)
                ->where('qty', '>=', 1);

            if ($customerId > 0) {
                $orders = $orders->where('o.customer_id', $customerId);
            }

            $orders = $orders->select(['order_products.sku', 'p.id'])->get();
        }
        $status_list = implode("','", $request->status ?? []);

        if ($request->supplier[0] != null) {
            $supplier = $request->supplier[0];

            if ($request->status[0] != null) {
                $status_list = implode("','", $request->status);

                $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])->join('orders as o', 'o.id', 'order_products.order_id')
                    ->join('products as p', 'p.id', 'order_products.product_id')
                    ->join('product_suppliers as ps', 'ps.product_id', 'p.id')
                    ->whereIn('o.order_status', $request->status)
                    ->whereIn('ps.supplier_id', $request->supplier)->where('qty', '>=', 1);
                if ($customerId > 0) {
                    $orders = $orders->where('o.customer_id', $customerId);
                }

                $orders = $orders->get();
            } else {
                $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])->join('orders as o', 'o.id', 'order_products.order_id');
                if ($page == 'canceled-refunded') {

                    $orders = $orders->whereIn('o.order_status_id', [OrderHelper::$cancel, OrderHelper::$refundToBeProcessed]);
                } elseif ($page == 'ordered') {

                } elseif ($page == 'delivered') {
                    $orders = $orders->whereIn('o.order_status_id', [OrderHelper::$delivered]);
                } elseif ($page == 'non_ordered') {
                    $orders = $orders->whereNotIn('o.order_status_id', [
                        OrderHelper::$followUpForAdvance,
                        OrderHelper::$proceedWithOutAdvance,
                        OrderHelper::$advanceRecieved,
                        OrderHelper::$prepaid,
                    ]);
                } elseif ($page != 'ordered') {
                    $orders = $orders->whereIn('o.order_status_id', [
                        OrderHelper::$followUpForAdvance,
                        OrderHelper::$proceedWithOutAdvance,
                        OrderHelper::$advanceRecieved,
                        OrderHelper::$prepaid,
                    ]);
                }
                $orders = $orders->join('products as p', 'p.id', 'order_products.product_id')->join('product_suppliers as ps', 'ps.product_id', 'p.id')->whereIn('ps.supplier_id', $request->supplier)
                    ->where('qty', '>=', 1);
                if ($customerId > 0) {
                    $orders = $orders->where('o.customer_id', $customerId);
                }

                $orders = $orders->get();
            }
        }

        if ($request->brand[0] != null) {
            $brand = $request->brand[0];

            if ($request->status[0] != null || $request->supplier[0] != null) {
                $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])
                    ->join('orders as o', 'o.id', 'order_products.order_id')
                    ->join('products as p', 'p.sku', 'order_products.sku');
                if ($request->status[0] != null) {
                    $orders = $orders->whereIn('o.order_status', $request->status);
                }
                $orders = $orders->where('brand', $brand)->where('qty', '>=', 1);
                if ($customerId > 0) {
                    $orders = $orders->where('o.customer_id', $customerId);
                }

                $orders = $orders->get();
            } else {
                $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])->join('orders as o', 'o.id', 'order_products.order_id');
                if ($page == 'canceled-refunded') {
                    $orders = $orders->whereIn('o.order_status_id', [
                        OrderHelper::$cancel,
                        OrderHelper::$refundToBeProcessed,
                    ]);
                } elseif ($page == 'delivered') {
                    $orders = $orders->whereIn('o.order_status_id', [
                        OrderHelper::$delivered,
                    ]);
                } elseif ($page == 'non_ordered') {
                    $orders = $orders->whereNotIn('o.order_status_id', [
                        OrderHelper::$followUpForAdvance,
                        OrderHelper::$proceedWithOutAdvance,
                        OrderHelper::$advanceRecieved,
                        OrderHelper::$prepaid,
                    ]);
                } elseif ($page != 'ordered') {
                    $orders = $orders->whereIn('o.order_status_id', [
                        OrderHelper::$followUpForAdvance,
                        OrderHelper::$proceedWithOutAdvance,
                        OrderHelper::$advanceRecieved,
                        OrderHelper::$prepaid,
                    ]);
                }

                $orders = $orders->join('products as p', 'p.id', 'order_products.product_id')->where('brand', $brand)->where('qty', '>=', 1);
                if ($customerId > 0) {
                    $orders = $orders->where('o.customer_id', $customerId);
                }

                $orders = $orders->get();
            }
        }

        if (! empty($request->order_id)) {
            $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])
                ->join('orders as o', 'o.id', 'order_products.order_id')
                ->join('products as p', 'p.id', 'order_products.product_id');
            if ($page == 'canceled-refunded') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$cancel,
                    OrderHelper::$refundToBeProcessed,
                ]);
            } elseif ($page == 'delivered') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$delivered,
                ]);
            } elseif ($page == 'non_ordered') {
                $orders = $orders->whereNotIn('o.order_status_id', [
                    OrderHelper::$followUpForAdvance,
                    OrderHelper::$proceedWithOutAdvance,
                    OrderHelper::$advanceRecieved,
                    OrderHelper::$prepaid,
                ]);
            } elseif ($page != 'ordered') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$followUpForAdvance,
                    OrderHelper::$proceedWithOutAdvance,
                    OrderHelper::$advanceRecieved,
                    OrderHelper::$prepaid,
                ]);
            }

            $orders = $orders->where('qty', '>=', 1)->where('o.id', '=', $request->order_id)->get();
        }

        if ($request->status[0] == null && $request->supplier[0] == null && $request->brand[0] == null && empty($request->order_id)) {
            $orders = OrderProduct::select(['order_products.sku', 'order_products.order_id', 'p.id'])
                ->join('orders as o', 'o.id', 'order_products.order_id')
                ->join('products as p', 'p.id', 'order_products.product_id');
            if ($page == 'canceled-refunded') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$cancel,
                    OrderHelper::$refundToBeProcessed,
                ]);
            } elseif ($page == 'delivered') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$delivered,
                ]);
            } elseif ($page == 'non_ordered') {
                $orders = $orders->whereNotIn('o.order_status_id', [
                    OrderHelper::$followUpForAdvance,
                    OrderHelper::$proceedWithOutAdvance,
                    OrderHelper::$advanceRecieved,
                    OrderHelper::$prepaid,
                ]);
            } elseif ($page != 'ordered') {
                $orders = $orders->whereIn('o.order_status_id', [
                    OrderHelper::$followUpForAdvance,
                    OrderHelper::$proceedWithOutAdvance,
                    OrderHelper::$advanceRecieved,
                    OrderHelper::$prepaid,
                ]);
            }

            $orders = $orders->where('qty', '>=', 1);
            if ($customerId > 0) {
                $orders = $orders->where('o.customer_id', $customerId);
            }

            $orders = $orders->get();
        }

        $new_orders = [];
        $includedOrders = [];
        foreach ($orders as $order) {
            array_push($new_orders, $order['id']);
            array_push($includedOrders, $order['order_id']);
        }

        $color = $request->get('color');
        $size = $request->get('size');
        $products = Product::with([
            'orderproducts' => function ($query) use ($page, $not_include_products, $includedOrders, $color, $size) {
                if ($page != 'ordered') {
                    $query->whereNotIn('id', $not_include_products);
                }
                $query->with([
                    'order' => function ($q) use ($includedOrders) {
                        $q->with('customer');
                        $q->whereIn('id', array_unique($includedOrders));
                    },
                ]);

                if (! empty($color) && is_array($color)) {
                    $query = $query->whereIn('color', $color);
                }

                if (! empty($size)) {
                    $query = $query->where('size', $size);
                }
            },
            'purchases',
            'suppliers',
            'brands',
        ])->whereIn('id', $new_orders);

        if ($page == 'ordered') {
            $products = $products->whereHas('purchases', function ($query) {
                $query->where('status', 'Ordered');
            });
        } else {
            $products = $products->whereNotIn('id', $ignoreSku);
        }

        $term = $request->input('term');
        $status = isset($status) ? $status : '';
        $supplier = isset($supplier) ? $supplier : '';
        $brand = isset($brand) ? $brand : '';
        $order_status = (new OrderStatus)->all();

        foreach ($order_status as $key => $value) {
            if (! $page) {
                if (! in_array($key, ['Follow up for advance', 'Proceed without Advance', 'Advance received', 'Prepaid'])) {
                    unset($order_status[$key]);
                }
            } else {
                if ($page == 'non_ordered') {
                    if (in_array($key, ['Follow up for advance', 'Proceed without Advance', 'Advance received', 'Prepaid'])) {
                        unset($order_status[$key]);
                    }
                }
            }
        }

        $supplier_list = (new SupplierList)->all();

        // $suppliers = DB::select('
        //   SELECT s.id, s.supplier
        //   FROM suppliers as s
        //   JOIN product_suppliers as ps on ps.supplier_id = s.id
        //   where
        //   ps.stock >= 1
        //   GROUP BY supplier_id');

        $suppliers = Supplier::select('suppliers.id', 'suppliers.supplier')
            ->join('product_suppliers as ps', 'ps.supplier_id', '=', 'suppliers.id')
            ->where('ps.stock', '>=', 1)
            ->groupBy('suppliers.id')
            ->get();

        $suppliers_array = [];
        foreach ($suppliers as $supp) {
            $suppliers_array[$supp->id] = $supp->supplier;
        }

        if (! empty($term)) {
            $products = $products->where(function ($query) use ($term) {
                return $query
                    ->orWhere('name', 'like', '%'.$term.'%')
                    ->orWhere('short_description', 'like', '%'.$term.'%')
                    ->orWhere('sku', 'like', '%'.$term.'%')
                    ->orWhere('supplier', 'like', '%'.$term.'%');
            });
        }

        if ($request->category_id != null && $request->category_id != 1) {
            $category_children = [];

            $is_parent = Category::isParent($request->category_id);

            if ($is_parent) {
                $childs = Category::find($request->category_id)->childs()->get();

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
                array_push($category_children, $request->category_id);
            }

            $products = $products->whereIn('category', $category_children);
        }

        $new_products = [];
        $products = $products->select(['id', 'sku', 'supplier', 'brand', 'category', 'price', 'price_inr'])->get()->sortBy('supplier');
        $count = 0;
        $productIds = [];
        foreach ($products as $key => $product) {
            $supplier_list = '';
            $single_supplier = '';
            $customers = [];
            $orderCount = 0;
            $sizeArr = [];
            foreach ($product->orderproducts as $key => $order_product) {
                if ($order_product->order && $order_product->order->customer) {
                    $customers[] = $order_product->order->customer;
                }

                if (! empty($order_product->order)) {
                    $orderCount++;
                    if (! empty($order_product->size)) {
                        $sizeArr[] = $order_product->size;
                    }
                }
            }

            if (! $orderCount) {
                continue;
            }

            $supplier_msg = PurchaseProductSupplier::select('suppliers.id', 'suppliers.supplier', 'chat_messages.id as chat_messages_id', 'chat_messages.message', 'chat_messages.created_at')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_product_supplier.supplier_id')
                ->leftJoin('chat_messages', 'chat_messages.id', '=', 'purchase_product_supplier.chat_message_id')
                ->where('purchase_product_supplier.product_id', '=', $product->id)
                ->orderByDesc('chat_messages.created_at')
                ->get();

            $supplier_msg_data = [];
            foreach ($supplier_msg as $key => $value) {
                $supplier_msg_data[$value->id]['supplier'] = $value->supplier;

                if (! empty($value->chat_messages_id)) {
                    $supplier_msg_data[$value->id]['chat_messages'][] = [
                        'message' => $value->message,
                        'created_at' => $value->created_at,
                    ];
                }
            }
            $productIds[] = $product->id;

            $new_products[$count]['id'] = $product->id;
            $new_products[$count]['sku'] = $product->sku;
            $new_products[$count]['price'] = $product->price;
            $new_products[$count]['price_inr'] = $product->price_inr;
            $new_products[$count]['supplier'] = $product->supplier;
            $new_products[$count]['supplier_list'] = $supplier_list;
            $new_products[$count]['single_supplier'] = $single_supplier;
            $new_products[$count]['brand'] = $product->brands ? $product->brands->name : 'No Brand';
            $new_products[$count]['brand_id'] = $product->brands ? $product->brands->id : '';
            $new_products[$count]['category'] = $product->category;
            $new_products[$count]['image'] = $product->getMedia(config('constants.media_tags'))->first() ? getMediaUrl($product->getMedia(config('constants.media_tags'))->first()) : '';
            $new_products[$count]['abs_img_url'] = $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getAbsolutePath() : '';
            $new_products[$count]['customer_id'] = ! empty($product->orderproducts->first()->order) ? (! empty($product->orderproducts->first()->order->customer) ? $product->orderproducts->first()->order->customer->id : 'No Customer') : 'No Order';
            $new_products[$count]['customers'] = $customers;
            $new_products[$count]['customer_names'] = '';
            $new_products[$count]['order_products'] = $product->orderproducts;
            $new_products[$count]['order_price'] = ! empty($product->orderproducts->first()->product_price) ? $product->orderproducts->first()->product_price : 0;
            $new_products[$count]['order_date'] = ! empty($product->orderproducts->first()->order) ? $product->orderproducts->first()->order->order_date : 'No Order';
            $new_products[$count]['order_advance'] = ! empty($product->orderproducts->first()->order) ? $product->orderproducts->first()->order->advance_detail : 'No Order';
            $new_products[$count]['supplier_msg'] = $supplier_msg_data;
            $new_products[$count]['size'] = implode(',', array_unique($sizeArr));

            $count++;
        }

        $new_products = array_values(Arr::sort($new_products, function ($value) {
            return $value['order_date'];
        }));

        $new_products = array_reverse($new_products);

        $suppliers_all = [];
        // $suppliersQuery = DB::select('SELECT sp.id FROM `scraped_products` sp
        //     join scrapers sc on sc.scraper_name =  sp.website
        //     JOIN suppliers s ON s.id=sc.supplier_id
        //     inner join order_products op on op.product_id = sp.product_id where last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)');
        $suppliersQuery = ScrapedProducts::select('scraped_products.id')
            ->join('scrapers AS sc', 'sc.scraper_name', '=', 'scraped_products.website')
            ->join('suppliers AS s', 's.id', '=', 'sc.supplier_id')
            ->join('order_products AS op', 'op.product_id', '=', 'scraped_products.product_id')
            ->where('last_inventory_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)'))
            ->get();

        $cnt = count($suppliersQuery);

        if ($cnt > 0 && ! empty($productIds)) {
            //     $suppliers_all = DB::select('SELECT id, supplier, product_id
            //   FROM suppliers
            //   INNER JOIN (
            //     SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
            //     ) as product_suppliers
            //   ON suppliers.id = product_suppliers.supplier_id
            //   LEFT JOIN purchase_product_supplier on purchase_product_supplier.supplier_id =suppliers.id and product_id in ( :product_id )', ['product_id' => implode(',', $productIds)]);
            $suppliers_all = Supplier::select('suppliers.id', 'suppliers.supplier', 'product_id')
                ->join(DB::raw('(SELECT supplier_id FROM product_suppliers GROUP BY supplier_id) AS product_suppliers'), function ($join) {
                    $join->on('suppliers.id', '=', 'product_suppliers.supplier_id');
                })
                ->leftJoin('purchase_product_supplier', function ($join) use ($productIds) {
                    $join->on('purchase_product_supplier.supplier_id', '=', 'suppliers.id')
                        ->whereIn('product_id', explode(',', $productIds));
                })
                ->get();

        }
        // $activSuppliers = DB::select('SELECT
        //                                 suppliers.id,
        //                                 supplier,
        //                                 "" as product_id
        //                             FROM
        //                                 suppliers
        //                             WHERE
        //                                 suppliers.status=1 and  deleted_at is null');
        $activSuppliers = Supplier::select('suppliers.id', 'supplier', DB::raw('"" as product_id'))
            ->where('suppliers.status', 1)
            ->whereNull('suppliers.deleted_at')
            ->get();
        if ($request->get('in_pdf') === 'on') {
            set_time_limit(0);

            $html = view('purchase.purchase-grid-pdf')->with([
                'products' => $new_products,
                'order_status' => $order_status,
                'supplier_list' => $supplier_list,
                'suppliers_array' => $suppliers_array,
                'suppliers_all' => $suppliers_all,
                'term' => $term,
                'status' => $status,
                'supplier' => $supplier,
                'brand' => $brand,
                'page' => $page,
            ]);

            $pdf = new Dompdf;
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream('orders.pdf');

            return $pdf;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($new_products, $perPage * ($currentPage - 1), $perPage);

        $totalSku = count($new_products);
        $new_products = new LengthAwarePaginator($currentItems, count($new_products), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2'])->selected(1)->renderAsDropdown();
        $categoryFilter = Category::attr(['name' => 'category_id', 'class' => 'form-control select-multiple2'])->selected(request()->get('category_id', 1))->renderAsDropdown();

        $suppliers = Supplier::select(['id', 'supplier'])->whereIn('id', ProductSupplier::selectRaw('DISTINCT(`supplier_id`) as suppliers')->pluck('suppliers')->toArray())->get();

        return view('purchase.purchase-grid')->with([
            'products' => $new_products,
            'order_status' => $order_status,
            'supplier_list' => $supplier_list,
            'suppliers_array' => $suppliers_array,
            'suppliers_all' => $suppliers_all,
            'term' => $term,
            'status' => $status,
            'supplier' => $supplier,
            'brand' => $brand,
            'page' => $page,
            'category_selection' => $category_selection,
            'activSuppliers' => $activSuppliers,
            'categoryFilter' => $categoryFilter,
            'suppliers' => $suppliers,
            'totalSku' => $totalSku,
        ]);
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

    public function export(Request $request)
    {
        $selected_purchases = json_decode($request->selected_purchases);

        foreach ($selected_purchases as $purchase_id) {
            $purchase = Purchase::find($purchase_id);
            $purchase->status = 'Request Sent to Supplier';
            $purchase->save();
        }

        $path = 'purchase_exports/'.Carbon::now()->format('Y-m-d-H-m-s').'_purchases_export.xlsx';

        Excel::store(new PurchasesExport($selected_purchases), $path, 'files');

        return Storage::disk('files')->download($path);
    }

    public function sendExport(Request $request): RedirectResponse
    {
        $path = 'purchase_exports/'.Carbon::now()->format('Y-m-d-H-m-s').'_purchases_export.xlsx';
        $filename = Carbon::now()->format('Y-m-d-H-m-s').'_purchases_export.xlsx';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file->storeAs('purchase_exports', $filename, 'files');
        }

        $first_agent_email = '';
        $cc_agents_emails = [];
        foreach ($request->agent_id as $key => $agent_id) {
            $agent = Agent::find($agent_id);

            if ($key == 0) {
                $first_agent_email = $agent->email;
            } else {
                $cc_agents_emails[] = $agent->email;
            }
        }

        (new PurchaseExport($path, $request->subject, $request->message))->build();
        $from_email = Helpers::getFromEmail();
        $email = Email::create([
            'model_id' => $request->supplier_id,
            'model_type' => Supplier::class,
            'from' => $from_email,
            'to' => $first_agent_email,
            'subject' => $request->subject,
            'message' => $request->message,
            'template' => 'purchase-simple',
            'additional_data' => json_encode(['attachment' => $path]),
            'status' => 'pre-send',
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return redirect()->back()->withSuccess('You have successfully sent an email!');
    }

    public function downloadFile(Request $request, $id)
    {
        $file = File::find($id);

        return Storage::disk('files')->download('files/'.$file->filename);
    }

    public function downloadAttachments(Request $request)
    {
        return Storage::disk('files')->download($request->path);
    }

    public function merge(Request $request): RedirectResponse
    {
        $selected_purchases = json_decode($request->selected_purchases);

        foreach ($selected_purchases as $key => $purchase_id) {
            if ($key == 0) {
                $main_purchase = Purchase::find($purchase_id);
            } else {
                $merging_purchase = Purchase::find($purchase_id);

                if ($main_purchase->transaction_amount == '' || $main_purchase->shipment_cost == '') {
                    $main_purchase->transaction_id = $merging_purchase->transaction_id;
                    $main_purchase->transaction_date = $merging_purchase->transaction_date;
                    $main_purchase->transaction_amount = $merging_purchase->transaction_amount;
                    $main_purchase->bill_number = $merging_purchase->bill_number;
                    $main_purchase->shipper = $merging_purchase->shipper;
                    $main_purchase->shipment_status = $merging_purchase->shipment_status;
                    $main_purchase->shipment_cost = $merging_purchase->shipment_cost;
                    $main_purchase->save();
                }

                foreach ($merging_purchase->purchaseProducts as $product) {
                    $purchaseProducts = new PurchaseProduct;
                    $purchaseProducts->purchase_id = $main_purchase->id;
                    $purchaseProducts->product_id = $product->product_id;
                    $purchaseProducts->order_product_id = $product->order_product_id;
                    $purchaseProducts->save();
                }

                $merging_purchase->products()->detach();

                $remarks = Remark::where('taskid', $merging_purchase->id)->where('module_type', 'purchase-product-remark')->get();

                foreach ($remarks as $remark) {
                    $remark->taskid = $main_purchase->id;
                    $remark->save();
                }

                $purchase_discounts = PurchaseDiscount::where('purchase_id', $merging_purchase->id)->get();

                foreach ($purchase_discounts as $discount) {
                    $discount->purchase_id = $main_purchase->id;
                    $discount->save();
                }

                $merging_purchase->delete();
            }
        }

        return redirect()->route('purchase.index')->with('success', 'You have successfully merged purchases');
    }

    public function assignBatch(Request $request, $id): RedirectResponse
    {
        $purchase = Purchase::find($id);

        if ($purchase->products) {
            foreach ($purchase->products as $product) {
                if ($product->orderproducts) {
                    foreach ($product->orderproducts as $order_product) {
                        $order_product->purchase_id = $id;
                        $order_product->batch_number = '';
                        $order_product->save();
                    }
                }
            }
        }

        return redirect()->route('purchase.show', $id)->withSuccess('You have successfully assigned a batch number!');
    }

    public function assignSplitBatch(Request $request, $id): RedirectResponse
    {
        $max_batch_number = OrderProduct::where('purchase_id', $id)->latest('batch_number')->first();

        if ($max_batch_number) {
            foreach (json_decode($request->order_products) as $order_product_id) {
                $order_product = OrderProduct::find($order_product_id);
                $order_product->purchase_id = $id;
                $order_product->batch_number = (int) $max_batch_number->batch_number + 1;
                $order_product->save();
            }
        } else {
            foreach (json_decode($request->order_products) as $order_product_id) {
                $order_product = OrderProduct::find($order_product_id);
                $order_product->purchase_id = $id;
                $order_product->batch_number = 1;
                $order_product->save();
            }
        }

        return redirect()->route('purchase.show', $id)->withSuccess('You have successfully assigned a batch number!');
    }

    public function calendar(): View
    {
        $order_products = OrderProduct::whereNotNull('shipment_date')->get();
        $purchase_data = [];

        foreach ($order_products as $order_product) {
            if ($order_product->order && $order_product->order->customer) {
                $purchase_data[] = [
                    'customer_id' => $order_product->order->customer->id,
                    'order_product_id' => $order_product->id,
                    'customer_name' => $order_product->order->customer->name,
                    'customer_city' => $order_product->order->customer->city,
                    'shipment_date' => $order_product->shipment_date,
                    'product_name' => $order_product->product->name,
                    'reschedule_count' => $order_product->reschedule_count,
                    'is_order_priority' => $order_product->order->is_priority,
                ];
            }
        }

        $vendorArray = Vendor::all()->pluck('name', 'id')->toArray();

        return view('purchase.calendar', [
            'purchase_data' => $purchase_data,
            'vendorArray' => $vendorArray,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $postOP = json_decode($request->order_products, true);
        $supplierWiseProducts = [];

        if (! empty($postOP)) {
            foreach ($postOP as $post) {
                @[$opId, $supplierId] = explode('#', $post);
                $supplierId = ! empty($supplierId) ? $supplierId : 0;
                $supplierWiseProducts[$supplierId][] = $opId;
            }
        }

        if (! empty($supplierWiseProducts)) {
            foreach ($supplierWiseProducts as $productList) {
                // assing purchase supllier wise
                $purchase = new Purchase;
                $purchase->purchase_handler = $request->purchase_handler;
                $purchase->supplier_id = $request->supplier_id;
                $purchase->status = 'Pending Purchase';

                // now store the order products
                if ($purchase->save()) {
                    // find all order products
                    $orderProducts = OrderProduct::whereIn('id', $productList)->get();

                    if (! $orderProducts->isEmpty()) {
                        foreach ($orderProducts as $orderProduct) {
                            PurchaseProduct::insert([
                                'purchase_id' => $purchase->id,
                                'product_id' => $orderProduct->product->id,
                                'order_product_id' => $orderProduct->id,
                            ]);

                            $orderProduct->purchase_status = 'Pending Purchase';
                            $orderProduct->save();
                        }
                    }
                    // storing in product end
                }
            }
        }

        return redirect()->route('purchase.index');
    }

    public function updateDelivery(Request $request, $id): Response
    {
        $order_product = OrderProduct::find($id);
        $order_product->shipment_date = $request->shipment_date;
        $order_product->reschedule_count += 1;
        $order_product->save();

        if (! $order_product->is_delivery_date_changed()) {
            // Customer Message
            $params = [
                'number' => null,
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
            ];

            if ($order_product->private_view) {
                $delivery_date = Carbon::parse($order_product->shipment_date)->format('d \of\ F');
                $product_name = $order_product->product->name;
                $params['customer_id'] = $order_product->private_view->customer_id;
                $params['message'] = "Your product $product_name delivery time has been rescheduled. It will be delivered on $delivery_date";

                ChatMessage::create($params);
            }

            CommunicationHistory::create([
                'model_id' => $order_product->id,
                'model_type' => OrderProduct::class,
                'type' => 'order-delivery-date-changed',
                'method' => 'whatsapp',
            ]);
        }

        return response('success');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $purchase = Purchase::find($id);
        $data['emails'] = [];
        $data['comments'] = Comment::with('user')->where('subject_id', $purchase->id)
            ->where('subject_type', '=', Order::class)->get();
        $data['users'] = User::all()->toArray();
        $messages = Message::all()->where('moduleid', $purchase->id)->where('moduletype', '=', 'purchase')->sortByDesc('created_at')->take(10)->toArray();
        $data['messages'] = $messages;
        $data['tasks'] = Task::where('model_type', 'purchase')->where('model_id', $purchase->id)->get()->toArray();
        $data['approval_replies'] = Reply::where('model', 'Approval Purchase')->get();
        $data['internal_replies'] = Reply::where('model', 'Internal Purchase')->get();
        $data['purchase_status'] = (new PurchaseStatus)->all();
        $data['reply_categories'] = ReplyCategory::all();
        $data['suppliers'] = Supplier::all();
        $data['purchase_discounts'] = PurchaseDiscount::where('purchase_id', $id)->where('type', 'product')->latest()->take(3)->get()->groupBy([
            function ($query) {
                return Carbon::parse($query->created_at)->format('Y-m-d H:i:s');
            },
            'product_id',
        ]);

        $data['purchase_discounts_rest'] = PurchaseDiscount::where('purchase_id', $id)->where('type', 'product')->latest()->skip(3)->take(30)->get()->groupBy([
            function ($query) {
                return Carbon::parse($query->created_at)->format('Y-m-d H:i:s');
            },
            'product_id',
        ]);

        $data['agents_array'] = [];
        $agents = Agent::all();

        foreach ($agents as $agent) {
            $data['agents_array'][$agent->model_id][$agent->id] = $agent->name.' - '.$agent->email;
        }
        $data['users'] = User::all();

        return view('purchase.show', $data)->withOrder($purchase);
    }

    public function productShow($id): View
    {
        $product = Product::find($id);

        $data['users'] = User::all()->toArray();
        $messages = Message::all()->where('moduleid', $product->id)->where('moduletype', '=', 'product')->sortByDesc('created_at')->take(10)->toArray();
        $data['messages'] = $messages;
        $data['approval_replies'] = Reply::where('model', 'Approval Purchase')->get();
        $data['internal_replies'] = Reply::where('model', 'Internal Purchase')->get();
        $data['order_details'] = OrderProduct::where('sku', $product->sku)->get(['order_id', 'size']);
        $data['media_tags'] = config('constants.media_tags');

        return view('purchase.product-show', $data)->withProduct($product);
    }

    public function productReplace(Request $request): RedirectResponse
    {
        $old_product = Product::find($request->moduleid);
        $new_product = Product::find(json_decode($request->images)[0]);

        foreach ($old_product->purchases as $purchase) {
            $purchase->products()->detach($old_product);
            $purchase->products()->attach($new_product);
        }

        foreach ($old_product->orderproducts as $order_product) {
            $new_order = new OrderProduct;
            $new_order->order_id = $order_product->order_id;
            $new_order->sku = $new_product->sku;
            $new_order->product_id = $new_product->id;
            $new_order->product_price = $new_product->price_inr_special;
            $new_order->size = $order_product->size;
            $new_order->color = $order_product->color;
            $new_order->purchase_status = 'Pending Purchase';
            $new_order->save();

            $order_product->purchase_status = 'Replaced';
            $order_product->save();
        }

        PurchaseDiscount::where('product_id', $old_product->id)->delete();

        return redirect()->route('purchase.index')->with('success', 'You have successfully replaced product!');
    }

    public function productRemove(Request $request, $id): RedirectResponse
    {
        $product = Product::find($id);
        $purchase = Purchase::find($request->purchase_id);

        $purchase->products()->detach($product);

        PurchaseDiscount::where('product_id', $id)->delete();

        return redirect()->route('purchase.show', $request->purchase_id)->with('success', 'You have successfully removed product!');
    }

    public function productCreateReplace(ProductCreateReplacePurchaseRequest $request): RedirectResponse
    {

        $product = new Product;

        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->size = $request->size;
        $product->brand = $request->brand;
        $product->color = $request->color;
        $product->supplier = $request->supplier;
        $product->price = $request->price;

        $brand = Brand::find($request->brand);

        if ($request->price) {
            if (isset($request->brand) && ! empty($brand->euro_to_inr)) {
                $product->price_inr = $brand->euro_to_inr * $product->price;
            } else {
                $product->price_inr = Setting::get('euro_to_inr') * $product->price;
            }

            $product->price_inr = round($product->price_inr, -3);
            $product->price_inr_special = $product->price_inr - ($product->price_inr * $brand->deduction_percentage) / 100;

            $product->price_inr_special = round($product->price_inr_special, -3);
        }

        $product->save();

        $product->detachMediaTags(config('constants.media_tags'));
        $media = MediaUploader::fromSource($request->file('image'))
            ->toDirectory('product/'.floor($product->id / config('constants.image_per_folder')))
            ->upload();
        $product->attachMedia($media, config('constants.media_tags'));

        $old_product = Product::find($request->product_id);

        foreach ($old_product->purchases as $purchase) {
            $purchase->products()->detach($old_product);
            $purchase->products()->attach($product);
        }

        foreach ($old_product->orderproducts as $order_product) {
            $new_order = new OrderProduct;
            $new_order->order_id = $order_product->order_id;
            $new_order->sku = $product->sku;
            $new_order->product_price = $product->price_inr_special;
            $new_order->size = $order_product->size;
            $new_order->color = $order_product->color;
            $new_order->purchase_status = 'Pending Purchase';
            $new_order->save();

            $order_product->purchase_status = 'Replaced';
            $order_product->save();
        }

        PurchaseDiscount::where('product_id', $old_product->id)->delete();

        return redirect()->back()->with('success', 'You have successfully created and replaced product!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    public function updateStatus(Request $request, $id): Response
    {
        $purchase = Purchase::find($id);

        StatusChange::create([
            'model_id' => $purchase->id,
            'model_type' => Purchase::class,
            'user_id' => Auth::id(),
            'from_status' => $purchase->status,
            'to_status' => $request->status,
        ]);

        $purchase->status = $request->status;
        $purchase->save();

        if ($request->status == 'Shipment Received in Dubai') {
            $product_names = '';

            if ($purchase->products) {
                foreach ($purchase->products as $product) {
                    $supplier = Supplier::where('supplier', 'In-stock')->first();

                    $product->supplier = 'In-stock';
                    $product->location = 'Dubai';
                    $product->save();

                    $product->suppliers()->syncWithoutDetaching($supplier);

                    $product_names .= "$product->name, ";
                }
            }

            if (! $purchase->is_sent_in_dubai()) {
                // Making task for Yogesh
                $data = [
                    'task_subject' => 'Shipment to India',
                    'task_details' => "Please arrange shipment for India - ID $purchase->id",
                    'is_statutory' => 0,
                    'assign_from' => Auth::id(),
                    'assign_to' => 6,
                    'category' => 12,
                ];

                $task = Task::create($data);

                $task->users()->attach([6 => ['type' => User::class]]);

                // Message to Carier
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => "These pcs: $product_names are available for shipment to India - confirm if urgency needed to drop for faster transit",
                    'approved' => 0,
                    'status' => 1,
                ];

                $stock_coordinators = User::role('Stock Coordinator')->get();

                foreach ($stock_coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }

                CommunicationHistory::create([
                    'model_id' => $purchase->id,
                    'model_type' => Purchase::class,
                    'type' => 'purchase-in-dubai',
                    'method' => 'whatsapp',
                ]);
            }
        }

        $product_information = '';
        $letters_array = [
            '1' => 'A',
            '2' => 'B',
            '3' => 'C',
            '4' => 'D',
            '5' => 'E',
            '6' => 'F',
            '7' => 'G',
        ];

        if ($request->status == 'Shipment in Transit from Dubai to India') {
            if (! $purchase->is_sent_dubai_to_india()) {
                $product_names = '';

                if ($purchase->products) {
                    foreach ($purchase->products as $key => $product) {
                        $product_names .= "$product->name - ";

                        if ($key == 0) {
                            $product_information .= "$product->name - Size $product->size - $product->color";
                        } else {
                            $product_information .= ", $product->name - Size $product->size - $product->color";
                        }

                        if ($product->orderproducts) {
                            foreach ($product->orderproducts as $order_product) {
                                $batch_number = $order_product->purchase_id.(array_key_exists($order_product->batch_number, $letters_array) ? $letters_array[$order_product->batch_number] : '');
                                $product_names .= "#$batch_number, ";

                                if ($order_product->order && $order_product->order->customer) {
                                    $product_information .= $order_product->order->customer->address.', '.$order_product->order->customer->pincode.', '.$order_product->order->customer->city.'; ';
                                }
                            }
                        }
                    }
                }

                // Message to Stock Coordinator
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => "These pcs: $product_names are expected to arrive in India - x + 2 days -pls. coordinate and arrange collection",
                    'approved' => 0,
                    'status' => 1,
                ];

                $stock_coordinators = User::role('Stock Coordinator')->get();

                foreach ($stock_coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }

                // Message to Delivery Coordinator
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => "This: $product_information are expected to arrive in India - x + 2 days to you. - for delivery to the follow customers pls. coordinate",
                    'approved' => 0,
                    'status' => 1,
                ];

                $coordinators = User::role('Delivery Coordinator')->get();

                foreach ($coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }

                CommunicationHistory::create([
                    'model_id' => $purchase->id,
                    'model_type' => Purchase::class,
                    'type' => 'purchase-dubai-to-india',
                    'method' => 'whatsapp',
                ]);
            }
        }

        if ($request->status == 'Shipment Received in India') {
            if ($purchase->products && ! $purchase->is_sent_in_mumbai()) {
                foreach ($purchase->products as $product) {
                    $supplier = Supplier::where('supplier', 'In-stock')->first();

                    $product->location = 'Mumbai';
                    $product->save();

                    $product->suppliers()->syncWithoutDetaching($supplier);

                    if ($product->orderproducts) {
                        $params = [
                            'number' => null,
                            'user_id' => Auth::id(),
                            'approved' => 0,
                            'status' => 1,
                            'message' => 'Your Order is received in India',
                        ];

                        foreach ($product->orderproducts as $order_product) {
                            if ($order_product->order && $order_product->order->customer) {
                                $params['customer_id'] = $order_product->order->customer->id;

                                ChatMessage::create($params);

                                // Creating inventory for Aliya
                                $private_view = new PrivateView;
                                $private_view->customer_id = $order_product->order->customer->id;
                                $private_view->date = Carbon::now()->addDays(3);
                                $private_view->save();

                                $private_view->products()->attach($product);
                            }
                        }
                    }
                }

                CommunicationHistory::create([
                    'model_id' => $purchase->id,
                    'model_type' => Purchase::class,
                    'type' => 'purchase-in-mumbai',
                    'method' => 'whatsapp',
                ]);

                // Message to Aliya about time ?
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => 'Orders are in India, please coordinate',
                    'approved' => 0,
                    'status' => 1,
                ];

                $coordinators = User::role('Delivery Coordinator')->get();

                foreach ($coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }

                // Message to Stock Holder
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => 'Confirm Aliyas time if it is ok to hand over the products',
                    'approved' => 0,
                    'status' => 1,
                ];

                $stock_coordinators = User::role('Stock Coordinator')->get();

                foreach ($stock_coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }
            }
        }

        foreach ($purchase->products as $product) {
            foreach ($product->orderproducts as $order_product) {
                if ($request->status != $order_product->purchase_status) {
                    StatusChange::create([
                        'model_id' => $order_product->id,
                        'model_type' => OrderProduct::class,
                        'user_id' => Auth::id(),
                        'from_status' => $order_product->purchase_status,
                        'to_status' => $request->status,
                    ]);
                }

                $order_product->purchase_status = $request->status;
                $order_product->save();
            }

            $product->purchase_status = $purchase->status;
            $product->save();
        }

        return response($purchase->status);
    }

    public function updateProductStatus(Request $request, $id): Response
    {
        $product = Product::find($request->product_id);
        $product->purchase_status = $request->status;
        $product->save();

        $params = [
            'number' => null,
            'user_id' => Auth::id(),
            'approved' => 0,
            'status' => 1,
            'message' => 'Your Product is not available with the Supplier. Please choose alternative',
        ];

        foreach ($product->purchases as $purchase) {
            if ($purchase->id == $id) {
                foreach ($purchase->products as $related_product) {
                    if ($related_product->id == $product->id) {
                        foreach ($product->orderproducts as $order_product) {
                            if ($order_product->order) {
                                $params['customer_id'] = $order_product->order->customer->id;

                                ChatMessage::create($params);
                            }
                        }
                    }
                }
            }
        }

        return response('success');
    }

    public function updatePercentage(Request $request, $id): Response
    {
        foreach ($request->percentages as $percentage) {
            $product = Product::find($percentage[0]);
            $product->percentage = $percentage[1];
            $product->save();

            PurchaseDiscount::create([
                'purchase_id' => $request->purchase_id,
                'product_id' => $percentage[0],
                'percentage' => $percentage[1],
                'amount' => $request->amount,
                'type' => $request->type,
            ]);
        }

        $purchase = Purchase::find($request->purchase_id);
        $purchase->status = 'Price under Negotiation';
        $purchase->save();

        return response('success');
    }

    public function saveBill(Request $request, $id): JsonResponse
    {
        $purchase = Purchase::find($id);
        $purchase->supplier_id = $request->supplier;
        $purchase->agent_id = $request->agent_id;
        $purchase->transaction_id = $request->transaction_id;
        $purchase->transaction_date = $request->transaction_date;
        $purchase->transaction_amount = $request->transaction_amount;
        $purchase->bill_number = $request->bill_number;
        $purchase->shipper = $request->shipper;
        $purchase->shipment_cost = $request->shipment_cost;
        $purchase->shipment_date = $request->shipment_date;
        $purchase->shipment_status = $request->shipment_status;
        $purchase->supplier_phone = $request->supplier_phone;
        $purchase->whatsapp_number = $request->whatsapp_number;

        if ($request->bill_number != '') {
            $purchase->status = 'AWB Details Received';
        }

        if ($request->transaction_date != '') {
            if (! $purchase->is_sent_awb_actions()) {
                // Task to Sushil
                $data = [
                    'task_subject' => 'Purchase Delivery',
                    'task_details' => "Please Follow up with Purchase Delivery - ID $purchase->id",
                    'is_statutory' => 0,
                    'assign_from' => Auth::id(),
                    'assign_to' => 7,
                    'category' => 12,
                ];

                $task = Task::create($data);

                $task->users()->attach([7 => ['type' => User::class]]);

                // Message to Yogesh
                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'approved' => 1,
                    'status' => 2,
                    'erp_user' => 6,
                    'message' => "Products from Purchase ID $purchase->id are in transit",
                ];

                ChatMessage::create($params);

                $delivery_information = '';
                foreach ($purchase->products as $product) {
                    if ($product->orderproducts) {
                        foreach ($product->orderproducts as $order_product) {
                            // Update Order Product Details
                            $order_product->shipment_date = Carbon::parse($request->transaction_date)->addDays(12);
                            $order_product->save();

                            if ($order_product->order && $order_product->order->customer) {
                                $shipment_days = Carbon::parse($order_product->shipment_date)->diffInDays(Carbon::now());
                                $params['customer_id'] = $order_product->order->customer->id;
                                $params['message'] = "Your product $product->name has been shipped from our Italy office and is expected to be delivered to you in $shipment_days days - account for weekend and holiday";

                                // Aliya message details
                                $customer_city = $order_product->order->customer->city;
                                $customer_name = $order_product->order->customer->name;
                                $delivery_information .= "$customer_city - $product->name for $customer_name; ";

                                // Creating inventory for Aliya
                                $private_view = new PrivateView;
                                $private_view->customer_id = $order_product->order->customer->id;
                                $private_view->order_product_id = $order_product->id;
                                $private_view->date = Carbon::parse($order_product->shipment_date)->addDays(10);
                                $private_view->save();

                                $private_view->products()->attach($product);
                            }
                        }
                    }
                }

                // throw new \Exception($delivery_information);

                $params = [
                    'number' => null,
                    'user_id' => Auth::id(),
                    'message' => "These are the shipments that need to be delivered in the next 12 days and please ensure office boys are allocated and all travel bookings are made $delivery_information",
                    'approved' => 0,
                    'status' => 1,
                ];

                $coordinators = User::role('Delivery Coordinator')->get();

                foreach ($coordinators as $coordinator) {
                    $params['erp_user'] = $coordinator->id;
                    $chat_message = ChatMessage::create($params);

                    $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                    // throw new \Exception($coordinator->id);

                    app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                    $chat_message->update([
                        'approved' => 1,
                        'status' => 2,
                    ]);
                }

                CommunicationHistory::create([
                    'model_id' => $id,
                    'model_type' => Purchase::class,
                    'type' => 'purchase-awb-generated',
                    'method' => 'whatsapp',
                ]);
            }
        }

        $purchase->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $original_name = $file->getClientOriginalName();
                $filename = pathinfo($original_name, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                $full_name = $filename.'.'.$extension;

                $file->storeAs('files', $full_name, 'files');

                $new_file = new File;
                $new_file->filename = $full_name;
                $new_file->model_id = $id;
                $new_file->model_type = Purchase::class;
                $new_file->save();
            }
        }

        return response()->json(['data' => $request->all()]);
    }

    public function confirmProforma(Request $request, $id): JsonResponse
    {
        $purchase = Purchase::find($id);
        $matched = 0;
        $total_amount = 0;
        foreach ($request->proformas as $data) {
            $product = Product::find($data[0]);
            $discounted_price = round(($product->price - ($product->price * $product->percentage / 100)) / 1.22);
            $proforma = $data[1];
            $total_amount += $proforma;
            if (($proforma - $discounted_price) < 10) {
                $matched++;
            }
        }
        if ($matched == count($request->proformas)) {
            $purchase->proforma_confirmed = 1;
            $purchase->proforma_id = $request->proforma_id;
            $purchase->proforma_date = $request->proforma_date;

            $purchase->status = 'Price Confirmed - Payment in Process';
            $purchase->save();
            event(new ProformaConfirmed($purchase, $total_amount));
        }

        return response()->json([
            'proforma_confirmed' => $purchase->proforma_confirmed,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $purchase = Purchase::find($id);

        $purchase->delete();

        return redirect()->route('purchase.index')->with('success', 'Purchase has been archived');
    }

    public function permanentDelete($id): RedirectResponse
    {
        $purchase = Purchase::find($id);

        $purchase->products()->detach();
        $purchase->forceDelete();

        return redirect()->route('purchase.index')->with('success', 'Purchase has been deleted');
    }

    public function getOrderProductsWithProductData($order_id)
    {
        $orderProducts = OrderProduct::where('order_id', '=', $order_id)->get()->toArray();
        $temp = [];
        foreach ($orderProducts as $key => $value) {
            if (! empty($orderProducts[$key]['color'])) {
                $temp = Product::where('sku', '=', $orderProducts[$key]['sku'])
                    ->where('color', $orderProducts[$key]['color'])->whereNotNull('supplier_link')
                    ->get()->first();
            } else {
                $temp = Product::where('sku', '=', $orderProducts[$key]['sku'])->whereNotNull('supplier_link')
                    ->get()->first();
            }

            if (! empty($temp)) {
                $orderProducts[$key]['product'] = $temp;
                $orderProducts[$key]['product']['image'] = $temp->getMedia(config('constants.media_tags'))->first() ? getMediaUrl($temp->getMedia(config('constants.media_tags'))->first()) : '';
            }
        }

        return $temp;
    }

    // EMAIL INBOX

    public function emailInbox(Request $request)
    {
        try {
            $cm = new ClientManager;
            $imap = $cm->make([
                'host' => config('settings.imap_host_purchase'),
                'port' => config('settings.imap_port_purchase'),
                'encryption' => config('settings.imap_encryption_purchase'),
                'validate_cert' => config('settings.imap_validate_cert_purchase'),
                'username' => config('settings.imap_username_purchase'),
                'password' => config('settings.imap_password_purchase'),
                'protocol' => config('settings.imap_protocol_purchase'),
            ]);

            $imap->connect();
            if ($request->supplier_id) {
                $supplier = Supplier::find($request->supplier_id);

                if ($request->type == 'inbox') {
                    $inbox_name = 'INBOX';
                    $direction = 'from';
                    $type = 'incoming';
                } else {
                    $inbox_name = 'INBOX.Sent';
                    $direction = 'to';
                    $type = 'outgoing';
                }

                $inbox = $imap->getFolder($inbox_name);

                $latest_email = Email::where('type', $type)->where('model_id', $supplier->id)->where(function ($query) {
                    $query->where('model_type', Supplier::class)->orWhere('model_type', Purchase::class);

                })->latest()->first();

                $latest_email_date = $latest_email
                    ? Carbon::parse($latest_email->created_at)
                    : Carbon::parse('1990-01-01');

                $supplierAgentsCount = $supplier->agents()->count();

                if ($supplierAgentsCount == 0) {
                    $emails = $inbox->messages()->where($direction, $supplier->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                    $emails = $emails->leaveUnread()->get();
                    $this->createEmailsForEmailInbox($supplier, $type, $latest_email_date, $emails);
                } else {
                    if ($supplierAgentsCount == 1) {
                        $emails = $inbox->messages()->where($direction, $supplier->agents[0]->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                        $emails = $emails->leaveUnread()->get();

                        $this->createEmailsForEmailInbox($supplier, $type, $latest_email_date, $emails);
                    } else {
                        foreach ($supplier->agents as $key => $agent) {
                            if ($key == 0) {
                                $emails = $inbox->messages()->where($direction, $agent->email)->where([
                                    ['SINCE', $latest_email_date->format('d M y H:i')],
                                ]);
                                $emails = $emails->leaveUnread()->get();
                                $this->createEmailsForEmailInbox($supplier, $type, $latest_email_date, $emails);
                            } else {
                                $additional = $inbox->messages()->where($direction, $agent->email)->since(Carbon::parse($latest_email_date)->format('Y-m-d H:i:s'));
                                $additional = $additional->leaveUnread()->get();
                                $this->createEmailsForEmailInbox($supplier, $type, $latest_email_date, $additional);
                            }
                        }
                    }
                }

                $db_emails = $supplier->emails()->with('model')->where('type', $type)->get();

                $emails_array = [];
                $count = 0;
                foreach ($db_emails as $key2 => $email) {
                    $dateCreated = $email->created_at->format('D, d M Y');
                    $timeCreated = $email->created_at->format('H:i');
                    $userName = null;
                    if ($email->model instanceof Supplier) {
                        $userName = $email->model->supplier;
                    } elseif ($email->model instanceof Customer) {
                        $userName = $email->model->name;
                    }
                    if ($email->model_type == Supplier::class) {
                        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

                        if (array_key_exists('attachment', $array)) {
                            $attachment = json_decode($email->additional_data, true)['attachment'];
                            if (is_array($attachment)) {
                                foreach ($attachment as $attach) {
                                    $filename = explode('/', $attach);
                                    $filename = explode('.', end($filename));
                                    if (end($filename) == 'xlsx' || end($filename) == 'xls') {
                                        $log = LogExcelImport::where('supplier_email', $supplier->email)->where('filename', $filename[0])->first();
                                        if ($log != null) {
                                            if ($log->status == 1) {
                                                $alert[] = 'Excel import process';
                                            } elseif ($log->status == 2) {
                                                $alert[] = 'Excel import created';
                                            } elseif ($log->status == 0) {
                                                $alert[] = 'Excel import error';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (! isset($alert)) {
                        $alert = [];
                    }
                    $emails_array[$count + $key2]['id'] = $email->id;
                    $emails_array[$count + $key2]['subject'] = $email->subject;
                    $emails_array[$count + $key2]['seen'] = $email->seen;
                    $emails_array[$count + $key2]['type'] = $email->type;
                    $emails_array[$count + $key2]['date'] = $email->created_at;
                    $emails_array[$count + $key2]['from'] = $email->from;
                    $emails_array[$count + $key2]['to'] = $email->to;
                    $emails_array[$count + $key2]['message'] = $email->message;
                    $emails_array[$count + $key2]['cc'] = $email->cc;
                    $emails_array[$count + $key2]['bcc'] = $email->bcc;
                    $emails_array[$count + $key2]['alert'] = $alert;
                    $emails_array[$count + $key2]['replyInfo'] = "On {
                $dateCreated} at {
                $timeCreated}, $userName <{
                $email->from}> wrote:";
                    $emails_array[$count + $key2]['dateCreated'] = $dateCreated;
                    $emails_array[$count + $key2]['timeCreated'] = $timeCreated;
                }

                $emails_array = array_values(Arr::sort($emails_array, function ($value) {
                    return $value['date'];
                }));

                $emails_array = array_reverse($emails_array);

                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);
                $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage);

                $view = view('purchase.partials.email', ['emails' => $emails, 'type' => $request->type])->render();

                return response()->json(['emails' => $view]);
            } else {
                return response()->json(['message' => 'Something went wrong!'], 422);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!'], 422);
        }
    }

    private function createEmailsForEmailInbox($supplier, $type, $latest_email_date, $emails)
    {
        foreach ($emails as $email) {
            $content = $email->hasHTMLBody() ? $email->getHTMLBody() : $email->getTextBody();

            if ($email->getDate()->format('Y-m-d H:i:s') > $latest_email_date->format('Y-m-d H:i:s')) {
                $attachments_array = [];
                $attachments = $email->getAttachments();

                $attachments->each(function ($attachment) use (&$attachments_array) {
                    file_put_contents(storage_path('app/files/email-attachments/'.$attachment->name), $attachment->content);
                    $path = 'email-attachments/'.$attachment->name;
                    $attachments_array[] = $path;
                });

                $params = [
                    'model_id' => $supplier->id,
                    'model_type' => Supplier::class,
                    'type' => $type,
                    'seen' => $email->getFlags()['seen'],
                    'from' => $email->getFrom()[0]->mail,
                    'to' => array_key_exists(0, $email->getTo()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                    'subject' => $email->getSubject(),
                    'message' => $content,
                    'template' => 'customer-simple',
                    'additional_data' => json_encode(['attachment' => $attachments_array]),
                    'created_at' => $email->getDate(),
                ];

                Email::create($params);
            }
        }
    }

    public function emailFetch(Request $request): JsonResponse
    {
        $imap = new Client([
            'host' => config('settings.imap_host_purchase'),
            'port' => config('settings.imap_port_purchase'),
            'encryption' => config('settings.imap_encryption_purchase'),
            'validate_cert' => config('settings.imap_validate_cert_purchase'),
            'username' => config('settings.imap_username_purchase'),
            'password' => config('settings.imap_password_purchase'),
            'protocol' => config('settings.imap_protocol_purchase'),
        ]);

        $imap->connect();

        if ($request->type == 'inbox') {
            $inbox = $imap->getFolder('INBOX');
        } else {
            $inbox = $imap->getFolder('INBOX.Sent');
            $inbox->query();
        }

        if ($request->email_type == 'server') {
            $email = $inbox->getMessage($request->uid, null, null, true, true, true);
            if ($email->hasHTMLBody()) {
                $content = $email->getHTMLBody();
            } else {
                $content = $email->getTextBody();
            }

            $attachments = $email->getAttachments();

            $attachments->each(function ($attachment) use (&$content) {
                file_put_contents(storage_path('app/files/email-attachments/'.$attachment->name), $attachment->content);
                $path = 'email-attachments/'.$attachment->name;
                $content .= " <form action='".route('purchase.download.attachments')."' method='GET'><input type='hidden' name='path' value='".$path."' /><button type='submit' class='btn-link'>Attachment</button></form>";
            });
        } else {
            $email = Email::find($request->uid);
            $email->seen = 1;
            $email->save();

            $to_email = $email->to;
            $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

            if (array_key_exists('attachment', $array)) {
                $attachment = json_decode($email->additional_data, true)['attachment'];
                if (is_array($attachment)) {
                    $content = $email->message;
                    foreach ($attachment as $attach) {
                        if ($email->model_type == Supplier::class) {
                            $supplier = Supplier::find($email->model_id);
                            if ($supplier != null) {
                                $filename = explode('/', $attach);
                                $filename = explode('.', end($filename));
                                if (end($filename) == 'xlsx' || end($filename) == 'xls') {
                                    $log = LogExcelImport::where('supplier_email', $supplier->email)->where('filename', $filename[0])->first();
                                    if ($log != null) {
                                        if ($log->status == 1) {
                                            $alert = 'Excel import process';
                                        } elseif ($log->status == 2) {
                                            $alert = 'Excel import created';
                                        } else {
                                            $alert = 'Excel import error';
                                        }
                                    }
                                }
                            }
                        }
                        if (! isset($alert)) {
                            $alert = '';
                        }
                        $content .= " <form action='".route('purchase.download.attachments')."' method='GET'><input type='hidden' name='path' value='".$attach."' /><button type='submit' class='btn-link'>Attachment</button>
                        <button type='button' class='btn-secondary' onclick='processExcel(".$email->id.")' id='email".$email->id."' data-attached='".$attach."' >".$alert.'</button></form>';
                    }
                } else {
                    $content = "$email->message <form action='".route('purchase.download.attachments')."' method='GET'><input type='hidden' name='path' value='".$attachment."' /><button type='submit' class='btn-link'>Attachment</button></form>";
                }
            } else {
                $content = $email->message;
            }
        }

        return response()->json([
            'email' => $content,
            'to_email' => isset($to_email) ? $to_email : '',
        ]);
    }

    public function emailSend(EmailSendPurchaseRequest $request): RedirectResponse
    {

        $supplier = Supplier::find($request->supplier_id);

        if ($supplier->default_email != '' || $supplier->email != '') {
            $file_paths = [];

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $filename = $file->getClientOriginalName();

                    $file->storeAs('documents', $filename, 'files');

                    $file_paths[] = "documents/$filename";
                }
            }

            $cc = $bcc = [];
            $emails = $request->email;

            if ($request->has('cc')) {
                $cc = array_values(array_filter($request->cc));
            }
            if ($request->has('bcc')) {
                $bcc = array_values(array_filter($request->bcc));
            }

            if (is_array($emails) && ! empty($emails)) {
                $to = array_shift($emails);
                $cc = array_merge($emails, $cc);

                $mail = Mail::to($to);

                if ($cc) {
                    $mail->cc($cc);
                }
                if ($bcc) {
                    $mail->bcc($bcc);
                }

                $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths));
            } else {
                return redirect()->back()->withErrors('Please select an email');
            }
            $from_email = Helpers::getFromEmail();
            $params = [
                'model_id' => $supplier->id,
                'model_type' => Supplier::class,
                'from' => $from_email,
                'to' => $request->email[0],
                'seen' => 1,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
            ];

            Email::create($params);

            return redirect()->route('supplier.show', $supplier->id)->withSuccess('You have successfully sent an email!');
        }

        return redirect()->route('supplier.show', $supplier->id)->withError('Please add an email first');
    }

    public function emailResend(EmailResendPurchaseRequest $request): RedirectResponse
    {

        $attachment = [];
        $purchase = Purchase::find($request->purchase_id);

        $imap = new Client([
            'host' => config('settings.imap_host_purchase'),
            'port' => config('settings.imap_port_purchase'),
            'encryption' => config('settings.imap_encryption_purchase'),
            'validate_cert' => config('settings.imap_validate_cert_purchase'),
            'username' => config('settings.imap_username_purchase'),
            'password' => config('settings.imap_password_purchase'),
            'protocol' => config('settings.imap_protocol_purchase'),
        ]);

        $imap->connect();

        if ($request->type == 'inbox') {
            $inbox = $imap->getFolder('INBOX');
        } else {
            $inbox = $imap->getFolder('INBOX.Sent');
            $inbox->query();
        }

        if ($request->email_type == 'server') {
            $email = $inbox->getMessage($request->email_id, null, null, true, true, true);

            if ($email->hasHTMLBody()) {
                $content = $email->getHTMLBody();
            } else {
                $content = $email->getTextBody();
            }

            Mail::to($request->recipient)->send(new PurchaseEmail($email->getSubject(), $content, $attachment));
            $from_email = Helpers::getFromEmail();
            $params = [
                'model_id' => $purchase->id,
                'model_type' => Purchase::class,
                'from' => $from_email,
                'to' => $request->recipient,
                'subject' => 'Resent: '.$email->getSubject(),
                'message' => $content,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $attachment]),
            ];
        } else {
            $email = Email::find($request->email_id);

            $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

            if (array_key_exists('attachment', $array)) {
                $temp = json_decode($email->additional_data, true)['attachment'];
            }

            if (! is_array($temp)) {
                $attachment[] = $temp;
            } else {
                $attachment = $temp;
            }

            Mail::to($request->recipient)->send(new PurchaseEmail($email->subject, $email->message, $attachment));
            $from_email = Helpers::getFromEmail();
            $params = [
                'model_id' => $purchase->id,
                'model_type' => Purchase::class,
                'from' => $from_email,
                'to' => $request->recipient,
                'subject' => "Resent: $email->subject",
                'message' => $email->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $attachment]),
            ];
        }

        Email::create($params);

        return redirect()->route('purchase.show', $purchase->id)->withSuccess('You have successfully resent an email!');
    }

    public function emailReply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $emailToReply = Email::findOrFail($request->reply_email_id);
        Mail::send(new ReplyToEmail($emailToReply, $request->message));

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    public function emailForward(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'to.0' => 'required|email',
            'to.*' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $forwardEmail = Email::findOrFail($request->forward_email_id);
        $forwardTo = array_filter($request->to);

        foreach ($forwardTo as $to) {
            Mail::to($to)->send(new ForwardEmail($forwardEmail, $request->message));
        }

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    public function sendmsgsupplier(Request $request)
    {
        $supplier_id = json_decode($request->input('supplier_id'));

        $id = $request->input('id');

        $suppliers_all = Supplier::select('id', 'phone', 'whatsapp_number', 'supplier')
            ->whereIn('id', $supplier_id)
            ->get();
        if (count($suppliers_all) > 0) {
            // Get product
            $media = '';
            $product = Product::find($id);
            if ($product && $product->hasMedia(config('constants.media_tags'))) {
                $media = getMediaUrl($product->getMedia(config('constants.media_tags'))->first());
            }

            $sku = isset($product->sku) ? $product->sku : '';
            $size = ! empty($request->get('size')) ? ' size '.$request->get('size') : '';

            foreach ($suppliers_all as $supplier) {
                if ($supplier->phone != '') {
                    $message = $request->input('message').' ('.$sku.')'.$size;

                    try {
                        dump('Sending message');

                        app(WhatsAppController::class)->sendWithThirdApi($supplier->phone, $supplier->whatsapp_number, $message, isset($media) && ! empty($media) ? $media : null);

                        $params = [
                            'number' => $supplier->phone,
                            'user_id' => Auth::id(),
                            'supplier_id' => $supplier->id,
                            'message' => $message,
                            'approved' => 0,
                            'status' => 1,
                        ];

                        $chat_message = ChatMessage::create($params);

                        $values = ['product_id' => $id, 'supplier_id' => $supplier->id, 'chat_message_id' => $chat_message->id];
                        PurchaseProductSupplier::insert($values);
                    } catch (Exception $e) {
                        dump($e->getMessage());
                    }
                }
            }
        }
    }

    public function getMsgSupplier(Request $request): JsonResponse
    {
        $productId = $request->get('product_id', 0);

        $suppliers = PurchaseProductSupplier::select('suppliers.id', 'suppliers.supplier', 'chat_messages.id as chat_messages_id', 'chat_messages.message', 'chat_messages.created_at')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_product_supplier.supplier_id')
            ->leftJoin('chat_messages', 'chat_messages.id', '=', 'purchase_product_supplier.chat_message_id')
            ->where('purchase_product_supplier.product_id', '=', $productId)
            ->orderByDesc('chat_messages.created_at')
            ->get();
        $data = [];
        foreach ($suppliers as $value) {
            $data[$value->id]['supplier'] = $value->supplier;

            if (! isset($data[$value->id]['chat_messages'])) {
                $data[$value->id]['chat_messages'] = [];
            }

            if (! empty($value->chat_messages_id)) {
                $data[$value->id]['chat_messages'][] = [
                    'message' => $value->message,
                    'created_at' => $value->created_at,
                ];
            }
        }

        return response()->json($data);
    }

    public function sendEmailBulk(SendEmailBulkPurchaseRequest $request): RedirectResponse
    {

        if ($request->suppliers) {
            $suppliers = Supplier::whereIn('id', $request->suppliers)->where(function ($query) {
                $query->whereNotNull('default_email')->orWhereNotNull('email');
            })->get();
        } else {
            if ($request->not_received != 'on' && $request->received != 'on') {
                return redirect()->route('purchase.index')->withErrors(['Please select either suppliers or option']);
            }
        }

        if ($request->not_received == 'on') {
            $suppliers = Supplier::doesnthave('emails')->where(function ($query) {
                $query->whereNotNull('default_email')->orWhereNotNull('email');
            })->get();
        }

        if ($request->received == 'on') {
            $suppliers = Supplier::whereDoesntHave('emails', function ($query) {
                $query->where('type', 'incoming');
            })->where(function ($query) {
                $query->whereNotNull('default_email')->orWhereNotNull('email');
            })->where('has_error', 0)->get();
        }

        $file_paths = [];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs('documents', $filename, 'files');

                $file_paths[] = "documents/$filename";
            }
        }

        $cc = $bcc = [];
        if ($request->has('cc')) {
            $cc = array_values(array_filter($request->cc));
        }
        if ($request->has('bcc')) {
            $bcc = array_values(array_filter($request->bcc));
        }

        foreach ($suppliers as $supplier) {
            $mail = Mail::to($supplier->default_email ?? $supplier->email);

            if ($cc) {
                $mail->cc($cc);
            }
            if ($bcc) {
                $mail->bcc($bcc);
            }

            $mail->send(new PurchaseEmail($request->subject, $request->message, $file_paths));
            $from_email = Helpers::getFromEmail();
            $params = [
                'model_id' => $supplier->id,
                'model_type' => Supplier::class,
                'from' => $from_email,
                'seen' => 1,
                'to' => $supplier->default_email ?? $supplier->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc' => $cc ?: null,
                'bcc' => $bcc ?: null,
            ];

            Email::create($params);
        }

        return redirect()->route('purchase.index')->withSuccess('You have successfully sent emails in bulk!');
    }

    /**
     * Start to sync the products with order product id
     */
    public function syncOrderProductId()
    {
        $recordsOldUpdate = Db::select('
        select pp.id,pp.purchase_id, pp.product_id
        from purchase_products as pp join products as p on p.id = pp.product_id
        left join order_products as op on op.sku = p.sku
        where pp.order_product_id != op.id');

        if (! empty($recordsOldUpdate)) {
            foreach ($recordsOldUpdate as $records) {
                // start
                PurchaseProduct::where('id', $records['id'])->update(['order_product_id' => $records['order_product_id']]);
            }
        }
    }
}
