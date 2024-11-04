<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Subject;
use App\Checklist;
use App\ChecklistSubject;
use Illuminate\Http\Request;
use App\ChecklistSubjectRemarkHistory;
use Exception;

class CheckListController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view  = 'checklist.index';
        $this->create_view = 'checklist.create';
        $this->detail_view = 'checklist.details';
        $this->edit_view   = 'checklist.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Checklist::with('subjects');

            if (isset($request->category_name) && ! empty($request->category_name)) {
                $items->where('checklist.category_name', 'Like', '%' . $request->category_name . '%');
            }

            if (isset($request->sub_category_name) && ! empty($request->sub_category_name)) {
                $items->where('checklist.sub_category_name', 'Like', '%' . $request->sub_category_name . '%');
            }
            if (isset($request->subjects) && ! empty($request->subjects)) {
                //
            }

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'CheckLists Module';

            return view($this->index_view, compact('title'));
        }
    }

    public function view($id = null): View
    {
        $title = 'View Check List';

        return view('checklist.view', compact('title', 'id'));
    }

    public function subjects(Request $request)
    {
        if ($request->type == 'datatable') {
            $items = Subject::with('checklistsubject', 'checklistsubjectRemark')->where('checklist_id', $request->id);

            return datatables()->eloquent($items)->toJson();
        } else {
            $items = ChecklistSubject::where('user_id', Auth::id())->where('checklist_id', $request->id)->groupBy('date')->get();

            return json_encode($items);
        }
    }

    public function checked(Request $request)
    {
        $data = ChecklistSubject::where('subject_id', $request->subject_id)->where('checklist_id', $request->checklist_id)->where('date', $request->date)->first();

        return json_encode($data);
    }

    public function checklistUpdate(Request $request): JsonResponse
    {
        $update_record = ChecklistSubject::where('id', $request->id)->update(['is_checked' => $request->is_checked]);
        if (! empty($update_record)) {
            return response()->json([
                'status'      => true,
                'message'     => 'Checklist saved successfully',
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

    public function add(Request $request): JsonResponse
    {
        $subjects = array_unique($request->subjects);
        foreach ($subjects as $subject) {
            $record = [
                'subject_id'   => $subject,
                'checklist_id' => $request->id,
                'is_checked'   => 0,
                'user_id'      => Auth::id(),
                'date'         => $request->date,
            ];
            ChecklistSubject::create($record);
        }
        if (! empty($subjects)) {
            return response()->json([
                'status'      => true,
                'message'     => 'Record saved successfully',
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
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $checklist = Checklist::where('category_name', $input['category_name'])->where('sub_category_name', $input['sub_category_name'])->where('status', 1)->first();
        if ($checklist) {
            return response()->json([
                'status'      => false,
                'message'     => 'Category and Sub Category already exists!',
                'status_name' => 'error',
            ], 500);
        } else {
            $data = Checklist::create($input);
            if ($data) {
                if (! empty($input['subjects'])) {
                    $input['subjects'] = explode(',', $input['subjects']);
                    foreach ($input['subjects'] as $subject) {
                        $record = [
                            'title'        => $subject,
                            'checklist_id' => $data->id,
                        ];
                        Subject::create($record);
                    }
                }
            }
            if ($data) {
                return response()->json([
                    'status'      => true,
                    'message'     => 'Checklist saved successfully',
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
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $input     = $request->except('_token');
        $checklist = Checklist::find($id);
        if ($checklist) {
            $checklist->update($input);
            if (! empty($input['subjects'])) {
                $input['subjects'] = explode(',', $input['subjects']);
                Subject::where('checklist_id', $id)->delete();
                foreach ($input['subjects'] as $subject) {
                    $record = [
                        'title'        => $subject,
                        'checklist_id' => $id,
                    ];
                    Subject::create($record);
                }
            }
            if ($checklist) {
                return response()->json([
                    'status'      => true,
                    'message'     => 'Checklist updated successfully',
                    'status_name' => 'success',
                ], 200);
            } else {
                return response()->json([
                    'status'      => false,
                    'message'     => 'something error occurred',
                    'status_name' => 'error',
                ], 500);
            }
        } else {
            $saveRecord = Checklist::create($input);
            if ($saveRecord) {
                return response()->json([
                    'status'      => true,
                    'message'     => 'Checklist saved successfully',
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $data = Checklist::where('id', $id)->delete();

        if ($data) {
            return response()->json([
                'status'      => true,
                'message'     => 'Checklist Deleted successfully',
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

    public function subjectRemarkList(Request $request): JsonResponse
    {
        try {
            $remark = ChecklistSubjectRemarkHistory::leftJoin('users', 'users.id', 'create_checklist_subject_remark_histories.user_id')
                ->select('create_checklist_subject_remark_histories.*', 'users.name as username')
                ->where('create_checklist_subject_remark_histories.subject_id', $request->subject_id)
                ->orderByDesc('create_checklist_subject_remark_histories.id')
                ->get();

            return response()->json(['code' => 200, 'message' => 'Remark listed successfully.', 'data' => $remark]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function subjectRemarkCreate(Request $request): JsonResponse
    {
        try {
            $remark               = new ChecklistSubjectRemarkHistory();
            $remark->user_id      = Auth::user()->id;
            $remark->checklist_id = $request->checklist_id;
            $remark->subject_id   = $request->subject_id;
            $remark->remark       = $request->remark;
            $remark->old_remark   = $request->old_remark;

            $remark->save();

            return response()->json(['code' => 200, 'message' => 'Remark listed successfully.', 'data' => $remark]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage(), 'data' => []]);
        }
    }
}
