<?php

namespace App\Http\Controllers;

use App\Jobs\MagnetoGoogledriveUpload;
use App\Jobs\UploadGoogleDriveScreencast;
use App\Models\MagentoFrontendCategoryHistory;
use App\Models\MagentoFrontendChildFolder;
use App\Models\MagentoFrontendDocumentation;
use App\Models\MagentoFrontendHistory;
use App\Models\MagentoFrontendParentFolder;
use App\Models\MagentoFrontendRemark;
use App\SiteDevelopmentCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagentoFrontendDocumentationController extends Controller
{
    public function magentofrontenDocs(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();

        if ($request->ajax()) {
            $items = MagentoFrontendDocumentation::with('storeWebsiteCategory', 'user')
                ->select(
                    'magento_frontend_docs.*',
                    'magento_frontend_docs.location',
                    'magento_frontend_docs.admin_configuration',
                    'magento_frontend_docs.frontend_configuration',
                    'magento_frontend_docs.file_name',
                )
                ->join('site_development_categories', 'site_development_categories.id', '=', 'magento_frontend_docs.store_website_category_id');

            if (isset($request->frontend_configuration)) {
                $items->where('magento_frontend_docs.frontend_configuration', 'LIKE', '%'.$request->frontend_configuration.'%');
            }
            if (isset($request->admin_configuration)) {
                $items->where('magento_frontend_docs.admin_configuration', 'LIKE', '%'.$request->admin_configuration.'%');
            }
            if (isset($request->location)) {
                $items->whereIn('magento_frontend_docs.location', $request->location);
            }
            if (isset($request->categoryname)) {
                $items->whereIn('magento_frontend_docs.store_website_category_id', $request->categoryname);
            }

            return datatables()->eloquent($items)->addColumn('categories', $storecategories)->toJson();
        } else {
            return view('magento-frontend-documentation.index', $storecategories);
        }
    }

    public function magentofrontendStore(Request $request): JsonResponse
    {
        $magentofrontenddocs = [];

        $magentofrontenddoc = new MagentoFrontendDocumentation;
        $magentofrontenddoc->user_id = Auth::id();
        $magentofrontenddoc->store_website_category_id = $request->magento_docs_category_id;
        $magentofrontenddoc->location = $request->location;
        $magentofrontenddoc->admin_configuration = $request->admin_configuration;
        $magentofrontenddoc->frontend_configuration = $request->frontend_configuration;
        $magentofrontenddoc->parent_folder = $request->parent_folder;
        $magentofrontenddoc->child_folder = $request->child_folder;
        $magentofrontenddoc->read = $request->read ? implode(',', $request->read) : null;
        $magentofrontenddoc->write = $request->write ? implode(',', $request->write) : null;
        $magentofrontenddoc->save();

        if ($request->hasFile('child_folder_image')) {
            $file = $request->file('child_folder_image');
            $name = uniqid().time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-child-image');
            $file->move($destinationPath, $name);
            $magentofrontenddoc->child_folder_image = $name;
            $magentofrontenddoc->save();
        }

        if ($request->hasFile('file')) {
            foreach ($request->file as $file) {
                $magentofrontenddoc->file_name = $file->getClientOriginalName();
                $magentofrontenddoc->extension = $file->extension();

                $magentofrontenddoc->save();

                UploadGoogleDriveScreencast::dispatchSync($magentofrontenddoc, $file);

                $magentofrontenddocs[] = $magentofrontenddoc;
            }
        }

        $magnetohistory = new MagentoFrontendHistory;

        $magnetohistory->magento_frontend_docs_id = $magentofrontenddoc->id;
        $magnetohistory->store_website_category_id = $request->magento_docs_category_id;
        $magnetohistory->location = $request->location;
        $magnetohistory->admin_configuration = $request->admin_configuration;
        $magnetohistory->frontend_configuration = $request->frontend_configuration;
        $magnetohistory->updated_by = Auth::id();
        $magnetohistory->save();

        if ($request->child_folder) {
            $magentofrontendremark = new MagentoFrontendChildFolder;
            $magentofrontendremark->magento_frontend_docs_id = $magentofrontenddoc->id;
            $magentofrontendremark->child_folder_name = $request->child_folder;
            $magentofrontendremark->user_id = Auth::id();
            $magentofrontendremark->save();
        }

        if ($request->parent_folder) {
            $magentofrontendremark = new MagentoFrontendParentFolder;
            $magentofrontendremark->magento_frontend_docs_id = $magentofrontenddoc->id;
            $magentofrontendremark->parent_folder_name = $request->parent_folder;
            $magentofrontendremark->user_id = Auth::id();
            $magentofrontendremark->save();
        }

        return response()->json([
            'status' => true,
            'data' => $magentofrontenddocs,
            'message' => 'Magento frontend Documentation created successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendstoreRemark(Request $request): JsonResponse
    {
        $magentofrontendremark = new MagentoFrontendRemark;
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->remark = $request->remark;
        $magentofrontendremark->user_id = Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend Remark Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendgetRemarks(Request $request): JsonResponse
    {
        $remarks = MagentoFrontendRemark::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoLocationget(Request $request): JsonResponse
    {
        $remarks = MagentoFrontendHistory::with(['user'])->where('magento_frontend_docs_id', $request->id)->where('location_type', $request->location)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoFrontend(Request $request): JsonResponse
    {
        $remarks = MagentoFrontendHistory::with(['user'])->where('magento_frontend_docs_id', $request->id)->where('frontend_type', $request->admin)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoAdminget(Request $request): JsonResponse
    {
        $remarks = MagentoFrontendHistory::with(['user'])->where('magento_frontend_docs_id', $request->id)->where('admint_type', $request->admin)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendEdit($id): JsonResponse
    {
        $magento_module = MagentoFrontendDocumentation::find($id);
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();

        if ($magento_module) {
            return response()->json(['code' => 200, 'data' => $magento_module, 'storecategories' => $storecategories]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function magentofrontendOptions(Request $request): JsonResponse
    {
        $oldData = MagentoFrontendDocumentation::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoFrontendDocumentation::where('id', (int) $request->id)->update([$request->columnName => $request->data, 'user_id' => Auth::id()]);

        if ($request->columnName == 'store_website_category_id') {
            $oldCategoryId = $oldData->store_website_category_id;

            $this->saveCategoryHistory($oldData, $oldCategoryId, $request->data);
        }

        if ($updateMagentoModule) {
            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function magentofrontendUpdate(Request $request): JsonResponse
    {
        $oldData = MagentoFrontendDocumentation::where('id', (int) $request->id)->first();
        $oldlocation = $oldData->location;
        $oldAdminConfig = $oldData->admin_configuration;
        $oldFrontEndConfig = $oldData->frontend_configuration;

        $oldData->location = $request->location;
        $oldData->admin_configuration = $request->admin_configuration;
        $oldData->frontend_configuration = $request->frontend_configuration;
        $oldData->child_folder = $request->child_folder;
        $oldData->parent_folder = $request->parent_folder;
        $oldData->user_id = Auth::id();
        $oldData->save();

        if ($request->hasFile('child_folder_image')) {
            $file = $request->file('child_folder_image');
            $name = uniqid().time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('/magentofrontend-child-image');
            $file->move($destinationPath, $name);
        } else {
            $name = null;
        }

        if (! is_null($name)) {
            $oldData->child_folder_image = $name;
            $oldData->save();
        }

        $magnetohistory = new MagentoFrontendHistory;
        $magnetohistory->magento_frontend_docs_id = $oldData->id;
        $magnetohistory->store_website_category_id = $oldData->store_website_category_id;
        $magnetohistory->location = $request->location;
        $magnetohistory->admin_configuration = $request->admin_configuration;
        $magnetohistory->frontend_configuration = $request->frontend_configuration;
        $magnetohistory->frontend_configuration = $request->frontend_configuration;
        $magnetohistory->updated_by = Auth::id();

        if ($oldlocation != $request->location) {
            $magnetohistory->old_location = $oldlocation;
            $magnetohistory->location_type = 'location';
            $magnetohistory->save();
        }

        if ($oldAdminConfig != $request->admin_configuration) {
            $magnetohistory->old_admin_configuration = $oldAdminConfig;
            $magnetohistory->admint_type = 'AdminConfig';
            $magnetohistory->save();
        }

        if ($oldFrontEndConfig != $request->frontend_configuration) {
            $magnetohistory->old_frontend_configuration = $oldFrontEndConfig;
            $magnetohistory->frontend_type = 'FrontEndConfig';
            $magnetohistory->save();
        }

        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'status_name' => 'success',
            'code' => 200,
        ], 200);
    }

    public function magentofrontendhistoryShow($id): JsonResponse
    {
        $magento_module_api_histories = MagentoFrontendHistory::with([
            'user:id,name',
            'storeWebsiteCategory:id,title',
        ])
            ->where('magento_frontend_docs_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $magento_module_api_histories,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendCategoryHistoryShow($id): JsonResponse
    {
        $histories = MagentoFrontendCategoryHistory::with(['newCategory', 'oldCategory', 'user'])->where('magento_frontend_docs_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendStoreParentFolder(Request $request): JsonResponse
    {
        $magentofrontendremark = new MagentoFrontendParentFolder;
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->parent_folder_name = $request->folderName;
        $magentofrontendremark->user_id = Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend parent Folder Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendgetparentFolder(Request $request): JsonResponse
    {
        $remarks = MagentoFrontendParentFolder::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendparentFolderImage(Request $request): JsonResponse
    {
        $magentofrontenddoc = MagentoFrontendDocumentation::find($request->magento_frontend_id);

        if ($magentofrontenddoc) {
            $FrontendId = $request->magento_frontend_id;
            $newData = $magentofrontenddoc->parent_google_file_drive_id ?? '';
            $columnname = 'parentFolder';

            $magnetohistory = new MagentoFrontendHistory;
            $magnetohistory->magento_frontend_docs_id = $FrontendId;
            $magnetohistory->column_name = $columnname;
            $magnetohistory->new_value = $newData;
            $magnetohistory->updated_by = Auth::id();
            $magnetohistory->save();

            if ($request->hasFile('parent_folder_image')) {
                foreach ($request->parent_folder_image as $file) {
                    $magentofrontenddoc->parent_file_name = $file->getClientOriginalName();
                    $magentofrontenddoc->parent_extension = $file->extension();
                    $magentofrontenddoc->save();
                    MagnetoGoogledriveUpload::dispatchSync($magentofrontenddoc, $file);
                    $magnetohistory = MagentoFrontendHistory::find($magnetohistory->id);
                    $magnetohistory->new_value = $magentofrontenddoc->parent_google_file_drive_id;
                    $magnetohistory->file_name = $file->getClientOriginalName();
                    $magnetohistory->save();
                }

                return response()->json([
                    'status' => true,
                    'data' => $magnetohistory,
                    'message' => 'magneto frontend Parent Image Added succesfully',
                    'status_name' => 'success',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'magneto frontend Parent Image Added succesfully',
                'status_name' => 'failed',
            ], 200);
        }
    }

    public function magentofrontendChildImage(Request $request): JsonResponse
    {
        $magentofrontenddoc = MagentoFrontendDocumentation::find($request->magento_frontend_id);

        $frontendId = $request->magento_frontend_id;
        $newData = $magentofrontenddoc->google_drive_file_id;
        $columnname = 'childFolder';

        $magnetohistory = new MagentoFrontendHistory;
        $magnetohistory->magento_frontend_docs_id = $frontendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->new_value = $newData;
        $magnetohistory->updated_by = Auth::id();
        $magnetohistory->save();

        if ($request->hasFile('child_folder_image')) {
            foreach ($request->child_folder_image as $file) {
                $magentofrontenddoc->child_file_name = $file->getClientOriginalName();
                $magentofrontenddoc->child_extension = $file->extension();

                $magentofrontenddoc->save();
                MagnetoGoogledriveUpload::dispatchSync($magentofrontenddoc, $file);
                $magnetohistory = MagentoFrontendHistory::find($magnetohistory->id);
                $magnetohistory->new_value = $magentofrontenddoc->google_drive_file_id;
                $magnetohistory->file_name = $file->getClientOriginalName();
                $magnetohistory->save();
            }
        }

        return response()->json([
            'status' => true,
            'data' => $magentofrontenddoc,
            'message' => 'magneto frontend Child document Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendChildfolderstore(Request $request): JsonResponse
    {
        $magentofrontendremark = new MagentoFrontendChildFolder;
        $magentofrontendremark->magento_frontend_docs_id = $request->magento_front_end_id;
        $magentofrontendremark->child_folder_name = $request->folderName;
        $magentofrontendremark->user_id = Auth::id();
        $magentofrontendremark->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto frontend Child Folder Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontendgetChildFolder(Request $request): JsonResponse
    {
        $childFolder = MagentoFrontendChildFolder::with(['user'])->where('magento_frontend_docs_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $childFolder,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentofrontenddelete($id): JsonResponse
    {
        $magenotoFrontend = MagentoFrontendDocumentation::find($id);

        if (! $magenotoFrontend) {
            return response()->json(['message' => 'Magento frontend not found.'], 404);
        }

        $magenotoFrontend->delete();

        return response()->json(['message' => 'Magento frontend deleted successfully.']);
    }

    public function frontnedUploadedFilesList(Request $request): JsonResponse
    {
        try {
            $result = [];
            if (isset($request->id)) {
                $result = MagentoFrontendHistory::where('column_name', $request->type)
                    ->where('magento_frontend_docs_id', $request->id)
                    ->whereNotNull('new_value')
                    ->orderByDesc('id')
                    ->get();

                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('magento-frontend-documentation.upload-file-listing', compact('result'))->render(),
                ]);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => view('magento-frontend-documentation.upload-file-listing', ['result' => null])->render(),
            ]);
        }
    }

    protected function saveCategoryHistory($magentoFrontEnd, $oldCategoryId, $newCategoryId)
    {
        $history = new MagentoFrontendCategoryHistory;
        $history->magento_frontend_docs_id = $magentoFrontEnd->id;
        $history->old_category_id = $oldCategoryId;
        $history->new_category_id = $newCategoryId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }
}
