<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\CronJob;
use App\Customer;
use App\Email;
use App\EmailAddress;
use App\EmailAssign;
use App\EmailRunHistories;
use App\Exports\EmailFailedReport;
use App\GoogleAdsAccount;
use App\googleTraslationSettings;
use App\Helpers\MessageHelper;
use App\Http\Requests\CreateAcknowledgementEmailAddressRequest;
use App\Http\Requests\StoreEmailAddressRequest;
use App\Http\Requests\UpdateEmailAddressRequest;
use App\Models\EMailAcknowledgement;
use App\Setting;
use App\StoreWebsite;
use App\StoreWebsiteAnalytic;
use App\Supplier;
use App\Tickets;
use App\User;
use App\Vendor;
use App\VirtualminHelper;
use Carbon\Carbon;
use EmailReplyParser\Parser\EmailParser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Webklex\PHPIMAP\ClientManager;

use function Sentry\captureException;

class EmailAddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        try {
            $users = User::orderBy('name')->get();

            $ops = '';
            foreach ($users as $user) {
                $ops .= '<option class="form-control" value="'.$user['id'].'">'.$user['name'].'</option>';
            }

            if ($request->ajax()) {
                $query = EmailAddress::query();
                $query->select('email_addresses.*');
                if ($request->status != '') {
                    $query->whereHas('history_last_message', function ($q) use ($request) {
                        $q->where('is_success', $request->status);
                    });
                }

                if ($request->keyword) {
                    $query->orWhere('driver', 'LIKE', '%'.$request->keyword.'%')
                        ->orWhere('port', 'LIKE', '%'.$request->keyword.'%')
                        ->orWhere('encryption', 'LIKE', '%'.$request->keyword.'%')
                        ->orWhere('send_grid_token', 'LIKE', '%'.$request->keyword.'%')
                        ->orWhere('host', 'LIKE', '%'.$request->keyword.'%');
                }

                if ($request->username != '') {
                    $query->where('username', 'LIKE', '%'.$request->username.'%');
                }

                if ($request->website_id != '') {
                    $query->where('store_website_id', $request->website_id);
                }

                $emailAddress = $query->paginate(Setting::get('pagination', 10))->appends(request()->query());

                return view('email-addresses.index_ajax', [
                    'emailAddress' => $emailAddress,
                    'uops' => $ops,
                ]);

            } else {

                $allStores = StoreWebsite::pluck('title', 'id')->all();
                $runHistoriesCount = EmailRunHistories::count();

                // Retrieve all email addresses
                $emailAddresses = new EmailAddress;
                $allDriver = $emailAddresses->pluck('driver')->unique();
                $allIncomingDriver = $emailAddresses->pluck('incoming_driver')->unique();
                $allPort = $emailAddresses->pluck('port')->unique();
                $allEncryption = $emailAddresses->pluck('encryption')->unique();
                $userEmails = $emailAddresses->pluck('username')->unique();
                $fromAddresses = $emailAddresses->pluck('from_address')->unique();
                $emailAddress = $emailAddresses->with(['website', 'email_run_history', 'email_assignes', 'history_last_message', 'history_last_message_error'])->paginate(Setting::get('pagination', 10))->appends(request()->query());

                // default values for add form
                $defaultDriver = 'smtp';
                $defaultPort = '587';
                $defaultEncryption = 'tls';
                $defaultHost = 'mail.mio-moda.com';

                return view('email-addresses.index', [
                    'emailAddress' => $emailAddress,
                    'allStores' => $allStores,
                    'allDriver' => $allDriver,
                    'allIncomingDriver' => $allIncomingDriver,
                    'allPort' => $allPort,
                    'allEncryption' => $allEncryption,
                    'users' => $users,
                    'uops' => $ops,
                    'userEmails' => $userEmails,
                    'defaultDriver' => $defaultDriver,
                    'defaultPort' => $defaultPort,
                    'defaultEncryption' => $defaultEncryption,
                    'defaultHost' => $defaultHost,
                    'fromAddresses' => $fromAddresses,
                    'runHistoriesCount' => $runHistoriesCount,
                ]);
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    'code' => $e->getCode(),
                    'data' => $e->getMessage(),
                    'message' => 'Something went wrong...! Please check database email_run_history data',
                ]
            );
        }

    }

    public function createAcknowledgement(CreateAcknowledgementEmailAddressRequest $request): JsonResponse
    {

        $input = $request->all();
        $input['added_by'] = Auth::user()->id;

        EMailAcknowledgement::create($input);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Your email acknowledgement has been created!',
            ]
        );
    }

    public function acknowledgementCount($email_addresses_id): JsonResponse
    {
        $EMailAcknowledgement = EMailAcknowledgement::where('email_addresses_id', $email_addresses_id)->orderByDesc('id')->take(5)->get();

        return response()->json(['code' => 200, 'EMailAcknowledgement' => $EMailAcknowledgement]);
    }

    public function runHistoriesTruncate(): RedirectResponse
    {
        EmailRunHistories::truncate();

        return redirect()->back()->withSuccess('Data Removed Successfully!');
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
    public function store(StoreEmailAddressRequest $request): RedirectResponse
    {

        $data = $request->except('_token', 'signature_logo', 'signature_image');

        $id = EmailAddress::insertGetId($data);

        $signature_logo = $request->file('signature_logo');
        $signature_image = $request->file('signature_image');

        if ($signature_logo != '') {
            $signature_logo->storeAs(config('constants.default_uploads_dir'), $signature_logo->getClientOriginalName(), 's3');
            EmailAddress::find($id)->update(['signature_logo' => $signature_logo->getClientOriginalName()]);
        }
        if ($signature_image != '') {
            $signature_image->storeAs(config('constants.default_uploads_dir'), $signature_image->getClientOriginalName(), 's3');
            EmailAddress::find($id)->update(['signature_image' => $signature_image->getClientOriginalName()]);
        }

        $this->createEmail($id, $data['host'], $data['username'], $data['password']);

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully saved a Email Address!');
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
     */
    public function update(UpdateEmailAddressRequest $request, int $id): RedirectResponse
    {

        $data = $request->except('_token', 'signature_logo', 'signature_image');

        EmailAddress::find($id)->update($data);

        $signature_logo = $request->file('signature_logo');
        $signature_image = $request->file('signature_image');

        if ($signature_logo != '') {
            $signature_logo->storeAs(config('constants.default_uploads_dir'), $signature_logo->getClientOriginalName(), 's3');
            EmailAddress::find($id)->update(['signature_logo' => $signature_logo->getClientOriginalName()]);
        }
        if ($signature_image != '') {
            $signature_image->storeAs(config('constants.default_uploads_dir'), $signature_image->getClientOriginalName(), 's3');
            EmailAddress::find($id)->update(['signature_image' => $signature_image->getClientOriginalName()]);
        }

        $this->updateEmailPassword($id, $data['host'], $data['username'], $data['password']);

        return redirect()->back()->withSuccess('You have successfully updated a Email Address!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $emailAddress = EmailAddress::find($id);

        $emailAddress->delete();

        return redirect()->route('email-addresses.index')->withSuccess('You have successfully deleted a Email Address');
    }

    public function getEmailAddressHistory(Request $request): JsonResponse
    {
        $EmailHistory = EmailRunHistories::where('email_run_histories.email_address_id', $request->id)
            ->whereDate('email_run_histories.created_at', Carbon::today())
            ->join('email_addresses', 'email_addresses.id', 'email_run_histories.email_address_id')
            ->select(['email_run_histories.*', 'email_addresses.from_name'])
            ->latest()
            ->get();

        $history = '';
        if (count($EmailHistory) > 0) {
            foreach ($EmailHistory as $runHistory) {
                $status = ($runHistory->is_success == 0) ? 'Failed' : 'Success';
                $message = empty($runHistory->message) ? '-' : $runHistory->message;
                $history .= '<tr>
                <td>'.$runHistory->id.'</td>
                <td>'.$runHistory->from_name.'</td>
                <td>'.$status.'</td>
                <td>'.$message.'</td>
                <td>'.$runHistory->created_at->format('Y-m-d H:i:s').'</td>
                </tr>';
            }
        } else {
            $history .= '<tr>
                    <td colspan="5">
                        No Result Found
                    </td>
                </tr>';
        }

        return response()->json(['data' => $history]);
    }

    public function getRelatedAccount(Request $request): View
    {
        $adsAccounts = GoogleAdsAccount::where('account_name', $request->id)->get();
        $translations = googleTraslationSettings::where('email', $request->id)->get();
        $analytics = StoreWebsiteAnalytic::where('email', $request->id)->get();

        $accounts = [];

        if (! $adsAccounts->isEmpty()) {
            foreach ($adsAccounts as $adsAccount) {
                $accounts[] = [
                    'name' => $adsAccount->account_name,
                    'email' => $adsAccount->account_name,
                    'last_error' => $adsAccount->last_error,
                    'last_error_at' => $adsAccount->last_error_at,
                    'credential' => $adsAccount->config_file_path,
                    'store_website' => $adsAccount->store_websites,
                    'status' => $adsAccount->status,
                    'type' => 'Google Ads Account',
                ];
            }
        }

        if (! $translations->isEmpty()) {
            foreach ($translations as $translation) {
                $accounts[] = [
                    'name' => $translation->email,
                    'email' => $translation->email,
                    'last_error' => $translation->last_note,
                    'last_error_at' => $translation->last_error_at,
                    'credential' => $translation->account_json,
                    'store_website' => 'N/A',
                    'status' => $translation->status,
                    'type' => 'Google Translation',
                ];
            }
        }

        if (! $analytics->isEmpty()) {
            foreach ($analytics as $analytic) {
                $accounts[] = [
                    'name' => $analytic->email,
                    'email' => $analytic->email,
                    'last_error' => $analytic->last_error,
                    'last_error_at' => $analytic->last_error_at,
                    'credential' => $analytic->account_id.' - '.$analytic->view_id,
                    'store_website' => $analytic->website,
                    'status' => 'N/A',
                    'type' => 'Google Analytics',
                ];
            }
        }

        return view('email-addresses.partials.task', compact('accounts'));
    }

    public function getErrorEmailHistory(Request $request): JsonResponse
    {
        ini_set('memory_limit', -1);

        $histories = EmailAddress::whereHas('history_last_message', function ($query) {
            $query->where('is_success', 0);
        })
            ->with(['history_last_message' => function ($q) {
                $q->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-10 day')));
            }])
            ->get();

        $history = '';

        if ($histories) {
            foreach ($histories as $row) {
                if ($row->history_last_message) {
                    $status = ($row->history_last_message->is_success == 0) ? 'Failed' : 'Success';
                    $message = $row->history_last_message->message ?? '-';
                    $history .= '<tr>
                    <td>'.$row->history_last_message->id.'</td>
                    <td>'.$row->from_name.'</td>
                    <td>'.$status.'</td>
                    <td>'.$message.'</td>
                    <td>'.$row->history_last_message->created_at->format('Y-m-d H:i:s').'</td>
                    </tr>';
                }
            }
        } else {
            $history .= '<tr>
                    <td colspan="5">
                        No Result Found
                    </td>
                </tr>';
        }

        return response()->json(['data' => $history]);
    }

    public function downloadFailedHistory(Request $request)
    {
        $histories = EmailAddress::whereHas('history_last_message', function ($query) {
            $query->where('is_success', 0);
        })
            ->with(['history_last_message' => function ($q) {
                $q->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-1 day')));
            }])
            ->get();

        $recordsArr = [];
        foreach ($histories as $row) {
            if ($row->history_last_message) {
                $recordsArr[] = [
                    'id' => $row->history_last_message->id,
                    'from_name' => $row->from_name,
                    'status' => ($row->history_last_message->is_success == 0) ? 'Failed' : 'Success',
                    'message' => $row->history_last_message->message ?? '-',
                    'created_at' => $row->history_last_message->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }
        $filename = 'Report-Email-failed'.'.csv';

        return Excel::download(new EmailFailedReport($recordsArr), $filename);
    }

    public function passwordChange(Request $request): JsonResponse
    {
        if (empty($request->users)) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Please select user',
            ]);
        }

        $users = explode(',', $request->users);
        $data = [];
        foreach ($users as $key) {
            // Generate new password
            $newPassword = Str::random(12);

            $user = EmailAddress::findorfail($key);
            $user->password = $newPassword;
            $user->save();
            $data[$key] = $newPassword;

            //update password in virtualmin
            $this->updateEmailPassword($user->id, $user->host, $user->username, $newPassword);
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'Password Updated',
        ]);
    }

    public function sendToWhatsApp(Request $request): RedirectResponse
    {
        $emailDetail = EmailAddress::find($request->id);
        $user_id = $request->user_id;
        $user = User::findorfail($user_id);
        $number = $user->phone;

        $message = 'Password For '.$emailDetail->username.'is: '.$emailDetail->password;

        $whatsappmessage = new WhatsAppController;
        $whatsappmessage->sendWithThirdApi($number, $user->whatsapp_number, $message);
        Session::flash('success', 'Password sent');

        return redirect()->back();
    }

    public function assignUsers(Request $request): RedirectResponse
    {
        $data = [];
        EmailAssign::where(['email_address_id' => $request->email_id])->delete();
        if (isset($request->users)) {
            foreach ($request->users as $_user) {
                $data[] = ['user_id' => $_user, 'email_address_id' => $request->email_id, 'created_at' => Carbon::today(), 'updated_at' => Carbon::today()];
            }
        }

        if (count($data) > 0) {
            EmailAssign::insert($data);

            return redirect()->back()->withSuccess('You have successfully assigned users to email address!');
        }

        return redirect()->back();
    }

    public function searchEmailAddress(Request $request): JsonResponse
    {
        $search = $request->search;

        if ($search != null) {
            $emailAddress = EmailAddress::where('username', 'Like', '%'.$search.'%')->orWhere('password', 'Like', '%'.$search.'%')->get();
        } else {
            $emailAddress = EmailAddress::get();
        }

        return response()->json(['tbody' => view('email-addresses.partials.email-address', compact('emailAddress'))->render()], 200);
    }

    public function updateEmailAddress(Request $request): JsonResponse
    {
        $usernames = $request->username;

        if ($request->username && $request->password) {
            foreach ($usernames as $key => $username) {
                EmailAddress::where('id', $key)->update(['username' => $username, 'password' => $request->password[$key]]);
            }

            return response()->json([
                'status' => 'success',
                'msg' => 'Email And Password Updated Successfully.',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'msg' => 'Email And Password Updated Successfully.',
            ]);
        }
    }

    //create email in virtualmin
    public function createEmail($id, $smtpHost, $user, $password): string
    {
        $mailHelper = new VirtualminHelper;
        $result = parse_url(getenv('VIRTUALMIN_ENDPOINT'));
        $vmHost = isset($result['host']) ? $result['host'] : '';
        $status = 'failure';
        if ($smtpHost == $vmHost) {
            $response = $mailHelper->createMail($smtpHost, $user, $password);
            $status = 'failure';
            if ($response['code'] == 200) {
                $status = $response['data']['status'];
                EmailAddress::find($id)->update(['username' => $user.'@'.$smtpHost]);
            }
        }

        return $status;
    }

    //update password in virtualmin
    public function updateEmailPassword($id, $smtpHost, $user, $password): string
    {
        $mailHelper = new VirtualminHelper;
        $result = parse_url(getenv('VIRTUALMIN_ENDPOINT'));
        $vmHost = isset($result['host']) ? $result['host'] : '';
        $status = 'failure';
        if ($smtpHost == $vmHost) {
            $response = $mailHelper->changeMailPassword($smtpHost, $user, $password);
            $status = 'failure';
            if ($response['code'] == 200) {
                $status = $response['data']['status'];
                $parts = explode('@', $user);
                EmailAddress::find($id)->update(['username' => $parts[0].'@'.$smtpHost]);
            }
        }

        return $status;
    }

    public function singleEmailRunCron(Request $request): JsonResponse
    {
        $emailAddresses = EmailAddress::where('id', $request->get('id'))->first();

        $emailAddress = $emailAddresses;
        try {
            $cm = new ClientManager;
            $imap = $cm->make([
                'host' => $emailAddress->host,
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => false,
                'username' => $emailAddress->username,
                'password' => $emailAddress->password,
                'protocol' => 'imap',
            ]);

            $imap->connect();

            $types = [
                'inbox' => [
                    'inbox_name' => 'INBOX',
                    'direction' => 'from',
                    'type' => 'incoming',
                ],
                'sent' => [
                    'inbox_name' => 'INBOX.Sent',
                    'direction' => 'to',
                    'type' => 'outgoing',
                ],
            ];

            $available_models = [
                'supplier' => Supplier::class, 'vendor' => Vendor::class,
                'customer' => Customer::class, 'users' => User::class,
            ];
            $email_list = [];
            foreach ($available_models as $value) {
                $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique();
            }

            foreach ($types as $type) {
                $inbox = $imap->getFolder($type['inbox_name']);
                if ($type['type'] == 'incoming') {
                    $latest_email = Email::select('created_at')->where('to', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                } else {
                    $latest_email = Email::select('created_at')->where('from', $emailAddress->from_address)->where('type', $type['type'])->latest()->first();
                }

                $latest_email_date = $latest_email ? Carbon::parse($latest_email->created_at) : false;
                if ($latest_email_date) {
                    $emails = ($inbox) ? $inbox->messages()->where('SINCE', $latest_email_date->subDays(1)->format('d-M-Y')) : '';
                } else {
                    $emails = ($inbox) ? $inbox->messages() : '';
                }
                if ($emails) {
                    $emails = $emails->all()->get();
                    foreach ($emails as $email) {
                        try {
                            $reference_id = $email->references;
                            $origin_id = $email->message_id;

                            // Skip if message is already stored
                            if (Email::where('origin_id', $origin_id)->count() > 0) {
                                continue;
                            }

                            // check if email has already been received

                            $textContent = $email->getTextBody();
                            if ($email->hasHTMLBody()) {
                                $content = $email->getHTMLBody();
                            } else {
                                $content = $email->getTextBody();
                            }

                            $email_subject = $email->getSubject();
                            Log::channel('customer')->info('Subject  => '.$email_subject);

                            $attachments_array = [];
                            $attachments = $email->getAttachments();
                            $fromThis = $email->getFrom()[0]->mail;
                            $attachments->each(function ($attachment) use (&$attachments_array, $fromThis, $email_subject) {
                                $attachment->name = preg_replace("/[^a-z0-9\_\-\.]/i", '', $attachment->name);
                                file_put_contents(storage_path('app/files/email-attachments/'.$attachment->name), $attachment->content);
                                $path = 'email-attachments/'.$attachment->name;

                                $attachments_array[] = $path;

                                /*start 3215 attachment fetch from DHL mail */
                                Log::channel('customer')->info('Match Start  => '.$email_subject);

                                $findFromEmail = explode('@', $fromThis);
                                if (strpos(strtolower($email_subject), 'your copy invoice') !== false && isset($findFromEmail[1]) && (strtolower($findFromEmail[1]) == 'dhl.com')) {
                                    Log::channel('customer')->info('Match Found  => '.$email_subject);
                                    $this->getEmailAttachedFileData($attachment->name);
                                }
                                /*end 3215 attachment fetch from DHL mail */
                            });

                            $from = $email->getFrom()[0]->mail;
                            $to = array_key_exists(0, $email->getTo()->get()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

                            // Model is sender if its incoming else its receiver if outgoing
                            if ($type['type'] == 'incoming') {
                                $model_email = $from;
                            } else {
                                $model_email = $to;
                            }

                            // Get model id and model type

                            extract($this->getModel($model_email, $email_list));

                            $subject = explode('#', $email_subject);
                            if (isset($subject[1]) && ! empty($subject[1])) {
                                $findTicket = Tickets::where('ticket_id', $subject[1])->first();
                                if ($findTicket) {
                                    $model_id = $findTicket->id;
                                    $model_type = Tickets::class;
                                }
                            }

                            $params = [
                                'model_id' => $model_id,
                                'model_type' => $model_type,
                                'origin_id' => $origin_id,
                                'reference_id' => $reference_id,
                                'type' => $type['type'],
                                'seen' => isset($email->getFlags()['seen']) ? $email->getFlags()['seen'] : 0,
                                'from' => $email->getFrom()[0]->mail,
                                'to' => array_key_exists(0, $email->getTo()->get()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject' => $email->getSubject(),
                                'message' => $content,
                                'template' => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at' => $email->getDate(),
                            ];

                            $email_id = Email::insertGetId($params);

                            if ($type['type'] == 'incoming') {
                                $message = trim($textContent);

                                $reply = (new EmailParser)->parse($message);

                                $fragment = current($reply->getFragments());

                                $pattern = '(On[^abc,]*, (Jan(uary)?|Feb(ruary)?|Mar(ch)?|Apr(il)?|May|Jun(e)?|Jul(y)?|Aug(ust)?|Sep(tember)?|Oct(ober)?|Nov(ember)?|Dec(ember)?)\s+\d{1,2},\s+\d{4}, (1[0-2]|0?[1-9]):([0-5][0-9]) ([AaPp][Mm]))';

                                $reply = strip_tags($fragment);

                                $reply = preg_replace($pattern, ' ', $reply);

                                $mailFound = false;
                                if ($reply) {
                                    $customer = Customer::where('email', $from)->first();
                                    if (! empty($customer)) {
                                        // store the main message
                                        $params = [
                                            'number' => $customer->phone,
                                            'message' => $reply,
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
                                            'is_email' => 1,
                                            'from_email' => $from,
                                            'to_email' => $to,
                                            'email_id' => $email_id,
                                        ];
                                        $messageModel = \App\ChatMessage::create($params);
                                        MessageHelper::whatsAppSend($customer, $reply, null, null);
                                        MessageHelper::sendwatson($customer, $reply, null, $messageModel, $params);
                                        $mailFound = true;
                                    }

                                    if (! $mailFound) {
                                        $vandor = Vendor::where('email', $from)->first();
                                        if ($vandor) {
                                            $params = [
                                                'number' => $vandor->phone,
                                                'message' => $reply,
                                                'media_url' => null,
                                                'approved' => 0,
                                                'status' => 0,
                                                'contact_id' => null,
                                                'erp_user' => null,
                                                'supplier_id' => null,
                                                'task_id' => null,
                                                'dubizzle_id' => null,
                                                'vendor_id' => $vandor->id,
                                                'is_email' => 1,
                                                'from_email' => $from,
                                                'to_email' => $to,
                                                'email_id' => $email_id,
                                            ];
                                            $messageModel = ChatMessage::create($params);
                                            $mailFound = true;
                                        }
                                    }

                                    if (! $mailFound) {
                                        $supplier = Supplier::where('email', $from)->first();
                                        if ($supplier) {
                                            $params = [
                                                'number' => $supplier->phone,
                                                'message' => $reply,
                                                'media_url' => null,
                                                'approved' => 0,
                                                'status' => 0,
                                                'contact_id' => null,
                                                'erp_user' => null,
                                                'supplier_id' => $supplier->id,
                                                'task_id' => null,
                                                'dubizzle_id' => null,
                                                'is_email' => 1,
                                                'from_email' => $from,
                                                'to_email' => $to,
                                                'email_id' => $email_id,
                                            ];
                                            $messageModel = ChatMessage::create($params);
                                            $mailFound = true;
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            captureException($e);
                            Log::error('error while fetching some emails for '.$emailAddress->username.' Error Message: '.$e->getMessage());
                            $historyParam = [
                                'email_address_id' => $emailAddress->id,
                                'is_success' => 0,
                                'message' => 'error while fetching some emails for '.$emailAddress->username.' Error Message: '.$e->getMessage(),
                            ];
                            EmailRunHistories::create($historyParam);
                        }
                    }
                }
            }

            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success' => 1,
            ];

            EmailRunHistories::create($historyParam);

            return response()->json(['status' => 'success', 'message' => 'Successfully'], 200);
        } catch (Exception $e) {
            captureException($e);
            $exceptionMessage = $e->getMessage();

            if ($e->getPrevious() !== null) {
                $previousMessage = $e->getPrevious()->getMessage();
                $exceptionMessage = $previousMessage.' | '.$exceptionMessage;
            }

            Log::channel('customer')->info($exceptionMessage);
            $historyParam = [
                'email_address_id' => $emailAddress->id,
                'is_success' => 0,
                'message' => $exceptionMessage,
            ];
            EmailRunHistories::create($historyParam);
            CronJob::insertLastError('fetch:all_emails', $exceptionMessage);
            throw new Exception($exceptionMessage);
        }
    }

    public function listEmailRunLogs(Request $request)
    {
        $searchMessage = $request->search_message;
        $searchDate = $request->date;
        $searchName = $request->search_name;
        $searchStatus = $request->status ?? '';

        $emailRunHistoryQuery = EmailRunHistories::join('email_addresses', 'email_run_histories.email_address_id', '=', 'email_addresses.id')
            ->select(
                'email_run_histories.*',
                'email_addresses.from_name as email_from_name'
            )
            ->when($searchMessage, function ($query, $searchMessage) {
                return $query->where('email_run_histories.message', 'LIKE', '%'.$searchMessage.'%');
            })
            ->when($searchDate, function ($query, $searchDate) {
                return $query->where('email_run_histories.created_at', 'LIKE', '%'.$searchDate.'%');
            })
            ->when($searchName, function ($query, $searchName) {
                return $query->where('email_addresses.from_name', 'LIKE', '%'.$searchName.'%');
            })
            ->latest();

        if ($searchStatus != '') {
            if ($searchStatus === 'success') {
                $emailRunHistoryQuery->where('email_run_histories.is_success', 1);
            }

            if ($searchStatus === 'failed') {
                $emailRunHistoryQuery->where('email_run_histories.is_success', 0);
            }
        }

        $emailJobs = $emailRunHistoryQuery->paginate(Setting::get('pagination', 25));

        return view('email-addresses.email-run-log-listing', compact('emailJobs'));
    }

    public function setEmailAlert(Request $request)
    {
        $emailAddressId = $request->id;
        $emaiAddress = EmailAddress::findorfail($emailAddressId);
        $emaiAddress->email_alert = $request->email_alert == 'true' ? 1 : 0;
        $emaiAddress->save();

        return ['status' => true, 'message' => 'Email alert Updated'];
    }
}
