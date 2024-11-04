<?php

namespace App\Http\Controllers\Api\v1;
use App\Product;
use App\CustomerBasketProduct;
use App\CustomerBasket;
use App\Customer;

use App\Category;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderProduct;
use App\ProductCancellationPolicie;
use App\StoreWebsite;
use App\StoreWebsiteOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/v1/product/{sku}/price",
     *   tags={"Product"},
     *   summary="Get product price through sku",
     *   operationId="get-product-price-through-sku",
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
     *
     * @param  mixed  $sku
     */
    public function price(Request $request, $sku): JsonResponse
    {
        $product = Product::where('sku', $sku)->first();

        if (empty($request->store_website) && empty($request->country_code)) {
            return response()->json(['code' => 500, 'message' => 'Country Code and Store Website id is required', 'data' => []]);
        }

        if ($product) {
            $price = $product->getPrice($request->store_website, $request->country_code, '', '', '', '', '', '', '', '', $request->order_id, $product->id);
            $price['sub_total'] = $price['total'];
            $price['duty'] = $product->getDuty($request->store_website);
            $extra = ($price['total'] * $price['duty']) / 100;
            $price['total'] = $price['total'] + $extra;

            $return = [
                'original_price' => $price['original_price'],
                'promotion' => $price['promotion'],
                'sub_total' => $price['sub_total'],
                'duty' => $price['duty'],
                'total' => number_format($price['total'], 2, '.', ''),
            ];

            return response()->json(['code' => 200, 'message' => 'Success', 'data' => $return]);
        }

        return response()->json(['code' => 500, 'message' => 'Product not found in records', 'data' => []]);
    }

    public function checkCancellation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required',
            'order_id' => 'required',
            'cancellation_type' => 'required',
            'product_sku' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $cancellationType = $request->cancellation_type;
        if ($request->website) {
            $storeWebsite = StoreWebsite::where('website', 'like', $request->website)->first();
            // $get
            if ($storeWebsite) {
                $getStoreWebsiteOrder = StoreWebsiteOrder::where('website_id', $storeWebsite->id)->where('platform_order_id', $request->order_id)->first();
                $ProductCancellationPolicie = ProductCancellationPolicie::where('store_website_id', $storeWebsite->id)->first();
                if ($getStoreWebsiteOrder) {
                    //check order status
                    $getOrder = Order::with('order_product')->where('id', $getStoreWebsiteOrder->order_id)->first();
                    if ($cancellationType == 'order') {
                        $orderProducts = $getOrder->order_product;
                    } elseif ($cancellationType == 'products') {
                        $skus = explode(',', rtrim($request->product_sku, ','));
                        $orderProducts = OrderProduct::where('order_id', '=', $request->order_id)->whereIn('sku', $skus)->get();
                    } else {
                        $orderProducts = [];
                    }
                    if (count($orderProducts) > 0) {
                        $results = [];
                        foreach ($orderProducts as $orderProduct) {
                            $result_input = $request->input();
                            $result_input['iscanceled'] = true;
                            $result_input['isrefund'] = true;
                            $result_input['isreturn'] = true;
                            $result_input['sku'] = $orderProduct->sku;
                            if ($ProductCancellationPolicie) {
                                $created = new Carbon($orderProduct->created_at);
                                $now = Carbon::now();
                                $difference = ($created->diff($now)->days < 1) ? 0 : $created->diffInDays($now);
                                if ($difference >= $ProductCancellationPolicie->days_cancelation) {
                                    $result_input['iscanceled'] = false;
                                    $result_input['isreturn'] = false;
                                }

                                $created = new Carbon($orderProduct->shipment_date);
                                $now = Carbon::now();
                                $difference = ($created->diff($now)->days < 1) ? 0 : $created->diffInDays($now);
                                if ($difference >= $ProductCancellationPolicie->days_refund) {
                                    $result_input['isrefund'] = false;
                                    $result_input['isreturn'] = false;
                                }
                            }
                            if ($getOrder->order_status_id == 11) {
                                $result_input['iscanceled'] = false;
                                $result_input['isreturn'] = false;
                            }

                            if ($getOrder->shipment_date != '' && ! is_null($getOrder->shipment_date)) {
                                $result_input['iscanceled'] = false;
                            }
                            $results[] = $result_input;
                        }
                        $message = $this->generate_erp_response('order.cancel.success', 0, $default = 'Success', request('lang_code'));

                        return response()->json(['code' => 200, 'message' => $message, 'data' => $results]);
                    } else {
                        $message = $this->generate_erp_response('order.cancel.failed', 0, $default = 'data not found', request('lang_code'));

                        return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
                    }
                } else {
                    $message = $this->generate_erp_response('order.cancel.failed', 0, $default = 'data not found', request('lang_code'));

                    return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
                }
            } else {
                $message = $this->generate_erp_response('order.cancel.failed', 0, $default = 'data not found', request('lang_code'));

                return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
            }
        }
        $message = $this->generate_erp_response('order.cancel.failed.website_missing', 0, $default = 'website is missing.', request('lang_code'));

        return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
    }

    public function checkReturn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required',
            'order_id' => 'required',
            'product_sku' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $result_input = ['has_return_request' => false];
        if ($request->website) {
            $storeWebsite = StoreWebsite::where('website', 'like', $request->website)->first();
            $sku = explode('-', $request->product_sku);
            // $get
            if ($storeWebsite) {
                $returnExchange = StoreWebsiteOrder::where('website_id', $storeWebsite->id)->where('platform_order_id', $request->order_id)
                    ->leftJoin('orders as o', 'o.id', '=', 'store_website_orders.order_id')
                    ->leftJoin('order_products as op', 'op.order_id', '=', 'o.id')
                    ->leftJoin('return_exchange_products as rep', 'rep.order_product_id', '=', 'op.id')
                    ->where('op.sku', $sku[0])
                    ->first();
                $productCanDays = ProductCancellationPolicie::select('days_refund')->where('store_website_id', $storeWebsite->id)->first();
                $categoriesRef = null;
                $productRef = null;

                $order = Order::select('created_at', 'order_return_request')->withTrashed()->where('id', $request->order_id)->first();
                if ($order) {
                    $orderDays = strtotime($order->created_at);
                    $ordercurrent = strtotime(date('Y-m-d H:i:s'));
                    $timeleft = $ordercurrent - $orderDays;
                    $daysPanding = round((($timeleft / 24) / 60) / 60);
                    if ($order->order_return_request == 1) {
                        $result_input = ['has_return_request' => true, 'duration' => $daysPanding];
                    }
                }

                if ($returnExchange || (isset($daysPanding) && isset($productRef) && isset($categoriesRef) && $productRef >= $daysPanding && $categoriesRef >= $daysPanding)) {
                    if (! empty($daysPanding)) {
                        $result_input = ['has_return_request' => true, 'duration' => $daysPanding];
                    } else {
                        $result_input = ['has_return_request' => true];
                    }
                }

                $message = $this->generate_erp_response('order.return-check.success', 0, $default = 'Success', request('lang_code'));

                return response()->json(['code' => 200, 'message' => $message, 'data' => $result_input]);
            } else {
                $message = $this->generate_erp_response('order.return-check.failed', 0, $default = 'data not found', request('lang_code'));

                return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
            }
        }
        $message = $this->generate_erp_response('order.return-check.failed.website_missing', 0, $default = 'website is missing.', request('lang_code'));

        return response()->json(['code' => 500, 'message' => $message, 'data' => []]);
    }

    public function checkCategoryIsEligibility(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        if ($request->category) {
            $eligible_for_return = 'No';
            $duration = 0;
            $categories = Category::select('days_refund')->where('id', '=', $request->category)->first();
            if ($categories) {
                if ($categories->days_refund != null) {
                    $categoriesRef = $categories->days_refund;

                    $eligible_for_return = 'Yes';
                    $duration = $categoriesRef;
                }
            }

            $result_input = ['duration' => $duration, 'eligible_for_return' => $eligible_for_return];

            return response()->json(['code' => 200, 'data' => $result_input]);
        }

        return response()->json(['code' => 500, 'message' => 'Category not found', 'data' => []]);
    }

    public function wishList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|exists:store_websites,website',
            'customer_name' => 'required',
            'customer_email' => 'required',
            'language_code' => 'required',
            'product_sku' => 'required',
            'product_name' => 'required',
            'product_price' => 'required',
            'product_currency' => 'required',
        ]);

        $storeweb = StoreWebsite::where('website', $request->website)->first();
        if ($validator->fails()) {
            $message = $this->generate_erp_response('wishlist.failed.validation', isset($storeweb) ? $storeweb->id : null, $default = 'please check validation errors !', request('lang_code'));

            return response()->json(['status' => '500', 'message' => $message, 'errors' => $validator->errors()], 200);
        }

        $customer = Customer::where('email', $request->customer_email)->where('store_website_id', $storeweb->id)->first();
        $basket = CustomerBasket::where('customer_email', $request->customer_email)->first();
        if (! $basket) {
            $basket = new CustomerBasket;
            $basket->customer_name = $request->customer_name;
            $basket->customer_email = $request->customer_email;
            $basket->store_website_id = $storeweb->id;
            $basket->language_code = $request->language_code;
            $basket->save();
        }

        $sku = explode('-', $request->product_sku);

        $product = Product::where('sku', $sku[0])->select('id', 'sku')->first();

        $basketProduct = CustomerBasketProduct::where('customer_basket_id', $basket->id)->where('product_sku', $sku[0])->first();
        if (! $basketProduct) {
            $basketProduct = new CustomerBasketProduct;
            $basketProduct->customer_basket_id = $basket->id;
            $basketProduct->product_id = $product->id;
            $basketProduct->product_sku = $product->sku;
            $basketProduct->product_name = $request->product_name;
            $basketProduct->product_price = $request->product_price;
            $basketProduct->product_currency = $request->product_currency;
            $basketProduct->save();
        }

        $message = $this->generate_erp_response('wishlist.create.success', isset($storeweb) ? $storeweb->id : null, $default = 'Wishlist created successfully', request('lang_code'));

        return response()->json(['status' => '200', 'message' => $message], 200);
    }

    public function wishListRemove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'website' => 'required|exists:store_websites,website',
            'customer_email' => 'required',
            'product_sku' => 'required',
        ]);

        $storeweb = StoreWebsite::where('website', $request->website)->first();
        if ($validator->fails()) {
            $message = $this->generate_erp_response('wishlist.failed.validation', isset($storeweb) ? $storeweb->id : null, $default = 'please check validation errors !', request('lang_code'));

            return response()->json(['status' => '500', 'message' => $message, 'errors' => $validator->errors()], 200);
        }
        $sku = explode('-', $request->product_sku);

        $basketProduct = CustomerBasketProduct::join('customer_baskets as cb', 'cb.id', 'customer_basket_products.customer_basket_id')
            ->where('cb.customer_email', $request->customer_email)->where('customer_basket_products.product_sku', $sku[0])
            ->where('cb.store_website_id', $storeweb->id)
            ->first();

        if ($basketProduct) {
            $basketProduct->delete();
            $message = $this->generate_erp_response('wishlist.remove.success', isset($storeweb) ? $storeweb->id : null, $default = 'Product removed successfully from wishlist', request('lang_code'));

            return response()->json(['status' => '200', 'message' => $message], 200);
        }

        $message = $this->generate_erp_response('wishlist.remove.no_product', isset($storeweb) ? $storeweb->id : null, $default = 'Sorry there is no product available in wishlist', request('lang_code'));

        return response()->json(['status' => '500', 'message' => $message], 200);
    }
}
