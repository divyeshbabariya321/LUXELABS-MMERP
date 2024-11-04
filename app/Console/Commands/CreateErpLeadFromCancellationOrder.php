<?php

namespace App\Console\Commands;

use App\Brand;
use App\CronJob;
use App\ErpLeads;
use App\Helpers\OrderHelper;
use App\Order;
use App\Setting;
use Exception;
use Illuminate\Console\Command;

class CreateErpLeadFromCancellationOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-erp-lead-from-cancellation-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create erp lead from cancellation order';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! (Setting::getErpLeadsCronSave())) {
            exit('Disabled');
        }
        try {

            $orders = Order::where('order_status_id', OrderHelper::$cancel)->get();
            if ($orders) {
                foreach ($orders as $order) {
                    $orderProduct = $order->order_product()->first();
                    $product = $orderProduct->products()->first();
                    $brand = Brand::where('id', $product->id)->first();

                    $erpLeads = new ErpLeads;
                    $erpLeads->fill([
                        'lead_status_id' => 4,
                        'customer_id' => $order->customer_id,
                        'product_id' => $product->id,
                        'brand_id' => $product->brand,
                        'category_id' => $product->category,
                        'color' => $orderProduct->color,
                        'size' => $orderProduct->size,
                        'type' => 'erp-lead-from-cancellation-order',
                        'min_price' => $orderProduct->product_price,
                        'max_price' => $orderProduct->product_price,
                        'brand_segment' => $brand->brand_segment,
                    ]);
                    $erpLeads->save();

                    $media = $product->getMedia(config('constants.media_tags'))->first();
                    if ($media) {
                        $erpLeads->attachMedia($media, config('constants.media_tags'));
                    }
                    $this->info('order id = '.$order->id.' create id = '.$erpLeads->id."\n");
                }
            }
            $message = $this->generate_erp_response('erp.lead.created.for.candellation.order.success', 0, request('lang_code'));
            echo $message;
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
