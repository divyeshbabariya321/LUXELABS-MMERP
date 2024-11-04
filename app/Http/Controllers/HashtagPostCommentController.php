<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\HashtagPostComment;
use Illuminate\Http\Request;

class HashtagPostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
    public function show(HashtagPostComment $hashtagPostComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(HashtagPostComment $hashtagPostComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HashtagPostComment $hashtagPostComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\HashtagPostComment $hashtagPostComment
     * @param mixed                   $id
     */
    public function destroy($id): RedirectResponse
    {
        $hashtagPostComment = HashtagPostComment::find($id);
        if ($hashtagPostComment) {
            $hashtagPostComment->delete();
        }

        return redirect()->back()->with('message', 'Comment Deleted!');
    }
}
