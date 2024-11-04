<?php

namespace App\Http\Controllers;

use App\CronJobReport;
use App\Customer;
use App\DigitalMarketingPlatform;
use App\Email;
use App\EmailAssign;
use App\EmailCategory;
use App\Models\EmailBox;
use App\Models\EmailStatus;
use App\Supplier;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailBoxController extends Controller
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

        // Set default type as incoming
        $type = 'incoming';
        $seen = '0';
        $from = ''; //Purpose : Add var -  DEVTASK-18283

        $term = $request->term ?? '';
        $sender = $request->sender ?? '';
        $receiver = $request->receiver ?? '';
        $category = $request->category ?? '';
        $email_model_type = $request->email_model_type ?? '';
        $email_box_id = $request->email_box_id ?? '';
        $email_type = $request->email_type ?? '';

        $date = $request->date ?? '';
        $type = $request->type ?? $type;
        $seen = $request->seen ?? $seen;
        $query = (new Email)->newQuery();

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
        }
        //END - DEVTASK-18283

        // If type is bin, check for status only
        if ($type == 'bin') {
            $trash_query = true;
            $query = $query->where('status', 'bin');
        } elseif ($type == 'draft') {
            $query = $query->where('is_draft', 1)->where('status', '<>', 'pre-send');
        } elseif ($type == 'pre-send') {
            $query = $query->where('status', 'pre-send');
        } elseif (! empty($request->type)) {
            $query = $query->where(function ($query) use ($type) {
                $query->where('type', $type)->where('status', '<>', 'bin')->where('is_draft', '<>', 1)->where('status', '<>', 'pre-send');
            });
        }

        if ($email_model_type) {
            $model_type = explode(',', $email_model_type);
            $query = $query->where(function ($query) use ($model_type) {
                $query->whereIn('model_type', $model_type);
            });
        }

        if ($term) {
            $query = $query->where(function ($query) use ($term) {
                $query->where('from', 'like', '%'.$term.'%')
                    ->orWhere('to', 'like', '%'.$term.'%')
                    ->orWhere('subject', 'like', '%'.$term.'%')
                    ->orWhere('message', 'like', '%'.$term.'%');
            });
        }

        if ($sender) {
            $sender = explode(',', $request->sender);
            $query = $query->where(function ($query) use ($sender) {
                $query->whereIn('from', $sender);
            });
        }

        if ($receiver) {
            $receiver = explode(',', $request->receiver);
            $query = $query->where(function ($query) use ($receiver) {
                $query->whereIn('to', $receiver);
            });
        }

        if ($category) {
            $category = explode(',', $request->category);
            $query = $query->where(function ($query) use ($category) {
                $query->whereIn('email_category_id', $category);
            });
        }

        $query->where('email_category_id', '>', 0);

        if (! empty($email_type)) {
            if ($email_type == 'Read') {
                $query = $query->where('type', 'incoming');
                $query = $query->where('seen', 1);
            } elseif ($email_type == 'Unread') {
                $query = $query->where('type', 'incoming');
                $query = $query->where('seen', 0);
            } elseif ($email_type == 'Sent') {
                $query = $query->where('type', 'outgoing');
            } elseif ($email_type == 'Trash') {
                $query = $query->where('status', 'bin');
            } elseif ($email_type == 'Draft') {
                $query = $query->where('is_draft', 1)->where('status', '<>', 'pre-send');
            } elseif ($email_type == 'Queue') {
                $query = $query->where('status', 'pre-send');
            }
        }

        if ($email_box_id) {
            $emailBoxIds = explode(',', $email_box_id);

            $query = $query->where(function ($query) use ($emailBoxIds) {
                $query->whereIn('email_box_id', $emailBoxIds);
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
                $emails = $emails->whereNull('id');
                $emails = $emails->paginate(30)->appends(request()->except(['page']));
            }
        }

        //Get Cron Email Histroy
        $reports = CronJobReport::where('cron_job_reports.signature', 'fetch:all_emails')
            ->join('cron_jobs', 'cron_job_reports.signature', 'cron_jobs.signature')
            ->whereDate('cron_job_reports.created_at', '>=', Carbon::now()->subDays(10))
            ->select(['cron_job_reports.*', 'cron_jobs.last_error'])->paginate(15);

        //Get All Status
        $email_status = EmailStatus::get();

        //Get List of model types
        $emailModelTypes = Email::emailModelTypeList();

        //Get All Category
        $email_categories = EmailCategory::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('emails.search_email', compact('emails', 'date', 'term', 'type', 'email_categories', 'email_status', 'emailModelTypes'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $emails->links(),
                'count' => $emails->total(),
                'emails' => $emails,
            ], 200);
        }

        // suggested search for email forwarding
        $search_suggestions = $this->getAllEmails();

        $digita_platfirms = DigitalMarketingPlatform::all();

        $totalEmail = Email::whereNotNull('email_box_id')->count();

        $emailBoxes = EmailBox::select('id', 'box_name')->get();

        return view('mailbox.index', ['emails' => $emails, 'type' => 'email', 'search_suggestions' => $search_suggestions, 'email_status' => $email_status, 'email_categories' => $email_categories, 'emailModelTypes' => $emailModelTypes, 'reports' => $reports, 'digita_platfirms' => $digita_platfirms, 'receiver' => $receiver, 'from' => $from, 'totalEmail' => $totalEmail, 'emailBoxes' => $emailBoxes])->with('i', ($request->input('page', 1) - 1) * 5);
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
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
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
}
