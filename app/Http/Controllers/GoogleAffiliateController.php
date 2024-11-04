<?php

namespace App\Http\Controllers;

use App\Affiliates;
use App\Email;
use App\HashTag;
use App\Http\Requests\EmailSendGoogleAffiliateRequest;
use App\Http\Requests\StoreGoogleAffiliateRequest;
use App\Jobs\SendEmail;
use App\LogRequest;
use App\Mails\Manual\AffiliateEmail;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GoogleAffiliateController extends Controller
{
    public $platformsId;

    public function __construct(Request $request)
    {
        $this->platformsId = 3;
    }

    public function index(Request $request): View
    {
        $queryString = '';
        $sortBy = 'hashtag';
        if ($request->input('orderby') == '') {
            $orderBy = 'DESC';
        } else {
            $orderBy = 'ASC';
        }

        if ($request->term || $request->priority) {
            if ($request->term != null && $request->priority == 'on') {
                $keywords = HashTag::query()
                    ->where('priority', '1')
                    ->where('platforms_id', $this->platformsId)
                    ->where('hashtag', 'LIKE', "%{$request->term}%")
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(Setting::get('pagination'));

                $queryString = 'term='.$request->term.'&priority='.$request->priority.'&';
            } elseif ($request->priority == 'on') {
                $keywords = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));

                $queryString = 'priority='.$request->priority.'&';
            } elseif ($request->term != null) {
                $keywords = HashTag::query()
                    ->where('hashtag', 'LIKE', "%{$request->term}%")
                    ->where('platforms_id', $this->platformsId)
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(Setting::get('pagination'));

                $queryString = 'term='.$request->term.'&';
            }
        } else {
            $keywords = HashTag::where('platforms_id', $this->platformsId)->orderBy($sortBy, $orderBy)->paginate(Setting::get('pagination'));
        }

        return view('google.affiliate.index', compact('keywords', 'queryString', 'orderBy'));
    }

    public function store(StoreGoogleAffiliateRequest $request): RedirectResponse
    {

        $hashtag = new HashTag;
        $hashtag->hashtag = $request->get('name');
        $hashtag->rating = $request->get('rating') ?? 8;
        $hashtag->platforms_id = $this->platformsId;
        $hashtag->save();

        return redirect()->back()->with('message', 'Keyword created successfully!');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (is_numeric($id)) {
            $hash = HashTag::findOrFail($id);
            $hash->delete();
        } else {
            HashTag::where('hashtag', $id)->delete();
        }

        return redirect()->back()->with('message', 'Keyword has been deleted successfuly!');
    }

    public function markPriority(Request $request): JsonResponse
    {
        $id = $request->id;
        //check if 30 limit is exceded
        $hashtags = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->get();

        if (count($hashtags) >= 30 && $request->type == 1) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        $hashtag = HashTag::findOrFail($id);
        $hashtag->priority = $request->type;
        $hashtag->update();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getKeywordsApi(): JsonResponse
    {
        $keywords = HashTag::where('priority', 1)->where('platforms_id', $this->platformsId)->get(['hashtag', 'id']);

        return response()->json($keywords);
    }

    public function apiPost(Request $request): JsonResponse
    {
        // Get raw body
        $payLoad = $request->all();

        $payLoad = json_decode(json_encode($payLoad), true);

        // Process input
        if (count($payLoad) == 0) {
            return response()->json([
                'error' => 'Invalid json',
            ], 400);
        } else {
            $postedData = $payLoad['json'];
            // Loop over posts
            foreach ($postedData as $postJson) {
                // Set tag
                $tag = $postJson['searchKeyword'];

                // Get hashtag ID
                $keywords = HashTag::query()
                    ->where('hashtag', 'LIKE', $tag)
                    ->where('platforms_id', $this->platformsId)->first();

                if (is_null($keywords)) {
                    //keyword not in DB. For now skip this...
                } else {
                    // Retrieve instagram post or initiate new
                    $affiliateResults = Affiliates::firstOrNew(['location' => $postJson['URL']]);

                    $affiliateResults->hashtag_id = $keywords->id;
                    $affiliateResults->title = $postJson['title'];
                    $affiliateResults->caption = $postJson['description'];
                    $affiliateResults->posted_at = ($postJson['crawledAt']) ? date('Y-m-d H:i:s', strtotime($postJson['crawledAt'])) : date('Y-m-d H:i:s');
                    $affiliateResults->address = (isset($postJson['address'])) ? $postJson['address'] : '';
                    $affiliateResults->facebook = (isset($postJson['facebook'])) ? $postJson['facebook'] : '';
                    $affiliateResults->instagram = (isset($postJson['instagram'])) ? $postJson['instagram'] : '';
                    $affiliateResults->twitter = (isset($postJson['twitter'])) ? $postJson['twitter'] : '';
                    $affiliateResults->youtube = (isset($postJson['youtube'])) ? $postJson['youtube'] : '';
                    $affiliateResults->linkedin = (isset($postJson['linkedin'])) ? $postJson['linkedin'] : '';
                    $affiliateResults->pinterest = (isset($postJson['pinterest'])) ? $postJson['pinterest'] : '';
                    $affiliateResults->phone = (isset($postJson['phone'])) ? $postJson['phone'] : '';
                    $affiliateResults->emailaddress = (isset($postJson['emailaddress'])) ? $postJson['emailaddress'] : '';
                    $affiliateResults->source = 'google';
                    $affiliateResults->save();
                }
            }
        }

        // Return
        return response()->json([
            'ok',
        ], 200);
    }

    public function searchResults(Request $request): data
    {
        $queryString = '';
        $orderBy = 'DESC';
        if (! empty($request->hashtag)) {
            $queryString .= 'hashtag='.$request->hashtag.'&';
        }
        if (! empty($request->title)) {
            $queryString .= 'title='.$request->title.'&';
        }
        if (! empty($request->post)) {
            $queryString .= 'post='.$request->post.'&';
        }
        if (! empty($request->date)) {
            $queryString .= 'date='.$request->date.'&';
        }
        if (! empty($request->orderby)) {
            $orderBy = $request->orderby;
        }

        // Load posts
        $posts = $this->getFilteredGoogleSearchResults($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google.affiliate.row_results', compact('posts'))->render(),
                'links' => (string) $posts->appends($request->all())->render(),
            ], 200);
        }

        // Return view
        return view('google.affiliate.results', compact('posts', 'request', 'queryString', 'orderBy'));
    }

    private function getFilteredGoogleSearchResults(Request $request): array
    {
        $sortBy = ($request->input('sortby') == '') ? 'posted_at' : $request->input('sortby');
        $orderBy = ($request->input('orderby') == '') ? 'DESC' : $request->input('orderby');

        // Base query
        $affiliateResults = Affiliates::orderBy($sortBy, $orderBy)
            ->join('hash_tags', 'affiliates.hashtag_id', '=', 'hash_tags.id')
            ->select(['affiliates.*', 'hash_tags.hashtag']);

        //Pick google search result from DB
        $affiliateResults->where('source', '=', 'google');

        // Apply hashtag filter
        if (! empty($request->hashtag)) {
            $affiliateResults->where('hash_tags.hashtag', $request->hashtag);
        }

        // Apply location filter
        if (! empty($request->title)) {
            $affiliateResults->where('title', 'LIKE', '%'.$request->title.'%');
        }

        // Apply post filter
        if (! empty($request->post)) {
            $affiliateResults->where('caption', 'LIKE', '%'.$request->post.'%');
        }

        // Apply posted at filter
        if (! empty($request->date)) {
            $affiliateResults->where('posted_at', date('Y-m-d H:i:s', strtotime($request->date)));
        }

        // Return google search results
        return $affiliateResults;
    }

    public function flag(Request $request): JsonResponse
    {
        $affiliates = Affiliates::find($request->id);

        if ($affiliates->is_flagged == 0) {
            $affiliates->is_flagged = 1;
        } else {
            $affiliates->is_flagged = 0;
        }

        $affiliates->save();

        return response()->json(['is_flagged' => $affiliates->is_flagged]);
    }

    public function deleteSearch($id): JsonResponse
    {
        $affiliates = Affiliates::find($id);

        if ($affiliates) {
            $affiliates->delete();
        }

        return response()->json(['message' => 'delete successfully']);
    }

    public function emailSend(EmailSendGoogleAffiliateRequest $request): RedirectResponse
    {
        Log::channel('scraper')->info($request);
        $affiliates = Affiliates::find($request->affiliate_id);
        Log::channel('scraper')->info($affiliates);

        $emailClass = (new AffiliateEmail($request->subject, $request->message))->build();

        $email = Email::create([
            'model_id' => $affiliates->id,
            'model_type' => Affiliates::class,
            'from' => 'affiliate@amourint.com',
            'to' => $affiliates->emailaddress,
            'subject' => $request->subject,
            'message' => $emailClass->render(),
            'template' => 'order-confirmation',
            'additional_data' => '',
            'status' => 'pre-send',
            'store_website_id' => null,
        ]);

        SendEmail::dispatch($email)->onQueue('send_email');

        return redirect()->route('affiliates.index')->withSuccess('You have successfully sent an email!');
    }

    public function callScraper(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $searchKeywords = HashTag::where('id', $id)->get(['hashtag', 'id']);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        if (is_null($searchKeywords)) {
            // Return
            return response()->json([
                'error' => 'Keyword not found',
            ], 400);
        } else {
            $postData = ['data' => $searchKeywords];
            $url = config('settings.node_scraper_server').'api/googleSearchDetails';
            $response = Http::post($url, $postData);

            $responseData = $response->json();

            LogRequest::log($startTime, $url, 'POST', json_encode($postData), $responseData, $response->status(), GoogleAffiliateController::class, 'callScraper');

            // Return
            return response()->json([
                'success - scrapping initiated',
            ], 200);
        }
    }
}
