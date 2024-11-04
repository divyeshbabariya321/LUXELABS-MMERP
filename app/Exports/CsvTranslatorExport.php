<?php

namespace App\Exports;
use App\Exports;
use App\CsvTranslator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CsvTranslatorExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return CsvTranslator::select('csv_translators.id as id', 'csv_translators.key as key', 'csv_translators.en as en', 'csv_translators.es as es',
            'csv_translators.ru as ru', 'csv_translators.ko as ko', 'csv_translators.ja  as ja', 'csv_translators.it as it',
            'csv_translators.de as de', 'csv_translators.fr as fr', 'csv_translators.nl as nl', 'csv_translators.zh as zh',
            'csv_translators.ar as ar', 'csv_translators.ur as ur', 'users.name as updator_name', 'csv_translators.status as status',
            'csv_translators.updated_at as updated_date', )->leftjoin('users', 'users.id', 'csv_translators.updated_by_user_id')->get();
    }

    public function headings(): array
    {
        return ['Id', 'Key', 'En', 'Es', 'Ru', 'Ko', 'Ja', 'It', 'De', 'Fr', 'Nl', 'Zh', 'Ar', 'Ur', 'Updated By', 'Approved By', 'Status', 'Updated At'];
    }
}
