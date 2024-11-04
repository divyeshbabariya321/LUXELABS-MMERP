<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\DeveloperTask;
use App\DeveloperTaskHistory;
use App\Email;
use App\Exports\TimeDoctorActivityReport;
use App\Exports\TimeDoctorNotificationReport;
use App\Http\Requests\SubmitPaymentRequestTimeDoctorActivityRequest;
use App\Jobs\SendEmail;
use App\Library\TimeDoctor\Src\Timedoctor;
use App\Loggers\TimeDoctorCommandLogMessage;
use App\Mails\Manual\DocumentEmail;
use App\PayentMailData;
use App\Payment;
use App\PaymentReceipt;
use App\Task;
use App\Team;
use App\TimeDoctor\TimeDoctorActivity;
use App\TimeDoctor\TimeDoctorActivityByPaymentFrequency;
use App\TimeDoctor\TimeDoctorActivityNotification;
use App\TimeDoctor\TimeDoctorActivitySummary;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorTask;
use App\TimeDoctor\TimeDoctorTaskEfficiency;
use App\TimeDoctor\TimeDoctorTaskNote;
use App\User;
use App\UserAvaibility;
use App\UserRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TimeDoctorActivitiesController extends Controller
{
    public $timedoctor;

    public function __construct()
    {
        $this->timedoctor = Timedoctor::getInstance();
    }

    public function index(): View
    {
        $title = 'Time Doctor Activities';

        return view('time-doctor.activities.index', compact('title'));
    }

    public function notification(): View
    {
        $title = 'Time Doctor Notification';

        $users = User::orderBy('name')->get();

        return view('time-doctor.activities.notification.index', compact('title', 'users'));
    }

    public function notificationRecords(Request $request): JsonResponse
    {
        $records = TimeDoctorActivityNotification::join('users as u', 'time_doctor_activity_notifications.user_id', 'u.id');

        $records->leftJoin('user_avaibilities as av', 'time_doctor_activity_notifications.user_id', 'av.user_id');
        $records->where('av.is_latest', 1);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('u.name', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($request->user_id)) {
            $records = $records->where('time_doctor_activity_notifications.user_id', $request->user_id);
        }

        if ($request->start_date != null) {
            $records = $records->whereDate('start_date', '>=', $request->start_date.' 00:00:00');
        }

        if ($request->end_date != null) {
            $records = $records->whereDate('start_date', '<=', $request->end_date.' 23:59:59');
        }

        $records = $records->select([
            'time_doctor_activity_notifications.*',
            'u.name as user_name',
            'av.minute as daily_working_hour',
            'u.name as total_working_hour',
        ])
            ->orderByDesc('total_track')->get();

        $recordsArr = [];

        $totalUserTrack = 0;
        $display_user_total_hour = 0;

        foreach ($records as $row) {
            $totalUserTrack = $totalUserTrack + $row->total_track;

            $dwork = $row->daily_working_hour ? number_format($row->daily_working_hour, 2, '.', '') : 0;

            $thours = floor($row->total_track / 3600);
            $tminutes = floor(($row->total_track / 60) % 60);
            $twork = $thours.':'.sprintf('%02d', $tminutes);

            $difference = (($row->daily_working_hour * 60 * 60) - $row->total_track);

            $sing = '';
            if ($difference > 0) {
                $sign = '-';
            } elseif ($difference < 0) {
                $sign = '+';
            } else {
                $sign = '';
            }
            $admin = null;
            if (Auth::user()->hasRole(config('constants.ADMIN_ROLE'))) {
                $admin = 1;
            }

            $hours = floor(abs($difference) / 3600);
            $minutes = sprintf('%02d', floor((abs($difference) / 60) % 60));

            $latest_message = ChatMessage::where('user_id', $row->user_id)->where('hubstuff_activity_user_id', '!=', null)->orderByDesc('id')->first();
            $latest_msg = null;
            if ($latest_message) {
                $latest_msg = $latest_message->message;
                if (strlen($latest_message->message) > 20) {
                    $latest_msg = substr($latest_message->message, 0, 20).'...';
                }
            }
            $recordsArr[] = [

                'id' => $row->id,
                'user_name' => $row->user_name,
                'user_id' => $row->user_id,
                'start_date' => Carbon::parse($row->start_date)->format('Y-m-d'),
                'daily_working_hour' => $dwork,
                'total_working_hour' => $twork,
                'different' => $sign.$hours.':'.$minutes,
                'min_percentage' => $row->min_percentage,
                'actual_percentage' => $row->actual_percentage,
                'reason' => $row->reason,
                'status' => $row->status,
                'is_admin' => $admin,
                'is_hod_crm' => 'user',
                'latest_message' => $latest_msg,

            ];
        }

        if ($request->user_id) {
            $hrs = floor($totalUserTrack / 3600);
            $mnts = floor(($totalUserTrack / 60) % 60);
            $display_user_total_hour = $hrs.':'.sprintf('%02d', $mnts);
        }

        return response()->json([
            'code' => 200,
            'data' => $recordsArr,
            'total' => count($records),
            'user_id' => $request->get('user_id') ?? 0,
            'sum' => $display_user_total_hour,
        ]);
    }

    public function downloadNotification(Request $request)
    {
        $records = TimeDoctorActivityNotification::join('users as u', 'time_doctor_activity_notifications.user_id', 'u.id');

        $records->leftJoin('user_avaibilities as av', 'time_doctor_activity_notifications.user_id', 'av.user_id');
        $records->where('av.is_latest', 1);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('u.name', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($request->user_id)) {
            $records = $records->where('time_doctor_activity_notifications.user_id', $request->user_id);
        }

        if ($request->start_date != null) {
            $records = $records->whereDate('start_date', '>=', $request->start_date.' 00:00:00');
        }

        if ($request->end_date != null) {
            $records = $records->whereDate('start_date', '<=', $request->end_date.' 23:59:59');
        }

        $records = $records->select([
            'time_doctor_activity_notifications.*',
            'u.name as user_name',
            'av.minute as daily_working_hour',
            'u.name as total_working_hour',
        ])
            ->latest()->get();

        $recordsArr = [];
        foreach ($records as $row) {
            $dwork = $row->daily_working_hour ? number_format($row->daily_working_hour, 2, '.', '') : 0;

            $thours = floor($row->total_track / 3600);
            $tminutes = floor(($row->total_track / 60) % 60);
            $twork = $thours.':'.sprintf('%02d', $tminutes);

            $difference = (($row->daily_working_hour * 60 * 60) - $row->total_track);

            $sing = '';
            if ($difference > 0) {
                $sign = '-';
            } elseif ($difference < 0) {
                $sign = '+';
            } else {
                $sign = '';
            }

            $hours = floor(abs($difference) / 3600);
            $minutes = sprintf('%02d', floor((abs($difference) / 60) % 60));

            $recordsArr[] = [
                'user_name' => $row->user_name,
                'start_date' => Carbon::parse($row->start_date)->format('Y-m-d'),
                'daily_working_hour' => $dwork,
                'total_working_hour' => $twork,
                'different' => $sign.$hours.':'.$minutes,
                'min_percentage' => $row->min_percentage,
                'actual_percentage' => $row->actual_percentage,
                'reason' => $row->reason,
                'status' => $row->status,

            ];
        }

        $filename = 'Report-'.request('start_date').'-To-'.request('end_date').'.csv';

        return Excel::download(new TimeDoctorNotificationReport($recordsArr), $filename);
    }

    public function notificationReasonSave(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $tdnotification = TimeDoctorActivityNotification::find($request->id);
            if ($tdnotification != null) {
                $tdnotification->reason = $request->reason;
                $tdnotification->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Added succesfully']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Requested id is not in database']);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        if (! auth()->user()->isAdmin()) {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'only admin can change status.']);
        }
        if ($request->id != null) {
            $tdnotification = TimeDoctorActivityNotification::find($request->id);
            if ($tdnotification != null) {
                $tdnotification->status = $request->status;
                $tdnotification->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'changed succesfully']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Requested id is not in database']);
    }

    public function timeDoctorActivityCommandExecution(Request $request): JsonResponse
    {
        $start_date = $request->startDate ? $request->startDate : date('Y-m-d', strtotime('-1 days'));
        $end_date = $request->endDate ? $request->endDate : date('Y-m-d', strtotime('-1 days'));
        $userid = $request->user_id;

        $users = User::where('id', $userid)->get();

        foreach ($users as $key => $user) {
            $user_id = $user->id;

            $data['email'] = $user->email;
            $data['title'] = 'Time Doctor Activities Report';

            $tasks = PaymentReceipt::with('chat_messages', 'user')->where('user_id', $user_id)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();

            foreach ($tasks as $task) {
                $task->user;

                $totalPaid = Payment::where('payment_receipt_id', $task->id)->sum('amount');
                if ($totalPaid) {
                    $task->paid_amount = number_format($totalPaid, 2);
                    $task->balance = $task->rate_estimated - $totalPaid;
                    $task->balance = number_format($task->balance, 2);
                } else {
                    $task->paid_amount = 0;
                    $task->balance = $task->rate_estimated;
                    $task->balance = number_format($task->balance, 2);
                }
                if ($task->task_id) {
                    $task->taskdetails = Task::find($task->task_id);
                    $task->estimate_minutes = 0;
                    if ($task->taskdetails) {
                        $task->details = $task->taskdetails->task_details;
                        if ($task->worked_minutes == null) {
                            $task->estimate_minutes = $task->taskdetails->approximate;
                        } else {
                            $task->estimate_minutes = $task->worked_minutes;
                        }
                    }
                } elseif ($task->developer_task_id) {
                    $task->taskdetails = DeveloperTask::find($task->developer_task_id);
                    $task->estimate_minutes = 0;
                    if ($task->taskdetails) {
                        $task->details = $task->taskdetails->task;
                        if ($task->worked_minutes == null) {
                            $task->estimate_minutes = $task->taskdetails->estimate_minutes;
                        } else {
                            $task->estimate_minutes = $task->worked_minutes;
                        }
                    }
                } else {
                    $task->details = $task->remarks;
                    $task->estimate_minutes = $task->worked_minutes;
                }
            }

            $activityUsers = collect([]);

            foreach ($tasks as $task) {
                $a['date'] = $task->date;
                $a['details'] = $task->details;

                if ($task->task_id) {
                    $category = 'Task #'.$task->task_id;
                } elseif ($task->developer_task_id) {
                    $category = 'Devtask #'.$task->developer_task_id;
                } else {
                    $category = 'Manual';
                }

                $a['category'] = $category;
                $a['time_spent'] = $task->estimate_minutes;
                $a['amount'] = $task->rate_estimated;
                $a['currency'] = $task->currency;
                $a['amount_paid'] = $task->paid_amount;
                $a['balance'] = $task->balance;
                $activityUsers->push($a);
            }

            $total_amount = 0;
            $total_amount_paid = 0;
            $total_balance = 0;
            foreach ($activityUsers as $key => $value) {
                $total_amount += $value['amount'] ?? 0;
                $total_amount_paid += $value['amount_paid'] ?? 0;
                $total_balance += $value['balance'] ?? 0;
            }

            $path = '';
            $file_data = $this->downloadExcelReport($activityUsers);
            $path = $file_data;

            $today = Carbon::now()->toDateTimeString();
            $payment_date = Carbon::createFromFormat('Y-m-d H:s:i', $today);
            $storage_path = $path;

            PayentMailData::create([
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'file_path' => $storage_path,
                'total_amount' => round($total_amount, 2),
                'total_amount_paid' => round($total_amount_paid, 2),
                'total_balance' => round($total_balance, 2),
                'payment_date' => $payment_date,
                'command_execution' => 'Manually',
            ]);

            $file_paths[] = $path;

            $emailClass = (new DocumentEmail('Time Doctor Activities Report', 'Time Doctor Payment Activity', $file_paths))->build();

            $email = Email::create([
                'model_id' => $user_id,
                'model_type' => User::class,
                'from' => $emailClass->fromMailer,
                'to' => $user->email,
                'subject' => $emailClass->subject,
                'message' => $emailClass->render(),
                'template' => 'customer-simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'status' => 'pre-send',
                'is_draft' => 1,
                'cc' => null,
                'bcc' => null,
            ]);

            SendEmail::dispatch($email)->onQueue('send_email');
        }

        return response()->json(['code' => 200, 'message' => 'Command Execution Success']);
    }

    public function getActivityUsers(Request $request, $params = null, $where = null)
    {
        if ($params !== null) {
            $params = $params->request->all();

            $request->activity_command = $params['activity_command'];
            $request->user_id = $params['user_id'];
            $request->user = $params['user'];
            $request->developer_task_id = $params['developer_task_id'];
            $request->task_id = $params['task_id'];
            $request->task_status = $params['task_status'];
            $request->start_date = $params['start_date'];
            $request->end_date = $params['end_date'];
            $request->status = $params['status'];
            $request->submit = $params['submit'];
            $request->response_type = $params['response_type'];
            Auth::login($request->user);
        }

        if ($where == 'TimeDoctorActivityCommand') {
            if (isset($params['TimeDoctorCommandLogMessage_id'])) {
                $time_doctor_log = TimeDoctorCommandLogMessage::find($params['TimeDoctorCommandLogMessage_id']);
            }

            $title = 'Time Doctor Activities';
            $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime('-1 days'));
            $end_date = $request->end_date ? $request->end_date : date('Y-m-d', strtotime('-1 days'));
            $user_id = $request->user_id ? $request->user_id : null;

            $tasks = PaymentReceipt::with('chat_messages', 'user')->where('user_id', $user_id)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();

            $taskIds = PaymentReceipt::with('chat_messages', 'user')->where('user_id', $user_id)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->pluck('id');
            if (isset($time_doctor_log)) {
                $time_doctor_log->message = $time_doctor_log->message.'-->get payment receipt_in  date '.json_encode($taskIds);
                $time_doctor_log->save();
            }
            foreach ($tasks as $task) {
                $task->user;

                $totalPaid = Payment::where('payment_receipt_id', $task->id)->sum('amount');
                if ($totalPaid) {
                    $task->paid_amount = number_format($totalPaid, 2);
                    $task->balance = $task->rate_estimated - $totalPaid;
                    $task->balance = number_format($task->balance, 2);
                } else {
                    $task->paid_amount = 0;
                    $task->balance = $task->rate_estimated;
                    $task->balance = number_format($task->balance, 2);
                }
                if ($task->task_id) {
                    $task->taskdetails = Task::find($task->task_id);
                    $task->estimate_minutes = 0;
                    if ($task->taskdetails) {
                        $task->details = $task->taskdetails->task_details;
                        if ($task->worked_minutes == null) {
                            $task->estimate_minutes = $task->taskdetails->approximate;
                        } else {
                            $task->estimate_minutes = $task->worked_minutes;
                        }
                    }
                } elseif ($task->developer_task_id) {
                    $task->taskdetails = DeveloperTask::find($task->developer_task_id);
                    $task->estimate_minutes = 0;
                    if ($task->taskdetails) {
                        $task->details = $task->taskdetails->task;
                        if ($task->worked_minutes == null) {
                            $task->estimate_minutes = $task->taskdetails->estimate_minutes;
                        } else {
                            $task->estimate_minutes = $task->worked_minutes;
                        }
                    }
                } else {
                    $task->details = $task->remarks;
                    $task->estimate_minutes = $task->worked_minutes;
                }
            }

            $activityUsers = collect([]);

            foreach ($tasks as $task) {
                $a['date'] = $task->date;
                $a['details'] = $task->details;

                if ($task->task_id) {
                    $category = 'Task #'.$task->task_id;
                } elseif ($task->developer_task_id) {
                    $category = 'Devtask #'.$task->developer_task_id;
                } else {
                    $category = 'Manual';
                }

                $a['category'] = $category;
                $a['time_spent'] = $task->estimate_minutes;
                $a['amount'] = $task->rate_estimated;
                $a['currency'] = $task->currency;
                $a['amount_paid'] = $task->paid_amount;
                $a['balance'] = $task->balance;
                $activityUsers->push($a);
            }
        } else {
            $title = 'Time Doctor Activities';
            $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime('-1 days'));
            $end_date = $request->end_date ? $request->end_date : date('Y-m-d', strtotime('-1 days'));
            $user_id = $request->user_id ? $request->user_id : null;
            $task_id = $request->task_id ? $request->task_id : null;
            $task_status = $request->task_status ? $request->task_status : null;
            $developer_task_id = $request->developer_task_id ? $request->developer_task_id : null;

            $taskIds = [];
            if (! empty($developer_task_id)) {
                $developer_tasks = DeveloperTask::find($developer_task_id);
                if (! empty($developer_tasks)) {
                    if (! empty($developer_tasks->time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->time_doctor_task_id;
                    }
                    if (! empty($developer_tasks->lead_time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->lead_time_doctor_task_id;
                    }
                    if (! empty($developer_tasks->team_lead_time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->team_lead_time_doctor_task_id;
                    }
                    if (! empty($developer_tasks->tester_time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->tester_time_doctor_task_id;
                    }
                }
            }

            if (! empty($task_status)) {
                $developer_tasks = DeveloperTask::where('status', $task_status)->where('time_doctor_task_id', '!=', 0)->pluck('time_doctor_task_id');
                if (! empty($developer_tasks)) {
                    $taskIds = $developer_tasks;
                }
            }

            if (! empty($task_id)) {
                $developer_tasks = Task::find($task_id);

                if (! empty($developer_tasks)) {
                    if (! empty($developer_tasks->time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->time_doctor_task_id;
                    }
                    if (! empty($developer_tasks->lead_time_doctor_task_id)) {
                        $taskIds[] = $developer_tasks->lead_time_doctor_task_id;
                    }
                }
            }

            if (! empty($taskIds) || ! empty($task_id) || ! empty($developer_task_id)) {
                $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereIn('time_doctor_activities.task_id', $taskIds)->whereDate('time_doctor_activities.starts_at', '>=', $start_date)->whereDate('time_doctor_activities.starts_at', '<=', $end_date);
            } else {
                $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', '>=', $start_date)->whereDate('time_doctor_activities.starts_at', '<=', $end_date);
            }

            if (Auth::user()->isAdmin()) {
                $users = User::all()->pluck('name', 'id')->toArray();
            } else {
                $members = Team::join('team_user', 'team_user.team_id', 'teams.id')->where('teams.user_id', Auth::user()->id)->distinct()->pluck('team_user.user_id');

                if (! count($members)) {
                    $members = [Auth::user()->id];
                } else {
                    $members[] = Auth::user()->id;
                }
                $query = $query->whereIn('time_doctor_members.user_id', $members);

                $users = User::whereIn('id', $members)->pluck('name', 'id')->toArray();
            }

            if ($request->user_id) {
                $query = $query->where('time_doctor_members.user_id', $request->user_id);
            }

            $activities = $query->select(
                DB::raw('
            time_doctor_activities.user_id,
            SUM(time_doctor_activities.tracked) as total_tracked,DATE(time_doctor_activities.starts_at) as date,time_doctor_members.user_id as system_user_id')
            )->groupBy('date', 'system_user_id')->orderByDesc('date')->get();

            $activityUsers = collect([]);

            foreach ($activities as $activity) {
                $a = [];

                $efficiencyObj = TimeDoctorTaskEfficiency::where('user_id', $activity->user_id)->first();
                // all activities

                if (isset($efficiencyObj->id) && $efficiencyObj->id > 0) {
                    $a['admin_efficiency'] = $efficiencyObj->admin_input;
                    $a['user_efficiency'] = $efficiencyObj->user_input;
                    $a['efficiency'] = (Auth::user()->isAdmin()) ? $efficiencyObj->admin_input : $efficiencyObj->user_input;

                    Log::channel('time_doctor_activity_command')->info('check: time doctor activity id > 0'.$efficiencyObj->id.' and ingormattion'.json_encode($a));
                } else {
                    $a['admin_efficiency'] = '';
                    $a['user_efficiency'] = '';

                    $a['efficiency'] = '';
                }

                if ($activity->system_user_id) {
                    $user = User::find($activity->system_user_id);
                    if ($user) {
                        $activity->userName = $user->name;
                        $activity->payment_frequency = $user->payment_frequency;
                        $activity->last_mail_sent_payment = $user->last_mail_sent_payment;
                        $activity->fixed_price_user_or_job = $user->fixed_price_user_or_job;
                        $activity->user_id_data = $user->id;
                    } else {
                        $activity->userName = '';
                        $activity->payment_frequency = '';
                        $activity->last_mail_sent_payment = '';
                        $activity->fixed_price_user_or_job = '';
                        $activity->user_id_data = '';
                    }
                } else {
                    $activity->userName = '';
                    $activity->payment_frequency = '';
                    $activity->last_mail_sent_payment = '';
                    $activity->fixed_price_user_or_job = '';
                    $activity->user_id_data = '';
                }

                $ac_data = $query->select(
                    DB::raw('
                        time_doctor_activities.user_id,
                        SUM(time_doctor_activities.tracked) as total_tracked,DATE(time_doctor_activities.starts_at) as date,time_doctor_members.user_id as system_user_id')
                )->where('time_doctor_members.user_id', $activity->system_user_id)->groupBy('date', 'user_id')->orderByDesc('date')->get();

                $ac_user_id = [];
                $ac_user_count = 0;

                foreach ($ac_data as $data) {
                    $ac_user_id[] = "'".$data->user_id."'";
                }
                $ac_user_id = implode(',', $ac_user_id);

                // send time doctor activities
                $ac = [];
                if ($ac_user_id != '') {
                    $ac = TimeDoctorActivity::whereDate('starts_at', $activity->date)
                        ->whereRaw('time_doctor_activities.user_id IN ('.$ac_user_id.')')
                        ->get();
                }

                $totalApproved = 0;
                $totalPending = 0;
                $isAllSelected = 0;
                $a['tasks'] = [];
                $lsTask = [];
                foreach ($ac as $ar) {
                    $taskSubject = '';
                    if ($ar->task_id) {
                        if ($ar->is_manual) {
                            $task = DeveloperTask::where('id', $ar->task_id)->first();
                            if ($task) {
                                $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                $taskSubject = $ar->task_id.'||#DEVTASK-'.$task->id.'-'.$task->subject."||#DEVTASK-$task->id||$estMinutes||$task->status||$task->id";
                                Log::channel('time_doctor_activity_command')->info('task true ');
                            } else {
                                $task = Task::where('id', $ar->task_id)->first();
                                if ($task) {
                                    $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                    $taskSubject = $ar->task_id.'||#TASK-'.$task->id.'-'.$task->task_subject."||#TASK-$task->id||$estMinutes||$task->status||$task->id";
                                }
                            }
                        } else {
                            $tracked = $ar->tracked;
                            $task = DeveloperTask::where('time_doctor_task_id', 'like', '%'.$ar->task_id.'%')->orWhere('lead_time_doctor_task_id', 'like', '%'.$ar->task_id.'%')->first();
                            if ($task && empty($task_id)) {
                                $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                $taskSubject = $ar->task_id.'||#DEVTASK-'.$task->id.'-'.$task->subject."||#DEVTASK-$task->id||$estMinutes||$task->status||$task->id";
                            } else {
                                $task = Task::where('time_doctor_task_id', 'like', '%'.$ar->task_id.'%')->orWhere('lead_time_doctor_task_id', 'like', '%'.$ar->task_id.'%')->first();
                                if ($task && empty($developer_task_id)) {
                                    $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                    $taskSubject = $ar->task_id.'||#TASK-'.$task->id.'-'.$task->task_subject."||#TASK-$task->id||$estMinutes||$task->status||$task->id";
                                }
                            }
                        }
                    }
                    $lsTask[] = $taskSubject;
                }

                Log::channel('time_doctor_activity_command')->info('ls task array'.json_encode($lsTask));
                $a['tasks'] = array_unique($lsTask);

                $timeDoctorActivitySummery = TimeDoctorActivitySummary::where('date', $activity->date)->where('user_id', $activity->system_user_id)->orderByDesc('created_at')->first();

                if ($request->status == 'approved') {
                    if ($timeDoctorActivitySummery && $timeDoctorActivitySummery->final_approval == 1) {
                        if ($timeDoctorActivitySummery->forworded_person == 'admin') {
                            $status = 'Approved by admin';
                            $totalApproved = $timeDoctorActivitySummery->accepted;
                            $totalPending = $timeDoctorActivitySummery->pending;
                            $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                            $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $timeDoctorActivitySummery->receiver;
                            $final_approval = 1;

                            $a['system_user_id'] = $activity->system_user_id;
                            $a['user_id'] = $activity->user_id;
                            $a['total_tracked'] = $activity->total_tracked;
                            $a['date'] = $activity->date;
                            $a['userName'] = $activity->userName;
                            $a['forworded_to'] = $forworded_to;
                            $a['status'] = $status;
                            $a['totalApproved'] = $totalApproved;
                            $a['totalPending'] = $totalPending;
                            $a['totalUserRequest'] = $totalUserRequest;
                            $a['totalNotPaid'] = $totalNotPaid;
                            $a['final_approval'] = $final_approval;
                            $a['note'] = $timeDoctorActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                            Log::channel('time_doctor_activity_command')->info('end admin condition if forwarded and status approve');
                        }
                    }
                } elseif ($request->status == 'pending') {
                    if ($timeDoctorActivitySummery && $timeDoctorActivitySummery->final_approval == 1) {
                        if ($timeDoctorActivitySummery->forworded_person == 'admin') {
                            $status = 'Pending by admin';
                            $totalApproved = $timeDoctorActivitySummery->accepted;
                            $totalPending = $timeDoctorActivitySummery->pending;
                            $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                            $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 2)->where('paid', 0)->sum('tracked');

                            $forworded_to = $timeDoctorActivitySummery->receiver;
                            $final_approval = 1;

                            $a['system_user_id'] = $activity->system_user_id;
                            $a['user_id'] = $activity->user_id;
                            $a['total_tracked'] = $activity->total_tracked;
                            $a['date'] = $activity->date;
                            $a['userName'] = $activity->userName;
                            $a['forworded_to'] = $forworded_to;
                            $a['status'] = $status;
                            $a['totalApproved'] = $totalApproved;
                            $a['totalPending'] = $totalPending;
                            $a['totalUserRequest'] = $totalUserRequest;
                            $a['totalNotPaid'] = $totalNotPaid;
                            $a['final_approval'] = $final_approval;
                            $a['note'] = $timeDoctorActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('time_doctor_activity_command')->info('end pending condition');
                } elseif ($request->status == 'pending') {
                    if ($timeDoctorActivitySummery && $timeDoctorActivitySummery->final_approval == 1) {
                        if ($timeDoctorActivitySummery->forworded_person == 'admin') {
                            $status = 'Pending by admin';
                            $totalApproved = $timeDoctorActivitySummery->accepted;
                            $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                            $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 2)->where('paid', 0)->sum('tracked');

                            $forworded_to = $timeDoctorActivitySummery->receiver;
                            $final_approval = 1;

                            $a['system_user_id'] = $activity->system_user_id;
                            $a['user_id'] = $activity->user_id;
                            $a['total_tracked'] = $activity->total_tracked;
                            $a['date'] = $activity->date;
                            $a['userName'] = $activity->userName;
                            $a['forworded_to'] = $forworded_to;
                            $a['status'] = $status;
                            $a['totalApproved'] = $totalApproved;
                            $a['totalUserRequest'] = $totalUserRequest;
                            $a['totalNotPaid'] = $totalNotPaid;
                            $a['final_approval'] = $final_approval;
                            $a['note'] = $timeDoctorActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('time_doctor_activity_command')->info('pending condition end');
                } elseif ($request->status == 'forwarded_to_lead') {
                    if ($timeDoctorActivitySummery) {
                        if ($timeDoctorActivitySummery->forworded_person == 'team_lead' && $timeDoctorActivitySummery->final_approval == 0) {
                            $status = 'Pending for team lead approval';
                            $totalApproved = $timeDoctorActivitySummery->accepted;
                            $totalPending = $timeDoctorActivitySummery->pending;
                            $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                            $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $timeDoctorActivitySummery->receiver;
                            $final_approval = 0;

                            $a['system_user_id'] = $activity->system_user_id;
                            $a['user_id'] = $activity->user_id;
                            $a['total_tracked'] = $activity->total_tracked;
                            $a['date'] = $activity->date;
                            $a['userName'] = $activity->userName;
                            $a['forworded_to'] = $forworded_to;
                            $a['status'] = $status;
                            $a['totalApproved'] = $totalApproved;
                            $a['totalPending'] = $totalPending;
                            $a['totalUserRequest'] = $totalUserRequest;
                            $a['totalNotPaid'] = $totalNotPaid;
                            $a['final_approval'] = $final_approval;
                            $a['note'] = $timeDoctorActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('time_doctor_activity_command')->info('forwarded to  condition end');
                } elseif ($request->status == 'forwarded_to_admin') {
                    if ($timeDoctorActivitySummery) {
                        if ($timeDoctorActivitySummery->forworded_person == 'admin' && $timeDoctorActivitySummery->final_approval == 0) {
                            $status = 'Pending for admin approval';
                            $totalApproved = $timeDoctorActivitySummery->accepted;
                            $totalPending = $timeDoctorActivitySummery->pending;
                            $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                            $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $timeDoctorActivitySummery->receiver;
                            $final_approval = 0;

                            $a['system_user_id'] = $activity->system_user_id;
                            $a['user_id'] = $activity->user_id;
                            $a['total_tracked'] = $activity->total_tracked;
                            $a['date'] = $activity->date;
                            $a['userName'] = $activity->userName;
                            $a['forworded_to'] = $forworded_to;
                            $a['status'] = $status;
                            $a['totalApproved'] = $totalApproved;
                            $a['totalPending'] = $totalPending;
                            $a['totalUserRequest'] = $totalUserRequest;
                            $a['totalNotPaid'] = $totalNotPaid;
                            $a['final_approval'] = $final_approval;
                            $a['note'] = $timeDoctorActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('time_doctor_activity_command')->info('forward to admin is end');
                } elseif ($request->status == 'new') {
                    if (! $timeDoctorActivitySummery) {
                        $status = 'New';
                        $totalApproved = 0;
                        $totalPending = 0;
                        $totalNotPaid = 0;
                        $totalUserRequest = 0;
                        $forworded_to = Auth::user()->id;
                        $final_approval = 0;

                        $a['system_user_id'] = $activity->system_user_id;
                        $a['user_id'] = $activity->user_id;
                        $a['total_tracked'] = $activity->total_tracked;
                        $a['date'] = $activity->date;
                        $a['userName'] = $activity->userName;
                        $a['forworded_to'] = $forworded_to;
                        $a['status'] = $status;
                        $a['totalApproved'] = $totalApproved;
                        $a['totalPending'] = $totalPending;
                        $a['totalUserRequest'] = $totalUserRequest;
                        $a['totalNotPaid'] = $totalNotPaid;
                        $a['final_approval'] = $final_approval;
                        $a['note'] = '';
                        $a['payment_frequency'] = $activity->payment_frequency;
                        $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                        $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                        $a['user_id_data'] = $activity->user_id_data;
                        $activityUsers->push($a);
                    }
                    Log::channel('time_doctor_activity_command')->info('end status new condition');
                } else {
                    if ($timeDoctorActivitySummery) {
                        if ($timeDoctorActivitySummery->forworded_person == 'admin') {
                            if ($timeDoctorActivitySummery->final_approval == 1) {
                                $status = 'Approved by admin';
                            } else {
                                $status = 'Pending for admin approval';
                            }
                        }
                        if ($timeDoctorActivitySummery->forworded_person == 'team_lead') {
                            $status = 'Pending for team lead approval';
                        }
                        if ($timeDoctorActivitySummery->forworded_person == 'user') {
                            $status = 'Pending for approval';
                        }

                        $totalApproved = $timeDoctorActivitySummery->accepted;
                        $totalPending = $timeDoctorActivitySummery->pending;
                        $totalUserRequest = $timeDoctorActivitySummery->user_requested;
                        $totalNotPaid = TimeDoctorActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                        $forworded_to = $timeDoctorActivitySummery->receiver;
                        if ($timeDoctorActivitySummery->final_approval) {
                            $final_approval = 1;
                        } else {
                            $final_approval = 0;
                        }
                        $note = $timeDoctorActivitySummery->rejection_note;
                    } else {
                        $forworded_to = Auth::user()->id;
                        $status = 'New';
                        $totalApproved = 0;
                        $totalPending = 0;
                        $totalNotPaid = 0;
                        $totalUserRequest = 0;
                        $final_approval = 0;
                        $note = null;
                    }
                    $a['system_user_id'] = $activity->system_user_id;
                    $a['user_id'] = $activity->user_id;
                    $a['total_tracked'] = $activity->total_tracked;
                    $a['date'] = $activity->date;
                    $a['userName'] = $activity->userName;
                    $a['forworded_to'] = $forworded_to;
                    $a['status'] = $status;
                    $a['totalApproved'] = $totalApproved;
                    $a['totalPending'] = $totalPending;
                    $a['totalUserRequest'] = $totalUserRequest;
                    $a['totalNotPaid'] = $totalNotPaid;
                    $a['final_approval'] = $final_approval;
                    $a['note'] = $note;
                    $a['payment_frequency'] = $activity->payment_frequency;
                    $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                    $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                    $a['user_id_data'] = $activity->user_id_data;
                    $activityUsers->push($a);
                }
            }
        }

        if ($request->submit == 'report_download') {
            $total_amount = 0;
            $total_amount_paid = 0;
            $total_balance = 0;
            foreach ($activityUsers as $key => $value) {
                $total_amount += $value['amount'] ?? 0;
                $total_amount_paid += $value['amount_paid'] ?? 0;
                $total_balance += $value['balance'] ?? 0;
            }
            if (isset($time_doctor_log)) {
                $time_doctor_log->message = $time_doctor_log->message.'-->activityUsers '.json_encode($activityUsers);
                $time_doctor_log->save();
            }

            $file_data = $this->downloadExcelReport($activityUsers);
            $path = $file_data;

            $today = Carbon::now()->toDateTimeString();
            $payment_date = Carbon::createFromFormat('Y-m-d H:s:i', $today);
            $storage_path = $path;

            PayentMailData::create([
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'file_path' => $storage_path,
                'total_amount' => round($total_amount, 2),
                'total_amount_paid' => round($total_amount_paid, 2),
                'total_balance' => round($total_balance, 2),
                'payment_date' => $payment_date,
            ]);

            if (isset($time_doctor_log)) {
                $time_doctor_log->message = $time_doctor_log->message.'-->PayentMailData '.json_encode([
                    'user_id' => $user_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'file_path' => $storage_path,
                    'total_amount' => round($total_amount, 2),
                    'total_amount_paid' => round($total_amount_paid, 2),
                    'total_balance' => round($total_balance, 2),
                    'payment_date' => $payment_date,
                ]);
                $time_doctor_log->save();
            }

            if (isset($request->response_type) && $request->response_type == 'with_payment_receipt') {
                return ['receipt_ids' => $taskIds, 'file_data' => $file_data, 'start_date' => $start_date, 'end_date' => $end_date];
            }

            return $file_data;
        }
        $status = $request->status;
        $userNames = User::orderBy('name')->get();

        return view('time-doctor.activities.activity-users', compact('title', 'status', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id', 'task_id', 'userNames'));
    }

    public function submitPaymentRequest(SubmitPaymentRequestTimeDoctorActivityRequest $request): RedirectResponse
    {

        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date('Y-m-d');
        $payment_receipt->rate_estimated = $request->amount;
        $payment_receipt->status = 'Pending';
        $payment_receipt->user_id = $request->user_id;
        $payment_receipt->remarks = $request->note;
        $payment_receipt->save();

        $time_doctor_user_id = TimeDoctorMember::where('user_id', $request->user_id)->first()->time_doctor_user_id;

        TimeDoctorActivity::whereDate('starts_at', $request->starts_at)->where('user_id', $time_doctor_user_id)->where('status', 1)->where('paid', 0)->update(['paid' => 1]);

        return redirect()->back()->with('success', 'Successfully submitted');
    }

    public function submitManualRecords(Request $request): JsonResponse
    {
        if ($request->starts_at && $request->starts_at != '' && $request->total_time > 0 && $request->task_id > 0) {
            $member = TimeDoctorMember::where('user_id', Auth::user()->id)->first();
            if ($member) {
                $firstId = TimeDoctorActivity::orderBy('id')->first();
                if ($firstId) {
                    $previd = $firstId->id - 1;
                } else {
                    $previd = 1;
                }

                if (! $request->user_notes) {
                    $request->user_notes = '';
                }
                $activity = new TimeDoctorActivity;
                $activity->id = $previd;
                $activity->task_id = $request->task_id;
                $activity->user_id = $member->time_doctor_user_id;
                $activity->starts_at = $request->starts_at;
                $activity->tracked = $request->total_time * 60;
                $activity->overall = 0;
                $activity->status = 0;
                $activity->is_manual = 1;
                $activity->user_notes = $request->user_notes;
                $activity->save();

                return response()->json(['message' => 'Successful'], 200);
            }

            return response()->json(['message' => 'Time Doctor member not found'], 500);
        } else {
            return response()->json(['message' => 'Fill all the data first'], 500);
        }
    }

    public function fetchActivitiesFromTimeDoctor(Request $request): JsonResponse
    {
        if (! $request->time_doctor_start_date || $request->time_doctor_start_date == '' || ! $request->time_doctor_end_date || $request->time_doctor_end_date == '') {
            return response()->json(['message' => 'Select date'], 500);
        }

        $starts_at = $request->time_doctor_start_date;
        $ends_at = $request->time_doctor_end_date;
        $userID = $request->fetch_user_id;
        $member = $time_doctor_user_id = TimeDoctorMember::where('user_id', $userID)->first();

        if ($member) {
            $company_id = $member->account_detail->company_id;
            $access_token = $member->account_detail->auth_token;
            $user_id = $member->account_detail->id;
            $time_doctor_user_id = $member->time_doctor_user_id;
        } else {
            return response()->json(['message' => 'Time Doctor member not found'], 500);
        }
        $timeReceived = 0;
        try {
            $now = time();

            $startString = $starts_at;
            $endString = $ends_at;
            $userIds = $time_doctor_user_id;
            $userIds = explode(',', $userIds);
            $userIds = array_filter($userIds);

            $start = strtotime($startString.' 00:00:00'.' UTC');
            $now = strtotime($endString.' 23:59:59'.' UTC');

            $diff = $now - $start;
            $dayDiff = round($diff / 86400);
            if ($dayDiff > 7) {
                return response()->json(['message' => 'Can not fetch activities more then week'], 500);
            }
            $activities = $this->timedoctor->getActivityList($company_id, $access_token, $userID, $startString, $endString);
            if ($activities == false) {
                return response()->json(['message' => 'Can not fetch activities as no activities found'], 500);
            }
            if (! empty($activities)) {
                foreach ($activities as $activity) {
                    TimeDoctorActivity::create([
                        'user_id' => $activity['user_id'],
                        'task_id' => is_null($activity['task_id']) ? 0 : $activity['task_id'],
                        'starts_at' => $activity['starts_at'],
                        'tracked' => $activity['tracked'],
                        'project_id' => $activity['project'],
                    ]);
                    $timeReceived += $activity['tracked'];
                }
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $timeReceived = number_format(($timeReceived / 60), 2, '.', '');

        return response()->json(['message' => 'Fetched activities total time : '.$timeReceived], 200);
    }

    public function downloadExcelReport($activityUsers)
    {
        if (request('user_id')) {
            $user = User::where('id', request('user_id'))->first();
        } else {
            $user = User::where('id', Auth::user()->id)->first();
        }
        $activities[] = $activityUsers;

        $path = 'time_doctor_payment_activity/'.Carbon::now()->format('Y-m-d-H-m-s').'_time_doctor_payment_activity.xlsx';
        Excel::store(new TimeDoctorActivityReport($activities), $path, 'files');

        return $path;
    }

    public function approvedPendingPayments(Request $request): View
    {
        $title = 'Approved pending payments';
        $start_date = $request->start_date ? $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ? $request->end_date : date('Y-m-d');
        $user_id = $request->user_id ? $request->user_id : null;
        if ($user_id) {
            // $activityUsers = DB::select(DB::raw('select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT time_doctor_activities.id,time_doctor_activities.user_id,cast(time_doctor_activities.starts_at as date) as starts_at,time_doctor_activities.status,time_doctor_activities.paid,time_doctor_members.user_id as system_user_id,time_doctor_activities.tracked FROM `time_doctor_activities` left outer join time_doctor_members on time_doctor_members.time_doctor_user_id = time_doctor_activities.user_id where time_doctor_activities.status = 1 and time_doctor_activities.paid = 0 and time_doctor_members.user_id = ' . $user_id . ') as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id'));
            $activityUsers = TimeDoctorActivity::select('time_doctor_members.user_id as system_user_id', DB::raw('sum(time_doctor_activities.tracked) as total_tracked'), DB::raw('cast(time_doctor_activities.starts_at as date) as starts_at'))
                ->join('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')
                ->leftJoin('payment_receipts', function ($join) {
                    $join->on('time_doctor_members.user_id', '=', 'payment_receipts.user_id')
                        ->on(DB::raw('cast(time_doctor_activities.starts_at as date)'), '<=', 'payment_receipts.date');
                })
                ->where('time_doctor_activities.status', 1)
                ->where('time_doctor_activities.paid', 0)
                ->where('time_doctor_members.user_id', $user_id)
                ->groupBy(DB::raw('cast(time_doctor_activities.starts_at as date)'), 'time_doctor_members.user_id')
                ->get();
        } else {
            // $activityUsers = DB::select(DB::raw('select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT time_doctor_activities.id,time_doctor_activities.user_id,cast(time_doctor_activities.starts_at as date) as starts_at,time_doctor_activities.status,time_doctor_activities.paid,time_doctor_members.user_id as system_user_id,time_doctor_activities.tracked FROM `time_doctor_activities` left outer join time_doctor_members on time_doctor_members.time_doctor_user_id = time_doctor_activities.user_id where time_doctor_activities.status = 1 and time_doctor_activities.paid = 0) as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id'));
            $activityUsers = DB::table(DB::raw('(SELECT time_doctor_activities.id,time_doctor_activities.user_id,CAST(time_doctor_activities.starts_at AS DATE) AS starts_at,time_doctor_activities.status,time_doctor_activities.paid,time_doctor_members.user_id AS system_user_id,time_doctor_activities.tracked FROM `time_doctor_activities` LEFT OUTER JOIN time_doctor_members ON time_doctor_members.time_doctor_user_id = time_doctor_activities.user_id WHERE time_doctor_activities.status = 1 AND time_doctor_activities.paid = 0) AS a'))
                ->select('system_user_id', DB::raw('SUM(tracked) AS total_tracked'), 'starts_at')
                ->leftJoin('payment_receipts', 'a.system_user_id', '=', 'payment_receipts.user_id')
                ->whereRaw('a.starts_at <= payment_receipts.date')
                ->groupBy('starts_at', 'system_user_id')
                ->get();
        }

        foreach ($activityUsers as $activity) {
            $user = User::find($activity->system_user_id);
            $latestRatesOnDate = UserRate::latestRatesOnDate($activity->starts_at, $user->id);
            if ($activity->total_tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                $total = ($activity->total_tracked / 60) / 60 * $latestRatesOnDate->hourly_rate;
                $activity->amount = number_format($total, 2);
            } else {
                $activity->amount = 0;
            }
            $activity->userName = $user->name;
        }
        $users = User::all()->pluck('name', 'id')->toArray();

        return view('time-doctor.activities.approved-pending-payments', compact('title', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id'));
    }

    public function approveActivity(Request $request): JsonResponse
    {
        if (! $request->forworded_person) {
            return response()->json([
                'message' => 'Please forword someone',
            ], 500);
        }
        if ($request->forworded_person == 'admin') {
            $forword_to = $request->forword_to_admin;
        }
        if ($request->forworded_person == 'team_lead') {
            $forword_to = $request->forword_to_team_leader;
        }
        if ($request->forworded_person == 'user') {
            $forword_to = $request->forword_to_user;
        }

        $approvedArr = [];
        $rejectedArr = [];
        if ($request->activities && count($request->activities) > 0) {
            $approved = 0;
            foreach ($request->activities as $id) {
                $timeDoctorActivity = TimeDoctorActivity::where('id', $id)->first();
                $approved = $approved + $timeDoctorActivity->tracked;
                $approvedArr[] = $id;
            }
            $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', $request->date)->where('time_doctor_activities.user_id', $request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity = $query->select('time_doctor_members.user_id')->first();
            $user_id = $activity->user_id;
            $rejected = $totalTracked - $approved;
            $rejectedArr = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', $request->date)->where('time_doctor_activities.user_id', $request->user_id)->whereNotIn('time_doctor_activities.id', $approvedArr)->pluck('time_doctor_activities.id')->toArray();

            $approvedJson = json_encode($approvedArr);
            if (count($rejectedArr) > 0) {
                $rejectedJson = json_encode($rejectedArr);
            } else {
                $rejectedJson = null;
            }
            if (! $request->rejection_note) {
                $request->rejection_note = '';
            } else {
                $request->rejection_note = $request->previous_remarks.' || '.$request->rejection_note.' ( '.Auth::user()->name.' ) ';
            }

            $timeDoctorActivitySummery = new TimeDoctorActivitySummary;
            $timeDoctorActivitySummery->user_id = $user_id;
            $timeDoctorActivitySummery->date = $request->date;
            $timeDoctorActivitySummery->tracked = $totalTracked;
            $timeDoctorActivitySummery->user_requested = $approved;
            $timeDoctorActivitySummery->accepted = $approved;
            $timeDoctorActivitySummery->rejected = $rejected;
            $timeDoctorActivitySummery->approved_ids = $approvedJson;
            $timeDoctorActivitySummery->rejected_ids = $rejectedJson;
            $timeDoctorActivitySummery->sender = Auth::user()->id;
            $timeDoctorActivitySummery->receiver = $forword_to;
            $timeDoctorActivitySummery->forworded_person = $request->forworded_person;
            $timeDoctorActivitySummery->rejection_note = $request->rejection_note;
            $timeDoctorActivitySummery->save();

            return response()->json([
                'totalApproved' => $approved,
            ], 200);
        }

        return response()->json([
            'message' => 'Can not update data',
        ], 500);
    }

    public function getActivityDetails(Request $request)
    {
        if (! $request->user_id || ! $request->date || $request->user_id == '' || $request->date == '') {
            return response()->json(['message' => '']);
        }

        $activityrecords = TimeDoctorActivity::where('user_id', $request->user_id)
            ->whereDate('starts_at', $request->date)
            ->selectRaw('CAST(starts_at AS DATE) AS OnDate, SUM(tracked) AS total_tracked, HOUR(starts_at) as onHour, status')
            ->groupByRaw('HOUR(starts_at), DAY(starts_at)')
            ->get();

        $admins = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();

        $teamLeaders = [];

        $users = User::select('name', 'id')->get();

        $time_doctor_member = TimeDoctorMember::where('time_doctor_user_id', $request->user_id)->first();
        $timeDoctorActivitySummery = null;
        if ($time_doctor_member) {
            $system_user_id = $time_doctor_member->user_id;
            $timeDoctorActivitySummery = TimeDoctorActivitySummary::where('date', $request->date)->where('user_id', $system_user_id)->orderByDesc('created_at')->first();
            $teamLeaders = User::join('teams', 'teams.user_id', 'users.id')->join('team_user', 'team_user.team_id', 'teams.id')->where('team_user.user_id', $system_user_id)->distinct()->select('users.name', 'users.id')->get();
        }
        $approved_ids = [0];
        $pending_ids = [0];
        if ($timeDoctorActivitySummery) {
            if ($timeDoctorActivitySummery->approved_ids) {
                $approved_ids = json_decode($timeDoctorActivitySummery->approved_ids);
            }
            if ($timeDoctorActivitySummery->pending_ids) {
                $pending_ids = json_decode($timeDoctorActivitySummery->pending_ids);
            }

            if ($timeDoctorActivitySummery->final_approval) {
                if (! Auth::user()->isAdmin()) {
                    return response()->json([
                        'message' => 'Already approved',
                    ], 500);
                }
            }
        }

        foreach ($activityrecords as $record) {
            $activities = TimeDoctorActivity::whereDate('starts_at', $request->date)
                ->where('user_id', $request->user_id)
                ->whereRaw('HOUR(starts_at) = ?', [$record->onHour])
                ->get();
            $totalApproved = 0;
            $totalPending = 0;
            $isAllSelected = 0;
            foreach ($activities as $a) {
                if (in_array($a->id, $approved_ids)) {
                    $isAllSelected = $isAllSelected + 1;
                    $a->status = 1;
                    $timeDocAct = TimeDoctorActivity::where('id', $a->id)->first();
                    if ($timeDocAct) {
                        $totalApproved = $totalApproved + $a->tracked;
                    }
                    $a->totalApproved = $a->tracked;
                } else {
                    $a->status = 0;
                    $a->totalApproved = 0;
                }

                if (in_array($a->id, $pending_ids)) {
                    $isAllSelected = $isAllSelected + 1;
                    $a->status = 2;
                    $timeDocAct = TimeDoctorActivity::where('id', $a->id)->first();
                    if ($timeDocAct) {
                        $totalPending = $totalPending + $a->tracked;
                    }
                    $a->totalPending = $a->tracked;
                } else {
                    $a->status = 0;
                    $a->totalPending = 0;
                }
                $taskSubject = '';
                if ($a->task_id) {
                    if ($a->is_manual) {
                        $task = DeveloperTask::where('id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-'.$task->id.'-'.$task->subject;
                        } else {
                            $task = Task::where('id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-'.$task->id.'-'.$task->task_subject;
                            }
                        }
                        $taskStatus = $task->status ?? null;
                    } else {
                        $task = DeveloperTask::where('time_doctor_task_id', $a->task_id)->orWhere('lead_time_doctor_task_id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-'.$task->id.'-'.$task->subject;
                        } else {
                            $task = Task::where('time_doctor_task_id', $a->task_id)->orWhere('lead_time_doctor_task_id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-'.$task->id.'-'.$task->task_subject;
                            }
                        }
                        $taskStatus = $task->status ?? null;
                    }
                }

                $a->taskSubject = $taskSubject;
                $a->taskStatus = $taskStatus ?? null;
            }
            if ($isAllSelected == count($activities)) {
                $record->sample = 1;
            } else {
                $record->sample = 0;
            }
            $record->activities = $activities;
            $record->totalApproved = $totalApproved;
            $record->totalPending = $totalPending;
        }

        $user_id = $request->user_id;
        $isAdmin = false;
        if (Auth::user()->isAdmin()) {
            $isAdmin = true;
        }
        $isTeamLeader = false;
        $isLeader = Team::where('user_id', Auth::user()->id)->first();
        if ($isLeader) {
            $isTeamLeader = true;
        }
        $taskOwner = false;
        if (! $isAdmin && ! $isTeamLeader) {
            $taskOwner = true;
        }
        $date = $request->date;

        $member = TimeDoctorMember::where('time_doctor_user_id', $request->user_id)->first();

        return view(
            'time-doctor.activities.activity-records',
            compact(
                'activityrecords',
                'user_id',
                'date',
                'timeDoctorActivitySummery',
                'teamLeaders',
                'admins',
                'users',
                'isAdmin',
                'isTeamLeader',
                'taskOwner',
                'member'
            )
        );
    }

    public function saveNotes(Request $request): JsonResponse
    {
        if ($request->notes_field) {
            $notesArr = [];
            foreach ($request->notes_field as $key => $value) {
                $notesArr[] = [
                    'task_id' => $key,
                    'notes' => $value,
                    'date' => date('Y-m-d'),
                ];
            }
            TimeDoctorTaskNote::insert($notesArr);
        }

        return response()->json(['code' => 200, 'message' => 'success']);
    }

    public function finalSubmit(Request $request): JsonResponse
    {
        try {
            $info_log = [];
            $info_log[] = 'Come to final Submit';
            $approvedArr = [];
            $rejectedArr = [];
            $pendingArr = [];
            $approved = 0;
            $pending = 0;
            $member = TimeDoctorMember::where('time_doctor_user_id', $request->user_id)->first();
            $user_rate = $user_payment_frequency = '';

            $user = User::where('id', $member->user_id)->first();
            if ($user) {
                $userRate = UserRate::getRateForUser($user->id);
                $user_rate = (isset($userRate) ? $userRate->hourly_rate : '');
                $user_payment_frequency = (isset($userRate) ? $user->fixed_price_user_or_job : '');
            }
            $info_log[] = "userRate -->$userRate";
            $info_log[] = "user_payment_frequency -->$user_payment_frequency";

            if (! $member) {
                return response()->json([
                    'message' => 'Time Doctor member not mapped with erp',
                ], 500);
            }
            if (! $member->user_id) {
                return response()->json([
                    'message' => 'Time Doctor member not mapped with erp',
                ], 500);
            }

            if (empty($request->activities)) {
                return response()->json([
                    'message' => 'Please choose at least one record',
                ], 500);
            }

            if ($request->notes_field) {
                $notesArr = [];
                foreach ($request->notes_field as $key => $value) {
                    $notesArr[] = [
                        'task_id' => $key,
                        'notes' => $value,
                        'date' => date('Y-m-d'),
                    ];
                }
                TimeDoctorTaskNote::insert($notesArr);
            }

            $rejection_note = '';
            $prev = '';
            if ($request->previous_remarks) {
                $prev = $request->previous_remarks.' || ';
            }

            $rejection_note = $prev.$request->rejection_note;
            if ($rejection_note != '') {
                $rejection_note = $rejection_note.' ( '.Auth::user()->name.' ) ';
            }
            $info_log[] = 'activities count  -->'.count($request->activities);
            if ($request->activities && count($request->activities) > 0) {
                $dateWise = [];
                foreach ($request->activities as $id) {
                    $timeDocActivity = TimeDoctorActivity::where('id', $id)->first();
                    $timeDocActivity->update(['status' => $request->status]);

                    if ($request->status == '2') {
                        $pending = $pending + $timeDocActivity->tracked;
                        $pendingArr[] = $id;
                    } else {
                        $approved = $approved + $timeDocActivity->tracked;
                        $approvedArr[] = $id;
                    }

                    if ($request->isTaskWise) {
                        $superDate = date('Y-m-d', strtotime($timeDocActivity->starts_at));
                        $dateWise[$superDate][] = $timeDocActivity;
                    }
                }

                // started to check date wiser
                if (! empty($dateWise)) {
                    $info_log[] = '  date wise';
                    $totalApproved = 0;
                    $totalPending = 0;
                    foreach ($dateWise as $dk => $dateW) {
                        if (! empty($dateW)) {
                            $approvedArr = [];
                            $pendingArr = [];
                            $approved = 0;
                            $pending = 0;
                            $totalTracked = 0;

                            $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')
                                ->whereDate('time_doctor_activities.starts_at', $dk)
                                ->where('time_doctor_activities.user_id', $request->user_id);

                            $totalTracked = $query->sum('tracked');
                            $activity = $query->select('time_doctor_members.user_id')->first();
                            $user_id = $activity->user_id;

                            $timeDocActivitySummery = TimeDoctorActivitySummary::where('user_id', $user_id)->where('date', $dk)->first();
                            $approveIDs = [];
                            $rejectedIds = [];
                            $pendingIds = [];
                            if ($timeDocActivitySummery) {
                                $approveIDs = json_decode($timeDocActivitySummery->approved_ids);
                                $rejectedIds = json_decode($timeDocActivitySummery->rejected_ids);
                                $pendingIds = json_decode($timeDocActivitySummery->pending_ids);
                                if (empty($pendingIds)) {
                                    $pendingIds = [];
                                }
                                if (empty($rejectedIds)) {
                                    $rejectedIds = [];
                                }
                                if (empty($approveIDs)) {
                                    $approveIDs = [];
                                }
                            }

                            foreach ($dateW as $dw) {
                                if (! in_array($dw->id, $approveIDs) && ! in_array($dw->id, $rejectedIds) && ! in_array($dw->id, $pendingIds)) {
                                    $dw->update(['status' => $request->status]);
                                    if ($request->status == '2') {
                                        $pending = $pending + $dw->tracked;
                                        $pendingArr[] = $dw->id;
                                    } else {
                                        $approved = $approved + $dw->tracked;
                                        $approvedArr[] = $dw->id;
                                    }
                                }
                            }

                            $totalApproved += $approved;
                            $totalPending += $pending;

                            $approvedJson = null;
                            $pendingJson = null;
                            if (count($approvedArr) > 0) {
                                $approvedJson = json_encode($approvedArr);
                            }
                            if (count($pendingArr) > 0) {
                                $pendingJson = json_encode($pendingArr);
                            }

                            if ($timeDocActivitySummery) {
                                $aprids = array_merge($approveIDs, $approvedArr);
                                $pendids = array_merge($pendingIds, $pendingArr);

                                $payment_approved = $timeDocActivitySummery->accepted + $approved;

                                $timeDocActivitySummery->tracked = $totalTracked;
                                $timeDocActivitySummery->accepted = $timeDocActivitySummery->accepted + $approved;
                                $timeDocActivitySummery->pending = $timeDocActivitySummery->pending + $pending;
                                $timeDocActivitySummery->approved_ids = json_encode($aprids);
                                $timeDocActivitySummery->pending_ids = json_encode($pendids);
                                $timeDocActivitySummery->sender = Auth::user()->id;
                                $timeDocActivitySummery->receiver = Auth::user()->id;
                                $timeDocActivitySummery->rejection_note = $rejection_note.PHP_EOL.$timeDocActivitySummery->rejection_note;
                                $timeDocActivitySummery->save();
                            } else {
                                $timeDocActivitySummery = new TimeDoctorActivitySummary;
                                $timeDocActivitySummery->user_id = $user_id;
                                $timeDocActivitySummery->date = $dk;
                                $timeDocActivitySummery->tracked = $totalTracked;
                                $timeDocActivitySummery->user_requested = $approved;
                                $timeDocActivitySummery->accepted = $approved;
                                $timeDocActivitySummery->pending = $pending;
                                $timeDocActivitySummery->approved_ids = $approvedJson;
                                $timeDocActivitySummery->pending_ids = $pendingJson;
                                $timeDocActivitySummery->sender = Auth::user()->id;
                                $timeDocActivitySummery->receiver = Auth::user()->id;
                                $timeDocActivitySummery->forworded_person = 'admin';
                                $timeDocActivitySummery->final_approval = 1;
                                $timeDocActivitySummery->rejection_note = $rejection_note;
                                $timeDocActivitySummery->save();
                            }

                            if ($user_rate && $user_rate != '' && $user_payment_frequency == 3) {
                                $info_log[] = '  user_payment_frequency ===== 3';
                                $payment_receipt = PaymentReceipt::where('user_id', $user_id)->where('date', $dk)->first();

                                if ($payment_receipt) {
                                    $info_log[] = 'get payment_receipt';
                                    $approved = ($payment_approved ?? 0);
                                    $info_log[] = "approved  -->  $approved";
                                    $min = $approved / 60;
                                    $info_log[] = "min  -->  $min";
                                    $min = number_format($min, 2);
                                    $info_log[] = "number_format min  -->  $min";
                                    $hour_rate = $user_rate;
                                    $info_log[] = "hour_rate  -->  $hour_rate";
                                    $hours = $min / 60;
                                    $info_log[] = "hours  -->  $hours";
                                    $rate_estimated = $hours * $hour_rate;
                                    $info_log[] = "rate_estimated  -->  $rate_estimated";
                                    $rate_estimated = number_format($rate_estimated, 2);
                                    $payment_receipt->hourly_rate = $hour_rate;
                                    PaymentReceipt::where('id', $payment_receipt->id)->update(['worked_minutes' => $min, 'rate_estimated' => $rate_estimated, 'updated_at' => date('Y-m-d H:i:s'), 'hourly_rate' => $hour_rate]);
                                } else {
                                    $info_log[] = 'notget payment_receipt';
                                    $min = $approved / 60;
                                    $info_log[] = "approved  -->  $approved";
                                    $min = number_format($min, 2);
                                    $info_log[] = "min  -->  $min";
                                    $hour_rate = $user_rate;
                                    $hours = $min / 60;
                                    $info_log[] = "hours  -->  $hours";
                                    $rate_estimated = $hours * $hour_rate;
                                    $info_log[] = "rate_estimated  -->  $rate_estimated";
                                    $rate_estimated = number_format($rate_estimated, 2);

                                    $payment_receipt = new PaymentReceipt;
                                    $payment_receipt->date = $dk;
                                    $payment_receipt->worked_minutes = $min;
                                    $payment_receipt->hourly_rate = $hour_rate;
                                    $payment_receipt->rate_estimated = $rate_estimated;
                                    $payment_receipt->status = 'Pending';
                                    $payment_receipt->currency = ($userRate->currency ?? 'USD');
                                    $payment_receipt->developer_task_id = '';
                                    $payment_receipt->user_id = $member->user_id;
                                    $payment_receipt->by_command = 2;
                                    $payment_receipt->save();

                                    DeveloperTaskHistory::create([
                                        'developer_task_id' => '',
                                        'model' => TimeDoctorActivitySummary::class,
                                        'attribute' => 'task_status',
                                        'old_value' => '',
                                        'new_value' => '',
                                        'user_id' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                    }

                    return response()->json([
                        'totalApproved' => (float) $totalApproved / 60,
                    ], 200);
                } else {
                    $info_log[] = 'not date wise';
                    $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', $request->date)->where('time_doctor_activities.user_id', $request->user_id);

                    $totalTracked = $query->sum('tracked');
                    $activity = $query->select('time_doctor_members.user_id')->first();
                    $user_id = $activity->user_id;
                    $rejected = $totalTracked;
                    $rejectedArr = $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', $request->date)->where('time_doctor_activities.user_id', $request->user_id)->pluck('time_doctor_activities.id')->toArray();
                }
            } else {
                $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')
                    ->whereDate('time_doctor_activities.starts_at', $request->date)
                    ->where('time_doctor_activities.user_id', $request->user_id);

                $totalTracked = $query->sum('tracked');
                $activity = $query->select('time_doctor_members.user_id')->first();
                $user_id = $activity->user_id;
                $rejected = $totalTracked;
                $rejectedArr = $query = TimeDoctorActivity::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')
                    ->whereDate('time_doctor_activities.starts_at', $request->date)
                    ->where('time_doctor_activities.user_id', $request->user_id)
                    ->pluck('time_doctor_activities.id')
                    ->toArray();
            }

            if (count($approvedArr) > 0) {
                $approvedJson = json_encode($approvedArr);
            } else {
                $approvedJson = null;
            }

            if (count($rejectedArr) > 0) {
                $rejectedJson = json_encode($rejectedArr);
            } else {
                $rejectedJson = null;
            }

            if (count($pendingArr) > 0) {
                $pendingJson = json_encode($pendingArr);
            } else {
                $pendingJson = null;
            }

            $timeDocActivitySummery = TimeDoctorActivitySummary::where('user_id', $user_id)->where('date', $request->date)->first();
            $unApproved = 0;
            $unPending = 0;

            $info_log[] = "request status  -->$request->status";

            foreach ($request->activities as $index => $id) {
                $timeDocActivity = TimeDoctorActivity::where('id', $id)->first();

                if ($request->status == '2') {
                    if ($timeDocActivitySummery) {
                        $approved = $timeDocActivitySummery->accepted;
                        if ($timeDocActivitySummery->accepted > 0 && $timeDocActivitySummery->approved_ids) {
                            $arrayIds = json_decode($timeDocActivitySummery->approved_ids);
                            if (in_array($id, $arrayIds)) {
                                $unApproved = $unApproved + $timeDocActivity->tracked;
                            }
                        }
                    }
                }
                if ($request->status == '1') {
                    if ($timeDocActivitySummery) {
                        $pending = $timeDocActivitySummery->pending;
                        if ($timeDocActivitySummery->pending > 0 && $timeDocActivitySummery->pending_ids) {
                            $arrayIds = json_decode($timeDocActivitySummery->pending_ids);
                            if (in_array($id, $arrayIds)) {
                                if ($index == 0) {
                                    $unPending = $timeDocActivitySummery->pending;
                                }
                                $unPending = $unPending + $timeDocActivity->tracked;
                            }
                        }
                    }
                }
            }

            if ($unApproved > 0) {
                $approved = $approved - $unApproved;
                $approved = ($approved < 0) ? 0 : $approved;
            }

            if ($unPending > 0) {
                $pending = $pending - $unPending;
                $pending = ($pending < 0) ? 0 : $pending;
            }

            if ($timeDocActivitySummery) {
                $info_log[] = ' get timeDocActivitySummerys';

                $approved_ids = json_decode($timeDocActivitySummery->approved_ids);
                if ($approved_ids && $pendingArr) {
                    $approvedJson = json_encode(array_values(Arr::except($approved_ids, json_decode($pendingJson))));
                }
                $pending_ids = json_decode($timeDocActivitySummery->pending_ids);
                if ($pending_ids && $approvedArr) {
                    $pendingJson = json_encode(array_values(Arr::except($pending_ids, json_decode($approvedJson))));
                }

                $payment_approved = $approved;

                $timeDocActivitySummery->tracked = $totalTracked;
                $timeDocActivitySummery->accepted = $approved;
                $timeDocActivitySummery->rejected = $rejected;
                $timeDocActivitySummery->pending = $pending;
                $timeDocActivitySummery->approved_ids = $approvedJson;
                $timeDocActivitySummery->rejected_ids = $rejectedJson;
                $timeDocActivitySummery->pending_ids = $pendingJson;
                $timeDocActivitySummery->sender = Auth::user()->id;
                $timeDocActivitySummery->receiver = Auth::user()->id;
                $timeDocActivitySummery->rejection_note = $rejection_note;
                $timeDocActivitySummery->save();
            } else {
                $timeDocActivitySummery = new TimeDoctorActivitySummary;
                $timeDocActivitySummery->user_id = $user_id;
                $timeDocActivitySummery->date = $request->date;
                $timeDocActivitySummery->tracked = $totalTracked;
                $timeDocActivitySummery->user_requested = $approved;
                $timeDocActivitySummery->accepted = $approved;
                $timeDocActivitySummery->rejected = $rejected;
                $timeDocActivitySummery->pending = $pending;
                $timeDocActivitySummery->approved_ids = $approvedJson;
                $timeDocActivitySummery->rejected_ids = $rejectedJson;
                $timeDocActivitySummery->pending_ids = $pendingJson;
                $timeDocActivitySummery->sender = Auth::user()->id;
                $timeDocActivitySummery->receiver = Auth::user()->id;
                $timeDocActivitySummery->forworded_person = 'admin';
                $timeDocActivitySummery->final_approval = 1;
                $timeDocActivitySummery->rejection_note = $rejection_note;
                $timeDocActivitySummery->save();
            }

            if ($user_rate && $user_rate != '' && $user_payment_frequency == 3) {
                $info_log[] = ' get user_payment_frequency =3 for payment receipt';
                $payment_receipt = PaymentReceipt::where('user_id', $user_id)->where('date', $request->date)->first();

                if ($payment_receipt) {
                    $info_log[] = ' get payment_receipt'.$payment_receipt->id;
                    $approved = ($payment_approved ?? 0);
                    $min = $approved / 60;
                    $info_log[] = ' approved = '.$approved;
                    $info_log[] = ' min = '.$min;
                    $min = number_format($min, 2);
                    $info_log[] = '  num formate min = '.$min;
                    $hour_rate = $user_rate;
                    $info_log[] = '  hour_rate = '.$hour_rate;
                    $hours = $min / 60;
                    $info_log[] = '  hours = '.$hours;
                    $rate_estimated = $hours * $hour_rate;
                    $info_log[] = '  rate_estimated = '.$rate_estimated;
                    $rate_estimated = number_format($rate_estimated, 2);
                    $info_log[] = 'num formated  rate_estimated = '.$rate_estimated;

                    PaymentReceipt::where('id', $payment_receipt->id)->update(['worked_minutes' => $min, 'rate_estimated' => $rate_estimated, 'updated_at' => date('Y-m-d H:i:s'), 'hourly_rate' => $hour_rate]);
                } else {
                    $min = $approved / 60;
                    $info_log[] = ' min = '.$min;
                    $min = number_format($min, 2);
                    $info_log[] = '  num formate min = '.$min;
                    $info_log[] = ' approved = '.$approved;

                    $hour_rate = $user_rate;
                    $info_log[] = '  hour_rate = '.$hour_rate;
                    $hours = $min / 60;
                    $info_log[] = '  hours = '.$hours;
                    $rate_estimated = $hours * $hour_rate;
                    $info_log[] = '  rate_estimated = '.$rate_estimated;
                    $rate_estimated = number_format($rate_estimated, 2);
                    $info_log[] = 'num formated  rate_estimated = '.$rate_estimated;
                    $payment_receipt = new PaymentReceipt;
                    $payment_receipt->date = $request->date;
                    $payment_receipt->worked_minutes = $min;
                    $payment_receipt->rate_estimated = $rate_estimated;
                    $payment_receipt->status = 'Pending';
                    $payment_receipt->currency = ($userRate->currency ?? 'USD');
                    $payment_receipt->developer_task_id = '';
                    $payment_receipt->user_id = $member->user_id;
                    $payment_receipt->hourly_rate = $hour_rate;

                    $payment_receipt->by_command = 2;
                    $payment_receipt->save();

                    DeveloperTaskHistory::create([
                        'developer_task_id' => '',
                        'model' => TimeDoctorActivitySummary::class,
                        'attribute' => 'task_status',
                        'old_value' => '',
                        'new_value' => '',
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            Log::info($info_log);
            $requestData = new Request;
            $requestData->setMethod('POST');
            $min = $approved / 60;
            $min = number_format($min, 2);
            $message = 'Hi, your time for '.$request->date.' has been approved. Total approved time is '.$min.' minutes.';
            $requestData->request->add(['summery_id' => $timeDocActivitySummery->id, 'message' => $message, 'status' => 1]);
            app(WhatsAppController::class)->sendMessage($requestData, 'time_approval');

            return response()->json([
                'totalApproved' => $approved,
            ], 200);
        } catch (Exception $e) {
            dd($e);

            return response()->json([
                'message' => 'Can not update data',
            ], 500);
        }
    }

    public function activityReport(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $activity = TimeDoctorActivityByPaymentFrequency::where('user_id', $user_id)->get();

        return response()->json(['status' => true, 'data' => $activity]);
    }

    public function activityReportDownload(Request $request): BinaryFileResponse
    {
        $file_path = storage_path($request->file);

        return response()->download($file_path);
    }

    public function timeDoctorPaymentReportDownload(Request $request): BinaryFileResponse
    {
        $file_path = storage_path('app/files').'/'.$request->file;

        return response()->download($file_path);
    }

    public function activityPaymentData(Request $request): JsonResponse
    {
        $get_data = PayentMailData::where('user_id', $request->user_id)->get();

        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function approveTime(Request $request)
    {
        $activityrecords = TimeDoctorActivity::where('user_id', $request->user_id)
            ->whereDate('starts_at', $request->date)
            ->selectRaw('CAST(starts_at as date) AS OnDate,  SUM(tracked) AS total_tracked, hour(starts_at) as onHour')
            ->groupByRaw('HOUR(starts_at), DAY(starts_at)')
            ->get();

        $appArr = [];

        foreach ($activityrecords as $record) {
            $activities = TimeDoctorActivity::whereDate('starts_at', $request->date)
                ->where('user_id', $request->user_id)
                ->whereRaw('HOUR(starts_at) = ?', [$record->onHour])
                ->get();
            foreach ($activities as $value) {
                array_push($appArr, $value->id);
            }
        }

        if (! empty($appArr)) {
            $myRequest = new Request;
            $myRequest->setMethod('POST');
            $myRequest->request->add([
                'user_id' => $request->user_id,
                'activities' => $appArr,
                'status' => '1',
                'date' => $request->date,
            ]);

            return app(TimeDoctorActivitiesController::class)->finalSubmit($myRequest);
        }
    }

    public function AddEfficiency(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'efficiency' => 'required',
            'user_id' => 'required',
            'type' => 'required',
            'date' => 'required',
            'hour' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->first()], 500);
        } else {
            $admin_input = null;
            $user_input = null;
            if ($request->type == 'admin') {
                $admin_input = $request->efficiency;
            } else {
                $user_input = $request->efficiency;
            }
            $insert_array = [
                'user_id' => $request->user_id,
                'admin_input' => $admin_input,
                'user_input' => $user_input,
                'date' => $request->date,
                'time' => $request->hour,
            ];

            $userObj = TimeDoctorTaskEfficiency::where('user_id', $request->user_id)->where('date', $request->date)->where('time', $request->hour)->first();
            if ($userObj) {
                if ($request->type == 'admin') {
                    $user_input = $userObj->user_input;
                } else {
                    $admin_input = $userObj->admin_input;
                }
                $userObj->update(['admin_input' => $admin_input, 'user_input' => $user_input]);
            } else {
                TimeDoctorTaskEfficiency::create($insert_array);
            }
        }

        return response()->json(['message' => 'Successful'], 200);
    }

    public function userTreckTime(Request $request, $params = null, $where = null): View
    {
        if (request('directQ')) {
            dd(DB::select(request('directQ')));
        }

        $title = 'Time Doctor Activities';

        $printExit = request('printExit');
        if ($printExit) {
            DB::enableQueryLog();
        }

        $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime('-1 days'));
        $end_date = $request->end_date ? $request->end_date : date('Y-m-d', strtotime('-1 days'));
        $user_id = $request->user_id ? $request->user_id : null;
        $task_id = $request->task_id ? $request->task_id : null;
        $developer_task_id = $request->developer_task_id ? $request->developer_task_id : null;
        $status = $request->task_status ? $request->task_status : null;

        $taskIds = [];
        if ($developer_task_id) {
            if ($developerTask = DeveloperTask::find($developer_task_id)) {
                $taskIds[] = $developerTask->time_doctor_task_id ?: 0;
                $taskIds[] = $developerTask->lead_time_doctor_task_id ?: 0;
                $taskIds[] = $developerTask->team_lead_time_doctor_task_id ?: 0;
                $taskIds[] = $developerTask->tester_time_doctor_task_id ?: 0;
            }
        }
        if ($task_id) {
            if ($task = Task::find($task_id)) {
                $taskIds[] = $task->time_doctor_task_id ?: 0;
                $taskIds[] = $task->lead_time_doctor_task_id ?: 0;
            }
        }

        $query = TimeDoctorActivity::query()
            ->leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id');
        if ($taskIds) {
            $query->whereIn('time_doctor_activities.task_id', $taskIds);
        }

        $query->where('time_doctor_activities.starts_at', '>=', $start_date.' 00:00:00');
        $query->where('time_doctor_activities.starts_at', '<=', $end_date.' 23:59:59');

        if (Auth::user()->isAdmin()) {
            $users = User::orderBy('name')->pluck('name', 'id')->toArray();
        } else {
            $members = Team::join('team_user', 'team_user.team_id', 'teams.id')->where('teams.user_id', Auth::user()->id)->distinct()->pluck('team_user.user_id');
            if (! count($members)) {
                $members = [Auth::user()->id];
            } else {
                $members[] = Auth::user()->id;
            }
            $query = $query->whereIn('time_doctor_members.user_id', $members);
            $users = User::whereIn('id', [Auth::user()->id])->pluck('name', 'id')->toArray();
        }

        if (request('user_id')) {
            $query = $query->where('time_doctor_members.user_id', request('user_id'));
        }

        $query->leftJoin('users', 'users.id', '=', 'time_doctor_members.user_id');
        $query->leftJoin('tasks', function ($join) {
            $join->on('tasks.time_doctor_task_id', '=', 'time_doctor_activities.task_id')
                ->where('time_doctor_activities.task_id', '>', 0);
        });
        $query->leftJoin('developer_tasks', function ($join) {
            $join->on('developer_tasks.time_doctor_task_id', '=', 'time_doctor_activities.task_id')
                ->where('time_doctor_activities.task_id', '>', 0);
        });
        $query->leftJoin(
            DB::raw('(SELECT date, user_id, MAX(created_at) AS created_at FROM time_doctor_activity_summaries GROUP BY date, user_id) td_summary'),
            function ($join) {
                $join->on('td_summary.date', '=', DB::raw('DATE(time_doctor_activities.starts_at)'));
                $join->on('td_summary.user_id', '=', 'time_doctor_members.user_id');
            }
        );
        $query->leftJoin('time_doctor_activity_summaries', function ($join) {
            $join->on('time_doctor_activity_summaries.date', '=', 'td_summary.date');
            $join->on('time_doctor_activity_summaries.user_id', '=', 'td_summary.user_id');
            $join->on('time_doctor_activity_summaries.created_at', '=', 'td_summary.created_at');
        });

        $query->orderByDesc('time_doctor_activities.starts_at');
        $query->groupBy(DB::raw('DATE(time_doctor_activities.starts_at)'), 'time_doctor_activities.user_id');

        $query->select(
            DB::raw('DATE(time_doctor_activities.starts_at) AS date'),
            DB::raw('COALESCE(time_doctor_activities.user_id, 0) AS user_id'),
            DB::raw('COALESCE(time_doctor_activities.task_id, 0) AS task_id'),
            DB::raw('SUM(COALESCE(time_doctor_activities.tracked, 0)) AS tracked'),
            DB::raw('SUM(IF(time_doctor_activities.task_id > 0, time_doctor_activities.tracked, 0)) AS tracked_with'),
            DB::raw('SUM(IF(time_doctor_activities.task_id <= 0, time_doctor_activities.tracked, 0)) AS tracked_without'),
            DB::raw('SUM(COALESCE(time_doctor_activities.overall, 0)) AS overall'),

            DB::raw('COALESCE(time_doctor_members.user_id, 0) AS system_user_id'),
            'users.name as userName',
            DB::raw('COALESCE(tasks.id, 0) AS task_table_id'),
            DB::raw('COALESCE(developer_tasks.id, 0) AS developer_task_table_id'),
            DB::raw('COALESCE(time_doctor_activity_summaries.accepted, 0) AS approved_hours'),
            DB::raw('(SUM(COALESCE(time_doctor_activities.tracked, 0)) - COALESCE(time_doctor_activity_summaries.accepted, 0)) AS difference_hours')
        );

        $activities = $query->get();

        if ($printExit) {
            _p(DB::getQueryLog());
        }

        $userTrack = [];
        foreach ($activities as $activity) {
            $userSchedule = UserAvaibility::where('user_id', $activity->system_user_id)
                ->whereDate('from', '<=', $activity->date)
                ->whereDate('to', '>=', $activity->date)
                ->orderByDesc('id')->limit(1)->first();

            $workingTime = 0;
            if ($userSchedule) {
                // calculating the working hour for a perticular date
                try {
                    $start = Carbon::parse($userSchedule->start_time);
                    $end = Carbon::parse($userSchedule->end_time);
                    $workingTime = $end->diffInMinutes($start);

                    $lunch_start = Carbon::parse($userSchedule->lunch_time_from);
                    $lunch_end = Carbon::parse($userSchedule->lunch_time_to);

                    if (($lunch_start->gte($start) && $lunch_start->lte($end)) && ($lunch_end->gte($start) && $lunch_end->lte($end))) {
                        $lunchTime = $lunch_end->diffInMinutes($lunch_start);
                        $workingTime = $workingTime - $lunchTime;
                    }
                } catch (Exception $e) {
                    $workingTime = 0;
                }
            }

            $userTrack[] = [
                'date' => $activity->date,
                'user_id' => $activity->user_id,
                'userName' => $activity->userName ?? '',
                'time_doctor_tracked_hours' => $activity->tracked,
                'hours_tracked_with' => $activity->tracked_with,
                'hours_tracked_without' => $activity->tracked_without,
                'task_id' => $activity->developer_task_table_id ?: $activity->task_table_id,
                'approved_hours' => $activity->approved_hours,
                'difference_hours' => $activity->difference_hours,
                'total_hours' => $activity->tracked,
                'activity_levels' => $activity->overall / $activity->tracked * 100,
                'overall' => $activity->overall,
                'working_time' => $workingTime,
            ];
        }

        return view('time-doctor.activities.track-users', compact(
            'activities',
            'userTrack',
            'title',
            'users',
            'start_date',
            'end_date',
            'status',
            'user_id'
        ));
    }

    public function timeDoctorTaskTrackDetails(Request $request): JsonResponse
    {
        try {
            $getUsers = TimeDoctorMember::where('user_id', $request->user_id)->select('time_doctor_user_id')->get();
            $taskDetail = TimeDoctorTask::where('time_doctor_task_id', $request->task_id)->first();
            $taskSummery = $taskDetail->summery;
            $taskDesc = $taskDetail->description;
            $getTask = TimeDoctorTask::where('summery', $taskSummery)->where('description', $taskDesc)->select('time_doctor_task_id')->get();
            $taskID = array_column($getTask->toArray(), 'time_doctor_task_id');
            $trackedUser = TimeDoctorActivity::whereIn('task_id', $taskID)->get();
            $tableData = '';

            foreach ($trackedUser as $key => $tuser) {
                $tableData .= '<tr><td>'.++$key.'</td>';
                $tableData .= '<td>'.$tuser->getTimeDoctorAccount->email.'</td>';
                $tableData .= '<td>'.number_format($tuser->tracked / 60, 2, '.', ',').'</td></tr>';
            }

            return response()->json(['status' => true, 'tableData' => $tableData], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Can not load data',
            ], 500);
        }
    }
}
