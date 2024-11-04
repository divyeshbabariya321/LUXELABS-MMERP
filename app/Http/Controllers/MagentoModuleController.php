<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\Http\Requests\MagentoModule\MagentoModuleRemarkRequest;
use App\Http\Requests\MagentoModule\MagentoModuleRequest;
use App\Http\Requests\StoreM2ErrorStatusMagentoModuleRequest;
use App\Http\Requests\StoreUnitTestStatusMagentoModuleRequest;
use App\Http\Requests\StoreVerifiedStatusMagentoModuleRequest;
use App\Jobs\SyncMagentoModules;
use App\MagentoModule;
use App\MagentoModuleApiValueHistory;
use App\MagentoModuleCategory;
use App\MagentoModuleHistory;
use App\MagentoModuleLocation;
use App\MagentoModuleLogs;
use App\MagentoModuleM2ErrorAssigneeHistory;
use App\MagentoModuleRemark;
use App\MagentoModuleType;
use App\MagentoModuleVerifiedBy;
use App\MagentoModuleVerifiedStatus;
use App\MagentoModuleVerifiedStatusHistory;
use App\MagnetoLocationHistory;
use App\MagnetoReviewStandardHistory;
use App\Models\ColumnVisbility;
use App\Models\DataTableColumn;
use App\Models\MagentoModuleDependency;
use App\Models\MagentoModuleM2ErrorStatus;
use App\Models\MagentoModuleM2ErrorStatusHistory;
use App\Models\MagentoModuleM2RemarkHistory;
use App\Models\MagentoModuleReturnTypeErrorHistoryStatus;
use App\Models\MagentoModuleReturnTypeErrorStatus;
use App\Models\MagentoModuleUnitTestRemarkHistory;
use App\Models\MagentoModuleUnitTestStatus;
use App\Models\MagentoModuleUnitTestStatusHistory;
use App\Models\MagentoModuleUnitTestUserHistory;
use App\StoreWebsite;
use App\TaskStatus;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MagentoModuleController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view = 'magento_module.index';
        $this->create_view = 'magento_module.create';
        $this->detail_view = 'magento_module.details';
        $this->edit_view = 'magento_module.edit';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        if (config('settings.production')) {
            $users = User::select('name', 'id')->role('Developer')->orderBy('name')->where('is_active', 1)->get();
        } else {
            $users = User::select('name', 'id')->where('is_active', 1)->orderBy('name')->get();
        }
        $module_categories = MagentoModuleCategory::select('category_name', 'id')->where('status', 1)->get();
        $module_locations = MagentoModuleLocation::select('magento_module_locations', 'id')->get();
        $module_return_type_statuserrors = MagentoModuleReturnTypeErrorStatus::select('return_type_name', 'id')->get();
        $magento_module_types = MagentoModuleType::select('magento_module_type', 'id')->get();
        $task_statuses = TaskStatus::select('name', 'id')->get();
        $store_websites = StoreWebsite::select('website', 'id')->get();
        $verified_status = MagentoModuleVerifiedStatus::select('name', 'id', 'color')->get();
        $verified_status_array = $verified_status->pluck('name', 'id');
        $m2_error_status = MagentoModuleM2ErrorStatus::select('m2_error_status_name', 'id')->get();
        $m2_error_status_array = $m2_error_status->pluck('m2_error_status_name', 'id');
        $unit_test_status = MagentoModuleUnitTestStatus::select('unit_test_status_name', 'id')->get();
        //get column visbilities
        $columns = ColumnVisbility::select('columns')->where('user_id', auth()->user()->id)->first();
        $hideColumns = $columns->columns ?? '';

        $moduleNames = MagentoModule::with(['lastRemark'])
            ->join('magento_module_categories', 'magento_module_categories.id', 'magento_modules.module_category_id')
            ->leftjoin('magento_module_locations', 'magento_module_locations.id', 'magento_modules.magneto_location_id')
            ->leftjoin('magento_module_return_type_error_status', 'magento_module_return_type_error_status.id', 'magento_modules.return_type_error_status')
            ->join('magento_module_types', 'magento_module_types.id', 'magento_modules.module_type')
            ->join('store_websites', 'store_websites.id', 'magento_modules.store_website_id')
            ->leftjoin('users', 'users.id', 'magento_modules.developer_name')
            ->leftJoin('task_statuses', 'task_statuses.id', 'magento_modules.task_status')
            ->groupBy('magento_modules.module')
            ->pluck('module', 'module')
            ->toArray();

        $title = 'Magento Module';
        $users = $users->pluck('name', 'id');
        $module_categories = $module_categories->pluck('category_name', 'id');
        $module_locations = $module_locations->pluck('magento_module_locations', 'id');
        $magento_module_types = $magento_module_types->pluck('magento_module_type', 'id');
        $task_statuses = $task_statuses->pluck('name', 'id');
        $store_websites = $store_websites->pluck('website', 'id');
        $module_return_type_statuserrors = $module_return_type_statuserrors->pluck('return_type_name', 'id');

        return view($this->index_view, compact('title', 'module_categories', 'magento_module_types', 'task_statuses', 'store_websites', 'users', 'verified_status', 'verified_status_array', 'm2_error_status', 'm2_error_status_array', 'moduleNames', 'module_locations', 'module_return_type_statuserrors', 'unit_test_status', 'hideColumns'));
    }

    public function indexPost(Request $request)
    {
        if (config('settings.production')) {
            $users = User::select('name', 'id')->role('Developer')->orderBy('name')->where('is_active', 1)->get();
        } else {
            $users = User::select('name', 'id')->where('is_active', 1)->orderBy('name')->get();
        }

        $module_categories = MagentoModuleCategory::select('category_name', 'id')->where('status', 1)->get();
        $module_locations = MagentoModuleLocation::select('magento_module_locations', 'id')->get();
        $module_return_type_statuserrors = MagentoModuleReturnTypeErrorStatus::select('return_type_name', 'id')->get();
        $magento_module_types = MagentoModuleType::select('magento_module_type', 'id')->get();
        $store_websites = StoreWebsite::select('website', 'id')->get();
        $verified_status = MagentoModuleVerifiedStatus::select('name', 'id', 'color')->get();
        $m2_error_status = MagentoModuleM2ErrorStatus::select('m2_error_status_name', 'id')->get();
        $m2_error_status_array = $m2_error_status->pluck('m2_error_status_name', 'id');
        $unit_test_status = MagentoModuleUnitTestStatus::select('unit_test_status_name', 'id')->get();

        //get column visbilities
        $columns = ColumnVisbility::select('columns')->where('user_id', auth()->user()->id)->first();
        $hideColumns = $columns->columns ?? '';

        $items = MagentoModule::with(['lastRemark'])
            ->leftjoin('magento_module_categories', 'magento_module_categories.id', 'magento_modules.module_category_id')
            ->leftjoin('magento_module_locations', 'magento_module_locations.id', 'magento_modules.magneto_location_id')
            ->leftjoin('magento_module_return_type_error_status', 'magento_module_return_type_error_status.id', 'magento_modules.return_type_error_status')
            ->leftjoin('magento_module_types', 'magento_module_types.id', 'magento_modules.module_type')
            ->join('store_websites', 'store_websites.id', 'magento_modules.store_website_id')
            ->leftjoin('users', 'users.id', 'magento_modules.developer_name')
            ->leftJoin('task_statuses', 'task_statuses.id', 'magento_modules.task_status')
            ->select(
                'magento_modules.*',
                'magento_module_categories.category_name',
                'magento_module_locations.magento_module_locations',
                'magento_module_return_type_error_status.return_type_name',
                'task_statuses.name as task_name',
                'store_websites.website',
                'store_websites.title',
                'users.name as developer_name1',
                'users.id as developer_id'
            )
            ->orderByDesc('magento_modules.module_review_standard');

        if (isset($request->module) && ! empty($request->module)) {
            $items->where('magento_modules.module', 'Like', '%'.$request->module.'%');
        }

        if (isset($request->user_id) && ! empty($request->user_id)) {
            $items->where('users.user_id', $request->user_id);
        }

        if (isset($request->store_website_id) && ! empty($request->store_website_id)) {
            $items->where('magento_modules.store_website_id', $request->store_website_id);
        }

        if (isset($request->module_type) && ! empty($request->module_type)) {
            $items->where('magento_modules.module_type', $request->module_type);
        }

        if (isset($request->task_status) && ! empty($request->task_status)) {
            $items->where('magento_modules.task_status', $request->task_status);
        }

        if (isset($request->is_customized)) {
            $items->where('magento_modules.is_customized', $request->is_customized);
        }

        if (isset($request->module_category_id) && ! empty($request->module_category_id)) {
            $items->where('magento_modules.module_category_id', $request->module_category_id);
        }
        if (isset($request->site_impact)) {
            $items->where('magento_modules.site_impact', $request->site_impact);
        }

        if (isset($request->modules_status)) {
            $items->where('magento_modules.status', $request->modules_status);
        }
        if (isset($request->dev_verified_by)) {
            $items->whereIn('magento_modules.dev_verified_by', $request->dev_verified_by);
        }
        if (isset($request->lead_verified_by)) {
            $items->whereIn('magento_modules.lead_verified_by', $request->lead_verified_by);
        }
        if (isset($request->dev_verified_status_id)) {
            $items->whereIn('magento_modules.dev_verified_status_id', $request->dev_verified_status_id);
        }
        if (isset($request->lead_verified_status_id)) {
            $items->whereIn('magento_modules.lead_verified_status_id', $request->lead_verified_status_id);
        }
        if (isset($request->return_type_error_status)) {
            $items->where('magento_modules.return_type_error_status', $request->return_type_error_status);
        }
        if (isset($request->m2_error_status_id)) {
            $items->whereIn('magento_modules.m2_error_status_id', $request->m2_error_status_id);
        }

        $items->groupBy('magento_modules.module');

        return datatables()->eloquent($items)->addColumn('m_types', $magento_module_types)->addColumn('developer_list', $users)->addColumn('categories', $module_categories)->addColumn('website_list', $store_websites)->addColumn('verified_status', $verified_status)->addColumn('m2_error_status', $m2_error_status)->addColumn('locations', $module_locations)->addColumn('module_return_type_statuserrors', $module_return_type_statuserrors)->addColumn('m2_error_status_array', $m2_error_status_array)->addColumn('unit_test_status', $unit_test_status)->addColumn('hideColumns', $hideColumns)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = 'Magento Module';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $module_locations = MagentoModuleLocation::pluck('magento_module_locations', 'id');

        $magento_module_types = MagentoModuleType::get()->pluck('magento_module_type', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses', 'magento_module_types', 'module_locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MagentoModuleRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = MagentoModule::create($input);

        if ($data) {
            $input_data = $data->toArray();
            $input_data['magento_module_id'] = $data->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            MagentoModuleHistory::create($input_data);

            // New Script
            $moduleName = $data->module;
            $website = $data->store_website->title;
            $server = $data->store_website->server_ip;
            $rootDir = $data->store_website->working_directory;
            $websiteStoreProjectName = $data->store_website->websiteStoreProject->name ?? null;
            $action = 'add';
            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

            $cmd = "bash $scriptsPath"."sync-magento-modules.sh -w \"$website\" -s \"$server\" -d \"$rootDir\" -m \"$moduleName\" -g \"$websiteStoreProjectName\" -a \"$action\" 2>&1";
            if (empty($website) || empty($server) || empty($rootDir) || empty($websiteStoreProjectName)) {
                MagentoModuleLogs::create(['magento_module_id' => $data->id, 'store_website_id' => $data->store_website_id, 'updated_by' => $input_data['user_id'], 'command' => $cmd, 'status' => 'Error', 'response' => 'Parameter is missing in command']);

                $return_data[] = ['code' => 500, 'message' => 'The response is not found!', 'store_website_id' => $data->store_website_id, 'magento_module_id' => $data->id];
                Log::info('magentoModuleUpdateStatus output is not set:'.print_r($return_data, true));
            }
            // NEW Script

            exec($cmd, $output, $return_var);
            Log::info('store command:'.$cmd);
            Log::info('store output:'.print_r($output, true));
            Log::info('store return_var:'.$return_var);

            if (! isset($output[0])) {
                MagentoModuleLogs::create(['magento_module_id' => $data->id, 'store_website_id' => $data->store_website_id, 'updated_by' => $input_data['user_id'], 'command' => $cmd, 'status' => 'Error', 'response' => json_encode($output)]);

                $return_data[] = ['code' => 500, 'message' => 'The response is not found!', 'store_website_id' => $data->store_website_id, 'magento_module_id' => $data->id];
                Log::info('magentoModuleUpdateStatus output is not set:'.print_r($return_data, true));
            }

            $response = json_decode($output[0]);
            if (isset($response->status) && ($response->status == 'true' || $response->status)) {
                $message = 'Magento module status change successfully';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoModuleLogs::create(['magento_module_id' => $data->id, 'store_website_id' => $data->store_website_id, 'updated_by' => $input_data['user_id'], 'command' => $cmd, 'status' => 'Success', 'response' => json_encode($output)]);

                $return_data[] = ['code' => 200, 'message' => $message, 'store_website_id' => $data->store_website_id, 'magento_module_id' => $data->id];
            } else {
                $message = 'Something Went Wrong! Please check Logs for more details';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoModuleLogs::create(['magento_module_id' => $data->id, 'store_website_id' => $data->store_website_id, 'updated_by' => $input_data['user_id'], 'command' => $cmd, 'status' => 'Error', 'response' => json_encode($output)]);

                $return_data[] = ['code' => 500, 'message' => $message, 'store_website_id' => $data->store_website_id, 'magento_module_id' => $data->id];
            }

            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Magento Module saved successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoModule $magento_module)
    {
        $title = 'Magento Module Details';

        if (request()->ajax() && $magento_module) {
            return response()->json([
                'data' => view('magento_module.partials.data', compact('magento_module'))->render(),
                'title' => $title,
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'data' => '',
                'title' => $title,
                'code' => 500,
            ], 500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(MagentoModule $magento_module): View
    {
        $title = 'Magento Module';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $module_locations = MagentoModuleLocation::pluck('module_locations', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');
        $module_return_type_statuserrors = MagentoModuleReturnTypeErrorStatus::pluck('return_type_name', 'id')->get();

        return view($this->edit_view, compact('module_categories', 'title', 'magento_module', 'task_statuses', 'module_locations', 'module_return_type_statuserrors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MagentoModuleRequest $request, MagentoModule $magento_module): JsonResponse
    {
        $input = $request->except(['_token']);
        $data = $magento_module->update($input);

        if ($data) {
            $input_data = $magento_module->toArray();
            $input_data['magento_module_id'] = $magento_module->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            MagentoModuleHistory::create($input_data);

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MagentoModule $magento_module): JsonResponse
    {
        $data = $magento_module->delete();

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeRemark(MagentoModuleRemarkRequest $request): JsonResponse
    {
        $input = $request->except(['_token']);
        $input['user_id'] = Auth::user()->id;

        $magento_module_remark = MagentoModuleRemark::create($input);

        $message = 'Remark';
        if ($magento_module_remark) {
            if ($input['type'] == 'general') {
                MagentoModule::where('id', $request->magento_module_id)->update(['last_message' => $request->remark]);
            }
            if ($input['type'] == 'dev') {
                $message = 'Developer Remark';
                MagentoModule::where('id', $request->magento_module_id)->update(['dev_last_remark' => $request->remark]);
            }
            if ($input['type'] == 'lead') {
                $message = 'Lead Remark';
                MagentoModule::where('id', $request->magento_module_id)->update(['lead_last_remark' => $request->remark]);
            }
            if ($input['type'] == 'return_type_error') {
                $message = 'Return Type Error';
                MagentoModule::where('id', $request->magento_module_id)->update(['return_type_error' => $request->remark]);
            }
            if ($input['type'] == 'return_type_error_status') {
                $message = 'Return Type Error Status';
                MagentoModule::where('id', $request->magento_module_id)->update(['return_type_error_status' => $request->remark]);
            }

            return response()->json([
                'status' => true,
                'message' => "{$message} added successfully",
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => "{$message} added unsuccessfully",
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $magento_module
     * @param  mixed  $type
     */
    public function getRemarks($magento_module, $type = 'general'): JsonResponse
    {
        $remarks = MagentoModuleRemark::with(['user'])->where('magento_module_id', $magento_module)->where('type', $type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getApiValueHistories($magento_module): JsonResponse
    {
        $histories = MagentoModuleApiValueHistory::with(['user'])->where('magento_module_id', $magento_module)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get verified status',
            'status_name' => 'success',
        ], 200);
    }

    public function getM2ErrorStatusHistories($magento_module): JsonResponse
    {
        $histories = MagentoModuleM2ErrorStatusHistory::with(['user', 'newM2ErrorStatus', 'oldM2ErrorStatus'])->where('magento_module_id', $magento_module)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get verified status',
            'status_name' => 'success',
        ], 200);
    }

    public function getVerifiedStatusHistories($magento_module, $type): JsonResponse
    {
        $histories = MagentoModuleVerifiedStatusHistory::with(['user', 'newStatus', 'oldStatus'])->where('magento_module_id', $magento_module)->where('type', $type)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get verified status',
            'status_name' => 'success',
        ], 200);
    }

    public function updateMagentoModuleOptions(Request $request): JsonResponse
    {
        $oldData = MagentoModule::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoModule::where('id', (int) $request->id)->update([$request->columnName => $request->data]);
        $newData = MagentoModule::where('id', (int) $request->id)->first();

        $input_data = $newData->toArray();
        $input_data['magento_module_id'] = $newData->id;
        unset($input_data['id']);
        $input_data['user_id'] = auth()->user()->id;
        MagentoModuleHistory::create($input_data);

        if ($request->columnName == 'dev_verified_status_id' || $request->columnName == 'lead_verified_status_id') {
            if ($request->columnName == 'dev_verified_status_id') {
                $type = 'dev';
                $oldStatusId = $oldData->dev_verified_status_id;
            }
            if ($request->columnName == 'lead_verified_status_id') {
                $type = 'lead';
                $oldStatusId = $oldData->lead_verified_status_id;
            }
            $this->saveVerifiedStatusHistory($oldData, $oldStatusId, $request->data, $type);
        }

        if ($request->columnName == 'dev_verified_by' || $request->columnName == 'lead_verified_by') {
            if ($request->columnName == 'dev_verified_by') {
                $type = 'dev';
                $oldStatusId = $oldData->dev_verified_by;
            }
            if ($request->columnName == 'lead_verified_by') {
                $type = 'lead';
                $oldStatusId = $oldData->lead_verified_by;
            }

            $this->saveVerifiedByHistory($oldData, $oldStatusId, $request->data, $type);
        }
        if ($request->columnName == 'magneto_location_id') {
            $oldStatusId = $oldData->magneto_location_id;

            $this->saveLocationHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'module_review_standard') {
            $this->savereviewStandard($oldData, $request->data);
        }

        if ($request->columnName == 'return_type_error_status') {
            $oldStatusId = $oldData->return_type_error_status;
            $this->saveReturnTypeHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'm2_error_status_id') {
            $oldStatusId = $oldData->m2_error_status_id;
            $this->saveM2ErrorStatusHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'm2_error_assignee') {
            $oldStatusId = $oldData->m2_error_assignee;
            $this->saveM2ErrorAssigneeHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'unit_test_status_id') {
            $oldStatusId = $oldData->unit_test_status_id;
            $this->saveUnitTeststatusHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'unit_test_user_id') {
            $oldStatusId = $oldData->unit_test_user_id;
            $this->saveUnitTestUserHistory($oldData, $oldStatusId, $request->data);
        }

        if ($request->columnName == 'api') {
            $history = new MagentoModuleApiValueHistory;
            $history->magento_module_id = $request->id;
            $history->old_value = $oldData->api;
            $history->new_value = $request->data;
            $history->user_id = Auth::user()->id;
            $history->save();
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

    protected function savereviewStandard($magentoModule, $reviewValue)
    {
        $history = new MagnetoReviewStandardHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->review_standard = $reviewValue;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveReturnTypeHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagentoModuleReturnTypeErrorHistoryStatus;
        $history->magento_module_id = $magentoModule->id;
        $history->old_location_id = $oldStatusId;
        $history->new_location_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveM2ErrorStatusHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagentoModuleM2ErrorStatusHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_m2_error_status_id = $oldStatusId;
        $history->new_m2_error_status_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    public function magentoModuleList(Request $request): View
    {
        $all_store_websites = StoreWebsite::where('website_source', 'magento')->pluck('title', 'id')->toArray();

        $selecteStoreWebsites = ['151', '152', '153', '154'];

        if (isset($request->store_webs) && $request->store_webs) {
            $selecteStoreWebsites = $request->store_webs;

            $storeWebsites = StoreWebsite::where('website_source', 'magento')->whereIn('id', $request->store_webs)->pluck('title', 'id')->toArray();
        } else {
            // Default QA store websites will select
            $storeWebsites = StoreWebsite::where('website_source', 'magento')->whereIn('id', $selecteStoreWebsites)->pluck('title', 'id')->toArray();
        }

        if (! empty($request->store_webs)) {
            $magento_modules_check = MagentoModule::groupBy('module')->get();

            if (! empty($magento_modules_check)) {
                foreach ($magento_modules_check as $value) {
                    foreach ($request->store_webs as $valueStoreWebsite) {
                        $mmInCheckStoreWebsite = MagentoModule::where('module', $value->module)->where('store_website_id', $valueStoreWebsite)->select('id')->first();

                        if (empty($mmInCheckStoreWebsite)) {
                            $mm_create = [];
                            $mm_create['store_website_id'] = $valueStoreWebsite;
                            $mm_create['module_category_id'] = $value['module_category_id'];
                            $mm_create['module'] = $value['module'];
                            $mm_create['module_description'] = $value['module_description'];
                            $mm_create['current_version'] = $value['current_version'];
                            $mm_create['module_type'] = $value['module_type'];
                            $mm_create['status'] = 0;
                            $mm_create['payment_status'] = $value['payment_status'];
                            $mm_create['developer_name'] = $value['developer_name'];
                            $mm_create['dev_verified_by'] = $value['dev_verified_by'];
                            $mm_create['dev_verified_status_id'] = $value['dev_verified_status_id'];
                            $mm_create['lead_verified_by'] = $value['lead_verified_by'];
                            $mm_create['lead_verified_status_id'] = $value['lead_verified_status_id'];
                            $mm_create['created_at'] = $value['created_at'];
                            $mm_create['updated_at'] = $value['updated_at'];
                            $mm_create['last_message'] = $value['last_message'];
                            $mm_create['dev_last_remark'] = $value['dev_last_remark'];
                            $mm_create['lead_last_remark'] = $value['lead_last_remark'];
                            $mm_create['cron_time'] = $value['cron_time'];
                            $mm_create['task_status'] = $value['task_status'];
                            $mm_create['is_sql'] = $value['is_sql'];
                            $mm_create['is_third_party_plugin'] = $value['is_third_party_plugin'];
                            $mm_create['is_third_party_js'] = $value['is_third_party_js'];
                            $mm_create['is_js_css'] = $value['is_js_css'];
                            $mm_create['api'] = $value['api'];
                            $mm_create['cron_job'] = $value['cron_job'];
                            $mm_create['site_impact'] = $value['site_impact'];
                            $mm_create['dependency'] = $value['dependency'];
                            $mm_create['composer'] = $value['composer'];
                            $mm_create['magneto_location_id'] = $value['magneto_location_id'];
                            $mm_create['module_review_standard'] = $value['module_review_standard'];
                            $mm_create['used_at'] = $value['used_at'];
                            $mm_create['return_type_error'] = $value['return_type_error'];
                            $mm_create['return_type_error_status'] = $value['return_type_error_status'];
                            $mm_create['magento_dependency'] = $value['magento_dependency'];
                            $mm_create['m2_error_status_id'] = $value['m2_error_status_id'];
                            $mm_create['m2_error_assignee'] = $value['m2_error_assignee'];
                            $mm_create['m2_error_remark'] = $value['m2_error_remark'];
                            $mm_create['unit_test_status_id'] = $value['unit_test_status_id'];
                            $mm_create['unit_test_remark'] = $value['unit_test_remark'];
                            $mm_create['unit_test_user_id'] = $value['unit_test_user_id'];
                            MagentoModule::create($mm_create);
                        }
                    }
                }
            }
        }

        // For Filter
        $allMagentoModules = MagentoModule::orderBy('module')->pluck('module', 'module')->toArray();

        $magento_modules = MagentoModule::orderBy('module');

        if (isset($request->module_name) && $request->module_name != '') {
            $magento_modules = $magento_modules->where('module', 'Like', '%'.$request->module_name.'%');
        }

        $magento_modules_array = $magento_modules->get()->toArray();

        $magento_modules = $magento_modules->groupBy('module')->get();

        $magento_modules_count = $magento_modules->count();

        $result = [];
        array_walk($magento_modules_array, function ($value, $key) use (&$result) {
            $result[$value['store_website_id']][] = $value;
        });

        $magento_modules_array = $result;

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'magento-modules-sync_logs')->first();
        $dynamicColumnsToShow = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShow = json_decode($hideColumns, true);
        }

        return view('magento_module.magento-listing', ['all_store_websites' => $all_store_websites, 'selecteStoreWebsites' => $selecteStoreWebsites, 'magento_modules' => $magento_modules, 'storeWebsites' => $storeWebsites, 'magento_modules_array' => $magento_modules_array, 'magento_modules_count' => $magento_modules_count, 'allMagentoModules' => $allMagentoModules, 'dynamicColumnsToShow' => $dynamicColumnsToShow]);
    }

    public function magentoModuleListLogs(Request $request): View
    {
        $allMagentoModules = MagentoModule::pluck('module', 'module')->toArray();

        $magento_modules = MagentoModuleLogs::select('magento_modules.module', 'magento_module_logs.*')->leftJoin('magento_modules', 'magento_modules.id', 'magento_module_logs.magento_module_id')->orderByDesc('id');

        if (isset($request->module_name_sync) && $request->module_name_sync) {
            $magento_modules = $magento_modules->where('module', 'LIKE', '%'.$request->module_name_sync.'%');
        }

        if (isset($request->selected_date) && $request->selected_date) {
            $magento_modules = $magento_modules->whereDate('magento_module_logs.created_at', '=', $request->selected_date);
        }

        $magento_modules = $magento_modules->paginate(10);

        $magento_modules_count = MagentoModuleLogs::count();

        return view('magento_module.magento-listing_logs', ['magento_modules' => $magento_modules, 'magento_modules_count' => $magento_modules_count, 'allMagentoModules' => $allMagentoModules])
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function magentoModuleListLogsAjax(Request $request): JsonResponse
    {
        $magento_modules_q = new MagentoModuleLogs;
        $perPage = 2;

        if (isset($request->module_name_sync) && $request->module_name_sync) {
            $magento_modules_q = $magento_modules_q->where('module', 'LIKE', '%'.$request->module_name_sync.'%');
        }

        if (isset($request->selected_date) && $request->selected_date) {
            $magento_modules_q = $magento_modules_q->whereDate('magento_module_logs.created_at', '=', $request->selected_date);
        }

        $magento_modules_q = $magento_modules_q->select('module', 'magento_module_logs.*')->leftJoin('magento_modules', 'magento_modules.id', 'magento_module_logs.magento_module_id')->orderByDesc('magento_module_logs.id')
            ->paginate($perPage);

        return response()->json(['code' => 200, 'data' => $magento_modules_q, 'message' => 'Listed successfully!!!']);
    }

    public function magentoModuleListLogsAjax_bk(Request $request): JsonResponse
    {
        $magento_modules_q = MagentoModuleLogs::select('module', 'magento_module_logs.*')->leftJoin('magento_modules', 'magento_modules.id', 'magento_module_logs.magento_module_id')->orderByDesc('magento_module_logs.id');

        if (isset($request->module_name_sync) && $request->module_name_sync) {
            $magento_modules_q = $magento_modules_q->where('module', 'LIKE', '%'.$request->module_name_sync.'%');
        }

        $magento_modules = $magento_modules_q->get();

        return response()->json([
            'tbody' => view('magento_module.partials.sync-logs-modal-html', compact('magento_modules'))->render(),
            'count' => $magento_modules->count(),
        ]);
    }

    public function magentoModuleUpdateStatuslogs(Request $request): JsonResponse
    {
        $store_website_id = $request->store_website_id;
        $magento_module_id = $request->magento_module_id;

        $histories = MagentoModuleLogs::select('magento_module_logs.*', 'u.name AS userName')->leftJoin('users AS u', 'u.id', 'magento_module_logs.updated_by')->where('magento_module_id', $magento_module_id)->where('store_website_id', $store_website_id)->latest()->get();

        foreach ($histories as $logs) {
            if ($logs->store_website_id != '' && $logs->job_id != '') {
                $storeWebsite = StoreWebsite::where('id', $logs->store_website_id)->first();
                $assetsmanager = new AssetsManager;
                if ($storeWebsite) {
                    $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
                }

                if ($assetsmanager && $assetsmanager->client_id != '') {
                    $client_id = $assetsmanager->client_id;
                    $job_id = $logs->job_id;
                    $url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/'.$client_id.'/commands/'.$job_id;
                    $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                    $headers = [];
                    $headers[] = 'Authorization: Basic '.$key;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    Log::info('API Response: '.$result);
                    if (isset($response->data) && isset($response->data->result)) {
                        $logs->status = $response->data->status;
                        $result = $response->data->result;
                        $message = '';
                        if (isset($result->stdout) && $result->stdout != '') {
                            $message .= 'Output: '.$result->stdout;
                        }
                        if (isset($result->stderr) && $result->stderr != '') {
                            $message .= 'Error: '.$result->stderr;
                        }
                        if (isset($result->summary) && $result->summary != '') {
                            $message .= 'summary: '.$result->summary;
                        }
                        if ($message != '') {
                            $logs->response = $message;
                        }
                    }

                    curl_close($ch);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function runMagentoCacheFlushCommand($magento_module_id, $store_website_id, $client_id, $cwd)
    {
        $updated_by = auth()->user()->id;
        $cmd = 'bin/magento cache:flush';
        Log::info('Start cache:flush');

        $website = StoreWebsite::where('id', $store_website_id)->first();

        $url = getenv('MAGENTO_COMMAND_API_URL');
        $key = base64_encode('admin:86286706-032e-44cb-981c-588224f80a7d');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $parameters = [
            'command' => $cmd,
            'dir' => $cwd,
            'is_sudo' => true,
            'timeout_sec' => 300,
            'server' => $website->server_ip,
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

        $headers = [];
        $headers[] = 'Authorization: Basic '.$key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        curl_getinfo($ch, CURLINFO_HTTP_CODE);
        Log::info('API result: '.$result);
        Log::info('API Error Number: '.curl_errno($ch));
        if (curl_errno($ch)) {
            Log::info('API Error: '.curl_error($ch));
            MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => curl_error($ch)]);
        }
        $response = json_decode($result);

        curl_close($ch);

        if (isset($response->errors)) {
            $message = '';
            foreach ($response->errors as $error) {
                $message .= ' '.$error->code.':'.$error->title.':'.$error->detail;
            }
            MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => $message]);
            Log::info($message);
        } else {
            if (isset($response->data) && isset($response->data->jid)) {
                $job_id = $response->data->jid;
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Success', 'response' => 'Success', 'job_id' => $job_id]);
                Log::info('Job Id:'.$job_id);
            } else {
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => 'Job Id not found in response']);

                Log::info('Job Id not found in response!');
            }
        }

        Log::info('End cache:flush');

        return true;
    }

    public function syncModules(Request $request): RedirectResponse
    {
        Log::info('Database name.'.DB::connection()->getDatabaseName());
        Log::info('########## syncModules started ##########');

        Log::info('########## Database Host in env : '.config('settings.db_host'));

        Log::info('########## Database Name in env : '.config('settings.db_database'));

        Log::info('########## Database User in env: '.config('settings.db_username'));

        Log::info('########## Database Password in env: '.config('settings.db_password'));
        if ($request->has('store_website_id') && $request->store_website_id != '') {
            Log::info('selected websites:'.print_r($request->store_website_id, true));
            $updated_by = auth()->user()->id;
            $storeWebsites = StoreWebsite::whereIn('id', $request->store_website_id)->get();
            Log::info('Database name after StoreWebsite.'.DB::connection()->getDatabaseName());
            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

            foreach ($storeWebsites as $storeWebsite) {
                SyncMagentoModules::dispatch($storeWebsite, $scriptsPath, $updated_by)->onQueue('syncmagentomodules');
            }

            Log::info('########## syncModules end ##########');

            return redirect()->route('magento_module_listing')->with('success', 'Sync process started in JOB, Please check the logs for more details');
        }

        Log::info('########## syncModules end ##########');

        return redirect()->route('magento_module_listing')->with('error', 'Please select the store website!');
    }

    public function magentoModuleUpdateStatus(Request $request): JsonResponse
    {
        $store_website_id = $request->store_website_id;
        $magento_module_id = $request->magento_module_id;
        $status = $request->status;
        $magento_modules = MagentoModule::where('id', $magento_module_id)->where('store_website_id', $store_website_id)->first();
        if (! $magento_modules) {
            return response()->json(['code' => 500, 'message' => 'The Magento module is not found on the store website!']);
        }
        $storeWebsite = StoreWebsite::where('id', $store_website_id)->first();
        if ($storeWebsite->parent_id) {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
        } else {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
        }
        $updated_by = auth()->user()->id;
        $cmd = 'bin/magento module:disable '.$magento_modules->module;

        if ($status) {
            $cmd = 'bin/magento module:enable '.$magento_modules->module;
        }
        $search_module = $magento_modules->module;
        $cmd .= ' && bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento cache:flush';
        $return_data = [];
        foreach ($allInstances as $storeWebsite) {
            Log::info('Start Magento module change status');
            Log::info('Store Website:'.$storeWebsite->id);
            $store_website_id = $storeWebsite->id;
            $magento_modules = MagentoModule::where('module', $search_module)->where('store_website_id', $store_website_id)->first();
            if (! $magento_modules) {
                Log::info($search_module.' is not found in store website');

                continue;
            }
            $magento_module_id = $magento_modules->id;
            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');
            $moduleName = $magento_modules->module;

            // New Script
            $website = $storeWebsite->title;
            $server = $storeWebsite->server_ip;
            $rootDir = $storeWebsite->working_directory;
            $websiteStoreProjectName = $storeWebsite->websiteStoreProject->name ?? null;
            $action = 'disable';
            if ($status) {
                $action = 'enable';
            }

            $cmd = "bash $scriptsPath"."sync-magento-modules.sh -w \"$website\" -s \"$server\" -d \"$rootDir\" -m \"$moduleName\" -g \"$websiteStoreProjectName\" -a \"$action\" 2>&1";
            if (empty($website) || empty($server) || empty($rootDir) || empty($websiteStoreProjectName)) {
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => 'Parameter is missing in command']);

                $return_data[] = ['code' => 500, 'message' => 'The response is not found!', 'store_website_id' => $store_website_id, 'magento_module_id' => $magento_module_id];
                Log::info('magentoModuleUpdateStatus output is not set:'.print_r($return_data, true));

                continue;
            }
            exec($cmd, $output, $return_var);
            Log::info('magentoModuleUpdateStatus command:'.$cmd);
            Log::info('magentoModuleUpdateStatus output:'.print_r($output, true));
            Log::info('magentoModuleUpdateStatus return_var:'.$return_var);

            // [0] => {"status":"success"}
            if (! isset($output[0])) {
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => json_encode($output)]);

                $return_data[] = ['code' => 500, 'message' => 'The response is not found!', 'store_website_id' => $store_website_id, 'magento_module_id' => $magento_module_id];
                Log::info('magentoModuleUpdateStatus output is not set:'.print_r($return_data, true));

                continue;
            }

            $response = json_decode($output[0]);
            if (isset($response->status) && ($response->status == 'success' || $response->status)) {
                $message = 'Magento module status change successfully';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Success', 'response' => json_encode($output)]);
                $magento_modules->status = $status;
                $magento_modules->save();

                $return_data[] = ['code' => 200, 'message' => $message, 'store_website_id' => $store_website_id, 'magento_module_id' => $magento_module_id];

                continue;
            } else {
                $message = 'Something Went Wrong! Please check Logs for more details';
                if (isset($response->message) && $response->message != '') {
                    $message = $response->message;
                }
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => json_encode($output)]);

                $return_data[] = ['code' => 500, 'message' => $message, 'store_website_id' => $store_website_id, 'magento_module_id' => $magento_module_id];
            }
        }

        return response()->json(['code' => 200, 'message' => '', 'data' => $return_data]);
    }

    public function storeVerifiedStatus(StoreVerifiedStatusMagentoModuleRequest $request): JsonResponse
    {

        $input = $request->except(['_token']);

        $data = MagentoModuleVerifiedStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function storeM2ErrorStatus(StoreM2ErrorStatusMagentoModuleRequest $request): JsonResponse
    {

        $input = $request->except(['_token']);

        $data = MagentoModuleM2ErrorStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function storeUnitTestStatus(StoreUnitTestStatusMagentoModuleRequest $request): JsonResponse
    {

        $input = $request->except(['_token']);

        $data = MagentoModuleUnitTestStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    protected function saveVerifiedStatusHistory($magentoModule, $oldStatusId, $newStatusId, $statusType)
    {
        $history = new MagentoModuleVerifiedStatusHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_status_id = $oldStatusId;
        $history->new_status_id = $newStatusId;
        $history->type = $statusType;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveVerifiedByHistory($magentoModule, $oldStatusId, $newStatusId, $statusType)
    {
        $history = new MagentoModuleVerifiedBy;
        $history->magento_module_id = $magentoModule->id;
        $history->old_verified_by_id = $oldStatusId;
        $history->new_verified_by_id = $newStatusId;
        $history->type = $statusType;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveM2ErrorAssigneeHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagentoModuleM2ErrorAssigneeHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_assignee_id = $oldStatusId;
        $history->new_assignee_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveUnitTeststatusHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagentoModuleUnitTestStatusHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_unit_test_status_id = $oldStatusId;
        $history->new_unit_test_status_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    protected function saveUnitTestUserHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagentoModuleUnitTestUserHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_unit_test_user_id = $oldStatusId;
        $history->new_unit_test_user_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    public function verifiedStatusUpdate(Request $request): RedirectResponse
    {
        $statusColor = $request->all();
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = MagentoModuleVerifiedStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The verified status color updated successfully.');
    }

    public function getM2ErrorAssigneeHistories(Request $request): JsonResponse
    {
        $histories = MagentoModuleM2ErrorAssigneeHistory::with(['user', 'newAssignee', 'oldAssignee'])->where('magento_module_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get histories',
            'status_name' => 'success',
        ], 200);
    }

    public function verifiedByUser(Request $request): JsonResponse
    {
        $histories = MagentoModuleVerifiedBy::with(['user', 'newVerifiedBy', 'oldVerifiedBy'])->where('magento_module_id', $request->id)->where('type', $request->type)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get verified status',
            'status_name' => 'success',
        ], 200);
    }

    public function reviewStandardHistories(Request $request): JsonResponse
    {
        $histories = MagnetoReviewStandardHistory::with(['user'])->where('magento_module_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get review status',
            'status_name' => 'success',
        ], 200);
    }

    public function locationHistory(Request $request): JsonResponse
    {
        $histories = MagnetoLocationHistory::with(['newLocation', 'oldLocation', 'user'])->where('magento_module_id', $request->id)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function descriptionHistory(Request $request): JsonResponse
    {
        $histories = MagentoModuleHistory::with(['user'])->where('magento_module_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history',
            'status_name' => 'success',
        ], 200);
    }

    public function usedAtHistory(Request $request): JsonResponse
    {
        $histories = MagentoModuleHistory::with(['user'])->whereNotNull('used_at')->where('magento_module_id', $request->id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history',
            'status_name' => 'success',
        ], 200);
    }

    public function moduleEdit($id): JsonResponse
    {
        $magento_module = MagentoModule::find($id);

        if ($magento_module) {
            return response()->json(['code' => 200, 'data' => $magento_module]);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function storedependency(Request $request): JsonResponse
    {
        $remark = $request->remark;
        $moduleDependency = new MagentoModuleDependency;
        $moduleDependency->magento_module_id = $request->magento_module_id;
        $moduleDependency->depency_remark = $remark;
        $moduleDependency->depency_module_issues = $request->module_issues;
        $moduleDependency->depency_api_issues = $request->api_issues;
        $moduleDependency->depency_theme_issues = $request->theme_issues;
        $moduleDependency->user_id = Auth::user()->id;
        $moduleDependency->save();

        if ($remark) {
            $message = 'depencey';
            MagentoModule::where('id', $request->magento_module_id)->update(['magento_dependency' => $remark]);

            return response()->json([
                'status' => true,
                'message' => "{$message} added successfully",
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark filed is Required',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function getDependencyRemarks($id): JsonResponse
    {
        $dependencyRemarks = MagentoModuleDependency::with(['user'])->where('magento_module_id', $id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $dependencyRemarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function storeM2Remark(Request $request): JsonResponse
    {
        $magentoModule = MagentoModule::find($request->magento_module_id);
        $oldM2ErrorRemark = $magentoModule->m2_error_remark;
        $magentoModule->m2_error_remark = $request->remark;
        $magentoModule->save();

        $m2ErrorRemarkHistory = new MagentoModuleM2RemarkHistory;
        $m2ErrorRemarkHistory->magento_module_id = $request->magento_module_id;
        $m2ErrorRemarkHistory->old_m2_error_remark = $oldM2ErrorRemark;
        $m2ErrorRemarkHistory->new_m2_error_remark = $request->remark;
        $m2ErrorRemarkHistory->user_id = Auth::user()->id;
        $m2ErrorRemarkHistory->save();

        return response()->json([
            'status' => true,
            'message' => ' M2 remark Added Successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getUnitTestUserHistories(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $histories = MagentoModuleUnitTestUserHistory::with(['user', 'newTestUser', 'oldTestUser'])
                ->where('magento_module_id', $id)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get histories',
                'status_name' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUnitTestRemarkHistories(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $histories = MagentoModuleUnitTestRemarkHistory::with(['user'])
                ->where('magento_module_id', $id)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get histories',
                'status_name' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getM2RemarkHistories(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $histories = MagentoModuleM2RemarkHistory::with(['user'])
                ->where('magento_module_id', $id)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get histories',
                'status_name' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUnitTestStatusHistories(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $histories = MagentoModuleUnitTestStatusHistory::with(['user', 'newTestStatus', 'oldTestStatus'])
                ->where('magento_module_id', $id)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'data' => $histories,
                'message' => 'Successfully get histories',
                'status_name' => 'success',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeUniTestRemark(Request $request): JsonResponse
    {
        $magentoModule = MagentoModule::find($request->magento_module_id);
        $oldM2ErrorRemark = $magentoModule->unit_test_remark;
        $magentoModule->unit_test_remark = $request->remark;
        $magentoModule->save();

        $unittestRemarkHistory = new MagentoModuleUnitTestRemarkHistory;
        $unittestRemarkHistory->magento_module_id = $request->magento_module_id;
        $unittestRemarkHistory->old_unit_test_remark = $oldM2ErrorRemark;
        $unittestRemarkHistory->new_unit_test_remark = $request->remark;
        $unittestRemarkHistory->user_id = Auth::user()->id;
        $unittestRemarkHistory->save();

        return response()->json([
            'status' => true,
            'message' => ' Unit test remark Added Successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function columnVisbilityUpdate(Request $request): JsonResponse
    {
        $userCheck = ColumnVisbility::where('user_id', auth()->user()->id)->first();

        if ($userCheck) {
            $column = ColumnVisbility::find($userCheck->id);
            $column->columns = json_encode($request->columns);
            $column->save();
        } else {
            $column = new ColumnVisbility;
            $column->columns = json_encode($request->columns);
            $column->user_id = Auth::user()->id;
            $column->save();
        }

        return response()->json([
            'status' => true,
            'message' => ' column visiblity Added Successfully',
            'status_name' => 'success',
        ], 200);
    }

    protected function saveLocationHistory($magentoModule, $oldStatusId, $newStatusId)
    {
        $history = new MagnetoLocationHistory;
        $history->magento_module_id = $magentoModule->id;
        $history->old_location_id = $oldStatusId;
        $history->new_location_id = $newStatusId;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    public function syncLogsColumnVisbilityUpdate(Request $request): JsonResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'magento-modules-sync_logs')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'magento-modules-sync_logs';
            $column->column_name = json_encode($request->columns);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'magento-modules-sync_logs';
            $column->column_name = json_encode($request->columns);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return response()->json([
            'status' => true,
            'message' => ' column visiblity Added Successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function magentoModuleCheckStatus(Request $request): JsonResponse
    {
        $store_website_id = $request->store_website_id;
        $magento_module_id = $request->magento_module_id;

        $data = MagentoModule::where('id', (int) $magento_module_id)->first();

        $store_website = StoreWebsite::select('title', 'server_ip', 'working_directory')->where('id', $store_website_id)->first();

        // New Script
        $moduleName = $data->module;
        $website = $store_website->title;
        $server = $store_website->server_ip;
        $rootDir = $store_website->working_directory;
        $websiteStoreProjectName = null;
        $action = 'status';
        $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

        $cmd = "bash $scriptsPath"."sync-magento-modules.sh -w \"$website\" -s \"$server\" -d \"$rootDir\" -m \"$moduleName\" -g \"$websiteStoreProjectName\" -a \"$action\" 2>&1";

        exec($cmd, $output, $return_var);
        Log::info('store command:'.$cmd);
        Log::info('store output:'.print_r($output, true));
        Log::info('store return_var:'.$return_var);

        return response()->json(['code' => 200, 'data' => $result]);
    }

    public function magentoModuleListLogsDetails($id): JsonResponse
    {
        $MagentoModuleLogs = MagentoModuleLogs::findorFail($id);

        return response()->json([
            'status' => true,
            'data' => $MagentoModuleLogs,
            'message' => 'Data get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
