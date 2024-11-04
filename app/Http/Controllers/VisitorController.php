<?php

namespace App\Http\Controllers;

use App\Setting;
use App\VisitorLog;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ip || $request->browser || $request->location) {
            $query = VisitorLog::query();

            if (request('ip') != null) {
                $query->where('ip', 'LIKE', "%{$request->ip}%");
            }

            if (request('browser') != null) {
                $query->where('browser', 'LIKE', "%{$request->browser}%");
            }

            if (request('location') != null) {
                $query->where('browser', 'LIKE', "%{$request->location}%");
            }

            $paginate = (Setting::get('pagination') * 10);
            $logs     = $query->paginate($paginate)->appends(request()->except(['page']));
        } else {
            $paginate = (Setting::get('pagination') * 10);
            $logs     = VisitorLog::orderby('created_at', 'desc')->paginate($paginate);
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.visitordata', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('logging.visitorlog', compact('logs'));
    }
}
