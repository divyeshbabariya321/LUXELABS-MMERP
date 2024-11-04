<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\GoogleDeveloperLogs;
use Illuminate\Http\Request;

class GoogleDeveloperLogsController extends Controller
{
    public function index(): View
    {
        $id         = 0;
        $anrcrashes = GoogleDeveloperLogs::get();

        return view('google.developer-api.logs', ['anrcrashes' => $anrcrashes, 'id' => $id]);
    }

    public function logsfilter(Request $request): View
    {
        $anrcrashes = new GoogleDeveloperLogs();
        if ($request->input('app_name')) {
            $app_name   = $request->input('app_name');
            $anrcrashes = $anrcrashes->Where('log_name', 'like', '%' . $app_name . '%');
        }
        if ($request->input('date')) {
            $date       = $request->input('date');
            $anrcrashes = $anrcrashes->Where('created_at', 'like', '%' . $date . '%');
        }
        $id         = 0;
        $anrcrashes = $anrcrashes->get();

        return view('google.developer-api.logs', ['anrcrashes' => $anrcrashes, 'id' => $id]);
    }
}
