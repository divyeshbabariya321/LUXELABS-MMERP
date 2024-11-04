<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostmanStatus extends Model
{
    use HasFactory;

    public $table = 'postman_status';

    public $fillable = [
        'status_name',
        'postman_color',
    ];
}
