<?php

namespace App\Http\Controllers;

use App\PageInstruction;
use App\PageNotes;
use App\PageNotesCategories;
use App\Setting;
use App\TodoList;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageNotesController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $pageNotes = new PageNotes;

        if ($request->get('isUpdate')) {
            $pageNotes = PageNotes::where('user_id', Auth::user()->id)->whereDate('created_at', Carbon::today())->first();
            if (empty($pageNotes)) {
                $pageNotes = new PageNotes;
            }
        }

        $pageNotes->user_id = Auth::user()->id;
        $pageNotes->url = $request->get('page', '');
        $pageNotes->category_id = $request->get('category_id', null);
        $pageNotes->note = $request->get('note', '');

        if ($pageNotes->save()) {
            $list = $pageNotes->getAttributes();
            $list['name'] = $pageNotes->user->name;
            $list['category_name'] = ! empty($pageNotes->pageNotesCategories->name) ? $pageNotes->pageNotesCategories->name : '';

            return response()->json(['code' => 1, 'notes' => $list]);
        }

        return response()->json(['code' => -1, 'message' => 'oops, something went wrong!!']);
    }

    public function list(Request $request): JsonResponse
    {
        $pageNotes = PageNotes::join('users', 'users.id', '=', 'page_notes.user_id')
            ->leftJoin('page_notes_categories', 'page_notes.category_id', '=', 'page_notes_categories.id')
            ->select(['page_notes.*', 'users.name', 'page_notes_categories.name as category_name'])
            ->where('page_notes.user_id', Auth::user()->id)
            ->where('url', $request->get('url'))
            ->orderByDesc('page_notes.id')
            ->get()
            ->toArray();

        return response()->json(['code' => 1, 'notes' => $pageNotes]);
    }

    public function edit(Request $request)
    {
        $id = $request->get('id', 0);
        $pageNotes = PageNotes::where('id', $id)->first();
        if ($pageNotes) {
            $category = PageNotesCategories::pluck('name', 'id')->toArray();

            return view('pagenotes.edit', compact('pageNotes', 'category'));
        }

        return 'Page Note Not Found';
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);
        $pageNotes = PageNotes::where('id', $id)->first();
        if ($pageNotes) {
            $pageNotes->user_id = Auth::user()->id;
            $pageNotes->category_id = $request->get('category_id', null);
            $pageNotes->note = $request->get('note', '');

            if ($pageNotes->save()) {
                $list = $pageNotes->getAttributes();
                $list['name'] = $pageNotes->user->name;
                $list['category_name'] = ! empty($pageNotes->pageNotesCategories->name) ? $pageNotes->pageNotesCategories->name : '';

                return response()->json(['code' => 1, 'notes' => $list]);
            }
        }

        return response()->json(['code' => -1, 'message' => 'oops, something went wrong!!']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);
        $pageNotes = PageNotes::where('id', $id)->first();
        if ($pageNotes) {
            $pageNotes->delete();

            return response()->json(['code' => 1, 'message' => 'success!']);
        }

        return response()->json(['code' => -1, 'message' => 'oops, something went wrong!!']);
    }

    public function index(Request $request): View
    {
        //START - Purpose : Get Page Note - DEVTASK-4289
        $records = PageNotes::join('users', 'users.id', '=', 'page_notes.user_id')
            ->leftJoin('page_notes_categories', 'page_notes.category_id', '=', 'page_notes_categories.id')
            ->where('page_notes.user_id', Auth::user()->id)
            ->orderByDesc('page_notes.id')
            ->select(['page_notes.*', 'users.name', 'page_notes_categories.name as category_name']);

        //START - Purpose : Add search - DEVTASK-4289
        if ($request->search) {
            $search = '%'.$request->search.'%';
            $records = $records->where('page_notes.note', 'like', $search);
        }

        if ($request->category_id) {
            $records = $records->where('page_notes.category_id', $request->category_id);
        }
        //END - DEVTASK-4289

        $records = $records->paginate(Setting::get('pagination'));

        return view('pagenotes.index', compact('records'));
        //END - DEVTASK-4289
    }

    public function records()
    {
        return datatables()->of(PageNotes::join('users', 'users.id', '=', 'page_notes.user_id')
            ->leftJoin('page_notes_categories', 'page_notes.category_id', '=', 'page_notes_categories.id')
            ->where('page_notes.user_id', Auth::user()->id)
            ->orderByDesc('page_notes.id')
            ->select(['page_notes.*', 'users.name', 'page_notes_categories.name as category_name']
            )->get())->make();
    }

    public function instructionCreate(Request $request): JsonResponse
    {
        $page = PageInstruction::where('page', $request->get('page'))->first();
        if (! $page) {
            $page = new PageInstruction;
        }

        $page->page = $request->get('page');
        $page->instruction = $request->get('note');
        $page->created_by = Auth::user()->id;
        $page->save();

        return response()->json(['code' => 200, 'data' => []]);
    }

    public function notesCreate(Request $request): JsonResponse
    {
        PageNotes::create([
            'url' => $request->url,
            'category_id' => '',
            'note' => $request->data,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json(['code' => 200, 'message' => 'Notes Added Successfully.']);
    }

    public function stickyNotesCreate(Request $request): JsonResponse
    {
        if (! empty($request['type']) && $request['type'] == 'todolist') {
            $todolists = new TodoList;
            $todolists->user_id = Auth::user()->id;
            $todolists->title = $request->get('title');
            $todolists->subject = $request->get('value');
            $todolists->status = 'Active';
            $todolists->save();

            return response()->json(['code' => 200, 'message' => 'Todo List Added Successfully.']);
        } else {
            $pageNotes = new PageNotes;
            $pageNotes->url = $request->get('page');
            $pageNotes->note = $request->get('value');
            $pageNotes->title = $request->get('title');
            $pageNotes->user_id = Auth::user()->id;
            $pageNotes->save();

            return response()->json(['code' => 200, 'message' => 'Sticky Notes Added Successfully.']);
        }
    }

    public function createCategory(Request $request): JsonResponse
    {
        $isExist = PageNotesCategories::where('name', $request->name)->first();
        if (! $isExist) {
            PageNotesCategories::create([
                'name' => $request->name,
                'created_by' => Auth::user()->id,
            ]);

            return response()->json(['message' => 'Successful'], 200);
        } else {
            return response()->json(['message' => 'Fail'], 401);
        }
    }
}
