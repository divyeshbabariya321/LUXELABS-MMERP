<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryUpdateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'datacontent', 'logtype',
    ];
}
