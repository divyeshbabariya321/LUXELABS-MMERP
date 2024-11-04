<?php

namespace App\Http\Controllers;

use App\AutoReply;
use App\Brand;
use App\CallBusyMessage;
use App\CallBusyMessageStatus;
use App\CallHistory;
use App\CallRecording;
use App\CashFlow;
use App\Category;
use App\ChatMessage;
use App\Comment;
use App\CommunicationHistory;
use App\CreditHistory;
use App\Currency;
use App\Customer;
use App\DeliveryApproval;
use App\Email;
use App\EmailAddress;
use App\EmailCommonExceptionLog;
use App\ErpLeads;
use App\EstimatedDeliveryHistory;
use App\Events\OrderUpdated;
use App\Helpers;
use App\Helpers\OrderHelper;
use App\Http\Requests\CreateStatusOrderRequest;
use App\Http\Requests\EditStatusOrderRequest;
use App\Http\Requests\GenerateAWBDHLOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UploadForApprovalOrderRequest;
use App\Invoice;
use App\Jobs\SendEmail;
use App\Jobs\TwilioSmsJob;
use App\Jobs\UpdateOrderStatusMessageTpl;
use App\Library\DHL\CreateShipmentRequest;
use App\Library\DHL\GetRateRequest;
use App\Library\DHL\TrackShipmentRequest;
use App\Mail\OrderInvoice;
use App\Mail\OrderStatusMail;
use App\Mail\ViewInvoice;
use App\MailinglistTemplate;
use App\MailinglistTemplateCategory;
use App\Mails\Manual\AdvanceReceipt;
use App\Mails\Manual\AdvanceReceiptPDF;
use App\Mails\Manual\OrderCancellationMail;
use App\Mails\Manual\OrderConfirmation;
use App\Mails\Manual\OrderDeliveryDateChangeMail;
use App\Mails\Manual\OrderStatusChangeMail;
use App\Mails\Manual\RefundProcessed;
use App\Marketing\WhatsappConfig;
use App\Message;
use App\Models\DataTableColumn;
use App\Models\InvoiceLater;
use App\Models\OrderStatusMagentoRequestResponseLog;
use App\MonetaryAccount;
use App\Order;
use App\OrderCustomerAddress;
use App\OrderEmailSendJourneyLog;
use App\OrderErrorLog;
use App\OrderMagentoErrorLog;
use App\OrderProduct;
use App\OrderReport;
use App\OrderStatus as OrderStatuses;
use App\OrderStatusHistory;
use App\Product;
use App\PurchaseProductOrder;
use App\ReadOnly\OrderStatus;
use App\ReadOnly\PaymentModes;
use App\Refund;
use App\Reply;
use App\ReplyCategory;
use App\Setting;
use App\StatusChange;
use App\StatusMapping;
use App\Store_order_status;
use App\StoreMasterStatus;
use App\StoreOrderStatusesHistory;
use App\StoreWebsite;
use App\StoreWebsiteOrder;
use App\StoreWebsiteProductPrice;
use App\StoreWebsiteTwilioNumber;
use App\Task;
use App\TicketStatuses;
use App\TwilioActiveNumber;
use App\TwilioAgent;
use App\TwilioCallWaiting;
use App\TwilioDequeueCall;
use App\User;
use App\Waybill;
use App\waybillTrackHistories;
use App\Website;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use seo2websites\MagentoHelper\MagentoHelperv2;
use SoapClient;

class OrderController extends Controller
{
    /**
     * @param  Request  $request
     *                            Generate the PDf for the orders list page
     */
    public function downloadOrderInPdf(Request $request)
    {
        $term = $request->input('term');
        $order_status = $request->status ?? [''];
        $date = $request->date ?? '';

        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }

        switch ($request->input('sortby')) {
            case 'type':
                $sortby = 'order_type';
                break;
            case 'date':
                $sortby = 'order_date';
                break;
            case 'order_handler':
                $sortby = 'sales_person';
                break;
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'status':
                $sortby = 'order_status_id';
                break;
            case 'advance':
                $sortby = 'advance_detail';
                break;
            case 'balance':
                $sortby = 'balance_amount';
                break;
            case 'action':
                $sortby = 'action';
                break;
            case 'due':
                $sortby = 'due';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            default:
                $sortby = 'order_date';
        }

        // Create query
        $orders = (new Order)->newQuery()->with('customer');

        if (empty($term)) {
        } else {
            // AN order should have customer, if term is filled
            $orders = $orders->whereHas('customer', function ($query) use ($term) {
                return $query->where('name', 'LIKE', "%$term%");
            })
                ->orWhere('order_id', 'like', '%'.$term.'%')
                ->orWhere('order_type', $term)
                ->orWhere('sales_person', Helpers::getUserIdByName($term))
                ->orWhere('received_by', Helpers::getUserIdByName($term))
                ->orWhere('client_name', 'like', '%'.$term.'%')
                ->orWhere('city', 'like', '%'.$term.'%')
                ->orWhere('order_status_id', (new OrderStatus)->getIDCaseInsensitive($term));
        }

        if ($order_status[0] != '') {
            $orders = $orders->whereIn('order_status_id', $order_status);
        }

        if ($date != '') {
            $orders = $orders->where('order_date', $date);
        }

        $users = Helpers::getUserArray(User::all());
        $order_status_list = (new OrderStatus)->all();

        // also sort by communication action and due
        if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
            $orders = $orders->orderByDesc('is_priority')->orderBy($sortby, $orderby);
        } else {
            $orders = $orders->orderByDesc('is_priority')->orderByDesc('created_at');
        }

        $orders_array = $orders->paginate(500);
        $media_tags = config('constants.media_tags');

        // load the view for pdf and after that load that into dompdf instance, and then stream (download) the pdf
        $html = view('orders.index_pdf', compact('orders_array', 'users', 'term', 'orderby', 'order_status_list', 'order_status', 'date', 'media_tags'));
        $pdf = new Dompdf;
        $pdf->loadHtml($html);
        $pdf->render();
        $pdf->stream('orders.pdf');
    }

    public function downloadOrderMailPdf(Request $request)
    {
        if (! empty($request->email_id)) {
            $email = Email::where('id', $request->email_id)->first();
        } else {
            $order = Order::where('id', $request->order_id)->first();
            $email = Email::where('model_id', $order->id)->where('model_type', Order::class)->orderByDesc('id')->first();
        }

        if ($email) {
            $content = $email->message;
        } else {
            $content = 'No Email found';
        }

        // load the view for pdf and after that load that into dompdf instance, and then stream (download) the pdf
        $html = view('orders.order_mail', compact('content'));
        $pdf = new Dompdf;
        $paper_size = [0, 0, 700, 1080];
        $pdf->set_paper($paper_size);
        $pdf->loadHtml($html->render());
        $pdf->render();
        $pdf->stream('orderMail.pdf');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $term = $request->input('term');
        $advance_detail = $request->input('advance_detail');
        $balance_amount = $request->input('balance_amount');
        $order_status = $request->status ?? [''];
        $date = $request->date ?? '';
        $estimated_delivery_date = $request->estimated_delivery_date ?? '';
        $brandList = Brand::all()->pluck('name', 'id')->toArray();
        $brandIds = array_filter($request->get('brand_id', []));
        $registerSiteList = StoreWebsite::pluck('website', 'id')->toArray();
        $fromdatadefault = [
            'street' => config('dhl.shipper.street'),
            'city' => config('dhl.shipper.city'),
            'postal_code' => config('dhl.shipper.postal_code'),
            'country_code' => config('dhl.shipper.country_code'),
            'person_name' => config('dhl.shipper.person_name'),
            'company_name' => config('dhl.shipper.company_name'),
            'phone' => config('dhl.shipper.phone'),
        ];
        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }

        switch ($request->input('sortby')) {
            case 'type':
                $sortby = 'order_type';
                break;
            case 'date':
                $sortby = 'order_date';
                break;
            case 'estdeldate':
                $sortby = 'estimated_delivery_date';
                break;
            case 'order_handler':
                $sortby = 'sales_person';
                break;
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'status':
                $sortby = 'order_status_id';
                break;
            case 'advance':
                $sortby = 'advance_detail';
                break;
            case 'balance':
                $sortby = 'balance_amount';
                break;
            case 'action':
                $sortby = 'action';
                break;
            case 'due':
                $sortby = 'due';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            default:
                $sortby = 'order_date';
        }

        $orders = (new Order)->newQuery()->with('customer')->leftJoin('store_website_orders as swo', 'swo.order_id', 'orders.id');

        if (empty($term)) {
        } else {
            $orders = $orders->whereHas('customer', function ($query) use ($term) {
                return $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('id', 'LIKE', '%'.$term.'%')
                    ->orWhere('email', 'LIKE', '%'.$term.'%');
            })
                ->orWhere('orders.order_id', 'like', '%'.$term.'%')
                ->orWhere('order_type', $term)
                ->orWhere('sales_person', Helpers::getUserIdByName($term))
                ->orWhere('received_by', Helpers::getUserIdByName($term))
                ->orWhere('orders.city', 'like', '%'.$term.'%')
                ->orWhere('order_status_id', (new OrderStatus)->getIDCaseInsensitive($term))
                ->Where('client_name', 'like', $term);
        }

        if ($order_status[0] != '') {
            $orders = $orders->whereIn('order_status_id', $order_status);
        }

        if ($date != '') {
            $orders = $orders->where('order_date', $date);
        }

        if ($estimated_delivery_date != '') {
            $orders = $orders->where('estimated_delivery_date', $estimated_delivery_date);
        }

        if ($store_site = $request->store_website_id) {
            $orders = $orders->whereIn('swo.website_id', $store_site);
        }

        if ($advance_detail != '') {
            $orders = $orders->where('advance_detail', '<=', $advance_detail);
        }

        if ($balance_amount != '') {
            $orders = $orders->where('balance_amount', '<=', $balance_amount);
        }

        $statusFilterList = clone $orders;

        $orders = $orders->leftJoin('order_products as op', 'op.order_id', 'orders.id')
            ->leftJoin('customers as cs', 'cs.id', 'orders.customer_id')
            ->leftJoin('products as p', 'p.id', 'op.product_id')
            ->leftJoin('brands as b', 'b.id', 'p.brand');

        if (! empty($brandIds)) {
            $orders = $orders->whereIn('p.brand', $brandIds);
        }

        $orders = $orders->groupBy('orders.order_id');

        $orders = $orders->select(['orders.*', 'cs.email as cust_email', DB::raw('group_concat(b.name) as brand_name_list'), 'swo.website_id']);

        $users = Helpers::getUserArray(User::all());
        $order_status_list = OrderHelper::getStatus();

        if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
            $orders = $orders->orderByDesc('is_priority')->orderBy($sortby, $orderby);
        } else {
            $orders = $orders->orderByDesc('is_priority')->orderByDesc('created_at');
        }

        $statusFilterList = $statusFilterList->leftJoin('order_statuses as os', 'os.id', 'orders.order_status_id')
            ->where('order_status', '!=', '')->groupBy('order_status')->select(DB::raw('count(*) as total'), 'os.status as order_status', 'swo.website_id')->get()->toArray();

        $totalOrders = count($orders->get());
        $orders_array = $orders->paginate(10);

        $quickreply = Reply::where('model', 'Order')->get();

        $duty_shipping = [];
        foreach ($orders_array as $order) {
            $duty_shipping[$order->id]['id'] = $order->id;

            $website_code_data = $order->duty_tax;
            if ($website_code_data != null) {
                $product_qty = count($order->order_product);

                $code = $website_code_data->website_code->code;

                $duty_countries = $website_code_data->website_code->duty_of_country;
                $shipping_countries = $website_code_data->website_code->shipping_of_country($code);

                $duty_amount = ($duty_countries->default_duty * $product_qty);
                $shipping_amount = ($shipping_countries->price * $product_qty);

                $duty_shipping[$order->id]['shipping'] = $duty_amount;
                $duty_shipping[$order->id]['duty'] = $shipping_amount;
            } else {
                $duty_shipping[$order->id]['shipping'] = 0;
                $duty_shipping[$order->id]['duty'] = 0;
            }
        }
        $orderStatusList = OrderStatuses::all();

        $store_site = $request->store_website_id;

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'orders-listing')->first();

        $dynamicColumnsToShowPostman = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPostman = json_decode($hideColumns, true);
        }
        $attach_image_tag = config('constants.attach_image_tag');

        return view('orders.index', compact('orders_array', 'users', 'term', 'orderby', 'order_status_list', 'order_status', 'date', 'statusFilterList', 'brandList', 'registerSiteList', 'store_site', 'totalOrders', 'quickreply', 'fromdatadefault', 'duty_shipping', 'orderStatusList', 'dynamicColumnsToShowPostman', 'estimated_delivery_date', 'advance_detail', 'balance_amount', 'attach_image_tag'));
    }

    public function orderPreviewSentMails(Request $request): JsonResponse
    {
        $id = $request->id;
        $lists = Email::where('model_id', $id)->orderByDesc('id')->get();

        return response()->json(['code' => 200, 'data' => $lists]);
    }

    public function charity_order(Request $request)
    {
        $term = $request->input('term');
        $order_status = $request->status ?? [''];
        $date = $request->date ?? '';
        $brandList = Brand::all()->pluck('name', 'id')->toArray();
        $brandIds = array_filter($request->get('brand_id', []));
        $registerSiteList = StoreWebsite::pluck('website', 'id')->toArray();
        $fromdatadefault = [
            'street' => config('dhl.shipper.street'),
            'city' => config('dhl.shipper.city'),
            'postal_code' => config('dhl.shipper.postal_code'),
            'country_code' => config('dhl.shipper.country_code'),
            'person_name' => config('dhl.shipper.person_name'),
            'company_name' => config('dhl.shipper.company_name'),
            'phone' => config('dhl.shipper.phone'),
        ];
        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }

        switch ($request->input('sortby')) {
            case 'type':
                $sortby = 'order_type';
                break;
            case 'date':
                $sortby = 'order_date';
                break;
            case 'estdeldate':
                $sortby = 'estimated_delivery_date';
                break;
            case 'order_handler':
                $sortby = 'sales_person';
                break;
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'status':
                $sortby = 'order_status_id';
                break;
            case 'advance':
                $sortby = 'advance_detail';
                break;
            case 'balance':
                $sortby = 'balance_amount';
                break;
            case 'action':
                $sortby = 'action';
                break;
            case 'due':
                $sortby = 'due';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            default:
                $sortby = 'order_date';
        }

        $orders = (new Order)->newQuery()->with('customer')->leftJoin('store_website_orders as swo', 'swo.order_id', 'orders.id');
        if (empty($term)) {
        } else {
            $orders = $orders->whereHas('customer', function ($query) use ($term) {
                return $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('id', 'LIKE', '%'.$term.'%')
                    ->orWhere('email', 'LIKE', '%'.$term.'%');
            })
                ->orWhere('orders.order_id', 'like', '%'.$term.'%')
                ->orWhere('order_type', $term)
                ->orWhere('sales_person', Helpers::getUserIdByName($term))
                ->orWhere('received_by', Helpers::getUserIdByName($term))
                ->orWhere('client_name', 'like', '%'.$term.'%')
                ->orWhere('orders.city', 'like', '%'.$term.'%')
                ->orWhere('order_status_id', (new OrderStatus)->getIDCaseInsensitive($term));
        }
        if ($order_status[0] != '') {
            $orders = $orders->whereIn('order_status_id', $order_status);
        }

        if ($date != '') {
            $orders = $orders->where('order_date', $date);
        }

        if ($store_site = $request->store_website_id) {
            $orders = $orders->where('swo.website_id', $store_site);
        }

        $statusFilterList = clone $orders;

        $orders = $orders->leftJoin('order_products as op', 'op.order_id', 'orders.id')
            ->leftJoin('customers as cs', 'cs.id', 'orders.customer_id')
            ->leftJoin('products as p', 'p.id', 'op.product_id')
            ->join('customer_charities', 'customer_charities.product_id', 'p.id')
            ->leftJoin('brands as b', 'b.id', 'p.brand');

        if (! empty($brandIds)) {
            $orders = $orders->whereIn('p.brand', $brandIds);
        }

        $orders = $orders->groupBy('orders.id');
        $orders = $orders->select(['orders.*', 'cs.email as cust_email', DB::raw('group_concat(b.name) as brand_name_list'), 'swo.website_id']);

        $users = Helpers::getUserArray(User::all());
        $order_status_list = OrderHelper::getStatus();

        if ($sortby != 'communication' && $sortby != 'action' && $sortby != 'due') {
            $orders = $orders->orderByDesc('is_priority')->orderBy($sortby, $orderby);
        } else {
            $orders = $orders->orderByDesc('is_priority')->orderByDesc('created_at');
        }

        $statusFilterList = $statusFilterList->leftJoin('order_statuses as os', 'os.id', 'orders.order_status_id')
            ->where('order_status', '!=', '')->groupBy('order_status')->select(DB::raw('count(*) as total'), 'os.status as order_status', 'swo.website_id')->get()->toArray();
        $totalOrders = count($orders->get());
        $orders_array = $orders->paginate(10);

        $quickreply = Reply::where('model', 'Order')->get();

        $duty_shipping = [];
        foreach ($orders_array as $order) {
            $duty_shipping[$order->id]['id'] = $order->id;

            $website_code_data = $order->duty_tax;
            if ($website_code_data != null) {
                $product_qty = count($order->order_product);

                $code = $website_code_data->website_code->code;

                $duty_countries = $website_code_data->website_code->duty_of_country;
                $shipping_countries = $website_code_data->website_code->shipping_of_country($code);

                $duty_amount = ($duty_countries->default_duty * $product_qty);
                $shipping_amount = ($shipping_countries->price * $product_qty);

                $duty_shipping[$order->id]['shipping'] = $duty_amount;
                $duty_shipping[$order->id]['duty'] = $shipping_amount;
            } else {
                $duty_shipping[$order->id]['shipping'] = 0;
                $duty_shipping[$order->id]['duty'] = 0;
            }
        }
        $orderStatusList = OrderStatuses::all();
        $attach_image_tag = config('constants.attach_image_tag');

        return view('orders.charity_order', compact('orders_array', 'users', 'term', 'orderby', 'order_status_list', 'order_status', 'date', 'statusFilterList', 'brandList', 'registerSiteList', 'store_site', 'totalOrders', 'quickreply', 'fromdatadefault', 'duty_shipping', 'orderStatusList', 'attach_image_tag'));
    }

    public function addProduct(Request $request): JsonResponse
    {
        $this->createProduct($request);
        $productArr = [
            'sku' => request('sku'),
            'product_price' => request('price'),
            'color' => request('color'),
            'order_id' => request('order_id'),
            'qty' => request('qty'),
            'size' => request('size'),
        ];
        OrderProduct::insert($productArr);

        return response()->json(['code' => 200, 'message' => 'Product added successfully']);
    }

    public function products(Request $request)
    {
        $term = $request->input('term');

        if ($request->input('orderby') == '') {
            $orderby = 'desc';
        } else {
            $orderby = 'asc';
        }

        switch ($request->input('sortby')) {
            case 'supplier':
                $sortby = 'supplier';
                break;
            case 'customer':
                $sortby = 'client_name';
                break;
            case 'customer_price':
                $sortby = 'price';
                break;
            case 'date':
                $sortby = 'created_at';
                break;
            case 'delivery_date':
                $sortby = 'date_of_delivery';
                break;
            case 'updated_date':
                $sortby = 'estimated_delivery_date';
                break;
            case 'status':
                $sortby = 'order_status_id';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            default:
                $sortby = 'id';
        }

        if (empty($term)) {
            $products = OrderProduct::with(['Product' => function ($query) {
                $query->with('Purchases');
            }, 'Order'])->get()->toArray();
        } else {
            $products = OrderProduct::whereHas('Product', function ($query) use ($term) {
                $query->where('supplier', 'like', '%'.$term.'%');
            })
                ->with(['Product', 'Order'])->orWhere('product_price', 'LIKE', "%$term%")
                ->orWhereHas('Order', function ($query) use ($term) {
                    $query->where('date_of_delivery', 'LIKE', "%$term%")
                        ->orWhere('estimated_delivery_date', 'LIKE', "%$term%")
                        ->orWhere('order_status', 'LIKE', "%$term%");
                })->get()->toArray();
        }

        $brand = $request->input('brand');
        $supplier = $request->input('supplier');

        if ($sortby == 'supplier') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['product']['supplier'];
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['product']['supplier'];
                }));
            }
        }

        if ($sortby == 'client_name') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['client_name'];
                    }

                    return '';
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['client_name'];
                    }

                    return '';
                }));
            }
        }

        if ($sortby == 'price') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['product_price'];
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['product_price'];
                }));
            }
        }

        if ($sortby == 'created_at') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['created_at'];
                    }

                    return '1999-01-01 00:00:00';
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['created_at'];
                    }

                    return '1999-01-01 00:00:00';
                }));
            }
        }

        if ($sortby == 'date_of_delivery') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['date_of_delivery'];
                    }

                    return '1999-01-01 00:00:00';
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['date_of_delivery'];
                    }

                    return '1999-01-01 00:00:00';
                }));
            }
        }

        if ($sortby == 'estimated_delivery_date') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['estimated_delivery_date'];
                    }

                    return '1999-01-01 00:00:00';
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['estimated_delivery_date'];
                    }

                    return '1999-01-01 00:00:00';
                }));
            }
        }

        if ($sortby == 'order_status') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['order_status'];
                    }

                    return '';
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    if ($value['order']) {
                        return $value['order']['order_status'];
                    }

                    return '';
                }));
            }
        }

        if ($sortby == 'communication') {
            if ($orderby == 'asc') {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['communication']['created_at'];
                }));

                $products = array_reverse($products);
            } else {
                $products = array_values(Arr::sort($products, function ($value) {
                    return $value['communication']['created_at'];
                }));
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($products, $perPage * ($currentPage - 1), $perPage);

        $products = new LengthAwarePaginator($currentItems, count($products), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('orders.products', compact('products', 'term', 'orderby', 'brand', 'supplier'));
    }

    public function getCustomerAddress(Request $request): JsonResponse
    {
        $address = OrderCustomerAddress::where('order_id', $request->order_id)->get();

        return response()->json(['code' => 200, 'data' => $address]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $defaultSelected = [];
        $key = request()->get('key', false);

        if (! empty($key)) {
            $defaultData = session($key);
            if (! empty($defaultData)) {
                $defaultSelected = $defaultData;
            }
        }

        $order = new Order;

        $data = [];
        foreach ($order->getFillable() as $item) {
            $data[$item] = '';
        }

        $expiresAt = Carbon::now()->addMinutes(10);

        $last = Order::withTrashed()->latest()->first();
        $last_order = ($last) ? $last->id + 1 : 1;

        Cache::put('user-order-'.Auth::id(), $last_order, $expiresAt);
        Cache::put('last-order', $last_order, $expiresAt);

        if (! empty($defaultSelected['selected_product'])) {
            foreach ($defaultSelected['selected_product'] as $product) {
                self::attachProduct($last_order, $product);
            }
        }

        $data['id'] = $last_order;
        $data['sales_persons'] = Helpers::getUsersArrayByRole('Sales');
        $data['modify'] = 0;
        $data['order_products'] = $this->getOrderProductsWithProductData($data['id']);

        $customer_suggestions = [];
        $customers = (new Customer)->newQuery()->latest()->select('name')->get()->toArray();

        foreach ($customers as $customer) {
            array_push($customer_suggestions, $customer['name']);
        }

        $data['customers'] = Customer::all();
        $orderStatusData = new OrderStatus;
        $paymentModes = new PaymentModes;
        $monetaryAccount = MonetaryAccount::pluck('name', 'id')->toArray();

        $data['customer_suggestions'] = $customer_suggestions;
        $data['defaultSelected'] = $defaultSelected;
        $data['key'] = $key;
        $data['orderStatusData'] = $orderStatusData->all();
        $data['paymentModes'] = $paymentModes->all();
        $data['monetaryAccount'] = $monetaryAccount;

        return view('orders.form', $data);
    }

    public function searchProduct(Request $request): JsonResponse
    {
        $exist = Product::where('sku', request('sku'))->first();
        if (! empty($exist)) {
            return response()->json(['code' => 200, 'data' => $exist, 'message' => 'Product added successfully']);
        }

        return response()->json(['code' => 500, 'message' => 'Product not found']);
    }

    public function createProduct(Request $request): JsonResponse
    {
        $productArr = [
            'sku' => request('sku'),
            'price' => request('price'),
            'size' => request('size'),
            'name' => request('name'),
            'stock' => 1,
            'quick_product' => 1,
        ];
        $exist = Product::where('sku', request('sku'))->select('id')->first();
        if ($exist->id) {
            Product::insert($productArr);

            return response()->json(['code' => 200, 'message' => 'Product added successfully']);
        }

        return response()->json(['code' => 500, 'message' => 'Product already exist']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {

        $data = $request->all();
        $sessionKey = $request->get('key', '');
        $data['user_id'] = Auth::id();
        $oPrefix = ($request->input('order_type') == 'offline') ? 'OFF-'.date('Ym') : 'ONN-'.date('Ym');
        $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $nextId = 0;
        if (! empty($statement)) {
            $nextId = $statement[0]->Auto_increment;
        }

        $data['order_id'] = $oPrefix.'-'.$nextId;

        if (empty($request->input('order_date'))) {
            $data['order_date'] = date('Y-m-d');
        }
        if (! empty($request->website_address)) {
            $data['website_address_id'] = $request->website_address;
        }
        $customer = Customer::find($request->customer_id);

        $data['client_name'] = $customer->name;
        $data['contact_detail'] = $customer->phone;
        if ($request->hdn_order_mail_status == '1') {
            $data['auto_emailed'] = 1;
        } else {
            $data['auto_emailed'] = 0;
        }

        if (isset($data['date_of_delivery'])) {
            $data['estimated_delivery_date'] = $data['date_of_delivery'];
        }

        $currency = $request->get('currency', 'INR');
        $data['store_currency_code'] = $currency;

        $order = Order::create($data);

        $customerShippingAddress = [
            'address_type' => 'shipping',
            'city' => $customer->city,
            'country_id' => $customer->country,
            'email' => $customer->email,
            'firstname' => $customer->name,
            'postcode' => $customer->pincode,
            'street' => $customer->address,
            'order_id' => $order->id,
        ];
        OrderCustomerAddress::insert($customerShippingAddress);

        $currency = $request->get('currency', 'INR');

        if (! empty($request->input('order_products'))) {
            foreach ($request->input('order_products') as $key => $order_product_data) {
                $order_product = OrderProduct::findOrFail($key);
                if ($order_product->order_id != $order->id) {
                    $nw_order_product = new OrderProduct;
                    foreach ($order_product->getAttributes() as $k => $attr) {
                        if (! in_array($k, ['id', 'created_at', 'updated_at'])) {
                            $nw_order_product->{$k} = $attr;
                        }
                    }

                    foreach ($order_product_data as $k => $v) {
                        $nw_order_product->{$k} = $v;
                    }

                    $nw_order_product->currency = $currency;
                    $nw_order_product->eur_price = Currency::convert($order_product->product_price, 'EUR', $currency);
                    $nw_order_product->order_id = $order->id;
                    $nw_order_product->save();
                } else {
                    if ($order_product) {
                        $order_product->currency = $currency;
                        $order_product->eur_price = Currency::convert($order_product->product_price, 'EUR', $currency);
                        $order_product->save();
                    }
                }
            }
        }

        $totalAmount = 0;
        foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
            $totalAmount += $order_product->product_price;
        }

        $order->balance_amount = ($totalAmount - $order->advance_detail);
        $order->save();

        $store_order_website = new StoreWebsiteOrder;
        $store_order_website->website_id = 15;
        $store_order_website->status_id = $order->order_status_id;
        $store_order_website->order_id = $order->id;
        $store_order_website->save();

        $store_website_product_price = [];

        foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
            $store_website_product_price['product_id'] = $order_product->product_id;

            $address = OrderCustomerAddress::where('order_id', $order->id)->where('address_type', 'shipping')->first();

            $product = Product::find($order_product->product_id);
            $getPrice = $product->getPrice($customer->store_website_id, 'IN', '', '', '', '', '', '', '', '', $order->id, $order_product->product_id);
            $getDuty = $product->getDuty($address->country_id);

            $store_website_product_price['default_price'] = $getPrice['original_price'];
            $store_website_product_price['duty_price'] = (float) $getDuty['duty'];
            $store_website_product_price['segment_discount'] = (float) $getPrice['segment_discount'];
            $store_website_product_price['override_price'] = $getPrice['total'];
            $store_website_product_price['status'] = 1;
            $store_website_product_price['store_website_id'] = 15;
        }

        StoreWebsiteProductPrice::insert($store_website_product_price);

        if ($customer->credit > 0) {
            $balance_amount = $order->balance_amount;
            $totalCredit = $customer->credit;
            if (($order->balance_amount - $customer->credit) < 0) {
                $left_credit = ($order->balance_amount - $customer->credit) * -1;
                $order->advance_detail += $order->balance_amount;
                $balance_amount = 0;
                $customer->credit = $left_credit;
            } else {
                $balance_amount -= $customer->credit;
                $order->advance_detail += $customer->credit;
                $customer->credit = 0;
            }

            $order->balance_amount = $balance_amount;
            $order->order_id = $oPrefix.'-'.$order->id;
            $order->save();
            $customer->save();

            if ($order->id) {
                CreditHistory::create(
                    [
                        'customer_id' => $request->customer_id,
                        'model_id' => $order->id,
                        'model_type' => Order::class,
                        'used_credit' => (float) $totalCredit - $customer->credit,
                        'used_in' => 'ORDER',
                        'type' => 'MINUS',
                    ]
                );
            }
        }

        $expiresAt = Carbon::now()->addMinutes(10);
        $last_order = $order->id + 1;
        Cache::put('user-order-'.Auth::id(), $last_order, $expiresAt);

        if ($request->convert_order == 'convert_order') {
            if (! empty($request->selected_product)) {
                foreach ($request->selected_product as $product) {
                    self::attachProduct($order->id, $product);
                }
            }
        }

        if ($order->order_status_id == OrderHelper::$proceedWithOutAdvance && $order->order_type == 'online') {
            $product_names = '';
            foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
                $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
            }

            $delivery_time = $order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F');

            $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();

            $auto_message = str_replace('/{product_names}/i', $product_names, $auto_reply->reply);
            $auto_message = str_replace('/{delivery_time}/i', $delivery_time, $auto_message);

            $followup_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;

            $requestData = new Request;
            $requestData2 = new Request;
            $requestData->setMethod('POST');
            $requestData2->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 1]);
            $requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message, 'status' => 1]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            app(WhatsAppController::class)->sendMessage($requestData2, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'initial-advance',
                'method' => 'whatsapp',
            ]);
        } elseif ($order->order_status_id == OrderHelper::$prepaid) {
            $auto_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply;
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 1]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'online-confirmation',
                'method' => 'whatsapp',
            ]);
        } elseif ($order->order_status_id == OrderHelper::$refundToBeProcessed) {
            $refund = Refund::where('order_id', $order->id)->first();

            if (! $refund) {
                Refund::create([
                    'customer_id' => $order->customer->id,
                    'order_id' => $order->id,
                    'type' => 'Cash',
                    'date_of_request' => Carbon::now(),
                    'date_of_issue' => Carbon::now()->addDays(10),
                ]);
            }

            if ($order->payment_mode == 'paytm') {
                if ($order->customer) {
                    $all_amount = 0;

                    if ($order->order_product) {
                        foreach ($order->order_product as $order_product) {
                            $all_amount += $order_product->product_price;
                        }
                    }

                    $order->customer->credit += $all_amount;
                    $order->customer->save();
                }
            } elseif ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
                if ($order->customer) {
                    $order->customer->credit += $order->advance_detail;
                    $order->customer->save();
                }
            }
        }

        if ($request->hdn_order_mail_status == '1') {
            if (! $order->is_sent_offline_confirmation()) {
                if ($order->order_type == 'offline') {
                    if (! empty($order->customer) && ! empty($order->customer->email)) {
                        $emailClass = (new OrderConfirmation($order))->build();

                        $email = Email::create([
                            'model_id' => $order->id,
                            'model_type' => Order::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $order->customer->email,
                            'subject' => $emailClass->subject,
                            'message' => $emailClass->render(),
                            'template' => 'order-confirmation',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    }
                }
            }
        }

        // sending order message to the customer
        UpdateOrderStatusMessageTpl::dispatch($order->id)->onQueue('customer_message');

        if ($request->ajax()) {
            return response()->json(['code' => 200, 'order' => $order]);
        }

        if ($request->get('return_url_back')) {
            return redirect()->back()->with('message', 'Order created successfully');
        }

        if (! empty($sessionKey)) {
            $defaultData = session($sessionKey);
            if (! empty($defaultData) && ! empty($defaultData['redirect_back'])) {
                return redirect()->to($defaultData['redirect_back'])->with('message', 'Order created successfully');
            }
        }

        return redirect()->route('order.index')
            ->with('message', 'Order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $authUser = Auth::user();
        $data = $order->toArray();
        $data['sales_persons'] = Helpers::getUsersArrayByRole('Sales');
        $data['order_products'] = $this->getOrderProductsWithProductData($order->id);
        $data['comments'] = Comment::with('user')->where('subject_id', $order->id)
            ->where('subject_type', '=', Order::class)->get();
        $data['users'] = User::all()->toArray();
        $messages = Message::all()->where('moduleid', '=', $data['id'])->where('moduletype', '=', 'order')->sortByDesc('created_at')->take(10)->toArray();
        $data['messages'] = $messages;
        $data['total_price'] = $this->getTotalOrderPrice($order);

        $order_statuses = (new OrderStatus)->all();
        $data['order_statuses'] = $order_statuses;
        $data['tasks'] = Task::where('model_type', 'order')->where('model_id', $order->id)->get()->toArray();
        $data['order_recordings'] = CallRecording::where('order_id', '=', $data['order_id'])->get()->toArray();
        $data['order_status_report'] = OrderStatuses::all();
        if ($order->customer) {
            $data['order_reports'] = OrderReport::where('order_id', $order->customer->id)->get();
        }

        // $data['users_array']       = Helpers::getUserArray(User::all());
        $data['has_customer'] = $order->customer ? $order->customer->id : false;
        $data['customer'] = $order->customer;
        $data['reply_categories'] = ReplyCategory::all();
        $data['delivery_approval'] = $order->delivery_approval;
        $data['waybill'] = $order->waybill;
        $data['waybills'] = $order->waybills;
        $data['customerAddress'] = $order->orderCustomerAddress;
        $data['shipping_address'] = $order->shippingAddress();
        $data['billing_address'] = $order->billingAddress();
        $data['order'] = $order;
        $data['paymentModes'] = (new PaymentModes)->all();
        $data['brands'] = Brand::getAll();
        $data['users_array'] = User::all()->pluck('name', 'id')->toArray();
        $data['categories'] = TaskCategoryController::getAllTaskCategory();
        $data['authUser'] = $authUser;

        return view('orders.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): View
    {
        $data = $order->toArray();
        $data['modify'] = 1;
        $data['sales_persons'] = Helpers::getUsersArrayByRole('Sales');
        $data['order_products'] = $this->getOrderProductsWithProductData($order->id);
        $orderStatusData = new OrderStatus;
        $paymentModes = new PaymentModes;
        $monetaryAccount = MonetaryAccount::pluck('name', 'id')->toArray();
        $data['orderStatusData'] = $orderStatusData->all();
        $data['paymentModes'] = $paymentModes->all();
        $data['monetaryAccount'] = $monetaryAccount;

        return view('orders.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {

        if (! empty($request->input('order_products'))) {
            foreach ($request->input('order_products') as $key => $order_product_data) {
                $order_product = OrderProduct::findOrFail($key);

                if (isset($order_product_data['purchase_status']) && $order_product_data['purchase_status'] != $order_product->purchase_status) {
                    StatusChange::create([
                        'model_id' => $order_product->id,
                        'model_type' => OrderProduct::class,
                        'user_id' => Auth::id(),
                        'from_status' => $order_product->purchase_status,
                        'to_status' => $order_product_data['purchase_status'],
                    ]);
                }

                $order_product->update($order_product_data);
            }
        }

        if ($request->status != $order->order_status) {
            StatusChange::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'user_id' => Auth::id(),
                'from_status' => $order->order_status,
                'to_status' => $request->status,
            ]);
        }

        $data = $request->except(['_token', '_method', 'status', 'purchase_status']);
        $data['order_status'] = $request->status;
        $data['is_priority'] = $request->is_priority == 'on' ? 1 : 0;
        $order->update($data);

        $this->calculateBalanceAmount($order);
        $order = Order::find($order->id);

        if ($customer = Customer::find($order->customer_id)) {
            if ($customer->credit > 0) {
                $balance_amount = $order->balance_amount;

                if (($order->balance_amount - $customer->credit) < 0) {
                    $left_credit = ($order->balance_amount - $customer->credit) * -1;
                    $order->advance_detail += $order->balance_amount;
                    $balance_amount = 0;
                    $customer->credit = $left_credit;
                } else {
                    $balance_amount -= $customer->credit;
                    $order->advance_detail += $customer->credit;
                    $customer->credit = 0;
                }

                $order->balance_amount = $balance_amount;
                $order->save();
                $customer->save();

                if ($order->id) {
                    CreditHistory::create(
                        [
                            'customer_id' => $request->customer_id,
                            'model_id' => $order->id,
                            'model_type' => Order::class,
                            'used_credit' => $customer->credit,
                            'used_in' => 'ORDER',
                            'type' => 'MINUS',
                        ]
                    );
                }
            }
        }

        if (! $order->is_sent_initial_advance() && $order->order_status_id == OrderHelper::$proceedWithOutAdvance && $order->order_type == 'online') {
            $product_names = '';
            foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
                $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
            }

            $delivery_time = $order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F');

            $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();

            $auto_message = str_replace('/{product_names}/i', $product_names, $auto_reply->reply);
            $auto_message = str_replace('/{delivery_time}/i', $delivery_time, $auto_message);

            $followup_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;

            $requestData = new Request;
            $requestData2 = new Request;
            $requestData->setMethod('POST');
            $requestData2->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 1]);
            $requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message, 'status' => 1]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            app(WhatsAppController::class)->sendMessage($requestData2, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'initial-advance',
                'method' => 'whatsapp',
            ]);
        } elseif (! $order->is_sent_online_confirmation() && $order->order_status_id == OrderHelper::$prepaid) {
            $auto_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply;
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 2]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'online-confirmation',
                'method' => 'whatsapp',
            ]);
        }

        if ($request->hdn_order_mail_status == '1') {
            $id_order_inc = $order->id;
            $order_new = Order::find($id_order_inc);
            if (! $order_new->is_sent_offline_confirmation()) {
                if ($order_new->order_type == 'offline') {
                    if (! empty($order_new->customer) && ! empty($order_new->customer->email)) {
                        //Mail::to($order_new->customer->email)->send(new OrderConfirmation($order_new));
                        $emailClass = (new OrderConfirmation($order_new))->build();

                        $emailObject = Email::create([
                            'model_id' => $order_new->id,
                            'model_type' => Order::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $order_new->customer->email,
                            'subject' => $emailClass->subject,
                            'message' => $emailClass->render(),
                            'template' => 'order-confirmation',
                            'additional_data' => $order_new->id,
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);

                        SendEmail::dispatch($emailObject)->onQueue('send_email');
                    }
                }
            }
        }

        if ($order->order_status_id == OrderHelper::$refundToBeProcessed) {
            if ($order->payment_mode == 'paytm') {
                if ($order->customer) {
                    $all_amount = 0;

                    if ($order->order_product) {
                        foreach ($order->order_product as $order_product) {
                            $all_amount += $order_product->product_price;
                        }
                    }

                    $order->customer->credit += $all_amount;
                    $order->customer->save();
                }
            } elseif ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
                if ($order->customer) {
                    $order->customer->credit += $order->advance_detail;
                    $order->customer->save();
                }
            }
            $refund = Refund::where('order_id', $order->id)->first();

            if (! $refund) {
                Refund::create([
                    'customer_id' => $order->customer->id,
                    'order_id' => $order->id,
                    'type' => 'Cash',
                    'date_of_request' => Carbon::now(),
                    'date_of_issue' => Carbon::now()->addDays(10),
                ]);
            }
        }

        if ($order->order_status == OrderHelper::$delivered) {
            if ($order->order_product) {
                foreach ($order->order_product as $order_product) {
                    if ($order_product->product) {
                        if ($order_product->product->supplier == 'In-stock') {
                            $order_product->product->supplier = '';
                            $order_product->product->save();
                        }
                    }
                }
            }

            if (! $order->is_sent_order_delivered()) {
                $message = AutoReply::where('type', 'auto-reply')->where('keyword', 'order-delivery-confirmation')->first()->reply;
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add(['customer_id' => $order->customer_id, 'message' => $message, 'status' => 2]);

                app(WhatsAppController::class)->sendMessage($requestData, 'customer');

                CommunicationHistory::create([
                    'model_id' => $order->id,
                    'model_type' => Order::class,
                    'type' => 'order-delivered',
                    'method' => 'whatsapp',
                ]);
            }
            event(new OrderUpdated($order));
            $order->delete();

            if ($request->type != 'customer') {
                return redirect()->route('order.index')->with('success', 'Order was updated and archived successfully!');
            } else {
                return redirect()->back()->with('success', 'Order was updated and archived successfully!');
            }
        }
        event(new OrderUpdated($order));

        return redirect()->back()->with('message', 'Order updated successfully');
    }

    public function printAdvanceReceipt($id)
    {
        $order = Order::find($id);

        $view = (new AdvanceReceiptPDF($order))->render();

        $pdf = new Dompdf;
        $pdf->loadHtml($view);
        $pdf->render();
        $pdf->stream();

        return $view;
    }

    public function emailAdvanceReceipt($id): RedirectResponse
    {
        $order = Order::find($id);
        if (! $order) {
            return redirect()->back()->withError('Order not found.');
        }

        if ($order->order_status == OrderHelper::$advanceRecieved) {
            $from_email = Helpers::getFromEmail($order->customer->id);
            $emailClass = (new AdvanceReceipt($order))->build();

            $storeWebsiteOrder = $order->storeWebsiteOrder;
            $email = Email::create([
                'model_id' => $order->customer->id,
                'model_type' => Customer::class,
                'from' => $from_email,
                'to' => $order->customer->email,
                'subject' => $emailClass->subject,
                'message' => $emailClass->render(),
                'template' => 'advance-receipt',
                'additional_data' => $order->id,
                'status' => 'pre-send',
                'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
            ]);

            SendEmail::dispatch($email)->onQueue('send_email');
        }

        return redirect()->back()->withSuccess('Advance Receipt was successfully emailed!');
    }

    public function sendConfirmation($id): RedirectResponse
    {
        $order = Order::find($id);

        if (! $order->is_sent_offline_confirmation()) {
            if ($order->order_type == 'offline') {
                $emailClass = (new OrderConfirmation($order))->build();

                $storeWebsiteOrder = $order->storeWebsiteOrder;
                $email = Email::create([
                    'model_id' => $order->customer->id,
                    'model_type' => Customer::class,
                    'from' => $emailClass->fromMailer,
                    'to' => $order->customer->email,
                    'subject' => $emailClass->subject,
                    'message' => $emailClass->render(),
                    'template' => 'order-confirmation',
                    'additional_data' => $order->id,
                    'status' => 'pre-send',
                    'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                ]);

                SendEmail::dispatch($email)->onQueue('send_email');
            }
        }

        return redirect()->back()->withSuccess('You have successfully sent confirmation email!');
    }

    public function generateInvoice($id)
    {
        $order = Order::find($id);
        $consignor = [
            'name' => Setting::get('consignor_name'),
            'address' => Setting::get('consignor_address'),
            'city' => Setting::get('consignor_city'),
            'country' => Setting::get('consignor_country'),
            'phone' => Setting::get('consignor_phone'),
        ];

        $view = view('emails.orders.invoice-pdf', [
            'order' => $order,
            'consignor' => $consignor,
        ])->render();

        $pdf = new Dompdf;
        $pdf->loadHtml($view);
        $pdf->render();
        $pdf->stream();
    }

    public function uploadForApproval(UploadForApprovalOrderRequest $request, $id): RedirectResponse
    {

        $delivery_approval = Order::find($id)->delivery_approval;

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('order/'.floor($delivery_approval->id / config('constants.image_per_folder')))
                    ->upload();
                $delivery_approval->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->back()->with('success', 'You have successfully uploaded delivery images for approval!');
    }

    public function deliveryApprove(Request $request, $id): RedirectResponse
    {
        $delivery_approval = DeliveryApproval::find($id);

        $delivery_approval->approved = 1;

        $delivery_approval->save();

        return redirect()->back()->with('success', 'You have successfully approved delivery!');
    }

    public function downloadPackageSlip($id)
    {
        $waybill = Waybill::find($id);

        return Storage::disk('files')->download('waybills/'.$waybill->package_slip);
    }

    public function refundAnswer(Request $request, $id): Response
    {
        $order = Order::find($id);

        $order->refund_answer = $request->answer;
        $order->refund_answer_date = Carbon::now();

        $order->save();

        return response('success');
    }

    public function sendSuggestion(Request $request, $id): RedirectResponse
    {
        $params = [
            'number' => null,
            'status' => 1, // message status for auto messaging
            'user_id' => 6,
        ];

        $order = Order::with(['Order_Product' => function ($query) {
            $query->with('Product');
        }])->where('id', $id)->first();

        if (count($order->order_product) > 0) {
            $order_products_count = count($order->order_product);
            $limit = $order_products_count > 20 ? 1 : (int) round(20 / $order_products_count);

            foreach ($order->order_product as $order_product) {
                $brand = (int) $order_product->product->brand;
                $category = (int) $order_product->product->category;

                if ($category != 0 && $category != 1 && $category != 2 && $category != 3) {
                    $is_parent = Category::isParent($category);
                    $category_children = [];

                    if ($is_parent) {
                        $children = Category::find($category)->childs()->get();

                        foreach ($children as $child) {
                            array_push($category_children, $child->id);
                        }
                    } else {
                        $children = Category::find($category)->parent->childs;

                        foreach ($children as $child) {
                            array_push($category_children, $child->id);
                        }

                        if (($key = array_search($category, $category_children)) !== false) {
                            unset($category_children[$key]);
                        }
                    }
                }

                if ($brand && $category != 1) {
                    $products = Product::where('brand', $brand)->whereIn('category', $category_children)->latest()->take($limit)->get();
                } elseif ($brand) {
                    $products = Product::where('brand', $brand)->latest()->take($limit)->get();
                } elseif ($category != 1) {
                    $products = Product::where('category', $category)->latest()->take($limit)->get();
                }

                if (count($products) > 0) {
                    $params['customer_id'] = $order->customer_id;

                    $chat_message = ChatMessage::create($params);

                    foreach ($products as $product) {
                        $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
                    }
                }
            }
        }

        $order->refund_answer = 'yes';
        $order->refund_answer_date = Carbon::now();
        $order->save();

        return redirect()->back()->withSuccess('You have successfully sent suggestions!');
    }

    public function sendDelivery(Request $request): Response
    {
        $params = [
            'number' => null,
            'user_id' => Auth::id() ?? 6,
            'approved' => 0,
            'status' => 1,
        ];

        $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'product-delivery-times')->first();

        $exploded = explode('[', $auto_reply->reply);

        $customer = Customer::find($request->customer_id);
        $message = $exploded[0];
        $express_shipping = '';
        $normal_shipping = '';
        $in_stock = 0;
        $normal_products = 0;

        foreach ($request->selected_product as $product_id) {
            $product = Product::find($product_id);

            if ($product->supplier == 'In-stock') {
                $express_shipping .= $in_stock == 0 ? $product->name : ", $product->name";
                $in_stock++;
            } else {
                $normal_shipping .= $normal_products == 0 ? $product->name : ", $product->name";
                $normal_products++;
            }
        }

        $second_explode = explode(']', $exploded[1]);
        $shipping_times = explode('/', $second_explode[0]);

        if ($in_stock >= 1) {
            $express_shipping .= $shipping_times[0];
        }

        if ($normal_products >= 1) {
            $normal_shipping .= $shipping_times[1];
        }

        $message .= $express_shipping.$normal_shipping.$second_explode[1];

        $params['customer_id'] = $customer->id;
        $params['message'] = $message;

        ChatMessage::create($params);

        $histories = CommunicationHistory::where('model_id', $customer->id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

        foreach ($histories as $history) {
            $history->is_stopped = 1;
            $history->save();
        }

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'initiate-followup',
            'method' => 'whatsapp',
        ]);

        return response('success');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        StatusChange::create([
            'model_id' => $order->id,
            'model_type' => Order::class,
            'user_id' => Auth::id(),
            'from_status' => $order->order_status,
            'to_status' => $request->status,
        ]);

        $order->order_status = $request->status;
        $order->save();

        if (! $order->is_sent_initial_advance() && $order->order_status == OrderHelper::$proceedWithOutAdvance && $order->order_type == 'online') {
            $product_names = '';
            foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
                $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
            }

            $delivery_time = $order->estimated_delivery_date ? Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F');

            $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();

            $auto_message = str_replace('/{product_names}/i', $product_names, $auto_reply->reply);
            $auto_message = str_replace('/{delivery_time}/i', $delivery_time, $auto_message);

            $followup_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;
            $requestData = new Request;
            $requestData2 = new Request;
            $requestData->setMethod('POST');
            $requestData2->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 2]);
            $requestData2->request->add(['customer_id' => $order->customer->id, 'message' => $followup_message, 'status' => 2]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');
            app(WhatsAppController::class)->sendMessage($requestData2, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'initial-advance',
                'method' => 'whatsapp',
            ]);
        } elseif (! $order->is_sent_online_confirmation() && $order->order_status == OrderHelper::$prepaid) {
            $auto_message = AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply;
            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 2]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'online-confirmation',
                'method' => 'whatsapp',
            ]);
        }

        if ($order->order_status == OrderHelper::$refundToBeProcessed) {
            $refund = Refund::where('order_id', $order->id)->first();

            if (! $refund) {
                Refund::create([
                    'customer_id' => $order->customer->id,
                    'order_id' => $order->id,
                    'type' => 'Cash',
                    'date_of_request' => Carbon::now(),
                    'date_of_issue' => Carbon::now()->addDays(10),
                ]);
            }

            if ($order->payment_mode == 'paytm') {
                if ($order->customer) {
                    $all_amount = 0;

                    if ($order->order_product) {
                        foreach ($order->order_product as $order_product) {
                            $all_amount += $order_product->product_price;
                        }
                    }

                    $order->customer->credit += $all_amount;
                    $order->customer->save();
                }
            } elseif ($order->payment_mode != 'paytm' || $order->advance_detail > 0) {
                if ($order->customer) {
                    $order->customer->credit += $order->advance_detail;
                    $order->customer->save();
                }
            }
        }

        if ($order->order_status == OrderHelper::$delivered) {
            if ($order->order_product) {
                foreach ($order->order_product as $order_product) {
                    if ($order_product->product) {
                        if ($order_product->product->supplier == 'In-stock') {
                            $order_product->product->supplier = '';
                            $order_product->product->save();
                        }
                    }
                }
            }

            if (! $order->is_sent_order_delivered()) {
                $message = AutoReply::where('type', 'auto-reply')->where('keyword', 'order-delivery-confirmation')->first()->reply;
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add(['customer_id' => $order->customer_id, 'message' => $message, 'status' => 2]);

                app(WhatsAppController::class)->sendMessage($requestData, 'customer');

                CommunicationHistory::create([
                    'model_id' => $order->id,
                    'model_type' => Order::class,
                    'type' => 'order-delivered',
                    'method' => 'whatsapp',
                ]);
            }
        }
    }

    public function sendRefund(Request $request, $id): Response
    {
        $order = Order::find($id);

        if (! $order->is_sent_refund_initiated()) {
            $product_names = '';
            foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
                $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
            }

            $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'order-refund')->first();

            $auto_message = str_replace('/{order_id}/i', $order->order_id, $auto_reply->reply);
            $auto_message = str_replace('/{product_names}/i', $product_names, $auto_message);

            $requestData = new Request;
            $requestData->setMethod('POST');
            $requestData->request->add(['customer_id' => $order->customer->id, 'message' => $auto_message, 'status' => 2]);

            app(WhatsAppController::class)->sendMessage($requestData, 'customer');

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'refund-initiated',
                'method' => 'whatsapp',
            ]);

            $from_email = Helpers::getFromEmail($order->customer->id);
            $emailClass = (new RefundProcessed($order->order_id, $product_names))->build();

            $storeWebsiteOrder = $order->storeWebsiteOrder;
            $email = Email::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'from' => $from_email,
                'to' => $order->customer->email,
                'subject' => $emailClass->subject,
                'message' => $emailClass->render(),
                'template' => 'refund-initiated',
                'additional_data' => $order->id,
                'status' => 'pre-send',
                'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
            ]);

            SendEmail::dispatch($email)->onQueue('send_email');
        }

        return response('success');
    }

    public function generateAWB(Request $request): RedirectResponse
    {
        $options = [
            'trace' => 1,
            'style' => SOAP_DOCUMENT,
            'use' => SOAP_LITERAL,
            'soap_version' => SOAP_1_2,
        ];

        $soap = new SoapClient('https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Waybill/WayBillGeneration.svc?wsdl', $options);

        $soap->__setLocation('https://netconnect.bluedart.com/Ver1.8/ShippingAPI/Waybill/WayBillGeneration.svc');

        $soap->sendRequest = true;
        $soap->printRequest = false;
        $soap->formatXML = true;

        $actionHeader = new \SoapHeader('https://www.w3.org/5005/08/addressing', 'Action', 'https://tempuri.org/IWayBillGeneration/GenerateWayBill', true);

        $soap->__setSoapHeaders($actionHeader);

        $order = Order::find($request->order_id);

        $order->customer->name = $request->customer_name;
        $order->customer->address = $request->customer_address1;
        $order->customer->city = $request->customer_address2;
        $order->customer->pincode = $request->customer_pincode;

        $order->customer->save();

        $pickup_datetime = explode(' ', $request->pickup_time);
        $pickup_date = $pickup_datetime[0];
        $pickup_time = str_replace(':', '', $pickup_datetime[1]);


        $piece_count = $order->order_product()->count();

        $actual_weight = $request->box_width * $request->box_length * $request->box_height / 5000;

        $params = [
            'Request' => [
                'Consignee' => [
                    'ConsigneeAddress1' => $order->customer->address,
                    'ConsigneeAddress2' => $order->customer->city,
                    'ConsigneeMobile' => $order->customer->phone,
                    'ConsigneeName' => $order->customer->name,
                    'ConsigneePincode' => $order->customer->pincode,
                ],
                'Services' => [
                    'ActualWeight' => $actual_weight,
                    'CreditReferenceNo' => $order->id,
                    'PickupDate' => $pickup_date,
                    'PickupTime' => $pickup_time,
                    'PieceCount' => $piece_count,
                    'DeclaredValue' => 500,
                    'ProductCode' => 'D',
                    'ProductType' => 'Dutiables',
                    'Dimensions' => [
                        'Dimension' => [
                            'Breadth' => $request->box_width,
                            'Count' => $piece_count,
                            'Height' => $request->box_height,
                            'Length' => $request->box_length,
                        ],
                    ],
                ],
                'Shipper' => [
                    'CustomerAddress1' => '807, Hubtown Viva, Western Express Highway, Shankarwadi, Andheri East',
                    'CustomerAddress2' => 'Mumbai',
                    'CustomerCode' => '382500',
                    'CustomerMobile' => '022-62363488',
                    'CustomerName' => 'Solo Luxury',
                    'CustomerPincode' => '400060',
                    'IsToPayCustomer' => '',
                    'OriginArea' => 'BOM',
                ],
            ],
            'Profile' => [
                'Api_type' => 'S',
                'LicenceKey' => config('settings.bluedart_license_key'),
                'LoginID' => config('settings.bluedart_login_id'),
                'Version' => '1.3', ],
        ];

        $result = $soap->__soapCall('GenerateWayBill', [$params])->GenerateWayBillResult;

        if ($result->IsError) {
            if (is_array($result->Status->WayBillGenerationStatus)) {
                $error = '';
                foreach ($result->Status->WayBillGenerationStatus as $error_object) {
                    $error .= $error_object->StatusInformation.'. ';
                }
            } else {
                $error = $result->Status->WayBillGenerationStatus->StatusInformation;
            }

            return redirect()->back()->with('error', "$error");
        } else {
            Storage::disk('files')->put('waybills/'.$order->id.'_package_slip.pdf', $result->AWBPrintContent);

            $waybill = new Waybill;
            $waybill->order_id = $order->id;
            $waybill->awb = $result->AWBNo;
            $waybill->box_width = $request->box_width;
            $waybill->box_height = $request->box_height;
            $waybill->box_length = $request->box_length;
            $waybill->actual_weight = $actual_weight;
            $waybill->package_slip = $order->id.'_package_slip.pdf';
            $waybill->pickup_date = $request->pickup_time;
            $waybill->save();
        }

        return redirect()->back()->with('success', 'You have successfully generated AWB!');
    }

    public function calculateBalanceAmount(Order $order)
    {
        $order_instance = Order::where('id', $order->id)->with('order_product')->get()->first();

        $balance_amt = 0;

        foreach ($order_instance->order_product as $order_product) {
            $balance_amt += $order_product->product_price * $order_product->qty;
        }

        if (! empty($order_instance->advance_detail)) {
            $balance_amt -= $order_instance->advance_detail;
        }

        $order->update([
            'balance_amount' => $balance_amt,
        ]);
    }

    public function getTotalOrderPrice($order_instance)
    {
        $balance_amt = 0;

        foreach ($order_instance->order_product as $order_product) {
            $balance_amt += $order_product->product_price * $order_product->qty;
        }

        return $balance_amt;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->to('order')->with('success', 'Order has been archived');
    }

    public function permanentDelete(Order $order): RedirectResponse
    {
        $order_products = OrderProduct::where('order_id', '=', $order->id);

        $order_products->delete();
        $comments = Comment::where('subject_id', $order->id)->where('subject_type', Order::class);
        $comments->delete();

        $order->forceDelete();

        return redirect()->to('order')->with('success', 'Order has been  deleted');
    }

    public function deleteOrderProduct(OrderProduct $order_product): RedirectResponse
    {
        $key = request('key');
        if (! empty($key)) {
            $defaultData = session($key);
            if (! empty($defaultData['selected_product'])) {
                $postProducts = [];
                foreach ($defaultData['selected_product'] as $product) {
                    if ($product != $order_product->product_id) {
                        $postProducts = $product;
                    }
                }
                $defaultData['selected_product'] = $postProducts;
                session([$key => $defaultData]);
            }
        }
        $order_product->delete();

        return redirect()->back()->with('message', 'Product Detached');
    }

    public static function attachProduct($model_id, $product_id)
    {
        $product = Product::where('id', '=', $product_id)->get()->first();

        $order_product = OrderProduct::where('order_id', $model_id)->where('product_id', $product_id)->first();
        $order = Order::find($model_id);
        $size = '';

        if ($order && $order->customer && ($order->customer->shoe_size != '' || $order->customer->clothing_size != '')) {
            if ($product->category != 1) {
                if ($product->product_category->title != 'Clothing' || $product->product_category->title != 'Shoes') {
                    if ($product->product_category->parent && ($product->product_category->parent->title == 'Clothing' || $product->product_category->parent->title == 'Shoes')) {
                        if ($product->product_category->parent->title == 'Clothing') {
                            $size = $order->customer->clothing_size;
                        } else {
                            $size = $order->customer->shoe_size;
                        }
                    }
                } else {
                    if ($product->product_category->title == 'Clothing') {
                        $size = $order->customer->clothing_size;
                    } else {
                        $size = $order->customer->shoe_size;
                    }
                }
            }
        }

        if (empty($order_product)) {
            $product = OrderProduct::create([
                'order_id' => $model_id,
                'product_id' => $product->id,
                'sku' => $product->sku,
                'product_price' => $product->price_special_offer != '' ? $product->price_special_offer : $product->price_inr_special,
                'color' => $product->color,
                'size' => $size,
            ]);

            $action = 'Attached';
        } else {
            $action = 'Attached';
        }

        return $action;
    }

    public function generateNextOrderId()
    {
        $previous = Order::withTrashed()->latest()->where('order_type', '=', 'Offline')->first(['order_id']);

        if (! empty($previous)) {
            $temp = explode('-', $previous);

            return 'OFF-'.(intval($temp[1]) + 1);
        }

        return 'OFF-1000001';
    }

    public function getOrderProductsWithProductData($order_id)
    {
        $orderProducts = OrderProduct::where('order_id', '=', $order_id)->get()->toArray();

        foreach ($orderProducts as $key => $value) {
            if (! empty($orderProducts[$key]['color'])) {
                $temp = Product::with('brands')->where('id', '=', $orderProducts[$key]['product_id'])
                    ->where('color', $orderProducts[$key]['color'])
                    ->get()->first();
            } else {
                $temp = Product::with('brands')->where('id', '=', $orderProducts[$key]['product_id'])
                    ->get()->first();
            }

            if (! empty($temp)) {
                $orderProducts[$key]['product'] = $temp;
                $orderProducts[$key]['product']['image'] = $temp->getMedia(config('constants.media_tags'))->first() ? getMediaUrl($temp->getMedia(config('constants.media_tags'))->first()) : '';
            }
        }

        return $orderProducts;
    }

    public function callManagement(Request $request): View
    {
        $reservedCalls = TwilioCallWaiting::with('storeWebsite')->leftJoin('customers as c', 'c.phone', DB::raw('REPLACE(twilio_call_waitings.from, "+", "")'))->orderByDesc('twilio_call_waitings.created_at')
            ->select(['twilio_call_waitings.*', 'c.name', 'c.email'])->get();

        return view('orders.call_management', compact('reservedCalls'));
    }

    public function getCurrentCallNumber(): JsonResponse
    {
        $getnumber = TwilioDequeueCall::where('agent_id', Auth::id())->first();

        return response()->json([
            'number' => $getnumber->caller,
        ]);
    }

    public function getCurrentCallInformation(): JsonResponse
    {
        try {
            $getnumber = TwilioDequeueCall::where('agent_id', Auth::id())->first();
            $agent = TwilioAgent::where('user_id', Auth::id())->first();

            $users = Customer::select('id')->where('phone', str_replace('+', '', $getnumber->caller))->get()->toArray();

            $allleads = [];
            $orders = (new Order)->newQuery()->with('customer')->leftJoin('store_website_orders as swo', 'swo.order_id', 'orders.id')
                ->leftJoin('order_products as op', 'op.order_id', 'orders.id')
                ->leftJoin('products as p', 'p.id', 'op.product_id')
                ->leftJoin('brands as b', 'b.id', 'p.brand')->groupBy('orders.id')
                ->where('orders.store_id', $agent->store_website_id)
                ->whereIn('customer_id', $users)
                ->select(['orders.*', DB::raw('group_concat(b.name) as brand_name_list'), 'swo.website_id'])->orderByDesc('created_at')->limit(5)->get();
            $allleads[] = $this->getLeadsInformation($users);
            if ($orders->count()) {
                foreach ($orders as &$value) {
                    $value->storeWebsite = $value->storeWebsiteOrder ? ($value->storeWebsiteOrder->storeWebsite ?? 'N/A') : 'N/A';
                    $value->order_date = Carbon::parse($value->order_date)->format('d-m-y');
                    $totalBrands = explode(',', $value->brand_name_list);
                    $value->brand_name_list = (count($totalBrands) > 1) ? 'Multi' : $value->brand_name_list;
                    $value->status = OrderHelper::getStatusNameById($value->order_status_id);
                }
            }

            return response()->json([
                'all_leads' => $allleads,
                'orders' => $orders,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getLeadsInformation($ids)
    {
        $source = ErpLeads::leftJoin('products', 'products.id', '=', 'erp_leads.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_leads.customer_id')
            ->leftJoin('erp_lead_status as els', 'els.id', 'erp_leads.lead_status_id')
            ->leftJoin('categories as cat', 'cat.id', 'erp_leads.category_id')
            ->leftJoin('brands as br', 'br.id', 'erp_leads.brand_id')
            ->whereIn('erp_leads.customer_id', $ids)
            ->orderByDesc('erp_leads.id')
            ->select(['erp_leads.*', 'products.name as product_name', 'cat.title as cat_title', 'br.name as brand_name', 'els.name as status_name', 'c.name as customer_name', 'c.id as customer_id']);

        $source = $source->latest()->limit(5)->get();

        foreach ($source as $key => $value) {
            $source[$key]->media_url = null;
            $media = $value->getMedia(config('constants.media_tags'))->first();
            if ($media) {
                $source[$key]->media_url = getMediaUrl($media);
            }

            if (empty($source[$key]->media_url) && $value->product_id) {
                $product = Product::find($value->product_id);
                $media = $product->getMedia(config('constants.media_tags'))->first();
                if ($media) {
                    $source[$key]->media_url = getMediaUrl($media);
                }
            }
        }

        return $source;
    }

    public function missedCalls(Request $request)
    {
        $callBusyMessages = CallBusyMessage::with(['status' => function ($q) {
            return $q->select('id', 'name', 'label');
        }])
            ->leftjoin('call_recordings as cr', 'cr.twilio_call_sid', 'call_busy_messages.caller_sid')
            ->leftjoin('twilio_call_data as tcd', 'tcd.call_sid', 'call_busy_messages.caller_sid')
            ->select('call_busy_messages.*', 'cr.recording_url as recording_urls', 'tcd.aget_user_id', 'tcd.from', 'tcd.to', 'tcd.call_data')
            ->groupby('call_busy_messages.caller_sid')
            ->orderByDesc('call_busy_messages.id');

        if (! empty($request->filterStatus)) {
            $callBusyMessages->where('call_busy_message_statuses_id', $request->filterStatus);
        }

        if (! empty($request->filterWebsite)) {
            $callBusyMessages->whereHas('customer.storeWebsite', function (Builder $query) use ($request) {
                $query->where('id', $request->filterWebsite);
            });
        }

        $callBusyMessages_pagination = $callBusyMessages->paginate(Setting::get('pagination'));
        $callBusyMessages = $callBusyMessages->paginate(Setting::get('pagination'))->toArray();

        foreach ($callBusyMessages['data'] as $key => $value) {
            $storeId = null;
            $activeNumber = TwilioActiveNumber::where('phone_number', '+'.trim($value['to'], '+'))->first();
            if ($activeNumber) {
                $storeId = StoreWebsiteTwilioNumber::where('twilio_active_number_id', $activeNumber->id)->first();
            }

            if (is_numeric($value['twilio_call_sid'])) {
                $formatted_phone = str_replace('+', '', $value['twilio_call_sid']);
                if (! empty($storeId->store_website_id)) {
                    $customer_array = Customer::with('storeWebsite', 'orders')->where('phone', $formatted_phone)->where('store_website_id', $storeId->store_website_id)->get()->toArray();
                }

                if ($value['aget_user_id'] != '') {
                    $user_data = User::where('id', $value['aget_user_id'])->first();
                    $agent_name = $user_data->name;
                } else {
                    $agent_name = '';
                }

                if (! empty($customer_array)) {
                    $callBusyMessages['data'][$key]['customerid'] = $customer_array[0]['id'];
                    $callBusyMessages['data'][$key]['customer_name'] = $customer_array[0]['name'];
                    $callBusyMessages['data'][$key]['store_website_id'] = $customer_array[0]['store_website_id'];

                    $callBusyMessages['data'][$key]['agent'] = $agent_name;
                    $callBusyMessages['data'][$key]['from'] = $value['from'];
                    $callBusyMessages['data'][$key]['to'] = $value['to'];
                    $callBusyMessages['data'][$key]['call_data'] = $value['call_data'];

                    if (isset($customer_array[0]['store_website']) && count($customer_array[0]['store_website'])) {
                        $callBusyMessages['data'][$key]['store_website_name'] = $customer_array[0]['store_website']['title'];
                    }

                    if (! empty($customer_array[0]['lead'])) {
                        $callBusyMessages['data'][$key]['lead_id'] = $customer_array[0]['lead']['id'];
                    }
                }
            }
        }

        $storeWebsite = StoreWebsite::pluck('title', 'id');
        $selectedStatus = $request->filterStatus;
        $selectedWebsite = $request->filterWebsite;
        $allStatuses = CallBusyMessageStatus::get();
        $assigned_to = User::with('roles')->get();
        $statuses = TicketStatuses::all();

        $reservedCalls = TwilioCallWaiting::leftJoin('customers as c', 'c.phone', DB::raw('REPLACE(twilio_call_waitings.from, "+", "")'))->orderByDesc('twilio_call_waitings.created_at')
            ->select(['twilio_call_waitings.*', 'c.name', 'c.email'])->get();

        return view('orders.missed_call', compact('callBusyMessages', 'allStatuses', 'storeWebsite', 'selectedStatus', 'selectedWebsite', 'callBusyMessages_pagination', 'reservedCalls', 'assigned_to', 'statuses'));
    }

    public function getOrdersFromMissedCalls(Request $request): JsonResponse
    {
        $callBusyMessages = CallBusyMessage::findOrFail($request->id);

        $formatted_phone = str_replace('+91', '', $callBusyMessages->twilio_call_sid);

        $customer_array = Customer::with('orders')->where('phone', 'LIKE', "%$formatted_phone%")->first();

        return response()->json($customer_array->orders);
    }

    public function callsHistory(Request $request): View
    {
        $calls = CallHistory::latest();
        $storeWebId = $request->get('storewebsite_filter');
        $customerIds = $request->get('customer_filter');
        $status = $request->get('status_filter');
        $customer_num = $request->get('phone_number') ? $request->get('phone_number') : '';
        $storeWebsite = $customer = $callHistoryStatus = [];
        if ((int) $storeWebId > 0) {
            $calls = $calls->whereIn('store_website_id', $storeWebId);
            $storeWebsite = StoreWebsite::whereIn('id', $storeWebId)->orderBy('website')->get();
        }
        if ((int) $customerIds > 0) {
            $calls = $calls->whereIn('customer_id', $customerIds);
            $customer = Customer::orWhereIn('id', $customerIds)->orderBy('name')->get();
        }
        if ((int) $status > 0) {
            $calls = $calls->where(function ($query) use ($status) {
                foreach ($status as $term) {
                    $query->orWhere('status', 'like', "%$term%");
                }
            });
            $callHistoryStatus = CallHistory::where(function ($query) use ($status) {
                foreach ($status as $term) {
                    $query->orWhere('status', 'like', "%$term%");
                }
            })->groupBy('status')->get();
        }

        if (isset($request->phone_number)) {
            $phone = explode(',', $request->phone_number);
            $customerPhone = Customer::select(DB::raw('group_concat(id) as customer_ids'))->where(function ($query) use ($phone) {
                foreach ($phone as $term) {
                    $query->orWhere('phone', 'like', "%$term%");
                }
            })->first();
            if (! empty($customerPhone->customer_ids)) {
                $customer_ids = explode(',', $customerPhone->customer_ids);
                $calls = $calls->whereIn('customer_id', $customer_ids);
            }
        }
        $calls = $calls->paginate(Setting::get('pagination'));

        return view('orders.call_history', [
            'calls' => $calls,
            'customer' => $customer,
            'storeWebsite' => $storeWebsite,
            'callHistoryStatus' => $callHistoryStatus,
            'customer_num' => $customer_num,
        ]);
    }

    public function createProductOnMagento(Request $request, $id)
    {
        $order = Order::find($id);
        $total_special_price = 0;

        foreach ($order->order_product as $order_product) {
            $total_special_price += $order_product->product_price;

            if ($order_product->product->category != 1) {
                $category = Category::find($order_product->product->category);
                $url_structure = [];
                $category_id = $category->magento_id;

                if ($category->parent) {
                    $parent = $category->parent;
                    $url_structure[0] = $parent->title;
                    $category_id = $parent->magento_id;

                    if ($parent->parent) {
                        $second_parent = $parent->parent;
                        $url_structure[0] = $second_parent->title;
                        $url_structure[1] = $parent->title;
                    }
                }
            }
        }

        dd($url_structure, $category_id);

        $options = [
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
        ];

        $proxy = new \SoapClient(config('magentoapi.url'), $options);
        $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

        /**
         * Configurable product
         */
        $productData = [
            'categories' => $category_id,
            'name' => 'Test Product from ERP',
            'description' => '<p></p>',
            'short_description' => 'Short Test Description from ERP',
            'website_ids' => [1],
            // Id or code of website
            'status' => 1,
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
            'price' => $total_special_price,
            // Same price than configurable product, no price change
            'special_price' => '',
            'associated_skus' => '',
            // Simple products to associate
            'configurable_attributes' => [155],
        ];
        // Creation of configurable product
        $result = $proxy->catalogProductCreate($sessionId, 'configurable', 14, "CUSTOMPRO$order->id", $productData);
        $product_url = "https://www.sololuxury.co.in/$url_structure[0]/$url_structure[1]/show-all/test-product-from-erp-$result.html";
        dd($product_url, $result);

        return $result;
    }

    /**
     * This function is use for Create Order email send journey log
     *
     * @param  mixed  $order_id
     * @param  mixed  $steps
     * @param  mixed  $modelType
     * @param  mixed  $sendType
     * @param  mixed  $seen
     * @param  mixed  $from
     * @param  mixed  $to
     * @param  mixed  $subject
     * @param  mixed  $message
     * @param  mixed  $template
     * @param  mixed  $errorMsg
     * @param  mixed  $storeWebsiteId
     * @return created data
     */
    public function createEmailSendJourneyLog($order_id = '', $steps = '', $modelType = '', $sendType = '', $seen = '0', $from = '', $to = '', $subject = '', $message = '', $template = '', $errorMsg = '', $storeWebsiteId = ''): created
    {
        return OrderEmailSendJourneyLog::create(
            [
                'order_id' => $order_id,
                'steps' => $steps,
                'model_type' => $modelType,
                'send_type' => $sendType,
                'seen' => $seen,
                'from_email' => $from,
                'to_email' => $to,
                'subject' => $subject,
                'message' => $message,
                'template' => $template,
                'error_msg' => $errorMsg,
                'store_website_id' => $storeWebsiteId,
            ]
        );
    }

    public function statusChange(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $status = $request->get('status');
        $order_via = $request->order_via ?: [];
        if (! empty($id) && ! empty($status)) {
            $order = Order::where('id', $id)->first();
            $statuss = OrderStatus::where('id', $status)->first();
            if ($order) {
                $old_status = $order->order_status_id;
                $order->order_status = $statuss->status;
                $order->order_status_id = $status;
                $order->save();

                $history = new OrderStatusHistory;
                $history->order_id = $order->id;
                $history->old_status = $old_status;
                $history->new_status = $status;
                $history->user_id = Auth::user()->id;
                $history->save();

                if (in_array('email', $order_via)) {
                    if (isset($request->sendmessage) && $request->sendmessage == '1') {
                        //Sending Mail on changing of order status
                        try {
                            $from_mail_address = $request->from_mail;
                            $to_mail_address = $request->to_mail;
                            // send order canellation email
                            if (strtolower($statuss->status) == 'cancel') {
                                $emailClass = (new OrderCancellationMail($order))->build();

                                if ($from_mail_address != '') {
                                    $emailClass->fromMailer = $from_mail_address;
                                }
                                if ($to_mail_address != '') {
                                    $order->customer->email = $to_mail_address;
                                }

                                $storeWebsiteOrder = $order->storeWebsiteOrder;
                                $email = Email::create([
                                    'model_id' => $order->id,
                                    'model_type' => Order::class,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $order->customer->email,
                                    'subject' => $emailClass->subject,
                                    'message' => $request->message,
                                    // 'message'          => $emailClass->render(),
                                    'template' => 'order-cancellation-update',
                                    'additional_data' => $order->id,
                                    'status' => 'pre-send',
                                    'store_website_id' => (isset($storeWebsiteOrder)) ? $storeWebsiteOrder->store_website_id : null,
                                    'is_draft' => 0,
                                ]);

                                SendEmail::dispatch($email)->onQueue('send_email');
                                $this->createEmailSendJourneyLog($id, 'Email type via Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                            } else {
                                $emailClass = (new OrderStatusChangeMail($order))->build();
                                if ($from_mail_address != '') {
                                    $emailClass->fromMailer = $from_mail_address;
                                }
                                if ($to_mail_address != '') {
                                    $order->customer->email = $to_mail_address;
                                }

                                $storeWebsiteOrder = $order->storeWebsiteOrder;
                                $email = Email::create([
                                    'model_id' => $order->id,
                                    'model_type' => Order::class,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $order->customer->email,
                                    'subject' => $emailClass->subject,
                                    'message' => $request->custom_email_content,
                                    'template' => 'order-status-update',
                                    'additional_data' => $order->id,
                                    'status' => 'pre-send',
                                    'is_draft' => 0,
                                ]);

                                SendEmail::dispatch($email)->onQueue('send_email');
                                $this->createEmailSendJourneyLog($id, 'Email type via Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                            }
                        } catch (Exception $e) {
                            $this->createEmailCommonExceptionLog($order->id, $e->getMessage(), 'email');
                            $this->createEmailSendJourneyLog($id, 'Email type via Error', Order::class, 'outgoing', '0', $from_mail_address, $to_mail_address, $emailClass->subject, $request->message, '', $e->getMessage(), $order->storeWebsiteOrder);
                            Log::info('Sending mail issue at the ordercontroller #2215 ->'.$e->getMessage());
                        }
                    } else {
                        $emailClass = (new OrderStatusChangeMail($order))->build();

                        $storeWebsiteOrder = $order->storeWebsiteOrder;
                        $email = Email::create([
                            'model_id' => $order->id,
                            'model_type' => Order::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $order->customer->email,
                            'subject' => $emailClass->subject,
                            'template' => 'order-status-update',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                            'is_draft' => 0,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                        $this->createEmailSendJourneyLog($id, 'Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                    }
                }

                if (in_array('sms', $order_via)) {
                    if (isset($request->sendmessage) && $request->sendmessage == '1') {
                        if (isset($order->storeWebsiteOrder)) {
                            $website = Website::where('id', $order->storeWebsiteOrder->website_id)->first();

                            $receiverNumber = $order->contact_detail;
                            TwilioSmsJob::dispatch($receiverNumber, $request->message, $website->store_website_id, $order->id);
                            $this->createEmailSendJourneyLog($id, 'Email type IVA SMS Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, 'Phone : '.$receiverNumber.' <br/> '.$request->message, '', '', $website->website_id);
                        }
                    }
                }
            }
            //Sending Mail on changing of order status
            if (isset($request->sendmessage) && $request->sendmessage == '1') {
                //sending order message to the customer
                UpdateOrderStatusMessageTpl::dispatch($order->id, request('message', null))->onQueue('customer_message');
            }
            $storeWebsiteOrder = StoreWebsiteOrder::where('order_id', $order->id)->first();
            if ($storeWebsiteOrder) {
                $website = StoreWebsite::find($storeWebsiteOrder->website_id);
                if ($website) {
                    $store_order_status = Store_order_status::where('order_status_id', $status)->where('store_website_id', $storeWebsiteOrder->website_id)->first();
                    if ($store_order_status) {
                        $magento_status = StoreMasterStatus::find($store_order_status->store_master_status_id);
                        if ($magento_status) {
                            $magentoHelper = new MagentoHelperv2;
                            $result = $magentoHelper->changeOrderStatus($order, $website, $magento_status->value, '', '');
                            $this->createEmailSendJourneyLog($id, 'Magento Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento replay', $request->message, '', '', $storeWebsiteOrder->website_id);
                            /**
                             *check if response has error
                             */
                            $response = $result->getData();
                            if (isset($response) && isset($response->status) && $response->status == false) {
                                $this->createOrderMagentoErrorLog($order->id, $response->error);
                                $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error', $response->error, '', '', $storeWebsiteOrder->website_id);

                                return response()->json($response->error, 400);
                            }
                        } else {
                            $this->createOrderMagentoErrorLog($order->id, 'Store MasterStatus Not Present');
                            $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store MasterStatus Not Present', '', '', '', $storeWebsiteOrder->website_id);

                            return response()->json('Store MasterStatus Not Present', 400);
                        }
                    } else {
                        $this->createOrderMagentoErrorLog($order->id, 'Store Order Status Not Present');
                        $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Order Status Not Present', '', '', '', $storeWebsiteOrder->website_id);

                        return response()->json('Store Order Status Not Present', 400);
                    }
                } else {
                    $this->createOrderMagentoErrorLog($order->id, 'Website Order Not Present');
                    $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Website Order Not Present', '', '', '', '');

                    return response()->json('Website Order Not Present', 400);
                }
                $storeWebsiteOrder->update(['order_id', $status]);
            } else {
                $this->createOrderMagentoErrorLog($order->id, 'Store Website Order Not Present');
                $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Website Order Not Present', '', '', '', '');

                return response()->json('Store Website Order Not Present', 400);
            }
        }

        return response()->json('Success', 200);
    }

    /**
     * This function is use for List Order Exception Error Log
     *
     * @param  Request  $request  Request
     */
    public function getOrderEmailSendJourneyLog(Request $request): view
    {
        try {
            $logs = new OrderEmailSendJourneyLog;

            $from_email = $request->get('from_email');
            $to_email = $request->get('to_email');
            $keyword = $request->get('keyword');

            if ($from_email) {
                $logs = $logs->where('from_email', $from_email);
            }

            if ($to_email) {
                $logs = $logs->where('to_email', $to_email);
            }

            if (! empty($keyword)) {
                $logs = $logs->where(function ($q) use ($keyword) {
                    $q->orWhere('subject', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('order_id', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('steps', 'LIKE', '%'.$keyword.'%');
                });
            }

            $logs = $logs->get();

            $orderJourney = $logs->groupBy('order_id')->map(function ($group) {
                return $group->last();
            });

            // Group the logs by order_id
            $groupedLogs = $logs->groupBy('order_id')->map(function ($item) {
                // Within each order_id group, further group the logs by steps starting with "<Step Names>"
                $groupedSteps = $item->groupBy(function ($log) {
                    if (strpos($log->steps, 'Status Change') === 0) {
                        return 'Status Change';
                    }
                    if (strpos($log->steps, 'Email type via Order update status') === 0) {
                        return 'Email type via Order update status';
                    }
                    if (strpos($log->steps, 'Email type via Error') === 0) {
                        return 'Email type via Error';
                    }
                    if (strpos($log->steps, 'Email type IVA SMS Order update status') === 0) {
                        return 'Email type IVA SMS Order update status';
                    }
                    if (strpos($log->steps, 'Magento Order update status') === 0) {
                        return 'Magento Order update status';
                    }
                    if (strpos($log->steps, 'Magento Error') === 0) {
                        return 'Magento Error';
                    }

                    // For items that do not start with '<Step Names>', return the original steps
                    return $log->steps;
                });

                // Sort the logs within each steps group by 'created_at' in descending order
                return $groupedSteps->map(function ($logs) {
                    return $logs->sortByDesc('created_at');
                });
            });

            $allLogs = OrderEmailSendJourneyLog::all();

            $groupByOrders = $allLogs->reject(function ($log) {
                return empty($log->order_id);
            })->groupBy('order_id')->keys()->toArray();

            $groupByFromEmail = $allLogs->reject(function ($log) {
                return empty($log->from_email);
            })->groupBy('from_email')->keys()->toArray();

            $groupByToEmail = $allLogs->reject(function ($log) {
                return empty($log->to_email);
            })->groupBy('to_email')->keys()->toArray();

            if (count($orderJourney) > 0) {
                return view('orders.email_send_journey', compact('orderJourney', 'groupedLogs', 'groupByOrders', 'groupByFromEmail', 'groupByToEmail'));
            } else {
                return redirect()->back()->with('error', 'Record not found');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getOrderEmailSendJourneyStepLog(Request $request): View
    {
        $stepName = $request->input('step_name');
        $orderId = $request->input('order_id');

        // Fetch the step history data from the database using the $stepName and $orderId
        $stepHistoryData = OrderEmailSendJourneyLog::where('steps', 'LIKE', '%'.$stepName.'%')
            ->where('order_id', $orderId)
            ->orderByDesc('created_at')
            ->get();

        // Return the step history data in a Blade view (step_history_modal_content.blade.php)
        return view('orders.email_send_journey_step_history_modal_content', compact('stepHistoryData'));
    }

    /**
     * This function is used to list the Order Status Journey
     *
     * @param  Request  $request  Request
     */
    public function getOrderStatusJourney(Request $request): view
    {
        $orders = Order::paginate(25);
        $orderStatusList = OrderHelper::getStatus();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('orders.partials.order-status', compact('orders', 'orderStatusList'))->render(),
            ], 200);
        }

        return view('orders.order-status-journey', compact('orders', 'orderStatusList'));
    }

    /**
     * This function is used to list the Order Journey
     *
     * @param  Request  $request  Request
     */
    public function getOrderJourney(Request $request): view
    {
        $filter_order = $request->input('filter_order');
        $filer_customer_list = $request->filer_customer_list ?? '';

        $orders = Order::with('order_product', 'order_product.order_product_details', 'customer');

        if ($filter_order != '') {
            $orders = $orders->where('order.order_id', $filter_order);
        }

        if ($filer_customer_list != '') {
            $orders = $orders->whereHas('customer', function ($query) use ($filer_customer_list) {
                $query->whereIn('customers.id', $filer_customer_list);
            });
        }
        $orders = $orders->latest('id')->paginate(25);

        $orderStatusList = OrderStatus::pluck('status', 'id')->all();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'get-order-journey')->first();

        $dynamicColumnsToShowoj = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowoj = json_decode($hideColumns, true);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('orders.partials.order-journey', compact('orders', 'orderStatusList', 'dynamicColumnsToShowoj'))->render(),
            ], 200);
        }

        $customer_list = Customer::pluck('name', 'id');

        return view('orders.order-journey', compact('orders', 'orderStatusList', 'dynamicColumnsToShowoj', 'customer_list'));
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'get-order-journey')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'get-order-journey';
            $column->column_name = json_encode($request->column_oj);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'get-order-journey';
            $column->column_name = json_encode($request->column_oj);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function getOrderProductsList(Request $request): JsonResponse
    {
        $order = Order::where('id', $request->id)->first();

        $productsIds = [];
        if (! empty($order->order_product)) {
            foreach ($order->order_product as $value) {
                $productsIds[] = $value->product_id;
            }
        }

        $datas = Product::select('name')->whereIn('id', $productsIds)->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    /**
     * This function is use for List Order Exception Error Log
     *
     * @param  Request  $request  Request
     */
    public function getOrderExceptionErrorLog(Request $request): JsonResponse
    {
        try {
            $orderError = EmailCommonExceptionLog::where('order_id', $request->order_id)->get();

            if (count($orderError) > 0) {
                return response()->json(['code' => 200, 'data' => $orderError]);
            } else {
                return response()->json(['code' => 500, 'message' => 'Could not find any error Log']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function is use for List Order Exception Error Log
     *
     * @param  Request  $request  Request
     */
    public function getOrderEmailSendLog(Request $request): JsonResponse
    {
        try {
            $orderError = Email::where('model_id', $request->order_id)->get();

            if (count($orderError) > 0) {
                return response()->json(['code' => 200, 'data' => $orderError]);
            } else {
                return response()->json(['code' => 500, 'message' => 'Could not find any error Log']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function is use for List Order SMS send Log
     *
     * @param  Request  $request  Request
     * @param  mixed  $id
     */
    public function getOrderSmsSendLog($id): JsonResponse
    {
        try {
            $smsSendLogs = ChatMessage::where('order_id', $id)->latest()->get();

            if (count($smsSendLogs) > 0) {
                return response()->json(['code' => 200, 'data' => $smsSendLogs]);
            } else {
                return response()->json(['code' => 500, 'message' => 'Could not find any Log']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function user for create email commaon  error list
     *
     * @param  $order  (INT),
     * @param  $logMsg  (string)
     * @param  mixed  $order_id
     * @param  mixed  $type
     * @return void
     */
    public function createEmailCommonExceptionLog($order_id = '', $logMsg = '', $type = '')
    {
        try {
            EmailCommonExceptionLog::create([
                'order_id' => $order_id,
                'exception_error' => $logMsg,
                'type' => $type,
            ]);
        } catch (Exception $e) {
            EmailCommonExceptionLog::create([
                'order_id' => $order_id,
                'log_msg' => $e->getMessage(),
                'type' => $type,
            ]);
        }
    }

    /**
     * This function user for get magent to order error list
     *
     * @param  $order  (INT),
     * @param  $logMsg  (string)
     */
    public function getOrderMagentoErrorLogList(Request $request): JsonResponse
    {
        try {
            $getOrderList = OrderMagentoErrorLog::where('order_id', $request->order_id)->get();
            $html = '';
            foreach ($getOrderList as $getOrder) {
                $html .= '<tr>';
                $html .= '<td>'.$getOrder->id.'</td>';
                $html .= '<td>'.$getOrder->log_msg.'</td>';
                $html .= '<td>'.$getOrder->created_at.'</td>';
                $html .= '</tr>';
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Log Listed successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Sorry , there is no matching order log']);
        }
    }

    /**
     * This function user for get  order payuload list
     *
     * @param  $order  (INT),
     * @param  $logMsg  (string)
     */
    public function getOrderPayloadList(Request $request): JsonResponse
    {
        try {
            $getOrderList = Order::where('id', $request->order_id)->get();
            $html = '';
            foreach ($getOrderList as $getOrder) {
                $html .= '<tr>';
                $html .= '<td>'.$getOrder->id.'</td>';
                $html .= '<td>'.$getOrder->order_id.'</td>';
                $html .= '<td>'.$getOrder->payload.'</td>';
                $html .= '<td>'.$getOrder->created_at.'</td>';
                $html .= '</tr>';
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Payload Listed successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    /**
     * This function user for create magent to order error list
     *
     * @param  $order  (INT),
     * @param  $logMsg  (string)
     * @param  mixed  $order_id
     * @return void
     */
    public function createOrderMagentoErrorLog($order_id, $logMsg)
    {
        try {
            OrderMagentoErrorLog::create([
                'order_id' => $order_id,
                'log_msg' => $logMsg,
            ]);
        } catch (Exception $e) {
            OrderMagentoErrorLog::create([
                'order_id' => $order_id,
                'log_msg' => $e->getMessage(),
            ]);
        }
    }

    public function sendInvoice(Request $request, $id): JsonResponse
    {
        $order = Order::where('id', $id)->first();

        if ($order) {
            $data['order'] = $order;
            $data['customer'] = $order->customer;

            if ($order->customer) {
                Mail::to($order->customer->email)->send(new OrderInvoice($data));

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Email sent successfully']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Sorry , there is no matching order found']);
    }

    public function sendOrderEmail(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);
        if (! $order->is_sent_offline_confirmation()) {
            if ($order->order_type == 'offline') {
                $emailClass = (new OrderConfirmation($order))->build();

                $storeWebsiteOrder = $order->storeWebsiteOrder;
                $email = Email::create([
                    'model_id' => $order->id,
                    'model_type' => Order::class,
                    'from' => $emailClass->fromMailer,
                    'to' => $order->customer->email,
                    'subject' => 'New Order # '.$order->order_id,
                    'message' => $emailClass->render(),
                    'template' => 'order-confirmation',
                    'additional_data' => $order->id,
                    'status' => 'pre-send',
                    'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                    'is_draft' => 0,
                ]);

                SendEmail::dispatch($email)->onQueue('send_email');
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'You have successfully sent confirmation email!']);
    }

    public function previewInvoice(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();
        if ($order) {
            $data['order'] = $order;
            $data['customer'] = $order->customer;
            if ($order->customer) {
                $invoice = new OrderInvoice($data);

                return $invoice->preview();
            }
        }

        return abort('404');
    }

    public function viewInvoice($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        if ($invoice) {
            $data['invoice'] = $invoice;
            $data['orders'] = $invoice->orders;
            $data['buyerDetails'] = $invoice->orders[0]->customer;
            if ($invoice->orders) {
                $viewInvoice = new ViewInvoice($data);

                return $viewInvoice->preview();
            }
        }

        return abort('404');
    }

    //TODO downloadInvoice - added by jammer
    public function downloadInvoice(Request $request, $id)
    {
        $invoice = Invoice::with('orders.duty_tax')->where('id', $id)->first();
        if ($invoice) {
            $data['invoice'] = $invoice;
            $data['orders'] = $invoice->orders;
            $data['buyerDetails'] = $invoice->orders[0]->customer;
            if ($invoice->orders) {
                $viewInvoice = new ViewInvoice($data);

                return $viewInvoice->download();
            }
        }

        return abort('404');
    }

    public function generateRateRequet(Request $request): JsonResponse
    {
        $rateReq = new GetRateRequest('soap');
        $rateReq->setRateEstimates('Y');
        $rateReq->setDetailedBreakDown('Y');
        $rateReq->setShipper([
            'city' => $request->get('from_customer_city'),
            'postal_code' => $request->get('from_customer_pincode'),
            'country_code' => $request->get('from_customer_country'),
            'person_name' => $request->get('from_customer_name'),
            'company_name' => $request->get('from_company_name'),
            'phone' => $request->get('from_customer_phone'),
        ]);
        $rateReq->setRecipient([
            'city' => $request->get('customer_city'),
            'postal_code' => $request->get('customer_pincode'),
            'country_code' => $request->get('customer_country', 'IN'),
            'person_name' => $request->get('customer_name'),
            'company_name' => $request->get('company_name', ''),
            'phone' => $request->get('customer_phone'),
        ]);

        $rateReq->setShippingTime(gmdate("Y-m-d\TH:i:s-05:00", strtotime($request->get('pickup_time'))));
        $rateReq->setDeclaredValue($request->get('amount'));
        $rateReq->setDeclaredValueCurrencyCode($request->get('currency'));
        $rateReq->setPackages([
            [
                'weight' => $request->get('actual_weight'),
                'length' => $request->get('box_length'),
                'width' => $request->get('box_width'),
                'height' => $request->get('box_height'),
            ],
        ]);

        $response = $rateReq->call();
        if (! $response->hasError()) {
            $charges = $response->getChargesBreakDown();

            return response()->json(['code' => 200, 'data' => $charges]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => ($response->getErrorMessage()) ? implode('<br>', $response->getErrorMessage()) : 'Rate request not generated']);
        }
    }

    public function generateAWBDHL(GenerateAWBDHLOrderRequest $request): JsonResponse
    {

        // find order and customer
        $order = Order::find($request->order_id);

        if (! empty($order)) {
            $order->customer->name = $request->customer_name;
            $order->customer->address = $request->customer_address1;
            $order->customer->city = $request->customer_address2;
            $order->customer->pincode = $request->customer_pincode;
            $order->customer->save();
        }

        $rateReq = new CreateShipmentRequest('soap');
        $rateReq->setShipper([
            'street' => $request->get('from_customer_address1'),
            'city' => $request->get('from_customer_city'),
            'postal_code' => $request->get('from_customer_pincode'),
            'country_code' => $request->get('from_customer_country'),
            'person_name' => $request->get('from_customer_name'),
            'company_name' => $request->get('from_company_name'),
            'phone' => $request->get('from_customer_phone'),
        ]);

        $rateReq->setRecipient([
            'street' => $request->get('customer_address1'),
            'city' => $request->get('customer_city'),
            'postal_code' => $request->get('customer_pincode'),
            'country_code' => $request->get('customer_country', 'IN'),
            'person_name' => $request->get('customer_name'),
            'company_name' => $request->get('customer_name'),
            'phone' => $request->get('customer_phone'),
            'email' => $request->get('customer_email'),
        ]);

        $rateReq->setShippingTime(gmdate("Y-m-d\TH:i:s", strtotime($request->get('pickup_time'))).' GMT+04:00');

        $declaredValue = 0;
        if (! empty($request->items)) {
            foreach ($request->items as $itm) {
                $qty = is_numeric($itm['qty']) ? $itm['qty'] : 1;
                $declaredValue += $itm['unit_price'] * $qty;
            }
        }

        $rateReq->setDeclaredValue($declaredValue);
        $rateReq->setDescription($request->description);
        if ($request->duty_mode != null) {
            $rateReq->setPaymentInfo($request->duty_mode);
        }
        $rateReq->setPackages([
            [
                'weight' => (float) $request->get('actual_weight'),
                'length' => $request->get('box_length'),
                'width' => $request->get('box_width'),
                'height' => $request->get('box_height'),
                'note' => $request->get('notes'),
            ],
        ]);

        $phone = ! empty($request->get('customer_phone')) ? $request->get('customer_phone') : $order->customer->phone;
        $rateReq->setMobile($phone);
        $invoiceNumber = ($order) ? $order->order_id.'-'.date('Y-m-d-h-i-s') : 'OFFLINE'.'-'.date('Y-m-d-h-i-s');
        $rateReq->setInvoiceNumber($invoiceNumber);
        $rateReq->setPaperLess(true);
        $rateReq->setItems($request->items);

        $response = $rateReq->call();

        if ($response->hasError()) {
            $message = $response->getErrorMessage();
            $isPaperLessTradeIssue = false;

            if (! empty($message)) {
                foreach ($message as $m) {
                    $pos = strpos($m, "'WY' is not available between this origin and destination");
                    if ($pos !== false) {
                        $isPaperLessTradeIssue = true;
                    }
                }
            }
            // set paperless trade fix
            if ($isPaperLessTradeIssue) {
                $rateReq->setPaperLess(0);
                $response = $rateReq->call(true);
            }
        }

        if (! $response->hasError()) {
            $receipt = $response->getReceipt();
            if (! empty($receipt) && ! empty($receipt['label_format'])) {
                if (strtolower($receipt['label_format']) == 'pdf') {
                    Storage::disk('files')->put('waybills/'.$receipt['tracking_number'].'_package_slip.pdf', base64_decode($receipt['label_image'], true));

                    $waybill = new Waybill;
                    $waybill->order_id = ($order) ? $order->id : null;
                    $waybill->awb = $receipt['tracking_number'];
                    $waybill->box_width = $request->box_width;
                    $waybill->box_height = $request->box_height;
                    $waybill->box_length = $request->box_length;
                    $waybill->actual_weight = (float) $request->get('actual_weight');
                    $waybill->package_slip = $receipt['tracking_number'].'_package_slip.pdf';
                    $waybill->pickup_date = $request->pickup_time;
                    //newly added
                    $waybill->from_customer_id = ($order) ? $order->customer_id : null;
                    $waybill->from_customer_name = $request->from_customer_name;
                    $waybill->from_city = $request->from_customer_city;
                    $waybill->from_country_code = $request->from_customer_country;
                    $waybill->from_customer_phone = $request->from_customer_phone;
                    $waybill->from_customer_address_1 = $request->from_customer_address1;
                    $waybill->from_customer_address_2 = $request->from_customer_address2;
                    $waybill->from_customer_pincode = $request->from_customer_pincode;
                    $waybill->from_company_name = $request->from_company_name;
                    $waybill->to_customer_id = null;
                    $waybill->to_customer_name = $request->customer_name;
                    $waybill->to_city = $request->customer_city;
                    $waybill->to_country_code = $request->customer_country;
                    $waybill->to_customer_phone = $request->customer_phone;
                    $waybill->to_customer_address_1 = $request->customer_address1;
                    $waybill->to_customer_address_2 = $request->customer_address2;
                    $waybill->to_customer_pincode = $request->customer_pincode;
                    $waybill->to_company_name = $request->company_name;
                    $waybill->save();
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Receipt Created successfully']);
            }
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => ($response->getErrorMessage()) ? implode('<br>', $response->getErrorMessage()) : 'Receipt not created']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong can not create receipt']);
    }

    public function trackPackageSlip(Request $request): JsonResponse
    {
        $awb = $request->get('awb');
        $wayBill = Waybill::where('awb', $awb)->first();
        if (! empty($wayBill)) {
            // check from the awb
            $trackShipment = new TrackShipmentRequest;
            $trackShipment->setAwbNumbers([$awb]);
            $results = $trackShipment->call();
            $response = $results->getResponse();
            $view = (string) view('partials.dhl.tracking', compact('response'));

            return response()->json(['code' => 200, '_h' => $view, 'awb' => $awb]);
        }

        return response()->json(['code' => 200, '_h' => 'No records found']);
    }

    public function viewAllInvoices(Request $request): View
    {
        $invoices = Invoice::with('orders.order_product', 'orders.customer')->orderByDesc('id');
        if (! empty($request->invoice_date)) {
            $invoices = $invoices->whereDate('invoice_date', '>=', $request->invoice_date);
        }
        if (! empty($request->invoice_to_date)) {
            $invoices = $invoices->whereDate('invoice_date', '<=', $request->invoice_to_date);
        }

        if (! empty($request->invoice_number)) {
            $invoices = $invoices->whereIn('invoice_number', $request->invoice_number);
        }

        if (! empty($request->customer_id)) {
            $invoices = $invoices->WhereHas('orders.customer', function ($query) use ($request) {
                $query->whereIn('customer_id', $request['customer_id']);
            });
        }

        if (! empty($request->store_website_id)) {
            $invoices = $invoices->WhereHas('orders.customer', function ($query) use ($request) {
                $query->whereIn('store_website_id', $request['store_website_id']);
            });
        }
        $invoices = $invoices->paginate(30);

        $invoice_array = $invoices->toArray();
        $invoice_id = array_column($invoice_array['data'], 'id');

        $orders_array = Order::whereIn('invoice_id', $invoice_id)->get();

        $duty_shipping = [];
        foreach ($orders_array as $order) {
            $duty_shipping[$order->id]['id'] = $order->id;

            $website_code_data = $order->duty_tax;
            if ($website_code_data != null) {
                $product_qty = count($order->order_product);

                $code = $website_code_data->website_code->code;

                $duty_countries = $website_code_data->website_code->duty_of_country;
                $shipping_countries = $website_code_data->website_code->shipping_of_country($code);

                $duty_amount = ($duty_countries->default_duty * $product_qty);
                $shipping_amount = ($shipping_countries->price * $product_qty);

                $duty_shipping[$order->invoice_id]['shipping'] = $duty_amount;
                $duty_shipping[$order->invoice_id]['duty'] = $shipping_amount;
            } else {
                $duty_shipping[$order->invoice_id]['shipping'] = 0;
                $duty_shipping[$order->invoice_id]['duty'] = 0;
            }
        }

        $invoiceNumber = Invoice::orderByDesc('id')->select('id', 'invoice_number')->get();
        $customerName = Customer::select('id', 'name')->orderByDesc('id')->groupBy('name')->get();
        $websiteName = StoreWebsite::select('id', 'website')->orderByDesc('id')->groupBy('website')->get();

        return view('orders.invoices.index', compact('invoices', 'duty_shipping', 'invoiceNumber', 'customerName', 'websiteName'));
    }

    public function saveLaterCreate(Request $request)
    {
        $invoice = Invoice::with('orders.duty_tax')->where('id', $request->invoiceId)->first();
        if ($invoice) {
            $data['invoice'] = $invoice;
            $data['orders'] = $invoice->orders;
            $data['buyerDetails'] = $invoice->orders[0]->customer;
            $data['savePDF'] = true;
            if ($invoice->orders) {
                $viewInvoice = new ViewInvoice($data);
                $file = $viewInvoice->download();
                $invoice = new InvoiceLater;
                $invoice->invoice_id = $request->invoiceId;
                $invoice->invoice_number = $request->invoiceNumber;
                $invoice->file_name = $file;
                $invoice->created_at = date('Y-m-d H:i:s');
                $invoice->updated_at = date('Y-m-d H:i:s');
                $invoice->save();
            }
        }
    }

    public function saveLaterList(Request $request)
    {
        $autoDeleteDays = config('constants.PRINT_LATER_AUTO_DELETE_DAYS');
        InvoiceLater::where('created_at', '<', Carbon::now()->subDays($autoDeleteDays))->delete();
        $invoiceList = new InvoiceLater;
        if ($request->has('from_date') && ! empty($request->from_date)) {
            $invoiceList = $invoiceList->where('created_at', '>=', $request->from_date.' 00:00:00');
        }
        if ($request->has('to_date') && ! empty($request->to_date)) {
            $invoiceList = $invoiceList->where('created_at', '<=', $request->to_date.' 23:59:59');
        }
        $invoiceList = $invoiceList->paginate(20);
        $ids = $invoiceList->pluck('invoice_id')->toArray();
        $invoices = Invoice::with('orders.order_product', 'orders.customer')->whereIn('id', $ids)->get();

        if ($request->has('invoice_num') && ! empty($request->invoice_num)) {
            $invoices = $invoices->WhereIn('invoice_number', $request->invoice_num);
        }

        if ($request->has('customer_name') && ! empty($request->customer_name)) {
            $customerNames = $request->customer_name;
            $invoices = $invoices->filter(function ($invoice) use ($customerNames) {
                return $invoice->orders->contains(function ($order) use ($customerNames) {
                    return $order->customer && in_array($order->customer->name, $customerNames);
                });
            });
        }

        $invoice_array = $invoices->toArray();

        $invoice_id = array_column($invoice_array, 'id');
        $orders_array = Order::whereIn('invoice_id', $invoice_id)->get();

        $duty_shipping = [];
        foreach ($orders_array as $order) {
            $website_code_data = $order->duty_tax;
            if ($website_code_data != null) {
                $code = $website_code_data->website_code->code;
            }
        }
        $invoiceNumber = Invoice::orderByDesc('id')->select('id', 'invoice_number')->groupBy('invoice_number')->get();
        $customerName = Customer::select('id', 'name')->orderByDesc('id')->groupBy('name')->get();
        $websiteName = StoreWebsite::select('id', 'website')->orderByDesc('id')->groupBy('website')->get();

        return view('orders.invoices.saveLaterInvoice', compact('invoiceList', 'invoices', 'duty_shipping', 'invoiceNumber', 'customerName', 'websiteName'));
    }

    public function ViewsaveLaterList(Request $request, $id): Response
    {
        $invoice = InvoiceLater::where('invoice_id', $id)->first();
        if (! empty($invoice)) {
            return response(file_get_contents(base_path().'/public/pdf/'.$invoice->file_name))->withHeaders(['content-type' => 'application/pdf']);
        }
    }

    public function addInvoice($id): View
    {
        $firstOrder = Order::find($id);
        if ($firstOrder->customer) {
            if ($firstOrder->customer->country) {
                $prefix = substr($firstOrder->customer->country, 0, 3);
            } else {
                $prefix = 'Lux';
            }
        } else {
            $prefix = 'Lux';
        }
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix.'%')->orderByDesc('id')->first();
        if ($lastInvoice) {
            $inoicePieces = explode('-', $lastInvoice->invoice_number);
            $nextInvoiceNumber = $inoicePieces[1] + 1;
        } else {
            $nextInvoiceNumber = '1001';
        }
        $invoice_number = $prefix.'-'.$nextInvoiceNumber;
        $more_orders = Order::where('customer_id', $firstOrder->customer_id)->where('invoice_id', null)->get();

        return view('orders.invoices.add', compact('firstOrder', 'invoice_number', 'more_orders'));
    }

    public function submitInvoice(Request $request): RedirectResponse
    {
        if (! $request->invoice_number) {
            return redirect()->back()->with('error', 'Invoice number is mandatory');
        }
        if (! $request->first_order_id) {
            return redirect()->back()->with('error', 'Invalid approach');
        }
        $firstOrder = Order::where('invoice_id', null)->where('id', $request->first_order_id)->first();
        if (! $firstOrder) {
            return redirect()->back()->with('error', 'This order is already associated with an invoice');
        }

        $customerShippingAddress = [
            'address_type' => 'shipping',
            'city' => $firstOrder->customer->city,
            'country_id' => $firstOrder->customer->country,
            'email' => $firstOrder->customer->email,
            'firstname' => $firstOrder->customer->name,
            'postcode' => $firstOrder->customer->pincode,
            'street' => $firstOrder->customer->address,
            'order_id' => $request->first_order_id,
        ];
        OrderCustomerAddress::insert($customerShippingAddress);

        $invoice = new Invoice;
        $invoice->invoice_number = $request->invoice_number;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->save();
        $firstOrder->update(['invoice_id' => $invoice->id]);
        if ($request->order_ids && count($request->order_ids) > 0) {
            $orders = Order::whereIn('id', $request->order_ids)->get();
            foreach ($orders as $order) {
                if ($order->id != $request->first_order_id) {
                    $order->update(['invoice_id' => $invoice->id]);
                }
            }
        }

        return redirect()->action([OrderController::class, 'viewAllInvoices']);
    }

    //TODO::Update Invoice Address
    public function updateCustomerInvoiceAddress(Request $request): RedirectResponse
    {
        Customer::where('id', $request->codex)->update([
            'country' => $request->country,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'address' => $request->address,
        ]);

        Session::flash('actSuccess', 'Address updated successfully!');

        return redirect()->back();
    }

    public function editInvoice($id): View
    {
        $invoice = Invoice::where('id', $id)->first();
        $order = Order::where('invoice_id', $invoice['id'])->first();
        $more_orders = Order::where('customer_id', $order['customer_id'])->where(function ($query) use ($id) {
            $query->where('invoice_id', $id)
                ->orWhere('invoice_id', null);
        })->get();

        return view('orders.invoices.edit', compact('invoice', 'more_orders'));
    }

    //TODO::Working Invoice without existing order
    public function createInvoiceWithoutOrderNumber(): View
    {
        return view('orders.invoices.invoice-without-existing-order');
    }

    public function submitEdit(Request $request): RedirectResponse
    {
        $invoice = Invoice::find($request->id);
        if (! $request->invoice_date || $request->invoice_date == '') {
            return redirect()->back()->with('error', 'Invalid approach');
        }
        $invoice->update(['invoice_date' => $request->invoice_date]);
        Order::where('invoice_id', $request->id)->update(['invoice_id' => null]);
        if ($request->order_ids && count($request->order_ids) > 0) {
            $orders = Order::whereIn('id', $request->order_ids)->get();
            foreach ($orders as $order) {
                $order->update(['invoice_id' => $invoice->id]);
            }
        }

        return redirect()->action([OrderController::class, 'viewAllInvoices']);
    }

    /**
     * This function is use to get invoice customer email address.
     *
     *
     * @return array
     */
    public function getInvoiceCustomerEmail(Request $request, int $id)
    {
        $invoice = Invoice::where('id', $id)->first();

        return [
            'email' => $invoice->orders[0]->customer->email,
            'id' => $id,
        ];
    }

    /**
     * This function is use to Email invoice to customer
     */
    public function mailInvoice(Request $request, int $id): JsonResponse
    {
        try {
            $invoice = Invoice::where('id', $id)->first();
            if ($invoice) {
                $data['invoice'] = $invoice;
                $data['orders'] = $invoice->orders;
                if ($invoice->orders) {
                    Mail::to($invoice->orders[0]->customer->email)->send(new ViewInvoice($data));

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Email sent successfully']);
                }
            } else {
                Invoice::where('id', $id)->update(['invoice_error_log' => 'Sorry , there is no matching order found']);

                return response()->json(['code' => 500, 'data' => [], 'message' => 'Sorry , there is no matching order found']);
            }
        } catch (Exception $e) {
            Log::info('Sending mail issue at the ordercontroller invoice log->'.$e->getMessage());
            Invoice::where('id', $id)->update(['invoice_error_log' => $e->getMessage()]);

            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function mailInvoiceMultiSelect(Request $request, $index = ''): JsonResponse
    {
        try {
            $invoices = Invoice::whereIn('id', $request->invoice_id)->get();
            foreach ($invoices as $invoice) {
                if ($invoice) {
                    $data['invoice'] = $invoice;
                    $data['orders'] = $invoice->orders;
                    if ($invoice->orders) {
                        Mail::to($invoice->orders[0]->customer->email)->send(new ViewInvoice($data));
                    }
                } else {
                    Invoice::where('id', $invoice->id)->update(['invoice_error_log' => 'Sorry , there is no matching order found']);
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Email sent successfully']);
        } catch (Exception $e) {
            Log::info('Sending mail issue at the ordercontroller invoice log->'.$e->getMessage());

            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function GetInvoiceOrderUsers(Request $request): JsonResponse
    {
        try {
            $customerName = Customer::select('id', 'name')->where('name', 'LIKE', '%'.$request->searchTerm.'%')->orderByDesc('id')->groupBy('name')->get();
            $data = [];
            foreach ($customerName as $value) {
                $data[] = ['id' => $value['id'], 'text' => $value['name']];
            }
            echo json_encode($data);
        } catch (Exception $e) {
            Log::info('Having issue at the ordercontroller invoice log->'.$e->getMessage());

            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function viewAllStatuses(Request $request): View
    {
        $request->order_status_id ? $erp_status = $request->order_status_id :
            $erp_status = null;
        $store = null;
        $query = Store_order_status::query();
        if ($request->order_status_id) {
            $query = $query->where('order_status_id', $request->order_status_id);
            $erp_status = $request->order_status_id;
        }
        if ($request->store_website_id) {
            $query = $query->where('store_website_id', $request->store_website_id);
            $store = $request->store_website_id;
        }
        $store_order_statuses = $query->paginate(20);
        $order_statuses = OrderStatus::all();
        $store_website = StoreWebsite::all();

        return view('orders.statuses.index', compact('store_order_statuses', 'order_statuses', 'store_website', 'erp_status', 'store'));
    }

    public function viewFetchStatus(): View
    {
        $store_website = StoreWebsite::all();

        return view('orders.statuses.fetch-order-status', compact('store_website'));
    }

    public function fetchStatus(Request $request): RedirectResponse
    {
        $website = StoreWebsite::find($request->store_website_id);
        $magentoHelper = new MagentoHelperv2;
        $result = $magentoHelper->fetchOrderStatus($website);
        if ($result) {
            if ($result->status() == 200) {
                $statuses = json_decode($result->getContent());

                foreach ($statuses as $status) {
                    StoreMasterStatus::updateOrCreate([
                        'store_website_id' => $request->store_website_id,
                        'value' => $status->value,
                    ], [
                        'label' => $status->label,
                    ]);
                }
                $this->store_order_status_history_create($request, $result, $request->store_website_id);
            } else {
                return redirect()->back()->with('error', $result->getContent());
            }
        } else {
            return redirect()->back()->with('error', 'Could not fetch the statuses');
        }

        return redirect()->back()->with('success', 'Status successfully updated');
    }

    public function viewCreateStatus(): View
    {
        $order_statuses = OrderStatuses::all();
        $store_website = StoreWebsite::all();
        $store_master_statuses = StoreMasterStatus::all();

        return view('orders.statuses.create', compact('order_statuses', 'store_website', 'store_master_statuses'));
    }

    public function createStatus(CreateStatusOrderRequest $request): RedirectResponse
    {
        $input = $request->except('_token');
        $isExist = Store_order_status::where('order_status_id', $request->order_status_id)->where('store_website_id', $request->store_website_id)->where('store_master_status_id', $request->store_master_status_id)->first();
        if (! $isExist) {
            Store_order_status::create($input);

            $this->store_order_status_history_create($request, '', '');

            return redirect()->back();
        } else {
            return redirect()->back()->with('warning', 'Already exists');
        }
    }

    public function viewEdit($id): View
    {
        $store_order_status = Store_order_status::find($id);
        $order_statuses = OrderStatuses::all();
        $store_website = StoreWebsite::all();
        $store_master_statuses = StoreMasterStatus::where('store_website_id', $store_order_status->store_website_id)->get();

        return view('orders.statuses.edit', compact('store_order_status', 'order_statuses', 'store_website', 'store_master_statuses'));
    }

    public function editStatus($id, EditStatusOrderRequest $request): RedirectResponse
    {
        $input = $request->except('_token');
        $isExist = Store_order_status::where('order_status_id', $request->order_status_id)->where('store_website_id', $request->store_website_id)->where('store_master_status_id', $request->store_master_status_id)->first();

        if (! $isExist) {
            $this->store_order_status_history_update($request, '', $id);
            $store_order_status = Store_order_status::find($id);
            $store_order_status->update($input);

            return redirect()->back();
        } else {
            return redirect()->back()->with('warning', 'Already exists');
        }
    }

    public function fetchMasterStatus($id)
    {
        $store_master_statuses = StoreMasterStatus::where('store_website_id', $id)->get();

        return $store_master_statuses;
    }

    public function deleteBulkOrders(Request $request): JsonResponse
    {
        foreach ($request->ids as $id) {
            Order::where('id', $id)->delete();
        }

        return response()->json(['message' => 'Order has been archived']);
    }

    public function updateCustomer(Request $request): JsonResponse
    {
        if ($request->update_type == 1) {
            $ids = explode(',', $request->selected_orders);
            foreach ($ids as $id) {
                $order = Order::where('id', $id)->first();
                if ($order && $request->customer_message && $request->customer_message != '') {
                    UpdateOrderStatusMessageTpl::dispatch($order->id, $request->customer_message)->onQueue('customer_message');
                }
            }
        } else {
            $ids = explode(',', $request->selected_orders);
            foreach ($ids as $id) {
                if (! empty($id) && $request->order_status) {
                    $order = Order::where('id', $id)->first();
                    $statuss = OrderStatus::where('id', $request->order_status)->first();
                    if ($order) {
                        $order->order_status = $statuss->status;
                        $order->order_status_id = $request->order_status;
                        $order->save();

                        // this code is duplicate we need to fix it
                        //Sending Mail on changing of order status
                        $mailingListCategory = MailinglistTemplateCategory::where('title', 'Order Status Change')->first();
                        if ($mailingListCategory) {
                            if ($order->storeWebsiteOrder) {
                                $templateData = MailinglistTemplate::where('category_id', $mailingListCategory->id)->where('store_website_id', $order->storeWebsiteOrder->website_id)->first();
                            } else {
                                $templateData = MailinglistTemplate::where('name', 'Order Status Change')->first();
                            }
                            // @todo put the function to send mail from specific store emails
                            if ($templateData) {
                                $arrToReplace = ['{FIRST_NAME}', '{ORDER_STATUS}'];
                                $valToReplace = [$order->customer->name, $statuss->status];
                                $bodyText = str_replace($arrToReplace, $valToReplace, $templateData->static_template);

                                $storeEmailAddress = EmailAddress::where('store_website_id', $order->customer->store_website_id)->first();
                                if ($storeEmailAddress) {
                                    $emailData['subject'] = $templateData->subject;
                                    $emailData['static_template'] = $bodyText;
                                    $emailData['from'] = $storeEmailAddress->from_address;

                                    $emailClass = (new OrderStatusMail($emailData))->build();

                                    $storeWebsiteOrder = $order->storeWebsiteOrder;
                                    $email = Email::create([
                                        'model_id' => $order->customer->id,
                                        'model_type' => Customer::class,
                                        'from' => $storeEmailAddress->from_address,
                                        'to' => $order->customer->email,
                                        'subject' => $emailClass->subject,
                                        'message' => $emailClass->render(),
                                        'template' => 'order-status-update',
                                        'additional_data' => $order->id,
                                        'status' => 'pre-send',
                                        'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                                    ]);

                                    SendEmail::dispatch($email)->onQueue('send_email');
                                }
                            }
                        }
                        // this code is duplicate we need to fix it

                        UpdateOrderStatusMessageTpl::dispatch($order->id, $request->customer_message)->onQueue('customer_message');

                        $storeWebsiteOrder = StoreWebsiteOrder::where('order_id', $order->id)->first();
                        if ($storeWebsiteOrder) {
                            $website = StoreWebsite::find($storeWebsiteOrder->website_id);
                            if ($website) {
                                $store_order_status = Store_order_status::where('order_status_id', $request->order_status)->where('store_website_id', $storeWebsiteOrder->website_id)->first();
                                if ($store_order_status) {
                                    $magento_status = StoreMasterStatus::find($store_order_status->store_master_status_id);
                                    if ($magento_status) {
                                        $magentoHelper = new MagentoHelperv2;
                                        $magentoHelper->changeOrderStatus($order, $website, $magento_status->value, '', '');
                                    }
                                }
                            }
                            $storeWebsiteOrder->update(['order_id', $request->order_status]);
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Successful'], 200);
    }

    public function searchOrderForInvoice(Request $request)
    {
        $term = $request->q;
        $orders = Order::leftJoin('customers', 'customers.id', 'orders.customer_id')
            ->where('orders.invoice_id', null)
            ->where(function ($q) use ($term) {
                $q->where('orders.order_id', 'like', '%'.$term.'%')
                    ->orWhere('orders.order_type', $term)
                    ->orWhere('orders.sales_person', Helpers::getUserIdByName($term))
                    ->orWhere('orders.received_by', Helpers::getUserIdByName($term))
                    ->orWhere('orders.client_name', 'like', '%'.$term.'%')
                    ->orWhere('customers.city', 'like', '%'.$term.'%')
                    ->orWhere('customers.name', 'like', '%'.$term.'%')
                    ->orWhere('customers.id', 'like', '%'.$term.'%')
                    ->orWhere('customers.phone', 'like', '%'.$term.'%');
            })
            ->select('orders.*', 'customers.name', 'customers.phone')
            ->get();

        return $orders;
    }

    //TODO::Get customerList
    public function getCustomers(Request $request)
    {
        if ($request->ajax()) {
            $term = $request->q;
            try {
                $customer = Customer::where('name', 'like', '%'.$term.'%')->take(100)->get();

                return $customer;
            } catch (Exception $ex) {
                //later put exception block message here
            }
        }
    }

    //TODO::Get companyList
    public function getCompany(Request $request)
    {
        if ($request->ajax()) {
            try {
                $term = $request->q;
                $storeWebsites = StoreWebsite::where('website_address', 'like', '%'.$term.'%')->take(100)->get();

                return $storeWebsites;
            } catch (Exception $ex) {
                //later put exception block message here
            }
        }
    }

    public function getSearchedProducts(Request $request)
    {
        $term = $request->q;
        try {
            $product = Product::where('name', 'like', '%'.$term.'%')->orWhere('short_description', 'like', '%'.$term.'%')->take(100)->get();

            return $product;
        } catch (Exception $ex) {
            //later put exception block message here
        }
    }

    public function updateDelDate(request $request): JsonResponse
    {
        $orderid = $request->input('orderid');
        $newdeldate = $request->input('newdeldate');
        $fieldname = $request->input('fieldname');
        $oldOrderDelData = Order::where('id', $orderid);
        $oldOrderDelDate = $oldOrderDelData->pluck('estimated_delivery_date');
        $oldOrderDelDate = (isset($oldOrderDelDate[0]) && $oldOrderDelDate[0] != '') ? $oldOrderDelDate[0] : '';
        $userId = Auth::id();
        $estimated_delivery_histories = new EstimatedDeliveryHistory;
        $estimated_delivery_histories->order_id = $orderid;
        $estimated_delivery_histories->field = $fieldname;
        $estimated_delivery_histories->updated_by = $userId;
        $estimated_delivery_histories->old_value = $oldOrderDelDate;
        $estimated_delivery_histories->new_value = $newdeldate;
        $order_via = $request->order_via;

        if ($estimated_delivery_histories->save()) {
            $oldOrderDelData->update(['estimated_delivery_date' => $newdeldate]);
            $order = Order::where('id', $orderid)->first();

            if (in_array('email', $order_via)) {
                $emailClass = (new OrderDeliveryDateChangeMail($order))->build();
                $storeWebsiteOrder = $order->storeWebsiteOrder;
                $email = Email::create([
                    'model_id' => $order->id,
                    'model_type' => Order::class,
                    'from' => $emailClass->fromMailer,
                    'to' => $order->customer->email,
                    'subject' => $emailClass->subject,
                    'message' => $emailClass->render(),
                    'template' => 'order-status-update',
                    'additional_data' => $order->id,
                    'status' => 'pre-send',
                    'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                    'is_draft' => 0,
                ]);
                SendEmail::dispatch($email)->onQueue('send_email');
            }
            $message = 'Order delivery date has been changed to '.$newdeldate;
            if (in_array('sms', $order_via)) {
                if (isset($order->storeWebsiteOrder)) {
                    $receiverNumber = $order->contact_detail;
                    if ($storeWebsiteOrder->store_website_id) {
                        TwilioSmsJob::dispatch($receiverNumber, $message, $storeWebsiteOrder->store_website_id);
                    }
                }
            }

            UpdateOrderStatusMessageTpl::dispatch($order->id, $message)->onQueue('customer_message');

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Delivery Date Updated Successfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong']);
    }

    public function viewEstDelDateHistory(request $request): JsonResponse
    {
        $orderid = $request->input('order_id');
        $estimated_delivery_histories = EstimatedDeliveryHistory::select('estimated_delivery_histories.*', 'users.name')
            ->where('order_id', $orderid)
            ->where('estimated_delivery_histories.field', 'estimated_delivery_date')
            ->leftJoin('users', 'users.id', 'estimated_delivery_histories.updated_by')
            ->orderByDesc('estimated_delivery_histories.created_at')
            ->get();
        $html = view('partials.modals.estimated-delivery-date-histories')->with('estimated_delivery_histories', $estimated_delivery_histories)->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Something went wrong']);
    }

    /**
     * @SWG\Get(
     *   path="/customer/order-details",
     *   tags={"Customer"},
     *   summary="Get customer order details",
     *   operationId="get-customer-order-details",
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
    public function customerOrderDetails(Request $request)
    {
        $email = $request->email;
        $order_no = $request->order_no;
        $store_url = $request->website;

        $token = $request->bearerToken();
        if ((! $email || trim($email) == '') && empty($order_no)) {
            $message = $this->generate_erp_response('customer.order.failed', 0, 'Email is absent in your request', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 400]);
        }

        if ((! $order_no || trim($order_no) == '') && empty($email)) {
            $message = $this->generate_erp_response('customer.order.failed.reference_no_absent', 0, 'Order reference no is absent in your request', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 400]);
        }

        if (! $store_url || trim($store_url) == '') {
            $message = $this->generate_erp_response('customer.order.failed.store_url_absent', 0, 'Store Url is absent in your request', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 400]);
        }
        $store_website = StoreWebsite::where('website', 'like', $store_url)->first();
        if (! $store_website) {
            $message = $this->generate_erp_response('customer.order.failed.store_not_found', 0, 'Store not found with this url', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 404]);
        }
        if ($store_website->api_token != $token) {
            $message = $this->generate_erp_response('customer.order.failed.token_missing', $store_website->id, 'Token mismatched', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 401]);
        }

        if (! empty($email)) {
            $customer = Customer::where('email', $email)->where('store_website_id', $store_website->id)->first();
            if (! $customer) {
                return response()->json(['message' => 'Customer not found in this store for the requested email', 'status' => 404]);
            }
            $orders = Order::join('store_website_orders', 'orders.id', 'store_website_orders.order_id')
                ->where('orders.customer_id', $customer->id)
                ->where('store_website_orders.website_id', $store_website->id)
                ->select('orders.*')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $orders = Order::join('store_website_orders', 'orders.id', 'store_website_orders.order_id')
                ->where('store_website_orders.website_id', $store_website->id)
                ->where('store_website_orders.platform_order_id', $order_no)
                ->select('orders.*')
                ->orderByDesc('created_at')
                ->get();
        }

        if (count($orders) == 0) {
            $message = $this->generate_erp_response('customer.order.failed.no_order_found', $store_website->id, 'No orders found against this customer', request('lang_code'));

            return response()->json(['message' => $message, 'status' => 200]);
        }
        foreach ($orders as $order) {
            $histories = OrderStatusHistory::join('order_statuses', 'order_statuses.id', 'order_status_histories.new_status')
                ->where('order_status_histories.order_id', $order->id)
                ->select(['order_statuses.*', 'order_status_histories.created_at as created_at_time'])
                ->orderBy('order_status_histories.created_at')
                ->get();
            $return_histories = [];
            if (count($histories) > 0) {
                foreach ($histories->toArray() as $h) {
                    $return_histories[] = [
                        'status' => $h['status'],
                        'created_at' => $h['created_at_time'],
                    ];
                }
            }
            $waybill_history = waybillTrackHistories::join('waybills', 'waybills.id', 'waybill_track_histories.waybill_id')
                ->where('waybills.order_id', $order->id)
                ->select(['waybill_track_histories.*', 'waybill_track_histories.created_at  as created_at_time'])
                ->orderBy('waybill_track_histories.created_at')
                ->get();

            if (count($waybill_history) > 0) {
                foreach ($waybill_history->toArray() as $h) {
                    $return_histories[] = [
                        'status' => $h['comment'],
                        'created_at' => $h['created_at_time'],
                    ];
                }
            }

            if (! empty($return_histories)) {
                usort($return_histories, function ($a, $b) {
                    return strtotime($a['created_at']) - strtotime($b['created_at']);
                });
            }

            $order->status_histories = array_reverse($return_histories);
        }
        $orders = $orders->toArray();
        $message = $this->generate_erp_response('customer.order.success', $store_website->id, 'Orders Fetched successfully', request('lang_code'));

        return response()->json(['message' => $message, 'status' => 200, 'data' => $orders]);
    }

    public function addNewReply(request $request): JsonResponse
    {
        if ($request->reply) {
            $replyData = [];
            $html = '';
            $replyData['reply'] = $request->reply;
            $replyData['model'] = 'Order';
            $replyData['category_id'] = 1;
            $success = Reply::create($replyData);
            if ($success) {
                $replies = Reply::where('model', 'Order')->get();
                if ($replies) {
                    $html .= "<option value=''>Select Order Status</option>";
                    foreach ($replies as $reply) {
                        $html .= '<option value="'.$reply->id.'">'.$reply->reply.'</option>';
                    }
                }

                return response()->json(['message' => 'reply added successfully', 'html' => $html, 'status' => 200]);
            }

            return response()->json(['message' => 'unable to add reply', 'status' => 500]);
        }

        return response()->json(['message' => 'please enter a reply', 'status' => 400]);
    }

    public function testEmail(Request $request): JsonResponse
    {
        Mail::raw('Hi, welcome user!', function ($message) {
            $message->to('webreak.pravin@gmail.com')->subject('Welcome Message');
        });

        exit;

        $order = Order::find(2032);

        $emailClass = (new OrderConfirmation($order))->build();

        $email = Email::create([
            'model_id' => $order->id,
            'model_type' => Order::class,
            'from' => $emailClass->fromMailer,
            'to' => $order->customer->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'order-confirmation',
            'additional_data' => $order->id,
            'status' => 'pre-send',
            'is_draft' => 1,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return response()->json(['message' => 'unable to add reply', 'status' => 500]);
    }

    public function statusChangeTemplate(Request $request): JsonResponse
    {
        $statusModal = OrderStatus::where('id', $request->order_status_id)->first();
        $order = Order::where('id', $request->order_id)->first();
        $template = Order::ORDER_STATUS_TEMPLATE;
        if ($statusModal) {
            if (! empty($statusModal->message_text_tpl)) {
                $template = $statusModal->message_text_tpl;
            }
        }

        $template = str_replace(['#{order_id}', '#{order_status}'], [$order->order_id, $statusModal->status], $template);
        $from = '';
        $preview = '';
        if (strtolower($statusModal->status) == 'cancel') {
            $emailClass = (new OrderCancellationMail($order))->build();
            $storeWebsiteOrder = $order->storeWebsiteOrder;
            if ($emailClass != null) {
                $preview = $emailClass->render();
            }
            if ($storeWebsiteOrder) {
                $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
                if ($emailAddress) {
                    $from = $emailAddress->from_address;
                    $fromTemplate = "<input type='email' required id='email_from_mail' class='form-control' name='from_mail' value='".$from."' >";
                } else {
                    $emailAddresses = EmailAddress::pluck('from_address', 'id')->toArray();
                    $fromTemplate = "<select class='form-control' id='email_from_mail' name='from_mail'>";
                    foreach ($emailAddresses as $emailAddress) {
                        $fromTemplate .= '<option>'.$emailAddress.'</option>';
                    }
                    $fromTemplate .= '</select>';
                }
            }
            $preview = "<table>
                    <tr>
                       <td>To</td><td>
                       <input type='email' required id='email_to_mail' class='form-control' name='to_mail' value='".$order->customer->email."' >
                       </td></tr><tr>
                       <td>From </td> <td>
                       $fromTemplate
                       </td></tr><tr>
                       <td>Preview </td> <td><textarea name='editableFile' rows='10' id='customEmailContent' >".$preview.'</textarea></td>
                    </tr>
            </table>';
            $this->createEmailSendJourneyLog($order->id, 'Status Change to '.$statusModal->status, Order::class, 'outgoing', '0', $from, '', 'Order # '.$order->id.' Status has been changed', $preview, $template, '', $storeWebsiteOrder->website_id);
        } else {
            $emailClass = (new OrderStatusChangeMail($order))->build();
            if ($emailClass != null) {
                $preview = $emailClass->render();
            }
            $storeWebsiteOrder = $order->storeWebsiteOrder;
            if ($storeWebsiteOrder) {
                $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
                if ($emailAddress) {
                    $from = $emailAddress->from_address;
                    $fromTemplate = "<input type='email' required id='email_from_mail' class='form-control' name='from_mail' value='".$from."' >";
                } else {
                    $emailAddresses = EmailAddress::pluck('from_address', 'id')->toArray();
                    $fromTemplate = "<select class='form-control' id='email_from_mail' name='from_mail'>";
                    foreach ($emailAddresses as $emailAddress) {
                        $fromTemplate .= '<option>'.$emailAddress.'</option>';
                    }
                    $fromTemplate .= '</select>';
                }
            }
            $preview = "<table>
                    <tr>
                       <td>To</td><td>
                       <input type='email' required id='email_to_mail' class='form-control' name='to_mail' value='".$order->customer->email."' >
                       </td></tr><tr>
                       <td>From </td> <td>
                       $fromTemplate
                       </td></tr><tr>
                       <td>Preview </td> <td><textarea name='editableFile' rows='10' id='customEmailContent' >".$preview.'</textarea></td>
                    </tr>
            </table>';
            $this->createEmailSendJourneyLog($order->id, 'Status Change to '.$statusModal->status, Order::class, 'outgoing', '0', $from, '', 'Order # '.$order->id.' Status has been changed', $preview, $template, '', $storeWebsiteOrder);
        }

        return response()->json(['code' => 200, 'template' => $template, 'preview' => $preview]);
    }

    public function prodctStatusChangeTemplate(Request $request): JsonResponse
    {
        $statusModal = OrderStatus::where('id', $request->order_status_id)->first();
        $order = Order::where('id', $request->order_id)->first();
        $template = Order::ORDER_STATUS_TEMPLATE;
        if ($statusModal) {
            if (! empty($statusModal->message_text_tpl)) {
                $template = $statusModal->message_text_tpl;
            }
        }

        $template = str_replace(['#{order_id}', '#{order_status}'], [$order->order_id, $statusModal->status], $template);
        $from = config('env.MAIL_FROM_ADDRESS');
        $preview = '';
        if (strtolower($statusModal->status) == 'cancel') {
            $emailClass = (new OrderCancellationMail($order))->build();
            $storeWebsiteOrder = $order->storeWebsiteOrder;
            if ($emailClass != null) {
                $preview = $emailClass->render();
            }
            if ($storeWebsiteOrder) {
                $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
                if ($emailAddress) {
                    $from = $emailAddress->from_address;
                }
            }
            $preview = "<table>
                    <tr>
                       <td>To</td><td>
                       <input type='email' required id='email_to_mail' class='form-control' name='to_mail' value='".$order->customer->email."' >
                       </td></tr><tr>
                       <td>From </td> <td>
                       <input type='email' required id='email_from_mail' class='form-control' name='from_mail' value='".$from."' >
                       </td></tr><tr>
                       <td>Preview </td> <td><textarea name='editableFileproduct' rows='10' id='editableFileproduct1' >".$preview.'</textarea></td>
                    </tr>
            </table>';
            $this->createEmailSendJourneyLog($order->id, 'Status Change to '.$statusModal->status, Order::class, 'outgoing', '0', $from, '', 'Order # '.$order->id.' Status has been changed', $preview, $template, '', $storeWebsiteOrder->website_id);
        } else {
            $emailClass = (new OrderStatusChangeMail($order))->build();
            if ($emailClass != null) {
                $preview = $emailClass->render();
            }
            $storeWebsiteOrder = $order->storeWebsiteOrder;
            if ($storeWebsiteOrder) {
                $emailAddress = EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
                if ($emailAddress) {
                    $from = $emailAddress->from_address;
                }
            }
            $preview = "<table>
                    <tr>
                       <td>To</td><td>
                       <input type='email' required id='email_to_mail' class='form-control' name='to_mail' value='".$order->customer->email."' >
                       </td></tr><tr>
                       <td>From </td> <td>
                       <input type='email' required id='email_from_mail' class='form-control' name='from_mail' value='".$from."' >
                       </td></tr><tr>
                       <td>Preview </td> <td><textarea name='editableFileproduct' rows='10' id='editableFileproduct1' >".$preview.'</textarea></td>
                    </tr>
            </table>';
            $this->createEmailSendJourneyLog($order->id, 'Status Change to '.$statusModal->status, Order::class, 'outgoing', '0', $from, '', 'Order # '.$order->id.' Status has been changed', $preview, $template, '', $storeWebsiteOrder);
        }

        return response()->json(['code' => 200, 'template' => $template, 'preview' => $preview]);
    }

    public function productItemStatusChange(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $order_product_item_id = $request->order_product_item_id;
        $status = $request->get('status');
        $order_status_id = $request->get('order_status_id');
        $order_via = $request->order_via;
        if (! empty($id) && ! empty($status)) {
            $order = Order::where('id', $id)->first();
            $order_product = OrderProduct::where('id', $order_product_item_id)->first();
            $statuss = OrderStatus::where('id', $status)->first();
            $order_statuss = OrderStatus::where('id', $order_status_id)->first();

            $order_statuss_name = 'Status not assigned';
            if (! empty($order_statuss)) {
                $order_statuss_name = $order_statuss->status;
            }
            if ($order) {
                $order_product->delivery_status = $request->status;
                if ($request->status == '10') {
                    $order_product->delivery_date = date('Y-m-d H:s:i');
                } else {
                    $order_product->delivery_date = '';
                }
                $order_product->save();
                if (in_array('email', $order_via)) {
                    if (isset($request->sendmessage) && $request->sendmessage == '1') {
                        //Sending Mail on changing of order status
                        try {
                            $from_mail_address = $request->from_mail;
                            $to_mail_address = $request->to_mail;
                            // send order canellation email
                            if (strtolower($statuss->status) == 'cancel') {
                                $emailClass = (new OrderCancellationMail($order))->build();

                                if ($from_mail_address != '') {
                                    $emailClass->fromMailer = $from_mail_address;
                                }
                                if ($to_mail_address != '') {
                                    $order->customer->email = $to_mail_address;
                                }

                                $storeWebsiteOrder = $order->storeWebsiteOrder;
                                $email = Email::create([
                                    'model_id' => $order->id,
                                    'model_type' => Order::class,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $order->customer->email,
                                    'subject' => $emailClass->subject,
                                    'message' => $request->message,
                                    // 'message'          => $emailClass->render(),
                                    'template' => 'order-cancellation-update',
                                    'additional_data' => $order->id,
                                    'status' => 'pre-send',
                                    'store_website_id' => (isset($storeWebsiteOrder)) ? $storeWebsiteOrder->store_website_id : null,
                                    'is_draft' => 0,
                                ]);

                                SendEmail::dispatch($email)->onQueue('send_email');
                                $this->createEmailSendJourneyLog($id, 'Email type via Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                            } else {
                                $emailClass = (new OrderStatusChangeMail($order))->build();
                                if ($from_mail_address != '') {
                                    $emailClass->fromMailer = $from_mail_address;
                                }
                                if ($to_mail_address != '') {
                                    $order->customer->email = $to_mail_address;
                                }

                                $storeWebsiteOrder = $order->storeWebsiteOrder;
                                $email = Email::create([
                                    'model_id' => $order->id,
                                    'model_type' => Order::class,
                                    'from' => $emailClass->fromMailer,
                                    'to' => $order->customer->email,
                                    'subject' => $emailClass->subject,
                                    'message' => $request->custom_email_content,
                                    // 'message'          => $emailClass->render(),
                                    'template' => 'order-status-update',
                                    'additional_data' => $order->id,
                                    'status' => 'pre-send',
                                    'is_draft' => 0,
                                ]);

                                SendEmail::dispatch($email)->onQueue('send_email');
                                $this->createEmailSendJourneyLog($id, 'Email type via Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                            }
                        } catch (Exception $e) {
                            $this->createEmailCommonExceptionLog($order->id, $e->getMessage(), 'email');
                            $this->createEmailSendJourneyLog($id, 'Email type via Error', Order::class, 'outgoing', '0', $from_mail_address, $to_mail_address, $emailClass->subject, $request->message, '', $e->getMessage(), $order->storeWebsiteOrder);
                            Log::info('Sending mail issue at the ordercontroller #2215 ->'.$e->getMessage());
                        }
                    } else {
                        $emailClass = (new OrderStatusChangeMail($order))->build();

                        $storeWebsiteOrder = $order->storeWebsiteOrder;
                        $email = Email::create([
                            'model_id' => $order->id,
                            'model_type' => Order::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $order->customer->email,
                            'subject' => $emailClass->subject,
                            'template' => 'order-status-update',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'store_website_id' => ($storeWebsiteOrder) ? $storeWebsiteOrder->store_website_id : null,
                            'is_draft' => 0,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                        $this->createEmailSendJourneyLog($id, 'Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, $request->message, '', '', $storeWebsiteOrder->website_id);
                    }
                }

                if (in_array('sms', $order_via)) {
                    if (isset($request->sendmessage) && $request->sendmessage == '1') {
                        if (isset($order->storeWebsiteOrder)) {
                            $website = Website::where('id', $order->storeWebsiteOrder->website_id)->first();

                            $receiverNumber = $order->contact_detail;
                            TwilioSmsJob::dispatch($receiverNumber, $request->message, $website->store_website_id, $order->id);
                            $this->createEmailSendJourneyLog($id, 'Email type IVA SMS Order update status with '.$statuss->status, Order::class, 'outgoing', '0', $emailClass->fromMailer, $order->customer->email, $emailClass->subject, 'Phone : '.$receiverNumber.' <br/> '.$request->message, '', '', $website->website_id);
                        }
                    }
                }
            }

            //Sending Mail on changing of order status
            if (isset($request->sendmessage) && $request->sendmessage == '1') {
                //sending order message to the customer
                UpdateOrderStatusMessageTpl::dispatch($order->id, request('message', null))->onQueue('customer_message');
            }
            $storeWebsiteOrder = StoreWebsiteOrder::where('order_id', $order->id)->first();
            if ($storeWebsiteOrder) {
                $website = StoreWebsite::find($storeWebsiteOrder->website_id);
                if ($website) {
                    $store_order_status = Store_order_status::where('order_status_id', $status)->where('store_website_id', $storeWebsiteOrder->website_id)->first();
                    if ($store_order_status) {
                        $magento_status = StoreMasterStatus::find($store_order_status->store_master_status_id);
                        if ($magento_status) {
                            $magentoHelper = new MagentoHelperv2;
                            $result = $magentoHelper->changeOrderStatus($order, $website, $magento_status->value, $order_product, $order_statuss_name);
                            $this->createEmailSendJourneyLog($id, 'Magento Order Product Item update status with '.$statuss->status, Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento replay', $request->message, '', '', $storeWebsiteOrder->website_id);
                            /**
                             *check if response has error
                             */
                            $response = $result->getData();
                            if (isset($response) && isset($response->status) && $response->status == false) {
                                $this->createOrderMagentoErrorLog($order->id, $response->error);
                                $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error', $response->error, '', '', $storeWebsiteOrder->website_id);

                                return response()->json($response->error, 400);
                            }
                        } else {
                            $this->createOrderMagentoErrorLog($order->id, 'Store MasterStatus Not Present');
                            $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store MasterStatus Not Present', '', '', '', $storeWebsiteOrder->website_id);

                            return response()->json('Store MasterStatus Not Present', 400);
                        }
                    } else {
                        $this->createOrderMagentoErrorLog($order->id, 'Store Order Status Not Present');
                        $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Order Status Not Present', '', '', '', $storeWebsiteOrder->website_id);

                        return response()->json('Store Order Status Not Present', 400);
                    }
                } else {
                    $this->createOrderMagentoErrorLog($order->id, 'Website Order Not Present');
                    $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Website Order Not Present', '', '', '', '');

                    return response()->json('Website Order Not Present', 400);
                }
                $storeWebsiteOrder->update(['order_id', $status]);
            } else {
                $this->createOrderMagentoErrorLog($order->id, 'Store Website Order Not Present');
                $this->createEmailSendJourneyLog($id, 'Magento Error', Order::class, 'outgoing', '0', $request->from_mail, $request->to_mail, 'Magento Error Store Website Order Not Present', '', '', '', '');

                return response()->json('Store Website Order Not Present', 400);
            }
        }

        return response()->json('Success', 200);
    }

    public function orderProductStatusChange(Request $request): JsonResponse
    {
        try {
            // Get order product
            $orderProduct = OrderProduct::FindOrFail($request->orderProductId);

            if ($orderProduct) {
                // Get status from request
                $orderProductStatusId = $request->orderProductStatusId;

                // Update the order product status in order products table.
                $orderProduct->order_product_status_id = $orderProductStatusId;
                $orderProduct->save();

                // Find mapped purchase status
                $mappedStatus = StatusMapping::where('order_status_id', $orderProductStatusId)->first();
                if ($mappedStatus) {
                    $purchaseStatusId = $mappedStatus->purchase_status_id;
                    if ($purchaseStatusId) {
                        $purchaseProductOrders = PurchaseProductOrder::whereRaw('json_contains(order_products_order_id, \'["'.$request->orderProductId.'"]\')')->pluck('id')->toArray();
                        if ($purchaseProductOrders) {
                            PurchaseProductOrder::whereIn('id', $purchaseProductOrders)->update(['purchase_status_id' => $purchaseStatusId]);
                        }
                    }
                }

                return response()->json(['messages' => 'Order Product Status Updated Successfully', 'code' => 200]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Order product not found!'], 404);
        }
    }

    public function getInvoiceDetails(Request $request, $invoiceId): View
    {
        $invoice = Invoice::find($invoiceId);

        return view('orders.invoices.partials.edit-invoice-modal', compact('invoice'));
    }

    public function updateDetails(Request $request, $invoiceId): JsonResponse
    {
        $items = $request->order;

        if (! empty($items)) {
            foreach ($items as $k => $item) {
                $order = Order::find($k);
                $address = OrderCustomerAddress::where('order_id', $k)->where('address_type', 'shipping')->first();
                if (! $address) {
                    $address = new OrderCustomerAddress;
                    $address->order_id = $k;
                    $address->address_type = 'shipping';
                    if ($order) {
                        $customer = $order->customer;
                        if ($customer) {
                            $address->customer_id = $customer->id;
                            $address->email = $customer->email;
                            @[$firstname, $lastname] = explode(' ', $customer->name);
                            $address->firstname = isset($firstname) ? $firstname : '';
                            $address->lastname = isset($lastname) ? $lastname : '';
                            $address->telephone = $customer->phone;
                        }
                    }
                }
                $address->city = $item['city'];
                $address->country_id = $item['country_id'];
                $address->street = $item['street'];
                $address->postcode = $item['postcode'];
                $address->save();
            }
        }

        $orderproducts = $request->order_product;

        if (! empty($orderproducts)) {
            foreach ($orderproducts as $k => $op) {
                $orderP = OrderProduct::find($k);
                if ($orderP) {
                    $orderP->fill($op);
                    $orderP->save();
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Invoice updated successfully']);
    }

    public function addStatus(Request $request): JsonResponse
    {
        $label = str_replace('/[^A-Za-z0-9-]+/', '-', $request->name);

        $newStatus = CallBusyMessageStatus::create([
            'label' => $label,
            'name' => $request->name,
        ]);

        return response()->json(['data' => $newStatus, 'message' => $newStatus->name.' status added successfully.']);
    }

    public function storeStatus(Request $request, $id): JsonResponse
    {
        $callBusyMessage = CallBusyMessage::find($id);
        $callBusyMessage->call_busy_message_statuses_id = $request->select_id;
        $callBusyMessage->save();

        return response()->json(['message' => ' Status updated successfuly.']);
    }

    public function sendWhatappMessageOrEmail(Request $request): JsonResponse
    {
        $newValue = [];
        parse_str($request->formData, $newValue);

        $defaultWhatapp = WhatsappConfig::select('*')
            ->whereRaw('find_in_set('.CustomerController::DEFAULT_FOR.',default_for)')
            ->first();
        $defaultNo = $defaultWhatapp->number;

        $newArr = $request->except(['_token', 'formData']);
        $addRequestData = array_merge($newValue, $newArr);

        if (empty($addRequestData['message'])) {
            return response()->json(['error' => 'Please type message']);
        }

        if (empty($addRequestData['whatsapp']) && empty($addRequestData['email'])) {
            return response()->json(['error' => 'Please select atleast one checkbox']);
        }

        $customer = null;
        $shouldSaveInChatMessage = false;

        if ($addRequestData['customerId'] && ! empty($addRequestData['whatsapp'])) {
            $customer = Customer::find($addRequestData['customerId']);

            if (! empty($customer) && ! empty($customer->phone) && ! empty($customer->whatsapp_number)) {
                app(WhatsAppController::class)->sendWithWhatsApp($customer->phone, $customer->whatsapp_number, $addRequestData['message']);
                $shouldSaveInChatMessage = true;
            }
        } elseif (! $addRequestData['customerId'] && ! empty($addRequestData['whatsapp'])) {
            $sendTo = str_replace('+', '', $addRequestData['fullNumber']);
            $sendFrom = $defaultNo;
            if (! empty($addRequestData['whatsapp']) && ! empty($sendTo) && ! empty($sendFrom)) {
                app(WhatsAppController::class)->sendWithWhatsApp($sendTo, $sendFrom, $addRequestData['message']);
                $shouldSaveInChatMessage = true;
            }
        }

        if ($addRequestData['customerId'] && ! empty($addRequestData['email'])) {
            $customer = Customer::find($addRequestData['customerId']);

            $subject = 'Ordered miss-called';

            if (! empty($customer) && ! empty($customer->email) && ! empty($addRequestData['message'])) {
                // dump('send customer email final');
                $from = config('env.MAIL_FROM_ADDRESS');
                // Check from address exist for customer's store website
                $emailAddress = EmailAddress::where('store_website_id', $customer->store_website_id)->first();
                if ($emailAddress) {
                    $from = $emailAddress->from_address;
                }

                $email = Email::create([
                    'model_id' => $customer->id,
                    'model_type' => Customer::class,
                    'from' => $from,
                    'to' => $customer->email,
                    'subject' => $subject,
                    'message' => $addRequestData['message'],
                    'template' => 'customer-simple',
                    'additional_data' => '',
                    'status' => 'pre-send',
                    'is_draft' => 0,
                ]);

                SendEmail::dispatch($email)->onQueue('send_email');

                $shouldSaveInChatMessage = true;
            }
        }

        if ($shouldSaveInChatMessage) {
            $params = [
                'customer_id' => $customer->id,
                'number' => $customer->phone,
                'message' => $addRequestData['message'],
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
            ];

            ChatMessage::create($params);

            return response()->json(['message' => 'Message send successfully']);
        }
    }

    /**
     * This function is use for create Order log
     *
     * @param type [array] inputArray
     * @param  Request  $request  Request
     * @param  mixed  $logType
     * @param  mixed  $log
     * @return void;
     */
    public function createOrderLog(Request $request, $logType = '', $log = '')
    {
        try {
            OrderErrorLog::create([
                'order_id' => $request->order_id ?? '',
                'event_type' => $logType,
                'log' => $log,
            ]);
        } catch (Exception $e) {
            OrderErrorLog::create(['order_id' => $request->order_id ?? '', 'log' => $e->getMessage(), 'event_type' => $logType]);
        }
    }

    /**
     * This function is use for Payment History
     *
     * @param  Request  $request  Request
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        $order_id = $request->input('order_id');
        $html = '';
        $paymentData = CashFlow::where('cash_flow_able_id', $order_id)
            ->where('cash_flow_able_type', Order::class)
            ->where('type', 'paid')
            ->orderByDesc('date')
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$history->id.'</td>';
                $html .= '<td>'.$history->amount.'</td>';
                $html .= '<td>'.$history->date.'</td>';
                $html .= '<td>'.$history->description.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">No Record found</td>';
            $html .= '</tr>';
            $this->createOrderLog($request, 'Payment History', 'No Record found');
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    /**
     * This function is use for cancel Transaction
     *
     * @param  Request  $request  Request
     */
    public function cancelTransaction(Request $request): JsonResponse
    {
        $order_id = $request->get('order_id');
        $order = Order::where('id', $order_id)->first();
        $storeWebsiteOrder = StoreWebsiteOrder::where('order_id', $order_id)->first();
        if ($storeWebsiteOrder) {
            $website = StoreWebsite::find($storeWebsiteOrder->website_id);

            if ($website) {
                $magentoHelper = new MagentoHelperv2;
                $result = $magentoHelper->cancelTransaction($order, $website);
                $this->createOrderLog($request, 'Cancel Transaction from Magento request', $result);

                return response()->json(['message' => $result, 'success' => true], 200);
            }
        } else {
            $this->createOrderLog($request, 'Cancel Transaction', 'Store Website Orders not found');

            return response()->json(['message' => 'Store Website Orders not found', 'order_id' => $order_id, 'success' => false], 500);
        }
    }

    /**
     * This function is use for List Order log
     *
     * @param  Request  $request  Request
     */
    public function getOrderErrorLog(Request $request): JsonResponse
    {
        try {
            $orderError = OrderErrorLog::where('order_id', $request->order_id)->get();

            if (count($orderError) > 0) {
                return response()->json(['code' => 200, 'data' => $orderError]);
            } else {
                return response()->json(['code' => 500, 'message' => 'Could not find any data']);
            }
        } catch (Exception $e) {
            $orderError = OrderErrorLog::where('order_id', $request->order_id)->get();

            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function syncTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $order_id = $request->get('order_id');
        $transaction_id = $request->get('transaction_id');
        $order = Order::where('order_id', $order_id)->first();
        $message = 'Issue in order';
        $success = false;
        if ($order) {
            $order->transaction_id = $transaction_id;
            $order->save();
            $message = 'Transaction id updated successfully';
            $success = true;
        }
        $this->createOrderLog($request, 'Sync Transaction', $message);

        return response()->json(['message' => $message, 'success' => $success], 200);
    }

    public function returnStatus(Request $request): JsonResponse
    {
        try {
            $order_id = $request->get('id');
            $return_status = $request->get('status');
            if ($return_status) {
                $return_name = 'true';
            } else {
                $return_name = 'false';
            }
            $order = Order::where('id', $order_id)->first();
            $message = 'Return Order status updated with '.$return_name;
            $success = false;
            if ($order) {
                $order->order_return_request = $return_status;
                $order->save();
                $message = 'Return Order updated successfully with '.$return_name;
                $success = true;
            }

            return response()->json(['message' => $message, 'success' => $success, 'code' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function store_order_status_history_create($request, $response, $store_website_id)
    {
        if (isset($response) && $response != '') {
            $response = json_encode($response);
        }
        $storeHistory = [
            'request' => json_encode($request->all()),
            'response' => $response,
            'store_website_id' => $store_website_id,
            'updated_by' => Auth::user()->id,
            'action_type' => 'Fetch Store Status',
        ];
        StoreOrderStatusesHistory::create($storeHistory);
    }

    public function store_order_status_history_update($request, $response, $id)
    {
        $store_order_status = Store_order_status::find($id);
        if (isset($response) && $response != '') {
            $response = json_encode($response);
        }
        $storeHistory = [
            'request' => json_encode($request->all()),
            'response' => $response,
            'store_order_statuses_id' => $id,
            'old_order_status_id' => $store_order_status->order_status_id,
            'old_store_website_id' => $store_order_status->store_website_id,
            'old_status' => $store_order_status->status,
            'old_store_master_status_id' => $store_order_status->store_master_status_id,

            'new_order_status_id' => $request->order_status_id,
            'new_store_website_id' => $request->store_website_id,
            'new_status' => $request->status,
            'new_store_master_status_id' => $request->store_master_status_id,

            'updated_by' => Auth::user()->id,
            'action_type' => 'Edit',
        ];

        StoreOrderStatusesHistory::create($storeHistory);
    }

    public function statusHistory()
    {
        $id = $_REQUEST['id'];

        $statusHistorySite = StoreOrderStatusesHistory::where('store_order_statuses_id', $id)->get();

        $store_website_id = 0;
        if (isset($statusHistorySite[0]->new_store_website_id) && $statusHistorySite[0]->new_store_website_id > 0) {
            $store_website_id = $statusHistorySite[0]->new_store_website_id;
        }

        $statusHistory = StoreOrderStatusesHistory::where('store_order_statuses_id', $id)->orWhere('store_website_id', $store_website_id)->get();

        $statusHistory = $statusHistory->map(function ($status) {
            $status->request_detail = $status->request;
            $status->response_detail = $status->response;
            $status->old_order_status_id = OrderStatus::where('id', $status->old_order_status_id)->value('status');
            $status->old_store_website_id = StoreWebsite::where('id', $status->old_store_website_id)->value('website');
            $status->old_store_master_status_id = StoreMasterStatus::where('id', $status->old_store_master_status_id)->value('label');

            $status->new_order_status_id = OrderStatus::where('id', $status->new_order_status_id)->value('status');
            $status->new_store_website_id = StoreWebsite::where('id', $status->new_store_website_id)->value('website');
            $status->new_store_master_status_id = StoreMasterStatus::where('id', $status->new_store_master_status_id)->value('label');

            return $status;
        });

        return response()->json(['code' => 200, 'data' => $statusHistory]);
    }

    public function customerList(Request $request): JsonResponse
    {
        $customer = Customer::where('name', '!=', '')->orderBy('name');
        if (! empty($request->q)) {
            $customer->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }
        $customer = $customer->paginate(30);
        $result['total_count'] = $customer->total();
        $result['incomplete_results'] = $customer->nextPageUrl() !== null;

        foreach ($customer as $customer) {
            $result['items'][] = [
                'id' => $customer->id,
                'text' => $customer->name,
            ];
        }

        return response()->json($result);
    }

    public function callhistoryStatusList(Request $request): JsonResponse
    {
        $callhistory = CallHistory::groupBy('status');
        if (! empty($request->q)) {
            $callhistory->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }
        $callhistory = $callhistory->paginate(30);
        $result['total_count'] = $callhistory->total();
        $result['incomplete_results'] = $callhistory->nextPageUrl() !== null;

        foreach ($callhistory as $callhistory) {
            $result['items'][] = [
                'id' => $callhistory->status,
                'text' => $callhistory->status,
            ];
        }

        return response()->json($result);
    }

    public function storeWebsiteList(Request $request): JsonResponse
    {
        $storewebsite = StoreWebsite::orderBy('website');
        if (! empty($request->q)) {
            $storewebsite->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }
        $storewebsite = $storewebsite->paginate(30);
        $result['total_count'] = $storewebsite->total();
        $result['incomplete_results'] = $storewebsite->nextPageUrl() !== null;

        foreach ($storewebsite as $storewebsite) {
            $result['items'][] = [
                'id' => $storewebsite->id,
                'text' => $storewebsite->website,
            ];
        }

        return response()->json($result);
    }

    public function getInvoiceCustomerEmailSelected(Request $request)
    {
        $ids = explode(',', $request->ids);
        $emails = [];
        $invoices = Invoice::whereIn('id', $ids)->get();
        foreach ($invoices as $invoice) {
            $emails[] = ['email' => $invoice->orders[0]->customer->email, 'id' => $invoice->id];
        }

        return $emails;
    }

    public function orderStatusColorCode(Request $request): JsonResponse
    {
        $perPage = 10;

        $orderStatus = OrderStatus::latest()
            ->paginate($perPage);

        $html = view('orders.order-status-modal-html')->with('orderStatus', $orderStatus)->render();

        return response()->json(['code' => 200, 'data' => $orderStatus, 'html' => $html, 'message' => 'Content render']);
    }

    public function orderStatusColorCodeUpdate(Request $request): JsonResponse
    {
        $orderstatus = OrderStatus::find($request->orderId);
        $orderstatus->color = $request->colorValue;
        $orderstatus->save();

        return response()->json(['code' => 200, 'orderstatus' => $orderstatus, 'message' => 'Color Code has been Updated Succeesfully!']);
    }

    public function ordersColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'orders-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'orders-listing';
            $column->column_name = json_encode($request->column_orders);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'orders-listing';
            $column->column_name = json_encode($request->column_orders);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function orderChangeStatusHistory(Request $request): JsonResponse
    {
        $order_id = $request->order_id;
        $order_product_id = $request->product_item_id;

        $datas = OrderStatusMagentoRequestResponseLog::with('user', 'order')
            ->where('order_id', $order_id)
            ->where('order_product_id', $order_product_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
