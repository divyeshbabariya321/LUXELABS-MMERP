<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\MagentoModuleM2ErrorStatus;
use App\Models\MagentoModuleReturnTypeErrorStatus;

class MagentoModule extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['row_bg_colour'];

    protected $fillable = [
        'module_category_id',
        'store_website_id',
        'module',
        'module_description',
        'current_version',
        'task_status',
        'last_message',
        'cron_time',
        'module_type',
        'status',
        'is_sql',
        'api',
        'cron_job',
        'is_third_party_plugin',
        'is_third_party_js',
        'is_js_css',
        'payment_status',
        'developer_name',
        'is_customized',
        'site_impact',
        'dev_verified_by',
        'dev_verified_status_id',
        'lead_verified_by',
        'lead_verified_status_id',
        'dev_last_remark',
        'lead_last_remark',
        'dependency',
        'composer',
        'module_review_standard',
        'magneto_location_id',
        'used_at',
        'return_type_error',
        'return_type_error_status',
        'm2_error_status_id',
        'm2_error_assignee',
        'm2_error_remark',
        'unit_test_status_id',
        'unit_test_remark',
        'unit_test_user_id',
    ];

    public function module_category(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleCategory::class, 'module_category_id');
    }

    public function module_location(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleLocation::class, 'magneto_location_id');
    }

    public function module_error_status_type(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleReturnTypeErrorStatus::class, 'return_type_error_status');
    }

    public function module_m2_error_status(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleM2ErrorStatus::class, 'm2_error_status_id');
    }

    public function store_website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id');
    }

    public function lastRemark(): HasOne
    {
        return $this->hasOne(MagentoModuleRemark::class, 'magento_module_id', 'id')->orderByDesc('created_at')->latest();
    }

    public function remarks(): HasMany
    {
        return $this->hasMany(MagentoModuleRemark::class, 'magento_module_id', 'id')->orderByDesc('created_at');
    }

    public function task_status_data(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'task_status', 'id');
    }

    public function module_type_data(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleType::class, 'module_type', 'id');
    }

    public function dev_verified(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dev_verified_by', 'id');
    }

    public function dev_verified_status(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'dev_verified_status_id', 'id');
    }

    public function lead_verified(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_verified_by', 'id');
    }

    public function lead_verified_status(): BelongsTo
    {
        return $this->belongsTo(MagentoModuleVerifiedStatus::class, 'lead_verified_status_id', 'id');
    }

    public function getRowBgColourAttribute()
    {
        $colour = '';
        if ($this->lead_verified_status) {
            $colour = @$this->lead_verified_status->color;
        } elseif ($this->dev_verified_status) {
            $colour = @$this->dev_verified_status->color;
        }

        return $colour;
    }

    public function getColumns()
    {
        $columns = [
            '0'  => 'Id',
            '1'  => 'Remark',
            '2'  => 'Category',
            '3'  => 'Description',
            '4'  => 'Name',
            '5'  => 'Location',
            '6'  => 'Used At',
            '7'  => 'API',
            '8'  => 'Cron',
            '9'  => 'Version',
            '10' => 'Type',
            '11' => 'Payment Status',
            '12' => 'Status',
            '13' => 'Dev Verified By',
            '14' => 'Dev Verified Status',
            '15' => 'Lead Verified By',
            '16' => 'Lead Verified Status	',
            '17' => 'Developer Name',
            '18' => 'Customized',
            '19' => 'js/css',
            '20' => '3rd Party Js',
            '21' => 'Sql',
            '22' => '3rd Party plugin',
            '23' => 'Site Impact',
            '24' => 'Review Standard',
            '25' => 'Return Type Error',
            '26' => 'Return Type Error Status',
            '27' => 'M2 Error Status',
            '28' => 'M2 Error Remark',
            '29' => 'M2 Error Assignee',
            '30' => 'Unit Test status',
            '31' => 'Unit Test Remark',
            '32' => 'Unit test User',
            '33' => 'Dependancies',
            '34' => 'Action',
        ];

        return $columns;
    }
}
