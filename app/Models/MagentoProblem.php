<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoProblem extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'source',
        'test',
        'severity',
        'type',
        'error_body',
        'status',
    ];
}
