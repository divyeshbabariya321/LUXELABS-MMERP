<?php

namespace App\Http\Controllers;

use App\Jobs\MagnetoGoogledriveUpload;
use App\MagentoModule;
use App\Models\MagentoBackendDocumentation;
use App\Models\MagentoBackendDocumentationHistory;
use App\PostmanRequestCreate;
use App\SiteDevelopmentCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagentoBackendDocumentationController extends Controller
{
    public function magentoBackendeDocs(Request $request)
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
        $postManAPi = PostmanRequestCreate::select('request_url', 'id')->groupBy('request_url')->get();
        $magentoModules = MagentoModule::select('module', 'id')->groupBy('module')->get();

        if ($request->ajax()) {
            $items = MagentoBackendDocumentation::with('siteDevelopementCategory', 'postmamRequest', 'magentoModule', 'user')
                ->select(
                    'magento_backend_docs.*',
                    'magento_backend_docs.features',
                    'magento_backend_docs.bug',
                    'magento_backend_docs.bug_details',
                    'magento_backend_docs.bug_resolution',
                    'magento_backend_docs.updated_by'
                )
                ->join('site_development_categories', 'site_development_categories.id', '=', 'magento_backend_docs.site_development_category_id')
                ->join('postman_request_creates', 'postman_request_creates.id', '=', 'magento_backend_docs.post_man_api_id')
                ->join('magento_modules', 'magento_modules.id', '=', 'magento_backend_docs.mageneto_module_id');

            if (isset($request->search_features)) {
                $items->where('magento_backend_docs.features', 'LIKE', '%'.$request->search_features.'%');
            }
            if (isset($request->search_template_file)) {
                $items->where('magento_backend_docs.template_file', 'LIKE', '%'.$request->search_template_file.'%');
            }
            if (isset($request->search_bug_details)) {
                $items->where('magento_backend_docs.bug_details', 'LIKE', '%'.$request->search_bug_details.'%');
            }
            if (isset($request->search_template_file)) {
                $items->where('magento_backend_docs.template_file', 'LIKE', '%'.$request->search_template_file.'%');
            }
            if (isset($request->search_bug_solution)) {
                $items->where('magento_backend_docs.bug_resolution', 'LIKE', '%'.$request->search_bug_solution.'%');
            }
            if (isset($request->date)) {
                $items->where('magento_backend_docs.created_at', 'LIKE', '%'.$request->date.'%');
            }
            if (isset($request->postman_api)) {
                $items->whereIn('magento_backend_docs.post_man_api_id', $request->postman_api);
            }
            if (isset($request->categoryname)) {
                $items->whereIn('magento_backend_docs.site_development_category_id', $request->categoryname);
            }
            if (isset($request->modules)) {
                $items->whereIn('magento_backend_docs.mageneto_module_id', $request->modules);
            }

            return datatables()->eloquent($items)->addColumn('categories', $storecategories)->addColumn('postManAPi', $postManAPi)->addColumn('magentoModules', $magentoModules)->toJson();
        } else {
            return view('magento-backend-documentation.index', compact('storecategories', 'postManAPi', 'magentoModules'));
        }

    }

    public function getBackendDropdownDatas(Request $request): JsonResponse
    {
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
        $postManAPi = PostmanRequestCreate::select('request_url', 'id')->groupBy('request_url')->get();
        $magentoModules = MagentoModule::select('module', 'id')->groupBy('module')->get();

        return response()->json(['storecategories' => $storecategories, 'postManAPi' => $postManAPi, 'magentoModules' => $magentoModules]);
    }

    public function magentoBackendStore(Request $request): JsonResponse
    {
        $magentobackenddoc = new MagentoBackendDocumentation;
        $magentobackenddoc->site_development_category_id = $request->site_development_category;
        $magentobackenddoc->post_man_api_id = $request->post_man_api_id;
        $magentobackenddoc->mageneto_module_id = $request->mageneto_module_id;
        $magentobackenddoc->features = $request->features;
        $magentobackenddoc->bug = $request->bug;
        $magentobackenddoc->bug_details = $request->bug_details;
        $magentobackenddoc->bug_resolution = $request->bug_resolution;
        $magentobackenddoc->template_file = $request->template_file;
        $magentobackenddoc->read = $request->read ? implode(',', $request->read) : null;
        $magentobackenddoc->write = $request->write ? implode(',', $request->write) : null;

        $magentobackenddoc->updated_by = Auth::id();
        $magentobackenddoc->save();

        return response()->json([
            'status' => true,
            'data' => $magentobackenddoc,
            'message' => 'Magento Backend Documentation created successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendOptions(Request $request): JsonResponse
    {
        $oldData = MagentoBackendDocumentation::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoBackendDocumentation::where('id', (int) $request->id)->update([$request->columnName => $request->data, 'updated_by' => Auth::id()]);

        $columnname = $request->columnName;
        $newData = $request->data;
        $backendId = $request->id;

        if ($request->columnName == 'site_development_category_id') {
            $oldId = $oldData->site_development_category_id;
        }
        if ($request->columnName == 'post_man_api_id') {
            $oldId = $oldData->post_man_api_id;
        }
        if ($request->columnName == 'mageneto_module_id') {
            $oldId = $oldData->mageneto_module_id;
        }

        $this->magentoBackendHistorysave($backendId, $oldId, $newData, $columnname);

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

    public function magentoBackendCategoryHistoryShow(Request $request): JsonResponse
    {
        $histories = MagentoBackendDocumentationHistory::with(['siteDevelopementOldCategory', 'siteDevelopementNewCategory', 'user'])
            ->where('magento_backend_docs_id', $request->id)
            ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendPostmanHistoryShow(Request $request): JsonResponse
    {
        $histories = MagentoBackendDocumentationHistory::with(['postmanoldrequestapi', 'postmannewrequestapi', 'user'])
            ->where('magento_backend_docs_id', $request->id)
            ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendModuleHistoryShow(Request $request): JsonResponse
    {
        $histories = MagentoBackendDocumentationHistory::with(['magneteoldmodule', 'magnetenewmodule', 'user'])
            ->where('magento_backend_docs_id', $request->id)
            ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentobackendstoreRemark(Request $request): JsonResponse
    {
        $magentobackendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentobackendremark->api_remark;
        $magentobackendremark->api_remark = $request->remark;
        $magentobackendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->remark;
        $columnname = 'api_remark';

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentobackendremark,
            'message' => 'magneto backend Remark Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendRemarkHistoryDataShow(Request $request): JsonResponse
    {
        $histories = MagentoBackendDocumentationHistory::with(['user'])
            ->where('magento_backend_docs_id', $request->id)
            ->Where('column_name', $request->column)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackenddescriptionHistoryShow(Request $request): JsonResponse
    {
        $histories = MagentoBackendDocumentationHistory::with(['user'])
            ->where('magento_backend_docs_id', $request->id)
            ->Where('column_name', $request->column)
            ->whereNull('file_name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoStorebackendFolder(Request $request): JsonResponse
    {
        $magentofrontendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentofrontendremark->description;
        $magentofrontendremark->description = $request->description;
        $magentofrontendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->description;
        $columnname = 'description';

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto backend description Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoStorebackendadminConfig(Request $request): JsonResponse
    {
        $magentofrontendremark = MagentoBackendDocumentation::find($request->magento_back_end_id);
        $oldId = $magentofrontendremark->admin_configuration;
        $magentofrontendremark->admin_configuration = $request->adminconfig;
        $magentofrontendremark->save();

        $backendId = $request->magento_back_end_id;
        $newData = $request->adminconfig;
        $columnname = 'admin_configuration';

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_value = $oldId;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magentofrontendremark,
            'message' => 'magneto backend admin_configuration Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendDescriptionUpload(Request $request): JsonResponse
    {
        $validationRules = [
            'upload_description' => ['required'],
            'upload_description.*' => ['required'],
        ];

        $this->validate($request, $validationRules);

        $magentobackenddoc = MagentoBackendDocumentation::find($request->magento_backend_id);

        $backendId = $request->magento_backend_id;
        $newData = $magentobackenddoc->google_drive_file_id;
        $columnname = 'description';

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        if ($request->hasFile('upload_description')) {
            foreach ($request->upload_description as $file) {
                $magentobackenddoc->description_file_name = $file->getClientOriginalName();
                $magentobackenddoc->description_extension = $file->extension();

                $magentobackenddoc->save();

                MagnetoGoogledriveUpload::dispatchSync($magentobackenddoc, $file);

                $magnetohistory = MagentoBackendDocumentationHistory::find($magnetohistory->id);
                $magnetohistory->new_value = $magentobackenddoc->google_drive_file_id;
                $magnetohistory->file_name = $file->getClientOriginalName();
                $magnetohistory->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'magneto backend Added succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBackendadminConfigUpload(Request $request): JsonResponse
    {
        $validationRules = [
            'admin_config_image' => ['required'],
            'admin_config_image.*' => ['required'],
        ];

        $this->validate($request, $validationRules);

        $magentobackenddoc = MagentoBackendDocumentation::find($request->magento_backend_id);

        $backendId = $request->magento_backend_id;
        $newData = $magentobackenddoc->google_drive_file_id;
        $columnname = 'admin_configuration';

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->new_value = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        if ($request->hasFile('admin_config_image')) {
            foreach ($request->admin_config_image as $file) {
                $magentobackenddoc->admin_configuration_file_name = $file->getClientOriginalName();
                $magentobackenddoc->admin_configuration_extension = $file->extension();

                $magentobackenddoc->save();

                MagnetoGoogledriveUpload::dispatchSync($magentobackenddoc, $file);

                $magnetohistory->new_value = $magentobackenddoc->admin_config_google_drive_file_id;
                $magnetohistory->file_name = $file->getClientOriginalName();
                $magnetohistory->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'magneto backend Updated succesfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentobackenddelete($id): JsonResponse
    {
        $magenotobackend = MagentoBackendDocumentation::find($id);

        if (! $magenotobackend) {
            return response()->json(['message' => 'Magento backend not found.'], 404);
        }

        $magenotobackend->delete();

        return response()->json(['message' => 'Magento backend deleted successfully.']);
    }

    public function magentoBackendEdit($id): JsonResponse
    {
        $magento_module = MagentoBackendDocumentation::find($id);
        $storecategories = SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
        $postManAPi = PostmanRequestCreate::select('request_url', 'id')->groupBy('request_url')->get();
        $magentoModules = MagentoModule::select('module', 'id')->groupBy('module')->get();

        if ($magento_module) {
            return response()->json(['code' => 200, 'data' => $magento_module, 'storecategories' => $storecategories, 'postManAPi' => $postManAPi, 'magentoModules' => $magentoModules]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function magentoBackendUpdate(Request $request): JsonResponse
    {
        $oldData = MagentoBackendDocumentation::where('id', (int) $request->id)->first();
        $oldfeatures = $oldData->features;
        $old_bug_details = $oldData->bug_details;
        $old_template_file = $oldData->template_file;
        $old_bug_resolution = $oldData->bug_resolution;
        $old_description = $oldData->description;
        $old_adminConfiguration = $oldData->admin_configuration;

        $oldData->features = $request->features;
        $oldData->bug_details = $request->bug_details;
        $oldData->template_file = $request->template_file;
        $oldData->bug_resolution = $request->bug_resolution;
        $oldData->description = $request->description;
        $oldData->admin_configuration = $request->admin_configuration;
        $oldData->updated_by = Auth::id();
        $oldData->save();

        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $oldData->id;
        $magnetohistory->new_template_file = $request->template_file;
        $magnetohistory->new_features = $request->features;
        $magnetohistory->new_bug_details = $request->bug_details;
        $magnetohistory->new_bug_solutions = $request->bug_resolution;
        $magnetohistory->new_value = $request->admin_configuration;

        $magnetohistory->user_id = Auth::id();

        if ($old_adminConfiguration != $request->admin_configuration) {
            $magnetohistory->old_value = $old_adminConfiguration;
            $magnetohistory->column_name = 'admin_configuration';
            $magnetohistory->save();
        }

        if ($old_description != $request->description) {
            $magnetohistory->old_value = $old_description;
            $magnetohistory->column_name = 'description';
            $magnetohistory->save();
        }

        if ($oldfeatures != $request->features) {
            $magnetohistory->old_features = $oldfeatures;
            $magnetohistory->feature_type = 'features';
            $magnetohistory->save();
        }

        if ($old_bug_details != $request->bug_details) {
            $magnetohistory->old_bug_details = $old_bug_details;
            $magnetohistory->old_bug_type = 'BugDeatils';
            $magnetohistory->save();
        }

        if ($old_template_file != $request->template_file) {
            $magnetohistory->old_template_file = $old_template_file;
            $magnetohistory->template_file_type = 'TemplateFile';
            $magnetohistory->save();
        }

        if ($old_bug_resolution != $request->bug_resolution) {
            $magnetohistory->old_bug_solutions = $old_bug_resolution;
            $magnetohistory->old_bug_solutuion_type = 'BugResolution';
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

    public function magentoFeatureget(Request $request): JsonResponse
    {
        $remarks = MagentoBackendDocumentationHistory::with(['user'])->where('magento_backend_docs_id', $request->id)->where('feature_type', $request->type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoTemplateget(Request $request): JsonResponse
    {
        $remarks = MagentoBackendDocumentationHistory::with(['user'])->where('magento_backend_docs_id', $request->id)->where('template_file_type', $request->type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBugDetailget(Request $request): JsonResponse
    {
        $remarks = MagentoBackendDocumentationHistory::with(['user'])->where('magento_backend_docs_id', $request->id)->where('old_bug_type', $request->type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoBugSolutionget(Request $request): JsonResponse
    {
        $remarks = MagentoBackendDocumentationHistory::with(['user'])->where('magento_backend_docs_id', $request->id)->where('old_bug_solutuion_type', $request->type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getUploadedFilesList(Request $request): JsonResponse
    {
        try {
            $result = [];
            if (isset($request->id)) {
                $result = MagentoBackendDocumentationHistory::where('column_name', $request->type)
                    ->where('magento_backend_docs_id', $request->id)
                    ->whereNotNull('new_value')
                    ->orderByDesc('id')
                    ->get();

                if (isset($result) && count($result) > 0) {
                    $result = $result->toArray();
                }

                return response()->json([
                    'data' => view('magento-backend-documentation.description-google-drive-list', compact('result'))->render(),
                ]);
            } else {
                throw new Exception('Task not found');
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => view('magento-backend-documentation.description-google-drive-list', ['result' => null])->render(),
            ]);
        }
    }

    private function magentoBackendHistorysave($backendId, $oldId, $newData, $columnname)
    {
        $magnetohistory = new MagentoBackendDocumentationHistory;
        $magnetohistory->magento_backend_docs_id = $backendId;
        $magnetohistory->column_name = $columnname;
        $magnetohistory->old_id = $oldId;
        $magnetohistory->new_id = $newData;
        $magnetohistory->user_id = Auth::id();
        $magnetohistory->save();

        return response()->json([
            'status' => true,
            'data' => $magnetohistory,
            'message' => 'Magento Bcakend Documentation created successfully',
            'status_name' => 'success',
        ], 200);
    }
}
