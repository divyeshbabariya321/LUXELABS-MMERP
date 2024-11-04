<?php

namespace App\Exports;
use App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LogListMagentoExport implements FromArray, ShouldAutoSize
{
    public function __construct(public $data)
    {
    }

    public function array(): array
    {
        return $this->data;
    }
}
