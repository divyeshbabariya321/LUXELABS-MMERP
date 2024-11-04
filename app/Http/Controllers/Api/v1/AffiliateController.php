<?php

namespace App\Http\Controllers\Api\v1;

use App\Affiliates;
use App\Http\Controllers\Controller;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
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
     *   path="/affiliate/add",
     *   tags={"Affiliate"},
     *   summary="store affiliate",
     *   operationId="store-affiliate",
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
     * @SWG\Post(
     *   path="/influencer/add",
     *   tags={"Influencer"},
     *   summary="store influencer",
     *   operationId="store-influencer",
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
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|exists:store_websites,website',
        ]);

        $type = $request->get('type', 'affiliate');

        $storeweb = StoreWebsite::where('website', $request->website)->first();
        if ($validator->fails()) {
            $message = $this->generate_erp_response("$type.failed.validation", isset($storeweb) ? $storeweb->id : null, $default = 'please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $affiliates = new Affiliates;
        $affiliates->fill([
            'store_website_id' => optional($storeweb)->id,
            'first_name' => data_get($request, 'first_name', ''),
            'last_name' => data_get($request, 'last_name', ''),
            'phone' => data_get($request, 'phone', ''),
            'emailaddress' => data_get($request, 'email', ''),
            'website_name' => data_get($request, 'website_name', ''),
            'url' => data_get($request, 'url', ''),
            'unique_visitors_per_month' => data_get($request, 'unique_visitors_per_month', ''),
            'page_views_per_month' => data_get($request, 'page_views_per_month', ''),
            'address' => data_get($request, 'street_address', ''),
            'city' => data_get($request, 'city', ''),
            'postcode' => data_get($request, 'postcode', ''),
            'country' => data_get($request, 'country', ''),
            'location' => data_get($request, 'location', ''),
            'title' => data_get($request, 'title', ''),
            'caption' => data_get($request, 'caption', ''),
            'posted_at' => data_get($request, 'posted_at', ''),
            'facebook' => data_get($request, 'facebook', ''),
            'facebook_followers' => data_get($request, 'facebook_followers', ''),
            'instagram' => data_get($request, 'instagram', ''),
            'instagram_followers' => data_get($request, 'instagram_followers', ''),
            'twitter' => data_get($request, 'twitter', ''),
            'twitter_followers' => data_get($request, 'twitter_followers', ''),
            'youtube' => data_get($request, 'youtube', ''),
            'youtube_followers' => data_get($request, 'youtube_followers', ''),
            'linkedin' => data_get($request, 'linkedin', ''),
            'linkedin_followers' => data_get($request, 'linkedin_followers', ''),
            'pinterest' => data_get($request, 'pinterest', ''),
            'pinterest_followers' => data_get($request, 'pinterest_followers', ''),
            'worked_on' => data_get($request, 'worked_on'),
            'type' => $type,
            'source' => data_get($request, 'source', ''),
        ]);

        if ($affiliates->save()) {
            $message = $this->generate_erp_response("$type.success", ($storeweb) ? $storeweb->id : null, $default = ucwords($affiliates->type).' added successfully !', request('lang_code'));

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 200);
        }
        $message = $this->generate_erp_response("$type.failed", ($storeweb) ? $storeweb->id : null, $default = 'Unable to add '.ucwords($affiliates->type).'!', request('lang_code'));

        return response()->json([
            'status' => 'failed',
            'message' => $message,
        ], 500);
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
     */
    public function edit(int $id): JsonResponse
    {
        //
        $affiliates = Affiliates::find($id);

        return response()->json(['code' => 200, 'data' => $affiliates]);
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
}
