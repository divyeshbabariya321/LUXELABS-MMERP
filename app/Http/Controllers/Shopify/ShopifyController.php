<?php

namespace App\Http\Controllers\Shopify;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\StoreWebsite;
use App\ShopifyHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @author Sukwhinder Singh
 */
class ShopifyController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * @SWG\Post(
     *   path="/shopify/order/create",
     *   tags={"Shopify"},
     *   summary="Create Shopify Order",
     *   operationId="shopify-create-order",
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
     * Get a webhook event and create orders out of it
     */
    public function setShopifyOrders(Request $request): JsonResponse
    {
        $store_id = $request->query('store_website_id');

        if (! $store_id) {
            return response()->json(['error' => 'Store website id missing'], 400);
        }

        // Validate the webhook request and authenticity
        // https://shopify.dev/tutorials/manage-webhooks#verifying-webhooks
        // Get the secret key from store_websites
        $shopify_secret = StoreWebsite::find($store_id)->api_token;
        $hmac_header    = $request->header('x-shopify-hmac-sha256');

        if (! ShopifyHelper::validateShopifyWebhook($request->getContent(), $shopify_secret, $hmac_header)) {
            // Log into general log channel
            Log::channel('customer')->debug('Order webhook failed ');

            return response()->json(['error' => 'Couldnot verify webhook'], 400);
        }

        $order = $request->all();
        ShopifyHelper::syncShopifyOrders($store_id, $order);

        return response()->json(['success'], 200);
    }

    /**
     * @SWG\Post(
     *   path="/shopify/customer/create",
     *   tags={"Shopify"},
     *   summary="Shopify create customer",
     *   operationId="shopify-create-customer",
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
     * Get a webhook event and create customers out of it
     */
    public function setShopifyCustomers(Request $request): JsonResponse
    {
        $store_id = $request->query('store_website_id');

        if (! $store_id) {
            return response()->json(['error' => 'Store website id missing'], 400);
        }

        // Validate the webhook request and authenticity
        // https://shopify.dev/tutorials/manage-webhooks#verifying-webhooks
        // Get the secret key from store_websites
        $shopify_secret = StoreWebsite::find($store_id)->api_token;
        $hmac_header    = $request->header('x-shopify-hmac-sha256');

        if (! ShopifyHelper::validateShopifyWebhook($request->getContent(), $shopify_secret, $hmac_header)) {
            Log::channel('customer')->debug('Customer webhook failed ');

            return response()->json(['error' => 'Couldnot verify webhook'], 400);
        }

        $customer = $request->all();
        ShopifyHelper::syncShopifyCustomers($store_id, $customer);

        return response()->json(['success'], 200);
    }
}
