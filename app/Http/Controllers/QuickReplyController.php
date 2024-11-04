<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyStoreWiseReplyQuickReplyRequest;
use App\Http\Requests\StoreQuickReplyRequest;
use App\QuickReply;
use App\Reply;
use App\ReplyCategory;
use App\StoreWebsite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Zend\Diactoros\Response\JsonResponse;
use Exception;

class QuickReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $replies = QuickReply::all();

        return view('quick_reply.index', compact('replies'));
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
     */
    public function store(StoreQuickReplyRequest $request): RedirectResponse
    {

        $r = new QuickReply;
        $r->text = $request->get('text');
        $r->save();

        return redirect()->back()->with('message', 'Quick reply added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuickReply  $quickReply
     * @param  mixed  $id
     */
    public function show($id): RedirectResponse
    {
        $reply = QuickReply::findOrFail($id);

        $reply->delete();

        return redirect()->back()->with('message', 'Deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(QuickReply $quickReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuickReply $quickReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuickReply $quickReply)
    {
        //
    }

    public function quickReplies(Request $request)
    {
        try {
            $subcat = '';
            $all_categories = ReplyCategory::where('parent_id', 0);

            if ($request->sub_category) {
                $subcat = $request->sub_category;
                $parent_id = ReplyCategory::find($request->sub_category);
                $all_categories->where('id', $parent_id->parent_id);
            }
            $all_categories = $all_categories->get();
            $store_websites = StoreWebsite::get();
            $sub_categories = ['' => 'Select Sub Category'] + ReplyCategory::where('parent_id', '!=', 0)->pluck('name', 'id')->toArray();
            $website_length = count($store_websites);

            $all_replies = Reply::whereNotNull('store_website_id')->select('id', 'category_id', 'reply', 'store_website_id')->get();
            $category_wise_reply = [];
            foreach ($all_replies as $replies) {
                if (! empty($replies->store_website_id)) {
                    $category_wise_reply[$replies->category_id][$replies->store_website_id][$replies->id] = $replies->toArray();
                }
            }

            if ($all_categories) {
                foreach ($all_categories as $k => $_cat) {
                    $childs = ReplyCategory::where('parent_id', $_cat->id);
                    if ($request->sub_category) {
                        $childs->where('id', $request->sub_category);
                    }
                    $childs = $childs->get();
                    $all_categories[$k]['childs'] = $childs;
                    if ($childs) {
                        foreach ($all_categories[$k]['childs'] as $c => $_child) {
                            $subchilds = ReplyCategory::where('parent_id', $_child->id);
                            $subchilds = $subchilds->get();
                            $all_categories[$k]['childs'][$c]['subchilds'] = $subchilds;
                        }
                    }
                }
            }

            return view('quick_reply.quick_replies', compact('all_categories', 'store_websites', 'website_length', 'category_wise_reply', 'sub_categories', 'subcat'));
        } catch (Exception $e) {
            return redirect()->back();
        }
    }

    public function getStoreWiseReplies($category_id, $store_website_id = null)
    {
        try {
            $replies = ($store_website_id)
                ? Reply::where(['category_id' => $category_id, 'store_website_id' => $store_website_id])->get()
                : Reply::where(['category_id' => $category_id])->get();

            return new JsonResponse(['status' => 1, 'data' => $replies]);
        } catch (Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Try again']);
        }
    }

    public function saveStoreWiseReply(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (isset($request->reply_id)) {
                //update reply
                Reply::where('id', '=', $request->reply_id)->update([
                    'reply' => $request->reply,
                    'pushed_to_watson' => 0,
                ]);

                return response()->json(['status' => 1, 'data' => $request->reply, 'message' => 'Reply updated successfully']);
            } else {
                Reply::create([
                    'category_id' => $request->category_id,
                    'store_website_id' => $request->store_website_id,
                    'reply' => $request->reply,
                    'model' => 'Store Website',
                    'pushed_to_watson' => 0,
                ]);

                return response()->json(['status' => 1, 'data' => $request->reply, 'message' => 'Reply added successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Try again']);
        }
    }

    public function copyStoreWiseReply(CopyStoreWiseReplyQuickReplyRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();

        try {
            $replyContent = Reply::find($data['reply_id']);

            Reply::create([
                'category_id' => $replyContent->category_id,
                'store_website_id' => $data['website_store_id'],
                'reply' => $replyContent->reply,
                'model' => 'Store Website',
                'pushed_to_watson' => 0,
            ]);

            return response()->json(['status' => 1, 'message' => 'Reply copied successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Try again']);
        }
    }

    public function saveSubCat(Request $request)
    {
        try {
            if (isset($request->sub_id)) {
                //update name
                $replyCategory = ReplyCategory::find($request->sub_id);
                if ($replyCategory != null && $replyCategory['name'] != $request->name) {
                    ReplyCategory::where('id', '=', $request->sub_id)->update([
                        'name' => $request->name,
                        'intent_id' => 0,
                        'dialog_id' => 0,
                        'pushed_to_watson' => 0,
                    ]);
                }

                return new JsonResponse(['status' => 1, 'data' => $request->name, 'message' => 'Category updated successfully']);
            } else {
                ReplyCategory::create([
                    'name' => $request->reply,
                    'parent_id' => $request->category_id,
                ]);

                return new JsonResponse(['status' => 1, 'data' => $request->reply, 'message' => 'Category added successfully']);
            }
        } catch (Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Try again']);
        }
    }
}
