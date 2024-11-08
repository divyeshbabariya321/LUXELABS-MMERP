<?php

namespace App\Http\Controllers;

use App\DigitalMarketingPlatform;
use App\Email;
use App\EmailAddress;
use App\EmailAssign;
use App\EmailCategory;
use App\EmailLog;
use App\EmailRemark;
use App\Jobs\SendEmail;
use App\LogRequest;
use App\Mails\Manual\ForwardEmail;
use App\Mails\Manual\PurchaseEmail;
use App\Models\EmailStatus;
use App\SendgridEvent;
use App\Waybill;
use App\Waybillinvoice;
use App\Wetransfer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\PHPIMAP\ClientManager;

class EmailDataExtractionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  null|mixed  $email
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $email = null)
    {
        $user = Auth::user();
        $admin = $user->isAdmin();
        $usernames = [];
        if (! $admin) {
            $emaildetails = EmailAssign::select('id', 'email_address_id')->with('emailAddress')->where(['user_id' => $user->id])->get();
            if ($emaildetails) {
                foreach ($emaildetails as $_email) {
                    $usernames[] = $_email->emailAddress->username;
                }
            }
        }

        $type = 'incoming';
        $seen = '0';
        $from = '';

        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $mailbox = $request->mail_box ?? '';

        $date = $request->date ?? '';
        $type = $request->type ?? $type;
        $seen = $request->seen ?? $seen;
        $email_type = $request->email_type ?? '';
        $query = (new Email)->newQuery();
        if ($email_type) {
            $query = $query->where('template', $email_type);
        } else {
            $query = $query->whereIn('template', ['coupons', 'referr-coupon']);
        }
        $trash_query = false;

        if (count($usernames) > 0) {
            $query = $query->where(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $query = $query->orWhere(function ($query) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $query->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }

        if ($email != '' && $receiver == '') {
            $receiver = $email;
            $from = 'order_data';
            $seen = 'both';
            $type = 'outgoing';
        }

        if ($type == 'bin') {
            $trash_query = true;
            $query = $query->where('status', 'bin');
        } elseif ($type == 'draft') {
            $query = $query->where('is_draft', 1);
        } elseif ($type == 'pre-send') {
            $query = $query->where('status', 'pre-send');
        } else {
            $query = $query->where(function ($query) use ($type) {
                $query->where('type', $type)->orWhere('type', 'outgoing')->orWhere('type', 'open')->orWhere('type', 'delivered')->orWhere('type', 'processed');
            });
        }

        if ($date) {
            $query = $query->whereDate('created_at', $date);
        }
        if ($term) {
            $query = $query->where(function ($query) use ($term) {
                $query->where('from', 'like', '%'.$term.'%')
                    ->orWhere('to', 'like', '%'.$term.'%')
                    ->orWhere('subject', 'like', '%'.$term.'%')
                    ->orWhere('message', 'like', '%'.$term.'%');
            });
        }

        if (! $term) {
            if ($sender) {
                $query = $query->where(function ($query) use ($sender) {
                    $query->orWhere('from', 'like', '%'.$sender.'%');
                });
            }
            if ($receiver) {
                $query = $query->where(function ($query) use ($receiver) {
                    $query->orWhere('to', 'like', '%'.$receiver.'%');
                });
            }
            if ($status) {
                $query = $query->where(function ($query) use ($status) {
                    $query->orWhere('status', $status);
                });
            }
            if ($category) {
                $query = $query->where(function ($query) use ($category) {
                    $query->orWhere('email_category_id', $category);
                });
            }
        }

        if (! empty($mailbox)) {
            $query = $query->where(function ($query) use ($mailbox) {
                $query->orWhere('to', 'like', '%'.$mailbox.'%');
            });
        }

        if (isset($seen)) {
            if ($seen != 'both') {
                $query = $query->where('seen', $seen);
            }
        }

        // If it isn't trash query remove email with status trashed
        if (! $trash_query) {
            $query = $query->where(function ($query) {
                return $query->where('status', '<>', 'bin')->orWhereNull('status');
            });
        }

        if ($admin == 1) {
            $query = $query->orderByDesc('created_at');
            $emails = $query->paginate(30)->appends(request()->except(['page']));
        } else {
            if (count($usernames) > 0) {
                $query = $query->where(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('from', 'like', '%'.$_uname.'%');
                    }
                });

                $query = $query->orWhere(function ($query) use ($usernames) {
                    foreach ($usernames as $_uname) {
                        $query->orWhere('to', 'like', '%'.$_uname.'%');
                    }
                });

                $query = $query->orderByDesc('created_at');
                $emails = $query->paginate(30)->appends(request()->except(['page']));
            } else {
                $emails = (new Email)->newQuery();
                if ($email_type) {
                    $emails = $emails->where('template', $email_type);
                } else {
                    $emails = $emails->whereIn('template', ['coupons', 'referr-coupon']);
                }
                $emails = $emails->whereNull('id');
                $emails = $emails->paginate(30)->appends(request()->except(['page']));
            }
        }

        //Get All Category
        $email_status = EmailStatus::get();

        //Get All Status
        $email_categories = EmailCategory::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails', 'date', 'term', 'type', 'email_categories', 'email_status'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $emails->links(),
                'count' => $emails->total(),
                'emails' => $emails,
            ], 200);
        }

        // suggested search for email forwarding
        $search_suggestions = $this->getAllEmails();

        $digita_platfirms = DigitalMarketingPlatform::all();
        $sender_drpdwn = Email::select('from');

        if (count($usernames) > 0) {
            $sender_drpdwn = $sender_drpdwn->where(function ($sender_drpdwn) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $sender_drpdwn->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $sender_drpdwn = $sender_drpdwn->orWhere(function ($sender_drpdwn) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $sender_drpdwn->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }

        $sender_drpdwn = $sender_drpdwn->distinct()->get()->toArray();

        $receiver_drpdwn = Email::select('to');

        if (count($usernames) > 0) {
            $receiver_drpdwn = $receiver_drpdwn->where(function ($receiver_drpdwn) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiver_drpdwn->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $receiver_drpdwn = $receiver_drpdwn->orWhere(function ($receiver_drpdwn) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiver_drpdwn->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }

        $receiver_drpdwn = $receiver_drpdwn->distinct()->get()->toArray();

        $mailboxdropdown = EmailAddress::pluck('from_address', 'from_name', 'username');

        $mailboxdropdown = $mailboxdropdown->toArray();

        return view('email-data-extraction.index', ['emails' => $emails, 'type' => 'email', 'search_suggestions' => $search_suggestions, 'email_categories' => $email_categories, 'email_status' => $email_status, 'sender_drpdwn' => $sender_drpdwn, 'digita_platfirms' => $digita_platfirms, 'receiver_drpdwn' => $receiver_drpdwn, 'receiver' => $receiver, 'from' => $from, 'mailboxdropdown' => $mailboxdropdown])->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function platformUpdate(Request $request): RedirectResponse
    {
        if ($request->id) {
            if (Email::where('id', $request->id)->update(['digital_platfirm' => $request->platform])) {
                return redirect()->back()->with('success', 'Updated successfully.');
            }

            return redirect()->back()->with('error', 'Records not found!');
        }

        return redirect()->back()->with('error', 'Error Occured! Please try again later.');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $email = Email::find($id);
        $status = 'bin';
        $message = 'Email has been trashed';

        // If status is already trashed, move to inbox
        if ($email->status == 'bin') {
            $status = '';
            $message = 'Email has been sent to inbox';
        }

        $email->status = $status;
        $email->update();

        return response()->json(['message' => $message]);
    }

    public function resendMail($id, Request $request): JsonResponse
    {
        $email = Email::find($id);
        $attachment = [];
        $cm = new ClientManager;
        $imap = $cm->make([
            'host' => config('settings.imap_host_purchase'),
            'port' => config('settings.imap_port_purchase'),
            'encryption' => config('settings.imap_encryption_purchase'),
            'validate_cert' => config('settings.imap_validate_cert_purchase'),
            'username' => config('settings.imap_username_purchase'),
            'password' => config('settings.imap_password_purchase'),
            'protocol' => config('settings.imap_protocol_purchase'),
        ]);

        $imap->connect();

        $array = is_array(json_decode($email->additional_data, true)) ? json_decode($email->additional_data, true) : [];

        if (array_key_exists('attachment', $array)) {
            $temp = json_decode($email->additional_data, true)['attachment'];
        }
        if (isset($temp)) {
            if (! is_array($temp)) {
                $attachment[] = $temp;
            } else {
                $attachment = $temp;
            }
        }

        Email::create([
            'model_id' => $email->id,
            'model_type' => Email::class,
            'type' => $email->type,
            'from' => $email->from,
            'to' => $email->to,
            'subject' => $email->subject,
            'message' => $email->message,
            'template' => 'resend-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        Mail::to($email->to)->send(new PurchaseEmail($email->subject, $email->message, $attachment));
        $type = $email->type;
        if ($type == 'approve') {
            $email->update(['approve_mail' => 0]);
        }

        return response()->json(['message' => 'Mail resent successfully']);
    }

    /**
     * Provide view for email reply modal
     *
     * @param [type] $id
     */
    public function replyMail($id): View
    {
        $email = Email::find($id);

        return view('emails.reply-modal', compact('email'));
    }

    /**
     * Provide view for email forward modal
     *
     * @param [type] $id
     */
    public function forwardMail($id): View
    {
        $email = Email::find($id);

        return view('emails.forward-modal', compact('email'));
    }

    /**
     * Handle the email reply
     */
    public function submitReply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->reply_email_id);
        $replyPrefix = 'Re: ';
        $subject = substr($email->subject, 0, 4) === $replyPrefix
            ? $email->subject
            : $replyPrefix.$email->subject;
        $dateCreated = $email->created_at->format('D, d M Y');
        $timeCreated = $email->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, <{$email->from}> wrote:";
        $message_to_store = $originalEmailInfo.'<br/>'.$request->message.'<br/>'.$email->message;
        $emailsLog = Email::create([
            'model_id' => $email->id,
            'model_type' => Email::class,
            'from' => $email->from,
            'to' => $email->to,
            'subject' => $subject,
            'message' => $message_to_store,
            'template' => 'reply-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);
        SendEmail::dispatch($emailsLog)->onQueue('send_email');

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    /**
     * Handle the email forward
     */
    public function submitForward(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->forward_email_id);

        $emailClass = (new ForwardEmail($email, $email->message))->build();

        $email = Email::create([
            'model_id' => $email->id,
            'model_type' => Email::class,
            'from' => @$emailClass->from[0]['address'],
            'to' => $request->email,
            'subject' => $emailClass->subject,
            'message' => $emailClass->render(),
            'template' => 'forward-email',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
            'is_draft' => 1,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return response()->json(['success' => true, 'message' => 'Email has been successfully sent.']);
    }

    public function getRemark(Request $request): JsonResponse
    {
        $email_id = $request->input('email_id');

        $remark = EmailRemark::where('email_id', $email_id)->get();

        return response()->json($remark, 200);
    }

    public function addRemark(Request $request): JsonResponse
    {
        $remark = $request->input('remark');
        $email_id = $request->input('id');

        if (! empty($remark)) {
            EmailRemark::create([
                'email_id' => $email_id,
                'remarks' => $remark,
                'user_name' => Auth::user()->name,
            ]);
        }

        return response()->json(['remark' => $remark], 200);
    }

    public function markAsRead($id): JsonResponse
    {
        $email = Email::find($id);
        $email->seen = 1;
        $email->update();

        return response()->json(['success' => true, 'message' => 'Email has been read.']);
    }

    public function getAllEmails()
    {
        $email_list = [];

        return array_values(array_unique($email_list));
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $email_id = $request->input('email_id');
        $category = $request->input('category');
        $status = $request->input('status');

        $email = Email::find($email_id);
        $email->status = $status;
        $email->email_category_id = $category;

        $email->update();

        session()->flash('success', 'Data updated successfully');

        return redirect()->to('email');
    }

    public function getFileStatus(Request $request): JsonResponse
    {
        $id = $request->id;
        $email = Email::find($id);

        if (isset($email->email_excel_importer)) {
            $status = 'No any update';

            if ($email->email_excel_importer === 3) {
                $status = 'File move on wetransfer';
            } elseif ($email->email_excel_importer === 2) {
                $status = 'Executed but we transfer file not exist';
            } elseif ($email->email_excel_importer === 1) {
                $status = 'Transfer exist';
            }

            return response()->json([
                'status' => true,
                'mail_status' => $status,
                'message' => 'Data found',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found',
        ], 200);
    }

    public function excelImporter(Request $request): JsonResponse
    {
        $id = $request->id;

        $email = Email::find($id);

        $body = $email->message;

        //check for wetransfer link

        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $body, $match);

        if (isset($match[0])) {
            $matches = $match[0];
            foreach ($matches as $matchLink) {
                if (strpos($matchLink, 'wetransfer.com') !== false || strpos($matchLink, 'we.tl') !== false) {
                    if (strpos($matchLink, 'google.com') === false) {
                        //check if wetransfer already exist
                        $checkIfExist = Wetransfer::where('url', $matchLink)->where('supplier', $request->supplier)->first();
                        if (! $checkIfExist) {
                            $wetransfer = new Wetransfer;
                            $wetransfer->type = 'excel';
                            $wetransfer->url = $matchLink;
                            $wetransfer->is_processed = 1;
                            $wetransfer->supplier = $request->supplier;
                            $wetransfer->save();

                            Email::where('id', $id)->update(['email_excel_importer' => 3]);

                            try {
                                self::downloadFromURL($matchLink, $request->supplier);
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Something went wrong!'], 422);
                            }
                            //downloading wetransfer and generating data
                        }
                    }
                }
            }
        }

        //getting from attachments

        $attachments = $email->additional_data;
        if ($attachments) {
            $attachJson = json_decode($attachments);
            $attachs = $attachJson->attachment;

            //getting all attachments
            //check if extension is .xls or xlsx
            foreach ($attachs as $attach) {
                $attach = str_replace('email-attachments/', '', $attach);
                $extension = last(explode('.', $attach));
                if ($extension == 'xlsx' || $extension == 'xls') {
                    if (class_exists('seo2websitesErpExcelImporterErpExcelImporter')) {
                        $excel = $request->supplier;
                        ErpExcelImporter::excelFileProcess($attach, $excel, '');
                    }
                } elseif ($extension == 'zip') {
                    if (class_exists('seo2websitesErpExcelImporterErpExcelImporter')) {
                        $excel = $request->supplier;
                        $attachments_array = [];
                        $attachments = ErpExcelImporter::excelZipProcess('', $attach, $excel, '', $attachments_array);
                    }
                }
            }
        }

        return response()->json(['message' => 'Successfully Imported'], 200);
    }

    public static function downloadFromURL($url, $supplier)
    {
        $WETRANSFER_API_URL = 'https://wetransfer.com/api/v4/transfers/';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if (strpos($url, 'https://we.tl/') !== false) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:21.0) Gecko/20100101 Feirefox/21.0'); // Necessary. The server checks for a valid User-Agent.
            curl_exec($ch);

            $response = curl_exec($ch);
            preg_match_all('/^Location:(.*)$/mi', $response, $matches);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, \App\Console\Commands\EmailDataExtractionController::class, 'downloadFromURL');
            curl_close($ch);

            if (isset($matches[1])) {
                if (isset($matches[1][0])) {
                    $url = trim($matches[1][0]);
                }
            }
        }

        //replace https://wetransfer.com/downloads/ from url

        $url = str_replace('https://wetransfer.com/downloads/', '', $url);

        //making array from url

        $dataArray = explode('/', $url);

        if (count($dataArray) == 2) {
            $securityhash = $dataArray[1];
            $transferId = $dataArray[0];
        } elseif (count($dataArray) == 3) {
            $securityhash = $dataArray[2];
            $transferId = $dataArray[0];
        } else {
            exit('Something is wrong with url');
        }

        //making post request to get the url
        $data = [];
        $data['intent'] = 'entire_transfer';
        $data['security_hash'] = $securityhash;

        $curlURL = $WETRANSFER_API_URL.$transferId.'/download';

        $cookie = 'cookie.txt';
        $url = 'https://wetransfer.com/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/'.$cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/'.$cookie);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            exit(curl_error($ch));
        }

        $re = '/name="csrf-token" content="([^"]+)"/m';

        preg_match_all($re, $response, $matches, PREG_SET_ORDER, 0);

        if (count($matches) != 0) {
            if (isset($matches[0])) {
                if (isset($matches[0][1])) {
                    $token = $matches[0][1];
                }
            }
        }

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-CSRF-Token:'.$token;

        curl_setopt($ch, CURLOPT_URL, $curlURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $real = curl_exec($ch);

        $urlResponse = json_decode($real); // respons decode
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $curlURL, 'POST', json_encode([]), $urlResponse, $httpcode, EmailDataExtractionController::class, 'downloadFromURL');

        if (isset($urlResponse->direct_link)) {
            $downloadURL = $urlResponse->direct_link;

            $d = explode('?', $downloadURL);

            $fileArray = explode('/', $d[0]);

            $filename = end($fileArray);

            $file = file_get_contents($downloadURL);

            Storage::put($filename, $file);

            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

            $path = $storagePath.'/'.$filename;

            if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {

                if (strpos($filename, '.xls') !== false || strpos($filename, '.xlsx') !== false) {
                    if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                        ErpExcelImporter::excelFileProcess($path, $filename, '');
                    }
                }
            }
        }
    }

    public function bluckAction(Request $request): JsonResponse
    {
        $ids = $request->ids;
        $status = $request->status;
        $action_type = $request->action_type;

        if ($action_type == 'delete') {
            session()->flash('success', 'Email has been moved to trash successfully');
            Email::whereIn('id', $ids)->update(['status' => 'bin']);
        } else {
            session()->flash('success', 'Status has been updated successfully');
            Email::whereIn('id', $ids)->update(['status' => $status]);
        }

        return response()->json(['type' => 'success'], 200);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        Email::where('id', $request->email_id)->update(['status' => $request->status_id]);
        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    public function getModel($email, $email_list)
    {
        $model_id = null;
        $model_type = null;

        // Traverse all models
        foreach ($email_list as $key => $value) {
            // If email exists in the DB
            if (isset($value[$email])) {
                $model_id = $value[$email];
                $model_type = $key;
                break;
            }
        }

        return compact('model_id', 'model_type');
    }

    public function getEmailAttachedFileData($fileName = '')
    {
        $file = fopen(storage_path('app/files/email-attachments/'.$fileName), 'r');

        $skiprowupto = 1; //skip first line
        $rowincrement = 1;
        $attachedFileDataArray = [];
        while (($data = fgetcsv($file, 4000, ',')) !== false) {
            if ($rowincrement > $skiprowupto) {
                if (isset($data[0]) && ! empty($data[0])) {
                    try {
                        $due_date = date('Y-m-d', strtotime($data[9]));
                        $attachedFileDataArray = [
                            'line_type' => $data[0],
                            'billing_source' => $data[1],
                            'original_invoice_number' => $data[2],
                            'invoice_number' => $data[3],
                            'invoice_identifier' => $data[5],
                            'invoice_currency' => $data[69],
                            'invoice_amount' => $data[70],
                            'invoice_type' => $data[6],
                            'invoice_date' => $data[7],
                            'payment_terms' => $data[8],
                            'due_date' => $due_date,
                            'billing_account' => $data[11],
                            'billing_account_name' => $data[12],
                            'billing_account_name_additional' => $data[13],
                            'billing_address_1' => $data[14],
                            'billing_postcode' => $data[17],
                            'billing_city' => $data[18],
                            'billing_state_province' => $data[19],
                            'billing_country_code' => $data[20],
                            'billing_contact' => $data[21],
                            'shipment_number' => $data[23],
                            'shipment_date' => $data[24],
                            'product' => $data[30],
                            'product_name' => $data[31],
                            'pieces' => $data[32],
                            'origin' => $data[33],
                            'orig_name' => $data[34],
                            'orig_country_code' => $data[35],
                            'orig_country_name' => $data[36],
                            'senders_name' => $data[37],
                            'senders_city' => $data[42],
                            'created_at' => \Carbon\Carbon::now(),
                            'updated_at' => \Carbon\Carbon::now(),
                        ];
                        if (! empty($attachedFileDataArray)) {
                            $attachresponse = Waybillinvoice::create($attachedFileDataArray);

                            // check that way bill exist not then create
                            $wayBill = Waybill::where('awb', $attachresponse->shipment_number)->first();
                            if (! $wayBill) {
                                $wayBill = new Waybill;
                                $wayBill->awb = $attachresponse->shipment_number;

                                $wayBill->from_customer_name = $data[45];
                                $wayBill->from_city = $data[42];
                                $wayBill->from_country_code = $data[44];
                                $wayBill->from_customer_address_1 = $data[38];
                                $wayBill->from_customer_address_2 = $data[39];
                                $wayBill->from_customer_pincode = $data[41];
                                $wayBill->from_company_name = $data[39];

                                $wayBill->to_customer_name = $data[50];
                                $wayBill->to_city = $data[55];
                                $wayBill->to_country_code = $data[57];
                                $wayBill->to_customer_phone = '';
                                $wayBill->to_customer_address_1 = $data[51];
                                $wayBill->to_customer_address_2 = $data[52];
                                $wayBill->to_customer_pincode = $data[54];
                                $wayBill->to_company_name = '';

                                $wayBill->actual_weight = $data[68];
                                $wayBill->volume_weight = $data[66];

                                $wayBill->cost_of_shipment = $data[70];
                                $wayBill->package_slip = $attachresponse->shipment_number;
                                $wayBill->pickup_date = date('Y-m-d', strtotime($data[24]));
                                $wayBill->save();
                            }

                            $cash_flow = new CashFlow;
                            $cash_flow->fill([
                                'date' => $attachresponse->due_date ? $attachresponse->due_date : null,
                                'type' => 'pending',
                                'description' => 'Waybill invoice details',
                                'cash_flow_able_id' => $attachresponse->id,
                                'cash_flow_able_type' => Waybillinvoice::class,
                            ])->save();
                        }
                    } catch (Exception $e) {
                        Log::error('Error from the dhl invoice : '.$e->getMessage());
                    }
                }
            }
            $rowincrement++;
        }
        fclose($file);
    }

    public function getEmailEvents($originId)
    {
        $exist = Email::where('origin_id', $originId)->first(); //$originId = "9e238becd3bc31addeff3942fc54e340@swift.generated";
        $events = [];
        $eventData = '';
        if ($exist != null) {
            $events = SendgridEvent::where('payload', 'like', '%"smtp-id":"<'.$originId.'>"%')->select('timestamp', 'event')->orderByDesc('id')->get();
        }
        foreach ($events as $event) {
            $eventData .= '<tr><td>'.$event['timestamp'].'</td><td>'.$event['event'].'</td></tr>';
        }
        if ($eventData == '') {
            $eventData = '<tr><td>No data found.</td></tr>';
        }

        return $eventData;
    }

    /**
     * Get Email Logs
     *
     * @param  mixed  $emailid
     */
    public function getEmailLogs($emailid)
    {
        $emailLogs = EmailLog::where('email_id', $emailid)->orderByDesc('id')->get();

        $emailLogData = '';

        foreach ($emailLogs as $emailLog) {
            $emailLogData .= '<tr><td>'.$emailLog['created_at'].'</td><td>'.$emailLog['email_log'].'</td><td>'.$emailLog['message'].'</td></tr>';
        }
        if ($emailLogData == '') {
            $emailLogData = '<tr><td>No data found.</td></tr>';
        }

        return $emailLogData;
    }

    /**
     * Update Email Category using Ajax
     */
    public function changeEmailCategory(Request $request): JsonResponse
    {
        Email::where('id', $request->email_id)->update(['email_category_id' => $request->category_id]);
        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }
}
