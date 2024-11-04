<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DialogflowEntityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'kind',
    ];
}
