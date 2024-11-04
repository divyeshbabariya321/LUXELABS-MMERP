<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoProblemStatus extends Model
{
    use HasFactory;

    public $fillable = [
        'status_name',
    ];
}
