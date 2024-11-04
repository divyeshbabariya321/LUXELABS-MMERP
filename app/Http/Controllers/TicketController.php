<?php

namespace App\Http\Controllers;
use App\Http\Controllers\WhatsAppController;

use App\Http\Requests\TicketPriceUpdateRequest;
use App\Mails\Manual\PriceDropNotif;
use App\Product;
use App\Setting;
use App\Tickets;
use App\TicketStatuses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'tickets';

        $selectArray[] = 'tickets.*';
        $selectArray[] = 'users.name AS assigned_to_name';
        $selectArray[] = 'customers.is_auto_simulator AS customer_auto_simulator';
        $query = Tickets::query();
        $query = $query->leftjoin('users', 'users.id', '=', 'tickets.assigned_to');
        $query = $query->leftjoin('customers', 'customers.id', '=', 'tickets.customer_id');

        $query = $query->select($selectArray);

        if ($request->ticket_id != '') {
            $query = $query->whereIn('ticket_id', $request->ticket_id);
        }

        if ($request->users_id != '') {
            $query = $query->whereIn('assigned_to', $request->users_id);
        }

        if ($request->term != '') {
            $query = $query->whereIn('tickets.name', $request->term);
        }

        if ($request->user_email != '') {
            $query = $query->whereIn('tickets.email', $request->user_email);
        }

        if ($request->user_message != '') {
            $query = $query->where('tickets.message', 'LIKE', '%'.$request->user_message.'%');
        }

        if ($request->search_country != '') {
            $query = $query->where('tickets.country', 'LIKE', '%'.$request->search_country.'%');
        }

        if ($request->search_order_no != '') {
            $query = $query->where('tickets.order_no', 'LIKE', '%'.$request->search_order_no.'%');
        }

        if ($request->search_phone_no != '') {
            $query = $query->where('tickets.phone_no', 'LIKE', '%'.$request->search_phone_no.'%');
        }

        // if ($request->serach_inquiry_type != '') {
        //     $query = $query->where('tickets.type_of_inquiry', 'LIKE', '%' . $request->serach_inquiry_type . '%');
        // }
        $query = $query->where('tickets.type_of_inquiry', 'LIKE', '%Price-Match%');

        // Use for search by source tof ticket
        if ($request->search_source != '') {
            $query = $query->where('tickets.source_of_ticket', 'LIKE', '%'.$request->search_source.'%');
        }

        if ($request->status_id != '') {
            $query = $query->whereIn('status_id', $request->status_id);
        }

        if ($request->date != '') {
            $query = $query->whereDate('tickets.created_at', $request->date);
        }

        $pageSize = Setting::get('pagination', 25);
        if ($pageSize == '') {
            $pageSize = 1;
        }

        $query = $query->groupBy('tickets.ticket_id');
        $data = $query->orderByDesc('created_at')->paginate($pageSize)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('ticket.partials.table_list', compact('data'))->with('i', ($request->input('page', 1) - 1) * $pageSize)->render(),
                'links' => (string) $data->links(),
                'count' => $data->total(),
            ], 200);
        }
        $taskstatus = TicketStatuses::get();

        return view('ticket.list', compact('data', 'taskstatus'))->with('i', ($request->input('page', 1) - 1) * $pageSize);
    }

    public function updatePrice(TicketPriceUpdateRequest $request)
    {
        try {
            $ticket = Tickets::find($request->ticket_id);

            if ($ticket) {

                $ticket->amount = $request->product_price;
                $ticket->save();

                Product::where('sku', $ticket->sku)->update(['price' => $request->product_price]);

                $this->sendNotification($request);

                return redirect()->route('ticket.index')->with('success', 'Price successfully updated');
            } else {
                return redirect()->back()->with('error', 'Record not found');
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function sendNotification(Request $request)
    {
        try {
            $ticketsData = Tickets::select('pr.id as prod_id', 'pr.price as prod_real_price', 'pr.sku as prod_sku', 'pr.name as prod_name', 'tickets.*')->join('products as pr', 'pr.sku', '=', 'tickets.sku')->where('tickets.id', $request->ticket_id)->get();

            foreach ($ticketsData as $ticket) {
                if ($ticket->prod_real_price <= $ticket->amount) {
                    if ($ticket->notify_on == 'phone' && $ticket->phone_no != null) {
                        $message = 'Your are recieving this message as a notification for your inquiry Ticket No '.$ticket->ticket_id.' regarding price drop for product '.$ticket->prod_name;
                        $requestData = new Request;
                        $requestData->setMethod('POST');
                        $requestData->request->add(['ticket_id' => $ticket->id, 'message' => $message, 'status' => 1]);
                        app(WhatsAppController::class)->sendMessage($requestData, 'ticket');
                    } elseif ($ticket->notify_on == 'email' && $ticket->email != null) {
                        Mail::to($ticket->email)->send(new PriceDropNotif($ticket));
                    }

                    $ticketStatus = Tickets::find($request->ticket_id);
                    if ($ticketStatus) {

                        $ticketStatus->status_id = '4';
                        $ticketStatus->save();

                        return response()->json(['status' => 'success', 'code' => 200, 'message' => 'Notification sent successfully']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'code' => 200, 'message' => 'Price is not lower than real price']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }
}
