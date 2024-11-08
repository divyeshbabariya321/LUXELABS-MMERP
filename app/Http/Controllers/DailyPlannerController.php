<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\DailyActivitiesHistories;
use App\DailyActivity;
use App\GeneralCategory;
use App\Helpers;
use App\Http\Requests\SendScheduleDailyPlannerRequest;
use App\Instruction;
use App\Task;
use App\User;
use App\UserEvent\UserEvent;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyPlannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userid = $request->user_id ?? Auth::id();
        $planned_at = $request->planned_at ?? Carbon::now()->format('Y-m-d');

        $tasks = Task::where('is_statutory', '=', 0)
            ->where(function ($query) use ($userid) {
                return $query->orWhere('assign_from', '=', $userid)
                    ->orWhere('assign_to', '=', $userid);
            })
            ->oldest()->get();

        $planned_tasks = Task::whereNotNull('time_slot')->where('planned_at', $planned_at)->where(function ($query) use ($userid) {
            return $query->orWhere('assign_from', '=', $userid)
                ->orWhere('assign_to', '=', $userid);
        })->orderBy('time_slot')->get()->groupBy('time_slot');

        $statutory = Task::where(function ($query) use ($userid) {
            return $query->whereRaw("tasks.id IN (SELECT task_id FROM task_users WHERE user_id = $userid)")->orWhere('assign_from', '=', $userid)
                ->orWhere('assign_to', '=', $userid);
        })->where('is_statutory', 1)->whereNull('is_verified')->count();

        $dailyActivities = DailyActivity::where('user_id', $userid)->where('for_date', $planned_at)->get()->groupBy('time_slot');
        $userEvents = UserEvent::where('date', $planned_at)->orderBy('start')->get();

        $time_slots = [
            '08:00am - 09:00am' => [],
            '09:00am - 10:00am' => [],
            '10:00am - 11:00am' => [],
            '11:00am - 12:00pm' => [],
            '12:00pm - 01:00pm' => [],
            '01:00pm - 02:00pm' => [],
            '02:00pm - 03:00pm' => [],
            '03:00pm - 04:00pm' => [],
            '04:00pm - 05:00pm' => [],
            '05:00pm - 06:00pm' => [],
            '06:00pm - 07:00pm' => [],
            '07:00pm - 08:00pm' => [],
            '08:00pm - 09:00pm' => [],
            '09:00pm - 10:00pm' => [],
        ];

        if ($statutory > 0) {
            $task = new Task;
            $task->task_subject = "Complete $statutory statutory tasks today";
            $task->is_completed = Carbon::now();
            $time_slots['08:00am - 09:00am'][] = $task;
            $time_slots['09:00am - 10:00am'][] = $task;
        }

        foreach ($planned_tasks as $time_slot => $data) {
            foreach ($data as $task) {
                $time_slots[$time_slot][] = $task;
            }
        }

        foreach ($dailyActivities as $time_slot => $data) {
            foreach ($data as $task) {
                $time_slots[$time_slot][] = $task;
            }
        }

        $call_instructions = Instruction::select(['id', 'category_id', 'instruction', 'assigned_to', 'created_at'])->where('category_id', 10)->where('created_at', 'LIKE', "%$planned_at%")->where('assigned_to', $userid)->get();
        $users_array = Helpers::getUserArray(User::all());

        $generalCategories = GeneralCategory::all()->pluck('name', 'id')->toArray();

        // start calulation of all time spent
        $taskCategoryWise = Task::where('actual_start_date', '!=', '')
            ->where('is_completed', '!=', '')
            ->where('general_category_id', '!=', '')
            ->select([DB::raw('sum(TIMESTAMPDIFF(MINUTE,actual_start_date, is_completed)) as spent_time'), 'general_category_id', 'is_completed', 'actual_start_date'])
            ->groupBy('general_category_id')->get()->pluck('spent_time', 'general_category_id')->toArray();

        $activitiesCategoryWise = DailyActivity::where('actual_start_date', '!=', '')
            ->where('is_completed', '!=', '')
            ->where('general_category_id', '!=', '')
            ->select([DB::raw('sum(TIMESTAMPDIFF(MINUTE, actual_start_date, is_completed)) as spent_time'), 'general_category_id', 'is_completed', 'actual_start_date'])
            ->groupBy('general_category_id')->get()->pluck('spent_time', 'general_category_id')->toArray();

        $spentTime = [];
        if (! empty($generalCategories)) {
            foreach ($generalCategories as $id => $name) {
                if (! isset($spentTime[$id])) {
                    $spentTime[$id] = 0;
                }
                $spentTime[$id] += isset($taskCategoryWise[$id]) ? $taskCategoryWise[$id] : 0;
                $spentTime[$id] += isset($activitiesCategoryWise[$id]) ? $activitiesCategoryWise[$id] : 0;
            }
        }

        return view('dailyplanner.index', [
            'tasks' => $tasks,
            'time_slots' => $time_slots,
            'users_array' => $users_array,
            'call_instructions' => $call_instructions,
            'userid' => $userid,
            'planned_at' => $planned_at,
            'generalCategories' => $generalCategories,
            'spentTime' => $spentTime,
            'meetings' => $userEvents,
        ]);
    }

    /**
     * Show the planner history.
     */
    public function history(Request $request): JsonResponse
    {
        if ($request->id) {
            $history = DailyActivitiesHistories::where('daily_activities_id', $request->id)->orderByDesc('created_at')->get();
            $html = view('partials.history_table_rows', ['history' => $history])->render();

            return response()->json(['code' => 200, 'data' => $history, 'html' => $html]);
        }
    }

    /**
     * Send schedule to User.
     */
    public function sendSchedule(SendScheduleDailyPlannerRequest $request): RedirectResponse
    {
        try {
            $events = UserEvent::where('user_id', $request->user)->whereDate('date', $request->date)->get();

            $userWise = [];
            $vendorParticipants = [];
            if (! $events->isEmpty()) {
                foreach ($events as $event) {
                    $userWise[$event->user_id][] = $event;
                    $participants = $event->attendees;
                    if (! $participants->isEmpty()) {
                        foreach ($participants as $participant) {
                            if ($participant->object == Vendor::class) {
                                $vendorParticipants[$participant->object_id] = $event;
                            }
                        }
                    }
                }
            }

            if (! empty($userWise)) {
                foreach ($userWise as $id => $events) {
                    // find user into database
                    $user = User::find($id);
                    // if user exist
                    if (! empty($user)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on today';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;

                            $history = [
                                'daily_activities_id' => $event->daily_activity_id,
                                'title' => 'Sent notification',
                                'description' => 'To '.$user->name,
                            ];
                            DailyActivitiesHistories::insert($history);
                        }

                        $params['user_id'] = $user->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], null, $chat_message->id);
                    }
                }
            }

            if (! empty($vendorParticipants)) {
                foreach ($vendorParticipants as $id => $vendorParticipant) {
                    $vendor = Vendor::find($id);
                    if (! empty($vendor)) {
                        $notification = [];
                        $notification[] = 'Following Event Schedule on today';
                        $no = 1;
                        foreach ($events as $event) {
                            $notification[] = $no.') ['.$event->start.'] => '.$event->subject;
                            $no++;

                            $history = [
                                'daily_activities_id' => $event->daily_activity_id,
                                'title' => 'Sent notification',
                                'description' => 'To '.$vendor->name,
                            ];
                            DailyActivitiesHistories::insert($history);
                        }

                        $params['vendor_id'] = $vendor->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = ChatMessage::create($params);
                        // send
                        app(WhatsAppController::class)
                            ->sendWithThirdApi($vendor->phone, $vendor->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            return redirect()->back()->with('success', 'success');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Resend notification.
     */
    public function resendNotification(Request $request): JsonResponse
    {
        if ($request->id) {
            try {
                $events = UserEvent::where('daily_activity_id', $request->id)->get();
                $dailyActivities = DailyActivity::where('id', $request->id)->first();
                $userWise = [];
                $vendorParticipants = [];

                if (! $events->isEmpty()) {
                    foreach ($events as $event) {
                        $userWise[$event->user_id][] = $event;
                        $participants = $event->attendees;
                        if (! $participants->isEmpty()) {
                            foreach ($participants as $participant) {
                                if ($participant->object == Vendor::class) {
                                    $vendorParticipants[$participant->object_id] = $event;
                                }
                            }
                        }
                    }
                } else {
                    return response()->json(['code' => 500, 'data' => 'No data found']);
                }

                if (! empty($userWise)) {
                    foreach ($userWise as $id => $events) {
                        // find user into database
                        $user = User::find($id);
                        // if user exist
                        if (! empty($user)) {
                            $notification = [];
                            $notification[] = 'Following Event Schedule on within the next 30 min';
                            $no = 1;

                            foreach ($events as $event) {
                                $notification[] = $no.') ['.changeTimeZone($dailyActivities->for_datetime, null, $dailyActivities->timezone).'] => '.$event->subject;
                                $no++;

                                $history = [
                                    'daily_activities_id' => $event->daily_activity_id,
                                    'title' => 'Sent notification',
                                    'description' => 'To '.$user->name,
                                ];
                                DailyActivitiesHistories::insert($history);
                            }

                            $params['user_id'] = $user->id;
                            $params['message'] = implode("\n", $notification);
                            // send chat message
                            $chat_message = ChatMessage::create($params);
                            // send
                            app(WhatsAppController::class)
                                ->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                        }
                    }
                }

                if (! empty($vendorParticipants)) {
                    foreach ($vendorParticipants as $id => $vendorParticipant) {
                        $vendor = Vendor::find($id);
                        if (! empty($vendor)) {
                            $notification = [];
                            $notification[] = 'Following Event Schedule on within the next 30 min';
                            $no = 1;
                            foreach ($events as $event) {
                                $notification[] = $no.') ['.changeTimeZone($dailyActivities->for_datetime, null, $dailyActivities->timezone).'] => '.$event->subject;
                                $no++;
                                $history = [
                                    'daily_activities_id' => $event->daily_activity_id,
                                    'title' => 'Sent notification',
                                    'description' => 'To '.$vendor->name,
                                ];
                                DailyActivitiesHistories::insert($history);
                            }

                            $params['vendor_id'] = $vendor->id;
                            $params['message'] = implode("\n", $notification);
                            // send chat message
                            $chat_message = ChatMessage::create($params);
                            // send
                            app(WhatsAppController::class)
                                ->sendWithThirdApi($vendor->phone, $vendor->whatsapp_number, $params['message'], false, $chat_message->id);
                        }
                    }
                }

                return response()->json(['code' => 200, 'data' => 'Successfully sent']);
            } catch (\Throwable $th) {
                return response()->json(['code' => 500, 'data' => $th->getMessage()]);
            }
        }
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

    public function complete(Request $request): RedirectResponse
    {
        $user = User::find(Auth::id());
        $user->is_planner_completed = 1;
        $user->save();

        return redirect()->to('/task')->withSuccess('You have successfully completed your daily plan!');
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

    public function reschedule(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $id = $request->get('id');
        $plannedAt = $request->get('planned_at');

        if ($type == 'task') {
            $modal = Task::find($id);
        } else {
            $modal = DailyActivity::find($id);
        }

        if (! empty($modal)) {
            if ($type == 'task') {
                $modal->planned_at = $plannedAt;
            } else {
                $time = date('H:i:s', strtotime($modal->for_datetime));

                $modal->for_date = $plannedAt;
                $modal->for_datetime = $plannedAt.' '.$time;
            }
            $modal->save();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Your task has been rescheduled']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Oops, somethign went wrong while rescheduling task']);
    }
}
