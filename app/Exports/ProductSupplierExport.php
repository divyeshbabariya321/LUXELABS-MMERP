<?php

namespace App\Exports;
use App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductSupplierExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /***
     * @return \Illuminate\Support\Collection
     */

    public function __construct(protected array $supplier_not_exist_product_supplier_table, protected $path)
    {
    }

    public function array(): array
    {
        $products_array = [];

        foreach ($this->supplier_not_exist_product_supplier_table as $k => $v) {
            $arr = [];

            $arr['product_id']   = $v['product_id'];
            $arr['product_name'] = $v['product_name'];
            $arr['supplier_id']  = $v['supplier_id'];

            $products_array[] = $arr;
        }

        return $products_array;
    }

    public function headings(): array
    {
        return [
            'Product Id',
            'Prodcuct Name',
            'Supplier Id',
        ];
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
            },
        ];
    }
}
