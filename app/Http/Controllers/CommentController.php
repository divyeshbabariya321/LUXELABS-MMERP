<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request): RedirectResponse
    {

        $data            = $request->all();
        $data['user_id'] = Auth::id();

        $comment = Comment::create($data);

        return redirect()->back()->with('success', 'Comment added');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted');
    }
}
