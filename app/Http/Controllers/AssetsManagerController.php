<?php

namespace App\Http\Controllers;
use App\Jobs\SendEmail;

use App\AssetMagentoDevScripUpdateLog;
use App\AssetManagerLinkUser;
use App\AssetManamentUpdateLog;
use App\AssetPlateForm;
use App\AssetsCategory;
use App\AssetsManager;
use App\assetUserChangeHistory;
use App\CashFlow;
use App\ChatMessage;
use App\Email;
use App\EmailAddress;
use App\Http\Requests\AssetsManagerCreateTerminalUserAccessRequest;
use App\Http\Requests\AssetsManagerSendEmailRequest;
use App\Http\Requests\SaveRemarksAssetsManagerRequest;
use App\Http\Requests\StoreAssetsManagerRequest;
use App\Http\Requests\UpdateAssetsManagerRequest;
use App\Marketing\WhatsappConfig;
use App\Models\AssetManagerTerminalUserAccess;
use App\Models\AssetManagerTerminalUserAccessRemakrs;
use App\Models\AssetManagerUserAccess;
use App\Models\DataTableColumn;
use App\StoreWebsite;
use App\User;
use App\UserEvent\UserEvent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AssetsManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $archived = 0;
        if ($request->archived == 1) {
            $archived = 1;
        }

        $assets_category = AssetsCategory::all();

        $search = request('search', '');
        $paymentCycle = request('payment_cycle', '');
        $assetType = request('asset_type', '');
        $purchaseType = request('purchase_type', '');
        $website_id = request('website_id');
        $asset_plate_form_id = request('asset_plate_form_id');
        $email_address_id = request('email_address_id');
        $whatsapp_config_id = request('whatsapp_config_id');
        $ip_ids = request('ip_ids');
        $user_ids = request('user_ids');
        $date = request('date');

        $assets = new AssetsManager;
        $assets = $assets->leftJoin('store_websites', 'store_websites.id', 'assets_manager.website_id')
            ->leftJoin('asset_plate_forms AS apf', 'apf.id', 'assets_manager.asset_plate_form_id')
            ->leftJoin('email_addresses As ea', 'ea.id', 'assets_manager.email_address_id')
            ->leftJoin('whatsapp_configs AS wc', 'wc.id', 'assets_manager.whatsapp_config_id')
            ->leftJoin('assets_manager_link_user as linkuser', 'linkuser.asset_manager_id', 'assets_manager.id');

        if (! Auth::user()->hasRole('Admin')) {
            $assets->where('assets_manager.created_by', Auth::user()->id)->orWhere('linkuser.user_id', Auth::user()->id);
        }

        if (! empty($search)) {
            $assets = $assets->where(function ($q) use ($search) {
                $q->where('assets_manager.name', 'LIKE', '%'.$search.'%')->orWhere('provider_name', 'LIKE', '%'.$search.'%');
            });
        }

        if (! empty($paymentCycle)) {
            $assets = $assets->where('assets_manager.payment_cycle', $paymentCycle);
        }

        if (! empty($assetType)) {
            $assets = $assets->where('assets_manager.asset_type', $assetType);
        }

        if (! empty($purchaseType)) {
            $assets = $assets->where('assets_manager.purchase_type', $purchaseType);
        }

        if (! empty($website_id)) {
            $assets = $assets->where('assets_manager.website_id', $website_id);
        }

        if (! empty($asset_plate_form_id)) {
            $assets = $assets->where('assets_manager.asset_plate_form_id', $asset_plate_form_id);
        }

        if (! empty($email_address_id)) {
            $assets = $assets->where('assets_manager.purchase_type', $email_address_id);
        }

        if (! empty($whatsapp_config_id)) {
            $assets = $assets->where('assets_manager.purchase_type', $whatsapp_config_id);
        }
        if (! empty($user_ids)) {
            $assets = $assets->whereIn('assets_manager.created_by', $user_ids);
        }

        if (! empty($ip_ids) && (count($ip_ids) > 0)) {
            $ip_idss = array_filter($ip_ids, fn ($ip_ids) => ! is_null($ip_ids));
            if (! in_array(null, $ip_idss)) {
                $assets = $assets->whereIn('assets_manager.ip', $ip_idss);
            }
        }

        $assets = $assets->orderBy('id');

        $assetsIds = $assets->select('assets_manager.id')->get()->toArray();
        $assets = $assets->select(DB::raw('DISTINCT assets_manager.*, linkuser.asset_manager_id'), 'store_websites.website AS website_name', 'apf.name AS plateform_name', 'ea.from_address', 'wc.number');
        $assets = $assets->orderBy('assets_manager.id')->paginate(25);
        $websites = StoreWebsite::all();
        $plateforms = AssetPlateForm::all();
        $emailAddress = EmailAddress::all();
        $whatsappCon = WhatsappConfig::all();

        //Cash Flows
        $cashflows = CashFlow::whereIn('cash_flow_able_id', $assetsIds)->where(['cash_flow_able_type' => AssetsManager::class])->get();
        $users = User::get()->toArray();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'asset-manager')->first();

        $dynamicColumnsToShowAM = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowAM = json_decode($hideColumns, true);
        }

        return view('assets-manager.index', compact('assets', 'assets_category', 'cashflows', 'users', 'websites', 'plateforms', 'whatsappCon', 'emailAddress', 'dynamicColumnsToShowAM'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function asColumnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'asset-manager')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'asset-manager';
            $column->column_name = json_encode($request->column_assetsmanager);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'asset-manager';
            $column->column_name = json_encode($request->column_assetsmanager);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetsManagerRequest $request): RedirectResponse
    {

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = AssetsCategory::where('cat_name', $othercat)->first();

            if (! empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = AssetsCategory::insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');
        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        $data['start_date'] = ($request->input('start_date') == '') ? $request->input('old_start_date') : $request->input('start_date');
        $data['ip_name'] = $request->ip_name;
        $data['server_password'] = $request->server_password;
        $data['folder_name'] = json_encode($request->folder_name);
        $data['website_id'] = $request->website_id;
        $data['asset_plate_form_id'] = $request->asset_plate_form_id;
        $data['email_address_id'] = $request->email_address_id;
        $data['whatsapp_config_id'] = $request->whatsapp_config_id;
        $data['created_by'] = Auth::user()->id;
        $data['link'] = $request->get('link');
        $data['ip'] = $request->get('ip');
        $insertData = AssetsManager::create($data);
        if ($request->input('payment_cycle') == 'One time') {
            //create entry in table cash_flows
            CashFlow::create(
                [
                    'description' => 'Asset Manager Payment for '.$insertData->name,
                    'date' => date('Y-m-d'),
                    'amount' => $request->input('amount'),
                    'type' => 'pending',
                    'currency' => $insertData->currency,
                    'cash_flow_able_type' => AssetsManager::class,
                    'cash_flow_able_id' => $insertData->id,

                ]
            );
        }

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets created successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetsManagerRequest $request, int $id): RedirectResponse
    {

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = AssetsCategory::Where('cat_name', $othercat)
                ->first();

            if (! empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = AssetsCategory::insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');

        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        if ($request->input('old_user_name') != $request->input('user_name') || $request->input('old_password') != $request->input('password')) {
            $assetLog = new AssetManamentUpdateLog;
            $assetLog->assetmenament_id = $id;
            $assetLog->user_id = Auth::user()->id;
            $assetLog->user_name = $request->input('old_user_name');
            $assetLog->password = $request->input('old_password');
            $assetLog->ip = $request->input('old_ip');
            $assetLog->save();
        }
        if ($request->input('old_user_name') != $request->input('user_name')) {
            $this->createUserHistory($request, $id);
        }
        $data['ip_name'] = $request->ip_name;
        $data['server_password'] = $request->server_password;
        $data['folder_name'] = json_encode($request->folder_name);
        $data['website_id'] = $request->website_id;
        $data['asset_plate_form_id'] = $request->asset_plate_form_id;
        $data['email_address_id'] = $request->email_address_id;
        $data['whatsapp_config_id'] = $request->whatsapp_config_id;
        $data['link'] = $request->get('link');
        AssetsManager::find($id)->update($data);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $data['archived'] = 1;
        AssetsManager::destroy($id);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets deleted successfully');
    }

    public function addNote($id, Request $request): JsonResponse
    {
        $assetmanager = AssetsManager::findOrFail($id);
        $notes = $assetmanager->notes;
        if (! is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $assetmanager->notes = $notes;
        $assetmanager->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function paymentHistory(request $request): JsonResponse
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        $paymentData = CashFlow::where('cash_flow_able_id', $asset_id)
            ->where('cash_flow_able_type', AssetsManager::class)
            ->where('type', 'paid')
            ->orderByDesc('date')
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->amount.'</td>';
                $html .= '<td>'.$history->date.'</td>';
                $html .= '<td>'.$history->description.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function assetManamentLog(request $request): JsonResponse
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        $assetLogs = AssetManamentUpdateLog::select('asset_manamentupdate_logs.*', 'users.name AS userName')
            ->leftJoin('users', 'users.id', '=', 'asset_manamentupdate_logs.user_id')
            ->where('asset_manamentupdate_logs.assetmenament_id', $asset_id)
            ->orderByDesc('asset_manamentupdate_logs.id')
            ->get();
        $i = 1;
        if (count($assetLogs) > 0) {
            foreach ($assetLogs as $assetLog) {
                $html .= '<tr>';
                $html .= '<td>'.$assetLog->id.'</td>';
                $html .= '<td>'.$assetLog->userName.'</td>';
                $html .= '<td>'.$assetLog->user_name.'</td>';
                $html .= '<td>'.$assetLog->password.'</td>';
                $html .= '<td>'.$assetLog->created_at.'</td>';
                $html .= '</tr>';
                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function getMagentoDevScriptUpdatesLogs(Request $request, $asset_manager_id): JsonResponse
    {
        try {
            $responseLog = AssetMagentoDevScripUpdateLog::where('asset_manager_id', '=', $asset_manager_id)->orderByDesc('id')->get();
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="ip" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-ip-'.$res->id.'">'.Str::limit($res->ip, 15, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-ip-'.$res->id.' hidden">'.$res->website.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="command" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-command-'.$res->id.'">'.Str::limit($res->command_name, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-command-'.$res->id.' hidden">'.$res->command_name.'</span>
                    </td>';

                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento bash Log Listed successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function magentoDevScriptUpdate(Request $request): JsonResponse
    {
        try {
            $run = Artisan::call('command:MagentoDevUpdateScriptAsset', ['id' => $request->id, 'folder_name' => $request->folder_name]);

            return response()->json(['code' => 200, 'message' => 'Magento Setting Updated successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function userChangesHistoryLog(request $request): JsonResponse
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        $assetLogs = assetUserChangeHistory::select('asset_user_change_histories.*', 'users.name AS userNameChangeBy', 'u.name AS userName')
            ->leftJoin('users', 'users.id', '=', 'asset_user_change_histories.user_id')
            ->leftJoin('users AS u', 'u.id', '=', 'asset_user_change_histories.new_user_id')
            ->where('asset_user_change_histories.asset_id', $asset_id)
            ->orderByDesc('asset_user_change_histories.id')
            ->get();
        $i = 1;
        if (count($assetLogs) > 0) {
            foreach ($assetLogs as $assetLog) {
                $html .= '<tr>';
                $html .= '<td>'.$assetLog->id.'</td>';
                $html .= '<td>'.$assetLog->userNameChangeBy.'</td>';
                $html .= '<td>'.$assetLog->userName.'</td>';
                $html .= '<td>'.$assetLog->created_at.'</td>';
                $html .= '</tr>';
                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function createUserHistory(Request $request, $id): JsonResponse
    {
        try {
            $userHistory = assetUserChangeHistory::create([
                'asset_id' => $id,
                'user_id' => Auth::user()->id,
                'new_user_id' => $request->user_name,
                'old_user_id' => $request->old_user_name,
            ]);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function plateFormStore(Request $request): JsonResponse
    {
        try {
            $plateform = AssetPlateForm::create([
                'name' => $request->name,
            ]);

            return response()->json(['code' => 500, 'data' => $plateform, 'message' => 'Plateform Data has been saved successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Send email to given emailId
     */
    public function assetsManagerSendEmail(AssetsManagerSendEmailRequest $request): RedirectResponse
    {
        try {
            $assetsmanager = AssetsManager::where('id', $request->assets_manager_id)->first();
            $usersdetails = User::where('id', $request->user_name)->first();
            $newPassword = Str::random(12);
            $message = '';
            $message .= '<center><h4>Assets Manager Details <h4></center>'.'<br>';
            $message .= 'Name = '.$assetsmanager->name.'<br>';
            $message .= 'Capacity = '.$assetsmanager->capacity.'<br>';
            $message .= 'Password = '.$assetsmanager->password.'<br>';
            $message .= 'Provider Name = '.$assetsmanager->provider_name.'<br>';
            $message .= 'Asset Type = '.$assetsmanager->asset_type.'<br>';
            $message .= 'Category = '.$assetsmanager->category->cat_name.'<br>';
            $message .= 'Purchase Type = '.$assetsmanager->purchase_type.'<br>';
            $message .= 'Payment Cycle = '.$assetsmanager->payment_cycle.'<br>';
            $message .= 'Amount = '.$assetsmanager->amount.'<br>';
            $message .= 'Currency = '.$assetsmanager->currency.'<br>';
            $message .= 'Ip = '.$assetsmanager->ip.'<br>';
            $message .= 'Ip Name = '.$assetsmanager->ip_name.'<br>';

            //Store data in chat_message table.
            $params = [
                'number' => $usersdetails->phone,
                'user_id' => Auth::user()->id,
                'message' => $message,
            ];

            ChatMessage::create($params);

            // Store data in email table
            $from_address = isset($request->from_email) && $request->from_email != '' ? $request->from_email : config('env.MAIL_FROM_ADDRESS');

            $email = Email::create([
                'model_id' => '',
                'model_type' => AssetsManager::class,
                'from' => $from_address,
                'to' => $usersdetails->email,
                'subject' => 'Assets Manager',
                'message' => $message,
                'template' => 'reset-password',
                'status' => 'pre-send',
                'store_website_id' => null,
            ]);

            // Send email
            SendEmail::dispatch($email)->onQueue('send_email');
            Session::flash('success', 'Assets manager email send successfully');
        } catch (\Throwable $th) {
            $emails = Email::latest('created_at')->first();
            $emails->error_message = $th->getMessage();
            $emails->save();
            Session::flash('error', $th->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Assets manager record permission
     */
    public function assetsManagerRecordPermission(Request $request): RedirectResponse
    {
        //Delete existing records
        $existsRec = AssetManagerLinkUser::select(DB::raw('group_concat( id) as linkId'))->where('asset_manager_id', $request->assets_manager_id)->first();
        if (! empty($existsRec->linkId)) {
            AssetManagerLinkUser::whereIn('id', explode(',', $existsRec->linkId))->delete();
        }

        //Insert new records
        $assetManagerLinkArr = [];
        if (isset($request->user_name)) {
            foreach ($request->user_name as $key => $value) {
                $assetManagerLinkArr[] = ['user_id' => $value, 'asset_manager_id' => $request->assets_manager_id];
            }
            AssetManagerLinkUser::insert($assetManagerLinkArr);
            if (! empty($existsRec->linkId)) {
                Session::flash('success', 'Permission updated successfully');
            } else {
                Session::flash('success', 'Permission added successfully');
            }
        } else {
            Session::flash('success', 'Permission removed successfully');
        }

        return redirect()->back();
    }

    /**
     * Assets manager link users Ids
     */
    public function linkUserList(Request $request): JsonResponse
    {
        $linkuser = AssetManagerLinkUser::select(DB::raw('group_concat(DISTINCT user_id) as userids'))->distinct()->where('asset_manager_id', $request->asset_id)->first();

        return response()->json(['code' => 200, 'data' => $linkuser, 'message' => 'Assets manager data link user data fetch successfully']);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $asset_manager = AssetsManager::find($request->asset_id);
            if ($asset_manager) {
                if ($asset_manager->active == 1) {
                    $asset_manager->active = 0;
                    $asset_manager->save();
                    UserEvent::where('asset_manager_id', $asset_manager->id)->forceDelete();
                } else {
                    $asset_manager->active = 1;
                    $asset_manager->save();
                }

                return response()->json(['status' => true, 'message' => 'Status updated']);
            } else {
                throw new Exception('Asset not found');
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error while updating status']);
        }
    }

    public function assetManamentUsers(request $request): JsonResponse
    {
        $html = '';

        $users = User::get();

        $i = 1;
        if (count($users) > 0) {
            foreach ($users as $user) {
                $html .= '<tr>';
                $html .= '<td>'.$user->id.'</td>';
                $html .= '<td>'.$user->name.'</td>';
                $html .= '<td>'.$user->email.'</td>';
                $html .= '</tr>';
                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="3">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function assetManamentUsersAccess(request $request): JsonResponse
    {
        $html = '';

        if (! empty($request->assets_management_id)) {
            $user_accesses = new AssetManagerUserAccess;
            $user_accesses = $user_accesses->leftJoin('users', 'users.id', 'asset_manager_user_accesses.user_id')->where('assets_management_id', $request->assets_management_id)->select('asset_manager_user_accesses.*', 'users.name AS selectedUser')->get();

            $i = 1;
            if (count($user_accesses) > 0) {
                foreach ($user_accesses as $user_access) {
                    $html .= '<tr>';
                    $html .= '<td>'.$i.'</td>';
                    $html .= '<td>'.$user_access->selectedUser.'</td>';
                    $html .= '<td>'.$user_access->username.'</td>';
                    $html .= '<td>'.$user_access->password.'</td>';
                    $html .= '<td>'.$user_access->created_at.'</td>';
                    $html .= '<td><button type="button" data-id="'.$user_access->id.'" class="btn user-access-request-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';
                    $html .= '<td><button type="button" data-id="'.$user_access->id.'" class="btn user-access-response-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';
                    $html .= '<td> <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="deleteUserAccess('.$user_access->id.')"><i class="fa fa-trash"></i></button></td>';
                    $html .= '</tr>';
                    $i++;
                }

                return response()->json(['html' => $html, 'success' => true], 200);
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="8">Record not found</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="8">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function createUserAccess(Request $request): JsonResponse
    {
        try {
            // New Script
            $action = 'add';
            $SFTP = true;
            $ssh = true;
            $server = strval($request->server_var);

            $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

            if ($request->login_type == 'key') {
                $cmd = "bash $scriptsPath"."manageusers.sh -f \"$action\" -s \"$server\" -u \"$request->username\" -p \"$request->password\" -l \"$request->login_type\" -t \"$SFTP\" -b \"$ssh\" -r \"$request->key_type\" -R \"$request->user_role\" 2>&1";
            } else {
                $cmd = "bash $scriptsPath"."manageusers.sh -f \"$action\" -s \"$server\" -u \"$request->username\" -p \"$request->password\" -l \"$request->login_type\" -t \"$SFTP\" -b \"$ssh\" -R \"$request->user_role\" 2>&1";
            }
            // NEW Script
            $result = exec($cmd, $output, $return_var);

            $useraccess = AssetManagerUserAccess::create([
                'assets_management_id' => $request->assets_management_id,
                'user_id' => $request->user_id,
                'created_by' => Auth::user()->id,
                'username' => $request->username,
                'password' => $request->password,
                'request_data' => $cmd,
                'response_data' => json_encode($result),
                'usernamehost' => $request->usernamehost,
                'login_type' => $request->login_type,
                'key_type' => $request->key_type,
                'user_role' => $request->user_role,
            ]);

            return response()->json(['code' => 200, 'data' => $useraccess, 'message' => 'User Access has been created successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function deleteUserAccess(Request $request): JsonResponse
    {
        try {
            $user_access = AssetManagerUserAccess::where('id', $request->id)->first();

            if (! empty($user_access)) {
                // New Script
                $action = 'delete';
                $SFTP = true;
                $ssh = true;
                $server = 'demo.mio-moda.com';

                $scriptsPath = getenv('DEPLOYMENT_SCRIPTS_PATH');

                if ($user_access->login_type == 'key') {
                    $cmd = "bash $scriptsPath"."manageusers.sh -f \"$action\" -s \"$server\" -u \"$user_access->username\" -p \"$user_access->password\" -l \"$user_access->login_type\" -t \"$SFTP\" -b \"$ssh\" -r \"$user_access->key_type\" -R \"$user_access->user_role\" 2>&1";
                } else {
                    $cmd = "bash $scriptsPath"."manageusers.sh -f \"$action\" -s \"$server\" -u \"$user_access->username\" -p \"$user_access->password\" -l \"$user_access->login_type\" -t \"$SFTP\" -b \"$ssh\" -R \"$user_access->user_role\" 2>&1";
                }

                // NEW Script
                $result = exec($cmd, $output, $return_var);

                $access = AssetManagerUserAccess::find($request->id);
                $access->delete();

                return response()->json(['code' => 200, 'message' => 'User Access has been deleted successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong. Please try again.']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function assetsUserList(Request $request)
    {
        $dataDropdown = User::pluck('name', 'id')->toArray();

        // Get the user input
        $input = $_GET['term'];

        // Filter tags based on user input
        $filteredTags = array_filter($dataDropdown, function ($tag) use ($input) {
            return stripos($tag, $input) !== false;
        });

        // Return the filtered tags as JSON
        echo json_encode($filteredTags);
    }

    public function userAccessRequest($id): JsonResponse
    {
        $userAccessRequest = AssetManagerUserAccess::findorFail($id);

        return response()->json([
            'status' => true,
            'data' => $userAccessRequest,
            'request_data' => $userAccessRequest['request_data'],
            'response_data' => $userAccessRequest['response_data'],
            'message' => 'Data get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function createTerminalUserAccess(AssetsManagerCreateTerminalUserAccessRequest $request): JsonResponse
    {
        try {
            $useraccess = AssetManagerTerminalUserAccess::create([
                'assets_management_id' => $request->assets_management_id,
                'created_by' => Auth::user()->id,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            return response()->json(['code' => 200, 'data' => $useraccess, 'message' => 'Terminal User Access has been created successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function assetManamentTerminalUsersAccess(request $request): JsonResponse
    {
        $html = '';

        if (! empty($request->assets_management_id)) {
            $user_accesses = new AssetManagerTerminalUserAccess;
            $user_accesses = $user_accesses->where('assets_management_id', $request->assets_management_id)->select('asset_manager_terminal_user_accesses.*')->orderByDesc('id')->get();

            $i = 1;
            if (count($user_accesses) > 0) {
                foreach ($user_accesses as $user_access) {
                    $html .= '<tr>';
                    $html .= '<td>'.$i.'</td>';
                    $html .= '<td>';
                    $html .= '<div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="user_access_username" placeholder="Username" value="'.$user_access->username.'" id="user_access_username_'.$user_access->id.'">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="updateUsernamePassword('.$user_access->id.', 1)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                                </div>
                            </div>';
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= '<div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="user_access_password" placeholder="Password" value="'.$user_access->password.'" id="user_access_password_'.$user_access->id.'">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="updateUsernamePassword('.$user_access->id.',2)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                                </div>
                            </div>';
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= '<div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_'.$user_access->id.'">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks('.$user_access->id.')"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-id="'.$user_access->id.'" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>';
                    $html .= '</td>';
                    $html .= '<td>'.$user_access->created_at.'</td>';
                    $html .= '<td> <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="deleteTerminalUserAccess('.$user_access->id.')"><i class="fa fa-trash"></i></button></td>';
                    $html .= '</tr>';
                    $i++;
                }

                return response()->json(['html' => $html, 'success' => true], 200);
            } else {
                $html .= '<tr>';
                $html .= '<td colspan="8">Record not found</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="8">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function deleteTerminalUserAccess(Request $request): JsonResponse
    {
        try {
            $user_access = AssetManagerTerminalUserAccess::where('id', $request->id)->first();

            if (! empty($user_access)) {
                $access = AssetManagerTerminalUserAccess::find($request->id);
                $access->delete();

                return response()->json(['code' => 200, 'message' => 'Terminal User Access has been deleted successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Something went wrong. Please try again.']);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function saveRemarks(SaveRemarksAssetsManagerRequest $request): JsonResponse
    {
        $post = $request->all();

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        AssetManagerTerminalUserAccessRemakrs::create($input);

        return response()->json(['code' => 200, 'data' => $input, 'message' => 'Remarks added successfully']);
    }

    public function getRemarksHistories(Request $request): JsonResponse
    {
        $datas = AssetManagerTerminalUserAccessRemakrs::with(['user'])
            ->where('amtua_id', $request->amtua_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateUsernamePassword(Request $request): JsonResponse
    {
        try {
            $post = $request->all();

            $this->validate($request, [
                'amtua_id' => 'required',
                'type' => 'required',
                'username' => 'required|unique:asset_manager_terminal_user_accesses,username,'.$request->amtua_id.',id',
                'password' => 'required',
            ]);

            $amtua = AssetManagerTerminalUserAccess::find($request->amtua_id);

            if ($request->type == 1) {
                $amtua->username = $request->username;
            } else {
                $amtua->password = $request->password;
            }
            $amtua->save();

            return response()->json(['code' => 200, 'data' => $post, 'message' => 'User access has been updated successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
