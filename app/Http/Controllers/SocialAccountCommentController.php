<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Helpers\MessageHelper;
use App\Models\SocialComments;
use App\Reply;
use App\ReplyCategory;
use App\Services\CommonGoogleTranslateService;
use App\Services\Facebook\FB;
use App\Social\SocialConfig;
use App\Social\SocialPost;
use App\SocialWebhookLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialAccountCommentController extends Controller
{
    public function index(Request $request, $postId)
    {
        // Add self join to get sub_comments, get quick reply comments. DEVTASK-24825
        $post = SocialPost::where('id', $postId)->firstOrFail();
        $search = $request->get('search');
        $comments = SocialComments::where('post_id', $post->id)->with('sub_comments')->whereNull('parent_id');
        $comments = $comments->when($request->has('search'), function (Builder $builder) use ($search) {
            return $builder->whereLike(['comment_id', 'message'], $search);
        });

        $comments = $comments->latest()->get();
        // $googleTranslateStichoza = new CommonGoogleTranslateService();
        // $target = $post->account->page_language ? $post->account->page_language : 'en';
        // foreach ($comments as $key => $value) {
        //     $translationString    = $googleTranslateStichoza->translate($target, $value['message']);
        //     $value['translation'] = $translationString;
        // }

        $replies = Reply::where('model', 'Comments')->get();

        return view('social-account.comment', compact('post', 'comments', 'replies'));
    }

    public function sync($postId): RedirectResponse
    {
        try {
            $post = SocialPost::where('id', $postId)->with('account')->firstOrFail();
            $fb = new FB($post->account);
            $is_facebook = $post->account->platform == 'facebook';
            $comments = $is_facebook ? $fb->getPostComments($post->ref_post_id) : $fb->getInstaPostComments($post->ref_post_id);
            $socialConfig = SocialConfig::where('id', $post->account->id)->first();
            $target = $socialConfig->page_language;

            $googleTranslateStichoza = new CommonGoogleTranslateService;
            foreach ($comments as $comment) {
                if ($is_facebook) {
                    $translated_message = $googleTranslateStichoza->translate($target, $comment['message']);
                } else {
                    $translated_message = $googleTranslateStichoza->translate($target, $comment['text']);
                }
                $parent = SocialComments::updateOrCreate(['comment_ref_id' => $comment['id']], [
                    'commented_by_id' => $comment['from']['id'],
                    'commented_by_user' => $is_facebook ? $comment['from']['name'] : $comment['from']['username'],
                    'post_id' => $post->id,
                    'config_id' => $post->account->id,
                    'message' => $is_facebook ? $comment['message'] : $comment['text'],
                    'translated_message' => $translated_message,
                    'parent_id' => null,
                    'can_comment' => $is_facebook ? $comment['can_comment'] : true,
                    'created_at' => $is_facebook ? Carbon::parse($comment['created_time']) : Carbon::parse($comment['timestamp']),
                ]);

                $messageModel = ChatMessage::create([
                    'message' => $is_facebook ? $comment['message'] : $comment['text'],
                    'message_type' => $is_facebook ? 'FB_COMMENT' : 'IG_COMMENT',
                    'message_type_id' => $parent->id,
                ]);
                if ($socialConfig->storeWebsite->ai_assistant == 'geminiai') {
                    MessageHelper::sendGeminiAiReply($messageModel->message, 'COMMENT', $messageModel, $socialConfig->storeWebsite);
                }
                // else {
                //     //Here is the logic for Watson Reply
                // }

                if (isset($comment['comments'])) {
                    foreach ($comment['comments'] as $c) {
                        $trans_message = $googleTranslateStichoza->translate($target, $c['message']);
                        SocialComments::updateOrCreate(['comment_ref_id' => $c['id']], [
                            'commented_by_id' => $c['from']['id'],
                            'commented_by_user' => $c['from']['name'],
                            'post_id' => $post->id,
                            'config_id' => $post->account->id,
                            'message' => $c['message'],
                            'translated_message' => $trans_message,
                            'parent_id' => $parent->id,
                            'created_at' => Carbon::parse($c['created_time']),
                        ]);
                    }
                }
                if (isset($comment['replies'])) {
                    foreach ($comment['replies'] as $c) {
                        $trans_text = $googleTranslateStichoza->translate($target, $c['text']);
                        SocialComments::updateOrCreate(['comment_ref_id' => $c['id']], [
                            'commented_by_id' => $c['from']['id'],
                            'commented_by_user' => $c['from']['username'],
                            'post_id' => $post->id,
                            'config_id' => $post->account->id,
                            'message' => $c['text'],
                            'translated_message' => $trans_text,
                            'parent_id' => $parent->id,
                            'created_at' => Carbon::parse($c['timestamp']),
                        ]);
                    }
                }
            }

            return redirect()->back()->with('Success', 'Comments synced successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('Error', 'Comments cannot be synced');
        }
    }

    public function allcomments(Request $request)
    {
        $search = $request->get('search');
        $comments = SocialComments::with(['sub_comments', 'post', 'post.account'])->whereNull('parent_id');
        $comments = $comments->when($request->has('search'), function (Builder $builder) use ($search) {
            return $builder->whereLike(['comment_id', 'message'], $search);
        });

        $comments = $comments->latest()->paginate();
        foreach ($comments as $value) {
            $target = $value['post']['account']['page_language'];
            $value['page_language'] = $target;
        }

        $replies = Reply::where('model', 'Comments')->get();
        $reply_categories = ReplyCategory::select('id', 'name')
            ->with('approval_leads', 'sub_categories')
            ->where('parent_id', 0)
            ->where('id', 44)
            ->orderBy('name')->get();

        return view('social-account.allcomment', compact('comments', 'replies', 'reply_categories'));
    }

    public function replyComments(Request $request): JsonResponse
    {
        try {
            $comments = SocialComments::where('parent_id', $request->id)->with('user')->latest()->get();

            return response()->json(['comments' => $comments]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function devCommentsReply(Request $request): JsonResponse
    {
        $commentId = $request->contactId;
        $message = $request->get('input');
        $base_comment = SocialComments::find($commentId);
        $socialConfig = SocialConfig::where('id', $base_comment->config_id)->first();
        $is_facebook = $socialConfig->platform == 'facebook';
        $googleTranslateStichoza = new CommonGoogleTranslateService;
        $target = $socialConfig->page_language;
        $translated_message = $googleTranslateStichoza->translate($target, $message);
        try {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => '']);
            $fb = new FB($socialConfig);
            $response = $is_facebook ? $fb->replyToPostComments($translated_message, $base_comment->comment_ref_id) : $fb->replyToInstaPostComments($translated_message, $base_comment->comment_ref_id);
            if (isset($response['id'])) {
                SocialComments::updateOrCreate(['comment_ref_id' => $response['id']], [
                    'commented_by_id' => $socialConfig->account_id,
                    'commented_by_user' => $socialConfig->user_name,
                    'post_id' => $base_comment->post_id,
                    'config_id' => $base_comment->config_id,
                    'message' => $message,
                    'translated_message' => $translated_message,
                    'parent_id' => $base_comment->id,
                    'user_id' => auth()->user()->id,
                ]);

                SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Comment Added) => Reply on Comment Successfully', ['data' => $response]);

                return response()->json([
                    'message' => 'Message sent successfully',
                ]);
            }
        } catch (Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => $e]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getEmailreplies(Request $request)
    {
        $id = $request->id;
        $emailReplies = Reply::where('category_id', $id)->orderBy('id')->get();

        return json_encode($emailReplies);
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $comment = SocialComments::where('id', $id)->first();
        if ($comment) {
            $commentScore = app('translation-lambda-helper')->getTranslateScore($comment->message, $comment->translated_message);

            $comment->translated_message_score = ($commentScore != 0) ? $commentScore : 0.1;
            $comment->save();

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong comment id!']);
        }
    }
}
