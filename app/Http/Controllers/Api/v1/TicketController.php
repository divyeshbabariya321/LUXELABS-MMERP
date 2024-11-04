<?php

namespace App\Http\Controllers\Api\v1;

use App\ChatMessage;
use App\Customer;
use App\Email;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Mails\Manual\TicketCreate;
use App\Models\TicketsImages;
use App\Services\CommonGoogleTranslateService;
use App\Tickets;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @SWG\Post(
     *   path="/ticket/create",
     *   tags={"Ticket"},
     *   summary="create ticket",
     *   operationId="create-ticket",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:80',
            'last_name' => 'required|max:80',
            'email' => 'required|email',
            'type_of_inquiry' => 'required',
            'subject' => 'required|max:80',
            'message' => 'required',
            'source_of_ticket' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        if (isset($request->notify_on) && ! in_array($request->notify_on, ['email', 'phone'])) {
            return $this->errorResponse('ticket.failed.email_or_phone', 'notify_on field must be either email or phone!', [], 400);
        }

        $data = $this->prepareTicketData($request);

        $ticket = Tickets::create($data);

        $this->handleImages($request, $ticket);

        $this->dispatchEmailNotification($ticket);

        if ($ticket) {
            $this->checkMessageAndSendReply($ticket->id);

            return $this->successResponse($ticket, 'ticket.success', 'Ticket created successfully');
        }

        return $this->errorResponse('ticket.failed', 'Unable to create ticket', [], 500);
    }

    private function prepareTicketData(Request $request): array
    {
        $data = $request->all();
        $data['ticket_id'] = 'T'.date('YmdHis');
        $data['status_id'] = 1;
        $data['resolution_date'] = Carbon::now()->addDays(2)->format('Y-m-d H:i:s');

        if (! empty($request->lang_code)) {
            $lang = explode('_', str_replace('-', '_', $request->lang_code));
            $data['lang_code'] = $lang[1] ?? $request->lang_code;
        }

        return $data;
    }

    private function handleImages(Request $request, $ticket): void
    {
        try {
            if ($request->hasFile('images') && is_array($request->file('images'))) {
                $directoryPath = public_path('images/tickets');
                if (! File::isDirectory($directoryPath)) {
                    File::makeDirectory($directoryPath, 0777, true, true);
                }
                foreach ($request->file('images') as $image) {
                    $img = new TicketsImages;
                    $img->setTicketId($ticket->id);
                    $img->setFile($image);
                    $img->save();
                }
            }
        } catch (Exception $e) {
            Log::error('Error handling ticket images: '.$e->getMessage());
        }
    }

    private function dispatchEmailNotification($ticket): void
    {
        $emailClass = (new TicketCreate($ticket))->build();

        if (! empty($emailClass->fromMailer) && ! empty($emailClass->subject)) {
            $email = Email::create([
                'model_id' => $ticket->id,
                'model_type' => Tickets::class,
                'from' => $emailClass->fromMailer,
                'to' => $ticket->email,
                'subject' => $emailClass->subject,
                'message' => $emailClass->render(),
                'template' => 'ticket-create',
                'additional_data' => $ticket->id,
                'status' => 'pre-send',
                'is_draft' => 1,
            ]);

            SendEmail::dispatch($email)->onQueue('send_email');
        } else {
            $this->generate_erp_response('ticket.failed', 0, 'Please set email category with template first.', request('lang_code'));
        }
    }

    private function successResponse($ticket, string $messageKey, string $defaultMessage): JsonResponse
    {
        $message = $this->generate_erp_response($messageKey, 0, $defaultMessage, request('lang_code'));

        return response()->json(['status' => 'success', 'data' => ['id' => $ticket->ticket_id], 'message' => $message], 200);
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

    /**
     * @SWG\Post(
     *   path="/ticket/send",
     *   tags={"Ticket"},
     *   summary="Send ticket to customers",
     *   operationId="send-ticket-to-customer",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendTicketsToCustomers(Request $request): JsonResponse
    {
        $validationResponse = $this->validateRequest($request);
        if ($validationResponse) {
            return $validationResponse;
        }

        $ticketsQuery = Tickets::select('tickets.*', 'ts.name as status')
            ->where('source_of_ticket', $request->website)
            ->when($request->email, fn ($query) => $query->where('email', $request->email))
            ->when($request->ticket_id, fn ($query) => $query->where('ticket_id', $request->ticket_id))
            ->join('ticket_statuses as ts', 'ts.id', '=', 'tickets.status_id');

        if ($request->action === 'send_message') {
            $this->sendMessage($request);
        }

        $tickets = $ticketsQuery->paginate($request->per_page ?? '');

        if ($tickets->isEmpty()) {
            return $this->errorResponse('ticket.send.failed', 'Tickets not found for customer !');
        }

        $tickets->transform(fn ($ticket) => $this->attachMessagesToTicket($ticket));

        return response()->json(['status' => 'success', 'tickets' => $tickets], 200);
    }

    private function validateRequest($request): ?JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('ticket.send.failed.validation', 'Please check validation errors!', 400, $validator->errors());
        }

        if (empty($request->email) && empty($request->ticket_id)) {
            return $this->errorResponse('ticket.send.failed.ticket_or_email', 'Please input either email or ticket_id!');
        }

        return null;
    }

    private function sendMessage($request)
    {
        $ticket = Tickets::where('source_of_ticket', $request->website)
            ->where('ticket_id', $request->ticket_id)
            ->first();

        if ($ticket) {
            $messageEn = $request->get('message');
            $messageScore = null;

            if ($ticket->lang_code !== '' && $ticket->lang_code !== 'en') {
                $messageEn = (new CommonGoogleTranslateService)->translate('en', $messageEn);
                $messageScore = app('translation-lambda-helper')->getTranslateScore($request->get('message'), $messageEn);
            }

            ChatMessage::create([
                'message' => $request->get('message'),
                'message_en' => $messageEn,
                'message_score' => $messageScore,
                'ticket_id' => $ticket->id,
                'user_id' => 6,
                'approved' => 1,
                'status' => 2,
            ]);
        }
    }

    private function attachMessagesToTicket($ticket)
    {
        $messages = ChatMessage::where('ticket_id', $ticket->id)
            ->select('id', 'message', 'created_at', 'user_id')
            ->latest()
            ->get()
            ->map(function ($message) {
                $message->send_by = $message->user_id == 6 ? 'Customer' : 'Admin';

                if ($message->user_id) {
                    $userData = User::find($message->user_id);
                    if ($userData) {
                        $message->send_by = $userData->screen_name ?: $userData->name;
                    }
                }

                return $message;
            });

        $ticket->messages = $messages;

        return $ticket;
    }

    private function errorResponse($errorKey, $defaultMessage, $statusCode = 400, $errors = [])
    {
        $message = $this->generate_erp_response($errorKey, 0, $defaultMessage, request('lang_code'));

        // Check $statusCode is an integer
        if (! is_int($statusCode)) {
            $statusCode = 400;
        }

        // Check $errors is an array and not a MessageBag object
        if ($errors instanceof MessageBag) {
            $errors = $errors->toArray();
        }

        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /*Get message reply for ticket from database of Watson */
    public function checkMessageAndSendReply($ticker_id)
    {
        $get_ticket_data = Tickets::where(['id' => $ticker_id])->first();
        if (! empty($get_ticket_data)) {
            $customer = Customer::where('email', $get_ticket_data->email)->first();

            if (! empty($customer)) {
                $params = [
                    'number' => $customer->phone,
                    'message' => $get_ticket_data->message,
                    'media_url' => null,
                    'approved' => 0,
                    'status' => 0,
                    'contact_id' => null,
                    'erp_user' => null,
                    'supplier_id' => null,
                    'task_id' => null,
                    'dubizzle_id' => null,
                    'vendor_id' => null,
                    'customer_id' => $customer->id,
                    'ticket_id' => $ticker_id,
                ];
                $messageModel = ChatMessage::create($params);

                if ($customer->storeWebsite->ai_assistant == 'geminiai') {
                    MessageHelper::sendGeminiAiReply($get_ticket_data->message, 'Ticket', $messageModel, $customer->storeWebsite, $customer);
                } else {
                    MessageHelper::sendwatson($customer, $get_ticket_data->message, null, $messageModel, $params);
                }
            } else {
                $this->generate_erp_response('ticket.send.failed', 0, 'Customer not found having email ='.$get_ticket_data->email, request('lang_code'));
            }
        }

        return true;
    }
}
