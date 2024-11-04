<?php

namespace App\Http\Controllers;

use App\Jobs\PushMagentoCronSettings;
use App\Jobs\PushMagentoSettings;
use App\MagentoSetting;
use App\MagentoSettingLog;
use App\MagentoSettingNameLog;
use App\MagentoSettingPushLog;
use App\MagentoSettingStatus;
use App\Models\MagentoSettingRevisionHistory;
use App\Models\MagentoSettingValueHistory;
use App\StoreWebsite;
use App\User;
use App\Website;
use App\WebsiteStore;
use App\WebsiteStoreView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MagentoSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $all_paths = MagentoSetting::groupBy('path')->get()->pluck('path', 'path')->toArray();
        $all_names = MagentoSetting::groupBy('name')->get()->pluck('name', 'name')->toArray();
        $magentoSettings = MagentoSetting::with(
            'storeview.websiteStore.website.storeWebsite',
            'store.website.storeWebsite',
            'website',
            'fromStoreId', 'fromStoreIdwebsite');

        $magentoSettings->leftJoin('users', 'magento_settings.created_by', 'users.id');
        $magentoSettings->select('magento_settings.*', 'users.name as uname');
        if ($request->scope) {
            $magentoSettings->where('scope', $request->scope);
        }
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
            ->select('store_websites.website', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at')->orderByDesc('magento_setting_push_logs.created_at')->get();

        if (is_array($request->website)) {
            foreach ($request->website as $website) {
                if (empty($request->scope)) {
                    $magentoSettings->whereHas('storeview.websiteStore.website.storeWebsite', function ($q) use ($website) {
                        $q->where('id', $website);
                    })->orWhereHas('store.website.storeWebsite', function ($q) use ($website) {
                        $q->where('id', $website);
                    })->orWhereHas('website', function ($q) use ($website) {
                        $q->where('id', $website);
                    });
                } else {
                    if ($request->scope == 'default') {
                        $website_ids = StoreWebsite::where('id', $website)->get()->pluck('id')->toArray();
                        $magentoSettings->whereIn('scope_id', $website_ids ?? []);
                    } elseif ($request->scope == 'websites' || $request->scope == 'stores') {
                        $website_ids = StoreWebsite::where('id', $website)->get()->pluck('id')->toArray();
                        $magentoSettings->whereIn('store_website_id', $website_ids ?? []);
                    }
                }
            }
        }

        if (isset($request->name) && ! empty($request->name)) {
            $magentoSettings->whereIn('magento_settings.name', $request->name);
        }
        if (isset($request->path) && ! empty($request->path)) {
            $magentoSettings->whereIn('magento_settings.path', $request->path);
        }
        if ($request->status != '') {
            $magentoSettings->where('magento_settings.status', 'LIKE', '%'.$request->status.'%');
        }
        if ($request->user_name != null && $request->user_name != 'undefined') {
            $magentoSettings->whereIn('magento_settings.created_by', $request->user_name);
        }

        $magentoSettings = $magentoSettings->orderByDesc('magento_settings.id')->paginate(25);
        $storeWebsites = StoreWebsite::get();
        $websitesStores = WebsiteStore::get()->pluck('name')->unique()->toArray();
        $websiteStoreViews = WebsiteStoreView::get()->pluck('code')->unique()->toArray();
        $allUsers = User::where('is_active', '1')->get();
        $magentoSettingStatuses = MagentoSettingStatus::all();
        $data = $magentoSettings;
        $data = $data->groupBy('store_website_id')->toArray();
        $newValues = [];

        $countList = MagentoSetting::all();
        if (is_array($request->website) || $request->name || $request->path || $request->status || $request->scope) {
            $counter = $magentoSettings->count();
        } else {
            $counter = $countList->count();
        }
        if ($request->ajax()) {
            return view('magento.settings.index_ajax', [
                'magentoSettings' => $magentoSettings,
                'newValues' => $newValues,
                'storeWebsites' => $storeWebsites,
                'websitesStores' => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs' => $pushLogs,
                'counter' => $counter,
                'allUsers' => $allUsers,
                'magentoSettingStatuses' => $magentoSettingStatuses,
                'all_paths' => $all_paths,
                'all_names' => $all_names,
            ]);
        } else {
            return view('magento.settings.index', [
                'magentoSettings' => $magentoSettings,
                'newValues' => $newValues,
                'storeWebsites' => $storeWebsites,
                'websitesStores' => $websitesStores,
                'websiteStoreViews' => $websiteStoreViews,
                'pushLogs' => $pushLogs,
                'counter' => $counter,
                'allUsers' => $allUsers,
                'magentoSettingStatuses' => $magentoSettingStatuses,
                'all_paths' => $all_paths,
                'all_names' => $all_names,
            ]);
        }
    }

    public function getLogs(Request $request): View
    {
        $storeWebsites = StoreWebsite::get();
        $magentoSettings = MagentoSetting::get();
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
            ->select('store_websites.website', 'magento_setting_push_logs.id', 'magento_setting_push_logs.command_output', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at', 'magento_setting_push_logs.store_website_id', 'magento_setting_push_logs.command_server', 'magento_setting_push_logs.job_id', 'magento_setting_push_logs.setting_id')
            ->orderByDesc('magento_setting_push_logs.id');
        if ($request->website) {
            $pushLogs->where('store_website_id', $request->website);
        }
        if ($request->date) {
            $pushLogs->whereDate('magento_setting_push_logs.created_at', $request->date);
        }

        $counter = MagentoSettingPushLog::select('*');
        if ($request->website) {
            $counter->where('store_website_id', $request->website);
        }
        if ($request->search_status) {
            $pushLogs = $pushLogs->where('status', $request->search_status);
        }
        if ($request->search_url) {
            $pushLogs = $pushLogs->where('command_server', 'LIKE', '%'.$request->search_url.'%');
        }
        if ($request->request_data) {
            $pushLogs = $pushLogs->where('command', 'LIKE', '%'.$request->request_data.'%');
        }
        if ($request->request_setting) {
            $pushLogs = $pushLogs->whereHas('setting', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->request_setting.'%');
            });
        }

        $pushLogs = $pushLogs->paginate(25)->withQueryString();

        $counter = $counter->count();

        return view('magento.settings.sync_logs', [
            'pushLogs' => $pushLogs,
            'storeWebsites' => $storeWebsites,
            'counter' => $counter,
            'magentoSettings' => $magentoSettings,
        ]);
    }

    public function magentoSyncLogSearch(Request $request): JsonResponse
    {
        $pushLogs = MagentoSettingPushLog::leftJoin('store_websites', 'store_websites.id', '=', 'magento_setting_push_logs.store_website_id')
            ->select('store_websites.website', 'magento_setting_push_logs.status', 'magento_setting_push_logs.command', 'magento_setting_push_logs.created_at');
        if ($request->sync_date != '') {
            $pushLogs = $pushLogs->whereDate('magento_setting_push_logs.created_at', date('Y-m-d', strtotime($request->sync_date)));
        }

        $pushLogs = $pushLogs->orderByDesc('magento_setting_push_logs.created_at')->get();
        if (! empty($pushLogs)) {
            return response()->json(['status' => 200, 'data' => $pushLogs, 'msg' => 'Data Listed successfully!']);
        } else {
            return response()->json(['status' => 500, 'data' => [], 'msg' => 'Could, not find data!']);
        }
    }

    public function create(Request $request): JsonResponse
    {
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $datatype = $request->datatype;
        $copyWebsites = (! empty($request->websites)) ? $request->websites : [];
        $save_record_status = 0;
        foreach ($request->scope as $scope) {
            if ($scope === 'default') {
                $totalWebsites = array_merge($request->website, $copyWebsites);
                $storeWebsites = StoreWebsite::whereIn('id', $totalWebsites)->get();

                foreach ($storeWebsites as $storeWebsite) {
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $storeWebsite->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $storeWebsite->id,
                            'store_website_id' => $storeWebsite->id,
                            'website_store_id' => 0,
                            'website_store_view_id' => 0,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }
            }

            if ($scope === 'websites') {
                $websiteStores = [];
                $stores = [];
                if ($request->website_store != null) {
                    $websiteStores = WebsiteStore::whereIn('id', $request->website_store)->get();
                }
                foreach ($websiteStores as $websiteStore) {
                    $stores[] = $websiteStore->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStore->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStore->id,
                            'website_store_view_id' => 0,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }

                if (! empty($copyWebsites) && ! empty($stores)) {
                    foreach ($copyWebsites as $cw) {
                        $websiteStores = WebsiteStore::join('websites as w', 'w.id', 'website_stores.website_id')->where('w.store_website_id', $cw)->whereIn('website_stores.code', $stores)->whereNotIn('website_stores.id', $request->website_store)->get();
                        foreach ($websiteStores as $websiteStore) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStore->id)->where('path', $path)->first();
                            if (! $m_setting) {
                                MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStore->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStore->id,
                                    'website_store_view_id' => 0,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                                    'created_by' => Auth::id(),
                                ]);
                                $save_record_status = 1;
                            }
                        }
                    }
                }
            }

            if ($scope === 'stores') {
                $websiteStoresViews = [];
                if ($request->website_store_view != null) {
                    $websiteStoresViews = WebsiteStoreView::whereIn('id', $request->website_store_view)->get();
                }
                $stores = [];
                foreach ($websiteStoresViews as $websiteStoresView) {
                    $stores[] = $websiteStoresView->code;
                    $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                    if (! $m_setting) {
                        MagentoSetting::Create([
                            'scope' => $scope,
                            'scope_id' => $websiteStoresView->id,
                            'store_website_id' => $request->single_website,
                            'website_store_id' => $websiteStoresView->website_store_id,
                            'website_store_view_id' => $websiteStoresView->id,
                            'name' => $name,
                            'path' => $path,
                            'value' => $value,
                            'data_type' => $datatype,
                            'created_by' => Auth::id(),
                        ]);
                        $save_record_status = 1;
                    }
                }

                if (! empty($copyWebsites)) {
                    foreach ($copyWebsites as $cw) {
                        $websiteStoresViews = WebsiteStoreView::join('websites as w', 'w.id', 'website_store_views.website_store_id')->where('w.store_website_id', $cw)->whereIn('website_stores.code', $stores);

                        foreach ($websiteStoresViews as $websiteStoresView) {
                            $m_setting = MagentoSetting::where('scope', $scope)->where('scope_id', $websiteStoresView->id)->where('path', $path)->first();
                            if (! $m_setting) {
                                MagentoSetting::Create([
                                    'scope' => $scope,
                                    'scope_id' => $websiteStoresView->id,
                                    'store_website_id' => $cw,
                                    'website_store_id' => $websiteStoresView->website_store_id,
                                    'website_store_view_id' => $websiteStoresView->id,
                                    'name' => $name,
                                    'path' => $path,
                                    'value' => $value,
                                    'data_type' => $datatype,
                                    'created_by' => Auth::id(),
                                ]);
                                $save_record_status = 1;
                            }
                        }
                    }
                }
            }
        }

        $return = [];
        if ($save_record_status == 1) {
            $return = ['code' => 200, 'message' => 'Magento setting has been created.'];
        } else {
            $return = ['code' => 500, 'message' => 'Magento setting has not been created.'];
        }

        return response()->json($return);
    }

    public function update(Request $request): JsonResponse
    {
        $entity_id = $request->id;
        $scope = $request->scope;
        $name = $request->name;
        $path = $request->path;
        $value = $request->value;
        $website_ids = $request->websites;

        $m = MagentoSetting::where('id', $request->id)->first();
        if ($m) {
            MagentoSettingNameLog::insert([
                'old_value' => $m->name,
                'new_value' => $name,
                'updated_by' => Auth::id(),
                'magento_settings_id' => $request->id,
                'updated_at' => date('Y-m-d H:i'),
            ]);
        }

        MagentoSetting::where('id', $request->id)->update([
            'name' => $name,
            'path' => $path,
            'value' => $value,
        ]);

        if ($value !== $m->value) {
            $history = new MagentoSettingValueHistory;
            $history->magento_setting_id = $m->id;
            $history->old_value = $m->value;
            $history->new_value = $value;
            $history->user_id = Auth::user()->id;
            $history->save();

            $history = new MagentoSettingRevisionHistory;
            $history->setting = $m->name;
            $history->date = date('Y-m-d H:i');
            $history->status = 1;
            $history->log = json_encode($request->all());
            $history->config_revision = $value;
            $history->active = 1;
            $history->save();
        }

        $magentoSetting = MagentoSetting::find($entity_id);

        if (! empty($request->selectedCheckboxes)) {
            $selectedCheckboxes = explode(',', $request->selectedCheckboxes);
        }

        if (empty($website_ids) || ! in_array($magentoSetting->store_website_id, $website_ids)) {
            $website_ids[] = $magentoSetting->store_website_id;
        }

        if ($scope == 'execute') {
            PushMagentoCronSettings::dispatch($magentoSetting, $website_ids)->onQueue('pushmagentocronsettings');
        } else {
            PushMagentoSettings::dispatch($magentoSetting, $website_ids)->onQueue('pushmagentosettings');
        }

        Log::info('Admin setting command::COMMENT::NEED TO CHECK WITH MAGENTO TEAM');
        //AdminSettingCommandJob::dispatch($selectedCheckboxes, $path, $value)->onQueue('admin_setting_command');

        return response()->json(['code' => 200, 'message' => 'Updated successfully !']);
    }

    public function pushMagentoSettings(Request $request): RedirectResponse
    {
        if ($request->has('store_website_id') && $request->store_website_id != '') {
            $store_website_id = $request->store_website_id;
            $magentoSettings = MagentoSetting::where('store_website_id', $store_website_id)->get();
            $website_ids[] = $store_website_id;
            foreach ($magentoSettings as $magentoSetting) {
                PushMagentoSettings::dispatch($magentoSetting, $website_ids)->onQueue('pushmagentosettings');
            }

            return redirect()->route('magento.setting.index')->with('success', 'Successfully pushed Magento settings to the store website');
        }

        return redirect()->route('magento.setting.index')->with('error', 'Please select the store website!');
    }

    public function websiteStores(Request $request): JsonResponse
    {
        $website_ids = Website::where('store_website_id', $request->website_id)->get()->pluck('id')->toArray();

        return response()->json([
            'data' => WebsiteStore::select('id', 'name')->whereNotNull('name')->whereIn('website_id', $website_ids)->get(),
        ]);
    }

    public function websiteStoreViews(Request $request): JsonResponse
    {
        $website_store_ids = $request->website_id;
        $website_store_view_data = [];
        if (! empty($website_store_ids)) {
            $website_store_view_data = WebsiteStoreView::select('id', 'code')->whereNotNull('code')->whereIn('website_store_id', $website_store_ids)->get();
        }

        return response()->json([
            'data' => $website_store_view_data,
        ]);
    }

    public function deleteSetting($id): RedirectResponse
    {
        $m_setting = MagentoSetting::find($id);
        if ($m_setting) {
            $m_setting->delete();
            $log = $id.' Id Deleted successfully';
            $formData = ['event' => 'delete', 'log' => $log];
            MagentoSettingLog::create($formData);
        }

        return redirect()->route('magento.setting.index');
    }

    public function namehistrory($id)
    {
        $ms = MagentoSettingNameLog::select('magento_setting_name_logs.*', 'users.name')
            ->leftJoin('users', 'magento_setting_name_logs.updated_by', 'users.id')
            ->where('magento_settings_id', $id)->get();

        return view('magento.name_history', ['logs' => $ms]);

    }

    public function magentoPushLogs($settingId)
    {
        $logs = MagentoSettingPushLog::where('setting_id', $settingId)->orderByDesc('id')->get();

        return view('magento.push-logs', ['logs' => $logs]);

    }

    public function getAllStoreWebsites($id): JsonResponse
    {
        $storeWebsites = StoreWebsite::where('parent_id', '=', $id)->get();

        return response()->json($storeWebsites);
    }

    public function getMagentoSetting($id): JsonResponse
    {

        $magentoSetting = MagentoSetting::find($id)->first();

        $taggedStoreWebsites = '';

        if ($magentoSetting) {
            if ($magentoSetting->store_website_id) {
                $storeWebsite = StoreWebsite::find($magentoSetting->store_website_id);
                if ($storeWebsite->parent_id) {
                    $taggedStoreWebsites = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();

                    return response()->json(['code' => 200, 'taggedWebsites' => $taggedStoreWebsites]);
                } else {
                    $taggedStoreWebsites = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();

                    return response()->json(['code' => 200, 'taggedWebsites' => $taggedStoreWebsites]);
                }
            }

            return response()->json(['code' => 500, 'error' => 'No data found']);
        }

        return response()->json(['code' => 500, 'error' => 'Id is wrong!']);
    }

    public function pushRowMagentoSettings(Request $request): RedirectResponse
    {
        if ($request->has('tagged_websites') && $request->has('row_id')) {
            // Find individual setting
            $individualSetting = MagentoSetting::with(
                'storeview.websiteStore.website.storeWebsite',
                'store.website.storeWebsite',
                'website',
                'fromStoreId', 'fromStoreIdwebsite')->find($request->row_id);

            if ($request->has('new_value') && $individualSetting->value !== $request->new_value) {
                $history = new MagentoSettingValueHistory;
                $history->magento_setting_id = $individualSetting->id;
                $history->old_value = $individualSetting->value;
                $history->new_value = $request->new_value;
                $history->user_id = Auth::user()->id;
                $history->save();
            }
            // Assign new value when push
            if ($request->has('new_value')) {
                $individualSetting->value = $request->new_value;
                $individualSetting->save();
            }

            // Push individual setting to selected websites
            PushMagentoSettings::dispatch($individualSetting, $request->tagged_websites)->onQueue('pushmagentosettings');

            return redirect()->route('magento.setting.index')->with('success', 'Successfully pushed Magento settings to the store website');
        }

        return redirect()->route('magento.setting.index')->with('error', 'Please select the store website!');
    }

    public function statusColor(Request $request): RedirectResponse
    {
        $statusColor = $request->all();

        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoSettingStatus = MagentoSettingStatus::find($key);
            $magentoSettingStatus->color = $value;
            $magentoSettingStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function assignSetting(Request $request): RedirectResponse
    {
        $storeWebsite = StoreWebsite::find($request->store_website_id);
        if ($storeWebsite->parent_id) {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
        } else {
            $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
        }

        $allInstancesIds = $allInstances->pluck('id');
        // Find all the Magento settings for these instances & assing it to the selected user
        $allMagentoSettings = MagentoSetting::whereIn('store_website_id', $allInstancesIds)->get()->pluck('id')->toArray();
        if ($allMagentoSettings) {
            $user = User::find($request->assign_user);
            $user->magentoSettings()->attach($allMagentoSettings);
        }

        return redirect()->back()->with('success', 'Assigned successfully.');
    }

    public function assignIndividualSetting(Request $request): RedirectResponse
    {
        $magentoSetting = MagentoSetting::where('id', $request->row_id)->first();

        if ($magentoSetting) {
            $assign_settings[] = $magentoSetting->id;
            if ($magentoSetting->store_website_id) {
                $storeWebsite = StoreWebsite::find($magentoSetting->store_website_id);
                if ($storeWebsite->parent_id) {
                    $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->parent_id)->orWhere('id', $storeWebsite->parent_id)->get();
                } else {
                    $allInstances = StoreWebsite::where('parent_id', '=', $storeWebsite->id)->orWhere('id', $storeWebsite->id)->get();
                }
                $allInstancesIds = $allInstances->pluck('id');
                $allMagentoSettings = MagentoSetting::whereIn('store_website_id', $allInstancesIds)->where('path', $magentoSetting->path)->where('scope', $magentoSetting->scope)->get()->pluck('id')->toArray();
                $assign_settings = array_merge($assign_settings, $allMagentoSettings);
            }
            $user = User::find($request->assign_user);
            $user->magentoSettings()->attach($assign_settings);

            return redirect()->back()->with('success', 'Assigned successfully.');
        }

        return redirect()->back()->with('error', 'Not Assigned, MagentoSetting not found');
    }

    public function magentoSettingvalueHistories($id): JsonResponse
    {
        $datas = MagentoSettingValueHistory::with(['user'])
            ->where('magento_setting_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function syncAdminMagentoSettings(): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:get-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json([
                'status' => false,
                'message' => 'There is sync magento settings process is already running. Please wait for sometime.',
            ], 200);
        } else {
            exec(env('PHP_ENV_PATH').' '.env('ERP_APP_RELATIVE_PATH').'artisan magento:get-config-value > /dev/null 2>&1 &');

            return response()->json([
                'status' => true,
                'message' => 'Magento settings sync process started.',
            ], 200);
        }
    }

    public function checkSyncAdminMagentoProcess(): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:get-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json(['status' => 'running', 'process' => $output]);
        } else {
            return response()->json(['status' => 'not running']);
        }
    }

    public function syncCreateAdminMagentoSettings($type = ''): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:sync-and-create-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json([
                'status' => false,
                'message' => 'There is sync magento settings process is already running. Please wait for sometime.',
            ], 200);
        } else {
            exec(env('PHP_ENV_PATH').' '.env('ERP_APP_RELATIVE_PATH').'artisan magento:sync-and-create-config-value '.Auth::user()->id.' '.$type.'> /dev/null 2>&1 &');

            return response()->json([
                'status' => true,
                'message' => 'Magento settings sync process started.',
            ], 200);
        }
    }

    public function checkSyncCreateAdminMagentoSettings(): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:sync-and-create-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json(['status' => 'running', 'process' => $output]);
        } else {
            return response()->json(['status' => 'not running']);
        }
    }

    public function syncCronMagentoSettings(): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:sync-and-create-cron-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json([
                'status' => false,
                'message' => 'There is sync cron magento settings process is already running. Please wait for sometime.',
            ], 200);
        } else {
            exec(env('PHP_ENV_PATH').' '.env('ERP_APP_RELATIVE_PATH').'artisan magento:sync-and-create-cron-config-value '.Auth::user()->id.' > /dev/null 2>&1 &');

            return response()->json([
                'status' => true,
                'message' => 'Magento cron settings sync process started.',
            ], 200);
        }
    }

    public function checkSyncCronMagentoProcess(): JsonResponse
    {
        // Command to check for the running Magento process
        $command = "ps aux | grep 'magento:sync-and-create-cron-config-value' | grep -v grep";

        // Execute the command
        $output = [];
        $return_var = null;
        exec($command, $output, $return_var);

        // Check if the process is found
        if (! empty($output)) {
            return response()->json(['status' => 'running', 'process' => $output]);
        } else {
            return response()->json(['status' => 'not running']);
        }
    }
}
