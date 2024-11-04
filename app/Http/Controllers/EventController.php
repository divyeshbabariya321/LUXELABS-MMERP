<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\Email;
use App\EmailAddress;
use App\Event;
use App\EventAvailability;
use App\Jobs\SendEmail;
use App\MailinglistTemplate;
use App\Mails\Manual\EventEmail;
use App\Models\AppointmentRequest;
use App\Models\EventCategory;
use App\Models\EventRemarkHistory;
use App\Models\EventSchedule;
use App\Remark;
use App\TodoList;
use App\ToDoListRemarkHistoryLog;
use App\TodoStatus;
use App\User;
use App\UserAvaibility;
use App\Vendor;
use App\VirtualminDomain;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $admin = $user->isAdmin();

        if ($admin) {
            $users = User::where('id', '!=', Auth::user()->id)->orderBy('name')->get()->toArray();
        } else {
            $users = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
                ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();
        }
        $vendorArray = Vendor::all()->pluck('name', 'id')->toArray();

        $emailAddressArr = EmailAddress::all();
        $mail_templates = MailinglistTemplate::whereNotNull('static_template')->get();

        return view(
            'events.calendar', compact('users', 'vendorArray', 'emailAddressArr', 'mail_templates'),
        );
    }

    public function publicEvents(Request $request)
    {
        $events = Event::myEvents(Auth::user()->id);
        $todoLists = [];
        $todo = [];

        $todolistStatus = TodoStatus::get();
        $requestData = $request->all();

        if (! empty($requestData)) {
            if ($request->search_name) {
                $events = $events->where('name', 'LIKE', '%'.$request->search_name.'%');
            }

            if ($request->search_description) {
                $events = $events->where('description', 'LIKE', '%'.$request->search_description.'%');
            }

            if ($request->search_duration) {
                $events = $events->where('duration_in_min', 'LIKE', '%'.$request->search_duration.'%');
            }

            if ($request->search_date_range_type) {
                $events = $events->where('date_range_type', 'LIKE', '%'.$request->search_date_range_type.'%');
            }

            if ($request->date) {
                $events = $events->where('created_at', 'LIKE', '%'.$request->date.'%');
            }

            if ($request->search_event_type) {
                $events = $events->where('event_type', 'LIKE', '%'.$request->search_event_type.'%');
            }
            if ($request->search_event_type == 'ToDo') {
                $todo = TodoStatus::where('name', '=', 'Completed')->first();
                if ($todo) {
                    $todoLists = TodoList::where('status', '!=', $todo->id)->latest()->paginate(25);
                }
            }
        } else {
            $todo = TodoStatus::where('name', '=', 'Completed')->first();
            if ($todo) {
                $todoLists = TodoList::where('status', '!=', $todo->id)->latest()->paginate(25);
            }
        }

        $events = $events->latest()->withTrashed()->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('events.partials.index', compact('events'))->render(),
            ], 200);
        }

        return view('events.index', compact('events', 'todoLists', 'todo', 'todolistStatus'));
    }

    public function store(Request $request): JsonResponse
    {
        $userId = Auth::user()->id;

        if (! $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $name = $request->get('name');
        $slug = Str::slug($name);
        $description = $request->get('description');
        $durationInMin = $request->get('duration_in_min');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $dateRangeType = $request->get('date_range_type');
        $eventType = $request->get('event_type');
        $eventcategoryId = $request->get('event_category_id');
        $vendorId = $request->get('vendor_id');
        $eventuserId = $request->get('event_user_id');
        $emailFrom = $request->get('from_address');
        $vendorCategoryId = $request->get('vendor_category_id');
        $vendorName = $request->get('vendor_name');
        $vendorEmail = $request->get('vendor_email');
        $vendorPhone = $request->get('vendor_phone');
        $recurringType = $request->get('recurring_type');

        $errors = [];
        if (empty(trim($name))) {
            $errors['name'][] = 'Name is required';
        }

        if (empty(trim($description))) {
            $errors['description'][] = 'Description is required';
        }

        if (empty(trim($startDate))) {
            $errors['start_date'][] = 'Start Date is required';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        if ($request->get('date_range_type') == 'within') {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
        } else {
            $endDate = null;
        }

        if (empty(trim($eventcategoryId))) {
            $errors['event_category_id'][] = 'event catagory is required';
        }

        if (empty(trim($eventuserId))) {
            $errors['event_user_id'][] = 'User is required';
        }

        if (empty(trim($emailFrom))) {
            $errors['email_from_address'][] = 'From email is required';
        }

        if ($vendorId === null) {
            if (empty(trim($vendorCategoryId))) {
                $errors['vendor_category_id'][] = 'Vendor catagory is required';
            }

            if (empty(trim($vendorName))) {
                $errors['vendor_name'][] = 'name is required';
            }
        }

        if (! empty($errors)) {
            return response()->json($errors, 400);
        }

        if ($eventcategoryId & $vendorName) {
            $vendor = new Vendor;
            $vendor->category_id = $vendorCategoryId;
            $vendor->name = $vendorName;
            $vendor->email = $vendorEmail;
            $vendor->phone = $vendorPhone;
            $vendor->save();

            $vendorId = $vendor->id;
        }

        // Event
        $event = new Event;
        $event->user_id = $userId;
        $event->name = $name;
        $event->slug = $slug;
        $event->description = $description;
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
        $event->event_type = $eventType;
        $event->date_range_type = $dateRangeType;
        $event->event_category_id = $eventcategoryId;
        $event->event_user_id = $eventuserId;
        $event->vendor_id = $vendorId;
        $event->recuring_type = $recurringType;
        $event->save();

        $eventSchedule = new EventSchedule;
        $eventSchedule->event_id = $event->id;
        $eventSchedule->schedule_date = $startDate;
        $eventSchedule->public_email = $emailFrom ?? '';
        $eventSchedule->public_remark = $description ?? '';
        $eventSchedule->public_name = $name ?? '';
        $eventSchedule->save();

        // Event Availabilities
        foreach ($request->event_availability as $key => $event_availability) {
            if (isset($event_availability['day']) && $event_availability['day'] == 'on') {
                $eventAvailability = new EventAvailability;
                $eventAvailability->event_id = $event->id;
                $eventAvailability->numeric_day = $key;
                $eventAvailability->start_at = $event_availability['start_at'].':00';
                $eventAvailability->end_at = $event_availability['end_at'].':00';
                $eventAvailability->save();
            }
        }

        $subject = 'Event Scdhuled';
        $message = '';
        $user = User::find($event->event_user_id);
        $eventLink = 'https://us05web.zoom.us/j/6928700773?pwd=Qnp6V2VQWGJ1NkhYd3c4ZHdBTjFoZz09';
        $emailClass = (new EventEmail($subject, $message, $event->user->email, $eventLink))->build();

        $email = Email::create([
            'model_id' => $event->id,
            'model_type' => Event::class,
            'from' => $emailFrom,
            'to' => $user->email,
            'subject' => $subject,
            'message' => $emailClass->render(),
            'template' => '',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return response()->json([
            'code' => 200,
            'message' => 'Event created successfully',
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $result = Event::where('id', $id)->myEvents(Auth::user()->id)->first();

        if ($result) {
            $result->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Event deleted successfully !!',
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404,
        ]);
    }

    public function deleteSchedule(Request $request, $id): JsonResponse
    {
        $result = EventSchedule::where('id', $id)->first();

        if ($result) {
            $result->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Event Schedule deleted successfully !!',
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404,
        ]);
    }

    public function stopRecurring(Request $request, $id): JsonResponse
    {
        $result = Event::where('id', $id)->myEvents(Auth::user()->id)->first();
        if ($result) {
            $eventStartDate = Carbon::parse($result->start_date);
            $nowDate = Carbon::now();

            $result->date_range_type = 'within';
            if ($nowDate->gt($eventStartDate)) {
                $result->end_date = date('Y-m-d');
            } else {
                $result->end_date = $result->start_date;
            }
            $result->save();

            return response()->json([
                'code' => 200,
                'message' => 'Recurring stopped successfully !!',
            ]);
        }

        return response()->json([
            'message' => 'Failed to stop recurring',
            404,
        ]);
    }

    public function reschedule(Request $request): JsonResponse
    {
        $userId = Auth::user()->id;

        if (! $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $event = Event::where('id', $request->get('id'))->myEvents(Auth::user()->id)->first();
        if (! $event) {
            return response()->json([
                'message' => 'Failed to reschedule',
                404,
            ]);
        }

        $durationInMin = $request->get('duration_in_min');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $mailBody = $request->get('mail_body');
        $dateRangeType = $request->get('date_range_type');

        $errors = [];
        if (empty(trim($startDate))) {
            $errors['start_date'][] = 'Start Date is required';
        }

        if (empty(trim($durationInMin))) {
            $errors['duration_in_min'][] = 'Duration is required';
        }

        if (empty(trim($mailBody))) {
            $errors['mail_body'][] = 'Mail body is required';
        }

        if ($dateRangeType == 'within') {
            if (empty(trim($endDate))) {
                $errors['end_date'][] = 'End date is required';
            }
        } else {
            $endDate = null;
        }

        if (! empty($errors)) {
            return response()->json($errors, 400);
        }

        // Event
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->duration_in_min = $durationInMin;
        $event->date_range_type = $dateRangeType;
        $event->save();

        if ($event->eventSchedules->count() > 0) {
            foreach ($event->eventSchedules as $eventSchedule) {
                $eventSchedule->delete(); // Delete already booked schedule for this event.
                $this->emailSend($event, $eventSchedule, $mailBody);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Event rescheduled successfully',
        ]);
    }

    public function emailSend($event, $eventSchedule, $message)
    {
        $subject = 'Event Rescheduled';
        $emailClass = (new EventEmail($subject, $message, $event->user->email, $event->link))->build();

        $email = Email::create([
            'model_id' => $event->id,
            'model_type' => Event::class,
            'from' => $event->user->email,
            'to' => $eventSchedule->public_email,
            'subject' => $subject,
            'message' => $emailClass->render(),
            'template' => '',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');
    }

    public function getSchedules(Request $request)
    {
        $userId = Auth::user()->id;
        $user = Auth::user();

        if (! $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $eventSchedules = EventSchedule::where('start_at', '!=', '00:00:00')->where('end_at', '!=', '00:00:00')->
        whereBetween('schedule_date', [$start, $end])->
        whereHas('event', function ($event) use ($userId, $start, $end) {
            $event->where('user_id', $userId)->where('event_type', 'PU');
            $event
                ->orWhereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere([
                    ['start_date', '<=', $start],
                    ['end_date', '=', null],
                ])
                ->orWhere([
                    ['start_date', '<=', $start],
                    ['end_date', '>=', $end],
                ]);
        })
            ->get()
            ->map(function ($eventSchedule) {
                return [
                    'event_id' => $eventSchedule->event_id,
                    'event_schedule_id' => $eventSchedule->id,
                    'subject' => $eventSchedule->event->name,
                    'title' => $eventSchedule->event->name,
                    'description' => $eventSchedule->event->description,
                    'start' => $eventSchedule->schedule_date.' '.$eventSchedule->start_at,
                    'end' => $eventSchedule->schedule_date.' '.$eventSchedule->end_at,
                    'event_type' => $eventSchedule->event->event_type,
                ];
            });

        // Private & Public Events
        $userPrivateEvents = Event::join('event_availabilities', 'event_availabilities.event_id', '=', 'events.id')
            ->select('events.*', 'event_availabilities.numeric_day', 'event_availabilities.start_at', 'event_availabilities.end_at')
            ->where('user_id', $userId)
            ->where(function ($query) use ($start, $end) {
                $query
                    ->orWhereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere([
                        ['start_date', '<=', $start],
                        ['end_date', '=', null],
                    ])
                    ->orWhere([
                        ['start_date', '<=', $start],
                        ['end_date', '>=', $end],
                    ]);
            })
            ->get();

        $userPrivateEventCollection = new Collection;

        foreach ($userPrivateEvents as $userPrivateEvent) {
            $eventEndDate = $userPrivateEvent->end_date ?: $end;
            $startDate = new Carbon($userPrivateEvent->start_date);
            $endDate = new Carbon($eventEndDate);

            if ($userPrivateEvent->recuring_type) {
                $eventTitle = $userPrivateEvent->name.' - '.$userPrivateEvent->recuring_type;
            } else {
                $eventTitle = $userPrivateEvent->name;
            }

            while ($startDate->lte($endDate)) {
                if ($startDate->format('N') == $userPrivateEvent->numeric_day) {
                    $userPrivateEventCollection->push((object) [
                        'event_id' => $userPrivateEvent->id,
                        'subject' => $userPrivateEvent->name,
                        'title' => $eventTitle,
                        'description' => $userPrivateEvent->description,
                        'start' => $startDate->toDateString().' '.$userPrivateEvent->start_at,
                        'end' => $startDate->toDateString().' '.$userPrivateEvent->end_at,
                        'event_type' => $userPrivateEvent->event_type,
                    ]);
                }
                $startDate->addDay();
            }
        }

        // Asset manager
        $myAssets = new Collection;
        $admin = $user->isAdmin();

        $cStartDate = Carbon::parse($start);

        if ($admin) {
            $assetsManagers = AssetsManager::where([
                'active' => 1,
            ])
                ->whereIn('payment_cycle', ['Monthly', 'Yearly', 'One time'])
                ->whereNotNull('due_date')
                ->where(function ($query) use ($start, $end) {
                    $query->WhereBetween('due_date', [$start, $end])
                        ->orWhere([
                            ['due_date', '<=', $start],
                        ]);
                })
                ->get();

            foreach ($assetsManagers as $assetsManager) {
                $cDueDate = Carbon::parse($assetsManager->due_date);
                // Monthly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Monthly') {
                    if (($cDueDate->month <= $cStartDate->month && $cDueDate->year == $cStartDate->year) ||
                        ($cDueDate->year < $cStartDate->year)) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
                // Yearly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Yearly') {
                    if ($cDueDate->month == $cStartDate->month && $cDueDate->year <= $cStartDate->year) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
                // One time Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'One time') {
                    if ($cDueDate->month == $cStartDate->month && $cDueDate->year == $cStartDate->year) {
                        $myAssets->push((object) [
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                            'event_type' => 'AS',
                        ]);
                    }
                }
            }
        }

        // Virtualmin Domain Expiry
        $virtualminDomainCollections = new Collection;
        if ($admin) {
            $virtualminDomains = VirtualminDomain::whereNotNull('expiry_date')
                ->where(function ($query) use ($start, $end) {
                    $query->WhereBetween('expiry_date', [$start, $end]);
                })
                ->get();

            foreach ($virtualminDomains as $virtualminDomain) {
                $cDueDate = Carbon::parse($virtualminDomain->expiry_date);
                if ($cDueDate->month == $cStartDate->month && $cDueDate->year == $cStartDate->year) {
                    $virtualminDomainCollections->push((object) [
                        'virtualmin_domain_id' => $virtualminDomain->id,
                        'subject' => 'Domain Expiry'.' ( '.($virtualminDomain->name ?? '-').' )',
                        'title' => 'Domain Expiry'.' ( '.($virtualminDomain->name ?? '-').' )',
                        'description' => 'Domain Expiry'.' ( '.($virtualminDomain->name ?? '-').' )',
                        'start' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                        'end' => $cDueDate->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                        'event_type' => 'VD',
                    ]);
                }
            }
        }

        // Task Notes (Remarks)
        $taskRemarkCollections = new Collection;
        $taskRemarks = Remark::where([
            'module_type' => 'task-note',
            'is_hide' => 0,
        ])
            ->where(function ($query) use ($start, $end) {
                $query->WhereBetween('created_at', [$start, $end]);
            })
            ->get();

        foreach ($taskRemarks as $taskRemark) {
            $taskRemarkCreated = Carbon::parse($taskRemark->created_at);
            if ($taskRemarkCreated->month == $cStartDate->month && $taskRemarkCreated->year == $cStartDate->year) {
                $taskRemarkCollections->push((object) [
                    'remark_id' => $taskRemark->id,
                    'subject' => "Task #{$taskRemark->taskid} - {$taskRemark->remark}",
                    'title' => "Task #{$taskRemark->taskid} - {$taskRemark->remark}",
                    'description' => "Task #{$taskRemark->taskid} - {$taskRemark->remark}",
                    'start' => $taskRemarkCreated->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                    'end' => $taskRemarkCreated->setMonth($cStartDate->month)->setYear($cStartDate->year)->toDateTimeString(),
                    'event_type' => 'TR', // Task Remark
                ]);
            }
        }

        $userAvailableSlot = [];
        if ($request->srchUser > 0) {
            $stDate = $request->start;
            $enDate = $request->end;

            if ($stDate && $enDate) {
                $filterDates = dateRangeArr($stDate, $enDate);
                $filterDatesNew = [];
                foreach ($filterDates as $row) {
                    $filterDatesNew[$row['date']] = $row;
                }

                $q = User::query();
                $q->leftJoin('user_avaibilities as ua', 'ua.user_id', '=', 'users.id');
                $q->where('users.is_task_planned', 1);
                $q->where('ua.is_latest', 1);
                $q->where('users.id', $request->srchUser);
                $q->select([
                    'users.id',
                    'users.name',
                    DB::raw('ua.id AS uaId'),
                    DB::raw('ua.date AS uaDays'),
                    DB::raw('ua.from AS uaFrom'),
                    DB::raw('ua.to AS uaTo'),
                    DB::raw('ua.start_time AS uaStTime'),
                    DB::raw('ua.end_time AS uaEnTime'),
                    DB::raw('ua.lunch_time AS uaLunchTime'),
                    DB::raw('ua.lunch_time_from AS lunch_time_from'),
                    DB::raw('ua.lunch_time_to AS lunch_time_to'),
                ]);
                $usersDetails = $q->first();

                if (! empty($usersDetails)) {
                    $filterDatesOnly = array_column($filterDates, 'date');

                    if ($usersDetails->uaId) {
                        $usersDetails->uaStTime = date('H:i:00', strtotime($usersDetails->uaStTime));
                        $usersDetails->uaEnTime = date('H:i:00', strtotime($usersDetails->uaEnTime));
                        $usersDetails->uaLunchTime = $usersDetails->uaLunchTime ? date('H:i:00', strtotime($usersDetails->uaLunchTime)) : '';

                        $usersDetails->uaDays = $usersDetails->uaDays ? explode(',', str_replace(' ', '', $usersDetails->uaDays)) : [];
                        $availableDates = UserAvaibility::getAvailableDates($usersDetails->uaFrom, $usersDetails->uaTo, $usersDetails->uaDays, $filterDatesOnly);
                        $availableSlots = UserAvaibility::dateWiseHourlySlotsV2($availableDates, $usersDetails->uaStTime, $usersDetails->uaEnTime, $usersDetails->uaLunchTime, $usersDetails);

                        $iii = 0;
                        if (! empty($availableSlots)) {
                            foreach ($availableSlots as $valueArray) {
                                if (! empty($valueArray)) {
                                    foreach ($valueArray as $key => $value) {
                                        if ($value['type'] == 'AVL') {
                                            $userAvailableSlot[$iii]['event_id'] = $iii;
                                            $userAvailableSlot[$iii]['subject'] = $value['st'].' - '.$value['en'];
                                            $userAvailableSlot[$iii]['title'] = 'Slot - '.($key + 1);
                                            $userAvailableSlot[$iii]['start'] = $value['st'];
                                            $userAvailableSlot[$iii]['end'] = $value['en'];
                                            $userAvailableSlot[$iii]['event_type'] = 'AV';

                                            $iii++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $merged = $eventSchedules->concat($userPrivateEventCollection)
            ->concat($myAssets)
            ->concat($virtualminDomainCollections)
            ->concat($taskRemarkCollections)
            ->concat($userAvailableSlot);

        return response()->json($merged);
    }

    public function getEventAlerts(Request $request)
    {
        $userId = Auth::user()->id;
        $user = Auth::user();
        $dt = Carbon::now()->startOfDay();

        if ($request->get('date')) {
            $dt = Carbon::createFromFormat('Y-m-d', $request->get('date'));
        }

        // Public events
        $publicEventSchedules = EventSchedule::whereDate('schedule_date', $dt->toDateString())
            ->whereHas('event', function ($event) use ($userId) {
                $event->where('user_id', $userId)->where('event_type', 'PU');
            })
            ->whereDoesntHave('eventAlertLogs', function ($q) {
                $q->where('is_read', 1);
            })
            ->get()
            ->map(function ($eventSchedule) {
                return (object) [
                    'event_id' => $eventSchedule->event_id,
                    'event_schedule_id' => $eventSchedule->id,
                    'assets_manager_id' => '',
                    'subject' => $eventSchedule->event->name,
                    'title' => $eventSchedule->event->name,
                    'description' => $eventSchedule->event->description,
                    'start' => $eventSchedule->schedule_date.' '.$eventSchedule->start_at,
                    'end' => $eventSchedule->schedule_date.' '.$eventSchedule->end_at,
                    'event_type' => $eventSchedule->event->event_type,
                    'event_type_name' => $eventSchedule->event->event_type_name,
                ];
            });

        // Asset manager
        $myAssets = new Collection;
        $admin = $user->isAdmin();
        if ($admin) {
            $assetsManagers = AssetsManager::where([
                'active' => 1,
            ])
                ->whereIn('payment_cycle', ['Monthly', 'Yearly', 'One time'])
                ->whereNotNull('due_date')
                ->whereDoesntHave('eventAlertLogs', function ($q) use ($dt) {
                    $q->where('is_read', 1);
                    $q->where('event_alert_date', $dt->toDateString());
                })
                ->where(function ($query) use ($dt) {
                    $query->WhereDate('due_date', $dt->toDateString())
                        ->orWhere([
                            ['due_date', '<=', $dt->toDateString()],
                        ]);
                })
                ->get();

            foreach ($assetsManagers as $assetsManager) {
                $cDueDate = Carbon::parse($assetsManager->due_date);
                // Monthly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Monthly') {
                    $monthlyRecurringDueDate = $cDueDate->setMonth($dt->month)->setYear($dt->year);

                    if ($dt->eq($monthlyRecurringDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setMonth($dt->month)->setYear($dt->year)->toDateTimeString(),
                            'end' => $cDueDate->setMonth($dt->month)->setYear($dt->year)->toDateTimeString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
                // Yearly Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'Yearly') {
                    $yearlyRecurringDueDate = $cDueDate->setYear($dt->year);

                    if ($dt->eq($yearlyRecurringDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->setYear($dt->year)->toDateTimeString(),
                            'end' => $cDueDate->setYear($dt->year)->toDateTimeString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
                // One time Payment Cycle - Logic
                if ($assetsManager->payment_cycle == 'One time') {
                    if ($dt->eq($cDueDate)) {
                        $myAssets->push((object) [
                            'event_id' => '',
                            'event_schedule_id' => '',
                            'assets_manager_id' => $assetsManager->id,
                            'subject' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'title' => 'Payment Due'.' (Asset: '.($assetsManager->name ?? '-').", Provider name: $assetsManager->provider_name, Location: $assetsManager->location )",
                            'description' => "Provider name: $assetsManager->provider_name, Location: $assetsManager->location",
                            'start' => $cDueDate->toDateTimeString(),
                            'end' => $cDueDate->toDateTimeString(),
                            'event_type' => Event::ASSET,
                            'event_type_name' => Event::$eventTypes[Event::ASSET],
                        ]);
                    }
                }
            }
        }

        // Private Events
        $userPrivateEvents = Event::join('event_availabilities', 'event_availabilities.event_id', '=', 'events.id')
            ->select('events.*', 'event_availabilities.numeric_day', 'event_availabilities.start_at', 'event_availabilities.end_at')
            ->where('user_id', $userId)
            ->where('event_type', 'PR')
            ->where(function ($query) use ($dt) {
                $query->where([
                    ['start_date', '<=', $dt->toDateString()],
                    ['end_date', '>=', $dt->toDateString()],
                ])
                    ->orWhere([
                        ['start_date', '<=', $dt->toDateString()],
                        ['end_date', '=', null],
                    ]);
            })
            ->get();

        $userPrivateEventCollection = new Collection;
        foreach ($userPrivateEvents as $userPrivateEvent) {
            if ($dt->format('N') == $userPrivateEvent->numeric_day) {
                $eventAlertDate = $dt->toDateString().' '.$userPrivateEvent->start_at;
                $eventAlertLogExists = $userPrivateEvent->whereHas('eventAlertLogs', function ($eventAlertLog) use ($userId, $eventAlertDate) {
                    $eventAlertLog->where('user_id', $userId)
                        ->where('is_read', 1)
                        ->where('event_alert_date', $eventAlertDate)
                        ->where('event_type', 'PR');
                })->exists();

                if (! $eventAlertLogExists) {
                    $userPrivateEventCollection->push((object) [
                        'event_id' => $userPrivateEvent->id,
                        'event_schedule_id' => '',
                        'assets_manager_id' => '',
                        'subject' => $userPrivateEvent->name,
                        'title' => $userPrivateEvent->name,
                        'description' => $userPrivateEvent->description,
                        'start' => $dt->toDateString().' '.$userPrivateEvent->start_at,
                        'end' => $dt->toDateString().' '.$userPrivateEvent->end_at,
                        'event_type' => $userPrivateEvent->event_type,
                        'event_type_name' => $userPrivateEvent->event_type_name,
                    ]);
                }
            }
        }

        $merged = $publicEventSchedules->concat($myAssets)->concat($userPrivateEventCollection);

        $html = view('partials.modals.event-alerts-modal-html')->with('eventAlerts', $merged)->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Content render', 'count' => $merged->count()]);
    }

    public function saveAlertLog(Request $request): JsonResponse
    {
        $eventType = $request->event_type;
        $eventId = $request->event_id;
        $eventScheduleId = $request->event_schedule_id;
        $assetsManagerId = $request->assets_manager_id;
        $eventAlertDate = $request->event_alert_date;
        $isRead = ($request->is_read == 'true' ? 1 : 0);

        if ($eventType == Event::PUBLIC) {
            $eventSchedule = EventSchedule::findOrFail($eventScheduleId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $eventSchedule->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        if ($eventType == Event::PRIVATE) {
            $event = Event::findOrFail($eventId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $event->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        if ($eventType == Event::ASSET) {
            $assetsManager = AssetsManager::findOrFail($assetsManagerId);

            $eventAlertLog['user_id'] = Auth::user()->id;
            $eventAlertLog['event_alert_date'] = $eventAlertDate;
            $eventAlertLog['event_type'] = $eventType;

            $assetsManager->eventAlertLogs()->updateOrCreate($eventAlertLog, ['is_read' => $isRead]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Updated successfully !!',
        ]);
    }

    public function eventCategoryStore(Request $request): JsonResponse
    {
        $eventCategory = new EventCategory;
        $eventCategory->category = $request->category;
        $eventCategory->save();

        return response()->json(['code' => 200, 'data' => $eventCategory, 'message' => 'Category create Succcesfully']);
    }

    public function addEventsRemarks(Request $request): JsonResponse
    {
        if ($request->event_type == 'event-list') {
            $eventRemark = Event::where('id', $request->event_id)->first();
            $eventRemark->remarks = $request->remark;
            $eventRemark->save();

            $eventRemarkhistory = new EventRemarkHistory;
            $eventRemarkhistory->event_id = $request->event_id;
            $eventRemarkhistory->remarks = $request->remark;
            $eventRemarkhistory->user_id = Auth::id();
            $eventRemarkhistory->save();

            return response()->json(['code' => 500, 'message' => 'Event Remark Added Successfully!']);
        } else {
            $todoRemark = TodoList::where('id', $request->event_id)->first();
            $todoOldRemark = $todoRemark->remark;
            $todoRemark->remark = $request->remark;
            $todoRemark->save();

            $todoRemarkHistory = new ToDoListRemarkHistoryLog;
            $todoRemarkHistory->user_id = Auth::user()->id;
            $todoRemarkHistory->todo_list_id = $request->event_id;
            $todoRemarkHistory->remark = $request->remark;
            $todoRemarkHistory->old_remark = $todoOldRemark ?? '';
            $todoRemarkHistory->save();

            return response()->json(['code' => 500, 'message' => 'Todo List Remark Added Successfully!']);
        }
    }

    public function getEventremarkList(Request $request): JsonResponse
    {
        if ($request->event_type == 'event-list') {
            $taskRemarkData = EventRemarkHistory::where('event_id', '=', $request->eventId)->get();

            $html = '';
            foreach ($taskRemarkData as $taskRemark) {
                $html .= '<tr>';
                $html .= '<td>'.$taskRemark->id.'</td>';
                $html .= '<td>'.$taskRemark->user->name.'</td>';
                $html .= '<td>'.$taskRemark->remarks.'</td>';
                $html .= '<td>'.$taskRemark->created_at.'</td>';
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='".$taskRemark->remarks."'></i></td>";
            }
        } else {
            $taskRemarkData = ToDoListRemarkHistoryLog::where('todo_list_id', '=', $request->eventId)->get();

            $html = '';
            foreach ($taskRemarkData as $taskRemark) {
                $html .= '<tr>';
                $html .= '<td>'.$taskRemark->id.'</td>';
                $html .= '<td>'.$taskRemark->username->name.'</td>';
                $html .= '<td>'.$taskRemark->remark.'</td>';
                $html .= '<td>'.$taskRemark->created_at.'</td>';
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='".$taskRemark->remarks."'></i></td>";
            }
        }

        return response()->json(['code' => 200, 'data' => $html, 'message' => 'Remark listed Successfully']);
    }

    public function statusUpdate(Request $request): JsonResponse
    {
        $event = [];
        $todolists = [];

        if ($request->type == 'todo') {
            $todolists = TodoList::findorfail($request->id);
            $todolists->status = $request->status;
            $todolists->save();
        }

        if ($request->type == 'event') {
            $event = Event::findorfail($request->id);
            $event->status = $request->status;
            $event->save();
        }

        return response()->json(['code' => 200, 'todo' => $todolists, 'event' => $event, 'message' => 'YourStatus has been Updated Succeesfully!']);
    }

    public function sendAppointmentRequest(Request $request): JsonResponse
    {
        $arequest = new AppointmentRequest;
        $arequest->user_id = Auth::user()->id;
        $arequest->requested_user_id = $request->requested_user_id;
        $arequest->remarks = $request->requested_remarks;
        $arequest->request_status = 0;
        $arequest->requested_time = $request->requested_time;
        $arequest->requested_time_end = $request->requested_time_end;
        $arequest->is_view = 0;
        $arequest->save();

        $userAppointments = getAppointments();
        broadcastAppointments($userAppointments);

        return response()->json([
            'code' => 200,
            'message' => 'Updated successfully !!',
        ]);
    }

    public function getAppointmentRequest(Request $request): JsonResponse
    {
        $currentDateTimeFormatted = Carbon::now()->format('Y-m-d H:i:s');

        $result = AppointmentRequest::with('user')->where('requested_time', '<=', $currentDateTimeFormatted)
            ->where('requested_time_end', '>=', $currentDateTimeFormatted)
            ->where('requested_user_id', Auth::user()->id)
            ->where('request_status', 0)->orderByDesc('id')->first();

        $result_rerquest_user = AppointmentRequest::with('userrequest')->where('request_status', '!=', 0)
            ->where('user_id', Auth::user()->id)
            ->where('is_view', 0)->orderByDesc('id')->first();

        if (! empty($result) && ! empty($result_rerquest_user)) {
            return response()->json([
                'code' => 200,
                'result' => $result,
                'result_rerquest_user' => $result_rerquest_user,
                'message' => 'Event Schedule deleted successfully !!',
            ]);
        } elseif (! empty($result)) {
            return response()->json([
                'code' => 200,
                'result' => $result,
                'result_rerquest_user' => '',
                'message' => 'Event Schedule deleted successfully !!',
            ]);
        } elseif (! empty($result_rerquest_user)) {
            return response()->json([
                'code' => 200,
                'result' => '',
                'result_rerquest_user' => $result_rerquest_user,
                'message' => 'Event Schedule deleted successfully !!',
            ]);
        }
    }

    public function updateAppointmentRequest(Request $request): JsonResponse
    {
        $arequest = AppointmentRequest::findorfail($request->id);
        $arequest->request_status = $request->request_status;
        $arequest->save();

        $userAppointments = getAppointments();
        broadcastAppointments($userAppointments);

        return response()->json([
            'code' => 200,
            'message' => 'Updated successfully !!',
        ]);
    }

    public function updateuserAppointmentRequest(Request $request): JsonResponse
    {
        $arequest = AppointmentRequest::findorfail($request->id);
        $arequest->is_view = 1;
        $arequest->save();

        $userAppointments = getAppointments();
        broadcastAppointments($userAppointments);

        return response()->json([
            'code' => 200,
            'message' => 'Updated successfully !!',
        ]);
    }

    public function declineRemarks(Request $request): JsonResponse
    {
        $AppointmentRequest = AppointmentRequest::where('id', $request->appointment_requests_id)->first();

        if ($AppointmentRequest) {
            $AppointmentRequest->decline_remarks = $request->get('appointment_requests_remarks', '');
            $AppointmentRequest->save();

            if ($AppointmentRequest) {
                $userAppointments = getAppointments();
                broadcastAppointments($userAppointments);

                return response()->json(['code' => 200, 'data' => $AppointmentRequest, 'message' => 'Your remarks has been added!']);
            }
        }
    }

    public function userOnlineStatusUpdate(Request $request): JsonResponse
    {
        $user = User::find(Auth::user()->id);
        $user->is_online_flag = ($request->is_online_flag == 'Online') ? 1 : 0;
        $user->save();

        return response()->json(['code' => 200, 'message' => 'User Status has been Updated Succeesfully!']);
    }

    public function getUserDetailsForOnline(Request $request): JsonResponse
    {
        $user = User::find($request->id);

        $is_online_flag = 0;

        if (Auth::user()->isAdmin()) {
            if ($user->isOnline() == 1) {
                $is_online_flag = 1;
            }
        } else {
            if ($user->isOnline() == 1 && $user->is_online_flag) {
                $is_online_flag = 1;
            }
        }

        return response()->json(['code' => 200, 'is_online_flag' => $is_online_flag, 'name' => $user->name]);
    }
}
