<?php

namespace App\Http\Controllers;

use App\ConfigRefactor;
use App\ConfigRefactorRemarkHistory;
use App\ConfigRefactorSection;
use App\ConfigRefactorStatus;
use App\ConfigRefactorStatusHistory;
use App\ConfigRefactorUserHistory;
use App\Http\Requests\StoreConfigRefactorRequest;
use App\Models\ZabbixWebhookData;
use App\StoreWebsite;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConfigRefactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $section = $request->get('section');
        $section_type = $request->get('section_type');
        $status = $request->get('status');

        $configRefactors = ConfigRefactor::with(['storeWebsite', 'configRefactorSection'])
            ->select('config_refactors.*')
            ->join('config_refactor_sections', 'config_refactor_sections.id', 'config_refactors.config_refactor_section_id');

        if ($section) {
            $configRefactors = $configRefactors->where('config_refactor_section_id', $section);
        }

        if ($section_type) {
            $configRefactors = $configRefactors->where('config_refactor_sections.type', $section_type);
        }

        if ($status) {
            $configRefactors = $configRefactors->where(function ($q) use ($status) {
                $q->orWhere('config_refactors.step_1_status', 'LIKE', '%'.$status.'%')
                    ->orWhere('config_refactors.step_2_status', 'LIKE', '%'.$status.'%')
                    ->orWhere('config_refactors.step_3_status', 'LIKE', '%'.$status.'%')
                    ->orWhere('config_refactors.step_3_1_status', 'LIKE', '%'.$status.'%')
                    ->orWhere('config_refactors.step_3_2_status', 'LIKE', '%'.$status.'%');
            });
        }

        $configRefactors = $configRefactors->latest('config_refactors.created_at')->paginate(10);

        $configRefactorStatuses = ConfigRefactorStatus::pluck('name', 'id')->toArray();
        $configRefactorSections = ConfigRefactorSection::pluck('name', 'id')->toArray();
        $users = User::select('name', 'id')->role('Developer')->orderby('name', 'asc')->where('is_active', 1)->get();
        $users = $users->pluck('name', 'id');
        $store_websites = StoreWebsite::get()->pluck('website', 'id');

        return view('config-refactor.index', compact('configRefactors', 'configRefactorStatuses', 'users', 'configRefactorSections', 'store_websites'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function store(StoreConfigRefactorRequest $request): JsonResponse
    {
        // Validation Part

        $data = $request->except('_token');

        // Save
        $configRefactorSection = new ConfigRefactorSection;
        $configRefactorSection->name = $data['name'];
        $configRefactorSection->type = $data['type'];
        $configRefactorSection->save();

        // Save one entry in config refactor table
        $configRefactor = new ConfigRefactor;
        $configRefactor->store_website_id = $data['store_website_id'];
        $configRefactor->config_refactor_section_id = $configRefactorSection->id;
        $configRefactor->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Config refactor has been created!',
            ]
        );
    }

    public function duplicateCreate(Request $request): JsonResponse
    {
        $configRefactors = ConfigRefactor::find(explode(',', $request->config_refactors));

        if (! $request->store_website_id) {
            return response()->json(['status' => false, 'message' => 'No website selected']);
        }

        if ($configRefactors) {
            foreach ($configRefactors as $configRefactor) {
                foreach ($request->store_website_id as $store_website_id) {
                    ConfigRefactor::firstOrCreate([
                        'store_website_id' => $store_website_id,
                        'config_refactor_section_id' => $configRefactor->config_refactor_section_id,
                    ]);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Duplicate entries created successfully']);
    }

    public function storeStatus(Request $request): JsonResponse
    {
        $input = $request->except(['_token']);

        $data = ConfigRefactorStatus::create($input);

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

    public function getRemarks($config_refactor, $column_name): JsonResponse
    {
        $remarks = ConfigRefactorRemarkHistory::with(['user'])
            ->where('config_refactor_id', $config_refactor)
            ->where('column_name', $column_name)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function storeRemark(Request $request): JsonResponse
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $configRefactor->{$request->column} = $request->remark;
            $configRefactor->save();

            $history = new ConfigRefactorRemarkHistory;
            $history->config_refactor_id = $configRefactor->id;
            $history->column_name = $request->column;
            $history->remarks = $request->remark;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Remark updated successfully',
                ], 200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Remark not updated.',
                ], 500
            );
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_status = $configRefactor->{$request->column};

            $configRefactor->{$request->column} = $request->status;

            $configRefactor->save();

            $history = new ConfigRefactorStatusHistory;
            $history->config_refactor_id = $configRefactor->id;
            $history->column_name = $request->column;
            $history->old_status_id = $old_status;
            $history->new_status_id = $request->status;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Status updated successfully',
                ], 200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Status not updated.',
                ], 500
            );
        }
    }

    public function getStatuses($config_refactor, $column_name): JsonResponse
    {
        $statuses = ConfigRefactorStatusHistory::with(['user', 'newStatus', 'oldStatus'])
            ->where('config_refactor_id', $config_refactor)
            ->where('column_name', $column_name)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $statuses,
            'message' => 'Status get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateUser(Request $request): JsonResponse
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_user_id = $configRefactor->user_id;
            $configRefactor->user_id = $request->user_id;
            $configRefactor->save();

            $history = new ConfigRefactorUserHistory;
            $history->config_refactor_id = $configRefactor->id;
            $history->old_user = $old_user_id;
            $history->new_user = $request->user_id;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'User updated successfully',
                ], 200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not updated.',
                ], 500
            );
        }
    }

    public function getUsers($config_refactor): JsonResponse
    {
        $users = ConfigRefactorUserHistory::with(['user', 'newUser', 'oldUser'])
            ->where('config_refactor_id', $config_refactor)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $users,
            'message' => 'User get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function issuesSummary(Request $request): JsonResponse
    {
        $perPage = 10; // Number of records per page

        $zabbixWebhookDatas = ZabbixWebhookData::latest();
        $zabbixWebhookDatas = $zabbixWebhookDatas->where('severity', 'high');

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate($perPage);

        return response()->json($zabbixWebhookDatas);
    }
}
