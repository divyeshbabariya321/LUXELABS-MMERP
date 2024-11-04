<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\GoogleDoc;
use App\Http\Requests\CreateDocumentOnTaskGoogleDocRequest;
use App\Jobs\CreateGoogleDoc;
use App\Jobs\CreateGoogleSpreadsheet;
use App\Models\GoogleDocsCategory;
use App\Task;
use App\User;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GoogleDocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = GoogleDoc::orderByDesc('created_at');
        if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->whereIn('google_docs.id', $keyword);
            });
        }
        if ($keyword = request('search')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('docid')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('docid', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('user_gmail')) {
            $data = $data->where(function ($q) use ($keyword) {
                foreach ($keyword as $value) {
                    $q->whereRaw("find_in_set('".$value."',google_docs.read)")->orWhereRaw("find_in_set('".$value."',google_docs.write)");
                }
            });
        }

        if ($keyword = request('tasks')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_docs.belongable_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('task_type')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_docs.belongable_type', $keyword);
            });
        }
        if (isset($request->googleDocCategory)) {
            $data = $data->whereIn('category', $request->googleDocCategory ?? []);
        }
        if (! Auth::user()->isAdmin()) {
            $data->whereRaw("find_in_set('".Auth::user()->gmail."',google_docs.read)")->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_docs.write)");
        }
        $data = $data->paginate(5);
        $users = User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();

        if ($request->ajax()) {
            $googleDocCategory = GoogleDocsCategory::pluck('name', 'id');

            return response()->json(['code' => 200, 'status' => 'success', 'data' => view('googledocs.partials.list-files', compact('data', 'googleDocCategory'))->render()]);
        }

        return view('googledocs.index', compact('data', 'users', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $data = $this->validate($request, [
                'type' => ['required', Rule::in('spreadsheet', 'doc', 'ppt', 'txt', 'xps')],
                'doc_name' => ['required', 'max:800'],
                'existing_doc_id' => ['sometimes', 'nullable', 'string', 'max:800'],
                'read' => ['sometimes'],
                'write' => ['sometimes'],
            ]);

            DB::transaction(function () use ($data) {
                $googleDoc = new GoogleDoc;
                $googleDoc->type = $data['type'];
                $googleDoc->name = $data['doc_name'];
                $googleDoc->created_by = Auth::user()->id;
                $googleDoc->category = $data['doc_category'] ?? null;
                if (isset($data['read'])) {
                    $googleDoc->read = implode(',', $data['read']);
                }

                if (isset($data['write'])) {
                    $googleDoc->write = implode(',', $data['write']);
                }
                $googleDoc->save();

                if (! empty($data['existing_doc_id'])) {
                    $googleDoc->docId = $data['existing_doc_id'];
                    $googleDoc->save();
                } else {
                    if ($googleDoc->type === 'spreadsheet') {
                        CreateGoogleSpreadsheet::dispatchSync($googleDoc);
                    }

                    if ($googleDoc->type === 'doc' || $googleDoc->type === 'ppt' || $googleDoc->type === 'txt' || $googleDoc->type === 'xps') {
                        CreateGoogleDoc::dispatchSync($googleDoc);
                    }
                }
            });

            return response()->json(['code' => 200, 'status' => 'success', 'data' => [], 'msg' => "Google {$data['type']} is Created."]);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit(int $id): JsonResponse
    {
        $modal = GoogleDoc::where('id', $id)->first();

        if ($modal) {
            return response()->json(['code' => 200, 'data' => $modal]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request): RedirectResponse
    {
        $updateData = [];
        if (isset($request->doc_category)) {
            $updateData['category'] = $request->doc_category;
        }
        if (isset($request->type)) {
            $updateData['type'] = $request->type;
        }
        if (isset($request->name)) {
            $updateData['name'] = $request->name;
        }
        if (isset($request->docId)) {
            $updateData['docId'] = $request->docId;
        }
        if (count($updateData) > 0) {
            $modal = GoogleDoc::where('id', $request->id)->update($updateData);
            if ($modal) {
                return redirect()->back()->with('success', 'Google Doc Category successfully updated.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $client = new Client;
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        try {
            $driveService->files->delete($id);
        } catch (Exception $e) {
            echo 'An error occurred: '.$e->getMessage();
        }
        GoogleDoc::where('docId', $id)->delete();

        return redirect()->back()->with('success', 'Your File has been deleted successfuly!');
    }

    public function permissionUpdate(Request $request): RedirectResponse
    {
        $fileId = request('file_id');
        $fileData = GoogleDoc::find(request('id'));
        $readData = request('read');
        $writeData = request('write');
        $permissionEmails = [];
        $client = new Client;
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $driveService = new Drive($client);
        // Build a parameters array
        $parameters = [];
        // Specify what fields you want
        $parameters['fields'] = 'permissions(*)';
        // Call the endpoint to fetch the permissions of the file
        $permissions = $driveService->permissions->listPermissions($fileId, $parameters);

        foreach ($permissions->getPermissions() as $permission) {
            $permissionEmails[] = $permission['emailAddress'];
            //Remove Permission
            if ($permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                $driveService->permissions->delete($fileId, $permission['id']);
            }
        }
        //assign permission based on requested data
        $index = 1;
        $driveService->getClient()->setUseBatch(true);
        if (! empty($readData)) {
            $batch = $driveService->createBatch();
            foreach ($readData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'reader',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $index++;
            }
            $batch->execute();
        }
        if (! empty($writeData)) {
            $batch = $driveService->createBatch();
            foreach ($writeData as $email) {
                $userPermission = new Drive\Permission([
                    'type' => 'user',
                    'role' => 'writer',
                    'emailAddress' => $email,
                ]);

                $request = $driveService->permissions->create($fileId, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $index++;
            }
            $batch->execute();
        }
        $fileData->read = ! empty($readData) ? implode(',', $readData) : null;
        $fileData->write = ! empty($writeData) ? implode(',', $writeData) : null;
        $fileData->save();

        return redirect()->back()->with('success', 'Permission successfully updated.');
    }

    public function permissionRemove(Request $request): JsonResponse
    {
        try {
            $googledocs = GoogleDoc::where(function ($query) use ($request) {
                $query->orWhere('read', 'like', '%'.($request->remove_permission).'%');
                $query->orWhere('read', 'like', '%'.($request->remove_permission).'%');
            })->get();

            foreach ($googledocs as $googledoc) {
                $permissionEmails = [];
                $client = new Client;
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);
                // Build a parameters array
                $parameters = [];
                // Specify what fields you want
                $parameters['fields'] = 'permissions(*)';
                // Call the endpoint to fetch the permissions of the file
                $permissions = $driveService->permissions->listPermissions($googledoc->docId, $parameters);
                foreach ($permissions->getPermissions() as $permission) {
                    $permissionEmails[] = $permission['emailAddress'];
                    //Remove old Permission
                    if ($permission['emailAddress'] == $request->remove_permission && $permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                        $driveService->permissions->delete($googledoc->docId, $permission['id']);
                    }
                }

                $read = explode(',', $googledoc->read);

                if (($key = array_search($request->remove_permission, $read)) !== false) {
                    unset($read[$key]);
                }

                $new_read_data = implode(',', $read);
                $googledoc->read = $new_read_data;

                $write = explode(',', $googledoc->write);
                if (($key = array_search($request->remove_permission, $write)) !== false) {
                    unset($write[$key]);
                }
                $new_write_data = implode(',', $write);
                $googledoc->write = $new_write_data;

                $googledoc->update();
            }

            return response()->json(['code' => 200, 'status' => 'success', 'data' => [], 'msg' => 'Permission successfully Remove']);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function permissionView(Request $request)
    {
        $googledoc = GoogleDoc::where('id', $request->id)->first();

        $data = [
            'read' => $googledoc->read,
            'write' => $googledoc->write,
            'code' => 200,
        ];

        return $data;
    }

    /**
     * Search data of google docs.
     *
     * @param  string  $subject
     */
    public function googledocSearch(Request $request): View
    {
        $subject = $request->subject;
        $data = GoogleDoc::where('name', 'LIKE', '%'.$subject.'%')->orderByDesc('created_at')->paginate(4);

        return view('googledocs.partials.list-files-header', compact('data'));
    }

    /**
     * create the document on devtask
     */
    public function createDocumentOnTask(CreateDocumentOnTaskGoogleDocRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $authUser = Auth::user();

            DB::transaction(function () use ($data, $authUser, $request) {
                $task = null;
                $class = null;

                if ($data['task_type'] == 'DEVTASK') {
                    $task = DeveloperTask::find($data['task_id']);
                    $class = DeveloperTask::class;
                }
                if ($data['task_type'] == 'TASK') {
                    $task = Task::find($data['task_id']);
                    $class = Task::class;
                }

                $googleDoc = new GoogleDoc;
                $googleDoc->type = $data['doc_type'];
                $googleDoc->name = $data['doc_name'];
                $googleDoc->created_by = Auth::user()->id;
                $googleDoc->category = $data['doc_category'] ?? null;

                // Add the task name and description in document name
                if (isset($request->attach_task_detail)) {
                    if ($data['task_type'] == 'DEVTASK') {
                        $googleDoc->name .= " (DEVTASK-$task->id ".($task->subject ?? '-').')';
                    }
                    if ($data['task_type'] == 'TASK') {
                        $googleDoc->name .= " (TASK-$task->id ".($task->task_subject ?? '-').')';
                    }
                }

                if ($authUser->isAdmin()) {
                    $googleDoc->read = null;
                    $googleDoc->write = null;
                } else {
                    $googleDoc->read = $authUser->gmail;
                    $googleDoc->write = $authUser->gmail;
                }

                if (isset($task) && isset($task->id)) {
                    $googleDoc->belongable_type = $class;
                    $googleDoc->belongable_id = $task->id;
                }

                $googleDoc->save();

                if ($googleDoc->type === 'spreadsheet') {
                    CreateGoogleSpreadsheet::dispatch($googleDoc);
                }

                if ($googleDoc->type === 'doc' || $googleDoc->type === 'ppt' || $googleDoc->type === 'txt' || $googleDoc->type === 'xps') {
                    CreateGoogleDoc::dispatch($googleDoc);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Document created successsfuly.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'success' => 'Something went wrong!',
            ]);
        }
    }

    /**
     * This function will list the created google document
     */
    public function listDocumentOnTask(Request $request): JsonResponse
    {
        try {
            if (isset($request->task_id)) {
                $class = '';
                if ($request->task_type == 'TASK') {
                    $class = Task::class;
                }
                if ($request->task_type == 'DEVTASK') {
                    $class = DeveloperTask::class;
                }

                $googleDoc = GoogleDoc::where('belongable_type', $class)->where('belongable_id', $request->task_id)->get();

                return response()->json([
                    'status' => false,
                    'data' => view('googledocs.task-document', compact('googleDoc'))->render(),
                ]);
            } else {
                throw new Exception('Task ID not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'data' => view('googledocs.task-document')->render(),
            ]);
        }
    }

    public function updateGoogleDocCategory(Request $request): JsonResponse
    {
        try {
            if (isset($request->category_id) && isset($request->doc_id)) {
                GoogleDoc::where('id', $request->doc_id)->update([
                    'category' => $request->category_id,
                ]);

                return response()->json(['status' => true, 'message' => 'Category updated.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid request']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error while updating status']);
        }
    }

    public function createGoogleDocCategory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();

                return response()->json(['code' => 400, 'status' => 'error', 'msg' => $errors ? $errors[0] : 'error']);
            }

            GoogleDocsCategory::create([
                'name' => $request->name,
            ]);

            return response()->json(['code' => 200, 'status' => 'success', 'data' => [], 'msg' => 'Category added successfully.']);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'status' => 'error', 'data' => [], 'msg' => $e->getMessage()]);
        }
    }

    public function assignUserPermission(Request $request): RedirectResponse
    {
        try {
            if (! isset($request->user_id)) {
                throw new Exception('User Id required');
            }
            if (! isset($request->document_id)) {
                throw new Exception('Please select Document');
            }

            $user = User::find($request->user_id);
            if (! $user) {
                throw new Exception('User not found.');
            }
            $doc = GoogleDoc::find($request->document_id);
            if (! $doc) {
                throw new Exception('Document not found.');
            }

            $readPermission = [];
            if ($doc->read) {
                $readPermission = explode(',', $doc->read);
            }
            $writePermission = [];
            if ($doc->read) {
                $writePermission = explode(',', $doc->write);
            }

            if (! isset($user->gmail) || $user->gmail == '') {
                throw new Exception('User Email not found.');
            }

            $permissionEmails = [];
            $client = new Client;
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            // Build a parameters array
            $parameters = [];
            // Specify what fields you want
            $parameters['fields'] = 'permissions(*)';
            // Call the endpoint to fetch the permissions of the file
            $permissions = $driveService->permissions->listPermissions($doc->docId, $parameters);

            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove old Permission
                if ($permission['emailAddress'] == $user->gmail && $permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                    $driveService->permissions->delete($doc->docId, $permission['id']);
                    unset($readPermission[array_search($user->gmail, $readPermission)]);
                    unset($writePermission[array_search($user->gmail, $writePermission)]);
                }
            }
            $driveService->getClient()->setUseBatch(true);

            $batch = $driveService->createBatch();
            $userPermission = new Drive\Permission([
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $user->gmail,
            ]);

            $r_request = $driveService->permissions->create($doc->docId, $userPermission, ['fields' => 'id']);
            $batch->add($r_request, 'user'.rand(0, 999));
            $batch->execute();
            $readPermission[] = $user->gmail;

            $batch = $driveService->createBatch();
            $userPermission = new Drive\Permission([
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => $user->gmail,
            ]);

            $w_request = $driveService->permissions->create($doc->docId, $userPermission, ['fields' => 'id']);
            $batch->add($w_request, 'user'.rand(0, 999));
            $batch->execute();
            $writePermission[] = $user->gmail;

            if ($doc->belongable_id == null) {
                $doc->belongable_id = $request->task_id;
                $doc->belongable_type = ($request->task_type == 'DEVTASK') ? DeveloperTask::class : Task::class;
            }
            $doc->read = ! empty($readPermission) ? implode(',', $readPermission) : null;
            $doc->write = ! empty($writePermission) ? implode(',', $writePermission) : null;
            $doc->save();

            return redirect()->back()->withSuccess('Permission assigned successsfully');
        } catch (Exception $e) {
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function getGoogleDocList(Request $request): JsonResponse
    {
        try {
            $doc = GoogleDoc::select('id', 'name as text')->get()->toArray();

            return response()->json([
                'status' => true,
                'docs' => $doc,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function googleDocRemovePermission(Request $request): JsonResponse
    {
        try {
            $fileIds = explode(',', request('remove_doc_ids'));
            $fileIds = array_map('intval', $fileIds);
            $readArray = request('read');
            $writeArray = request('write');

            foreach ($fileIds as $fileId) {
                $file = GoogleDoc::find($fileId);
                $permissionEmails = [];
                $client = new Client;
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);
                // Build a parameters array
                $parameters = [];
                // Specify what fields you want
                $parameters['fields'] = 'permissions(*)';
                // Call the endpoint to fetch the permissions of the file
                $permissions = $driveService->permissions->listPermissions($file->docId, $parameters);
                foreach ($permissions->getPermissions() as $permission) {
                    $permissionEmails[] = $permission['emailAddress'];
                    //Remove old Permission
                    if (in_array($permission['emailAddress'], $readArray) && $permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                        $driveService->permissions->delete($file->docId, $permission['id']);
                    }
                }

                $readUsers = array_diff(explode(',', $file->read), $readArray);
                $writeUsers = array_diff(explode(',', $file->write), $writeArray);
                $file->read = implode(',', $readUsers);
                $file->write = implode(',', $writeUsers);
                $file->save();
            }

            return response()->json(['code' => 200, 'status' => 'success', 'data' => [], 'msg' => 'Permission successfully removed']);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function addMulitpleDocPermission(Request $request): JsonResponse
    {
        try {
            $fileIds = explode(',', request('add_doc_ids'));
            $fileIds = array_map('intval', $fileIds);
            $readData = request('read');
            $writeData = request('write');
            $permissionEmails = [];

            foreach ($fileIds as $fileId) {
                $fileData = GoogleDoc::find($fileId);
                $client = new Client;
                $client->useApplicationDefaultCredentials();
                $client->addScope(Drive::DRIVE);
                $driveService = new Drive($client);
                // Build a parameters array
                $parameters = [];
                // Specify what fields you want
                $parameters['fields'] = 'permissions(*)';
                // Call the endpoint to fetch the permissions of the file
                $permissions = $driveService->permissions->listPermissions($fileData->docId, $parameters);

                foreach ($permissions->getPermissions() as $permission) {
                    $permissionEmails[] = $permission['emailAddress'];
                    //Remove Permission
                    if ($permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                        $driveService->permissions->delete($fileData->docId, $permission['id']);
                    }
                }
                //assign permission based on requested data
                $index = 1;
                $driveService->getClient()->setUseBatch(true);
                if (! empty($readData)) {
                    $batch = $driveService->createBatch();
                    foreach ($readData as $email) {
                        $userPermission = new Drive\Permission([
                            'type' => 'user',
                            'role' => 'reader',
                            'emailAddress' => $email,
                        ]);

                        $request = $driveService->permissions->create($fileData->docId, $userPermission, ['fields' => 'id']);
                        $batch->add($request, 'user'.$index);
                        $index++;
                    }
                    $batch->execute();
                }
                if (! empty($writeData)) {
                    $batch = $driveService->createBatch();
                    foreach ($writeData as $email) {
                        $userPermission = new Drive\Permission([
                            'type' => 'user',
                            'role' => 'writer',
                            'emailAddress' => $email,
                        ]);

                        $request = $driveService->permissions->create($fileData->docId, $userPermission, ['fields' => 'id']);
                        $batch->add($request, 'user'.$index);
                        $index++;
                    }
                    $batch->execute();
                }
                $fileData->read = ! empty($readData) ? implode(',', $readData) : null;
                $fileData->write = ! empty($writeData) ? implode(',', $writeData) : null;
                $fileData->save();
            }

            return response()->json(['code' => 200, 'status' => 'success', 'data' => [], 'msg' => 'Permission successfully updated.']);
        } catch (\Throwable $e) {
            return response()->json(['code' => 400, 'status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function googleDocumentList(Request $request)
    {
        $dataDropdown = GoogleDoc::pluck('name', 'id')->toArray();

        // Get the user input
        $input = $_GET['term'];

        // Filter tags based on user input
        $filteredTags = array_filter($dataDropdown, function ($tag) use ($input) {
            return stripos($tag, $input) !== false;
        });

        // Return the filtered tags as JSON
        echo json_encode($filteredTags);
    }

    public function googleTasksList(Request $request)
    {
        $tasksData = Task::pluck('id')->toArray();
        $DeveloperTaskData = DeveloperTask::pluck('id')->toArray();

        $tasks = array_unique(array_merge($tasksData, $DeveloperTaskData));

        sort($tasks);

        if (! empty($tasks)) {
            $tasks = explode(', ', implode(', ', $tasks));
        }

        // Get the user input
        $input = $_GET['term'];

        // Filter tags based on user input
        $filteredTags = array_filter($tasks, function ($tag) use ($input) {
            return stripos($tag, $input) !== false;
        });

        // Return the filtered tags as JSON
        echo json_encode($filteredTags);
    }
}
