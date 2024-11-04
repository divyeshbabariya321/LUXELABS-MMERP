<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapApks extends Model
{
    use HasFactory;

    public $fillable = [
        'application_name',
        'apk_file',
        'status',
    ];
}
