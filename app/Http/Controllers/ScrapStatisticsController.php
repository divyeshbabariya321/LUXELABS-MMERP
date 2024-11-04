<?php

namespace App\Http\Controllers;

use App\Brand;
use App\DeveloperTask;
use App\Exports\ScrapRemarkExport;
use App\Http\Requests\StoreScrapStatisticRequest;
use App\Models\DataTableColumn;
use App\Models\ScrapStatisticsStaus;
use App\Product;
use App\Reply;
use App\ScrapedProducts;
use App\Scraper;
use App\ScraperKilledHistory;
use App\ScraperPositionHistory;
use App\ScraperProcess;
use App\ScraperScreenshotHistory;
use App\ScraperServerStatusHistory;
use App\ScrapHistory;
use App\ScrapLog;
use App\ScrapRemark;
use App\ScrapStatistics;
use App\Supplier;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Zend\Diactoros\Response\JsonResponse;

class ScrapStatisticsController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/stat",
     *   tags={"Statistic"},
     *   summary="Get Statistics",
     *   operationId="get-statistics",
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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Set dates
        $keyWord = $request->get('term', '');
        $madeby = $request->get('scraper_made_by', 0);
        $scrapeType = $request->get('scraper_type', 0);

        $timeDropDown = self::get_times();

        $serverIds = Scraper::groupBy('server_id')->where('server_id', '!=', null)->pluck('server_id');
        $getLatestOptimization = ScraperServerStatusHistory::whereRaw('id in (
            SELECT MAX(id)
            FROM scraper_server_status_histories
            GROUP BY server_id
        )')
            ->pluck('in_percentage', 'server_id')->toArray();

        // Get active suppliers
        $activeSuppliers = Scraper::with([
            'scraperDuration' => function ($q) {
                $q->orderByDesc('id');
            },
            'scrpRemark' => function ($q) {
                $q->whereNull('scrap_field')->where('user_name', '!=', '')->orderByDesc('created_at');
            },
            'latestMessageNew' => function ($q) {
                $q->whereNotIn('chat_messages.status', ['7', '8', '9', '10'])
                    ->take(1)
                    ->orderByDesc('id');
            },
            'lastErrorFromScrapLogNew',
            'developerTaskNew',
            'scraperMadeBy',
            'childrenScraper.scraperMadeBy',
            'mainSupplier',

        ])
            ->withCount('childrenScraper')
            ->join('suppliers as s', 's.id', 'scrapers.supplier_id')
            ->where('supplier_status_id', 1)
            ->whereIn('scrapper', [1, 2])
            ->whereNull('parent_id');

        if (! empty($keyWord)) {
            $activeSuppliers->where(function ($q) use ($keyWord) {
                $q->where('s.supplier', 'like', "%{$keyWord}%")
                    ->orWhere('scrapers.scraper_name', 'like', "%{$keyWord}%");
            });
        }

        if ($madeby > 0) {
            $activeSuppliers->where('scrapers.scraper_made_by', $madeby);
        }

        if ($request->get('scrapers_status', '') != '') {
            $activeSuppliers->where('scrapers.status', $request->get('scrapers_status', ''));
        }

        if ($scrapeType > 0) {
            $activeSuppliers->where('scraper_type', $scrapeType);
        }

        if ($request->task_assigned_to > 0) {
            $activeSuppliers->whereRaw('scrapers.id IN (SELECT scraper_id FROM developer_tasks WHERE assigned_to = '.$request->task_assigned_to.' and scraper_id > 0)');
        }

        $activeSuppliers = $activeSuppliers->orderByDesc('scrapers.flag')->orderBy('s.supplier');

        $activeSuppliers = $activeSuppliers->paginate(10);

        $suppliers = Product::select(DB::raw('count(*) as inventory'), 'supplier_id as id', DB::raw('max(created_at) as last_date'))
            ->groupBy('supplier_id')->orderByDesc('created_at')->get();
        $data = [];

        foreach ($suppliers as $supplier) {
            if ($supplier->id !== null) {
                $data[$supplier->id]['inventory'] = $supplier->inventory;
                $data[$supplier->id]['last_date'] = $supplier->last_date;
            }
        }

        foreach ($activeSuppliers as $activeSupplier) {
            if (isset($data[$activeSupplier->supplier_id])) {
                $activeSupplier->inventory = $data[$activeSupplier->supplier_id]['inventory'];
                $activeSupplier->last_date = $data[$activeSupplier->supplier_id]['last_date'];
            } else {
                $activeSupplier->inventory = 0;
                $activeSupplier->last_date = null;
            }
        }
        // Get scrape data
        $yesterdayDate = date('Y-m-d', strtotime('-1 day'));
        $sql = '
            SELECT
                s.id,
                s.supplier,
                sc.inventory_lifetime,
                sc.scraper_new_urls,
                sc.scraper_existing_urls,
                sc.scraper_total_urls,
                sc.scraper_start_time,
                sc.scraper_logic,
                sc.scraper_made_by,
                sc.server_id,
                sc.id as scraper_id,
                ls.website,
                ls.ip_address,
                COUNT(ls.id) AS total,
                SUM(IF(ls.validated=0,1,0)) AS failed,
                SUM(IF(ls.validated=1,1,0)) AS validated,
                SUM(IF(ls.validation_result LIKE "%[error]%",1,0)) AS errors,
                SUM(IF(ls.validation_result LIKE "%[warning]%",1,0)) AS warnings,
                SUM(IF(ls.created_at LIKE "%'.$yesterdayDate.'%",1,0)) AS total_new_product,
                MAX(ls.last_inventory_at) AS last_scrape_date,
                IF(MAX(ls.last_inventory_at) < DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY),0,1) AS running
            FROM
                suppliers s
            JOIN
                scrapers sc
            ON
                sc.supplier_id = s.id
            JOIN
                scraped_products ls
            ON
                sc.scraper_name=ls.website
            WHERE
                sc.scraper_name IS NOT NULL AND

                '.($request->excelOnly == 1 ? 'ls.website LIKE "%_excel" AND' : '').'
                '.($request->excelOnly == -1 ? 'ls.website NOT LIKE "%_excel" AND' : '').'
                ls.last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
            GROUP BY
                sc.id
            ORDER BY
                sc.scraper_priority desc
        ';
        $scrapeData = DB::select($sql);

        $scrapper_total = count($scrapeData); //Purpose : Scrapper Count - DEVTASK-4219

        $allScrapperName = [];

        if (! empty($scrapeData)) {
            foreach ($scrapeData as $data) {
                if (isset($data->id) && $data->id > 0) {
                    $allScrapperName[$data->id] = $data->website;
                }
            }
        }

        /* Scrapper status count */

        $allStatus = Scraper::scrapersStatus();

        $allStatusCounts = Scraper::join('suppliers as s', 's.id', 'scrapers.supplier_id')
            ->selectRaw('COUNT(s.id) as total_count, scrapers.status')
            ->whereIn('scrapers.status', $allStatus)
            ->where('supplier_status_id', 1)
            ->groupBy('scrapers.status')
            ->get()
            ->pluck('total_count', 'status');

        $lastRunAt = ScrapedProducts::groupBy('website')->select([DB::raw('MAX(last_inventory_at) as last_run_at'), 'website'])->pluck('last_run_at', 'website')->toArray();

        $users = User::all()->pluck('name', 'id')->toArray();
        $allScrapper = Scraper::whereNull('parent_id')->pluck('scraper_name', 'id')->toArray();
        // Return view

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'scrap-statistics')->first();

        $dynamicColumnsToShows = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShows = json_decode($hideColumns, true);
        }

        if ($request->ajax()) {
            if ($request->get('load-status')) {
                return view('scrap.ajax.status', compact('allStatus', 'allStatusCounts'))->render();
            }

            return view('scrap.ajax.stats', compact('allStatus', 'allStatusCounts', 'activeSuppliers', 'serverIds', 'scrapeData', 'users', 'allScrapperName', 'timeDropDown', 'lastRunAt', 'allScrapper', 'getLatestOptimization', 'scrapper_total', 'dynamicColumnsToShows'))->render();
        }
        $scrapersStatus = Scraper::scrapersStatus();
        $selectedMadeBy = User::find(request('scraper_made_by'));
        $users = User::pluck('name', 'id')->toArray();

        return view('scrap.stats', compact('allStatus', 'allStatusCounts', 'activeSuppliers', 'serverIds', 'scrapeData', 'users', 'allScrapperName', 'timeDropDown', 'lastRunAt', 'allScrapper', 'getLatestOptimization', 'scrapper_total', 'dynamicColumnsToShows', 'scrapersStatus', 'selectedMadeBy', 'users'));
    }

    public function ssstatusCreate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $status = new ScrapStatisticsStaus;
            $status->status = $request->status_name;
            $status->status_value = $request->status_name;
            $status->save();

            $statuses = Scraper::scrapersStatus();

            return response()->json(['code' => 200, 'message' => 'status Create successfully', 'data' => $statuses]);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function columnVisbilityUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'scrap-statistics')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'scrap-statistics';
            $column->column_name = json_encode($request->column_s);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'scrap-statistics';
            $column->column_name = json_encode($request->column_s);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'column visiblity Added Successfully!',
        ]);
    }

    public function quickView(Request $request): View
    {
        $keyWord = $request->get('term', '');
        $column = request('column');
        $orderby = request('order_by', 'desc');
        $selectedSupplier = [];

        $timeDropDown = self::get_times();

        $developerTasks = DeveloperTask::where('scraper_id', $request->id)->latest()->get();

        $serverIds = Scraper::groupBy('server_id')->where('server_id', '!=', null)->pluck('server_id');
        $getLatestOptimization = ScraperServerStatusHistory::whereRaw('id in (
            SELECT MAX(id)
            FROM scraper_server_status_histories
            GROUP BY server_id
        )')
            ->pluck('in_percentage', 'server_id')->toArray();

        // Get active suppliers
        $activeSuppliers = Scraper::with([
            'scrpRemark' => function ($q) {
                $q->whereNull('scrap_field')->where('user_name', '!=', '')->orderByDesc('created_at');
            },
            'latestMessageNew' => function ($q) {
                $q->whereNotIn('chat_messages.status', ['7', '8', '9', '10'])
                    ->take(1)
                    ->orderByDesc('id');
            },
            'lastErrorFromScrapLogNew',
            'developerTaskNew',
            'scraperMadeBy',
            'childrenScraper.scraperMadeBy',
            'mainSupplier',
        ])
            ->withCount('childrenScraper')
            ->join('suppliers as s', 's.id', 'scrapers.supplier_id')
            ->where('supplier_status_id', 1)
            ->whereIn('scrapper', [1, 2])
            ->whereNull('parent_id');

        if (! empty($keyWord)) {
            if ($keyWord > 0) {
                $supplier = Supplier::find($keyWord);
                $selectedSupplier = ['id' => $supplier->id, 'name' => $supplier->supplier];
                $keyWord = $supplier->supplier;
            }
            $activeSuppliers->where(function ($q) use ($keyWord) {
                $q->where('s.supplier', 'like', "%{$keyWord}%")->orWhere('scrapers.scraper_name', 'like', "%{$keyWord}%");
            });
        }

        if (isset($request->assigned_to) && count($request->assigned_to)) {
            $activeSuppliers->whereHas('developerTaskNew', function ($q) use ($request) {
                $q->whereIn('assigned_to', $request->assigned_to);
            });
        }

        if (! empty($column) && $column == 'last_started_at') {
            $activeSuppliers = $activeSuppliers->orderby('scrapers.'.$column.'', $orderby)->get();
        } else {
            $activeSuppliers = $activeSuppliers->orderByDesc('scrapers.flag')->orderBy('s.supplier')->get();
        }

        $assignedUsers = [];
        if ($activeSuppliers) {
            foreach ($activeSuppliers as $_supplier) {
                $developerTasks = DeveloperTask::where('scraper_id', $_supplier->id)->latest()->get();
                if ($developerTasks) {
                    foreach ($developerTasks as $_task) {
                        if (isset($_task->assigned_to) && isset($_task->assignedUser->name)) {
                            $assignedUsers[$_task->assigned_to] = $_task->assignedUser->name;
                        }
                    }
                }
            }
        }

        // Get scrape data
        $yesterdayDate = date('Y-m-d', strtotime('-1 day'));
        $sql = '
            SELECT
                s.id,
                s.supplier,
                sc.inventory_lifetime,
                sc.scraper_new_urls,
                sc.scraper_existing_urls,
                sc.scraper_total_urls,
                sc.scraper_start_time,
                sc.scraper_logic,
                sc.scraper_made_by,
                sc.server_id,
                sc.id as scraper_id,
                ls.website,
                ls.ip_address,
                COUNT(ls.id) AS total,
                SUM(IF(ls.validated=0,1,0)) AS failed,
                SUM(IF(ls.validated=1,1,0)) AS validated,
                SUM(IF(ls.validation_result LIKE "%[error]%",1,0)) AS errors,
                SUM(IF(ls.validation_result LIKE "%[warning]%",1,0)) AS warnings,
                SUM(IF(ls.created_at LIKE "%'.$yesterdayDate.'%",1,0)) AS total_new_product,
                MAX(ls.last_inventory_at) AS last_scrape_date,
                IF(MAX(ls.last_inventory_at) < DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY),0,1) AS running
            FROM
                suppliers s
            JOIN
                scrapers sc
            ON
                sc.supplier_id = s.id
            JOIN
                scraped_products ls
            ON
                sc.supplier_id=ls.supplier_id
            WHERE
                sc.scraper_name IS NOT NULL AND

                '.($request->excelOnly == 1 ? 'ls.website LIKE "%_excel" AND' : '').'
                '.($request->excelOnly == -1 ? 'ls.website NOT LIKE "%_excel" AND' : '').'
                ls.last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
            GROUP BY
                sc.id
            ORDER BY
                '.($column == 'least_product' ? 'total_new_product '.$orderby.' ' : 'sc.scraper_priority DESC').'
            ';

        $scrapeData = DB::select($sql);

        $allScrapperName = [];

        if (! empty($scrapeData)) {
            foreach ($scrapeData as $data) {
                if (isset($data->id) && $data->id > 0) {
                    $allScrapperName[$data->id] = $data->website;
                }
            }
        }

        $allStatus = Scraper::scrapersStatus();

        $allStatusCounts = Scraper::join('suppliers as s', 's.id', 'scrapers.supplier_id')
            ->selectRaw('COUNT(s.id) as total_count, scrapers.status')
            ->whereIn('scrapers.status', $allStatus)
            ->where('supplier_status_id', 1)
            ->groupBy('scrapers.status')
            ->get()
            ->pluck('total_count', 'status');

        $lastRunAt = ScrapedProducts::groupBy('website')->select([DB::raw('MAX(last_inventory_at) as last_run_at'), 'website'])->pluck('last_run_at', 'website')->toArray();

        $users = User::all()->pluck('name', 'id')->toArray();
        $allScrapper = Scraper::whereNull('parent_id')->pluck('scraper_name', 'id')->toArray();
        // Return view
        try {
            return view('scrap.quick-stats', compact('allStatusCounts', 'allStatus', 'activeSuppliers', 'serverIds', 'scrapeData', 'users', 'allScrapperName', 'timeDropDown', 'lastRunAt', 'allScrapper', 'getLatestOptimization', 'assignedUsers', 'selectedSupplier'));
        } catch (Exception $e) {
            Log::error('Quick-stats-page :: '.$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScrapStatisticRequest $request): \Illuminate\Http\JsonResponse
    {

        $stat = new ScrapStatistics;
        $stat->supplier = $request->get('supplier');
        $stat->type = $request->get('type');
        $stat->url = $request->get('url');
        $stat->description = $request->get('description');
        $stat->save();

        return response()->json([
            'status' => 'Added successfully!',
        ]);
    }

    public function assetManager(): View
    {
        return view('scrap.asset-manager');
    }

    public function showHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $remarks = ScrapRemark::where('scrap_id', $request->search)->where('scrap_field', $request->field);

        if (in_array($request->field, ['scraper_start_time', 'server_id', 'status'])) {
            $remarks = $remarks->where(function ($q) {
                $q->orWhere('old_value', '!=', '')->orWhere('new_value', '!=', '');
            });
        }

        $remarks = $remarks->get();

        return response()->json($remarks, 200);
    }

    public function getRemark(Request $request): \Illuminate\Http\JsonResponse
    {
        $name = $request->input('name');

        $remarks = ScrapRemark::where('scraper_name', $name)->where('user_name', '!=', '');

        if ($request->get('auto') == 'true') {
            $remarks = $remarks->whereNull('scrap_field');
        }

        $remarks = $remarks->latest()->get();

        return response()->json($remarks, 200);
    }

    public function addRemark(Request $request): \Illuminate\Http\JsonResponse
    {
        $remark = $request->input('remark');
        $name = $request->input('id');
        $last_rec = ''; //Purpose : Last Record - DEVTASK-4219

        if (! empty($remark)) {
            ScrapRemark::create([
                'scraper_name' => $name,
                'remark' => $remark,
                'user_name' => Auth::user()->name,
            ]);

            $needToSend = request()->get('need_to_send', false);
            $includeAssignTo = request()->get('inlcude_made_by', false);

            if ($needToSend == 1) {
                if (Auth::user()->phone != '' && Auth::user()->whatsapp_number != '') {
                    app(WhatsAppController::class)->sendWithThirdApi(Auth::user()->phone, Auth::user()->whatsapp_number, 'SCRAPER-REMARK#'.$name."\n".$remark);
                }

                if ($includeAssignTo == 1) {
                    $scraper = Scraper::where('scraper_name', $name)->first();
                    if ($scraper) {
                        $sendPer = $scraper->scraperMadeBy;
                        if ($sendPer) {
                            app(WhatsAppController::class)->sendWithThirdApi($sendPer->phone, $sendPer->whatsapp_number, 'SCRAPER-REMARK#'.$name."\n".$remark);
                        }
                    }
                }
            }

            $last_rec = ScrapRemark::latest()->first(); //Purpose : Last Record - DEVTASK-4219
        }

        return response()->json(['remark' => $remark, 'last_record' => $last_rec], 200); //Purpose : Send Last Record - DEVTASK-4219
    }

    public function updateField(Request $request): \Illuminate\Http\JsonResponse
    {
        $fieldName = request()->get('field');
        $fieldValue = request()->get('field_value');
        $search = request()->get('search');
        $remark = request()->get('remark');
        $suplier = Scraper::where('supplier_id', $search)->first();

        if (! $suplier) {
            $suplier = Scraper::find($search);
        }

        if ($suplier) {
            $oldValue = $suplier->{$fieldName};

            if ($fieldName == 'scraper_made_by') {
                $oldValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
            }

            if ($fieldName == 'parent_supplier_id') {
                $oldValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
            }

            $suplier->{$fieldName} = $fieldValue;
            $suplier->save();

            $suplier = Scraper::where('supplier_id', $search)->first();

            if (! $suplier) {
                $suplier = Scraper::find($search);
            }

            $newValue = $fieldValue;

            if ($fieldName == 'scraper_made_by') {
                $newValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
            }

            if ($fieldName == 'parent_supplier_id') {
                $newValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
            }

            ScrapRemark::create([
                'scraper_name' => $suplier->scraper_name,
                'remark' => "{$fieldName} updated old value was $oldValue and new value is $newValue",
                'user_name' => Auth::user()->name,
                'scrap_field' => $fieldName,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'scrap_id' => $suplier->id,
            ]);

            if (! empty($remark)) {
                ScrapRemark::create([
                    'scraper_name' => $suplier->scraper_name,
                    'remark' => $remark,
                    'user_name' => Auth::user()->name,
                    'scrap_id' => $suplier->id,
                ]);
            }
        }

        return response()->json(['code' => 200, 'data' => $suplier]);
    }

    public function multipleUpdateField(Request $request): \Illuminate\Http\JsonResponse
    {
        $fieldName = 'full_scrape';
        $fieldValue = 1;

        if (! empty($request->ids)) {
            foreach ($request->ids as $value) {
                $search = $value;
                $remark = request()->get('remark');
                $suplier = Scraper::where('supplier_id', $search)->first();

                if (! $suplier) {
                    $suplier = Scraper::find($search);
                }

                if ($suplier) {
                    $oldValue = $suplier->{$fieldName};

                    if ($fieldName == 'scraper_made_by') {
                        $oldValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
                    }

                    if ($fieldName == 'parent_supplier_id') {
                        $oldValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
                    }

                    $suplier->{$fieldName} = $fieldValue;
                    $suplier->save();

                    $suplier = Scraper::where('supplier_id', $search)->first();

                    if (! $suplier) {
                        $suplier = Scraper::find($search);
                    }

                    $newValue = $fieldValue;

                    if ($fieldName == 'scraper_made_by') {
                        $newValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
                    }

                    if ($fieldName == 'parent_supplier_id') {
                        $newValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
                    }

                    ScrapRemark::create([
                        'scraper_name' => $suplier->scraper_name,
                        'remark' => "{$fieldName} updated old value was $oldValue and new value is $newValue",
                        'user_name' => Auth::user()->name,
                        'scrap_field' => $fieldName,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'scrap_id' => $suplier->id,
                    ]);

                    if (! empty($remark)) {
                        ScrapRemark::create([
                            'scraper_name' => $suplier->scraper_name,
                            'remark' => $remark,
                            'user_name' => Auth::user()->name,
                            'scrap_id' => $suplier->id,
                        ]);
                    }
                }
            }
        }

        return response()->json(['code' => 200]);
    }

    public function updateScrapperField(Request $request): \Illuminate\Http\JsonResponse
    {
        $fieldName = request()->get('field');
        $fieldValue = request()->get('field_value');
        $search = request()->get('search');

        $suplier = Scraper::find($search);

        if (! $suplier) {
            return response()->json(['code' => 500]);
        }

        if ($suplier) {
            $oldValue = $suplier->{$fieldName};

            if ($fieldName == 'scraper_made_by') {
                $oldValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
            }

            if ($fieldName == 'parent_supplier_id') {
                $oldValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
            }

            $suplier->{$fieldName} = $fieldValue;
            $suplier->save();

            $suplier = Scraper::where('supplier_id', $search)->first();

            if (! $suplier) {
                $suplier = Scraper::find($search);
            }

            $newValue = $fieldValue;

            if ($fieldName == 'scraper_made_by') {
                $newValue = ($suplier->scraperMadeBy) ? $suplier->scraperMadeBy->name : '';
            }

            if ($fieldName == 'parent_supplier_id') {
                $newValue = ($suplier->scraperParent) ? $suplier->scraperParent->scraper_name : '';
            }

            ScrapRemark::create([
                'scrap_id' => $suplier->id,
                'scraper_name' => $suplier->scraper_name,
                'remark' => "{$fieldName} updated old value was $oldValue and new value is $newValue",
                'user_name' => Auth::user()->name,
                'scrap_field' => $fieldName,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);
        }

        return response()->json(['code' => 200]);
    }

    public function updatePriority(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        $prio = count($ids);

        if (! empty($ids)) {
            foreach ($ids as $id) {
                if (isset($id['id'])) {
                    $scrap = Scraper::where('supplier_id', $id['id'])->first();
                    if ($scrap) {
                        $scrap->scraper_priority = $prio;
                        $scrap->save();
                    }
                }
                $prio--;
            }
        }

        return response()->json(['code' => 200]);
    }

    public function getHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $field = $request->get('field', 'supplier');
        $value = $request->get('search', '0');

        $history = [];

        if ($value > 0) {
            if ($field == 'supplier') {
                $history = ScrapHistory::where('model', Supplier::class)->join('users as u', 'u.id', 'scrap_histories.created_by')->where('model_id', $value)
                    ->orderByDesc('created_at')
                    ->select('scrap_histories.*', 'u.name as created_by_name')
                    ->get()
                    ->toArray();
            }
        }

        return response()->json(['code' => 200, 'data' => $history]);
    }

    private static function get_times($default = '19:00', $interval = '+60 minutes')
    {
        $output = [];

        $current = strtotime('00:00');
        $end = strtotime('23:59');

        while ($current <= $end) {
            $time = date('G', $current);
            $output[$time] = date('h.i A', $current);
            $current = strtotime($interval, $current);
        }

        return $output;
    }

    public function getLastRemark(Request $request)
    {
        // $lastRemark = DB::select("select * from scrap_remarks as sr join ( SELECT MAX(id) AS id FROM scrap_remarks WHERE user_name != '' AND scrap_field IS NULL  GROUP BY scraper_name ) as max_s on sr.id =  max_s.id   join scrapers as scr on scr.scraper_name = sr.scraper_name  left join scrap_logs as scr_logs on scr_logs.scraper_id = scr.id  WHERE sr.user_name IS NOT NULL order by sr.scraper_name asc");

        $lastRemark = ScrapRemark::select('scrap_remarks.*', 'scrapers.*', 'scrap_logs.*')
            ->join(
                DB::raw('(SELECT MAX(id) AS id FROM scrap_remarks WHERE user_name != \'\' AND scrap_field IS NULL GROUP BY scraper_name) AS max_s'),
                'scrap_remarks.id',
                '=',
                'max_s.id'
            )
            ->join('scrapers', 'scrapers.scraper_name', '=', 'scrap_remarks.scraper_name')
            ->leftJoin('scrap_logs', 'scrap_logs.scraper_id', '=', 'scrapers.id')
            ->whereNotNull('scrap_remarks.user_name')
            ->orderBy('scrap_remarks.scraper_name')
            ->get();

        $suppliers = Product::select(DB::raw('count(*) as inventory'), 'supplier_id as id', DB::raw('max(created_at) as last_date'))
            ->groupBy('supplier_id')->orderByDesc('created_at')->get();
        $data = [];

        foreach ($suppliers as $supplier) {
            if ($supplier->id !== null) {
                $data[$supplier->id]['inventory'] = $supplier->inventory;
                $data[$supplier->id]['last_date'] = $supplier->last_date;
            }
        }

        foreach ($lastRemark as $lastRemar) {
            if (isset($data[$lastRemar->supplier_id])) {
                $lastRemar->inventory = $data[$lastRemar->supplier_id]['inventory'];
                $lastRemar->last_date = $data[$lastRemar->supplier_id]['last_date'];
            } else {
                $lastRemar->inventory = 0;
                $lastRemar->last_date = null;
            }
        }

        $download = $request->input('download');
        if (! empty($download)) {
            return Excel::download(new ScrapRemarkExport($lastRemark), 'remarks.csv');
        }

        return response()->json(['code' => 200, 'data' => $lastRemark]);
    }

    public function addNote(Request $request)
    {
        try {
            $this->validate($request, [
                'scraper_name' => 'required',
                'remark' => 'required',
            ]);
            $remark = $request->remark;

            if (! empty($remark)) {
                $note = ScrapRemark::create([
                    'scraper_name' => $request->scraper_name,
                    'remark' => $request->remark,
                    'user_name' => Auth::user()->name,
                ]);

                if ($request->hasfile('image')) {
                    $media = MediaUploader::fromSource($request->file('image'))
                        ->toDirectory('scrap-note')
                        ->upload();
                    $note->attachMedia($media, config('constants.media_tags'));
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'msg' => 'Note added successfully.',
                ]);
            }

            session()->flash('success', 'Note added successfully.');

            return redirect()->back();
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'msg' => $e->getMessage(),
                ]);
            }

            session()->flash('error', $e->getMessage());

            return redirect()->back();
        }
    }

    public function serverStatistics(Request $request)
    {
        try {
            $scrappers = Scraper::query();
            $scrap = $scrappers->where('inventory_lifetime', '!=', 0)->where('server_id', '!=', '');

            if ($request->type) {
                if ($request->type == 'server_id_filter') {
                    if (! empty($request->order)) {
                        $scrappers->where('server_id', $request->order);
                    }
                } elseif ($request->type == 'filter_by_text') {
                    if (! empty($request->order)) {
                        $scrappers->where('scraper_name', 'LIKE', '%'.$request->order.'%');
                    }
                } else {
                    $scrappers->orderBy($request->type, $request->order);
                }
            }

            $scrappers = $scrap->paginate(50);

            $servers = Scraper::select('server_id')->whereNotNull('server_id')->groupBy('server_id')->get();

            if ($request->ajax()) {
                return response()->json([
                    'tbody' => view('scrap.partials.scrap-server-status-data', compact('scrappers', 'servers'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                    'links' => (string) $scrappers->render(),
                    'count' => $scrappers->total(),
                ], 200);
            }

            return view('scrap.scrap-server-status', compact('scrappers', 'servers'));
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);

        }
    }

    public function serverStatisticsHistory($scrap_name)
    {
        try {
            $scrap_history = Scraper::where(['scraper_name' => $scrap_name])
                ->where('created_at', '>=', Carbon::now()->subDays(25)->toDateTimeString())
                ->get();

            return new JsonResponse(['status' => 1, 'message' => 'Scrapping history', 'data' => $scrap_history, 'name' => $scrap_name]);
        } catch (Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function getScreenShot(Request $request): View
    {
        $screenshots = ScraperScreenshotHistory::where('scraper_id', $request->id)->latest()->paginate(15);
        $media_screenshot_tag = config('constants.media_screenshot_tag');

        return view('scrap.partials.screenshot-history', compact('screenshots', 'media_screenshot_tag'));
    }

    public function positionHistory(Request $request): View
    {
        $histories = ScraperPositionHistory::where('scraper_id', $request->id)->latest()->paginate(15);

        return view('scrap.partials.position-history', compact('histories'));
    }

    //STRAT - Purpose : Download  Position History - DEVTASK-4086
    public function positionHistorydownload(Request $request): \Illuminate\Http\JsonResponse
    {
        $histories = ScraperPositionHistory::where('scraper_id', $request->id)->latest()->get();

        $chatFileData = '';
        $chatFileData .= html_entity_decode('Scraper Position History', ENT_QUOTES, 'UTF-8');
        $chatFileData .= "\n"."\n";

        if (! $histories->isEmpty()) {
            foreach ($histories as $v) {
                $chatFileData .= html_entity_decode('Scraper Name : '.$v->scraper_name, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n";
                $chatFileData .= html_entity_decode('Comment : '.$v->comment, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n";
                $chatFileData .= html_entity_decode('Created at : '.$v->created_at, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n"."\n";
            }
        }

        $storagelocation = storage_path().'/chatMessageFiles';
        if (! is_dir($storagelocation)) {
            mkdir($storagelocation, 0777, true);
        }
        $filename = 'Scraper_Position_History.txt';
        $file = $storagelocation.'/'.$filename;
        $txt = fopen($file, 'w') || exit('Unable to open file!');
        fwrite($txt, $chatFileData);
        fclose($txt);
        if ($chatFileData == '') {
            return response()->json([
                'downloadUrl' => '',
            ]);
        }

        return response()->json([
            'downloadUrl' => $file,
        ]);
    }

    //END - DEVTASK-4086

    public function taskList(Request $request): View
    {
        $id = $request->id;

        if (isset($request->type) && $request->type == 'brand') {
            $developerTasks = DeveloperTask::where('brand_id', $request->id)->latest()->get();
        } else {
            $developerTasks = DeveloperTask::where('scraper_id', $request->id)->latest()->get();
        }

        $replies = Reply::where('model', 'scrap-statistics')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();

        return view('scrap.partials.task', compact('developerTasks', 'id', 'replies'));
    }

    public function taskListMultiple(Request $request): View
    {
        $id = implode(',', $request->id);

        return view('scrap.partials.task-multiple', compact('id'));
    }

    public function killedList(Request $request): View
    {
        $id = $request->id;

        $histories = ScraperKilledHistory::where('scraper_id', $request->id)->latest()->get();

        return view('scrap.partials.killed', compact('histories', 'id'));
    }

    public function addReply(Request $request): \Illuminate\Http\JsonResponse
    {
        $reply = $request->get('reply');
        $autoReply = [];
        // add reply from here
        if (! empty($reply)) {
            $autoReply = Reply::updateOrCreate(
                ['reply' => $reply, 'model' => 'scrap-statistics', 'category_id' => 1],
                ['reply' => $reply]
            );
        }

        return response()->json(['code' => 200, 'data' => $autoReply]);
    }

    public function deleteReply(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->get('id');

        if ($id > 0) {
            $autoReply = Reply::where('id', $id)->first();
            if ($autoReply) {
                $autoReply->delete();
            }
        }

        return response()->json([
            'code' => 200, 'data' => Reply::where('model', 'scrap-statistics')
                ->whereNull('deleted_at')
                ->pluck('reply', 'id')
                ->toArray(),
        ]);
    }

    public function taskCreateMultiple(Request $request, $id): View
    {
        $requestData = new Request;
        $requestData->setMethod('POST');

        if (isset($request->type) && $request->type == 'brand') {
            $scraper = Brand::whereIn('id', explode(',', $id))->get();
        }

        if ($scraper) {
            foreach ($scraper as $_brand) {
                $requestData->request->add([
                    'priority' => 1,
                    'issue' => 'EXTERNAL SCRAPPER '.$_brand->scraper_name,
                    'status' => 'In Progress',
                    'module' => 'Scraper',
                    'subject' => 'EXTERNAL SCRAPPER '.$_brand->scraper_name,
                    'assigned_to' => $request->get('assigned_to'),
                    'scraper_id' => $_brand->id,
                    'task_type_id' => 1,
                ]);

                if (isset($request->type) && $request->type == 'brand') {
                    $requestData->request->add([
                        'brand_id' => $_brand->id,
                        'scraper_id' => '',
                        'subject' => 'EXTERNAL SCRAPPER '.$_brand->name,
                    ]);
                }

                app(DevelopmentController::class)->issueStore($requestData, 'issue');
            }
        }

        return view('scrap.partials.task-multiple', compact('id'));
    }

    public function taskCreate(Request $request, $id): View
    {
        $requestData = new Request;
        $requestData->setMethod('POST');

        $scraper = Scraper::find($id);

        if (isset($request->type) && $request->type == 'brand') {
            $scraper = Brand::find($id);
        }

        if ($scraper) {
            $requestData->request->add([
                'priority' => 1,
                'issue' => $request->task_description,
                'status' => 'In Progress',
                'module' => 'Scraper',
                'subject' => $scraper->scraper_name.' - '.$request->task_subject,
                'assigned_to' => $request->get('assigned_to'),
                'scraper_id' => $id,
                'task_type_id' => 1,
            ]);

            if (isset($request->type) && $request->type == 'brand') {
                $requestData->request->add([
                    'brand_id' => $id,
                    'scraper_id' => '',
                    'subject' => 'EXTERNAL SCRAPPER '.$scraper->name.' - '.$request->task_subject,
                ]);
            }

            app(DevelopmentController::class)->issueStore($requestData, 'issue');
        }

        if (isset($request->type) && $request->type == 'brand') {
            $developerTasks = DeveloperTask::where('brand_id', $request->id)->latest()->get();
        } else {
            $developerTasks = DeveloperTask::where('scraper_id', $request->id)->latest()->get();
        }

        $replies = Reply::where('model', 'scrap-statistics')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();

        return view('scrap.partials.task', compact('developerTasks', 'id', 'replies'));
    }

    public function autoRestart(Request $request): RedirectResponse
    {
        if ($request->status == 'on') {
            Scraper::update(['auto_restart' => 1]);
        } else {
            Scraper::update(['auto_restart' => 0]);
        }

        return redirect()->back();
    }

    public function positionAll(): \Illuminate\Http\JsonResponse
    {
        $histories = ScraperPositionHistory::whereDate('created_at', now()->subDays(7)->format('Y-m-d'))->latest()->get();
        $chatFileData = '';
        $chatFileData .= html_entity_decode('Scraper Position History', ENT_QUOTES, 'UTF-8');
        $chatFileData .= "\n"."\n";

        if (! $histories->isEmpty()) {
            foreach ($histories as $v) {
                $chatFileData .= html_entity_decode('Scraper Name : '.$v->scraper_name, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n";
                $chatFileData .= html_entity_decode('Comment : '.$v->comment, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n";
                $chatFileData .= html_entity_decode('Created at : '.$v->created_at, ENT_QUOTES, 'UTF-8');
                $chatFileData .= "\n"."\n";
            }
        }

        $storagelocation = storage_path().'/chatMessageFiles';
        if (! is_dir($storagelocation)) {
            mkdir($storagelocation, 0777, true);
        }
        $filename = 'Scraper_Position_History.txt';
        $file = $storagelocation.'/'.$filename;
        $txt = fopen($file, 'w') || exit('Unable to open file!');
        fwrite($txt, $chatFileData);
        fclose($txt);
        if ($chatFileData == '') {
            return response()->json([
                'downloadUrl' => '',
            ]);
        }

        return response()->json([
            'downloadUrl' => $file,
        ]);
    }

    public function serverStatusHistory(Request $request): View
    {
        $statusHistory = ScraperServerStatusHistory::whereDate('created_at', $request->date)->latest()->get();

        return view('scrap.partials.status-history', compact('statusHistory'));
    }

    public function serverStatusProcess(Request $request): View
    {
        $statusHistory = ScraperProcess::whereDate('created_at', $request->date)->orderBy('scraper_name')->latest()->get();

        return view('scrap.partials.process-status-history', compact('statusHistory'));
    }

    public function getScraperServerTiming(Request $request): View
    {
        Artisan::call('check:scraper-running-status');

        $statusHistory = ScraperServerStatusHistory::where('scraper_name', $request->scraper_name)->latest()->get();

        return view('scrap.partials.status-history', compact('statusHistory'));
    }

    public function getLastErrors(Request $request): View
    {
        $remarks = ScrapRemark::where('scrap_field', 'last_line_error')->where('scrap_id', $request->id)->get();

        return view('scrap.partials.scrap-remarks', compact('remarks'));
    }

    public function logDetails(Request $request): View
    {
        $logDetails = ScrapLog::where('scraper_id', $request->scrapper_id)->latest()->get();

        return view('scrap.partials.log-details', compact('logDetails'));
    }

    public function scrapperLogList(Request $request): View
    {
        $logDetails = ScrapLog::leftJoin('scrapers', 'scrapers.id', '=', 'scrap_logs.scraper_id')
            ->whereNull('folder_name')->select('scrap_logs.*', 'scrapers.scraper_name');

        $scrapname = '';
        $scrapdate = '';

        if ($request->scraper_name) {
            $scrapname = $request->scraper_name;
            $logDetails->where('scrapers.scraper_name', 'LIKE', '%'.$request->scraper_name.'%');
        }

        if ($request->created_at) {
            $scrapdate = $request->created_at;

            $logDetails->whereDate('scrap_logs.created_at', $request->created_at);
        }

        $logDetails = $logDetails->orderByDesc('id')->paginate(50)->appends(request()->query());

        return view('scrap.log_list', compact('logDetails', 'scrapname', 'scrapdate'));
    }

    public function serverHistory(Request $request): View
    {
        $requestedDate = request('planned_at', date('Y-m-d'));

        $totalServers = ScraperServerStatusHistory::groupBy('server_id')->pluck('server_id')->toArray();

        $timeSlots = [];
        $listOfServerUsed = [];
        for ($i = 0; $i < 24; $i++) {
            $tms = strlen($i) > 1 ? $i : '0'.$i;
            $timeSlots["$tms"] = $tms;
            // check the scrapper which run on current time
            $scrapers = ScraperServerStatusHistory::runOnGiveTime($requestedDate, $tms);
            if (! $scrapers->isEmpty()) {
                foreach ($scrapers as $s) {
                    $listOfServerUsed["$tms"][$s->server_id][] = [
                        'scraper_name' => $s->scraper_name,
                        'memory_string' => 'T: '.$s->total_memory.' U:'.$s->used_memory.' P:'.$s->in_percentage,
                        'pid' => $s->pid,
                    ];
                }
            }
        }

        return view('scrap.server-history', compact('totalServers', 'timeSlots', 'requestedDate', 'listOfServerUsed'));
    }

    public function endJob(Request $request): \Illuminate\Http\JsonResponse
    {
        $pid = $request->get('pid');
        $server = $request->get('server_id');

        $cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/scraper-kill.sh '.$server.' '.$pid.' 2>&1';

        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        Log::info(print_r($result, true));

        return response()->json(['code' => 200, 'message' => 'Your job has been stopped']);
    }

    //START - Purpose : Add get data for scrappers - DEVTASK-20102
    public function view_scrappers_data(Request $request): View
    {
        $scraper_proc = [];

        $scraper_process = ScraperProcess::where('scraper_name', '!=', '')->orderByDesc('started_at')->get()->unique('scraper_id');
        foreach ($scraper_process as $key => $sp) {

            $to = Carbon::createFromFormat('Y-m-d H:s:i', $sp->started_at);
            $from = Carbon::now();
            $diff_in_hours = $to->diffInMinutes($from);
            if ($diff_in_hours > 1440) {
                array_push($scraper_proc, $sp);
            }
        }
        $users = User::pluck('name', 'id')->toArray();
        $scrapers = Scraper::leftJoin('users', 'users.id', '=', 'scrapers.assigned_to')->whereNotIn('id', $scraper_process->pluck('scraper_id'))->select('scrapers.*', 'users.email as assignedTo')->get();

        return view('scrap.scraper-process-list', compact('scraper_process', 'scrapers', 'users'));
    }

    //END - DEVTASK-20102

    public function assignScrapperIssue(Request $request)
    {
        $assigendTo = $request->assigned_to;
        $scrapperDetails = Scraper::where('id', $request->scrapper_id)->first();
        if ($assigendTo != null && $scrapperDetails != null) {
            $hasAssignedIssue = DeveloperTask::where('scraper_id', $scrapperDetails->scrapper_id)->where('assigned_to', $assigendTo)
                ->where('is_resolved', 0)->first();
            if (! $hasAssignedIssue) {
                $requestData = new Request;
                $requestData->setMethod('POST');
                $requestData->request->add([
                    'priority' => 1,
                    'issue' => "Scraper didn't Run In Last 24 Hr",
                    'status' => 'Planned',
                    'module' => 'Scraper',
                    'subject' => $scrapperDetails->scraper_name,
                    'assigned_to' => $assigendTo,
                ]);
                app(DevelopmentController::class)->issueStore($requestData, $assigendTo);
            }
            Scraper::where('id', $request->scrapper_id)->update(['assigned_to' => $assigendTo]);
        }

        return 'success';
    }

    public function changeUser()
    {
        // $insert = DB::insert('insert into `developer_tasks` (`priority`, `subject`, `task`, `responsible_user_id`, `assigned_to`, `module_id`, `user_id`, `assigned_by`, `created_by`, `reference`, `status`, `task_type_id`, `scraper_id`, `brand_id`, `updated_at`, `created_at`,`parent_id`,`estimate_date`,hubstaff_task_id)
        // select `priority`, `subject`, `task`, `responsible_user_id`, "500", `module_id`, `user_id`, `assigned_by`, `created_by`, `reference`, `status`, `task_type_id`, `scraper_id`, `brand_id`, `updated_at`, `created_at`,`parent_id`,`estimate_date`,hubstaff_task_id from `developer_tasks` where`assigned_to` = 472 and `status` = "In Progress"');

        $tasks = DeveloperTask::where('assigned_to', 472)
            ->where('status', 'In Progress')
            ->get();

        foreach ($tasks as $task) {
            $newTask = new DeveloperTask;
            $newTask->priority = $task->priority;
            $newTask->subject = $task->subject;
            $newTask->task = $task->task;
            $newTask->responsible_user_id = $task->responsible_user_id;
            $newTask->assigned_to = 500; // Change the assigned_to value
            $newTask->module_id = $task->module_id;
            $newTask->user_id = $task->user_id;
            $newTask->assigned_by = $task->assigned_by;
            $newTask->created_by = $task->created_by;
            $newTask->reference = $task->reference;
            $newTask->status = $task->status;
            $newTask->task_type_id = $task->task_type_id;
            $newTask->scraper_id = $task->scraper_id;
            $newTask->brand_id = $task->brand_id;
            $newTask->updated_at = $task->updated_at;
            $newTask->created_at = $task->created_at;
            $newTask->parent_id = $task->parent_id;
            $newTask->estimate_date = $task->estimate_date;
            $newTask->hubstaff_task_id = $task->hubstaff_task_id;
            $newTask->save();
        }

        echo 'Data inserted successfully';
        exit;
    }
}
