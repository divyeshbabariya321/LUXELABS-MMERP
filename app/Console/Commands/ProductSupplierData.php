<?php

namespace App\Console\Commands;

use App\Exports\ProductSupplierExport;
use App\Product;
use App\ProductSupplier;
use App\Supplier;
use Carbon\Carbon;
use Excel;
use Illuminate\Console\Command;

class ProductSupplierData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supplier set in products Table, but the Supplier is not set for this product in product_suppliers Table';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $product_data = Product::get();

        $product_suppliers_data = ProductSupplier::get();

        $supplier_data = Supplier::get();
        $supplier_arr = [];
        foreach ($supplier_data as $key => $value) {
            $supplier_arr[$value->id] = $value->supplier;
        }

        $product_suppliers_arr = [];
        foreach ($product_suppliers_data as $key => $value) {
            $product_suppliers_arr[$value->product_id][$value->supplier_id] = ($supplier_arr[$value->supplier_id] ?? $value->supplier_id);
        }

        $product_not_available_product_supplier_table = [];
        $supplier_exist_product_supplier_table = [];
        $supplier_not_exist_product_supplier_table = [];

        foreach ($product_data as $key => $value) {
            if ($value->supplier_id != '' && $value->supplier_id != null) {
                $supplier_id = $value->supplier_id;
                $product_id = $value->id;
                if (array_key_exists($product_id, $product_suppliers_arr)) {
                    if (array_key_exists($supplier_id, $product_suppliers_arr[$product_id])) {
                        $supplier_exist_product_supplier_table[$key]['product_id'] = $product_id;
                        $supplier_exist_product_supplier_table[$key]['product_name'] = ($value->name ?? '');
                        $supplier_exist_product_supplier_table[$key]['supplier_id'] = $supplier_id;
                        $supplier_exist_product_supplier_table[$key]['supplier_name'] = ($product_suppliers_arr[$product_id][$supplier_id] ?? '-');
                    } else {
                        $supplier_not_exist_product_supplier_table[$key]['product_id'] = $product_id;
                        $supplier_not_exist_product_supplier_table[$key]['product_name'] = ($value->name ?? '');
                        $supplier_not_exist_product_supplier_table[$key]['supplier_id'] = $supplier_id;
                    }
                } else {
                    $product_not_available_product_supplier_table[$key]['product_id'] = $product_id;
                    $product_not_available_product_supplier_table[$key]['product_name'] = ($value->name ?? '');
                }
            }
        }

        $filename = Carbon::now()->format('Y-m-d-H-m-s').'_not_mapping_supplier.xlsx';
        $path = 'not_mapping_product_supplier/'.$filename;

        Excel::store(new ProductSupplierExport($supplier_not_exist_product_supplier_table, $path), $path, 'files');

        $downloadUrl = '/admin-menu/db-query/report-download?file=app/files/not_mapping_product_supplier/'.$filename;

        $download_url = '<a href="'.$downloadUrl.'" >Download File</a>';

        dd('Please Check This File : '.$download_url);
    }
}
