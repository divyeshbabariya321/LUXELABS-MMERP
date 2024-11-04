<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScriptDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'script_type',
        'file',
        'category',
        'usage_parameter',
        'comments',
        'author',
        'description',
        'location',
        'last_run',
        'last_output',
        'status',
        'history_status',
    ];
}
