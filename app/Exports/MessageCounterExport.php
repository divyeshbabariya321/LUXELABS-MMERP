<?php

namespace App\Exports;
use App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MessageCounterExport implements FromCollection, WithHeadings
{
    public function __construct(protected $header, protected $data)
    {
    }

    public function headings(): array
    {
        return [
            $this->header,
        ];
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }
}
