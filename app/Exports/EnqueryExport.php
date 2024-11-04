<?php

namespace App\Exports;
use App\Exports;

use App\Product;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EnqueryExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    /***
     * @return \Illuminate\Support\Collection
     */

    protected $count = 0;

    public function __construct(protected array $products, protected array $orders, protected $path)
    {
    }

    public function array(): array
    {
        $products_array = [];

        $products = Product::join('order_products', 'order_products.product_id', 'products.id')
            ->join('product_suppliers', 'product_suppliers.product_id', 'products.id')
            ->join('brands', 'brands.id', 'products.brand')
            ->select('product_suppliers.price as product_price', 'products.*', 'brands.name as brand_name')
            ->whereIn('products.id', $this->products)->whereIn('order_products.id', $this->orders)->groupBy('order_products.sku')->get();

        foreach ($products as $product) {
            $arr                      = [];
            $arr['name']              = $product->name;
            $arr['brand']             = $product->brand_name;
            $arr['sku']               = $product->sku;
            $arr['short_description'] = $product->short_description;
            $arr['product_price']     = $product->price;
            $arr['composition']       = $product->composition;
            $arr['product_link']      = $product->product_link;
            $products_array[]         = $arr;
        }

        return $products_array;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Brand',
            'SKU Code',
            'Description',
            'Price',
            'Composition',
            'Product Link',
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
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(40);
            },
        ];
    }
}
