<?php

namespace App\Http\Controllers;
use App\OrderStatus;
use App\Jobs\SendEmail;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\LeadsController;
use App\HistoryWhatsappNumber;
use App\CustomerKycDocument;
use App\CustomerDetailHistory;
use App\CreditEmailLog;

use App\ApiKey;
use App\Brand;
use App\ChatMessage;
use App\CommunicationHistory;
use App\Complaint;
use App\CreditHistory;
use App\CreditLog;
use App\Customer;
use App\CustomerAddressData;
use App\CustomerPriorityPoint;
use App\CustomerPriorityRangePoint;
use App\Email;
use App\EmailAddress;
use App\ErpLeads;
use App\Exports\CustomersExport;
use App\Helpers;
use App\Http\Requests\AddReplyCategoryCustomerRequest;
use App\Http\Requests\DestroyReplyCategoryCustomerRequest;
use App\Http\Requests\EmailSendCustomerRequest;
use App\Http\Requests\ImportCustomerRequest;
use App\Http\Requests\MergeCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Requests\UpdatePhoneCustomerRequest;
use App\Imports\CustomerImport;
use App\Instruction;
use App\InstructionCategory;
use App\Mails\Manual\AdvanceReceipt;
use App\Mails\Manual\CustomerEmail;
use App\Mails\Manual\IssueCredit;
use App\Mails\Manual\OrderConfirmation;
use App\Mails\Manual\RefundProcessed;
use App\Marketing\WhatsappConfig;
use App\Message;
use App\MessageQueue;
use App\Models\CustomerNextAction;
use App\Order;
use App\OrderStatus as OrderStatuses;
use App\Product;
use App\QuickSellGroup;
use App\ReadOnly\PurchaseStatus;
use App\ReadOnly\SoloNumbers;
use App\Reply;
use App\ReplyCategory;
use App\Setting;
use App\Status;
use App\StoreWebsite;
use App\StoreWebsiteTwilioNumber;
use App\SuggestedProduct;
use App\Supplier;
use App\TwilioPriority;
use App\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Media as PlunkMediable;

class CustomerController extends Controller
{
    const DEFAULT_FOR = 1; //For Customer

    /**
     * This function is use for getting data from the credit history data
     *
     * @param  $id  int
     * @return $htlm
     */
    public function creditHistory($id)
    {
        $custHosData = CreditHistory::where('customer_id', $id)->get();
        $html = '';
        foreach ($custHosData as $key => $val) {
            $html .= '<tr>';
            $html .= '<td>'.$val->id.'</td>';
            $html .= '<td>'.$val->used_credit.'</td>';
            $html .= '<td>'.$val->used_in.'</td>';
            $html .= '<td>'.$val->type.'</td>';
            $html .= '<td>'.date('d-m-Y', strtotime($val->created_at)).'</td>';
            $html .= '</tr>';
        }
        if ($html) {
            return $html;
        } else {
            return 'No record found';
        }
    }

    /**
     * This function is use for getting data from the credit log data
     *
     * @param  $id  int
     * @return $htlm
     */
    public function creditLog($id)
    {
        $custHosData = CreditLog::where('customer_id', $id)->get();
        $html = '';
        foreach ($custHosData as $key => $val) {
            $html .= '<tr>';
            $html .= '<td>'.date('d-m-Y', strtotime($val->created_at)).'</td>';
            $html .= '<td>'.$val->request.'</td>';
            $html .= '<td>'.$val->response.'</td>';
            $html .= '<td>'.$val->status.'</td>';
            $html .= '<td>'.$val->id.'</td>';
            $html .= '</tr>';
        }
        if ($html) {
            return $html;
        } else {
            return 'No record found';
        }
    }

    public function add_customer_address(Request $request)
    {
        $apply_job = CustomerAddressData::create([
            'customer_id' => $request->customer_id,
            'entity_id' => $request->entity_id,
            'parent_id' => $request->parent_id,
            'address_type' => $request->address_type,
            'region' => $request->region,
            'region_id' => $request->region_id,
            'postcode' => $request->postcode,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'company' => $request->company,
            'country_id' => $request->country_id,
            'telephone' => $request->telephone,
            'prefix' => $request->prefix,
            'street' => $request->street,
        ]);
        $apply_job->save();

        return $apply_job;
    }

    public function index(Request $request): View
    {
        $complaints = Complaint::whereNotNull('customer_id')->pluck('complaint', 'customer_id')->toArray();
        $instructions = Instruction::with('remarks')->orderByDesc('is_priority')->orderByDesc('created_at')->select(['id', 'instruction', 'customer_id', 'assigned_to', 'pending', 'completed_at', 'verified', 'is_priority', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $orders = Order::latest()->select(['id', 'customer_id', 'order_status', 'order_status_id', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $order_stats = Order::selectRaw('order_status, COUNT(*) as total')->whereNotNull('order_status')->groupBy('order_status')->get();

        $totalCount = 0;
        foreach ($order_stats as $order_stat) {
            $totalCount += $order_stat->total;
        }

        $orderStatus = [
            'order received',
            'follow up for advance',
            'prepaid',
            'proceed without advance',
            'pending purchase (advance received)',
            'purchase complete',
            'product shipped from italy',
            'product in stock',
            'product shipped to client',
            'delivered',
            'cancel',
            'refund to be processed',
            'refund credited',
        ];

        $finalOrderStats = [];
        foreach ($orderStatus as $status) {
            foreach ($order_stats as $order_stat) {
                if ($status == strtolower($order_stat->order_status)) {
                    $finalOrderStats[] = $order_stat;
                }
            }
        }

        foreach ($order_stats as $order_stat) {
            if (! in_array(strtolower($order_stat->order_status), $orderStatus)) {
                $finalOrderStats[] = $order_stat;
            }
        }

        $order_stats = $finalOrderStats;

        $finalOrderStats = [];
        foreach ($order_stats as $key => $order_stat) {
            $finalOrderStats[] = [
                $order_stat->order_status,
                $order_stat->total,
                ($order_stat->total / $totalCount) * 100,
                [
                    '#CCCCCC',
                    '#95a5a6',
                    '#b2b2b2',
                    '#999999',
                    '#2c3e50',
                    '#7f7f7f',
                    '#666666',
                    '#4c4c4c',
                    '#323232',
                    '#191919',
                    '#000000',
                    '#414a4c',
                    '#353839',
                    '#232b2b',
                    '#34495e',
                    '#7f8c8d',
                ][$key],

            ];
        }

        $order_stats = $finalOrderStats;

        $results = $this->getCustomersIndex($request);
        $term = $request->input('term');
        $reply_categories = ReplyCategory::all();
        $api_keys = ApiKey::select('number')->get();

        $type = $request->type ?? '';

        $orderby = 'desc';
        if ($request->orderby == '') {
            $orderby = 'asc';
        }

        $customers_all = Customer::all();
        $customer_names = Customer::select(['name'])->get()->toArray();

        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $brands = Brand::all()->toArray();

        foreach ($customer_names as $name) {
            $search_suggestions[] = $name['name'];
        }

        $users_array = Helpers::getUserArray(User::all());
        $sortUsers = User::all()->sortBy('name')->pluck('name', 'id');

        $last_set_id = MessageQueue::max('group_id');

        $queues_total_count = MessageQueue::where('status', '!=', 1)->where('group_id', $last_set_id)->count();
        $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('group_id', $last_set_id)->count();

        $start_time = $request->range_start ? "$request->range_start 00:00" : Carbon::now()->subDay();
        $end_time = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->subDay();

        $allCustomers = $results[0]->pluck('id')->toArray();

        // Get all sent broadcasts from the past month
        // $sbQuery = DB::select("select MIN(group_id) AS minGroup, MAX(group_id) AS maxGroup from message_queues where sent = 1 and created_at>'" . date('Y-m-d H:i:s', strtotime('1 month ago')) . "'");
        $sbQuery = MessageQueue::select(DB::raw('MIN(group_id) AS minGroup'), DB::raw('MAX(group_id) AS maxGroup'))
            ->where('sent', 1)
            ->where('created_at', '>', Carbon::now()->subMonth())
            ->first();

        // Add broadcasts to array
        $broadcasts = [];
        if ($sbQuery !== null) {
            // Get min and max
            $minBroadcast = $sbQuery[0]->minGroup;
            $maxBroadcast = $sbQuery[0]->maxGroup;

            // Deduct 2 from min
            $minBroadcast = $minBroadcast - 2;

            for ($i = $minBroadcast; $i <= $maxBroadcast; $i++) {
                $broadcasts[] = $i;
            }
        }

        $shoe_size_group = Customer::selectRaw('shoe_size, count(id) as counts')
            ->whereNotNull('shoe_size')
            ->groupBy('shoe_size')
            ->pluck('counts', 'shoe_size');

        $clothing_size_group = Customer::selectRaw('clothing_size, count(id) as counts')
            ->whereNotNull('clothing_size')
            ->groupBy('clothing_size')
            ->pluck('counts', 'clothing_size');

        $groups = QuickSellGroup::select('id', 'name', 'group')->orderBy('name')->get();
        $storeWebsites = StoreWebsite::all()->pluck('website', 'id')->toArray();
        $solo_numbers = (new SoloNumbers)->all();

        $mediaTags = config('constants.media_tags'); // Use config variable
        $apiInstance = WhatsappConfig::getWhatsappConfigs();

        $image_message = [];
        foreach ($results[0] as $result) {
            $imageMessage = ChatMessage::find($result->message_id);
            if ($imageMessage && $imageMessage->hasMedia($mediaTags)) {
                $image_message[$result->message_id] = $imageMessage->getMedia($mediaTags);
            } else {
                $image_message[$result->message_id] = null;
            }
        }

        $shortCuts = ['image_shortcut', 'price_shortcut', 'call_shortcut', 'screenshot_shortcut', 'details_shortcut', 'purchase_shortcut'];
        $shortcutArr = [];
        foreach ($shortCuts as $shotcut) {
            $shortcutArr[$shotcut] = Setting::get($shotcut);
        }

        return view('customers.index', [
            'storeWebsites' => $storeWebsites,
            'solo_numbers' => $solo_numbers,
            'customers' => $results[0],
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($results[1]),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'leads_data' => $results[2],
            'order_stats' => $order_stats,
            'complaints' => $complaints,
            'shoe_size_group' => $shoe_size_group,
            'clothing_size_group' => $clothing_size_group,
            'broadcasts' => $broadcasts,
            'groups' => $groups,
            'mediaTags' => $mediaTags,
            'apiInstance' => $apiInstance,
            'image_message' => $image_message,
            'shortcutArr' => $shortcutArr,
            'sortUsers' => $sortUsers,
        ]);
    }

    public function getCustomersIndex(Request $request)
    {
        // Set search term
        $term = $request->term;
        // Set delivery status
        $delivery_status = [
            'Follow up for advance',
            'Proceed without Advance',
            'Advance received',
            'Cancel',
            'Prepaid',
            'Product Shiped form Italy',
            'In Transist from Italy',
            'Product shiped to Client',
            'Delivered',
        ];

        // Set empty clauses for later usage
        $orderWhereClause = '';
        $searchWhereClause = '';
        $filterWhereClause = '';
        $leadsWhereClause = '';

        if (! empty($term)) {
            $searchWhereClause = " AND (customers.name LIKE '%$term%' OR customers.phone LIKE '%$term%' OR customers.instahandler LIKE '%$term%')";
            $orderWhereClause = "WHERE orders.order_id LIKE '%$term%'";
        }

        if ($request->get('shoe_size')) {
            $searchWhereClause .= " AND customers.shoe_size = '".$request->get('shoe_size')."'";
        }

        if ($request->get('clothing_size')) {
            $searchWhereClause .= " AND customers.clothing_size = '".$request->get('clothing_size')."'";
        }

        if ($request->get('shoe_size_group')) {
            $searchWhereClause .= " AND customers.shoe_size = '".$request->get('shoe_size_group')."'";
        }

        if ($request->get('clothing_size_group')) {
            $searchWhereClause .= " AND customers.clothing_size = '".$request->get('clothing_size_group')."'";
        }

        if ($request->get('customer_id')) {
            $searchWhereClause .= " AND customers.id LIKE '%".$request->get('customer_id')."%'";
        }

        if ($request->get('customer_name')) {
            $searchWhereClause .= " AND customers.name LIKE '%".$request->get('customer_name')."%'";
        }

        $orderby = 'DESC';

        if ($request->input('orderby')) {
            $orderby = 'ASC';
        }
        $sortby = 'communication';

        $sortBys = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'instagram' => 'instahandler',
            'lead_created' => 'lead_created',
            'order_created' => 'order_created',
            'rating' => 'rating',
            'communication' => 'communication',
        ];

        if (isset($sortBys[$request->input('sortby')])) {
            $sortby = $sortBys[$request->input('sortby')];
        }

        $start_time = $request->range_start ? "$request->range_start 00:00" : '';
        $end_time = $request->range_end ? "$request->range_end 23:59" : '';

        if ($start_time != '' && $end_time != '') {
            $filterWhereClause = " AND last_communicated_at BETWEEN '".$start_time."' AND '".$end_time."'";
        }

        if ($request->type == 'unread' || $request->type == 'unapproved') {
            $join = 'RIGHT';
            $type = $request->type == 'unread' ? 0 : ($request->type == 'unapproved' ? 1 : 0);
            $orderByClause = " ORDER BY is_flagged DESC, message_status ASC, last_communicated_at $orderby";
            $filterWhereClause = " AND chat_messages.status = $type";
            $messageWhereClause = ' WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 AND chat_messages.status != 10';

            if ($start_time != '' && $end_time != '') {
                $filterWhereClause = " AND (last_communicated_at BETWEEN '".$start_time."' AND '".$end_time."') AND message_status = $type";
            }
        } else {
            if (
                strtolower($request->get('type')) === 'advance received' ||
                strtolower($request->get('type')) === 'cancel' ||
                strtolower($request->get('type')) === 'delivered' ||
                strtolower($request->get('type')) === 'follow up for advance' ||
                strtolower($request->get('type')) === 'high priority' ||
                strtolower($request->get('type')) === 'in transist from italy' ||
                strtolower($request->get('type')) === 'prepaid' ||
                strtolower($request->get('type')) === 'proceed without advance' ||
                strtolower($request->get('type')) === 'product shiped form italy' ||
                strtolower($request->get('type')) === 'product shiped to client' ||
                strtolower($request->get('type')) === 'refund credited' ||
                strtolower($request->get('type')) === 'refund dispatched' ||
                strtolower($request->get('type')) === 'refund to be processed'
            ) {
                $join = 'LEFT';
                $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                $messageWhereClause = ' WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9';
                if ($orderWhereClause) {
                    $orderWhereClause .= ' AND ';
                } else {
                    $orderWhereClause = ' WHERE ';
                }
                $orderWhereClause .= 'orders.order_status = "'.$request->get('type').'"';
                $filterWhereClause = ' AND order_status = "'.$request->get('type').'"';
            } else {
                if (strtolower($request->type) != 'new' && strtolower($request->type) != 'delivery' && strtolower($request->type) != 'refund to be processed' && strtolower($request->type) != '') {
                    $join = 'LEFT';
                    $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                    $messageWhereClause = ' WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9';

                    if ($request->type == '0') {
                        $leadsWhereClause = ' AND lead_status IS NULL';
                    } else {
                        $leadsWhereClause = " AND lead_status = $request->type";
                    }
                } else {
                    if ($sortby === 'communication') {
                        $join = 'LEFT';
                        $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                        $messageWhereClause = ' WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9';
                    }
                }
            }
        }

        $assignedWhereClause = '';
        if (Auth::user()->hasRole('Customer Care')) {
            $user_id = Auth::id();
            $assignedWhereClause = " AND id IN (SELECT customer_id FROM user_customers WHERE user_id = $user_id)";
        }

        if (! $orderByClause) {
            $orderByClause = ' ORDER BY instruction_completed_at DESC';
        } else {
            $orderByClause .= ', instruction_completed_at DESC';
        }

        $sql = '
            SELECT
                customers.id,
                customers.email,
                customers.frequency,
                customers.reminder_message,
                customers.name,
                customers.phone,
                customers.is_blocked,
                customers.is_flagged,
                customers.is_error_flagged,
                customers.is_priority,
                customers.instruction_completed_at,
                customers.whatsapp_number,
                customers.do_not_disturb,
                chat_messages.*,
                chat_messages.status AS message_status,
                chat_messages.number,
                twilio_active_numbers.phone_number as phone_number,
                orders.*,
                order_products.*,
                leads.*
            FROM
                customers
            LEFT JOIN
                (
                    SELECT
                        chat_messages.id AS message_id,
                        chat_messages.customer_id,
                        chat_messages.number,
                        chat_messages.message,
                        chat_messages.sent AS message_type,
                        chat_messages.status,
                        chat_messages.created_at,
                        chat_messages.created_at AS last_communicated_at
                    FROM
                        chat_messages
                    '.$messageWhereClause.'
                ) AS chat_messages
            ON
                customers.id=chat_messages.customer_id AND
                chat_messages.message_id=(
                    SELECT
                        MAX(id)
                    FROM
                        chat_messages
                    '.$messageWhereClause.(! empty($messageWhereClause) ? ' AND ' : '').'
                        chat_messages.customer_id=customers.id
                    GROUP BY
                        chat_messages.customer_id
                )
            LEFT JOIN
                (
                    SELECT
                        MAX(orders.id) as order_id,
                        orders.customer_id,
                        MAX(orders.created_at) as order_created,
                        orders.order_status as order_status
                    FROM
                        orders
                    '.$orderWhereClause.'
                    GROUP BY
                        customer_id
                ) as orders
            ON
                customers.id=orders.customer_id
            LEFT JOIN
                (
                    SELECT
                        order_products.order_id as purchase_order_id,
                        order_products.purchase_status
                    FROM
                        order_products
                    GROUP BY
                        purchase_order_id
                ) as order_products
            ON
                orders.order_id=order_products.purchase_order_id
            LEFT JOIN
                (
                    SELECT
                        MAX(id) as lead_id,
                        leads.customer_id,
                        leads.rating as lead_rating,
                        MAX(leads.created_at) as lead_created,
                        leads.status as lead_status
                    FROM
                        leads
                    GROUP BY
                        customer_id
                ) AS leads
            ON
                customers.id = leads.customer_id
            LEFT JOIN store_website_twilio_numbers
            ON
                store_website_twilio_numbers.store_website_id = customers.store_website_id
            LEFT JOIN twilio_active_numbers
            On
                twilio_active_numbers.id = store_website_twilio_numbers.twilio_active_number_id
            WHERE
                customers.deleted_at IS NULL AND
                customers.id IS NOT NULL
            '.$searchWhereClause.'
            '.$filterWhereClause.'
            '.$leadsWhereClause.'
            '.$assignedWhereClause.'
            '.$orderByClause.'
        ';
        $customers = DB::select($sql);

        echo '<!-- ';
        echo $sql;
        echo '-->';

        $oldSql = '
            SELECT
              *
            FROM
            (
                SELECT
                    customers.id,
                    customers.frequency,
                    customers.reminder_message,
                    customers.name,
                    customers.phone,
                    customers.is_blocked,
                    customers.is_flagged,
                    customers.is_error_flagged,
                    customers.is_priority,
                    customers.deleted_at,
                    customers.instruction_completed_at,
                    order_status,
                    purchase_status,
                    (
                    SELECT
                            mm5.status
                        FROM
                            leads mm5
                        WHERE
                            mm5.id=lead_id
                    ) AS lead_status,
                    lead_id,
                    (
                    SELECT
                            mm3.id
                        FROM
                            chat_messages mm3
                        WHERE
                            mm3.id=message_id
                    ) AS message_id,
                    (
                    SELECT
                            mm1.message
                        FROM
                            chat_messages mm1
                        WHERE mm1.id=message_id
                    ) as message,
                    (
                    SELECT
                            mm2.status
                        FROM
                            chat_messages mm2
                        WHERE
                            mm2.id = message_id
                    ) AS message_status,
                    (
                    SELECT
                            mm4.sent
                        FROM
                            chat_messages mm4
                        WHERE
                            mm4.id = message_id
                    ) AS message_type,
                    (
                    SELECT
                            mm2.created_at
                        FROM
                            chat_messages mm2
                        WHERE
                            mm2.id = message_id
                    ) as last_communicated_at
                FROM
                    (
                        SELECT
                            *
                        FROM
                            customers
                        LEFT JOIN
                            (
                                SELECT
                                    MAX(id) as lead_id,
                                    leads.customer_id as lcid,
                                    leads.rating as lead_rating,
                                    MAX(leads.created_at) as lead_created,
                                    leads.status as lead_status
                                FROM
                                    leads
                                GROUP BY
                                    customer_id
                            ) AS leads
                        ON
                            customers.id = leads.lcid
                        LEFT JOIN
                            (
                                SELECT
                                    MAX(id) as order_id,
                                    orders.customer_id as ocid,
                                    MAX(orders.created_at) as order_created,
                                    orders.order_status as order_status
                                FROM
                                    orders '.$orderWhereClause.'
                                GROUP BY
                                    customer_id
                            ) as orders
                        ON
                            customers.id = orders.ocid
                        LEFT JOIN
                            (
                                SELECT
                                    order_products.order_id as purchase_order_id,
                                    order_products.purchase_status
                                FROM
                                    order_products
                                GROUP BY
                                    purchase_order_id
                            ) as order_products
                        ON
                            orders.order_id = order_products.purchase_order_id
                        '.$join.' JOIN
                            (
                                SELECT
                                    MAX(id) as message_id,
                                    customer_id,
                                    message,
                                    MAX(created_at) as message_created_At
                                FROM
                                    chat_messages '.$messageWhereClause.'
                                GROUP BY
                                    customer_id
                                ORDER BY
                                    chat_messages.created_at '.$orderby.'
                            ) AS chat_messages
                        ON
                            customers.id = chat_messages.customer_id
                    ) AS customers
                WHERE
                    deleted_at IS NULL
                ) AND (
                    id IS NOT NULL
                )
                '.$searchWhereClause.'
          ) AS customers
          '.$filterWhereClause.$leadsWhereClause.
            $assignedWhereClause.
            $orderByClause;

        // $leads_data = DB::select('
        //               SELECT COUNT(*) AS total,
        //               (SELECT mm1.status FROM leads mm1 WHERE mm1.id = lead_id) as lead_final_status
        //                FROM customers

        //               LEFT JOIN (
        //                 SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as lead_rating, MAX(leads.created_at) as lead_created, leads.status as lead_status
        //                 FROM leads
        //                 GROUP BY customer_id
        //               ) AS leads
        //               ON customers.id = leads.lcid

        //               WHERE (deleted_at IS NULL) AND (id IS NOT NULL)
        //               GROUP BY lead_final_status;
        // 					');

        $leads_data = Customer::leftJoinSub(function ($query) {
            $query->select(DB::raw('MAX(id) as lead_id'), 'customer_id as lcid', 'rating as lead_rating', DB::raw('MAX(created_at) as lead_created'), 'status as lead_status')
                ->from('leads')
                ->groupBy('customer_id');
        }, 'leads', 'customers.id', '=', 'leads.lcid')
            ->whereNull('deleted_at')
            ->whereNotNull('customers.id')
            ->selectRaw('COUNT(*) AS total, (SELECT mm1.status FROM leads mm1 WHERE mm1.id = lead_id) as lead_final_status')
            ->groupBy('lead_final_status')
            ->get();

        $ids_list = [];

        foreach ($customers as $customer) {
            if ($customer->id != null) {
                $ids_list[] = $customer->id;
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = empty(Setting::get('pagination')) ? 25 : Setting::get('pagination');
        $currentItems = array_slice($customers, $perPage * ($currentPage - 1), $perPage);
        $customers = new LengthAwarePaginator($currentItems, count($customers), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return [$customers, $ids_list, $leads_data];
    }

    public function customerstest(Request $request): View
    {
        $instructions = Instruction::with('remarks')->orderByDesc('is_priority')->orderByDesc('created_at')->select(['id', 'instruction', 'customer_id', 'assigned_to', 'pending', 'completed_at', 'verified', 'is_priority', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $orders = Order::latest()->select(['id', 'customer_id', 'order_status', 'created_at'])->get()->groupBy('customer_id')->toArray();

        $reply_categories = ReplyCategory::all();
        $api_keys = ApiKey::select('number')->get();

        
        $customers_all = Customer::all();
        $customer_names = Customer::select(['name'])->get()->toArray();

        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $brands = Brand::all()->toArray();

        foreach ($customer_names as $name) {
            $search_suggestions[] = $name['name'];
        }

        $users_array = Helpers::getUserArray(User::all());

        $last_set_id = MessageQueue::max('group_id');

        $queues_total_count = MessageQueue::where('status', '!=', 1)->where('group_id', $last_set_id)->count();
        $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('group_id', $last_set_id)->count();

        $term = $request->input('term');
        $delivery_status = [
            'Follow up for advance',
            'Proceed without Advance',
            'Advance received',
            'Cancel',
            'Prepaid',
            'Product Shiped form Italy',
            'In Transist from Italy',
            'Product shiped to Client',
            'Delivered',
        ];

        $orderWhereClause = '';
        $searchWhereClause = '';
        $filterWhereClause = '';

        if (! empty($term)) {
            $searchWhereClause = " AND (customers.name LIKE '%$term%' OR customers.phone LIKE '%$term%' OR customers.instahandler LIKE '%$term%')";
            $orderWhereClause = "WHERE orders.order_id LIKE '%$term%'";
        }

        $orderby = 'DESC';

        if ($request->input('orderby')) {
            $orderby = 'ASC';
        }

        $sortby = 'communication';

        $sortBys = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'instagram' => 'instahandler',
            'lead_created' => 'lead_created',
            'order_created' => 'order_created',
            'rating' => 'rating',
            'communication' => 'communication',
        ];

        if (isset($sortBys[$request->input('sortby')])) {
            $sortby = $sortBys[$request->input('sortby')];
        }

        $start_time = $request->input('range_start') ?? '';
        $end_time = $request->input('range_end') ?? '';

        if ($start_time != '' && $end_time != '') {
            $filterWhereClause = " WHERE last_communicated_at BETWEEN '".$start_time."' AND '".$end_time."'";
        }

        if ($request->type == 'unread' || $request->type == 'unapproved') {
            $join = 'RIGHT';
            $type = $request->type == 'unread' ? 0 : ($request->type == 'unapproved' ? 1 : 0);
            $orderByClause = " ORDER BY is_flagged DESC, message_status ASC, `last_communicated_at` $orderby";
            $filterWhereClause = " WHERE message_status = $type";

            if ($start_time != '' && $end_time != '') {
                $filterWhereClause = " WHERE (last_communicated_at BETWEEN '".$start_time."' AND '".$end_time."') AND message_status = $type";
            }
        } else {
            if ($sortby === 'communication') {
                $join = 'LEFT';
                $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
            }
        }

        // $new_customers = DB::select('
        // 							SELECT * FROM
        //             (SELECT customers.id, customers.name, customers.phone, customers.is_blocked, customers.is_flagged, customers.is_error_flagged, customers.is_priority, customers.deleted_at,
        //             lead_id, lead_status, lead_created, lead_rating,
        //             order_id, order_status, order_created, purchase_status,
        //             (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
        //             (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
        //             (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
        //             (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
        //             (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

        //             FROM (
        //               SELECT * FROM customers

        //               LEFT JOIN (
        //                 SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as lead_rating, MAX(leads.created_at) as lead_created, leads.status as lead_status
        //                 FROM leads
        //                 GROUP BY customer_id
        //               ) AS leads
        //               ON customers.id = leads.lcid

        //               LEFT JOIN
        //                 (SELECT MAX(id) as order_id, orders.customer_id as ocid, MAX(orders.created_at) as order_created, orders.order_status as order_status FROM orders ' . $orderWhereClause . ' GROUP BY customer_id) as orders
        //                   LEFT JOIN (SELECT order_products.order_id as purchase_order_id, order_products.purchase_status FROM order_products) as order_products
        //                   ON orders.order_id = order_products.purchase_order_id

        //               ' . $join . ' JOIN (SELECT MAX(id) as message_id, customer_id, message, MAX(created_at) as message_created_At FROM chat_messages GROUP BY customer_id ORDER BY created_at DESC) AS chat_messages
        //               ON customers.id = chat_messages.customer_id

        //             ) AS customers
        //             WHERE (deleted_at IS NULL)
        //             ' . $searchWhereClause . '
        //             ' . $orderByClause . '
        //           ) AS customers
        //           ' . $filterWhereClause . ';
        // 					');

        // Assuming you have models for customers, leads, orders, order_products, and chat_messages

        $new_customers = Customer::select(
            'customers.id',
            'customers.name',
            'customers.phone',
            'customers.is_blocked',
            'customers.is_flagged',
            'customers.is_error_flagged',
            'customers.is_priority',
            'customers.deleted_at',
            'leads.id as lead_id',
            'leads.rating as lead_rating',
            'leads.created_at as lead_created',
            'leads.status as lead_status',
            'orders.id as order_id',
            'orders.order_status as order_status',
            'orders.created_at as order_created',
            'order_products.purchase_status',
            'chat_messages.id as message_id',
            'chat_messages.message',
            'chat_messages.status as message_status',
            'chat_messages.sent as message_type',
            'chat_messages.created_at as last_communicated_at'
        )
            ->leftJoin('leads', function ($join) {
                $join->on('customers.id', '=', 'leads.customer_id')
                    ->whereNull('leads.deleted_at');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('customers.id', '=', 'orders.customer_id')
                    ->whereNull('orders.deleted_at');
            })
            ->leftJoin('order_products', 'orders.id', '=', 'order_products.order_id')
            ->leftJoin('chat_messages', function ($join) {
                $join->on('customers.id', '=', 'chat_messages.customer_id')
                    ->whereNull('chat_messages.deleted_at');
            })
            ->whereNull('customers.deleted_at')
            // Add your other where clauses here
            ->orderByRaw($orderByClause)
            ->get();

        $ids_list = [];
        foreach ($new_customers as $customer) {
            $ids_list[] = $customer->id;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($new_customers, $perPage * ($currentPage - 1), $perPage);

        $new_customers = new LengthAwarePaginator($currentItems, count($new_customers), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        dd([
            'customers' => $new_customers,
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($ids_list),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
        ]);

        $mediaTags = config('constants.media_tags'); // Use config variable
        $apiInstance = config('apiwha.instance'); // Use config variable

        return view('customers.index', [
            'customers' => $new_customers,
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($ids_list),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
            'mediaTags' => $mediaTags,
            'apiInstance' => $apiInstance,
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->get('keyword');
        $messages = ChatMessage::where('message', 'LIKE', "%$keyword%")->where('customer_id', '>', 0)->groupBy('customer_id')->with('customer')->select(DB::raw('MAX(id) as message_id, customer_id, message'))->get()->map(function ($item) {
            return [
                'customer_id' => $item->customer_id,
                'customer_name' => $item->customer->name,
                'message_id' => $item->message_id,
                'message' => $item->message,
            ];
        });

        return response()->json($messages);
    }

    public function loadMoreMessages(Request $request): JsonResponse
    {
        $limit = request()->get('limit', 3);

        $customer = Customer::find($request->customer_id);

        $chat_messages = $customer->whatsapps_all()->where('message', '!=', '')->skip(1)->take($limit)->get();

        $messages = [];

        foreach ($chat_messages as $chat_message) {
            $messages[] = $chat_message->message;
        }

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function sendAdvanceLink(Request $request, $id): Response
    {
        $customer = Customer::find($id);

        $options = [
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
        ];

        $proxy = new \SoapClient(config('magentoapi.url'), $options);
        $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

        $errors = 0;

        $productData = [
            'price' => $request->price_inr,
            'special_price' => $request->price_special,
        ];

        try {
            $result = $proxy->catalogProductUpdate($sessionId, 'QUICKADVANCEPAYMENT', $productData);

            $params = [
                'customer_id' => $customer->id,
                'number' => null,
                'message' => 'https://www.sololuxury.co.in/advance-payment-product.html',
                'user_id' => Auth::id(),
                'approve' => 0,
                'status' => 1,
            ];

            ChatMessage::create($params);

            return response('success');
        } catch (Exception $e) {
            $errors++;

            return response($e->getMessage());
        }
    }

    public function initiateFollowup(Request $request, $id): RedirectResponse
    {
        CommunicationHistory::create([
            'model_id' => $id,
            'model_type' => Customer::class,
            'type' => 'initiate-followup',
            'method' => 'whatsapp',
        ]);

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully initiated follow up sequence!');
    }

    public function stopFollowup(Request $request, $id): RedirectResponse
    {
        $histories = CommunicationHistory::where('model_id', $id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

        foreach ($histories as $history) {
            $history->is_stopped = 1;
            $history->save();
        }

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully stopped follow up sequence!');
    }

    public function export()
    {
        $customers = Customer::select(['name', 'phone'])->get()->toArray();

        return Excel::download(new CustomersExport($customers), 'customers.xlsx');
    }

    public function block(Request $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_blocked == 0) {
            $customer->is_blocked = 1;
        } else {
            $customer->is_blocked = 0;
        }

        $customer->save();

        return response()->json(['is_blocked' => $customer->is_blocked]);
    }

    public function flag(Request $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_flagged == 0) {
            $customer->is_flagged = 1;
        } else {
            $customer->is_flagged = 0;
        }

        $customer->save();

        return response()->json(['is_flagged' => $customer->is_flagged]);
    }

    public function addInWhatsappList(Request $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->in_w_list == 0) {
            $customer->in_w_list = 1;
        } else {
            $customer->in_w_list = 0;
        }

        $customer->save();

        return response()->json(['in_w_list' => $customer->in_w_list]);
    }

    public function prioritize(Request $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_priority == 0) {
            $customer->is_priority = 1;
        } else {
            $customer->is_priority = 0;
        }

        $customer->save();

        return response()->json(['is_priority' => $customer->is_priority]);
    }

    public function sendInstock(Request $request): Response
    {
        $customer = Customer::find($request->customer_id);

        $products = Product::where('supplier', 'In-stock')->latest()->get();

        $params = [
            'customer_id' => $customer->id,
            'number' => null,
            'user_id' => Auth::id(),
            'message' => 'In Stock Products',
            'status' => 1,
        ];

        $chat_message = ChatMessage::create($params);

        foreach ($products as $product) {
            $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
        }

        return response('success');
    }

    public function load(Request $request): JsonResponse
    {
        $first_customer = Customer::find($request->first_customer);
        $second_customer = Customer::find($request->second_customer);

        return response()->json([
            'first_customer' => $first_customer,
            'second_customer' => $second_customer,
        ]);
    }

    public function merge(MergeCustomerRequest $request): RedirectResponse
    {

        $first_customer = Customer::find($request->first_customer_id);

        $first_customer->name = $request->name;
        $first_customer->email = $request->email;
        $first_customer->phone = $request->phone;
        $first_customer->whatsapp_number = $request->whatsapp_number;
        $first_customer->instahandler = $request->instahandler;
        $first_customer->rating = $request->rating;
        $first_customer->address = $request->address;
        $first_customer->city = $request->city;
        $first_customer->country = $request->country;
        $first_customer->pincode = $request->pincode;

        $first_customer->save();

        $chat_messages = ChatMessage::where('customer_id', $request->second_customer_id)->get();

        foreach ($chat_messages as $chat) {
            $chat->customer_id = $first_customer->id;
            $chat->save();
        }

        $messages = Message::where('customer_id', $request->second_customer_id)->get();

        foreach ($messages as $message) {
            $message->customer_id = $first_customer->id;
            $message->save();
        }

        $leads = ErpLeads::where('customer_id', $request->second_customer_id)->get();

        foreach ($leads as $lead) {
            $lead->customer_id = $first_customer->id;
            $lead->save();
        }

        $orders = Order::where('customer_id', $request->second_customer_id)->get();

        foreach ($orders as $order) {
            $order->customer_id = $first_customer->id;
            $order->save();
        }

        $instructions = Instruction::where('customer_id', $request->second_customer_id)->get();

        foreach ($instructions as $instruction) {
            $instruction->customer_id = $first_customer->id;
            $instruction->save();
        }

        $second_customer = Customer::find($request->second_customer_id);
        $second_customer->delete();

        return redirect()->route('customer.index');
    }

    public function import(ImportCustomerRequest $request): RedirectResponse
    {

        (new CustomerImport)->queue($request->file('file'));

        return redirect()->back()->with('success', 'Customers are being imported in the background');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $solo_numbers = (new SoloNumbers)->all();

        return view('customers.create', [
            'solo_numbers' => $solo_numbers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {

        $customer = new Customer;
        $customer->store_website_id = ! empty($request->store_website_id) ? $request->store_website_id : '';
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        if (empty($request->whatsapp_number)) {
            //get default whatsapp number for vendor from whatsapp config
            $task_info = WhatsappConfig::select('*')
                ->whereRaw('find_in_set('.self::DEFAULT_FOR.',default_for)')
                ->first();

            $data['whatsapp_number'] = $task_info->number;
        }

        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;

        $customer->save();

        return redirect()->back()->with('success', 'You have successfully added new customer!');
    }

    public function addNote($id, Request $request): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $notes = $customer->notes;
        if (! is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $customer->notes = $notes;
        $customer->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $customer = Customer::with(['call_recordings', 'orders', 'leads', 'facebookMessages'])->where('id', $id)->first();
        $customers = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->get();
        $emails = [];
        $lead_status = (new status)->all();
        $users_array = Helpers::getUserArray(User::all());
        $sortUsers = User::all()->sortBy('name')->pluck('name', 'id');
        $brands = Brand::all()->toArray();
        $reply_categories = ReplyCategory::all();
        $instruction_categories = InstructionCategory::all();
        $instruction_replies = Reply::where('model', 'Instruction')->get();
        $order_status_report = OrderStatuses::all();
        $purchase_status = (new PurchaseStatus)->all();
        $solo_numbers = (new SoloNumbers)->all();
        $api_keys = ApiKey::select(['number'])->get();
        $broadcastsNumbers = collect(DB::select('select number from whatsapp_configs where is_customer_support = 0'))->pluck('number', 'number')->toArray();
        $suppliers = Supplier::select(['id', 'supplier'])
            ->whereRaw('suppliers.id IN (SELECT product_suppliers.supplier_id FROM product_suppliers)')->get();
        $categories = Category::all();
        $storeWebsites = StoreWebsite::all()->pluck('website', 'id')->toArray();

        $facebookMessages = null;
        if (@$customer->facebook_id) {
            $facebookMessages = $customer->facebookMessages()->get();
        }

        $mediaTags = config('constants.media_tags'); // Use config variable
        $apiInstances = WhatsappConfig::getWhatsappConfigs();

        $shortCuts = ['image_shortcut', 'price_shortcut', 'call_shortcut', 'screenshot_shortcut', 'details_shortcut', 'purchase_shortcut'];
        $shortcutArr = [];
        foreach ($shortCuts as $shotcut) {
            $shortcutArr[$shotcut] = Setting::get($shotcut);
        }

        return view('customers.show', [
            'customer' => $customer,
            'customers' => $customers,
            'lead_status' => $lead_status,
            'brands' => $brands,
            'users_array' => $users_array,
            'reply_categories' => $reply_categories,
            'instruction_categories' => $instruction_categories,
            'instruction_replies' => $instruction_replies,
            'order_status_report' => $order_status_report,
            'purchase_status' => $purchase_status,
            'solo_numbers' => $solo_numbers,
            'api_keys' => $api_keys,
            'emails' => $emails,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'facebookMessages' => $facebookMessages,
            'broadcastsNumbers' => $broadcastsNumbers,
            'mediaTags' => $mediaTags,
            'apiInstances' => $apiInstances,
            'storeWebsites' => $storeWebsites,
            'shortCuts' => $shortCuts,
            'sortUsers' => $sortUsers,
        ]);
    }

    public function exportCommunication($id)
    {
        $messages = ChatMessage::where('customer_id', $id)->orderByDesc('created_at')->get();

        $mediaTags = config('constants.media_tags'); // Use config variable
        $html = view('customers.chat_export', compact('messages', 'mediaTags'));

        $pdf = new Dompdf;
        $pdf->loadHtml($html);
        $pdf->render();
        $pdf->stream('orders.pdf');
    }

    public function postShow(Request $request, $id): View
    {
        $customer = Customer::with(['call_recordings', 'orders', 'leads', 'facebookMessages'])->where('id', $id)->first();
        $storeActiveNumber = StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number')
            ->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
            ->where('store_website_twilio_numbers.store_website_id', $customer->store_website_id)
            ->first(); // Get store website active number assigned with customer
        $customers = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->get();

        $searchedMessages = null;
        if ($request->get('sm')) {
            $searchedMessages = ChatMessage::where('customer_id', $id)->where('message', 'LIKE', '%'.$request->get('sm').'%')->get();
        }

        $customer_ids = json_decode($request->customer_ids ?? '[0]');
        $key = array_search($id, $customer_ids);

        if ($key != 0) {
            $previous_customer_id = $customer_ids[$key - 1];
        } else {
            $previous_customer_id = 0;
        }

        if ($key == (count($customer_ids) - 1)) {
            $next_customer_id = 0;
        } else {
            $next_customer_id = $customer_ids[$key + 1];
        }

        $emails = [];
        $lead_status = (new status)->all();
        $users_array = Helpers::getUserArray(User::all());
        $brands = Brand::all()->toArray();
        $reply_categories = ReplyCategory::orderByDesc('id')->get();
        $instruction_categories = InstructionCategory::all();
        $instruction_replies = Reply::where('model', 'Instruction')->get();
        $order_status_report = OrderStatuses::all();
        $purchase_status = (new PurchaseStatus)->all();
        $solo_numbers = (new SoloNumbers)->all();
        $api_keys = ApiKey::select(['number'])->get();
        $suppliers = Supplier::select(['id', 'supplier'])->get();
        $broadcastsNumbers = collect(DB::select('select number from whatsapp_configs where is_customer_support = 0'))->pluck('number', 'number')->toArray();
        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $facebookMessages = null;
        if ($customer->facebook_id) {
            $facebookMessages = $customer->facebookMessages()->get();
        }

        $mediaTags = config('constants.media_tags'); // Use config variable
        $apiInstances = WhatsappConfig::getWhatsappConfigs();

        return view('customers.show', [
            'customer_ids' => json_encode($customer_ids),
            'previous_customer_id' => $previous_customer_id,
            'next_customer_id' => $next_customer_id,
            'customer' => $customer,
            'customers' => $customers,
            'lead_status' => $lead_status,
            'brands' => $brands,
            'users_array' => $users_array,
            'reply_categories' => $reply_categories,
            'instruction_categories' => $instruction_categories,
            'instruction_replies' => $instruction_replies,
            'order_status_report' => $order_status_report,
            'purchase_status' => $purchase_status,
            'solo_numbers' => $solo_numbers,
            'api_keys' => $api_keys,
            'emails' => $emails,
            'category_suggestion' => $category_suggestion,
            'suppliers' => $suppliers,
            'facebookMessages' => $facebookMessages,
            'searchedMessages' => $searchedMessages,
            'broadcastsNumbers' => $broadcastsNumbers,
            'storeActiveNumber' => $storeActiveNumber,
            'mediaTags' => $mediaTags,
            'apiInstances' => $apiInstances,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function emailInbox(Request $request)
    {
        $inbox = 'to';
        if ($request->type != 'inbox') {
            $inbox = 'from';
        }

        $customer = Customer::find($request->customer_id);

        $emails = Email::select()->where($inbox, $customer->email)->get();

        $count = count($emails);
        foreach ($emails as $key => $email) {
            $emails_array[$count + $key]['id'] = $email->id;
            $emails_array[$count + $key]['subject'] = $email->subject;
            $emails_array[$count + $key]['type'] = $email->type;
            $emails_array[$count + $key]['message'] = $email->message;
            $emails_array[$count + $key]['date'] = $email->created_at;
        }
        $emails_array = array_values(Arr::sort($emails_array, function ($value) {
            return $value['date'];
        }));
        $emails_array = array_reverse($emails_array);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);
        $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $view = view('customers.email', [
            'emails' => $emails,
            'type' => $request->type,
        ])->render();

        return response()->json(['emails' => $view]);
    }

    public function emailFetch(Request $request): JsonResponse
    {
        $email = Email::find($request->id);
        
        if ($email->template == 'customer-simple') {
            $content = (new CustomerEmail($email->subject, $email->message, $email->from))->render();
        } else {
            if ($email->template == 'refund-processed') {
                $details = json_decode($email->additional_data, true);

                $content = (new RefundProcessed($details['order_id'], $details['product_names']))->render();
            } else {
                if ($email->template == 'order-confirmation') {
                    $order = Order::find($email->additional_data);

                    $content = (new OrderConfirmation($order))->render();
                } else {
                    if ($email->template == 'advance-receipt') {
                        $order = Order::find($email->additional_data);

                        $content = (new AdvanceReceipt($order))->render();
                    } else {
                        if ($email->template == 'issue-credit') {
                            $customer = Customer::find($email->model_id);

                            $content = (new IssueCredit($customer))->render();
                        } else {
                            $content = 'No Template';
                        }
                    }
                }
            }
        }

        return response()->json(['email' => $content]);
    }

    public function emailSend(EmailSendCustomerRequest $request): RedirectResponse
    {

        $customer = Customer::find($request->customer_id);

        //Store ID Email
        $emailAddressDetails = EmailAddress::select()->where(['store_website_id' => $customer->store_website_id])->first();

        if ($request->order_id != '') {
            $order_data = json_encode(['order_id' => $request->order_id]);
        }

        $emailClass = (new CustomerEmail($request->subject, $request->message, $emailAddressDetails->from_address))->build();

        $email = Email::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'from' => $emailAddressDetails->from_address,
            'to' => $customer->email,
            'subject' => $request->subject,
            'message' => $emailClass->render(),
            'template' => 'customer-simple',
            'additional_data' => isset($order_data) ? $order_data : '',
            'status' => 'pre-send',
            'store_website_id' => null,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully sent an email!');
    }

    public function edit($id): View
    {
        $customer = Customer::find($id);
        $solo_numbers = (new SoloNumbers)->all();

        return view('customers.edit', [
            'customer' => $customer,
            'solo_numbers' => $solo_numbers,
        ]);
    }

    public function updateReminder(Request $request): JsonResponse
    {
        $customer = Customer::find($request->get('customer_id'));
        $customer->frequency = $request->get('frequency');
        $customer->reminder_message = $request->get('message');
        $customer->reminder_from = $request->get('reminder_from', '0000-00-00 00:00');
        $customer->reminder_last_reply = $request->get('reminder_last_reply', 0);
        $customer->save();

        return response()->json([
            'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, int $id): RedirectResponse
    {
        $customer = Customer::find($id);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        if ($request->get('whatsapp_number', false)) {
            $customer->whatsapp_number = $request->whatsapp_number;
        }
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->do_not_disturb = $request->do_not_disturb == 'on' ? 1 : 0;
        $customer->is_blocked = $request->is_blocked == 'on' ? 1 : 0;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;
        $customer->credit = $request->credit;
        $customer->shoe_size = $request->shoe_size;
        $customer->clothing_size = $request->clothing_size;
        $customer->gender = $request->gender;

        $customer->save();

        if ($request->do_not_disturb == 'on') {
            Log::channel('customerDnd')->debug('(Customer ID '.$customer->id.' line '.$customer->name.' '.$customer->number.': Added To DND');
            MessageQueue::where('customer_id', $customer->id)->delete();
        }

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully updated the customer!');
    }

    public function updateNumber(Request $request, $id): Response
    {
        $customer = Customer::find($id);

        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->save();

        return response('success');
    }

    public function updateDnd(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);

        if ($customer->do_not_disturb == 1) {
            $customer->do_not_disturb = 0;
        } else {
            $customer->do_not_disturb = 1;
        }

        $customer->save();

        if ($request->do_not_disturb == 1) {
            Log::channel('customerDnd')->debug('(Customer ID '.$customer->id.' line '.$customer->name.' '.$customer->number.': Added To DND');
            MessageQueue::where('customer_id', $customer->id)->delete();
        }

        return response()->json([
            'do_not_disturb' => $customer->do_not_disturb,
        ]);
    }

    public function updatePhone(UpdatePhoneCustomerRequest $request, $id): Response
    {

        $customer = Customer::find($id);

        $customer->phone = $request->phone;
        $customer->save();

        return response('success');
    }

    public function issueCredit(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        $emailClass = (new ($customer))->build();

        $email = Email::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'from' => $emailClass->fromMailer,
            'to' => $customer->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'issue-credit',
            'additional_data' => '',
            'status' => 'pre-send',
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        $message = "Dear $customer->name, this is to confirm that an amount of Rs. $customer->credit - is credited with us against your previous order. You can use this credit note for reference on your next purchase. Thanks & Regards, Solo Luxury Team";
        $requestData = new Request;
        $requestData->setMethod('POST');
        $requestData->request->add(['customer_id' => $customer->id, 'message' => $message]);

        app(WhatsAppController::class)->sendMessage($requestData, 'customer');

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'issue-credit',
            'method' => 'whatsapp',
        ]);
    }

    public function sendSuggestion(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $params = [
            'customer_id' => $customer->id,
            'number' => $request->number,
            'brands' => '',
            'categories' => '',
            'size' => '',
            'supplier' => '',
        ];

        if ($request->brand[0] != null) {
            $products = Product::whereIn('brand', $request->brand);

            $params['brands'] = json_encode($request->brand);
        }

        if ($request->category[0] != null && $request->category[0] != 1) {
            $categorySel = $request->category;
            $category = Category::whereIn('parent_id', $categorySel)->get()->pluck('id')->toArray();
            $categorySelected = array_merge($categorySel, $category);
            if ($request->brand[0] != null) {
                $products = $products->whereIn('category', $categorySelected);
            } else {
                $products = Product::whereIn('category', $categorySelected);
            }

            $params['categories'] = json_encode($request->category);
        }

        if ($request->size[0] != null) {
            if ($request->brand[0] != null || ($request->category[0] != 1 && $request->category[0] != null)) {
                $products = $products->where(function ($query) use ($request) {
                    foreach ($request->size as $size) {
                        $query->orWhere('size', 'LIKE', "%$size%");
                    }

                    return $query;
                });
            } else {
                $products = Product::where(function ($query) use ($request) {
                    foreach ($request->size as $size) {
                        $query->orWhere('size', 'LIKE', "%$size%");
                    }

                    return $query;
                });
            }

            $params['size'] = json_encode($request->size);
        }

        if ($request->supplier[0] != null) {
            $products = $products->join('product_suppliers as ps', 'ps.sku', 'products.sku');
            $products = $products->whereIn('ps.supplier_id', $request->supplier);
            $products = $products->groupBy('products.id');

            $params['supplier'] = json_encode($request->supplier);
        }

        if ($request->brand[0] == null && ($request->category[0] == 1 || $request->category[0] == null) && $request->size[0] == null && $request->supplier[0] == null) {
            $products = (new Product)->newQuery();
        }

        $price = explode(',', $request->get('price'));

        $products = $products->whereBetween('price_inr_special', [$price[0], $price[1]]);

        $products = $products->where('category', '!=', 1)->select(['products.*'])->latest()->take($request->number)->get();

        if ($customer->suggestion) {
            $suggestion = SuggestedProduct::find($customer->suggestion->id);
            $suggestion->update($params);
        } else {
            $suggestion = SuggestedProduct::create($params);
        }

        if (count($products) > 0) {
            $params = [
                'number' => null,
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
                'message' => 'Suggested images',
                'customer_id' => $customer->id,
            ];

            $count = 0;

            foreach ($products as $product) {
                if (! $product->suggestions->contains($suggestion->id)) {
                    if ($image = $product->getMedia(config('constants.attach_image_tag'))->first()) {
                        if ($count == 0) {
                            $params['status'] = ChatMessage::CHAT_SUGGESTED_IMAGES;
                            $chat_message = ChatMessage::create($params);
                            $suggestion->chat_message_id = $chat_message->id;
                            $suggestion->save();
                        }

                        $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                        $count++;
                    }

                    $product->suggestions()->attach($suggestion->id);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['code' => 200, 'data' => [], 'message' => 'Your records has been update successfully']);
        }

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully created suggested message');
    }

    public function sendScraped(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $products = new Product;
        if ($request->brand[0] != null) {
            $products = $products->whereIn('brand', $request->brand);
        }

        if ($request->category[0] != null && $request->category[0] != 1) {
            $products = $products->whereIn('category', $request->category);
        }
        $total_images = $request->total_images;
        if (! $total_images) {
            $total_images = 20;
        }
        $products = $products->where('is_scraped', 1)->where('is_without_image', 0)->where('category', '!=', 1)->orderByDesc(DB::raw('products.created_at'))->take($total_images)->get();
        if (count($products) > 0) {
            $params = [
                'number' => null,
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
                'message' => 'Suggested images',
                'customer_id' => $customer->id,
            ];

            $count = 0;

            foreach ($products as $product) {
                if ($image = $product->getMedia(config('constants.media_tags'))->first()) {
                    if ($count == 0) {
                        $chat_message = ChatMessage::create($params);
                    }

                    $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                    $count++;
                }
            }
        }

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully created suggested message');
    }

    public function attachAll(Request $request): RedirectResponse
    {
        $data = [];
        $term = $request->input('term');
        $roletype = $request->input('roletype');
        $model_type = $request->input('model_type');

        $data['term'] = $term;
        $data['roletype'] = $roletype;

        $doSelection = $request->input('doSelection');

        if (! empty($doSelection)) {
            $data['doSelection'] = true;
            $data['model_id'] = $request->input('model_id');
            $data['model_type'] = $request->input('model_type');

            $data['selected_products'] = ProductController::getSelectedProducts($data['model_type'], $data['model_id']);
        }

        if ($request->brand[0] != null) {
            $productQuery = (new Product)->newQuery()
                ->latest()->whereIn('brand', $request->brand);
        }

        if ($request->color[0] != null) {
            if ($request->brand[0] != null) {
                $productQuery = $productQuery->whereIn('color', $request->color);
            } else {
                $productQuery = (new Product)->newQuery()
                    ->latest()->whereIn('color', $request->color);
            }
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

            if ($request->brand[0] != null || $request->color[0] != null) {
                $productQuery = $productQuery->whereIn('category', $category_children);
            } else {
                $productQuery = (new Product)->newQuery()
                    ->latest()->whereIn('category', $category_children);
            }
        }

        if ($request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[0];
            $max = $exploded[1];

            if ($min != '0' || $max != '400000') {
                if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1)) {
                    $productQuery = $productQuery->whereBetween('price_inr_special', [$min, $max]);
                } else {
                    $productQuery = (new Product)->newQuery()
                        ->latest()->whereBetween('price_inr_special', [$min, $max]);
                }
            }
        }

        if ($request->supplier[0] != null) {
            $suppliers_list = implode(',', $request->supplier);

            if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1) || $request->price != '0,400000') {
                $productQuery = $productQuery->with('Suppliers')->whereRaw("products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
            } else {
                $productQuery = (new Product)->newQuery()->with('Suppliers')
                    ->latest()->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
            }
        }

        if (trim($request->size) != '') {
            if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1) || $request->price != '0,400000' || $request->supplier[0] != null) {
                $productQuery = $productQuery->whereNotNull('size')->where('size', 'LIKE', "%$request->size%");
            } else {
                $productQuery = (new Product)->newQuery()
                    ->latest()->whereNotNull('size')->where('size', 'LIKE', "%$request->size%");
            }
        }

        if ($request->location[0] != null) {
            if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1) || $request->price != '0,400000' || $request->supplier[0] != null || trim($request->size) != '') {
                $productQuery = $productQuery->whereIn('location', $request->location);
            } else {
                $productQuery = (new Product)->newQuery()
                    ->latest()->whereIn('location', $request->location);
            }

            $data['location'] = $request->location[0];
        }

        if ($request->type[0] != null) {
            if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1) || $request->price != '0,400000' || $request->supplier[0] != null || trim($request->size) != '' || $request->location[0] != null) {
                if (count($request->type) > 1) {
                    $productQuery = $productQuery->where('is_scraped', 1)->orWhere('status', 2);
                } else {
                    if ($request->type[0] == 'scraped') {
                        $productQuery = $productQuery->where('is_scraped', 1);
                    } elseif ($request->type[0] == 'imported') {
                        $productQuery = $productQuery->where('status', 2);
                    } else {
                        $productQuery = $productQuery->where('isUploaded', 1);
                    }
                }
            } else {
                if (count($request->type) > 1) {
                    $productQuery = (new Product)->newQuery()
                        ->latest()->where('is_scraped', 1)->orWhere('status', 2);
                } else {
                    if ($request->type[0] == 'scraped') {
                        $productQuery = (new Product)->newQuery()
                            ->latest()->where('is_scraped', 1);
                    } elseif ($request->type[0] == 'imported') {
                        $productQuery = (new Product)->newQuery()
                            ->latest()->where('status', 2);
                    } else {
                        $productQuery = (new Product)->newQuery()
                            ->latest()->where('isUploaded', 1);
                    }
                }
            }

            $data['type'] = $request->type[0];
        }

        if ($request->date != '') {
            if ($request->brand[0] != null || $request->color[0] != null || ($request->category[0] != null && $request->category[0] != 1) || $request->price != '0,400000' || $request->supplier[0] != null || trim($request->size) != '' || $request->location[0] != null || $request->type[0] != null) {
                if ($request->type[0] != null && $request->type[0] == 'uploaded') {
                    $productQuery = $productQuery->where('is_uploaded_date', 'LIKE', "%$request->date%");
                } else {
                    $productQuery = $productQuery->where('created_at', 'LIKE', "%$request->date%");
                }
            } else {
                $productQuery = (new Product)->newQuery()
                    ->latest()->where('created_at', 'LIKE', "%$request->date%");
            }
        }

        if ($request->quick_product === 'true') {
            $productQuery = (new Product)->newQuery()
                ->latest()->where('quick_product', 1);
        }

        if (trim($term) != '') {
            $productQuery = (new Product)->newQuery()
                ->latest()
                ->orWhere('sku', 'LIKE', "%$term%")
                ->orWhere('id', 'LIKE', "%$term%");

            if ($term == -1) {
                $productQuery = $productQuery->orWhere('isApproved', -1);
            }

            if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                $productQuery = $productQuery->orWhere('brand', 'LIKE', "%$brand_id%");
            }

            if ($category = Category::where('title', 'LIKE', "%$term%")->first()) {
                $category_id = $category = Category::where('title', 'LIKE', "%$term%")->first()->id;
                $productQuery = $productQuery->orWhere('category', CategoryController::getCategoryIdByName($term));
            }

            if (! empty($productQuery->getIDCaseInsensitive($term))) {
                $productQuery = $productQuery->orWhere('stage', $stage->getIDCaseInsensitive($term));
            }

            if (! (Auth::user()->hasRole(['Admin', 'Supervisors']))) {
                $productQuery = $productQuery->where('stage', '>=', $stage->get($roletype));
            }

            if ($roletype != 'Selection' && $roletype != 'Searcher') {
                $productQuery = $productQuery->whereNull('dnf');
            }
        } else {
            if ($request->brand[0] == null && $request->color[0] == null && ($request->category[0] == null || $request->category[0] == 1) && $request->price == '0,400000' && $request->supplier[0] == null && trim($request->size) == '' && $request->date == '' && $request->type == null && $request->location[0] == null) {
                $productQuery = (new Product)->newQuery()->latest();
            }
        }

        if ($request->ids[0] != null) {
            $productQuery = (new Product)->newQuery()
                ->latest()->whereIn('id', $request->ids);
        }

        $data['products'] = $productQuery->select(['id', 'sku', 'size', 'price_inr_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])->get();

        $params = [
            'user_id' => Auth::id(),
            'number' => null,
            'status' => 1,
            'customer_id' => $request->customer_id,
        ];

        $chat_message = ChatMessage::create($params);

        $mediaList = [];

        foreach ($data['products'] as $product) {
            if ($product->hasMedia(config('constants.media_tags'))) {
                $mediaList[] = $product->getMedia(config('constants.media_tags'));
            }
        }

        foreach (array_unique($mediaList) as $list) {
            try {
                $chat_message->attachMedia($list, config('constants.media_tags'));
            } catch (Exception $e) {
                return response()->json(['code' => 400,'error' => 'Opps! Something went wrong, Please try again.'], 400);
            }
        }

        return redirect()->route('customer.show', $request->customer_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $customer = Customer::find($id);

        if (count($customer->leads) > 0 || count($customer->orders) > 0) {
            return redirect()->route('customer.index')->with('warning', 'You have related leads or orders to this customer');
        }

        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'You have successfully deleted a customer');
    }

    /**
     * using for creating file and save into the on given folder path
     */
    public function testImage()
    {
        $path = request()->get('path');
        $text = request()->get('text');
        $color = request()->get('color', 'FFF');
        $fontSize = request()->get('size', 42);

        $img = \IImage::make(public_path($path));
        // use callback to define details
        $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
            $font->file(public_path('fonts/Arial.ttf'));
            $font->size($fontSize);
            $font->color('#'.$color);
            $font->align('top');
        });

        return $img->response();
    }

    public function broadcast()
    {
        $customerId = request()->get('customer_id', 0);

        $pendingBroadcast = MessageQueue::where('customer_id', $customerId)
            ->where('sent', 0)->orderBy('group_id')->groupBy('group_id')->select('group_id as id')->get()->toArray();
        // last two
        $lastBroadcast = MessageQueue::where('customer_id', $customerId)
            ->where('sent', 1)->orderByDesc('group_id')->groupBy('group_id')->limit(2)->select('group_id as id')->get()->toArray();

        $allRequest = array_merge($pendingBroadcast, $lastBroadcast);

        if (! empty($allRequest)) {
            usort($allRequest, function ($a, $b) {
                $a = $a['id'];
                $b = $b['id'];

                if ($a == $b) {
                    return 0;
                }

                return ($a < $b) ? -1 : 1;
            });
        }

        return response()->json(['code' => 1, 'data' => $allRequest]);
    }

    public function broadcastSendPrice(): JsonResponse
    {
        $broadcastId = request()->get('broadcast_id', 0);
        $customerId = request()->get('customer_id', 0);
        $productsToBeRun = explode(',', request()->get('product_to_be_run', ''));

        $products = [];
        if (! empty(array_filter($productsToBeRun))) {
            foreach ($productsToBeRun as $prd) {
                if (is_numeric($prd)) {
                    $products[] = $prd;
                }
            }
        }

        $customer = Customer::where('id', $customerId)->first();

        if ($customer && $customer->do_not_disturb == 0) {
            $this->dispatchBroadSendPrice($customer, array_unique($products));
        }

        return response()->json(['code' => 1, 'message' => 'Broadcast run successfully']);
    }

    public function dispatchBroadSendPrice($customer, $product_ids, $dimention = false)
    {
        if (! empty($customer) && is_numeric($customer->phone)) {
            Log::info('Customer with phone found for customer id : '.$customer->id.' and product ids '.json_encode($product_ids));
            if (! empty(array_filter($product_ids))) {
                foreach ($product_ids as $pid) {
                    $product = Product::where('id', $pid)->first();

                    $quick_lead = ErpLeads::create([
                        'customer_id' => $customer->id,
                        'lead_status_id' => 3,
                        'store_website_id' => 15,
                        'product_id' => $pid,
                        'brand_id' => $product ? $product->brand : null,
                        'category_id' => $product ? $product->category : null,
                        'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : null,
                        'color' => $customer->color,
                        'size' => $customer->size,
                        'type' => 'dispatch-send-price',
                        'created_at' => Carbon::now(),
                    ]);
                }

                $requestData = new Request;
                $requestData->setMethod('POST');
                if ($dimention) {
                    $requestData->request->add(['customer_id' => $customer->id, 'dimension' => true, 'lead_id' => $quick_lead->id, 'selected_product' => $product_ids]);
                } else {
                    $requestData->request->add(['customer_id' => $customer->id, 'lead_id' => $quick_lead->id, 'selected_product' => $product_ids]);
                }

                $res = app(LeadsController::class)->sendPrices($requestData, new GuzzleClient);

                return true;
            }

            return false;
        }
    }

    public function broadcastDetails(): JsonResponse
    {
        $broadcastId = request()->get('broadcast_id', 0);
        $customerId = request()->get('customer_id', 0);

        $messages = MessageQueue::where('group_id', $broadcastId)->where('customer_id', $customerId)->get();

        $response = [];

        if (! $messages->isEmpty()) {
            foreach ($messages as $message) {
                $response[] = $message->getImagesWithProducts();
            }
        }

        return response()->json(['code' => 1, 'data' => $response]);
    }

    /**
     * Change in whatsapp no
     */
    public function changeWhatsappNo(): JsonResponse
    {
        $customerId = request()->get('customer_id', 0);
        $whatsappNo = request()->get('number', null);
        $type = request()->get('type', 'whatsapp_number');

        if ($customerId > 0) {
            // find the record from customer table
            $customer = Customer::where('id', $customerId)->first();

            if ($customer) {
                // assing nummbers
                $oldNumber = $customer->whatsapp_number;
                if ($type == 'broadcast_number') {
                    $customer->broadcast_number = $whatsappNo;
                } else {
                    $customer->whatsapp_number = $whatsappNo;
                }

                if ($customer->save()) {
                    if ($type == 'whatsapp_number') {
                        // update into whatsapp history table
                        $wHistory = new HistoryWhatsappNumber;
                        $wHistory->date_time = date('Y-m-d H:i:s');
                        $wHistory->object = Customer::class;
                        $wHistory->object_id = $customerId;
                        $wHistory->old_number = $oldNumber;
                        $wHistory->new_number = $whatsappNo;
                        $wHistory->save();
                    }
                }
            }
        }

        return response()->json(['code' => 1, 'message' => 'Number updated successfully']);
    }

    public function sendContactDetails(): JsonResponse
    {
        $userID = request()->get('user_id', 0);
        $customerID = request()->get('customer_id', 0);

        $user = User::where('id', $userID)->first();
        $customer = Customer::where('id', $customerID)->first();

        // if found customer and  user
        if ($user && $customer) {
            $data = [
                'Customer details:',
                "$customer->name",
                "$customer->phone",
                "$customer->email",
                "$customer->address",
                "$customer->city",
                "$customer->country",
                "$customer->pincode",
            ];

            $messageData = implode("\n", $data);

            $params['erp_user'] = $user->id;
            $params['approved'] = 1;
            $params['message'] = $messageData;
            $params['status'] = 2;

            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $messageData);

            $chat_message = ChatMessage::create($params);
        }

        return response()->json(['code' => 1, 'message' => 'done']);
    }

    public function addReplyCategory(AddReplyCategoryCustomerRequest $request): JsonResponse
    {

        $category = new ReplyCategory;
        $category->name = $request->name;
        if (! empty($request->quickCategoryId)) {
            $category->parent_id = $request->quickCategoryId;
        }
        $category->save();

        return response()->json(['code' => 1, 'data' => $category]);
    }

    public function destroyReplyCategory(DestroyReplyCategoryCustomerRequest $request): JsonResponse
    {

        Reply::where('category_id', $request->get('id'))->delete();
        ReplyCategory::where('id', $request->get('id'))->delete();

        return response()->json(['code' => 1, 'message' => 'Deleted successfully']);
    }

    public function downloadContactDetails()
    {
        $userID = request()->get('user_id', 0);
        $customerID = request()->get('customer_id', 0);

        $user = User::where('id', $userID)->first();
        $customer = Customer::where('id', $customerID)->first();

        // if found customer and  user
        if ($user && $customer) {
            // load the view for pdf and after that load that into dompdf instance, and then stream (download) the pdf
            $html = view('customers.customer_pdf', compact('customer'));

            $pdf = new Dompdf;
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream('orders.pdf');
        }
    }

    public function downloadContactDetailsPdf($id)
    {
        $customerID = request()->get('id', 0);

        $customer = Customer::where('id', $id)->first();

        // if found customer and  user
        if ($customer) {
            // load the view for pdf and after that load that into dompdf instance, and then stream (download) the pdf
            $html = view('customers.customer_pdf', compact('customer'));

            $pdf = new Dompdf;
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream($id.'-label.pdf');
        }
    }

    public function languageTranslate(Request $request): JsonResponse
    {
        if ($request->language == '') {
            $language = 'en';
        } else {
            $language = $request->language;
        }

        $customer = Customer::find($request->id);
        $customer->language = $language;
        $customer->save();

        return response()->json(['success' => 'Customer language updated'], 200);
    }

    public function getLanguage(Request $request): JsonResponse
    {
        $customerDetails = Customer::find($request->id);

        return response()->json(['data' => $customerDetails]);
    }

    public function updateField(Request $request): JsonResponse
    {
        $field = $request->get('field');
        $value = $request->get('value');

        $customerId = $request->get('customer_id');

        if (! empty($customerId)) {
            $customer = Customer::find($customerId);
            if (! empty($customer)) {
                $customer->{$field} = $value;
                $customer->save();
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => $field.' updated successfully']);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Sorry , no customer found']);
    }

    public function createKyc(Request $request): JsonResponse
    {
        $customer_id = $request->get('customer_id');
        $media_id = $request->get('media_id');

        if (empty($customer_id)) {
            return response()->json(['code' => 500, 'message' => 'Customer id is required']);
        }

        if (empty($media_id)) {
            return response()->json(['code' => 500, 'message' => 'Media id is required']);
        }

        $media = PlunkMediable::find($media_id);
        if (! empty($media)) {
            $kycDoc = new CustomerKycDocument;
            $kycDoc->customer_id = $customer_id;
            $kycDoc->url = getMediaUrl($media);
            $kycDoc->path = $media->getAbsolutePath();
            $kycDoc->type = 1;
            $kycDoc->save();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Kyc document added successfully']);
        }

        return response()->json(['code' => 500, 'message' => 'Ooops, something went wrong']);
    }

    public function quickcustomer(Request $request): View
    {
        $results = $this->getCustomersIndex($request);
        $nextActionArr = CustomerNextAction::get();
        $type = @$request->type;

        $customerMessage = [];
        foreach ($results[0] as $result) {
            $customerMessage[$result->id] = DB::table(function ($query) use ($result) {
                $query->select('id', 'message', 'customer_id')
                    ->from('chat_messages')
                    ->where('customer_id', '=', $result->id)
                    ->orderByDesc('id')
                    ->limit(1);
            })->get();
        }

        return view('customers.quickcustomer', ['customers' => $results[0], 'nextActionArr' => $nextActionArr, 'type' => $type, 'customerMessage' => $customerMessage]);
    }

    //START - Purpose : Add Customer Data - DEVTASK-19932
    public function add_customer_data(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'website' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->email) {
            $email = $request->email;
            $website = $request->website;

            $website_data = StoreWebsite::where('website', $website)->first();

            if ($website_data) {
                $website_id = $website_data->id;
            } else {
                $website_id = '';
            }

            if ($email != '' && $website_id != '') {
                $find_customer = Customer::where('email', $email)->where('store_website_id', $website_id)->first();

                if ($find_customer) {
                    foreach ($request->post() as $key => $value) {
                        if ($value['entity_id'] != '') {
                            $check_record = CustomerAddressData::where('customer_id', $find_customer->id)->where('entity_id', $value['entity_id'])->first();
                        }

                        if ($check_record) {
                            if (isset($value['is_deleted']) && $value['is_deleted'] == 1) {
                                CustomerAddressData::where('customer_id', $find_customer->id)
                                    ->where('entity_id', $value['entity_id'])
                                    ->delete();
                            } else {
                                CustomerAddressData::where('customer_id', $find_customer->id)
                                    ->where('entity_id', $value['entity_id'])
                                    ->update(
                                        [
                                            'parent_id' => ($value['parent_id'] ?? ''),
                                            'address_type' => ($value['address_type'] ?? ''),
                                            'region' => ($value['region'] ?? ''),
                                            'region_id' => ($value['region_id'] ?? ''),
                                            'postcode' => ($value['postcode'] ?? ''),
                                            'firstname' => ($value['firstname'] ?? ''),
                                            'middlename' => ($value['middlename'] ?? ''),
                                            'company' => ($value['company'] ?? ''),
                                            'country_id' => ($value['country_id'] ?? ''),
                                            'telephone' => ($value['telephone'] ?? ''),
                                            'prefix' => ($value['prefix'] ?? ''),
                                            'street' => ($value['street'] ?? ''),
                                            'updated_at' => \Carbon\Carbon::now(),
                                        ]
                                    );
                            }
                        } else {
                            $params[] = [
                                'customer_id' => $find_customer->id,
                                'entity_id' => ($value['entity_id'] ?? ''),
                                'parent_id' => ($value['parent_id'] ?? ''),
                                'address_type' => ($value['address_type'] ?? ''),
                                'region' => ($value['region'] ?? ''),
                                'region_id' => ($value['region_id'] ?? ''),
                                'postcode' => ($value['postcode'] ?? ''),
                                'firstname' => ($value['firstname'] ?? ''),
                                'middlename' => ($value['middlename'] ?? ''),
                                'company' => ($value['company'] ?? ''),
                                'country_id' => ($value['country_id'] ?? ''),
                                'telephone' => ($value['telephone'] ?? ''),
                                'prefix' => ($value['prefix'] ?? ''),
                                'street' => ($value['street'] ?? ''),
                                'created_at' => \Carbon\Carbon::now(),
                                'updated_at' => \Carbon\Carbon::now(),

                            ];
                        }
                    }

                    if (! empty($params)) {
                        CustomerAddressData::insert($params);
                    }

                    return response()->json(['code' => 200]);
                } else {
                    return response()->json(['code' => 404, 'message' => 'Not Exist!']);
                }
            } else {
                return response()->json(['code' => 404, 'message' => 'Website Not Found!']);
            }
        }

        // If email is not provided, return an error response
        return response()->json(['code' => 400, 'message' => 'Email is required!']);
    }

    //END - DEVTASK-19932

    public function customerinfo(Request $request): JsonResponse
    {
        $customer = Customer::leftjoin('store_websites as sw', 'sw.id', 'customers.store_website_id')->where('customers.id', $request->customer_id)->select('customers.*', 'sw.website')->first();

        return response()->json(['status' => 200, 'data' => $customer]);
    }

    public function fetchCreditBalance(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform_id' => 'required',
            'website' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $platform_id = $request->platform_id;
        $website = $request->website;
        $store_website = StoreWebsite::where('website', 'like', $website)->first();
        if ($store_website) {
            $store_website_id = $store_website->id;
            $customer = Customer::where('store_website_id', $store_website_id)->where('platform_id', $platform_id)->first();
            if ($customer) {
                $message = $this->generate_erp_response('credit_fetch.success', $store_website_id, $default = 'Credit Fetched Successfully', request('lang_code'));

                return response()->json(['message' => $message, 'code' => 200, 'status' => 'success', 'data' => ['credit_balance' => $customer->credit, 'currency' => $customer->currency]]);
            } else {
                $message = $this->generate_erp_response('credit_fetch.customer.failed', $store_website_id, $default = 'Customer not found', request('lang_code'));

                return response()->json(['message' => $message, 'code' => 500, 'status' => 'failed']);
            }
        } else {
            $message = $this->generate_erp_response('credit_fetch.website.failed', $store_website_id, $default = 'Website not found', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 500, 'status' => 'failed']);
        }
    }

    public function deductCredit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform_id' => 'required',
            'website' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $platform_id = $request->platform_id;
        $website = $request->website;
        $balance = $request->amount;

        $store_website = StoreWebsite::where('website', 'like', $website)->first();
        if ($store_website) {
            $store_website_id = $store_website->id;
        } else {
            $message = $this->generate_erp_response('credit_deduct.website.failed', $store_website_id, $default = 'Website Not found', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 500, 'status' => 'failure']);
        }
        $customer = Customer::where('store_website_id', $store_website->id)->where('platform_id', $platform_id)->first();
        if ($customer) {
            $customer_id = $customer->id;
            $totalCredit = $customer->credit;
            if ($customer->credit > $balance) {
                $calc_credit = $customer->credit - $balance;
                $customer->credit = $calc_credit;

                CreditHistory::create(
                    [
                        'customer_id' => $customer_id,
                        'model_id' => $customer_id,
                        'model_type' => Customer::class,
                        'used_credit' => (float) $totalCredit - $calc_credit,
                        'used_in' => 'MANUAL',
                        'type' => 'MINUS',
                    ]
                );
                $customer->save();
                $message = $this->generate_erp_response('credit_deduct.success', $store_website_id, $default = 'Credit deducted successfully', request('lang_code'));

                return response()->json(['message' => $message, 'code' => 200, 'status' => 'success']);
            } else {
                $toAdd = $balance - $customer->credit;
                $message = $this->generate_erp_response('credit_deduct.insufficient_balance', $store_website_id, $default = 'You do not have sufficient credits, Please add '.$toAdd.' to proceed.', request('lang_code'));

                return response()->json(['message' => $message, 'code' => 500, 'status' => 'failure']);
            }
        } else {
            $message = $this->generate_erp_response('credit_deduct.customer.failed', $store_website_id, $default = 'Customer not found.', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 500, 'status' => 'failure']);
        }
    }

    public function storeCredit(Request $request): View
    {
        $customers_all = Customer::leftjoin('store_websites', 'customers.store_website_id', 'store_websites.id')
            ->leftjoin('credit_history', 'customers.id', 'credit_history.customer_id');
        $customers_all->select('customers.*', 'store_websites.title', DB::raw('( select created_at from credit_history where credit_history.customer_id = customers.id ORDER BY id DESC LIMIT 0,1) as date'));
        $customers_all->latest('date')->groupBy('customers.id')->orderByDesc('date');

        if ($request->name != '') {
            $customers_all->where('name', 'Like', '%'.$request->name.'%');
        }

        if ($request->email != '') {
            $customers_all->where('email', 'Like', '%'.$request->email.'%');
        }

        if ($request->phone != '') {
            $customers_all->where('phone', 'Like', '%'.$request->phone.'%');
        }

        if ($request->store_website != '') {
            $customers_all->where('store_website_id', $request->store_website);
        }
        $customers = $customers_all->get();
        $customers_all = $customers_all->paginate(Setting::get('pagination'));
        $store_website = StoreWebsite::all();
        $users = Customer::get();
        if ($request->ajax()) {
            return view('livechat.store_credit_ajax', [
                'customers_all' => $customers_all,
                'store_website' => $store_website,
                'customers' => $customers,
                'users' => $users,
            ]);
        } else {
            return view('livechat.store_credit', [
                'customers_all' => $customers_all,
                'store_website' => $store_website,
                'customers' => $customers,
                'users' => $users,
            ]);
        }
    }

    public function getWebsiteCustomers(Request $request)
    {
        $storeWebsiteId = $request->store_website_id;

        $customerQuery = Customer::query();

        if ($storeWebsiteId == 'Others') {
            $customerQuery = $customerQuery->whereNull('store_website_id')->orWhere('store_website_id', '');
        } else {
            $customerQuery = $customerQuery->where('store_website_id', $storeWebsiteId);
        }

        $customers = $customerQuery->get();

        return $customers;
    }

    public function creditEmailLog(Request $request): JsonResponse
    {
        $creditEmailLog = CreditEmailLog::where('customer_id', $request->cust_id)->get();

        if (count($creditEmailLog) > 0) {
            $html = '';
            foreach ($creditEmailLog as $log) {
                $html .= '<tr>';
                $html .= '<td>'.$log->id.'</td>';
                $html .= '<td>'.$log->from_email.'</td>';
                $html .= '<td>'.$log->to_email.'</td>';
                $html .= '<td>'.$log->created_at.'</td>';
                $html .= '</tr>';
            }

            return response()->json(['msg' => 'Listed successfully', 'code' => 200, 'data' => $html]);
        } else {
            return response()->json(['msg' => 'Record not found', 'code' => 500, 'data' => '']);
        }
    }

    public function accounts(Request $request): View
    {
        $customers_all = Customer::where('store_website_id', '>', 0);
        $customers_all->select('customers.*', 'store_websites.title');
        $customers_all->join('store_websites', 'store_websites.id', 'customers.store_website_id');

        if ($request->from_date != '') {
            $customers_all->whereBetween('customers.created_at', [$request->from_date, $request->to_date]);
        }

        if ($request->name != '') {
            $customers_all->whereIn('name', $request->name);
        }

        if ($request->email != '') {
            $customers_all->whereIn('email', $request->email);
        }

        if ($request->phone != '') {
            $customers_all->whereIn('phone', $request->phone);
        }

        if ($request->store_website != '') {
            $customers_all->whereIn('store_website_id', $request->store_website);
        }

        $customers_all->orderByDesc('created_at');
        $total = $customers_all->count();
        $customers_all = $customers_all->paginate(Setting::get('pagination'));
        $store_website = StoreWebsite::all();
        $customers_name = Customer::select('name')->distinct()->where('store_website_id', '>', 0)->get();
        $customers_phone = Customer::select('phone')->distinct()->where('store_website_id', '>', 0)->get();
        $customers_email = Customer::select('email')->distinct()->where('store_website_id', '>', 0)->get();

        if ($request->ajax()) {
            return view('customers.account_ajax', [
                'customers_all' => $customers_all,

            ]);
        } else {
            return view('customers.account', [
                'customers_all' => $customers_all,
                'total' => $total,
                'store_website' => $store_website,
                'customers_name' => $customers_name,
                'customers_phone' => $customers_phone,
                'customers_email' => $customers_email,

            ]);
        }
    }

    public function customerUpdate(Request $request): JsonResponse
    {
        $input = $request->input();
        unset($input['_token']);
        $details = Customer::where('id', $input['customer_id'])->select('id as customer_id', 'name', 'email', 'phone', 'address', 'city', 'country', 'pincode')->first()->toArray();
        CustomerDetailHistory::create($details);
        $customerId = $input['customer_id'];
        unset($input['customer_id']);
        Customer::where('id', $customerId)->update($input);

        return response()->json(['message' => 'Details updated', 'code' => 200, 'status' => 'success']);
    }

    public function customerUpdateHistory($customerId): JsonResponse
    {
        $history = CustomerDetailHistory::where('customer_id', $customerId)->get();
        $records = '';
        foreach ($history as $c) {
            $records .= '<tr>
              <td>'.$c->id.'</td>
              <td>'.$c->name.'</td>
              <td>'.$c->email.'</td>
              <td>'.$c->phone.'</td>
              <td>'.$c->address.'</td>
              <td>'.$c->city.'</td>
              <td>'.$c->pincode.'</td>
              <td>'.$c->country.'</td> </tr>';
        }

        return response()->json(['records' => $records, 'code' => 200, 'status' => 'success']);
    }

    public function addCredit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform_id' => 'required',
            'website' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $platform_id = $request->platform_id;
        $website = $request->website;
        $credit = $request->amount;
        $store_website = StoreWebsite::where('website', 'like', $website)->first();
        if ($store_website) {
            $store_website_id = $store_website->id;
        } else {
            $message = $this->generate_erp_response('credit_add.website.failed', $store_website_id, $default = 'Website Not found', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 500, 'status' => 'failure']);
        }
        $customer = Customer::where('store_website_id', $store_website->id)->where('platform_id', $platform_id)->first();
        if ($customer) {
            $customer_id = $customer->id;
            $totalCredit = $customer->credit;
            if ($credit > 0) {
                $calc_credit = $customer->credit + $credit;
                $customer->credit = $calc_credit;

                CreditHistory::create(
                    [
                        'customer_id' => $customer_id,
                        'model_id' => $customer_id,
                        'model_type' => Customer::class,
                        'used_credit' => (float) $credit,
                        'used_in' => 'MANUAL',
                        'type' => 'PLUS',
                    ]
                );
                $customer->save();
            }
            $message = $this->generate_erp_response('credit_add.success', $store_website_id, $default = 'Credit added successfully', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 200, 'status' => 'success']);
        } else {
            $message = $this->generate_erp_response('credit_add.customer.failed', $store_website_id, $default = 'Customer not found.', request('lang_code'));

            return response()->json(['message' => $message, 'code' => 500, 'status' => 'failure']);
        }
    }

    /**
     * This function is use for get all proirity data
     *
     * @param [int] $id
     */
    public function customerPriorityPoints(Request $request): View
    {
        $custPriority = CustomerPriorityPoint::leftjoin('store_websites', 'store_websites.id', 'customer_priority_points.store_website_id')->get(
            [
                'customer_priority_points.store_website_id',
                'customer_priority_points.website_base_priority',
                'customer_priority_points.lead_points',
                'customer_priority_points.order_points',
                'customer_priority_points.refund_points',
                'customer_priority_points.ticket_points',
                'customer_priority_points.return_points',
                'store_websites.website',
            ]
        );

        $storeWebsite = StoreWebsite::all();

        return view('customers.customer_priority_point', compact('storeWebsite', 'custPriority'));
    }

    /**
     * This function is use for get proirity data
     *
     * @param [int] $id
     * @param  mixed  $webSiteId
     */
    public function getCustomerPriorityPoints($webSiteId): JsonResponse
    {
        try {
            $custPriority = CustomerPriorityPoint::where('store_website_id', $webSiteId)->get();
            if ($custPriority) {
                return response()->json(['code' => 200, 'data' => compact('custPriority'), 'message' => 'Priority listed successfully']);
            }

            return response()->json(['code' => 500, 'data' => [], 'message' => 'Sorry there is no Website exist']);
        } catch (Exception $exception) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $exception->getMessage()]);
        }
    }

    /**
     * This function is use for save proirity data
     */
    public function addCustomerPriorityPoints(Request $request): JsonResponse
    {
        $custPri = CustomerPriorityPoint::updateOrCreate(
            [
                'store_website_id' => $request->get('store_website_id'),
            ],
            [
                'website_base_priority' => $request->get('website_base_priority'),
                'store_website_id' => $request->get('store_website_id'),
                'lead_points' => $request->get('lead_points'),
                'refund_points' => $request->get('refund_points'),
                'order_points' => $request->get('order_points'),
                'ticket_points' => $request->get('ticket_points'),
                'return_points' => $request->get('return_points'),
            ]
        );

        return response()->json(['message' => 'Record added successfully', 'code' => 200, 'data' => $custPri, 'status' => 'success']);
    }

    /**
     * This function is use for get all proirity Range data
     *
     * @param [int] $id
     */
    public function getCustomerPriorityRangePoints(Request $request): View
    {
        $custRangePoint = CustomerPriorityRangePoint::leftjoin('store_websites', 'store_websites.id', 'customer_priority_range_points.store_website_id')
            ->leftjoin('twilio_priorities', 'twilio_priorities.id', 'customer_priority_range_points.twilio_priority_id')
            ->where('customer_priority_range_points.deleted_at', '=', null)
            ->get(
                [
                    'customer_priority_range_points.id',
                    'customer_priority_range_points.store_website_id',
                    'customer_priority_range_points.twilio_priority_id',
                    'customer_priority_range_points.min_point',
                    'customer_priority_range_points.max_point',
                    'customer_priority_range_points.range_name',
                    'customer_priority_range_points.created_at',
                    'store_websites.website',
                    'twilio_priorities.priority_name',
                ]
            );

        $storeWebsite = StoreWebsite::all();

        return view('customers.customer_priority_range_point', compact('storeWebsite', 'custRangePoint'));
    }

    /**
     * This function is use for get all proirity Range data
     *
     * @param [int] $id
     */
    public function getSelectCustomerPriorityRangePoints(Request $request, $id): JsonResponse
    {
        $custRangePoint = CustomerPriorityRangePoint::select([
            'customer_priority_range_points.id',
            'customer_priority_range_points.store_website_id',
            'customer_priority_range_points.twilio_priority_id',
            'customer_priority_range_points.min_point',
            'customer_priority_range_points.max_point',
            'customer_priority_range_points.created_at',
            'store_websites.website',
            'twilio_priorities.priority_name',
        ])->leftjoin('store_websites', 'store_websites.id', 'customer_priority_range_points.store_website_id')
            ->leftjoin('twilio_priorities', 'twilio_priorities.id', 'customer_priority_range_points.twilio_priority_id')
            ->where('customer_priority_range_points.deleted_at', '=', null)
            ->where('customer_priority_range_points.id', $id)
            ->first();

        $storeWebsite = StoreWebsite::all();
        $twilioPriority = TwilioPriority::where('account_id', function ($query) use ($custRangePoint) {
            $query->select('twilio_credentials_id')
                ->from('store_website_twilio_numbers')
                ->where('store_website_twilio_numbers.store_website_id', $custRangePoint->store_website_id);
        })->get();
        $twilioPriority = $twilioPriority->toArray();

        return response()->json(['message' => 'Record Listed successfully', 'code' => 200, 'data' => compact('custRangePoint', 'storeWebsite', 'twilioPriority'), 'status' => 'success']);
    }

    /**
     * This function is use for get all proirity Range data
     *
     * @param [int] $id
     */
    public function selectCustomerPriorityRangePoints(Request $request, $id): JsonResponse
    {
        $twilioPriority = TwilioPriority::where('account_id', function ($query) use ($id) {
            $query->select('twilio_credentials_id')
                ->from('store_website_twilio_numbers')
                ->where('store_website_id', $id);
        })->get();

        return response()->json(['message' => 'Record Listed successfully', 'code' => 200, 'data' => $twilioPriority->toArray(), 'status' => 'success']);
    }

    /**
     * This function is use for save proirity range data
     */
    public function addCustomerPriorityRangePoints(Request $request): JsonResponse
    {
        $custPri = CustomerPriorityRangePoint::updateOrCreate(
            [
                'twilio_priority_id' => $request->get('twilio_priority_id'),
                'store_website_id' => $request->get('store_website_id'),
            ],
            [
                'twilio_priority_id' => $request->get('twilio_priority_id'),
                'store_website_id' => $request->get('store_website_id'),
                'min_point' => $request->get('min_point'),
                'max_point' => $request->get('max_point'),
                'deleted_at' => null,
            ]
        );

        return response()->json(['message' => 'Record added successfully', 'code' => 200, 'data' => $custPri, 'status' => 'success']);
    }

    /**
     * This function is use for save proirity range delete data
     */
    public function deleteCustomerPriorityRangePoints(Request $request): RedirectResponse
    {
        $custPri = CustomerPriorityRangePoint::where('id', '=', $request->id)->update([
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->withSuccess('You have successfully Deleted');
    }

    public function customerName(request $request)
    {
        $id = $request->input('id');
        $name = Customer::where('id', $id)->value('name');
        $htmlContent = '<tr><td>'.$name.'</td></tr>';

        return $htmlContent;
    }
}
