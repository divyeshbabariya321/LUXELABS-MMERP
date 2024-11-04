<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeywordassignRequest;
use App\Http\Requests\UpdateKeywordassignRequest;
use App\Keywordassign;
use App\KeywordAutoGenratedMessageLog;
use App\TaskCategories;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeywordassignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $keywordassign = Keywordassign::select('keywordassigns.id', 'keywordassigns.keyword', 'task_categories.title', 'keywordassigns.task_description', 'users.name')
            ->leftJoin('users', 'keywordassigns.assign_to', '=', 'users.id')
            ->leftJoin('task_categories', 'keywordassigns.task_category', '=', 'task_categories.id')
            ->orderBy('id')
            ->get();

        return view('keywordassign.index', compact('keywordassign'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $task_category = TaskCategories::select('id', 'title')->get();
        $userslist = User::select('id', 'name')->get();

        return view('keywordassign.create', compact('task_category', 'userslist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKeywordassignRequest $request): RedirectResponse
    {
        $exp_keyword = explode(',', $request->keyword);
        $new_keywordstr = '';
        for ($i = 0; $i < count($exp_keyword); $i++) {
            $new_keywordstr .= trim($exp_keyword[$i]).',';
        }
        $keyword = trim($new_keywordstr, ',');
        $task_category = $request->task_category;
        $task_description = $request->task_description;
        $assign_to = $request->assign_to;
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $insert_data = [
            'keyword' => $keyword,
            'task_category' => $task_category,
            'task_description' => $task_description,
            'assign_to' => $assign_to,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
        Keywordassign::insert($insert_data);

        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign created successfully.');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        //
        $keywordassign = Keywordassign::select('id', 'keyword', 'task_category', 'task_description', 'assign_to')->where('id', $id)->get();
        $task_category = TaskCategories::select('id', 'title')->get();
        $userslist = User::select('id', 'name')->get();

        return view('keywordassign.edit', compact('keywordassign', 'task_category', 'userslist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKeywordassignRequest $request, int $id): RedirectResponse
    {
        // Create the task
        $keyword = $request->keyword;
        $task_category = $request->task_category;
        $task_description = $request->task_description;
        $assign_to = $request->assign_to;
        $updated_at = date('Y-m-d H:i:s');
        $insert_data = [
            'keyword' => $keyword,
            'task_category' => $task_category,
            'task_description' => $task_description,
            'assign_to' => $assign_to,
            'updated_at' => $updated_at,
        ];
        Keywordassign::where('id', $id)
            ->update($insert_data);

        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        Keywordassign::where('id', '=', $id)->delete();

        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign deleted successfully.');
    }

    public function taskcategory(Request $request): JsonResponse
    {
        $task_category_name = $request->task_category_name;
        $TaskCategories = new TaskCategories;
        $TaskCategories->parent_id = 0;
        $TaskCategories->title = $task_category_name;
        $TaskCategories = $task_category_name->save();

        $id = $TaskCategories ? $TaskCategories->id : 0;

        return response()->json(['code' => 200, 'data' => ['id' => $id, 'Category' => $task_category_name], 'message' => 'Task Category Inserted']);
    }

    //START - Purpose : create function for get data - DEVTASK-4233
    public function keywordreponse_logs(Request $request): View
    {
        try {
            $query = KeywordAutoGenratedMessageLog::orderByDesc('id');

            if ($request->get('keyword') != '') {
                $keywordlogs = $query->where('keyword', 'like', '%'.$request->get('keyword').'%');
            }

            if ($request->get('keyword_duedate') != '') {
                $keywordlogs = $query->whereDate('created_at', '=', $request->get('keyword_duedate'));
            }

            $keywordlogs = $query->paginate(30);

            return view('keywordassign.logs', compact('keywordlogs', 'request'));
        } catch (Exception $e) {

            return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);
        }
    }
    //END - DEVTASK-4233
}
