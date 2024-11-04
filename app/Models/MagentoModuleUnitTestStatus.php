<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoModuleUnitTestStatus extends Model
{
    use HasFactory;

    public $table = 'magento_modules_unit_test_statuses';

    protected $fillable = ['unit_test_status_name'];
}
