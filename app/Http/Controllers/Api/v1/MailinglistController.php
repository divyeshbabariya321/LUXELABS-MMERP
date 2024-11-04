<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Api\v1;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Language;
use App\LogRequest;
use App\Mailinglist;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ListContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MailinglistController extends Controller
{
    /**
     * @create_customer
     *
     * @author Hitesh
     *
     * @param  mixed  $email
     * @param  mixed  $store_website_id
     * @param  null|mixed  $storeName
     * @param  null|mixed  $language
     */
    public function create_customer($email, $store_website_id, $storeName = null, $language = null)
    {
        $customer = new Customer;

        if (! empty($language)) {
            $language = explode('_', $language);
            $language = end($language);
            if (! empty($language)) {
                $customer->language = $language;
            }
        }

        $customer->email = $email;
        $customer->store_website_id = $store_website_id;
        $customer->store_name = $storeName;
        $customer->save();

        return $customer;
    }

    public function get_customer($email, $store_website_id)
    {
        $customer = Customer::where('email', $email)->where('store_website_id', $store_website_id)->first();

        return $customer;
    }

    /**
     * @SWG\Post(
     *   path="/mailinglist/add",
     *   tags={"Mailing List"} ,
     *   summary="Add customer to mailing list",
     *   operationId="mail-list",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="email",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * @SWG\Parameter(
     *          name="website",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * @function add
     *Step1) get store website from request
     *Step2) if store website not present send return message
     *Step3) if it's present get cutomer using store_website_Id and email id
     *        if customer is not present create new customer
     *Step4) Now using store_website_id get mailing list
     *Step5) loop on all mailing list and call to addToList method
     **/
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'website' => 'required',
            'store_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        // Step1
        $store_website = StoreWebsite::Where('website', $request->website)->first();
        // Step 2
        if (! $store_website) {
            $message = $this->generate_erp_response('newsletter.failed', 0, $default = 'Store website not found', request('lang_code'));

            return response()->json(['code' => 200, 'message' => $message]);
        }
        // Step 3
        $customer = $this->get_customer($request->get('email'), $store_website->id);

        if ($customer && $customer->newsletter == 1 && $customer->store_website_id == $store_website->id) {
            $message = $this->generate_erp_response('newsletter.failed.already_subscribed', $store_website->id, $default = 'You have already subscibed newsletter', request('lang_code'));

            return response()->json(['code' => 500, 'message' => $message]);
        }

        if (! $customer) {
            $customer = $this->create_customer($request->get('email'), $store_website->id, $request->get('store_name', null), $request->lang_code);
        }

        // Step4
        $language = explode('_', $request->lang_code);
        $language = end($language);

        $languageId = Language::where('locale', $language)->pluck('id')->first();
        $mailinglist = Mailinglist::where('website_id', $store_website->id)
            ->where('language', $languageId)->get();

        // Step5
        foreach ($mailinglist as $key => $m) {
            $this->addToList($m->remote_id, $request->get('email'), $store_website->send_in_blue_api);
        }

        $customer->newsletter = 1;
        $customer->save();

        $message = $this->generate_erp_response('newsletter.success', $store_website->id, $default = 'Successfully added', request('lang_code'));

        return response()->json(['code' => 200, 'message' => $message]);
    }

    /**
     * @param  mixed  $id
     * @param  mixed  $email
     * @param  mixed  $send_in_blue_api
     */
    public function addToList($id, $email, $send_in_blue_api): JsonResponse
    {
        $api_key = ($send_in_blue_api != '') ? $send_in_blue_api : config('env.SEND_IN_BLUE_API');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $curl = curl_init();
        $url = 'https://api.sendinblue.com/v3/contacts/';
        $data = [
            'email' => $email,
            'listIds' => [intval($id)],
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
        $res = json_decode($response);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $parameters = [];
        curl_close($curl);

        LogRequest::log($startTime, $url, 'GET', json_encode($parameters), $res, $httpcode, self::class, 'addToList');

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
                $respw = json_decode($respw);
                $httpcode = curl_getinfo($curl3, CURLINFO_HTTP_CODE);
                $parameters = [];
                LogRequest::log($startTime, $url, 'DELETE', json_encode($parameters), $respw, $httpcode, self::class, 'addToList');
                curl_close($curl3);

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
                $ress = json_decode($resp);
                $parameters = [];
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode($parameters), $ress, $httpcode, self::class, 'addToList');
                curl_close($curl2);

                if (isset($ress->message)) {
                    return response()->json(['status' => 'error']);
                }
                $customer = Customer::where('email', $email)->first();
                $mailinglist = Mailinglist::find($id);
                ListContact::where('customer_id', $customer->id)->delete();
                if (! empty($mailinglist)) {
                    $mailinglist->listCustomers()->attach($customer->id);
                }

                return response()->json(['status' => 'success']);
            }
        } else {
            $customer = Customer::where('email', $email)->first();
            $mailinglist = Mailinglist::find($id);
            $mailinglist->listCustomers()->attach($customer->id);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Opps! Something went wrong, Please try again.'], 400);
    }
}
