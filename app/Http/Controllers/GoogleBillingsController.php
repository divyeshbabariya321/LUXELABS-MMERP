<?php

namespace App\Http\Controllers;

use App\GoogleBillingMaster;
use App\GoogleBillingProjects;
use App\Http\Requests\GoogleBillingsStoreProjectRequest;
use App\Http\Requests\GoogleBillingsStoreRequest;
use App\Http\Requests\GoogleBillingsUpdateProjectRequest;
use App\Services\GoogleCloudBigQueryBillingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader;

class GoogleBillingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $googleBillingAccounts = GoogleBillingMaster::orderByDesc('id')->get()->paginate(config('constants.paginate'));
        $serviceType = config('constants.GOOGLE_SERVICE_ACCOUNTS');

        return view('google-billing.index', compact('googleBillingAccounts', 'serviceType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GoogleBillingsStoreRequest $request): RedirectResponse
    {
        try {
            $serviceFile = MediaUploader::fromSource($request->file('service_file'))
                // ->toDisk('s3_social_media')
                ->toDirectory('googleBillingService/')->upload();

            GoogleBillingMaster::create([
                'billing_account_name' => $request->get('billing_account_name'),
                'service_file' => $serviceFile->getAbsolutePath(),
                'email' => $request->get('email'),
            ]);

            return Redirect::route('google.billing.index')->with('success', 'google account added to billing successfully!');
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeProject(GoogleBillingsStoreProjectRequest $request): RedirectResponse
    {
        try {
            GoogleBillingProjects::create([
                'google_billing_master_id' => $request->get('google_billing_master_id'),
                'service_type' => $request->get('service_type'),
                'project_id' => $request->get('project_id'),
                'dataset_id' => $request->get('dataset_id'),
                'table_id' => $request->get('table_id'),
            ]);

            return Redirect::route('google.billing.project.list')->with('success', 'google project added to billing successfully!');
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage();
        }
    }

    /**
     * Display the specified resource.
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
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'edit_billing_account_name' => 'required',
                'edit_email' => 'required|email',
            ]);
            if ($request->hasFile('edit_service_file')) {
                $validator = Validator::make($request->all(), [
                    'edit_service_file' => 'required|mimes:json',
                ]);
            }
            if ($validator->fails()) {
                return Redirect::route('google.billing.index')->withInput()->withErrors($validator);
            }
            $googleBillingAccount = GoogleBillingMaster::where('id', $request->get('id'))->first();
            if (! $googleBillingAccount) {
                return Redirect::route('google.billing.index')->with('error', 'Account not found');
            }

            $googleBillingAccount->billing_account_name = $request->get('edit_billing_account_name');
            $googleBillingAccount->email = $request->get('edit_email');
            if ($request->hasFile('edit_service_file')) {
                $serviceFile = MediaUploader::fromSource($request->file('edit_service_file'))
                    // ->toDisk('s3_social_media')
                    ->toDirectory('googleBillingService/')->upload();
                $googleBillingAccount->service_file = $serviceFile->getAbsolutePath();
            }
            $googleBillingAccount->save();

            return Redirect::route('google.billing.index')->with('success', 'google account updated successfully!');
        } catch (Exception $e) {
            return Redirect::route('google.billing.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProject(GoogleBillingsUpdateProjectRequest $request, int $id): RedirectResponse
    {
        try {
            $googleBillingProject = GoogleBillingProjects::where('id', $request->get('id'))->first();
            if (! $googleBillingProject) {
                return Redirect::route('google.billing.project.list')->with('error', 'Project not found');
            }

            $googleBillingProject->google_billing_master_id = $request->get('edit_google_billing_master_id');
            $googleBillingProject->service_type = $request->get('edit_service_type');
            $googleBillingProject->project_id = $request->get('edit_project_id');
            $googleBillingProject->dataset_id = $request->get('edit_dataset_id');
            $googleBillingProject->table_id = $request->get('edit_table_id');
            $googleBillingProject->save();

            return Redirect::route('google.billing.project.list')->with('success', 'google project updated successfully!');
        } catch (Exception $e) {
            return Redirect::route('google.billing.project.list')->with('error', $e->getMessage());
        }
    }

    public function projectList(Request $request, $id = '')
    {
        try {
            $serviceType = config('constants.GOOGLE_SERVICE_ACCOUNTS');
            $googleBillingMaster = GoogleBillingMaster::orderBy('billing_account_name')->pluck('billing_account_name', 'id')->toArray();

            $googleBillingProjects = new GoogleBillingProjects;
            if (! empty($id)) {
                $googleBillingProjects = $googleBillingProjects->where('google_billing_master_id', $id);
            }
            $googleBillingProjects = $googleBillingProjects->get()->paginate(config('constants.paginate'));
            foreach ($googleBillingProjects as $key => $googleBillingProject) {
                $googleCloudBigQueryService = new GoogleCloudBigQueryBillingService;
                $googleBillingProjects[$key]['billing_detail'] = $googleCloudBigQueryService->showBillingAmount($googleBillingProject);
            }

            return view('google-billing.list', compact('googleBillingProjects', 'serviceType', 'googleBillingMaster'));
        } catch (Exception $e) {
            return Redirect::route('google.billing.index')->with('error', $e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $googleBillingMaster = GoogleBillingMaster::orderBy('billing_account_name')->get();
            $googleBillingAccounts = GoogleBillingProjects::where('google_billing_master_id', $id)->get();
            if (! $googleBillingAccounts) {
                Redirect::route('google-billing/index')->with('error', 'Account not found');
            }

            return view('google-billing.detail', compact('googleBillingAccounts', 'googleBillingMaster'));
        } catch (Exception $e) {
            return Redirect::route('google-billing/index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $id): RedirectResponse
    {
        try {
            $googleBillingAccount = GoogleBillingMaster::where('id', $id)->first();
            if (! $googleBillingAccount) {
                return Redirect::route('google-billing/index')->with('error', 'Account not found');
            }
            $googleBillingAccount->delete();

            return Redirect::route('google-billing/index')->with('success', 'google dialog account deleted successfully!');
        } catch (Exception $e) {
            return Redirect::route('google-billing/index')->with('error', $e->getMessage());
        }
    }

    /**
     * Get account details
     *
     * @param  mixed  $id
     */
    public function get(Request $request, $id): JsonResponse
    {
        try {
            $googleBillingAccount = GoogleBillingMaster::where('id', $id)->first();
            if (! $googleBillingAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }

            return response()->json(['status' => true, 'message' => 'google account found!', 'data' => $googleBillingAccount->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get account details
     *
     * @param  mixed  $id
     */
    public function getProject(Request $request, $id): JsonResponse
    {
        try {
            $googleBillingProject = GoogleBillingProjects::where('id', $id)->first();
            if (! $googleBillingProject) {
                return response()->json(['status' => false, 'message' => 'Project not found']);
            }

            return response()->json(['status' => true, 'message' => 'google project found!', 'data' => $googleBillingProject->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
