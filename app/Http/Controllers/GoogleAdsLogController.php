<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Models\GoogleAdsLog;
use Illuminate\Http\Request;

class GoogleAdsLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = GoogleAdsLog::with('user')->orderby('created_at', 'desc');

        if (! empty($request->type)) {
            $logs->where('type', 'LIKE', '%' . $request->type . '%');
        }

        if (! empty($request->module)) {
            $logs->where('module', 'LIKE', '%' . $request->module . '%');
        }

        if (! empty($request->message)) {
            $logs->where('message', 'LIKE', '%' . $request->message . '%');
        }

        if (! empty($request->created_at)) {
            $logs->whereDate('created_at', $request->created_at);
        }

        if (! empty($request->user_name)) {
            $logs->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->user_name . '%');
            });
        }

        $paginate = (Setting::get('pagination') * 10);
        $logs     = $logs->paginate($paginate)->appends(request()->except(['page']));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('google_ads_log.partials.list', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('google_ads_log.index', compact('logs'));
    }
}
