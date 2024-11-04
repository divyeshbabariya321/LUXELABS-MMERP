<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\DeveloperTask;
use App\GoogleScreencast;
use App\Http\Requests\UpdateGoogleScreencastRequest;
use App\Jobs\UploadGoogleDriveScreencast;
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

class GoogleScreencastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get file list
        $data = GoogleScreencast::with(['user' => function ($query) {
            return $query->select('id', 'name');
        }])->orderByDesc('id');
        //fetch task list
        $taskList = DeveloperTask::where('task_type_id', 1)->orderByDesc('id');
        $generalTask = Task::orderByDesc('id');
        if (! Auth::user()->isAdmin()) {
            $taskList = $taskList->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id())->orWhere('tester_id', Auth::id())->orWhere('team_lead_id', Auth::id());
            $generalTask = $generalTask->where('assign_to', Auth::id());
        }
        $tasks = $taskList->select('id', 'subject')->get();
        $generalTask = $generalTask->select('id', 'task_subject as subject')->get();

        $taskIds = $taskList->pluck('id');
        $users = User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();
        if ($keyword = request('name')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('file_name', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('docid')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('google_drive_file_id', 'LIKE', "%$keyword%");
            });
        }
        if ($keyword = request('task_id')) {
            if (str_contains($keyword, 'TASK-')) {
                $keyword = trim($keyword, 'TASK-');
                $data = $data->where(function ($q) use ($keyword) {
                    $q->where('belongable_id', $keyword);
                });
            } else {
                $keyword = trim($keyword, 'DEV-');
                $data = $data->where(function ($q) use ($keyword) {
                    $q->where('developer_task_id', $keyword);
                });
            }
        }

        if ($keyword = request('user_id')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('user_id', $keyword);
            });
        }
        if ($keyword = request('remark')) {
            $data = $data->where(function ($q) use ($keyword) {
                $q->where('remarks', 'LIKE', '%'.$keyword.'%');
            });
        }

        if (empty($request->input('name')) && empty($request->input('docid')) && empty($request->input('task_id')) && ! Auth::user()->isAdmin()) {
            $data->whereIn('developer_task_id', $taskIds)->orWhere('user_id', Auth::id())->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_drive_screencast_upload.read)")->orWhereRaw("find_in_set('".Auth::user()->gmail."',google_drive_screencast_upload.write)");
        }

        $data = $data->paginate(25);

        return view('googledrivescreencast.index', compact('data', 'tasks', 'users', 'generalTask'))
            ->with('i', ($request->input('page', 1) - 1) * 25);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): JsonResponse
    {
        $isAdmin = Auth::user()->isAdmin();

        $validationRules = [
            'file' => ['required'],
            'file.*' => ['required'],
            'file_creation_date' => ['required'],
            'file_read' => ['sometimes'],
            'file_write' => ['sometimes'],
            'remarks' => ['sometimes'],
        ];

        $validationRules['task_id'] = ['sometimes'];

        $data = $this->validate($request, $validationRules);

        foreach ($data['file'] as $file) {
            $class = '';

            if (str_contains($data['task_id'], 'TASK-')) {
                $class = Task::class;
                $data['task_id'] = trim($data['task_id'], 'TASK-');
            } else {
                $class = DeveloperTask::class;
            }

            DB::transaction(function () use ($file, $data, $class) {
                $googleScreencast = new GoogleScreencast;
                $googleScreencast->file_name = $file->getClientOriginalName();
                $googleScreencast->extension = $file->extension();
                $googleScreencast->user_id = Auth::id();
                if (isset($data['file_read'])) {
                    $googleScreencast->read = implode(',', $data['file_read']);
                }
                if (isset($data['file_write'])) {
                    $googleScreencast->write = implode(',', $data['file_write']);
                }
                $googleScreencast->remarks = $data['remarks'];
                $googleScreencast->file_creation_date = $data['file_creation_date'];

                if ($class == DeveloperTask::class) {
                    $googleScreencast->developer_task_id = $data['task_id'];
                }
                if ($class == Task::class) {
                    $googleScreencast->belongable_id = $data['task_id'];
                    $googleScreencast->belongable_type = $class;
                }

                $googleScreencast->save();

                UploadGoogleDriveScreencast::dispatchSync($googleScreencast, $file);

                // Initialize the $params array
                $params = [];

                $googledriveId = GoogleScreencast::find($googleScreencast->id);

                $userName = Auth::user()->name;
                $fileLink = config('settings.google_drive_file_url').$googledriveId->google_drive_file_id.'/view?usp=share_link';

                // Create a formatted message with titles
                $message = "Google Drive Information:\n\n"; // Add an extra line break for space
                $message .= "Created by: {$userName}\n\n";
                $message .= "File Creation Date: {$data['file_creation_date']}\n\n";
                $message .= "File Link: {$fileLink}";

                // Assign the formatted message to the 'message' key in the $params array
                $params['message'] = $message;
                $params['user_id'] = Auth::user()->id;
                $params['developer_task_id'] = $data['task_id'];
                ChatMessage::create($params);
            });
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'File is Uploaded to Google Drive.',
        ], 200);
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
     *
     * @param  int  $id
     */
    public function update(UpdateGoogleScreencastRequest $request): RedirectResponse
    {

        try {
            $googlescreencast = GoogleScreencast::find(request('id'));
            $googlescreencast->file_name = $request->file_name;
            $googlescreencast->google_drive_file_id = $request->file_id;
            $googlescreencast->remarks = $request->file_remark;
            $googlescreencast->save();

            return redirect()->back()->with('success', 'Data updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error while updating data.');
        }
    }

    public function driveFilePermissionUpdate(Request $request): RedirectResponse
    {
        $fileId = request('file_id');
        $fileData = GoogleScreencast::find(request('id'));
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
            if ($permission['role'] != 'owner' && $permission['emailAddress'] != (config('settings.google_screencast_folder_owner_id'))) {
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
        GoogleScreencast::where('google_drive_file_id', $id)->delete();

        return redirect()->back()->with('success', 'Your File has been deleted successfuly!');
    }

    /**
     * Get drive files for requested task
     *
     * @param  mixed  $taskId
     */
    public function getTaskDriveFiles($taskId)
    {
        $driveFiles = GoogleScreencast::where('developer_task_id', $taskId)->orderByDesc('id')->with('user')->get();

        $driveFileData = '';

        foreach ($driveFiles as $driveFile) {
            $driveFileData .= '<tr><td>'.$driveFile['file_name'].'</td>
            <td>'.$driveFile['file_creation_date'].'</td>
            <td><a href="'.config('settings.google_drive_file_url').$driveFile['google_drive_file_id'].'/view?usp=share_link" target="_blank"><input class="fileUrl" type="text" value="'.config('settings.google_drive_file_url').$driveFile['google_drive_file_id'].'/view?usp=share_link" /></a>
            <button class="copy-button btn btn-secondary" data-message="'.config('settings.google_drive_file_url').$driveFile['google_drive_file_id'].'/view?usp=share_link">Copy</button></td>
            <td>'.$driveFile['remarks'].'</td>
            <td>'.$driveFile->user->name.'</td>
        </tr>';
        }
        if ($driveFileData == '') {
            $driveFileData = '<tr><td colspan="4">No data found.</td></tr>';
        }

        return $driveFileData;
    }

    public function getGoogleScreencast(Request $request): JsonResponse
    {
        $datas = GoogleScreencast::latest()->take(10)->get();

        if (! Auth::user()->isAdmin()) {
            $datas = GoogleScreencast::where('user_id', '=', Auth::id())->latest()->take(10)->get();
        }

        return response()->json([
            'tbody' => view('partials.modals.google-drive-screen-cast-modal-html', compact('datas'))->render(),
            'count' => $datas->count(),
        ]);
    }

    public function getDropdownDatas(Request $request): JsonResponse
    {
        $taskList = DeveloperTask::where('task_type_id', 1)->orderByDesc('id');
        $generalTask = Task::orderByDesc('id');

        if (! Auth::user()->isAdmin()) {
            $taskList = $taskList->where('user_id', Auth::id())->orWhere('assigned_to', Auth::id())->orWhere('tester_id', Auth::id())->orWhere('team_lead_id', Auth::id());
            $generalTask = $generalTask->where('assign_to', Auth::id());
        }
        $tasks = $taskList->select('id', 'subject')->get();
        $generalTask = $generalTask->select('id', 'task_subject as subject')->get();
        $users = User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();

        return response()->json(['tasks' => $tasks, 'users' => $users, 'generalTask' => $generalTask]);
    }

    public function addMultipleDocPermission(Request $request): RedirectResponse
    {
        $filePKIds = explode(',', request('multiple_file_id'));
        $filePKIds = array_map('intval', $filePKIds);
        $readData = request('read');
        $writeData = request('write');

        foreach ($filePKIds as $filePKId) {
            $fileData = GoogleScreencast::find($filePKId);
            $fileId = $fileData->google_drive_file_id;

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
                if ($permission['role'] != 'owner' && $permission['emailAddress'] != (config('settings.google_screencast_folder_owner_id'))) {
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
        }

        return redirect()->back()->with('success', 'Permission successfully updated.');
    }

    public function driveFileRemovePermission(Request $request): RedirectResponse
    {
        $fileIds = explode(',', request('remove_file_ids'));
        $fileIds = array_map('intval', $fileIds);
        $readArray = request('read');
        $writeArray = request('write');

        foreach ($fileIds as $fileId) {
            $file = GoogleScreencast::find($fileId);
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
            $permissions = $driveService->permissions->listPermissions($file->google_drive_file_id, $parameters);

            foreach ($permissions->getPermissions() as $permission) {
                $permissionEmails[] = $permission['emailAddress'];
                //Remove old Permission
                if (in_array($permission['emailAddress'], $readArray) && $permission['role'] != 'owner' && ($permission['emailAddress'] != config('settings.google_screencast_folder_owner_id'))) {
                    $driveService->permissions->delete($file->google_drive_file_id, $permission['id']);
                }
            }
            $readUsers = array_diff(explode(',', $file->read), $readArray);
            $writeUsers = array_diff(explode(',', $file->write), $writeArray);
            $file->read = implode(',', $readUsers);
            $file->write = implode(',', $writeUsers);
            $file->save();
        }

        return redirect()->back()->with('success', 'Permission successfully removed');
    }
}
