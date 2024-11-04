<?php

namespace App\Http\Controllers;

use App\GoogleScreencast;
use App\Http\Requests\BuilderIOTaskstoreUicheckRequest;
use App\Http\Requests\UploadFileUicheckRequest;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Language;
use App\Models\UicheckHistory;
use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;
use App\SiteDevelopmentStatus;
use App\StoreWebsite;
use App\Task;
use App\UiAdminStatusHistoryLog;
use App\Uicheck;
use App\UiCheckAssignToHistory;
use App\UicheckAttachement;
use App\UiCheckCommunication;
use App\UicheckDeviceAttachment;
use App\UiCheckIssueHistoryLog;
use App\UicheckLangAttchment;
use App\UicheckLanguageMessageHistory;
use App\UicheckType;
use App\UicheckUserAccess;
use App\UiDeveloperStatusHistoryLog;
use App\UiDevice;
use App\UiDeviceBuilderIoData;
use App\UiDeviceBuilderIoDataDownloadHistory;
use App\UiDeviceBuilderIoDataRemarkHistory;
use App\UiDeviceBuilderIoDataStatus;
use App\UiDeviceBuilderIoDataStatusHistory;
use App\UiDeviceHistory;
use App\UiDeviceLog;
use App\UiDeviceUserHistory;
use App\UiLanguage;
use App\UiResponsivestatusHistory;
use App\UiTranslatorStatusHistory;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Throwable;

class UicheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax() || request('draw')) {
            if (Auth::user()->hasRole('Admin')) {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        'uichecks.id AS uicheck_id',
                        'uichecks.issue',
                        'uichecks.website_id AS websiteid',
                        'uichecks.uicheck_type_id',
                        'uichecks.dev_status_id',
                        'uichecks.admin_status_id',
                        'uichecks.lock_developer',
                        'uichecks.lock_admin',
                        'uichecks.language_flag',
                        'uichecks.translation_flag',
                        'uua.user_id as accessuser'
                    );
                if ($s = request('srch_lock_type')) {
                    if ($s == 1) {
                        $q->where('uichecks.lock_developer', 0);
                        $q->where('uichecks.lock_admin', 0);
                    } elseif ($s == 2) {
                        $q->where('uichecks.lock_developer', 1);
                        $q->where('uichecks.lock_admin', 1);
                    } elseif ($s == 3) {
                        $q->where('uichecks.lock_developer', 0);
                        $q->where('uichecks.lock_admin', 1);
                    } elseif ($s == 4) {
                        $q->where('uichecks.lock_developer', 1);
                        $q->where('uichecks.lock_admin', 0);
                    }
                }
            } else {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->join('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('uua.user_id', '=', Auth::user()->id)
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->where('uichecks.lock_developer', '=', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        'uichecks.id AS uicheck_id',
                        'uichecks.issue',
                        'uichecks.website_id AS websiteid',
                        'uichecks.uicheck_type_id',
                        'uichecks.dev_status_id',
                        'uichecks.admin_status_id',
                        'uichecks.lock_developer',
                        'uichecks.lock_admin',
                        'uichecks.language_flag',
                        'uichecks.translation_flag',
                        'uua.user_id as accessuser'
                    );
            }

            if ($s = request('category_name')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('uichecks.website_id', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('sub_category_name')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('site_development_categories.id', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('dev_status')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('uichecks.dev_status_id', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('admin_status')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('uichecks.admin_status_id', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('assign_to')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('uua.user_id', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('id')) {
                $q = $q->where('uichecks.id', $s);
            }

            if ($s = request('srch_flags')) {
                if ($s == 'Both') {
                    $q = $q->where('uichecks.language_flag', 1);
                    $q = $q->orWhere('uichecks.translation_flag', 1);
                } elseif ($s == 'Language flag') {
                    $q = $q->where('uichecks.language_flag', 1);
                } elseif ($s == 'Translation flag') {
                    $q = $q->where('uichecks.translation_flag', 1);
                }
            }

            $q->groupBy('uichecks.id');

            if ($s = request('order_by')) {
                $q->orderByRaw('uichecks.'.request('order_by').' DESC, uichecks.updated_at DESC');
            } else {
                $q->orderByDesc('uichecks.updated_at');
            }

            $counter = $q->get();

            return datatables()->eloquent($q)->toJson();
        } else {
            $data = [];
            $data['all_store_websites'] = StoreWebsite::all();
            $data['users'] = User::select('id', 'name')->get();
            $data['allTypes'] = UicheckType::orderBy('name')->pluck('name', 'id')->toArray();
            $data['categories'] = SiteDevelopmentCategory::paginate(20); //all();
            $data['search_website'] = isset($request->store_webs) ? $request->store_webs : '';
            $data['search_category'] = isset($request->categories) ? $request->categories : '';
            $data['user_id'] = isset($request->user_id) ? $request->user_id : '';
            $data['assign_to'] = isset($request->assign_to) ? $request->assign_to : '';
            $data['dev_status'] = isset($request->dev_status) ? $request->dev_status : '';
            $data['admin_status'] = isset($request->admin_status) ? $request->admin_status : '';
            $data['site_development_status_id'] = isset($request->site_development_status_id) ? $request->site_development_status_id : [];
            $data['allStatus'] = SiteDevelopmentStatus::pluck('name', 'id')->toArray();
            $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments', 'store_websites.id', '=', 'site_developments.website_id');
            if ($data['search_website'] != '') {
                $store_websites = $store_websites->where('store_websites.id', $data['search_website']);
            }
            $data['store_websites'] = $store_websites->where('is_ui', 1)->groupBy('store_websites.id')->get();
            $data['allUsers'] = User::query()
                ->join('role_user', 'role_user.user_id', 'users.id')
                ->join('roles', 'roles.id', 'role_user.role_id')
                ->where('roles.name', 'Developer')
                ->pluck('users.name', 'users.id')->toArray();

            $data['log_user_id'] = Auth::user()->id ?? '';

            $q = SiteDevelopmentCategory::query()
                ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                ->select(
                    'site_development_categories.*',
                    'site_developments.id AS site_id',
                    'site_developments.website_id',
                    'uichecks.id AS uicheck_id',
                    'uichecks.language_flag',
                    'uichecks.translation_flag'
                )
                ->where('uichecks.id', '>', 0);

            if ($data['search_website'] != '') {
                $q = $q->where('uichecks.website_id', $data['store_websites'][0]->id);
            }
            if ($data['search_category'] != '') {
                $q = $q->where('site_development_categories.id', $data['search_category']);
            }
            $q->groupBy('uichecks.id');
            $q->orderByDesc('uichecks.updated_at');
            $data['site_development_categories'] = $q->pluck('site_development_categories.title', 'site_development_categories.id')->toArray();
            $data['record_count'] = count($q->get());
            $data['languages'] = Language::all();

            return view('uicheck.index', $data);
        }
    }

    public function access(Request $request): JsonResponse
    {
        $check = UicheckUserAccess::where('uicheck_id', $request->uicheck_id)->first();
        if (! is_null($check)) {
            $access = UicheckUserAccess::find($check->id);
            $access->delete();
        }
        $this->CreateUiAssignToHistoryLog($request, $check);
        $array = [
            'user_id' => $request->id,
            'uicheck_id' => $request->uicheck_id,
        ];
        UicheckUserAccess::create($array);

        return response()->json(['code' => 200, 'message' => 'Permission Given!!!']);
    }

    public function typeSave(Request $request): JsonResponse
    {
        $array = [
            'uicheck_type_id' => $request->type,
        ];
        Uicheck::where('id', $request->uicheck_id)->update($array);

        return response()->json(['code' => 200, 'message' => 'Type Updated!!!']);
    }

    public function createDuplicateCategory(Request $request): JsonResponse
    {
        $uiCheck = Uicheck::where('id', $request->id)->first();
        Uicheck::create([
            'site_development_id' => $uiCheck->site_development_id ?? '',
            'site_development_category_id' => $uiCheck->site_development_category_id ?? '',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        return response()->json(['code' => 200, 'message' => 'Category Duplicate Created successfully!!!']);
    }

    public function upload_document(Request $request): JsonResponse
    {
        $uicheck_id = $request->uicheck_id;
        $subject = $request->subject;
        $description = $request->description;

        if ($uicheck_id > 0 && ! empty($subject)) {
            if ($request->hasfile('files')) {
                $path = public_path('uicheckdocs');
                if (! file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $uicheckDocName = '';
                if ($request->file('files')) {
                    $file = $request->file('files')[0];
                    $uicheckDocName = uniqid().'_'.trim($file->getClientOriginalName());
                    $file->move($path, $uicheckDocName);
                }
                $docArray = [
                    'user_id' => Auth::id(),
                    'filename' => $uicheckDocName,
                    'uicheck_id' => $uicheck_id,
                    'subject' => $subject,
                    'description' => $description,
                ];
                UicheckAttachement::create($docArray);

                return response()->json(['code' => 200, 'success' => 'Done!']);
            } else {
                return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields']);
            }
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields']);
        }
    }

    public function getDocument(Request $request): JsonResponse
    {
        $id = $request->get('id', 0);

        if ($id > 0) {
            $devDocuments = UicheckAttachement::with('user', 'uicheck')->where('uicheck_id', $id)->latest()->get();
            $html = view('uicheck.ajax.document-list', compact('devDocuments'))->render();

            return response()->json(['code' => 200, 'data' => $html]);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, id is required field']);
        }
    }

    public function typeStore(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        UicheckType::create($data);

        return redirect()->back()->with('success', 'You have successfully created a status!');
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
        try {
            $uicheck = Uicheck::find($request->id);
            if (empty($uicheck)) {
                $uicheck = new Uicheck;
            }

            $uicheck->site_development_id = $request->site_development_id;
            $uicheck->site_development_category_id = $request->category;

            if ($request->website_id) {
                $uicheck->website_id = $request->website_id;
            }
            if ($request->issue) {
                if ($request->issue != $uicheck->issue) {
                    $this->CreateUiissueHistoryLog($request, $uicheck);
                }
                $uicheck->issue = $request->issue;
            }
            if ($request->developer_status) {
                if ($request->developer_status != $uicheck->developer_status) {
                    $this->CreateUiDeveloperStatusHistoryLog($request, $uicheck);
                }
                $uicheck->dev_status_id = $request->developer_status;
            }
            if ($request->admin_status) {
                if ($request->admin_status != $uicheck->admin_status_id) {
                    $this->createUiAdminStatusHistoryLog($request, $uicheck);
                }
                $uicheck->admin_status_id = $request->admin_status;
            }

            $uicheck->save();

            return response()->json(['code' => 200, 'data' => $uicheck, 'message' => 'Updated successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiAdminStatusHistoryLog  $createUiAdminStatusHistoryLog
     * @param  mixed  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function CreateUiAdminStatusHistoryLog(Request $request, $uicheck)
    {
        $adminStatusLog = new UiAdminStatusHistoryLog;
        $adminStatusLog->user_id = Auth::user()->id;
        $adminStatusLog->uichecks_id = $request->id;
        $adminStatusLog->old_status_id = $uicheck->admin_status_id;
        $adminStatusLog->status_id = $request->admin_status;
        $adminStatusLog->save();
    }

    public function getUiAdminStatusHistoryLog(Request $request): JsonResponse
    {
        $adminStatusLog = UiAdminStatusHistoryLog::select('ui_admin_status_history_logs.*', 'users.name as userName', 'site_development_statuses.name AS dev_status', 'old_stat.name AS old_name')
            ->leftJoin('users', 'users.id', 'ui_admin_status_history_logs.user_id')
            ->leftJoin('site_development_statuses', 'site_development_statuses.id', 'ui_admin_status_history_logs.status_id')
            ->leftJoin('site_development_statuses as old_stat', 'old_stat.id', 'ui_admin_status_history_logs.old_status_id')
            ->where('ui_admin_status_history_logs.uichecks_id', $request->id)
            ->orderByDesc('ui_admin_status_history_logs.id')
            ->get();

        $html = View::make('uicheck.ui-adminstatus-historyLog', ['adminStatusLog' => $adminStatusLog])->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiDeveloperStatusHistoryLog  $createUiDeveloperStatusHistoryLog
     * @param  mixed  $uicheck
     * @return \Illuminate\Http\Response
     */
    public function CreateUiDeveloperStatusHistoryLog(Request $request, $uicheck)
    {
        $devStatusLog = new UiDeveloperStatusHistoryLog;
        $devStatusLog->user_id = Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_status_id = $uicheck->dev_status_id;
        $devStatusLog->status_id = $request->developer_status;
        $devStatusLog->save();
    }

    public function getUiDeveloperStatusHistoryLog(Request $request): JsonResponse
    {
        $adminStatusLog = UiDeveloperStatusHistoryLog::select('ui_developer_status_history_logs.*', 'users.name as userName', 'site_development_statuses.name AS dev_status', 'old_stat.name AS old_name')
            ->leftJoin('users', 'users.id', 'ui_developer_status_history_logs.user_id')
            ->leftJoin('site_development_statuses', 'site_development_statuses.id', 'ui_developer_status_history_logs.status_id')
            ->leftJoin('site_development_statuses as old_stat', 'old_stat.id', 'ui_developer_status_history_logs.old_status_id')
            ->where('ui_developer_status_history_logs.uichecks_id', $request->id)
            ->orderByDesc('ui_developer_status_history_logs.id')
            ->get();

        $html = View::make('uicheck.ui-developerstatus-historyLog', ['adminStatusLog' => $adminStatusLog])->render();

        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);

    }

    public function CreateUiissueHistoryLog(Request $request, $uicheck)
    {
        $devStatusLog = new UiCheckIssueHistoryLog;
        $devStatusLog->user_id = Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_issue = $uicheck->issue;
        $devStatusLog->issue = $request->issue;
        $devStatusLog->save();
    }

    public function getUiIssueHistoryLog(Request $request): JsonResponse
    {
        try {
            $getIssueLog = UiCheckIssueHistoryLog::select('ui_check_issue_history_logs.*', 'users.name as userName')
                ->leftJoin('users', 'users.id', 'ui_check_issue_history_logs.user_id')
                ->where('ui_check_issue_history_logs.uichecks_id', $request->id)
                ->orderByDesc('ui_check_issue_history_logs.id')
                ->get();

            $html = View::make('uicheck.ui-issue-historylog', ['getIssueLog' => $getIssueLog])->render();

            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);

        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getUiCheckMessageHistoryLog(Request $request): JsonResponse
    {
        try {
            $getMessageLog = UiCheckCommunication::select('ui_check_communications.*', 'users.name as userName')
                ->leftJoin('users', 'users.id', 'ui_check_communications.user_id')
                ->where('ui_check_communications.uichecks_id', $request->id)
                ->orderByDesc('ui_check_communications.id')
                ->get();

            $html = View::make('uicheck.ui-check-message-historylog', ['getMessageLog' => $getMessageLog])->render();

            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);

        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function CreateUiMessageHistoryLog(Request $request): JsonResponse
    {
        $messageLog = new UiCheckCommunication;
        $messageLog->user_id = Auth::user()->id;
        $messageLog->uichecks_id = $request->id;
        $messageLog->message = $request->message;
        $messageLog->save();
        $uicheck = Uicheck::find($request->id);
        $uicheck->updated_at = \Carbon\Carbon::now();
        $uicheck->save();

        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function CreateUiAssignToHistoryLog(Request $request, $uicheck): JsonResponse
    {
        $messageLog = new UiCheckAssignToHistory;
        $messageLog->user_id = Auth::user()->id;
        $messageLog->uichecks_id = $request->uicheck_id;
        $messageLog->assign_to = $request->id;
        $messageLog->old_assign_to = $uicheck->user_id ?? '';
        $messageLog->save();

        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function getUiCheckAssignToHistoryLog(Request $request): JsonResponse
    {
        try {
            $getMessageLog = UiCheckAssignToHistory::select('ui_check_assign_to_histories.*', 'users.name as userName', 'assignTo.name AS assignToName')
                ->leftJoin('users', 'users.id', 'ui_check_assign_to_histories.user_id')
                ->leftJoin('users AS assignTo', 'assignTo.id', 'ui_check_assign_to_histories.assign_to')
                ->where('ui_check_assign_to_histories.uichecks_id', $request->id)
                ->orderByDesc('ui_check_assign_to_histories.id')
                ->get();

            $html = View::make('uicheck.ui-check-assign-to-historylog', ['getMessageLog' => $getMessageLog])->render();

            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);

        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function historyAll(Request $request)
    {
        try {
            $lastDate = request('lastDate') ?: date('Y-m-d H:i:s');

            $whQ = '';
            $whArr = [$lastDate];
            if (! Auth::user()->hasRole('Admin')) {
                $whQ .= ' AND listdata.uichecks_id IN ( SELECT uicheck_id FROM uicheck_user_accesses WHERE user_id = ? ) ';
                $whArr[] = Auth::user()->id;
            }
            if (request('user_id')) {
                $whQ .= ' AND listdata.user_id = ?';
                $whArr[] = request('user_id');
            }

            $sql = "SELECT
                    listdata.*,
                    sdc.title AS site_development_category_name,
                    sw.title AS store_website_name,
                    u.name AS addedBy
                FROM (
                    (
                        SELECT
                        curr.uichecks_id,
                        'assign' AS type,
                        curr.old_assign_to AS old_val,
                        curr.assign_to AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_check_assign_to_histories AS curr
                        LEFT JOIN users AS ov ON ov.id = curr.old_assign_to
                        LEFT JOIN users AS nv ON nv.id = curr.assign_to
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        'issue' AS type,
                        old_issue AS old_val,
                        issue AS new_val,
                        old_issue AS old_disp_val,
                        issue AS new_disp_val,
                        user_id,
                        created_at
                        FROM ui_check_issue_history_logs
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        'communication' AS type,
                        NULL AS old_val,
                        message AS new_val,
                        NULL AS old_disp_val,
                        message AS new_disp_val,
                        user_id,
                        created_at
                        FROM ui_check_communications
                    )
                    UNION
                    (
                        SELECT
                        curr.uichecks_id,
                        'developer_status' AS type,
                        curr.old_status_id AS old_val,
                        curr.status_id AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_developer_status_history_logs AS curr
                        LEFT JOIN site_development_statuses AS ov ON ov.id = curr.old_status_id
                        LEFT JOIN site_development_statuses AS nv ON nv.id = curr.status_id
                    )
                    UNION
                    (
                        SELECT
                        curr.uichecks_id,
                        'admin_status' AS type,
                        curr.old_status_id AS old_val,
                        curr.status_id AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_admin_status_history_logs AS curr
                        LEFT JOIN site_development_statuses AS ov ON ov.id = curr.old_status_id
                        LEFT JOIN site_development_statuses AS nv ON nv.id = curr.status_id
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        type,
                        old_val,
                        new_val,
                        old_val AS old_disp_val,
                        new_val AS new_disp_val,
                        user_id,
                        created_at
                        FROM  uichecks_hisotry
                    )
                ) AS listdata
                LEFT JOIN users AS u ON u.id = listdata.user_id
                LEFT JOIN uichecks AS uic ON uic.id = listdata.uichecks_id
                LEFT JOIN site_development_categories AS sdc ON sdc.id = uic.site_development_category_id
                LEFT JOIN store_websites AS sw ON sw.id = uic.website_id
                WHERE listdata.created_at < ? 
                ".$whQ.' 
                ORDER BY listdata.created_at DESC
                LIMIT 10';
            $data = DB::select($sql, $whArr);
            $html = view('uicheck.history-all', compact('data'))->render();

            return response()->json(['code' => 200, 'html' => $html]);

        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function get()
    {
        try {
            if ($single = Uicheck::find(request('id'))) {
                return respJson(200, '', [
                    'data' => $single,
                ]);
            }

            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function updateDates()
    {
        try {
            if ($single = Uicheck::find(request('id'))) {
                $single->updateElement('start_time', request('start_time'));
                $single->updateElement('expected_completion_time', request('expected_completion_time'));
                if (Auth::user()->hasRole('Admin')) {
                    $single->updateElement('actual_completion_time', request('actual_completion_time'));
                }

                return respJson(200, 'Dates updated successfully.', []);
            }

            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function historyDates()
    {
        try {
            $data = UicheckHistory::with('updatedBy')->orderByDesc('id')->get();

            $html = view('uicheck.history-dates', compact('data'))->render();

            return respJson(200, '', [
                'html' => implode('', $html),
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function bulkShow()
    {
        try {
            $uiCheckIds = request('uiCheckIds');

            $uiChecks = Uicheck::find($uiCheckIds);
            foreach ($uiChecks as $uiCheck) {
                $uiCheck->updateElement('lock_developer', 0);
            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function bulkHide()
    {
        try {
            $uiCheckIds = request('uiCheckIds');

            $uiChecks = Uicheck::find($uiCheckIds);
            foreach ($uiChecks as $uiCheck) {
                $uiCheck->updateElement('lock_developer', 1);
            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function updateLock()
    {
        try {
            if ($single = Uicheck::find(request('id'))) {
                $key = request('type') == 'developer' ? 'lock_developer' : 'lock_admin';
                // Have to update the lock status for separate user wise. So the above code hide.
                // Below logic is - It will lock uicheck in user wise.
                $uicheckUserAccess = UicheckUserAccess::where('uicheck_id', request('id'))->where('user_id', request('user_accessable_user_id'))->first();
                if ($uicheckUserAccess) {
                    $uicheckUserAccess->$key = $uicheckUserAccess->$key ? 0 : 1;
                    $uicheckUserAccess->save();
                }

                return respJson(200, 'Record updated successfully.', []);
            }

            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function updateLanguage(Request $request)
    {
        try {
            $uiLanData = UiLanguage::where('languages_id', '=', $request->id)->get();

            $uiLan['user_id'] = Auth::user()->id;
            $uiLan['languages_id'] = $request->id;
            $uiLan['uicheck_id'] = $request->uicheck_id;
            if ($request->message) {
                $uiLan['message'] = $request->message;
            }
            if ($request->uilanstatus) {
                $uiLan['status'] = $request->uilanstatus;
            }
            if ($request->estimated_time) {
                $uiLan['estimated_time'] = $request->estimated_time;
            }

            if (count($uiLanData) == 0) {
                $uiLans = UiLanguage::create($uiLan);
                $uiData = UiLanguage::where('languages_id', $uiLans->id)->first();
            } else {
                $uiData = UiLanguage::where('languages_id', $request->id)->first();
                $uiLans = UiLanguage::where('languages_id', $request->id)->update($uiLan);
            }

            $uiMess = $uiLanData[0]->message ?? '';
            $uiLan['ui_languages_id'] = $uiData->id ?? $request->id;
            if ($request->message != $uiMess) {
                $reData = $this->uicheckLanUpdateHistory($uiLan);
            }
            $uistatus = $uiData->status ?? '';
            if ($request->uilanstatus != $uistatus) {
                //
            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function uicheckLanUpdateHistory($data)
    {
        try {
            $createdHistory = UicheckLanguageMessageHistory::create(
                $data
            );
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function getuicheckLanUpdateHistory(Request $request)
    {
        try {
            $getHistory = UicheckLanguageMessageHistory::leftJoin('users', 'users.id', 'uicheck_language_message_histories.user_id')
                ->leftJoin('site_development_statuses AS sds', 'sds.id', 'uicheck_language_message_histories.status')
                ->select('uicheck_language_message_histories.*', 'users.name As userName', 'sds.name as status_name')
                ->where('languages_id', $request->id)
                ->where('uicheck_id', $request->uicheck_id)
                ->orderByDesc('id')->get();

            $html = view('uicheck.ui-checklan-update-history', compact('data'))->render();

            return respJson(200, '', [
                'html' => implode('', $html),
            ]);
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            $uiDevData = UiLanguage::where('languages_id', '=', $request->id)->where('uicheck_id', '=', $request->uicheck_id)->first();

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('uicheckAttach/'.floor($request->id / config('constants.image_per_folder')))
                    ->upload();
                $attachment = UicheckLangAttchment::create([
                    'languages_id' => $request->id,
                    'user_id' => Auth::user()->id,
                    'uicheck_id' => $request->uicheck_id ?? '',
                    'attachment' => $media,
                ]);
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function listDocuments(Request $request): JsonResponse
    {
        $uicheckAttch = UicheckLangAttchment::where('languages_id', $request->id)
            ->where('uicheck_id', $request->uicheck_id)
            ->get();

        $userList = [];

        $records = [];
        if ($uicheckAttch) {
            foreach ($uicheckAttch as $media) {
                // Convert JSON string to Object
                $imagepath = json_decode($media->attachment);
                $records[] = [
                    'id' => $media->id,
                    'url' => 'uploads/'.$imagepath->directory.'/'.$imagepath->filename.'.'.$imagepath->extension,
                    'ui_attach_id' => $media->id,
                ];
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function deleteDocument(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $uicheckAttch = UicheckLangAttchment::where('id', $request->id)->delete();

            return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
        }

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function updateDevice(Request $request)
    {
        try {
            // Fetch existing device data based on the conditions
            $uiDevData = UiDevice::where([
                ['uicheck_id', '=', $request->uicheck_id],
                ['device_no', '=', $request->device_no],
                ['user_id', '=', $request->user_access_user_id],
            ])->first();

            // Track changes for logging history
            $logHistory = false;

            // Prepare the data to be updated/inserted
            $uiDev = [
                'user_id' => $request->user_access_user_id,
                'device_no' => $request->device_no,
                'uicheck_id' => $request->uicheck_id,
            ];

            // Update fields only if present and not equal to current values
            $fieldsToUpdate = [
                'message' => 'message',
                'uidevstatus' => 'status',
                'uidevdatetime' => 'estimated_time',
                'uidevExpectedStartTime' => 'expected_start_time',
                'uidevExpectedCompletionTime' => 'expected_completion_time',
            ];

            foreach ($fieldsToUpdate as $requestKey => $dbKey) {
                if ($request->filled($requestKey) && ($uiDevData->$dbKey ?? '') !== $request->$requestKey) {
                    $uiDev[$dbKey] = $request->$requestKey;
                    $logHistory = true;
                }
            }
            // Create or update the device record
            if ($uiDevData) {
                $uiDevData->update($uiDev);
                $uiData = $uiDevData;
            } else {
                $uiData = UiDevice::create($uiDev);
            }

            // Log history if there are changes and message has been updated
            if ($logHistory && ($uiDevData->message ?? '') !== $request->message) {
                $uiDev['ui_devices_id'] = $uiData->id;
                $this->uicheckDevUpdateHistory($uiDev);

            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (Throwable $th) {
            return respException($th);
        }
    }

    public function setDeviceLog(Request $request)
    {
        try {
            $uiDevice = UiDevice::where('uicheck_id', '=', $request->uicheckId)->where('device_no', '=', $request->deviceNo)->where('user_id', '=', $request->user_access_user_id)->first();

            if ($uiDevice) {
                $uiDeviceLog = UiDeviceLog::where('user_id', $request->user_access_user_id)
                    ->where('uicheck_id', $request->uicheckId)
                    ->where('ui_device_id', $uiDevice->id)
                    ->whereNotNull('start_time')
                    ->whereNull('end_time')
                    ->first();

                // If toggle event is true
                if ($request->eventType == 'true') {
                    if ($uiDeviceLog) {
                        // While toggle ON, If record already exists then just update the start time once again.
                        $uiDeviceLog['start_time'] = \Carbon\Carbon::now();
                        $uiDeviceLog->save();
                    } else {
                        // While toggle ON, If record not exists then create new entry.
                        $uiDeviceLogNew['user_id'] = $request->user_access_user_id;
                        $uiDeviceLogNew['uicheck_id'] = $request->uicheckId;
                        $uiDeviceLogNew['ui_device_id'] = $uiDevice->id;
                        $uiDeviceLogNew['start_time'] = \Carbon\Carbon::now();

                        UiDeviceLog::create($uiDeviceLogNew);
                    }

                    return respJson(200, 'Device log created successfully.', []);
                } else {
                    // While toggle OFF, If record exists then just update the end time.
                    if ($uiDeviceLog) {
                        $uiDeviceLog['end_time'] = \Carbon\Carbon::now();
                        $uiDeviceLog->save();
                    }

                    return respJson(200, 'Device log updated successfully.', []);
                }
            } else {
                return respJson(404, 'Device entry not found', []);
            }
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function deviceLogs(Request $request)
    {
        try {
            $uiDevices = UiDevice::has('uiDeviceHistories')
                ->join('uichecks as uic', 'uic.id', 'ui_devices.uicheck_id')
                ->leftJoin('uicheck_user_accesses as uua', 'ui_devices.uicheck_id', 'uua.uicheck_id')
                ->leftJoin('users as u', 'u.id', 'uua.user_id')
                ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
                ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id')
                ->leftJoin('site_development_statuses as sds', 'sds.id', 'ui_devices.status');

            if ($request->category != '') {
                $uiDevices = $uiDevices->where('uic.site_development_category_id', $request->category);
            }

            if ($request->uicheck_type != '') {
                $uiDevices = $uiDevices->where('uic.uicheck_type_id', $request->uicheck_type);
            }

            if ($request->status != '') {
                $uiDevices = $uiDevices->where('ui_devices.status', $request->status);
            }

            if ($request->user_name != null && $request->user_name != 'undefined') {
                $uiDevices = $uiDevices->whereIn('u.id', $request->user_name);
            }

            if ($request->daterange != '') {
                $date = explode('-', $request->daterange);
                $datefrom = date('Y-m-d', strtotime($date[0]));
                $dateto = date('Y-m-d', strtotime($date[1]));
                $uiDevices = $uiDevices->whereRaw("date(ui_devices.expected_completion_time) between date('$datefrom') and date('$dateto')");
            }

            // If not an admin, then get logged in user logs only.
            if (! Auth::user()->hasRole('Admin')) {
                $uiDevices = $uiDevices->where(['uua.user_id' => Auth::user()->id]);
            }

            $uiDevices = $uiDevices->select('ui_devices.*', 'sw.website', 'sdc.title', 'u.name', 'uic.uicheck_type_id', 'sds.color')
                ->orderByDesc('ui_devices.updated_at')
                ->paginate(10);

            $siteDevelopmentCategories = SiteDevelopmentCategory::pluck('title', 'id')->toArray();
            $allUsers = User::where('is_active', '1')->get();
            $allUicheckTypes = UicheckType::get()->pluck('name', 'id')->toArray();
            $allStatus = SiteDevelopmentStatus::pluck('name', 'id')->toArray();

            return view('uicheck.device-logs', compact('uiDevices', 'siteDevelopmentCategories', 'allUsers', 'allStatus', 'allUicheckTypes'))->with('i', ($request->input('page', 1) - 1) * 10);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function deviceHistories(Request $request)
    {
        try {
            $uiDeviceHistories = UiDeviceHistory::join('ui_devices as uid', 'uid.id', 'ui_device_histories.ui_devices_id')
                ->leftJoin('users', 'users.id', 'ui_device_histories.user_id')
                ->leftJoin('uichecks as uic', 'uic.id', 'ui_device_histories.uicheck_id')
                ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
                ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id');

            if ($request->category != '') {
                $uiDeviceHistories = $uiDeviceHistories->where('uic.site_development_category_id', $request->category);
            }

            if ($request->user_name != null && $request->user_name != 'undefined') {
                $uiDeviceHistories = $uiDeviceHistories->whereIn('ui_device_histories.user_id', $request->user_name);
            }

            // If not an admin, then get logged in user logs only.
            if (! Auth::user()->hasRole('Admin')) {
                $uiDeviceHistories = $uiDeviceHistories->where('ui_device_histories.user_id', Auth::user()->id);
            }

            $uiDeviceHistories = $uiDeviceHistories->select('ui_device_histories.*', 'sw.website', 'sdc.title', 'users.name')
                ->orderByDesc('ui_device_histories.id')
                ->paginate(25);

            $siteDevelopmentCategories = SiteDevelopmentCategory::pluck('title', 'id')->toArray();
            $allUsers = User::where('is_active', '1')->get();
            $siteDevelopmentStatuses = SiteDevelopmentStatus::pluck('name', 'id')->toArray();

            return view('uicheck.device-histories', compact('uiDeviceHistories', 'siteDevelopmentCategories', 'allUsers', 'siteDevelopmentStatuses'))->with('i', ($request->input('page', 1) - 1) * 25);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function responseDevicePage(Request $request)
    {
        try {
            DB::enableQueryLog();
            $uiDevDatas = new UiDevice;
            $uiDevDatas = $uiDevDatas->with('uichecks.uiDevice.lastUpdatedStatusHistory.stausColor')->join('uichecks as uic', 'uic.id', 'ui_devices.uicheck_id')
                ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
                ->leftJoin('uicheck_user_accesses as uua', function ($join) {
                    $join->on('ui_devices.uicheck_id', '=', 'uua.uicheck_id')
                        ->on('ui_devices.user_id', '=', 'uua.user_id'); // Additional condition
                })
                ->leftJoin('users as u', 'u.id', 'uua.user_id')
                ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id')
                ->leftJoin('site_development_statuses as sds', 'sds.id', 'ui_devices.status')
                ->leftJoin('ui_device_histories as udh', 'ui_devices.id', 'udh.ui_devices_id');
            $uiDevDatas->whereNull('uic.deleted_at');

            $isAdmin = Auth::user()->isAdmin();
            $show_inactive = 0;
            if ($isAdmin) {
                if ($request->show_inactive == 'inactive') {
                    $show_inactive = 1;
                    $uiDevDatas = $uiDevDatas->where('uua.lock_developer', 1);
                } elseif ($request->show_inactive == 'active') {
                    $uiDevDatas = $uiDevDatas->where('uua.lock_developer', 0);
                }
                // otherwise show all.
            } else {
                // Non admin user - Show only lock = 0 records
                $uiDevDatas = $uiDevDatas->where('uua.lock_developer', 0);
            }

            if ($request->status != '') {
                $uiDevDatas = $uiDevDatas->where('ui_devices.status', $request->status);
            }
            if (! empty($request->categories) && $request->categories[0] != null) {
                $uiDevDatas = $uiDevDatas->whereIn('uic.site_development_category_id', $request->categories)->where('ui_devices.device_no', '1');
            }

            $search_website = isset($request->store_webs) ? $request->store_webs : ['1', '3', '5', '9', '17'];
            if ($search_website) {
                $uiDevDatas = $uiDevDatas->whereIn('uic.website_id', $search_website)->where('ui_devices.device_no', '1');
            }
            if ($request->id != '') {
                $uiDevDatas = $uiDevDatas->where('ui_devices.uicheck_id', $request->id);
            }

            if ($request->user_name != null && $request->user_name != 'undefined') {
                $uiDevDatas = $uiDevDatas->whereIn('u.id', $request->user_name);
            }

            if (! Auth::user()->hasRole('Admin')) {
                $uiDevDatas = $uiDevDatas->where(['uua.user_id' => Auth::user()->id]);
            }

            if (! empty($request->type) && $request->type[0] != null) {
                $uiDevDatas = $uiDevDatas->whereIn('uic.uicheck_type_id', $request->type);
            }

            if ($request->website != '') {
                $uiDevDatas = $uiDevDatas->where('uic.website_id', $request->website);
            }

            if ($request->user != '') {
                $uiDevDatas = $uiDevDatas->where('ui_devices.user_id', $request->user);
            }

            $uiDevDatas = $uiDevDatas->select('ui_devices.*', 'uic.uicheck_type_id', 'u.name as username', 'sw.website', 'sdc.title', 'sds.name as statusname', 'uua.lock_developer',
                DB::raw('(select message from ui_device_histories where uicheck_id  =   ui_devices.id  order by id DESC limit 1) as messageDetail'),
                'u.id AS user_accessable_user_id', // New - Separate row for every user
                'u.name AS user_accessable' // New - Separate row for every user
            )->orderByDesc('ui_devices.id')->groupBy(['ui_devices.uicheck_id', 'u.id'])->paginate(30);

            $allStatus = SiteDevelopmentStatus::pluck('name', 'id')->toArray();
            $status = '';
            $devid = '';
            $uicheck_id = '';
            $site_development_categories = SiteDevelopmentCategory::pluck('title', 'id')->toArray();
            $allUsers = User::where('is_active', '1')->get();

            $siteDevelopmentStatuses = SiteDevelopmentStatus::get();

            $store_websites = StoreWebsite::get()->pluck('website', 'id');
            $allUicheckTypes = UicheckType::get()->pluck('name', 'id')->toArray();

            return view('uicheck.responsive', compact('uiDevDatas', 'status', 'allStatus', 'devid', 'siteDevelopmentStatuses', 'uicheck_id', 'site_development_categories', 'allUsers', 'store_websites', 'allUicheckTypes', 'show_inactive', 'search_website'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msg' => $e->getMessage()]);
        }
    }

    public function responseDeviceUserChange(Request $request): JsonResponse
    {
        try {
            $uiDevDatas = UiDevice::where('id', $request->id)
                ->where('device_no', $request->device_no)
                ->where('uicheck_id', $request->uicheck_id)
                ->first();

            if ($uiDevDatas) {
                // check new user already assigned another device.
                $newUserAllDevices = UiDevice::where('user_id', $request->new_user_accessable_user_id)
                    ->where('uicheck_id', $request->uicheck_id)->count();

                if ($newUserAllDevices == 0) {
                    $userAllDevices = UiDevice::where('user_id', $uiDevDatas->user_id)
                        ->where('uicheck_id', $request->uicheck_id)->get();

                    foreach ($userAllDevices as $userAllDevice) {
                        $old_user_id = $userAllDevice->user_id;
                        $userAllDevice->update(['user_id' => $request->new_user_accessable_user_id]);

                        UiDeviceUserHistory::create([
                            'ui_device_id' => $userAllDevice->id,
                            'uicheck_id' => $request->uicheck_id,
                            'user_id' => Auth::user()->id,
                            'new_user_id' => $request->new_user_accessable_user_id,
                            'old_user_id' => $old_user_id,
                        ]);
                    }

                    UicheckUserAccess::where('uicheck_id', $request->uicheck_id)
                        ->where('user_id', $uiDevDatas->user_id)
                        ->update(['user_id' => $request->new_user_accessable_user_id]);

                    return response()->json(['code' => 200, 'message' => 'User updated succesfully']);
                } else {
                    return response()->json(['code' => 500, 'message' => 'This user already assigned, Please choose different user']);
                }
            } else {
                return response()->json(['code' => 500, 'message' => 'Device not found']);
            }
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function responseDeviceStatusChange(Request $request): JsonResponse
    {
        try {
            $old_status = null;
            $uiDevDatas = UiDevice::where('id', $request->id)
                ->where('device_no', $request->device_no)
                ->where('uicheck_id', $request->uicheck_id)->first();
            if ($uiDevDatas) {
                if ($request->update_status_all_device == 'true') {
                    $userAllDevices = UiDevice::where('user_id', $uiDevDatas->user_id)
                        ->where('uicheck_id', $request->uicheck_id)->get();

                    foreach ($userAllDevices as $userAllDevice) {
                        $old_status = $userAllDevice->status;
                        $userAllDevice->update(['status' => $request->status]);

                        $dataArray = [
                            'id' => $userAllDevice->id,
                            'uicheck_id' => $userAllDevice->uicheck_id,
                            'device_no' => $userAllDevice->device_no,
                            'old_status' => $old_status,
                            'status' => $request->status,
                        ];

                        $collection = collect($dataArray);
                        // Convert the collection to an object
                        $object = json_decode(json_encode($collection));

                        $this->uicheckResponsiveUpdateHistory($object, $old_status);
                    }
                } else {
                    $old_status = $uiDevDatas->status;
                    $uiDevDatas->update(['status' => $request->status]);
                    $this->uicheckResponsiveUpdateHistory($request, $old_status);
                }
            } else {
                UiDevice::create([
                    'user_id' => Auth::user()->id,
                    'device_no' => $request->device_no,
                    'uicheck_id' => $request->uicheck_id,
                    'languages_id' => $request->language_id,
                    'status' => $request->status,
                ]);
                $this->uicheckResponsiveUpdateHistory($request, $old_status);
            }

            $status = SiteDevelopmentStatus::find($request->status);

            return response()->json(['code' => 200, 'message' => 'Status updated succesfully', 'data' => $status?->color]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function responseDeviceIsApprovedChange(Request $request): JsonResponse
    {
        try {
            $uiDevDatas = UiDevice::where('device_no', $request->device_no)
                ->where('uicheck_id', $request->uicheck_id)
                ->first();

            if ($uiDevDatas) {
                if ($uiDevDatas->is_approved == 1) {
                    $uiDevDatas->is_approved = 0;
                    $uiDevDatas->save();
                } else {
                    $uiDevDatas->is_approved = 1;
                    $uiDevDatas->save();
                }

                return response()->json(['code' => 200, 'status' => true, 'message' => 'Status updated']);
            } else {
                throw new Exception('Record not found');
            }
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function uicheckResponsiveUpdateHistory($data, $old_status = 3)
    {
        try {
            $createdHistory = UiResponsivestatusHistory::create(
                [
                    'user_id' => Auth::user()->id ?? '',
                    'ui_device_id' => $data->id ?? '',
                    'uicheck_id' => $data->uicheck_id ?? '',
                    'device_no' => $data->device_no ?? '',
                    'status' => $data->status ?? '',
                    'old_status' => $old_status ?? '',
                ]
            );
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function responseDeviceStatusHistory(Request $request): JsonResponse
    {
        try {
            $createdHistory = UiResponsivestatusHistory::leftJoin('site_development_statuses as sds', 'sds.id', 'ui_responsivestatus_histories.status')
                ->leftJoin('site_development_statuses as sds1', 'sds1.id', 'ui_responsivestatus_histories.old_status')
                ->leftJoin('users as u', 'u.id', 'ui_responsivestatus_histories.user_id')
                ->where('ui_device_id', '=', $request->id)
                ->where('device_no', '=', $request->device_no)
                ->select('ui_responsivestatus_histories.*', 'u.name as username', 'sds.name as statusname', 'sds1.name as oldstatusname')->orderByDesc('ui_responsivestatus_histories.id')
                ->get();

            return response()->json(['code' => 200, 'message' => 'Listed succesfully', 'data' => $createdHistory]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function responseTranslatorPage(Request $request)
    {
        try {
            $uiLanguages = new UiLanguage;
            $uiLanguages = $uiLanguages->join('uichecks as uic', 'uic.id', 'ui_languages.uicheck_id')
                ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
                ->leftJoin('uicheck_user_accesses as uua', 'ui_languages.uicheck_id', 'uua.uicheck_id')
                ->leftJoin('users as u', 'u.id', 'uua.user_id')
                ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id')
                ->leftJoin('site_development_statuses as sds', 'sds.id', 'ui_languages.status');

            if ($request->status != '') {
                $uiLanguages = $uiLanguages->where('ui_languages.status', $request->status);
            }
            if ($request->categories != '') {
                $uiLanguages = $uiLanguages->where('uic.site_development_category_id', $request->categories)->where('ui_languages.languages_id', '2');
            }
            if ($request->id != '') {
                $uiLanguages = $uiLanguages->where('ui_languages.uicheck_id', $request->id);
            }

            if ($request->user_name != null && $request->user_name != 'undefined') {
                $uiLanguages = $uiLanguages->whereIn('u.id', $request->user_name);
            }

            if (! Auth::user()->hasRole('Admin')) {
                $uiLanguages = $uiLanguages->where(['uua.user_id' => Auth::user()->id]);
            }

            $uiLanguages = $uiLanguages->select('ui_languages.*', 'u.name as username', 'sw.website', 'sdc.title', 'sds.name as statusname')
                ->groupBy('ui_languages.uicheck_id')
                ->orderByDesc('id')
                ->paginate(8);
            $allStatus = SiteDevelopmentStatus::pluck('name', 'id')->toArray();
            $status = '';
            $lanid = '';
            $languages = Language::all();
            $allUsers = User::where('is_active', '1')->get();
            $site_development_categories = SiteDevelopmentCategory::pluck('title', 'id')->toArray();

            return view('uicheck.language', compact('uiLanguages', 'status', 'languages', 'allStatus', 'lanid', 'site_development_categories', 'allUsers'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['msg' => $e]);
        }
    }

    public function translatorStatusChange(Request $request): JsonResponse
    {
        try {
            $uiDevDatas = UiLanguage::where('uicheck_id', $request->uicheck_id)
                ->where('languages_id', $request->language_id)
                ->update(['status' => $request->status]);
            if ($uiDevDatas == 0) {
                UiLanguage::create([
                    'user_id' => Auth::user()->id,
                    'uicheck_id' => $request->uicheck_id,
                    'languages_id' => $request->language_id,
                    'status' => $request->status,
                ]);
            }
            $this->uicheckTranslatorUpdateHistory($request);

            return response()->json(['code' => 200, 'message' => 'Status updated succesfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function uicheckTranslatorUpdateHistory($data)
    {
        try {
            $data['user_id'] = Auth::user()->id ?? '';

            $createdHistory = UiTranslatorStatusHistory::create(
                [
                    'user_id' => Auth::user()->id ?? '',
                    'ui_language_id' => $data->id ?? '',
                    'language_id' => $data->language_id ?? '',
                    'uicheck_id' => $data->uicheck_id ?? '',
                    'status' => $data->status ?? '',
                    'old_status' => $data->old_status ?? '',
                ]);
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function translatorStatusHistory(Request $request): JsonResponse
    {
        try {
            $createdHistory = UiTranslatorStatusHistory::leftJoin('site_development_statuses as sds', 'sds.id', 'ui_translator_status_histories.status')
                ->leftJoin('site_development_statuses as sds1', 'sds1.id', 'ui_translator_status_histories.old_status')
                ->leftJoin('users as u', 'u.id', 'ui_translator_status_histories.user_id')
                ->where('ui_language_id', '=', $request->id)
                ->where('language_id', '=', $request->language_id)
                ->where('uicheck_id', '=', $request->uicheck_id)
                ->select('ui_translator_status_histories.*', 'u.name as username', 'sds.name as statusname', 'sds1.name as oldstatusname')->orderByDesc('ui_translator_status_histories.id')
                ->get();

            return response()->json(['code' => 200, 'message' => 'Listed succesfully', 'data' => $createdHistory]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function uicheckDevUpdateHistory($data)
    {
        try {
            $createdHistory = UiDeviceHistory::create(
                $data
            );
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDevDocuments(Request $request): JsonResponse
    {
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            $uiDevData = UiDevice::where('uicheck_id', '=', $request->uicheck_id)->where('device_no', '=', $request->device_no)->first();

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('uicheckAttach/dev/'.floor($request->id / config('constants.image_per_folder')))
                    ->upload();
                $attachment = UicheckDeviceAttachment::create([
                    'device_no' => $request->device_no ?? '',
                    'uicheck_id' => $request->uicheck_id,
                    'ui_devices_id' => $uiDevData->id ?? '',
                    'user_id' => Auth::user()->id,
                    'attachment' => $media,
                ]);
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function devListDocuments(Request $request): JsonResponse
    {
        $uicheckAttch = UicheckDeviceAttachment::leftJoin('users', 'users.id', 'uicheck_device_attachments.user_id')
            ->select('uicheck_device_attachments.*', 'users.name as userName')
            ->where('device_no', $request->device_no)->where('device_no', $request->device_no)
            ->where('uicheck_id', $request->ui_check_id)
            ->get();

        $userList = [];

        $records = [];
        if ($uicheckAttch) {
            foreach ($uicheckAttch as $media) {
                // Convert JSON string to Object
                $imagepath = json_decode($media->attachment);
                $records[] = [
                    'id' => $media->id,
                    'url' => 'uploads/'.$imagepath->directory.'/'.$imagepath->filename.'.'.$imagepath->extension,
                    'userName' => $media->userName,
                    'ui_attach_id' => $media->id,
                ];
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function deleteDevDocument(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $uicheckAttch = UicheckDeviceAttachment::where('id', $request->id)->delete();

            return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
        }

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function getuicheckDevUpdateHistory(Request $request)
    {
        try {
            $getHistory = UiDeviceHistory::leftJoin('users', 'users.id', 'ui_device_histories.user_id')
                ->leftJoin('site_development_statuses AS sds', 'sds.id', 'ui_device_histories.status')
                ->select('ui_device_histories.*', 'users.name As userName', 'sds.name AS status_name')
                ->where('ui_device_histories.device_no', $request->device_no)
                ->where('ui_device_histories.user_id', $request->user_access_user_id)
                ->where('ui_device_histories.uicheck_id', $request->uicheck_id)
                ->orderByDesc('id')->get();
            $siteDevelopmentStatuses = SiteDevelopmentStatus::pluck('name', 'id')->toArray();

            $html = view('uicheck.ui-check-dev-update-history', compact('getHistory', 'siteDevelopmentStatuses'))->render();

            return respJson(200, '', [
                'html' => $html,
            ]);
        } catch (Exception $e) {
            return respException($e);
        }
    }

    public function languageFlag(Request $request): JsonResponse
    {
        try {
            $data = Uicheck::where('id', $request->id);
            $retunData = $data->get();

            if ($retunData[0]->language_flag == 1) {
                $array['language_flag'] = 0;
            } else {
                $array['language_flag'] = 1;
                $langs = Language::get();

                foreach ($langs as $lang) {
                    $uiDevDatas = UiLanguage::where(['uicheck_id' => $request->id, 'languages_id' => $lang->id])->first();
                    if (! $uiDevDatas) {
                        UiLanguage::create([
                            'user_id' => Auth::user()->id,
                            'uicheck_id' => $request->id,
                            'languages_id' => $lang->id,
                        ]);
                    }
                }
            }
            $data->update($array);
            $retunData1 = Uicheck::where('id', $request->id)->get();

            return response()->json(['code' => 200, 'data' => $retunData1, 'message' => 'Type Updated!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function translationFlag(Request $request): JsonResponse
    {
        try {
            $data = Uicheck::where('id', $request->id);
            $retunData = $data->get();

            if ($retunData[0]->translation_flag == 1) {
                $array['translation_flag'] = 0;
            } else {
                $array['translation_flag'] = 1;

                for ($i = 1; $i <= 5; $i++) {
                    $uiDevDatas = UiDevice::where(['uicheck_id' => $request->id, 'device_no' => $i])->first();
                    if (! $uiDevDatas) {
                        UiDevice::create([
                            'user_id' => Auth::user()->id,
                            'device_no' => $i,
                            'uicheck_id' => $request->id,
                        ]);
                    }
                }
            }
            $data->update($array);
            $retunData1 = Uicheck::where('id', $request->id)->get();

            return response()->json(['code' => 200, 'data' => $retunData1, 'message' => 'Type Updated!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        $data = $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $siteDevelopmentstatus = SiteDevelopmentStatus::find($key);
            $siteDevelopmentstatus->color = $value;
            $siteDevelopmentstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function updateDeviceStatus(Request $request)
    {
        try {
            $id = $request->id;
            $statusId = $request->status_id;
            $udh = UiDeviceHistory::find($id);
            if ($udh) {
                $udh->status = $statusId == '-' ? null : $statusId;
                $udh->save();

                // Update status also in ui_devices table
                $udh->uiDevice->status = $statusId == '-' ? null : $statusId;
                $udh->uiDevice->save();

                if ($udh->save()) {
                    $status = SiteDevelopmentStatus::find($statusId);

                    return respJson(200, '', [
                        'message' => 'Status updated successfully',
                        'data' => $status?->color,
                    ]);
                } else {
                    return respJson(500, '', [
                        'message' => 'Something went wrong',
                    ]);
                }
            }
        } catch (Exception $e) {
            return respJson(500, '', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Assign a new user to website and category
     */
    public function assignNewUser(Request $request): JsonResponse
    {
        try {
            //master category id for design
            $siteDevelopmentMasterCategory = SiteDevelopmentMasterCategory::select('id')->where('title', 'Design')->first();
            $siteDevelopmentDesignMasterCategoryId = $siteDevelopmentMasterCategory->id;

            $siteDevelopmentCategoryIds = SiteDevelopmentCategory::join('site_development_master_categories as sdmc', 'sdmc.id', 'site_development_categories.master_category_id')
                ->where('sdmc.title', 'Design')->select('site_development_categories.id')->pluck('id');

            if ($siteDevelopmentCategoryIds->count() > 0) {
                $siteDevelopmentCategoryIds = $siteDevelopmentCategoryIds->toArray();
            } else {
                $siteDevelopmentCategoryIds = [];

                return response()->json(['status' => false, 'message' => 'Category not found.']);
            }

            $userIds = $request->user;
            $websiteIds = $request->website;
            $uicheckTypeIds = $request->type;

            $noDataFoundMessage = [];

            foreach ($userIds as $userId) {
                foreach ($websiteIds as $websiteId) {
                    $all_site_development = $this->processSiteDevelopmentCategory($userId, $websiteId, $siteDevelopmentCategoryIds, $siteDevelopmentDesignMasterCategoryId);
                    if (isset($all_site_development) && ! empty($all_site_development)) {
                        foreach ($all_site_development as $site_development_id => $site_development_category_id) {
                            foreach ($uicheckTypeIds as $uicheckTypeId) {
                                $uicheck = Uicheck::where([
                                    'website_id' => $websiteId,
                                    'site_development_id' => $site_development_id,
                                    'site_development_category_id' => $site_development_category_id,
                                    'uicheck_type_id' => $uicheckTypeId,
                                ])->get();

                                if ($uicheck->count() == 0) {
                                    $this->addNewUirecords($websiteId, $site_development_id, $site_development_category_id, $uicheckTypeId, $userId);
                                } else {
                                    $uicheck = $uicheck->first();
                                    if ($uicheck->uiDeviceCount($userId) == 0) {
                                        UiDevice::create([
                                            'user_id' => $userId ?? 0,
                                            'device_no' => '1',
                                            'uicheck_id' => $uicheck->id,
                                            'message' => '',
                                        ]);
                                    }

                                    UicheckUserAccess::provideAccess($uicheck->id, $userId);
                                }
                            }
                        }
                    } else {
                        $noDataFoundMessage[] = "No data found for User {$userId} and Website {$websiteId}";
                    }
                }
            }

            if ($noDataFoundMessage) {
                return response()->json(['status' => false, 'message' => implode(', ', $noDataFoundMessage)]);
            } else {
                return response()->json(['status' => true, 'message' => 'User has assigned successfully.']);
            }
        } catch (Exception $e) {
            Log::info($e);

            return response()->json(['status' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Create UI Test and UI Desing type records for given site_development_category_id
     *
     * @param  mixed  $userId
     * @param  mixed  $websiteId
     * @param  mixed  $siteDevelopmentCategoryIds
     * @param  mixed  $siteDevelopmentDesignMasterCategoryId
     */
    public function processSiteDevelopmentCategory($userId, $websiteId, $siteDevelopmentCategoryIds, $siteDevelopmentDesignMasterCategoryId)
    {
        try {
            $siteDevelopmentCategoryIds = array_unique($siteDevelopmentCategoryIds);
            $inserted_record = SiteDevelopment::where('website_id', $websiteId)->whereIn('site_development_category_id', $siteDevelopmentCategoryIds)->distinct()->select('site_development_category_id')->pluck('site_development_category_id');

            if (isset($inserted_record) && $inserted_record->count() > 0) {
                $inserted_record = $inserted_record->toArray();
            } else {
                $inserted_record = [];
            }

            $not_inserted_category_ids = array_diff($siteDevelopmentCategoryIds, $inserted_record);

            $insertData = [];
            if (isset($not_inserted_category_ids) && count($not_inserted_category_ids) > 0) {
                foreach ($not_inserted_category_ids as $key => $ids) {
                    $insertData[] = [
                        'site_development_category_id' => $ids,
                        'website_id' => $websiteId,
                        'site_development_master_category_id' => $siteDevelopmentDesignMasterCategoryId,
                    ];
                }
            }

            if (count($insertData) > 0) {
                SiteDevelopment::insert($insertData);
            }

            $all_site_development = SiteDevelopment::where('website_id', $websiteId)->whereIn('site_development_category_id', $siteDevelopmentCategoryIds)->distinct()->select()->pluck('site_development_category_id', 'id');

            if ($all_site_development->count() > 0) {
                return $all_site_development->toArray();
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Add new ui record
     *
     * @param  mixed  $websiteId
     * @param  mixed  $site_development_id
     * @param  mixed  $site_development_category_id
     * @param  mixed  $ui_type
     * @param  mixed  $userId
     */
    public function addNewUirecords($websiteId, $site_development_id, $site_development_category_id, $ui_type, $userId)
    {
        try {
            $uicheck = Uicheck::create([
                'website_id' => $websiteId,
                'site_development_id' => $site_development_id,
                'site_development_category_id' => $site_development_category_id,
                'created_at' => now(),
                'uicheck_type_id' => $ui_type,
                'lock_developer' => 1, // By default we have to lock for developer - New requirement.
            ]);

            $uidevice = UiDevice::create([
                'user_id' => $userId ?? 0,
                'device_no' => '1',
                'uicheck_id' => $uicheck->id,
                'message' => '',
            ]);

            UicheckUserAccess::create([
                'user_id' => $userId ?? 0,
                'uicheck_id' => $uicheck->id,
                'lock_developer' => 1, // By default we have to lock for developer - New requirement.
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Opps! Something went wrong, Please try again.']);

        }
    }

    public function userHistory(Request $request): JsonResponse
    {
        try {
            $userAccess = UiDeviceUserHistory::with('user')
                ->where('uicheck_id', $request->uicheck_id)
                ->where('ui_device_id', $request->ui_device_id)
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'status' => true,
                'data' => view('uicheck.user-history', compact('userAccess'))->render(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => true,
                'data' => view('uicheck.user-history')->render(),
            ]);
        }
    }

    /**
     * This function will upload file on google drive
     */
    public function uploadFile(UploadFileUicheckRequest $request): RedirectResponse
    {

        $data = $request->all();
        try {
            $uiCheck = Uicheck::find($request->ui_check_id);

            $uiDevData = UiDevice::where('uicheck_id', '=', $request->ui_check_id)->where('device_no', '=', $request->device_no)->first();
            $uiDev['user_id'] = Auth::user()->id;
            $uiDev['device_no'] = $request->device_no;
            $uiDev['uicheck_id'] = $request->ui_check_id;
            $uiDev['message'] = 'New File uploaded';
            if ($request->uidevdatetime) {
                $uiDev['estimated_time'] = $request->uidevdatetime;
            }
            $uiDevid = $uiDevData->id ?? '';
            if ($uiDevid == '') {
                $uiDevs = UiDevice::create($uiDev);
                $uiData = UiDevice::where('id', $uiDevs->id)->first();
            } else {
                $uiData = $uiDevData;
                $uiLans = UiDevice::where('id', $uiDevData->id)->update($uiDev);
            }

            $uiDev['ui_devices_id'] = $uiData->id;
            $deviceHistory = UiDeviceHistory::create($uiDev);

            foreach ($data['file'] as $file) {
                DB::transaction(function () use ($file, $data, $uiData, $deviceHistory) {
                    $googleScreencast = new GoogleScreencast;
                    $googleScreencast->file_name = $file->getClientOriginalName();
                    $googleScreencast->extension = $file->extension();
                    $googleScreencast->user_id = Auth::id();

                    $googleScreencast->read = '';
                    $googleScreencast->write = '';

                    $googleScreencast->remarks = $data['remarks'];
                    $googleScreencast->file_creation_date = $data['file_creation_date'];
                    $googleScreencast->belongable_id = $uiData->id; //Ui device Id
                    $googleScreencast->belongable_type = UiDevice::class;
                    $googleScreencast->save();

                    UploadGoogleDriveScreencast::dispatchSync(
                        $googleScreencast, $file, 'anyone',
                        [
                            UiDevice::class => $uiData->id,
                            UiDeviceHistory::class => $deviceHistory->id,
                        ]
                    );
                });
            }

            return redirect()->back()->with('success', 'File is Uploaded to Google Drive.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong. Please try again');
        }
    }

    /**
     * This function will return a list of files which are uploaded under uicheck class
     */
    public function getUploadedFilesList(Request $request): JsonResponse
    {
        try {
            $class = null;
            if (isset($request->device_no)) {
                $class = UiDevice::class;
            }

            $device = UiDevice::where('uicheck_id', $request->ui_check_id)->where('device_no', $request->device_no)->first();

            if (isset($device)) {
                $result = [];
                if (isset($request->ui_check_id)) {
                    $result = GoogleScreencast::where('belongable_type', $class)->where('belongable_id', $device->id)->orderByDesc('id')->get();
                    if (isset($result) && count($result) > 0) {
                        $result = $result->toArray();
                    }

                    return response()->json([
                        'data' => view('uicheck.google-drive-list', compact('result'))->render(),
                    ]);
                }
            } else {
                throw new Exception('Device not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => view('uicheck.google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    /**
     * Assign a new user to website
     */
    public function addNewUser(Request $request): JsonResponse
    {
        try {
            $oldUserId = $request->oldUserId;
            $newUserId = $request->newUserId;
            $websiteId = $request->websiteId;

            $uiDevDatas = new UiDevice;
            $uiDevDatas = $uiDevDatas->with('uichecks.uiDevice.lastUpdatedStatusHistory.stausColor')->join('uichecks as uic', 'uic.id', 'ui_devices.uicheck_id')
                ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
                ->leftJoin('uicheck_user_accesses as uua', 'ui_devices.uicheck_id', 'uua.uicheck_id')
                ->leftJoin('users as u', 'u.id', 'uua.user_id')
                ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id')
                ->leftJoin('site_development_statuses as sds', 'sds.id', 'ui_devices.status')
                ->leftJoin('ui_device_histories as udh', 'ui_devices.id', 'udh.status');

            $uiDevDatas = $uiDevDatas->where('uic.website_id', $websiteId);
            $uiDevDatas = $uiDevDatas->whereIn('u.id', [$oldUserId]);

            $uiDevDatas = $uiDevDatas->select('ui_devices.*', 'uic.uicheck_type_id', 'u.name as username', 'sw.website', 'sdc.title', 'sds.name as statusname',
                DB::raw('(select message from ui_device_histories where uicheck_id  =   ui_devices.id  order by id DESC limit 1) as messageDetail'), DB::raw('GROUP_CONCAT(DISTINCT u.name order by uua.id desc) as user_accessable')
            )->orderByDesc('id')->groupBy('ui_devices.uicheck_id')->get();

            foreach ($uiDevDatas as $uiRow) {
                $user = UicheckUserAccess::firstOrNew(
                    ['user_id' => $newUserId, 'uicheck_id' => $uiRow->uicheck_id],
                    ['user_id' => $newUserId, 'uicheck_id' => $uiRow->uicheck_id]
                );

                $user->save();
            }

            return response()->json(['status' => true, 'message' => 'User has assigned successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        Uicheck::whereIn('id', $request->uiCheckIds)->delete();

        return response()->json(['status' => true, 'message' => 'Ui checks deleted successfully']);
    }

    public function bulkDeleteUserWise(Request $request): JsonResponse
    {
        $uicheckIds = UicheckUserAccess::where('user_id', $request->userId)
            ->join('uichecks as uic', 'uic.id', 'uicheck_user_accesses.uicheck_id')
            ->where('uic.website_id', $request->uicheckWebsite)
            ->where('uic.uicheck_type_id', $request->uicheckType)
            ->pluck('uicheck_id')
            ->toArray();

        Uicheck::whereIn('id', $uicheckIds)->delete();

        return response()->json(['status' => true, 'message' => 'Ui checks deleted successfully']);
    }

    public function bulkDeleteUserWiseMultiple(Request $request): JsonResponse
    {
        if ($request->data) {
            $datas = json_decode(stripslashes($request->data), true);
            foreach ($datas as $data) {
                $uicheckIds = UicheckUserAccess::where('user_id', $data['user_id'])
                    ->join('uichecks as uic', 'uic.id', 'uicheck_user_accesses.uicheck_id')
                    ->where('uic.website_id', $data['uicheck_website'])
                    ->where('uic.uicheck_type_id', $data['uicheck_type'])
                    ->pluck('uicheck_id')
                    ->toArray();

                Uicheck::whereIn('id', $uicheckIds)->delete();
            }
        }

        return response()->json(['status' => true, 'message' => 'Records deleted successfully']);
    }

    public function userAccessList(Request $request): JsonResponse
    {
        try {
            $perPage = 20;
            $uicheckUserAccess = new UicheckUserAccess;

            $uicheckUserAccess = $uicheckUserAccess->with('user')
                ->leftJoin('users', 'users.id', 'uicheck_user_accesses.user_id')
                ->leftJoin('uichecks', 'uichecks.id', 'uicheck_user_accesses.uicheck_id')
                ->leftJoin('store_websites', 'store_websites.id', 'uichecks.website_id')
                ->leftJoin('uicheck_types', 'uicheck_types.id', 'uichecks.uicheck_type_id')
                ->whereNull('uichecks.deleted_at')
                ->whereNotNull('uicheck_user_accesses.user_id')
                ->whereNotNull('uicheck_user_accesses.uicheck_id')
                ->select('uicheck_user_accesses.*', 'uichecks.uicheck_type_id', 'uichecks.website_id', 'users.name as username', 'store_websites.title as website', 'uicheck_types.name as type', DB::raw('count(*) as total'))
                ->groupBy('uicheck_user_accesses.user_id', 'uichecks.website_id', 'uichecks.uicheck_type_id')
                ->orderByDesc('uicheck_user_accesses.updated_at');

            $keyword = $request->get('keyword');
            if ($keyword != '') {
                $uicheckUserAccess = $uicheckUserAccess->where(function ($q) use ($keyword) {
                    $q->orWhere('store_websites.title', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('uicheck_types.name', 'LIKE', '%'.$keyword.'%')
                        ->orWhere('users.name', 'LIKE', '%'.$keyword.'%');
                });
            }

            $uicheckUserAccess = $uicheckUserAccess->paginate($perPage);

            return response()->json(['code' => 200, 'data' => $uicheckUserAccess, 'count' => count($uicheckUserAccess), 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function deviceHistoryIstimeApprove(Request $request): JsonResponse
    {
        $uiDeviceHistory = UiDeviceHistory::find($request->id);
        if ($request->isEstimatedTimeApproved != '' && $uiDeviceHistory) {
            $uiDeviceHistory->update(['is_estimated_time_approved' => $request->isEstimatedTimeApproved]);

            return response()->json(['messages' => 'Successfull', 'code' => 200]);
        }

        return response()->json(['messages' => 'Not changed', 'code' => 500]);
    }

    public function fetchDeviceBuilderData(Request $request): JsonResponse
    {
        try {
            $uiDevice = UiDevice::where('uicheck_id', '=', $request->uicheckId)->where('device_no', '=', $request->deviceNo)->where('user_id', '=', $request->user_access_user_id)->first();
            if (! $uiDevice) {
                return response()->json(['message' => 'Device not found'], 400);
            }

            $uiCheckStoreWebsiteWithbuilderAPIKey = Uicheck::where('id', $request->uicheckId)->whereHas('storeWebsite', function ($store_website) {
                $store_website->whereNotNull('builder_io_api_key')->orWhere('builder_io_api_key', '<>', '');
            })->first();

            if (! $uiCheckStoreWebsiteWithbuilderAPIKey) {
                return response()->json(['message' => 'API key not found for this website'], 400);
            }

            $apiKey = $uiCheckStoreWebsiteWithbuilderAPIKey->storeWebsite->builder_io_api_key;

            $baseUrl = 'https://cdn.builder.io/api/v1/html/page';
            $url = $uiCheckStoreWebsiteWithbuilderAPIKey->siteDevelopmentCategory->title;
            $device = 'device '.$uiDevice->device_no;

            $response = Http::get("$baseUrl?apiKey=$apiKey&url=$url&device=$device");

            if ($response->successful()) {
                $responseData = json_decode($response->body(), true);

                // Check if a record with the same lastUpdated value exists
                $existingRecord = UiDeviceBuilderIoData::where([
                    'uicheck_id' => $uiDevice->uicheck_id,
                    'ui_device_id' => $uiDevice->id,
                    'title' => $responseData['data']['title'],
                    'builder_last_updated' => $responseData['lastUpdated'],
                ])->first();

                if (! $existingRecord) {
                    // Check "Fetched" Status exists. Otherwise create & use.
                    $fetchedStatus = UiDeviceBuilderIoDataStatus::firstOrCreate(['name' => 'Fetched']);

                    $builderIoData = UiDeviceBuilderIoData::create([
                        'uicheck_id' => $uiDevice->uicheck_id,
                        'ui_device_id' => $uiDevice->id,
                        'title' => $responseData['data']['title'],
                        'html' => $responseData['data']['html'],
                        'builder_created_date' => $responseData['createdDate'],
                        'builder_last_updated' => $responseData['lastUpdated'],
                        'builder_created_by' => $responseData['createdBy'],
                        'builder_last_updated_by' => $responseData['lastUpdatedBy'],
                        'status_id' => $fetchedStatus->id,
                    ]);

                    UiDeviceBuilderIoDataStatusHistory::create([
                        'ui_device_builder_io_data_id' => $builderIoData->id,
                        'user_id' => Auth::id(),
                        'old_status_id' => null,
                        'new_status_id' => $fetchedStatus->id,
                    ]);

                    return response()->json(['message' => 'Data saved successfully']);
                } else {
                    return response()->json(['message' => 'Data fetched already up to date, No new entry.'], 400);
                }
            } else {
                return response()->json(['message' => 'Error fetching data from Builder.io'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }

    public function deviceBuilderDatas(Request $request): View
    {
        $builderDatas = UiDeviceBuilderIoData::join('ui_devices as uid', 'uid.id', 'ui_device_builder_io_datas.ui_device_id')
            ->join('uichecks as uic', 'uic.id', 'ui_device_builder_io_datas.uicheck_id')
            ->leftJoin('uicheck_user_accesses as uua', function ($join) {
                $join->on('uid.uicheck_id', '=', 'uua.uicheck_id')
                    ->on('uid.user_id', '=', 'uua.user_id');
            })
            ->leftJoin('users as u', 'u.id', 'uua.user_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'uic.website_id')
            ->leftjoin('site_development_categories as sdc', 'uic.site_development_category_id', '=', 'sdc.id')
            ->leftJoin('site_development_statuses as sds', 'sds.id', 'uid.status')
            ->leftJoin('ui_device_builder_io_data_statuses as bs', 'bs.id', 'ui_device_builder_io_datas.status_id');

        $webIds = request()->input('web_ids');
        if (is_array($webIds) && count($webIds) > 0) {
            $builderDatas->whereIn('sw.id', $webIds);
        }

        $catIds = request()->input('cat_name');
        if (is_array($catIds) && count($catIds) > 0) {
            $builderDatas->whereIn('sdc.id', $catIds);
        }

        $statusIds = request()->input('status');
        if (is_array($statusIds) && count($statusIds) > 0) {
            $builderDatas->whereIn('bs.id', $statusIds);
        }

        $builderDatas = $builderDatas->select(
            'ui_device_builder_io_datas.*',
            'uid.device_no',
            'sw.website',
            'sdc.title as category',
            'u.name',
            'uic.uicheck_type_id',
        );

        $builderDatas = $builderDatas->orderByDesc('ui_device_builder_io_datas.created_at')
            ->paginate(10);

        $allUicheckTypes = UicheckType::get()->pluck('name', 'id')->toArray();

        $getbuildStatuses = UiDeviceBuilderIoDataStatus::all();
        $siteDevelopmentCategories = SiteDevelopmentCategory::get()->pluck('title', 'id')->toArray();
        $storeWebsites = StoreWebsite::select('id', 'website')->orderByDesc('id')->groupBy('website')->get();

        return view('uicheck.device-builder-datas-index', compact('builderDatas', 'allUicheckTypes', 'getbuildStatuses', 'siteDevelopmentCategories', 'storeWebsites'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function getDeviceBuilderDatas(Request $request)
    {
        $uiDevice = UiDevice::where('uicheck_id', '=', $request->uicheckId)->where('device_no', '=', $request->deviceNo)->where('user_id', '=', $request->user_access_user_id)->first();
        if (! $uiDevice) {
            return response()->json(['message' => 'Device not found'], 400);
        }

        $history = UiDeviceBuilderIoData::where('uicheck_id', $uiDevice->uicheck_id)->where('ui_device_id', $uiDevice->id)->get();

        return view('uicheck.device-builder-datas', compact('history'));
    }

    public function getBuilderHtml($id): View
    {
        $data = UiDeviceBuilderIoData::findOrFail($id);

        return view('uicheck.device-builder-html', compact('data'));
    }

    public function getBuilderDownloadHtml($id): Response
    {
        $data = UiDeviceBuilderIoData::findOrFail($id);

        // Log the download
        $log = new UiDeviceBuilderIoDataDownloadHistory;
        $log->user_id = auth()->id(); // Assuming you have authentication in place
        $log->ui_device_builder_io_data_id = $data->id;
        $log->downloaded_at = now();
        $log->save();

        $filename = $data->title.'.html';

        return response($data->html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function getBuilderDownloadHistory($dataId): View
    {
        $downloadHistory = UiDeviceBuilderIoDataDownloadHistory::where('ui_device_builder_io_data_id', $dataId)
            ->get();

        return view('uicheck.device-builder-download-history', compact('downloadHistory'));
    }

    public function deviceBuilderStatusStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:ui_device_builder_io_data_statuses,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'status_name' => 'error',
            ], 422);
        }

        $input = $request->except(['_token']);

        $data = UiDeviceBuilderIoDataStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Status Created Successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something Error Occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function storeBuilderDataRemark(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);
        if ($request->remarks == '') {
            return response()->json([
                'status' => false,
                'message' => 'Please enter remarks',
                'status_name' => 'error',
            ], 500);
        }

        $input['user_id'] = Auth::user()->id;

        $remarkHistory = UiDeviceBuilderIoDataRemarkHistory::create($input);

        if ($remarkHistory) {
            UiDeviceBuilderIoData::where('id', $request->ui_device_builder_io_data_id)->update(['remarks' => $request->remarks]);

            return response()->json([
                'status' => true,
                'message' => 'Remark added successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark added unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function deviceBuilderStatusColorUpdate(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = UiDeviceBuilderIoDataStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function getBuilderDataRemarks($id): JsonResponse
    {
        $remarks = UiDeviceBuilderIoDataRemarkHistory::with(['user'])
            ->where('ui_device_builder_io_data_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateDeviceUpdateStatus(Request $request): JsonResponse
    {
        $uiBuild = UiDeviceBuilderIoData::find($request->buildId);
        $oldStatusId = $uiBuild->status_id;
        $uiBuild->status_id = $request->statusId;
        $uiBuild->save();

        UiDeviceBuilderIoDataStatusHistory::create([
            'ui_device_builder_io_data_id' => $request->buildId,
            'user_id' => Auth::id(),
            'old_status_id' => $oldStatusId,
            'new_status_id' => $request->statusId,
        ]);

        $statusColour = UiDeviceBuilderIoDataStatus::find($request->statusId);
        $statusColour = $statusColour->color;

        return response()->json([
            'status' => true,
            'message' => 'Status Update successfully',
            'status_name' => 'success',
            'colourCode' => $statusColour,
        ], 200);
    }

    public function getBuilderDataStatus($id): JsonResponse
    {
        $status = UiDeviceBuilderIoDataStatusHistory::with(['user', 'newStatus', 'oldStatus'])
            ->where('ui_device_builder_io_data_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $status,
            'message' => 'Status get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function builderIOTaskstore(BuilderIOTaskstoreUicheckRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        $selectedRows = explode(',', $data['selected_rows']);
        if (! $selectedRows) {
            return response()->json(
                [
                    'code' => 404,
                    'data' => [],
                    'message' => 'Rows not selected',
                ]
            );
        }

        // Create task directly in tasks table.
        $task = Task::where('task_subject', $data['task_name'])->where('assign_to', $data['assign_to'])->first();
        if (! $task) {
            $data['assign_from'] = Auth::id();
            $data['is_statutory'] = 0;
            $data['task_details'] = $data['task_name'];
            $data['task_subject'] = $data['task_name'];

            $task = Task::create($data);

            if ($data['assign_to']) {
                $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
            }
        }

        // Assign Zabbix Task Id to selected zabbix webhook datas
        $uiDeviceBuilderIoDatas = UiDeviceBuilderIoData::whereIn('id', $selectedRows);
        $uiDeviceBuilderIoDatas->update(['task_id' => $task->id]);

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Builder IO task has been created!',
            ]
        );
    }
}
