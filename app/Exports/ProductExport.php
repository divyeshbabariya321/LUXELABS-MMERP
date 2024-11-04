<?php

namespace App\Exports;
use App\Exports;
use App\Brand;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductExport implements FromView
{
    public function __construct(public $data)
    {
    }

    public function view(): View
    {
        $brands = Brand::all()->pluck('name', 'id')->toArray();
        $attach_image_tag = config('constants.attach_image_tag');
        return view('exports.products', ['data' => $this->data, 'brands' => $brands, 'attach_image_tag' => $attach_image_tag]);
    }
}
