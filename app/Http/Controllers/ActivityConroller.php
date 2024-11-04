<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Benchmark;
use App\Helpers\StatusHelper;
use App\LogScraperVsAi;
use App\Product;
use App\ScrapedProducts;
use App\User;
use App\Supplier;
use App\ListingHistory;
use App\Productactivitie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityConroller extends Controller
{
    private $dataLabelDay = [
        '12:00 am',
        '1:00 am',
        '2:00 am',
        '3:00 am',
        '4:00 am',
        '5:00 am',
        '6:00 am',
        '7:00 am',
        '8:00 am',
        '9:00 am',
        '10:00 am',
        '11:00 am',
        '12:00 pm',
        '1:00 pm',
        '2:00 pm',
        '3:00 pm',
        '4:00 pm',
        '5:00 pm',
        '6:00 pm',
        '7:00 pm',
        '8:00 pm',
        '9:00 pm',
        '10:00 pm',
        '11:00 pm',
    ];

    private $dataLabelMonth = [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
    ];

    public function __construct()
    {
        $this->middleware('permission:view-activity', ['only' => ['index', 'store']]);
    }

    public function showActivity(Request $request): View
    {
        // Set range start and range end
        $range_start = $request->input('range_start');
        $range_end = $request->input('range_end');

        // Set empty AI activity
        $aiActivity = [];

        // Get total row count for products
        $aiActivity['total'] = 0;
        $aiActivity['total_range'] = 0;

        // Get ai activity
        $logScraperVsAi = LogScraperVsAi::selectRaw('DISTINCT(product_id) AS product_id')->get();
        $aiActivity['ai'] = $logScraperVsAi->count();
        $logScraperVsAi = LogScraperVsAi::selectRaw('DISTINCT(product_id) AS product_id')->whereBetween('created_at', [$range_start.' 00:00', $range_end.' 23:59'])->get();
        $aiActivity['ai_range'] = $logScraperVsAi->count();

        // Free up memory by unsetting unused variables
        unset($products);
        unset($logScraperVsAi);

        $allActivity = ListingHistory::selectRaw('
	        SUM(case when action = "CROP_APPROVAL" then 1 Else 0 End) as crop_approved,
	        SUM(case when action = "CROP_APPROVAL_DENIED" then 1 Else 0 End) as crop_approval_denied,
	        SUM(case when action = "CROP_APPROVAL_CONFIRMATION" then 1 Else 0 End) as crop_approval_confirmation,
            SUM(case when action = "CROP_REJECTED"  then 1 Else 0 End) as crop_rejected,
            SUM(case when action = "CROP_SEQUENCED" then 1 Else 0 End) as crop_ordered,
            SUM(case when action = "LISTING_APPROVAL" then 1 Else 0 End) as attribute_approved,
            SUM(case when action = "LISTING_REJECTED" then 1 Else 0 End) as attribute_rejected,
            SUM(case when action = "MAGENTO_LISTED" then 1 Else 0 End) as magento_listed
	    ');

        $activity = ListingHistory::selectRaw('
            user_id,
            SUM(case when action = "CROP_APPROVAL" then 1 Else 0 End) as crop_approved,
            SUM(case when action = "CROP_APPROVAL_DENIED" then 1 Else 0 End) as crop_approval_denied,
            SUM(case when action = "CROP_APPROVAL_CONFIRMATION" then 1 Else 0 End) as crop_approval_confirmation,
            SUM(case when action = "CROP_REJECTED"  then 1 Else 0 End) as crop_rejected,
            SUM(case when action = "CROP_SEQUENCED" then 1 Else 0 End) as crop_ordered,
            SUM(case when action = "LISTING_APPROVAL" then 1 Else 0 End) as attribute_approved,
            SUM(case when action = "LISTING_REJECTED" then 1 Else 0 End) as attribute_rejected,
            SUM(case when action = "MAGENTO_LISTED" then 1 Else 0 End) as magento_listed
        ')->whereNotNull('user_id');

        $ca = Product::where('is_image_processed', 1)
            ->where('is_crop_rejected', 0)
            ->where('is_crop_approved', 0)
            ->where('is_crop_being_verified', 0)
            ->whereDoesntHave('amends')->count();

        $productStats = StatusHelper::getStatusCount();
        $productStatsDateRange = StatusHelper::getStatusCountByDateRange($range_start, $range_end);

        if (is_array($request->get('selected_user'))) {
            $activity = $activity->whereIn('user_id', $request->get('selected_user'));
        }

        $users = $this->getUserArray();
        $selected_user = $request->input('selected_user');

        $scrapCount = new ScrapedProducts;
        $inventoryCount = new ScrapedProducts;
        $rejectedListingsCount = Product::where('is_listing_rejected', 1);

        // Get total number of scraped products
        // $sqlScrapedProductsInStock = "
        //         SELECT
        //             COUNT(DISTINCT(ls.sku)) as ttl
        //         FROM
        //             suppliers s
        //         JOIN
        //             scrapers sc
        //         ON
        //             s.id=sc.supplier_id
        //         JOIN
        //             scraped_products ls
        //         ON
        //             ls.website=sc.scraper_name
        //         WHERE
        //             s.supplier_status_id=1 AND
        //             ls.validated=1 AND
        //             ls.website!='internal_scraper' AND
        //             ls.last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
        //     ";
        // $resultScrapedProductsInStock = DB::select($sqlScrapedProductsInStock);

        $resultScrapedProductsInStock = Supplier::join('scrapers as sc', 'suppliers.id', '=', 'sc.supplier_id')
            ->join('scraped_products as ls', 'ls.website', '=', 'sc.scraper_name')
            ->where('suppliers.supplier_status_id', 1)
            ->where('ls.validated', 1)
            ->where('ls.website', '!=', 'internal_scraper')
            ->where('ls.last_inventory_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)'))
            ->select(DB::raw('COUNT(DISTINCT(ls.sku)) as ttl'))
            ->first();

        // If you need the ttl value specifically
        $ttl = $resultScrapedProductsInStock->ttl;

        if ($range_start != '' && $range_end != '') {
            $activity = $activity->where(function ($query) use ($range_end, $range_start) {
                $query->whereBetween('created_at', [$range_start.' 00:00', $range_end.' 23:59']);
            });

            $allActivity = $allActivity->whereBetween('created_at', [$range_start.' 00:00', $range_end.' 23:59']);
            $scrapCount = $scrapCount->whereBetween('created_at', [$range_start.' 00:00', $range_end.' 23:59']);
            $inventoryCount = $inventoryCount->whereBetween('last_inventory_at', [$range_start.' 00:00', $range_end.' 23:59']);
            $rejectedListingsCount = $rejectedListingsCount->whereBetween('listing_rejected_on', [$range_start.' 00:00', $range_end.' 23:59']);
        }

        if (! $range_start || ! $range_end) {
            $inventoryCount = $inventoryCount->whereRaw('TIMESTAMPDIFF(HOUR, last_inventory_at, NOW())<= 48');
            $scrapCount = $scrapCount->where('created_at', 'LIKE', '%'.date('Y-m-d').'%');
        }

        $scrapCount = $scrapCount->count();
        $inventoryCount = $inventoryCount->count();
        $rejectedListingsCount = $rejectedListingsCount->count();

        $allActivity = $allActivity->first();
        $userActions = $activity->groupBy('user_id')->get();

        $cropCountPerMinute = Product::whereRaw('TIMESTAMPDIFF(DAY, cropped_at, NOW()) IN (0,1)')->count();
        $cropCountPerMinute = round($cropCountPerMinute / 1440, 4);

        return view('activity.index', compact('resultScrapedProductsInStock', 'aiActivity', 'userActions', 'users', 'selected_user', 'range_end', 'range_start', 'allActivity', 'scrapCount', 'inventoryCount', 'rejectedListingsCount', 'productStats', 'productStatsDateRange', 'cropCountPerMinute'));
    }

    public function showGraph(Request $request): View
    {
        $data['date_type'] = $request->input('date_type') ?? 'week';

        $data['week_range'] = $request->input('week_range') ?? date('Y-\WW');
        $data['month_range'] = $request->input('month_range') ?? date('Y-m');

        if ($data['date_type'] == 'week') {
            $weekRange = $this->getStartAndEndDateByWeek($data['week_range']);
            $start = $weekRange['start_date'];
            $end = $weekRange['end_date'];

            $workDoneResult = Activity::where('description', 'create')
                ->whereBetween('created_at', [$start, $end])
                ->select('activities.subject_id', 'activities.subject_type', 'activities.created_at', DB::raw('WEEKDAY(created_at) as xaxis, count(*) as total'))
                ->groupByRaw('WEEKDAY(created_at)')
                ->get();

            $benchmarkResult = Benchmark::selectRaw('WEEKDAY(for_date) as day,
								sum(selections + searches + attributes + supervisor + imagecropper + lister + approver + inventory) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupByRaw('WEEKDAY(for_date)')
                ->get();

            $workDone = [];
            $dowMap = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            foreach ($workDoneResult as $item) {
                $workDone[$dowMap[$item->xaxis]] = $item->total;
            }

            $benchmark = [];

            foreach ($benchmarkResult as $item) {
                $benchmark[$dowMap[$item->day]] = $item->total;
            }
        } else {
            $monthRange = $this->getStartAndEndDateByMonth($data['month_range']);
            $start = $monthRange['start_date'];
            $end = $monthRange['end_date'];

            $workDoneResult = Activity::where('description', 'create')
                ->whereBetween('created_at', [$start, $end])
                ->select('activities.subject_id', 'activities.subject_type', 'activities.created_at', DB::raw('DAYOFMONTH(created_at) as xaxis ,COUNT(*) AS total'))
                ->groupByRaw('DAYOFMONTH(created_at)')
                ->get();

            $benchmarkResult = Benchmark::selectRaw('DAYOFMONTH(for_date) as day,
								sum(selections + searches + attributes + supervisor + imagecropper + lister + approver + inventory) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupByRaw('DAYOFMONTH(for_date)')
                ->get();

            foreach ($workDoneResult as $item) {
                $workDone[$item->xaxis] = $item->total;
            }

            foreach ($benchmarkResult as $item) {
                $benchmark[$item->day] = $item->total;
            }
        }

        $data['benchmark'] = $benchmark ?? [];
        $data['workDone'] = $workDone ?? [];

        return view('activity.graph', $data);
    }

    public function showUserGraph(Request $request): View
    {
        $data['users'] = $this->getUserArray();
        $data['selected_user'] = $request->input('selected_user') ?? 3;

        $data['date_type'] = $request->input('date_type') ?? 'day';

        $data['day_range'] = $request->input('day_range') ?? date('Y-m-d');
        $data['month_range'] = $request->input('month_range') ?? date('Y-m');

        if ($data['date_type'] == 'day') {
            $start = $data['day_range'].' 00:00:00.000000';
            $end = $data['day_range'].' 23:59:59.000000';

            // $workDoneResult = DB::select('
            // 						SELECT HOUR(created_at) as xaxis,subject_type ,COUNT(*) AS total FROM
            // 					 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
            // 					  		 FROM activities
            // 					  		 WHERE activities.description = "create"
            // 					  		 AND activities.causer_id = ?
            // 					  		 AND activities.created_at BETWEEN ? AND ?)
            // 					    AS SUBQUERY
            // 					   	GROUP BY HOUR(created_at),subject_type ORDER By xaxis;
            // 				', [$data['selected_user'], $start, $end]);

            

            $workDoneResult = Activity::selectRaw('HOUR(created_at) as xaxis, subject_type, COUNT(*) AS total')
                ->distinct()
                ->where('description', 'create')
                ->where('causer_id', $data['selected_user'])
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(DB::raw('HOUR(created_at)'), 'subject_type')
                ->orderBy('xaxis')
                ->get();

            $workDone = [];

            foreach ($workDoneResult as $item) {
                $workDone[$item->subject_type][$item->xaxis] = $item->total;
            }

            foreach ($workDone as $subject_type => $subject_type_array) {
                for ($i = 0; $i <= 23; $i++) {
                    $workDone[$subject_type][$i] = $subject_type_array[$i] ?? 0;
                }
            }
        } else {
            $monthRange = $this->getStartAndEndDateByMonth($data['month_range']);
            $start = $monthRange['start_date'];
            $end = $monthRange['end_date'];

            // $workDoneResult = DB::select('
            // 						SELECT DAYOFMONTH(created_at) as xaxis,subject_type ,COUNT(*) AS total FROM
            // 					 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
            // 					  		 FROM activities
            // 					  		 WHERE activities.description = "create"
            // 					  		 AND activities.causer_id = ?
            // 					  		 AND activities.created_at BETWEEN ? AND ?)
            // 					    AS SUBQUERY
            // 					   	GROUP BY DAYOFMONTH(created_at),subject_type ORDER By xaxis;
            // 				', [$data['selected_user'], $start, $end]);

            $workDoneResult = Activity::selectRaw('DAYOFMONTH(created_at) as xaxis, subject_type, COUNT(DISTINCT subject_id) as total')
                ->where('description', 'create')
                ->where('causer_id', $data['selected_user'])
                ->whereBetween('created_at', [$start, $end])
                ->groupBy(DB::raw('DAYOFMONTH(created_at)'), 'subject_type')
                ->orderBy('xaxis')
                ->get();

            $workDone = [];

            foreach ($workDoneResult as $item) {
                $workDone[$item->subject_type][$item->xaxis] = $item->total;
            }

            foreach ($workDone as $subject_type => $subject_type_array) {
                for ($i = 1; $i <= 31; $i++) {
                    $workDone[$subject_type][$i] = $subject_type_array[$i] ?? 0;
                }
            }
        }

        $data['workDone'] = $workDone ?? [];
        $data['dataLabel'] = $data['date_type'] == 'day' ? $this->dataLabelDay : $this->dataLabelMonth;

        return view('activity.graph-user', $data);
    }

    public function getUserArray()
    {
        $users = User::all();

        $userArray = [];

        foreach ($users as $user) {
            $userArray[((string) $user->id)] = $user->name;
        }

        return $userArray;
    }

    public function getStartAndEndDateByWeek($week_range)
    {
        $arr = explode('-', $week_range);

        $week = str_replace('W', '', $arr[1]);
        $year = $arr[0];

        $dateTime = new \DateTime;
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format('Y-m-d').' 00:00:00.000000';
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format('Y-m-d').' 23:59:59.000000';

        return $result;
    }

    public function getStartAndEndDateByMonth($month_range)
    {
        $arr = explode('-', $month_range);

        $year = $arr[0];
        $month = $arr[1];

        $dateTime = new \DateTime;
        $dateTime->setDate($year, $month, 1);
        $result['start_date'] = $dateTime->format('Y-m-d').' 00:00:00.000000';
        $dateTime->modify('+1 month');
        $dateTime->modify('-1 days');
        $result['end_date'] = $dateTime->format('Y-m-d').' 23:59:59.000000';

        return $result;
    }

    public function recentActivities(Request $request)
    {
        $productStats = Productactivitie::where('status_id', $request->type)
            ->whereDate('created_at', '>', Carbon::now()->subDays(10))
            ->orderByDesc('created_at')
            ->get();

        return $productStats;
    }
}
