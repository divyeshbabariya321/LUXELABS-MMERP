<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnvDescription extends Model
{
    use HasFactory;

    public $fillable = [
        'key',
        'description',
    ];
}
