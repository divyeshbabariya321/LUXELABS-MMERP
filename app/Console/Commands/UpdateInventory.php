<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\InventoryStatusHistory;
use App\Jobs\CallHelperForZeroStockQtyUpdate;
use App\Models\InventoryUpdateLog;
use App\Product;
use App\ScrapedProducts;
use App\Setting;
use App\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UpdateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the inventory in the ERP';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);
        Log::info('Update Inventory');
        try {
            $report = $this->createCronJobReport();
            $inventoryRecordsChunk = $this->getInventoryRecordsChunk();
            $products = $this->getProductsToUpdate($inventoryRecordsChunk);

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier model query finished.']);
            $skusArr = $products->pluck('sku')->toArray();
            $commandInvokeDateTime = Carbon::now();
            $this->logInventoryUpdate($skusArr, $commandInvokeDateTime);

            $selectedProducts = $this->getSelectedProducts($skusArr);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product model query finished.']);
            $skuProductArr = $this->mapSkuProducts($selectedProducts);

            $this->processProducts($products, $skuProductArr, $commandInvokeDateTime);
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    private function createCronJobReport(): CronJobReport
    {
        return CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);
    }

    private function getInventoryRecordsChunk(): int
    {
        $setting = Setting::select('name', 'val')
            ->where('name', 'inventory_update_records_chunk')
            ->first();

        return $setting && isset($setting['val']) ? (int) $setting['val'] : 0;
    }

    private function getProductsToUpdate(int $inventoryRecordsChunk): Collection
    {
        return Supplier::join('scrapers as sc', 'sc.supplier_id', 'suppliers.id')
            ->join('scraped_products as sp', 'sp.website', 'sc.scraper_name')
            ->where(function ($q) {
                $q->whereDate('last_cron_check', '!=', date('Y-m-d'))->orWhereNull('last_cron_check');
            })
            ->where('suppliers.supplier_status_id', 1)
            ->select('sp.last_inventory_at', 'sp.sku', 'sc.inventory_lifetime', 'suppliers.id as supplier_id', 'sp.id as sproduct_id', 'last_cron_check')
            ->groupBy('sku')
            ->take($inventoryRecordsChunk)
            ->get();
    }

    private function logInventoryUpdate(array $skusArr, Carbon $dateTime): void
    {
        $log = new InventoryUpdateLog;
        $log->logtype = 'sku';
        $log->datacontent = json_encode($skusArr);
        $log->created_at = $dateTime;
        $log->save();
    }

    private function getSelectedProducts(array $skusArr): Collection
    {
        return Product::select('id', 'isUploaded', 'color', 'sku')
            ->whereIn('sku', $skusArr)
            ->get();
    }

    private function mapSkuProducts(Collection $selectedProducts): array
    {
        $skuProductArr = [];
        foreach ($selectedProducts as $product) {
            $skuProductArr[$product->sku] = [
                'product_id' => $product->id,
                'isUploaded' => $product->isUploaded,
                'color' => $product->color,
            ];
        }

        return $skuProductArr;
    }

    private function processProducts(Collection $products, array $skuProductArr, Carbon $commandInvokeDateTime): void
    {
        $sproductIdArr = [];
        $statusHistory = [];
        $needToCheck = [];
        $productIdsArr = [];
        $hasInventory = false;
        $today = date('Y-m-d');

        foreach ($products as $records) {
            $sku = $records['sku'];
            if (isset($skuProductArr[$sku]) && $skuProductArr[$sku]['isUploaded'] == 1) {
                $this->updateProductInventory($records, $skuProductArr, $sproductIdArr, $statusHistory, $needToCheck, $commandInvokeDateTime, $today, $hasInventory);
            } else {
                Log::info('Product not found or isUploaded value is 0');
            }
        }

        $this->finalizeInventoryUpdate($sproductIdArr, $productIdsArr, $statusHistory, $needToCheck, $commandInvokeDateTime);
    }

    private function updateProductInventory($records, array $skuProductArr, &$sproductIdArr, &$statusHistory, &$needToCheck, Carbon $commandInvokeDateTime, string $today, &$hasInventory): void
    {
        $sku = $records['sku'];
        $records['product_id'] = $skuProductArr[$sku]['product_id'];
        $records['isUploaded'] = $skuProductArr[$sku]['isUploaded'];
        $records['color'] = $skuProductArr[$sku]['color'];
        array_push($sproductIdArr, $records['sproduct_id']);

        // Inventory status history update logic here...

        if (is_null($records['last_inventory_at']) || strtotime($records['last_inventory_at']) < strtotime('-'.$records['inventory_lifetime'].' days')) {
            $needToCheck[] = ['id' => $records['product_id'], 'sku' => $records['sku'].'-'.$records['color']];
        } else {
            $hasInventory = true;
        }
    }

    private function finalizeInventoryUpdate(array $sproductIdArr, array $productIdsArr, array $statusHistory, array $needToCheck, Carbon $commandInvokeDateTime): void
    {
        if (! empty($sproductIdArr)) {
            ScrapedProducts::whereIn('id', $sproductIdArr)->update(['last_cron_check' => date('Y-m-d H:i:s')]);
        }

        if (! empty($productIdsArr)) {
            Product::whereIn('id', $productIdsArr)->update(['stock' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        if (! empty($statusHistory)) {
            InventoryStatusHistory::insert($statusHistory);
        }

        if (! empty($needToCheck)) {
            try {
                $this->dispatchZeroStockUpdate($needToCheck, $commandInvokeDateTime);
            } catch (Exception $e) {
                Log::error('inventory:update :: CallHelperForZeroStockQtyUpdate :: '.$e->getMessage());
            }
        }
    }

    private function dispatchZeroStockUpdate(array $needToCheck, Carbon $dateTime): void
    {
        $log = new InventoryUpdateLog;
        $log->logtype = 'data_push_to_magento';
        $log->datacontent = json_encode($needToCheck);
        $log->created_at = $dateTime;
        $log->save();
        CallHelperForZeroStockQtyUpdate::dispatch($needToCheck)->onQueue('MagentoHelperForZeroStockQtyUpdate');
    }

    private function handleException(Exception $e): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);
        Log::info('Update Inventory CATCH');
        Log::error($e->getMessage());
        CronJob::insertLastError($this->signature, $e->getMessage());
    }
}
