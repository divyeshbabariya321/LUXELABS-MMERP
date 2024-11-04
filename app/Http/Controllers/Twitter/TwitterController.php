<?php

namespace App\Http\Controllers\Twitter;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Twitter\TweetTwitterRequest;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Twitter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TwitterController extends Controller
{
    public function twitterUserTimeLine(): View
    {
        $data = Twitter::getUserTimeline(['count' => 5, 'format' => 'array']);

        return view('twitter.twitter', compact('data'));
    }

    public function tweet(TweetTwitterRequest $request): RedirectResponse
    {

        $newTwitte = ['status' => $request->tweet];

        if (! empty($request->images)) {
            foreach ($request->images as $key => $value) {
                $uploaded_media = Twitter::uploadMedia(['media' => File::get($value->getRealPath())]);
                if (! empty($uploaded_media)) {
                    $newTwitte['media_ids'][$uploaded_media->media_id_string] = $uploaded_media->media_id_string;
                }
            }
        }

        $twitter = Twitter::postTweet($newTwitte);

        return redirect()->back();
    }
}
