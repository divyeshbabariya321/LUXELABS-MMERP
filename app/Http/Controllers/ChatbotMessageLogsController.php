<?php

namespace App\Http\Controllers;
use App\WatsonAccount;
use App\ReplyCategory;
use App\Reply;
use App\ChatbotQuestionReply;
use App\ChatbotQuestion;
use App\ChatbotMessageLogResponse;
use App\ChatbotMessageLog;
use App\ChatbotDialogErrorLog;
use App\ChatbotDialog;
use App\ChatbotCategory;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Setting;
use App\WatsonJourney;
use App\Jobs\ManageGoogle;
use Illuminate\Http\Request;
use App\ChatbotDialogResponse;
use App\ChatbotQuestionExample;
use Illuminate\Support\Facades\Validator;
use App\Library\Watson\Model as WatsonManager;

class ChatbotMessageLogsController extends Controller
{
    public function index(Request $request): View
    {
        // Get results

        $logListMagentos = ChatbotMessageLog::orderByDesc('chatbot_message_logs.id');
        $logListMagentos->leftjoin('customers', function ($join) {
            $join->on('chatbot_message_logs.model_id', '=', 'customers.id');
            $join->where('model', '=', 'customers');
        });

        if ($request->name != '') {
            $logListMagentos->where('customers.name', $request->name);
        }

        if ($request->email != '') {
            $logListMagentos->where('customers.email', $request->email);
        }

        if ($request->phone != '') {
            $logListMagentos->where('customers.phone', $request->phone);
        }

        // Get paginated result
        $logListMagentos->select('chatbot_message_logs.*', 'customers.name as cname');
        $logListMagentos = $logListMagentos->paginate(Setting::get('pagination'));
        $total_count     = $logListMagentos->total();
        $allCategory     = ChatbotCategory::all();
        $allCategoryList = [];
        $watson_accounts = WatsonAccount::all();
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        // Show results
        if ($request->ajax()) {
            return view('chatboat_message_logs.index_ajax', compact('logListMagentos', 'total_count', 'allCategoryList', 'watson_accounts'))
                ->with('success', \Request::Session()->get('success'));
        } else {
            return view('chatboat_message_logs.index', compact('logListMagentos', 'total_count', 'allCategoryList', 'watson_accounts'))
                ->with('success', \Request::Session()->get('success'));
        }
    }

    public function chatbotMessageLogHistory(Request $request, $id): View
    {
        $response = ChatbotMessageLogResponse::where('chatbot_message_log_id', $id)->get();

        return view('chatboat_message_logs.history', compact('response'));
    }

    public function pushwaston(Request $request)
    {
        $params = $param = $request->all();

        $validator = Validator::make($params, [
            'value'               => 'required|unique:chatbot_questions|max:255',
            'keyword_or_question' => 'required',
            'watson_account'      => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => $validator->errors()]);
        }
        $params['value']             = str_replace(' ', '_', $params['value']);
        $params['watson_account_id'] = $request->watson_account;
        if ($request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {
            $validator = Validator::make($request->all(), [
                'keyword'         => 'sometimes|nullable|string',
                'suggested_reply' => 'required|min:3|string',
                'sending_time'    => 'sometimes|nullable|date',
                'repeat'          => 'sometimes|nullable|string',
                'is_active'       => 'sometimes|nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 500, 'error' => $validator->errors()]);
            }
        }

        $chatbotQuestion = ChatbotQuestion::create($params);
        WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_created' => 1]);

        if (! empty($params['question'])) {
            foreach ($params['question'] as $qu) {
                if ($qu) {
                    $params['chatbot_question_id']               = $chatbotQuestion->id;
                    $chatbotQuestionExample                      = new ChatbotQuestionExample;
                    $chatbotQuestionExample->question            = $qu;
                    $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                    $chatbotQuestionExample->save();
                    WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_example_created' => 1]);
                }
            }
        }

        if (array_key_exists('types', $params) && $params['types'] != null && array_key_exists('type', $params) && $params['type'] != null) {
            $chatbotQuestionExample = null;
            if (! empty($params['value_name'])) {
                $chatbotQuestionExample                      = new ChatbotQuestionExample;
                $chatbotQuestionExample->question            = $params['value_name'];
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->types               = $params['types'];
                $chatbotQuestionExample->save();
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_example_created' => 1]);
            }

            if ($chatbotQuestionExample) {
                $valueType                             = [];
                $valueType['chatbot_keyword_value_id'] = $chatbotQuestionExample->id;
                if (! empty($params['type'])) {
                    foreach ($params['type'] as $value) {
                        if ($value != null) {
                            $valueType['type']        = $value;
                            $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                            $chatbotKeywordValueTypes->fill($valueType);
                            $chatbotKeywordValueTypes->save();
                        }
                    }
                }
            }
        }

        if ($request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {
            $exploded = explode(',', $request->keyword);

            foreach ($exploded as $keyword) {
                $chatbotQuestionExample                      = new ChatbotQuestionExample;
                $chatbotQuestionExample->question            = trim($keyword);
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->save();
            }

            if ($request->type == 'priority-customer') {
                if ($request->repeat == '') {
                    $customers = Customer::where('is_priority', 1)->get();

                    foreach ($customers as $customer) {
                        ScheduledMessage::create([
                            'user_id'      => Auth::id(),
                            'customer_id'  => $customer->id,
                            'message'      => $chatbotQuestion->suggested_reply,
                            'sending_time' => $request->sending_time,
                        ]);
                    }
                }
            }
        }
        if ($params['watson_account'] > 0) {
            $wotson_account_ids = WatsonAccount::where('id', $request->watson_account)->get();
        } else {
            $wotson_account_ids = WatsonAccount::all();
        }

        foreach ($wotson_account_ids as $id) {
            $data_to_insert[] = [
                'suggested_reply'     => $params['suggested_reply'],
                'store_website_id'    => $id->store_website_id,
                'chatbot_question_id' => $chatbotQuestion->id,
            ];
        }
        ChatbotQuestionReply::insert($data_to_insert);
        WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_reply_inserted' => 1]);

        if ($request->erp_or_watson == 'watson') {
            if ($request->keyword_or_question == 'intent' || $request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {
                ChatbotQuestion::where('id', $chatbotQuestion->id)->update(['watson_status' => 'Pending watson send']);

                $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, $request->watson_account, $id->store_website_id));
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'response' => json_encode($result)]);
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_pushed' => 1]);
                $this->createdialog($request->value, $request->suggested_reply, $request->watson_account, $id->store_website_id);
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'dialog_inserted' => 1]);
                session()->flash('msg', 'Successfully done the operation.');

                return redirect()->back();
            }

            if ($request->keyword_or_question == 'entity') {
                ChatbotQuestion::where('id', $chatbotQuestion->id)->update(['watson_status' => 'Pending watson send']);

                $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, $request->watson_account, $id->store_website_id));
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'response' => json_encode($result)]);
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'question_pushed' => 1]);
                $this->createdialog($request->value, $request->suggested_reply, $request->watson_account, $id->store_website_id);
                WatsonJourney::updateOrCreate(['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id], ['chatbot_message_log_id' => $param['log_id'], 'question_id' => $chatbotQuestion->id, 'dialog_inserted' => 1]);
                session()->flash('msg', 'Successfully done the operation.');

                return redirect()->back();
            }
        }
    }

    public function watsonJourney(): View
    {
        $watsonJourney = WatsonJourney::whereNotNull('id')->paginate(30);

        return view('chatboat_message_logs.journey', compact('watsonJourney'));
    }

    public function createdialog($name, $suggested_reply, $watson_account, $websiteId = null, $replyId = null)
    {
        $params = [
            'name'             => $name,
            'parent_id'        => 0,
            'dialog_type'      => 'node',
            'response_type'    => 'standard',
            'previous_sibling' => 0,
            'store_website_id' => $websiteId,
            'match_condition'  => '#' . $name,
            'title'            => $name,
        ];

        $dialog                           = ChatbotDialog::create($params);
        $params['name']                   = str_replace(' ', '_', $name);
        $params['chatbot_dialog_id']      = $dialog->id;
        $params['response_type']          = 'text';
        $params['value']                  = $suggested_reply;
        $params['message_to_human_agent'] = 1;

        $chatbotDialogResponse = new ChatbotDialogResponse;
        $chatbotDialogResponse->fill($params);
        $chatbotDialogResponse->save();
        $result = json_decode(WatsonManager::pushDialog($dialog->id, $suggested_reply, $replyId));

        return $dialog;
    }

    public function pushQuickRepliesToWaston(): RedirectResponse
    {
        $replyCategories               = ReplyCategory::where('pushed_to_watson', 0)->get();
        $params['watson_account']      = 0;
        $params['keyword_or_question'] = 'intent';
        $params['erp_or_watson']       = 'watson';
        foreach ($replyCategories as $replyCategory) {
            $replies = Reply::where('category_id', $replyCategory['id'])->where('pushed_to_watson', 0)->orderByDesc('id')->pluck('reply', 'id')->toArray();
            foreach ($replies as $replyId => $reply) {
                $params['value']           = str_replace(' ', '_', $replyCategory['name']);
                $params['suggested_reply'] = $reply;

                $chatbotQuestion = ChatbotQuestion::updateOrCreate($params, $params);

                $watson_account_ids = WatsonAccount::all();
                $data_to_insert     = [];
                foreach ($watson_account_ids as $id) {
                    $recordAvailable = ChatbotQuestionReply::where([
                        'suggested_reply'     => $params['suggested_reply'],
                        'store_website_id'    => $id->store_website_id,
                        'chatbot_question_id' => $chatbotQuestion->id,
                    ])->first();

                    if ($recordAvailable == null) {
                        $data_to_insert[] = [
                            'suggested_reply'     => $params['suggested_reply'],
                            'store_website_id'    => $id->store_website_id,
                            'chatbot_question_id' => $chatbotQuestion->id,
                        ];
                    }
                }

                ChatbotQuestionReply::insert($data_to_insert);

                ChatbotQuestion::where('id', $chatbotQuestion->id)->update(['watson_status' => 'Pending watson send']);
                $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, $params['watson_account'], $id->store_website_id));
                $dialog = $this->createdialog($params['value'], $params['suggested_reply'], $params['watson_account'], $id->store_website_id, $replyId);
                $replyCategory->update(['intent_id' => $chatbotQuestion->id, 'dialog_id' => $dialog->id, 'pushed_to_watson' => 1]);
                Reply::where('id', $replyId)->update(['pushed_to_watson' => 1]);
            }
        }
        session()->flash('msg', 'Successfully done the operation.');

        return redirect()->back();
    }

    public function pushRepyToWaston(Request $request)
    {
        $input                         = $request->input();
        $params['keyword_or_question'] = 'intent';
        $params['erp_or_watson']       = 'watson';
        $reply                         = Reply::where('id', $input['id'])->where('pushed_to_watson', 0)->first();
        if ($reply != null) {
            $replyCategory             = ReplyCategory::where('id', $reply['category_id'])->first();
            $params['value']           = str_replace(' ', '_', $replyCategory['name']);
            $params['suggested_reply'] = $reply['reply'];

            $chatbotQuestion          = ChatbotQuestion::updateOrCreate($params, $params);
            $params['watson_account'] = 0;
            $watson_account_ids       = WatsonAccount::all();
            $data_to_insert           = [];
            foreach ($watson_account_ids as $id) {
                $recordAvailable = ChatbotQuestionReply::where([
                    'suggested_reply'     => $params['suggested_reply'],
                    'store_website_id'    => $id->store_website_id,
                    'chatbot_question_id' => $chatbotQuestion->id,
                ])->first();

                if ($recordAvailable == null) {
                    $data_to_insert[] = [
                        'suggested_reply'     => $params['suggested_reply'],
                        'store_website_id'    => $id->store_website_id,
                        'chatbot_question_id' => $chatbotQuestion->id,
                    ];
                }
            }

            ChatbotQuestionReply::insert($data_to_insert);

            ChatbotQuestion::where('id', $chatbotQuestion->id)->update(['watson_status' => 'Pending watson send']);
            $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, $params['watson_account'], $id->store_website_id));
            $dialog = $this->createdialog($params['value'], $params['suggested_reply'], $params['watson_account'], $id->store_website_id, $reply['id']);
            $replyCategory->update(['intent_id' => $chatbotQuestion->id, 'dialog_id' => $dialog->id, 'pushed_to_watson' => 1]);
            Reply::where('id', $reply['id'])->update(['pushed_to_watson' => 1]);
        }

        return ['code' => 200, 'message' => 'Reply Pushed'];
    }

    public function replyLogs(Request $request): JsonResponse
    {
        $replyId = $request->get('id');
        $logs    = ChatbotDialogErrorLog::where('reply_id', $replyId)->orderByDesc('id')->get();

        return response()->json(['logs' => $logs]);
    }

    public function pushQuickRepliesToGoogle(): RedirectResponse
    {
        $replyCategories               = ReplyCategory::where('push_to_google', 0)->get()->chunk(5)->toArray();
        $params['google_account_id']   = 0;
        $params['keyword_or_question'] = 'intent';
        foreach ($replyCategories as $replyCategory) {
            foreach ($replyCategory as $value) {
                $replies = Reply::where('category_id', $value['id'])->where('pushed_to_google', 0)->orderByDesc('id')->pluck('reply', 'id')->toArray();
                foreach ($replies as $reply) {
                    $params['value']           = str_replace(' ', '_', $value['name']);
                    $params['suggested_reply'] = $reply;
                    $chatbotQuestion           = ChatbotQuestion::updateOrCreate($params, $params);
                    $data_to_insert            = [];

                    $recordAvailable = ChatbotQuestionReply::where([
                        'suggested_reply'     => $params['suggested_reply'],
                        'chatbot_question_id' => $chatbotQuestion->id,
                    ])->first();

                    if ($recordAvailable == null) {
                        $data_to_insert[] = [
                            'suggested_reply'     => $params['suggested_reply'],
                            'chatbot_question_id' => $chatbotQuestion->id,
                        ];
                    }
                    ChatbotQuestionReply::insert($data_to_insert);

                    ChatbotQuestion::where('id', $chatbotQuestion->id)->update(['google_status' => '1']);

                    ManageGoogle::dispatch($chatbotQuestion->id, $data_to_insert)->delay(Carbon::now()->addSeconds(120));
                }
            }
        }
        session()->flash('msg', 'Successfully done the operation.');

        return redirect()->back();
    }
}
