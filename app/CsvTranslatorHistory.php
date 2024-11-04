<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CsvTranslatorHistory extends Model
{
    protected $table = 'csv_translator_history';

    protected $fillable = ['csv_translator_id', 'key', 'en', 'es', 'ru', 'ko', 'ja', 'it', 'de', 'fr', 'nl', 'zh', 'ar', 'ur'];
}
