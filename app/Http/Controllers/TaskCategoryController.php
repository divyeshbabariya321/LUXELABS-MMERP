<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCategoryRequest;
use App\Http\Requests\UpdateTaskCategoryRequest;
use App\TaskCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskCategoryController extends Controller
{
    public function index(): View
    {
        $data = TaskCategory::latest()->get();

        return view('task-module.category.index', compact('data'));
    }

    public function create(): View
    {
        $data = [];
        $data['title'] = '';
        $data['modify'] = 0;

        return view('task-module.category.form', $data);
    }

    public function edit(TaskCategory $task_category): View
    {
        $data = $task_category->toArray();
        $data['modify'] = 1;

        return view('task-module.category.form', $data);
    }

    public function store(StoreTaskCategoryRequest $request): RedirectResponse
    {

        $is_approved = Auth::user()->hasRole('Admin') ? 1 : 0;

        if ($request->title != '') {
            TaskCategory::create(['title' => $request->title, 'is_approved' => $is_approved]);
        }

        if ($request->parent_id != '' && $request->subcategory != '') {
            TaskCategory::create(['title' => $request->subcategory, 'parent_id' => $request->parent_id, 'is_approved' => $is_approved]);
        }

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $categories = TaskCategory::all();
        foreach ($categories as $cat) {
            $cat->update(['is_active' => 0]);
        }
        foreach ($request->categoriesList as $category) {
            TaskCategory::where('id', $category)->update(['is_active' => 1]);
        }

        return response()->json(['message' => 'Successful'], 200);
    }

    public function update(UpdateTaskCategoryRequest $request, TaskCategory $task_category): RedirectResponse
    {

        $task_category->update($request->all());

        return redirect()->route('task_category.index')->with('success', 'Category udpated successfully');
    }

    public function approve(Request $request, $id)
    {
        $task_category = TaskCategory::find($id);
        $task_category->is_approved = 1;
        $task_category->save();

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('task_category.index')->with('success', 'Category approved successfully');
    }

    public function destroy(TaskCategory $task_category): RedirectResponse
    {
        $task_category->delete();

        return redirect()->route('task_category.index')->with('success', 'Category deleted successfully');
    }

    public static function getAllTaskCategory()
    {
        $task_category = TaskCategory::all()->toArray();
        $task_category_new = [];

        foreach ($task_category as $item) {
            $task_category_new[$item['id']] = $item['title'];
        }

        return $task_category_new;
    }

    public static function getCategoryNameById($id)
    {
        $task_category = self::getAllTaskCategory();

        if (! empty($task_category[$id])) {
            return $task_category[$id];
        }

        return '';
    }
}
