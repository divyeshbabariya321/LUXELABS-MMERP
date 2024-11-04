<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Criteria;
use App\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request_object = $request->all();
        $position       = [
            'user_id' => Auth::id(),
            'title'   => $request_object['title'],
        ];
        $position_id = Position::create($position);
        if (! empty($request_object['criteria'])) {
            $request_object['criteria'] = explode(',', $request_object['criteria']);
            foreach ($request_object['criteria'] as $criteria) {
                $criteria_array = [
                    'title'       => $criteria,
                    'position_id' => $position_id->id,
                ];
                Criteria::create($criteria_array);
            }
        }

        return redirect()->route('vendors.index')->withSuccess('Position added successfully');
    }

    public function list($id = null): JsonResponse
    {
        $criterias = Criteria::where('position_id', $id)->get();

        return response()->json(['code' => 200, 'data' => $criterias, 'message' => 'Success']);
    }
}
