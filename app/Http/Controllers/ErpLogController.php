<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\ErpLog;

class ErpLogController extends Controller
{
    public function index(): View
    {
        $erpLogData = ErpLog::all()->toArray();

        return view('erp-log.index', compact('erpLogData'));
    }
}
