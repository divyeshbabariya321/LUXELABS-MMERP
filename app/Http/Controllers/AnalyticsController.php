<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use App\Setting;
use App\Analytics;
use App\LinksToPost;
use App\ArticleCategory;
use App\AnalyticsSummary;
use App\GoogleAnalyticData;
use Illuminate\Http\Request;
use App\StoreWebsiteAnalytic;
use App\GoogleAnalyticsHistories;
use App\AnalyticsCustomerBehaviour;
use Exception;

class AnalyticsController extends Controller
{
    public function showData(Request $request): View
    {
        $website_list          = StoreWebsiteAnalytic::all()->toArray();
        $google_analytics_data = GoogleAnalyticData::leftJoin('store_website_analytics', 'google_analytic_datas.website_analytics_id', '=', 'store_website_analytics.id')
            ->select('google_analytic_datas.*', 'store_website_analytics.website');
        /** Filter */
        if ($request->start_date && $request->end_date) {
            $google_analytics_data->whereBetween('google_analytic_datas.created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->website) {
            $google_analytics_data->where('google_analytic_datas.website_analytics_id', $request->website);
        }
        if ($request->browser) {
            $google_analytics_data->where('google_analytic_datas.browser', $request->browser);
        }
        if ($request->os) {
            $google_analytics_data->where('google_analytic_datas.os', $request->os);
        }
        if ($request->country) {
            $google_analytics_data->where('google_analytic_datas.country', $request->country);
        }
        if ($request->user_type) {
            $google_analytics_data->where('google_analytic_datas.user_type', $request->user_type);
        }

        $google_analytics_data = $google_analytics_data->orderByDesc('google_analytic_datas.created_at')->paginate(Setting::get('pagination'));

        $browsers   = GoogleAnalyticData::select('browser')->distinct()->pluck('browser', 'browser')->toArray();
        $os         = GoogleAnalyticData::select('os')->distinct()->pluck('os', 'os')->toArray();
        $countries  = GoogleAnalyticData::select('country')->distinct()->pluck('country', 'country')->toArray();
        $user_types = GoogleAnalyticData::select('user_type')->distinct()->pluck('user_type', 'user_type')->toArray();

        return View('analytics.index-new', compact('website_list', 'google_analytics_data', 'browsers', 'os', 'countries', 'user_types'));
    }

    public function analyticsDataSummary(Request $request): View
    {
        $brands = [
            'balenciaga'     => 'Balenciaga',
            'gucci'          => 'Gucci',
            'jimmy-choo'     => 'Jimmy Choo',
            'saint-laurent'  => 'Saint Laurent',
            'givenchy'       => 'Givenchy',
            'christian-dior' => 'Christian Dior',
            'prada'          => 'Prada',
            'dolce-gabbana'  => 'Dolce Gabbana',
            'fendi'          => 'Fendi',
            'bottega-veneta' => 'Bottega Vneta',
            'burberry'       => 'Burberry',
        ];
        $genders = [
            'mens'  => 'Mens',
            'women' => 'Womens',
        ];
        if (! empty($_GET['location'])) {
            $location = $_GET['location'];
            $data     = AnalyticsSummary::where('country', 'like', '%' . $location . '%')->get()->toArray();
        } elseif (! empty($_GET['brand'])) {
            $data = AnalyticsSummary::where('brand_name', $request['brand'])->get()->toArray();
        } elseif (! empty($_GET['gender'])) {
            $data = AnalyticsSummary::where('gender', $request['gender'])->get()->toArray();
        } else {
            include app_path() . '/Functions/Analytics.php';
        }

        return View('analytics.summary', compact('data', 'brands', 'genders'));
    }

    public function displayLinksToPostData(): View
    {
        $data     = LinksToPost::orderByDesc('id')->paginate(15);
        $category = ArticleCategory::all();

        return View('analytics.linkstopost', compact('data', 'category'));
    }

    public function updateCategoryPost(Request $request): JsonResponse
    {
        $post              = LinksToPost::findorfail($request->link_id);
        $post->category_id = $request->id;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Post Updated',
        ]);
    }

    public function addArticleCategory(Request $request): RedirectResponse
    {
        $category       = new ArticleCategory;
        $category->name = $request->name;
        $category->save();

        return redirect()->back()->with(['message', 'Category Saved', 'success', 'true']);
    }

    /**
     * Customer Behaviour By Page
     */
    public function customerBehaviourByPage(Request $request): View
    {
        $pages = AnalyticsCustomerBehaviour::select('ID', 'pages')->pluck('pages', 'ID')->toArray();
        if (! empty($request['page'])) {
            $data = AnalyticsCustomerBehaviour::where('pages', $request['page'])->get()->toArray();
        } else {
            include app_path() . '/Functions/Analytics.php';
        }

        return View('analytics.customer-behaviour', compact('data', 'pages'));
    }

    public function cronShowData()
    {
        Log::channel('daily')->info('Google Analytics Started running ...');
        $analyticsDataArr = [];

        include app_path() . '/Functions/Analytics.php';
        $data = StoreWebsiteAnalytic::all()->toArray();
        foreach ($data as $value) {
            $ERPlogArray = [
                'model_id' => $value['id'],
                'url'      => 'https://www.googleapis.com/auth/analytics.readonly',
                'model'    => StoreWebsiteAnalytic::class,
                'type'     => 'success',
                'request'  => $value,
            ];

            try {
                $response   = getReport($analytics, $value);
                $resultData = printResults($response);

                if (! empty($resultData)) {
                    foreach ($resultData as $new_item) {
                        $analyticsDataArr = [
                            'operatingSystem'    => $new_item['operatingSystem'],
                            'user_type'          => $new_item['user_type'],
                            'time'               => $new_item['time'],
                            'page_path'          => $value['website'] . $new_item['page_path'],
                            'country'            => $new_item['country'],
                            'city'               => $new_item['city'],
                            'social_network'     => $new_item['social_network'],
                            'date'               => $new_item['date'],
                            'device_info'        => $new_item['device_info'],
                            'sessions'           => $new_item['sessions'],
                            'pageviews'          => $new_item['pageviews'],
                            'bounceRate'         => $new_item['bounceRate'],
                            'avgSessionDuration' => $new_item['avgSessionDuration'],
                            'timeOnPage'         => $new_item['timeOnPage'],

                        ];
                        Analytics::insert($analyticsDataArr);
                    }
                }

                $ERPlogArray['request']  = $value;
                $ERPlogArray['response'] = $resultData;
            } catch (Exception  $e) {
                $ERPlogArray['type']     = 'error';
                $ERPlogArray['response'] = $e->getMessage();
            }
            storeERPLog($ERPlogArray);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $history = GoogleAnalyticsHistories::orderByDesc('id')->take(50)->get();

        return response()->json(['code' => 200, 'data' => $history]);
    }

    public function cronGetUserShowData()
    {
        Log::channel('daily')->info('Google Analytics Cron running ...');
        $analyticsDataArr = [];

        include app_path() . '/Functions/Analytics_user.php';
        $data = StoreWebsiteAnalytic::all()->toArray();

        foreach ($data as $value) {
            $ERPlogArray = [
                'model_id' => $value['id'],
                'url'      => 'https://www.googleapis.com/auth/analytics.readonly',
                'model'    => StoreWebsiteAnalytic::class,
                'type'     => 'success',
                'request'  => $value,
            ];

            try {
                if (! empty($value['view_id']) && ! empty($value['google_service_account_json'])) {
                    $response = getReportRequest($analytics, $value);
                    Log::info('google-analytics  response -->' . json_encode($response));
                    extract($response);

                    $resultData = getGoogleAnalyticData($analyticsObj, $requestObj);
                    Log::info('google-analytics  resultData -->' . json_encode($resultData));
                    $resultPageTrackingData = printGoogleAnalyticResults($resultData, $value['id']);

                    $history = [
                        'website'     => $value['website'],
                        'account_id'  => $value['id'],
                        'title'       => 'success',
                        'description' => 'Data fetched successfully',
                        'created_at'  => now(),
                    ];
                    GoogleAnalyticsHistories::insert($history);
                } else {
                    $history = [
                        'website'     => $value['website'],
                        'account_id'  => $value['id'],
                        'title'       => 'error',
                        'description' => 'Please add auth json file and view id',
                        'created_at'  => now(),
                    ];
                    GoogleAnalyticsHistories::insert($history);
                }
            } catch (Exception  $e) {
                dump($e->getMessage());
                $history = [
                    'website'     => $value['website'],
                    'account_id'  => $value['id'],
                    'title'       => 'error',
                    'description' => $e->getMessage(),
                    'created_at'  => now(),
                ];
                GoogleAnalyticsHistories::insert($history);
                Log::error('google-analytics :: ' . $e->getMessage());
            }
        }
    }
}
