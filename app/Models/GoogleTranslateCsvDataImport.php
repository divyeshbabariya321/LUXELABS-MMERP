<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleTranslateCsvDataImport implements ToModel, WithHeadingRow
{
    use HasFactory;

    private $param1;

    private $param2;

    public function __construct($param1, $param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }

    public function model(array $row): ?Model
    {
        return new GoogleTranslateCsvData([
            'key'                      => reset($row),
            'value'                    => next($row),
            'lang_id'                  => $this->param1,
            'google_file_translate_id' => $this->param2,
        ]);
    }
}
