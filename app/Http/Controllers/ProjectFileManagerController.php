<?php

namespace App\Http\Controllers;

use App\ProjectFileManager;
use App\ProjectFileManagerHistory;
use App\Setting;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProjectFileManagerController extends Controller
{
    public $folderLimit = ['public' => 200];

    public $dumpData = [];

    public $updateData = [];

    public $count = 0;

    public function index(Request $request): View
    {
        $totalSizeq = ProjectFileManager::whereNull('parent')->get();

        $totalSize = 0;

        if (! $totalSizeq->isEmpty()) {
            foreach ($totalSizeq as $tSq) {
                $size = preg_replace('/[^0-9.]+/', '', $tSq->size);
                $totalSize += $size;
            }
        }

        $query = ProjectFileManager::query();
        if ($request->search) {
            $query = $query->where('name', 'LIKE', '%'.$request->search.'%')->orWhere('parent', 'LIKE', '%'.$request->search.'%');
        }
        $projectDirectoryData = $query->orderByRaw('CAST(size AS DECIMAL(10,2)) DESC')->paginate(25)->appends(request()->except(['page']));

        $limit_data = Setting::get('project_file_managers');

        if ($limit_data) {
            $limit_rec = $limit_data;
        } else {
            $limit_rec = 10;
        }

        return view('project_directory_manager.index', compact('projectDirectoryData', 'totalSize', 'limit_rec'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function insertsize(Request $request)
    {
        $data = ['val' => $request->size,
            'name' => 'project_file_managers',
            'type' => 'int',
        ];

        Setting::updateOrCreate(
            [
                'name' => 'project_file_managers',
            ],
            $data
        );
    }

    public function update(Request $request)
    {
        if ($request->post('id') && $request->post('size')) {
            $directoryData = ProjectFileManager::find($request->post('id'));
            $directoryData->notification_at = $request->post('size');
            $directoryData->save();
            echo 'Size Updated Successfully';
        } else {
            echo 'Incomplete Request';
        }
    }

    //Cron Funciton called from ProjectDirectory Console Command to dump all folders in Db
    public function listTree()
    {
        $directory = base_path();
        Log::info('PROJECT_MANAGER => started to scan file directory');
        $this->listFolderFiles($directory);

        ProjectFileManager::insert($this->dumpData);

        foreach ($this->updateData as $value) {
            ProjectFileManager::where('id', $value['id'])
                ->update(['size' => $value['size']]);
        }
        exit;
    }

    public function listFolderFiles($dir)
    {
        //for replace base path
        $basePath = base_path();
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if (! $fileInfo->isDot()) {
                if ($fileInfo->isDir()) {
                    $exePath = ['.git', 'vendor'];
                    $yes = false;
                    foreach ($exePath as $exe) {
                        if (stripos($fileInfo->getPathname(), $exe) !== false) {
                            $yes = true;
                        }
                    }

                    if ($yes) {
                        continue;
                    }

                    $batchPathReplace = str_replace($basePath, '', $fileInfo->getPathname());
                    $parentPath = str_replace($fileInfo->getFilename(), '', $batchPathReplace);
                    $parentPath = str_replace('\\', '/', $parentPath);

                    $size = $this->folderSize($fileInfo->getPathname());

                    $data = ProjectFileManager::where('name', $fileInfo->getFilename())->where('parent', $parentPath)->first();

                    if (empty($data)) {
                        $this->dumpData[$this->count]['name'] = $fileInfo->getFilename();
                        $this->dumpData[$this->count]['project_name'] = 'erp';
                        $this->dumpData[$this->count]['size'] = $size;
                        $this->dumpData[$this->count]['parent'] = $parentPath;
                        $this->dumpData[$this->count]['created_at'] = date('Y-m-d H:i:s');
                    } else {
                        $this->updateData[$data->id]['id'] = $data->id;
                        $this->updateData[$data->id]['size'] = $size;
                        $sizeInMB = number_format($size / 1048576, 0);
                        if (isset($data->notification_at) && $sizeInMB > $data->notification_at) {
                            $requestData = new Request;
                            $requestData->setMethod('POST');
                            $requestData->request->add(['priority' => 1, 'issue' => "Error With folder size {$fileInfo->getFilename()} which is more then {$sizeInMB} and expected size is {$data->notification_at}", 'status' => 'Planned', 'module' => "{$sizeInMB}", 'subject' => "Error With folder size {$fileInfo->getFilename()}", 'assigned_to' => 6]);
                            app(DevelopmentController::class)->issueStore($requestData, 'issue');
                        }
                    }
                    $this->count++;
                    self::listFolderFiles($fileInfo->getPathname());
                }
            }
        }
    }

    public function folderSize($dir)
    {
        $size = 0;

        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : self::folderSize($each);
        }

        return $size;
    }

    public function getLatestSize(Request $request): JsonResponse
    {
        ini_set('memory_limt', -1);
        $id = $request->get('id');
        $fileManager = \App\ProjectFileManager::find($id);
        $new_size = '0';
        if ($fileManager) {
            $path = base_path().DIRECTORY_SEPARATOR.(str_replace('./', '', $fileManager->name));
            $old_size = $fileManager->size;

            $limit_data = Setting::get('project_file_managers');

            if ($limit_data) {
                $limit_rec = $limit_data;
            } else {
                $limit_rec = 10;
            }

            $increase_size = (($old_size * $limit_rec) / 100);

            if (is_dir($path)) {
                $io = popen('/usr/bin/du -sk '.$path, 'r');
                $size = fgets($io, 4096);
                $new_size = substr($size, 0, strpos($size, "\t"));
                pclose($io);
            } else {
                $new_size = filesize($path) / 1024;
                $new_size = round($new_size, 2);
            }

            $fileManager->size = $new_size;
            $fileManager->save();

            if ($old_size != $new_size) {
                $param = [
                    'project_id' => $id,
                    'name' => $fileManager->name,
                    'old_size' => $old_size.'MB',
                    'new_size' => $new_size.'MB',
                    'user_id' => Auth::user()->id,
                ];

                ProjectFileManagerHistory::create($param);
            }

            $both_size = ($old_size + $increase_size);

            if ($new_size >= $both_size) {
                $message = 'Project Directory Size increase in Path = '.$fileManager->name.','.' OldSize = '.$old_size.'MB'.' And '.'NewSize = '.$new_size.'MB';

                $users = User::get();
                foreach ($users as $user) {
                    if ($user->isAdmin()) {
                        app(WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, $message);
                    }
                }
                ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 1]);
            } else {
                ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 0]);
            }

            return response()->json(['code' => 200, 'message' => 'Current size is : '.$new_size, 'size' => $new_size.'(MB)']);
        }

        return response()->json(['code' => 500, 'message' => 'Current size is : '.$new_size]);
    }

    public function sizelogHistory(Request $request)
    {
        $id = $request->id;

        $past_date = date('Y-m-d', strtotime('-7 days'));

        $size_log = ProjectFileManagerHistory::Leftjoin('users', 'users.id', 'project_file_managers_history.user_id')
            ->where('project_id', $id)
            ->whereDate('project_file_managers_history.created_at', '>=', $past_date)
            ->select('project_file_managers_history.*', 'users.name')->get();

        if ($size_log) {
            return $size_log;
        }

        return 'error';
    }

    public function deleteFile(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $fileManager = ProjectFileManager::find($id);
        if ($fileManager) {
            $path = base_path().DIRECTORY_SEPARATOR.(str_replace('./', '', $fileManager->name));
            if (! is_dir($path)) {
                if (! is_writable($path)) {
                    return response()->json(['code' => 500, 'message' => "{$path} is not writeable"]);
                } else {
                    unlink($path);
                    $fileManager->delete();

                    return response()->json(['code' => 200, 'message' => "{$path} has been deleted"]);
                }
            } else {
                return response()->json(['code' => 500, 'message' => "can not delete {$path} is directory"]);
            }
        }

        return response()->json(['code' => 500, 'message' => 'this file has been not found in record']);
    }

    public function getfilenameandsize(Request $request): JsonResponse
    {
        $name = $request->get('name');

        $path = base_path().DIRECTORY_SEPARATOR.(str_replace('./', '', $name));
        $files = File::files($path);

        $file_size_arr = [];

        foreach ($files as $key => $value) {
            $file_size_arr[$key]['file_name'] = $value->getfilename();

            $base = log($value->getSize()) / log(1024);
            $suffix = ['', 'k', 'M', 'G', 'T'][floor($base)];
            $size = pow(1024, $base - floor($base)).$suffix;
            $File_size = round($size, 2).' '.$suffix;

            $file_size_arr[$key]['file_size'] = $File_size;
        }

        return response()->json(['file_size_arr' => $file_size_arr, 'path' => $path, 'files' => $files]);
    }
}
