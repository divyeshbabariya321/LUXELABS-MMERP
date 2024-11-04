<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleTranslateCsvData extends Model
{
    use HasFactory;

    public $table = 'google_file_translate_csv_datas';

    protected $fillable = ['key', 'value', 'lang_id', 'google_file_translate_id'];
}
