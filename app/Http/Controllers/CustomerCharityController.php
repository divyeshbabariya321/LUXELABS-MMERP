<?php

namespace App\Http\Controllers;
use App\Reply;
use App\Http\Controllers;

use App\Http\Requests\StoreCustomerCharityRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Brand;
use App\Product;
use App\Setting;
use App\Website;
use App\Category;
use App\StoreWebsite;
use App\WebsiteStore;
use App\CharityCountry;
use App\VendorCategory;
use App\CustomerCharity;
use Illuminate\Http\Request;
use App\Helpers\ProductHelper;
use App\Marketing\WhatsappConfig;
use App\CharityProductStoreWebsite;
use App\CustomerCharityWebsiteStore;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerCharityController extends Controller
{
    const DEFAULT_FOR = 2; //For Vendor

    public function index(Request $request): View
    {
        $term         = $request->term ?? '';
        $sortByClause = '';
        $orderby      = 'DESC';

        if ($request->orderby == '') {
            $orderby = 'ASC';
        }
        if ($request->sortby == 'id') {
            $sortByClause = "id $orderby,";
        }
        $whereArchived = ' `deleted_at` IS NULL ';

        if ($request->get('with_archived') == 'on') {
            $whereArchived = '  `deleted_at` IS NOT NULL  ';
        }

        $isAdmin = Auth::user()->isAdmin();

        if ($isAdmin) {
            $permittedCategories = [];
        } else {
            $permittedCategories = Auth::user()->vendorCategoryPermission->pluck('id')->all() + [0];
        }
        //getting request
        if (
            $request->term || $request->name || $request->id || $request->category || $request->email || $request->phone ||
            $request->address || $request->email || $request->communication_history || $request->status != null || $request->updated_by != null
        ) {
            //Query Initiate
            if ($isAdmin) {
                $query = CustomerCharity::query();
            } else {
                $imp_permi = implode(',', $permittedCategories);
                if ($imp_permi != 0) {
                    $query = CustomerCharity::whereIn('category_id', $permittedCategories);
                } else {
                    $query = CustomerCharity::query();
                }
            }

            if (request('term') != null) {
                $query->where('name', 'LIKE', "%{$request->term}%");
            }

            //if Id is not null
            if (request('id') != null) {
                $query->where('id', request('id', 0));
            }

            //If name is not null
            if (request('name') != null) {
                $query->where('name', 'LIKE', '%' . request('name') . '%');
            }

            //if addess is not null
            if (request('address') != null) {
                $query->where('address', 'LIKE', '%' . request('address') . '%');
            }

            //if email is not null
            if (request('email') != null) {
                $query->where('email', 'LIKE', '%' . request('email') . '%');
            }

            //if phone is not null
            if (request('phone') != null) {
                $query->where('phone', 'LIKE', '%' . request('phone') . '%');
            }
            $status = request('status');
            if ($status != null && !request('with_archived')) {
                $query = $query->where(function ($q) use ($status) {
                    $q->orWhere('status', $status);
                });
            }

            if (request('updated_by') != null && !request('with_archived')) {
                $query = $query->where(function ($q) {
                    $q->orWhere('updated_by', request('updated_by'));
                });
            }

            //if category is not nyll
            if (request('category') != null) {
                $query->whereHas('category', function ($qu) {
                    $qu->where('category_id', '=', request('category'));
                });
            }
            //if email is not nyll
            if (request('email') != null) {
                $query->where('email', 'like', '%' . request('email') . '%');
            }
            if (request('communication_history') != null && !request('with_archived')) {
                $communication_history = request('communication_history');
                $query->orWhereRaw("customer_charities.id in (select charity_id from chat_messages where charity_id is not null and message like '%" . $communication_history . "%')");
            }

            if ($request->with_archived != null && $request->with_archived != '') {
                $pagination = Setting::get('pagination');
                

                $totalVendor        = $query->orderby('name', 'asc')->whereNotNull('deleted_at')->count();
                $customer_charities = $query->orderby('name', 'asc')->whereNotNull('deleted_at')->paginate($pagination);
                
              
            } else {
                $pagination = Setting::get('pagination');
                if (request()->get('select_all') == 'true') {
                    $pagination = $customer_charities->count();
                }
                $totalVendor        = $query->orderby('name', 'asc')->count();
                $customer_charities = $query->orderby('name', 'asc')->paginate($pagination);
            }
        } else {
            if ($isAdmin) {
                $permittedCategories = '';
            } else {
                if (empty($permittedCategories)) {
                    $permittedCategories = [0];
                }
                $permittedCategories_all = implode(',', $permittedCategories);
                if ($permittedCategories_all == 0) {
                    $permittedCategories = '';
                } else {
                    $permittedCategories = 'and customer_charities.category_id in (' . implode(',', $permittedCategories) . ')';
                }
            }

            // $customer_charities = DB::select('
            //     SELECT customer_charities.id,customer_charities.product_id, customer_charities.frequency, customer_charities.is_blocked ,customer_charities.reminder_message, customer_charities.category_id, customer_charities.name, customer_charities.phone, customer_charities.email, customer_charities.address, customer_charities.social_handle, customer_charities.website, customer_charities.login, customer_charities.password, customer_charities.gst, customer_charities.account_name, customer_charities.account_iban, customer_charities.account_swift,
            //     customer_charities.frequency_of_payment,
            //     customer_charities.bank_name,
            //     customer_charities.bank_address,
            //     customer_charities.city,
            //     customer_charities.country,
            //     customer_charities.ifsc_code,
            //     customer_charities.remark,
            //         customer_charities.created_at,customer_charities.updated_at,
            //         customer_charities.updated_by,
            //         customer_charities.reminder_from,
            //         customer_charities.reminder_last_reply,
            //         customer_charities.status,
            //         customer_charities.store_website_id,
            //         store_websites.title as store_websites_name,
            //         0 as message_status
            //         FROM customer_charities
            //         LEFT JOIN store_websites on store_websites.id=customer_charities.store_website_id
            //         WHERE (name LIKE "%' . $term . '%" OR
            //         phone LIKE "%' . $term . '%" OR
            //         email LIKE "%' . $term . '%" OR
            //         address LIKE "%' . $term . '%" OR
            //         social_handle LIKE "%' . $term . '%" OR
            //         customer_charities.id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Vendor%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%"))) ' . $permittedCategories . '
            //         ORDER BY ' . $sortByClause . ' created_at DESC;

            // ');


            $customer_charities = CustomerCharity::select(
                    'customer_charities.id',
                    'customer_charities.product_id',
                    'customer_charities.frequency',
                    'customer_charities.is_blocked',
                    'customer_charities.reminder_message',
                    'customer_charities.category_id',
                    'customer_charities.name',
                    'customer_charities.phone',
                    'customer_charities.email',
                    'customer_charities.address',
                    'customer_charities.social_handle',
                    'customer_charities.website',
                    'customer_charities.login',
                    'customer_charities.password',
                    'customer_charities.gst',
                    'customer_charities.account_name',
                    'customer_charities.account_iban',
                    'customer_charities.account_swift',
                    'customer_charities.frequency_of_payment',
                    'customer_charities.bank_name',
                    'customer_charities.bank_address',
                    'customer_charities.city',
                    'customer_charities.country',
                    'customer_charities.ifsc_code',
                    'customer_charities.remark',
                    'customer_charities.created_at',
                    'customer_charities.updated_at',
                    'customer_charities.updated_by',
                    'customer_charities.reminder_from',
                    'customer_charities.reminder_last_reply',
                    'customer_charities.status',
                    'customer_charities.store_website_id',
                    'store_websites.title as store_websites_name',
                    DB::raw('0 as message_status')
                )
                ->leftJoin('store_websites', 'store_websites.id', '=', 'customer_charities.store_website_id')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term . '%')
                        ->orWhere('phone', 'like', '%' . $term . '%')
                        ->orWhere('email', 'like', '%' . $term . '%')
                        ->orWhere('address', 'like', '%' . $term . '%')
                        ->orWhere('social_handle', 'like', '%' . $term . '%')
                        ->orWhereIn('customer_charities.id', function ($subquery) use ($term) {
                            $subquery->select('model_id')
                                ->from('agents')
                                ->where('model_type', 'like', '%Vendor%')
                                ->where(function ($q) use ($term) {
                                    $q->where('name', 'like', '%' . $term . '%')
                                        ->orWhere('phone', 'like', '%' . $term . '%')
                                        ->orWhere('email', 'like', '%' . $term . '%');
                                });
                        });
                })
                ->orderByDesc('created_at')
                ->get();

            $arrCustomerCharities = $customer_charities->toArray();
            $totalVendor = count($arrCustomerCharities);

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage     = Setting::get('pagination');
            if (request()->get('select_all') == 'true') {
                $perPage     = count($arrCustomerCharities);
                $currentPage = 1;
            }

            if (!is_numeric($perPage)) {
                $perPage = 2;
            }
            $currentItems = array_slice($arrCustomerCharities, $perPage * ($currentPage - 1), $perPage);

            $customer_charities = new LengthAwarePaginator($currentItems, count($arrCustomerCharities), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]);
        }

        $vendor_categories = VendorCategory::all();

        $users = User::all();

        $replies = Reply::where('model', 'Vendor')->whereNull('deleted_at')->pluck('reply', 'id')->toArray();

        $updatedProducts = CustomerCharity::join('users as u', 'u.id', 'customer_charities.updated_by')
            ->groupBy('customer_charities.updated_by')
            ->select([\DB::raw('count(u.id) as total_records'), 'u.name'])
            ->get();

        $storewebsite = StoreWebsite::all();

        $website1 = Website::all();

        return view('vendors.charity', [
            'vendors'           => $customer_charities,
            'vendor_categories' => $vendor_categories,
            'term'              => $term,
            'orderby'           => $orderby,
            'users'             => $users,
            'replies'           => $replies,
            'updatedProducts'   => $updatedProducts,
            'totalVendor'       => $totalVendor,
            'storewebsite'      => $storewebsite,
            'website1'          => $website1,

        ]);
    }

    public function store(StoreCustomerCharityRequest $request, $id = null): RedirectResponse
    {

        $data = $request->except(['_token', 'create_user']);
        if (empty($data['whatsapp_number'])) {
            //get default whatsapp number for vendor from whatsapp config
            $task_info = WhatsappConfig::select('*')
                ->whereRaw('find_in_set(' . self::DEFAULT_FOR . ',default_for)')
                ->first();
            if (isset($task_info->number) && $task_info->number != null) {
                $data['whatsapp_number'] = $task_info->number;
            }
        }

        if (empty($data['default_phone'])) {
            $data['default_phone'] = $data['phone'];
        }

        if (!empty($data)) {
            $data['status'] = 0;
        }

        unset($data['websites']);
        unset($data['website_stores']);
        if ($id == null) {
            $charity                    = CustomerCharity::create($data);
            $charity_category           = Category::where('title', 'charity')->first();
            $charity_brand              = Brand::where('name', 'charity')->first();
            $product                    = new Product();
            $product->sku               = '';
            $product->status_id         = '115';
            $product->name              = $charity->name;
            $product->short_description = $charity->name;
            $product->brand             = $charity_brand->id;
            $product->category          = $charity_category->id;
            $product->price             = 1;
            $product->save();
            CustomerCharity::where('id', $charity->id)->update([
                'product_id' => $product->id,
            ]);
            Product::where('id', $product->id)->update(['sku' => 'charity_' . $product->id]);
        } else {
            CustomerCharity::where('id', $id)->update($data);
        }

        return redirect()->route('customer.charity')->withSuccess('You have successfully saved a charity!');
    }

    public function delete($id): RedirectResponse
    {
        $customer_charity = CustomerCharity::find($id);
        $customer_charity->delete();

        return redirect()->route('customer.charity')->withSuccess('You have successfully deleted a charity');
    }

    public function charitySearch(): JsonResponse
    {
        $term   = request()->get('q', null);
        $search = CustomerCharity::where('name', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function charityEmail(): JsonResponse
    {
        $term   = request()->get('q', null);
        $search = CustomerCharity::where('email', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function charityPhoneNumber(): JsonResponse
    {
        $term   = request()->get('q', null);
        $search = CustomerCharity::where('phone', 'LIKE', '%' . $term . '%')
            ->get();

        return response()->json($search);
    }

    public function charityWebsites($id): JsonResponse
    {
        $cc            = CustomerCharity::find($id);
        $websiteArrays = ProductHelper::getStoreWebsiteName($cc->product_id);
        if (count($websiteArrays)) {
            foreach ($websiteArrays as $websiteArray) {
                $website   = StoreWebsite::find($websiteArray);
                $webStores = Website::select('code', 'name')->where('store_website_id', $website->id)->get();
            }
        }
        foreach ($webStores as $w) {
            $c_raw    = CharityCountry::where('charity_id', $id)->where('country_code', $w->code)->first();
            $w->price = 1;
            if ($c_raw) {
                $w->price = $c_raw->price;
            }
        }

        return response()->json($webStores);
    }

    public function addCharityWebsites(Request $request, $id): JsonResponse
    {
        $countries = explode('&', $request->data);
        foreach ($countries as $c) {
            $cc  = explode('=', $c)[0];
            $val = explode('=', $c)[1];
            if ($val) {
                $c_raw = CharityCountry::where('charity_id', $id)->where('country_code', $cc)->first();
                if (!$c_raw) {
                    $c_raw               = new CharityCountry();
                    $c_raw->charity_id   = $id;
                    $c_raw->country_code = $cc;
                }
                $c_raw->price = $val;
                $c_raw->save();
            }
        }

        return response()->json('Charity Updated Successfully!');
    }

    public function savewebsite(Request $request): JsonResponse
    {
        $c    = CharityProductStoreWebsite::where('charity_id', $request->charity_id)->where('website_id', $request->website_id)->first();
        $data = [
            'charity_id' => $request->charity_id,
            'website_id' => $request->website_id,
            'price'      => $request->price,
        ];
        if ($c) {
            CharityProductStoreWebsite::where('charity_id', $request->charity_id)->where('website_id', $request->website_id)->update($data);
        } else {
            CharityProductStoreWebsite::insert($data);
        }

        return response()->json('Charity Website Updated Successfully!');
    }

    public function deletewebsite(Request $request): JsonResponse
    {
        CharityProductStoreWebsite::where('id', $request->id)->delete();

        return response()->json('Charity Website deleted Successfully!');
    }

    public function getwebsite(Request $request)
    {
        $charity_id = $request->charity_id;
        $charity    = CustomerCharity::where('id', $charity_id)->first();
        $website    = Website::where('store_website_id', $charity->store_website_id)->get();
        $Website    = CharityProductStoreWebsite::select('charity_product_store_websites.id', 'charity_product_store_websites.price', 'websites.name')->join('websites', 'charity_product_store_websites.website_id', 'websites.id')->where('charity_id', $charity_id)->get();
        $html       = '';
        foreach ($Website as $w) {
            $html .= '<tr><td>' . $w->name . '</td>';
            $html .= '<td>' . $w->price . '</td>';
            $html .= '<td><button onclick="delwebsite(' . $w->id . ')" type="button" class="btn btn-default">Delete</button></td></tr>';
        }

        echo $html = " <table class='table table-bordered' >
      <thead><tr><th>Website</th><th>Price</th></tr> </thead>
      <tbody>" . $html . '</tbody></table';
    }

    public function getCharityWebsiteStores($id): JsonResponse
    {
        $website_stores    = CustomerCharityWebsiteStore::with('websiteStore.website.storeWebsite')->where('customer_charity_id', $id)->get();
        $website_store_ids = $website_stores->pluck('website_store_id')->toArray();
        $website_ids       = WebsiteStore::whereIn('id', $website_store_ids)->pluck('website_id')->toArray();
        $store_website_ids = Website::whereIn('id', $website_ids)->pluck('store_website_id')->toArray();
        $website_ids       = Website::whereIn('store_website_id', $store_website_ids)->pluck('id')->toArray();
        $all_stores        = WebsiteStore::whereIn('website_id', $website_ids)->get();
        $all_websites      = StoreWebsite::get();

        return response()->json([
            'website_stores' => $website_stores,
            'all_stores'     => $all_stores,
            'all_websites'   => $all_websites,
        ]);
    }
}
