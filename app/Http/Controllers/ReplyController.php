<?php

namespace App\Http\Controllers;

use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\ChatbotQuestionReply;
use App\Http\Requests\CategoryStoreReplyRequest;
use App\Http\Requests\ChatBotQuestionReplyRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\SubcategoryStoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Jobs\ProcessTranslateReply;
use App\Models\QuickRepliesPermissions;
use App\Models\RepliesTranslatorHistory;
use App\Models\ReplyLog;
use App\Reply;
use App\ReplyCategory;
use App\ReplyTranslatorStatus;
use App\ReplyUpdateHistory;
use App\Setting;
use App\StoreWebsite;
use App\StoreWebsitePage;
use App\TranslateReplies;
use App\User;
use App\WatsonAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use function GuzzleHttp\json_encode;

class ReplyController extends Controller
{
    public function index(Request $request): View
    {
        $reply_categories = ReplyCategory::where('parent_id', 0)->orderBy('name')->get();

        $replies = Reply::with('category', 'category.parent');

        if (! empty($request->keyword)) {
            $replies->where('reply', 'LIKE', '%'.$request->keyword.'%');
        }

        $replysubcategories = [];
        if (! empty($request->sub_category_id)) {
            $replies->where('category_id', $request->sub_category_id);
        } elseif (! empty($request->category_id)) {
            $allIds = ReplyCategory::whereIn('id', $request->category_id)
                ->orWhereIn('parent_id', $request->category_id)
                ->pluck('id')
                ->toArray();

            $replies->whereIn('category_id', $allIds);
        }

        if (is_array($request->category_id)) {
            $replysubcategories = ReplyCategory::whereIn('parent_id', $request->category_id)->get();
        } else {
            $replysubcategories = ReplyCategory::where('parent_id', $request->category_id)->get();
        }

        $replies->orderByDesc('replies.id');

        $replies = $replies->paginate(Setting::get('pagination'));

        $reply_main_categories = ReplyCategory::where('parent_id', 0)->get();

        return view('reply.index', compact('replies', 'reply_categories', 'reply_main_categories', 'replysubcategories'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['reply'] = '';
        $data['model'] = '';
        $data['category_id'] = '';
        $data['modify'] = 0;
        $data['reply_categories'] = ReplyCategory::all();

        return view('reply.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReplyRequest $request, Reply $reply)
    {

        $data = $request->except('_token', '_method');

        if (! empty($request->sub_category_id) && $request->sub_category_id != 'undefined') {
            $data['category_id'] = $data['sub_category_id'];
        }
        $data['reply'] = trim($data['reply']);
        $createdReply = $reply->create($data);

        if ($request->ajax()) {
            if (isset($request->type) && $request->type === 'with-extra-attributes') {
                return response()->json(['reply' => trim($createdReply->reply), 'id' => $createdReply->id]);
            }

            return response()->json(trim($request->reply));
        }

        return redirect()->route('reply.index')->with('success', 'Quick Reply added successfully');
    }

    public function categorySetDefault(Request $request): JsonResponse
    {
        if ($request->has('model') && $request->has('cat_id')) {
            $model = $request->model;
            $cat_id = $request->cat_id;
            $ReplyCategory = ReplyCategory::find($cat_id);
            if ($ReplyCategory) {
                $ReplyCategory->default_for = $model;
                $ReplyCategory->save();

                return response()->json(['success' => true, 'message' => 'Category Assignments Successfully']);
            }

            return response()->json(['success' => false, 'message' => 'The Reply Category data was not found']);
        }

        return response()->json(['success' => false, 'message' => 'The requested data was not found']);
    }

    public function categoryStore(CategoryStoreReplyRequest $request): RedirectResponse
    {

        $category = new ReplyCategory;
        $category->name = $request->name;
        $category->save();

        return redirect()->route('reply.index')->with('success', 'You have successfully created category');
    }

    public function subcategoryStore(SubcategoryStoreReplyRequest $request): RedirectResponse
    {

        $category = new ReplyCategory;
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->save();

        return redirect()->route('reply.index')->with('success', 'You have successfully created sub category');
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
     * @param  int  $id
     */
    public function edit(Reply $reply): View
    {
        $data = $reply->toArray();
        $data['modify'] = 1;
        $data['reply_categories'] = ReplyCategory::all();

        return view('reply.form', $data);
    }

    public function editReply(Request $request)
    {
        $id = $request->get('id', 0);
        $ReplyNotes = Reply::where('id', $id)->first();
        if ($ReplyNotes) {
            $reply_categories = ReplyCategory::where('parent_id', 0)->get();

            $reply_sub_categories = ReplyCategory::where('parent_id', $request->c_id)->get();

            $category_id = $request->c_id;
            $sub_category_id = $request->sc_id;

            return view('reply.edit', compact('ReplyNotes', 'reply_categories', 'category_id', 'sub_category_id', 'reply_sub_categories'));
        }

        return 'Quick Reply Not Found';
    }

    public function getSubcategories(Request $request): JsonResponse
    {
        if (is_array($request->category_id)) {
            $subcategories = ReplyCategory::whereIn('parent_id', $request->category_id)->pluck('name', 'id');
        } else {
            $subcategories = ReplyCategory::where('parent_id', $request->category_id)->pluck('name', 'id');
        }

        return response()->json($subcategories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(UpdateReplyRequest $request, Reply $reply): RedirectResponse
    {

        $data = $request->except('_token', '_method');

        $reply->is_pushed = 0;
        $reply->update($data);

        (new ReplyLog)->addToLog($reply->id, 'System updated FAQ', 'Updated');

        return redirect()->route('reply.index')->with('success', 'Quick Reply updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply, Request $request)
    {
        $reply->delete();
        if ($request->ajax()) {
            return response()->json(['message' => 'Deleted successfully']);
        }

        return redirect()->route('reply.index')->with('success', 'Quick Reply Deleted successfully');
    }

    public function removepermissions(Request $request)
    {
        if ($request->type == 'remove_permission') {
            $edit_data = QuickRepliesPermissions::where('user_id', $request->user_permission_id)->whereNotIn('lang_id', $request->edit_lang_name)->where('action', 'edit')->get();
            $view_data = QuickRepliesPermissions::where('user_id', $request->user_permission_id)->whereNotIn('lang_id', $request->view_lang_name)->where('action', 'view')->get();

            foreach ($edit_data as $edit_lang) {
                $edit_lang->delete();
            }

            foreach ($view_data as $view_lang) {
                $view_lang->delete();
            }

            return redirect()->back()->with('success', 'Remove Permission successfully');
        } else {
            $checkExists = QuickRepliesPermissions::where('user_id', $request->id)->get();
            $edit_lang = [];
            $view_lang = [];
            foreach ($checkExists as $checkExist) {
                if ($checkExist->action == 'edit') {
                    $edit_lang[] = $checkExist->lang_id;
                }
                if ($checkExist->action == 'view') {
                    $view_lang[] = $checkExist->lang_id;
                }
            }

            $data = [
                'edit_lang' => $edit_lang,
                'view_lang' => $view_lang,
                'status' => '200',
            ];

            return $data;
        }
    }

    public function chatBotQuestion(ChatBotQuestionReplyRequest $request): JsonResponse
    {

        $ChatbotQuestion = null;
        $example = ChatbotQuestionExample::where('question', $request->question)->first();
        if ($example) {
            return response()->json(['message' => 'User intent is already available']);
        }

        if (is_numeric($request->intent_name)) {
            $ChatbotQuestion = ChatbotQuestion::where('id', $request->intent_name)->first();
        } else {
            if ($request->intent_name != '') {
                $ChatbotQuestion = ChatbotQuestion::create([
                    'value' => str_replace(' ', '_', preg_replace('/\s+/', ' ', $request->intent_name)),
                ]);
            }
        }
        $ChatbotQuestion->suggested_reply = $request->intent_reply;
        $ChatbotQuestion->category_id = $request->intent_category_id;
        $ChatbotQuestion->keyword_or_question = 'intent';
        $ChatbotQuestion->is_active = 1;
        $ChatbotQuestion->erp_or_watson = 'erp';
        $ChatbotQuestion->auto_approve = 1;
        $ChatbotQuestion->save();

        $ex = new ChatbotQuestionExample;
        $ex->question = $request->question;
        $ex->chatbot_question_id = $ChatbotQuestion->id;
        $ex->save();

        $wotson_account_website_ids = WatsonAccount::get()->pluck('store_website_id')->toArray();

        $data_to_insert = [];

        foreach ($wotson_account_website_ids as $id_) {
            $data_to_insert[] = [
                'chatbot_question_id' => $ChatbotQuestion->id,
                'store_website_id' => $id_,
                'suggested_reply' => $request->intent_reply,
            ];
        }

        ChatbotQuestionReply::insert($data_to_insert);
        Reply::where('id', $request->intent_reply_id)->delete();

        return response()->json(['message' => 'Successfully created', 'code' => 200]);
    }

    public function replyList(Request $request): View
    {
        $storeWebsite = $request->get('store_website_id');
        $keyword = $request->get('keyword');
        $parent_category = $request->get('parent_category_ids') ? $request->get('parent_category_ids') : [];
        $category_ids = $request->get('category_ids') ? $request->get('category_ids') : [];
        $sub_category_ids = $request->get('sub_category_ids') ? $request->get('sub_category_ids') : [];

        $categoryChildNode = [];
        if ($parent_category) {
            $parentNode = ReplyCategory::select(DB::raw('group_concat(id) as ids'))->whereIn('id', $parent_category)->where('parent_id', '=', 0)->first();
            if ($parentNode) {
                $subCatChild = ReplyCategory::whereIn('parent_id', explode(',', $parentNode->ids))->get()->pluck('id')->toArray();
                $categoryChildNode = ReplyCategory::whereIn('parent_id', $subCatChild)->get()->pluck('id')->toArray();
            }
        }

        $replies = ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
            ->where('model', 'Store Website')
            ->select(['replies.*', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id']);

        if ($storeWebsite > 0) {
            $replies = $replies->where('replies.store_website_id', $storeWebsite);
        }

        if (! empty($keyword)) {
            $replies = $replies->where(function ($q) use ($keyword) {
                $q->orWhere('reply_categories.name', 'LIKE', '%'.$keyword.'%')->orWhere('replies.reply', 'LIKE', '%'.$keyword.'%');
            });
        }
        if (! empty($parent_category)) {
            if ($categoryChildNode) {
                $replies = $replies->where(function ($q) use ($categoryChildNode) {
                    $q->orWhereIn('reply_categories.id', $categoryChildNode);
                });
            } else {
                $replies = $replies->where(function ($q) use ($parent_category) {
                    $q->orWhereIn('reply_categories.id', $parent_category)->where('reply_categories.parent_id', '=', 0);
                });
            }
        }

        if (! empty($category_ids)) {
            $replies = $replies->where(function ($q) use ($category_ids) {
                $q->orWhereIn('reply_categories.parent_id', $category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        if (! empty($sub_category_ids)) {
            $replies = $replies->where(function ($q) use ($sub_category_ids) {
                $q->orWhereIn('reply_categories.id', $sub_category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        $replies = $replies->paginate(25);

        $parentCategory = $allSubCategory = [];
        $replyCategories = ReplyCategory::all();
        $parentCategory = $replyCategories->where('parent_id', 0)->pluck('name', 'id')->toArray();
        $allSubCategory = $replyCategories->where('parent_id', '!=', 0);
        $category = $subCategory = [];
        foreach ($allSubCategory as $value) {
            $categoryList = ReplyCategory::where('id', $value->parent_id)->first();
            if ($categoryList && $categoryList->parent_id == 0) {
                $category[$value->id] = $value->name;
            } else {
                $subCategory[$value->id] = $value->name;
            }
        }

        $websites = StoreWebsite::pluck('title', 'id')->toArray();

        return view('reply.list', compact('replies', 'parentCategory', 'category', 'subCategory', 'parent_category', 'category_ids', 'sub_category_ids', 'websites'));
    }

    public function replyListDelete(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $record = ReplyCategory::find($id);

        if ($record) {
            $replies = $record->replies;
            if (! $replies->isEmpty()) {
                foreach ($replies as $re) {
                    $re->delete();
                }
            }
            $record->delete();
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Record deleted successfully']);
    }

    public function replyUpdate(Request $request): RedirectResponse|JsonResponse
    {
        $id = $request->get('id');
        $reply = Reply::find($id);

        $replies = Reply::where('id', $id)->first();
        $ReplyUpdateHistory = new ReplyUpdateHistory;
        $ReplyUpdateHistory->last_message = $replies->reply;
        $ReplyUpdateHistory->reply_id = $replies->id;
        $ReplyUpdateHistory->user_id = Auth::id();
        $ReplyUpdateHistory->save();

        if ($reply) {
            $reply->reply = $request->reply;
            $reply->pushed_to_watson = 0;
            $reply->save();

            $replyCategory = ReplyCategory::find($reply->category_id);

            $replyCategories = $replyCategory->parentList();
            $cats = explode('>', str_replace(' ', '', $replyCategories));

            if (isset($cats[0]) and $cats[0] == 'FAQ') {
                $faqCat = ReplyCategory::where('name', 'FAQ')->pluck('id')->first();
                if ($faqCat != null) {
                    $faqToPush = '<div class="cls_shipping_panelmain">';
                    $topParents = ReplyCategory::where('parent_id', $faqCat)->get();
                    foreach ($topParents as $topParent) {
                        $faqToPush .= '<div class="cls_shipping_panelsub">
                        <div id="shopPlaceOrder" class="accordion_head" role="tab">
                            <h4 class="panel-title"><a role="button" href="javascript:;" class="cls_abtn"> '.$topParent['name'].' </a><span class="plusminus">-</span></h4>
                        </div> <div class="accordion_body" style="display: block;">';
                        $questions = ReplyCategory::where('parent_id', $topParent['id'])->get();
                        foreach ($questions as $question) {
                            $answer = Reply::where('category_id', $question['id'])->first();
                            if ($answer != null) {
                                $faqToPush .= '<p class="md-paragraph"><strong>'.$question['name'].'</strong></p>
                                    <p class="md-paragraph"> '.$answer['reply'].' </p>';
                            }
                        }
                        $faqToPush .= '</div></div>';
                    }
                    $faqToPush .= '</div>';
                    $faqPage = StoreWebsitePage::where(['store_website_id' => $reply->store_website_id, 'url_key' => 'faqs'])->first();
                    if ($faqPage == null) {
                        echo 'if';
                        StoreWebsitePage::create(['title' => 'faqs', 'content' => $faqToPush, 'store_website_id' => $reply->store_website_id, 'url_key' => 'faqs', 'is_pushed' => 0]);
                    } else {
                        echo 'else';
                        StoreWebsitePage::where('id', $faqPage->id)->update(['content' => $faqToPush, 'is_pushed' => 0]);
                    }
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Quick Reply Updated successfully']);
        }

        return redirect()->back()->with('success', 'Quick Reply Updated successfully');

    }

    public function replyUpdateStores(Request $request): RedirectResponse|JsonResponse
    {
        $id = $request->get('id');
        $post_websites = $request->get('websites');
        $update_websites = [];
        $reply = Reply::find($id);
        $replies = Reply::whereIn('store_website_id', $post_websites)->where('category_id', $reply->category_id)->where('is_pushed', 0)->get();
        foreach ($replies as $replyData) {
            $replyUpdateHistory = new ReplyUpdateHistory;
            $replyUpdateHistory->last_message = $replyData->reply;
            $replyUpdateHistory->reply_id = $replyData->id;
            $replyUpdateHistory->user_id = Auth::id();
            $replyUpdateHistory->save();

            $replyData->reply = $request->reply;
            $replyData->pushed_to_watson = 0;
            $replyData->is_pushed = 0;
            $replyData->is_flagged = 0;
            $replyData->platform_id = $reply->platform_id;
            $replyData->is_translate = 0;
            $replyData->pushed_to_google = 0;
            $replyData->save();

            $update_websites[] = $replyData->store_website_id;
        }

        $update_websites = array_unique($update_websites);
        $missing_websites = array_diff($post_websites, $update_websites);
        $missing_websites = array_unique($missing_websites);
        if (! empty($missing_websites)) {
            foreach ($missing_websites as $website_data) {
                $replyData = new Reply;
                $replyData->category_id = $reply->category_id;
                $replyData->reply = $request->reply;
                $replyData->model = $reply->model;
                $replyData->pushed_to_watson = 0;
                $replyData->is_pushed = 0;
                $replyData->is_flagged = 0;
                $replyData->platform_id = $reply->platform_id;
                $replyData->is_translate = 0;
                $replyData->pushed_to_google = 0;
                $replyData->store_website_id = $website_data;
                $replyData->save();
            }
        }
        if ($request->ajax()) {
            return response()->json(['message' => 'Quick Reply Updated successfully']);
        }

        return redirect()->back()->with('success', 'Quick Reply Updated successfully');
    }

    public function getReplyedHistory(Request $request): JsonResponse
    {
        $id = $request->id;
        // $reply_histories = DB::select(DB::raw('SELECT reply_update_histories.id,reply_update_histories.reply_id,reply_update_histories.user_id,reply_update_histories.last_message,reply_update_histories.created_at,users.name FROM `reply_update_histories` JOIN `users` ON users.id = reply_update_histories.user_id where reply_update_histories.reply_id = ' . $id));
        $reply_histories = ReplyUpdateHistory::select('reply_update_histories.id', 'reply_update_histories.reply_id', 'reply_update_histories.user_id', 'reply_update_histories.last_message', 'reply_update_histories.created_at', 'users.name')
            ->join('users', 'users.id', '=', 'reply_update_histories.user_id')
            ->where('reply_update_histories.reply_id', $id)
            ->get();

        return response()->json(['histories' => $reply_histories]);
    }

    public function replyTranslate(Request $request): JsonResponse
    {
        $id = $request->reply_id;
        $is_flagged_request = $request->is_flagged;

        if ($is_flagged_request == '1') {
            $is_flagged = 0;
        } else {
            $is_flagged = 1;
        }

        if ($is_flagged == '1') {
            $record = Reply::find($id);
            if ($record) {
                ProcessTranslateReply::dispatch($record, Auth::id())->onQueue('replytranslation');

                $record->is_flagged = 1;
                $record->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Replies Set For Translatation']);
            }

            return response()->json(['code' => 400, 'data' => [], 'message' => 'There is a problem while translating']);
        } else {
            $res_rec = Reply::find($id);
            $res_rec->is_flagged = 0;
            $res_rec->save();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Translation off successfully']);
        }
    }

    public function replyTranslateList(Request $request): View
    {
        $storeWebsite = $request->get('store_website_id');
        $language = $request->get('lang');
        $keyword = $request->get('keyword');
        $status = $request->get('status');

        $lang = [];
        $ids = [];
        $translate_text = [];

        $StatusResults = TranslateReplies::select('translate_to', 'status', DB::raw('COUNT(*) as count'))->groupBy('translate_to', 'status')->orderBy('translate_to')->get();

        $StatusArray = [];
        if (! empty($StatusResults)) {
            foreach ($StatusResults as $value) {
                $StatusArray[$value->translate_to]['language'] = $value->translate_to;

                if ($value->status == 'approved') {
                    $StatusArray[$value->translate_to]['approve'] = $value->count;
                }

                if ($value->status == 'rejected') {
                    $StatusArray[$value->translate_to]['rejected'] = $value->count;
                }

                if ($value->status == 'new') {
                    $StatusArray[$value->translate_to]['new'] = $value->count;
                }

                if ($value->status === null) {
                    $StatusArray[$value->translate_to]['uncheck'] = $value->count;
                }
            }
        }

        $getLangs = TranslateReplies::distinct('translate_to')->pluck('translate_to');

        if ($storeWebsite > 0 && ! empty($language)) {
            $replies = TranslateReplies::join('replies', 'translate_replies.replies_id', 'replies.id')
                ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
                ->leftJoin('reply_categories', 'reply_categories.id', 'replies.category_id')
                ->where('model', 'Store Website')->where('replies.is_flagged', '1')
                ->whereNull('replies.deleted_at')
                ->select(['replies.*', 'translate_replies.status', 'translate_replies.replies_id as replies_id', 'replies.reply as original_text', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id', 'translate_replies.id as id', 'translate_replies.translate_from', 'translate_replies.translate_to', 'translate_replies.translate_text', 'translate_replies.created_at', 'translate_replies.updated_at', 'translate_replies.translate_text_score']);

            $replies = $replies->where('replies.store_website_id', $storeWebsite);

            if (! empty($keyword)) {
                $replies = $replies->where(function ($q) use ($keyword) {
                    $q->orWhere('reply_categories.name', 'LIKE', '%'.$keyword.'%')->orWhere('replies.reply', 'LIKE', '%'.$keyword.'%');
                });
            }

            $replies = $replies->where('translate_replies.translate_to', $language);

            if (! empty($status)) {
                $replies = $replies->where(function ($q) use ($status) {
                    $q->orWhere('translate_replies.status', 'LIKE', $status);
                });
            }

            $replies = $replies->get();

            foreach ($replies as $replie) {
                if (! in_array($replie->replies_id, $ids)) {
                    $ids[] = $replie->replies_id;

                    $translate_text[$replie->replies_id]['id'] = $replie->id;
                    $translate_text[$replie->replies_id]['website'] = $replie->website;
                    $translate_text[$replie->replies_id]['category_name'] = $replie->category_name;
                    $translate_text[$replie->replies_id]['translate_from'] = $replie->translate_from;
                    $translate_text[$replie->replies_id]['original_text'] = $replie->original_text;

                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_text'] = $replie->translate_text;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_lang'] = $replie->translate_to;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_id'] = $replie->id;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_status'] = $replie->status;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_status_color'] = $replie->status_color;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_text_score'] = $replie->translate_text_score;

                    $translate_text[$replie->replies_id]['created_at'] = $replie->created_at;
                    $translate_text[$replie->replies_id]['updated_at'] = $replie->updated_at;
                } else {
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_text'] = $replie->translate_text;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_lang'] = $replie->translate_to;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_id'] = $replie->id;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_status'] = $replie->status;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_status_color'] = $replie->status_color;
                    $translate_text[$replie->replies_id]['transalates'][$replie->translate_to]['translate_text_score'] = $replie->translate_text_score;
                }

                if (! in_array($replie->translate_to, $lang)) {
                    $lang[$replie->id] = $replie['translate_to'];
                }
            }
        }

        $itemsPerPage = 25; // Define the number of items per page
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Paginate the JSON-encoded data manually
        $totalItems = count($translate_text);

        // Calculate the total number of pages
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Extract the data for the current page
        $paginatedTranslateText = array_slice($translate_text, $offset, $itemsPerPage, true);

        // Convert the paginated data back to JSON
        $replies = json_encode($paginatedTranslateText);

        $replyTranslatorStatuses = ReplyTranslatorStatus::all();
        $check_permission = QuickRepliesPermissions::where('user_id', auth()->user()->id)->get();
        $active_users = User::where('is_active', '1')->orderBy('name', 'ASC')->get();
        $store_websites = StoreWebsite::pluck('website', 'id')->toArray();

        return view('reply.translate-list', compact('replies', 'lang', 'replyTranslatorStatuses', 'getLangs', 'totalItems', 'itemsPerPage', 'currentPage', 'totalPages', 'StatusArray', 'check_permission', 'active_users', 'store_websites'))->with('i', ($request->input('page', 1) - 1) * 25);
    }

    public function quickRepliesPermissions(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $data = $request->only('user_id', 'lang_id', 'action');
            $checkExists = QuickRepliesPermissions::where('user_id', $data['user_id'])->where('lang_id', $data['lang_id'])->where('action', $data['action'])->first();

            if ($checkExists) {
                return response()->json(['status' => 412]);
            }

            QuickRepliesPermissions::insert($data);

            return response()->json(['status' => 200]);
        }
    }

    public function replyTranslateUpdate(Request $request): RedirectResponse
    {
        $record = TranslateReplies::find($request->record_id);
        $oldRecord = $request->lang_id;
        if ($record) {
            $record->updated_by_user_id = ! empty($request->update_by_user_id) ? $request->update_by_user_id : '';
            $record->translate_text = ! empty($request->update_record) ? $request->update_record : '';
            $record->status = 'new';
            $record->update();

            $historyData = [];
            $historyData['translate_replies_id'] = $record->id;
            $historyData['updated_by_user_id'] = $record->updated_by_user_id;
            $historyData['translate_text'] = $request->update_record;
            $historyData['status'] = 'new';
            $historyData['lang'] = $oldRecord;
            $historyData['created_at'] = \Carbon\Carbon::now();
            RepliesTranslatorHistory::insert($historyData);

            return redirect()->back()->with(['success' => 'Successfully Updated']);
        } else {
            return redirect()->back()->withErrors('Something Wrong');
        }
    }

    public function replyTranslatehistory(Request $request): JsonResponse
    {
        $key = $request->key;
        $language = $request->language;
        if ($request->type == 'all_view') {
            $history = RepliesTranslatorHistory::whereRaw('status is not null')->get();
        } else {
            $history = RepliesTranslatorHistory::where([
                'translate_replies_id' => $request->id,
                'lang' => $language,
            ])->whereRaw('status is not null')->get();
        }
        if (count($history) > 0) {
            foreach ($history as $key => $historyData) {
                $history[$key]['updater'] = User::where('id', $historyData['updated_by_user_id'])->pluck('name')->first();
                $history[$key]['approver'] = User::where('id', $historyData['approved_by_user_id'])->pluck('name')->first();
            }
        }
        $html = view('reply.history_table', compact('history'))->render();

        return response()->json(['status' => 200, 'data' => $html]);
    }

    public function approvedByAdmin(Request $request): JsonResponse
    {
        $record = TranslateReplies::where('id', $request->id)->first();
        $record['status'] = $request->status;
        $record['approved_by_user_id'] = Auth::user()->id;
        $record->update();

        $record_history = RepliesTranslatorHistory::where('translate_replies_id', $request->id)->where('lang', $request->lang)->orderByDesc('id')->first();
        $record_history['status'] = $request->status;
        $record_history['approved_by_user_id'] = Auth::user()->id;
        $record_history->update();

        return response()->json(['status' => 200]);
    }

    public function show_logs(Request $request, ReplyLog $ReplyLog): JsonResponse
    {
        $data = $request->all();

        $data = $ReplyLog->where('reply_id', $data['id'])->orderByDesc('created_at')->paginate(20);
        $paginateHtml = $data->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $data, 'message' => 'Logs found']);
    }

    public function replyLogList(Request $request): View
    {
        $replyLogs = new ReplyLog;

        $replyLogs = $replyLogs->latest()->paginate(Setting::get('pagination', 25));

        return view('reply.log-reply', compact('replyLogs'));
    }

    public function replyMulitiple(Request $request): JsonResponse
    {
        $replyIds = $request->input('reply_ids');

        $replyIdsArray = explode(',', $replyIds);

        foreach ($replyIdsArray as $replyId) {
            $replyLog = Reply::find($replyId);
            if ($replyLog) {
                $replyLog->is_flagged = 1;
                $replyLog->save();
            }
        }

        return response()->json(['message' => 'Flag Added successfully']);
    }

    public function statusColor(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        foreach ($statusColor['color_name'] as $key => $value) {
            $cronStatus = ReplyTranslatorStatus::find($key);
            $cronStatus->color = $value;
            $cronStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function getTranslatedTextScore(Request $request): JsonResponse
    {
        $translateReplies = TranslateReplies::with('reply')->where('replies_id', $request->replies_id)->get();
        if (! empty($translateReplies) && count($translateReplies) > 0) {
            foreach ($translateReplies as $transReply) {
                $originalText = (! empty($transReply->reply)) ? $transReply->reply->reply : '';
                if ($originalText != '') {
                    $textScore = app('translation-lambda-helper')->getTranslateScore($originalText, $transReply->translate_text);

                    $transReply->translate_text_score = ($textScore != 0) ? $textScore : 0;
                    $transReply->save();
                }
            }

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Get translated text score successfully']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Something went wrong']);
        }
    }
}
