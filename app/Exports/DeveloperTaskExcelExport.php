<?php

namespace App\Exports;
use App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\DevelopmentController;

class DeveloperTaskExcelExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $issues = app(DevelopmentController::class)->loadAllTasks($this->request)->get();

        return collect(app(DevelopmentController::class)->getTasksCsvNeededFormat($issues));
    }

    public function headings(): array
    {
        return [
            'Id',
            'Subject',
            'Assigned To',
            'Approved Time',
            'Status',
            'Tracked Time',
            'Difference',
        ];
    }
}
