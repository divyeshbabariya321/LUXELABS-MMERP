<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChanelYoutubeRequest;
use App\Http\Requests\UpdateChannelYoutubeRequest;
use App\Http\Requests\YoutubeUploadVideoRequest;
use App\Jobs\FetchYoutubeChannelData;
use App\Library\Youtube\Helper;
use App\Models\StoreWebsiteYoutube;
use App\Models\YoutubeChannel;
use App\Models\YoutubeComment;
use App\Models\YoutubeVideo;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

use function Sentry\captureException;

class YoutubeController extends Controller
{
    /*
    * used to get google refresh token for ads
    */
    public function refreshToken(Request $request): RedirectResponse
    {
        $client_id = $request->client_id;
        $client_secret = $request->client_secret;
        Session::put('client_id', $client_id);
        Session::put('client_secret', $client_secret);
        Session::save();

        $scopes = [
            'Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl',
            'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit',
            'Youtube3' => 'https://www.googleapis.com/auth/youtube.upload',
        ];

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('youtube.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => route('youtubeaccount.get-refresh-token'),
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $client_id,
                'clientSecret' => $client_secret,
                'scope' => $scopes,
            ]
        );

        $authUrl = $oauth2->buildFullAuthorizationUri([
            'prompt' => 'consent',
        ]);

        $authUrl = filter_var($authUrl, FILTER_SANITIZE_URL);

        return redirect()->away($authUrl);
    }

    public function viewUploadVideo(Request $request, $id): View
    {
        $chaneltableData = YoutubeChannel::where('id', $id)->firstOrFail();
        $chanelTableId = $chaneltableData->id;
        $categoriesData = Helper::getVideoCategories();

        return view('youtube.chanel.video.create', compact('chanelTableId', 'categoriesData'));
    }

    public function youtubeRedirect(Request $request)
    {
        return Socialite::driver('youtube')->with(['state' => $request->id, 'access_type' => 'offline', 'prompt' => 'consent select_account', 'scope' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit', 'scope' => 'https://www.googleapis.com/auth/youtube.force-ssl'])->redirect();
    }

    public function updateYoutubeAccessToken($websiteId)
    {
        try {
            $websiteData = StoreWebsiteYoutube::where('store_website_id', $websiteId)->first();

            $params = [
                'refresh_token' => $websiteData->refresh_token,
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'grant_type' => 'refresh_token',
            ];
            $headers = [
                'Host' => 'oauth2.googleapis.com',
            ];

            $response = Http::withHeaders($headers)->post('https://oauth2.googleapis.com/token', $params)->json();
            $websiteData->access_token = $response['access_token'];
            $expireIn = ! empty($response['expires_in']) ? $response['expires_in'] : null;
            if (! empty($expireIn)) {
                $currentTime = strtotime(Carbon::now());
                $expireIn = Carbon::createFromTimestamp(($currentTime + $expireIn));
            }
            $websiteData->token_expire_time = $expireIn;
            $websiteData->save();
        } catch (Exception $e) {
            captureException($e);
            Log::info(__('failedToUpdateUserAccessToken', [$websiteData]));
            Log::info($e->getMessage());
        }
    }

    public function uploadVideo(YoutubeUploadVideoRequest $request): RedirectResponse
    {
        try {
            // dd($request->all());
            if (! isset($request->youtubeVideo)) {
                return redirect()->to('/youtube/add-chanel')->with('actError', 'Video is required');
            }

            $chaneltableData = YoutubeChannel::where('id', $request->tableChannelId)->firstOrFail();
            Helper::regenerateToken($chaneltableData->id);
            $accessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
            if (empty($accessToken)) {
                return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
            }

            $client = new Google_Client();
            $client->setApplicationName('Youtube Upload video');
            $client->setScopes([
                'https://www.googleapis.com/auth/youtube.upload',
            ]);
            $client->setAccessToken($accessToken);

            $client->setAccessType('offline');

            $service = new Google_Service_YouTube($client);

            $video = new Google_Service_YouTube_Video();
            $videoSnippet = new Google_Service_YouTube_VideoSnippet();

            $videoSnippet->setCategoryId($request->videoCategories);
            $videoSnippet->setDescription($request->description);
            $videoSnippet->setTitle($request->title);
            $videoSnippet->setPublishedAt(now());
            $video->setSnippet($videoSnippet);

            // Add 'status' object to the $video object.
            $videoStatus = new Google_Service_YouTube_VideoStatus();
            $videoStatus->setPrivacyStatus($request->status);
            $video->setStatus($videoStatus);
            $part = ['snippet', 'status'];
            $file = $request->file('youtubeVideo');

            // Read file contents as a string
            $fileContents = file_get_contents($file->getPathname());
            $response = $service->videos->insert(
                ['snippet', 'status'],
                $video,
                [
                    'data' => $fileContents,
                    'mimeType' => 'application/octet-stream',
                    'uploadType' => 'multipart',
                ]
            );

            Log::info('videoResponse');
            Log::info(json_encode($response));

            // $newAccessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);

            if (! empty($response['id'])) {
                if (! empty($chaneltableData->oauth2_refresh_token)) {
                    $accessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
                    if (! empty($accessToken)) {
                        Helper::getVideoAndInsertDB($chaneltableData->id, $accessToken, $chaneltableData->chanelId);
                    }
                }

                return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Upload Video Successfully!');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }

    public function storeChanel(StoreChanelYoutubeRequest $request): RedirectResponse
    {
        //create account

        // dd($request->all());
        try {
            $createChannel = new YoutubeChannel();
            $createChannel->store_websites = $request->store_websites;
            $createChannel->status = $request->status;
            $createChannel->email = $request->email;
            $createChannel->oauth2_client_secret = $request->oauth2_client_secret;
            $createChannel->oauth2_client_id = $request->oauth2_client_id;
            $createChannel->oauth2_refresh_token = $request->oauth2_refresh_token;

            $createChannel->save();

            FetchYoutubeChannelData::dispatch($createChannel);

            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Youtube Channel added successfully');
        } catch (Exception $e) {
            // captureException($e);
            Log::info($e->getMessage());

            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }

    /*
    * Refresh token Redirect API
    */
    public function getRefreshToken(Request $request)
    {
        $google_redirect_url = route('youtubeaccount.get-refresh-token');
        $scopes = ['Youtube1' => 'https://www.googleapis.com/auth/youtube.force-ssl', 'Youtube2' => 'https://www.googleapis.com/auth/youtubepartner-channel-audit'];
        $oauth2 = new OAuth2(
            [
                'authorizationUri' => config('google.GOOGLE_ADS_AUTHORIZATION_URI'),
                'redirectUri' => $google_redirect_url,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => Session::get('client_id'),
                'clientSecret' => Session::get('client_secret'),
                'scope' => $scopes,
            ]
        );
        if ($request->code) {
            $code = $request->code;
            $oauth2->setCode($code);
            $authToken = $oauth2->fetchAuthToken();
            Session::forget('client_secret');
            Session::forget('client_id');

            return view('youtube.chanel.view_token', ['refresh_token' => $authToken['refresh_token'], 'access_token' => $authToken['access_token']]);
        } else {
            return redirect()->to('/youtube/add-chanel')->with('message', 'Unable to Get Tokens ');
        }
    }

    public function createChanel(Request $request)
    {
        // Create Chanel Means Get Chanel Data  using refresh Token.
        $query = YoutubeChannel::query();
        $query = $query->when($request->website, fn ($q) => $q->where('store_websites', $request->website));
        $query = $query->when($request->accountname, fn (Builder $q) => $q->whereLike('store_websites', $request->accountname));

        $googleadsaccount = $query->orderby('id', 'desc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('youtube.chanel.filter-channel', compact('googleadsaccount'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $googleadsaccount->render(),
                'count' => $googleadsaccount->total(),
            ]);
        }

        $store_website = StoreWebsite::all();
        $totalentries = $googleadsaccount->count();

        return view('youtube.chanel.chanel-create', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries, 'store_website' => $store_website]);
    }

    public function CommentByVideoId(Request $request, $videoId): View
    {
        $commentsList = YoutubeComment::where('video_id', $videoId)->paginate(10)->appends($request->except(['page']));

        return view('youtube.chanel.comment.comment-list', compact('commentsList'));
    }

    public function editChannel($id)
    {
        return YoutubeChannel::findOrFail($id);
    }

    public function updateChannel(UpdateChannelYoutubeRequest $request): RedirectResponse
    {
        $account_id = $request->account_id;

        try {
            $input = $request->all();

            $googleadsAcQuery = new YoutubeChannel();
            $googleadsAc = $googleadsAcQuery->find($account_id);
            $googleadsAc->fill($input);
            $googleadsAc->save();

            return redirect()->to('/youtube/add-chanel')->with('actSuccess', 'Channel updated successfully');
        } catch (Exception $e) {
            captureException($e);

            return redirect()->to('/youtube/add-chanel')->with('actError', $e->getMessage());
        }
    }

    public function listVideo(Request $request, $youtubeChannelTableId)
    {
        $chaneltableData = YoutubeChannel::where('id', $youtubeChannelTableId)->first();

        if (empty($chaneltableData)) {
            return redirect()->to('/youtube/add-chanel')->with('actError', 'Something Went Wromg');
        }
        if (! empty($chaneltableData->oauth2_refresh_token)) {
            $newAccessToken = Helper::getAccessTokenFromRefreshToken($chaneltableData->oauth2_refresh_token, $chaneltableData->id);
            if (! empty($newAccessToken)) {
                Helper::getVideoAndInsertDB($chaneltableData->id, $newAccessToken, $chaneltableData->chanelId);
            }
        }
        $videoList = YoutubeVideo::where('channel_id', $chaneltableData->chanelId)->paginate(5)->appends(request()->except(['page']));

        return view('youtube.chanel.video.video-list', compact('videoList'));
    }
}
