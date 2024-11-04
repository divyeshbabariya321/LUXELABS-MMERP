<?php

namespace App\Http\Controllers;

use App\Models\InventoryUpdateLog;
use DataTables;
use Illuminate\Http\Request;

class InventoryUpdateLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function grid(Request $request)
    {
        if ($request->ajax()) {
            $query = InventoryUpdateLog::query();
            //dd($request->all());
            if (isset($request->filterbydate) && ! empty($request->filterbydate)) {
                //dd(\Carbon\Carbon::parse($request->filterbydate)->format('Y-m-d'));
                $query->whereDate('created_at', \Carbon\Carbon::parse($request->filterbydate)->format('Y-m-d'));
            }
            $query->orderByDesc('id');

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
                ->addColumn('logtype', function ($row) {
                    return $row->logtype;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->addColumn('datacontent', function ($row) {
                    return $row->datacontent;
                })
                ->rawColumns(['id', 'logtype', 'datacontent', 'created_at'])
                ->make(true);
        }

        return view('inventory_update_log.grid');
    }
}
