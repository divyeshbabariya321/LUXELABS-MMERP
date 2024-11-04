<?php

namespace App\Http\Controllers;
use App\Product;
use App\Helpers\OrderHelper;
use App\ErpLeads;

use Illuminate\Support\Facades\DB;
use App\User;
use App\Agent;
use App\Leads;
use App\Order;
use App\Vendor;
use App\Contact;
use App\Customer;
use App\Dubbizle;
use App\Supplier;
use Carbon\Carbon;

class FindByNumberController
{
    protected function findLeadByNumber($number)
    {
        return Leads::where('contactno', '=', $number)->first();
    }

    protected function findDubbizleByNumber($number)
    {
        return Dubbizle::where('phone_number', $number)->first();
    }

    protected function findCustomerByNumber($number, $storeId = null)
    {
        return Customer::where('phone', '=', $number)->where('store_website_id', $storeId)->first();
    }

    protected function findOrderByNumber($number)
    {
        return Order::where('contact_detail', '=', $number)->first();
    }

    protected function findSupplierByNumber($number)
    {
        if ($agent = Agent::where('phone', $number)->first()) {
            if ($agent->purchase && $agent->purchase->purchase_supplier) {
                return $agent->purchase->purchase_supplier;
            }

            if (preg_match('/supplier/i', $agent->model_type)) {
                return Supplier::find($agent->model_id);
            }
        }

        return Supplier::where('phone', $number)->first();
    }

    protected function findVendorByNumber($number)
    {
        if ($agent = Agent::where('phone', $number)->where('model_type', 'LIKE', '%Vendor%')->first()) {
            if (preg_match('/vendor/i', $agent->model_type)) {
                return Vendor::find($agent->model_id);
            }
        }

        return Vendor::where('phone', $number)->first();
    }

    protected function findUserByNumber($number)
    {
        return User::where('phone', '=', $number)->first();
    }

    protected function findContactByNumber($number)
    {
        return Contact::where('phone', '=', $number)->first();
    }

    protected function findLeadOrOrderByNumber($number)
    {
        $lead = $this->findLeadByNumber($number);
        if ($lead) {
            return ['leads', $lead];
        }
        $order = $this->findOrderByNumber($number);
        if ($order) {
            return ['orders', $order];
        }

        return [false, false];
    }

    protected function findCustomerOrLeadOrOrderByNumber($number, $storeId = null)
    {
        $customer = $this->findCustomerByNumber($number, $storeId);
        if ($customer) {
            return ['customers', $customer];
        }
        $lead = $this->findLeadByNumber($number);
        if ($lead) {
            return ['leads', $lead];
        }
        $order = $this->findOrderByNumber($number);
        if ($order) {
            return ['orders', $order];
        }

        return [false, false];
    }

    protected function findCustomerAndRelationsByNumber($number, $storeId = null)
    {
        $customer = $this->findCustomerByNumber($number, $storeId);
        if ($customer) {
            $orders = (new Order())->newQuery()->with('customer')->leftJoin('store_website_orders as swo', 'swo.order_id', 'orders.id')
                ->leftJoin('order_products as op', 'op.order_id', 'orders.id')
                ->leftJoin('products as p', 'p.id', 'op.product_id')
                ->leftJoin('brands as b', 'b.id', 'p.brand')->groupBy('orders.id')
                ->where('customer_id', $customer->id)
                ->select(['orders.*', DB::raw('group_concat(b.name) as brand_name_list'), 'swo.website_id'])->orderByDesc('created_at')->get();
            [$leads_total, $leads] = $this->getLeadsInformation($customer->id);
            $exchanges_return      = $customer->return_exchanges;
            if ($orders->count()) {
                foreach ($orders as &$value) {
                    $value->storeWebsite    = $value->storeWebsiteOrder ? ($value->storeWebsiteOrder->storeWebsite ?? 'N/A') : 'N/A';
                    $value->order_date      = Carbon::parse($value->order_date)->format('d-m-y');
                    $totalBrands            = explode(',', $value->brand_name_list);
                    $value->brand_name_list = (count($totalBrands) > 1) ? 'Multi' : $value->brand_name_list;
                    $value->status          = OrderHelper::getStatusNameById($value->order_status_id);
                }
            }

            return [
                true,
                [
                    'orders_total'           => $orders->count(),
                    'leads_total'            => $leads_total,
                    'exchanges_return_total' => $exchanges_return->count(),
                    'exchanges_return'       => $exchanges_return,
                    'leads'                  => $leads,
                    'orders'                 => $orders,
                    'customer'               => $customer,
                ],
            ];
        }

        return [false, false];
    }

    private function getLeadsInformation($id)
    {
        $source = ErpLeads::leftJoin('products', 'products.id', '=', 'erp_leads.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_leads.customer_id')
            ->leftJoin('erp_lead_status as els', 'els.id', 'erp_leads.lead_status_id')
            ->leftJoin('categories as cat', 'cat.id', 'erp_leads.category_id')
            ->leftJoin('brands as br', 'br.id', 'erp_leads.brand_id')
            ->where('erp_leads.customer_id', $id)
            ->orderByDesc('erp_leads.id')
            ->select(['erp_leads.*', 'products.name as product_name', 'cat.title as cat_title', 'br.name as brand_name', 'els.name as status_name', 'c.name as customer_name', 'c.id as customer_id']);

        $total  = $source->count();
        $source = $source->get();

        foreach ($source as $key => $value) {
            $source[$key]->media_url = null;
            $media                   = $value->getMedia(config('constants.media_tags'))->first();
            if ($media) {
                $source[$key]->media_url = getMediaUrl($media);
            }

            if (empty($source[$key]->media_url) && $value->product_id) {
                $product = Product::find($value->product_id);
                if ($product) {
                    $media = $product->getMedia(config('constants.media_tags'))->first();
                    if ($media) {
                        $source[$key]->media_url = getMediaUrl($media);
                    }
                }
            }
        }

        return [$total, $source];
    }
}
