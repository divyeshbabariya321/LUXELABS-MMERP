<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\User;
use App\TargetLocation;
use App\Models\UsersAutoCommentHistory;
use App\AutoReplyHashtags;
use App\AutoCommentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoCommentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *                                   get comments, hashtags and country list for commenting in bulk
     */
    public function index(Request $request): View
    {
        $comments  = AutoCommentHistory::orderByDesc('created_at');
        $hashtags  = AutoReplyHashtags::all();
        $countries = TargetLocation::all();

        if ($request->get('verified')) {
            $comments = $comments->where('is_verified', $request->get('verified') == 1 ? 1 : 0);
        }

        if ($request->get('posted')) {
            $comments = $comments->where('status', $request->get('posted') == 1 ? 1 : 0);
        }
        if ($request->get('assigned') == 1) {
            $comments = $comments->whereHas('user');
        }
        if ($request->get('assigned') == 2) {
            $comments = $comments->whereDoesntHave('user');
        }

        if ($request->get('user_id') > 0) {
            $comments = $comments->whereIn('id', UsersAutoCommentHistory::where('user_id', $request->get(user_id))->pluck('auto_comment_history_id')->toArray());
        }

        $comments = $comments->paginate(50);

        //verified, posted, assigned
        $statsByCountry = AutoCommentHistory::selectRaw('country, COUNT("*") AS total')->groupBy(['country'])->get();
        $statsByHashtag = AutoCommentHistory::selectRaw('target, COUNT("*") AS total')->groupBy(['target'])->get();

        $users = User::all();

        return view('instagram.auto_comments.report', compact('comments', 'hashtags', 'countries', 'statsByCountry', 'statsByHashtag', 'request', 'users'));
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
     * @return \Illuminate\Http\Response
     */
    public function show(AutoCommentHistory $autoCommentHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\AutoCommentHistory $autoCommentHistory
     * @param mixed                   $id
     */
    public function edit($id): JsonResponse
    {
        $comment              = AutoCommentHistory::find($id);
        $comment->is_verified = 1;
        $comment->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AutoCommentHistory $autoCommentHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoCommentHistory $autoCommentHistory)
    {
        //
    }
}
