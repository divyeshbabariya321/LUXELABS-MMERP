<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDatabaseLog extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'updated_by',
        'request_data',
        'response_data',
    ];
}
