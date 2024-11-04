<?php

namespace App\Http\Controllers;

use App\GTMatrixErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GTMatrixErrorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'GTmetrix Error';

        return view('gtmetrix.gtmetrix-error', compact('title'));
    }

    public function listGTmetrixError(Request $request)
    {
        if ($request->ajax()) {
            $items = GTMatrixErrorLog::select('id', 'store_viewGTM_id', 'error_title', 'error', 'created_at')->orderByDesc('id');
            if (isset($request->name) && ! empty($request->name)) {
                $items->where('error_title', 'Like', '%'.$request->name.'%')->orWhere('error', 'Like', '%'.$request->name.'%')->orWhere('store_viewGTM_id', 'Like', '%'.$request->name.'%');
            }

            return datatables()->eloquent($items)->toJson();
        }

        return redirect()->back()->with('error', 'Somthing wrong here to access GTMetrix error');
    }

    public function truncateTables(Request $request): JsonResponse
    {
        DB::statement('TRUNCATE TABLE g_t_matrix_error_logs');

        return response()->json([
            'status' => true,
            'message' => ' Your selected batabase tables has been truncate successfully',
            'status_name' => 'success',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(GTMatrixErrorLog $gTMatrixErrorLog)
    {
        //
    }
}
