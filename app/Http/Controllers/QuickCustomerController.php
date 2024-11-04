<?php

namespace App\Http\Controllers;
use App\User;
use App\TicketStatuses;
use App\StoreWebsite;
use App\ReplyCategory;
use App\QuickSellGroup;
use App\Category;
use App\Brand;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Customer;
use Illuminate\Http\Request;
use App\ReadOnly\SoloNumbers;
use App\Models\CustomerNextAction;

class QuickCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $title          = 'Quick Customer';
        $nextActionArr  = CustomerNextAction::get();
        $nextActionList = CustomerNextAction::pluck('name', 'id')->toArray();

        $reply_categories    = ReplyCategory::orderByDesc('id')->get();
        $groups              = QuickSellGroup::select('id', 'name', 'group')->orderByDesc('id')->get();
        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])->renderAsDropdown();
        $brands              = Brand::all()->toArray();
        $solo_numbers        = (new SoloNumbers)->all();
        $storeWebsites       = StoreWebsite::all()->pluck('website', 'id')->toArray();
        $assigned_to         = User::with('roles')->get();
        $statuses            = TicketStatuses::all();

        $request->merge(['do_not_disturb' => '0']);

        return view('quick-customer.index', compact('storeWebsites', 'solo_numbers', 'title', 'category_suggestion', 'brands', 'nextActionArr', 'reply_categories', 'groups', 'nextActionList', 'assigned_to', 'statuses'));
    }

    public function records(Request $request): JsonResponse
    {
        $type              = $request->get('type', 'last_received');
        $chatMessagesWhere = 'WHERE status not in (7,8,9,10)';

        $customer = Customer::query();

        if ($type == 'unread') {
            $customer = $customer->join('chat_messages_quick_datas as cmqs', function ($q) {
                $q->on('cmqs.model_id', 'customers.id')->where('cmqs.model', Customer::class);
            });
            $customer = $customer->join('chat_messages as cm', 'cm.id', 'cmqs.last_unread_message_id');
        } elseif ($type == 'last_communicated') {
            $customer = $customer->join('chat_messages_quick_datas as cmqs', function ($q) {
                $q->on('cmqs.model_id', 'customers.id')->where('cmqs.model', Customer::class);
            });
            $customer = $customer->join('chat_messages as cm', 'cm.id', 'cmqs.last_communicated_message_id');
        } elseif ($type == 'last_received') {
            $chatMessagesWhere .= " and message != '' and message is not null and number = c.phone";
            $customer = $customer->leftJoin(DB::raw('(SELECT MAX(chat_messages.id) as  max_id, customer_id ,message as matched_message  FROM `chat_messages` join customers as c on c.id = chat_messages.customer_id ' . $chatMessagesWhere . ' GROUP BY customer_id ) m_max'), 'm_max.customer_id', '=', 'customers.id');
            $customer = $customer->leftJoin('chat_messages as cm', 'cm.id', '=', 'm_max.max_id');
            $customer = $customer->orderByDesc('cm.created_at');
        } elseif ($type == null) {
            $customer = $customer->leftJoin(DB::raw('(SELECT MAX(chat_messages.id) as  max_id, customer_id ,message as matched_message  FROM `chat_messages` join customers as c on c.id = chat_messages.customer_id ' . $chatMessagesWhere . ' GROUP BY customer_id ) m_max'), 'm_max.customer_id', '=', 'customers.id');
            $customer = $customer->leftJoin('chat_messages as cm', 'cm.id', '=', 'm_max.max_id');
        }
        $customer = $customer->orderByDesc('cm.created_at');
        if ($request->customer_id != null) {
            $customer = $customer->where('customers.id', $request->customer_id);
        }

        if ($request->customer_name != null) {
            $customer = $customer->where('customers.name', 'like', '%' . $request->customer_name . '%');
        }

        if ($request->next_action != null) {
            $customer = $customer->where('customers.customer_next_action_id', $request->next_action);
        }

        if ($request->get('do_not_disturb') ||
            (($request->get('do_not_disturb') === '0' || $request->get('do_not_disturb') === 0) && $request->get('do_not_disturb') != '')) {
            $customer = $customer->where('customers.do_not_disturb', $request->get('do_not_disturb'));
        }

        $customer = $customer->select(['customers.*', 'cm.id as message_id', 'cm.status as message_status', 'cm.message'])->paginate(10);

        $items = [];
        foreach ($customer->items() as $item) {
            $item->message          = utf8_encode($item->message);
            $item->name             = utf8_encode($item->name);
            $item->address          = utf8_encode($item->address);
            $item->city             = utf8_encode($item->city);
            $item->country          = utf8_encode($item->country);
            $item->reminder_message = utf8_encode($item->reminder_message);
            $item->message          = utf8_encode($item->message);
            $item['short_message']  = strlen($item->message) > 20 ? substr($item->message, 0, 20) : $item->message;
            $item['short_name']     = strlen($item->name) > 10 ? substr($item->name, 0, 10) : $item->name;
            $items[]                = $item;
        }

        $title            = 'Quick Customer';

        $nextActionArr    = CustomerNextAction::get();
        $reply_categories = ReplyCategory::orderby('name')->get();

        if (isset($_GET['page'])) {
            unset($_GET['page']);
        }

        return response()->json([
            'code'       => 200,
            'data'       => view('quick-customer.quicklist-html', compact('items', 'title', 'nextActionArr', 'reply_categories'))->render(),
            'total'      => $customer->total(),
            'pagination' => (string) $customer->appends($_GET)->links(),
            'page'       => $customer->currentPage(),
        ]);
    }

    public function addInWhatsappList(Request $request): JsonResponse
    {
        $ids = $request->customer_ids;
        if (! empty($ids)) {
            Customer::whereIn('id', $ids)->update(['in_w_list' => 1]);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Whatsapp list added successfully']);
    }
}
