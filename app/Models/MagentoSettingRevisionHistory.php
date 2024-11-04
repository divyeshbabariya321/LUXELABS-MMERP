<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoSettingRevisionHistory extends Model
{
    use HasFactory;

    protected $table = 'magento_setting_revision_history';

    public $fillable = [
        'setting',
        'date',
        'status',
        'log',
        'config_revision',
        'active',
    ];

    const ACTIVE = 1;

    const INACTIVE = 0;

    const SUCCESSFUL = 1;

    const FAILED = 0;

    public static $active = [
        self::ACTIVE   => 'Active',
        self::INACTIVE => 'InActive',
    ];

    public static $status = [
        self::SUCCESSFUL => 'Successful',
        self::FAILED     => 'Failed',
    ];
}
