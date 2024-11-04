<?php

namespace App\Http\Controllers;

use App\UsersFeedbackHrTicket;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersFeedbackHrTicketController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $task = new UsersFeedbackHrTicket;
            $task->user_id = Auth::user()->id ?? '';
            $task->feedback_cat_id = $request->feedback_cat_id;
            $task->task_subject = $request->task_subject;
            $task->task_type = $request->task_type;
            $task->repository_id = $request->repository_id;
            $task->task_detail = $request->task_detail;
            $task->cost = $request->cost;
            $task->task_asssigned_to = $request->task_asssigned_to;
            $task->status = 'In progress';
            $task->save();

            return response()->json(['code' => '200', 'data' => $task, 'message' => 'Data saved successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UsersFeedbackHrTicket  $usersFeedbackHrTicket
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $task = UsersFeedbackHrTicket::select('users_feedback_hr_tickets.*', 'users_feedback_hr_tickets.task_subject as subject', 'users.name as assigned_to_name')
                ->join('users', 'users.id', 'users_feedback_hr_tickets.task_asssigned_to')
                ->where('feedback_cat_id', $request->id)->get();

            return response()->json(['code' => '200', 'data' => $task, 'message' => 'Ticket Details listed successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }
}
