<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailStatus extends Model
{
    use HasFactory;

    protected $table = 'email_status';

    protected $fillable = [
        'email_status',
        'color',
    ];
}
