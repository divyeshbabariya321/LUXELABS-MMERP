<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoogleDialogFlowStoreRequest;
use App\Http\Requests\GoogleDialogFlowUpdateRequest;
use App\Models\GoogleDialogAccount;
use App\StoreWebsite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader;

class GoogleDialogFlowController extends Controller
{
    /**
     * Get all the dialogflow accounts
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $google_dialog_accounts = GoogleDialogAccount::with(['storeWebsite'])->orderByDesc('id')->get();
        $store_websites = StoreWebsite::all();

        return view('google-dialogflow.index', compact('google_dialog_accounts', 'store_websites'));
    }

    /**
     * Create a new account in ERP with client Id & Secret.
     */
    public function store(GoogleDialogFlowStoreRequest $request): RedirectResponse
    {
        try {
            $serviceFile = MediaUploader::fromSource($request->file('service_file'))
                ->toDisk('s3_social_media')
                ->toDirectory('googleDialogService/')->upload();
            GoogleDialogAccount::create([
                'service_file' => $serviceFile->getAbsolutePath(),
                'site_id' => $request->get('site_id'),
                'project_id' => $request->get('project_id'),
                'default_selected' => $request->get('default_account'),
                'email' => $request->get('email'),
            ]);

            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account added successfully!');
        } catch (Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Update a account in ERP with client Id & Secret.
     */
    public function update(GoogleDialogFlowUpdateRequest $request): RedirectResponse
    {
        try {
            $googleAccount = GoogleDialogAccount::where('id', $request->get('account_id'))->first();
            if (! $googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleAccount->site_id = $request->get('edit_site_id');
            $googleAccount->project_id = $request->get('edit_project_id');
            $googleAccount->default_selected = $request->get('default_account');
            $googleAccount->email = $request->get('edit_email');
            if ($request->hasFile('edit_service_file')) {
                $serviceFile = MediaUploader::fromSource($request->file('edit_service_file'))
                    ->toDisk('s3_social_media')
                    ->toDirectory('googleDialogService/')->upload();
                $googleAccount->service_file = $serviceFile->getAbsolutePath();
            }
            $googleAccount->save();

            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account updated successfully!');
        } catch (Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a account in ERP with client Id & Secret.
     *
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): RedirectResponse
    {
        try {
            $googleAccount = GoogleDialogAccount::where('id', $id)->first();
            if (! $googleAccount) {
                return Redirect::route('google-chatbot-accounts')->with('error', 'Account not found');
            }
            $googleAccount->delete();

            return Redirect::route('google-chatbot-accounts')->with('success', 'google dialog account deleted successfully!');
        } catch (Exception $e) {
            return Redirect::route('google-chatbot-accounts')->with('error', $e->getMessage());
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
            $googleAccount = GoogleDialogAccount::where('id', $id)->first();
            if (! $googleAccount) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }

            return response()->json(['status' => true, 'message' => 'google dialog account deleted successfully!', 'data' => $googleAccount->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
