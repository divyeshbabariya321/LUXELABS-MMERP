<?php

namespace App\Http\Controllers;
use App\CustomerLiveChat;

use App\ChatMessage;
use App\Customer;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwiliochatController extends Controller
{
    public function getTwilioChat(Request $request): View
    {
        $store_websites = [];
        $website_stores = [];
        $name = '';
        $customerInital = '';

        $query = ChatMessage::query();

        if ($request->number) {
            $query = $query->where('number', 'LIKE', '%'.$request->number.'%');
        }
        if ($request->send_by) {
            $query = $query->where('send_by', 'LIKE', '%'.$request->send_by.'%');
        }
        if ($request->message) {
            $query = $query->where('message', 'LIKE', '%'.$request->message.'%');
        }

        $chat_message = $query->where('message_application_id', 3)->orderByDesc('id')->paginate(25);

        if (count($chat_message) > 0) {
            foreach ($chat_message as $chat) {
                if ($chat->user_id != 0) {
                    // Finding Agent
                    if ($chat->customer_id != '') {
                        $obj = Customer::where('id', $chat->customer_id)->first();
                        $chat->customer_name = $obj->name;
                        $chat->customer_email = $obj->email;
                    }

                    $agent = User::where('id', $chat->user_id)->first();
                    $agentInital = $agent ? substr($agent->name, 0, 1) : '';

                    $chat->message = '<div data-chat-id="'.$chat->id.'" class="d-flex mb-4"><div class="rounded-circle user_inital">'.$agentInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time"> '.\Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>';
                } else {
                    if ($chat->customer_id != '') {
                        $obj = Customer::where('id', $chat->customer_id)->first();
                        $chat->customer_name = $obj->name;
                        $chat->customer_email = $obj->email;
                    }
                    $chat->message = '<div data-chat-id="'.$chat->id.'" class="d-flex justify-content-start mb-4"><div class="rounded-circle user_inital">'.$customerInital.'</div><div class="msg_cotainer">'.$chat->message.'<span class="msg_time"> '.\Carbon\Carbon::createFromTimeStamp(strtotime($chat->created_at))->diffForHumans().'</span></div></div>';
                }
            }
        }

        $chatIds = CustomerLiveChat::latest()->orderBy('seen', 'asc')->orderBy('status', 'desc')->get();
        $newMessageCount = CustomerLiveChat::where('seen', 0)->count();

        return view('twilio.chatMessages', compact('chat_message', 'name', 'customerInital', 'store_websites', 'website_stores', 'chatIds', 'newMessageCount'));
    }

    public function chatsDelete(Request $request): RedirectResponse
    {
        $id = $request->id;
        ChatMessage::where('id', $id)->delete();

        return redirect()->to('twilio/getChats')->with('flash_type', 'alert-info')->with('message', 'Deleted Successfully.');
    }

    public function twilioChatsEdit(Request $request): View
    {
        $id = $request->id;
        $data = ChatMessage::where('id', $id)->first();

        return view('twilio.edit', compact('data'));
    }

    public function twilioChatsUpdate(Request $request): JsonResponse
    {
        $input = $request->all();
        $data = ChatMessage::where('id', $input['id'])->first();

        $data->update($input);

        return response()->json(['code' => '200']);
    }
}
