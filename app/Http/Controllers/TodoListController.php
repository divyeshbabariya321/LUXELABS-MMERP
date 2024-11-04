<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Setting;
use App\TodoList;
use App\TodoStatus;
use App\TodoCategory;
use Illuminate\Http\Request;
use App\ToDoListRemarkHistoryLog;
use Illuminate\Database\RecordsNotFoundException;
use Exception;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $search_title            = request('search_title') ?? '';
            $search_status           = request('search_status') ?? '';
            $search_date             = request('search_date') ?? '';
            $search_todo_category_id = request('search_todo_category_id') ?? '';
            $todolists               = TodoList::with('username');

            if ($s = $search_title) {
                $todolists->where('title', 'like', '%' . $s . '%');
            }

            if ($s = $search_status) {
                $todolists->where('status', 'like', '%' . $s . '%');
            }

            if ($s = $search_date) {
                $todolists->where('todo_date', $s);
            }

            if ($s = $search_todo_category_id) {
                $todolists->where('todo_category_id', $s);
            }

            $todolists_count = $todolists->count();

            $todolists = $todolists->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->paginate(Setting::get('pagination'));

            $statuses       = TodoStatus::all()->toArray();
            $todoCategories = TodoCategory::get();
            if ($request->ajax()) {
                return response()->json([
                    'tbody' => view('todolist.data', compact('todolists', 'search_title', 'statuses', 'search_todo_category_id', 'todoCategories', 'statuses', 'todolists_count'))->render(),
                    'links' => (string) $todolists->render(),
                ], 200);
            }

            return view('todolist.index', compact('todolists', 'search_title', 'search_status', 'search_date', 'statuses', 'search_todo_category_id', 'todoCategories', 'statuses', 'todolists_count'));
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
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
        try {
            $todolists                   = new TodoList();
            $todolists->user_id          = Auth::user()->id ?? '';
            $todolists->title            = $request->title;
            $todolists->subject          = $request->subject;
            $todolists->status           = $request->status ?? '';
            $todolists->todo_date        = $request->todo_date;
            $todolists->remark           = $request->remark;
            $todolists->todo_category_id = $request->todo_category_id ?? '';
            $todolists->save();
            $this->createTodolistRemarkHistory($request, $todolists->id);

            if($request->ajax()) {
                return response()->json(['code' => 200, 'message' => 'Record added Successfully!']);
            }

            return redirect()->back()->with('success', 'Your Todo List has been created!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function ajax_store(Request $request): JsonResponse
    {
        if (! $request->title || $request->subject == '' || $request->status == '' || $request->todo_date == '' || $request->todo_category_id == '') {
            return response()->json(
                [
                    'code'    => 500,
                    'data'    => [],
                    'message' => 'Please enter all required details.',
                ]
            );
        }

        try {
            $todolists            = new TodoList();
            $todolists->user_id   = Auth::user()->id ?? '';
            $todolists->title     = $request->title;
            $todolists->subject   = $request->subject;
            $todolists->status    = $request->status ?? '';
            $todolists->todo_date = $request->todo_date;
            $todolists->remark    = $request->remark ?? '-';

            if ($request->todo_category_id > 0) {
                $todolists->todo_category_id = $request->todo_category_id ?? '';
            } else {
                $todoCategory         = new TodoCategory();
                $todoCategory->name   = $request->other;
                $todoCategory->status = 1;
                $todoCategory->save();
                $todolists->todo_category_id = $todoCategory->id;
            }

            $todolists->save();
            $this->createTodolistRemarkHistory($request, $todolists->id);

            return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo List has been created!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(TodoList $todoList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TodoList $todoList): JsonResponse
    {
        try {
            $id        = request('id') ?? '';
            $todolists = TodoList::with('username');
            if ($s = $id) {
                $todolists->where('id', $s);
            }
            $todolists = $todolists->orderByDesc('todo_lists.id')->first();

            return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo List has been listed!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TodoList $todoList): RedirectResponse
    {
        try {
            $todolists            = TodoList::findorfail($request->id);
            $todolists->user_id   = Auth::user()->id ?? '';
            $todolists->title     = $request->title;
            $todolists->subject   = $request->subject;
            $todolists->status    = $request->status;
            $todolists->todo_date = $request->todo_date;
            $todolists->remark    = $request->remark;
            $todolists->save();
            if ($request->remark != $request->old_remark) {
                $this->createTodolistRemarkHistory($request, $todolists->id);
            }

            return redirect()->back()->with('success', 'Your Todo List has been Updated!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     */
    public function destroy($id): RedirectResponse
    {
        TodoList::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Your Todo Task has been deleted successfuly!');
    }

    public function createTodolistRemarkHistory($request, $id)
    {
        try {
            $todoRemark               = new ToDoListRemarkHistoryLog();
            $todoRemark->user_id      = Auth::user()->id;
            $todoRemark->todo_list_id = $id;
            $todoRemark->remark       = $request->remark;
            $todoRemark->old_remark   = $request->old_remark ?? '';
            $todoRemark->save();
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getRemarkHistory(Request $request)
    {
        try {
            $todoRemark = ToDoListRemarkHistoryLog::with('username')->where('todo_list_id', $request->id)->get()->toArray();

            return response()->json(['code' => 200, 'data' => $todoRemark, 'message' => 'Your Todo remark history has been list!']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function storeStatus(Request $request): RedirectResponse
    {
        try {
            $todoStatus        = new TodoStatus();
            $todoStatus->name  = $request->status_name;
            $todoStatus->color = $request->status_color;
            $todoStatus->save();

            return redirect()->back()->with('success', 'Your Todo status has been Added!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function statusUpdate(Request $request): JsonResponse
    {
        $todolists         = TodoList::findorfail($request->id);
        $todolists->status = $request->status;
        $todolists->save();

        return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo Status has been Updated!']);
    }

    public function todoCategoryUpdate(Request $request)
    {
        try {
            $todolists                   = TodoList::findorfail($request->id);
            $todolists->todo_category_id = $request->todo_category_id;
            $todolists->save();

            return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo Category has been Updated!']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function storeTodoCategory(Request $request): RedirectResponse
    {
        try {
            $todoStatus       = new TodoCategory();
            $todoStatus->name = $request->todo_category_name;
            $todoStatus->save();

            return redirect()->back()->with('success', 'Your Todo status has been Added!');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function StatusColorUpdate(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        $data        = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus        = TodoStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function indexJson(Request $request): JsonResponse
    {
        try {
            $search_title            = request('search_title') ?? '';
            $search_status           = request('search_status') ?? '';
            $search_date             = request('search_date') ?? '';
            $search_todo_category_id = request('search_todo_category_id') ?? '';
            $todolists               = TodoList::with('username');

            if ($s = $search_title) {
                $todolists->where('title', 'like', '%' . $s . '%');
            }

            if ($s = $search_status) {
                $todolists->where('status', 'like', '%' . $s . '%');
            }

            if ($s = $search_date) {
                $todolists->where('todo_date', $s);
            }

            if ($s = $search_todo_category_id) {
                $todolists->where('todo_category_id', $s);
            }

            $todolists = $todolists->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->paginate(Setting::get('pagination'));

            $statuses       = TodoStatus::all()->toArray();
            $todoCategories = TodoCategory::get();

            return response()->json([
                'items' => (array) $todolists->getIterator(),
                'page'  => $todolists->currentPage(),
                'total' => $todolists->total(),
            ]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateJson(Request $request, TodoList $todoList): JsonResponse
    {
        try {
            $data = $request->all();

            $id = $data['id'] ?? null;
            unset($data['id']);

            if ($id !== null) {
                $todolists = TodoList::find($id);

                if ($todolists === null) {
                    throw new RecordsNotFoundException('Not found.');
                }
            } else {
                $todolists = new TodoList();
            }

            $todolists->user_id = Auth::user()->id ?? '';
            $todolists->fill($data);

            $todolists->save();
            if ($request->remark != $request->old_remark) {
                $this->createTodolistRemarkHistory($request, $todolists->id);
            }

            return response()->json(['message' => sprintf('You todolist was successfully updated with id: %s.', $todolists->id)]);
        } catch (RecordsNotFoundException $foundException) {
            return response()->json(['message' => $foundException->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function destroyJson($id): JsonResponse
    {
        TodoList::where('id', $id)->delete();

        return response()->json(['message' => 'Your Todo Task has been deleted successfuly!']);
    }

    public function remarkPostHistory(Request $request): JsonResponse
    {
        try {
            $remarks = ToDoListRemarkHistoryLog::create(
                [
                    'user_id'      => Auth::user()->id,
                    'remark'       => $request->remarks,
                    'todo_list_id' => $request->todo_list_id,
                ]
            );
            $remarks = ToDoListRemarkHistoryLog::where('id', $remarks->id)->first();

            return response()->json(['code' => 200, 'data' => $remarks, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function searchTodoListHeader(Request $request): View
    {
        $query = TodoList::where('user_id', \Auth()->user()->id)->where('status', 1)->orderByDesc('id');

        if ($request->get('keyword') != '') {
            $query->where('title', 'LIKE', '%' . $request->get('keyword') . '%')->orWhere('subject', 'LIKE', '%' . $request->get('keyword') . '%');
        }

        if ($request->get('todolist_start_date') != '' && $request->get('todolist_end_date') != '') {
            $query->whereBetween('todo_date', [$request->get('todolist_start_date'), $request->get('todolist_end_date')]);
        }

        $todoLists = $query->with('category')->limit(10)->get();

        $statuses = TodoStatus::get();

        return view('todolist.menu-todata', [
            'todoLists' => $todoLists,
            'statuses'  => $statuses,
        ]);
    }

    public function getTodoCategoryAndStatus(): JsonResponse
    {
        $todoCategories = TodoCategory::get();
        $todoStatuses   = TodoStatus::get();

        return response()->json(['todoCategories' => $todoCategories, 'statuses' => $todoStatuses]);
    }
}
