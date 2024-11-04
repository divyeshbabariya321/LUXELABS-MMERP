<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\GoogleAdsGroup;
use App\GoogleAdsAccount;
use App\GoogleAdsCampaign;
use App\GoogleAdsReporting;
use Illuminate\Http\Request;

class GoogleAdReportController extends Controller
{
    public function index(Request $request): View
    {
        $accounts  = GoogleAdsAccount::all();
        $campaigns = GoogleAdsCampaign::has('account')->with('account')->get();
        $adgroups  = GoogleAdsGroup::has('campaign')->with('campaign', 'campaign.account')->get();

        $records = GoogleAdsReporting::select(
            'google_ads_reportings.*',
            DB::raw('SUM(average_cpc) as sum_average_cpc'),
            DB::raw('SUM(cost_micros) as sum_cost_micros'),
            DB::raw('SUM(impression) as sum_impression'),
            DB::raw('SUM(click) as sum_click')
        )
            ->has('adgroup')
            ->with('account', 'campaign', 'adgroup')->latest();

        if (! empty($request->campaign_id)) {
            $records->where('adgroup_google_campaign_id', $request->campaign_id);
        }
        if (! empty($request->adgroup_id)) {
            $records->where('google_adgroup_id', $request->adgroup_id);
        }
        if (! empty($request->account_id)) {
            $records->where('google_account_id', $request->account_id);
        }

        if (! empty($request->campaign_status)) {
            $records->whereHas('campaign', function ($q) use ($request) {
                $q->where('status', $request->campaign_status);
            });
        }

        if (! empty($request->start_date)) {
            $records->where('date', '>=', $request->start_date);
        }

        if (! empty($request->end_date)) {
            $records->where('date', '<=', $request->end_date);
        }

        $records = $records->groupBy('google_ad_id')->paginate(20)->appends(request()->except(['page']));

        $totalNumEntries = count($records);

        return view('google_ad_report.index', compact('records', 'totalNumEntries', 'campaigns', 'adgroups', 'accounts'));
    }
}
