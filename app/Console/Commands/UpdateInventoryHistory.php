<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\InventoryHistory;
use App\InventoryStatusHistory;
use App\Product;
use App\Supplier;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInventoryHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:history:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the inventory History in the ERP';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $date = date('Y-m-d');
            $totalProduct = Supplier::join('scrapers as sc', 'sc.supplier_id', 'suppliers.id')
                ->join('scraped_products as sp', 'sp.website', 'sc.scraper_name')
                ->join('products as p', 'p.id', 'sp.product_id')
                ->where('suppliers.supplier_status_id', 1)
                ->select(DB::raw('count(distinct p.id) as total'))->first();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier query finished.']);

            $totalProduct = ($totalProduct) ? $totalProduct->total : 0;

            $noofProductInStock = Product::where('stock', '>', 0)->count();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'product stock count query finished.']);

            $updated_product = InventoryStatusHistory::whereDate('date', '=', $date)->select(DB::raw('count(distinct product_id) as total'))->first();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Inventory status history query finished.']);

            $data = [
                'date' => $date,
                'total_product' => $totalProduct,
                'updated_product' => ($updated_product) ? $updated_product->total : 0,
                'in_stock' => $noofProductInStock,
            ];
            $history = InventoryHistory::whereDate('date', '=', $date)->first();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Inventory history query finished.']);
            if ($history) {
                InventoryHistory::where('id', $history->id)->update($data);
            } else {
                InventoryHistory::insert($data);
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Inventory history query finished.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
