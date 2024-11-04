<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleM2ErrorStatus extends Model
{
    use HasFactory;

    protected $fillable = ['m2_error_status_name'];
}
