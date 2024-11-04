<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use App\Setting;
use App\LogRequest;
use App\SimplyDutyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SimplyDutyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->code || $request->description) {
            $query = SimplyDutyCategory::query();

            if (request('code') != null) {
                $query->where('code', request('code'));
            }
            if (request('description') != null) {
                $query->where('description', 'LIKE', "%{$request->description}%");
            }
            $categories = $query->paginate(Setting::get('pagination'));
        } else {
            $categories = SimplyDutyCategory::paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('simplyduty.category.partials.data', compact('categories'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }

        return view('simplyduty.category.index', compact('categories'));
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
    public function show(SimplyDutyCategory $simplyDutyCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(SimplyDutyCategory $simplyDutyCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SimplyDutyCategory $simplyDutyCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SimplyDutyCategory $simplyDutyCategory)
    {
        //
    }

    public function getCategoryFromApi(): JsonResponse
    {
        $startTime    = date('Y-m-d H:i:s', LARAVEL_START);
        $url          = 'https://www.api.simplyduty.com/api/Supporting/categories';
        $response     = Http::get($url);
        $responseData = $response->json();

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $responseData, $response->status(), SimplyDutyCategoryController::class, 'getCategoryFromApi');
        foreach ($responseData as $category) {
            $code        = $category->Code;
            $description = $category->Description;
            $cat         = SimplyDutyCategory::where('code', $code)->where('description', $description)->first();
            if ($cat != '' && $cat != null) {
                $cat->touch();
            } else {
                $category              = new SimplyDutyCategory;
                $category->code        = $code;
                $category->description = $description;
                $category->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
