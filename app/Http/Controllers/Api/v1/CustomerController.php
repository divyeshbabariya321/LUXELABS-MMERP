<?php

namespace App\Http\Controllers\Api\v1;
use App\StoreWebsite;
use App\Email;
use App\CustomerReview;

use App\Customer;
use App\ErpLeads;
use App\Flow;
use App\Http\Controllers\Controller;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function add_cart_data(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'website' => 'required',
            'name' => 'required',
            'lang_code' => 'required',
            'item_info' => 'required|array',
            'item_info.*.sku' => 'required',
            'item_info.*.qty' => 'required',
        ]);
        if ($validator->fails()) {
            $message = $this->generate_erp_response('customercart.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }

        $responseData = [];

        $storeWebsite = StoreWebsite::where('website', 'like', $request->website)->first();

        if ($storeWebsite) {
            $checkCustomer = Customer::where('email', $request->email)
                ->where('store_website_id', $storeWebsite->id)
                ->first();
            if (! $checkCustomer) {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'store_website_id' => $storeWebsite->id,
                ];
                $checkCustomer = Customer::create($data);
            }

            $customer_id = $checkCustomer->id;
            foreach ($request->item_info as $item) {
                $skuarr = explode('-', $item['sku']);
                $product = Product::where('sku', $skuarr[0])->first();
                if ($product) {
                    $erp_lead = new ErpLeads;
                    $erp_lead->lead_status_id = 1;
                    $erp_lead->customer_id = $customer_id;
                    $erp_lead->product_id = $product->id;
                    $erp_lead->store_website_id = $storeWebsite->id;
                    $erp_lead->category_id = $product->category;
                    $erp_lead->brand_id = $product->brand;
                    $erp_lead->brand_segment = $product->brands ? $product->brands->brand_segment : null;
                    $erp_lead->color = $checkCustomer->color;
                    $erp_lead->size = $checkCustomer->size;
                    $erp_lead->gender = $checkCustomer->gender;
                    $erp_lead->qty = $item['qty'];
                    $erp_lead->type = $request->get('type', 'add-to-cart');
                    $erp_lead->min_price = $product->price;
                    $erp_lead->max_price = $product->price;
                    $erp_lead->save();
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Successfully Added'], 200);
    }

    public function storeReviews(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'website' => 'required',
            'name' => 'required',
            'lang_code' => 'required',
            'platform_id' => 'required',
            'stars' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $this->generate_erp_response('reviews.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }

        $responseData = [];

        $storeWebsite = StoreWebsite::where('website', 'like', $request->website)->first();

        if ($storeWebsite) {
            $checkCustomer = Customer::where('email', $request->email)
                ->where('store_website_id', $storeWebsite->id)
                ->first();
            if (! $checkCustomer) {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'store_website_id' => $storeWebsite->id,
                ];
                $checkCustomer = Customer::create($data);
            }

            $input['email'] = $checkCustomer->email;
            $input['name'] = $checkCustomer->name;
            $input['store_website_id'] = $storeWebsite->id;
            $input['platform_id'] = $request->platform_id;
            $input['stars'] = $request->stars;
            $input['comment'] = $request->comment;
            $input['status'] = 0;
            $reviews = CustomerReview::create($input);
            $flowId = Flow::where('flow_name', 'order_reviews')->pluck('id')->first();
            if ($flowId != null and $checkCustomer->email != null) {
                Email::where('scheduled_at', '>=', Carbon::now())->where('email', $checkCustomer->email)
                    ->where('template', 'flow#'.$flowId)->delete();
            }

            if ($reviews) {
                return response()->json(['status' => 'success', 'message' => 'Successfully Added'], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Please try again'], 400);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Store website not found!'], 400);
    }

    public function allReviews(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'website' => 'required',
            'lang_code' => 'required',
            'platform_id' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $this->generate_erp_response('reviews.failed.validation', 0, $default = 'Please check validation errors !', request('lang_code'));

            return response()->json(['status' => 'failed', 'message' => $message, 'errors' => $validator->errors()], 400);
        }
        $reviews = CustomerReview::with('storeWebsite')->latest()->get();
        if ($reviews) {
            return response()->json(['status' => 'success', 'data' => $reviews, 'message' => 'All reviews fetched successfully'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Please try again'], 400);
        }
    }
}
