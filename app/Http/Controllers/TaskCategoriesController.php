<?php

namespace App\Http\Controllers;

use App\TaskCategories;
use App\TaskHistories;
use App\TaskSubCategory;
use App\TaskSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCategoriesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = TaskSubCategory::with(['task_category', 'task_subject']);

            if (isset($request->category_name) && ! empty($request->category_name)) {
                $items = TaskSubCategory::with(['task_subject', 'task_category'])
                    ->whereHas('task_category', function ($q) use ($request) {
                        $q->where('task_category.name', 'Like', '%'.$request->category_name.'%');
                    });
            }
            if (isset($request->sub_category_name) && ! empty($request->sub_category_name)) {
                $items->where('task_sub_categories.name', 'Like', '%'.$request->sub_category_name.'%');
            }
            if (isset($request->subjects) && ! empty($request->subjects)) {
                $items = TaskSubCategory::with(['task_subject', 'task_category'])->whereHas('task_subject', function ($q) use ($request) {
                    $q->where('task_subjects.name', 'Like', '%'.$request->subjects.'%');
                });
            }

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'Project Documentation Module';

            return view('Taskcategories.index', ['title' => $title]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $name = $request['category_name'];
            $user_id = Auth::user()->id;
            $category_array = [
                'name' => $name,
            ];
            $task_category = TaskCategories::create($category_array);
            $sub_category_array = [
                'task_category_id' => $task_category->id,
                'name' => $request['sub_category_name'],
            ];

            $task_subcategory = TaskSubCategory::create($sub_category_array);
            $subject1 = [];
            foreach ($request['subject'] as $key => $subject) {
                $subject_array = [
                    'task_category_id' => $task_category->id,
                    'task_subcategory_id' => $task_subcategory->id,
                    'name' => $request['subjectname'][$key],
                    'description' => $subject,
                ];
                $subject1[$key] = TaskSubject::create($subject_array);
            }

            foreach ($subject1 as $key => $sub) {
                $task_history = [
                    'user_id' => $user_id,
                    'task_subject_id' => $sub->id,
                    'name_before' => $sub->name,
                    'name_after' => null,
                    'description_before' => $sub->description,
                    'description_after' => null,
                ];
                TaskHistories::create($task_history);
            }

            return response()->json(['code' => 200, 'message' => 'Record added Successfully!']);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            $id = $request->id;
            $categoryname = $request->category_name;
            $sub_category = $request->sub_category_name;
            $category = TaskCategories::find($id);
            if (! empty($category)) {
                $category->update(['name', $categoryname]);
            }

            $subcategory = TaskSubCategory::where('task_category_id', $id)->first();
            if (! empty($subcategory)) {
                $subcategory->update(['name', $sub_category]);
            }
            $task_subject = TaskSubject::where('task_category_id', $id)->get();
            if (! empty($request->subject)) {
                foreach ($request->subject as $key => $subject) {
                    $data[$key] = [
                        'name' => $subject,
                        'description' => $request->description[$key],
                    ];
                    $task_subject[$key]->update($data[$key]);
                }
            }
            if (! empty($request->subject1)) {
                foreach ($request->subject1 as $key => $subjects) {
                    $data[$key] = [
                        'task_category_id' => $category->id,
                        'task_subcategory_id' => $subcategory->id,
                        'name' => $subjects,
                        'description' => $request->description1[$key],
                    ];
                    $subject1[$key] = TaskSubject::create($data[$key]);
                }
            }

            $user_id = Auth::user()->id;
            if (! empty($subject1)) {
                foreach ($subject1 as $key => $sub) {
                    $data[$key] = [
                        'user_id' => $user_id,
                        'task_subject_id' => $sub->id,
                        'name_before' => $sub->name,
                        'name_after' => null,
                        'description_before' => $sub->description,
                        'description_after' => null,
                    ];
                    TaskHistories::create($data[$key]);
                }
            }

            foreach ($task_subject as $key => $tasks) {
                $task_history = TaskHistories::where('task_subject_id', $tasks->id)->get();

                if ($task_history[0]->name_before == $tasks->name) {
                    $taskname = $task_history[0]->name_before;
                } else {
                    if (empty($task_history[0]->name_after)) {
                        $taskname = $task_history[0]->name_before;
                    } else {
                        $taskname = $task_history[0]->name_after;
                        $tasknameup = $tasks->name;
                    }
                }
                if ($task_history[0]->description_before == $tasks->description) {
                    $taskdescription = $task_history[0]->description_before;
                } else {
                    if (empty($task_history[0]->description_after)) {
                        $taskdescription = $task_history[0]->description_before;
                    } else {
                        $taskdescription = $task_history[0]->description_after;
                        $taskdescriptionup = $tasks->description;
                    }
                }

                $data[$key] = [
                    'user_id' => $user_id,
                    'task_subject_id' => $tasks->id,
                    'name_before' => $taskname,
                    'name_after' => ! empty($tasknameup) ? $tasknameup : '',
                    'description_before' => $taskdescription,
                    'description_after' => ! empty($taskdescriptionup) ? $taskdescriptionup : '',
                ];
                $task_history[0]->update($data[$key]);
            }

            return response()->json(['code' => 200, 'message' => 'Record updated Successfully!']);
        }
    }

    public function delete($id): JsonResponse
    {
        $items = TaskSubCategory::with(['task_category', 'task_subject'])->where('task_category_id', $id);
        $items->delete();

        return response()->json(['code' => 200, 'message' => 'Record deleted Successfully!']);
    }

    public function destroy($id): JsonResponse
    {
        $items = TaskSubject::where('id', $id);

        $items->delete();

        return response()->json(['code' => 200, 'message' => 'subject deleted Successfully!']);
    }
}
