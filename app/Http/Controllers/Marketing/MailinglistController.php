<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\EmailEvent;
use App\Http\Controllers\Controller;
use App\Loggers\MailinglistIinfluencersDetailLogs;
use App\Loggers\MailinglistIinfluencersLogs;
use App\LogRequest;
use App\Mailinglist;
use App\MailinglistTemplate;
use App\MailingRemark;
use App\MaillistCustomerHistory;
use App\Models\ListContact;
use App\Service;
use App\Setting;
use App\StoreWebsite;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MailinglistController extends Controller
{
    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $services = Service::pluck('name', 'id');
        $list = Mailinglist::paginate(15);
        $websites = StoreWebsite::select('id', 'title')->orderByDesc('id')->get();

        return view('marketing.mailinglist.index', compact('services', 'list', 'websites'));
    }

    /**
     * @param  Request  $request
     */
    public function textcurl(): JsonResponse
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        $ch = curl_init();
        $url = 'http://165.232.42.174/api/v1/lists';
        $req = 'api_token='.getenv('ACELLE_MAIL_API_TOKEN').'&name=List+1&from_email=admin@abccorp.org&from_name=ABC+Corp.&default_subject=Welcome+to+ABC+Corp.&contact[company]=ABC+Corp.&contact[state]=Armagh&contact[address_1]=14+Tottenham+Court+Road+London+England&contact[address_2]=44-46+Morningside+Road+Edinburgh+Scotland&contact[city]=Noname&contact[zip]=80000&contact[phone]=123+456+889&contact[country_id]=1&contact[email]=info@abccorp.org&contact[url]=https://www.abccorp.org&subscribe_confirmation=1&send_welcome_email=1&unsubscribe_notification=1';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch,
            CURLOPT_POSTFIELDS, $req);

        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        dd(json_decode($result));
        if (curl_errno($ch)) {
            echo 'Error:'.curl_error($ch);
        }
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($ch);
        LogRequest::log($startTime, $url, 'POST', json_encode($req), json_decode($result), $httpcode, Controllers\MailinglistController::class, 'MultiRunErpEvent');
    }

    public function create(Request $request)
    {
        $rules = [
            'service_id' => 'required',
            'website_id' => 'required',
            'email' => 'required',
            'name' => 'required',
            'subject' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return ['status' => false, 'messages' => $validation->getMessageBag()];
        }
        $website_id = $request->website_id;
        $store_website = StoreWebsite::Where('id', $website_id)->first();
        //Find Service
        $service = Service::find($request->service_id);

        if ($service) {
            if (strpos(strtolower($service->name), strtolower('SendInBlue')) !== false) {
                $api_key = ($store_website->send_in_blue_api != '') ? $store_website->send_in_blue_api : config('env.SEND_IN_BLUE_API');
                $curl = curl_init();
                $url = 'https://api.sendinblue.com/v3/contacts/lists';
                $data = [
                    'folderId' => 1,
                    'name' => $request->name,
                ];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);
                Log::info($response);
                $res = json_decode($response);

                $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                LogRequest::log($startTime, $url, 'POST', json_encode($data), $res, $httpcode, Controllers\MailinglistController::class, 'create');
                if (! isset($res->id) && isset($res->code) && isset($res->message)) {
                    $errror_message = $res->code.':'.$res->message;

                    return response()->json(['status' => false, 'messages' => [$errror_message]]);
                }
                Mailinglist::create([
                    'id' => $res->id,
                    'name' => $request->name,
                    'website_id' => $website_id,
                    'service_id' => $request->service_id,
                    'remote_id' => $res->id,
                    'send_in_blue_api' => $store_website->send_in_blue_api,
                    'send_in_blue_account' => $store_website->send_in_blue_account,
                ]);
            }

            if (strpos($service->name, 'AcelleMail') !== false) {
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://acelle.theluxuryunlimited.com/api/v1/lists?api_token='.config('env.ACELLE_MAIL_API_TOKEN'),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => ['contact[company]' => '.', 'contact[state]' => 'afdf', 'name' => $request->name, 'default_subject' => $request->subject, 'from_email' => $request->email, 'from_name' => 'dsfsd', 'contact[address_1]' => 'af', 'contact[country_id]' => '219', 'contact[city]' => 'sdf', 'contact[zip]' => 'd', 'contact[phone]' => 'd', 'contact[email]' => $request->email],
                ]);

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);

                $res = json_decode($response);
                LogRequest::log($startTime, $url, 'POST', json_encode(['contact[company]' => '.', 'contact[state]' => 'afdf', 'name' => $request->name, 'default_subject' => $request->subject, 'from_email' => $request->email, 'from_name' => 'dsfsd', 'contact[address_1]' => 'af', 'contact[country_id]' => '219', 'contact[city]' => 'sdf', 'contact[zip]' => 'd', 'contact[phone]' => 'd', 'contact[email]' => $request->email]), $res, $httpcode, Controllers\MailinglistController::class, 'create');
                Log::info($res);
                if (! isset($res->status) && ! isset($res->list_uid)) {
                    return response()->json(['status' => false, 'messages' => ['Not getting any response. Please check AcelleMail API']]);
                }
                if ($res->status == 1) {
                    //getting last id
                    $list = Mailinglist::orderByDesc('id')->first();
                    if ($list) {
                        $id = ($list->id + 1);
                    } else {
                        $id = 1;
                    }
                    Mailinglist::create([
                        'id' => $id,
                        'name' => $request->name,
                        'website_id' => $website_id,
                        'email' => $request->email,
                        'service_id' => $request->service_id,
                        'remote_id' => $res->list_uid,
                    ]);

                    return response()->json(true);
                }
            }
        } else {
            return response()->json(false);
        }

        return response()->json(true);
    }

    /**
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id, Request $request): View
    {
        $customers = Customer::whereNotNull('email');
        if (! is_null($request->term)) {
            $customers = $customers->where('email', 'LIKE', "%{$request->term}%");
        }
        //Total Result
        if (request('total') != null) {
            //search with date
            if (request('total') == 1 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range))->where('active', 1);
                    })->where('do_not_disturb', 0);
                } else {
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)])->where('active', 1);
                    })->where('do_not_disturb', 0);
                }
            } elseif (request('total') == 1) {
                $customers->whereHas('customerMarketingPlatformActive', function ($qu) {
                    $qu->where('active', 1);
                })->where('do_not_disturb', 0);
            }

            if (request('total') == 2 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->doesntHave('customerMarketingPlatformActive')->whereDate('created_at', end($range))->where('do_not_disturb', 0);
                } else {
                    $customers->doesntHave('customerMarketingPlatformActive')->whereBetween('created_at', [$range[0], end($range)])->where('do_not_disturb', 0);
                }
            }

            if (request('total') == 2) {
                $customers->doesntHave('customerMarketingPlatformActive')->where('do_not_disturb', 0);
            }

            if (request('total') == 3 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->where('do_not_disturb', 1)->whereDate('updated_at', end($range));
                } else {
                    $customers->where('do_not_disturb', 1)->whereBetween('updated_at', [$range[0], end($range)]);
                }
            } elseif (request('total') == 3) {
                $customers->where('do_not_disturb', 1);
            }

            if (request('total') == 4 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->whereHas('leads', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range));
                    });
                } else {
                    $customers->whereHas('leads', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)]);
                    });
                }
            } elseif (request('total') == 4) {
                $customers->whereHas('leads');
            }

            if (request('total') == 5 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->whereHas('orders', function ($qu) use ($range) {
                        $qu->whereDate('created_at', end($range));
                    });
                } else {
                    $customers->whereHas('orders', function ($qu) use ($range) {
                        $qu->whereBetween('created_at', [$range[0], end($range)]);
                    });
                }
            } elseif (request('total') == 5) {
                $customers->whereHas('orders');
            }

            if (request('total') == 6 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) {
                        $qu->where('active', 1);
                    })->where('broadcast_number', null)->whereDate('created_at', end($range));
                } else {
                    $customers->whereHas('customerMarketingPlatformActive', function ($qu) {
                        $qu->where('active', 1);
                    })->where('broadcast_number', null)->whereBetween('created_at', [$range[0], end($range)]);
                }
            } elseif (request('total') == 6) {
                $customers->whereHas('customerMarketingPlatformActive', function ($qu) {
                    $qu->where('active', 1);
                })->where('broadcast_number', null)->where('do_not_disturb', 0);
            }

            if (request('total') == 7 && request('customrange') != null) {
                $range = explode(' - ', request('customrange'));
                if ($range[0] == end($range)) {
                    $customers->whereHas('notDelieveredImQueueMessage', function ($qu) use ($range) {
                        $qu->whereDate('send_after', end($range));
                    });
                } else {
                    $customers->whereHas('notDelieveredImQueueMessage', function ($qu) use ($range) {
                        $qu->whereBetween('send_after', [$range[0], end($range)]);
                    });
                }
            } elseif (request('total') == 7) {
                $customers->whereHas('notDelieveredImQueueMessage');
            }
        }

        if (! empty($request->store_id)) {
            $customers = $customers->where('store_website_id', $request->store_id);
        }

        $customers = $customers->select('email', 'id', 'name', 'do_not_disturb', 'source', 'created_at')->paginate(20);
        $list = Mailinglist::where('remote_id', $id)->with('listCustomers')->first();

        $contacts = ($list) ? $list->listCustomers->pluck('id')->toArray() : [];

        $countDNDCustomers = Customer::where('do_not_disturb', '1')->count();

        return view('marketing.mailinglist.show', compact('customers', 'id', 'contacts', 'list', 'countDNDCustomers'));
    }

    /**
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request): View
    {
        $services = Service::all();
        $websites = StoreWebsite::select('id', 'title')->orderByDesc('id')->get();
        $list = Mailinglist::where('remote_id', $id)->first();

        return view('marketing.mailinglist.edit', compact('list', 'services', 'websites'));
    }

    /**
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update($id, Request $request): JsonResponse
    {
        $mailing_list = Mailinglist::find($id);
        $mailing_list->website_id = $request->website_id;
        $mailing_list->service_id = $request->service_id;
        $mailing_list->name = $request->name;
        $mailing_list->email = $request->email;
        $mailing_list->save();

        return response()->json(true);
    }

    /**
     * @param  mixed  $id
     * @param  mixed  $email
     */
    public function addToList($id, $email): JsonResponse
    {
        //getting mailing list
        $list = Mailinglist::where('remote_id', $id)->first();
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($list->service && isset($list->service->name)) {
            if ($list->service->name == 'AcelleMail') {
                $url = "http://165.232.42.174/api/v1/subscribers/email/'.$email.'?api_token=".config('env.ACELLE_MAIL_API_TOKEN');
                $headers = ['Content-Type: application/json'];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $parameters = [];
                curl_close($curl);
                $res = json_decode($response);

                LogRequest::log($startTime, $url, 'GET', json_encode([]), $res, $httpcode, Controllers\MailinglistController::class, 'addToList');
                if (isset($res->subscribers)) {
                    foreach ($res->subscribers as $subscriber) {
                        if ($subscriber->list_uid == $id) {
                            return response()->json(['status' => 'success']);
                        }
                    }
                } else {
                    return response()->json(['status' => 'error']);
                }

                //Assign Customer to list

                $curl = curl_init();
                $url = 'http://165.232.42.174/api/v1/lists/'.$id.'/subscribers/store?api_token='.config('env.ACELLE_MAIL_API_TOKEN');

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => ['EMAIL' => $email, 'name' => ' '],
                ]);

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // response decode
                $response = json_decode($response);
                $parameters = [];
                LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $res, $httpcode, Controllers\MailinglistController::class, 'addToList');

                //subscribe to email
                $url = 'http://165.232.42.174/api/v1/lists/'.$id.'/subscribers/'.$response->subscriber_uid.'/subscribe?api_token='.config('env.ACELLE_MAIL_API_TOKEN');
                $headers = ['Content-Type: application/json'];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($curl);

                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $parameters = [];
                LogRequest::log($startTime, $url, 'PATCH', json_encode($parameters), json_decode($response), $httpcode, Controllers\MailinglistController::class, 'addToList');
                $customer = Customer::where('email', $email)->first();
                ListContact::where('customer_id', $customer->id)->delete();
                $list->listCustomers()->attach($customer->id);

                return response()->json(['status' => 'success']);
            }
        }

        $website = StoreWebsite::where('id', $list->website_id)->first();
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : config('env.SEND_IN_BLUE_API');

        $curl = curl_init();
        $data = [
            'email' => $email,
            'listIds' => [intval($id)],
        ];
        $url = 'https://api.sendinblue.com/v3/contacts}';
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'api-key: '.$api_key,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $res = json_decode($response);
        LogRequest::log($startTime, $url, 'POST', json_encode($data), $res, $httpcode, Controllers\MailinglistController::class, 'addToList');
        Log::info($response);
        if (isset($res->message)) {
            if ($res->message == 'Contact already exist') {
                $curl3 = curl_init();
                $url = 'https://api.sendinblue.com/v3/contacts/'.$email;
                curl_setopt_array($curl3, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);
                curl_exec($curl3);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl3);

                LogRequest::log($startTime, $url, 'GET', json_encode($data), json_deocde($res), $httpcode, Controllers\MailinglistController::class, 'addToList');

                $curl2 = curl_init();
                $url = 'https://api.sendinblue.com/v3/contacts';
                curl_setopt_array($curl2, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);
                $resp = curl_exec($curl2);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl2);
                $ress = json_decode($resp);
                LogRequest::log($startTime, $url, 'POST', json_encode($data), $ress, $httpcode, Controllers\MailinglistController::class, 'addToList');
                if (isset($ress->message)) {
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                $mailinglist = Mailinglist::find($id);
                ListContact::where('customer_id', $customer->id)->delete();
                $mailinglist->listCustomers()->attach($customer->id);

                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error']);
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * @param  mixed  $id
     * @param  mixed  $email
     */
    public function delete($id, $email): RedirectResponse
    {
        $mailinglist = Mailinglist::find($id);
        $website = StoreWebsite::where('id', $mailinglist->website_id)->first();
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : config('env.SEND_IN_BLUE_API');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = "https://api.sendinblue.com/v3/contacts/' . $email";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => [
                'api-key: '.$api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        $res = json_decode($response);
        $parameters = [];

        LogRequest::log($startTime, $url, 'DELETE', json_encode($parameters), $res, $httpcode, Controllers\MailinglistController::class, 'delete');

        if (isset($res->message)) {
            return redirect()->back()->withErrors($res->message);
        } else {
            $customer = Customer::where('email', $email)->first();

            $mailinglist->listCustomers()->detach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * @param  mixed  $id
     */
    public function deleteList($id): RedirectResponse
    {
        //getting mailing list
        $list = Mailinglist::where('remote_id', $id)->first();
        $website = StoreWebsite::where('id', $list->website_id)->first();
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : config('env.SEND_IN_BLUE_API');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($list->service && isset($list->service->name)) {
            if ($list->service->name == 'AcelleMail') {
                $curl = curl_init();
                $url = "http://165.232.42.174/api/v1/lists/' . $list->remote_id . '/delete?api_token=' . config('env.ACELLE_MAIL_API_TOKEN')";

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => [],
                ]);

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);
                $res = json_decode($response);

                LogRequest::log($startTime, $url, 'POST', json_encode([]), $res, $httpcode, Controllers\MailinglistController::class, 'deleteList');

                if (! isset($res->status)) {
                    return redirect()->back()->with('error', 'Not getting any response. Please check AcelleMail API');
                }
            } else {
                $curl = curl_init();
                $url = "https://api.sendinblue.com/v3/contacts/lists/' . $id";
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);
                $res = json_decode($response);

                LogRequest::log($startTime, $url, 'DELETE', json_encode([]), $res, $httpcode, Controllers\MailinglistController::class, 'deleteList');

                if (isset($res->message) && isset($res->code)) {
                    $errror_message = $res->code.': '.$res->message;

                    return redirect()->back()->with('error', $errror_message);
                }
            }

            Mailinglist::where('remote_id', $id)->delete();

            return redirect()->back()->with('success', 'Removed successfully.');
        }
    }

    public function addRemark(Request $request): JsonResponse
    {
        $remark = $request->input('remark');
        $id = $request->input('id');
        MailingRemark::create([
            'customer_id' => $id,
            'text' => $remark,
            'user_name' => Auth::user()->name,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json(['remark' => $remark], 200);
    }

    public function getBroadCastRemark(Request $request): JsonResponse
    {
        $id = $request->input('id');

        $remark = MailingRemark::where('customer_id', $id)->whereNotNull('text')->get();

        return response()->json($remark, 200);
    }

    public function addManual(Request $request): JsonResponse
    {
        $email = $request->email;
        $id = $request->id;
        $mailinglist = Mailinglist::find($id);
        $website = StoreWebsite::where('id', $mailinglist->website_id)->first();
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : config('env.SEND_IN_BLUE_API');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        $data = [
            'email' => $email,
            'listIds' => [intval($id)],
        ];
        $url = 'https://api.sendinblue.com/v3/contacts';
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'api-key: '.$api_key,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $res = json_decode($response);
        LogRequest::log($startTime, $url, 'POST', json_encode($data), $res, $httpcode, Controllers\MailinglistController::class, 'addManual');

        if (isset($res->message)) {
            if ($res->message == 'Contact already exist') {
                $curl3 = curl_init();
                $url = "https://api.sendinblue.com/v3/contacts/' . $email";
                curl_setopt_array($curl3, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);
                $respw = curl_exec($curl3);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl3);
                $parameters = [];
                $respw = json_decode($respw);

                LogRequest::log($startTime, $url, 'DELETE', json_encode($parameters), $respw, $httpcode, Controllers\MailinglistController::class, 'addManual');

                $curl2 = curl_init();
                $url = 'https://api.sendinblue.com/v3/contacts';
                curl_setopt_array($curl2, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        'api-key: '.$api_key,
                        'Content-Type: application/json',
                    ],
                ]);
                $resp = curl_exec($curl2);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl2);
                $ress = json_decode($resp); //response deocded

                LogRequest::log($startTime, $url, 'POST', json_encode($data), $ress, $httpcode, Controllers\MailinglistController::class, 'addManual');
                if (isset($ress->message)) {
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                ListContact::where('customer_id', $customer->id)->delete();
                $mailinglist->listCustomers()->attach($customer->id);

                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error']);
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * @param  mixed  $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateCustomerSource($id, Request $request): JsonResponse
    {
        $customer = Customer::find($id);
        $customer->source = $request->source;
        $customer->save();

        return response()->json(true);
    }

    public function notifyUrl(Request $request)
    {
        $update = [];
        $id = str_replace('["', '', $request->tag);
        $id = str_replace('"]', '', $id);
        if ($request->event == 'sent') {
            $update = ['sent' => 1];
        } elseif ($request->event == 'delivered') {
            $update = ['delivered' => 1];
        } elseif ($request->event == 'opened') {
            $update = ['opened' => 1];
        } elseif ($request->event == 'blocked' || $request->event == 'unsubscribed' || $request->event == 'spam') {
            $update = ['spam' => 1, 'spam_date' => Carbon::now()->format('Y-m-d H:i:s')];
        }
        if (count($update) > 0) {
            EmailEvent::where(['id' => $id])->update($update);
        }
    }

    public static function sendAutoEmails()
    {
        $mailing_templates = MailinglistTemplate::where('auto_send', 1)->where('duration', '>', 0)->get();
        foreach ($mailing_templates as $mailing_item) {
            $now = Carbon::now();
            if ($mailing_item) {
                if ($mailing_item->duration_in == 'hours') {
                    $customer_created_at = $now->subHours($mailing_item['duration'])->format('Y-m-d H:i:s');
                } else {
                    $customer_created_at = $now->subDays($mailing_item['duration'])->format('Y-m-d H:i:s');
                }
                $spamedListContactIds = EmailEvent::where('spam', 1)->pluck('id')->toArray();

                $mailingLists = MailingList::leftJoin('list_contacts', 'list_contacts.list_id', '=', 'mailinglists.id')
                    ->leftJoin('customers', 'customers.id', '=', 'list_contacts.customer_id')
                    ->where('mailinglists.created_at', '<', Carbon::parse($customer_created_at)->addMinutes(60))
                    ->where('mailinglists.created_at', '>=', $customer_created_at)
                    ->whereNotIn('list_contacts.id', $spamedListContactIds)->whereNotNull('list_contacts.id')
                    ->select('mailinglists.id as mailingListId', 'customers.id as customerId', 'customers.email', 'customers.name', 'list_contacts.id as list_contact_id')->get();
                foreach ($mailingLists as $mailingList) {
                    $service = Service::find($mailingList->service_id)(new Mailinglist)->sendAutoEmails($mailingList, $mailing_item, $service);
                }
            }
        }
    }

    public function getlog(Request $request)
    {
        $paginate = (Setting::get('pagination') * 10);
        $logs = MailinglistIinfluencersDetailLogs::orderByDesc('id')->paginate($paginate);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('marketing.mailinglist.partials.logdata', compact('logs'))->render(),
                'links' => (string) $logs->render(),
                'count' => $logs->total(),
            ], 200);
        }

        return view('marketing.mailinglist.log', compact('logs'));
    }

    public function flowlog(Request $request): View
    {
        $paginate = (Setting::get('pagination') * 10);
        $logs = MailinglistIinfluencersLogs::orderByDesc('id')->paginate($paginate);

        return view('marketing.mailinglist.flowlog', compact('logs'));
    }

    public function customerlog(Request $request): View
    {
        $paginate = (Setting::get('pagination') * 10);
        $customers = Customer::pluck('email', 'id')->toArray();
        $mailists = Mailinglist::pluck('name', 'id')->toArray();
        $logs = MaillistCustomerHistory::orderByDesc('id')->paginate($paginate);

        return view('marketing.mailinglist.customerlog', compact('logs', 'customers', 'mailists'));
    }
}
