<?php

namespace App\Http\Controllers;

use App\ChatbotQuestion;
use App\ChatMessage;
use App\Console\Commands\EmailController as EmailCommand;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\DigitalMarketingPlatform;
use App\Email;
use App\EmailAddress;
use App\EmailAssign;
use App\EmailCategory;
use App\EmailLog;
use App\EmailRemark;
use App\EmailRunHistories;
use App\Helpers\MessageHelper;
use App\Jobs\SendEmail;
use App\LogRequest;
use App\Mails\Manual\ForwardEmail;
use App\Mails\Manual\PurchaseEmail;
use App\ModelColor;
use App\Models\DataTableColumn;
use App\Models\EmailBox;
use App\Models\EmailCategoryHistory;
use App\Models\EmailStatus;
use App\Models\EmailStatusChangeHistory;
use App\Reply;
use App\ReplyCategory;
use App\SendgridEvent;
use App\SendgridEventColor;
use App\Setting;
use App\StoreWebsite;
use App\Supplier;
use App\Tickets;
use App\User;
use App\Vendor;
use App\Waybill;
use App\Waybillinvoice;
use App\Wetransfer;
use Carbon\Carbon;
use DataTables;
use EmailReplyParser\Parser\EmailParser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use Webklex\PHPIMAP\ClientManager;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  null|mixed  $email
     * @return \Illuminate\Http\Response
     */
    //Purpose : Add Email Parameter - DEVTASK-18283
    public function index(Request $request, $email = null)
    {
        // Set default type as incoming
        $from = ''; //Purpose : Add var -  DEVTASK-18283

        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $status = $request->status ?? '';
        $category = $request->category ?? '';
        $mailbox = $request->mail_box ?? '';
        $email_model_type = $request->email_model_type ?? '';

        $date = $request->date ?? '';
        $type = $request->type ?? 'incoming';
        $seen = $request->seen ?? '0';

        $emailModelTypes = Email::emailModelTypeList();
        $email_status = cache()->remember('email_statuses', (7 * 24 * 60), function () {
            return EmailStatus::select('id', 'email_status')->get();
        });

        $email_categories = cache()->remember('email_categories', (7 * 24 * 60), function () {
            return EmailCategory::select('id', 'category_name')->get();
        });

        if ($request->ajax()) {
            // $user      = Auth::user();
            $admin = Auth::user()->isAdmin();
            $usernames = [];
            if (! $admin) {
                $emaildetails = EmailAssign::select('id', 'email_address_id')
                    ->with('emailAddress:username')
                    ->where(['user_id' => Auth::user()->id])
                    ->getModels();
                if ($emaildetails) {
                    $usernames = array_map(fn ($item) => $item->emailAddress->username, $emaildetails);
                }
            }

            $query = (new Email)->newQuery();
            $trash_query = false;
            $query = $query->leftJoin('chat_messages', 'chat_messages.email_id', 'emails.id')
                ->leftJoin('customers as c', 'c.id', 'chat_messages.customer_id')
                ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
                ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id');
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

            //START - Purpose : Add Email - DEVTASK-18283
            if ($email != '' && $receiver == '') {
                $receiver = $email;
                $seen = 'both';
                $type = 'outgoing';
            }
            //END - DEVTASK-18283

            // If type is bin, check for status only
            if ($type == 'bin') {
                $trash_query = true;
                $query = $query->where('emails.status', 'bin');
            } elseif ($type == 'draft') {
                $query = $query->where('is_draft', 1)->where('emails.status', '<>', 'pre-send');
            } elseif ($type == 'pre-send') {
                $query = $query->where('emails.status', 'pre-send');
            } else {
                $query = $query->where(function ($query) use ($type) {
                    $query->where('emails.type', $type)->orWhere('emails.type', 'open')->orWhere('emails.type', 'delivered')->orWhere('emails.type', 'processed');
                });
            }
            if ($email_model_type) {
                $model_type = explode(',', $email_model_type);
                $query = $query->where(function ($query) use ($model_type) {
                    $query->whereIn('model_type', $model_type);
                });
            }
            if ($date) {
                $query = $query->whereDate('created_at', $date);
            }
            if ($term) {
                $query = $query->where(function ($query) use ($term) {
                    $query->orWhere('from', 'like', '%'.$term.'%')
                        ->orWhere('to', 'like', '%'.$term.'%')
                        ->orWhere('emails.subject', 'like', '%'.$term.'%')
                        ->orWhere(DB::raw('FROM_BASE64(emails.message)'), 'like', '%'.$term.'%')
                        ->orWhere('chat_messages.message', 'like', '%'.$term.'%');
                });
            }

            if (! $term) {
                if ($sender) {
                    $sender = explode(',', $request->sender);
                    $query = $query->where(function ($query) use ($sender) {
                        $query->whereIn('emails.from', $sender);
                    });
                }
                if ($receiver) {
                    $receiver = explode(',', $request->receiver);
                    $query = $query->where(function ($query) use ($receiver) {
                        $query->whereIn('emails.to', $receiver);
                    });
                }
                if ($status) {
                    $status = explode(',', $request->status);
                    $query = $query->where(function ($query) use ($status) {
                        $query->whereIn('emails.status', $status);
                    });
                }
                if ($category) {
                    $category = explode(',', $request->category);
                    $query = $query->where(function ($query) use ($category) {
                        $query->whereIn('email_category_id', $category);
                    });
                }
            }

            if (! empty($mailbox)) {
                $mailbox = explode(',', $request->mail_box);
                $query = $query->where(function ($query) use ($mailbox) {
                    $query->whereIn('email_box_id', $mailbox);
                });
            }

            if (isset($seen) && $seen != '0') {
                if ($seen != 'both') {
                    $query = $query->where('seen', $seen);
                } elseif ($seen == 'both' && $type == 'outgoing') {
                    $query = $query->where('emails.status', 'outgoing');
                }
            }

            // If it isn't trash query remove email with status trashed
            if (! $trash_query) {
                $query = $query->where(function ($query) use ($type) {
                    $isDraft = ($type == 'draft') ? 1 : 0;

                    return $query->where('emails.status', '<>', 'bin')->orWhereNull('emails.status')->where('is_draft', $isDraft);
                });
            }
            $emails = $query->select('emails.id', 'emails.created_at', 'emails.from', 'emails.to', 'emails.model_type', 'emails.type', 'emails.subject', 'emails.message',
                'emails.status', 'emails.is_draft', 'emails.error_message', 'emails.email_category_id', 'emails.email_excel_importer', 'emails.approve_mail', 'emails.is_unknow_module', 'emails.is_unknow_module',
                'chat_messages.customer_id', 'chat_messages.supplier_id', 'chat_messages.vendor_id', 'c.is_auto_simulator as customer_auto_simulator',
                'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator');

            if ($admin == 1) {
                // $emails = $query->orderByDesc('emails.id');

                // $emails = $query->paginate(30)->appends(request()->except(['page']));
            } else {
                if (count($usernames) > 0) {
                    $query = $query->where(function ($query) use ($usernames) {
                        foreach ($usernames as $_uname) {
                            $query->orWhere('from', 'like', '%'.$_uname.'%');
                        }
                    });

                    $emails = $query->where(function ($query) use ($usernames) {
                        foreach ($usernames as $_uname) {
                            $query->orWhere('to', 'like', '%'.$_uname.'%');
                        }
                    });

                    // $query  = $query->orderByDesc('emails.id');
                    // $emails = $query->paginate(30)->appends(request()->except(['page']));
                } else {
                    $emails = (new Email)->newQuery();
                    $emails = $emails->whereNull('id');
                    // $emails = $emails->orderByDesc('emails.id');
                    // $emails = $emails->paginate(30)->appends(request()->except(['page']));
                }
            }

            //Get Cron Email Histroy

            //Get List of model types

            return Datatables::of($emails)
                ->addColumn('checkbox', function ($email) {
                    $btn = '';
                    if ($email->status != 'bin') {
                        $btn .= '<input name="selector[]" id="ad_Checkbox_'.$email->id.'" class="ads_Checkbox" type="checkbox" value="'.$email->id.'" style="margin: 0px; height: auto;" />';
                    }

                    return $btn;
                })
                ->addColumn('created_at_format', function ($email) {
                    return Carbon::parse($email->created_at)->format('d-m-Y H:i:s');
                })
                ->editColumn('created_at', function ($email) {
                    return $email->created_at;
                })
                    // ->orderColumn('created_at', function ($query, $order) {
                    //     $query->orderBy('created_at1', $order);
                    // })
                ->editColumn('from', function ($email) {
                    return substr($email->from, 0, 20).(strlen($email->from) > 20 ? '...' : '');
                })
                ->editColumn('from_full', function ($email) {
                    return $email->from;
                })
                ->editColumn('to', function ($email) {
                    return substr($email->to, 0, 15).(strlen($email->to) > 10 ? '...' : '');
                })
                ->addColumn('to_full', function ($email) {
                    return $email->to;
                })
                ->editColumn('model_type', function ($email) use ($emailModelTypes) {
                    if (array_key_exists($email->model_type, $emailModelTypes)) {
                        return $email->model_type ? $emailModelTypes[$email->model_type] : 'N/A';
                    } else {
                        return $email->model_type;
                    }
                })
                ->editColumn('subject', function ($email) {
                    return $email->subject;
                })
                ->addColumn('message_short', function ($email) {
                    return substr(strip_tags($email->message), 0, 120).(strlen(strip_tags($email->message)) > 110 ? '...' : '');
                })
                ->editColumn('message', function ($email) {
                    return $email->message;
                })
                ->editColumn('status', function ($email) use ($email_status) {
                    if ($email->status == 'bin') {
                        return 'Deleted';
                    } else {
                        $status = '';
                        $status .= '<select class="form-control selecte2 status">';
                        $status .= '<option  value="" >Please select</option>';
                        foreach ($email_status as $s) {
                            if (strtolower($s->email_status) == strtolower($email->status)) {
                                $status .= '<option data-id="'.$email->id.'" selected>'.$s->email_status.'</option>';
                            } else {
                                $status .= '<option data-id="'.$email->id.'">'.$s->email_status.'</option>';
                            }
                        }
                        $status .= '</select>';

                        return $status;
                    }
                })
                ->editColumn('is_draft', function ($email) {
                    return $email->is_draft == 1 ? 'Yes' : 'No';
                })
                ->addColumn('error_message', function ($email) {
                    return strlen($email->error_message) > 20 ? substr($email->error_message, 0, 20).'...' : $email->error_message;
                })
                ->addColumn('error_message_full', function ($email) {
                    return $email->error_message;
                })
                ->addColumn('email_category_id', function ($email) use ($email_categories) {
                    $category = '<select class="form-control selecte2 email-category">';
                    $category .= '<option  value="" >Please select</option>';
                    foreach ($email_categories as $email_category) {
                        $category .= '<option  value="'.$email_category->id.'" data-id="'.$email->id.'" '.($email_category->id == $email->email_category_id ? 'selected' : '').'>'.$email_category->category_name.'</option>';
                    }
                    $category .= '</select>';

                    return $category;
                })
                ->addColumn('action', function ($email) {
                    $btn = '';
                    $btn .= '<button type="button" class="btn btn-secondary btn-sm mt-2 toggle-action" data-id="'.$email->id.'"><i class="fa fa-arrow-down"></i></button>';
                    $btn .= '<div id="action-'.$email->id.'" class="d-none">';
                    if ($email->type != 'incoming') {
                        $btn .= '<a title="Resend"  class="btn-image resend-email-btn" data-type="resend" data-id="'.$email->id.'" >
                            <i class="fa fa-repeat"></i>
                            </a>';
                    }

                    $btn .= '<a title="Reply" class="btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="'.$email->id.'" >
                        <i class="fa fa-reply"></i>
                        </a>

                        <a title="Reply All" class="btn-image reply-all-email-btn" data-toggle="modal" data-target="#replyAllMail" data-id="'.$email->id.'" >
                        <i class="fa fa-reply-all"></i>
                        </a>

                        <a title="Forward" class="btn-image forward-email-btn" data-toggle="modal" data-target="#forwardMail" data-id="'.$email->id.'" >
                        <i class="fa fa-share"></i>
                        </a>

                        <a title="Bin" class="btn-image bin-email-btn" data-id="'.$email->id.'" >
                        <i class="fa fa-trash"></i>
                        </a>

                        <button title="Remarks" style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="'.$email->id.'"><img width="2px;" src="/images/remark.png"/></button>

                        <button title="Update Status & Category" style="padding:3px;" type="button" class="btn btn-image d-inline mailupdate border-0" data-toggle="modal" data-status="'.$email->status.'" data-category="'.$email->email_category_id.'" data-target="#UpdateMail" data-id="'.$email->id.'"><img width="2px;" src="images/edit.png"/></button>

                        <a title="Import Excel Imported" href="javascript:void(0);">  <i class="fa fa-cloud-download" aria-hidden="true" onclick="excelImporter('.$email->id.')"></i></a>

                        <button title="Files Status" style="padding:3px;" type="button" class="btn btn-image d-inline" onclick="showFilesStatus('.$email->id.')">  <i class="fa fa-history" aria-hidden="true" ></i></button>';

                    if ($email->email_excel_importer == 1) {
                        $btn .= '<a href="javascript:void(0);">  <i class="fa fa-check"></i></a>';
                    }

                    if ($email->approve_mail == 1) {
                        $btn .= '<a title="Approve and send watson reply" class="btn-image resend-email-btn" data-id="'.$email->id.'" data-type="approve" href="javascript:void(0);">  <i class="fa fa-check-circle"></i></a>';
                    }

                    $btn .= '<a class="btn btn-image btn-ht" href="'.route('order.generate.order-mail.pdf', ['order_id' => 'empty', 'email_id' => $email->id]).'">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>

                        <button title="Assign Platform" style="padding:3px;" type="button" class="btn btn-image make-label d-inline" data-toggle="modal" data-target="#labelingModal" data-id="'.$email->id.'"><i class="fa fa-tags" aria-hidden="true"></i></button>

                        <a title="Email reply" class="btn btn-image btn-ht" onclick="fetchEvents('.$email['id'].')">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>

                        <a title="Email Logs" class="btn btn-image btn-ht" title="View Email Log" onclick="fetchEmailLog('.$email['id'].')">
                            <i class="fa fa-history" aria-hidden="true"></i>
                        </a>';

                    if (empty($email->module_type) && $email->is_unknow_module == 1) {
                        $btn .= '<a style="padding:3px;" type="button" title="Assign Model" class="btn btn-image make-label d-inline" data-id="'.$email->id.'" onclick="openAssignModelPopup(this);"> <i class="fa fa-envelope" aria-hidden="true"></i> </a>';
                    }

                    $btn .= '<a itle="Email Category Change Logs" style="padding:3px;" type="button" title="Email Category Change Logs" class="btn btn-image make-label d-inline" data-id="'.$email->id.'" onclick="openEmailCategoryChangeLogModelPopup(this);"> <i class="fa fa-calendar" aria-hidden="true"></i> </a>
                        <a title="Shortcut" href="javascript:;" data-toggle="modal" data-target="#create-sop-shortcut" class="btn btn-image create-sop-shortcut ml-1 create_short_cut" data-msg="'.$email->subject.'" data-id="'.$email->id.'"><i class="fa fa-asterisk" data-message="'.$email->subject.'" aria-hidden="true"></i></a>
                        <a itle="Email Status Change Logs" style="padding:3px;" type="button" title="Email Status Change Logs" class="btn btn-image make-label d-inline" data-id="'.$email->id.'" onclick="openEmailStatusChangeLogModelPopup(this);"> <i class="fa fa-calendar" aria-hidden="true"></i> </a>';

                    if ($email->customer_id > 0) {
                        $btn .= '<button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="changeSimulatorSetting(`customer`, '.$email->customer_id.', '.($email->customer_auto_simulator == 0).')"><i style="color: #757575c7;" class="fa fa-'.$email->customer_auto_simulator == 0 ? 'play' : 'pause'.'"
                            aria-hidden="true"></i>
                            </button>
                            <a href="'.route('simulator.message.list', ['object' => 'customer', 'object_id' => $email->customer_id]).'"
                            title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>';
                    } elseif ($email->vendor_id > 0) {
                        $btn .= '<button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image"
                            onclick="changeSimulatorSetting(`vendor`, '.$email->vendor_id.', '.($email->vendor_auto_simulator == 0).')">
                            <i style="color: #757575c7;" class="fa fa-'.($email->vendor_auto_simulator == 0 ? 'play' : 'pause').'"
                            aria-hidden="true"></i>
                            </button>
                            <a href="'.route('simulator.message.list', ['object' => 'customer', 'object_id' => $email->vendor_id]).'"
                            title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>';
                    } elseif ($email->supplier_id > 0) {
                        $btn .= '<button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image"
                            onclick="changeSimulatorSetting(`vendor`, '.$email->supplier_id.', '.($email->supplier_auto_simulator == 0).')">
                            <i style="color: #757575c7;" class="fa fa-'.($email->supplier_auto_simulator == 0 ? 'play' : 'pause').'"
                            aria-hidden="true"></i>
                            </button>
                            <a href="'.route('simulator.message.list', ['object' => 'customer', 'object_id' => $email->supplier_id]).'"
                            title="Load messages"><i style="color: #757575c7;" class="fa fa-file-text-o" aria-hidden="true"></i></a>';
                    }
                    // return $btn;
                    $btn .= '<button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image" onclick="createVendorPopup(`'.$email->from.'`)"><i style="color: #757575c7;" class="fa fa-user-plus" aria-hidden="true"></i></button>';
                    $btn .= '</div>';

                    return $btn;
                })
                ->addColumn('action_all', function ($email) {
                    $btn = '<tr class="action-btn-tr-'.$email->id.' d-none"><th>Action</th><td colspan="11">---</td></tr>';

                    return $btn;
                })
                ->rawColumns(['checkbox', 'message', 'status', 'email_category_id', 'action', 'action_all'])
                ->make(true);
        }
        $reports = CronJobReport::where('cron_job_reports.signature', 'fetch:all_emails')
            ->join('cron_jobs', 'cron_job_reports.signature', 'cron_jobs.signature')
            ->whereDate('cron_job_reports.created_at', '>=', Carbon::now()->subDays(10))
            ->select(['cron_job_reports.*', 'cron_jobs.last_error'])->paginate(15);
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search', compact('emails', 'date', 'term', 'type', 'email_categories', 'email_status', 'emailModelTypes'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $emails->links(),
                'count' => $emails->total(),
                'emails' => $emails,
            ], 200);
        }

        // suggested search for email forwarding
        $search_suggestions = $this->getAllEmails();

        // dont load any data, data will be loaded by tabs based on ajax
        // return view('emails.index',compact('emails','date','term','type'))->with('i', ($request->input('page', 1) - 1) * 5);
        $digita_platfirms = DigitalMarketingPlatform::select('id', 'platform', 'sub_platform')->get();

        $totalEmail = Email::count();
        $modelColors = ModelColor::select('id', 'model_name', 'color_code')->whereIn('model_name', ['customer', 'vendor', 'supplier', 'user'])->limit(10)->get();

        $datatableModel = DataTableColumn::select('column_name')
            ->where('user_id', auth()->user()->id)
            ->where('section_name', 'emails')->first();
        $dynamicColumnsToShowb = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            if (json_decode($hideColumns, true)) {
                $dynamicColumnsToShowb = json_decode($hideColumns, true);
            }
        }

        $columns[] = ['data' => 'checkbox', 'name' => 'checkbox'];
        $columns[] = ['data' => 'created_at_format', 'name' => 'created_at'];
        $columns[] = ['data' => 'from', 'name' => 'from'];
        $columns[] = ['data' => 'to', 'name' => 'to'];
        $columns[] = ['data' => 'model_type', 'name' => 'model_type'];
        $columns[] = ['data' => 'type', 'name' => 'type'];
        $columns[] = ['data' => 'subject', 'name' => 'subject'];
        $columns[] = ['data' => 'message_short', 'name' => 'message'];
        $columns[] = ['data' => 'status', 'name' => 'status'];
        $columns[] = ['data' => 'is_draft', 'name' => 'is_draft'];
        $columns[] = ['data' => 'error_message', 'name' => 'error_message'];
        $columns[] = ['data' => 'email_category_id', 'name' => 'email_category_id'];
        $columns[] = ['data' => 'action', 'name' => 'action'];

        return view('emails.index',
            [
                'columns' => $columns,
                'type' => 'email',
                'search_suggestions' => $search_suggestions,
                'email_status' => $email_status,
                'email_categories' => $email_categories,
                'emailModelTypes' => $emailModelTypes,
                'reports' => $reports,
                'digita_platfirms' => $digita_platfirms,
                'receiver' => $receiver,
                'from' => $from,
                'totalEmail' => $totalEmail,
                'modelColors' => $modelColors,
                'dynamicColumnsToShowb' => $dynamicColumnsToShowb,
            ]);
    }

    public function emailsColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'emails')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'emails';
            $column->column_name = json_encode($request->column_data);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'emails';
            $column->column_name = json_encode($request->column_data);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity saved successfully!');
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
        EmailLog::create([
            'email_id' => $email->id,
            'email_log' => 'Email resend initiated',
            'message' => $email->to,
        ]);
        Mail::to($email->to)->send(new PurchaseEmail($email->subject, $email->message, $attachment));

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

        $replyCategories = ReplyCategory::orderBy('name')->get();
        $storeWebsites = StoreWebsite::get();

        $parentCategory = ReplyCategory::where('parent_id', 0)->get();
        $allSubCategory = ReplyCategory::where('parent_id', '!=', 0)->get();
        $category = $subCategory = [];
        foreach ($allSubCategory as $value) {
            $categoryList = ReplyCategory::where('id', $value->parent_id)->first();
            if ($categoryList->parent_id == 0) {
                $category[$value->id] = $value->name;
            } else {
                $subCategory[$value->id] = $value->name;
            }
        }

        $categories = $category;

        return view('emails.reply-modal', compact('email', 'replyCategories', 'storeWebsites', 'parentCategory', 'subCategory', 'categories'));
    }

    /**
     * Provide view for email reply all modal
     *
     * @param [type] $id
     */
    public function replyAllMail($id): View
    {
        $email = Email::find($id);

        return view('emails.reply-all-modal', compact('email'));
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
            'receiver_email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->all()]);
        }

        $email = Email::find($request->reply_email_id);
        $replyPrefix = 'Re: ';
        $subject = substr($request->subject, 0, 4) === $replyPrefix
            ? $request->subject
            : $replyPrefix.$request->subject;
        $dateCreated = $email->created_at->format('D, d M Y');
        $timeCreated = $email->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, <{$email->from}> wrote:";

        $message_to_store = $originalEmailInfo.'<br/>'.$request->message;
        if ($request->pass_history == 1) {
            $message_to_store = $originalEmailInfo.'<br/>'.$request->message.'<br/>'.$email->message;
        }

        $emailsLog = Email::create([
            'model_id' => $email->id,
            'model_type' => Email::class,
            'from' => $email->from,
            'to' => $request->receiver_email,
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
     * Handle the email reply
     */
    public function submitReplyAll(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $email = Email::find($request->reply_email_id);
        $replyPrefix = 'Re: ';
        $subject = substr($request->subject, 0, 4) === $replyPrefix
            ? $request->subject
            : $replyPrefix.$request->subject;
        $dateCreated = $email->created_at->format('D, d M Y');
        $timeCreated = $email->created_at->format('H:i');
        $originalEmailInfo = "On {$dateCreated} at {$timeCreated}, <{$email->to}> wrote:";
        $message_to_store = $originalEmailInfo.'<br/>'.$request->message.'<br/>'.$email->message;

        $emailAddress = $email->to;
        $emailPattern = '/<([^>]+)>/';

        $emailsLog = Email::create([
            'model_id' => $email->id,
            'model_type' => Email::class,
            'from' => $email->to,
            'to' => $email->from,
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
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
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
        $available_models = ['supplier' => Supplier::class, 'vendor' => Vendor::class,
            'customer' => Customer::class, 'users' => User::class, ];
        $email_list = [];

        foreach ($available_models as $value) {
            $email_list = array_merge($email_list, $value::whereNotNull('email')->pluck('email')->unique()->all());
        }

        return array_values(array_unique($email_list));
    }

    public function category(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
                'category_priority' => 'required',
                'category_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors(), 'validation' => true], 200);
            }

            $values = ['category_name' => $request->input('category_name'), 'priority' => $request->input('category_priority'), 'type' => $request->category_type];
            EmailCategory::insert($values);

            cache()->forget('email_categories');

            $expirationTime = 7 * 24 * 60;

            cache()->remember('email_categories', $expirationTime, function () {
                return EmailCategory::select('id', 'category_name')->get();
            });

            return response()->json(['status' => true, 'message' => 'Category added successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
        }

    }

    public function status(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_status' => 'required',
                'status_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors(), 'validation' => true], 200);
            }

            $values = ['email_status' => $request->input('email_status'), 'type' => $request->status_type];
            EmailStatus::insert($values);

            cache()->forget('email_statuses');

            $expirationTime = 7 * 24 * 60;

            cache()->remember('email_statuses', $expirationTime, function () {
                return EmailStatus::select('id', 'email_status')->get();
            });

            return response()->json(['status' => true, 'message' => 'Status added successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
        }

    }

    public function updateEmail(Request $request): JsonResponse
    {

        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors(), 'validation' => true], 200);
            }

            $email_id = $request->input('email_id');
            $category = $request->input('category');
            $status = $request->input('status');

            $email = Email::find($email_id);
            $email->status = $status;
            $email->email_category_id = $category;
            $email->update();

            return response()->json(['status' => true, 'message' => 'Data updated successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 200);
        }
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

            LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $httpcode, EmailCommand::class, 'downloadFromURL');

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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
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

        $urlResponse = json_decode($real); //response decode
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $parameters = [];
        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $urlResponse, $httpcode, EmailCommand::class, 'downloadFromURL');

        if (isset($urlResponse->direct_link)) {
            $downloadURL = $urlResponse->direct_link;

            $d = explode('?', $downloadURL);

            $fileArray = explode('/', $d[0]);

            $filename = end($fileArray);

            $file = file_get_contents($downloadURL);

            file_put_contents(storage_path('app/files/email-attachments/'.$filename), $file);
            $path = 'email-attachments/'.$filename;

            if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {

                if (strpos($filename, '.xls') !== false || strpos($filename, '.xlsx') !== false) {
                    if (class_exists('seo2websitesErpExcelImporterErpExcelImporter')) {
                        $excel = $supplier;
                        ErpExcelImporter::excelFileProcess($filename, $excel, '');
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

        $emailStatusHistory = EmailStatusChangeHistory::where('email_id', $request->email_id)->orderByDesc('id')->first();

        $old_status_id = '';
        $old_user_id = '';

        if (! empty($emailStatusHistory)) {
            $old_status_id = $emailStatusHistory->status_id;
            $old_user_id = $emailStatusHistory->user_id;
        }

        EmailStatusChangeHistory::create([
            'status_id' => $request->status_id,
            'user_id' => Auth::id(),
            'old_status_id' => $old_status_id,
            'old_user_id' => $old_user_id,
            'email_id' => $request->email_id,
        ]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    public function syncroniseEmail(): RedirectResponse
    {
        $report = CronJobReport::create([
            'signature' => 'fetch:all_emails',
            'start_time' => Carbon::now(),
        ]);
        $failedEmailAddresses = [];
        $emailAddresses = EmailAddress::orderBy('id')->get();

        foreach ($emailAddresses as $emailAddress) {
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
                    $email_list[$value] = $value::whereNotNull('email')->pluck('id', 'email')->unique()->all();
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
                            $reference_id = $email->references;
                            //                        dump($reference_id);
                            $origin_id = $email->message_id;

                            // Skip if message is already stored
                            if (Email::where('origin_id', $origin_id)->count() > 0) {
                                continue;
                            }

                            // check if email has already been received

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
                            $to = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;

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
                                'seen' => count($email->getFlags()) > 0 ? $email->getFlags()['seen'] : 0,
                                'from' => $email->getFrom()[0]->mail,
                                'to' => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                'subject' => $email->getSubject(),
                                'message' => $content,
                                'template' => 'customer-simple',
                                'additional_data' => json_encode(['attachment' => $attachments_array]),
                                'created_at' => $email->getDate(),
                            ];

                            $emailData = Email::create($params);

                            if ($type['type'] == 'incoming') {
                                $message = trim($content);
                                $reply = (new EmailParser)->parse($message);
                                $fragment = current($reply->getFragments());
                                if ($reply) {
                                    $customer = Customer::where('email', $from)->first();
                                    if (! empty($customer)) {
                                        // store the main message
                                        $params = [
                                            'number' => $customer->phone,
                                            'message' => $fragment->getContent(),
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
                                            'from_email' => $email->getFrom()[0]->mail,
                                            'to_email' => array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail,
                                            'email_id' => $emailData->id,
                                        ];
                                        $messageModel = ChatMessage::create($params);
                                        MessageHelper::whatsAppSend($customer, $fragment->getContent(), null, null);
                                        if ($customer->storeWebsite->ai_assistant == 'geminiai') {
                                            MessageHelper::sendGeminiAiReply($fragment->getContent(), 'EMAIL', $messageModel, $customer->storeWebsite, $customer, $emailData);
                                        } else {
                                            MessageHelper::sendwatson($customer, $fragment->getContent(), null, $messageModel, $params);
                                        }

                                        // Code for if auto approve flag is YES then send Bot replay to customer email address account, If No then save email in draft tab.
                                        $replies = ChatbotQuestion::join('chatbot_question_examples', 'chatbot_questions.id', 'chatbot_question_examples.chatbot_question_id')
                                            ->join('chatbot_questions_reply', 'chatbot_questions.id', 'chatbot_questions_reply.chatbot_question_id')
                                            ->where('chatbot_questions_reply.store_website_id', ($customer->store_website_id) ? $customer->store_website_id : 1)
                                            ->select('chatbot_questions.value', 'chatbot_questions.keyword_or_question', 'chatbot_questions.erp_or_watson', 'chatbot_questions.auto_approve', 'chatbot_question_examples.question', 'chatbot_questions_reply.suggested_reply')
                                            ->where('chatbot_questions.erp_or_watson', 'erp')
                                            ->get();

                                        $messages = $fragment->getContent();

                                        foreach ($replies as $reply) {
                                            if ($messages != '' && $customer) {
                                                $keyword = $reply->question;
                                                if (($keyword == $messages || strpos(strtolower(trim($messages)), strtolower(trim($keyword))) !== false) && $reply->suggested_reply) {
                                                    $lastInsertedEmail = Email::where('id', $emailData->id)->first();
                                                    if ($reply->auto_approve == 0) {
                                                        $lastInsertedEmail->is_draft = 1;
                                                        $lastInsertedEmail->save();
                                                    } else {
                                                        $emaildetails = [];

                                                        $emaildetails['id'] = $lastInsertedEmail->id;
                                                        $emaildetails['to'] = $customer->email;
                                                        $emaildetails['subject'] = $lastInsertedEmail->subject;
                                                        $emaildetails['message'] = $reply->suggested_reply;
                                                        $from_address = '';
                                                        $from_address = array_key_exists(0, $email->getTo()->toArray()) ? $email->getTo()[0]->mail : $email->getReplyTo()[0]->mail;
                                                        if (empty($from_address)) {
                                                            $from_address = config('env.MAIL_FROM_ADDRESS');
                                                        }
                                                        $emaildetails['from'] = $from_address;

                                                        SendEmail::dispatch($lastInsertedEmail, $emaildetails)->onQueue('send_email');

                                                        $createEmail = Email::create([
                                                            'model_id' => $model_id,
                                                            'model_type' => $model_type,
                                                            'from' => $emaildetails['from'],
                                                            'to' => $emaildetails['to'],
                                                            'subject' => $emaildetails['subject'],
                                                            'message' => $reply->suggested_reply,
                                                            'template' => 'customer-simple',
                                                            'additional_data' => $model_id,
                                                            'status' => 'send',
                                                            'store_website_id' => null,
                                                            'is_draft' => 0,
                                                            'type' => 'outgoing',
                                                        ]);

                                                        $chatMessage = [
                                                            'number' => $customer->phone,
                                                            'message' => $reply->suggested_reply,
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
                                                            'from_email' => $emaildetails['from'],
                                                            'to_email' => $emaildetails['to'],
                                                            'email_id' => $createEmail->id,
                                                        ];
                                                        ChatMessage::create($chatMessage);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $historyParam = [
                    'email_address_id' => $emailAddress->id,
                    'is_success' => 1,
                ];

                EmailRunHistories::create($historyParam);
                $report->update(['end_time' => Carbon::now()]);
            } catch (Exception $e) {
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
                $failedEmailAddresses[] = $emailAddress->username;
            }
        }
        if (! empty($failedEmailAddresses)) {
            session()->flash('danger', 'Some address failed to synchronize.For more details: please check Email Run History for following Email Addresses: '.implode(', ', $failedEmailAddresses));

            return redirect()->to('/email');
        } else {
            session()->flash('success', 'Emails added successfully');

            return redirect()->to('/email');
        }
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
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
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

    public function getEmailEvents($emailId)
    {
        $exist = Email::where('id', $emailId)->first(); //$originId = "9e238becd3bc31addeff3942fc54e340@swift.generated";
        $events = [];
        $eventData = '';
        if ($exist != null) {
            $events = SendgridEvent::where('email_id', $emailId)->select('timestamp', 'event')->orderByDesc('id')->get();
        }
        foreach ($events as $event) {
            $eventData .= '<tr><td>'.$event['timestamp'].'</td><td>'.$event['event'].'</td></tr>';
        }
        if ($eventData == '') {
            $eventData = '<tr><td>No data found.</td></tr>';
        }

        return $eventData;
    }

    public function getAllEmailEvents(Request $request): View
    {
        $events = SendgridEvent::select('*');

        if (! empty($request->email)) {
            $events = $events->where('email', 'like', '%'.$request->email.'%');
        }

        if (! empty($request->event)) {
            $events = $events->where('event', 'like', '%'.$request->event.'%');
        }

        $events = $events->orderByDesc('id')->groupBy('sg_message_id')->paginate(30)->appends(request()->except(['page']));

        $event = $request->event ?? '';

        return view('emails.events', compact('events', 'event'));
    }

    public function getAllEmailEventsJourney(Request $request): View
    {
        $events = SendgridEvent::select('*');

        if (! empty($request->email)) {
            $events = $events->where('email', 'like', '%'.$request->email.'%');
        }

        if (! empty($sender_email = $request->sender_email)) {
            $events = $events->whereHas('sender', function ($query) use ($sender_email) {
                // Define the condition for filtering the related emails
                $query->where('from', $sender_email);
            });
        }

        if (! empty($request->event)) {
            $events = $events->where('event', 'like', '%'.$request->event.'%');
        }
        $events = $events->orderByDesc('id')->paginate(30)->appends(request()->except(['page']));

        $eventColors = SendgridEventColor::all();

        return view('emails.event_journey', compact('events', 'eventColors'));
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
            $colorCode = '';

            if ($emailLog['is_error'] == 1 && $emailLog['service_type'] === 'SMTP') {
                $colorCode = config('settings.email_log_smtp_error_color_code');
            }

            if ($emailLog['is_error'] == 1 && $emailLog['service_type'] === 'IMAP') {
                $colorCode = config('settings.email_log_imap_error_color_code');
            }

            $emailLogData .= '<tr style="background:'.$colorCode.'"><td>'.$emailLog['created_at'].'</td><td>'.$emailLog['email_log'].'</td><td>'.$emailLog['message'].'</td></tr>';
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

        $emailCategoryHistory = EmailCategoryHistory::where('email_id', $request->email_id)->orderByDesc('id')->first();

        $old_category_id = '';
        $old_user_id = '';

        if (! empty($emailCategoryHistory)) {
            $old_category_id = $emailCategoryHistory->category_id;
            $old_user_id = $emailCategoryHistory->user_id;
        }

        EmailCategoryHistory::create([
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'old_category_id' => $old_category_id,
            'old_user_id' => $old_user_id,
            'email_id' => $request->email_id,
        ]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    public function changeEmailStatus(Request $request): JsonResponse
    {
        Email::where('id', $request->status)->update(['status' => $request->status_id]);

        session()->flash('success', 'Status has been updated successfully');

        return response()->json(['type' => 'success'], 200);
    }

    /**
     * To view email in iframe
     */
    public function viewEmailFrame(Request $request): View
    {
        $id = $request->id;
        $emailData = Email::find($id);
        if ($emailData->seen == 1) {
            $emailData->seen = 0;
        } else {
            $emailData->seen = 1;
        }
        $emailData->save();

        return view('emails.frame-view', compact('emailData'));
    }

    public function getEmailFilterOptions(Request $request)
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

        $senderDropdown = Email::select('from');

        if (count($usernames) > 0) {
            $senderDropdown = $senderDropdown->where(function ($senderDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $senderDropdown->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $senderDropdown = $senderDropdown->orWhere(function ($senderDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $senderDropdown->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }
        $senderDropdown = $senderDropdown->distinct()->get()->toArray();

        $receiverDropdown = Email::select('to');

        if (count($usernames) > 0) {
            $receiverDropdown = $receiverDropdown->where(function ($receiverDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiverDropdown->orWhere('from', 'like', '%'.$_uname.'%');
                }
            });

            $receiverDropdown = $receiverDropdown->orWhere(function ($receiverDropdown) use ($usernames) {
                foreach ($usernames as $_uname) {
                    $receiverDropdown->orWhere('to', 'like', '%'.$_uname.'%');
                }
            });
        }

        $receiverDropdown = $receiverDropdown->distinct()->get()->toArray();

        $mailboxDropdown = EmailAddress::pluck('from_address', 'id', 'username');

        $mailboxDropdown = $mailboxDropdown->toArray();

        $response = [
            'senderDropdown' => $senderDropdown,
            'receiverDropdown' => $receiverDropdown,
            'mailboxDropdown' => $mailboxDropdown,
        ];

        return $response;
    }

    public function ajaxsearch(Request $request)
    {
        $searchEmail = $request->get('search');
        if (! empty($searchEmail)) {
            $userEmails = Email::where('type', 'incoming')->where('from', 'like', '%'.$searchEmail.'%')->orderByDesc('created_at')->get();
        } else {
            $userEmails = Email::where('type', 'incoming')->orderByDesc('created_at')->limit(5)->get();
        }
        $html = view('emails.email-search-modal-content', compact('userEmails'))->render();

        return $html;
    }

    public function getCategoryMappings(Request $request): View
    {
        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $category = $request->category ?? '';
        $email_box_id = $request->email_box_id ?? '';

        //where('type', 'incoming')
        $userEmails = Email::where('email_category_id', '>', 0)
            ->orderByDesc('created_at')
            ->groupBy('from');

        if ($term) {
            $userEmails = $userEmails->where(function ($userEmails) use ($term) {
                $userEmails->where('from', 'like', '%'.$term.'%')
                    ->orWhere('to', 'like', '%'.$term.'%')
                    ->orWhere('subject', 'like', '%'.$term.'%')
                    ->orWhere('message', 'like', '%'.$term.'%');
            });
        }

        if ($sender) {
            $sender = explode(',', $request->sender);
            $userEmails = $userEmails->where(function ($userEmails) use ($sender) {
                $userEmails->whereIn('from', $sender);
            });
        }

        if ($receiver) {
            $receiver = explode(',', $request->receiver);
            $userEmails = $userEmails->where(function ($userEmails) use ($receiver) {
                $userEmails->whereIn('to', $receiver);
            });
        }

        if ($category) {
            $category = explode(',', $request->category);
            $userEmails = $userEmails->where(function ($userEmails) use ($category) {
                $userEmails->whereIn('email_category_id', $category);
            });
        }

        if ($email_box_id) {
            $emailBoxIds = explode(',', $email_box_id);

            $userEmails = $userEmails->where(function ($userEmails) use ($emailBoxIds) {
                $userEmails->whereIn('email_box_id', $emailBoxIds);
            });
        }

        $userEmails = $userEmails->paginate(10)->appends(request()->except(['page']));

        //Get All Category
        $email_categories = EmailCategory::all();

        $emailModelTypes = Email::emailModelTypeList();

        $emailBoxes = EmailBox::select('id', 'box_name')->get();

        return view('emails.category.mappings', compact('userEmails', 'email_categories', 'emailModelTypes', 'emailBoxes'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    // DEVTASK - 23369
    public function assignModel(Request $request): JsonResponse
    {
        $model = '';
        if ($request->model_name == 'customer') {
            $model_type = Customer::class;
            $model = new Customer;
            $model_name = 'Customer';
        } elseif ($request->model_name == 'vendor') {
            $model_type = Vendor::class;
            $model = new Vendor;
            $model_name = 'Vendor';
        } elseif ($request->model_name == 'supplier') {
            $model_type = Supplier::class;
            $model = new Supplier;
            $model_name = 'Supplier';
        } else {
            $model_type = User::class;
            $model = new User;
            $model_name = 'User';
        }

        $email = Email::where('id', $request->email_id)->first();
        $email->is_unknow_module = 0;
        $email->model_type = $model_name;
        $email->save();

        Log::info('Assign Model to email : '.$model_name);

        $userExist = $model::where('email', $email->from)->first();

        if (empty($userExist)) {
            if ($request->model_name == 'supplier') {
                $model::create([
                    'email' => $email->from,
                ]);
            } else {
                $model::create([
                    'name' => explode('@', $email->from)[0],
                    'email' => $email->from,
                ]);
            }

            return response()->json(['type' => 'success'], 200);
        }
    }

    public function updateModelColor(Request $request): RedirectResponse
    {
        foreach ($request->color_name as $key => $value) {
            $model = ModelColor::where('id', $key)->first();
            $model->color_code = $value;
            $model->save();
        }

        return redirect()->to('/email');
    }

    public function getModelNames(Request $request): JsonResponse
    {
        $modelColors = ModelColor::where('model_name', 'like', '%'.$request->model_name.'%')->get();
        $returnHTML = view('emails.modelTable')->with('modelColors', $modelColors)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getEmailCategoryChangeLogs(Request $request): JsonResponse
    {
        $emailId = $request->email_id;
        $emailCagoryLogs = EmailCategoryHistory::with(['category', 'oldCategory', 'updatedByUser', 'user'])->where('email_id', $emailId)->get();

        $returnHTML = view('emails.categoryChangeLogs')->with('data', $emailCagoryLogs)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getEmailStatusChangeLogs(Request $request): JsonResponse
    {
        $emailId = $request->email_id;
        $emailCagoryLogs = EmailStatusChangeHistory::with(['status', 'oldstatus', 'updatedByUser', 'user'])->where('email_id', $emailId)->get();

        $returnHTML = view('emails.statusChangeLogs')->with('data', $emailCagoryLogs)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getReplyListByCategory(Request $request): JsonResponse
    {
        $replies = Reply::where('category_id', $request->category_id)->get();
        $returnHTML = view('emails.replyList')->with('data', $replies)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function getReplyListFromQuickReply(Request $request): JsonResponse
    {
        $storeWebsite = $request->get('storeWebsiteId');
        $parent_category = $request->get('parentCategoryId');
        $category_ids = $request->get('categoryId');
        $sub_category_ids = $request->get('subCategoryId');

        $categoryChildNode = [];

        if ($parent_category) {
            $parentNode = ReplyCategory::select(DB::raw('group_concat(id) as ids'))->where('id', $parent_category)->where('parent_id', '=', 0)->first();
            if ($parentNode) {
                $subCatChild = ReplyCategory::whereIn('parent_id', explode(',', $parentNode->ids))->get()->pluck('id')->toArray();
                $categoryChildNode = ReplyCategory::whereIn('parent_id', $subCatChild)->get()->pluck('id')->toArray();
            }
        }

        $replies = ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
            ->where('model', 'Store Website')
            ->select(['replies.*', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id']);

        if ($storeWebsite > 0) {
            $replies = $replies->where('replies.store_website_id', $storeWebsite);
        }

        if (! empty($parent_category)) {
            if ($categoryChildNode) {
                $replies = $replies->where(function ($q) use ($categoryChildNode) {
                    $q->orWhereIn('reply_categories.id', $categoryChildNode);
                });
            } else {
                $replies = $replies->where(function ($q) use ($parent_category) {
                    $q->orWhere('reply_categories.id', $parent_category)->where('reply_categories.parent_id', '=', 0);
                });
            }
        }

        if (! empty($category_ids)) {
            $replies = $replies->where(function ($q) use ($category_ids) {
                $q->orWhere('reply_categories.parent_id', $category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        if (! empty($sub_category_ids)) {
            $replies = $replies->where(function ($q) use ($sub_category_ids) {
                $q->orWhere('reply_categories.id', $sub_category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        $replies = $replies->get();

        $returnHTML = view('emails.replyList')->with('data', $replies)->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }

    public function eventColor(Request $request): RedirectResponse
    {
        $eventColors = $request->all();
        foreach ($eventColors['color_name'] as $key => $value) {
            $sendgridEventColor = SendgridEventColor::find($key);
            $sendgridEventColor->color = $value;
            $sendgridEventColor->save();
        }

        return redirect()->back()->with('success', 'The event color updated successfully.');
    }

    public function updateEmailRead(Request $request): JsonResponse
    {
        $email = Email::findOrFail($request->get('id'));
        $email->seen = 1;
        $email->update();

        return response()->json(['code' => 200, 'data' => $email, 'message' => 'Email Update successfully!!!']);
    }

    public function quickEmailList(Request $request): View
    {
        $emails = new Email;
        $email_categories = EmailCategory::get();

        $senderEmailIds = Email::select('from')->groupBy('from')->get();
        $receiverEmailIds = Email::select('to')->groupBy('to')->get();
        $modelsTypes = Email::select('model_type')->groupBy('model_type')->get();
        $mailTypes = Email::select('type')->groupBy('type')->get();
        $emailStatuses = Email::select('status')->groupBy('status')->get();

        //Get All Status
        $email_status = new EmailStatus;

        if (! empty($request->type) && $request->type == 'outgoing') {
            $email_status = $email_status->where('type', 'sent');
        } else {
            $email_status = $email_status->where('type', '!=', 'sent');
        }

        $email_status = $email_status->get();

        if ($request->sender_ids) {
            $emails = $emails->WhereIn('from', $request->sender_ids);
        }
        if ($request->receiver_ids) {
            $emails = $emails->WhereIn('website_id', $request->receiver_ids);
        }
        if ($request->model_types) {
            $emails = $emails->WhereIn('to', $request->model_types);
        }
        if ($request->mail_types) {
            $emails = $emails->WhereIn('type', $request->mail_types);
        }
        if ($request->cat_ids) {
            $emails = $emails->WhereIn('email_category_id', $request->cat_ids);
        }
        if ($request->status) {
            $emails = $emails->WhereIn('status', $request->status);
        }
        if ($request->date) {
            $emails = $emails->where('created_at', 'LIKE', '%'.$request->date.'%');
        }

        $emails = $emails->latest()->paginate(Setting::get('pagination', 25));

        return view('emails.quick-email-list', compact('emails', 'email_categories', 'senderEmailIds', 'receiverEmailIds', 'modelsTypes', 'mailTypes', 'emailStatuses', 'email_status'));
    }

    public function getEmailreplies(Request $request)
    {
        $id = $request->id;
        $emailReplies = Reply::where('category_id', $id)->orderBy('id')->get();

        return json_encode($emailReplies);
    }

    public function viewEmailFrameInfo(Request $request): View
    {
        $id = $request->id;
        $emailData = Email::find($id);

        $sender_email = $emailData->to;
        $emailAddresses = EmailAddress::where('from_address', $sender_email)->orderBy('id')->first();

        if (! empty($emailAddresses)) {
            return view('emails.content-view', compact('emailAddresses'));
        }
    }

    public function getEmailSearchModal(): JsonResponse
    {
        $userEmails = Email::where('seen', '0')
            ->orderByDesc('created_at')
            ->latest()
            ->take(20)
            ->get();
        $returnHTML = view('emails.partials.modals.email-search-modal', compact('userEmails'))->render();

        return response()->json(['html' => $returnHTML, 'type' => 'success'], 200);
    }
}
