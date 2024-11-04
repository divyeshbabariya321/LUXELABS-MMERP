<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataTableColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'section_name', 'column_name',
    ];
}
