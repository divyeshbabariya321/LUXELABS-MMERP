<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrapStatisticsStaus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'status_value',
    ];
}
