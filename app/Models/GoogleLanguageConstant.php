<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleLanguageConstant extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_language_constant_id',
        'name',
        'code',
        'is_targetable',
    ];
}
