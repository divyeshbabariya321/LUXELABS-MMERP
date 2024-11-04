<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Http\Requests\ShowInstagramAutoCommentRequest;
use App\Http\Requests\StoreInstagramAutoCommentRequest;
use App\InstagramAutoComments;
use App\TargetLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstagramAutoCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *                                   All the comments by updated at and the location that we have saved
     */
    public function index(): View
    {
        $comments = InstagramAutoComments::orderByDesc('updated_at')->get();
        $countries = TargetLocation::all();
        $brands = Brand::all();

        return view('instagram.auto_comments.index', compact('comments', 'countries', 'brands'));
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
     *                                   Create a new comment for a country or gender
     */
    public function store(StoreInstagramAutoCommentRequest $request): RedirectResponse
    {

        $comment = new InstagramAutoComments();
        $comment->comment = $request->get('text');
        $comment->source = $request->get('comment');
        $comment->country = $request->get('country');
        $comment->gender = $request->get('gender');
        $comment->options = $request->get('options') ?? [];
        $comment->save();

        return redirect()->back()->with('message', 'Comment added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @param  mixed  $action
     * @return \Illuminate\Http\Response
     *                                   This will delete the comments given
     */
    public function show($action, ShowInstagramAutoCommentRequest $request): RedirectResponse
    {

        InstagramAutoComments::whereIn('id', $request->get('comments'))->delete();

        return redirect()->back()->with('message', 'Deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     *                                   Edit the Instagrm auto comment resource
     */
    public function edit($id): View
    {
        $comment = InstagramAutoComments::findOrFail($id);
        $brands = Brand::all();

        return view('instagram.auto_comments.edit', compact('comment', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\InstagramAutoComments  $instagramAutoComments
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     *                                   Update the source, text and options
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $comment = InstagramAutoComments::findOrFail($id);
        $comment->comment = $request->get('text');
        $comment->source = $request->get('source');
        $comment->options = $request->get('options') ?? [];
        $comment->save();

        return redirect()->action([InstagramAutoCommentsController::class, 'index'])->with('message', 'Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstagramAutoComments $instagramAutoComments)
    {
        //
    }
}
