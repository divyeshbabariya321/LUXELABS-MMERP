<?php

namespace App\Http\Controllers;

use App\MagentoModuleHistory;

class MagentoModuleHistoryController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int   $id
     * @param mixed $magento_module
     *
     * @return \Illuminate\Http\Response
     */
    public function show($magento_module)
    {
        $title                        = 'Magento Module History';
        $magento_module_api_histories = MagentoModuleHistory::with([
            'user:id,name',
            'module_category:id,category_name',
            'store_website:id,website',
            'module_type_data:id,magento_module_type',
            'developer_name_data:id,name',
            'task_status_data:id,name',
        ])
            ->where('magento_module_id', $magento_module)->get();

        if (request()->ajax() && $magento_module_api_histories) {
            return response()->json([
                'status' => true,
                'data'   => $magento_module_api_histories,
                'title'  => $title,
                'code'   => 200,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'data'   => '',
                'title'  => $title,
                'code'   => 500,
            ], 500);
        }

    }
}
