<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagentoModule\MagentoModuleJsRequireHistoryRequest;
use App\MagentoModuleJsRequireHistory;
use Illuminate\Http\JsonResponse;

class MagentoModuleJsRequireHistoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(MagentoModuleJsRequireHistoryRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);
        $input['user_id'] = auth()->user()->id;

        $data = MagentoModuleJsRequireHistory::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  mixed  $magento_module
     * @return \Illuminate\Http\Response
     */
    public function show($magento_module)
    {
        $title = 'Magento Module Type Details';
        $magento_module_cron_job_histories = MagentoModuleJsRequireHistory::with(['user'])->where('magento_module_id', $magento_module)->get();

        if (request()->ajax() && $magento_module_cron_job_histories) {
            return response()->json([
                'data' => $magento_module_cron_job_histories,
                'title' => $title,
                'status' => true,
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'data' => '',
                'title' => $title,
                'code' => 500,
            ], 500);
        }
    }
}
