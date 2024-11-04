<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWithCategory extends Model
{
    use HasFactory;
    protected $table = 'customer_with_categories';
}
