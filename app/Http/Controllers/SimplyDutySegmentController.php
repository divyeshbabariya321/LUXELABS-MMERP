<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Setting;
use App\SimplyDutySegment;
use Illuminate\Http\Request;

class SimplyDutySegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $segments = SimplyDutySegment::paginate(Setting::get('pagination'));

        return view('simplyduty.segment.index', compact('segments'));
    }

    public function segment_add(Request $request): JsonResponse
    {
        $id      = $request->segment_id;
        $segment = $request->segment;
        $price   = $request->price;
        if ($id == 0) {
            SimplyDutySegment::insert(['segment' => $segment, 'price' => $price]);

            return response()->json(['success' => true, 'message' => 'Segment Updated Successfully']);
        } else {
            SimplyDutySegment::where('id', $id)->update(['segment' => $segment, 'price' => $price]);

            return response()->json(['success' => true, 'message' => 'Segment Updated Successfully']);
        }
    }

    public function segment_delete(Request $request): JsonResponse
    {
        $id = $request->segment_id;
        if ($id > 0) {
            SimplyDutySegment::where('id', $id)->delete();

            return response()->json(['success' => true, 'message' => 'Segment Deleted Successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid']);
        }
    }
}
