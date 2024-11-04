<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\DeveloperTask;
use App\DeveloperTaskHistory;
use App\Email;
use App\Exports\HubstaffActivityReport;
use App\Exports\HubstaffNotificationReport;
use App\Helpers\HubstaffTrait;
use App\Http\Requests\SubmitPaymentRequestHubstaffActivityRequest;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffActivityNotification;
use App\Hubstaff\HubstaffActivitySummary;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffTaskNotes;
use App\HubstaffActivityByPaymentFrequency;
use App\HubstaffTaskEfficiency;
use App\Jobs\SendEmail;
use App\Loggers\HubstuffCommandLogMessage;
use App\Mails\Manual\DocumentEmail;
use App\PayentMailData;
use App\Payment;
use App\PaymentReceipt;
use App\Task;
use App\Team;
use App\User;
use App\UserRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HubstaffActivitiesController extends Controller
{
    use HubstaffTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'Hubstaff Activities';

        return view('hubstaff.activities.index', compact('title'));
    }

    public function notification(): View
    {
        $title = 'Hubstaff Notification';

        $users = User::orderBy('name')->get();

        return view('hubstaff.activities.notification.index', compact('title', 'users'));
    }

    public function notificationRecords(Request $request): JsonResponse
    {
        $records = HubstaffActivityNotification::join('users as u', 'hubstaff_activity_notifications.user_id', 'u.id');

        $records->leftJoin('user_avaibilities as av', 'hubstaff_activity_notifications.user_id', 'av.user_id');
        $records->where('av.is_latest', 1);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('u.name', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($request->user_id)) {
            $records = $records->where('hubstaff_activity_notifications.user_id', $request->user_id);
        }

        if ($request->start_date != null) {
            $records = $records->whereDate('start_date', '>=', $request->start_date.' 00:00:00');
        }

        if ($request->end_date != null) {
            $records = $records->whereDate('start_date', '<=', $request->end_date.' 23:59:59');
        }

        $records = $records->select([
            'hubstaff_activity_notifications.*',
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

            if ($difference > 0) {
                $sign = '-';
            } elseif ($difference < 0) {
                $sign = '+';
            } else {
                $sign = '';
            }
            $admin = null;
            if (\Auth::user()->hasRole('Admin')) {
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
        $records = HubstaffActivityNotification::join('users as u', 'hubstaff_activity_notifications.user_id', 'u.id');

        $records->leftJoin('user_avaibilities as av', 'hubstaff_activity_notifications.user_id', 'av.user_id');
        $records->where('av.is_latest', 1);

        $keyword = request('keyword');
        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('u.name', 'LIKE', "%$keyword%");
            });
        }

        if (! empty($request->user_id)) {
            $records = $records->where('hubstaff_activity_notifications.user_id', $request->user_id);
        }

        if ($request->start_date != null) {
            $records = $records->whereDate('start_date', '>=', $request->start_date.' 00:00:00');
        }

        if ($request->end_date != null) {
            $records = $records->whereDate('start_date', '<=', $request->end_date.' 23:59:59');
        }

        $records = $records->select([
            'hubstaff_activity_notifications.*',
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

        return Excel::download(new HubstaffNotificationReport($recordsArr), $filename);
    }

    public function notificationReasonSave(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $hnotification = HubstaffActivityNotification::find($request->id);
            if ($hnotification != null) {
                $hnotification->reason = $request->reason;
                $hnotification->save();

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
            $hnotification = HubstaffActivityNotification::find($request->id);
            if ($hnotification != null) {
                $hnotification->status = $request->status;
                $hnotification->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'changed succesfully']);
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Requested id is not in database']);
    }

    public function HubstaffActivityCommandExecution(Request $request): JsonResponse
    {
        $start_date = $request->startDate ? $request->startDate : date('Y-m-d', strtotime('-1 days'));
        $end_date = $request->endDate ? $request->endDate : date('Y-m-d', strtotime('-1 days'));
        $userid = $request->user_id;

        $users = User::where('id', $userid)->get();

        foreach ($users as $user) {
            $user_id = $user->id;

            $data['email'] = $user->email;
            $data['title'] = 'Hubstuff Activities Report';

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

            $emailClass = (new DocumentEmail('Hubstuff Activities Report', 'Hubstaff Payment Activity', $file_paths))->build();

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

        if ($where == 'HubstuffActivityCommand') {
            if (isset($params['HubstuffCommandLogMessage_id'])) {
                $hubstufflog = HubstuffCommandLogMessage::find($params['HubstuffCommandLogMessage_id']);
            }

            $title = 'Hubstaff Activities';
            $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime('-1 days'));
            $end_date = $request->end_date ? $request->end_date : date('Y-m-d', strtotime('-1 days'));
            $user_id = $request->user_id ? $request->user_id : null;

            $tasks = PaymentReceipt::with('chat_messages', 'user')->where('user_id', $user_id)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();

            $taskIds = PaymentReceipt::with('chat_messages', 'user')->where('user_id', $user_id)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->pluck('id');
            if ($hubstufflog) {
                $hubstufflog->message = $hubstufflog->message.'-->get payment receipt_in  date '.json_encode($taskIds);
                $hubstufflog->save();
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
            $title = 'Hubstaff Activities';
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
                    if (! empty($developer_tasks->hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->hubstaff_task_id;
                    }
                    if (! empty($developer_tasks->lead_hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->lead_hubstaff_task_id;
                    }
                    if (! empty($developer_tasks->team_lead_hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->team_lead_hubstaff_task_id;
                    }
                    if (! empty($developer_tasks->tester_hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->tester_hubstaff_task_id;
                    }
                }
            }

            if (! empty($task_status)) {
                $developer_tasks = DeveloperTask::where('status', $task_status)->where('hubstaff_task_id', '!=', 0)->pluck('hubstaff_task_id');
                if (! empty($developer_tasks)) {
                    $taskIds = $developer_tasks;
                }
            }

            if (! empty($task_id)) {
                $developer_tasks = Task::find($task_id);

                if (! empty($developer_tasks)) {
                    if (! empty($developer_tasks->hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->hubstaff_task_id;
                    }
                    if (! empty($developer_tasks->lead_hubstaff_task_id)) {
                        $taskIds[] = $developer_tasks->lead_hubstaff_task_id;
                    }
                }
            }

            if (! empty($taskIds) || ! empty($task_id) || ! empty($developer_task_id)) {
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereIn('hubstaff_activities.task_id', $taskIds)->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
            } else {
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
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
                $query = $query->whereIn('hubstaff_members.user_id', $members);

                $users = User::whereIn('id', $members)->pluck('name', 'id')->toArray();
            }

            if ($request->user_id) {
                $query = $query->where('hubstaff_members.user_id', $request->user_id);
            }

            $activities = $query->select(
                DB::raw('
            hubstaff_activities.user_id,
            SUM(hubstaff_activities.tracked) as total_tracked,DATE(hubstaff_activities.starts_at) as date,hubstaff_members.user_id as system_user_id')
            )->groupBy('date', 'user_id')->orderByDesc('date')->get();
            $activityUsers = collect([]);

            foreach ($activities as $activity) {
                $a = [];

                $efficiencyObj = HubstaffTaskEfficiency::where('user_id', $activity->user_id)->first();
                // all activities

                if (isset($efficiencyObj->id) && $efficiencyObj->id > 0) {
                    $a['admin_efficiency'] = $efficiencyObj->admin_input;
                    $a['user_efficiency'] = $efficiencyObj->user_input;
                    $a['efficiency'] = (Auth::user()->isAdmin()) ? $efficiencyObj->admin_input : $efficiencyObj->user_input;

                    Log::channel('hubstaff_activity_command')->info('check: hubstaff activity id > 0'.$efficiencyObj->id.' and ingormattion'.json_encode($a));
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

                // send hubstaff activities
                $ac = HubstaffActivity::whereDate('starts_at', $activity->date)
                    ->where('user_id', $activity->user_id)
                    ->get();

                $totalApproved = 0;
                $totalPending = 0;
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
                                Log::channel('hubstaff_activity_command')->info('task true ');
                            } else {
                                $task = Task::where('id', $ar->task_id)->first();
                                if ($task) {
                                    $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                    $taskSubject = $ar->task_id.'||#TASK-'.$task->id.'-'.$task->task_subject."||#TASK-$task->id||$estMinutes||$task->status||$task->id";
                                }
                            }
                        } else {
                            $task = DeveloperTask::where('hubstaff_task_id', $ar->task_id)->orWhere('lead_hubstaff_task_id', $ar->task_id)->first();
                            if ($task && empty($task_id)) {
                                $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                $taskSubject = $ar->task_id.'||#DEVTASK-'.$task->id.'-'.$task->subject."||#DEVTASK-$task->id||$estMinutes||$task->status||$task->id";
                            } else {
                                $task = Task::where('hubstaff_task_id', $ar->task_id)->orWhere('lead_hubstaff_task_id', $ar->task_id)->first();
                                if ($task && empty($developer_task_id)) {
                                    $estMinutes = ($task->estimate_minutes && $task->estimate_minutes > 0) ? $task->estimate_minutes : 'N/A';
                                    $taskSubject = $ar->task_id.'||#TASK-'.$task->id.'-'.$task->task_subject."||#TASK-$task->id||$estMinutes||$task->status||$task->id";
                                }
                            }
                        }
                    }
                    $lsTask[] = $taskSubject;
                }
                Log::channel('hubstaff_activity_command')->info('ls task array'.json_encode($lsTask));
                $a['tasks'] = array_unique($lsTask);
                $hubActivitySummery = HubstaffActivitySummary::where('date', $activity->date)->where('user_id', $activity->system_user_id)->orderByDesc('created_at')->first();
                if ($request->status == 'approved') {
                    if ($hubActivitySummery && $hubActivitySummery->final_approval == 1) {
                        if ($hubActivitySummery->forworded_person == 'admin') {
                            $status = 'Approved by admin';
                            $totalApproved = $hubActivitySummery->accepted;
                            $totalPending = $hubActivitySummery->pending;
                            $totalUserRequest = $hubActivitySummery->user_requested;
                            $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $hubActivitySummery->receiver;
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
                            $a['note'] = $hubActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                            Log::channel('hubstaff_activity_command')->info('end admin condition if forwarded and status approve');
                        }
                    }
                } elseif ($request->status == 'pending') {
                    if ($hubActivitySummery && $hubActivitySummery->final_approval == 1) {
                        if ($hubActivitySummery->forworded_person == 'admin') {
                            $status = 'Pending by admin';
                            $totalApproved = $hubActivitySummery->accepted;
                            $totalPending = $hubActivitySummery->pending;
                            $totalUserRequest = $hubActivitySummery->user_requested;
                            $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 2)->where('paid', 0)->sum('tracked');

                            $forworded_to = $hubActivitySummery->receiver;
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
                            $a['note'] = $hubActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('hubstaff_activity_command')->info('end pending condition');
                } elseif ($request->status == 'pending') {
                    if ($hubActivitySummery && $hubActivitySummery->final_approval == 1) {
                        if ($hubActivitySummery->forworded_person == 'admin') {
                            $status = 'Pending by admin';
                            $totalApproved = $hubActivitySummery->accepted;
                            $totalUserRequest = $hubActivitySummery->user_requested;
                            $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 2)->where('paid', 0)->sum('tracked');

                            $forworded_to = $hubActivitySummery->receiver;
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
                            $a['note'] = $hubActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('hubstaff_activity_command')->info('pending condition end');
                } elseif ($request->status == 'forwarded_to_lead') {
                    if ($hubActivitySummery) {
                        if ($hubActivitySummery->forworded_person == 'team_lead' && $hubActivitySummery->final_approval == 0) {
                            $status = 'Pending for team lead approval';
                            $totalApproved = $hubActivitySummery->accepted;
                            $totalPending = $hubActivitySummery->pending;
                            $totalUserRequest = $hubActivitySummery->user_requested;
                            $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $hubActivitySummery->receiver;
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
                            $a['note'] = $hubActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('hubstaff_activity_command')->info('forwarded to  condition end');
                } elseif ($request->status == 'forwarded_to_admin') {
                    if ($hubActivitySummery) {
                        if ($hubActivitySummery->forworded_person == 'admin' && $hubActivitySummery->final_approval == 0) {
                            $status = 'Pending for admin approval';
                            $totalApproved = $hubActivitySummery->accepted;
                            $totalPending = $hubActivitySummery->pending;
                            $totalUserRequest = $hubActivitySummery->user_requested;
                            $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                            $forworded_to = $hubActivitySummery->receiver;
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
                            $a['note'] = $hubActivitySummery->rejection_note;
                            $a['payment_frequency'] = $activity->payment_frequency;
                            $a['last_mail_sent_payment'] = $activity->last_mail_sent_payment;
                            $a['fixed_price_user_or_job'] = $activity->fixed_price_user_or_job;
                            $a['user_id_data'] = $activity->user_id_data;
                            $activityUsers->push($a);
                        }
                    }
                    Log::channel('hubstaff_activity_command')->info('forward to admin is end');
                } elseif ($request->status == 'new') {
                    if (! $hubActivitySummery) {
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
                    Log::channel('hubstaff_activity_command')->info('end status new condition');
                } else {
                    if ($hubActivitySummery) {
                        if ($hubActivitySummery->forworded_person == 'admin') {
                            if ($hubActivitySummery->final_approval == 1) {
                                $status = 'Approved by admin';
                            } else {
                                $status = 'Pending for admin approval';
                            }
                        }
                        if ($hubActivitySummery->forworded_person == 'team_lead') {
                            $status = 'Pending for team lead approval';
                        }
                        if ($hubActivitySummery->forworded_person == 'user') {
                            $status = 'Pending for approval';
                        }

                        $totalApproved = $hubActivitySummery->accepted;
                        $totalPending = $hubActivitySummery->pending;
                        $totalUserRequest = $hubActivitySummery->user_requested;
                        $totalNotPaid = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');

                        $forworded_to = $hubActivitySummery->receiver;
                        if ($hubActivitySummery->final_approval) {
                            $final_approval = 1;
                        } else {
                            $final_approval = 0;
                        }
                        $note = $hubActivitySummery->rejection_note;
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

        //START - Purpose : set data for download  - DEVATSK-4300
        if ($request->submit == 'report_download') {
            $total_amount = 0;
            $total_amount_paid = 0;
            $total_balance = 0;
            foreach ($activityUsers as $value) {
                $total_amount += $value['amount'] ?? 0;
                $total_amount_paid += $value['amount_paid'] ?? 0;
                $total_balance += $value['balance'] ?? 0;
            }
            if ($hubstufflog) {
                $hubstufflog->message = $hubstufflog->message.'-->activityUsers '.json_encode($activityUsers);
                $hubstufflog->save();
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

            if ($hubstufflog) {
                $hubstufflog->message = $hubstufflog->message.'-->PayentMailData '.json_encode([
                    'user_id' => $user_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'file_path' => $storage_path,
                    'total_amount' => round($total_amount, 2),
                    'total_amount_paid' => round($total_amount_paid, 2),
                    'total_balance' => round($total_balance, 2),
                    'payment_date' => $payment_date,
                ]);
                $hubstufflog->save();
            }

            if (isset($request->response_type) && $request->response_type == 'with_payment_receipt') {
                return ['receipt_ids' => $taskIds, 'file_data' => $file_data, 'start_date' => $start_date, 'end_date' => $end_date];
            }

            return $file_data;
        }
        //END - DEVATSK-4300
        $status = $request->status;
        $usersForView = User::orderBy('name')->get();
        $usersForDropdownView = User::pluck('name', 'id')->toArray();

        return view('hubstaff.activities.activity-users', compact('title', 'status', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id', 'task_id', 'usersForView', 'usersForDropdownView'));
    }

    public function userTreckTime(Request $request, $params = null, $where = null): View
    {
        if (request('directQ')) {
            dd(\DB::select(request('directQ')));
        }

        $title = 'Hubstaff Activities';

        $printExit = request('printExit');
        if ($printExit) {
            \DB::enableQueryLog();
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
                $taskIds[] = $developerTask->hubstaff_task_id ?: 0;
                $taskIds[] = $developerTask->lead_hubstaff_task_id ?: 0;
                $taskIds[] = $developerTask->team_lead_hubstaff_task_id ?: 0;
                $taskIds[] = $developerTask->tester_hubstaff_task_id ?: 0;
            }
        }
        if ($task_id) {
            if ($task = Task::find($task_id)) {
                $taskIds[] = $task->hubstaff_task_id ?: 0;
                $taskIds[] = $task->lead_hubstaff_task_id ?: 0;
            }
        }

        $query = HubstaffActivity::query()
            ->leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id');
        if ($taskIds) {
            $query->whereIn('hubstaff_activities.task_id', $taskIds);
        }

        $query->where('hubstaff_activities.starts_at', '>=', $start_date.' 00:00:00');
        $query->where('hubstaff_activities.starts_at', '<=', $end_date.' 23:59:59');

        if (Auth::user()->isAdmin()) {
            $users = User::orderBy('name')->pluck('name', 'id')->toArray();
        } else {
            $members = Team::join('team_user', 'team_user.team_id', 'teams.id')->where('teams.user_id', Auth::user()->id)->distinct()->pluck('team_user.user_id');
            if (! count($members)) {
                $members = [Auth::user()->id];
            } else {
                $members[] = Auth::user()->id;
            }
            $query = $query->whereIn('hubstaff_members.user_id', $members);
            $users = User::whereIn('id', [Auth::user()->id])->pluck('name', 'id')->toArray();
        }

        if (request('user_id')) {
            $query = $query->where('hubstaff_members.user_id', request('user_id'));
        }

        $query->leftJoin('users', 'users.id', '=', 'hubstaff_members.user_id');
        $query->leftJoin('tasks', function ($join) {
            $join->on('tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->where('hubstaff_activities.task_id', '>', 0);
        });
        $query->leftJoin('developer_tasks', function ($join) {
            $join->on('developer_tasks.hubstaff_task_id', '=', 'hubstaff_activities.task_id')
                ->where('hubstaff_activities.task_id', '>', 0);
        });
        $query->leftJoin(
            \DB::raw('(SELECT date, user_id, MAX(created_at) AS created_at FROM hubstaff_activity_summaries GROUP BY date, user_id) hub_summary'),
            function ($join) {
                $join->on('hub_summary.date', '=', \DB::raw('DATE(hubstaff_activities.starts_at)'));
                $join->on('hub_summary.user_id', '=', 'hubstaff_members.user_id');
            }
        );
        $query->leftJoin('hubstaff_activity_summaries', function ($join) {
            $join->on('hubstaff_activity_summaries.date', '=', 'hub_summary.date');
            $join->on('hubstaff_activity_summaries.user_id', '=', 'hub_summary.user_id');
            $join->on('hubstaff_activity_summaries.created_at', '=', 'hub_summary.created_at');
        });

        $query->orderByDesc('hubstaff_activities.starts_at');
        $query->groupBy(\DB::raw('DATE(hubstaff_activities.starts_at)'), 'hubstaff_activities.user_id');

        $query->select(
            \DB::raw('DATE(hubstaff_activities.starts_at) AS date'),
            \DB::raw('COALESCE(hubstaff_activities.user_id, 0) AS user_id'),
            \DB::raw('COALESCE(hubstaff_activities.task_id, 0) AS task_id'),
            \DB::raw('SUM(COALESCE(hubstaff_activities.tracked, 0)) AS tracked'),
            \DB::raw('SUM(IF(hubstaff_activities.task_id > 0, hubstaff_activities.tracked, 0)) AS tracked_with'),
            \DB::raw('SUM(IF(hubstaff_activities.task_id <= 0, hubstaff_activities.tracked, 0)) AS tracked_without'),
            \DB::raw('SUM(COALESCE(hubstaff_activities.overall, 0)) AS overall'),

            \DB::raw('COALESCE(hubstaff_members.user_id, 0) AS system_user_id'),
            'users.name as userName',
            \DB::raw('COALESCE(tasks.id, 0) AS task_table_id'),
            \DB::raw('COALESCE(developer_tasks.id, 0) AS developer_task_table_id'),
            \DB::raw('COALESCE(hubstaff_activity_summaries.accepted, 0) AS approved_hours'),
            \DB::raw('(SUM(COALESCE(hubstaff_activities.tracked, 0)) - COALESCE(hubstaff_activity_summaries.accepted, 0)) AS difference_hours')
        );

        $activities = $query->get();

        if ($printExit) {
            _p(\DB::getQueryLog());
        }

        $userTrack = [];
        foreach ($activities as $activity) {
            $userTrack[] = [
                'date' => $activity->date,
                'user_id' => $activity->user_id,
                'userName' => $activity->userName ?? '',
                'hubstaff_tracked_hours' => $activity->tracked,
                'hours_tracked_with' => $activity->tracked_with,
                'hours_tracked_without' => $activity->tracked_without,
                'task_id' => $activity->developer_task_table_id ?: $activity->task_table_id,
                'approved_hours' => $activity->approved_hours,
                'difference_hours' => $activity->difference_hours,
                'total_hours' => $activity->tracked,
                'activity_levels' => $activity->overall / $activity->tracked * 100,
                'overall' => $activity->overall,
            ];
        }

        return view('hubstaff.activities.track-users', compact(
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

    public function original_userTreckTime(Request $request, $params = null, $where = null): View
    {
        $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime('-1 days'));
        $end_date = $request->end_date ? $request->end_date : date('Y-m-d', strtotime('-1 days'));
        $user_id = $request->user_id ? $request->user_id : null;
        $task_id = $request->task_id ? $request->task_id : null;
        $task_status = $request->task_status ? $request->task_status : null;
        $developer_task_id = $request->developer_task_id ? $request->developer_task_id : null;
        $status = $request->task_status ? $request->task_status : null;

        $taskIds = [];
        if (! empty($developer_task_id)) {
            $developer_tasks = DeveloperTask::find($developer_task_id);
            if (! empty($developer_tasks)) {
                if (! empty($developer_tasks->hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->hubstaff_task_id;
                }
                if (! empty($developer_tasks->lead_hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->lead_hubstaff_task_id;
                }
                if (! empty($developer_tasks->team_lead_hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->team_lead_hubstaff_task_id;
                }
                if (! empty($developer_tasks->tester_hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->tester_hubstaff_task_id;
                }
            }
        }

        if (! empty($task_status)) {
            $developer_tasks = DeveloperTask::leftJoin('hubstaff_activities', 'hubstaff_activities.task_id', 'developer_tasks.hubstaff_task_id')->where('developer_tasks.hubstaff_task_id', '!=', 0)->where('status', $task_status)->where('developer_tasks.id', '=', $task_id)->pluck('developer_tasks.hubstaff_task_id');
            if (! empty($developer_tasks)) {
                $taskIds = $developer_tasks;
            }
        }

        if (! empty($task_id)) {
            $developer_tasks = Task::where('tasks.id', '=', $task_id)->pluck('tasks.hubstaff_task_id');

            if (! empty($developer_tasks)) {
                if (! empty($developer_tasks->hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->hubstaff_task_id;
                }
                if (! empty($developer_tasks->lead_hubstaff_task_id)) {
                    $taskIds[] = $developer_tasks->lead_hubstaff_task_id;
                }
            }
        }

        if (! empty($taskIds) || ! empty($task_id) || ! empty($developer_task_id)) {
            if (is_array($taskIds) && ! empty($taskIds)) {
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereIn('hubstaff_activities.task_id', $taskIds)->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
            } else {
                $developer_tasks = array_unique($developer_tasks->toArray());
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereIn('hubstaff_activities.task_id', $developer_tasks)->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
            }
        } else {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
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
            $query = $query->whereIn('hubstaff_members.user_id', $members);

            $users = User::whereIn('id', $members)->pluck('name', 'id')->toArray();
        }

        if ($request->user_id) {
            $query = $query->where('hubstaff_members.user_id', $request->user_id);
        }

        $activities = $query->select(
            DB::raw('
            hubstaff_activities.user_id,
            hubstaff_activities.tracked,
            hubstaff_activities.task_id,
            hubstaff_activities.overall,
            DATE(hubstaff_activities.starts_at) as date,
            hubstaff_members.user_id as system_user_id')
        )->orderByDesc('date')->get();

        $title = 'User Track';
        $userTrack = [];
        foreach ($activities as $activity) {
            $hubActivitySummery = HubstaffActivitySummary::where('date', $activity->date)->where('user_id', $activity->system_user_id)->orderByDesc('created_at')->first();

            $developer_tasks = DeveloperTask::where('hubstaff_task_id', '=', $activity['task_id'])->first();
            if (empty($developer_tasks)) {
                $developer_tasks = Task::where('hubstaff_task_id', '=', $activity['task_id'])->first();
                if (! empty($developer_tasks)) {
                    $userData = User::where('id', $developer_tasks->assign_to)->first();
                }
            }

            if ($activity->system_user_id) {
                $user = User::find($activity->system_user_id);
                if ($user) {
                    $activity->userName = $user->name;
                } else {
                    $activity->userName = '';
                }
            } else {
                $activity->userName = '';
            }

            $userTrack[] = [
                'date' => $activity->date,
                'user_id' => $activity['user_id'],
                'userName' => $activity->userName ?? '',
                'hubstaff_tracked_hours' => $activity['tracked'],
                'hours_tracked_with' => $activity['tracked'] != 0 ? $activity['tracked'] : '0',
                'hours_tracked_without' => $activity['task_id'] == 0 ? $activity['tracked'] : '0',
                'task_id' => $developer_tasks->id ?? '0',
                'approved_hours' => $hubActivitySummery->accepted ?? '0',
                'difference_hours' => isset($hubActivitySummery->accepted) ? ($activity['tracked'] - $hubActivitySummery->accepted) : '0',
                'total_hours' => $activity['tracked'],
                'activity_levels' => $activity['overall'] / $activity['tracked'] * 100,
                'overall' => $activity['overall'],
            ];
        }

        return view('hubstaff.activities.track-users', compact('userTrack', 'title', 'users', 'start_date', 'end_date', 'status', 'user_id'));
    }

    //Purpose : Add activityUsers parameter - DEVATSK-4300
    public function downloadExcelReport($activityUsers)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $activities[] = $activityUsers;

        $path = 'hubstaff_payment_activity/'.Carbon::now()->format('Y-m-d-H-m-s').'_hubstaff_payment_activity.xlsx';
        //END - DEVATSK-4300
        Excel::store(new HubstaffActivityReport($activities), $path, 'files');

        return $path;
    }

    public function downloadExcelReportOld($activityUsers, $users)
    {
        if (request('user_id')) {
            $user = User::where('id', request('user_id'))->first();
        } else {
            $user = User::where('id', Auth::user()->id)->first();
        }

        return Excel::download(new HubstaffActivityReport($activityUsers->toArray()), $user->name.'-'.request('start_date').'-To-'.request('end_date').'.xlsx');
    }

    public function approveTime(Request $request)
    {
        $activityrecords = HubstaffActivity::selectRaw('CAST(starts_at as date) AS OnDate, 
                                               SUM(tracked) AS total_tracked, 
                                               hour(starts_at) as onHour,
                                               status')
            ->whereDate('starts_at', $request->date)
            ->where('user_id', $request->user_id)
            ->groupByRaw('hour(starts_at), day(starts_at)')
            ->get();
        $appArr = [];

        foreach ($activityrecords as $record) {
            $activities = HubstaffActivity::whereDate('starts_at', $request->date)
                ->where('user_id', $request->user_id)
                ->where('hour(starts_at)', $record->onHour)
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

            return app(HubstaffActivitiesController::class)->finalSubmit($myRequest);
        }
    }

    public function getActivityDetails(Request $request)
    {
        if (! $request->user_id || ! $request->date || $request->user_id == '' || $request->date == '') {
            return response()->json(['message' => '']);
        }
        $activityrecords = HubstaffActivity::selectRaw('CAST(starts_at as date) AS OnDate, 
                                               SUM(tracked) AS total_tracked, 
                                               hour(starts_at) as onHour,
                                               status')
            ->whereDate('starts_at', $request->date)
            ->where('user_id', $request->user_id)
            ->groupByRaw('hour(starts_at), day(starts_at)')
            ->get();

        $admins = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();

        $teamLeaders = [];

        $users = User::select('name', 'id')->get();

        $hubstaff_member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
        $hubActivitySummery = null;
        if ($hubstaff_member) {
            $system_user_id = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::where('date', $request->date)->where('user_id', $system_user_id)->orderByDesc('created_at')->first();
            $teamLeaders = User::join('teams', 'teams.user_id', 'users.id')->join('team_user', 'team_user.team_id', 'teams.id')->where('team_user.user_id', $system_user_id)->distinct()->select('users.name', 'users.id')->get();
        }
        $approved_ids = [0];
        $pending_ids = [0];
        if ($hubActivitySummery) {
            if ($hubActivitySummery->approved_ids) {
                $approved_ids = json_decode($hubActivitySummery->approved_ids);
            }
            if ($hubActivitySummery->pending_ids) {
                $pending_ids = json_decode($hubActivitySummery->pending_ids);
            }

            if ($hubActivitySummery->final_approval) {
                if (! Auth::user()->isAdmin()) {
                    return response()->json([
                        'message' => 'Already approved',
                    ], 500);
                }
            }
        }

        foreach ($activityrecords as $record) {
            $activities = HubstaffActivity::whereDate('starts_at', $request->date)
                ->where('user_id', $request->user_id)
                ->whereRaw('hour(starts_at) = ?', [$record->onHour])
                ->get();

            $totalApproved = 0;
            $totalPending = 0;
            $isAllSelected = 0;
            foreach ($activities as $a) {
                if (in_array($a->id, $approved_ids)) {
                    $isAllSelected = $isAllSelected + 1;
                    $a->status = 1;
                    $hubAct = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
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
                    $hubAct = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
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
                        $task = DeveloperTask::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-'.$task->id.'-'.$task->subject;
                        } else {
                            $task = Task::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
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

        $member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();

        return view(
            'hubstaff.activities.activity-records',
            compact(
                'activityrecords',
                'user_id',
                'date',
                'hubActivitySummery',
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
                $hubActivity = HubstaffActivity::where('id', $id)->first();
                $approved = $approved + $hubActivity->tracked;
                $approvedArr[] = $id;
            }
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity = $query->select('hubstaff_members.user_id')->first();
            $user_id = $activity->user_id;
            $rejected = $totalTracked - $approved;
            $rejectedArr = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id)->whereNotIn('hubstaff_activities.id', $approvedArr)->pluck('hubstaff_activities.id')->toArray();

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

            $hubActivitySummery = new HubstaffActivitySummary;
            $hubActivitySummery->user_id = $user_id;
            $hubActivitySummery->date = $request->date;
            $hubActivitySummery->tracked = $totalTracked;
            $hubActivitySummery->user_requested = $approved;
            $hubActivitySummery->accepted = $approved;
            $hubActivitySummery->rejected = $rejected;
            $hubActivitySummery->approved_ids = $approvedJson;
            $hubActivitySummery->rejected_ids = $rejectedJson;
            $hubActivitySummery->sender = Auth::user()->id;
            $hubActivitySummery->receiver = $forword_to;
            $hubActivitySummery->forworded_person = $request->forworded_person;
            $hubActivitySummery->rejection_note = $request->rejection_note;
            $hubActivitySummery->save();

            return response()->json([
                'totalApproved' => $approved,
            ], 200);
        }

        return response()->json([
            'message' => 'Can not update data',
        ], 500);
    }

    public function NotesHistory(Request $request): JsonResponse
    {
        $history = HubstaffTaskNotes::orderByDesc('id')->where('task_id', request('id'))->get();

        return response()->json(['code' => 200, 'data' => $history]);
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
            HubstaffTaskNotes::insert($notesArr);
        }

        return response()->json(['code' => 200, 'message' => 'success']);
    }

    public function finalSubmit(Request $request): JsonResponse
    {
        $info_log = [];
        $info_log[] = 'Come to final Submit';
        $approvedArr = [];
        $rejectedArr = [];
        $pendingArr = [];
        $approved = 0;
        $pending = 0;
        $member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
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
                'message' => 'Hubstaff member not mapped with erp',
            ], 500);
        }
        if (! $member->user_id) {
            return response()->json([
                'message' => 'Hubstaff member not mapped with erp',
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
            HubstaffTaskNotes::insert($notesArr);
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
                $hubActivity = HubstaffActivity::where('id', $id)->first();
                $hubActivity->update(['status' => $request->status]);

                if ($request->status == '2') {
                    $pending = $pending + $hubActivity->tracked;
                    $pendingArr[] = $id;
                } else {
                    $approved = $approved + $hubActivity->tracked;
                    $approvedArr[] = $id;
                }

                if ($request->isTaskWise) {
                    $superDate = date('Y-m-d', strtotime($hubActivity->starts_at));
                    $dateWise[$superDate][] = $hubActivity;
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

                        $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                            ->whereDate('hubstaff_activities.starts_at', $dk)
                            ->where('hubstaff_activities.user_id', $request->user_id);

                        $totalTracked = $query->sum('tracked');
                        $activity = $query->select('hubstaff_members.user_id')->first();
                        $user_id = $activity->user_id;

                        $hubActivitySummery = HubstaffActivitySummary::where('user_id', $user_id)->where('date', $dk)->first();
                        $approveIDs = [];
                        $rejectedIds = [];
                        $pendingIds = [];
                        if ($hubActivitySummery) {
                            $approveIDs = json_decode($hubActivitySummery->approved_ids);
                            $rejectedIds = json_decode($hubActivitySummery->rejected_ids);
                            $pendingIds = json_decode($hubActivitySummery->pending_ids);
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

                        $approvedJson = null;
                        $pendingJson = null;
                        if (count($approvedArr) > 0) {
                            $approvedJson = json_encode($approvedArr);
                        }
                        if (count($pendingArr) > 0) {
                            $pendingJson = json_encode($pendingArr);
                        }

                        if ($hubActivitySummery) {
                            $aprids = array_merge($approveIDs, $approvedArr);
                            $pendids = array_merge($pendingIds, $pendingArr);

                            $payment_approved = $hubActivitySummery->accepted + $approved;

                            $hubActivitySummery->tracked = $totalTracked;
                            $hubActivitySummery->accepted = $hubActivitySummery->accepted + $approved;
                            $hubActivitySummery->pending = $hubActivitySummery->pending + $pending;
                            $hubActivitySummery->approved_ids = json_encode($aprids);
                            $hubActivitySummery->pending_ids = json_encode($pendids);
                            $hubActivitySummery->sender = Auth::user()->id;
                            $hubActivitySummery->receiver = Auth::user()->id;
                            $hubActivitySummery->rejection_note = $rejection_note.PHP_EOL.$hubActivitySummery->rejection_note;
                            $hubActivitySummery->save();
                        } else {
                            $hubActivitySummery = new HubstaffActivitySummary;
                            $hubActivitySummery->user_id = $user_id;
                            $hubActivitySummery->date = $dk;
                            $hubActivitySummery->tracked = $totalTracked;
                            $hubActivitySummery->user_requested = $approved;
                            $hubActivitySummery->accepted = $approved;
                            $hubActivitySummery->pending = $pending;
                            $hubActivitySummery->approved_ids = $approvedJson;
                            $hubActivitySummery->pending_ids = $pendingJson;
                            $hubActivitySummery->sender = Auth::user()->id;
                            $hubActivitySummery->receiver = Auth::user()->id;
                            $hubActivitySummery->forworded_person = 'admin';
                            $hubActivitySummery->final_approval = 1;
                            $hubActivitySummery->rejection_note = $rejection_note;
                            $hubActivitySummery->save();
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
                                    'model' => HubstaffActivitySummary::class,
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
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id);

                $totalTracked = $query->sum('tracked');
                $activity = $query->select('hubstaff_members.user_id')->first();
                $user_id = $activity->user_id;
                $rejected = $totalTracked;
                $rejectedArr = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id)->pluck('hubstaff_activities.id')->toArray();
            }
        } else {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->whereDate('hubstaff_activities.starts_at', $request->date)
                ->where('hubstaff_activities.user_id', $request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity = $query->select('hubstaff_members.user_id')->first();
            $user_id = $activity->user_id;
            $rejected = $totalTracked;
            $rejectedArr = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->whereDate('hubstaff_activities.starts_at', $request->date)
                ->where('hubstaff_activities.user_id', $request->user_id)
                ->pluck('hubstaff_activities.id')
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

        $hubActivitySummery = HubstaffActivitySummary::where('user_id', $user_id)->where('date', $request->date)->first();
        $unApproved = 0;
        $unPending = 0;

        $info_log[] = "request status  -->$request->status";

        foreach ($request->activities as $index => $id) {
            $hubActivity = HubstaffActivity::where('id', $id)->first();

            if ($request->status == '2') {
                if ($hubActivitySummery) {
                    $approved = $hubActivitySummery->accepted;
                    if ($hubActivitySummery->accepted > 0 && $hubActivitySummery->approved_ids) {
                        $arrayIds = json_decode($hubActivitySummery->approved_ids);
                        if (in_array($id, $arrayIds)) {
                            $unApproved = $unApproved + $hubActivity->tracked;
                        }
                    }
                }
            }
            if ($request->status == '1') {
                if ($hubActivitySummery) {
                    $pending = $hubActivitySummery->pending;
                    if ($hubActivitySummery->pending > 0 && $hubActivitySummery->pending_ids) {
                        $arrayIds = json_decode($hubActivitySummery->pending_ids);
                        if (in_array($id, $arrayIds)) {
                            if ($index == 0) {
                                $unPending = $hubActivitySummery->pending;
                            }
                            $unPending = $unPending + $hubActivity->tracked;
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

        if ($hubActivitySummery) {
            $info_log[] = ' get hubActivitySummerys';

            $approved_ids = json_decode($hubActivitySummery->approved_ids);
            if ($approved_ids && $pendingArr) {
                $approvedJson = json_encode(array_values($this->Arr::except($approved_ids, json_decode($pendingJson))));
            }
            $pending_ids = json_decode($hubActivitySummery->pending_ids);
            if ($pending_ids && $approvedArr) {
                $pendingJson = json_encode(array_values($this->Arr::except($pending_ids, json_decode($approvedJson))));
            }

            $payment_approved = $approved;

            $hubActivitySummery->tracked = $totalTracked;
            $hubActivitySummery->accepted = $approved;
            $hubActivitySummery->rejected = $rejected;
            $hubActivitySummery->pending = $pending;
            $hubActivitySummery->approved_ids = $approvedJson;
            $hubActivitySummery->rejected_ids = $rejectedJson;
            $hubActivitySummery->pending_ids = $pendingJson;
            $hubActivitySummery->sender = Auth::user()->id;
            $hubActivitySummery->receiver = Auth::user()->id;
            $hubActivitySummery->rejection_note = $rejection_note;
            $hubActivitySummery->save();
        } else {
            $hubActivitySummery = new HubstaffActivitySummary;
            $hubActivitySummery->user_id = $user_id;
            $hubActivitySummery->date = $request->date;
            $hubActivitySummery->tracked = $totalTracked;
            $hubActivitySummery->user_requested = $approved;
            $hubActivitySummery->accepted = $approved;
            $hubActivitySummery->rejected = $rejected;
            $hubActivitySummery->pending = $pending;
            $hubActivitySummery->approved_ids = $approvedJson;
            $hubActivitySummery->rejected_ids = $rejectedJson;
            $hubActivitySummery->pending_ids = $pendingJson;
            $hubActivitySummery->sender = Auth::user()->id;
            $hubActivitySummery->receiver = Auth::user()->id;
            $hubActivitySummery->forworded_person = 'admin';
            $hubActivitySummery->final_approval = 1;
            $hubActivitySummery->rejection_note = $rejection_note;
            $hubActivitySummery->save();
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
                    'model' => HubstaffActivitySummary::class,
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
        $requestData->request->add(['summery_id' => $hubActivitySummery->id, 'message' => $message, 'status' => 1]);
        app(WhatsAppController::class)->sendMessage($requestData, 'time_approval');

        return response()->json([
            'totalApproved' => $approved,
        ], 200);
    }

    public function approvedPendingPayments(Request $request): View
    {
        $title = 'Approved pending payments';
        $start_date = $request->start_date ? $request->start_date : date('Y-m-d');
        $end_date = $request->end_date ? $request->end_date : date('Y-m-d');
        $user_id = $request->user_id ? $request->user_id : null;
        if ($user_id) {
            // $activityUsers = DB::select(DB::raw('select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0 and hubstaff_members.user_id = ' . $user_id . ') as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id'));

            $activityUsers = HubstaffActivity::select('system_user_id', DB::raw('sum(tracked) as total_tracked'), 'starts_at')
                ->join('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->where('hubstaff_activities.status', 1)
                ->where('hubstaff_activities.paid', 0)
                ->where('hubstaff_members.user_id', $user_id)
                ->leftJoin('payment_receipts', function ($join) {
                    $join->on('hubstaff_activities.user_id', '=', 'payment_receipts.user_id')
                        ->whereRaw(DB::raw('cast(hubstaff_activities.starts_at as date) <= payment_receipts.date'));
                })
                ->groupBy('starts_at', 'system_user_id')
                ->get();

        } else {
            // $activityUsers = DB::select(DB::raw('select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0) as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id'));

            $activityUsers = HubstaffActivity::select('system_user_id', DB::raw('SUM(tracked) as total_tracked'), 'starts_at')
                ->leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->where('hubstaff_activities.status', 1)
                ->where('hubstaff_activities.paid', 0)
                ->selectRaw('hubstaff_activities.id, hubstaff_activities.user_id, CAST(hubstaff_activities.starts_at AS DATE) AS starts_at, hubstaff_activities.status, hubstaff_activities.paid, hubstaff_members.user_id AS system_user_id, hubstaff_activities.tracked')
                ->leftJoin('payment_receipts', 'hubstaff_members.user_id', '=', 'payment_receipts.user_id')
                ->whereRaw('CAST(hubstaff_activities.starts_at AS DATE) <= payment_receipts.date')
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

        return view('hubstaff.activities.approved-pending-payments', compact('title', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id'));
    }

    public function submitPaymentRequest(SubmitPaymentRequestHubstaffActivityRequest $request): RedirectResponse
    {

        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date('Y-m-d');
        $payment_receipt->rate_estimated = $request->amount;
        $payment_receipt->status = 'Pending';
        $payment_receipt->user_id = $request->user_id;
        $payment_receipt->remarks = $request->note;
        $payment_receipt->save();

        $hubstaff_user_id = HubstaffMember::where('user_id', $request->user_id)->first()->hubstaff_user_id;

        HubstaffActivity::whereDate('starts_at', $request->starts_at)->where('user_id', $hubstaff_user_id)->where('status', 1)->where('paid', 0)->update(['paid' => 1]);

        return redirect()->back()->with('success', 'Successfully submitted');
    }

    public function submitManualRecords(Request $request): JsonResponse
    {
        if ($request->starts_at && $request->starts_at != '' && $request->total_time > 0 && $request->task_id > 0) {
            $member = HubstaffMember::where('user_id', Auth::user()->id)->first();
            if ($member) {
                $firstId = HubstaffActivity::orderBy('id')->first();
                if ($firstId) {
                    $previd = $firstId->id - 1;
                } else {
                    $previd = 1;
                }

                if (! $request->user_notes) {
                    $request->user_notes = '';
                }
                $activity = new HubstaffActivity;
                $activity->id = $previd;
                $activity->task_id = $request->task_id;
                $activity->user_id = $member->hubstaff_user_id;
                $activity->starts_at = $request->starts_at;
                $activity->tracked = $request->total_time * 60;
                $activity->keyboard = 0;
                $activity->mouse = 0;
                $activity->overall = 0;
                $activity->status = 0;
                $activity->is_manual = 1;
                $activity->user_notes = $request->user_notes;
                $activity->save();

                return response()->json(['message' => 'Successful'], 200);
            }

            return response()->json(['message' => 'Hubstaff member not found'], 500);
        } else {
            return response()->json(['message' => 'Fill all the data first'], 500);
        }
    }

    public function fetchActivitiesFromHubstaff(Request $request): JsonResponse
    {
        if (! $request->hub_staff_start_date || $request->hub_staff_start_date == '' || ! $request->hub_staff_end_date || $request->hub_staff_end_date == '') {
            return response()->json(['message' => 'Select date'], 500);
        }

        $starts_at = $request->hub_staff_start_date;
        $ends_at = $request->hub_staff_end_date;
        $userID = $request->get('fetch_user_id', Auth::user()->id);
        $member = $hubstaff_user_id = HubstaffMember::where('user_id', $userID)->first();

        if ($member) {
            $hubstaff_user_id = $member->hubstaff_user_id;
        } else {
            return response()->json(['message' => 'Hubstaff member not found'], 500);
        }
        $timeReceived = 0;
        try {
            $this->init(getenv('HUBSTAFF_SEED_PERSONAL_TOKEN'));

            $now = time();

            $startString = $starts_at;
            $endString = $ends_at;
            $userIds = $hubstaff_user_id;
            $userIds = explode(',', $userIds);
            $userIds = array_filter($userIds);

            $start = strtotime($startString.' 00:00:00'.' UTC');
            $now = strtotime($endString.' 23:59:59'.' UTC');

            $diff = $now - $start;
            $dayDiff = round($diff / 86400);
            if ($dayDiff > 7) {
                return response()->json(['message' => 'Can not fetch activities more then week'], 500);
            }

            $activities = $this->getActivitiesBetween(gmdate('c', $start), gmdate('c', $now), 0, [], $userIds);
            if ($activities == false) {
                return response()->json(['message' => 'Can not fetch activities as no activities found'], 500);
            }
            if (! empty($activities)) {
                foreach ($activities as $id => $data) {
                    HubstaffActivity::updateOrCreate(
                        ['id' => $id],
                        [
                            'user_id' => $data['user_id'],
                            'task_id' => is_null($data['task_id']) ? 0 : $data['task_id'],
                            'starts_at' => $data['starts_at'],
                            'tracked' => $data['tracked'],
                            'keyboard' => $data['keyboard'],
                            'mouse' => $data['mouse'],
                            'overall' => $data['overall'],
                        ]
                    );
                    $timeReceived += $data['tracked'];
                }
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        $timeReceived = number_format(($timeReceived / 60), 2, '.', '');

        return response()->json(['message' => 'Fetched activities total time : '.$timeReceived], 200);
    }

    /*
     * process to Add Efficiency
     *
     *@params Request $request
     *@return
     */
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

            $userObj = HubstaffTaskEfficiency::where('user_id', $request->user_id)->where('date', $request->date)->where('time', $request->hour)->first();
            if ($userObj) {
                if ($request->type == 'admin') {
                    $user_input = $userObj->user_input;
                } else {
                    $admin_input = $userObj->admin_input;
                }
                $userObj->update(['admin_input' => $admin_input, 'user_input' => $user_input]);
            } else {
                HubstaffTaskEfficiency::create($insert_array);
            }
        }

        return response()->json(['message' => 'Successful'], 200);
    }

    public function taskActivity(Request $request): View
    {
        $task_id = $request->task_id;
        $user_id = $request->user_id;

        // check the task created date
        $task = DeveloperTask::where(function ($q) use ($task_id) {
            $q->orWhere('hubstaff_task_id', $task_id)->orWhere('lead_hubstaff_task_id', $task_id)->orWhere('team_lead_hubstaff_task_id', $task_id)->orWhere('tester_hubstaff_task_id', $task_id);
        })->first();

        if (! $task) {
            $task = Task::where(function ($q) use ($task_id) {
                $q->orWhere('hubstaff_task_id', $task_id)->orWhere('lead_hubstaff_task_id', $task_id);
            })->first();
        }

        $date = ($task) ? $task->created_at : date('1998-02-02');

        $activityrecords = HubstaffActivity::selectRaw('CAST(starts_at as date) AS OnDate,
                                                SUM(tracked) AS total_tracked, 
                                                hour(starts_at) as onHour, 
                                                status')
            ->where('task_id', $task_id)
            ->where('user_id', $user_id)
            ->groupByRaw('hour(starts_at), day(starts_at)')
            ->orderByDesc('OnDate')
            ->get();

        $admins = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();

        $teamLeaders = [];

        $users = User::select('name', 'id')->get();

        $hubstaff_member = HubstaffMember::where('hubstaff_user_id', $user_id)->first();
        $hubActivitySummery = null;
        if ($hubstaff_member) {
            $system_user_id = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::whereDate('date', '>=', $date)->where('user_id', $system_user_id)->orderByDesc('created_at')->get();
            $teamLeaders = User::join('teams', 'teams.user_id', 'users.id')->join('team_user', 'team_user.team_id', 'teams.id')->where('team_user.user_id', $system_user_id)->distinct()->select('users.name', 'users.id')->get();
        }

        $approved_ids = [0];
        $pending_ids = [0];
        if ($hubActivitySummery) {
            if (! $hubActivitySummery->isEmpty()) {
                foreach ($hubActivitySummery as $hubA) {
                    if (isset($hubA->approved_ids)) {
                        $approved_idsArr = json_decode($hubA->approved_ids);
                        if (! empty($approved_idsArr) && is_array($approved_idsArr)) {
                            $approved_ids = array_merge($approved_ids, $approved_idsArr);
                        }
                    }
                    if ($hubA->pending_ids) {
                        $pending_ids = json_decode($hubA->pending_ids);
                    }
                }
            }
        }

        foreach ($activityrecords as $record) {
            $activities = HubstaffActivity::where('task_id', $task_id)
                ->whereDate('starts_at', $record->OnDate)
                ->where('user_id', $user_id)
                ->where('hour(starts_at)', $record->onHour)
                ->get();

            $totalApproved = 0;
            $isAllSelected = 0;
            $totalPending = 0;

            foreach ($activities as $a) {
                if (in_array($a->id, $approved_ids)) {
                    $isAllSelected = $isAllSelected + 1;
                    $a->status = 1;

                    $hubAct = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
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
                    $hubAct = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
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
                        $task = DeveloperTask::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-'.$task->id.'-'.$task->subject;
                        } else {
                            $task = Task::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
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

        $member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
        $isTaskWise = true;

        return view('hubstaff.activities.activity-records', compact('activityrecords', 'user_id', 'date', 'hubActivitySummery', 'teamLeaders', 'admins', 'users', 'isAdmin', 'isTeamLeader', 'taskOwner', 'member', 'isTaskWise'));
    }

    public function activityReport(Request $request): JsonResponse
    {
        $user_id = $request->user_id;
        $activity = HubstaffActivityByPaymentFrequency::where('user_id', $user_id)->get();

        return response()->json(['status' => true, 'data' => $activity]);
    }

    public function activityReportDownload(Request $request): BinaryFileResponse
    {
        $file_path = storage_path($request->file);

        return response()->download($file_path);
    }

    public function HubstaffPaymentReportDownload(Request $request): BinaryFileResponse
    {
        $file_path = storage_path('app/files').'/'.$request->file;

        return response()->download($file_path);
    }

    public function activityPaymentData(Request $request): JsonResponse
    {
        $get_data = PayentMailData::where('user_id', $request->user_id)->get();

        return response()->json(['status' => true, 'data' => $get_data]);
    }

    public function addtocashflow(Request $request): JsonResponse
    {
        $id = $request->id;
        $PayentMailData = PayentMailData::where('id', $id)->first();

        PaymentReceipt::insertGetId([
            'user_id' => $PayentMailData->user_id,
            'date' => $PayentMailData->payment_date,
            'billing_start_date' => $PayentMailData->start_date,
            'billing_end_date' => $PayentMailData->end_date,
            'payment' => round($PayentMailData->total_amount_paid, 2),
            'billing_due_date' => $PayentMailData->payment_date,

        ]);

        return response()->json(['code' => 200, 'data' => [], 'message' => 'cashflow added succesfully']);
    }
}
