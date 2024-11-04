<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Setting;
use App\GoogleBigQueryData;
use Illuminate\Http\Request;
use App\Models\DataTableColumn;
use Exception;

class GoogleBigQueryDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $bigData            = GoogleBigQueryData::paginate(Setting::get('pagination'));
        $google_project_ids = GoogleBigQueryData::select('google_project_id')->distinct('google_project_id')->get();
        $platforms          = GoogleBigQueryData::select('platform')->distinct('platform')->get();
        $event_ids          = GoogleBigQueryData::select('event_id')->distinct('event_id')->get();

        $datatableModel = DataTableColumn::select('column_name')
            ->where('user_id', auth()->user()->id)
            ->where('section_name', 'google-bigdata-bigquery')
            ->first();

        $dynamicColumnsToShowb = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns           = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowb = json_decode($hideColumns, true);
        }

        return view('google.big_data.index', compact('bigData', 'google_project_ids', 'platforms', 'event_ids', 'dynamicColumnsToShowb'));
    }

    public function columnVisibilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'google-bigdata-bigquery')->first();

        if ($userCheck) {
            $column               = DataTableColumn::find($userCheck->id);
            $column->section_name = 'google-bigdata-bigquery';
            $column->column_name  = json_encode($request->column_data);
            $column->save();
        } else {
            $column               = new DataTableColumn();
            $column->section_name = 'google-bigdata-bigquery';
            $column->column_name  = json_encode($request->column_data);
            $column->user_id      = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'Column visiblity added successfully!');
    }

    public function search(Request $request): View
    {
        $bigData = new GoogleBigQueryData();
        if (! empty($request->project_id)) {
            $bigData = $bigData->whereIn('google_project_id', $request->project_id);
        }
        if (! empty($request->platform)) {
            $bigData = $bigData->whereIn('platform', $request->platform);
        }
        if (! empty($request->event_id)) {
            $bigData = $bigData->whereIn('event_id', $request->event_id);
        }
        $bigData            = $bigData->paginate(Setting::get('pagination'));
        $google_project_ids = GoogleBigQueryData::select('google_project_id')->distinct('google_project_id')->get();
        $platforms          = GoogleBigQueryData::select('platform')->distinct('platform')->get();
        $event_ids          = GoogleBigQueryData::select('event_id')->distinct('event_id')->get();

        return view('google.big_data.index', compact('bigData', 'google_project_ids', 'platforms', 'event_ids'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoogleBigQueryData $googleBigQueryData, Request $request): JsonResponse
    {
        try {
            $bigData = GoogleBigQueryData::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $bigData, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
