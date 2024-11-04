<?php

namespace App\Http\Controllers;
use App\StoreWebsiteTwilioNumber;

use App\Customer;
use App\MarketingMessage;
use App\MarketingMessageCustomer;
use App\MessagingGroup;
use App\MessagingGroupCustomer;
use App\SmsService;
use App\StoreWebsite;
use App\TwilioError;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Twilio\Rest\Client;
use Exception;

class TwillioMessageController extends Controller
{
    public function index(Request $request): View
    {
        $inputs = $request->input();
        $data = MessagingGroup::leftJoin('sms_service', 'sms_service.id', '=', 'messaging_groups.service_id')
            ->leftJoin('store_websites', 'store_websites.id', '=', 'messaging_groups.store_website_id')
            ->leftJoin('marketing_messages', 'marketing_messages.message_group_id', '=', 'messaging_groups.id')
            ->select('messaging_groups.*', 'marketing_messages.title', 'marketing_messages.is_sent', 'marketing_messages.scheduled_at',
                'sms_service.name as service', 'store_websites.title as website');
        if (isset($inputs['status']) && $inputs['status'] != '') {
            if ($inputs['status'] == 'done') {
                $data = $data->where('is_sent', 1);
            } elseif ($inputs['status'] == 'pending') {
                $data = $data->whereNull('scheduled_at');
            } elseif ($inputs['status'] == 'scheduled') {
                $data = $data->whereNotNull('scheduled_at')->where('is_sent', 0);
            }
        }
        if (isset($inputs['webiste']) && $inputs['webiste'] != '') {
            $data = $data->where('messaging_groups.store_website_id', $inputs['webiste']);
        }
        if (isset($inputs['title'])) {
            $data = $data->where('marketing_messages.title', 'like', '%'.$inputs['title'].'%');
        }
        $data = $data->orderByDesc('messaging_groups.id')->paginate(15);
        $websites = ['' => 'Select Website'] + StoreWebsite::pluck('title', 'id')->toArray();
        $services = ['' => 'Select Service'] + SmsService::pluck('name', 'id')->toArray();

        return view('twillio_sms.index', compact('data', 'websites', 'services', 'inputs'));
    }

    public function showErrors(Request $request): View
    {
        $data = new TwilioError;

        if ($request->sid) {
            $data = $data->where('sid', 'LIKE', '%'.$request->sid.'%');
        }
        if ($request->account_sid) {
            $data = $data->where('account_sid', 'LIKE', '%'.$request->account_sid.'%');
        }
        if ($request->call_sid) {
            $data = $data->where('call_sid', 'LIKE', '%'.$request->call_sid.'%');
        }
        if ($request->error_code) {
            $data = $data->where('error_code', 'LIKE', '%'.$request->error_code.'%');
        }
        if ($request->message) {
            $data = $data->where('message_text', 'LIKE', '%'.$request->message.'%');
        }
        if ($request->date) {
            $data = $data->where('message_date', 'LIKE', '%'.$request->date.'%');
        }

        $data = $data->latest()->paginate(15);
        $inputs = $request->input();

        return view('twillio_sms.errors', compact('data', 'inputs'));
    }

    public function createMessagingGroup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'store_website_id' => 'required',
            'service_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $data = MessagingGroup::create([
            'name' => $request->name,
            'store_website_id' => $request->store_website_id,
            'service_id' => $request->service_id,
        ]);

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'MessagingGroup Created successfully']);
    }

    public function createService(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }
        $input = $request->input();
        $data = SmsService::firstOrCreate(['name' => $input['name']], ['name' => $input['name']]);

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Service Created successfully']);
    }

    public function showCustomerList($messageGroupId): View
    {
        $messageGroupDetails = MessagingGroup::find($messageGroupId);
        $customers = Customer::where('store_website_id', $messageGroupDetails['store_website_id'])->get();
        $customerAdded = MessagingGroupCustomer::leftJoin('customers', 'customers.id', '=', 'messaging_group_customers.customer_id')
            ->where('messaging_group_customers.message_group_id', $messageGroupId)->pluck('customers.id')->toArray();
        $marketing_messageId = MarketingMessage::where('message_group_id', $messageGroupId)->pluck('id')->first();
        $messageSentToCustomers = [];
        if ($marketing_messageId != null) {
            $messageSentToCustomers = MarketingMessageCustomer::where(['marketing_message_id' => $marketing_messageId])->where('is_sent', 1)->pluck('customer_id')->toArray();
        }

        return view('twillio_sms.customer', compact('customers', 'messageGroupId', 'customerAdded', 'messageSentToCustomers'));
    }

    public function removeCustomer(Request $request): JsonResponse
    {
        $customerId = $request->id;
        $messageGroupCustomer = MessagingGroupCustomer::where('id', $customerId)->first();
        if ($messageGroupCustomer != null) {
            $marketing_message = MarketingMessage::where('message_group_id', $messageGroupCustomer['message_group_id'])->first();
            if ($marketing_message != null) {
                MarketingMessageCustomer::where(['marketing_message_id' => $marketing_message->id, 'customer_id' => $customerId])->delete();
            }
            $messageGroupCustomer->delete();
        }

        return response()->json(['code' => 200, 'message' => 'Customer removed from message group successfully']);
    }

    public function deleteMessageGroup(Request $request): JsonResponse
    {
        $messageGroupId = $request->id;
        $messageGroup = MessagingGroup::where('id', $request->id)->first();
        if ($messageGroup != null) {
            $customers = MessagingGroupCustomer::where('message_group_id', $request->id)->delete();
            $marketing_message = MarketingMessage::where('message_group_id', $messageGroupId)->first();
            if ($marketing_message != null) {
                MarketingMessageCustomer::where('marketing_message_id', $marketing_message->id)->delete();
                $marketing_message->delete();
            }
            $messageGroup->delete();
        }

        return response()->json(['code' => 200, 'message' => 'Message group deleted successfully']);
    }

    public function deleteTwilioError(Request $request): JsonResponse
    {

        $ID    = $request->id;
        $error = TwilioError::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Twilio error deleted successfully']);
    }

    public function fetchCustomers(Request $request)
    {
        $q = $request->q;
        $customers = Customer::where('email', 'like', '%'.$q.'%')->select('id', 'email')->get();

        return json_encode($customers);
    }

    public function addCustomer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'message_group_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }
        $customerExist = MessagingGroupCustomer::where(['message_group_id' => $request->message_group_id, 'customer_id' => $request->customer_id])->first();
        if ($customerExist == null) {
            MessagingGroupCustomer::create(['message_group_id' => $request->message_group_id, 'customer_id' => $request->customer_id]);

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Customer added successfully']);
        } else {
            MessagingGroupCustomer::where(['message_group_id' => $request->message_group_id, 'customer_id' => $request->customer_id])->delete();

            return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Customer deleted successfully']);
        }
    }

    public function createMarketingMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'scheduled_at' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag();
            $errors = $errors->toArray();
            $message = '';
            foreach ($errors as $error) {
                $message .= $error[0].'<br>';
            }

            return response()->json(['status' => 'failed', 'statusCode' => 500, 'message' => $message]);
        }

        $data = MarketingMessage::updateOrCreate(['id' => $request->id], [
            'title' => $request->title,
            'scheduled_at' => $request->scheduled_at,
            'is_sent' => 0,
            'message_group_id' => $request->message_group_id,
        ]);
        $customers = MessagingGroupCustomer::where('message_group_id', $request->message_group_id)->get();
        foreach ($customers as $customer) {
            MarketingMessageCustomer::firstOrCreate(['marketing_message_id' => $data['id'], 'customer_id' => $customer['customer_id']],
                ['marketing_message_id' => $data['id'], 'customer_id' => $customer['customer_id']]);
        }

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Message Created successfully']);
    }

    public function messageTitle($messageGroupId): View
    {
        $details = MarketingMessage::where('message_group_id', $messageGroupId)->first();
        if ($details == null) {
            $details['id'] = null;
            $details['title'] = null;
            $details['scheduled_at'] = null;
            $details['message_group_id'] = $messageGroupId;
        }

        return view('twillio_sms.partials.message_title', compact('details'))->render();
    }

    public function processMarketingMessage(Request $request): JsonResponse
    {
        $group_id = $request->get('groupId');
        try {
            $date = Carbon::now();
            $date_added_hour = Carbon::now()->addHours(1);
            $services = SmsService::all();
            foreach ($services as $service) {
                if (strtolower($service['name']) == 'twilio') {
                    $groups = MessagingGroup::where('service_id', $service['id'])->where('id', $group_id)->get();
                    foreach ($groups as $group) {
                        $twilio_cred = StoreWebsiteTwilioNumber::select('twilio_active_numbers.account_sid as a_sid', 'twilio_active_numbers.phone_number as phone_number', 'twilio_credentials.auth_token as auth_token')
                            ->join('twilio_active_numbers', 'twilio_active_numbers.id', '=', 'store_website_twilio_numbers.twilio_active_number_id')
                            ->join('twilio_credentials', 'twilio_credentials.id', '=', 'twilio_active_numbers.twilio_credential_id')
                            ->where('store_website_twilio_numbers.store_website_id', $group->store_website_id)
                            ->first();

                        if (isset($twilio_cred)) {
                            $account_sid = $twilio_cred->a_sid;
                            $auth_token = $twilio_cred->auth_token;
                            $twilio_number = $twilio_cred->phone_number;
                        } else {
                            $account_sid = 'AC23d37fbaf2f8a851f850aa526464ee7d';
                            $auth_token = '51e2bf471c33a48332ea365ae47a6517';
                            $twilio_number = '+18318880662';
                        }

                        $marketing_messages = MarketingMessage::where('message_group_id', $group->id)->where('is_sent', 0)->whereBetween('scheduled_at', [$date, $date_added_hour])->get();
                        foreach ($marketing_messages as $index => $message) {
                            $marketingMessageCustomers = MarketingMessageCustomer::leftJoin('customers', 'customers.id', '=', 'marketing_message_customers.customer_id')
                                ->where('marketing_message_id', $message->id)->select('marketing_message_customers.*', 'customers.phone')->get();
                            foreach ($marketingMessageCustomers as $marketingMessageCustomer) {
                                try {
                                    // Get APP_URL from env because in console Request is not available
                                    $appUrl = config('env.CALL_BACK_URL');
                                    $lastchar = $appUrl[-1];

                                    if (strcmp($lastchar, '/') !== 0) {
                                        $appUrl = $appUrl.'/';
                                    }

                                    $client = new Client($account_sid, $auth_token);
                                    $client->messages->create('+'.$marketingMessageCustomer['phone'], [
                                        'from' => $twilio_number,
                                        'body' => $message['title'],
                                        'statusCallback' => $appUrl.'twilio/handleMessageDeliveryStatus/'.$marketingMessageCustomer['customer_id'].'/'.$marketingMessageCustomer['id'],
                                    ]);
                                    MarketingMessageCustomer::where(['customer_id' => $marketingMessageCustomer['customer_id'], 'marketing_message_id' => $message->id])->update(['is_sent' => 1]);
                                    $message->update(['is_sent' => 1]);
                                } catch (Exception $e) {
                                    $message->twilio_error = $e->getMessage();
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $message->error = $e->getMessage();
        }

        return response()->json(['status' => 'success', 'statusCode' => 200, 'message' => 'Message sent successfully', 'data' => $message]);
    }
}
