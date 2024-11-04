<?php

namespace App\Http\Controllers;
use App\Vendor;
use App\Supplier;
use App\Http\Controllers\WhatsAppController;
use App\Customer;
use App\ChatMessage;
use App\BroadcastMessageNumber;
use App\BroadcastMessage;
use App\BroadcastDetails;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use seo2websites\ErpCustomer\ErpCustomer;
use Exception;

class BroadcastController extends Controller
{
    public function index(Request $request): View
    {
        $inputs = $request->input();
        $data   = BroadcastMessage::with('numbers');

        //Suppliers
        $suppliers = Supplier::all();
        //Vendors
        $vendors = Vendor::all();
        //Customers
        $customers = Customer::all();

        if (@$inputs['name']) {
            $data->where('name', 'like', '%' . $inputs['name'] . '%');
        }

        if (@$inputs['order']) {
            $data->orderBy('id', $inputs['order']);
        } else {
            $data->latest();
        }

        $data = $data->paginate(15);

        return view('broadcast-messages.index', compact('data', 'inputs', 'suppliers', 'customers', 'vendors'));
    }

    public function deleteMessage(Request $request): JsonResponse
    {
        $ID      = $request->id;
        $deleted = BroadcastMessage::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Message deleted successfully']);
    }

    public function deleteType(Request $request): JsonResponse
    {
        $ID      = $request->id;
        $deleted = BroadcastMessageNumber::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Type deleted successfully']);
    }

    public function messagePreviewNumbers(Request $request): JsonResponse
    {
        $id    = $request->id;
        $lists = BroadcastMessageNumber::with(['customer', 'vendor', 'supplier'])->where('broadcast_message_id', $id)->orderByDesc('id')->get();

        return response()->json(['code' => 200, 'data' => $lists]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $data    = BroadcastMessageNumber::where(['broadcast_message_id' => $request->id])->orderByDesc('id')->groupBy('type_id')->get();
        $isEmail = $request->is_email;
        $params  = [];
        $message = [];
        //Create broadcast
        $BroadcastDetails = BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);
        if (count($data)) {
            foreach ($data as $key => $item) {
                if ($item->type == 'App\Http\Controllers\App\Vendor') {
                    //Vendor
                    $message = [
                        'type_id'              => $item->type_id,
                        'type'                 => Vendor::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $params = [
                        'vendor_id'            => $item->type_id,
                        'number'               => null,
                        'message'              => $request->message,
                        'user_id'              => Auth::id(),
                        'status'               => 2,
                        'approved'             => 1,
                        'is_queue'             => 0,
                        'is_email'             => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = ChatMessage::create($params);

                    $approveRequest = new Request();
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);

                    app(WhatsAppController::class)->approveMessage('vendor', $approveRequest, $chat_message->id);
                } elseif ($item->type == 'App\Http\Controllers\App\Supplier') {
                    //Supplier
                    $message = [
                        'type_id'              => $item->type_id,
                        'type'                 => Supplier::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $params = [
                        'supplier_id'          => $item->type_id,
                        'number'               => null,
                        'message'              => $request->message,
                        'user_id'              => Auth::id(),
                        'status'               => 1,
                        'is_email'             => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = ChatMessage::create($params);

                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);
                    app(WhatsAppController::class)->approveMessage('supplier', $myRequest, $chat_message->id);
                } else {
                    //Customer
                    $sendingData = [];

                    $message = [
                        'type_id'              => $item->type_id,
                        'type'                 => ErpCustomer::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $params = [
                        'sending_time'         => $request->get('sending_time', ''),
                        'user_id'              => Auth::id(),
                        'message'              => $request->message,
                        'phone'                => null,
                        'type'                 => 'message_all',
                        'data'                 => json_encode($sendingData),
                        'group_id'             => '',
                        'is_email'             => $isEmail,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = ChatMessage::create($params);
                    $custRequest  = new Request();
                    $custRequest->setMethod('POST');
                    $custRequest->request->add(['messageId' => $chat_message->id, 'subject' => $request->name]);
                    app(WhatsAppController::class)->approveMessage('customer', $custRequest, $chat_message->id);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent successfully']);
    }

    public function sendType(Request $request): JsonResponse
    {
        if ($request->all()) {
            if (count($request->values)) {
                foreach ($request->values as $_value) {
                    if ($request->type == 'vendor') {
                        $message = [
                            'type_id'              => $_value,
                            'type'                 => Vendor::class,
                            'broadcast_message_id' => $request->id,
                        ];
                        $broadcastnumber = BroadcastMessageNumber::create($message);
                    } elseif ($request->type == 'supplier') {
                        $message = [
                            'type_id'              => $_value,
                            'type'                 => Supplier::class,
                            'broadcast_message_id' => $request->id,
                        ];
                        $broadcastnumber = BroadcastMessageNumber::create($message);
                    } else {
                        $message = [
                            'type_id'              => $_value,
                            'type'                 => ErpCustomer::class,
                            'broadcast_message_id' => $request->id,
                        ];
                        $broadcastnumber = BroadcastMessageNumber::create($message);
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Data added successfully']);
    }

    /**
     * This function user for get the broadcast user group list
     */
    public function getSendType(Request $request): JsonResponse
    {
        try {
            $broadData = BroadcastMessageNumber::with(['customer', 'vendor', 'supplier'])->where(['broadcast_message_id' => $request->id])->orderByDesc('id')->groupBy('type_id')->get();

            return response()->json(['code' => 200, 'data' => $broadData, 'message' => 'Data Listed successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function resendMessage(Request $request): JsonResponse
    {
        if ($request['is_last'] == 1) {
            $data = BroadcastMessageNumber::where(['broadcast_message_id' => $request->id])->get();
        } else {
            $data = BroadcastMessageNumber::where(['id' => $request->id])->get();
        }
        $params  = [];
        $message = [];

        if (count($data)) {
            foreach ($data as $key => $item) {
                if ($item->type == 'App\Http\Controllers\App\Vendor') {
                    if ($request['is_last'] == 1) {
                        $message_data = ChatMessage::where('vendor_id', $item->type_id)->latest()->first();
                    } else {
                        $message_data = ChatMessage::where('broadcast_numbers_id', $item->id)->first();
                    }
                    //Vendor
                    $message = [
                        'type_id'              => $item->type_id,
                        'type'                 => Vendor::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $params = [
                        'vendor_id'            => $item->type_id,
                        'number'               => null,
                        'message'              => $message_data->message,
                        'user_id'              => Auth::id(),
                        'status'               => 2,
                        'approved'             => 1,
                        'is_queue'             => 0,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = ChatMessage::create($params);

                    $BroadcastDetails = BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                    $approveRequest = new Request();
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id]);

                    app(WhatsAppController::class)->approveMessage('vendor', $approveRequest);
                } elseif ($item->type == 'App\Http\Controllers\App\Supplier') {
                    if ($request['is_last'] == 1) {
                        $message_data = ChatMessage::where('supplier_id', $item->type_id)->latest()->first();
                    } else {
                        $message_data = ChatMessage::where('broadcast_numbers_id', $item->id)->first();
                    }
                    //Supplier
                    $message = [
                        'type_id'              => $item->type_id,
                        'type'                 => Supplier::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $params = [
                        'supplier_id'          => $item->type_id,
                        'number'               => null,
                        'message'              => $message_data->message,
                        'user_id'              => Auth::id(),
                        'status'               => 1,
                        'broadcast_numbers_id' => $broadcastnumber->id,
                    ];
                    $chat_message = ChatMessage::create($params);

                    $BroadcastDetails = BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add(['messageId' => $chat_message->id]);
                    app(WhatsAppController::class)->approveMessage('supplier', $myRequest);
                } else {
                    //Customer
                    $sendingData = [];
                    $message     = [
                        'type_id'              => $item->type_id,
                        'type'                 => ErpCustomer::class,
                        'broadcast_message_id' => $request->id,
                    ];
                    $broadcastnumber = BroadcastMessageNumber::create($message);

                    $BroadcastDetails = BroadcastDetails::create(['broadcast_message_id' => $request->id, 'name' => $request->name, 'message' => $request->message]);

                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent successfully']);
    }

    public function showMessage(Request $request): JsonResponse
    {
        $massage = BroadcastDetails::where(['broadcast_message_id' => $request->id])->get();
        if (count($massage)) {
            return response()->json(['code' => 200, 'data' => $massage]);
        } else {
            $lists_item = [];

            return response()->json(['code' => 300, 'data' => $lists_item]);
        }
    }
}
