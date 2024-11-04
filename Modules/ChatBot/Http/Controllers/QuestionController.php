<?php

namespace Modules\ChatBot\Http\Controllers;

use App\ChatbotCategory;
use App\ChatbotErrorLog;

use App\ChatbotIntentsAnnotation;
use App\ChatbotKeywordValue;
use App\ChatbotKeywordValueTypes;
use App\ChatbotQuestion;
use App\ChatbotQuestionErrorLog;
use App\ChatbotQuestionExample;
use App\ChatbotQuestionReply;
use App\Customer;
use App\DeveloperModule;
use App\Github\GithubRepository;
use App\Library\Google\DialogFlow\DialogFlowService;
use App\Library\Watson\Model as WatsonManager;
use App\MailinglistTemplate;
use App\Models\DialogflowEntityType;
use App\Models\GoogleDialogAccount;
use App\Models\GoogleResponseId;
use App\ScheduledMessage;
use App\StoreWebsite;
use App\TaskCategories;
use App\User;
use App\WatsonAccount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $q = request('q', '');
        $category_id = request('category_id', 0);
        $keyword_or_question = request('keyword_or_question', 'intent');
        $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
            ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
            ->where('keyword_or_question', $keyword_or_question)
            ->select('chatbot_questions.*', DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name');
        if (! empty($q)) {
            $chatQuestions = $chatQuestions->where(function ($query) use ($q) {
                $query->where('chatbot_questions.value', 'like', '%'.$q.'%')->orWhere('cqe.question', 'like', '%'.$q.'%');
            });
        }

        if (! empty($category_id)) {
            $chatQuestions = $chatQuestions->where('cc.id', $category_id);
        }

        $chatQuestions = $chatQuestions->groupBy('chatbot_questions.id')
            ->orderByDesc('chatbot_questions.id')
            ->paginate(24)->appends(request()->except(['page', '_token']));

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();

        $task_category = TaskCategories::select('*')->get();
        $userslist = User::select('*')->get();

        $moduleNames = [];
        // Get all modules
        $modules = DeveloperModule::all();
        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[$module->id] = $module->name;
        }
        $respositories = GithubRepository::all();

        $templates = MailinglistTemplate::all();
        $watson_accounts = WatsonAccount::all();
        $google_accounts = GoogleDialogAccount::all();
        $store_websites = StoreWebsite::all();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();

        //dd($chatQuestions);
        return view('chatbot::question.index', compact('chatQuestions', 'allCategoryList', 'watson_accounts', 'task_category', 'userslist', 'modules', 'respositories', 'templates', 'store_websites', 'google_accounts', 'allEntityType', 'variables', 'parentIntents'));
    }

    public function create(): View
    {
        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }

        return view('chatbot::question.create', compact('allCategoryList'));
    }

    public function save(Request $request): JsonResponse
    {
        $params = $request->all();
        $params['value'] = str_replace(' ', '_', $params['value']);
        $params['watson_account_id'] = $request->watson_account;
        $params['google_account_id'] = $request->google_account;
        $validator = Validator::make($params, [
            'value' => 'required|unique:chatbot_questions|max:255',
            'keyword_or_question' => 'required',
            'watson_account' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => $validator->errors()]);
        }

        if ($request->has('parent') && $request->get('parent')) {
            $params['parent'] = $request->get('parent');
        }
        if ($request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {
            $validator = Validator::make($request->all(), [
                'keyword' => 'sometimes|nullable|string',
                'suggested_reply' => 'required|min:3|string',
                'sending_time' => 'sometimes|nullable|date',
                'repeat' => 'sometimes|nullable|string',
                'is_active' => 'sometimes|nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 500, 'error' => $validator->errors()]);
            }
        }
        $params['erp_or_watson'] = 'erp';
        $chatbotQuestion = ChatbotQuestion::create($params);
        if (! empty($params['question'])) {
            foreach ($params['question'] as $qu) {
                if ($qu) {
                    $params['chatbot_question_id'] = $chatbotQuestion->id;
                    $chatbotQuestionExample = new ChatbotQuestionExample;
                    $chatbotQuestionExample->question = $qu;
                    $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                    $chatbotQuestionExample->save();
                }
            }
        }

        if (array_key_exists('types', $params) && $params['types'] != null && array_key_exists('type', $params) && $params['type'] != null) {
            $chatbotQuestionExample = null;
            if (! empty($params['value_name'])) {
                $chatbotQuestionExample = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = $params['value_name'];
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->types = $params['types'];
                $chatbotQuestionExample->save();
            }

            if ($chatbotQuestionExample) {
                $valueType = [];
                $valueType['chatbot_keyword_value_id'] = $chatbotQuestionExample->id;
                if (! empty($params['type'])) {
                    foreach ($params['type'] as $value) {
                        if ($value != null) {
                            $valueType['type'] = $value;
                            $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                            $chatbotKeywordValueTypes->fill($valueType);
                            $chatbotKeywordValueTypes->save();
                        }
                    }
                }
            }
        }

        if (array_key_exists('entity_type', $params) && $params['entity_type'] != null && array_key_exists('entity_types', $params) && $params['entity_types'] != null) {
            $chatbotQuestionExample = null;
            if (! empty($params['value_name'])) {
                $chatbotQuestionExample = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = $params['value_name'];
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->types = $params['entity_type'];
                $chatbotQuestionExample->save();
            }
            if (! empty($params['entity_types'])) {
                foreach ($params['entity_types'] as $value) {
                    if ($value != null) {
                        $chatbotQuestionExample = new ChatbotQuestionExample;
                        $chatbotQuestionExample->question = $value;
                        $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                        $chatbotQuestionExample->types = $params['entity_type'];
                        $chatbotQuestionExample->save();
                    }
                }
            }
        }

        if ($request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {
            $exploded = explode(',', $request->keyword);

            foreach ($exploded as $keyword) {
                $chatbotQuestionExample = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = trim($keyword);
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->save();
            }

            if ($request->type == 'priority-customer') {
                if ($request->repeat == '') {
                    $customers = Customer::where('is_priority', 1)->get();

                    foreach ($customers as $customer) {
                        ScheduledMessage::create([
                            'user_id' => Auth::id(),
                            'customer_id' => $customer->id,
                            'message' => $chatbotQuestion->suggested_reply,
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

        $storeWebsites = [];
        foreach ($wotson_account_ids as $id) {
            $storeWebsites[] = $id->store_website_id;
            $data_to_insert[] = [
                'suggested_reply' => $params['suggested_reply'],
                'store_website_id' => $id->store_website_id,
                'chatbot_question_id' => $chatbotQuestion->id,
            ];
        }
        $gogole_account_ids = GoogleDialogAccount::where('id', $request->google_account)->get();
        foreach ($gogole_account_ids as $id) {
            if (! in_array($id->site_id, $storeWebsites)) {
                $data_to_insert[] = [
                    'suggested_reply' => ! empty($params['suggested_reply']) ? $params['suggested_reply'] : '',
                    'store_website_id' => $id->site_id,
                    'chatbot_question_id' => $chatbotQuestion->id,
                ];
            }
        }
        ChatbotQuestionReply::insert($data_to_insert);

        $route = route('chatbot.question.edit', [$chatbotQuestion->id]);

        return response()->json(['code' => 200, 'data' => $chatbotQuestion, 'redirect' => $route]);
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        if ($id > 0) {
            $chatbotQuestion = ChatbotQuestion::where('id', $id)->first();
            if ($chatbotQuestion) {
                ChatbotQuestionExample::where('chatbot_question_id', $id)->delete();
                $chatbotQuestion->delete();
                WatsonManager::deleteQuestion($chatbotQuestion->id);
                $googleAccount = GoogleDialogAccount::where('id', $chatbotQuestion->google_account_id)->first();
                if ($googleAccount) {
                    $dialgflowService = new DialogFlowService($googleAccount);
                    $dialgflowService->deleteQuestion($chatbotQuestion);
                }

                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    public function edit(Request $request, $id): View
    {
        $chatbotQuestion = ChatbotQuestion::where('id', $id)->first();
        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }

        $task_category = TaskCategories::select('*')->get();
        $userslist = User::select('*')->get();

        $moduleNames = [];
        // Get all modules
        $modules = DeveloperModule::all();
        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[$module->id] = $module->name;
        }
        $respositories = GithubRepository::all();
        $templates = MailinglistTemplate::all();
        $watson_accounts = WatsonAccount::all();
        if ($request->store_website_id) {
            $replies = $chatbotQuestion->chatbotQuestionReplies()->where('store_website_id', $request->store_website_id)->get();
        } else {
            $replies = $chatbotQuestion->chatbotQuestionReplies()->get();
        }

        return view('chatbot::question.edit', compact('chatbotQuestion', 'allCategoryList', 'task_category', 'userslist', 'moduleNames', 'respositories', 'modules', 'templates', 'watson_accounts', 'replies'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $params = $request->all();
        $params['value'] = str_replace(' ', '_', $params['value']);
        $params['chatbot_question_id'] = $id;

        $chatbotQuestion = ChatbotQuestion::where('id', $id)->first();

        if ($chatbotQuestion) {
            $oldvalue = $chatbotQuestion->value;
            if ($chatbotQuestion->keyword_or_question == 'intent') {
                $validator = Validator::make($params, [
                    'question' => 'required|unique:chatbot_question_examples',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $chatbotQuestion->fill($params);
                $chatbotQuestion->save();
                if (! empty($params['category_id'])) {
                    if (is_numeric($params['category_id'])) {
                        $chatbotQuestion->category_id = $params['category_id'];
                        $chatbotQuestion->save();
                    } else {
                        $catModel = ChatbotCategory::create([
                            'name' => $params['category_id'],
                        ]);

                        if ($catModel) {
                            $chatbotQuestion->category_id = $catModel->id;
                            $chatbotQuestion->save();
                        }
                    }
                }

                if (! empty($params['question'])) {
                    $chatbotQuestionExample = new ChatbotQuestionExample;
                    $chatbotQuestionExample->fill($params);
                    $chatbotQuestionExample->save();
                }
            } elseif ($chatbotQuestion->keyword_or_question == 'entity') {
                $validator = Validator::make($params, [
                    'question' => 'required|unique:chatbot_question_examples',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $chatbotQuestion->fill($params);
                $chatbotQuestion->save();
                if (! empty($params['category_id'])) {
                    if (is_numeric($params['category_id'])) {
                        $chatbotQuestion->category_id = $params['category_id'];
                        $chatbotQuestion->save();
                    } else {
                        $catModel = ChatbotCategory::create([
                            'name' => $params['category_id'],
                        ]);

                        if ($catModel) {
                            $chatbotQuestion->category_id = $catModel->id;
                            $chatbotQuestion->save();
                        }
                    }
                }

                if (! empty($params['question'])) {
                    $chatbotQuestionExample = new ChatbotQuestionExample;
                    $chatbotQuestionExample->question = $params['question'];
                    $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                    $chatbotQuestionExample->types = $params['types'];
                    $chatbotQuestionExample->save();
                }

                if ($chatbotQuestionExample) {
                    $valueType = [];
                    $valueType['chatbot_keyword_value_id'] = $chatbotQuestionExample->id;
                    if (! empty($params['type'])) {
                        foreach ($params['type'] as $value) {
                            if ($value != null) {
                                $valueType['type'] = $value;
                                $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                                $chatbotKeywordValueTypes->fill($valueType);
                                $chatbotKeywordValueTypes->save();
                            }
                        }
                    }
                }
            }
            if ($chatbotQuestion->keyword_or_question == 'simple' || $chatbotQuestion->keyword_or_question == 'priority-customer') {
                $validator = Validator::make($request->all(), [
                    'keyword' => 'required|string',
                    'sending_time' => 'sometimes|nullable|date',
                    'repeat' => 'sometimes|nullable|string',
                    'is_active' => 'sometimes|nullable|integer',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $chatbotQuestion->fill($params);
                $chatbotQuestion->save();

                $chatbotQuestionExample = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = trim($params['keyword']);
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->save();
            }
        }

        return redirect()->back();
    }

    public function destroyValue(Request $request, $id, $valueId): RedirectResponse
    {
        $chQuestion = ChatbotQuestion::where('id', $id)->first();
        $cbValue = ChatbotQuestionExample::where('chatbot_question_id', $id)->where('id', $valueId)->first();
        $chatbotQuestion = ChatbotQuestion::where('id', $id)->first();
        if ($cbValue) {
            $cbValue->delete();
            if ($chatbotQuestion->keyword_or_question == 'intent' && $chatbotQuestion->erp_or_watson == 'watson') {
                WatsonManager::pushQuestion($id, $chQuestion->value);
            }
            if ($chatbotQuestion->keyword_or_question == 'entity' && $chatbotQuestion->erp_or_watson == 'watson') {
                WatsonManager::pushQuestion($id, $chQuestion->value);
            }
        }

        return redirect()->back();
    }

    public function saveAjax(Request $request): JsonResponse
    {
        $groupId = $request->get('group_id');
        $name = $request->get('name', '');
        $question = $request->get('question');
        $category_id = $request->get('category_id');
        $erp_or_watson = $request->get('erp_or_watson');
        $keyword_or_question = $request->get('keyword_or_question');
        if (! $erp_or_watson) {
            $erp_or_watson = 'erp';
        }

        if (! $keyword_or_question) {
            $keyword_or_question = 'intent';
        }
        // if (!empty($groupId) && $groupId > 0) {
        //     $chQuestion = ChatbotQuestion::where("id", $groupId)->first();
        //     $q = ChatbotQuestionExample::updateOrCreate(
        //         ["chatbot_question_id" => $groupId, "question" => $question],
        //         ["chatbot_question_id" => $groupId, "question" => $question]
        //     );
        //     WatsonManager::pushQuestion($groupId);
        //     if($request->suggested_reply && $request->suggested_reply != '') {
        //         $chQuestion->suggested_reply = $request->suggested_reply;
        //         $chQuestion->save();
        //     }
        // } else if (!empty($name)) {
        //     $chQuestion = null;

        //     if (is_numeric($name)) {
        //         $chQuestion = ChatbotQuestion::where("id", $name)->first();
        //     }

        //     if (!$chQuestion) {
        //         $chQuestion = ChatbotQuestion::create([
        //             "value" => str_replace(" ", "_", preg_replace('/\s+/', ' ', $name)),
        //         ]);

        //         if (!empty($category_id)) {
        //             if (is_numeric($category_id)) {
        //                 $chQuestion->category_id = $category_id;
        //                 $chQuestion->save();
        //             } else {
        //                 $catModel = ChatbotCategory::create([
        //                     "name" => $category_id,
        //                 ]);

        //                 if ($catModel) {
        //                     $chQuestion->category_id = $catModel->id;
        //                     $chQuestion->save();
        //                 }
        //             }
        //         }
        //     }

        //     if($request->suggested_reply && $request->suggested_reply != '') {
        //         $chQuestion->suggested_reply = $request->suggested_reply;
        //         $chQuestion->save();
        //     }

        //     $groupId = $chQuestion->id;

        //     if (is_string($question)) {
        //         ChatbotQuestionExample::create(
        //             ["chatbot_question_id" => $chQuestion->id, "question" => preg_replace("/\s+/", " ", $question)]
        //         );
        //     } elseif (is_array($question)) {
        //         foreach ($question as $key => $qRaw) {
        //             ChatbotQuestionExample::create(
        //                 ["chatbot_question_id" => $chQuestion->id, "question" => preg_replace("/\s+/", " ", $qRaw)]
        //             );
        //         }
        //     }
        // }

        $chQuestion = null;

        if (is_numeric($groupId)) {
            $chQuestion = ChatbotQuestion::where('id', $groupId)->first();
        } else {
            if ($groupId != '') {
                $chQuestion = ChatbotQuestion::create([
                    'value' => str_replace(' ', '_', preg_replace('/\s+/', ' ', $groupId)),
                ]);
            }
        }
        if ($chQuestion) {
            $oldvalue = $chQuestion->value;
            if (! empty($category_id)) {
                if (is_numeric($category_id)) {
                    $chQuestion->category_id = $category_id;
                    $chQuestion->save();
                } else {
                    $catModel = ChatbotCategory::create([
                        'name' => $category_id,
                    ]);

                    if ($catModel) {
                        $chQuestion->category_id = $catModel->id;
                        $chQuestion->save();
                    }
                }
            }
            if (! $chQuestion->keyword_or_question || $chQuestion->keyword_or_question == '') {
                $chQuestion->keyword_or_question = $keyword_or_question;
            }
            $chQuestion->erp_or_watson = $erp_or_watson;
            $chQuestion->save();
            if ($request->suggested_reply && $request->suggested_reply != '') {
                $chQuestion->suggested_reply = $request->suggested_reply;
                $chQuestion->save();
            }

            $groupId = $chQuestion->id;

            if (is_string($question)) {
                ChatbotQuestionExample::create(
                    ['chatbot_question_id' => $chQuestion->id, 'question' => preg_replace("/\s+/", ' ', $question)]
                );
            } elseif (is_array($question)) {
                foreach ($question as $key => $qRaw) {
                    ChatbotQuestionExample::create(
                        ['chatbot_question_id' => $chQuestion->id, 'question' => preg_replace("/\s+/", ' ', $qRaw)]
                    );
                }
            }

            if ($groupId > 0 && $erp_or_watson == 'watson') {
                WatsonManager::pushQuestion($groupId, $oldvalue);
            }
            $question = ChatbotQuestion::where('keyword_or_question', 'intent')->select(DB::raw("concat('#','',value) as value"))->get()->pluck('value', 'value')->toArray();
            $keywords = ChatbotQuestion::where('keyword_or_question', 'entity')->select(DB::raw("concat('@','',value) as value"))->get()->pluck('value', 'value')->toArray();

            $allSuggestedOptions = $keywords + $question;

            return response()->json(['code' => 200, 'allSuggestedOptions' => $allSuggestedOptions]);
        } else {
            return response()->json(['code' => 500, 'message' => 'Please select an intent or write a new one.']);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = request('term', '');
        $allquestion = ChatbotQuestion::where('value', 'like', '%'.$keyword.'%')->limit(10)->get();

        $allquestionList = [];
        if (! $allquestion->isEmpty()) {
            foreach ($allquestion as $all) {
                $allquestionList[] = ['id' => $all->id, 'text' => $all->value, 'suggested_reply' => $all->suggested_reply];
            }
        }

        return response()->json(['incomplete_results' => false, 'items' => $allquestionList, 'total_count' => count($allquestionList)]);
    }

    public function getCategories(): JsonResponse
    {
        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }

        return response()->json(['incomplete_results' => false, 'items' => $allCategoryList, 'total_count' => count($allCategoryList)]);
    }

    public function saveAnnotation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'chatbot_question_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => []]);
        }

        $model = ChatbotIntentsAnnotation::updateOrCreate(
            [
                'question_example_id' => $request->get('question_example_id'),
                'chatbot_keyword_id' => $request->get('chatbot_question_id'),
                'start_char_range' => $request->get('start_char_range'),
                'end_char_range' => $request->get('end_char_range'),
            ],
            $request->all()
        );

        if ($model) {
            // $chatbotKeywordValue = ChatbotKeywordValue::create([
            //     "chatbot_keyword_id" => $model->chatbot_keyword_id,
            //     "value"              => $request->get("keyword_value"),
            // ]);
            $chatbotQuestionExample = ChatbotQuestionExample::create([
                'chatbot_question_id' => $model->chatbot_keyword_id,
                'question' => $request->get('keyword_value'),
            ]);

            $model->chatbot_value_id = $chatbotQuestionExample->id;
            $model->save();
            WatsonManager::pushValue($model->question_example_id);
        }

        return response()->json(['code' => 200]);
    }

    public function deleteAnnotation(Request $request): JsonResponse
    {
        $annotationId = $request->get('id');
        $annotation = ChatbotIntentsAnnotation::where('id', $annotationId)->first();

        if ($annotation) {
            $questionExample = $annotation->question_example_id;
            $annotation->delete();
            WatsonManager::pushValue($questionExample);

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'message' => 'No record founds']);
    }

    public function searchCategory(Request $request): JsonResponse
    {
        $keyword = request('term', '');
        $allCategory = ChatbotCategory::where('name', 'like', '%'.$keyword.'%')->limit(10)->get();

        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }

        return response()->json(['incomplete_results' => false, 'items' => $allCategoryList, 'total_count' => count($allCategoryList)]);
    }

    public function changeCategory(Request $request): JsonResponse
    {
        if ($request->category_id && $request->id) {
            $chatbotQuestion = ChatbotQuestion::find($request->id);
            if ($chatbotQuestion) {
                $chatbotQuestion->category_id = $request->category_id;
                $chatbotQuestion->save();

                return response()->json(['message' => 'Success'], 200);
            }
        }

        return response()->json(['message' => 'Question or category not found'], 500);
    }

    public function searchKeyword(Request $request): JsonResponse
    {
        $keyword = request('term', '');
        $allKeyword = ChatbotQuestion::where('value', 'like', '%'.$keyword.'%')->limit(10)->get();

        $allKeywordList = [];
        if (! $allKeyword->isEmpty()) {
            foreach ($allKeyword as $all) {
                $allKeywordList[] = ['id' => $all->id, 'text' => $all->value];
            }
        }

        return response()->json(['incomplete_results' => false, 'items' => $allKeywordList, 'total_count' => count($allKeywordList)]);
    }

    public function saveAutoreply(Request $request)
    {
        // $this->validate($request, [
        //     'type'         => 'required|string',
        //     'keyword'      => 'sometimes|nullable|string',
        //     'reply'        => 'required|min:3|string',
        //     'sending_time' => 'sometimes|nullable|date',
        //     'repeat'       => 'sometimes|nullable|string',
        //     'is_active'    => 'sometimes|nullable|integer',
        // ]);

        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'keyword' => 'sometimes|nullable|string',
            'reply' => 'required|min:3|string',
            'sending_time' => 'sometimes|nullable|date',
            'repeat' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => []]);
        }

        $exploded = explode(',', $request->keyword);

        foreach ($exploded as $keyword) {
            $chatbotQuestion = new ChatbotQuestion;
            $chatbotQuestion->keyword_or_question = $request->type;
            $chatbotQuestion->value = trim($keyword);
            $chatbotQuestion->suggested_reply = $request->reply;
            $chatbotQuestion->sending_time = $request->sending_time;
            $chatbotQuestion->repeat = $request->repeat;
            $chatbotQuestion->is_active = $request->is_active;
            $chatbotQuestion->save();
        }

        if ($request->type == 'priority-customer') {
            if ($request->repeat == '') {
                $customers = Customer::where('is_priority', 1)->get();

                foreach ($customers as $customer) {
                    ScheduledMessage::create([
                        'user_id' => Auth::id(),
                        'customer_id' => $customer->id,
                        'message' => $chatbotQuestion->suggested_reply,
                        'sending_time' => $request->sending_time,
                    ]);
                }
            }
        }

        return redirect()->back()->withSuccess('You have successfully created a new auto-reply!');
    }

    public function saveDynamicTask(Request $request): JsonResponse
    {
        $params = $request->all();
        $params['value'] = str_replace(' ', '_', $params['value']);
        $validator = Validator::make($params, [
            'value' => 'required|unique:chatbot_questions|max:255',
            'question' => 'required',
            'category_id' => 'required',
            'assigned_to' => 'required',
            'task_category_id' => 'required',
            'task_description' => 'required',
            'suggested_reply' => 'required',
            'watson_account' => 'nullable',
        ]);
        if ($params['task_type'] == 'devtask') {
            if (! $params['repository_id'] || ! $params['module_id']) {
                return response()->json(['code' => 500, 'error' => 'Repository and module is required for Devtask']);
            }
        }
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Incomplete data or intent already exists']);
        }
        $params['keyword_or_question'] = 'intent';
        $params['erp_or_watson'] = 'erp';
        $params['auto_approve'] = 1;
        $params['is_active'] = 1;
        $params['watson_account_id'] = $params['watson_account'];
        $params['google_account_id'] = $params['google_account'];
        $chatbotQuestion = ChatbotQuestion::create($params);
        if (! empty($params['question'])) {
            $chatbotQuestionExample = new ChatbotQuestionExample;
            $chatbotQuestionExample->question = $params['question'];
            $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
            $chatbotQuestionExample->save();
        }
        $data_to_insert = [];
        $storeWebsites = [];
        if ($params['watson_account'] > 0) {
            $wotson_account_ids = WatsonAccount::where('id', $params['watson_account'])->get();
        } else {
            $wotson_account_ids = WatsonAccount::all();
        }

        foreach ($wotson_account_ids as $id) {
            $storeWebsites[] = $id->store_website_id;
            $data_to_insert[] = [
                'suggested_reply' => $params['suggested_reply'],
                'store_website_id' => $id->store_website_id,
                'chatbot_question_id' => $chatbotQuestion->id,
            ];
        }
        $gogole_account_ids = GoogleDialogAccount::where('id', $request->google_account)->get();

        foreach ($gogole_account_ids as $id) {
            if (! in_array($id->site_id, $storeWebsites)) {
                $data_to_insert[] = [
                    'suggested_reply' => $params['suggested_reply'],
                    'store_website_id' => $id->store_website_id,
                    'chatbot_question_id' => $chatbotQuestion->id,
                ];
            }
        }
        ChatbotQuestionReply::insert($data_to_insert);

        return response()->json(['message' => 'Successfully created the Intent', 'code' => 200]);
    }

    public function saveDynamicReply(Request $request): JsonResponse
    {
        $params = $request->all();
        $params['value'] = str_replace(' ', '_', $params['value']);
        $validator = Validator::make($params, [
            'value' => 'required|unique:chatbot_questions|max:255',
            'question' => 'required',
            'category_id' => 'required',
            'erp_or_watson' => 'required',
            'suggested_reply' => 'required',
            'watson_account' => 'nullable',
            'google_account' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => 'Incomplete data or intent already exists']);
        }
        $params['keyword_or_question'] = 'intent';
        $params['is_active'] = 1;
        $params['dynamic_reply'] = 1;
        $params['watson_account_id'] = $params['watson_account'];
        $params['google_account_id'] = $params['google_account'];
        $chatbotQuestion = ChatbotQuestion::create($params);
        if (! empty($params['question'])) {
            $chatbotQuestionExample = new ChatbotQuestionExample;
            $chatbotQuestionExample->question = $params['question'];
            $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
            $chatbotQuestionExample->save();
        }

        $storeWebsites = [];
        if ($params['watson_account'] > 0) {
            $wotson_account_ids = WatsonAccount::where('id', $params['watson_account'])->get();
        } else {
            $wotson_account_ids = WatsonAccount::all();
        }

        foreach ($wotson_account_ids as $id) {
            $data_to_insert[] = [
                'suggested_reply' => $params['suggested_reply'],
                'store_website_id' => $id->store_website_id,
                'chatbot_question_id' => $chatbotQuestion->id,
            ];
        }
        $gogole_account_ids = GoogleDialogAccount::where('id', $request->google_account)->get();

        foreach ($gogole_account_ids as $id) {
            if (! in_array($id->site_id, $storeWebsites)) {
                $data_to_insert[] = [
                    'suggested_reply' => $params['suggested_reply'],
                    'store_website_id' => $id->site_id,
                    'chatbot_question_id' => $chatbotQuestion->id,
                ];
            }
        }
        ChatbotQuestionReply::insert($data_to_insert);

        return response()->json(['message' => 'Successfully created the Intent', 'code' => 200]);
    }

    public function updateReply(Request $request): JsonResponse
    {
        $reply = ChatbotQuestionReply::find($request->id);
        $reply->suggested_reply = $request->suggested_reply;
        $reply->save();

        return response()->json(['suggested_reply' => $request->suggested_reply, 'code' => 200]);
    }

    public function addReply(Request $request): JsonResponse
    {
        $reply = ChatbotQuestionReply::where('store_website_id', $request->store_website_id)->where('chatbot_question_id', $request->id)->first();
        if ($reply) {
            return response()->json(['message' => 'Reply is already available, you can edit the reply', 'code' => 500]);
        }
        $reply = new ChatbotQuestionReply;
        $reply->store_website_id = $request->store_website_id;
        $reply->chatbot_question_id = $request->id;
        $reply->suggested_reply = $request->suggested_reply;
        $reply->save();

        return response()->json(['message' => 'Successfull', 'suggested_reply' => $request->suggested_reply, 'code' => 200]);
    }

    public function onlineUpdate($id): JsonResponse
    {
        $errorLog = ChatbotErrorLog::find($id);
        $result = WatsonManager::pushQuestionSingleWebsite($errorLog->chatbot_question_id, $errorLog->store_website_id);
        if ($result) {
            return response()->json(['message' => 'Created successfully', 'code' => 200]);
        } else {
            return response()->json(['message' => 'Something went wrong, check error log', 'code' => 500]);
        }
    }

    public function showLogById(Request $request): JsonResponse
    {
        $chatbotQuestion = ChatbotQuestionErrorLog::where('chatbot_question_id', $request->id)->get();
        $body = '';
        $i = 0;

        // foreach($chatbotQuestion as $error){
        //     $response = $error->response;
        //     $st =
        //     $status = $error->status == "Success" ? "<td>".$error->status."</td>" : "";
        //     $body .= "<tr><td>".$i."</td><td>".$response."</td><td>".;
        // }
        return response()->json(['code' => 200, 'data' => $chatbotQuestion], 200);
    }

    public function searchSuggestion(Request $request): JsonResponse
    {
        $listOfQuestions = ChatbotQuestionExample::join('chatbot_questions as cq', 'cq.id', 'chatbot_question_examples.chatbot_question_id')
            ->where('question', 'LIKE', '%'.$request->q.'%')
            ->where('cq.keyword_or_question', $request->type)
            ->select(['chatbot_question_examples.*', 'cq.value', 'cq.erp_or_watson'])
            ->limit(10)
            ->get()->toArray();

        return response()->json(['code' => 200, 'data' => $listOfQuestions]);
    }

    public function searchSuggestionDelete(Request $request): JsonResponse
    {
        $id = $request->id;
        $type = $request->type;

        $cbValue = ChatbotQuestionExample::where('id', $id)->first();
        if ($cbValue) {
            $questionModal = $cbValue->questionModal;
            $cbValue->delete();
            if ($questionModal) {
                if ($questionModal->keyword_or_question == 'intent' && $questionModal->erp_or_watson == 'watson') {
                    WatsonManager::pushQuestion($id, $questionModal->value);
                }
                if ($questionModal->keyword_or_question == 'entity' && $questionModal->erp_or_watson == 'watson') {
                    WatsonManager::pushQuestion($id, $questionModal->value);
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Example delete successfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Record does not exist in the database']);
    }

    public function repeatWatson(Request $request): JsonResponse
    {
        $id = $request->id;
        $chatbotQuestion = ChatbotQuestion::whereIn('id', $id)->get();

        if ($chatbotQuestion) {
            foreach ($chatbotQuestion as $key) {
                if ($key->erp_or_watson == 'watson') {
                    if ($key->keyword_or_question == 'intent' || $key->keyword_or_question == 'simple' || $key->keyword_or_question == 'priority-customer' || $key->keyword_or_question == 'entity') {
                        WatsonManager::pushQuestion($key->id, null, $key->watson_account_id);
                    }
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'send successfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong, Please try again!']);
    }

    public function suggestedResponse(Request $request): View
    {
        $value = $request->value;
        $ids = [];
        $chatbotQuestions = ChatbotQuestion::where('value', 'like', '%'.$value.'%')->get('id');
        foreach ($chatbotQuestions as $chatbotQuestion) {
            $ids[] = $chatbotQuestion->id;
        }
        $reply = ChatbotQuestionReply::whereIn('chatbot_question_id', $ids)->get();

        return view('chatbot::question.partial.suggested-response', compact('reply'));
    }

    public function syncWatson($id): JsonResponse
    {
        $chatBotQuestion = ChatbotQuestion::find($id);
        if ($chatBotQuestion) {
            ChatbotQuestion::where('id', $chatBotQuestion->id)->update(['watson_status' => 'Pending watson send']);
            $result = json_decode(WatsonManager::pushQuestion($chatBotQuestion->id, null, $chatBotQuestion->watson_account_id));

            return response()->json(['message' => 'Successfully created the Intent', 'code' => 200]);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Something went wrong, Please try again!']);
    }

    public function syncGoogle($id): JsonResponse
    {
        try {
            $chatBotQuestion = ChatbotQuestion::where('id', $id)->first();
            $chatBotQuestion->google_status = 'Pending google send';
            $chatBotQuestion->save();
            $questionArr = [];
            foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                $questionArr[] = $question->question;
            }
            if ($chatBotQuestion) {
                $googleAccounts = GoogleDialogAccount::where('id', $chatBotQuestion->google_account_id)->get();

                foreach ($googleAccounts as $googleAccount) {
                    $dialogService = new DialogFlowService($googleAccount);
                    if ($chatBotQuestion->keyword_or_question == 'intent' || $chatBotQuestion->keyword_or_question == 'priority-customer' || $chatBotQuestion->keyword_or_question == 'simple') {
                        try {
                            $response = $dialogService->createIntent([
                                'questions' => $questionArr,
                                'reply' => explode(',', $chatBotQuestion['suggested_reply']),
                                'name' => $chatBotQuestion['value'],
                                'parent' => $chatBotQuestion['parent'],
                            ], $chatBotQuestion->google_response_id ?: null);
                            if ($response) {
                                $name = explode('/', $response);
                                $chatBotQuestion->google_status = '1';
                                $chatBotQuestion->google_response_id = $name[count($name) - 1];
                                $chatBotQuestion->save();
                                $store_response = new GoogleResponseId;
                                $store_response->google_response_id = $name[count($name) - 1];
                                $store_response->google_dialog_account_id = $googleAccount->id;
                                $store_response->chatbot_question_id = $chatBotQuestion->id;
                                $store_response->save();
                            }
                        } catch (Exception $e) {
                            $chatBotQuestion->google_status = $e->getMessage();
                            $chatBotQuestion->save();
                        }
                    } elseif ($chatBotQuestion->keyword_or_question == 'entity') {
                        $ids = [];
                        foreach ($chatBotQuestion->chatbotQuestionExamples as $qu) {
                            $ids[] = $qu->types;
                        }
                        $entityType = DialogflowEntityType::whereIn('id', $ids)->first();
                        $entityId = $entityType->response_id;
                        if (! $entityType->response_id) {
                            $responseE = $dialogService->createEntityType($entityType->display_name, $entityType->kind);
                            $responseE = explode('/', $responseE);
                            $entityType->response_id = $responseE[count($responseE) - 1];
                            $entityId = $responseE[count($responseE) - 1];
                            $entityType->save();
                        }
                        $keywords = $chatBotQuestion->chatbotQuestionExamples->pluck('question')->toArray();
                        if ($entityType->kind == '2') {
                            $keywords = [$chatBotQuestion['value']];
                        }
                        try {
                            $response = $dialogService->createEntity($entityId, $chatBotQuestion['value'], $keywords);
                            if ($response) {
                                $name = explode('/', $response);
                                $store_response = new GoogleResponseId;

                                $chatBotQuestion = ChatbotQuestion::where('id', $id)->first();
                                $chatBotQuestion->google_status = '1';
                                $chatBotQuestion->google_response_id = $name[count($name) - 1];
                                $chatBotQuestion->save();

                                $store_response->google_response_id = $name[count($name) - 1];
                                $store_response->google_dialog_account_id = $googleAccount->id;
                                $store_response->chatbot_question_id = $chatBotQuestion->id;
                                $store_response->save();
                            }
                        } catch (Exception $e) {
                            $chatBotQuestion->google_status = $e->getMessage();
                            $chatBotQuestion->save();
                        }
                    }
                    $dialogService->trainAgent();
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'send successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function addEntityType(Request $request): JsonResponse
    {
        $params = $request->all();
        $params['name'] = $request->name;
        $params['display_name'] = $request->display_name;
        $params['kind'] = $request->kind_name;
        $validator = Validator::make($params, [
            'name' => 'required',
            'display_name' => 'required',
            'kind' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 500, 'error' => $validator->errors()]);
        }
        $entity_type = DialogflowEntityType::create($params);
        $route = route('chatbot.question.list');

        return response()->json(['code' => 200, 'data' => $entity_type, 'redirect' => $route]);
    }

    public function simulator(): View
    {
        $google_accounts = GoogleDialogAccount::all();

        return view('chatbot::google-chatbot.chatbot', compact('google_accounts'));
    }

    public function botReply(Request $request): JsonResponse
    {
        try {
            $session = Cache::get('chatbot-session');
            if (! $session) {
                $session = uniqid();
                Cache::set('chatbot-session', $session);
            }
            $googleAccount = GoogleDialogAccount::where('id', $request->googleAccount)->first();
            $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                ->select('chatbot_questions.*', DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                ->where('chatbot_questions.google_account_id', $request->googleAccount)
                ->where('chatbot_questions.keyword_or_question', 'intent')
                ->where('chatbot_questions.value', 'like', '%'.$request->question.'%')->orWhere('cqe.question', 'like', '%'.$request->question.'%')
                ->groupBy('chatbot_questions.id')
                ->orderByDesc('chatbot_questions.id')
                ->first();

            if ($chatQuestions) {
                if ($chatQuestions->auto_approve) {
                    return response()->json(['code' => 200, 'data' => $chatQuestions->suggested_reply]);
                }
            }
            $dialogFlowService = new DialogFlowService($googleAccount);
            $response = $dialogFlowService->detectIntent(null, $request->question);

            $intentName = $response->getIntent()->getName();
            $intentName = explode('/', $intentName);
            $intentName = $intentName[count($intentName) - 1];

            $question = ChatbotQuestion::where('google_response_id', $intentName)->first();
            if (! $question) {
                $question = ChatbotQuestion::where('value', $response->getIntent()->getDisplayName())->first();
                if (! $question) {
                    $question = ChatbotQuestion::create([
                        'keyword_or_question' => 'intent',
                        'is_active' => true,
                        'google_account_id' => $googleAccount->id,
                        'google_status' => '1',
                        'google_response_id' => $intentName,
                        'value' => $response->getIntent()->getDisplayName(),
                        'suggested_reply' => $response->getFulfillmentText(),
                    ]);
                }
            }
            $questionsE = ChatbotQuestionExample::where('question', 'like', '%'.$response->getQueryText().'%')->first();
            if (! $questionsE) {
                ChatbotQuestionExample::create([
                    'chatbot_question_id' => $question->id,
                    'question' => $response->getQueryText(),
                ]);
            }

            $questionsR = ChatbotQuestionReply::where('suggested_reply', 'like', '%'.$response->getFulfillmentText().'%')->first();
            if (! $questionsR) {
                $chatRply = new ChatbotQuestionReply;
                $chatRply->suggested_reply = $response->getFulfillmentText();
                $chatRply->store_website_id = $googleAccount->site_id;
                $chatRply->chatbot_question_id = $question->id;
                $chatRply->save();
            }
            $suggested_reply = $dialogFlowService->purifyResponse($response->getFulfillmentText());

            return response()->json(['code' => 200, 'data' => $suggested_reply]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'data' => $e->getMessage()]);
        }
    }
}
