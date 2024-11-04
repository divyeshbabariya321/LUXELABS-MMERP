<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\Currency;
use App\DailyActivitiesHistories;
use App\DailyActivity;
use App\Http\Requests\UpdateEventUserEventRequest;
use App\Learning;
use App\MailinglistTemplate;
use App\User;
use App\UserEvent\UserEvent;
use App\UserEvent\UserEventAttendee;
use App\UserEvent\UserEventParticipant;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserEventController extends Controller
{
    public function index(): View
    {
        $userId = Auth::user()->id;
        $expireTime = Carbon::now()->addMinutes(30)->toDateTimeString();
        $link = base64_encode('soloerp:'.$userId.":$expireTime");
        $vendorArray = Vendor::all()->pluck('name', 'id')->toArray();
        $mailTemplates = MailinglistTemplate::whereNotNull('static_template')->get();

        return view(
            'user-event.index',
            [
                'link' => $link,
                'vendorArray' => $vendorArray,
                'mailTemplates' => $mailTemplates,
            ]
        );
    }

    /**
     * list of user events as json
     */
    public function list(Request $request)
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

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $c_start = Carbon::parse($start);
        $c_end = Carbon::parse($end);

        $assetsmanager = AssetsManager::where([
            'user_name' => $userId,
            'active' => 1,
            'payment_cycle' => 'Monthly',
        ])->whereNotNull('due_date')->get();

        if (count($assetsmanager) > 0) {
            foreach ($assetsmanager as $key => $val) {
                $c_due_date = Carbon::parse($val->due_date);
                if ($c_due_date->lte($c_start) || $c_due_date->between($c_start, $c_end)) {
                    $arr = explode('-', $val->due_date);
                    for ($i = 0; $i < 2; $i++) {
                        $arr[1] = $c_start->month + $i;
                        $arr[0] = $c_start->year;
                        if ($arr[1] == 13) {
                            $arr[1] = 1;
                            $arr[0]++;
                        }
                        $c_due_date = implode('-', $arr);
                        $c_due_date = Carbon::parse($c_due_date);

                        if ($c_start->lte($c_due_date) && $c_end->gte($c_due_date)) {
                            $exist = UserEvent::where('asset_manager_id', $val->id)->where('date', $c_due_date->format('Y-m-d'))->count();

                            if ($exist == 0) {
                                $userEvent = new UserEvent;
                                $userEvent->user_id = $val->user_name;
                                $userEvent->subject = 'Payment Due';
                                $userEvent->subject .= ' (Asset: '.($val->name ?? '-').", Provider name: $val->provider_name, Location: $val->location )";
                                $userEvent->description = "Provider name: $val->provider_name, Location: $val->location";
                                $userEvent->date = $c_due_date;
                                $userEvent->start = $c_due_date;
                                $userEvent->end = $c_due_date;
                                $userEvent->asset_manager_id = $val->id;
                                $userEvent->save();
                            }
                        }
                    }
                }
            }
        }

        $events = UserEvent::with(['attendees'])
            ->where('start', '>=', $start)
            ->where('end', '<', $end)
            ->where('user_id', $userId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'subject' => $event->subject,
                    'title' => $event->subject,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->start,
                    'end' => $event->end,
                    'attendees' => $event->attendees,
                ];
            });

        return response()->json($events);
    }

    /**
     * edit event
     */
    public function editEvent(Request $request, int $id): JsonResponse
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
        $date = $request->get('date');
        $start = $request->get('start');
        $end = $request->get('end');

        $userEvent = UserEvent::find($id);

        if (! $userEvent) {
            return response()->json(
                [
                    'message' => 'Event not found',
                ],
                404
            );
        }

        if ($userEvent->user_id != $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed to edit event',
                ],
                401
            );
        }

        $userEvent->start = $start;
        $userEvent->end = $end;
        $userEvent->save();

        // once user event has been stored create the event in daily planner
        $dailyActivities = new DailyActivity;
        if ($userEvent->daily_activity_id > 0) {
            $dailyActivities = DailyActivity::find($userEvent->daily_activity_id);
            if (empty($dailyActivities)) {
                $dailyActivities = new DailyActivity;
            }
        }

        $dailyActivities->time_slot = date('h:00 a', strtotime($userEvent->start)).' - '.date('h:00 a', strtotime($userEvent->end));
        $dailyActivities->activity = $userEvent->subject;
        $dailyActivities->user_id = $userId;
        $dailyActivities->for_date = $date;

        if ($dailyActivities->save()) {
            $userEvent->daily_activity_id = $dailyActivities->id;
            $userEvent->save();
        }

        // check first and vendors
        $vendors = $request->get('vendors', []);
        UserEventParticipant::where('user_event_id', $userEvent->id)->delete();
        if (! empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $userEventParticipant = new UserEventParticipant;
                $userEventParticipant->user_event_id = $userEvent->id;
                $userEventParticipant->object = Vendor::class;
                $userEventParticipant->object_id = $vendor;
                $userEventParticipant->save();
            }
        }

        return response()->json([
            'message' => 'Event updated',
            'event' => [
                'id' => $userEvent->id,
                'title' => $userEvent->title,
                'start' => $userEvent->start,
                'end' => $userEvent->end,
            ],
        ]);
    }

    public function GetEditEvent(Request $request, int $id)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json([
                'message' => 'Not allowed',
            ], 401);
        }

        $edit = UserEvent::where('daily_activity_id', $id)->with('attendees')->first();
        if (empty($edit)) {
            return redirect()->back()->with('error', 'Not record found');
        }

        $vendor = UserEventParticipant::where('user_event_id', $edit->id)->pluck('object_id')->toArray();
        $vendors = Vendor::all()->pluck('name', 'id')->toArray();

        return view('dailyplanner.edit-event', compact('edit', 'vendor', 'vendors'));
    }

    public function UpdateEvent(UpdateEventUserEventRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $date = $request->get('date');
        $time = $request->get('time');
        $subject = $request->get('subject');
        $description = $request->get('description');
        $contactsString = $request->get('contacts');

        $start = $date.' '.$time;
        $start = strtotime($start);

        $userEvent = UserEvent::findorFail($request->edit_id);
        $userEvent->subject = $subject;
        $userEvent->description = ($description) ? $description : '';
        $userEvent->date = $date;

        if (isset($time)) {
            $start = strtotime($date.' '.$time);
            $end = strtotime($date.' '.$time.' + 1 hour');
            $userEvent->start = date('Y-m-d H:i:s', $start);
            $userEvent->end = date('Y-m-d H:i:s', $end);
        }

        $userEvent->save();

        $dailyActivities = DailyActivity::findorFail($request->daily_activity_id);
        $dailyActivities->time_slot = date('h:00a', strtotime($userEvent->start)).' - '.date('h:00a', strtotime($userEvent->end));
        $dailyActivities->activity = $userEvent->subject;
        $dailyActivities->for_date = $date;
        $dailyActivities->for_datetime = $date.' '.$time;
        $dailyActivities->save();

        if (request('edit_next_recurring') == '1') {
            $update = [
                'activity' => $userEvent->subject,
                'time_slot' => $dailyActivities->time_slot,
            ];

            $now_str = now()->format('Y-m-d');
            $future_event = DailyActivity::where('parent_row', $request->daily_activity_id)->where('for_date', '>', $now_str)->update($update);
        }

        $vendors = $request->get('vendors', []);
        if (! empty($vendors) && is_array($vendors)) {
            UserEventParticipant::where('user_event_id', $userEvent->id)->delete();
            foreach ($vendors as $vendor) {
                $userEventParticipant = new UserEventParticipant;
                $userEventParticipant->user_event_id = $userEvent->id;
                $userEventParticipant->object = Vendor::class;
                $userEventParticipant->object_id = $vendor;
                $userEventParticipant->save();
            }
        }
        $history = [
            'daily_activities_id' => $request->daily_activity_id,
            'title' => 'Event Edit',
            'description' => 'Event edit by '.Auth::user()->name,
        ];
        DailyActivitiesHistories::insert($history);

        return redirect()->back()->with('success', 'success');
    }

    /**
     * Stop notification
     */
    public function stopEvent(Request $request): JsonResponse
    {
        $id = $request->parent_id;
        if (! $id) {
            return response()->json(
                [
                    'message' => 'Not allowed',
                ],
                401
            );
        }

        try {
            DailyActivity::where('id', $id)->update(['status' => 'stop']);
            DailyActivity::where('parent_row', $id)->where('for_date', '>=', Carbon::now()->toDateTimeString())->delete();
            $history = [
                'daily_activities_id' => $id,
                'title' => 'Event Stop',
                'description' => 'Event Stop by '.Auth::user()->name,
            ];
            DailyActivitiesHistories::insert($history);

            return response()->json([
                'code' => 200,
                'message' => 'Event stop successfully',
            ]);
        } catch (\Throwable $th) {
            $history = [
                'daily_activities_id' => $id,
                'title' => 'Event Stop failed',
                'description' => $th->getMessage(),
            ];
            DailyActivitiesHistories::insert($history);

            return response()->json([
                'code' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Create a new event
     */
    public function createEvent(Request $request): JsonResponse
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

        $date = $request->get('date');
        $time = $request->get('time');
        $subject = $request->get('subject');
        $description = $request->get('description');
        $contactsString = $request->get('contacts');

        $errors = [];

        // date validations
        if (! $date) {
            $errors['date'][] = 'Date is missing';
        } elseif (! preg_match('/^[0-9]{4}-((0[1-9])|(1[0|1|2]))-(0|1|2|3)[0-9]$/', $date)) {
            $errors['date'][] = 'Invalid date format';
        } elseif (! validateDate($date)) {
            $errors['date'][] = 'Invalid date';
        }

        if (isset($time)) {
            if (! preg_match('/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/', $time)) {
                $errors['time'] = 'Invalid time format';
            }
        }

        if (empty(trim($subject))) {
            $errors['subject'][] = 'Subject is required';
        }

        if ($request->type == 'learning' && empty($request->users)) {
            $errors['vendor'][] = 'Please select user';
        }

        if ($request->type == 'learning' && $request->has('users') && (count($request->users) > 1)) {
            $errors['vendor'][] = 'Please select one user';
        }

        if (! empty($errors)) {
            return response()->json($errors, 400);
        }

        $start = $date.' '.$time;
        $for_datetime = $start;
        $start = strtotime($start);

        if ($request->type == 'event') {
            $userEvent = new UserEvent;
            $userEvent->user_id = $userId;
            $userEvent->subject = $subject;
            $userEvent->description = ($description) ? $description : '';
            $userEvent->date = $date;

            if (isset($time)) {
                $start = strtotime($date.' '.$time);
                $end = strtotime($date.' '.$time.' + 1 hour');
                $userEvent->start = date('Y-m-d H:i:s', $start);
                $userEvent->end = date('Y-m-d H:i:s', $end);
            }

            $userEvent->save();

            // once user event has been stored create the event in daily planner
            $dailyActivities = new DailyActivity;
            $dailyActivities->time_slot = date('h:00a', strtotime($userEvent->start)).' - '.date('h:00a', strtotime($userEvent->end));
            $dailyActivities->activity = $userEvent->subject;
            $dailyActivities->user_id = $userId;
            $dailyActivities->for_date = $date;
            $dailyActivities->for_datetime = $for_datetime;
            $dailyActivities->repeat_type = $request->repeat;
            $dailyActivities->repeat_on = $request->repeat_on;
            $dailyActivities->repeat_end = $request->ends_on;
            $dailyActivities->repeat_end_date = $request->repeat_end_date;
            $dailyActivities->timezone = $request->timezone;
            $dailyActivities->type = 'event';
            $dailyActivities->type_table_id = $userEvent->id;

            if ($dailyActivities->save()) {
                $dailyActivities->parent_row = $dailyActivities->id;
                $dailyActivities->save();
                $userEvent->daily_activity_id = $dailyActivities->id;
                $userEvent->save();
            }

            // save the attendees
            $attendees = explode(',', $contactsString);

            $attendeesResponse = [];

            foreach ($attendees as $attendee) {
                $attendeeDb = new UserEventAttendee;
                $attendeeDb->user_event_id = $userEvent->id;
                $attendeeDb->contact = $attendee;
                $attendeeDb->save();

                $attendeesResponse[] = $attendeeDb->toArray();
            }

            $vendors = $request->get('vendors', []);
            if (! empty($vendors) && is_array($vendors)) {
                foreach ($vendors as $vendor) {
                    $userEventParticipant = new UserEventParticipant;
                    $userEventParticipant->user_event_id = $userEvent->id;
                    $userEventParticipant->object = Vendor::class;
                    $userEventParticipant->object_id = $vendor;
                    $userEventParticipant->save();
                }
            }
            $history = [
                'daily_activities_id' => $dailyActivities->id,
                'title' => 'Event create',
                'description' => 'Event created by '.Auth::user()->name,
            ];
            DailyActivitiesHistories::insert($history);

            Log::error('Daily activities ::', DailyActivitiesHistories::where('daily_activities_id', $dailyActivities->id)->get()->toArray());

            return response()->json([
                'code' => 200,
                'message' => 'Event added successfully',
                'event' => $userEvent->toArray(),
                'attendees' => $attendeesResponse,
            ]);
        } else {
            $data['learning_user'] = Auth::id();
            $data['learning_vendor'] = $request->users[0];
            $data['learning_subject'] = $subject;
            $data['learning_assignment'] = $description;
            $data['learning_duedate'] = $request->date;
            $data['cost'] = $request->cost;
            $data['currency'] = $request->currency;

            $learning = Learning::create($data);

            $start = strtotime($date.' '.$time);
            $end = strtotime($date.' '.$time.' + 1 hour');

            $dailyActivities = new DailyActivity;
            $dailyActivities->time_slot = date('h:00a', strtotime($start)).' - '.date('h:00a', strtotime($end));
            $dailyActivities->activity = $learning->subject;
            $dailyActivities->user_id = $userId;
            $dailyActivities->for_date = $date;
            $dailyActivities->for_datetime = $for_datetime;
            $dailyActivities->repeat_type = $request->repeat;
            $dailyActivities->repeat_on = $request->repeat_on;
            $dailyActivities->repeat_end = $request->ends_on;
            $dailyActivities->repeat_end_date = $request->repeat_end_date;
            $dailyActivities->timezone = $request->timezone;
            $dailyActivities->type = 'learning';
            $dailyActivities->type_table_id = $learning->id;

            if ($dailyActivities->save()) {
                $dailyActivities->parent_row = $dailyActivities->id;
                $dailyActivities->save();
            }

            return response()->json([
                'code' => 200,
                'message' => 'Learning added successfully',
            ]);
        }
    }

    public function removeEvent(Request $request, $id): JsonResponse
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

        $result = UserEvent::where('id', $id)->where('user_id', $userId)->first();
        if ($result) {
            $result->delete();

            return response()->json([
                'message' => 'Event deleted:'.$result,
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404,
        ]);
    }

    /**
     * show public calendar
     *
     * @param  mixed  $id
     */
    public function publicCalendar($id): View
    {
        $calendarId = base64_decode($id);
        $calendarUserId = explode(':', $calendarId)[1];
        if (! Carbon::parse(explode(':', $calendarId, 3)[2])->gte(Carbon::now())) {
            abort(404, 'Link expired');
        }
        $user = User::find($calendarUserId, ['name']);

        return view(
            'user-event.public-calendar',
            [
                'calendarId' => $id,
                'user' => $user,
            ]
        );
    }

    /**
     * events of the user without auth
     *
     * @param  mixed  $id
     */
    public function publicEvents(Request $request, $id)
    {
        $text = base64_decode($id);
        $calendarUserId = explode(':', $text)[1];

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $events = UserEvent::with(['attendees'])
            ->where('start', '>=', $start)
            ->where('end', '<', $end)
            ->where('user_id', $calendarUserId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'subject' => $event->subject,
                    'title' => $event->subject,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->start,
                    'end' => $event->end,
                    'attendees' => $event->attendees,
                ];
            });

        return response()->json($events);
    }

    /**
     * suggest timing for the invitation view
     *
     * @param  mixed  $invitationId
     */
    public function suggestInvitationTiming($invitationId): View
    {
        $attendee = UserEventAttendee::with('event')->find($invitationId);

        return view(
            'user-event.public-calendar-time-suggestion',
            [
                'attendee' => $attendee,
                'invitationId' => $invitationId,
            ]
        );
    }

    /**
     * save suggested timing
     *
     * @param  mixed  $invitationId
     */
    public function saveSuggestedInvitationTiming(Request $request, $invitationId): RedirectResponse
    {
        UserEventAttendee::where('id', '=', $invitationId)
            ->update([
                'suggested_time' => $request->get('time'),
            ]);

        return redirect()->to('/calendar/public/event/suggest-time/'.$invitationId)->with([
            'message' => 'Saved data',
        ]);
    }

    public function showCreateEventModal(): JsonResponse
    {
        $vendorsArray = Vendor::pluck('name', 'id')->toArray();
        $users = User::orderBy('name')->get();
        $usersArray = $users->pluck('name', 'id')->toArray();
        $currencyData = [];
        $currencyData = cache()->remember('Currency::all', 60 * 60 * 24 * 7, function () {
            return Currency::all();
        });
        $htmlContent = view('partials.modals.quick-user-event-notification', compact('vendorsArray', 'usersArray', 'currencyData'))->render();

        return response()->json(['html' => $htmlContent], 200);
    }
}
