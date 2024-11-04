<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\TaskStatus;
use Illuminate\Http\Request;
use App\MagentoModuleCategory;
use App\Http\Requests\MagentoModule\MagentoModuleCategoryRequest;

class MagentoModuleCategoryController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view  = 'magento_module_category.index';
        $this->create_view = 'magento_module_category.create';
        $this->detail_view = 'magento_module_category.details';
        $this->edit_view   = 'magento_module_category.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = MagentoModuleCategory::query();

            return datatables()->eloquent($items)->toJson();
        } else {
            $title             = 'Magento Module Category';
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
        $title             = 'Magento Module Category';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses     = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MagentoModuleCategoryRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = MagentoModuleCategory::create($input);

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
    public function show(MagentoModuleCategory $magento_module_category)
    {
        $title = 'Magento Module Category Details';

        if (request()->ajax() && $magento_module_category) {
            return response()->json([
                'data'  => view('magento_module_category.partials.data', compact('magento_module_category'))->render(),
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
    public function edit(MagentoModuleCategory $magento_module_category): View
    {
        $title                     = 'Magento Module Category';
        $magento_module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses             = TaskStatus::pluck('name', 'id');

        return view($this->edit_view, compact('magento_module_categories', 'title', 'magento_module_category', 'task_statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MagentoModuleCategoryRequest $request, MagentoModuleCategory $magento_module_category): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = $magento_module_category->update($input);

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
    public function destroy(MagentoModuleCategory $magento_module_category): JsonResponse
    {
        $data = $magento_module_category->delete();

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
