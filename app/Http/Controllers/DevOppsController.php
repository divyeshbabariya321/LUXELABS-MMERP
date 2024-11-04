<?php

namespace App\Http\Controllers;

use App\Github\GithubRepository;
use App\GoogleScreencast;
use App\Helpers\MessageHelper;
use App\Http\Requests\SaveRemarksDevOppRequest;
use App\Http\Requests\UploadFileDevOppRequest;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Models\DevOopsStatus;
use App\Models\DevOopsStatusHistory;
use App\Models\DevOppsCategories;
use App\Models\DevOppsRemarks;
use App\Models\DevOppsSubCategory;
use App\Models\DevOppsSubCategoryDocument;
use App\Task;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class DevOppsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = DevOppsSubCategory::with(['devoops_category', 'status', 'remarks']);

            // updated conditions to match dropdown value change
            if (isset($request->category_name) && ! empty($request->category_name)) {
                $items->whereHas('devoops_category', function ($q) use ($request) {
                    $q->whereIn('dev_opps_categories.id', $request->category_name);
                });
            }
            if (isset($request->sub_category_name) && ! empty($request->sub_category_name)) {
                $items->whereIn('dev_opps_sub_categories.id', $request->sub_category_name);
            }

            if (isset($request->remarks) && ! empty($request->remarks)) {
                $items->whereHas('remarks', function ($q) use ($request) {
                    $q->where('dev_opps_remarks.remarks', 'Like', '%'.$request->remarks.'%');
                });
            }

            return datatables()->eloquent($items)->toJson();
        } else {
            $title = 'Dev Opps Module';

            $devoops_categories = DevOppsCategories::pluck('name', 'id')->prepend('Select category', '');

            // added to initialize category and subcategory list
            $devoops_main_categories = DevOppsCategories::all();
            $devoops_sub_categories = DevOppsSubCategory::all();

            $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();

            $status = DevOopsStatus::all();
            $github_repository = GithubRepository::all();

            return view('dev-oops.index', ['title' => $title, 'devoops_categories' => $devoops_categories, 'devoops_main_categories' => $devoops_main_categories, 'devoops_sub_categories' => $devoops_sub_categories, 'allUsers' => $allUsers, 'status' => $status, 'github_repository' => $github_repository]);
        }
    }

    // Created to get category wise subcategory on ajax call
    public function getSubcategoryByCategory($id)
    {
        $ids = explode(',', $id);
        $devoops_sub_categories = DevOppsSubCategory::whereIn('devoops_category_id', $ids)->get();

        return $devoops_sub_categories;
    }

    // Created to get all subcategories on ajax call
    public function getAllSubcategory()
    {
        $devoops_sub_categories = DevOppsSubCategory::all();

        return $devoops_sub_categories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required',
                'category_priority' => 'required',
                'category_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 200);
            }
            if ($request['category_type'] == 1) {
                $name = $request['category_name'];
                $category_array = [
                    'name' => $name,
                ];
                DevOppsCategories::create($category_array);
            } else {
                $sub_category_array = [
                    'devoops_category_id' => $request['devoops_category_id'],
                    'name' => $request['sub_category_name'],
                ];

                DevOppsSubCategory::create($sub_category_array);
            }

            return response()->json(['status' => true, 'code' => 200, 'message' => 'Record added Successfully!']);
        }
    }

    public function update(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            if ($request['category_type'] == 1) {
                $id = $request->id;
                $categoryname = $request->category_name;
                $category = DevOppsCategories::find($id);

                if (! empty($category)) {
                    $category->name = $categoryname; // Assign the new value to the 'name' attribute
                    $category->save(); // Save the changes to the database
                }
            } else {
                $id = $request->id;
                $sub_category = $request->sub_category_name;
                $devoops_category_id = $request->devoops_category_id;
                $subcategory = DevOppsSubCategory::find($id);
                if (! empty($subcategory)) {
                    $subcategory->name = $sub_category;
                    $subcategory->devoops_category_id = $devoops_category_id;
                    $subcategory->save();
                }
            }

            return response()->json(['code' => 200, 'message' => 'Record updated Successfully!']);
        }
    }

    public function delete($id): JsonResponse
    {
        $items = DevOppsCategories::find($id);

        return response()->json(['code' => 200, 'message' => 'Record deleted Successfully!']);
    }

    public function subdelete($id): JsonResponse
    {
        $items = DevOppsSubCategory::find($id);

        return response()->json(['code' => 200, 'message' => 'Record deleted Successfully!']);
    }

    public function saveRemarks(SaveRemarksDevOppRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        DevOppsRemarks::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getRemarksHistories(Request $request): JsonResponse
    {
        $datas = DevOppsRemarks::with(['user'])
            ->where('main_category_id', $request->main_category_id)
            ->where('sub_category_id', $request->sub_category_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function taskCount($site_developement_id): JsonResponse
    {
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();

        return response()->json(['code' => 200, 'taskStatistics' => $othertaskStatistics]);
    }

    public function createStatus(Request $request): JsonResponse
    {
        try {
            $status = new DevOopsStatus;
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $status_name = $request->input('status_name');

        $devoops = DevOppsSubCategory::find($id);
        $history = new DevOopsStatusHistory;
        $history->devoops_sub_category_id = $id;
        $history->old_value = $devoops->status_id;
        $history->new_value = $status_name;
        $history->user_id = Auth::user()->id;
        $history->save();

        $devoops->status_id = $status_name;
        $devoops->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $dostatus = DevOopsStatus::find($key);
            $dostatus->status_color = $value;
            $dostatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function getStatusHistories(Request $request): JsonResponse
    {
        $datas = DevOopsStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('devoops_sub_category_id', $request->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function uploadFile(UploadFileDevOppRequest $request): RedirectResponse
    {

        $data = $request->all();
        try {
            foreach ($data['file'] as $file) {
                DB::transaction(function () use ($file, $data) {
                    $googleScreencast = new GoogleScreencast;

                    $googleScreencast->file_name = $file->getClientOriginalName();

                    $googleScreencast->extension = $file->extension();
                    $googleScreencast->user_id = Auth::user()->id;

                    $googleScreencast->read = '';
                    $googleScreencast->write = '';

                    $googleScreencast->remarks = $data['remarks'];
                    $googleScreencast->file_creation_date = $data['file_creation_date'];

                    $googleScreencast->dev_oops_id = $data['task_id'];
                    $googleScreencast->save();
                    UploadGoogleDriveScreencast::dispatchSync($googleScreencast, $file);
                });
            }

            return redirect()->back()->with('success', 'File is Uploaded to Google Drive.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    //dev_oops_id
    public function getUploadedFilesList(Request $request): JsonResponse
    {
        try {
            $result = [];
            if (isset($request->dev_oops_id)) {
                $result = GoogleScreencast::where('dev_oops_id', $request->dev_oops_id)->orderByDesc('id')->with('user')->get();
                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('dev-oops.google-drive-list', compact('result'))->render(),
                ]);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'data' => view('dev-oops.google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $id = $request->get('devoops_task_id', 0);
        $subject = $request->get('subject', null);

        $loggedUser = $request->user();

        if ($id > 0 && ! empty($subject)) {
            $devTask = DevOppsSubCategory::find($id);

            if (! empty($devTask)) {
                $devDocuments = new DevOppsSubCategoryDocument;
                $devDocuments->fill(request()->all());
                $devDocuments->created_by = Auth::id();
                $devDocuments->save();

                if ($request->hasfile('files')) {
                    foreach ($request->file('files') as $files) {
                        $media = MediaUploader::fromSource($files)
                            ->toDirectory('developertask/'.floor($devTask->id / config('constants.image_per_folder')))
                            ->upload();
                        $devDocuments->attachMedia($media, config('constants.media_tags'));
                    }

                    $message = '[ '.$loggedUser->name.' ] - #DEVTASK-'.$devTask->id.' - '.$devTask->subject." \n\n".'New attchment(s) called '.$subject.' has been added. Please check and give your comment or fix it if any issue.';

                    MessageHelper::sendEmailOrWebhookNotification([Auth::user()->id], $message);
                }

                return response()->json(['code' => 200, 'success' => 'Done!']);
            }

            return response()->json(['code' => 500, 'error' => 'Oops, There is no record in database']);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields']);
        }
    }

    public function getDocument(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);

        if ($id > 0) {
            $devDocuments = DevOppsSubCategoryDocument::where('devoops_task_id', $id)->latest()->get();

            $mediaTags = config('constants.media_tags'); // Use config variable

            $html = view('dev-oops.document-list', compact('devDocuments', 'mediaTags'))->render();

            return response()->json(['code' => 200, 'data' => $html]);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, id is required field']);
        }
    }
}
