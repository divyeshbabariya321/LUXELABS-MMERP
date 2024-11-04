<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\TaskStatus;
use App\MagentoModuleType;
use Illuminate\Http\Request;
use App\MagentoModuleCategory;
use App\Http\Requests\MagentoModule\MagentoModuleTypeRequest;

class MagentoModuleTypeController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view  = 'magento_module_type.index';
        $this->create_view = 'magento_module_type.create';
        $this->detail_view = 'magento_module_type.details';
        $this->edit_view   = 'magento_module_type.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = MagentoModuleType::query();

            return datatables()->eloquent($items)->toJson();
        } else {
            $title             = 'Magento Module Type';
            $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
            $task_statuses     = TaskStatus::pluck('name', 'id');

            return view($this->index_view, compact('title', 'module_categories', 'task_statuses'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title             = 'Magento Module Type';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses     = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MagentoModuleTypeRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = MagentoModuleType::create($input);

        if ($data) {
            return response()->json([
                'status'      => true,
                'data'        => $data,
                'message'     => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status'      => false,
                'message'     => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoModuleType $magento_module_type)
    {
        $title = 'Magento Module Type Details';

        if (request()->ajax() && $magento_module_type) {
            return response()->json([
                'data'  => view('magento_module_type.partials.data', compact('magento_module_type'))->render(),
                'title' => $title,
                'code'  => 200,
            ], 200);
        } else {
            return response()->json([
                'data'  => '',
                'title' => $title,
                'code'  => 500,
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     */
    public function edit(MagentoModuleType $magento_module_type): View
    {
        $title             = 'Magento Module Type';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses     = TaskStatus::pluck('name', 'id');

        return view($this->edit_view, compact('module_categories', 'title', 'magento_module_type', 'task_statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MagentoModuleTypeRequest $request, MagentoModuleType $magento_module_type): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = $magento_module_type->update($input);

        if ($data) {
            return response()->json([
                'status'      => true,
                'message'     => 'Updated successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status'      => false,
                'message'     => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MagentoModuleType $magento_module_type): JsonResponse
    {
        $data = $magento_module_type->delete();

        if ($data) {
            return response()->json([
                'status'      => true,
                'message'     => 'Deleted successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status'      => false,
                'message'     => 'Deleted unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }
}
