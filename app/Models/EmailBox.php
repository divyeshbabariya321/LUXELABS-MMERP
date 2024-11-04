<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailBox extends Model
{
    use HasFactory;

    protected $fillable = ['box_name'];
}
