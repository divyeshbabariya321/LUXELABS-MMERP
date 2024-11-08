<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Helpers\InstantMessagingHelper;
use App\Helpers\MagentoOrderHandleHelper;
use App\MagentoCustomerReference;
use App\Setting;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagentoCustomerReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {}

    /**
     * Create magento order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $bodyContent = $request->getContent();
        $order = json_decode($bodyContent);
        $lang_code = $order->lang_code ?? null;
        if (empty($bodyContent)) {
            $message = $this->generate_erp_response('magento.order.failed.validation', 0, 'Invalid data', $lang_code);

            return response()->json([
                'status' => false,
                'message' => $message,
            ]);
        }
        $order = json_decode($bodyContent);

        $newArray = [];
        $newArray['items'][] = $order;
        $order = json_decode(json_encode($newArray));

        $website = StoreWebsite::where('website', $order->items[0]->website)->first();
        $orderCreate = MagentoOrderHandleHelper::createOrder($order, $website);
        if ($orderCreate == true) {
            $message = $this->generate_erp_response('magento.order.success', 0, 'Order create successfully', $lang_code);

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        $message = $this->generate_erp_response('magento.order.failed', 0, 'Something went wrong, Please try again', $lang_code);

        return response()->json([
            'status' => false,
            'message' => $message,
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/magento/customer-reference",
     *   tags={"Magento"},
     *   summary="store magento customer reference",
     *   operationId="store-magento-customer-reference",
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
        if (empty($request->name)) {
            $message = $this->generate_erp_response('customer_reference.403', 0, 'Name is required', request('lang_code'));

            return response()->json(['message' => $message], 403);
        }

        if (empty($request->email)) {
            $message = $this->generate_erp_response('customer_reference.403', 0, 'Email is required', request('lang_code'));

            return response()->json(['message' => $message], 403);
        }

        if (empty($request->website)) {
            $message = $this->generate_erp_response('customer_reference.403', 0, 'website is required', request('lang_code'));

            return response()->json(['message' => $message], 403);
        }

        if (empty($request->platform_id)) {
            $message = $this->generate_erp_response('customer_reference.403', 0, 'Platform id is required', request('lang_code'));

            return response()->json(['message' => $message], 403);
        }

        $name = $request->name;
        $email = $request->email;
        $website = $request->website;
        $phone = null;
        $dob = null;
        $store_website_id = null;
        $platform_id = null;
        $wedding_anniversery = null;
        if ($request->phone) {
            $phone = $request->phone;
        }
        if ($request->dob) {
            $dob = $request->dob;
        }
        if ($request->wedding_anniversery) {
            $wedding_anniversery = $request->wedding_anniversery;
        }

        //getting reference

        $store_website = StoreWebsite::where('website', 'like', $website)->first();
        if ($store_website) {
            $store_website_id = $store_website->id;
        }
        if ($request->platform_id) {
            $platform_id = $request->platform_id;
        }

        $reference = Customer::where('email', $email)->where('store_website_id', $store_website_id)->first();
        if (empty($reference)) {
            $reference = new Customer;
            $reference->name = $name;
            $reference->phone = $phone;
            $reference->email = $email;
            $reference->store_website_id = $store_website_id;
            $reference->platform_id = $platform_id;
            $reference->dob = $dob;
            $reference->wedding_anniversery = $wedding_anniversery;
            $reference->save();

            if ($reference->phone) {
                //get welcome message
                $welcomeMessage = InstantMessagingHelper::replaceTags($reference, Setting::get('welcome_message'));
                //sending message
                app(WhatsAppController::class)->sendWithThirdApi($reference->phone, '', $welcomeMessage, '', '');
            }
        } else {
            $reference->name = $name;
            $reference->phone = $phone;
            $reference->email = $email;
            $reference->store_website_id = $store_website_id;
            $reference->platform_id = $platform_id;
            $reference->dob = $dob;
            $reference->wedding_anniversery = $wedding_anniversery;
            $reference->save();
        }

        $this->generate_erp_response('customer_reference.success', $store_website_id, 'Saved successfully !', request('lang_code'));

        return response()->json(['message' => 'Saved SucessFully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }
}
