<?php

namespace App\Http\Controllers\Api\v1;

use App\Customer;
use App\ErpLeads;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderStatus;
use App\OutOfStockSubscribe;
use App\Product;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OutOfStockSubscribeController extends Controller
{
    public function Subscribe(Request $request): JsonResponse
    {
        $params = request()->all();
        // validate incoming request
        $validator = Validator::make($params, [
            'email' => 'required',
            'sku' => 'required',
            'website' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 500, 'data' => $validator->messages(), 'message' => 'Failed']);
        }
        $website = str_replace('www.', '', $request->website);
        $website = str_replace('WWW.', '', $website);

        $storeWebsite = StoreWebsite::where(function ($q) use ($website) {
            $q->where('website', 'like', $website)
                ->orWhere('website', 'like', 'www.'.$website);
        })->select('id')->first();

        if ($storeWebsite) {
            $website_id = $storeWebsite->id;
            $data = $params;
            $sku = explode('-', $request->get('sku'));
            $product = Product::where('sku', $sku[0])->select('id')->first();
            if ($product) {
                $customer = Customer::where('email', $data['email'])->where('store_website_id', $website_id)->first();
                if ($customer == null) {
                    $customer = Customer::create(['name' => $data['email'], 'email' => $data['email'], 'store_website_id' => $website_id]);
                }
                $status = 0;

                ErpLeads::create(['customer_id' => $customer->id, 'store_website_id' => $website_id, 'lead_status_id' => 1, 'product_id' => $product->id, 'type' => 'out-of-stock-subscribe', 'size' => $request->get('size', null)]);

                $arrayToStore = ['customer_id' => $customer['id'], 'product_id' => $product->id, 'status' => $status, 'website_id' => $website_id];
                OutOfStockSubscribe::updateOrCreate(['customer_id' => $customer['id'], 'product_id' => $product->id, 'website_id' => $website_id], $arrayToStore);

                $message = $this->generate_erp_response('erp.lead.created.for.subscribed.success', $website_id, $default = 'Subscribed successfully.', request('lang_code'));

                return response()->json(['code' => 'success', 'message' => $message]);
            } else {
                $message = $this->generate_erp_response('erp.lead.product.not.found.for.subscribed', 0, $default = 'Product not found in records', request('lang_code'));

                return response()->json(['code' => 500, 'data' => [], 'message' => $message]);
            }
        } else {
            $message = $this->generate_erp_response('erp.lead.failed.validation.for.subscribed', 0, $default = 'Website not found in records', request('lang_code'));

            return response()->json(['code' => 500, 'data' => [], 'message' => $message]);
        }
    }

    public function getOrderState(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attributes' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        Log::info(json_encode($request->all()));
        if ($request->token == 'PEtXGYMNqMD6Px3FKsbmgzMZrUnBtGq') {
            if ($request->isMethod('get')) {
                return $request->challenge;
            }
        }

        $attributes = $request->get('attributes');

        if ($attributes['action'] == 'track-order') {
            $order_data = Order::where('order_id', $attributes['trackingNumber'])->first();
            if ($order_data) {
                $status = OrderStatus::where('id', $order_data->order_status_id)->first();
                $statusMessage = $order_data->order_status;
                if ($status) {
                    $statusMessage = $status->status;
                }
                $response = [
                    'responses' => [
                        [
                            'type' => 'text',
                            'delay' => 1000,
                            'message' => 'Thanks for contacting us Your order is right now on '.ucwords($statusMessage),
                        ],
                    ],
                ];
            } else {
                $response = [
                    'responses' => [
                        [
                            'type' => 'text',
                            'delay' => 1000,
                            'message' => 'Sorry, We could not found your order number in our system please contact administrator',
                        ],
                    ],
                ];
            }

            return response()->json($response);
        }
    }
}
