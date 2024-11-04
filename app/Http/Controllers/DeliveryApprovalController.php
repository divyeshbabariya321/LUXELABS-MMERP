<?php

namespace App\Http\Controllers;
use App\Http\Controllers\WhatsAppController;

use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Helpers;
use App\ChatMessage;
use App\PrivateView;
use App\StatusChange;
use App\DeliveryApproval;
use Illuminate\Http\Request;

class DeliveryApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $delivery_approvals = DeliveryApproval::all();
        $users_array        = Helpers::getUserArray(User::all());

        $mediaTags =  config('constants.media_tags'); // Use config variable

        return view('deliveryapprovals.index', [
            'delivery_approvals' => $delivery_approvals,
            'users_array'        => $users_array,
            'media_tags'        => $mediaTags,
        ]);
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

    public function updateStatus(Request $request, $id): Response
    {
        $delivery_approval = DeliveryApproval::find($id);

        StatusChange::create([
            'model_id'    => $delivery_approval->id,
            'model_type'  => DeliveryApproval::class,
            'user_id'     => Auth::id(),
            'from_status' => $delivery_approval->status,
            'to_status'   => $request->status,
        ]);

        $delivery_approval->status = $request->status;
        $delivery_approval->save();

        if ($request->status == 'delivered') {
            $delivery_approval->private_view->products[0]->supplier = '';
            $delivery_approval->private_view->products[0]->save();

            // Message to Customer
            $params = [
                'number'      => null,
                'user_id'     => Auth::id(),
                'customer_id' => $delivery_approval->private_view->customer_id,
                'message'     => 'This product has been delivered. Thank you for your business',
                'approved'    => 0,
                'status'      => 1,
            ];

            ChatMessage::create($params);
        } elseif ($request->status == 'returned') {
            $delivery_approval->private_view->products[0]->supplier = 'In-stock';
            $delivery_approval->private_view->products[0]->save();

            // Message to Stock Coordinator
            $params = [
                'number'   => null,
                'user_id'  => Auth::id(),
                'message'  => 'This product will be sent back',
                'approved' => 0,
                'status'   => 1,
            ];

            ChatMessage::create($params);

            
            $stock_coordinators = User::role('Stock Coordinator')->get();

            foreach ($stock_coordinators as $coordinator) {
                $params['erp_user'] = $coordinator->id;
                $chat_message       = ChatMessage::create($params);

                $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                $chat_message->update([
                    'approved' => 1,
                    'status'   => 2,
                ]);
            }

            // Message to Aliya
            $coordinators = User::role('Delivery Coordinator')->get();

            foreach ($coordinators as $coordinator) {
                $params['erp_user'] = $coordinator->id;
                $chat_message       = ChatMessage::create($params);

                $whatsapp_number = $coordinator->whatsapp_number != '' ? $coordinator->whatsapp_number : null;

                app(WhatsAppController::class)->sendWithNewApi($coordinator->phone, $whatsapp_number, $params['message'], null, $chat_message->id);

                $chat_message->update([
                    'approved' => 1,
                    'status'   => 2,
                ]);
            }
        }

        if ($delivery_approval->private_view) {
            $delivery_approval->private_view->status = $request->status;
            $delivery_approval->private_view->save();

            StatusChange::create([
                'model_id'    => $delivery_approval->private_view->id,
                'model_type'  => PrivateView::class,
                'user_id'     => Auth::id(),
                'from_status' => $delivery_approval->private_view->status,
                'to_status'   => $request->status,
            ]);
        }

        return response('success');
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
}
