<?php

namespace App\Http\Controllers;

use App\Brand;
use App\ErpLeads;
use App\LeadProductPriceCountLogs;
use App\Order;
use App\ProductPriceDiscountLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $term = $request->input('term');
        $orderOrLead = $request->input('order_or_lead') ?? '';
        $brandList = Brand::all()->pluck('name', 'id')->toArray();
        $brandIds = $request->get('brand_id');

        if ($orderOrLead != 'lead') {
            $Order = Order::select('orders.id', 'orders.customer_id', 'order_products.product_id', 'order_date', 'products.name', 'brands.name as brand_name', 'products.price_inr', 'products.price_inr_discounted', 'customers.name as customer_name', 'brands.id as brand_id')
                ->join('order_products', 'order_products.order_id', '=', 'orders.id')
                ->join('products', 'order_products.product_id', '=', 'products.id')
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->join('brands', 'products.brand', '=', 'brands.id')->with('storeWebsiteOrder')->with('storeWebsiteOrder.storeWebsiteProductPrice');
            if (empty($term)) {
                $orders = $Order;
            } else {
                $orders = $Order->orWhere('orders.id', '=', $term)
                    ->orWhere('products.id', '=', $term)
                    ->orWhere('products.name', 'like', '%'.$term.'%')
                    ->orWhere('brands.id', '=', $brandIds)
                    ->orWhere('customers.name', 'like', '%'.$term.'%');
            }
        }

        $leads = null;

        if ($orderOrLead != 'order') {
            $leads = ErpLeads::select('erp_leads.id', 'erp_leads.customer_id', 'erp_leads.store_website_id', 'erp_leads.product_id', 'erp_leads.created_at as order_date', 'products.name', 'brands.name as brand_name', 'products.price_inr', 'products.price_inr_discounted', 'customers.name as customer_name', 'brands.id as brand_id')
                ->join('products', 'erp_leads.product_id', '=', 'products.id')
                ->leftJoin('customers', 'erp_leads.customer_id', '=', 'customers.id')
                ->join('brands', 'erp_leads.brand_id', '=', 'brands.id')->with('storeWebsite')->with('storeWebsite.storeWebsiteProductPrice');

            if (empty($term)) {
                $orders = $leads;
            } else {
                $orders = $leads->orWhere('erp_leads.id', '=', $term)
                    ->orWhere('erp_leads.product_id', '=', $term)
                    ->orWhere('products.name', 'like', '%'.$term.'%')
                    ->orWhere('customers.name', 'like', '%'.$term.'%');
            }
        }

        if ($orderOrLead != 'order') {
            if (! empty($brandIds)) {
                $orders = $orders->orWhere('brand_id', $brandIds);
            }
        }

        $orders = $orders->orderByDesc('id')->simplePaginate(20);

        $leadOrder_array = $orders;
        if ($request->ajax()) {
            return view('lead-order.lead-order-item', compact('leadOrder_array', 'leads', 'brandList', 'term', 'orderOrLead'))
                ->with('i', (request()->input('page', 1) - 1) * 10);
        }

        return view('lead-order.index', compact('leadOrder_array', 'leads', 'brandList', 'term', 'orderOrLead'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * This funcrtion is use for get product suggested log
     */
    public function leadProductPriceLog(Request $request): JsonResponse
    {
        try {
            $prodPriceDis = ProductPriceDiscountLog::where(['product_id' => $request->prod_id, 'customer_id' => $request->cust_id])->get();
            if ($prodPriceDis->toArray()) {
                $html = '';
                foreach ($prodPriceDis as $prodDisData) {
                    $html .= '<tr style="height:32px;width:100%;">';
                    $html .= '<td with="5%">'.$prodDisData->id.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->order_id.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->product_id.'</td>';
                    $html .= '<td with="20%"><span class="expand-text" style="float:left;height:24px;overflow:hidden;cursor: pointer;">'.$prodDisData->stage.'</span></td>';
                    $html .= '<td with="5%">'.$prodDisData->product_price.'</td>';
                    $html .= '<td with="5%">'.$prodDisData->product_total_price.'</td>';
                    $html .= '<td with="5%">'.$prodDisData->product_discount.'</td>';
                    $html .= '<td with="35%"><span class="expand-text" style="float:left;height:24px;overflow:hidden;cursor: pointer;">'.$prodDisData->log.'</span></td>';
                    $html .= '</tr>';
                }

                return response()->json(['code' => 200, 'data' => $html, 'message' => 'Log listed Successfully']);
            }

            return response()->json(['code' => 500, 'message' => 'Log not found']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * This funcrtion is use for get Lead product Cal log
     */
    public function leadProductPriceCalLog(Request $request): JsonResponse
    {
        try {
            $prodPriceDis = LeadProductPriceCountLogs::where(['product_id' => $request->prod_id, 'customer_id' => $request->cust_id])->get();
            if ($prodPriceDis->toArray()) {
                $html = '';
                foreach ($prodPriceDis as $prodDisData) {
                    $html .= '<tr style="height:32px;width:100%;">';
                    $html .= '<td with="5%">'.$prodDisData->id.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->product_id.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->original_price.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->promotion_per.'%</td>';
                    $html .= '<td with="10%">'.$prodDisData->promotion.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->segment_discount.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->segment_discount_per.'%</td>';
                    $html .= '<td with="10%">'.$prodDisData->total_price.'</td>';
                    $html .= '<td with="10%">'.$prodDisData->euro_to_inr_price.'</td>';
                    $html .= '</tr>';
                }

                return response()->json(['code' => 200, 'data' => $html, 'message' => 'Calculation Log listed Successfully']);
            }

            return response()->json(['code' => 500, 'message' => 'Calculation Log not found']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
}
