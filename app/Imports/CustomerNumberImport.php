<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerNumberImport implements ToModel
{
    public function model(array $row): ?Model
    {
        return $row[0];
    }
}
