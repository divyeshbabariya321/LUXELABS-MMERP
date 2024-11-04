<?php

namespace App\Http\Controllers;

use App\AutoCommentHistory;
use App\AutoReplyHashtags;
use App\Http\Requests\UpdateAutoReplyHashtagRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoReplyHashtagsController extends Controller
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
     */
    public function store(Request $request): RedirectResponse
    {
        $h = new AutoReplyHashtags();
        $h->text = $request->get('hashtag');
        $h->status = 1;
        $h->save();

        return redirect()->back()->with('action', 'Comment Target hashtag added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @param  mixed  $hashtag
     */
    public function show($hashtag, Request $request): View
    {
        $maxId = [];
        if ($request->has('maxId')) {
            $maxId = $request->get('maxId');
        }

        $country = $request->get('country');
        $hashtags = new Hashtags();
        $hashtags->login();

        $keywords = $request->get('keywords');

        $alltags = $request->get('hashtags');
        $allMedias = [];
        $allCounts = [];
        $maxIds = [];
        $alltagsWithCount = [];

        foreach ($alltags as $tag) {
            $arh = AutoReplyHashtags::where('text', $tag)->first();

            if (! $arh) {
                $arh = new AutoReplyHashtags();
                $arh->text = $tag;
                $arh->type = 'hashtag';
                $arh->save();
            }

            [$medias, $maxId] = $hashtags->getFeed($tag, $maxId[$tag] ?? '', $country, $keywords);
            $media_count = $hashtags->getMediaCount($tag);
            $alltagsWithCount[] = $tag."($media_count)";
            $allCounts[$tag] = $media_count;
            $maxIds[$tag] = $maxId;
            $allMedias = array_merge($allMedias, $medias);
        }

        $countryText = $request->get('country');

        $medias = $allMedias;

        $hashtag = implode(',', $alltagsWithCount);
        $usedPosts = AutoCommentHistory::whereIn('post_id', array_column($medias, 'media_id'))->pluck('post_id')->toArray();

        return view('instagram.auto_comments.prepare', compact('medias', 'media_count', 'maxId', 'hashtag', 'countryText', 'maxIds', 'allCounts', 'alltags', 'usedPosts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @param  mixed  $id
     */
    public function update(UpdateAutoReplyHashtagRequest $request, $id): RedirectResponse
    {

        $medias = $request->get('posts');

        foreach ($medias as $media) {
            $h = new AutoCommentHistory();
            $h->target = $request->get('hashtag_'.$media);
            $h->post_code = $request->get('code_'.$media);
            $h->post_id = $media;
            $h->caption = $request->get('caption_'.$media);
            $h->gender = $request->get('gender_'.$media);
            $h->auto_reply_hashtag_id = 1;
            $h->country = strlen($request->get('country')) > 4 ? $request->get('country') : '';
            $h->status = 0;
            $h->save();

            $caption = $h->caption;
            $caption = str_replace(['#', '@', '!', '-'.'/'], ' ', $caption);
            $caption = explode(' ', $caption);
        }

        return redirect()->back()->with('message', 'Attached successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }
}
