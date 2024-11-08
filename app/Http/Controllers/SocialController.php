<?php

namespace App\Http\Controllers;

use App\AdsSchedules;
use App\Helpers\SocialHelper;
use App\Http\Requests\CreateAdScheduleSocialRequest;
use App\Http\Requests\CreatePostSocialRequest;
use App\Http\Requests\GetCommentsSocialRequest;
use App\Http\Requests\PostCommentSocialRequest;
use App\Http\Requests\StoreAdsetSocialRequest;
use App\Http\Requests\StoreAdSocialRequest;
use App\Http\Requests\StoreCampaignSocialRequest;
use App\Image;
use App\LogRequest;
use App\Models\AdsSchedulesAttachment;
use App\Product;
use App\Setting;
use App\Social\SocialAdHistory;
use App\Social\SocialConfig;
use Exception;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdFields;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class SocialController extends Controller
{
    private $user_access_token;

    private $page_access_token;

    private $page_id;

    private $ad_acc_id;

    public function __construct(private Facebook $fb)
    {
        $this->user_access_token = config('settings.user_access_token');

        $this->page_access_token = config('settings.page_access_token');
        $this->page_id = '107451495586072';
        $this->ad_acc_id = 'act_128125721296439';
    }

    public function getSchedules(Request $request)
    {
        $schedules = AdsSchedules::all();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $p = '';
        if ($request->has('date_from') && $request->has('date_to')) {
            $p = "&time_range={'since':'$request->date_form','until':'$request->date_to'}";
        }

        $query = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/ads?fields=id,name,targeting,status,created_time,adcreatives{thumbnail_url},adset{name},insights{campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend,clicks}&limit=10&access_token='.$this->user_access_token;

        if ($request->has('previous')) {
            $query = $request->get('prev');
        }
        if ($request->has('next')) {
            $query = $request->get('nxt');
        }

        // Call to Graph api here
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $query, 'GET', json_encode([]), json_decode($resp), $httpcode, SocialController::class, 'getSchedules');
        $resp = json_decode($resp);

        $pagination = $resp->paging;
        $previous = $pagination->previous ?? '';
        $next = $pagination->next ?? '';

        $resp = collect($resp->data);

        $ads = $resp->map(function ($item) use ($p) {
            return $this->getAdsFromArray($item, $p);
        });

        return view('social.ad_schedules', compact('ads', 'schedules', 'previous', 'next', 'request'));
    }

    public function createAdSchedule(CreateAdScheduleSocialRequest $request): RedirectResponse
    {

        $ad = new AdsSchedules;
        $ad->name = $request->get('name');
        $ad->scheduled_for = $request->get('date');
        $ad->save();

        return redirect()->action([SocialController::class, 'showSchedule'], $ad->id)->with('message', 'The ad has been scheduled successfully!');
    }

    public function showSchedule($id, Request $request)
    {
        $ad = AdsSchedules::findOrFail($id);

        $images = AdsSchedulesAttachment::where('ads_schedule_id', $ad->id)->get();

        $images = $images->map(function ($item) {
            return [
                'id' => $item->attachment_id,
                'image' => $this->getImagesByType($item->attachment_id, $item->attachment_type),
            ];
        });

        return view('social.schedule', compact('ad', 'images'));
    }

    private function getImagesByType($aid, $type)
    {
        if ($type == 'image') {
            $img = Image::find($aid);

            return '/uploads/social-media/'.$img->filename;
        }

        $pro = Product::find($aid);

        return $pro->imageurl;
    }

    public function attachMedia($id, Request $request): View
    {
        if ($request->has('images')) {
            $images = $request->get('images') ?? [];

            AdsSchedulesAttachment::where('ads_schedule_id', $id)->where('attachment_type', 'image')->delete();

            foreach ($images as $image) {
                AdsSchedulesAttachment::insert([
                    'ads_schedule_id' => $id,
                    'attachment_id' => $image,
                    'attachment_type' => 'image',
                ]);
            }
        }

        $selectedImages = AdsSchedulesAttachment::where('ads_schedule_id', $id)->where('attachment_type', 'image')->get(['attachment_id'])->pluck('attachment_id');
        $images = Image::where('status', 2)->whereNotIn('id', $selectedImages)->get();
        $selectedImages = Image::whereIn('id', $selectedImages)->get();

        return view('social.attach_image', compact('images', 'selectedImages', 'id'));
    }

    public function attachProducts(Request $request, $scheduleId)
    {
        $schedule = AdsSchedules::find($scheduleId);

        if ($request->has('save')) {
            $selectedImages = $request->get('images') ?? [];
            $selectedImages = Product::whereIn('id', $selectedImages)->get();

            foreach ($selectedImages as $selectedImage) {
                AdsSchedulesAttachment::insert([
                    'ads_schedule_id' => $scheduleId,
                    'attachment_id' => $selectedImage->id,
                    'attachment_type' => 'product',
                ]);
            }

            return redirect()->action([SocialController::class, 'showSchedule'], $scheduleId);
        }

        $selectedImages = $request->get('images') ?? [];

        $selectedImages = Product::whereIn('id', $selectedImages)->get();

        $products = Product::whereNotNull('sku')->whereNotIn('id', $selectedImages)->latest()->paginate(40);

        return view('social.attach_products', compact('schedule', 'products', 'selectedImages', 'request'));
    }

    public function getAdInsights()
    {
        $query = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/campaigns?fields=ads{id,name,targeting,status,created_time,adcreatives{thumbnail_url},adset{name},insights.level(adset){campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend,clicks}}&limit=5&access_token='.$this->user_access_token.'';
        // Call to Graph api here
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);

        $resp = collect(json_decode($resp)->data);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $query, 'GET', json_encode([]), json_decode($resp), $httpcode, SocialController::class, 'getSchedules');

        $ads = $resp->map(function ($item) {
            if (isset($item->ads)) {
                return $this->getAdsFromArray($item->ads);
            }
        });

        return response()->json($ads);
    }

    public function getAdSchedules()
    {
        $account = new AdAccount($this->ad_acc_id, null, Api::init($this->fb->getApp()->getId(), $this->fb->getApp()->getSecret(), $this->page_access_token));
        $ads = $account->getAds([
            AdFields::NAME,
            AdFields::UPDATED_TIME,
            AdFields::ADSET_ID,
            AdFields::STATUS,
            AdFields::TARGETING,
            AdFields::PRIORITY,
            AdFields::CREATED_TIME,
            AdFields::CAMPAIGN_ID,
        ]);

        $ads = collect($ads)->map(function ($ad) {
            return [
                'id' => $ad->id,
                'title' => $ad->name,
                'updated_time' => $ad->updated_time,
                'adset_id' => $ad->adset_id,
                'status' => $ad->status,
                'targeting' => $this->getPropertiesAfterFiltration($ad->targeting),
                'priority' => $ad->priority,
                'start' => $ad->created_time,
                'campaign_id' => $ad->campaign_id,
            ];
        });

        return response()->json($ads);
    }

    private function getPropertiesAfterFiltration($properties): array
    {
        $propertiesToReturn = [];
        $genders = [
            '1' => 'Male',
            '2' => 'Female',
        ];

        foreach ($properties as $key => $property) {
            if (! is_array($property)) {
                $propertiesToReturn[$key] = $property;
            }

            if ($key === 'genders') {
                $p = [];
                foreach ($property as $item) {
                    if ($item === 0) {
                        continue;
                    }
                    $p[] = $genders[$item];
                }
                $propertiesToReturn[$key] = implode(', ', $p);
            }
        }

        return $propertiesToReturn;
    }

    public function index(): View
    {
        return view('social.post');
    }

    // public function for getting Social Page posts

    public function pagePost(Request $request)
    {
        if ($request->input('next') && ! empty($request->input('next'))) {
            $data['posts'] = substr($request->input('next'), 32);
            $data['posts'] = $this->fb->get($data['posts'])->getGraphEdge();
        } elseif ($request->input('previous') && ! empty($request->input('previous'))) {
            $data['posts'] = substr($request->input('previous'), 32);
            $data['posts'] = $this->fb->get($data['posts'])->getGraphEdge();
        } else {
            $data['posts'] = $this->fb->get(''.$this->page_id.'/feed?fields=id,full_picture,permalink_url,name,description,message,created_time,from,story,likes.limit(0).summary(true),comments.summary(true).filter(stream)&limit=10&access_token='.$this->page_access_token.'')->getGraphEdge();
        }

        // Making Pagination

        if (isset($data['posts']->getMetaData()['paging']['next']) && ! empty($data['posts']->getMetaData()['paging']['next'])) {
            $data['next'] = $data['posts']->getMetaData()['paging']['next'];
        }

        if (isset($data['posts']->getMetaData()['paging']['previous']) && ! empty($data['posts']->getMetaData()['paging']['previous'])) {
            $data['previous'] = $data['posts']->getMetaData()['paging']['previous'];
        }

        // Getting Final Result as Array
        $data['posts'] = $data['posts']->all();
        $data['posts'] = array_map(function ($post) {
            $post = $post->all();

            return [
                'id' => $post['id'],
                'full_picture' => $post['full_picture'] ?? null,
                'permalink_url' => $post['permalink_url'] ?? null,
                'name' => $post['name'] ?? 'N/A',
                'message' => $post['message'] ?? null,
                'created_time' => $post['created_time'],
                'from' => $post['from'],
                'likes' => [
                    'summary' => $post['likes']->getMetaData()['summary'],
                ],
                'comments' => [
                    'summary' => $post['comments']->getMetaData()['summary'],
                    'items' => implode(',', array_map(function ($item) {
                        return $item['id'];
                    }, $post['comments']->asArray())),
                    'url' => $post['comments']->getParentGraphEdge(),
                ],
            ];
        }, $data['posts']);

        return view('social.get-posts', $data);
    }

    public function getComments(GetCommentsSocialRequest $request)
    {
        $items = explode(',', $request->get('items'));
        $comments = array_map(function ($commmentId) {
            $comment = $this->fb->get($commmentId.'?fields=id,message,from,can_comment&access_token='.$this->page_access_token)->getDecodedBody();

            return $comment;
        }, $items);

        return response()->json($comments);
    }

    public function postComment(PostCommentSocialRequest $request): JsonResponse
    {

        $message = $request->get('message');
        $postId = $request->get('post_id');

        $comment = $this->fb
            ->post($postId.'/comments',
                [
                    'message' => $message,
                    'fields' => 'id,message,from',
                ],
                $this->page_access_token
            )->getDecodedBody();

        $comment['status'] = 'success';

        return response()->json($comment);
    }

    // Creating posts to page via sdk

    public function createPost(CreatePostSocialRequest $request): RedirectResponse
    {

        // Message
        $message = $request->input('message');

        // Image  Case

        if ($request->hasFile('source')) {
            // Description
            $data['caption'] = ($request->input('description')) ? $request->input('description') : '';
            $data['published'] = 'false';
            $data['access_token'] = $this->page_access_token;

            foreach ($request->file('source') as $key => $source) {
                $data['source'] = $this->fb->fileToUpload($source);

                // post multi-photo story
                $multiPhotoPost['attached_media['.$key.']'] = '{"media_fbid":"'.$this->fb->post('/me/photos', $data)->getGraphNode()->asArray()['id'].'"}';
            }

            // Uploading Multi story facebook photo
            $multiPhotoPost['access_token'] = $this->page_access_token;
            $multiPhotoPost['message'] = $message;
            if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                $multiPhotoPost['published'] = 'true';
                $multiPhotoPost['scheduled_publish_time'] = strtotime($request->input('date'));
            }
            $resp = $this->fb->post('/me/feed', $multiPhotoPost)->getGraphNode()->asArray();
            if (isset($resp->error->message)) {
                Session::flash('message', $resp->error->message);
            } else {
                Session::flash('message', 'Content Posted successfully');
            }

            return redirect()->route('social.post.page');
        } // Video Case
        elseif ($request->hasFile('video')) {
            $data['title'] = ''.trim($message).'';

            $data['description'] = ''.trim($request->input('description')).'';

            $data['source'] = $this->fb->videoToUpload(''.trim($request->file('video')).'');

            if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                $data['published'] = 'false';
                $data['scheduled_publish_time'] = strtotime($request->input('date'));
            }
            $resp = $this->fb->post('/me/videos', $data, $this->page_access_token)->getGraphNode()->asArray()['id'];

            if (isset($resp->error->message)) {
                Session::flash('message', $resp->error->message);
            } else {
                Session::flash('message', 'Content Posted successfully');
            }

            return redirect()->route('social.post.page');
        } // Simple Post Case

        else {
            $data['description'] = $request->input('description');
            $data['message'] = $message;
            $data['access_token'] = $this->page_access_token;
            if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                $data['published'] = 'false';
                $data['scheduled_publish_time'] = strtotime($request->input('date'));
            }
            $resp = $this->fb->post('/me/feed', $data)->getGraphNode()->asArray();

            if (isset($resp->error->message)) {
                Session::flash('message', $resp->error->message);
            } else {
                Session::flash('message', 'Content Posted successfully');
            }

            return redirect()->route('social.post.page');
        }
    }

    private function getAdsFromArray($ad, $p)
    {
        return [
            'id' => $ad->id,
            'name' => $ad->name,
            'status' => $ad->status,
            'created_time' => $ad->created_time,
            'adset_name' => $ad->adset->name,
            'adset_id' => $ad->adset->id,
            'ad_creatives' => $this->getAdCreative($ad->adcreatives->data),
            'ad_insights' => $this->getInsights($ad, $p),
            'targeting' => $this->getPropertiesAfterFiltration($ad->targeting),
        ];
    }

    private function getAdCreative($adc)
    {
        return collect($adc)->map(function ($item) {
            return $item->thumbnail_url;
        });
    }

    private function getInsights($ad, $p)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $adId = $ad->id;
        $url = "https://graph.facebook.com/v3.2/$adId/insights?fields=campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend,clicks&access_token=EAAD7Te0j0B8BAJKziYXYZCNZB0i6B9JMBvYULH5kIeH5qm6N9E3DZBoQyZCZC0bxZB4c4Rl5gifAqVa788DRaCWXQ2fNPtKFVnEoKvb5Nm1ufMG5cZCTTzKZAM8qUyaDtT0mmyC0zjhv5S9IJt70tQBpDMRHk9XNYoPTtmBedrvevtPIRPEUKns8feYJMkqHS6EZD".$p;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);

        $resp = json_decode($resp, true); // response deocded
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, SocialController::class, 'getImageByCurl');

        $insights = collect($resp['data']);

        foreach ($insights as $insight) {
            return $insight;
        }
    }

    public function getAdAccount($config, $fb)
    {
        $response = '';

        try {
            $token = $config->token;
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $url = sprintf('https://graph.facebook.com/v15.0//me/adaccounts?access_token='.$token); //New using graph API

            return $response = SocialHelper::httpGetRequest($url);
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
        } catch (FacebookSDKException $e) {
        }
        if ($response != '') {
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                    return $key['id'];
                }
            } catch (Exception $e) {
                $this->socialPostLog($config->id, $config->platform, 'error', 'not get adaccounts id->'.$e->getMessage());
            }
        }
    }

    // Function for Getting Reports via curl
    public function report(Request $request): View
    {
        if ($request->id) {
            $config = SocialConfig::find($request->id);
        }

        $resp = '';
        $socialConfigs = SocialConfig::latest()->paginate(Setting::get('pagination'));

        foreach ($socialConfigs as $config) {
            if (isset($config->ads_manager)) {
                $query = 'https://graph.facebook.com/v16.0/'.$config->ads_manager.'/campaigns?fields=ads{id,name,status,created_time,adcreatives{thumbnail_url},adset{name},insights.level(adset){campaign_name,account_id,reach,impressions,cost_per_unique_click,actions,spend}}&limit=3000&access_token='.$config->token.'';

                // Call to Graph api here
                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $query);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 0);

                $resp = curl_exec($ch);
                $resp = json_decode($resp);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $query, 'GET', json_encode([]), $resp, $httpcode, SocialController::class, 'getImageByCurl');

                curl_close($ch);

                $resp->token = $config->id;

                if (isset($resp->data)) {
                    if (isset($resp->error->error_user_msg)) {
                        Session::flash('message', $resp->error->error_user_msg);
                    } elseif (isset($resp->error->message)) {
                        Session::flash('message', $resp->error->message);
                    }

                    return view('social.reports', ['resp' => $resp]);
                }
            }
        }

        return view('social.reports', ['resp' => $resp]);
    }

    public function reportHistory(Request $request): JsonResponse
    {
        $socialHistoryData = SocialAdHistory::where('ad_ac_id', $request->id)->get();
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.reports-history', compact('socialHistoryData'))->render(),
            ], 200);
        }
    }

    // Get pagination Report()

    public function paginateReport(Request $request)
    {
        if ($request->has('next')) {
            $query = $request->input('next');
        } elseif ($request->has('previous')) {
            $query = $request->input('previous');
        } else {
            return redirect()->route('social.report');
        }

        // Call to Graph api here
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);
        $resp = json_decode($resp); //response decoded
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $query, 'GET', json_encode([]), $resp, $httpcode, SocialController::class, 'paginateReport');
        curl_close($ch);
        if (isset($resp->error->error_user_msg)) {
            Session::flash('message', $resp->error->error_user_msg);
        } elseif (isset($resp->error->message)) {
            Session::flash('message', $resp->error->message);
        }

        return view('social.reports', ['resp' => $resp]);
    }

    // Getting reports for adCreative

    // Function for Getting Reports via curl
    public function adCreativereport(): View
    {
        $resp = '';
        $socialConfigs = SocialConfig::latest()->paginate(Setting::get('pagination'));
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        foreach ($socialConfigs as $config) {
            $query = 'https://graph.facebook.com/v15.0/'.$config->ads_manager.'/campaigns?fields=ads{adcreatives{id,name,thumbnail_url},insights.level(ad).metrics(ctr){cost_per_unique_click,spend,impressions,frequency,reach,unique_clicks,clicks,ctr,ad_name,adset_name,cpc,cpm,cpp,campaign_name,ad_id,adset_id,account_id,account_name}}&access_token='.$config->token.'';

            // Call to Graph api here
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $query);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 0);

            $resp = curl_exec($ch);
            $resp = json_decode($resp);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $query, 'GET', json_encode([]), $resp, $httpcode, SocialController::class, 'adCreativereport');
            curl_close($ch);

            $resp->token = $config->token;

            if ($resp->data) {
                if (isset($resp->error->error_user_msg)) {
                    Session::flash('message', $resp->error->error_user_msg);
                } elseif (isset($resp->error->message)) {
                    Session::flash('message', $resp->error->message);
                }

                return view('social.reports', ['resp' => $resp]);
            }
        }

        return view('social.adcreative-reports', ['resp' => $resp]);
    }
    // end of getting reports via ad creatvie

    // paginate ad creative report
    public function adCreativepaginateReport(Request $request)
    {
        if ($request->has('next')) {
            $query = $request->input('next');
        } elseif ($request->has('previous')) {
            $query = $request->input('previous');
        } else {
            return redirect()->route('social.report');
        }

        // Call to Graph api here
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);
        $resp = json_decode($resp);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $query, 'GET', json_encode([]), $resp, $httpcode, SocialController::class, 'adCreativepaginateReport');
        curl_close($ch);

        if (isset($resp->error->error_user_msg)) {
            Session::flash('message', $resp->error->error_user_msg);
        } elseif (isset($resp->error->message)) {
            Session::flash('message', $resp->error->message);
        }

        return view('social.adcreative-reports', ['resp' => $resp]);
    }

    // end of paginate ad  creative report

    // Changing Ad status via curl
    public function changeAdStatus($ad_id, $status, $config): RedirectResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $config = SocialConfig::find($config);
        $data['access_token'] = $config['token'];
        $data['status'] = $status;

        $url = 'https://graph.facebook.com/v15.0/'.$ad_id;

        // Call to Graph api here
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->post($url, $data);

        $httpcode = $response->status();

        $responseData = $response->json();

        if ($response->failed()) {
            $err = $response->body();
        }

        LogRequest::log($startTime, $url, 'GET', json_encode($data), $responseData, $httpcode, SocialController::class, 'changeAdStatus');

        if (isset($err)) {
            Session::flash('message', $err);
        } else {
            Session::flash('message', 'Status changed successfully');
        }

        return redirect()->route('social.report');
    }

    // Creating New Campaign via curl

    public function createCampaign(): View
    {
        return view('social.campaign');
    }

    // For storing campaign to fb via curl

    public function storeCampaign(StoreCampaignSocialRequest $request): RedirectResponse
    {

        $data['name'] = $request->input('name');
        $data['objective'] = $request->input('objective');
        $data['status'] = $request->input('status');

        if ($request->has('buying_type')) {
            $data['buying_type'] = $request->input('buying_type');
        } else {
            $data['buying_type'] = 'AUCTION';
        }

        if ($request->has('daily_budget')) {
            $data['daily_budget'] = $request->input('daily_budget');
        }

        // Storing to fb via curl

        try {
            $data['access_token'] = $this->user_access_token;

            $url = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/campaigns';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            // Call to Graph api here
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            $resp = json_decode($resp); //response decodeed
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, SocialController::class, 'storeCampaign');
            curl_close($curl);

            if (isset($resp->error->message)) {
                Session::flash('message', $resp->error->message);
            } else {
                Session::flash('message', 'Campaign created  successfully');
            }

            return redirect()->route('social.ad.campaign.create');
        } catch (Exception $e) {
            Session::flash('message', $e);

            return redirect()->route('social.ad.campaign.create');
        }
    }

    // Creating New Campaign via curl

    public function createAdset(): View
    {
        $query = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/campaigns?fields=name,id&limit=100&access_token='.$this->user_access_token.'';

        // Call to Graph api here
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);
        $resp = json_decode($resp); //response decoded
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $parameters = [];
        LogRequest::log($startTime, $query, 'POST', json_encode($parameters), $resp, $httpcode, SocialController::class, 'createAdset');

        curl_close($ch);

        if (isset($resp->error->error_user_msg)) {
            Session::flash('message', $resp->error->error_user_msg);
        } elseif (isset($resp->error->message)) {
            Session::flash('message', $resp->error->message);
        }

        return view('social.adset', ['campaigns' => $resp->data]);
    }

    // For storing adset to fb via curl

    public function storeAdset(StoreAdsetSocialRequest $request): RedirectResponse
    {

        $data['name'] = $request->input('name');
        $data['destination_type'] = $request->input('destination_type');
        $data['campaign_id'] = $request->input('campaign_id');
        $data['billing_event'] = $request->input('billing_event');
        $data['start_time'] = strtotime($request->input('start_time'));
        $data['end_time'] = strtotime($request->input('end_time'));
        $data['targeting'] = json_encode(['geo_locations' => ['countries' => ['US']]]);
        if ($request->has('daily_budget')) {
            $data['daily_budget'] = $request->input('daily_budget');
        }

        $data['status'] = $request->input('status');

        if ($request->has('bid_amount')) {
            $data['bid_amount'] = $request->input('bid_amount');
        }

        // Storing to fb via curl

        try {
            $data['access_token'] = $this->user_access_token;

            $url = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/adsets';

            // Call to Graph api here
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            $resp = json_decode($resp); //response decoded
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, SocialController::class, 'storeAdset');

            curl_close($curl);
            if (isset($resp->error->error_user_msg)) {
                Session::flash('message', $resp->error->error_user_msg);
            } elseif (isset($resp->error->message)) {
                Session::flash('message', $resp->error->message);
            } else {
                Session::flash('message', 'Adset created  successfully');
            }

            return redirect()->route('social.ad.adset.create');
        } catch (Exception $e) {
            Session::flash('message', $e);

            return redirect()->route('social.ad.adset.create');
        }
    }

    // for creating Ad
    public function createAd(): View
    {
        $query = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/?fields=adsets{name,id},adcreatives{id,name}&limit=100&access_token='.$this->user_access_token.'';
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        // Call to Graph api here
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);

        $resp = curl_exec($ch);
        $resp = json_decode($resp);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $query, 'POST', json_encode([]), $resp, $httpcode, SocialController::class, 'createAd');

        curl_close($ch);

        if (isset($resp->error->message)) {
            Session::flash('message', $resp->error->message);
        }

        return view('social.ad', ['adsets' => $resp->adsets->data, 'adcreatives' => $resp->adcreatives->data]);
    }

    // For storing campaign to fb via curl

    public function storeAd(StoreAdSocialRequest $request): RedirectResponse
    {

        $data['name'] = $request->input('name');
        $data['adset_id'] = $request->input('adset_id');
        $data['creative'] = json_encode(['creative_id' => $request->input('adcreative_id')]);

        $data['status'] = $request->input('status');

        // Storing to fb via curl

        try {
            $data['access_token'] = $this->user_access_token;

            $url = 'https://graph.facebook.com/v3.2/'.$this->ad_acc_id.'/ads';
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            // Call to Graph api here
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            $resp = json_decode($resp); //response decoded
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, SocialController::class, 'storeAd');

            curl_close($curl);

            if (isset($resp->error->error_user_msg)) {
                Session::flash('message', $resp->error->error_user_msg);
            } elseif (isset($resp->error->message)) {
                Session::flash('message', $resp->error->error_user_msg);
            } else {
                Session::flash('message', 'Adset created  successfully');
            }

            return redirect()->route('social.ad.create');
        } catch (Exception $e) {
            Session::flash('message', $e);

            return redirect()->route('social.ad.create');
        }
    }
}
