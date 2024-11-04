<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facebook\PostCreateRequest;
use App\Http\Requests\Social\EditSocialPostRequest;
use App\Services\CommonGoogleTranslateService;
use App\Services\Facebook\FB;
use App\Setting;
use App\Social\SocialConfig;
use App\Social\SocialPost;
use App\Social\SocialPostLog;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use JanuSoftware\Facebook\Exception\SDKException;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class SocialPostController extends Controller
{
    public function index(Request $request, $id)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $query = SocialPost::where('config_id', $id)->with('account');
            $posts = $query->orderByDesc('id');
        } else {
            $posts = SocialPost::where('config_id', $id)
                ->with('account')
                ->latest()
                ->paginate(Setting::get('pagination'));
        }
        $websites = StoreWebsite::select('id', 'title')->get();

        $posts = $posts->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => $posts->render(),
            ]);
        }

        // Add storewebsites account variable. DEVTASK-24790
        $fetchConfig = SocialConfig::where('id', $id)->first();
        if ($fetchConfig->store_website_id) {
            $socialWebsiteAccount = SocialConfig::where('store_website_id', $fetchConfig->store_website_id)->get();
        }

        return view('social.posts.index', compact('posts', 'websites', 'id', 'socialWebsiteAccount'));
    }

    public function view($id): \Illuminate\View\View
    {
        $post = SocialPost::find($id);

        return view('social.posts.view', compact('post'));
    }

    // Add function to fetch hashtags from Rapid API. DEVTASK-24790
    public function getHashtags()
    {
        $input = $_GET['term'];
        $input = trim($input, '#');

        $rapid_key = config('app.rapid_api_key');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://hashtagy-generate-hashtags.p.rapidapi.com/v1/insta/tags?keyword='.$input.'&include_tags_info=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'X-RapidAPI-Host: hashtagy-generate-hashtags.p.rapidapi.com',
                "X-RapidAPI-Key: $rapid_key",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $final_response = ['not found'];
        if ($err) {
            return false;
        } else {
            $data = json_decode($response, true);
        }
        if (! empty($data['data'])) {
            $list_hashtags = collect($data['data']['hashtags']);
            $final_response = $list_hashtags->pluck('hashtag');
        }

        return response($final_response);
    }

    public function translationapproval(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'config_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $posts = SocialPost::find($request['post_id']);
        $config = SocialConfig::find($posts['config_id']);
        $data = [];
        $data['post_id'] = $request['post_id'];
        $data['caption'] = $posts['caption'];
        $data['hashtag'] = $posts['hashtag'];

        $googleTranslateStichoza = new CommonGoogleTranslateService;
        $target = $config->page_language ? $config->page_language : 'en';
        $data['caption_trans'] = $googleTranslateStichoza->translate($target, $posts['caption']);
        $data['hashtag_trans'] = $googleTranslateStichoza->translate($target, $posts['hashtag']);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    //@todo post approval need to be added
    public function approvepost(Request $request) {}

    public function grid(Request $request)
    {

        $posts = SocialPost::select('social_posts.*')->join('social_configs as sc', 'sc.id', 'social_posts.config_id')->where('social_posts.status', 1);
        if ($request->social_config) {
            $posts = $posts->whereIn('platform', $request->social_config);
        }

        if ($request->store_website_id) {
            // Fix social store website filter not working issue. DEVTASK-24791
            $posts = $posts->join('store_websites as sw', 'sw.id', 'sc.store_website_id')->whereIn('sc.store_website_id', $request->store_website_id);
        }

        // Handle new filters functionality. DEVTASK-24791
        if ($request->config_account) {
            $posts = $posts->whereIn('config_id', $request->config_account);
        }

        if ($request->post_on_date) {
            $dates = explode(' - ', $request->post_on_date);
            $from = date('Y-m-d', strtotime($dates[0]));
            $to = date('Y-m-d', strtotime($dates[1]));

            $posts = $posts->whereBetween('posted_on', [$from, $to]);
        }

        if ($request->caption_hashtags) {
            $posts = $posts->where('caption', 'LIKE', '%'.$request->caption_hashtags.'%')->orWhere('hashtag', 'LIKE', '%'.$request->caption_hashtags.'%');
        }

        $posts = $posts->orderByDesc('social_posts.id')->paginate(Setting::get('pagination'));

        $websites = StoreWebsite::select('id', 'title')->get();
        $socialconfigs = SocialConfig::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string) $posts->render(),
            ], 200);
        }

        return view('social.posts.grid', compact('posts', 'websites', 'socialconfigs'));
    }

    /**
     * @throws SDKException
     * @throws \FbException
     */
    public function deletePost(Request $request): JsonResponse
    {
        $post = SocialPost::where('id', $request['post_id'])->with('account')->first();

        try {
            if ($post->ref_post_id && $post->account->platform === 'facebook') {
                $fb = new FB($post->account);
                $fb->deletePagePost($post->ref_post_id);
            }
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);
        }
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog;
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialPost';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SocialConfig $id): \Illuminate\View\View
    {
        if (isset($id['store_website_id'])) {
            $socialWebsiteAccount = SocialConfig::where('store_website_id', $id['store_website_id'])->get();
        }

        return view('social.posts.create', compact('id', 'socialWebsiteAccount'));
    }

    public function getImage($id): \Illuminate\View\View
    {
        try {
            $config = SocialConfig::find($id);

            $website = StoreWebsite::where('id', $config->store_website_id)->first();
            $media = $website->getMedia('website-image-attach');
        } catch (Exception $e) {

            Session::flash('message', $e);

            Log::error($e);
        }

        return view('social.posts.attach-images', compact('media'));
    }

    /**
     * @todo Video upload is pending
     */
    public function store(PostCreateRequest $request): JsonResponse
    {
        $added_media = [];
        $page_config = SocialConfig::where('id', $request->config_id)->first();
        $hashtagsOfUse = '';
        $googleTranslate = new CommonGoogleTranslateService;
        $target = $page_config->page_language ? $page_config->page_language : 'en';
        if ($request->has('hashtags')) {
            $hashtags = explode('#', $request->input('hashtags'));
            $finalHashtags = [];
            foreach ($hashtags as $key => $hashtagi) {
                if ($hashtagi) {
                    $translationHashtags = $googleTranslate->translate($target, $hashtagi);
                    $finalHashtags[$key] = $translationHashtags;
                }
            }
            $hashtagsOfUse = implode(' #', $finalHashtags);
        }

        $data['message'] = $request->get('message').' '.$hashtagsOfUse;
        if ($request->has('date') && $request->get('date') != null) {
            $data['published'] = false;
            $data['scheduled_publish_time'] = Carbon::parse($request->date)->getTimestamp(); // Fix typo error on date field. DEVTASK-24790
        }
        $translationCaption = $googleTranslate->translate($target, $request->message);
        $post = SocialPost::create([
            'config_id' => $request->config_id,
            'caption' => $request->message,
            'post_body' => $request->message,
            'post_by' => Auth::user()->id,
            'posted_on' => $request->has('date') ? Carbon::parse($request->get('data')) : Carbon::now(),
            'hashtag' => $request->hashtags,
            'post_medium' => 'erp',
            'status' => 2,
            'translated_caption' => $translationCaption,
            'translated_hashtag' => $hashtagsOfUse,
        ]);

        $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Post created in in the database');

        if ($request->has('source')) {
            $files = $request->file('source');

            foreach ($files as $file) {
                $media = MediaUploader::fromSource($file)
                    ->toDirectory('social_images/'.floor($post->id / config('constants.image_per_folder')))
                    ->toDisk('s3_social_media')->upload();
                $post->attachMedia($media, config('constants.media_tags'));
                $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Image uploaded to disk');
                if ($page_config->platform == 'facebook') {
                    [, $added_media] = $this->uploadMediaToFacebook($page_config, $media, $added_media, $post);
                } else {
                    [, $added_media] = $this->uploadMediaToInstagram($page_config, $media, $added_media, $post);
                }
            }

            $data['attached_media'] = $added_media;

            $post->update(['media_file' => $added_media]);
        }

        $fb = new FB($page_config);

        $response = $page_config->platform == 'facebook' ? $fb->addPagePost($page_config->page_id, $data) : $this->postToInstagram($page_config, $data);

        if (isset($response['id'])) {
            $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Facebook post created');
            $post->ref_post_id = $response['id'];
            $post->status = 1;
            $post->save();
            $this->socialPostLog($post->config_id, $post->id, $page_config->platform, 'fb_post', $request->message);
            Session::flash('message', 'Post created successfully');

            // Change to ajax response. DEVTASK-24790
            $request->session()->flash('message', 'Post created successfully');

            return response()->json(['status' => 'Success']);
        } else {
            $post->status = 3;
            $post->save();
            $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Facebook post unsuccessful');
            Session::flash('message', 'Post not created successfully');

            return response()->json(['error' => 'Unable to create post']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EditSocialPostRequest $request): RedirectResponse
    {
        $config = SocialPost::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        $config = SocialPost::findorfail($request->id);
        $config->delete();

        return response()->json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)
            ->where('modal', 'SocialPost')
            ->orderByDesc('created_at')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    /**
     * @throws \Plank\Mediable\Exceptions\MediaUrlException
     */
    public function uploadMediaToFacebook(SocialConfig $page_config, Media $media, array $added_media, SocialPost $post): array
    {
        $pageInfoParams = [
            'endpoint_path' => $page_config->page_id.'/photos',
            'fields' => '',
            'access_token' => $page_config->page_token,
            'request_type' => 'POST',
            'data' => [
                'url' => $media->getUrl(),
                'published' => false,
            ],
        ];

        $response = getFacebookResults($pageInfoParams);
        $added_media[] = ['media_fbid' => $response['data']['id']];

        $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Image uploaded to facebook');

        return [$response, $added_media];
    }

    public function uploadMediaToInstagram(SocialConfig $page_config, Media $media, array $added_media, SocialPost $post): array
    {
        $fb = new FB($page_config);
        $response = $fb->addMediaToInstagram($page_config->account_id, getMediaUrl($media), $post->caption);
        $added_media[] = $response['id'];

        $this->socialPostLog($page_config->id, $post->id, $page_config->platform, 'message', 'Image uploaded to instagram');

        return [$response, $added_media];
    }

    public function postToInstagram(SocialConfig $page_config, array $data)
    {
        $fb = new FB($page_config);
        if (count($data['attached_media']) > 1) {
            $container = $fb->addCarouselMediaToInstagram($page_config->account_id, $data['attached_media'], $data['message']);
            $data = [
                'creation_id' => $container['id'],
            ];
        } else {
            $data = [
                'creation_id' => $data['attached_media'][0],
                'caption' => $data['message'],
            ];
        }

        return $fb->publishMediaPostToInstagram($page_config->account_id, $data);
    }

    // Add this function to handle Preview Post feature. DEVTASK-24790
    public function previewPost(Request $request): JsonResponse
    {
        $html = '';
        if ($request->message) {
            $html .= '<div class="row">';
            $html .= '<div class="col-12 col-md-4">Message: </div>';
            $html .= '<div class="col-12 col-md-8"> '.$request->message.'</div>';
            $html .= '</div>';
        }
        if ($request->date) {
            $html .= '<div class="row">';
            $html .= '<div class="col-12 col-md-4">Post On: </div>';
            $html .= '<div class="col-12 col-md-8"> '.$request->date.'</div>';
            $html .= '</div>';
        }
        if ($request->hashtags) {
            $html .= '<div class="row">';
            $html .= '<div class="col-12 col-md-4">Hashtags: </div>';
            $html .= '<div class="col-12 col-md-8"> '.$request->hashtags.'</div>';
            $html .= '</div>';
        }

        return response()->json(['previewContent' => $html, 'success' => true], 200);
    }

    public function getPostImages($id): \Illuminate\View\View
    {
        $post = SocialPost::where('id', $id)->with('media')->first();

        $images = [];

        foreach ($post?->media as $value) {
            $url = $value->getAbsolutePath();
            array_push($images, $url);
        }

        return view('social.posts.modals.post-images', compact('images'));
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $post = SocialPost::where('id', $id)->first();
        if ($post) {
            $captionScore = app('translation-lambda-helper')->getTranslateScore($post->post_body, $post->translated_caption);
            $post->translated_caption_score = ($captionScore != 0) ? $captionScore : 0.1;
            $post->save();

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong messasge id!']);
        }
    }
}
