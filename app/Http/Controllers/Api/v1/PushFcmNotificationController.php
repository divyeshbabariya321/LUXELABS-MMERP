<?php

namespace App\Http\Controllers\Api\v1;

use App\FcmToken;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushFcmNotificationController extends Controller
{
    /**
     * @SWG\Post(
     *   path="/notification/create",
     *   tags={"Notification"},
     *   summary="Create notification",
     *   operationId="create-notification",
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
    public function create(request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'website' => 'required|exists:store_websites,website',
            'device_id' => 'required',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response('notification.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();
        $token_data = [
            'token' => $request->token,
            'store_website_id' => $storeweb->id,
        ];
        $insert = FcmToken::updateOrCreate(
            [
                'store_website_id' => $storeweb->id,
                'device_id' => $request->device_id,
            ],
            $token_data
        );
        if (! $insert) {
            $message = $this->generate_erp_response('notification.failed', $storeweb->id, $default = 'Unable to create notification !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message], 500);
        }
        $message = $this->generate_erp_response('notification.success', $storeweb->id, $default = 'Notification created successfully !', request('lang_code'));

        return response()->json(['status' => 'success', 'message' => $message], 200);
    }

    public function updateLang(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'required|string',
            'website' => 'required|exists:store_websites,website',
            'device_id' => 'required|exists:fcm_tokens,device_id',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response('notification.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $storeweb = StoreWebsite::where('website', $request->website)->first();

        $fcmToken = FcmToken::where('device_id', $request->device_id)->where('store_website_id', $storeweb->id)->first();
        if (! empty($fcmToken)) {
            $fcmToken->lang = $request->lang;
            $fcmToken->save();

            $message = $this->generate_erp_response('notification.success', $storeweb->id, $default = 'Notification language updated successfully !', request('lang_code'));

            return response()->json(['status' => 'success', 'message' => $message], 200);
        } else {
            $message = $this->generate_erp_response('notification.failed', $storeweb->id, $default = 'Record not found!', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message], 500);
        }
    }
}
