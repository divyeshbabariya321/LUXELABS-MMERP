<?php

namespace App\Http\Controllers;
use App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\ChatMessage;
use App\StoreWebsite;
use App\StoreWebsiteUsers;
use App\LogStoreWebsiteUser;
use Illuminate\Http\Request;
use App\StoreWebsiteUserHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use seo2websites\MagentoHelper\MagentoHelperv2;

class MagentoUserFromErpController extends Controller
{
    public function index(Request $request): View
    {
        $storeWebsites = StoreWebsiteUsers::select(
            'store_websites.id as store_website_id',
            'store_website_users.username',
            'store_website_users.password',
            'store_websites.website',
            'store_websites.title',
            'store_website_users.website_mode',
            'store_websites.store_code_id',
            'store_website_users.created_at',
            'store_websites.magento_url',
            'store_website_users.user_role',
            'store_website_users.user_role_name',
            'store_website_users.is_active',
            'store_website_users.id',
        )->join('store_websites', function ($q) {
            $q->on('store_websites.id', '=', 'store_website_users.store_website_id');
            $q->where('store_websites.website_source', '=', 'magento');
        })
            ->where('store_website_users.is_deleted', 0)
            ->whereNull('store_websites.deleted_at')
            ->groupBy('store_website_users.username', 'store_websites.website', 'store_website_users.website_mode', 'store_websites.store_code_id')
            ->orderByDesc('store_website_users.id');

        //Apply store_website_id if exists
        if ($request->get('store_website_id')) {
            $storeWebsites->where('store_websites.id', $request->get('store_website_id'));
        }

        //Apply username if exists
        if ($request->get('username')) {
            $storeWebsites->where('store_website_users.username', $request->get('username'));
        }

        //Apply role if exists
        if ($request->get('role')) {
            $storeWebsites->where('store_website_users.user_role', $request->get('role'));
        }
        //show 20 records per page
        $storeWebsites = $storeWebsites->paginate(20);

        //For select website filter list
        $allStoreWebsites = StoreWebsite::where('website_source', '=', 'magento')
            ->whereNotNull('magento_username')
            ->whereNull('deleted_at')
            ->pluck('website', 'id')
            ->toArray();

        //For select role filter list
        $magentoRoles = StoreWebsiteUsers::whereNotNull('user_role_name')
            ->groupBy('user_role')
            ->pluck('user_role_name', 'user_role')
            ->toArray();

        return view('magento-user-from-erp.index', compact('storeWebsites', 'magentoRoles', 'allStoreWebsites'));
    }

    /**
     * Create User.
     *
     * @param Request $request [description]
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function magentoUserCreate(Request $request): JsonResponse
    {
        $post      = $request->all();
        $validator = Validator::make($post, [
            'username'    => 'required',
            'userEmail'   => 'required|email',
            'firstName'   => 'required',
            'lastName'    => 'required',
            'website'     => 'required',
            'userrole'    => 'required',
            'websitemode' => 'required',
            'password'    => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages     = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : " . $er . '<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $storeWebsite = StoreWebsite::where([
            ['website_source', '=', 'magento'],
            ['id', '=', $post['website']],
        ])->whereNotNull('magento_username')
            ->whereNull('deleted_at')
            ->first();

        if (! empty($storeWebsite->id)) {
            // Continue with the code for successful response (status code 200)
            $this->savelogwebsiteuser('#2', $storeWebsite->id, $post['username'], $post['userEmail'], $post['firstName'], $post['lastName'], $post['password'], $post['websitemode'], 'For this Website ' . $storeWebsite->id . ' ,A user has been created with specific roles.');

            $checkUserNameExist = '';
            $checkUserExist     = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('email', $post['userEmail'])->first();

            if (empty($checkUserExist)) {
                $checkUserNameExist = StoreWebsiteUsers::where('store_website_id', $storeWebsite->id)->where('is_deleted', 0)->where('username', $post['username'])->first();
            }

            if (! empty($checkUserExist)) {
                return response()->json(['code' => 500, 'error' => 'User Email already exist!']);
            }

            if (! empty($checkUserNameExist)) {
                return response()->json(['code' => 500, 'error' => 'Username already exist!']);
            }

            $uppercase = preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_@.\/#&+-]+)$/', $post['password']);
            if (! $uppercase || strlen($post['password']) < 7) {
                return response()->json(['code' => 500, 'error' => 'Your password must be at least 7 characters.Your password must include both numeric and alphabetic characters.']);
            }

            $params['username']         = $post['username'];
            $params['email']            = $post['userEmail'];
            $params['first_name']       = $post['firstName'];
            $params['last_name']        = $post['lastName'];
            $params['store_website_id'] = $post['website'];
            $params['user_role']        = $post['userrole'];
            $params['user_role_name']   = $post['userRoleName'];
            $params['website_mode']     = $post['websitemode'];
            $params['password']         = $post['password'];
            $params['is_active']        = 1;

            $StoreWebsiteUsersid = StoreWebsiteUsers::create($params);

            if ($post['userEmail'] && $post['password']) {
                $message           = 'Email: ' . $post['userEmail'] . ', Password is: ' . $post['password'];
                $params['user_id'] = Auth::id();
                $params['message'] = $message;
                ChatMessage::create($params);
            }

            $magentoHelper = new MagentoHelperv2();
            $magentoHelper->addMagentouser($storeWebsite, $post);

            StoreWebsiteUserHistory::create([
                'store_website_id'      => $StoreWebsiteUsersid->store_website_id,
                'store_website_user_id' => $StoreWebsiteUsersid->id,
                'model'                 => StoreWebsiteUsers::class,
                'attribute'             => 'username_password',
                'old_value'             => 'new_added',
                'new_value'             => 'new_added',
                'user_id'               => Auth::id(),
            ]);

            return response()->json(['code' => 200, 'messages' => 'User details saved successfully', 'error' => '']);
        } else {
            return response()->json(['code' => 200, 'messages' => '', 'error' => 'Store details not found.']);
        }
    }

    /**
     * Save log webstie.
     *
     * @param mixed $log_case_id
     * @param mixed $id
     * @param mixed $username
     * @param mixed $userEmail
     * @param mixed $firstName
     * @param mixed $lastName
     * @param mixed $password
     * @param mixed $website_mode
     * @param mixed $msg
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function savelogwebsiteuser($log_case_id, $id, $username, $userEmail, $firstName, $lastName, $password, $website_mode, $msg)
    {
        $log                   = new LogStoreWebsiteUser();
        $log->log_case_id      = $log_case_id;
        $log->store_website_id = $id;
        $log->username         = $username;
        $log->useremail        = $userEmail;
        $log->first_name       = $firstName;
        $log->last_name        = $lastName;
        $log->password         = $password;
        $log->website_mode     = $website_mode;
        $log->log_msg          = $msg;
        $log->save();
    }

    /**
     * Get roles.
     *
     * @param Request $request [description]
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getRoles(Request $request): JsonResponse
    {
        //For select website filter list
        $magentStoreData = StoreWebsite::where([
            ['website_source', '=', 'magento'],
            ['id', '=', $request->website_id],
        ])
            ->whereNotNull('magento_username')
            ->whereNull('deleted_at')
            ->first();

        $magento_url = $magentStoreData['magento_url'];

        if (! empty($magento_url)) {
            $token_response = Http::post(rtrim($magento_url, '/') . '/rest/V1/integration/admin/token', [
                'username' => $magentStoreData['magento_username'],
                'password' => $magentStoreData['magento_password'],
            ]);

            if ($token_response->ok()) {
                $generated_token            = trim($token_response->body(), '"');
                $magentStoreData->api_token = $generated_token;
                $magentStoreData->save();
                $role_response = Http::withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $magentStoreData['api_token'],
                    'Accept'        => 'application/json',
                ])->get(rtrim($magento_url, '/') . '/rest/all/V1/adminroles');

                if ($role_response->ok()) {
                    return response()->json(['code' => 500, 'roles' => json_decode($role_response->body()), 'error' => '']);
                } else {
                    return response()->json(['code' => 500, 'roles' => '', 'error' => $role_response->json('message')]);
                }
            } else {
                return response()->json(['code' => 200, 'roles' => '', 'error' => $token_response->json('message')]);
            }
        } else {
            return response()->json(['code' => 500, 'roles' => '', 'error' => 'Not found store url.']);
        }
    }

    /**
     * Handle account status.
     *
     * @param Request $request [description]
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function accountStatus(Request $request): JsonResponse
    {
        $status            = $request->get('status');
        $update_id         = $request->get('update_id');
        $StoreWebsiteUsers = StoreWebsiteUsers::find($update_id);
        $store_website     = StoreWebsite::where([
            ['website_source', '=', 'magento'],
            ['id', '=', $StoreWebsiteUsers['store_website_id']],
        ])
            ->whereNotNull('magento_username')
            ->whereNull('deleted_at')
            ->first();
        if (! empty($store_website['magento_url'])) {
            $client   = new \GuzzleHttp\Client();
            $response = $client->request('POST', rtrim($store_website['magento_url'], '/') . '/rest/V1/multistore/editadminuser', [
                'body'    => json_encode(['username' => $StoreWebsiteUsers['username'], 'is_active' => ($status == 'true') ? 1 : 0]),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $account_status = json_decode(json_decode($response->getBody()->getContents()), true);
            if ($account_status['status'] == 'error') {
                return response()->json(['code' => 200, 'messages' => '', 'error' => $account_status[0]]);
            } else {
                $StoreWebsiteUsers->is_active = ($status == 'true') ? 1 : 0;
                $StoreWebsiteUsers->save();

                return response()->json(['code' => 200, 'messages' => $account_status, 'error' => '']);
            }
        } else {
            return response()->json(['code' => 200, 'messages' => '', 'error' => 'Store website not found.']);
        }
    }
}
