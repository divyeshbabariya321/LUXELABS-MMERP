<?php

namespace App\Exports;
use App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmailFailedReport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function __construct(protected array $lists)
    {
    }

    public function array(): array
    {
        return $this->lists;
    }

    public function headings(): array
    {
        return [
            'Id',
            'From Name',
            'Status',
            'Message',
            'Created',
        ];
    }
}
