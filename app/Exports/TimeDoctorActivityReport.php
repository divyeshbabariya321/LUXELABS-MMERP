<?php

namespace App\Exports;
use App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TimeDoctorActivityReport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents
{
    public function __construct(protected array $user)
    {
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
            },
        ];
    }

    public function array(): array
    {
        $new_customers     = [];
        $total_amount      = 0;
        $total_amount_paid = 0;
        $total_balance     = 0;
        $index             = 0;

        foreach ($this->user as $key => $user) {
            foreach ($user as $kk => $val) {
                $index = $index + 1;

                $total_amount += $val['amount'] ?? 0;
                $total_amount_paid += $val['amount_paid'] ?? 0;
                $total_balance += $val['balance'] ?? 0;

                $new_customers[$index]['date']        = $val['date'] ?? null;
                $new_customers[$index]['details']     = $val['details'] ?? null;
                $new_customers[$index]['category']    = $val['category'] ?? null;
                $new_customers[$index]['time_spent']  = $val['time_spent'] ?? null;
                $new_customers[$index]['amount']      = $val['amount'] ?? 0;
                $new_customers[$index]['currency']    = $val['currency'] ?? null;
                $new_customers[$index]['amount_paid'] = $val['amount_paid'] ?? 0;
                $new_customers[$index]['balance']     = $val['balance'] ?? 0;
            }
        }

        array_push($new_customers, ['Total ', null, null, null, $total_amount, null, $total_amount_paid, $total_balance]);

        return $new_customers;
    }

    public function headings(): array
    {
        return ['Date', 'Details', 'Category', 'Time Spent', 'Amount', 'Currency', 'Amount Paid', 'Balance'];
    }
}
