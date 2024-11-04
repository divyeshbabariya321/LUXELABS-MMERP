<?php

namespace App\Http\Controllers\gtmetrix;

use App\GTMetrixCategories;
use App\Http\Controllers\Controller;
use App\Http\Requests\gtmetrix\SaveGTmetrixCronTypeWebsiteStoreViewGTMetrixRequest;
use App\Http\Requests\gtmetrix\SaveGTmetrixCronWebsiteStoreViewGTMetrixRequest;
use App\LogRequest;
use App\Models\DataTableColumn;
use App\Repositories\GtMatrixRepository;
use App\Setting;
use App\StoreGTMetrixAccount;
use App\StoreViewsGTMetrix;
use App\StoreViewsGTMetrixUrl;
use App\StoreWebsite;
use App\WebsiteStoreView;
use Entrecore\GTMetrixClient\GTMetrixClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;


class WebsiteStoreViewGTMetrixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = StoreViewsGTMetrix::select(DB::raw('store_views_gt_metrix.*'));

        if (request('date') && request('date') != null) {
            $query->whereDate('store_views_gt_metrix.created_at', request('date'));
        }

        if (request('status') && request('status') != null) {
            $query->where('store_views_gt_metrix.status', request('status'));
        }

        $columns = ['error', 'report_url', 'report_url', 'html_load_time', 'html_bytes', 'page_load_time', 'page_bytes', 'page_elements', 'pagespeed_score', 'yslow_score', 'website_url'];
        if (request('keyword')) {
            $query->where(function ($q) use ($columns) {
                foreach ($columns as $column) {
                    $q->orWhere('store_views_gt_metrix.'.$column, 'LIKE', '%'.request('keyword').'%');
                }
            });
        }
        if (request('sortby') && request('ord') && request('sortby') != null && request('ord') != null) {
            $query->orderBy(request('sortby'), request('ord'));
        }

        $query->from(DB::raw('(SELECT MAX( id) as id, status, store_view_id, website_url, html_load_time FROM store_views_gt_metrix  GROUP BY store_views_gt_metrix.website_url ) as t'))
            ->leftJoin('store_views_gt_metrix', 't.id', '=', 'store_views_gt_metrix.id');

        if (request('sortby') && request('sortby') != null) {
            $list = $query->orderBy('id', request('sortby'))->paginate(25);
        } else {
            $list = $query->orderByDesc('id')->paginate(25);
        }

        $cronStatus = Setting::where('name', 'gtmetrixCronStatus')->get()->first();
        $cronTime = Setting::where('name', 'gtmetrixCronType')->get()->first();
        $storeViewList = WebsiteStoreView::whereNotNull('website_store_id')->get();
        $StoreWebsite = StoreWebsite::get();
        if ($request->ajax()) {
            return view('gtmetrix.index_ajax', compact('list', 'cronStatus', 'cronTime'));
        } else {
            return view('gtmetrix.index', compact('list', 'cronStatus', 'cronTime', 'storeViewList', 'StoreWebsite'));
        }
    }

    public function delete_website_url(Request $request): JsonResponse
    {
        if (isset($request->rowid)) {
            try {
                StoreViewsGTMetrixUrl::where('id', '=', $request->rowid)->delete();

                return response()->json(['code' => 200, 'message' => 'Record Deleted Successfully']);
            } catch (Exception $e) {
                return response()->json(['code' => 500, 'message' => 'Error :' . $e->getMessage()]);
            }
        }
    }

    public function add_website_url(Request $request)
    {
        if (isset($request->arrayList)) {
            $allRecords = StoreViewsGTMetrixUrl::select('store_views_gt_metrix_url.*')->get();
            try {
                foreach ($allRecords as $recordsData) {
                    if (in_array($recordsData['id'], $request->arrayList)) {
                        $gtmetrix = StoreViewsGTMetrixUrl::where('id', $recordsData['id']);
                        $update = [
                            'process' => 1,
                        ];
                        $gtmetrix->update($update);
                    } else {
                        $gtmetrix = StoreViewsGTMetrixUrl::where('id', $recordsData['id']);
                        $update = [
                            'process' => 0,
                        ];
                        $gtmetrix->update($update);
                    }
                }

                return response()->json(['code' => 200, 'message' => 'Processed Successfully']);
            } catch (Exception $e) {
                return response()->json(['code' => 500, 'message' => 'Error :' . $e->getMessage()]);
            }
        } else {
            $storename = '';
            if (isset($request->store_view)) {
                $storeViewList = WebsiteStoreView::where('id', $request->store_view)->get();
                if (isset($storeViewList[0])) {
                    $storename = $storeViewList[0]->name;
                }
            }

            $date = Carbon::now();
            $addwebsite['website_url'] = $request->website;
            $addwebsite['process'] = $request->process_url;
            $addwebsite['store_view_id'] = $request->store_view;
            $addwebsite['account_id'] = '';
            $addwebsite['created_at'] = $date;
            $addwebsite['updated_at'] = $date;
            $addwebsite['store_name'] = $storename;
            $website_data = StoreViewsGTMetrixUrl::where('website_url', $request->website)->get();

            if ($website_data->isEmpty()) {
                StoreViewsGTMetrixUrl::create($addwebsite);

                return redirect()->back()->with('success', 'Website url added successfully');
            } else {
                return redirect()->back()->with('warning', 'Website url already exists');
            }
        }
    }

    public function website_url(Request $request): View
    {
        $query = StoreViewsGTMetrixUrl::select('store_views_gt_metrix_url.*');

        if (request('date') && request('date') != null) {
            $query->whereDate('store_views_gt_metrix_url.created_at', request('date'));
        }
        if (request('website_url') && request('website_url') != null) {
            $query->where('store_views_gt_metrix_url.website_url', 'like', '%'.request('website_url').'%');
        }
        if (request('process') && request('process') != null) {
            $process = (request('process') == 'yes') ? 1 : 0;
            $query->where('store_views_gt_metrix_url.process', $process);
        }

        if (request('sortby') && request('ord') && request('sortby') != null && request('ord') != null) {
            $query->orderBy(request('sortby'), request('ord'));
        }

        if (request('sortby') && request('sortby') != null) {
            $list = $query->orderBy('id', request('sortby'))->paginate(25);
        } else {
            $list = $query->orderByDesc('id')->paginate(25);
        }

        $storeViewList = WebsiteStoreView::whereNotNull('website_store_id')->get();

        if ($request->ajax()) {
            return view('gtmetrix.website_url_ajax', compact('list', 'storeViewList'));
        } else {
            return view('gtmetrix.website_url', compact('list', 'storeViewList'));
        }
    }

    public function toggleFlag(Request $request): JsonResponse
    {
        $input = $request->all();

        if ($input['flag'] == null || $input['flag'] == 2) {
            $data = ['flag' => 1];
        } else {
            $data = ['flag' => 2];
        }

        $response = StoreViewsGTMetrix::where('id', $input['id'])->update($data);

        if ($response) {
            return response()->json([
                'status' => true,
                'message' => 'Flag Successfully Updated.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong.',
            ]);
        }
    }

    public function saveGTmetrixCronStatus($status = null): RedirectResponse
    {
        if (empty($status)) {
            return redirect()->back()->with('error', 'Error');
        }

        $statusExit = Setting::where('name', 'gtmetrixCronStatus')->get()->first();
        if (empty($statusExit)) {
            $status_date['name'] = 'gtmetrixCronStatus';
            $status_date['type'] = 'string';
            $status_date['val'] = $status;
            Setting::create($status_date);
        } else {
            $statusExit->val = $status;
            $statusExit->save();
        }

        return redirect()->back()->with('success', 'Success');
    }

    public function saveGTmetrixCronType(SaveGTmetrixCronTypeWebsiteStoreViewGTMetrixRequest $request): RedirectResponse
    {

        $type = Setting::where('name', 'gtmetrixCronType')->get()->first();

        if (empty($type)) {
            $type['name'] = 'gtmetrixCronType';
            $type['type'] = 'string';
            $type['val'] = $request->type;
            Setting::create($type);
        } else {
            $type->val = $request->type;
            $type->save();
        }

        return redirect()->back()->with('success', 'Success');
    }

    public function saveGTmetrixCron(SaveGTmetrixCronWebsiteStoreViewGTMetrixRequest $request): RedirectResponse
    {
        $data['store_view_id'] = $request->store_view;
        $data['status'] = $request->status;
        $data['website_url'] = $request->website;

        StoreViewsGTMetrix::create($data);

        return redirect()->back()->with('success', 'Success');
    }

    /**
     * Show the store view history.
     *
     * @param  mixed  $id
     */
    public function history($id): View
    {
        $title = 'History';
        if ($id) {
            $history = StoreViewsGTMetrix::where('website_url', $id)->whereNotNull('test_id')->orderByDesc('created_at')->paginate(25);

            return view('gtmetrix.history', compact('history', 'title'));
        }
    }

    public function webHistory(Request $request): View
    {
        $id = $request->get('id');
        $title = 'History';
        if ($id) {
            $history = StoreViewsGTMetrix::where('website_url', $id)->whereNotNull('test_id')->orderByDesc('created_at')->paginate(25);
            $history->appends(['id' => $id])->links();

            return view('gtmetrix.history', compact('history', 'title'));
        }
    }

    public function runErpEvent(Request $request): JsonResponse
    {
        $gtmatrixAccount = StoreGTMetrixAccount::select(DB::raw('store_gt_metrix_account.*'));
        $gtmatrix = StoreViewsGTMetrix::where('id', $request->id)->first();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($gtmatrix) {
            $gt_metrix['store_view_id'] = $gtmatrix->store_view_id;
            $gt_metrix['website_url'] = $gtmatrix->website_url;
            $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
            $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
            $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_url', $gt_metrix['website_url'])->first();
            try {
                if (! empty($gtmatrix->account_id)) {
                    $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->first();

                    $curl = curl_init();
                    $url = 'https://gtmetrix.com/api/2.0/status';

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_USERPWD => $gtmatrixAccountData->account_id.':'.'',
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                    ]);

                    $response = curl_exec($curl);
                    $data = json_decode($response);
                    $parameters = [];
                    $httpcode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $data, $httpcode, WebsiteStoreViewGTMetrixController::class, 'runErpEvent');

                    curl_close($curl);

                    $credits = $data->data->attributes->api_credits;
                    // print_r($data->data->attributes->api_credits);
                    if ($credits != 0) {
                        $client = new GTMetrixClient;
                        $client->setUsername($gtmatrixAccountData->email);
                        $client->setAPIKey($gtmatrixAccountData->account_id);
                        $client->getLocations();
                        $client->getBrowsers();
                        $test = $client->startTest($gtmetrix->website_url);
                        $update = [
                            'test_id' => $test->getId(),
                            'status' => 'queued',
                        ];
                        $gtmetrix->update($update);
                    }
                } else {
                    $AccountData = $gtmatrixAccount->orderByDesc('id')->get();

                    foreach ($AccountData as $key => $value) {
                        $curl = curl_init();
                        $url = 'https://gtmetrix.com/api/2.0/status';

                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_USERPWD => $value['account_id'].':'.'',
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ]);

                        $response = curl_exec($curl);
                        $data = json_decode($response); //response decode
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $parameters = [];

                        curl_close($curl);

                        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $data, $httpcode, WebsiteStoreViewGTMetrixController::class, 'runErpEvent');
                        $credits = $data->data->attributes->api_credits;
                        if ($credits != 0) {
                            $client = new GTMetrixClient;
                            $client->setUsername($value['email']);
                            $client->setAPIKey($value['account_id']);
                            $client->getLocations();
                            $client->getBrowsers();
                            $test = $client->startTest($gtmetrix->website_url);
                            $update = [
                                'test_id' => $test->getId(),
                                'status' => 'queued',
                                'account_id' => $value['account_id'],
                            ];
                            $gtmetrix->update($update);
                            break;
                        }
                    }
                }

                return response()->json(['code' => 200, 'message' => 'Request has been send for queue successfully']);
            } catch (Exception $e) {
                return response()->json(['code' => 500, 'message' => 'Error :' . $e->getMessage()]);
            }
        }
    }

    public function remove_http($url)
    {
        $disallowed = ['http://', 'https://'];
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }

    /**
     * Show the pagespeed or yslow stats.
     *
     * @param  mixed  $type
     * @param  mixed  $id
     */
    public function getstats($type, $id): View
    {
        $Insightdata = [];
        $InsightTypeData = [];
        $data = [];
        $typeData = [];
        $resourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json', 'pagespeed_insight_json')->where('test_id', $id)->orderByDesc('created_at')->get();
        foreach ($resourcedata as $value) {
            if ($type == 'pagespeed' && $id) {
                $title = 'PageSpeed';
                $typeData['type'] = 'GT Metrix';
                if (! empty($value['pagespeed_json'])) {
                    $pagespeeddata = strip_tags(file_get_contents(public_path().$value['pagespeed_json']));
                    $jsondata = json_decode($pagespeeddata, true);
                    foreach ($jsondata['rules'] as $key => $pagespeed) {
                        $data[$key]['name'] = $pagespeed['name'];
                        if (isset($pagespeed['score'])) {
                            $data[$key]['score'] = $pagespeed['score'];
                        } else {
                            $data[$key]['score'] = 'n/a';
                        }
                    }
                }
            }
            if ($type == 'pagespeed' && $id) {
                $title = 'PageSpeed';
                $InsightTypeData['type'] = 'PageSpeed Insight';
                if (! empty($value['pagespeed_insight_json'])) {
                    $pagespeedInsightdata = strip_tags(file_get_contents(public_path().$value['pagespeed_insight_json']));
                    $jsondata = json_decode($pagespeedInsightdata, true);
                    foreach ($jsondata['lighthouseResult']['audits'] as $key => $pagespeed) {
                        $Insightdata[$key]['name'] = $pagespeed['id'];
                        if (isset($pagespeed['score'])) {
                            $Insightdata[$key]['score'] = $pagespeed['score'];
                        } else {
                            $Insightdata[$key]['score'] = 'n/a';
                        }
                    }
                }
            }

            if ($type == 'yslow' && $id) {
                $title = 'YSlow';
                if (! empty($value['yslow_json'])) {
                    $typeData['type'] = 'YSlow';
                    $yslowdata = strip_tags(file_get_contents(public_path().$value['yslow_json']));
                    $jsondata = json_decode($yslowdata, true);
                    $i = 0;
                    foreach ($jsondata['g'] as $key => $yslow) {
                        $data[$i]['name'] = trans('lang.'.$key);
                        if (isset($yslow['score'])) {
                            $data[$i]['score'] = $yslow['score'];
                        } else {
                            $data[$i]['score'] = 'n/a';
                        }
                        $i++;
                    }
                }
            }
        }

        return view('gtmetrix.stats', compact('data', 'title', 'typeData', 'Insightdata', 'InsightTypeData', 'Insightdata'));
    }

    public function getstatsComparison($id): View
    {
        $page_data = [];
        $yslow_data = [];
        $PageResourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json')->where('test_id', $id)->where('pagespeed_json', '!=', null)->orderByDesc('created_at')->limit(5)->get();
        $YslowResourcedata = StoreViewsGTMetrix::select('pagespeed_json', 'yslow_json')->where('test_id', $id)->where('yslow_json', '!=', null)->orderByDesc('created_at')->limit(5)->get();

        if ($PageResourcedata) {
            foreach ($PageResourcedata as $value) {
                if (! empty($value['pagespeed_json'])) {
                    $pagespeeddata = strip_tags(file_get_contents(public_path().$value['pagespeed_json']));
                    $jsondata = json_decode($pagespeeddata, true);
                    foreach ($jsondata['rules'] as $key => $pagespeed) {
                        if (! isset($page_data[$pagespeed['name']])) {
                            $page_data[$pagespeed['name']] = [];
                        }
                        if (isset($pagespeed['score'])) {
                            $page_data[$pagespeed['name']][] = $pagespeed['score'];
                        } else {
                            $page_data[$pagespeed['name']][] = 'NA';
                        }
                    }
                }
            }
        }
        if ($YslowResourcedata) {
            foreach ($PageResourcedata as $value) {
                if (! empty($value['yslow_json'])) {
                    $yslowdata = strip_tags(file_get_contents(public_path().$value['yslow_json']));
                    $jsondata = json_decode($yslowdata, true);
                    $i = 0;
                    foreach ($jsondata['g'] as $key => $yslow) {
                        if (! isset($yslow_data[trans('lang.'.$key)])) {
                            $yslow_data[trans('lang.'.$key)] = [];
                        }
                        if (isset($yslow['score'])) {
                            $yslow_data[trans('lang.'.$key)][] = $yslow['score'];
                        } else {
                            $yslow_data[trans('lang.'.$key)][] = 'NA';
                        }
                    }
                }
            }
        }
        $Colname = [
            'First', 'Second', 'Third', 'Fourth', 'Fifth',
        ];

        return view('gtmetrix.comparison', compact('yslow_data', 'page_data', 'Colname'));
    }

    public function MultiRunErpEvent(Request $request): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        foreach ($request->arrayList as $key => $value) {
            $gtmatrixAccount = StoreGTMetrixAccount::select(DB::raw('store_gt_metrix_account.*'));
            $gtmatrix = StoreViewsGTMetrix::where('id', $value)->first();

            if ($gtmatrix) {
                $gt_metrix['store_view_id'] = $gtmatrix->store_view_id;
                $gt_metrix['website_url'] = $gtmatrix->website_url;
                $new_id = StoreViewsGTMetrix::create($gt_metrix)->id;
                $gtmetrix = StoreViewsGTMetrix::where('id', $new_id)->first();
                $gtmatrix = StoreViewsGTMetrix::where('store_view_id', $gt_metrix['store_view_id'])->where('website_url', $gt_metrix['website_url'])->first();
                try {
                    if (! empty($gtmatrix->account_id)) {
                        $gtmatrixAccountData = StoreGTMetrixAccount::where('account_id', $gtmatrix->account_id)->where('status', 'active')->first();

                        $curl = curl_init();
                        $url = 'https://gtmetrix.com/api/2.0/status';

                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_USERPWD => $gtmatrixAccountData->account_id.':'.'',
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                        ]);

                        $response = curl_exec($curl);
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $parameters = [];

                        curl_close($curl);
                        $data = json_decode($response); //reponse encoded

                        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $data, $httpcode, WebsiteStoreViewGTMetrixController::class, 'MultiRunErpEvent');
                        $credits = $data->data->attributes->api_credits;
                        if ($credits != 0) {
                            $client = new GTMetrixClient;
                            $client->setUsername($gtmatrixAccountData->email);
                            $client->setAPIKey($gtmatrixAccountData->account_id);
                            $client->getLocations();
                            $client->getBrowsers();
                            $test = $client->startTest($gtmetrix->website_url);
                            $update = [
                                'test_id' => $test->getId(),
                                'status' => 'queued',
                            ];
                            $gtmetrix->update($update);
                        }
                    } else {
                        $AccountData = $gtmatrixAccount->where('status', 'active')->orderByDesc('id')->get();

                        foreach ($AccountData as $key => $value) {
                            $curl = curl_init();
                            $url = 'https://gtmetrix.com/api/2.0/status';
                            curl_setopt_array($curl, [
                                CURLOPT_URL => 'https://gtmetrix.com/api/2.0/status',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_USERPWD => $value['account_id'].':'.'',
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                            ]);

                            $response = curl_exec($curl);
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $parameters = [];

                            curl_close($curl);
                            // decode the response
                            $data = json_decode($response);

                            LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $data, $httpcode, WebsiteStoreViewGTMetrixController::class, 'MultiRunErpEvent');
                            $credits = $data->data->attributes->api_credits;
                            if ($credits != 0) {
                                $client = new GTMetrixClient;
                                $client->setUsername($value['email']);
                                $client->setAPIKey($value['account_id']);
                                $client->getLocations();
                                $client->getBrowsers();
                                $test = $client->startTest($gtmetrix->website_url);
                                $update = [
                                    'test_id' => $test->getId(),
                                    'status' => 'queued',
                                    'account_id' => $value['account_id'],
                                ];
                                $gtmetrix->update($update);
                                break;
                            }
                        }
                    }
                } catch (Exception $e) {
                    return response()->json(['code' => 500, 'message' => 'Error :' . $e->getMessage()]);
                }
            }
        }

        return response()->json(['code' => 200, 'message' => 'Request has been send for queue successfully']);
    }

    public function listGTmetrixCategories(Request $request)
    {
        $title = 'GTmetrix Categories';
        if ($request->ajax()) {
            $items = GTMetrixCategories::select('id', 'name', 'source');
            if (isset($request->name) && ! empty($request->name)) {
                $items->where('name', 'Like', '%'.$request->name.'%');
            }

            if (isset($request->source) && ! empty($request->source)) {
                $items->where('source', $request->source);
            }

            return datatables()->eloquent($items)->toJson();
        }

        return view('gtmetrix.category', compact('cateogry', 'title'));
    }

    public function listWebsiteWiseCategories(Request $request)
    {
        $title = 'GTmetrix Report Data';
        if ($request->ajax()) {
            $items = StoreViewsGTMetrix::where('status', 'completed');

            if (isset($request->website_url) && ! empty($request->website_url)) {
                $items->where('website_url', 'Like', '%'.$request->name.'%');
            }

            if (isset($request->test_id) && ! empty($request->test_id)) {
                $items->where('test_id', $request->test_id);
            }
            $items->orderByDesc('id');

            return datatables()->eloquent($items)->toJson();
        }

        return view('gtmetrix.gtmetrixReport', compact('cateogry', 'title'));
    }

    public function WebsiteWiseCategoriesReport(Request $request): View
    {
        $id = $request->id;
        $Insightdata = [];
        $InsightTypeData = [];
        $pagespeedData = [];
        $yslowData = [];
        $report = '';
        $data = [];
        $g_typeData = [];
        $y_typeData = [];
        $resourcedata = StoreViewsGTMetrix::select('id', 'pagespeed_json', 'yslow_json', 'pagespeed_insight_json')->where('id', $id)->orderByDesc('created_at')->first();
        $title = 'GTmetrix Report Data';

        if ($id) {
            $g_typeData['type'] = 'PageSpeed';
            if (! empty($resourcedata['pagespeed_json'])) {
                $pagespeeddata = strip_tags(file_get_contents(public_path().$resourcedata['pagespeed_json']));
                $jsondata = json_decode($pagespeeddata, true);
                foreach ($jsondata['rules'] as $key => $pagespeed) {
                    $pagespeedData[$key]['name'] = $pagespeed['name'];
                    if (isset($pagespeed['score'])) {
                        $pagespeedData[$key]['score'] = $pagespeed['score'];
                    } else {
                        $pagespeedData[$key]['score'] = 'n/a';
                    }

                    if (array_key_exists('impact', $pagespeed)) {
                        $pagespeedData[$key]['impact'] = round($pagespeed['impact'], 2);
                    } else {
                        $pagespeedData[$key]['impact'] = 'n/a';
                    }
                }
            }
        }
        if ($id) {
            $InsightTypeData['type'] = 'PageSpeed Insight';
            if (! empty($resourcedata['pagespeed_insight_json'])) {
                $pagespeedInsightdata = strip_tags(file_get_contents(public_path().$resourcedata['pagespeed_insight_json']));
                $jsondata = json_decode($pagespeedInsightdata, true);
                foreach ($jsondata['lighthouseResult']['audits'] as $key => $pagespeed) {
                    $Insightdata[$key]['name'] = $pagespeed['id'];
                    if (array_key_exists('scoreDisplayMode', $pagespeed)) {
                        $Insightdata[$key]['scoreDisplayMode'] = ucfirst($pagespeed['scoreDisplayMode']);
                    } else {
                        $Insightdata[$key]['scoreDisplayMode'] = 'n/a';
                    }
                    if (array_key_exists('numericValue', $pagespeed)) {
                        $Insightdata[$key]['numericValue'] = ucfirst($pagespeed['numericValue']);
                    } else {
                        $Insightdata[$key]['numericValue'] = 'n/a';
                    }
                    if (array_key_exists('numericUnit', $pagespeed)) {
                        $Insightdata[$key]['numericUnit'] = ucfirst($pagespeed['numericUnit']);
                    } else {
                        $Insightdata[$key]['numericUnit'] = 'n/a';
                    }
                    if (isset($pagespeed['score'])) {
                        $Insightdata[$key]['score'] = $pagespeed['score'];
                    } else {
                        $Insightdata[$key]['score'] = 'n/a';
                    }
                }
            }
        }
        if (! empty($resourcedata['yslow_json'])) {
            $y_typeData['type'] = 'YSlow';
            $yslowdata = strip_tags(file_get_contents(public_path().$resourcedata['yslow_json']));
            $jsondata = json_decode($yslowdata, true);
            $i = 0;
            foreach ($jsondata['g'] as $key => $yslow) {
                $yslowData[$i]['name'] = trans('lang.'.$key);
                if (isset($yslow['score'])) {
                    $yslowData[$i]['score'] = $yslow['score'];
                } else {
                    $yslowData[$i]['score'] = 'n/a';
                }
                $i++;
            }
        }

        return view('gtmetrix.gtmetrix_report', compact('data', 'title', 'g_typeData', 'y_typeData', 'Insightdata', 'InsightTypeData', 'Insightdata', 'pagespeedData', 'yslowData'));
    }

    public function CategoryWiseWebsiteReport(Request $request): View
    {
        try {
            $resourcedata = StoreViewsGTMetrix::select('id', 'website_url', 'test_id', 'pagespeed_json', 'yslow_json', 'pagespeed_insight_json')->where('test_id', '!=', '')->where('status', 'completed');

            $search = request('search', '');

            if (! empty($search)) {
                $resourcedata = $resourcedata->where(function ($q) use ($search) {
                    $q->where('website_url', 'LIKE', '%'.$search.'%');
                });
            }

            $resourcedata = $resourcedata->orderByDesc('created_at')->get();

            $title = 'GTmetrix Website Report Data';
            $iKey = '0';
            $inc = 0;
            $catName = [];
            $pagespeedDatanew = [];
            $catArr = [];
            foreach ($resourcedata as $datar) {
                $catScrore = [];
                $catImpact = [];
                if (! empty($datar['pagespeed_json']) && is_file(public_path().$datar['pagespeed_json'])) {
                    $pagespeeddata1 = strip_tags(file_get_contents(public_path().$datar['pagespeed_json']));
                    $jsondata = json_decode($pagespeeddata1, true);
                    if (is_array($jsondata) && ! empty($jsondata['rules'])) {
                        foreach ($jsondata['rules'] as $key => $pagespeed) {
                            $catName[] = $pagespeed['name'];
                            if (isset($pagespeed['score'])) {
                                $catScrore[] = $pagespeed['score'];
                            } else {
                                $catScrore[] = 'n\a';
                            }

                            if (array_key_exists('impact', $pagespeed)) {
                                $catImpact[] = round($pagespeed['impact'], 2);
                            } else {
                                $catImpact[] = 'n\a';
                            }
                            $inc++;
                        }
                    }
                }

                $InsightTypeData['type'] = 'PageSpeed Insight';
                if (! empty($datar['pagespeed_insight_json'])) {
                    if (is_file(public_path().$datar['pagespeed_insight_json'])) {
                        $pagespeedInsightdata = strip_tags(file_get_contents(public_path().$datar['pagespeed_insight_json']));
                        $jsondata = json_decode($pagespeedInsightdata, true);
                        if (is_array($jsondata) && ! empty($jsondata['lighthouseResult']['audits'])) {
                            foreach ($jsondata['lighthouseResult']['audits'] as $key => $pagespeed) {
                                $inc++;
                                $catName[] = $pagespeed['id'];
                                if (isset($pagespeed['score'])) {
                                    $catScrore[] = $pagespeed['score'];
                                } else {
                                    $catScrore[] = 'n\a';
                                }
                            }
                        }
                    }
                }

                if (! empty($datar['yslow_json'])) {
                    $y_typeData['type'] = 'YSlow';
                    if (is_file(public_path().$datar['yslow_json'])) {
                        $yslowdata = strip_tags(file_get_contents(public_path().$datar['yslow_json']));
                        $jsondata = json_decode($yslowdata, true);
                        if (is_array($jsondata) && ! empty($jsondata['g'])) {
                            $i = 0;
                            foreach ($jsondata['g'] as $key => $yslow) {
                                $inc++;
                                $catName[] = trans('lang.'.$key);
                                if (isset($yslow['score'])) {
                                    $catScrore[] = $yslow['score'];
                                } else {
                                    $catScrore[] = 'n/a';
                                }
                                $i++;
                            }
                        }
                    }
                }

                $iKey++;
                $pagespeedDatanew[] = ['website' => $datar->website_url, 'score' => $catScrore, 'impact' => $catImpact, 'catName' => array_unique($catName)];
                $catArr = array_unique($catName);
            }

            $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'gtmetrixcategoryWeb')->first();

            $dynamicColumnsToShowgt = [];
            if (! empty($datatableModel->column_name)) {
                $hideColumns = $datatableModel->column_name ?? '';
                $dynamicColumnsToShowgt = json_decode($hideColumns, true);
            }

            return view('gtmetrix.gtmetrixWebsiteCategoryReport', compact('pagespeedDatanew', 'title', 'catArr', 'dynamicColumnsToShowgt'));
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'gtmetrixcategoryWeb')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'gtmetrixcategoryWeb';
            $column->column_name = json_encode($request->column_gt);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'gtmetrixcategoryWeb';
            $column->column_name = json_encode($request->column_gt);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function runCurrentUrl(Request $request): JsonResponse
    {
        if (isset($request->rowid)) {
            try {
                $gtmetrixURL = StoreViewsGTMetrixUrl::find($request->rowid);
                app(GtMatrixRepository::class)->pushtoTest($gtmetrixURL);

                return response()->json(['code' => 200, 'message' => 'Record Pushed to Queue']);
            } catch (Exception $e) {
                return response()->json(['code' => 500, 'message' => 'Error :' . $e->getMessage()]);
            }
        }
    }
}
