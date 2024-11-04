<?php

namespace App\Http\Controllers;

use App\BrandFans;
use App\GroupMembers;
use App\HashtagPosts;
use App\ScrapInfluencer;
use App\ScrappedFacebookUser;
use App\Services\Facebook\Facebook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Exception;

class FacebookController extends Controller
{
    public function index(): View
    {
        $posts = HashtagPosts::all();

        return view('scrap.facebook', compact('posts'));
    }

    public function show($type): View
    {
        if ($type == 'group') {
            $groups = GroupMembers::all();

            return view('scrap.facebook_groups', compact('groups'));
        }
        if ($type == 'brand') {
            $brands = BrandFans::all();

            return view('scrap.facebook_brand_fans', compact('brands'));
        }
    }

    public function getInbox()
    {
        $facebook = new Facebook(new \Facebook\Facebook);

        return $facebook->getConversations();
    }

    /**
     * @SWG\Post(
     *   path="/facebook/scrape-user",
     *   tags={"Facebook"},
     *   summary="post facebook scrape user",
     *   operationId="post-facebook-scrape-user",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function apiPost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $file = $request->file('file');

        $f = File::get($file);

        $payLoad = json_decode($f);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json',
            ], 400);
        }
        if (is_array($payLoad) && count($payLoad) > 0) {
            $payLoad = json_decode(json_encode($payLoad), true);
            foreach ($payLoad as $postJson) {
                if ($postJson['Owner']) {
                    $inf = ScrappedFacebookUser::where('owner', $postJson['Owner'])->first();
                    if ($inf == null) {
                        $scrapeFacebook = new ScrappedFacebookUser;
                        $scrapeFacebook->url = $postJson['URL'];
                        $scrapeFacebook->owner = $postJson['Owner'];
                        $scrapeFacebook->bio = $postJson['Bio'];
                        if (isset($postJson['keyword'])) {
                            $scrapeFacebook->keyword = $postJson['keyword'];
                        }
                        $scrapeFacebook->save();
                    }
                }
            }
        }

        return response()->json([
            'ok',
        ], 200);
    }

    public function facebookPost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        // Get raw body
        $file = $request->file('file');

        $f = File::get($file);

        $payLoad = json_decode($f);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json',
            ], 400);
        }

        // Process input
        if (is_array($payLoad) && count($payLoad) > 0) {
            $payLoad = json_decode(json_encode($payLoad), true);

            // Loop over posts
            foreach ($payLoad as $postJson) {
                if (! empty($postJson['name'])) {
                    $inf = ScrapInfluencer::where('name', $postJson['name'])->first();
                    if ($inf == null) {
                        $influencer = new ScrapInfluencer;
                        $influencer->name = $postJson['name'];
                        $influencer->url = isset($postJson['url']) ? $postJson['url'] : '';
                        $influencer->country = isset($postJson['Country']) ? $postJson['Country'] : '';
                        $influencer->email = isset($postJson['Email']) ? $postJson['Email'] : '';
                        $influencer->followers = $postJson['followers'];
                        $influencer->following = $postJson['friends'];
                        $influencer->description = $postJson['bio'];

                        $influencer->profile_pic = $postJson['profile pic'];
                        $influencer->friends = $postJson['friends'];
                        $influencer->cover_photo = $postJson['cover photo'];
                        $influencer->interests = implode(',', $postJson['interests']);
                        $influencer->work_at = $postJson['works at'];
                        $influencer->platform = 'Facebook';

                        if (isset($postJson['keyword'])) {
                            $influencer->keyword = $postJson['keyword'];
                        }

                        $influencer->save();

                        try {
                            if (! empty($postJson['Screenshot'])) {
                                if (! $influencer->hasMedia('instagram-screenshot')) {
                                    $media = MediaUploader::fromString(base64_decode($postJson['Screenshot']))
                                        ->toDisk('s3')
                                        ->toDirectory('social-media/instagram-screenshot/'.floor($influencer->id / 1000))
                                        ->useFilename($influencer->id)
                                        ->beforeSave(function (\Plank\Mediable\Media $model, $source) {
                                            $model->setAttribute('extension', 'jpg');
                                        })
                                        ->upload();
                                    $influencer->attachMedia($media, 'instagram-screenshot');
                                }
                            }
                        } catch (Exception $e) {
                            Log::info('instagram influencer page error => ' . $e->getMessage());

                        }
                    }
                } else {
                    //
                }
            }
        }

        // Return
        return response()->json([
            'ok',
        ], 200);
    }
}
