<?php

namespace App\Http\Controllers;

use App\BulkCustomerRepliesKeyword;
use App\ChatbotKeyword;
use App\Customer;
use App\CustomerBulkMessageDND;
use App\Helpers;
use App\Http\Requests\StoreKeywordBulkCustomerReplyRequest;
use App\Models\CustomerNextAction;
use App\QuickSellGroup;
use App\ReplyCategory;
use App\Setting;
use App\TicketStatuses;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Marketing\WhatsappConfig;

class BulkCustomerRepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        set_time_limit(0);
        $keywords = BulkCustomerRepliesKeyword::where('is_manual', 1)->get();
        $autoKeywords = BulkCustomerRepliesKeyword::where('count', '>', 10)
            ->whereNotIn('value', [
                'test', 'have', 'sent', 'the', 'please', 'pls', 'through', 'using', 'solo', 'that',
                'comes', 'message', 'sending', 'Yogesh', 'Greetings', 'this', 'numbers', 'maam', 'from',
                'changed', 'them', 'with', '0008000401700', 'WhatsApp', 'send', 'Auto', 'based', 'suggestion',
                'Will', 'your', 'number', 'number,', 'messages', 'also', 'meanwhile',
            ])
            ->take(50)
            ->orderByDesc('count')
            ->get();
        $searchedKeyword = null;

        $customers = [];
        if ($request->get('keyword_filter')) {
            $keyword = $request->get('keyword_filter');

            $searchedKeyword = BulkCustomerRepliesKeyword::where('value', $keyword)->first();

            $customerids = Customer::whereHas('bulkMessagesKeywords', function ($q) use ($keyword) {
                $q->where('value', $keyword);
            });

            if ($request->dnd_enabled === '0') {
                $customerids = $customerids->whereHas('dnd');
            } elseif ($request->dnd_enabled === '1') {
                $customerids = $customerids->whereDoesntHave('dnd');
            }
            $customerids = $customerids->pluck('id')->toArray();

            // $customers = Customer::leftJoin(\DB::raw('(SELECT MAX(chat_messages.id) as  max_id,whatsapp_number, customer_id ,message as matched_message  FROM `chat_messages` join customers as c on c.id = chat_messages.customer_id  GROUP BY customer_id ) m_max'), 'm_max.customer_id', '=', 'customers.id')
            //     ->groupBy('customers.id')
            //     ->whereIn('id', $customerids);

            $customers = Customer::leftJoin(
                DB::raw('(SELECT MAX(chat_messages.id) as max_id, whatsapp_number, customer_id, message as matched_message FROM chat_messages JOIN customers as c ON c.id = chat_messages.customer_id GROUP BY customer_id) m_max'),
                'm_max.customer_id',
                '=',
                'customers.id'
            )
                ->groupBy('customers.id')
                ->whereIn('customers.id', $customerids)
                ->select('customers.*', 'm_max.max_id', 'm_max.whatsapp_number', 'm_max.matched_message')
                ->get();

            $customers = $customers->sortByDesc('max_id')->paginate(20);
        }

        $groups = QuickSellGroup::select('id', 'name', 'group')->orderByDesc('id')->get();
        $pdfList = [];

        $nextActionArr = CustomerNextAction::pluck('name', 'id');
        $reply_categories = ReplyCategory::with('approval_leads')->orderby('name')->get();

        $settingShortCuts = [
            'image_shortcut' => Setting::get('image_shortcut'),
            'price_shortcut' => Setting::get('price_shortcut'),
            'call_shortcut' => Setting::get('call_shortcut'),
            'screenshot_shortcut' => Setting::get('screenshot_shortcut'),
            'details_shortcut' => Setting::get('details_shortcut'),
            'purchase_shortcut' => Setting::get('purchase_shortcut'),
        ];
        $users_array = Helpers::getUserArray(User::all());

        $whatsappNos = getInstanceNo();
        $chatbotKeywords = ChatbotKeyword::all();
        $assigned_to = User::with('roles')->get();
        $statuses = TicketStatuses::all();
        $whatsappConfigs = WhatsappConfig::getWhatsappConfigs();

        return view('bulk-customer-replies.index', compact('customers', 'keywords', 'autoKeywords', 'searchedKeyword', 'nextActionArr', 'groups', 'pdfList', 'reply_categories', 'settingShortCuts', 'users_array', 'whatsappNos', 'chatbotKeywords', 'assigned_to', 'statuses', 'whatsappConfigs'));
    }

    public function updateWhatsappNo(Request $request): JsonResponse
    {
        $no = $request->get('whatsapp_no');
        $customers = explode(',', $request->get('customers', ''));
        $total = 0;
        if (! empty($no) && is_array(array_filter($customers))) {
            $lCustomer = array_filter($customers);
            $total = count($lCustomer);
        }

        return response()->json(['code' => 200, 'total' => $total]);
    }

    public function storeKeyword(StoreKeywordBulkCustomerReplyRequest $request): RedirectResponse
    {

        $type = 'keyword';
        $numOfSpaces = count(explode(' ', $request->get('keyword')));
        if ($numOfSpaces > 1 && $numOfSpaces < 4) {
            $type = 'phrase';
        } elseif ($numOfSpaces >= 4) {
            $type = 'sentence';
        }

        $keyword = new BulkCustomerRepliesKeyword;
        $keyword->value = $request->get('keyword');
        $keyword->text_type = $type;
        $keyword->is_manual = 1;
        $keyword->count = 0;
        $keyword->save();

        return redirect()->back()->with('message', Str::title($type).' added successfully!');
    }

    public function sendMessagesByKeyword(Request $request): JsonResponse
    {
        $customer_id_array = $request->get('customers');

        foreach ($request->get('customers') as $customer) {
            $myRequest = new Request;
            $myRequest->setMethod('POST');
            $myRequest->request->add([
                'message' => $request->get('message_bulk'),
                'customer_id' => $customer,
                'status' => 1,
            ]);

            app(WhatsAppController::class)->sendMessage($myRequest, 'customer');
        }

        return response()->json(['message' => 'Messages sent successfully!', 'c_id' => $customer_id_array]);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    public function addToDND(Request $request): JsonResponse
    {
        $exist = CustomerBulkMessageDND::where('customer_id', $request->customer_id)->where('filter', $request->filter['keyword_filter'])->first();

        if ($exist == null) {
            CustomerBulkMessageDND::create([
                'customer_id' => $request->customer_id,
                'filter' => $request->filter ? $request->filter['keyword_filter'] : null,
            ]);
        }

        return response()->json(true);
    }

    public function removeFromDND(Request $request): JsonResponse
    {
        $dnd = CustomerBulkMessageDND::where('customer_id', $request->customer_id)->where('filter', $request->filter['keyword_filter'])->delete();

        return response()->json(true);
    }
}
