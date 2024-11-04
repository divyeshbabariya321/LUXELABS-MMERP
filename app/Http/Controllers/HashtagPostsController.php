<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\HashtagPosts;
use Illuminate\Http\Request;

class HashtagPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(HashtagPosts $hashtagPosts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(HashtagPosts $hashtagPosts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HashtagPosts $hashtagPosts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\HashtagPosts $hashtagPosts
     * @param mixed             $id
     */
    public function destroy($id): RedirectResponse
    {
        $hashtagPosts = HashtagPosts::find($id);
        if ($hashtagPosts) {
            $hashtagPosts->delete();
        }

        return redirect()->back()->with('message', 'Post Deleted!');
    }
}
