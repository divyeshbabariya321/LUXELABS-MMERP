<?php

namespace App\Console\Commands;

use App\ErpLeads;
use App\ErpLeadsBrand;
use App\ErpLeadsCategory;
use App\ErpLeadSendingHistory;
use App\Product;
use App\Setting;
use App\SuggestedProduct;
use App\SuggestedProductList;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunErpLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erpleads:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Erp Leads';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lead_product_limit = Setting::where('name', 'send_leads_product')->value('val');
        if ($lead_product_limit == 0) {
            return false;
        }
        $leads = ErpLeads::join('customers as c', 'c.id', 'erp_leads.customer_id')
            ->where('is_blocked_lead', 0)
            ->where('c.do_not_disturb', 0)
            ->select(['erp_leads.*', 'c.lead_product_freq'])->get();

        Log::channel('customer')->info('Found leads'.$leads->count());
        Log::info('Found leads'.$leads->count());

        if (! $leads->isEmpty()) {
            foreach ($leads as $lead) {

                $products = Product::where(function ($q) {
                    $q->where('stock', '>', 0)->orWhere('supplier', 'in-stock');
                });

                $products = $products->join('brands as b', 'b.id', 'products.brand');
                $products = $products->join('categories as c', 'c.id', 'products.category');

                $products = $products->where('products.created_at', '>', Carbon::now()->subDays(30))
                    ->where('products.name', '!=', '')
                    ->where('products.sku', '!=', '')
                    ->where('products.price', '!=', '');

                $products = $products->where(function ($q) use ($lead) {
                    $Category_ids = ErpLeadsCategory::where('erp_lead_id', $lead->id)->where('category_id', '!=', '')->pluck('category_id')->toArray();
                    if (count($Category_ids) == 0) {
                        $Category_ids = [];
                    }
                    if (! in_array($lead->category_id, $Category_ids)) {
                        array_push($Category_ids, $lead->category_id);
                    }

                    $Brand_ids = ErpLeadsBrand::where('erp_lead_id', $lead->id)->where('brand_id', '!=', '')->pluck('brand_id')->toArray();
                    if (count($Brand_ids) == 0) {
                        $Brand_ids = [];
                    }
                    if (! in_array($lead->brand_id, $Brand_ids)) {
                        array_push($Brand_ids, $lead->brand_id);
                    }
                    $q->whereIn('b.id', $Brand_ids)->whereIn('c.id', $Category_ids);
                });

                $allProduts = $products->select(['products.*'])->orderByDesc('products.created_at')->limit($lead_product_limit)->get()->pluck('id')->toArray();

                Log::info('Count Products'.count($allProduts));

                if (! empty($products)) {
                    $allProdCounts = count($allProduts);
                    $newProdArr = [];
                    for ($i = 0; $i < $allProdCounts; $i++) {
                        //add data to erp_lead_sending_histories tables
                        $ErpLeadSendingHistory = new ErpLeadSendingHistory;
                        $checkCustomerExist = $ErpLeadSendingHistory::where('customer_id', '=', $lead->customer_id)
                            ->where('product_id', '=', $allProduts[$i])
                            ->where('lead_id', '=', $lead->id)
                            ->count();
                        if ($checkCustomerExist == 0) {
                            $ErpLeadSendingHistory->product_id = $allProduts[$i];
                            $ErpLeadSendingHistory->customer_id = $lead->customer_id;
                            $ErpLeadSendingHistory->lead_id = $lead->id;
                            $ErpLeadSendingHistory->save();
                            $newProdArr[$i] = $allProduts[$i];
                        }
                    }

                    Log::info('Count Products ARR'.count($newProdArr));

                    if (count($newProdArr) > 0) {
                        $suggestedProduct = SuggestedProduct::create([
                            'brands' => json_encode([$lead->brand_id]),
                            'categories' => json_encode([$lead->category_id]),
                            'customer_id' => $lead->customer_id,
                            'total' => count($newProdArr),
                            'platform' => 'lead',
                            'platform_id' => $lead->id,
                        ]);

                        if ($suggestedProduct) {
                            // setup here for the suggested product
                            foreach ($newProdArr as $new) {
                                SuggestedProductList::create([
                                    'suggested_products_id' => $suggestedProduct->id,
                                    'customer_id' => $lead->customer_id,
                                    'product_id' => $new,
                                    'date' => date('Y-m-d'),
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
