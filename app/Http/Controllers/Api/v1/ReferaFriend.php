<?php

namespace App\Http\Controllers\Api\v1;
use App\Jobs\SendEmail;
use App\Email;

use App\Coupon;
use App\Http\Controllers\Controller;
use App\LogReferalCoupon;
use App\Mail\SendReferralMail;
use App\ReferFriend;
use App\ReferralProgram;
use App\StoreWebsite;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReferaFriend extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @SWG\Post(
     *   path="/friend/referral/create",
     *   tags={"Friend"},
     *   summary="create referral friend",
     *   operationId="create-referral-friend",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referrer_first_name' => 'required|max:30',
            'referrer_last_name' => 'max:30',
            'referrer_email' => 'required|email',
            'referrer_phone' => 'max:20',
            'referee_first_name' => 'required|max:30',
            'referee_last_name' => 'max:30',
            'referee_email' => 'required|email',
            'referee_phone' => 'max:20',
            'website' => 'required|exists:store_websites,website',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response('refera.friend.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->input('website'))->first();

        $uuid = md5(Str::random(15));
        if (! $uuid) {
            $message = $this->generate_erp_response('refera.friend.failed', $storeweb->id, $default = 'Referrer does not exist in records', request('lang_code'));

            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $message,
                ],
                404
            );
        }
        $referFriendData = [];
        $referFriendData = $request->all();
        $referFriendData['store_website_id'] = $storeweb->id;
        $referFriendData['status'] = 'initialize';
        $success = ReferFriend::create($referFriendData);
        if (! is_null($success)) {
            $last_insert_id = $success->id;
            $refferal_data['referrer_email'] = $request->input('referrer_email');
            $refferal_data['referee_email'] = $request->input('referee_email');
            $refferal_data['website'] = $request->input('website');
            $refferal_data['uuid'] = $uuid;
            $refferal_data['store_website_id'] = $storeweb->id;
            $refferal_data['refer_log_id'] = $last_insert_id;
            LogReferalCoupon::create([
                'refer_friend_id' => $last_insert_id,
                'log' => 'user_created',
                'message' => 'Save the refer friend data',
            ]);

            return $this->createCoupon($refferal_data);
        }
        $message = $this->generate_erp_response('refera.friend.failed', $storeweb->id, $default = 'Unable to create referral at the moment. Please try later !', request('lang_code'));

        return response()->json(['status' => 'failed', 'message' => $message], 500);
    }

    public function createCoupon($data = null): JsonResponse
    {
        if (! isset($data) || $data == '') {
            $message = $this->generate_erp_response('coupon.failed', $data['store_website_id'], $default = 'Unable to create coupon', request('lang_code'));

            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $message,
                ],
                500
            );
        }
        $referrer_coupon = Str::random(15);
        $referee_coupon = Str::random(15);
        $refferal_program = ReferralProgram::where(['name' => 'signup_referral', 'store_website_id' => $data['store_website_id']])->first();
        if (! $refferal_program) {
            LogReferalCoupon::create([
                'refer_friend_id' => $data['refer_log_id'],
                'log' => 'error',
                'message' => 'Referral Program not found',
            ]);
            ReferFriend::where('id', $data['refer_log_id'])->update(['status' => 'Error: Referral Program Not Found']);
            $message = $this->generate_erp_response('coupon.failed.refferal_program', $data['store_website_id'], $default = 'refferal program for website does not exists !', request('lang_code'));

            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $message,
                ],
                404
            );
        }
        $coupondata = [
            'description' => 'Coupon generated from refer a friend scheme',
            'initial_amount' => $refferal_program->credit,
        ];
        $referrer_coupondata = [];
        $referee_coupondata = [];
        if ($data['referrer_email']) {
            $referrer_coupondata = $coupondata;
            $referrer_coupondata['code'] = $referrer_coupon;
            $referrer_coupondata['start'] = date('y-m-d H:i');
            $referrer_coupondata['expiration'] = null;
            $referrer_coupondata['email'] = $data['referrer_email'];
            $referrer_coupondata['uuid'] = $data['uuid'];
            $referrer_coupondata['currency'] = $refferal_program->currency;
            $referrer_coupondata['coupon_type'] = 'referafriend';
            $referrer_coupondata['status'] = 0;
        }
        if ($data['referee_email']) {
            $referee_coupondata = $coupondata;
            $referee_coupondata['code'] = $referee_coupon;
            $referee_coupondata['start'] = date('y-m-d H:i');
            $referee_coupondata['expiration'] = null;
            $referee_coupondata['email'] = $data['referee_email'];
            $referee_coupondata['currency'] = $refferal_program->currency;
            $referee_coupondata['coupon_type'] = 'referafriend';
            $referee_coupondata['status'] = 1;
        }
        Coupon::create($referrer_coupondata);
        $referreSuccess = Coupon::create($referee_coupondata);
        if ($referreSuccess) {
            LogReferalCoupon::create([
                'refer_friend_id' => $data['refer_log_id'],
                'log' => 'success',
                'message' => 'Coupon created succssfully',
            ]);
            ReferFriend::where('id', $data['refer_log_id'])->update(['status' => 'Success: Coupon Created Successfully']);
            try {
                $referlink = $data['website'].'/register?uuid='.$data['uuid'];
                $mailData['referee_email'] = $data['referee_email'];
                $mailData['referrer_email'] = $data['referrer_email'];
                $mailData['store_website_id'] = $data['store_website_id'];
                $mailData['referlink'] = $referlink;
                $mailData['referee_coupon'] = $referee_coupon;
                $mailData['model_type'] = Coupon::class;
                $mailData['model_id'] = $referreSuccess->id;

                $this->sendMail($mailData);

                LogReferalCoupon::create([
                    'refer_friend_id' => $data['refer_log_id'],
                    'log' => 'success',
                    'message' => 'Mail Send Successfully',
                ]);
                ReferFriend::where('id', $data['refer_log_id'])->update(['status' => 'Success: Coupon Created & Mailed']);
                $message = $this->generate_erp_response('refera.friend.success', 0, $default = 'refferal created successfully', request('lang_code'));

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'referrer_code' => $referrer_coupon,
                    //'referee_code' => $referee_coupon,
                    'referrer_email' => $data['referrer_email'],
                    'referee_email' => $data['referee_email'],
                ], 200);
            } catch (Exception $e) {
                LogReferalCoupon::create([
                    'refer_friend_id' => $data['refer_log_id'],
                    'log' => 'exception',
                    'message' => 'Mail not sent. Error => '.$e->getMessage(),
                ]);
                ReferFriend::where('id', $data['refer_log_id'])->update(['status' => 'Exception: Coupon Created But Mailed Not Sent']);
                $message = $this->generate_erp_response('refera.friend.success', 0, $default = 'refferal created successfully', request('lang_code'));

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'referrer_code' => $referrer_coupon,
                    'referrer_email' => $data['referrer_email'],
                    'referee_email' => $data['referee_email'],
                ], 200);
            }
        } else {
            LogReferalCoupon::create([
                'refer_friend_id' => $data['refer_log_id'],
                'log' => 'error',
                'message' => 'Unable to create a coupon',
            ]);
            ReferFriend::where('id', $data['refer_log_id'])->update(['status' => 'Error: Coupon Not Created']);
            $message = $this->generate_erp_response('coupon.failed', $data['store_website_id'], $default = 'Unable to create coupon', request('lang_code'));

            return response()->json([
                'status' => 'failed',
                'message' => $message,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    public function sendMail($data = null)
    {
        if ($data) {
            $to = $data['referee_email'];
            $storeweb = StoreWebsite::where('id', $data['store_website_id'])->first();
            $data['title'] = ! empty($storeweb->title) ? $storeweb->title : '';
            $data['website'] = ! empty($storeweb->website) ? $storeweb->website : '';

            $emailClass = (new SendReferralMail($data))->build();
            $email = Email::create([
                'model_id' => $data['model_id'],
                'model_type' => $data['model_type'],
                'from' => $emailClass->fromMailer,
                'to' => $to,
                'subject' => $emailClass->subject,
                'message' => $emailClass->render(),
                'template' => 'referr-coupon',
                'additional_data' => '',
                'status' => 'pre-send',
                'store_website_id' => null,
                'is_draft' => 1,
            ]);

            SendEmail::dispatch($email)->onQueue('send_email');
        }

        return true;
    }
}
