<?php

namespace App\Http\Controllers;
use App\Category;
use App\Brand;

use App\ErpEvents;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ErpEventController extends Controller
{
    public function index(): View
    {
        $events = ErpEvents::all();

        $listEvents = [];

        if (! $events->isEmpty()) {
            foreach ($events as $event) {
                $listEvents[] = [
                    'startDate' => date('Y-m-d', strtotime($event->start_date)),
                    'endDate' => date('Y-m-d', strtotime($event->end_date)),
                    'title' => $event->event_name,
                ];
            }
        }
        $brandName = Brand::where('name', '!=', '')->get()->pluck('name', 'id')->toArray();
        $categoryTitle = Category::where('title', '!=', '')->get()->pluck('title', 'id')->toArray();

        return view('erp-events.index', compact('events', 'listEvents', 'brandName', 'categoryTitle'));
    }

    public function store(): JsonResponse
    {
        $params = request()->all();
        $params['brand_id'] = implode(',', $params['brand_id']);
        $params['category_id'] = implode(',', $params['category_id']);
        $params['type'] = 1;
        $params['created_by'] = Auth::id();
        $erpEvnts = new ErpEvents();
        $erpEvnts->fill($params);
        $erpEvnts->save();

        return response()->json(['code' => 1]);
    }

    public function dummy()
    {
        $params = [
            'event_name' => 'Testing Event',
            'event_description' => 'This is test description',
            'start_date' => '2019-12-04',
            'end_date' => '2019-12-15',
            'type' => '1',
            'brand_id' => '1,2,3',
            'category_id' => '10,38',
            'number_of_person' => '20',
            'product_start_date' => '',
            'product_end_date' => '',
            'minute' => '0',
            'hour' => '1',
            'day_of_month' => '0',
            'month' => '0',
            'day_of_week' => '0',
            'created_by' => '1',
        ];

        $erpEvnts = new ErpEvents();
        $erpEvnts->fill($params);
        $erpEvnts->save();
    }
}
