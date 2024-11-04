<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\Github\GithubRepository;
use App\Jobs\PostmanRequestUrlRunJob;
use App\LogRequest;
use App\Models\DataTableColumn;
use App\Models\PostmanApiIssueFixDoneHistory;
use App\Models\PostmanStatus;
use App\Models\PostmanStatusHistory;
use App\PostmanCollection;
use App\PostmanCollectionFolder;
use App\PostmanEditHistory;
use App\PostmanError;
use App\PostmanFolder;
use App\PostmanHistory;
use App\PostmanMultipleUrl;
use App\PostmanRemarkHistory;
use App\PostmanRequestCreate;
use App\PostmanRequestHistory;
use App\PostmanRequestJsonHistory;
use App\PostmanResponse;
use App\PostmanWorkspace;
use App\Setting;
use App\StoreViewCodeServerMap;
use App\StoreWebsite;
use App\Task;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostmanRequestCreateController extends Controller
{
    public function PostmanErrorLog($id = '', $type = '', $error = '', $tabName = '', $auth_id_from_job = '')
    {
        try {
            PostmanError::create([
                'user_id' => ! empty($auth_id_from_job) ? $auth_id_from_job : Auth::user()->id,
                'parent_id' => $id,
                'parent_id_type' => $type,
                'parent_table' => $tabName,
                'error' => $error,
            ]);
        } catch (Exception $e) {
            PostmanError::create([
                'user_id' => ! empty($auth_id_from_job) ? $auth_id_from_job : Auth::user()->id,
                'parent_id' => $id,
                'parent_id_type' => $type,
                'parent_table' => $tabName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function createPostmanHistory($postmanId, $type): JsonResponse
    {
        try {
            $postHis = new PostmanHistory;
            $postHis->user_id = Auth::user()->id;
            $postHis->postman_id = $postmanId;
            $postHis->type = $type;
            $postHis->save();
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Create Postman History  Error => '.json_decode($e).' #id #'.$postmanId ?? '');
            $this->PostmanErrorLog($postmanId, 'Create Postman History  Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $q = PostmanRequestCreate::query();
            $q->select('postman_request_creates.*', 'pf.name', 'postman_responses.response', 'postman_responses.response_code', 'postman_responses.id AS resId');
            $q->leftJoin('postman_folders AS pf', 'pf.id', 'postman_request_creates.folder_name');
            $q->leftJoin('postman_responses', function ($query) {
                $query->on('postman_responses.request_id', '=', 'postman_request_creates.id')
                    ->whereRaw('postman_responses.id IN (select MAX(pr1.id) from postman_responses as pr1 WHERE pr1.request_id = postman_request_creates.id  ORDER BY id DESC )');
            });

            if ($s = request('folder_name')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('folder_name', 'like', '%'.$s[$i].'%');
                        }
                    }
                });
            }
            if ($s = request('request_type')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('request_type', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('request_name')) {
                $q->where(function ($query) use ($s) {
                    for ($i = 0; $i < count($s); $i++) {
                        if ($s[$i]) {
                            $query->orWhere('request_name', $s[$i]);
                        }
                    }
                });
            }
            if ($s = request('search_id')) {
                $q->where('postman_request_creates.id', $s);
            }
            if ($s = request('keyword')) {
                $q->where('postman_request_creates.request_url', 'LIKE', '%'.$s.'%')->orWhere('postman_request_creates.body_json', 'LIKE', '%'.$s.'%');
            }

            $q->orderByDesc('postman_request_creates.id');
            $counter = $q->count();
            $postmans = $q->paginate(Setting::get('pagination'));

            $folders = PostmanFolder::all();
            $status = PostmanStatus::all();
            $users = User::all();
            $userID = loginId();
            $addAdimnAccessID = isAdmin() ? loginId() : '';
            $listRequestNames = PostmanRequestCreate::dropdownRequestNames();

            $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'postman-listing')->first();

            $dynamicColumnsToShowPostman = [];
            if (! empty($datatableModel->column_name)) {
                $hideColumns = $datatableModel->column_name ?? '';
                $dynamicColumnsToShowPostman = json_decode($hideColumns, true);
            }

            $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();
            $postJsonVer = PostmanRequestJsonHistory::all();
            $githubRepository = GithubRepository::all();

            if (request()->ajax()) {
                return response()->json([
                    'tbody' => view('postman.partials.table.tbody', compact(
                        'postmans',
                        'userID',
                        'addAdimnAccessID',
                        'status',
                        'dynamicColumnsToShowPostman',
                    ))->render(),
                    'pagination' => view('postman.partials.table.pagination', compact('postmans'))->render(),
                ], 200);
            }

            return view('postman.index', compact(
                'postmans',
                'folders',
                'users',
                'userID',
                'addAdimnAccessID',
                'listRequestNames',
                'counter',
                'status',
                'dynamicColumnsToShowPostman',
                'allUsers',
                'postJsonVer',
                'githubRepository',
            ));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller index method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function search(Request $request)
    {
        return $this->index();
    }

    public function folderIndex()
    {
        try {
            $folders = PostmanFolder::paginate(15);

            return view('postman.folder', compact('folders'));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller folderIndex method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function workspaceIndex()
    {
        try {
            $folders = PostmanWorkspace::paginate(15);

            return view('postman.workspace', compact('folders'));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller workspaceIndex method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function collectionindex()
    {
        try {
            $folders = PostmanCollection::paginate(15);
            $workspaces = PostmanWorkspace::all();

            return view('postman.collection', ['folders' => $folders, 'workspaces' => $workspaces]);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller collectionindex method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderSearch(Request $request)
    {
        try {
            $folders = new PostmanFolder;
            if (! empty($request->folder_name)) {
                $folders = $folders->where('name', 'like', '%'.$request->folder_name.'%');
            }

            $folders = $folders->paginate(Setting::get('pagination'));

            return view('postman.folder', compact('folders'));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller folderSearch method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'request_name' => 'required|string|max:255',
            'folder_name' => 'required|string|max:255',
            'request_types' => 'required|string|max:255',
            'user_permission' => 'present|array|min:1',
        ];

        $this->validate($request, $rules);

        try {
            $created_user_permission = '';
            if (isset($request->id) && $request->id > 0) {
                $postman = PostmanRequestCreate::find($request->id);
                $type = 'Update';
                if ($postman->body_json != $request->body_json) {
                    $jsonVersion = PostmanRequestJsonHistory::create(
                        [
                            'user_id' => Auth::user()->id,
                            'request_id' => $request->id,
                            'request_data' => $request->body_json,
                        ]
                    );
                    PostmanRequestJsonHistory::where('id', $jsonVersion->id)->update(['version_json' => 'v'.$jsonVersion->id]);
                }
                if ($postman->remark != $request->remark) {
                    PostmanRemarkHistory::create(
                        [
                            'user_id' => Auth::user()->id,
                            'postman_request_create_id' => $request->id,
                            'old_remark' => $postman->remark,
                            'remark' => $request->remark,
                        ]
                    );
                }
            } else {
                $postman = new PostmanRequestCreate;
                $type = 'Created';
                $created_user_permission = ','.Auth::user()->id;
            }

            $postman->folder_name = $request->folder_name;
            $postman->request_name = $request->request_name;
            $postman->request_type = $request->request_types;
            $postman->request_url = ! empty($request->request_url) ? $request->request_url[0] : '';
            $postman->params = $request->params;
            $postman->authorization_type = $request->authorization_type;
            $postman->authorization_token = $request->authorization_token;
            $postman->request_headers = $request->request_headers;
            $postman->body_type = $request->body_type;
            $postman->body_json = $request->body_json;
            $postman->pre_request_script = $request->pre_request_script;
            $postman->tests = $request->tests;
            $postman->user_permission = ! empty($request->user_permission) ? implode(',', $request->user_permission).$created_user_permission : $created_user_permission;
            $postman->controller_name = $request->controller_name;
            $postman->method_name = $request->method_name;
            $postman->remark = $request->remark;
            $postman->end_point = $request->end_point;

            $postman->grumphp_errors = $request->grumphp_errors;
            $postman->magento_api_standards = $request->magento_api_standards;
            $postman->swagger_doc_block = $request->swagger_doc_block;
            $postman->used_for = $request->used_for;
            $postman->user_in = $request->user_in;
            $postman->save();

            //History store
            $postmanH = new PostmanEditHistory;
            $postmanH->user_id = Auth::user()->id;
            $postmanH->postman_request_id = $postman->id;
            $postmanH->folder_name = $request->folder_name;
            $postmanH->request_name = $request->request_name;
            $postmanH->request_type = $request->request_types;
            $postmanH->request_url = ! empty($request->request_url) ? implode(',', $request->request_url) : '';
            $postmanH->controller_name = $request->controller_name;
            $postmanH->method_name = $request->method_name;
            $postmanH->params = $request->params;
            $postmanH->authorization_type = $request->authorization_type;
            $postmanH->authorization_token = $request->authorization_token;
            $postmanH->request_headers = $request->request_headers;
            $postmanH->body_type = $request->body_type;
            $postmanH->body_json = $request->body_json;
            $postmanH->pre_request_script = $request->pre_request_script;
            $postmanH->tests = $request->tests;
            $postmanH->user_permission = ! empty($request->user_permission) ? implode(',', $request->user_permission).$created_user_permission : $created_user_permission;
            $postmanH->remark = $request->remark;
            $postmanH->end_point = $request->end_point;

            $postmanH->grumphp_errors = $request->grumphp_errors;
            $postmanH->magento_api_standards = $request->magento_api_standards;
            $postmanH->swagger_doc_block = $request->swagger_doc_block;
            $postmanH->used_for = $request->used_for;
            $postmanH->user_in = $request->user_in;

            $postmanH->save();

            if (is_array($request->request_url)) {
                PostmanMultipleUrl::where('postman_request_create_id', $request->id ?? $postman->id)->delete();
                $first_request_url = $request->request_url[0];
                $path = parse_url($first_request_url, PHP_URL_PATH);
                $query_str = parse_url($first_request_url, PHP_URL_QUERY);
                $url_suffix = $path.($query_str ? '?'.$query_str : '');
                foreach ($request->request_url as $req_key => $reqUrl) {
                    if ($reqUrl) {
                        if ($req_key > 0 && $url_suffix) {
                            //Check if request contains suffix or not
                            $check_req = trim($reqUrl, '/');
                            $path = parse_url($check_req, PHP_URL_PATH);
                            $query_str = parse_url($check_req, PHP_URL_QUERY);
                            if ($path == '' && $query_str == '') {
                                $reqUrl = $check_req.$url_suffix;
                            }
                        }

                        PostmanMultipleUrl::create([
                            'user_id' => Auth::user()->id,
                            'postman_request_create_id' => $request->id ?? $postman->id,
                            'request_url' => $reqUrl,
                        ]);
                    }
                }
            }
            $this->createPostmanHistory($postman->id, $type);
            if ($type == 'Created') {
                $this->createPostmanFolder($postman->folder_name, $request->folder_real_name);
                $this->createPostmanRequestAPI($postman->folder_name, $request);
            }

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Create Postman Request Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Create Postman History  Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * This function use for add to user permission
     */
    public function userPermission(Request $request): JsonResponse
    {
        try {
            $postmans = PostmanRequestCreate::where('folder_name', $request->per_folder_name)->get();
            foreach ($postmans as $postmanData) {
                $user_permission = $postmanData->user_permission.','.$request->per_user_name;
                $user_permission = array_unique(explode(',', $user_permission));
                $user_permission = implode(',', $user_permission);
                PostmanRequestCreate::where('id', '=', $postmanData->id)->update(
                    [
                        'user_permission' => $user_permission,
                    ]
                );
            }

            return response()->json(['code' => 200, 'message' => 'Permission Updated successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman User permission Error => '.json_decode($e).' #id #'.$request->id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getMulRequest(Request $request): JsonResponse
    {
        try {
            $multiReqs = PostmanMultipleUrl::where('postman_request_create_id', $request->id)->get();
            if (empty($multiReqs->toArray())) {
                $postmans = PostmanRequestCreate::where('id', $request->id)->first();
                $user_id = substr($postmans->user_permission, 0, strrpos($postmans->user_permission.',', ','));
                PostmanMultipleUrl::create([
                    'user_id' => $user_id,
                    'postman_request_create_id' => $postmans->id,
                    'request_url' => $postmans->request_url,
                ]);
            }

            $multiReqs = PostmanMultipleUrl::where('postman_request_create_id', $request->id)->get();
            $html = '';
            foreach ($multiReqs as $reqUrl) {
                $html .= "<div ><input type='checkbox' name='urls[]' value='".$reqUrl->id."' style='height: 13px;'/> ".$reqUrl->request_url.'<br/></div>';
            }

            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Request listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman get Mul Request Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman get Mul Request Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function jsonVersion(Request $request): JsonResponse
    {
        try {
            $jsonVersion = PostmanRequestJsonHistory::create(
                [
                    'user_id' => Auth::user()->id,
                    'request_id' => $request->id,
                    'request_data' => $request->json_data,
                    'json_Name' => $request->json_name,
                ]
            );
            PostmanRequestJsonHistory::where('id', $jsonVersion->id)->update(['version_json' => 'v'.$jsonVersion->id]);
            $jsonVersion = PostmanRequestJsonHistory::where('id', $jsonVersion->id)->first();

            return response()->json(['code' => 200, 'data' => $jsonVersion, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Create Json Version history Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Create Json Version history Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderStore(Request $request): JsonResponse
    {
        try {
            if (isset($request->id) && $request->id > 0) {
                $folder = PostmanFolder::find($request->id);
            } else {
                $folder = new PostmanFolder;
            }
            $folder->name = $request->folder_name;
            $folder->save();

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Create Folder Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Create Folder Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function workspaceStore(Request $request): JsonResponse
    {
        try {
            $name = $request->get('workspace_name');
            $type = $request->get('workspace_type');
            $workspace_id = $request->get('id');
            if ($request->get('id')) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->put('https://api.getpostman.com/workspaces/'.$workspace_id, [
                    'workspace' => [
                        'name' => $name,
                        'type' => $type,
                    ],
                ]);
            } else {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->post('https://api.getpostman.com/workspaces', [
                    'workspace' => [
                        'name' => $name,
                        'type' => $type,
                    ],
                ]);
            }
            if ($response->ok()) {
                if (isset($request->id)) {
                    $workspace = $response->json()['workspace'];
                    PostmanWorkspace::where('workspace_id', $request->get('id'))->update(['workspace_id' => $workspace['id'], 'workspace_name' => $workspace['name'], 'type' => $type]);
                } else {
                    $table = new PostmanWorkspace;
                    $workspace = $response->json()['workspace'];
                    $table->workspace_id = $workspace['id'];
                    $table->workspace_name = $workspace['name'];
                    $table->type = $type;
                    $table->save();
                }

                return response()->json(['code' => 200, 'message' => 'successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Create Workspace Error => '.json_decode($e).' #id #'.$request->id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function collectionStore(Request $request): JsonResponse
    {
        try {
            $name = $request->get('collection_name');
            $description = $request->get('collection_description');
            $workspace_id = $request->get('workspace_id');
            $collection_id = $request->get('collection_id');
            $workspace_name = PostmanWorkspace::where('workspace_id', $workspace_id)->get()->first()['workspace_name'];
            if ($request->get('collection_id')) {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->put('https://api.getpostman.com/collections/'.$collection_id, [
                    'collection' => [
                        'item' => [],
                        'info' => [
                            'name' => $name,
                            'description' => $description,
                            'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                        ],
                    ],
                ]);
            } else {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->post('https://api.getpostman.com/collections?workspace='.$workspace_id, [
                    'collection' => [
                        'item' => [],
                        'info' => [
                            'name' => $name,
                            'description' => $description,
                            'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                        ],
                    ],
                ]);
            }

            if ($response->ok()) {
                if ($request->get('collection_id')) {
                    PostmanCollection::where('collection_id', $request->get('collection_id'))
                        ->update(['workspace_id' => $workspace_id, 'description' => $description, 'workspace_name' => $workspace_name, 'collection_name' => $name]);
                } else {
                    $collection = $response->json()['collection'];
                    $table = new PostmanCollection;
                    $table->workspace_id = $workspace_id;
                    $table->workspace_name = $workspace_name;
                    $table->description = $description;
                    $table->collection_name = $name;
                    $table->collection_id = $collection['uid'];
                    $table->save();
                }

                return response()->json(['code' => 200, 'message' => 'successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Create Collection Error => '.json_decode($e).' #id #'.$request->id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostmanRequestCreate $postmanRequestCreate, Request $request): JsonResponse
    {
        try {
            $postman = PostmanRequestCreate::find($request->id);
            $postmanUrl = PostmanMultipleUrl::where('postman_request_create_id', $request->id)->get();

            $postmanJson = PostmanRequestJsonHistory::where('request_data', $postman->body_json)->first();
            $postmanJson->json_Name = $postman->request_name ?? '';
            $postmanJson->save();
            $postman->json_body_id = $postmanJson->id ?? '';
            $postman->save();

            $postman = PostmanRequestCreate::find($request->id);

            $ops = '';
            $folders = PostmanFolder::all();
            foreach ($folders as $folder) {
                $selected = '';
                if ($postman->folder_name == $folder->id) {
                    $selected = 'selected';
                }
                $ops .= '<option value="'.$folder->id.'" '.$selected.'>'.$folder->name.'</option>';
            }

            return response()->json(['code' => 200, 'data' => $postman, 'postmanUrl' => $postmanUrl, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Data Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderEdit(PostmanFolder $postmanFolder, Request $request): JsonResponse
    {
        try {
            $folders = PostmanFolder::find($request->id);

            return response()->json(['code' => 200, 'data' => $folders, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Folder Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Folder Data Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function workspaceEdit(PostmanWorkspace $postmanWorkspace, Request $request): JsonResponse
    {
        try {
            $folders = PostmanWorkspace::where('workspace_id', $request->get('id'))->first();

            return response()->json(['code' => 200, 'data' => $folders, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Folder Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Folder Data Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function collectionEdit(PostmanCollection $PostmanCollection, Request $request): JsonResponse
    {
        try {
            $collection = PostmanCollection::where('collection_id', $request->get('id'))->first();

            return response()->json(['code' => 200, 'data' => $collection, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Folder Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Folder Data Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostmanRequestCreate $postmanRequestCreate, Request $request): JsonResponse
    {
        try {
            $postman = PostmanRequestCreate::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $postman, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Request Delete Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Request Delete Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function folderDestroy(PostmanFolder $postmanFolder, Request $request): JsonResponse
    {
        try {
            $folders = PostmanFolder::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $folders, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Folder Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Folder Data Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function workspaceDestroy(PostmanWorkspace $postmanWorkspace, Request $request): JsonResponse
    {
        try {
            // Set up the API endpoint and headers
            $workspaceId = $request->get('id');
            $url = "https://api.getpostman.com/workspaces/{$workspaceId}";
            $headers = [
                'X-API-Key' => config('settings.x_api_key'),
            ];

            // Send the DELETE request to delete the workspace
            $response = Http::withHeaders($headers)->delete($url);

            // Check for errors and print the response
            if ($response->failed()) {
                return response()->json(['code' => 500, 'message' => 'Internal server error']);
            }
            $workspace = PostmanWorkspace::where('workspace_id', '=', $request->id)->delete();
            PostmanCollection::where('workspace_id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $workspace, 'message' => 'Deleted successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Edit Folder Data Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Edit Folder Data Error', $msg, 'postman_folders');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanHistory::select('postman_historys.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_historys.user_id')
                ->where('postman_id', '=', $request->id)->orderByDesc('id')->get();
            $html = view('partials.postman_history_rows', ['postHis' => $postHis])->render();

            return response()->json(['code' => 200, 'data' => $postHis, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Get History Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Get History Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanRemarkHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanRemarkHistory::select('postman_remark_histories.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_remark_histories.user_id')
                ->where('postman_request_create_id', '=', $request->id)->orderByDesc('id')->get();

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Get Remark History Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Get Remark History Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanEditHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanEditHistory::select('postman_edit_histories.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_edit_histories.user_id')
                ->where('postman_request_id', '=', $request->id)->orderByDesc('id')->get();

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Get Remark History Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Get Remark History Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanErrorHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanError::select('postman_errors.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_errors.user_id')
                ->where('postman_errors.parent_id', '=', $request->id)->orderByDesc('postman_errors.id')->get();

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Error history Log Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Error history Log Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanRequestHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanRequestHistory::select('postman_request_histories.request_url', 'postman_request_histories.created_at', 'postman_request_histories.id', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_request_histories.user_id')
                ->where('request_id', '=', $request->id)->orderByDesc('id')->get();

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Get Request History Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Get Request History Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanResponseHistoryLog(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanResponse::select('postman_responses.*', 'u.name AS userName')
                ->leftJoin('users AS u', 'u.id', 'postman_responses.user_id')
                ->where('request_id', '=', $request->id)->orderByDesc('id')->get();

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function removeUserPermission(Request $request): JsonResponse
    {
        try {
            $postHis = PostmanRequestCreate::select('*')->where('id', '=', $request->id)->orderByDesc('id')->first();
            $users = explode(',', $postHis->user_permission);
            if (($key = array_search($request->user_id, $users)) !== false) {
                unset($users[$key]);
            }
            $postHis = PostmanRequestCreate::select('*')->where('id', '=', $request->id)->update(['user_permission' => implode(',', $users)]);

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Get Request Response History Error => '.json_decode($e).' #id #'.$request->id ?? '');
            $this->PostmanErrorLog($request->id ?? '', 'Postman Get Request Response History Error', $msg, 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getPostmanWorkSpaceAPI(): JsonResponse
    {
        try {
            $url = 'https://api.getpostman.com/workspaces';
            $header = [
                'X-API-Key' => config('settings.x_api_key'),
            ];

            $this->fireApi('', $url, $header, 'GET');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller getPostmanWorkSpaceAPI method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getAllPostmanCollectionApi(): JsonResponse
    {
        try {
            $data = '';
            $url = 'https://api.getpostman.com/collections';
            $header = [
                'X-API-Key' => config('settings.x_api_key'),
                'Content-Type: application/json',
            ];

            $this->fireApi($data, $url, $header, 'GET');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller getAllPostmanCollectionApi method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function createPostmanCollectionAPI(): JsonResponse
    {
        try {
            $data = '{
            "collection": {
                "info": {
                    "name": "Sample Collection 909",
                    "description": "This is just a sample collection.",
                    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
                },
                "item": [
                    {
                        "name": "This is a folder",
                        "item": [
                            {
                                "name": "Sample POST Request",
                                "request": {
                                    "url": "https://postman-echo.com/post",
                                    "method": "POST",
                                    "header": [
                                        {
                                            "key": "Content-Type",
                                            "value": "application/json"
                                        }
                                    ],
                                    "body": {
                                        "mode": "raw",
                                        "raw": "{\\"data\\": \\"123\\"}"
                                    },
                                    "description": "This is a sample POST Request"
                                }
                            }
                        ]
                    },
                    {
                        "name": "Sample GET Request",
                        "request": {
                            "url": "https://postman-echo/get",
                            "method": "GET",
                            "description": "This is a sample GET Request"
                        }
                    }
                ]
            }
        }';
            $url = 'https://api.getpostman.com/collections';
            $header = [
                'Content-Type: application/json',
                'X-API-Key' => config('settings.x_api_key'),
            ];
            $this->fireApi($data, $url, $header, 'POST');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller createPostmanCollectionAPI method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function getPostmanCollectionAndCreateAPI(Request $request)
    {
        try {
            $requestData['folder_name'] = isset($request->folder_real_name) ? $request->folder_real_name : 'test New Folder';
            $requestData['request_name'] = isset($request->request_name) ? $request->request_name : 'This is New Request';
            $requestData['request_url'] = isset($request->request_url) ? $request->request_url : 'https://google.com';
            $requestData['request_type'] = isset($request->request_type) ? $request->request_type : 'POST';
            $requestData['body_json'] = isset($request->body_json) ? $request->body_json : '{"id": "1", "name":"hello"}';

            // Create folder
            $data = '';
            $url = 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914';
            $header = [
                'X-API-Key' => config('settings.x_api_key'),
            ];
            $response = $this->fireApi($data, $url, $header, 'GET');

            $collect = (array) json_decode($response);
            $collectNew = $collect;
            foreach ($collect['collection']->item as $key => $val) {
                $vals = (array) $val;
                foreach ($vals as $ival) {
                    if ($ival == $requestData['folder_name']) {
                        $collectNew['collection']->item[$key]->item[] = [
                            'name' => $requestData['request_name'],
                            'request' => [
                                'url' => $requestData['request_url'],
                                'method' => $requestData['request_type'],
                                'header' => [
                                    ['key' => 'Content-Type', 'value' => 'application/json'],
                                ],
                                'body' => [
                                    'mode' => 'raw',
                                    'raw' => $requestData['body_json'],
                                ],
                                'description' => 'This is a sample POST Request',
                            ],
                        ];
                    }
                }
            } //end foreach
            if ($request->isjson) {
                print_r(($collectNew));
                exit;
            }

            return json_encode((object) $collectNew);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller getPostmanCollectionAndCreateAPI method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Create exiting postman folder inside request
     */
    public function updatePostmanCollectionAPI(Request $request): JsonResponse
    {
        try {
            $data = $this->getPostmanCollectionAndCreateAPI($request);
            $url = 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914';
            $header = [
                'Content-Type: application/json',
                'X-API-Key' => config('settings.x_api_key'),
            ];
            $this->fireApi($data, $url, $header, 'PUT');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller updatePostmanCollectionAPI method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function createPostmanFolder($fID = '', $fName = ''): JsonResponse
    {
        try {
            if ($fID == '') {
                $fID = '1';
            }
            if ($fName == '') {
                $fName = 'test New Folder';
            }

            $url = 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914/folders';
            $data = '{ 
            "id": "'.$fID.'", 
            "name": "'.$fName.'", 
            "description": "This is a '.$fName.' folder." 
        }';
            $header = [
                'Accept: application/vnd.postman.v2+json',
                'X-API-Key' => config('settings.x_api_key'),
                'Content-Type: application/json',
            ];
            $this->fireApi($data, $url, $header, 'POST');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller createPostmanFolder method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function createPostmanRequestAPI($fID = '', $request = ''): JsonResponse
    {
        try {
            $requestData['id'] = isset($request->id) ? $request->id : '1';
            $requestData['folder_name'] = isset($request->folder_real_name) ? $request->folder_real_name : 'test New Folder';
            $requestData['request_name'] = isset($request->request_name) ? $request->request_name : 'This is New Request';
            $requestData['request_url'] = isset($request->request_url) ? $request->request_url : 'https://google.com';
            $requestData['request_type'] = isset($request->request_type) ? $request->request_type : 'POST';
            $requestData['body_json'] = isset($request->body_json) ? $request->body_json : '{"id": "1", "name":"hello"}';
            $requestData['isjson'] = isset($request->isjson) ? $request->isjson : 'isjson';
            $requestData['body_type'] = isset($request->body_type) ? $request->body_type : 'row';

            if ($fID == '') {
                $fID = '1';
            }
            $data = '{
            "id": "'.$requestData['id'].'",
            "name": "'.$requestData['request_name'].'",
            "description": "This is an '.$requestData['request_name'].'.",
            "headers": "",
            "url": "'.$requestData['request_url'].'",
            "preRequestScript": "",
            "pathVariables": {},
            "method": "'.$requestData['request_type'].'",
            "rawModeData": "'.$requestData['body_type'].'"
            "data": [
                '.$requestData['body_json'].'
            ],
            "dataMode": "params",
            "tests": "var data = JSON.parse(responseBody);"
        }';
            $url = 'https://api.getpostman.com/collections/40e314b8-610d-4396-824f-2d7896ac1914/requests?folder='.$fID;
            $header = [
                'Accept: application/vnd.postman.v2+json',
                'X-API-Key' => config('settings.x_api_key'),
                'Content-Type: application/json',
            ];
            $this->fireApi($data, $url, $header, 'POST');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller createPostmanRequestAPI method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function sendPostmanRequestAPI(Request $request): JsonResponse
    {
        try {
            if (! empty($request->urls)) {
                $postmanUrls = PostmanMultipleUrl::whereIn('id', $request->urls)->get();

                foreach ($postmanUrls as $postmanUrl) {
                    $postman = PostmanRequestCreate::where('id', $postmanUrl->postman_request_create_id)->first();
                    if (empty($postman)) {
                        Log::error('Postman Send request API Error=> Postman request data not found'.' #id #'.$postmanUrl->postman_request_create_id ?? '');
                        $this->PostmanErrorLog($postmanUrl->postman_request_create_id ?? '', 'Postman Send request API ', ' Postman request data not found', 'postman_request_creates');

                        return response()->json(['code' => 500, 'message' => 'Request Data not found']);
                    } else {
                        PostmanRequestHistory::create(
                            [
                                'user_id' => Auth::user()->id,
                                'request_id' => $postman->id,
                                'request_data' => $postman->body_json,
                                'request_url' => $postmanUrl->request_url,
                                'request_headers' => "'Content-Type: application/json',
                                                    'Authorization: '".$postman->authorization_type."',
                                                    'Cookie: PHPSESSID=l15g0ovuc3jpr98tol956voan6'",
                            ]
                        );
                        $header = [
                            'Content-Type: application/json',
                            $postman->request_headers,
                            'Authorization:Bearer '.$postman->authorization_token,
                        ];
                        $response = $this->fireApi($postman->body_json, $postmanUrl->request_url, $header, $postman->request_type);
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        $curl = curl_init();
                        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $url = $postmanUrl->request_url;
                        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($response), $http_code, PostmanRequestCreateController::class, 'sendPostmanRequestAPI');
                        curl_close($curl);

                        $response = $response ? json_encode($response) : 'Not found response';
                        PostmanResponse::create(
                            [
                                'user_id' => Auth::user()->id,
                                'request_id' => $postman->id,
                                'response' => $response,
                                'request_url' => $postmanUrl->request_url,
                                'request_data' => $postman->body_json,
                                'response_code' => $http_code,
                            ]
                        );

                        if (! empty($response)) {
                            Log::info('Postman Send request API Response => '.$response.' #id #'.$postman->id ?? '');
                            $this->PostmanErrorLog($postman->id ?? '', 'Postman Send request API Response ', $response, 'postman_responses');
                        }
                    }
                }

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Postman requested successfully!!!']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Please select URL.', 'status' => false]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Send request Send postman request API Error => '.json_decode($e));

            $urls = implode(',', $request->urls);
            $this->PostmanErrorLog($urls ? $urls : '', 'Postman Send postman request API Error', $msg.' #ids '.$urls ? $urls : '', 'postman_request_creates');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public static function fireApi($data, $url, $header, $method)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $header,
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, $method, json_encode($data), json_decode($response), $httpcode, PostmanRequestCreateController::class, 'fireApi');

        curl_close($curl);

        return $response;
    }

    public function getCollectionFolders(Request $request)
    {
        $folders = PostmanCollectionFolder::where('postman_collection_id', $request->collectionId)->get();

        return $folders;
    }

    public function upsertCollectionFolder(Request $request): JsonResponse
    {
        try {
            $collectionId = $request->collection_id;
            $folderName = $request->folder_name;
            $folderId = $request->folder_id;

            $postmanCollection = PostmanCollection::find($collectionId);

            if ($request->folder_id) {
                $collectionFolder = PostmanCollectionFolder::find($folderId);

                $response = Http::withHeaders([
                    'Accept' => 'application/vnd.postman.v2+json',
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->put('https://api.getpostman.com/collections/'.$postmanCollection->collection_id.'/folders/'.$collectionFolder->folder_id, [
                    'name' => $folderName,
                ]);
            } else {
                $response = Http::withHeaders([
                    'Accept' => 'application/vnd.postman.v2+json',
                    'Content-Type' => 'application/json',
                    'X-API-Key' => config('settings.x_api_key'),
                ])->post('https://api.getpostman.com/collections/'.$postmanCollection->collection_id.'/folders', [
                    'name' => $folderName,
                ]);
            }
            if ($response->ok()) {
                if (isset($request->folder_id)) {
                    $folder = $response->json()['data'];
                    PostmanCollectionFolder::where('id', $request->folder_id)->update(['folder_id' => $folder['id'], 'folder_name' => $folder['name']]);
                } else {
                    $folder = $response->json()['data'];

                    $table = new PostmanCollectionFolder;
                    $table->postman_collection_id = $collectionId;
                    $table->folder_id = $folder['id'];
                    $table->folder_name = $folder['name'];
                    $table->save();
                }

                return response()->json(['code' => 200, 'message' => 'successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Create Folder Error => '.json_decode($e).' #id #'.$request->folder_id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function deleteCollectionFolder(Request $request): JsonResponse
    {
        try {
            $collectionId = $request->collection_id;
            $folderId = $request->folder_id;

            $postmanCollection = PostmanCollection::find($collectionId);

            $collectionFolder = PostmanCollectionFolder::find($folderId);

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.postman.v2+json',
                'Content-Type' => 'application/json',
                'X-API-Key' => config('settings.x_api_key'),
            ])->delete('https://api.getpostman.com/collections/'.$postmanCollection->collection_id.'/folders/'.$collectionFolder->folder_id);

            if ($response->ok()) {
                $collectionFolder->delete();

                return response()->json(['code' => 200, 'message' => 'successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman Delete Folder Error => '.json_decode($e).' #id #'.$request->folder_id ?? '');

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanStatusCreate(Request $request): JsonResponse
    {
        try {
            $status = new PostmanStatus;
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
        $postId = $request->input('postId');
        $selectedStatus = $request->input('selectedStatus');

        $postman = PostmanRequestCreate::find($postId);
        $history = new PostmanStatusHistory;
        $history->postman_create_id = $postId;
        $history->old_value = $postman->status_id;
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        $postman->status_id = $selectedStatus;
        $postman->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function updateApiIssueFixDone(Request $request): JsonResponse
    {
        $postId = $request->input('postId');
        $selectedValue = $request->input('selectedValue');

        $postman = PostmanRequestCreate::findOrFail($postId);

        $history = new PostmanApiIssueFixDoneHistory;
        $history->postman_create_id = $postId;
        $history->old_value = $postman->api_issue_fix_done;
        $history->new_value = $selectedValue;
        $history->user_id = Auth::user()->id;
        $history->save();

        $postman->api_issue_fix_done = $selectedValue;
        $postman->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function postmanApiIssueFixDoneHistories($id): JsonResponse
    {
        $datas = PostmanApiIssueFixDoneHistory::with(['user'])
            ->where('postman_create_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function postmanStatusHistories($id): JsonResponse
    {
        $datas = PostmanStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('postman_create_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function postmanColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'postman-listing')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'postman-listing';
            $column->column_name = json_encode($request->column_postman);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'postman-listing';
            $column->column_name = json_encode($request->column_postman);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        $request->except('_token');
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = PostmanStatus::find($key);
            $bugstatus->postman_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function taskCount($site_developement_id): JsonResponse
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function addStoreWebsiteUrlInFlutterPostman(Request $request)
    {
        $ids = [1, 5, 9, 3, 17];
        $postmans = PostmanRequestCreate::select('id')->where('folder_name', 3)->get();

        $storeCodes = StoreViewCodeServerMap::groupBy('server_id')->orderBy('server_id')->select('code', 'id', 'server_id')->get()->toArray();

        $storeWebsites = StoreWebsite::select('id', 'website')->whereIn('id', $ids)->get();

        if (! empty($postmans)) {
            foreach ($postmans as $value_postmans) {
                if (! empty($storeWebsites)) {
                    foreach ($storeWebsites as $value_storeWebsites) {
                        if (! empty($storeCodes)) {
                            foreach ($storeCodes as $value_storeCodes) {
                                $request_url = $value_storeWebsites['website'].$value_storeCodes['code'].'/';

                                $urlCreated = PostmanMultipleUrl::where('postman_request_create_id', $value_postmans->id)->where('request_url', 'like', $request_url)->first();

                                if (empty($urlCreated)) {
                                    PostmanMultipleUrl::create([
                                        'user_id' => Auth::user()->id,
                                        'postman_request_create_id' => $value_postmans->id,
                                        'request_url' => $request_url,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        echo 'success';
        exit;
    }

    public function index_request_hisory()
    {
        try {
            $q = PostmanResponse::query();
            $q->select('postman_responses.*', 'u.name AS userName');
            $q->leftJoin('users AS u', 'u.id', 'postman_responses.user_id');
            $q->orderByDesc('id');
            $counter = $q->count();
            $postHis = $q->paginate(Setting::get('pagination'));

            return view('postman.index_request', compact(
                'postHis',
                'counter'
            ));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller index method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function index_response_hisory()
    {
        try {
            $q = PostmanResponse::query();
            $q->select('postman_responses.*', 'u.name AS userName');
            $q->leftJoin('users AS u', 'u.id', 'postman_responses.user_id');
            $q->orderByDesc('id');
            $counter = $q->count();
            $postHis = $q->paginate(Setting::get('pagination'));

            return view('postman.index_response', compact(
                'postHis',
                'counter'
            ));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller index method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function postmanRunRequestUrl(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'run_request_folder_name' => 'required',
            ]);

            $urls = PostmanMultipleUrl::select('postman_multiple_urls.id')->join('postman_request_creates', 'postman_request_creates.id', '=', 'postman_multiple_urls.postman_request_create_id')->where('postman_request_creates.folder_name', $request->run_request_folder_name)->pluck('id');

            if (! empty($urls)) {
                PostmanRequestUrlRunJob::dispatch($urls, Auth::user()->id)->onQueue('postman_request_url_run');

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Postman Request Url run successfully!!!']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Url is not available', 'status' => false]);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function updatePostManField(Request $request): RedirectResponse
    {
        $fieldId = $request->id;
        $postManRequest = PostmanRequestCreate::findOrfail($fieldId);

        if ($postManRequest->body_json != $request->body_json) {
            $jsonVersion = PostmanRequestJsonHistory::create(
                [
                    'user_id' => Auth::user()->id,
                    'request_id' => $request->id,
                    'request_data' => $request->body_json,
                ]
            );
            PostmanRequestJsonHistory::where('id', $jsonVersion->id)->update(['version_json' => 'v'.$jsonVersion->id]);

            $postmanH = new PostmanEditHistory;
            $postmanH->user_id = Auth::user()->id;
            $postmanH->postman_request_id = $postManRequest->id;
            $postmanH->folder_name = $postManRequest->folder_name;
            $postmanH->request_name = $postManRequest->request_name;
            $postmanH->request_type = $postManRequest->request_types;
            $postmanH->request_url = $postManRequest->request_url;
            $postmanH->controller_name = $postManRequest->controller_name;
            $postmanH->method_name = $postManRequest->method_name;
            $postmanH->params = $postManRequest->params;
            $postmanH->authorization_type = $postManRequest->authorization_type;
            $postmanH->authorization_token = $postManRequest->authorization_token;
            $postmanH->request_headers = $postManRequest->request_headers;
            $postmanH->body_type = $postManRequest->body_type;
            $postmanH->body_json = $request->body_json;
            $postmanH->pre_request_script = $postManRequest->pre_request_script;
            $postmanH->tests = $postManRequest->tests;
            $postmanH->user_permission = $postManRequest->user_permission;
            $postmanH->remark = $postManRequest->remark;
            $postmanH->end_point = $postManRequest->end_point;

            $postmanH->grumphp_errors = $postManRequest->grumphp_errors;
            $postmanH->magento_api_standards = $postManRequest->magento_api_standards;
            $postmanH->swagger_doc_block = $postManRequest->swagger_doc_block;
            $postmanH->used_for = $postManRequest->used_for;
            $postmanH->user_in = $postManRequest->user_in;

            $postmanH->save();

            $type = 'Update';
            $this->createPostmanHistory($postManRequest->id, $type);
        }

        $postManRequest->body_json = $request->body_json;
        $postManRequest->save();

        return redirect()->back()->with('success', 'Field Updated successfully');

        //updateField
    }

    public function addRemark(Request $request): JsonResponse
    {
        $fieldId = $request->id;
        $postManRequest = PostmanRequestCreate::findOrfail($fieldId);
        PostmanRemarkHistory::create(
            [
                'user_id' => Auth::user()->id,
                'postman_request_create_id' => $request->id,
                'old_remark' => $postManRequest->remark,
                'remark' => $request->remark,
            ]
        );
        $postManRequest->remark = $request->remark;
        $postManRequest->save();

        return response()->json(['code' => 200, 'message' => 'Remark Added']);
    }

    public function responsesHistory(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $q = PostmanResponse::query();
            $q->select('postman_responses.*', 'u.name AS userName');
            $q->where('request_id', $id);
            $q->leftJoin('users AS u', 'u.id', 'postman_responses.user_id');
            $q->orderByDesc('id');
            $postHis = $q->get();
            // $counter = $q->count();
            // $postHis = $q->paginate(Setting::get('pagination'));

            return response()->json(['code' => 200, 'data' => $postHis]);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Log::error('Postman controller index method error => '.json_encode($msg));

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
