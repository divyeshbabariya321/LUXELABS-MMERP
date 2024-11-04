<?php

namespace App\Exports;
use App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    public function __construct(public $data)
    {
    }

    public function view(): View
    {
        return view('exports.report', ['reportData' => $this->data]);
    }
}
