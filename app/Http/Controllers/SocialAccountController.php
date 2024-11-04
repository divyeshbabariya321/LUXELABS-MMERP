<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\SocialContact;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Models\SocialMessages;
use App\Services\GoogleTranslateStichoza;
use App\Social\SocialConfig;
use App\SocialContactThread;
use App\Services\CommonGoogleTranslateService;
use App\StoreWebsite;
use Exception;


class SocialAccountController extends Controller
{
    /**
     * Show inbox user list
     */
    public function inbox(Request $request): View
    {

        $socialContact = SocialContact::with('socialConfig.storeWebsite', 'thread_messages');
        if($request->social_config){
            $socialContact = $socialContact->whereHas('socialConfig', function ($query) use ($request) {
                $query->whereIn('platform', $request->social_config);
            });
        }
        if($request->name){
            $socialContact = $socialContact->whereHas('socialConfig', function ($query) use ($request) {
                $query->whereIn('name', $request->name);
            });
        }
        if($request->page_language){
            $socialContact = $socialContact->whereHas('socialConfig', function ($query) use ($request) {
                $query->whereIn('page_language', $request->page_language);
            });
        }
        if($request->store_website_id){
            $socialContact = $socialContact->whereHas('socialConfig.storeWebsite', function ($query) use ($request) {
                $query->whereIn('id', $request->store_website_id);
            });
        }
        if($request->from_date){
            $socialContact = $socialContact->whereHas('thread_messages', function ($query) use ($request) {
                $query->whereDate('created_at','>=' ,$request->from_date);
            });
        }
        if($request->to_date){
            $socialContact = $socialContact->whereHas('thread_messages', function ($query) use ($request) {
                $query->whereDate('created_at','<=' ,$request->to_date);
            });
        }
        $socialContact = $socialContact->get();

        $websites      = StoreWebsite::select('id', 'title')->get();
        $socialconfigs = SocialConfig::get();
        return view('instagram.inbox', compact('socialContact','websites','socialconfigs'));
    }

    /**
     * List Message of specific user
     */
    public function listMessage(Request $request): JsonResponse
    {
        try {
            $contactId = $request->id;
            $messages  = SocialContactThread::where('social_contact_id', $contactId)->with('socialContact', 'socialContact.socialConfig')->get();

            return response()->json(['messages' => $messages]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sending Message to Social contact user
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $contact      = SocialContact::with('socialConfig')->findOrFail($request->contactId);
        $page_id      = $contact->socialConfig->page_id;
        $to           = $contact->account_id;
        $from         = $page_id;

        $message = $request->input;
        $googleTranslateStichoza = new CommonGoogleTranslateService();
        $target = $contact->socialConfig->page_language;
        $translated_message   = $googleTranslateStichoza->translate($target, $message);

        $fb = new FB($contact->socialConfig);

        try {
            $response = $fb->replyFbMessage($contact->socialConfig->page_id, $to, $translated_message);
            $contact->thread_messages()->create([
                'sender_id'         => $from,
                'recipient_id'      => $to,
                'text'              => $message,
                'translated_text'   => $translated_message,
                'message_id'        => $response['message_id'],
                'created_at'        => Carbon::now(),
            ]);

            return response()->json([
                'message' => 'Message sent successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'exception' => $e->getMessage(),
                'message'   => 'Unable to sent message',
            ]);
        }
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $messages  = SocialContactThread::where('id', $id)->first();
        if ($messages) {
            $msgScore = app("translation-lambda-helper")->getTranslateScore($messages->text, $messages->translated_text);
            
            $messages->translated_text_score = ($msgScore != 0) ? $msgScore : 0.1;
            $messages->save();

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong messasge id!']);
        }
    }
}
