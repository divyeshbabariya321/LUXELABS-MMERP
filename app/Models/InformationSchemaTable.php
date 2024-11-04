<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformationSchemaTable extends Model
{
    use HasFactory;

    protected $table = 'information_schema.tables';
}
