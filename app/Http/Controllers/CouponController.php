<?php

namespace App\Http\Controllers;
use App\User;
use App\Jobs\SendEmail;
use App\EmailAddress;
use App\Email;

use App\Coupon;
use App\CouponCodeRuleLog;
use App\CouponCodeRules;
use App\Customer;
use App\Helpers\SSP;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\SaveRemarksCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\LogRequest;
use App\Mails\AddCoupon;
use App\Models\CouponCodeRulesRemarks;
use App\Models\CouponCodeRulesStatus;
use App\Models\DataTableColumn;
use App\Order;
use App\StoreWebsite;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreViewValue;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use stdClass;

class CouponController extends Controller
{
    private $DATA_COLUMN_KEY = -99;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $website_stores = WebsiteStore::with('storeView')->get();
        $store_websites = StoreWebsite::all();
        $customers = [];

        $q = CouponCodeRules::query();
        $q->with('users');
        if ($srch = request('flt_coupon')) {
            $q->where('coupon_code', $srch);
        }
        if ($srch = request('flt_status')) {
            if ($srch === 'Active') {
                $q->where('is_active', 1);
            }
            if ($srch === 'InActive') {
                $q->where('is_active', 0);
            }
        }
        if ($srch = request('flt_rule')) {
            $q->where('name', 'like', '%'.$srch.'%');
        }
        if ($srch = request('usernames')) {
            $q->where('created_by', $srch);
        }
        if ($srch = request('website_ids')) {
            $q->whereIn('website_ids', $srch);
        }
        if ($srch = request('website_id')) {
            $q->where('store_website_id', $srch);
        }
        if (request('flt_start_date') && request('flt_end_date')) {
            $startDate = request('flt_start_date');
            $endDate = request('flt_end_date');

            $q->where(function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('from_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('to_date', '<=', $endDate);
                }
            });
        }

        if (request('flt_start_date') && request('flt_end_date') === null) {
            $q->whereDate('from_date', '>=', request('flt_start_date'));
        }

        if (request('flt_end_date') && request('flt_start_date') === null) {
            $q->whereDate('to_date', '<=', request('flt_end_date'));
        }
        $rule_lists = $q->orderByDesc('id')->get();
        if ($rule_lists->count()) {
            $customers = Customer::where('email', '!=', '')->get();

            // Make Website Related Data
            $websites = [];
            $temp = Website::orderBy('name')->select(['id', 'name', 'platform_id', 'store_website_id'])->get();
            if ($temp->count()) {
                foreach ($temp as $t) {
                    $websites[$t->store_website_id][$t->platform_id][$t->id] = $t->name;
                }
            }

            foreach ($rule_lists as $rules) {
                $status_color = CouponCodeRulesStatus::where('status_alias', $rules->is_active)->first();
                if ($status_color == null) {
                    $status_color = new stdClass();
                }
                $rules->status_color = $status_color;

                // New Code to get Website Names
                $websiteNames = [];
                if ($websiteIds = $rules->website_ids ? explode(',', $rules->website_ids) : []) {
                    foreach ($websiteIds as $websiteId) {
                        if (isset($websites[$rules->store_website_id][$websiteId])) {
                            $websiteNames = array_merge_recursive($websiteNames, $websites[$rules->store_website_id][$websiteId]);
                        }
                    }
                }
                $rules->website_ids = $websiteNames ? implode(', ', $websiteNames) : '--';

                $storeWebsites = StoreWebsite::where('id', $rules->store_website_id)->first();

                $rules->website_name = $storeWebsites['website'];
            }
        }

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'checkout-coupons-code-rules')->first();

        $dynamicColumnsToShowccr = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowccr = json_decode($hideColumns, true);
        }

        $status = CouponCodeRulesStatus::all();
        $users = User::pluck('name', 'id')->toArray();

        return view('coupon.index', compact(
            'website_stores',
            'rule_lists',
            'store_websites',
            'customers',
            'dynamicColumnsToShowccr',
            'status',
            'users'
        ));
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'checkout-coupons-code-rules')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'checkout-coupons-code-rules';
            $column->column_name = json_encode($request->column_ccr);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'checkout-coupons-code-rules';
            $column->column_name = json_encode($request->column_ccr);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = CouponCodeRulesStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function saveRemarks(SaveRemarksCouponRequest $request): JsonResponse
    {
        $post = $request->all();

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        CouponCodeRulesRemarks::create($input);

        return response()->json(['code' => 200, 'data' => $input, 'message' => 'Remarks added successfully']);
    }

    public function getRemarksHistories(Request $request): JsonResponse
    {
        $datas = CouponCodeRulesRemarks::with(['user'])
            ->where('coupon_code_rules_id', $request->coupon_code_rules_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function loadData()
    {
        $tableName = with(new Coupon)->getTable();
        $primaryKey = 'id';
        $columns = [
            ['db' => 'id', 'dt' => $this->DATA_COLUMN_KEY],
            ['db' => 'start', 'dt' => 1],
            ['db' => 'code', 'dt' => 0],
            ['db' => 'expiration', 'dt' => 2],
            [
                'db' => 'currency',
                'dt' => 3,
                'formatter' => function ($d, $row) {
                    $discount = '';
                    if ($row['currency']) {
                        $discount .= $row['currency'].' ';
                    }

                    return $discount;
                },
            ],
            ['db' => 'uses', 'dt' => 3],
            ['db' => 'usage_count', 'dt' => 4],
            [
                'db' => 'id',
                'dt' => 9,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $code = $row['code'];
                    $start = date('Y-m-d H:i', strtotime($row['start']));
                    if ($row['expiration']) {
                        $expiration = date('Y-m-d H:i', strtotime($row['expiration']));
                    } else {
                        $expiration = '';
                    }
                    $currency = $row['currency'];

                    $functionCall = "(
                        '$id',
                        '$code',
                        '$start',
                        '$expiration'
                    )";
                },
            ],
        ];

        $sql_details = [
            'user' => config('database.connections.mysql.username'),
            'pass' => config('database.connections.mysql.password'),
            'db' => config('database.connections.mysql.database'),
            'host' => config('database.connections.mysql.host'),
        ];

        $tableArray = SSP::simple($_GET, $sql_details, $tableName, $primaryKey, $columns);

        $couponIds = array_map(
            function ($data) {
                return $data[$this->DATA_COLUMN_KEY];
            },
            $tableArray['data']
        );

        $couponCounts = Coupon::usageCount($couponIds);

        $dataArray = array_map(
            function ($data) use ($couponCounts) {
                foreach ($couponCounts as $couponCount) {
                    if ($couponCount->coupon_id == $data[$this->DATA_COLUMN_KEY]) {
                        $data['6'] = $couponCount->count;
                    }
                }

                return $data;
            },
            $tableArray['data']
        );

        $tableArray['data'] = $dataArray;

        return response(json_encode($tableArray));
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

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCouponRequest $request)
    {
        $httpClient = new Client;

        $data = [
            'name' => $request->get('code'),
            'description' => $request->get('description'),
            'code' => $request->get('code'),
            'start' => $request->get('start'),
            'expiration' => $request->get('expiration'),
            'fixed_discount' => $request->get('discount_fixed'),
            'percentage_discount' => $request->get('discount_percentage'),
            'minimum_order' => $request->get('minimum_order_amount'),
            'maximum_usage' => $request->get('maximum_usage'),

        ];
        $queryString = http_build_query($data);

        try {
            $url = 'https://devsite.sololuxury.com/contactcustom/index/createCoupen?'.$queryString;
            $response = $httpClient->get($url);

            Coupon::create($request->all());

            return response(
                json_encode([
                    'message' => 'Created new coupon',
                    'body' => $response->getBody(),
                    'code' => $response->getStatusCode(),
                    'url' => $url,
                ])
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Unable to create coupon',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
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
     */
    public function update(UpdateCouponRequest $request, int $id): Response
    {

        $validated = $request->all();

        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->code = $validated['code'];
            $coupon->description = $validated['description'];
            $coupon->start = $validated['start'];
            $coupon->expiration = $validated['expiration'];
            $coupon->discount_fixed = $validated['discount_fixed'];
            $coupon->discount_percentage = $validated['discount_percentage'];
            $coupon->minimum_order_amount = $validated['minimum_order_amount'];
            $coupon->maximum_usage = $validated['maximum_usage'];
            $coupon->initial_amount = $validated['initialAmount'];
            $coupon->email = $validated['email'];
            $coupon->save();

            return response(
                json_encode([
                    'message' => 'Updated coupon',
                ])
            );
        } catch (ModelNotFoundException $e) {
            return response(
                json_encode([
                    'message' => 'Did not find coupon with id: '.$id,
                ]),

            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): Response
    {
        $count = Coupon::destroy($id);
        if ($count == 1) {
            return response(
                json_encode([
                    'message' => 'Deleted the coupon',
                ])
            );
        } else {
            return response(
                json_encode([
                    'message' => 'Failed to delete coupon. It might be not present',
                ]),

            );
        }
    }

    public function showReport($couponId, Request $request): Response
    {
        $start = $request->start;
        $end = $request->end;

        if (isset($couponId) && $couponId != 0) {
            $orders = Order::where('coupon_id', $couponId)
                ->where('order_date', '>=', Carbon::parse($start))
                ->where('order_date', '<=', Carbon::parse($end))
                ->get();
        } else {
            $orders = Order::where('order_date', '>=', Carbon::parse($start))
                ->where('order_date', '<=', Carbon::parse($end))
                ->get();
        }

        $couponWithOrders = [];

        foreach ($orders as $order) {
            $couponId = $order->coupon_id;

            if (isset($couponWithOrders[$couponId])) {
                $couponWithOrders[$couponId][] = $order->toArray();
            } else {
                $couponWithOrders[$couponId] = [$order->toArray()];
            }
        }

        $response = [];
        foreach ($couponWithOrders as $couponId => $orders) {
            $response[] = [
                'coupon_id' => $couponId,
                'orders' => $orders,
            ];
        }

        return response(json_encode($response));
    }

    public function addRules(Request $request): JsonResponse
    {
        $storeWebsites = StoreWebsite::where('id', $request->store_website_id)->orWhere('parent_id', '=', $request->store_website_id)->get();

        $store_lables = [];
        if (! empty($request->store_labels)) {
            foreach ($request->store_labels as $key => $lables) {
                array_push($store_lables, ['store_id' => $key, 'store_label' => $lables, 'extension_attributes' => '{}']);
            }
        }

        $startDate = ! empty($request->start) ? $request->start : date('Y-m-d');
        $endDate = $request->expiration;
        $timeUsed = 6;

        $count = 0;
        foreach ($storeWebsites as $key => $storeWebsite) {
            $authorization = 'Authorization: Bearer '.$storeWebsite->api_token;
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);

            $parameters = [];
            $parameters['rule'] = [
                'name' => $request->name,
                'store_labels' => $store_lables,
                'description' => $request->description,
                'website_ids' => array_unique($request->website_ids),
                'customer_group_ids' => $request->customer_groups,
                'from_date' => $startDate,
                'to_date' => $endDate,
                'uses_per_customer' => $request->uses_per_coustomer,
                'is_active' => $request->active == '1' ? true : false,
                'condition' => [
                    'condition_type' => '',
                    'conditions' => [
                        [
                            'condition_type' => '',
                            'operator' => '',
                            'attribute_name' => '',
                            'value' => '',
                        ],
                    ],
                    'aggregator_type' => '',
                    'operator' => null,
                    'value' => '',
                ],
                'action_condition' => [
                    'condition_type' => '',
                    'conditions' => [
                        [
                            'condition_type' => '',
                            'conditions' => [],
                            'aggregator_type' => '',
                            'operator' => null,
                            'value' => '',
                        ],
                    ],
                    'aggregator_type' => '',
                    'operator' => null,
                    'value' => '',
                ],
                'stop_rules_processing' => $request->stop_rules_processing,
                'is_advanced' => true,
                'sort_order' => 0,
                'simple_action' => $request->simple_action,
                'discount_amount' => $request->discount_amount,
                'discount_step' => $request->discount_step,
                'discount_qty' => $request->discount_qty,
                'apply_to_shipping' => $request->apply_to_shipping,
                'times_used' => $timeUsed,
                'is_rss' => $request->rss,
                'coupon_type' => $request->coupon_type, // or "coupon_type" => "SPECIFIC_COUPON",
                'use_auto_generation' => isset($request->auto_generate) ? true : false, // use true if want to generate multiple codes for same rule
                'uses_per_coupon' => $request->uses_per_coupon,
                'simple_free_shipping' => '0',
            ];

            $url = $storeWebsite->magento_url.'/rest/V1/salesRules/';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
            $response = curl_exec($ch);
            $result = json_decode($response);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, CouponController::class, 'CouponRulesAdd');

            Log::channel('listMagento')->info(print_r([$url, $storeWebsite->api_token, json_encode($parameters), $response], true));
            if ($result != false) {
                if (isset($result->code) || isset($result->message)) {
                    return response()->json(['type' => 'error', 'message' => $result->message, 'data' => $result], 200);
                }

                if ($request->coupon_type == 'SPECIFIC_COUPON' && ! isset($request->auto_generate)) {
                    $response = $this->geteratePrimaryCouponCode($request->code, $result->rule_id, $request->uses_per_coustomer, $request->expiration, '', $storeWebsite);
                    if (! isset($response->coupon_id)) {
                        $this->deleteCouponCodeRuleByWebsiteId($result->rule_id, $request->store_website_id);
                        if (isset($response->message) && isset($response->parameters)) {
                            return response()->json(['type' => 'error', 'message' => str_replace('%1', $response->parameters[0], $response->message), 'data' => $result], 200);
                        } else {
                            return response()->json(['type' => 'error', 'message' => 'Coupon code cannot be create please check request with magento log', 'data' => $response], 200);
                        }
                    }
                }

                $local_rules = new CouponCodeRules();
                $local_rules->magento_rule_id = $result->rule_id;
                $local_rules->name = $request->name;
                $local_rules->description = $request->description;
                $local_rules->is_active = $request->active == '1' ? true : false;
                $local_rules->times_used = $timeUsed;
                $local_rules->website_ids = implode(',', $request->website_ids);
                $local_rules->customer_group_ids = implode(',', $request->customer_groups);
                $local_rules->coupon_type = $request->coupon_type;
                $local_rules->coupon_code = $request->code;
                $local_rules->use_auto_generation = isset($request->auto_generate) ? 1 : 0;
                $local_rules->uses_per_coupon = ! empty($request->uses_per_coupon) ? $request->uses_per_coupon : 0;
                $local_rules->uses_per_coustomer = ! empty($request->uses_per_coustomer) ? $request->uses_per_coustomer : 0;
                $local_rules->store_website_id = $request->store_website_id;
                $local_rules->is_rss = isset($request->rss) ? $request->rss : 0;
                $local_rules->priority = isset($request->priority) ? $request->priority : 0;
                $local_rules->from_date = $startDate;
                $local_rules->to_date = $endDate;
                $local_rules->stop_rules_processing = $request->stop_rules_processing;
                $local_rules->simple_action = $request->simple_action;
                $local_rules->discount_amount = $request->discount_amount;
                $local_rules->discount_step = $request->discount_step;
                $local_rules->discount_qty = $request->discount_qty;
                $local_rules->apply_to_shipping = $request->apply_to_shipping;
                $local_rules->simple_free_shipping = 0;
                $local_rules->created_by = Auth::User()->id;
                if ($local_rules->save()) {
                    CouponCodeRuleLog::create([
                        'rule_id' => $local_rules->id,
                        'coupon_code' => $local_rules->coupon_code,
                        'log_type' => 'store_success',
                        'message' => 'Rule id is created',
                    ]);
                    if (! empty($request->store_labels)) {
                        foreach ($request->store_labels as $key => $label) {
                            $store_view_value = new WebsiteStoreViewValue();
                            $store_view_value->rule_id = $local_rules->id;
                            $store_view_value->store_view_id = $key;
                            $store_view_value->value = $label;
                            $store_view_value->save();
                        }
                    }
                }
            }
            $count++;
        }
        if ($count == $key + 1) {
            return response()->json(['type' => 'success', 'data' => $result, 'message' => 'Added successfully'], 200);
        } else {
            return response()->json(['type' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

    public function geteratePrimaryCouponCode($code, $magento_rule_id, $uses_per_customer, $end_date, $laravel_rule_id, $store_website_id)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $authorization = 'Authorization: Bearer '.$store_website_id->api_token;
        $parameters = [];
        $parameters['coupon'] = [
            'code' => $code,
            'rule_id' => $magento_rule_id,
            'usage_limit' => 0,
            'usage_per_customer' => $uses_per_customer,
            'expiration_date' => $end_date,
            'is_primary' => true,
            'times_used' => 6,
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 0,
            'extension_attributes' => json_encode('{}'),
        ];

        $url = $store_website_id->magento_url.'/rest/V1/coupons';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = json_decode($response);
        curl_close($ch); // Close the connection
        LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode,CouponController::class, 'CouponRulesGeteratePrimaryCouponCode');

        Log::channel('listMagento')->info(print_r([$url, $store_website_id->api_token, json_encode($parameters)], true));

        return $result;
    }

    public function getCouponCodeRuleById(Request $request): JsonResponse
    {
        $result = CouponCodeRules::with(['store_labels'])->where('id', $request->rule_id)->first();
        $codes = Coupon::where('rule_id', $result->magento_rule_id)->orderByDesc('created_at')->get();
        $web_ids = explode(',', $result->website_ids);
        if (isset($result->id)) {
            $websites = Website::where('store_website_id', $result->store_website_id)->get();
            $website_stores = WebsiteStore::with('storeView')->get();
            $store_websites = StoreWebsite::all();
            $returnHTML = view('coupon.editModal')->with('result', $result)->with('websites', $websites)->with('website_stores', $website_stores)->with('web_ids', $web_ids)->with('store_websites', $store_websites)->with('codes', $codes)->render();

            return response()->json(['status' => 'success', 'data' => ['html' => $returnHTML], 'message' => 'Rule details'], 200);
        } else {
            return response()->json(['status' => 'error', 'data' => $result, 'message' => 'Something went wrong!'], 200);
        }
    }

    public function deleteCouponCodeRuleByWebsiteId($id, $storeWebsiteID)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $store_website = StoreWebsite::where('id', $storeWebsiteID)->first();
        $authorization = 'Authorization: Bearer '.$store_website->api_token;
        $url = $store_website->magento_url.'/rest/V1/salesRules/salesRules/'.$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($response);
        LogRequest::log($startTime, $url, 'DELETE', [], json_decode($response), $httpcode, CouponController::class, 'CouponRulesDeleteCouponCodeRuleByWebsiteId');

        return true;
    }

    public function deleteCouponCodeRuleById($id): RedirectResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $rule_lists = CouponCodeRules::where('id', $id)->first();
        $store_website = StoreWebsite::where('id', $rule_lists->store_website_id)->first();
        $authorization = 'Authorization: Bearer '.$store_website->api_token;
        $url = $store_website->magento_url.'/rest/V1/salesRules/salesRules/'.$rule_lists->magento_rule_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $result = json_decode($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'DELETE', [], json_decode($response), $httpcode, CouponController::class, 'CouponRulesDeleteCouponCodeRuleById');

        $coupon = Coupon::where('rule_id', $rule_lists->magento_rule_id)->delete();
        CouponCodeRules::where('id', $id)->delete();

        return redirect()->route('coupons.index');
    }

    public function generateCouponCode(Request $request): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $rule_id = CouponCodeRules::where('id', $request->rule_id)->first();
        $format = 'alphanum';

        if ($request->format == 1) {
            $format = 'alphanum';
        }

        if ($request->format == 2) {
            $format = 'alpha';
        }

        if ($request->format == 3) {
            $format = 'num';
        }

        $parameters = [
            'couponSpec' => [
                'rule_id' => $rule_id->magento_rule_id,
                'format' => $format,
                'quantity' => $request->qty,
                'length' => $request->length,
                'prefix' => $request->prefix,
                'suffix' => $request->suffix,
                'delimiter_at_every' => $request->dash,
                'delimiter' => '-',
            ],
        ];

        $store_website = StoreWebsite::where('id', $rule_id->store_website_id)->first();
        $authorization = 'Authorization: Bearer '.$store_website->api_token;

        $url = $store_website->magento_url.'/rest/V1/coupons/generate';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        $response = curl_exec($ch);
        $result = json_decode($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, CouponController::class, 'CouponRulesGenerateCouponCode');

        curl_close($ch); // Close the connection
        if (isset($result->message)) {
            return response()->json(['type' => 'error', 'message' => $result->message, 'data' => $result], 200);
        }

        foreach ($result as $re) {
            $coupon = new Coupon();
            $coupon->start = date('Y-m-d H:i:s');
            $coupon->magento_id = 0;
            $coupon->rule_id = $rule_id->magento_rule_id;
            $coupon->code = $re;
            $coupon->usage_count = $rule_id->times_used;
            $coupon->minimum_order_amount = 0;
            $coupon->discount_fixed = 0.0;
            $coupon->discount_percentage = 0.0;
            $coupon->uses = $rule_id->uses_per_coupon;
            $coupon->save();
        }

        return response()->json(['type' => 'success', 'data' => $result, 'message' => 'Added successfully'], 200);
    }

    public function updateRules(Request $request): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $store_lables = [];
        if (! empty($request->store_labels)) {
            foreach ($request->store_labels as $key => $lables) {
                array_push($store_lables, ['store_id' => $key, 'store_label' => $lables, 'extension_attributes' => '{}']);
            }
        }

        $local_rules = CouponCodeRules::where('id', $request->rule_id)->first();
        $store_website = StoreWebsite::where('id', $local_rules->store_website_id)->first();
        $authorization = 'Authorization: Bearer '.$store_website->api_token;
        $parameters = [];
        $parameters['rule'] = [
            'name' => $request->name_edit,
            'store_labels' => $store_lables,
            'description' => $request->description_edit,
            'website_ids' => array_unique($request->website_ids_edit),
            'customer_group_ids' => $request->customer_groups_edit,
            'from_date' => $request->start_edit,
            'to_date' => $request->expiration_edit,
            'uses_per_customer' => $request->uses_per_coustomer_edit,
            'is_active' => $request->active_edit == 1 ? true : false,
            'condition' => [
                'condition_type' => '',
                'conditions' => [
                    [
                        'condition_type' => '',
                        'operator' => '',
                        'attribute_name' => '',
                        'value' => '',
                    ],
                ],
                'aggregator_type' => '',
                'operator' => null,
                'value' => '',
            ],
            'action_condition' => [
                'condition_type' => '',
                'conditions' => [
                    [
                        'condition_type' => '',
                        'conditions' => [],
                        'aggregator_type' => '',
                        'operator' => null,
                        'value' => '',
                    ],
                ],
                'aggregator_type' => '',
                'operator' => null,
                'value' => '',
            ],
            'stop_rules_processing' => $request->stop_rules_processing,
            'is_advanced' => true,
            'sort_order' => 0,
            'simple_action' => $request->simple_action,
            'discount_amount' => $request->discount_amount,
            'discount_step' => $request->discount_step,
            'discount_qty' => $request->discount_qty,
            'apply_to_shipping' => $request->apply_to_shipping,
            'times_used' => 6,
            'is_rss' => isset($request->rss_edit) ? true : false,
            'coupon_type' => $request->coupon_type_edit, // or "coupon_type" => "SPECIFIC_COUPON",
            'use_auto_generation' => isset($request->auto_generate_edit) ? true : false, // use true if want to generate multiple codes for same rule
            'uses_per_coupon' => $request->uses_per_coupon_edit,
            'simple_free_shipping' => '0',
        ];

        $url = $store_website->magento_url.'/rest/V1/salesRules/'.$local_rules->magento_rule_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        $response = curl_exec($ch);
        $result = json_decode($response);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, CouponController::class, 'CouponRulesUpdate');

        Log::channel('listMagento')->info(print_r([$url, $store_website->api_token, json_decode($response)], true));

        curl_close($ch); // Close the connection
        if (isset($result->code) || isset($result->message)) {
            return response()->json(['type' => 'error', 'message' => $result->message, 'data' => $result], 200);
        }

        if ($request->coupon_type == 'SPECIFIC_COUPON' && ! isset($request->use_auto_generation)) {
            $this->geteratePrimaryCouponCode($request->code, $result->rule_id, $request->uses_per_coustomer, $request->expiration, $store_website);
        }

        $local_rules->name = $request->name_edit;
        $local_rules->description = $request->description_edit;
        $local_rules->is_active = $request->active_edit == 1 ? true : false;
        $local_rules->times_used = 6;
        $local_rules->website_ids = implode(',', array_unique($request->website_ids_edit));
        $local_rules->customer_group_ids = implode(',', array_unique($request->customer_groups_edit));
        $local_rules->coupon_type = $request->coupon_type_edit;
        $local_rules->use_auto_generation = isset($request->auto_generate_edit) ? 1 : 0;

        $local_rules->uses_per_coupon = ! empty($request->uses_per_coupon_edit) ? $request->uses_per_coupon_edit : 0;
        $local_rules->uses_per_coustomer = ! empty($request->uses_per_coustomer_edit) ? $request->uses_per_coustomer_edit : 0;

        $local_rules->store_website_id = $request->store_website_id_edit;
        $local_rules->is_rss = isset($request->rss_edit) ? 1 : 0;
        $local_rules->coupon_code = $request->code_edit;
        $local_rules->priority = $request->priority_edit;
        $local_rules->from_date = ! empty($request->start_edit) ? $request->start_edit : date('Y-m-d H:i:s');
        $local_rules->to_date = ! empty($request->expiration_edit) ? $request->expiration_edit : null;
        $local_rules->stop_rules_processing = $request->stop_rules_processing;
        $local_rules->simple_action = $request->simple_action;
        $local_rules->discount_amount = $request->discount_amount;
        $local_rules->discount_step = $request->discount_step;
        $local_rules->discount_qty = $request->discount_qty;
        $local_rules->apply_to_shipping = $request->apply_to_shipping;
        $local_rules->simple_free_shipping = 0;
        if ($local_rules->save()) {
            CouponCodeRuleLog::create([
                'rule_id' => $local_rules->id,
                'coupon_code' => $local_rules->coupon_code,
                'log_type' => 'update_success',
                'message' => 'Rule id is updated',
            ]);
            WebsiteStoreViewValue::where('rule_id', $request->rule_id)->delete();
            if (! empty($request->store_labels)) {
                foreach ($request->store_labels as $key => $label) {
                    $store_view_value = new WebsiteStoreViewValue();
                    $store_view_value->rule_id = $local_rules->id;
                    $store_view_value->store_view_id = $key;
                    $store_view_value->value = $label;
                    $store_view_value->save();
                }
            }
        }

        return response()->json(['type' => 'success', 'data' => $result, 'message' => 'Data updated successfully'], 200);
    }

    public function getWebsiteByStore(Request $request): JsonResponse
    {
        $store_id = $request->store_id;
        $websites = Website::where('store_website_id', $store_id)->get();
        $returnHTML = view('coupon.wepsiteDrpDwn')->with('websites', $websites)->render();

        return response()->json(['type' => 'success', 'data' => $returnHTML, 'message' => ''], 200);
    }

    public function deleteCouponByCode(Request $request): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $coupon = Coupon::where('id', $request->id)->first();
        $local_rules = CouponCodeRules::where('id', $coupon->rule_id)->first();
        $store_website = StoreWebsite::where('id', $local_rules->store_website_id)->first();

        $authorization = 'Authorization: Bearer '.$store_website->api_token;
        $parameters = ['codes' => [$coupon->code], 'ignoreInvalidCoupons' => true];
        $url = $store_website->magento_url.'/rest/V1/coupons/deleteByCodes';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

        $response = curl_exec($ch);
        $result = json_decode($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, CouponController::class, 'CouponRulesDeleteCouponByCode');

        $coupon = Coupon::where('id', $request->id)->delete();
        curl_close($ch); // Close the connection

        CouponCodeRuleLog::create([
            'rule_id' => $coupon->rule_id,
            'coupon_code' => $local_rules->coupon_code,
            'log_type' => 'delete_success',
            'message' => 'Rule id is deleted',
        ]);

        return response()->json(['type' => 'success', 'data' => [], 'message' => 'Coupon deleted successfully'], 200);
    }

    public function shortCutFroCreateCoupn(Request $request): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $store_website_id = ! empty($request->store_website_id) ? $request->store_website_id : 1;
        $website_ids = ! empty($request->website_ids) ? $request->website_ids : ['1', '2'];
        $store_labels = ! empty($request->store_labels) ? $request->store_labels : ['Quck Created Coupon'];
        $startDate = date('Y-m-d h:i');
        $name = ! empty($request->name) ? $request->name : 'Quick created coupon';
        $description = ! empty($request->description) ? $request->description : 'Quick created coupon';

        $customer_groups = ! empty($request->customer_groups) ? $request->customer_groups : [1];
        $uses_per_coustomer = ! empty($request->uses_per_coustomer) ? $request->uses_per_coustomer : 1;
        $is_active = true;
        $stop_rules_processing = ! empty($request->stop_rules_processing) ? $request->stop_rules_processing : true;
        $simple_action = ! empty($request->simple_action) ? $request->simple_action : 'by_percent';
        $discount_amount = ! empty($request->discount_amount) ? $request->discount_amount : '10';
        $discount_step = ! empty($request->discount_step) ? $request->discount_step : '1';
        $discount_qty = ! empty($request->discount_qty) ? $request->discount_qty : '10';
        $apply_to_shipping = ! empty($request->apply_to_shipping) ? $request->apply_to_shipping : true;
        $rss = isset($request->rss) ? $request->rss : 0;
        $coupon_type = ! empty($request->coupon_type) ? $request->coupon_type : 'SPECIFIC_COUPON';
        $auto_generate = isset($request->auto_generate) ? true : false; // use true if want to generate multiple codes for same rule
        $uses_per_coupon = ! empty($request->uses_per_coupon) ? $request->uses_per_coupon : '1';
        $code = ! empty($request->code) ? $request->code : '36PxZ';
        $rule_id = ! empty($request->rule_id) ? $request->rule_id : '';
        $priority = isset($request->priority) ? $request->priority : 0;

        $store_website = StoreWebsite::where('id', $request->store_website_id)->first();
        $store_lables = [];
        if (! empty($request->store_labels)) {
            foreach ($request->store_labels as $key => $lables) {
                array_push($store_lables, ['store_id' => $key, 'store_label' => $lables, 'extension_attributes' => '{}']);
            }
        }

        $startDate = ! empty($request->start) ? $request->start : date('Y-m-d');
        $endDate = $request->expiration;
        $timeUsed = 6;

        $authorization = 'Authorization: Bearer '.$store_website->api_token;

        $parameters = [];
        $parameters['rule'] = [
            'name' => $request->name,
            'store_labels' => $store_lables,
            'description' => $request->description,
            'website_ids' => array_unique($request->website_ids),
            'customer_group_ids' => $request->customer_groups,
            'from_date' => $startDate,
            'to_date' => $endDate,
            'uses_per_customer' => $request->uses_per_coustomer,
            'is_active' => $request->active == '1' ? true : false,
            'condition' => [
                'condition_type' => '',
                'conditions' => [
                    [
                        'condition_type' => '',
                        'operator' => '',
                        'attribute_name' => '',
                        'value' => '',
                    ],
                ],
                'aggregator_type' => '',
                'operator' => null,
                'value' => '',
            ],
            'action_condition' => [
                'condition_type' => '',
                'conditions' => [
                    [
                        'condition_type' => '',
                        'conditions' => [],
                        'aggregator_type' => '',
                        'operator' => null,
                        'value' => '',
                    ],
                ],
                'aggregator_type' => '',
                'operator' => null,
                'value' => '',
            ],
            'stop_rules_processing' => $request->stop_rules_processing,
            'is_advanced' => true,
            'sort_order' => 0,
            'simple_action' => $request->simple_action,
            'discount_amount' => $request->discount_amount,
            'discount_step' => $request->discount_step,
            'discount_qty' => $request->discount_qty,
            'apply_to_shipping' => $request->apply_to_shipping,
            'times_used' => $timeUsed,
            'is_rss' => $request->rss,
            'coupon_type' => $request->coupon_type, // or "coupon_type" => "SPECIFIC_COUPON",
            'use_auto_generation' => isset($request->auto_generate) ? true : false, // use true if want to generate multiple codes for same rule
            'uses_per_coupon' => $request->uses_per_coupon,
            'simple_free_shipping' => '0',
        ];

        $url = $store_website->magento_url.'/rest/V1/salesRules/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        Log::channel('listMagento')->info(print_r([$url, $store_website->api_token, json_encode($parameters)], true));
        $response = curl_exec($ch);
        $result = json_decode($response);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($response), $httpcode, CouponController::class, 'CouponRulesShortCutFroCreateCoupn');

        curl_close($ch); // Close the connection
        if (isset($result->code)) {
            return response()->json(['type' => 'error', 'message' => $result->message, 'data' => $result], 200);
        }

        if ($request->coupon_type == 'SPECIFIC_COUPON' && ! isset($request->auto_generate)) {
            $response = $this->geteratePrimaryCouponCode($request->code, $result->rule_id, $request->uses_per_coustomer, $request->expiration, '', $store_website);
            if (! isset($response->coupon_id)) {
                $this->deleteCouponCodeRuleByWebsiteId($result->rule_id, $request->store_website_id);
                if (isset($response->message) && isset($response->parameters)) {
                    return response()->json(['type' => 'error', 'message' => str_replace('%1', $response->parameters[0], $response->message), 'data' => $result], 200);
                } else {
                    return response()->json(['type' => 'error', 'message' => 'Coupon code cannot be create please check request with magento log', 'data' => $response], 200);
                }
            }
        }

        $local_rules = new CouponCodeRules();
        $local_rules->magento_rule_id = $result->rule_id;
        $local_rules->name = $request->name;
        $local_rules->description = $request->description;
        $local_rules->is_active = $request->active == '1' ? true : false;
        $local_rules->times_used = $timeUsed;
        $local_rules->website_ids = implode(',', $request->website_ids);
        $local_rules->customer_group_ids = implode(',', $request->customer_groups);
        $local_rules->coupon_type = $request->coupon_type;
        $local_rules->coupon_code = $request->code;
        $local_rules->use_auto_generation = isset($request->auto_generate) ? 1 : 0;

        $local_rules->uses_per_coupon = ! empty($request->uses_per_coupon) ? $request->uses_per_coupon : 0;
        $local_rules->uses_per_coustomer = ! empty($request->uses_per_coustomer) ? $request->uses_per_coustomer : 0;

        $local_rules->store_website_id = $request->store_website_id;
        $local_rules->is_rss = isset($request->rss) ? $request->rss : 0;
        $local_rules->priority = isset($request->priority) ? $request->priority : 0;
        $local_rules->from_date = $startDate;
        $local_rules->to_date = $endDate;
        $local_rules->stop_rules_processing = $request->stop_rules_processing;
        $local_rules->simple_action = $request->simple_action;
        $local_rules->discount_amount = $request->discount_amount;
        $local_rules->discount_step = $request->discount_step;
        $local_rules->discount_qty = $request->discount_qty;
        $local_rules->apply_to_shipping = $request->apply_to_shipping;
        $local_rules->simple_free_shipping = 0;
        if ($local_rules->save()) {
            if (! empty($request->store_labels)) {
                foreach ($request->store_labels as $key => $label) {
                    $store_view_value = new WebsiteStoreViewValue();
                    $store_view_value->rule_id = $local_rules->id;
                    $store_view_value->store_view_id = $key;
                    $store_view_value->value = $label;
                    $store_view_value->save();
                }
            }
        }

        return response()->json(['type' => 'success', 'data' => $result, 'message' => 'Added successfully'], 200);
    }

    /*
    * Send mail method
    */
    public function sendCoupons(Request $request): RedirectResponse
    {
        $ruleId = $request->send_rule_id;
        $couponCodeRule = CouponCodeRules::find($ruleId);
        $customerId = $request->customer;
        $customerData = Customer::find($customerId);
        CouponCodeRuleLog::create([
            'rule_id' => $ruleId,
            'coupon_code' => $couponCodeRule->coupon_code,
            'log_type' => 'send_to_user_intiate',
            'message' => 'Sending coupon mail to '.$customerData->email,
        ]);
        $emailAddress = EmailAddress::where('store_website_id', $couponCodeRule->store_website_id)->first();
        $mailData['receiver_email'] = $customerData->email;
        $mailData['sender_email'] = $emailAddress->from_address;
        $mailData['coupon'] = $couponCodeRule->coupon_code;
        $mailData['model_id'] = $ruleId;
        $mailData['model_class'] = CouponCodeRules::class;
        $mailData['store_website_id'] = $couponCodeRule->store_website_id;
        $emailClass = (new AddCoupon($mailData))->build();
        $email = Email::create([
            'model_id' => $ruleId,
            'model_type' => CouponCodeRules::class,
            'from' => $emailAddress->from_address,
            'to' => $customerData->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'coupons',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => $couponCodeRule->store_website_id,
            'is_draft' => 0,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');
        CouponCodeRuleLog::create([
            'rule_id' => $ruleId,
            'coupon_code' => $couponCodeRule->coupon_code,
            'log_type' => 'send_mail',
            'message' => 'Mail was sent to '.$customerData->email,
        ]);

        return redirect()->route('coupons.index')->with('success', 'Email is send to the user');
    }

    public function logCouponCodeRuleAjax(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $log = CouponCodeRuleLog::where('rule_id', $request->get('id'))->orderByDesc('id')->get();

            return response()->json(['data' => $log], 200);
        }

        return response()->json(['data' => []], 200);
    }
}
