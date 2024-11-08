<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoCompanyStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status_name', 'status_color'];
}
