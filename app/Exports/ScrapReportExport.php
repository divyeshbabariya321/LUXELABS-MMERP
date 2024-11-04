<?php

namespace App\Exports;
use App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScrapReportExport implements FromView
{
    public function __construct(public $data)
    {
    }

    public function view(): View
    {
        return view('exports.scrap-report', ['reportData' => $this->data]);
    }
}
