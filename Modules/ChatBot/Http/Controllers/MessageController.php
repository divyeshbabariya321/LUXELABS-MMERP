<?php

namespace Modules\ChatBot\Http\Controllers;

use App\ChatbotCategory;
use App\ChatbotMessageLog;
use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\ChatbotQuestionReply;
use App\ChatbotReply;
use App\ChatMessage;
use App\Console\Commands\ReindexMessages;
use App\Customer;
use App\DeveloperTask;
use App\Elasticsearch\Elasticsearch;
use App\Elasticsearch\Reindex\Messages;
use App\Email;
use App\Helpers\MessageHelper;
use App\Http\Controllers\WhatsAppController;
use App\Jobs\SendEmail;
use App\Jobs\SendMessageToCustomer;
use App\Library\Google\DialogFlow\DialogFlowService;
use App\Models\DataTableColumn;
use App\Models\DialogflowEntityType;
use App\Models\GoogleResponseId;
use App\Models\TmpReplay;
use App\ReplyCategory;
use App\StoreWebsite;
use App\SuggestedProduct;
use App\Supplier;
use App\Task;
use App\TicketStatuses;
use App\User;
use App\Vendor;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  null|mixed  $isElastic
     */
    public function index(Request $request, $isElastic = null)
    {
        /**
         * Elastic
         */
        $isElastic = false;

        return $this->indexDB($request, $isElastic);
        $elastic = new Elasticsearch;
        $sizeof = $elastic->count(Messages::INDEX_NAME);

        if (! $isElastic) {
            $isElastic = false;

            return $this->indexDB($request, $isElastic);
        }

        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $queryParam = [];

        if (! empty($search)) {
            $queryParam['multi_match']['query'] = $search;

            $queryParam['multi_match']['fields'][] = 'question';
            $queryParam['multi_match']['fields'][] = 'answer';
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (! empty($unreplied_msg)) {
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $queryParam['match']['approved'] = $status;
        }

        if (request('unread_message') == 'true') {
            $queryParam['match']['is_read'] = 0;
        }

        if (request('message_type') != null) {
            if (request('message_type') == 'email') {
                $queryParam['range']['is_email']['gt'] = 0;
            }
            if (request('message_type') == 'task') {
                $queryParam['range']['task_id']['gt'] = 0;
            }
            if (request('message_type') == 'dev_task') {
                $queryParam['range']['developer_task_id']['gt'] = 0;
            }
            if (request('message_type') == 'ticket') {
                $queryParam['range']['ticket_id']['gt'] = 0;
            }
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            if (in_array('customer', request('search_type'))) {
                $queryParam['range']['customer_id']['gt'] = 0;
            }
            if (in_array('vendor', request('search_type'))) {
                $queryParam['range']['vendor_id']['gt'] = 0;
            }
            if (in_array('supplier', request('search_type'))) {
                $queryParam['range']['supplier_id']['gt'] = 0;
            }
            if (in_array('dev_task', request('search_type'))) {
                $queryParam['range']['developer_task_id']['gt'] = 0;
            }
            if (in_array('task', request('search_type'))) {
                $queryParam['range']['task_id']['gt'] = 0;
            }
        }

        $currentPage = Paginator::resolveCurrentPage();

        $total = $sizeof;

        $body = [];

        if (isset($queryParam['match'])) {
            $body[]['match'] = $queryParam['match'];
        }
        if (isset($queryParam['range'])) {
            $range = [];
            foreach ($queryParam['range'] as $key => $value) {
                $body[]['range'][$key] = $value;
            }
        }
        if (isset($queryParam['multi_match'])) {
            $body[]['multi_match'] = $queryParam['multi_match'];
        }

        $body['exists'] = ['field' => 'message'];

        $response = Elasticsearch::search(
            [
                'index' => Messages::INDEX_NAME,
                'from' => ($currentPage - 1) * 20,
                'size' => 20,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $body,
                            'should' => [
                                ['exists' => ['field' => 'vendor_id']],
                                ['exists' => ['field' => 'customer_id']],
                                ['exists' => ['field' => 'user_id']],
                                ['exists' => ['field' => 'task_id']],
                                ['exists' => ['field' => 'developer_task_id']],
                                ['exists' => ['field' => 'bug_id']],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'aggs' => [
                        'group_by_customer' => [
                            'terms' => [
                                'field' => 'customer_id',
                                'size' => 1,
                            ],
                            'aggs' => [
                                'group_by_user' => [
                                    'terms' => [
                                        'field' => 'user_id',
                                        'size' => 1,
                                    ],
                                    'aggs' => [
                                        'group_by_vendor' => [
                                            'terms' => [
                                                'field' => 'vendor_id',
                                                'size' => 1,
                                            ],
                                            'aggs' => [
                                                'group_by_supplier' => [
                                                    'terms' => [
                                                        'field' => 'supplier_id',
                                                        'size' => 1,
                                                    ],
                                                    'aggs' => [
                                                        'group_by_task' => [
                                                            'terms' => [
                                                                'field' => 'task_id',
                                                                'size' => 1,
                                                            ],
                                                            'aggs' => [
                                                                'group_by_developer_task' => [
                                                                    'terms' => [
                                                                        'field' => 'developer_task_id',
                                                                        'size' => 1,
                                                                    ],
                                                                    'aggs' => [
                                                                        'group_by_bug' => [
                                                                            'terms' => [
                                                                                'field' => 'bug_id',
                                                                                'size' => 1,
                                                                            ],
                                                                            'aggs' => [
                                                                                'group_by_email' => [
                                                                                    'terms' => [
                                                                                        'field' => 'email_id',
                                                                                        'size' => 1,
                                                                                    ],
                                                                                    'aggs' => [
                                                                                        'max_number' => [
                                                                                            'max' => [
                                                                                                'field' => 'id',
                                                                                            ],
                                                                                        ],
                                                                                    ],
                                                                                ],
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['id' => 'desc'],
                    ],
                ],
            ]
        );

        $allItems = $response['hits']['hits'] ?? [];
        $total = $response['hits']['total']['value'] ?? 0;

        $pendingApprovalMsg = array_map(
            fn ($item) => (new ChatMessage)->setRawAttributes($item['_source']),
            $allItems
        );

        $pendingApprovalMsg = Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            'items' => $pendingApprovalMsg,
            'total' => $total,
            'perPage' => 20,
            'currentPage' => $currentPage,
            'options' => [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        ]);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $currentPage;
        $reply_categories = ReplyCategory::with('approval_leads')->orderby('name')->get();

        $assigned_to = User::with('roles')->get();
        $statuses = TicketStatuses::all();
        if ($request->ajax()) {
            $tml = (string) view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'isElastic', 'assigned_to', 'statuses'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'chatbot-messages')->first();

        $dynamicColumnsToShowPostman = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPostman = json_decode($hideColumns, true);
        }

        //dd($pendingApprovalMsg);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'isElastic', 'dynamicColumnsToShowPostman', 'assigned_to', 'statuses'));
    }

    public function reindex(Request $request): JsonResponse
    {
        $paramFix = $request->get('fix');

        if ($paramFix == 1) {
            Artisan::call('reindex:messages', ['param' => 'fix']);
        }

        if (ReindexMessages::isRunning()) {
            return response()->json(['message' => 'Reindex already started in background.', 'code' => 500], 500);
        }

        Artisan::call('reindex:messages');

        return response()->json(['message' => 'Reindex successful, reload page.', 'code' => 200]);
    }

    public function todayMessagesCheck(Request $request): JsonResponse
    {
        $last_message_id = $request->cmid;
        $tml = '';
        if ($last_message_id) {
        }

        return response()->json(['code' => 200, 'tpl' => $tml]);
    }

    public function todayMessages(Request $request)
    {
        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');
        $currentPage = Paginator::resolveCurrentPage('page');
        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select([
            'cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator',
        ])
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->paginate(10);
        // dd($pendingApprovalMsg);

        $allCategory = ChatbotCategory::all();

        $pendingApprovalMsg->links();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        $reply_categories = ReplyCategory::with('approval_leads')->orderby('name')->get();

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();
        $totalpage = $pendingApprovalMsg->lastPage();
        $assigned_to = User::with('roles')->get();
        $statuses = TicketStatuses::all();
        if ($request->ajax()) {
            $tml = '';
            foreach ($pendingApprovalMsg as $index => $pam) {
                $tml .= (string) view('chatbot::message.partial.today-list', compact('pam', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'assigned_to', 'statuses'));
            }

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page, 'totalpage' => $totalpage]);
        }

        //dd($pendingApprovalMsg);
        return view('chatbot::message.today', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'totalpage', 'assigned_to', 'statuses'));
    }

    public function approve(): JsonResponse
    {
        $id = request('id');

        $messageId = 0;

        if ($id > 0) {
            $myRequest = new Request;
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $id]);

            $chatMEssage = ChatMessage::find($id);

            $type = '';
            if ($chatMEssage->task_id > 0) {
                $type = 'task';
            } elseif ($chatMEssage->developer_tasK_id > 0) {
                $type = 'issue';
            } elseif ($chatMEssage->vendor_id > 0) {
                $type = 'vendor';
            } elseif ($chatMEssage->user_id > 0) {
                $type = 'user';
            } elseif ($chatMEssage->supplier_id > 0) {
                $type = 'supplier';
            } elseif ($chatMEssage->customer_id > 0) {
                $type = 'customer';
            } elseif ($chatMEssage->message_type == 'email') {
                $type = 'email';
                $messageId = $id;
            }

            app(WhatsAppController::class)->approveMessage($type, $myRequest, $messageId);
        }

        return response()->json(['code' => 200, 'message' => 'Messsage Send Successfully']);
    }

    /**
     * [removeImages description]
     *
     * @return [type] [description]
     */
    public function removeImages(Request $request): JsonResponse
    {
        $deleteImages = $request->get('delete_images', []);

        if (! empty($deleteImages)) {
            foreach ($deleteImages as $image) {
                [$mediableId, $mediaId] = explode('_', $image);
                if (! empty($mediaId) && ! empty($mediableId)) {
                    \Db::statement('delete from mediables where mediable_id = ? and media_id = ? limit 1', [$mediableId, $mediaId]);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Image has been removed now']);
    }

    public function uploadAudio(Request $request): JsonResponse
    {
        if ($request->hasFile('audio_data')) {
            $audio_data = $request->file('audio_data');
            $fileOriginalName = $audio_data->getClientOriginalName();
            $path = Storage::disk('s3')->putFileAs('audio-message', $audio_data, $fileOriginalName);
            $exists_file = Storage::disk('s3')->exists($path);
            if ($exists_file) {
                $path = Storage::disk('s3')->url($path);

                return response()->json(['success' => true, 'message' => '', 'url' => $path]);
            } else {
                return response()->json(['success' => false, 'message' => 'The file can not upload to the server']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Requested audio data is not found!']);
    }

    public function attachImages(Request $request): JsonResponse
    {
        $id = $request->get('chat_id', 0);

        $data = [];
        $ids = [];
        $images = [];

        if ($id > 0) {
            // find the chat message
            $chatMessages = ChatMessage::where('id', $id)->first();

            if ($chatMessages) {
                $chatsuggestion = $chatMessages->suggestion;
                if ($chatsuggestion) {
                    $data = SuggestedProduct::attachMoreProducts($chatsuggestion);
                    $code = 500;
                    $message = 'Sorry no images found!';
                    if (count($data) > 0) {
                        $code = 200;
                        $message = 'More images attached Successfully';
                    }

                    return response()->json(['code' => $code, 'data' => $data, 'message' => $message]);
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Sorry , There is not avaialble images']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'It looks like there is not validate id']);
    }

    public function forwardToCustomer(Request $request): JsonResponse
    {
        $customer = $request->get('customer');
        $images = $request->get('images');

        if ($customer > 0 && ! empty($images)) {
            $params = request()->all();
            $params['user_id'] = Auth::id();
            $params['is_queue'] = 0;
            $params['status'] = ChatMessage::CHAT_MESSAGE_APPROVED;
            $params['customer_ids'] = is_array($customer) ? $customer : [$customer];
            $groupId = ChatMessage::max('group_id');
            $params['group_id'] = ($groupId > 0) ? $groupId + 1 : 1;
            $params['images'] = $images;

            SendMessageToCustomer::dispatch($params);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message forward to customer(s)']);
    }

    public function resendToBot(Request $request): JsonResponse
    {
        $chatId = $request->get('chat_id');
        if (! empty($chatId)) {
            $chatMessage = ChatMessage::find($chatId);
            if ($chatMessage) {
                $customer = $chatMessage->customer;
                if ($customer) {
                    $params = $chatMessage->getAttributes();

                    MessageHelper::whatsAppSend($customer, $chatMessage->message, null, $chatMessage);

                    $data = [
                        'model' => Customer::class,
                        'model_id' => $customer->id,
                        'chat_message_id' => $chatId,
                        'message' => $chatMessage->message,
                        'status' => 'started',
                    ];
                    $chat_message_log_id = ChatbotMessageLog::generateLog($data);
                    $params['chat_message_log_id'] = $chat_message_log_id;
                    MessageHelper::sendwatson($customer, $chatMessage->message, null, $chatMessage, $params, false, 'customer');

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent Successfully']);
                }
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Message not exist in record']);
    }

    public function updateReadStatus(Request $request): JsonResponse
    {
        $chatId = $request->get('chat_id');
        $value = $request->get('value');

        $reply = ChatbotReply::find($chatId);

        if ($reply) {
            $reply->is_read = $value;
            $reply->save();

            $status = ($value == 1) ? 'read' : 'unread';

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Marked as '.$status]);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'Message not exist in record']);
    }

    public function stopReminder(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $type = $request->get('type');

        if ($type == 'developer_task') {
            $task = DeveloperTask::find($id);
        } else {
            $task = Task::find($id);
        }

        if ($task) {
            $task->frequency = 0;
            $task->save();

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Reminder turned off']);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'No task found']);
    }

    public function updateEmailAddress(Request $request): JsonResponse
    {
        $chat_id = $request->chat_id;
        $fromemail = $request->fromemail;
        $toemail = $request->toemail;
        $ccemail = $request->ccemail;
        if ($chat_id > 0) {
            ChatMessage::where('id', $chat_id)
                ->update(['from_email' => $fromemail, 'to_email' => $toemail, 'cc_email' => $ccemail]);

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Record Updated Successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function updateSimulator(Request $request): JsonResponse
    {
        $requestMessage = \Illuminate\Support\Facades\Request::create('/', 'GET', [
            'limit' => 1,
            'object' => $request->object,
            'object_id' => $request->objectId,
            'order' => 'asc',
            'for_simulator' => true,
        ]);
        $response = app(ChatMessagesController::class)->loadMoreMessages($requestMessage);
        $id = $request->objectId;
        if ($id > 0) {
            if ($request->object == 'customer') {
                $update_simulator = Customer::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            } elseif ($request->object == 'vendor') {
                $update_simulator = Vendor::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            } elseif ($request->object == 'supplier') {
                $update_simulator = Supplier::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            }

            return response()->json(['code' => 200, 'data' => [$update_simulator, $id], 'messages' => 'Auto simulator on successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function chatBotReplayList(Request $request): View
    {
        $requestMessage = \Illuminate\Support\Facades\Request::create('/', 'GET', [
            'limit' => 20,
            'object' => $request->object,
            'object_id' => $request->object_id,
            'order' => 'asc',
            'plan_response' => true,
        ]);
        $message_list = app(ChatMessagesController::class)->loadMoreMessages($requestMessage);

        return view('chatbot::message.partial.chatbot-list', compact('message_list'));
    }

    public function sendSuggestedMessage(Request $request): JsonResponse
    {
        $tmp_replay_id = $request->get('tmp_reply_id');
        $value = $request->get('value');
        $reply = TmpReplay::find($tmp_replay_id);
        if ($reply) {
            if ($value == 1) {
                $reply['is_approved'] = 1;
            } else {
                $reply['is_reject'] = 1;
            }
            $reply->save();
            if ($value == 1) {
                $requestMessage = \Illuminate\Support\Facades\Request::create('/', 'GET', [
                    'limit' => 1,
                    'object' => $reply->type,
                    'object_id' => $reply->type_id,
                    'plan_response' => true,
                ]);
                $lastMessage = app(ChatMessagesController::class)->loadMoreMessages($requestMessage);
                $requestData = [
                    'chat_id' => $reply->chat_message_id,
                    'status' => 2,
                    'add_autocomplete' => false,
                ];
                if ($lastMessage[0]['type'] === 'email') {
                    $requestData['email_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'chatbot') {
                    $requestData['customer_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'task') {
                    $requestData['task_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'issue') {
                    $requestData['issue_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'customer') {
                    $requestData['customer_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'developer_task') {
                    $requestData['developer_task_id'] = $lastMessage[0]['object_type_id'];
                }
                $requestData['message'] = $reply->suggested_replay;
                $requestData = \Illuminate\Support\Facades\Request::create('/', 'POST', $requestData);
                app(WhatsAppController::class)->sendMessage($requestData, $reply->type);
            }
            $status = ($value == 1) ? 'send message Successfully' : 'Suggested message rejected';

            return response()->json(['code' => 200, 'data' => [], 'messages' => $status]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Suggested replay does not exist in record']);
        }
    }

    public function simulatorMessageList(Request $request, $object, $objectId)
    {
        $object = $request->has('object') ? $request->get('object') : $object;
        $objectId = $request->has('object_id') ? $request->get('object_id') : $objectId;
        $objectData = [];
        if ($object == 'customer') {
            $customer = Customer::find($objectId);
            $objectData['type'] = $object;
            $objectData['name'] = $customer['name'];
            $google_accounts = GoogleDialogAccount::with('storeWebsite')->where('site_id', $customer->store_website_id)->first();
            $objectData['url'] = $google_accounts['storeWebsite']['website'];
        } else {
            if ($object == 'vendor') {
                $vendor = Vendor::find($objectId);
                $objectData['type'] = $object;
                $objectData['name'] = $vendor['name'];
            } elseif ($object == 'supplier') {
                $supplier = Supplier::find($objectId);
                $objectData['type'] = $object;
                $objectData['name'] = $supplier['name'];
            }
            $google_accounts = GoogleDialogAccount::with('storeWebsite')->where('default_selected', 1)->first();
            if (empty($google_accounts)) {
                $google_accounts = GoogleDialogAccount::with('storeWebsite')->first();
            }
            $objectData['url'] = $google_accounts ? $google_accounts['storeWebsite']['website'] : '';
        }
        $requestMessage = \Illuminate\Support\Facades\Request::create('/', 'GET', [
            'limit' => 1,
            'object' => $object,
            'object_id' => $objectId,
            'order' => 'asc',
            'plan_response' => true,
            'page' => $request->page_no,
        ]);
        $message = app(ChatMessagesController::class)->loadMoreMessages($requestMessage);

        //        if (!isset($message[0])) {
        //            return response()->json(['code' => 200, 'data' => null, 'messages' => 'Message completed']);
        //        }
        $intent = '';
        $type = '';
        $chatQuestions = [];

        if (! empty($message)) {
            if ($message[0]['inout'] == 'out') {
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('chatbot_questions.value', 'like', '%'.$message[0]['message'].'%')->orWhere('cqe.question', 'like', '%'.$message[0]['message'].'%')
                    ->groupBy('chatbot_questions.id')
                    ->orderByDesc('chatbot_questions.id')
                    ->first();
            } else {
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_questions_reply as cr', 'cr.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', DB::raw('group_concat(cr.suggested_reply) as `suggested_replies`'))
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('cr.suggested_reply', 'like', '%'.$message[0]['message'].'%')
                    ->groupBy('chatbot_questions.id')
                    ->orderByDesc('chatbot_questions.id')
                    ->first();
            }

            if ($chatQuestions) {
                $intent = $chatQuestions['value'];
                $type = 'Database';
            } else {
                $dialogFlowService = new DialogFlowService($google_accounts);
                $response = $dialogFlowService->detectIntent(null, $message[0]['message']);
                $intent = $response->getIntent()->getDisplayName();
                $intentName = $response->getIntent()->getName();
                $intentName = explode('/', $intentName);
                $intentName = $intentName[count($intentName) - 1];
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('chatbot_questions.google_response_id', $intentName)
                    ->groupBy('chatbot_questions.id')
                    ->orderByDesc('chatbot_questions.id')
                    ->first();
                if (! $chatQuestions) {
                    $chatQuestions = ['value' => $intent, 'id' => null];
                }
                $type = 'google';
            }
        }

        if ($request->request_type == 'ajax') {
            return response()->json(['code' => 200, 'data' => ['message' => $message ? $message[0] : '', 'chatQuestion' => $chatQuestions, 'type' => $type, 'intent' => $intent], 'messages' => 'Get message successfully']);
        }
        $allIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->pluck('value', 'id')->toArray();

        return view('chatbot::message.partial.chatbot', compact('message', 'intent', 'type', 'chatQuestions', 'allIntents', 'objectData'));
    }

    public function storeIntent(Request $request): JsonResponse
    {
        if ($request->question_id > 0) {
            $chatBotQuestion = ChatbotQuestion::where('id', $request->question_id)->first();
            $questionArr = [];
            if ($chatBotQuestion) {
                foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                    $questionArr[] = $question->question;
                }
                $googleAccount = GoogleDialogAccount::where('id', $chatBotQuestion->google_account_id)->first();
                $storeQuestion = new ChatbotQuestionExample;
                $storeQuestion->question = $request->value;
                $storeQuestion->chatbot_question_id = $chatBotQuestion['id'];
                $storeQuestion->save();
                $questionArr[] = $request->value;
                $dialogService = new DialogFlowService($googleAccount);
                try {
                    $response = $dialogService->createIntent([
                        'questions' => $questionArr,
                        'reply' => explode(',', $chatBotQuestion['suggested_reply']),
                        'name' => $chatBotQuestion['value'],
                        'parent' => $chatBotQuestion['parent'],
                    ], $chatBotQuestion->google_response_id ?: null);
                    if ($response) {
                        $chatBotQuestion->google_status = '1';
                        $chatBotQuestion->save();

                        $name = explode('/', $response);
                        $store_response = new GoogleResponseId;
                        $store_response->google_response_id = $name[count($name) - 1];
                        $store_response->google_dialog_account_id = $googleAccount->id;
                        $store_response->chatbot_question_id = $chatBotQuestion->id;
                        $store_response->save();
                    }

                    return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Intent Store successfully']);
                } catch (Exception $e) {
                    $chatBotQuestion->google_status = $e->getMessage();
                    $chatBotQuestion->save();

                    return response()->json(['code' => 00, 'data' => $chatBotQuestion, 'message' => $e->getMessage()]);
                }
            } else {
                if ($request->object === 'customer') {
                    $customer = Customer::find($request->object_id);
                    $googleAccount = GoogleDialogAccount::where('id', $customer->store_website_id)->first();
                } else {
                    $googleAccount = GoogleDialogAccount::where('default_selected', 1)->first();
                    if (empty($google_accounts)) {
                        $googleAccount = GoogleDialogAccount::with('storeWebsite')->first();
                    }
                }
                $chatBotQuestion = ChatbotQuestion::where('value', $request->question_id)->first();
                if (! $chatBotQuestion) {
                    $chatBotQuestion = new ChatbotQuestion;
                    $chatBotQuestion->keyword_or_question = 'intent';
                    $chatBotQuestion->value = $request->question_id;
                    $chatBotQuestion->auto_approve = 0;
                    $chatBotQuestion->suggested_reply = $request->value;
                    $chatBotQuestion->google_account_id = $googleAccount->id;
                    $chatBotQuestion->is_active = 1;
                    $chatBotQuestion->save();
                }
                $storeQuestion = new ChatbotQuestionExample;
                $storeQuestion->question = $request->value;
                $storeQuestion->chatbot_question_id = $chatBotQuestion->id;
                $storeQuestion->save();
                $questionArr[] = $request->value;
                $dialogService = new DialogFlowService($googleAccount);
                try {
                    $response = $dialogService->createIntent([
                        'questions' => $questionArr,
                        'reply' => explode(',', $chatBotQuestion->suggested_reply),
                        'name' => $chatBotQuestion->value,
                        'parent' => $chatBotQuestion->parent,
                    ], $chatBotQuestion->google_response_id ?: null);

                    if ($response) {
                        $name = explode('/', $response);
                        $chatBotQuestion->google_status = '1';
                        $chatBotQuestion->save();

                        $store_response = new GoogleResponseId;
                        $store_response->google_response_id = $name[count($name) - 1];
                        $store_response->google_dialog_account_id = $googleAccount->id;
                        $store_response->chatbot_question_id = $chatBotQuestion->id;
                        $store_response->save();
                    }

                    return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Intent Store successfully']);
                } catch (Exception $e) {
                    $chatBotQuestion->google_status = $e->getMessage();
                    $chatBotQuestion->save();
                }

                return response()->json(['code' => 00, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['code' => 00, 'data' => null, 'message' => 'Question not found']);
    }

    public function storeReplay(Request $request): JsonResponse
    {
        if ($request->object === 'customer') {
            $customer = Customer::find($request->object_id);
            $googleAccount = GoogleDialogAccount::where('id', $customer->store_website_id)->first();
        } else {
            $googleAccount = GoogleDialogAccount::where('default_selected', 1)->first();
            if (empty($google_accounts)) {
                $googleAccount = GoogleDialogAccount::with('storeWebsite')->first();
            }
        }
        $chatBotQuestion = ChatbotQuestion::where('id', $request->question_id)->first();
        $questionArr = [];
        $replyArr = [];
        if ($chatBotQuestion) {
            foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                $questionArr[] = $question->question;
            }
            $replyArr = explode(',', $chatBotQuestion->suggested_reply);
            foreach ($chatBotQuestion->chatbotQuestionReplies as $reply) {
                $replyArr[] = $reply->suggested_reply;
            }
            $googleAccount = GoogleDialogAccount::where('id', $chatBotQuestion->google_account_id)->first();
            $chatRply = new ChatbotQuestionReply;
            $chatRply->suggested_reply = $request->value;
            $chatRply->store_website_id = $googleAccount->site_id;
            $chatRply->chatbot_question_id = $chatBotQuestion->id;
            $chatRply->save();
            $replyArr[] = $request->value;
            $dialogService = new DialogFlowService($googleAccount);
            try {
                $response = $dialogService->createIntent([
                    'questions' => $questionArr,
                    'reply' => $replyArr,
                    'name' => $chatBotQuestion['value'],
                    'parent' => $chatBotQuestion['parent'],
                ], $chatBotQuestion->google_response_id ?: null);
                if ($response) {
                    $name = explode('/', $response);
                    $chatBotQuestion->google_status = '1';
                    $chatBotQuestion->save();

                    $store_response = new GoogleResponseId;
                    $store_response->google_response_id = $name[count($name) - 1];
                    $store_response->google_dialog_account_id = $googleAccount->id;
                    $store_response->chatbot_question_id = $chatBotQuestion->id;
                    $store_response->save();
                }

                return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Reply Stored successfully']);
            } catch (Exception $e) {
                $chatBotQuestion->google_status = $e->getMessage();
                $chatBotQuestion->save();

                return response()->json(['code' => 400, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['code' => 400, 'data' => null, 'message' => 'Question not found']);
    }

    public function chatbotMessagesColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'chatbot-messages')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'chatbot-messages';
            $column->column_name = json_encode($request->column_chatbox);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'chatbot-messages';
            $column->column_name = json_encode($request->column_chatbox);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function messagesJson(Request $request): JsonResponse
    {
        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        if (! empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where('cr.question', 'like', '%'.$search.'%')->orWhere('cr.answer', 'Like', '%'.$search.'%');
            });
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (! empty($unreplied_msg)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where('cm1.message', null);
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where('chat_messages.approved', $status);
            });
        }

        if (request('unread_message') == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->where('cr.is_read', 0);
            });
        }

        if (request('message_type') != null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (request('message_type') == 'email') {
                    $q->where('chat_messages.is_email', '>', 0);
                }
                if (request('message_type') == 'task') {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
                if (request('message_type') == 'dev_task') {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (request('message_type') == 'ticket') {
                    $q->orWhere('chat_messages.ticket_id', '>', 0);
                }
            });
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (in_array('customer', request('search_type'))) {
                    $q->where('chat_messages.customer_id', '>', 0);
                }
                if (in_array('vendor', request('search_type'))) {
                    $q->orWhere('chat_messages.vendor_id', '>', 0);
                }
                if (in_array('supplier', request('search_type'))) {
                    $q->orWhere('chat_messages.supplier_id', '>', 0);
                }
                if (in_array('dev_task', request('search_type'))) {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (in_array('task', request('search_type'))) {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
            });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select([
            'cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.id', 'chat_messages.message', 'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.ticket_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.issue_id', 'chat_messages.approved', 'chat_messages.is_audio', 'chat_messages.is_email', 'chat_messages.from_email', 'chat_messages.to_email', 'chat_messages.cc_email', 'chat_messages.email_id', 'chat_messages.message_type', 'chat_messages.message_type_id',
            'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator',
        ])
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->paginate(20);
        // dd($pendingApprovalMsg);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        $reply_categories = ReplyCategory::with('approval_leads')->orderby('name')->get();

        return response()->json(['code' => 200, 'items' => (array) $pendingApprovalMsg->getIterator(), 'page' => $page]);
    }

    public function indexDB(Request $request, $isElastic)
    {
        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            // ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            // ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            // ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            // ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            // ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id');

        $pendingApprovalMsg->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id', 'chat_messages.message_type', 'chat_messages.message_type_id']); //Purpose : Add task_id - DEVTASK-4203

        if (! empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where('cr.question', 'like', '%'.$search.'%')->orWhere('cr.answer', 'Like', '%'.$search.'%');
            });
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if ($unreplied_msg == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($query) {
                $query->where('cm1.message', null);
                $query->orWhere('cm1.message', ''); // Changes of task 24965
            });
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null && $status !== '') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where('chat_messages.approved', $status);
            });
        }

        if (request('unread_message') == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->where('cr.is_read', 0);
            });
        }

        if (request('message_type') != null && request('message_type') != '') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (request('message_type') == 'task') {
                    $q->where('chat_messages.task_id', '>', 0);
                } elseif (request('message_type') == 'dev_task') {
                    $q->where('chat_messages.developer_task_id', '>', 0);
                } elseif (request('message_type') == 'ticket') {
                    $q->where('chat_messages.ticket_id', '>', 0);
                } elseif (request('message_type') == 'email') {
                    $q->where('chat_messages.email_id', '>', 0);
                } elseif (request('message_type') == 'FB_DMS') {
                    $q->where('chat_messages.message_type', '=', 'FB_DMS');
                } elseif (request('message_type') == 'IG_DMS') {
                    $q->where('chat_messages.message_type', '=', 'IG_DMS');
                } elseif (request('message_type') == 'FB_COMMENT') {
                    $q->where('chat_messages.message_type', '=', 'FB_COMMENT');
                } elseif (request('message_type') == 'IG_COMMENT') {
                    $q->where('chat_messages.message_type', '=', 'IG_COMMENT');
                }
            });
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (in_array('customer', request('search_type'))) {
                    $q->where(function ($query) {
                        $query->where('chat_messages.customer_id', '>', 0);
                        $query->whereNotNull('chat_messages.customer_id');
                    });
                }
                if (in_array('vendor', request('search_type'))) {
                    $q->where(function ($query) {
                        $query->where('chat_messages.vendor_id', '>', 0);
                        $query->whereNotNull('chat_messages.vendor_id');
                    });
                }
                if (in_array('supplier', request('search_type'))) {
                    $q->where(function ($query) {
                        $query->where('chat_messages.supplier_id', '>', 0);
                        $query->whereNotNull('chat_messages.supplier_id');
                    });
                }
                if (in_array('dev_task', request('search_type'))) {
                    $q->where(function ($query) {
                        $query->where('chat_messages.developer_task_id', '>', 0);
                        $query->whereNotNull('chat_messages.developer_task_id');
                    });
                }
                if (in_array('task', request('search_type'))) {
                    $q->where(function ($query) {
                        $query->where('chat_messages.task_id', '>', 0);
                        $query->whereNotNull('chat_messages.task_id');
                    });
                }
            });
        }

        if (! empty(request('customer_id'))) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->whereIn('chat_messages.customer_id', request('customer_id'));
            });
        }

        /*   $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)'); */

        $currentPage = Paginator::resolveCurrentPage();
        /* $select      = ['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator',
            'chat_messages.message','chat_messages.customer_id','chat_messages.supplier_id','chat_messages.vendor_id','chat_messages.user_id','chat_messages.ticket_id','chat_messages.task_id','chat_messages.developer_task_id','chat_messages.bug_id',
            'chat_messages.issue_id','chat_messages.approved','chat_messages.is_audio','chat_messages.is_email','chat_messages.from_email','chat_messages.to_email','chat_messages.cc_email','chat_messages.email_id','chat_messages.message_type',
            'chat_messages.id AS cid']; */

        $select = [
            'chat_messages.id AS cid', 'cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'cr.reply_from', 'cr.suggested_reply',
            'chat_messages.message', 'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.ticket_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id',
            'chat_messages.issue_id', 'chat_messages.approved', 'chat_messages.is_audio', 'chat_messages.is_email', 'chat_messages.from_email', 'chat_messages.to_email', 'chat_messages.cc_email', 'chat_messages.email_id', 'chat_messages.message_type', 'chat_messages.message_type_id',
        ];

        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select($select)
            ->orderByRaw('chat_messages.id DESC');

        $pendingApprovalMsg = $pendingApprovalMsg->select([...$select])->paginate(20);

        $customerIds = $pendingApprovalMsg->pluck('customer_id')->toArray();
        $uniqueCustomerIds = collect($customerIds)->filter()->unique()->values()->all();

        $vendorIds = $pendingApprovalMsg->pluck('vendor_id')->toArray();
        $uniqueVendorIds = collect($vendorIds)->filter()->unique()->values()->all();

        $supplierIds = $pendingApprovalMsg->pluck('supplier_id')->toArray();
        $uniqueSupplierIds = collect($supplierIds)->filter()->unique()->values()->all();

        $emailIds = $pendingApprovalMsg->pluck('email_id')->toArray();
        $uniqueEmailIds = collect($emailIds)->filter()->unique()->values()->all();

        $storeWebsiteIds = [];
        $customerArr = [];
        if (count($uniqueCustomerIds) > 0) {
            $customers = Customer::whereIn('id', $uniqueCustomerIds)->select('id', 'name', 'is_auto_simulator', 'do_not_disturb', 'store_website_id')->get()->toArray();
            foreach ($customers as $customer) {
                $customerArr[$customer['id']]['customer_name'] = $customer['name'];
                $customerArr[$customer['id']]['customer_auto_simulator'] = $customer['is_auto_simulator'];
                $customerArr[$customer['id']]['customer_do_not_disturb'] = $customer['do_not_disturb'];
                $customerArr[$customer['id']]['store_website_id'] = $customer['store_website_id'];
                if ($customer['store_website_id'] > 0) {
                    $storeWebsiteIds[] = $customer['store_website_id'];
                }
            }
        }

        $storeWebsiteArr = [];
        if (count($storeWebsiteIds) > 0) {
            $storeWebsites = StoreWebsite::whereIn('id', $storeWebsiteIds)->select('id', 'title')->get()->toArray();
            foreach ($storeWebsites as $storeWebsite) {
                $storeWebsiteArr[$storeWebsite['id']]['website_title'] = $storeWebsite['title'];
            }
        }

        $emailArr = [];
        if (count($uniqueEmailIds) > 0) {
            $emails = Email::whereIn('id', $uniqueEmailIds)->select('id', 'name')->get()->toArray();
            foreach ($emails as $email) {
                $emailArr[$email['id']]['from_name'] = $email['name'];
            }
        }

        $vendorArr = [];
        if (count($uniqueVendorIds) > 0) {
            $vendors = Vendor::whereIn('id', $uniqueVendorIds)->select('id', 'name', 'is_auto_simulator')->get()->toArray();
            foreach ($vendors as $vendor) {
                $vendorArr[$vendor['id']]['vendor_name'] = $vendor['name'];
                $vendorArr[$vendor['id']]['vendor_auto_simulator'] = $vendor['is_auto_simulator'];
            }
        }
        $supplierArr = [];
        if (count($uniqueSupplierIds) > 0) {
            $suppliers = Supplier::whereIn('id', $uniqueSupplierIds)->select('id', 'supplier', 'is_auto_simulator')->get()->toArray();
            foreach ($suppliers as $supplier) {
                $supplierArr[$supplier['id']]['supplier_name'] = $supplier['supplier'];
                $supplierArr[$supplier['id']]['supplier_auto_simulator'] = $supplier['is_auto_simulator'];
            }
        }

        $pendingApprovalMsg->getCollection()->transform(function ($item) use ($emailArr, $customerArr, $vendorArr, $supplierArr, $storeWebsiteArr) {
            $item['from_name'] = $emailArr[$item->email_id]['from_name'] ?? '';
            $item['customer_name'] = $customerArr[$item->customer_id]['customer_name'] ?? '';
            $item['customer_auto_simulator'] = $customerArr[$item->customer_id]['customer_auto_simulator'] ?? '';
            $item['customer_do_not_disturb'] = $customerArr[$item->customer_id]['customer_do_not_disturb'] ?? '';
            $item['vendors_name'] = $vendorArr[$item->vendor_id]['vendor_name'] ?? '';
            $item['vendor_auto_simulator'] = $vendorArr[$item->vendor_id]['vendor_auto_simulator'] ?? '';
            $item['supplier_name'] = $supplierArr[$item->supplier_id]['supplier_name'] ?? '';
            $item['supplier_auto_simulator'] = $supplierArr[$item->supplier_id]['supplier_auto_simulator'] ?? '';
            $item['website_title'] = '';

            if (isset($customerArr[$item->customer_id]) && isset($customerArr[$item->customer_id]['store_website_id'])) {
                $item['website_title'] = $storeWebsiteArr[$customerArr[$item->customer_id]['store_website_id']]['website_title'] ?? '';
            }

            return $item;
        });

        $allCategoryList = ChatbotCategory::select('id', 'name as text')->get()->toArray();

        $page = $currentPage;
        $reply_categories = ReplyCategory::with('approval_leads')->orderby('name')->get();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'chatbot-messages')->first();
        $dynamicColumnsToShowPostman = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPostman = json_decode($hideColumns, true);
        }

        $selectedCustomer = [];
        if (! empty(request('customer_id'))) {
            $selectedCustomer = Customer::select('id', 'name')->whereIn('id', request('customer_id'))->get()->toArray();
        }
        // dd($selectedCustomer);

        $assigned_to = User::with('roles')->get();
        $statuses = TicketStatuses::all();
        if ($request->ajax()) {
            $tml = (string) view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'allCategoryList', 'reply_categories', 'isElastic', 'customerArr', 'storeWebsiteArr', 'emailArr', 'vendorArr', 'supplierArr', 'dynamicColumnsToShowPostman', 'selectedCustomer', 'assigned_to', 'statuses'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();

        // dd($pendingApprovalMsg[0]);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'isElastic', 'customerArr', 'storeWebsiteArr', 'emailArr', 'vendorArr', 'supplierArr', 'dynamicColumnsToShowPostman', 'selectedCustomer', 'assigned_to', 'statuses'));
    }

    public function sendMessageReply(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required',
                'message' => 'required',
                'receiver_email' => 'required',
                'sender_email_address' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            }

            $emailsLog = Email::create([
                'model_id' => 0,
                'model_type' => Email::class,
                'from' => $request->sender_email_address,
                'to' => $request->receiver_email,
                'subject' => $request->subject,
                'message' => $request->message,
                'template' => 'reply-email',
                'additional_data' => '',
                'status' => 'pre-send',
                'store_website_id' => null,
                'is_draft' => 1,
            ]);

            $data = [
                'status' => 1,
                'message' => $request->message,
                'from_email' => $request->sender_email_address,
                'to_email' => $request->receiver_email,
                'cc_email' => $request->cc_email ?? '',
                'email_id' => $emailsLog->id,
                'is_email' => 1,
            ];

            ChatMessage::create($data);

            SendEmail::dispatch($emailsLog)->onQueue('send_email');

            return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'errors' => 'Failed to send the email. Please try again later.']);
        }
    }
}
