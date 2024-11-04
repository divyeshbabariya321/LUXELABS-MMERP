<?php

namespace App\Http\Controllers\AffiliateMarketing;

use App\AffiliateCommissions;
use App\AffiliateConversions;
use App\AffiliateCustomers;
use App\AffiliateGroups;
use App\AffiliateMarketers;
use App\AffiliateMarketingLogs;
use App\AffiliatePayments;
use App\AffiliatePrograms;
use App\AffiliateProviderAccounts;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\AffiliateMarketing\AffiliateCreateRequest;
use App\Http\Requests\AffiliateMarketing\CommissionUpdateRequest;
use App\Http\Requests\AffiliateMarketing\ConversionCreateRequest;
use App\Http\Requests\AffiliateMarketing\ConversionUpdateRequest;
use App\Http\Requests\AffiliateMarketing\CreateAffiliateGroupRequest;
use App\Http\Requests\AffiliateMarketing\CustomerCreateRequest;
use App\Http\Requests\AffiliateMarketing\PaymentsCreateRequest;
use App\Http\Requests\AffiliateMarketing\UpdateAffiliateGroupRequest;
use App\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Affiliate Marketing data controller to manage multiple affiliate providers and accounts
 */
class AffiliateMarketingDataController extends Controller
{
    const NO_PROVIDE_FOUND = 'No provider found';

    const ACCOUNT_NOT_FOUND = 'Account Not found';

    /**
     * Get details for provider account
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function index(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersGroups = AffiliateGroups::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('group_name') && $request->group_name) {
                    $query->where('title', 'like', '%'.$request->group_name.'%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');

            return view('affiliate-marketing.providers.index', compact('providersGroups', 'provider'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    /**
     * Create new affiliate group on provider and save in the database
     */
    public function createAffiliateGroup(CreateAffiliateGroupRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliateGroup(['title' => $request->title]);
                if ($responseData['status']) {
                    AffiliateGroups::create([
                        'title' => $request->title,
                        'affiliate_provider_group_id' => $responseData['data']['id'],
                        'affiliate_account_id' => $request->affiliate_account_id,
                    ]);
                    $this->logActivity('Create Affiliate Group', $responseData);

                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group added successfully');
                }
                $this->logActivity('Create Affiliate Group', $responseData);

                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate Group', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Create Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate group on provider and save in the database
     *
     * @param  mixed  $id
     */
    public function updateAffiliateGroup(UpdateAffiliateGroupRequest $request, $id): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $groupData = AffiliateGroups::find($id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateGroup($groupData->affiliate_provider_group_id, ['title' => $request->title]);
                if ($responseData['status']) {
                    $groupData->title = $request->title;
                    $groupData->save();
                    $this->logActivity('Update Affiliate Group', $responseData);

                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group updated successfully');
                }
                $this->logActivity('Update Affiliate Group', $responseData);

                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Group', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Update Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get affiliate group details
     *
     * @param  mixed  $id
     */
    public function getAffiliateGroup(Request $request, $id): JsonResponse
    {
        try {
            $group = AffiliateGroups::findOrFail($id);
            if (! $group) {
                $response = ['status' => false, 'message' => 'Group not found'];
                $this->logActivity('Get Affiliate Group', $response);

                return response()->json($response);
            }
            $response = ['status' => true, 'message' => 'Group found', 'data' => $group->toArray()];
            $this->logActivity('Get Affiliate Group', $response);

            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate Group', $response);

            return response()->json($response);
        }
    }

    public function syncData(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getSyncData();
                _p($responseData);
                exit;
                if ($responseData['status']) {
                    $this->logActivity('Sync Affiliate Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate group updated successfully');
                }
                $this->logActivity('Sync Affiliate Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync Affiliate Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync Affiliate Group', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get List of all programmes in a provider account
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function programIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersProgrammes = AffiliatePrograms::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('title') && $request->title) {
                    $query->where('title', 'like', '%'.$request->title.'%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');

            return view('affiliate-marketing.providers.programes', compact('providersProgrammes', 'provider'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    /**
     * Get Commission type list for program
     */
    public function programCommissionType(Request $request): JsonResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getProgramCommissionType($request->program);

                if ($responseData['status']) {
                    return response()->json($responseData['data']);
                }
            }

            return response()->json([]);
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Sync Programmes from API to DB
     */
    public function programSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getProgrammes();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $programme) {
                        $programmeData = AffiliatePrograms::where('affiliate_program_id', $programme['id'])->first();
                        if (! $programmeData) {
                            AffiliatePrograms::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_program_id' => $programme['id'],
                                'currency' => $programme['currency'],
                                'title' => $programme['title'],
                                'cookie_time' => $programme['cookie_time'],
                                'default_landing_page_url' => $programme['default_landing_page_url'],
                                'recurring' => (bool) $programme['recurring'],
                                'recurring_cap' => $programme['recurring_cap'],
                                'recurring_period_days' => $programme['recurring_period_days'],
                                'program_category_id' => $programme['program_category'] ? $programme['program_category']['id'] : null,
                                'program_category_identifier' => $programme['program_category'] ? $programme['program_category']['identifier'] : null,
                                'program_category_title' => $programme['program_category'] ? $programme['program_category']['title'] : null,
                                'program_category_is_admitad_suitable' => $programme['program_category'] && (bool) $programme['program_category']['is_admitad_suitable'],
                            ]);
                        } else {
                            $programmeData->currency = $programme['currency'];
                            $programmeData->title = $programme['title'];
                            $programmeData->cookie_time = $programme['cookie_time'];
                            $programmeData->default_landing_page_url = $programme['default_landing_page_url'];
                            $programmeData->recurring = (bool) $programme['recurring'];
                            $programmeData->recurring_cap = $programme['recurring_cap'];
                            $programmeData->recurring_period_days = $programme['recurring_period_days'];
                            $programmeData->program_category_id = $programme['program_category'] ? $programme['program_category']['id'] : null;
                            $programmeData->program_category_identifier = $programme['program_category'] ? $programme['program_category']['identifier'] : null;
                            $programmeData->program_category_title = $programme['program_category'] ? $programme['program_category']['title'] : null;
                            $programmeData->program_category_is_admitad_suitable = $programme['program_category'] && (bool) $programme['program_category']['is_admitad_suitable'];
                            $programmeData->save();
                        }
                    }
                    $this->logActivity('Sync Programme Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate programmes synced successfully');
                }
                $this->logActivity('Sync Programme Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync Programme Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync Programme Group', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.program.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get List of all commission in a provider account
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function commissionIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersCommissions = AffiliateCommissions::where(function ($query) use ($request, $provider) {
                $query->where('affiliate_account_id', $provider->id);
                if ($request->has('amount') && $request->amount) {
                    $query->where('amount', $request->amount);
                }
                if ($request->has('commission_type') && $request->commission_type) {
                    $query->where('commission_type', 'like', '%'.$request->commission_type.'%');
                }
                if ($request->has('kind') && $request->kind) {
                    $query->where('kind', 'like', '%'.$request->kind.'%');
                }
                if ($request->has('currency') && $request->currency) {
                    $query->where('currency', 'like', '%'.$request->currency.'%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');

            return view('affiliate-marketing.providers.comissions', compact('providersCommissions', 'provider'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    /**
     * Sync Commissions from API to DB
     */
    public function commissionSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getCommissions();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $commission) {
                        $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                        $affiliateMarketerData = null;
                        if ($commission['affiliate'] && $commission['affiliate']['id']) {
                            $affiliateMarketerData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                        }
                        if (! $commissionData) {
                            AffiliateCommissions::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_commission_id' => $commission['id'],
                                'amount' => $commission['amount'],
                                'approved' => (bool) $commission['approved'],
                                'affiliate_commission_created_at' => $commission['created_at'],
                                'commission_type' => $commission['commission_type'],
                                'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                'comment' => $commission['comment'],
                                'affiliate_conversion_id' => $commission['conversion'] && $commission['conversion']['id'],
                                'payout' => $commission['payout'],
                                'affiliate_marketer_id' => $affiliateMarketerData && $affiliateMarketerData->id,
                                'kind' => $commission['kind'],
                                'currency' => $commission['currency'],
                                'final' => $commission['final'],
                                'finalization_date' => isset($commission['finalization_date']),
                            ]);
                        } else {
                            $commissionData->affiliate_commission_id = $commission['id'];
                            $commissionData->amount = $commission['amount'];
                            $commissionData->approved = (bool) $commission['approved'];
                            $commissionData->affiliate_commission_created_at = $commission['created_at'];
                            $commissionData->commission_type = $commission['commission_type'];
                            $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                            $commissionData->comment = $commission['comment'];
                            $commissionData->affiliate_conversion_id = $commission['conversion'] && $commission['conversion']['id'];
                            $commissionData->payout = $commission['payout'];
                            $commissionData->affiliate_marketer_id = $affiliateMarketerData && $affiliateMarketerData->id;
                            $commissionData->kind = $commission['kind'];
                            $commissionData->currency = $commission['currency'];
                            $commissionData->final = $commission['final'];
                            $commissionData->finalization_date = isset($commission['finalization_date']);
                            $commissionData->save();
                        }
                    }
                    $this->logActivity('Sync commissions Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commissions synced successfully');
                }
                $this->logActivity('Sync commissions Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync commissions Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync commissions Data', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get affiliate commission details
     *
     * @param  mixed  $id
     */
    public function commissionGet(Request $request, $id): JsonResponse
    {
        try {
            $commission = AffiliateCommissions::findOrFail($id);
            if (! $commission) {
                $response = ['status' => false, 'message' => 'Commission not found'];
                $this->logActivity('Get Affiliate Commission', $response);

                return response()->json($response);
            }
            $response = ['status' => true, 'message' => 'Affiliate Commission found', 'data' => $commission->toArray()];
            $this->logActivity('Get Affiliate Commission', $response);

            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate Commission', $response);

            return response()->json($response);
        }
    }

    /**
     * Update affiliate commission on provider and save in the database
     */
    public function commissionUpdate(CommissionUpdateRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $commissionData = AffiliateCommissions::find($request->commission_id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateCommission($commissionData->affiliate_commission_id, ['amount' => $request->amount]);
                if ($responseData['status']) {
                    $commissionData->amount = $request->amount;
                    $commissionData->save();
                    $this->logActivity('Update Affiliate Commission', $responseData);

                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commission updated successfully');
                }
                $this->logActivity('Update Affiliate commission', $responseData);

                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate commission on provider and save in the database
     *
     * @param  mixed  $id
     */
    public function commissionApproveDisapprove(Request $request, $id): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $commissionData = AffiliateCommissions::find($id);
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateAffiliateApproveDisapprove($commissionData->affiliate_commission_id, ! $commissionData->approved);
                if ($responseData['status']) {
                    $commissionData->approved = ! $commissionData->approved;
                    $commissionData->save();
                    $this->logActivity('Update Affiliate Commission', $responseData);

                    return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate commission updated successfully');
                }
                $this->logActivity('Update Affiliate commission', $responseData);

                return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Update Affiliate Commission', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.commission.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get List of all affiliates in a provider account
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function affiliateIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersAffiliates = AffiliateMarketers::with('group')->where('affiliate_account_id', $provider->id)->where(function ($query) use ($request) {
                if ($request->has('name') && $request->name) {
                    $query->orWhere('firstName', 'like', '%'.$request->name.'%');
                    $query->orWhere('lastName', 'like', '%'.$request->name.'%');
                    $query->orWhere('email', 'like', '%'.$request->name.'%');
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_groups');
            $affiliateGroups = AffiliateGroups::where('affiliate_account_id', $provider->id)->get();
            $affiliateProgrammes = AffiliatePrograms::where('affiliate_account_id', $provider->id)->get();

            return view('affiliate-marketing.providers.affiliates', compact('providersAffiliates', 'provider', 'affiliateGroups', 'affiliateProgrammes'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    /**
     * Sync Affiliates from API to DB
     */
    public function affiliateSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAffiliates();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $affiliate) {
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $affiliate['id'])->first();
                        $groupData = AffiliateGroups::where('affiliate_provider_group_id', $affiliate['affiliate_group_id'])->first();
                        if (! $affiliateData) {
                            AffiliateMarketers::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_id' => $affiliate['id'],
                                'firstname' => $affiliate['firstname'],
                                'lastname' => $affiliate['lastname'],
                                'email' => $affiliate['email'],
                                'company_name' => $affiliate['company'] && $affiliate['company']['name'],
                                'company_description' => $affiliate['company'] && $affiliate['company']['description'],
                                'address_one' => $affiliate['address'] && $affiliate['address']['address'],
                                'address_two' => $affiliate['address'] && $affiliate['address']['address_two'],
                                'address_postal_code' => $affiliate['address'] && $affiliate['address']['postal_code'],
                                'address_city' => $affiliate['address'] && $affiliate['address']['city'],
                                'address_state' => $affiliate['address'] && $affiliate['address']['state'],
                                'address_country_code' => $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['code'],
                                'address_country_name' => $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['name'],
                                'meta_data' => $affiliate['meta_data'] ? serialize($affiliate['meta_data']) : null,
                                'parent_id' => $affiliate['parent_id'],
                                'affiliate_created_at' => $affiliate['created_at'],
                                'affiliate_group_id' => $groupData ? $groupData->id : null,
                                'promoted_at' => $affiliate['promoted_at'],
                                'promotion_method' => $affiliate['promotion_method'],
                            ]);
                        } else {
                            $affiliateData->affiliate_id = $affiliate['id'];
                            $affiliateData->firstname = $affiliate['firstname'];
                            $affiliateData->lastname = $affiliate['lastname'];
                            $affiliateData->email = $affiliate['email'];
                            $affiliateData->company_name = $affiliate['company'] && $affiliate['company']['name'];
                            $affiliateData->company_description = $affiliate['company'] && $affiliate['company']['description'];
                            $affiliateData->address_one = $affiliate['address'] && $affiliate['address']['address'];
                            $affiliateData->address_two = $affiliate['address'] && $affiliate['address']['address_two'];
                            $affiliateData->address_postal_code = $affiliate['address'] && $affiliate['address']['postal_code'];
                            $affiliateData->address_city = $affiliate['address'] && $affiliate['address']['city'];
                            $affiliateData->address_state = $affiliate['address'] && $affiliate['address']['state'];
                            $affiliateData->address_country_code = $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['code'];
                            $affiliateData->address_country_name = $affiliate['address'] && $affiliate['address']['country'] && $affiliate['address']['country']['name'];
                            $affiliateData->meta_data = $affiliate['meta_data'] ? serialize($affiliate['meta_data']) : null;
                            $affiliateData->parent_id = $affiliate['parent_id'];
                            $affiliateData->affiliate_created_at = $affiliate['created_at'];
                            $affiliateData->affiliate_group_id = $groupData ? $groupData->id : null;
                            $affiliateData->promoted_at = $affiliate['promoted_at'];
                            $affiliateData->promotion_method = $affiliate['promotion_method'];
                            $affiliateData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate synced successfully');
                }
                $this->logActivity('Sync affiliate Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync affiliate Data', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create new affiliate on provider and save in the database
     */
    public function affiliateCreate(AffiliateCreateRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->affiliate_account_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliate([
                    'firstname' => $request->firstName,
                    'lastname' => $request->lastName,
                    'email' => $request->email ?? null,
                    'company' => ['name' => $request->company_name, 'description' => $request->company_description],
                    'address' => [
                        'address' => $request->address_one, 'address_two' => $request->address_two, 'postal_code' => $request->address_postal_code,
                        'city' => $request->address_city, 'state' => $request->address_state, 'country' => [
                            'code' => $request->address_country_code, 'name' => $request->address_country_name,
                        ],
                    ],
                ]);
                if ($responseData['status']) {
                    $groupData = AffiliateGroups::where('id', $request->affiliate_group_id)->first();
                    $tapfiliate->setAffiliateGroupForAffiliate($responseData['data']['id'], ['group_id' => $groupData->affiliate_provider_group_id]);
                    AffiliateMarketers::create([
                        'affiliate_account_id' => $request->affiliate_account_id,
                        'affiliate_id' => $responseData['data']['id'],
                        'firstname' => $request->firstName,
                        'lastname' => $request->lastName,
                        'email' => $request->email,
                        'company_name' => $request->company_name,
                        'company_description' => $request->company_description,
                        'address_one' => $request->address_one,
                        'address_two' => $request->address_two,
                        'address_postal_code' => $request->address_postal_code,
                        'address_city' => $request->address_city,
                        'address_state' => $request->address_state,
                        'address_country_code' => $request->address_country_code,
                        'address_country_name' => $request->address_country_name,
                        'meta_data' => null,
                        'parent_id' => null,
                        'affiliate_created_at' => null,
                        'affiliate_group_id' => $groupData->id,
                        'promoted_at' => null,
                        'promotion_method' => null,
                    ]);
                    $this->logActivity('Create Affiliate', $responseData);

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate added successfully');
                } elseif ($responseData['errors']) {
                    $validationErrors = [];
                    foreach ($responseData['response']['errors'] as $err) {
                        if (strpos($err['message'], 'first name')) {
                            $validationErrors['firstName'] = [$err['message']];
                        } elseif (strpos($err['message'], 'last name')) {
                            $validationErrors['lastName'] = [$err['message']];
                        } elseif (strpos($err['message'], 'email')) {
                            $validationErrors['email'] = [$err['message']];
                        }
                    }

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('create_popup', true)
                        ->withErrors($validationErrors)
                        ->withInput();
                }

                $this->logActivity('Create Affiliate', $responseData);

                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Create Affiliate', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete affiliate on provider also in DB
     *
     * @param  mixed  $id
     */
    public function affiliateDelete(Request $request, $id): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (! $affiliate) {
                $this->logActivity('Delete Affiliate', ['status' => false, 'message' => 'Affiliate not found']);

                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->deleteAffiliate($affiliate->affiliate_id);
                if ($responseData['status']) {
                    $affiliate->delete();
                    $this->logActivity('Delete Affiliate', $responseData);

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate Deleted successfully');
                }
                $this->logActivity('Delete Affiliate', $responseData);

                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Affiliate', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get all payout methods for affiliates
     *
     * @param  mixed  $id
     */
    public function affiliatePayoutMethods(Request $request, $id): JsonResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (! $affiliate) {
                $response = ['status' => false, 'message' => 'Affiliate not found'];
                $this->logActivity('Get All Affiliate payout methods', $response);

                return response()->json($response);
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAllAffiliatePayoutMethods($affiliate->affiliate_id);
                if ($responseData['status']) {
                    $response = ['status' => true, 'message' => 'Affiliate payout methods found', 'data' => $responseData['data']];
                    $this->logActivity('Get Affiliate payout methods', $response);

                    return response()->json($response);
                }
                $response = ['status' => false, 'message' => 'Affiliate payout methods not found'];
                $this->logActivity('Get Affiliate payout methods', $response);

                return response()->json($response);
            }
            $response = ['status' => false, 'message' => 'Account not found'];
            $this->logActivity('Get Affiliate payout methods', $response);

            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => false, 'message' => $e->getMessage()];
            $this->logActivity('Get Affiliate payout methods', $response);

            return response()->json($response);
        }
    }

    /**
     * Update the payout method for affiliate
     *
     * @param  mixed  $id
     */
    public function affiliateUpdatePayoutMethod(Request $request, $id): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($id);
            if (! $affiliate) {
                $this->logActivity('Update Affiliate Payout method', ['status' => false, 'message' => 'Affiliate not found']);

                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->setAffiliatePayoutMethods($affiliate->affiliate_id, $request->payout_id);
                if ($responseData['status']) {
                    $affiliate->payout = $request->payout_id;
                    $affiliate->save();
                    $this->logActivity('Update Affiliate Payout method', $responseData);

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payout method updated successfully');
                }
                $this->logActivity('Update Affiliate Payout method', $responseData);

                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate Payout method', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update affiliate programme
     *
     * @param  $id
     */
    public function affiliateAddToProgramme(Request $request): RedirectResponse
    {
        try {
            $affiliate = AffiliateMarketers::findOrFail($request->affiliate_id);
            if (! $affiliate) {
                $this->logActivity('Update Affiliate Programme method', ['status' => false, 'message' => 'Affiliate not found']);

                return Redirect::route('affiliate-marketing.provider.affiliate.index')
                    ->with('error', 'Affiliate not found');
            }
            $programme = AffiliatePrograms::find($request->programme_id);
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->addAffiliateToProgramme($programme->affiliate_program_id, [
                    'affiliate' => ['id' => $affiliate->affiliate_id],
                    'approved' => $request->has('approved') && $request->approved ? ($request->approved == 'true') : null,
                    'coupon' => $request->coupon,
                ]);
                if ($responseData['status']) {
                    $affiliate->referral_link = $responseData['data']['referral_link']['link'];
                    $affiliate->asset_id = $responseData['data']['referral_link']['asset_id'];
                    $affiliate->source_id = $responseData['data']['referral_link']['source_id'];
                    $affiliate->approved = $request->approved == 'true';
                    $affiliate->coupon = $request->coupon;
                    $affiliate->affiliate_programme_id = $programme->id;
                    $affiliate->save();
                    $this->logActivity('Update Affiliate programme method', $responseData);

                    return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payout method updated successfully');
                }
                $this->logActivity('Update Affiliate programme method', $responseData);

                return Redirect::route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update Affiliate programme method', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.affiliate.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function paymentsIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersPayments = AffiliatePayments::with('affiliate')->where('affiliate_account_id', $provider->id)
                ->where(function ($query) use ($request, $provider) {
                    if ($request->has('name') && $request->name) {
                        $query->whereHas('affiliate', function ($q) use ($request, $provider) {
                            $q->where('affiliate_account_id', $provider->id);
                            $q->where(function ($qr) use ($request) {
                                $qr->orWhere('firstName', 'like', '%'.$request->name.'%');
                                $qr->orWhere('lastName', 'like', '%'.$request->name.'%');
                                $qr->orWhere('email', 'like', '%'.$request->name.'%');
                            });
                        });
                    }
                })->paginate(Setting::get('pagination'), '*', 'affiliate_payments');
            $affiliates = AffiliateMarketers::where('affiliate_account_id', $provider->id)->get();

            return view('affiliate-marketing.providers.payments', compact('providersPayments', 'provider', 'affiliates'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    public function paymentsCreate(PaymentsCreateRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            $affiliateData = AffiliateMarketers::find($request->affiliate_id);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createAffiliatePayment([
                    'affiliate_id' => $affiliateData->affiliate_id,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                ]);
                if ($responseData['status']) {
                    AffiliatePayments::create([
                        'affiliate_account_id' => $request->provider_account,
                        'payment_id' => $responseData['data'][0]['id'],
                        'payment_created_at' => $responseData['data'][0]['created_at'],
                        'affiliate_marketer_id' => $affiliateData->id,
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                    ]);
                    $this->logActivity('Create Affiliate payment', $responseData);

                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payment added successfully');
                }
                $this->logActivity('Create Affiliate payment', $responseData);

                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create Affiliate payment', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Create Affiliate payment', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    public function paymentsCancel(Request $request, $id): RedirectResponse
    {
        try {
            $payment = AffiliatePayments::findOrFail($id);
            if (! $payment) {
                return Redirect::route('affiliate-marketing.provider.payments.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->cancelAffiliatePayment($payment->payment_id);
                if ($responseData['status']) {
                    $payment->delete();
                    $this->logActivity('Delete Affiliate payment', $responseData);

                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate payment deleted successfully');
                }
                $this->logActivity('Delete Affiliate payment', $responseData);

                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Affiliate payment', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.payments.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.payments.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sync Payments from API to DB
     */
    public function paymentsSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAffiliatePayment();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $payment) {
                        $paymentData = AffiliatePayments::where('payment_id', $payment['id'])->first();
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $payment['affiliate']['id'])->first();
                        if (! $paymentData) {
                            AffiliatePayments::create([
                                'affiliate_account_id' => $request->provider_account,
                                'payment_id' => $payment['id'],
                                'payment_created_at' => $payment['created_at'],
                                'affiliate_marketer_id' => $affiliateData->id,
                                'amount' => $payment['amount'],
                                'currency' => $payment['currency'],
                            ]);
                        } else {
                            $paymentData->payment_id = $payment['id'];
                            $paymentData->payment_created_at = $payment['created_at'];
                            $paymentData->affiliate_marketer_id = $affiliateData->id;
                            $paymentData->amount = $payment['amount'];
                            $paymentData->currency = $payment['currency'];
                            $paymentData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate Payment Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate Payment synced successfully');
                }
                $this->logActivity('Sync affiliate Payment Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate Payment Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync affiliate payments Data', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.payments.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function conversionIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersConversions = AffiliateConversions::with('affiliate')->where(function ($query) use ($request, $provider) {
                if ($request->has('name') && $request->name) {
                    $query->whereHas('affiliate', function ($q) use ($request, $provider) {
                        $q->where('affiliate_account_id', $provider->id);
                        $q->where(function ($qr) use ($request) {
                            $qr->orWhere('firstName', 'like', '%'.$request->name.'%');
                            $qr->orWhere('lastName', 'like', '%'.$request->name.'%');
                        });
                    });
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_conversions');
            $customers = Customer::where('store_website_id', $provider->store_website_id)->get();
            $affiliateProgrammes = AffiliatePrograms::where('affiliate_account_id', $provider->id)->get();
            $affiliates = AffiliateMarketers::where('affiliate_account_id', $request->provider_account)
                ->whereNotNull('asset_id')
                ->whereNotNull('source_id')
                ->get();

            return view('affiliate-marketing.providers.conversions', compact('providersConversions', 'provider', 'customers', 'affiliates', 'affiliateProgrammes'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    public function conversionCreate(ConversionCreateRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $affiliate = AffiliateMarketers::where('asset_id', $request->asset_id)->first();
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createConversions([
                    'referral_code' => $request->referral_code,
                    'tracking_id' => $request->tracking_id,
                    'click_id' => $request->click_id,
                    'coupon' => $request->coupon,
                    'currency' => $request->currency,
                    'asset_id' => $affiliate->asset_id,
                    'source_id' => $affiliate->source_id,
                    'amount' => $request->amount,
                    'customer_id' => $request->customer_id,
                    'commission_type' => $request->commission_type,
                    'commissions' => $request->commissions,
                    'user_agent' => $request->user_agent,
                    'ip' => $request->ip,
                    'external_id' => uniqid(),
                ]);
                if ($responseData['status']) {
                    if ($responseData['data']['commissions']) {
                        foreach ($responseData['data']['commissions'] as $commission) {
                            $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                            $affiliteData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                            if (! $commissionData) {
                                AffiliateCommissions::create([
                                    'affiliate_account_id' => $request->provider_account,
                                    'affiliate_commission_id' => $commission['id'],
                                    'amount' => $commission['amount'],
                                    'approved' => isset($commission['approved']) ? $commission['approved'] : false,
                                    'affiliate_commission_created_at' => $commission['created_at'],
                                    'commission_type' => $commission['commission_type'],
                                    'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                    'comment' => $commission['comment'],
                                    'affiliate_conversion_id' => $responseData['data']['id'],
                                    'payout' => isset($responseData['data']['payout']) && $responseData['data']['payout']['id'],
                                    'affiliate_marketer_id' => $affiliteData && $affiliteData->id,
                                    'kind' => $commission['kind'],
                                    'currency' => $commission['currency'],
                                    'final' => $commission['final'],
                                    'finalization_date' => isset($commission['finalization_date']),
                                ]);
                            } else {
                                $commissionData->amount = $commission['amount'];
                                $commissionData->approved = isset($commission['approved']) ? $commission['approved'] : false;
                                $commissionData->affiliate_commission_created_at = $commission['created_at'];
                                $commissionData->commission_type = $commission['commission_type'];
                                $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                                $commissionData->comment = $commission['comment'];
                                $commissionData->affiliate_conversion_id = $responseData['data']['id'];
                                $commissionData->payout = isset($responseData['data']['payout']) && $responseData['data']['payout']['id'];
                                $commissionData->affiliate_marketer_id = $affiliteData && $affiliteData->id;
                                $commissionData->kind = $commission['kind'];
                                $commissionData->currency = $commission['currency'];
                                $commissionData->final = $commission['final'];
                                $commissionData->finalization_date = isset($commission['finalization_date']);
                                $commissionData->save();
                            }
                        }
                    }
                    $programmeData = null;
                    $affiliateData = null;
                    if (isset($responseData['data']['program']) && $responseData['data']['program']['id']) {
                        $programmeData = AffiliatePrograms::where('affiliate_program_id', $responseData['data']['program']['id'])->first();
                    }
                    if (isset($responseData['data']['affiliate']) && $responseData['data']['affiliate']['id']) {
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $responseData['data']['affiliate']['id'])->first();
                    }
                    AffiliateConversions::create([
                        'affiliate_account_id' => $request->provider_account,
                        'affiliate_conversion_id' => $responseData['data']['id'],
                        'external_id' => $responseData['data']['external_id'],
                        'amount' => $responseData['data']['amount'],
                        'click_date' => isset($responseData['data']['click']) && $responseData['data']['click']['created_at'],
                        'click_referrer' => isset($responseData['data']['click']) && $responseData['data']['click']['referrer'],
                        'click_landing_page' => isset($responseData['data']['click']) && $responseData['data']['click']['landing_page'],
                        'program_id' => isset($responseData['data']['program']) && $responseData['data']['program']['id'],
                        'affiliate_id' => isset($responseData['data']['affiliate']) && $responseData['data']['affiliate']['id'],
                        'affiliate_program_id' => $programmeData->id,
                        'affiliate_marketer_id' => $affiliateData->id,
                        'customer_id' => isset($responseData['data']['customer']) && $responseData['data']['customer']['id'],
                        'customer_system_id' => isset($responseData['data']['customer']) && $responseData['data']['customer']['customer_id'],
                        'customer_status' => isset($responseData['data']['customer']) && $responseData['data']['customer']['status'],
                        'meta_data' => isset($responseData['data']['meta_data']) ? serialize($responseData['data']['meta_data']) : '',
                        'commission_created_at' => $responseData['data']['created_at'],
                        'warnings' => serialize($responseData['data']['warnings']),
                        'affiliate_meta_data' => serialize($responseData['data']['affiliate_meta_data']),
                    ]);
                    $this->logActivity('Create conversion', $responseData);

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Conversion added successfully');
                } elseif ($responseData['errors']) {
                    $validationErrors = [];
                    foreach ($responseData['response']['errors'] as $err) {
                        if (strpos($err['message'], 'coupon code')) {
                            $validationErrors['coupon'] = [$err['message']];
                        } elseif (strpos($err['message'], 'click id')) {
                            $validationErrors['click_id'] = [$err['message']];
                        } elseif (strpos($err['message'], 'referral code')) {
                            $validationErrors['referral_code'] = [$err['message']];
                        }
                    }

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('create_popup', true)
                        ->withErrors($validationErrors)
                        ->withInput();
                }
                $this->logActivity('Create conversion', $responseData);

                return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create conversion', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Create conversion', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    public function conversionUpdate(ConversionUpdateRequest $request): RedirectResponse
    {
        try {
            $conversionData = AffiliateConversions::find($request->conversion_id);
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->updateConversions($conversionData->affiliate_conversion_id, [
                    'amount' => $request->amount,
                ]);
                if ($responseData['status']) {
                    if ($responseData['data']) {
                        foreach ($responseData['data']['commissions'] as $commission) {
                            $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                            $affiliteData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                            if (! $commissionData) {
                                AffiliateCommissions::create([
                                    'affiliate_account_id' => $request->provider_account,
                                    'affiliate_commission_id' => $commission['id'],
                                    'amount' => $commission['amount'],
                                    'approved' => isset($commission['approved']) ? $commission['approved'] : false,
                                    'affiliate_commission_created_at' => $commission['created_at'],
                                    'commission_type' => $commission['commission_type'],
                                    'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                    'comment' => $commission['comment'],
                                    'affiliate_conversion_id' => $responseData['data']['id'],
                                    'payout' => isset($responseData['data']['payout']) && $responseData['data']['payout']['id'],
                                    'affiliate_marketer_id' => $affiliteData && $affiliteData->id,
                                    'kind' => $commission['kind'],
                                    'currency' => $commission['currency'],
                                    'final' => $commission['final'],
                                    'finalization_date' => isset($commission['finalization_date']),
                                ]);
                            } else {
                                $commissionData->amount = $commission['amount'];
                                $commissionData->approved = $commission['approved'];
                                $commissionData->affiliate_commission_created_at = $commission['created_at'];
                                $commissionData->commission_type = $commission['commission_type'];
                                $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                                $commissionData->comment = $commission['comment'];
                                $commissionData->affiliate_conversion_id = $responseData['data']['id'];
                                $commissionData->payout = isset($responseData['data']['payout']) && $responseData['data']['payout']['id'];
                                $commissionData->affiliate_marketer_id = $affiliteData && $affiliteData->id;
                                $commissionData->kind = $commission['kind'];
                                $commissionData->currency = $commission['currency'];
                                $commissionData->final = $commission['final'];
                                $commissionData->finalization_date = isset($commission['finalization_date']);
                                $commissionData->save();
                            }
                        }
                    }
                    $conversionData->amount = $request->amount;
                    $conversionData->save();
                    $this->logActivity('update conversion', $responseData);

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Conversion updated successfully');
                }
                $this->logActivity('Update conversion', $responseData);

                return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Update conversion', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Update conversion', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    public function conversionDelete(Request $request, $id): RedirectResponse
    {
        try {
            $conversions = AffiliateConversions::findOrFail($id);
            if (! $conversions) {
                return Redirect::route('affiliate-marketing.provider.conversion.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->deleteConversions($conversions->affiliate_conversion_id);
                if ($responseData['status']) {
                    $conversions->delete();
                    $this->logActivity('Delete Conversion', $responseData);

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Conversion deleted successfully');
                }
                $this->logActivity('Delete Conversion', $responseData);

                return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Conversion', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.conversion.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.conversion.index')
                ->with('error', $e->getMessage());
        }
    }

    public function conversionAddCommission(Request $request): RedirectResponse
    {
        try {
            $conversions = AffiliateConversions::findOrFail($request->conversion_id);
            if (! $conversions) {
                return Redirect::route('affiliate-marketing.provider.conversion.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->addCommissionConversions($conversions->affiliate_conversion_id, [
                    'conversion_sub_amount' => $request->conversion_sub_amount,
                    'commission_type' => $request->commission_type,
                    'comment' => $request->comment,
                ]);
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $commission) {
                        $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                        $affiliteData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                        if (! $commissionData) {
                            AffiliateCommissions::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_commission_id' => $commission['id'],
                                'amount' => $commission['amount'],
                                'approved' => isset($commission['approved']) ? $commission['approved'] : false,
                                'affiliate_commission_created_at' => $commission['created_at'],
                                'commission_type' => $commission['commission_type'],
                                'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                'comment' => $commission['comment'],
                                'affiliate_conversion_id' => $request->affiliate_conversion_id,
                                'payout' => isset($commission['payout']) && $commission['payout']['id'],
                                'affiliate_marketer_id' => $affiliteData && $affiliteData->id,
                                'kind' => $commission['kind'],
                                'currency' => $commission['currency'],
                                'final' => $commission['final'],
                                'finalization_date' => isset($commission['finalization_date']),
                            ]);
                        } else {
                            $commissionData->amount = $commission['amount'];
                            $commissionData->approved = (bool) $commission['approved'];
                            $commissionData->affiliate_commission_created_at = $commission['created_at'];
                            $commissionData->commission_type = $commission['commission_type'];
                            $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                            $commissionData->comment = $commission['comment'];
                            $commissionData->affiliate_conversion_id = $request->affiliate_conversion_id;
                            $commissionData->payout = isset($commission['payout']) && $commission['payout']['id'];
                            $commissionData->affiliate_marketer_id = $affiliteData && $affiliteData->id;
                            $commissionData->kind = $commission['kind'];
                            $commissionData->currency = $commission['currency'];
                            $commissionData->final = $commission['final'];
                            $commissionData->finalization_date = isset($commission['finalization_date']);
                            $commissionData->save();
                        }
                    }
                    $this->logActivity('Commission added to Conversion', $responseData);

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Commission added to Conversion successfully');
                }
                $this->logActivity('Commission added to Conversion', $responseData);

                return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Commission added to Conversion', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.conversion.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.conversion.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sync Payments from API to DB
     */
    public function conversionSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAllConversions();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $conversion) {
                        if ($conversion['commissions']) {
                            foreach ($conversion['commissions'] as $commission) {
                                $commissionData = AffiliateCommissions::where('affiliate_commission_id', $commission['id'])->first();
                                $affiliteData = AffiliateMarketers::where('affiliate_id', $commission['affiliate']['id'])->first();
                                if (! $commissionData) {
                                    AffiliateCommissions::create([
                                        'affiliate_account_id' => $request->provider_account,
                                        'affiliate_commission_id' => $commission['id'],
                                        'amount' => $commission['amount'],
                                        'approved' => isset($commission['approved']) ? $commission['approved'] : false,
                                        'affiliate_commission_created_at' => $commission['created_at'],
                                        'commission_type' => $commission['commission_type'],
                                        'conversion_sub_amount' => $commission['conversion_sub_amount'],
                                        'comment' => $commission['comment'],
                                        'affiliate_conversion_id' => $conversion['id'],
                                        'payout' => isset($commission['payout']) && $commission['payout']['id'],
                                        'affiliate_marketer_id' => $affiliteData && $affiliteData->id,
                                        'kind' => $commission['kind'],
                                        'currency' => $commission['currency'],
                                        'final' => $commission['final'],
                                        'finalization_date' => isset($commission['finalization_date']),
                                    ]);
                                } else {
                                    $commissionData->amount = $commission['amount'];
                                    $commissionData->approved = $commission['approved'];
                                    $commissionData->affiliate_commission_created_at = $commission['created_at'];
                                    $commissionData->commission_type = $commission['commission_type'];
                                    $commissionData->conversion_sub_amount = $commission['conversion_sub_amount'];
                                    $commissionData->comment = $commission['comment'];
                                    $commissionData->affiliate_conversion_id = $conversion['id'];
                                    $commissionData->payout = isset($commission['payout']) && $commission['payout']['id'];
                                    $commissionData->affiliate_marketer_id = $affiliteData && $affiliteData->id;
                                    $commissionData->kind = $commission['kind'];
                                    $commissionData->currency = $commission['currency'];
                                    $commissionData->final = $commission['final'];
                                    $commissionData->finalization_date = isset($commission['finalization_date']);
                                }
                            }
                        }
                        $conversionData = AffiliateConversions::where('affiliate_conversion_id', $conversion['id'])->first();
                        $programmeData = null;
                        $affiliateData = null;
                        if (isset($conversion['program']) && $conversion['program']['id']) {
                            $programmeData = AffiliatePrograms::where('affiliate_program_id', $conversion['program']['id'])->first();
                        }
                        if (isset($conversion['affiliate']) && $conversion['affiliate']['id']) {
                            $affiliateData = AffiliateMarketers::where('affiliate_id', $conversion['affiliate']['id'])->first();
                        }
                        if (! $conversionData) {
                            AffiliateConversions::create([
                                'affiliate_account_id' => $request->provider_account,
                                'affiliate_conversion_id' => $conversion['id'],
                                'external_id' => $conversion['external_id'] ?? uniqid(),
                                'amount' => $conversion['amount'],
                                'click_date' => isset($conversion['click']) ? $conversion['click']['created_at'] : null,
                                'click_referrer' => isset($conversion['click']) ? $conversion['click']['referrer'] : null,
                                'click_landing_page' => isset($conversion['click']) ? $conversion['click']['landing_page'] : null,
                                'program_id' => isset($conversion['program']) ? $conversion['program']['id'] : null,
                                'affiliate_id' => isset($conversion['affiliate']) ? $conversion['affiliate']['id'] : null,
                                'affiliate_program_id' => $programmeData->id,
                                'affiliate_marketer_id' => $affiliateData->id,
                                'customer_id' => isset($conversion['customer']) ? $conversion['customer']['id'] : null,
                                'customer_system_id' => isset($conversion['customer']) ? $conversion['customer']['customer_id'] : null,
                                'customer_status' => isset($conversion['customer']) ? $conversion['customer']['status'] : null,
                                'meta_data' => isset($conversion['meta_data']) ? serialize($conversion['meta_data']) : '',
                                'commission_created_at' => $conversion['created_at'],
                                'warnings' => serialize($conversion['warnings']),
                                'affiliate_meta_data' => serialize($conversion['affiliate_meta_data']),
                            ]);
                        } else {
                            $conversionData->affiliate_conversion_id = $conversion['id'];
                            $conversionData->external_id = $conversion['external_id'] ?? uniqid();
                            $conversionData->amount = $conversion['amount'];
                            $conversionData->click_date = isset($conversion['click']) ? $conversion['click']['created_at'] : null;
                            $conversionData->click_referrer = isset($conversion['click']) ? $conversion['click']['referrer'] : null;
                            $conversionData->click_landing_page = isset($conversion['click']) ? $conversion['click']['landing_page'] : null;
                            $conversionData->program_id = isset($conversion['program']) ? $conversion['program']['id'] : null;
                            $conversionData->affiliate_id = isset($conversion['affiliate']) ? $conversion['affiliate']['id'] : null;
                            $conversionData->affiliate_program_id = $programmeData->id;
                            $conversionData->affiliate_marketer_id = $affiliateData->id;
                            $conversionData->customer_id = isset($conversion['customer']) ? $conversion['customer']['id'] : null;
                            $conversionData->customer_system_id = isset($conversion['customer']) ? $conversion['customer']['customer_id'] : null;
                            $conversionData->customer_status = isset($conversion['customer']) ? $conversion['customer']['status'] : null;
                            $conversionData->meta_data = isset($conversion['meta_data']) ? serialize($conversion['meta_data']) : '';
                            $conversionData->commission_created_at = $conversion['created_at'];
                            $conversionData->warnings = $conversion['warnings'];
                            $conversionData->affiliate_meta_data = $conversion['affiliate_meta_data'];
                            $conversionData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate conversion Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate conversion synced successfully');
                }
                $this->logActivity('Sync affiliate conversion Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate conversion Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync affiliate conversion Data', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.conversion.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function customerIndex(Request $request)
    {
        if ($request->has('provider_account') && $request->provider_account) {
            $provider = $this->getProviderAccount($request->provider_account);
            $providersCustomers = AffiliateCustomers::with(['affiliate', 'programme'])->where(function ($query) use ($request, $provider) {
                if ($request->has('name') && $request->name) {
                    $query->whereHas('affiliate', function ($q) use ($request, $provider) {
                        $q->where('affiliate_account_id', $provider->id);
                        $q->where(function ($qr) use ($request) {
                            $qr->orWhere('firstName', 'like', '%'.$request->name.'%');
                            $qr->orWhere('lastName', 'like', '%'.$request->name.'%');
                        });
                    });
                }
            })->paginate(Setting::get('pagination'), '*', 'affiliate_customer');
            $customers = Customer::where('store_website_id', $provider->store_website_id)->get();
            $affiliates = AffiliateMarketers::where('affiliate_account_id', $request->provider_account)
                ->whereNotNull('asset_id')
                ->whereNotNull('source_id')
                ->get();

            return view('affiliate-marketing.providers.customers', compact('providersCustomers', 'provider', 'customers', 'affiliates'));
        }

        return Redirect::route('affiliate-marketing.providerAccounts')
            ->with('error', self::NO_PROVIDE_FOUND);
    }

    public function customerCreate(CustomerCreateRequest $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $affiliate = AffiliateMarketers::where('asset_id', $request->asset_id)->first();
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->createCustomer([
                    'referral_code' => $request->referral_code,
                    'tracking_id' => $request->tracking_id,
                    'click_id' => $request->click_id,
                    'coupon' => $request->coupon,
                    'asset_id' => $affiliate->asset_id,
                    'source_id' => $affiliate->source_id,
                    'customer_id' => $request->customer_id,
                    'status' => $request->status,
                    'user_agent' => $request->user_agent,
                    'ip' => $request->ip,
                ]);
                if ($responseData['status']) {
                    $programmeData = null;
                    $affiliateData = null;
                    if (isset($responseData['data']['program']) && $responseData['data']['program']['id']) {
                        $programmeData = AffiliatePrograms::where('affiliate_program_id', $responseData['data']['program']['id'])->first();
                    }
                    if (isset($responseData['data']['affiliate']) && $responseData['data']['affiliate']['id']) {
                        $affiliateData = AffiliateMarketers::where('affiliate_id', $responseData['data']['affiliate']['id'])->first();
                    }
                    AffiliateCustomers::create([
                        'affiliate_account_id' => $request->provider_account,
                        'customer_id' => $responseData['data']['id'],
                        'customer_system_id' => $responseData['data']['customer_id'],
                        'status' => $responseData['data']['status'],
                        'customer_created_at' => $responseData['data']['created_at'],
                        'click_date' => isset($responseData['data']['click']) && $responseData['data']['click']['created_at'],
                        'click_referrer' => isset($responseData['data']['click']) && $responseData['data']['click']['referrer'],
                        'click_landing_page' => isset($responseData['data']['click']) && $responseData['data']['click']['landing_page'],
                        'program_id' => isset($responseData['data']['program']) && $responseData['data']['program']['id'],
                        'affiliate_id' => isset($responseData['data']['affiliate']) && $responseData['data']['affiliate']['id'],
                        'affiliate_program_id' => $programmeData->id,
                        'affiliate_marketer_id' => $affiliateData->id,
                        'affiliate_meta_data' => serialize($responseData['data']['affiliate_meta_data']),
                        'meta_data' => serialize($responseData['data']['meta_data']),
                        'warnings' => serialize($responseData['data']['warnings']),
                    ]);
                    $this->logActivity('Create customer', $responseData);

                    return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Customer added successfully');
                } elseif ($responseData['errors']) {
                    $validationErrors = [];
                    foreach ($responseData['response']['errors'] as $err) {
                        if (strpos($err['message'], 'already a customer')) {
                            $validationErrors['customer_id'] = [$err['message']];
                        }
                    }

                    return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                        ->with('create_popup', true)
                        ->withErrors($validationErrors)
                        ->withInput();
                }

                $this->logActivity('Create customer', $responseData);

                return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Create customer', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Create customer', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    public function customerDelete(Request $request, $id): RedirectResponse
    {
        try {
            $customer = AffiliateCustomers::findOrFail($id);
            if (! $customer) {
                return Redirect::route('affiliate-marketing.provider.conversion.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->deleteCustomer($customer->customer_id);
                if ($responseData['status']) {
                    $customer->delete();
                    $this->logActivity('Delete Customer', $responseData);

                    return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Customer deleted successfully');
                }
                $this->logActivity('Delete Customer', $responseData);

                return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Delete Customer', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.customer.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.customer.index')
                ->with('error', $e->getMessage());
        }
    }

    public function customerCancelUnCancel(Request $request, $id): RedirectResponse
    {
        try {
            $customer = AffiliateCustomers::findOrFail($id);
            if (! $customer) {
                return Redirect::route('affiliate-marketing.provider.conversion.index')
                    ->with('error', 'No account found');
            }
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->cancelCustomer($customer->customer_id, $customer->status != 'stopped');
                if ($responseData['status']) {
                    $customer->status = $responseData['data']['status'];
                    $customer->save();
                    $this->logActivity('Cancel Customer', $responseData);
                    $successMsg = $responseData['data']['status'] == 'active' ? 'Customer Uncancel successfully' : 'Customer Cancel successfully';

                    return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                        ->with('success', $successMsg);
                }
                $this->logActivity('Cancel Customer', $responseData);

                return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Cancel Customer', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.customer.index')
                ->with('success', self::ACCOUNT_NOT_FOUND);
        } catch (Exception $e) {
            return Redirect::route('affiliate-marketing.provider.customer.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sync Customers from API to DB
     */
    public function customerSync(Request $request): RedirectResponse
    {
        try {
            $providerAccount = $this->getProviderAccount($request->provider_account);
            if (strtolower($providerAccount->provider->provider_name) == 'tapfiliate') {
                $tapfiliate = new Tapfiliate($providerAccount);
                $responseData = $tapfiliate->getAllCustomer();
                if ($responseData['status']) {
                    foreach ($responseData['data'] as $customer) {
                        $programmeData = null;
                        $affiliateData = null;
                        if (isset($customer['program']) && $customer['program']['id']) {
                            $programmeData = AffiliatePrograms::where('affiliate_program_id', $customer['program']['id'])->first();
                        }
                        if (isset($customer['affiliate']) && $customer['affiliate']['id']) {
                            $affiliateData = AffiliateMarketers::where('affiliate_id', $customer['affiliate']['id'])->first();
                        }
                        $customerData = AffiliateCustomers::where('customer_id', $customer['id'])->first();
                        if (! $customerData) {
                            AffiliateCustomers::create([
                                'affiliate_account_id' => $request->provider_account,
                                'customer_id' => $customer['id'],
                                'customer_system_id' => $customer['customer_id'],
                                'status' => $customer['status'],
                                'customer_created_at' => $customer['created_at'],
                                'click_date' => isset($customer['click']) && $customer['click']['created_at'],
                                'click_referrer' => isset($customer['click']) && $customer['click']['referrer'],
                                'click_landing_page' => isset($customer['click']) && $customer['click']['landing_page'],
                                'program_id' => isset($customer['program']) && $customer['program']['id'],
                                'affiliate_id' => isset($customer['affiliate']) && $customer['affiliate']['id'],
                                'affiliate_program_id' => $programmeData->id,
                                'affiliate_marketer_id' => $affiliateData->id,
                                'affiliate_meta_data' => serialize($customer['affiliate_meta_data']),
                                'meta_data' => serialize($customer['meta_data']),
                                'warnings' => serialize($customer['warnings']),
                            ]);
                        } else {
                            $customerData->customer_id = $customer['id'];
                            $customerData->customer_system_id = $customer['customer_id'];
                            $customerData->status = $customer['status'];
                            $customerData->customer_created_at = $customer['created_at'];
                            $customerData->click_date = isset($customer['click']) && $customer['click']['created_at'];
                            $customerData->click_referrer = isset($customer['click']) && $customer['click']['referrer'];
                            $customerData->click_landing_page = isset($customer['click']) && $customer['click']['landing_page'];
                            $customerData->program_id = isset($customer['program']) && $customer['program']['id'];
                            $customerData->affiliate_id = isset($customer['affiliate']) && $customer['affiliate']['id'];
                            $customerData->affiliate_program_id = $programmeData->id;
                            $customerData->affiliate_marketer_id = $affiliateData->id;
                            $customerData->affiliate_meta_data = serialize($customer['affiliate_meta_data']);
                            $customerData->meta_data = serialize($customer['meta_data']);
                            $customerData->warnings = serialize($customer['warnings']);
                            $customerData->save();
                        }
                    }
                    $this->logActivity('Sync affiliate customer Data', $responseData);

                    return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                        ->with('success', 'Affiliate affiliate customer synced successfully');
                }
                $this->logActivity('Sync affiliate customer Data', $responseData);

                return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                    ->with('error', $responseData['message']);
            }
            $this->logActivity('Sync affiliate customer Data', ['status' => false, 'message' => self::ACCOUNT_NOT_FOUND]);

            return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                ->with('error', 'Affiliate account not found');
        } catch (Exception $e) {
            $this->logActivity('Sync affiliate customer Data', ['status' => false, 'message' => $e->getMessage()]);

            return Redirect::route('affiliate-marketing.provider.customer.index', ['provider_account' => $request->provider_account])
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Logs activity for affiliate marketing
     *
     * @param  mixed  $name
     * @param  mixed  $data
     */
    private function logActivity($name, $data)
    {
        AffiliateMarketingLogs::create([
            'user_name' => Auth::user()->name,
            'name' => $name,
            'status' => $data['status'] ? 'Success' : 'Error',
            'message' => $data['message'],
        ]);
    }

    /**
     * Gets provider account details.
     *
     * @param  mixed  $providerId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    private function getProviderAccount($providerId)
    {
        return AffiliateProviderAccounts::with('provider')->findOrFail($providerId);
    }
}
