<?php

namespace App\Http\Controllers;

use App\ApiKey;
use App\ChatMessage;
use App\Customer;
use App\EmailNotificationEmailDetails;
use App\Helpers;
use App\Http\Requests\MakePaymentUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;
use App\Marketing\WhatsappConfig;
use App\Payment;
use App\PaymentMethod;
use App\Permission;
use App\Product;
use App\Role;
use App\Setting;
use App\SlackChannel;
use App\Task;
use App\User;
use App\UserLogin;
use App\UserLoginIp;
use App\UserProduct;
use App\UserRate;
use App\UserSysyemIp;
use App\WebhookNotification;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserController extends Controller
{
    const DEFAULT_FOR = 4; //For User

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('name', 'LIKE', '%'.$request->term.'%')->orWhere('email', 'LIKE', '%'.$request->term.'%')
                ->orWhere('phone', 'LIKE', '%'.$request->term.'%');
        }

        $data = $query->orderBy('name')->paginate(25)->appends(request()->except(['page']));
        $whatsapp = WhatsappConfig::where('status', 1)->pluck('number');
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('users.partials.list-users', compact('data', 'whatsapp'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }

        return view('users.index', compact('data', 'whatsapp'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function changeWhatsapp(Request $request): JsonResponse
    {
        $user = User::find($request->user_id);
        $data = ['whatsapp_number' => $request->whatsapp_number];
        $user->update($data);

        return response()->json(['success' => 'successfully updated', 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::pluck('name', 'name')->all();
        $users = User::all();
        $agent_roles = ['sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others'];
        $instances = WhatsappConfig::getWhatsappConfigs();
        $available_timezone = config('constants.AVAILABLE_TIMEZONES');

        return view('users.create', compact('roles', 'users', 'agent_roles', 'instances', 'available_timezone'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {

        $input = $request->all();
        $userRate = new UserRate;

        //get default whatsapp number for vendor from whatsapp config

        if (empty($input['whatsapp_number'])) {
            $task_info = WhatsappConfig::select('*')
                ->whereRaw('find_in_set('.self::DEFAULT_FOR.',default_for)')
                ->first();
            $input['whatsapp_number'] = $task_info->number;
        }

        $userRate->start_date = Carbon::now();
        $userRate->hourly_rate = $input['hourly_rate'];
        $userRate->currency = $input['currency'];

        unset($input['hourly_rate']);
        unset($input['currency']);

        $input['name'] = str_replace(' ', '_', $input['name']);
        $input['password'] = Hash::make($input['password']);
        if (isset($input['agent_role'])) {
            $input['agent_role'] = implode(',', $input['agent_role']);
        }

        $user = User::create($input);

        $userRate->user_id = $user->id;
        $userRate->save();

        return redirect()->to('/users/'.$user->id.'/edit')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = User::find($id);

        if (Auth::id() != $id) {
            return redirect()->route('users.index')->withWarning("You don't have access to this page!");
        }

        $users_array = Helpers::getUserArray(User::all());
        $roles = Role::pluck('name', 'name')->all();
        $users = User::all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $agent_roles = ['sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others'];
        $user_agent_roles = explode(',', $user->agent_role);
        $api_keys = ApiKey::select('number')->get();

        $pending_tasks = Task::where('is_statutory', 0)
            ->whereNull('is_completed')
            ->where(function ($query) use ($id) {
                return $query->orWhere('assign_from', $id)
                    ->orWhere('assign_to', $id);
            })->get();

        return view('users.show', [
            'user' => $user,
            'users_array' => $users_array,
            'roles' => $roles,
            'users' => $users,
            'userRole' => $userRole,
            'agent_roles' => $agent_roles,
            'user_agent_roles' => $user_agent_roles,
            'api_keys' => $api_keys,
            'pending_tasks' => $pending_tasks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $user = User::with('webhookNotification')->find($id);
        $roles = Role::orderBy('name')->pluck('name', 'id')->all();
        $permission = Permission::orderBy('name')->pluck('name', 'id')->all();
        $SlackChannel = SlackChannel::select(['channel_name as name', 'id'])->get();

        $users = User::all();
        $userRole = $user->roles->pluck('name', 'id')->all();
        $userPermission = $user->permissions->pluck('name', 'id')->all();
        $agent_roles = ['sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others'];
        $user_slack_channels = explode(',', $user->slack_channel_id);
        $user_agent_roles = explode(',', $user->agent_role);
        $api_keys = ApiKey::select('number')->get();
        $customers_all = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->whereRaw("customers.id NOT IN (SELECT customer_id FROM user_customers WHERE user_id != $id)")->get()->toArray();

        $userRate = UserRate::getRateForUser($user->id);

        $email_notification_data = EmailNotificationEmailDetails::where('user_id', $id)->first(); //Purpose : get email details - DEVTASK-4359
        $available_timezone = config('constants.AVAILABLE_TIMEZONES');

        return view(
            'users.edit',
            compact('user', 'users', 'roles', 'userRole', 'agent_roles', 'user_agent_roles', 'user_slack_channels', 'api_keys', 'customers_all', 'permission', 'userPermission', 'userRate', 'email_notification_data', 'available_timezone', 'SlackChannel') //Purpose : add email_notification_data - DEVTASK-4359
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {

        $input = $request->all();

        $hourly_rate = $input['hourly_rate'];
        $currency = $input['currency'];

        unset($input['hourly_rate']);
        unset($input['currency']);

        $input['name'] = str_replace(' ', '_', $input['name']);
        if (isset($input['agent_role'])) {
            $input['agent_role'] = implode(',', $input['agent_role']);
        } else {
            $input['agent_role'] = '';
        }

        if (isset($input['slack_channels'])) {
            $input['slack_channel_id'] = implode(',', $input['slack_channels']);
        } else {
            $input['slack_channel_id'] = '';
        }

        if (! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        //START - Purpose : Set Email notification status - DEVTASK-4359
        $input['mail_notification'] = 0;
        if (isset($request->email_notification_chkbox)) {
            if ($request->email_notification_chkbox == 1) {
                $input['mail_notification'] = 1;
            }
        }

        if ($request->notification_mail_id != '') {
            EmailNotificationEmailDetails::updateOrCreate(
                ['user_id' => $id],
                ['emails' => $request->notification_mail_id]
            );
        }
        //END - DEVTASK-4359

        $user = User::find($id);
        $user->update($input);

        if ($request->customer != null && $request->customer[0] != '') {
            $user->customers()->sync($request->customer);
        }

        $user->roles()->sync($request->input('roles'));
        $user->permissions()->sync($request->input('permissions'));

        $user->listing_approval_rate = $request->get('listing_approval_rate') ?? '0';
        $user->listing_rejection_rate = $request->get('listing_rejection_rate') ?? '0';
        $user->save();

        if ($request->webhook && isset($request->webhook['url']) && isset($request->webhook['payload'])) {
            WebhookNotification::updateOrCreate([
                'user_id' => $user->id,
            ], $request->webhook);
        }

        $userRate = new UserRate;
        $userRate->start_date = Carbon::now();
        $userRate->hourly_rate = $hourly_rate;
        $userRate->currency = $currency;
        $userRate->user_id = $user->id;
        $userRate->save();

        return redirect()->back()
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function unassignProducts(Request $request, $id): RedirectResponse
    {
        $user = User::find($id);

        $userProducts = UserProduct::where('user_id', $user->id)->pluck('product_id')->toArray();

        $products = Product::whereIn('id', $userProducts)->where('is_approved', 0)->where('is_listing_rejected', 0)->take($request->get('number') ?? 0)->get();

        foreach ($products as $product) {
            UserProduct::where('user_id', $user->id)->where('product_id', $product->id)->delete();
        }

        return redirect()->back()->with('success', 'Product unassigned successfully!');
    }

    public function showAllAssignedProductsForUser($id): View
    {
        $userProducts = UserProduct::where('user_id', $id)->with('product')->orderByDesc('created_at')->get();

        $user = User::find($id);

        return view('products.assigned_products_list_by_user', compact('userProducts', 'user'));
    }

    public function assignProducts(Request $request, $id): RedirectResponse
    {
        $user = User::find($id);
        $amount_assigned = 25;

        $products = Product::where('stock', '>=', 1)
            ->where('is_crop_ordered', 1)
            ->where('is_order_rejected', 0)
            ->where('is_approved', 0)
            ->where('is_listing_rejected', 0)
            ->where('isUploaded', 0)
            ->where('isFinal', 0);

        $user_products = UserProduct::pluck('product_id')->toArray();

        $products = $products->whereNotIn('id', $user_products)
            ->whereIn('category', [5, 6, 7, 9, 11, 21, 22, 23, 24, 25, 26, 29, 34, 36, 37, 52, 53, 54, 55, 56, 57, 58, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 76, 78, 79, 80, 81, 83, 84, 85, 87, 97, 98, 99, 100, 105, 109, 110, 111, 114, 117, 118])
            ->orderByDesc('is_on_sale')
            ->latest()
            ->take($amount_assigned)
            ->get();

        $user->products()->attach($products);

        if (count($products) >= $amount_assigned - 1) {
            $message = 'You have successfully assigned '.count($products).' products';

            return redirect()->back()->with('success', $message);
        }

        $remaining = $amount_assigned - count($products);

        $products = Product::where('stock', '>=', 1)
            ->where('is_crop_ordered', 1)
            ->where('is_order_rejected', 0)
            ->where('is_listing_rejected', 0)
            ->where('is_approved', 0)
            ->where('isUploaded', 0)
            ->where('isFinal', 0);

        $user_products = UserProduct::pluck('product_id')->toArray();

        $products = $products->whereNotIn('id', $user_products)->orderByDesc('is_on_sale')->latest()->take($remaining)->get();
        $user->products()->attach($products);

        if (count($products) > 0) {
            $message = 'You have successfully assigned products';
        } else {
            $message = 'There were no products to assign!';
        }

        return redirect()->back()->withSuccess($message);
    }

    public function login(Request $request): View
    {
        $date = $request->date ? $request->date : Carbon::now()->format('Y-m-d');
        $logins = UserLogin::whereBetween('login_at', [$date, Carbon::parse($date)->addDay()])->latest()->paginate(Setting::get('pagination'));

        return view('users.login', [
            'logins' => $logins,
            'date' => $date,
        ]);
    }

    public function activate(Request $request, $id): RedirectResponse
    {
        $user = User::find($id);

        if ($user->is_active == 1) {
            $user->is_active = 0;
        } else {
            $user->is_active = 1;
        }

        $user->save();

        return redirect()->back()->withSuccess('You have successfully updated the user!');
    }

    public function payments(Request $request)
    {
        $params = $request->all();

        $date = new DateTime;

        if (isset($params['year']) && isset($params['week'])) {
            $year = $params['year'];
            $week = $params['week'];
        } else {
            $week = $date->format('W');
            $year = $date->format('Y');
        }

        $result = getStartAndEndDate($week, $year);
        $start = $result['week_start'];
        $end = $result['week_end'];

        $users = User::join('hubstaff_payment_accounts as hpa', 'hpa.user_id', 'users.id')->with(['currentRate'])->get();
        $usersRatesThisWeek = UserRate::ratesForWeek($week, $year);

        $usersRatesPreviousWeek = UserRate::latestRatesForWeek($week - 1, $year);

        $activitiesForWeek = HubstaffActivity::getActivitiesForWeek($week, $year);

        $paymentsDone = Payment::getConsidatedUserPayments();

        $amountToBePaid = HubstaffPaymentAccount::getConsidatedUserAmountToBePaid();

        $now = now();

        foreach ($users as $user) {
            $user->secondsTracked = 0;
            $user->currency = '-';
            $user->total = 0;

            $userPaymentsDone = 0;
            $userPaymentsDoneModel = $paymentsDone->first(function ($value) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($userPaymentsDoneModel) {
                $userPaymentsDone = $userPaymentsDoneModel->paid;
            }

            $userPaymentsToBeDone = 0;
            $userAmountToBePaidModel = $amountToBePaid->first(function ($value) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($userAmountToBePaidModel) {
                $userPaymentsToBeDone = $userAmountToBePaidModel->amount;
            }

            $user->balance = $userPaymentsToBeDone - $userPaymentsDone;

            $invidualRatesPreviousWeek = $usersRatesPreviousWeek->first(function ($value, $key) use ($user) {
                return $value->user_id == $user->id;
            });

            $weekRates = [];

            if ($invidualRatesPreviousWeek) {
                $weekRates[] = [
                    'start_date' => $start,
                    'rate' => $invidualRatesPreviousWeek->hourly_rate,
                    'currency' => $invidualRatesPreviousWeek->currency,
                ];
            }

            $rates = $usersRatesThisWeek->filter(function ($value, $key) use ($user) {
                return $value->user_id == $user->id;
            });

            if ($rates) {
                foreach ($rates as $rate) {
                    $weekRates[] = [
                        'start_date' => $rate->start_date,
                        'rate' => $rate->hourly_rate,
                        'currency' => $rate->currency,
                    ];
                }
            }

            usort($weekRates, function ($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });

            if (count($weekRates) > 0) {
                $lastEntry = $weekRates[count($weekRates) - 1];

                $weekRates[] = [
                    'start_date' => $end,
                    'rate' => $lastEntry['rate'],
                    'currency' => $lastEntry['currency'],
                ];

                $user->currency = $lastEntry['currency'];
            }

            $activities = $activitiesForWeek->filter(function ($value, $key) use ($user) {
                return $value->system_user_id === $user->id;
            });

            $user->trackedActivitiesForWeek = $activities;

            foreach ($activities as $activity) {
                $user->secondsTracked += $activity->tracked;
                $i = 0;
                while ($i < count($weekRates) - 1) {
                    $start = $weekRates[$i];
                    $end = $weekRates[$i + 1];

                    if ($activity->starts_at >= $start['start_date'] && $activity->start_time < $end['start_date']) {
                        // the activity needs calculation for the start rate and hence do it
                        $earnings = $activity->tracked * ($start['rate'] / 60 / 60);
                        $activity->rate = $start['rate'];
                        $activity->earnings = $earnings;
                        $user->total += $earnings;
                        break;
                    }
                    $i++;
                }
            }
        }

        //exit;
        $paymentMethods = [];
        foreach (PaymentMethod::all() as $paymentMethod) {
            $paymentMethods[$paymentMethod->id] = $paymentMethod->name;
        }

        return view(
            'users.payments',
            [
                'users' => $users,
                'selectedYear' => $year,
                'selectedWeek' => $week,
                'paymentMethods' => $paymentMethods,
            ]
        );
    }

    public function makePayment(MakePaymentUserRequest $request): RedirectResponse
    {

        $parameters = $request->all();

        $paymentMethod = PaymentMethod::firstOrCreate([
            'name' => $parameters['payment_method'],
        ]);

        $payment = new Payment;
        $payment->user_id = $parameters['user_id'];
        $payment->amount = $parameters['amount'];
        $payment->currency = $parameters['currency'];
        $payment->note = $parameters['note'];
        $payment->payment_method_id = $paymentMethod->id;
        $payment->save();

        return redirect()->to('/hubstaff/payments')->withSuccess('Payment saved!');
    }

    public function checkUserLogins()
    {
        Log::channel('customer')->info(Carbon::now().' begin checking users logins');
        $users = User::all();

        foreach ($users as $user) {
            $login = UserLogin::where('user_id', $user->id)
                ->where('created_at', '>', Carbon::now()->startOfDay())
                ->latest()
                ->first();

            if (! $login) {
                $login = UserLogin::create(['user_id' => $user->id]);
            }

            if (Cache::has('user-is-online-'.$user->id)) {
                if ($login->logout_at) {
                    UserLogin::create(['user_id' => $user->id, 'login_at' => Carbon::now()]);
                } elseif (! $login->login_at) {
                    $login->update(['login_at' => Carbon::now()]);
                }
            } else {
                if ($login->created_at && ! $login->logout_at) {
                    $login->update(['logout_at' => Carbon::now()]);
                }
            }
        }

        Log::channel('customer')->info(Carbon::now().' end of checking users logins');
    }

    public function searchUser(Request $request)
    {
        $q = $request->input('q');

        $results = User::select('id', 'name', 'name AS text')
            ->orWhere('name', 'LIKE', '%'.$q.'%')
            ->offset(0)
            ->limit(15)
            ->get();

        return $results;
    }

    public function searchUserGlobal(Request $request)
    {
        $q = $request->input('q');

        $results = User::select('id', 'name', 'email', 'phone')
            ->orWhere('name', 'LIKE', '%'.$q.'%')
            ->orWhere('email', 'LIKE', '%'.$q.'%')
            ->orWhere('phone', 'LIKE', '%'.$q.'%')
            ->offset(0)
            ->limit(200)
            ->get();

        $html = view('partials.user_search_results', ['users' => $results])->render();

        return $results;
    }

    public function loginIps(Request $request)
    {
        $user_ips = UserLoginIp::join('users', 'user_login_ips.user_id', '=', 'users.id')
            ->select('user_login_ips.*', 'users.email')
            ->latest()
            ->get();
        if ($request->ajax()) {
            return response()->json(['code' => 200, 'data' => $user_ips]);
        } else {
            return view('users.ips', compact('user_ips'));
        }
    }

    public function addSystemIp(Request $request): JsonResponse
    {
        if ($request->ip) {
            $shell_bash_cmd = 'bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f add -i '.$request->ip.' -c '.$request->get('comment', '');
            $shell_cmd = shell_exec($shell_bash_cmd);

            // Decode the JSON string into a PHP array
            $data = json_decode($shell_cmd, true);
            UserSysyemIp::create([
                'index_txt' => $data['index'] ?? 'null',
                'ip' => $request->ip,
                'user_id' => $request->user_id ?? null,
                'other_user_name' => $request->other_user_name ?? null,
                'notes' => $request->comment ?? null,
                'source' => 'system',
                'command' => $shell_bash_cmd,
                'status' => $data['status'] ?? '',
                'message' => $data['message'] ?? '',
            ]);

            $userID = $request->user_id ?? null;

            if ($userID) {
                $user = User::find($userID);
                if ($user) {
                    $user->is_whitelisted = 1;
                    $user->save();
                    $params = [];
                    $params['user_id'] = $userID;
                    $params['message'] = 'Your ip address '.$request->ip.'  whitelist request has been approved';
                    // send chat message
                    $chat_message = ChatMessage::create($params);
                    // send
                    app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                }
            }

            return response()->json(['code' => 200, 'data' => 'Success']);
        }

        return response()->json(['code' => 500, 'data' => 'Error occured!']);
    }

    public function deleteSystemIp(Request $request): JsonResponse
    {
        if ($request->usersystemid) {
            $row = UserSysyemIp::where('id', $request->usersystemid)->first();
            $userID = $row->user_id ?? null;
            shell_exec('bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f delete -n '.$row->index ?? '');

            $row->delete();
            if ($userID) {
                $user = User::find($userID);
                if ($user) {
                    $user->is_whitelisted = 0;
                    $user->save();
                }
            }

            return response()->json(['code' => 200, 'data' => 'Success']);
        }

        return response()->json(['code' => 500, 'data' => 'Error occured!']);
    }

    public function statusChange(Request $request)
    {
        if ($request->status) {
            $user_ip_status = UserLoginIp::where('id', $request->id)->get();
            if ($request->status == 'Active') {
                $user_ip_status->is_status = UserLoginIp::where('id', $request->id)
                    ->update(['is_active' => true]);
            } else {
                $user_ip_status->is_status = UserLoginIp::where('id', $request->id)
                    ->update(['is_active' => false]);
            }
        }

        return $request->status;
    }

    public function addSystemIpFromText(Request $request): JsonResponse
    {
        if ($request->id) {
            $chatMessage = ChatMessage::where('id', $request->id)->first();
            if ($chatMessage) {
                preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $chatMessage->message, $ip_matches);
                $ip_matches = array_filter($ip_matches);
                if (! empty($ip_matches)) {
                    if ($chatMessage->user_id > 0 || $chatMessage->erp_user > 0) {
                        foreach ($ip_matches[0] as $key => $value) {
                            $shell_cmd = shell_exec('bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f add -i '.$value.' -c Added from chat messages');
                            $userID = $chatMessage->erp_user ?? $chatMessage->user_id;

                            UserSysyemIp::create([
                                'index_txt' => $shell_cmd['index'] ?? 'null',
                                'ip' => $value,
                                'user_id' => $chatMessage->erp_user ?? $chatMessage->user_id,
                                'other_user_name' => $request->other_user_name ?? null,
                                'notes' => 'Added from chat messages',
                                'source' => 'message',
                            ]);

                            if ($userID) {
                                $user = User::find($userID);
                                if ($user) {
                                    $user->is_whitelisted = 1;
                                    $user->save();
                                    $params = [];
                                    $params['user_id'] = $userID;
                                    $params['message'] = 'Your ip address '.$value.'  whitelist request has been approved';
                                    // send chat message
                                    $chat_message = ChatMessage::create($params);
                                    // send
                                    app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                                }
                            }
                        }
                    }
                }

                return response()->json(['code' => 200, 'message' => 'Success']);
            } else {
                return response()->json(['code' => 500, 'message' => 'Can only white list user ip only!']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'Message record not found!']);
    }

    public function bulkDeleteSystemIp(Request $request): JsonResponse
    {
        try {
            $systemIps = UserSysyemIp::get();
            if (! empty($systemIps)) {
                foreach ($systemIps as $systemIp) {
                    $userID = $systemIp->user_id ?? null;
                    shell_exec('bash '.getenv('DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f delete -n '.$systemIp->index ?? '');
                    $systemIp->delete();
                    if ($userID) {
                        $user = User::find($userID);
                        if ($user) {
                            $user->is_whitelisted = 0;
                            $user->save();
                        }
                    }
                }

                return response()->json(['code' => 200, 'data' => 'Success']);
            }
        } catch (\Throwable $e) {
            return response()->json(['code' => '500', 'message' => $e->getMessage()]);
        }
    }

    public function addSystemIpFromEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
            if (! empty($user)) {
                UserSysyemIp::create([
                    'index_txt' => $request->index_txt ?? 'null',
                    'ip' => $request->ip,
                    'user_id' => $user->id ?? null,
                    'notes' => $request->comment ?? null,
                    'source' => 'email',
                ]);
                $user->is_whitelisted = 1;
                $user->save();
                $params = [];
                $params['user_id'] = $user->id;
                $params['message'] = 'Your ip address '.$request->ip.'  whitelist request has been approved';
                // send chat message
                $chat_message = ChatMessage::create($params);
                // send
                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
            }

            return response()->json(['code' => 200, 'data' => 'Success']);
        }

        return response()->json(['code' => 500, 'data' => 'Error occured!']);
    }
}
