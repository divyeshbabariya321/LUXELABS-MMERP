<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsAppOfficialController;
use App\Http\Requests\WhatsappBusinessAccountCreateAccountRequest;
use App\Http\Requests\WhatsappBusinessAccountUpdateAccountRequest;
use App\Marketing\WhatsappBusinessAccounts;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Exception;

class WhatsappBusinessAccountController extends Controller
{
    /**
     * Get all whatsapp business account.
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $whatsappBusinessAccounts = WhatsappBusinessAccounts::where(function ($query) use ($request) {
            if ($request->has('business') && $request->business) {
                $query->orWhere(function ($query) use ($request) {
                    $query->where('business_phone_number', 'like', '%'.$request->business.'%');
                    $query->where('business_account_id', 'like', '%'.$request->business.'%');
                    $query->where('business_access_token', 'like', '%'.$request->business.'%');
                });
            }
        })->paginate(Setting::get('pagination'), '*', 'whatsapp_accounts');

        return view('marketing.whatsapp-business-accounts.index', compact('whatsappBusinessAccounts'));
    }

    /**
     * Create a whatsapp business account
     */
    public function createAccount(WhatsappBusinessAccountCreateAccountRequest $request): RedirectResponse
    {
        try {
            $account = WhatsappBusinessAccounts::create([
                'business_phone_number' => $request->business_phone_number,
                'business_account_id' => $request->business_account_id,
                'business_access_token' => $request->business_access_token,
                'business_phone_number_id' => $request->business_phone_number_id,
            ]);
            $whatsappApiController = new WhatsAppOfficialController($account->id);
            $whatsappApiController->updateBusinessProfile($request->all());
            $request->file('profile_picture_url')->getPathname();

            return Redirect::route('whatsapp.business.account.index')
                ->with('success', 'Whatsapp business account added successfully');
        } catch (Exception $e) {
            return Redirect::route('whatsapp.business.account.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update a whatsapp business account
     *
     * @param  $id
     */
    public function updateAccount(WhatsappBusinessAccountUpdateAccountRequest $request): RedirectResponse
    {
        try {
            $whatsappBusinessAccount = WhatsappBusinessAccounts::find($request->edit_id);
            if (! $whatsappBusinessAccount) {
                return Redirect::route('whatsapp.business.account.index')
                    ->with('error', 'No account found');
            }
            $whatsappBusinessAccount->business_phone_number = $request->business_phone_number;
            $whatsappBusinessAccount->business_account_id = $request->business_account_id;
            $whatsappBusinessAccount->business_access_token = $request->business_access_token;
            $whatsappBusinessAccount->business_phone_number_id = $request->business_phone_number_id;
            $whatsappBusinessAccount->save();
            $whatsappApiController = new WhatsAppOfficialController($whatsappBusinessAccount->id);
            $whatsappApiController->updateBusinessProfile($request->all());

            return Redirect::route('whatsapp.business.account.index')
                ->with('success', 'Whatsapp business account updated successfully');
        } catch (Exception $e) {
            return Redirect::route('whatsapp.business.account.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a whatsapp business account
     *
     * @param  mixed  $id
     */
    public function deleteAccount(Request $request, $id): RedirectResponse
    {
        try {
            $whatsappBusinessAccount = WhatsappBusinessAccounts::find($id);
            if (! $whatsappBusinessAccount) {
                return Redirect::route('whatsapp.business.account.index')
                    ->with('error', 'No account found');
            }
            $whatsappBusinessAccount->delete();

            return Redirect::route('whatsapp.business.account.index')
                ->with('success', 'Whatsapp business account deleted successfully');
        } catch (Exception $e) {
            return Redirect::route('whatsapp.business.account.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get the account by id
     *
     * @param  mixed  $id
     */
    public function getAccount(Request $request, $id): JsonResponse
    {
        try {
            $account = WhatsappBusinessAccounts::findOrFail($id);
            if (! $account) {
                return response()->json(['status' => false, 'message' => 'Account not found']);
            }

            return response()->json(['status' => true, 'message' => 'Account found', 'data' => $account->toArray()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
