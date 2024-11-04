<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSocialTagRequest;
use App\Http\Requests\StoreSocialTagRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\SocialTags;
use Illuminate\Http\Request;
use App\Services\Facebook\Facebook;

class SocialTagsController extends Controller
{
    public function __construct(private Facebook $facebook)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tags = SocialTags::all();

        return view('socialtags.index', compact('tags'));
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
     */
    public function store(StoreSocialTagRequest $request): RedirectResponse
    {

        $tag       = new SocialTags();
        $tag->name = $request->get('name');
        $tag->save();

        return redirect()->back()->with('message', 'Tag added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $tag = SocialTags::findOrFail($id);

        $data = $this->facebook->getMentions($tag);

        dd($data);

        return view('socialtags.scraped_images', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $tag = SocialTags::findOrFail($id);

        return view('socialtags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSocialTagRequest $request, int $id): RedirectResponse
    {

        $tag       = SocialTags::findOrFail($id);
        $tag->name = $request->get('name');
        $tag->save();

        return redirect()->back()->with('message', 'Tag updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $tag = SocialTags::findOrFail($id);
        $tag->delete();

        return redirect()->back()->with('message', 'Tag deleted successfully!');
    }
}
