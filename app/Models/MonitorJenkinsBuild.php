<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitorJenkinsBuild extends Model
{
    use HasFactory;

    // 1 = Failure, 0 = Success
    protected $appends = ['failuare_status_list'];

    protected $fillable = [
        'build_number',
        'project',
        'worker',
        'store_id',
        'clone_repository',
        'lock_build',
        'update_code',
        'composer_install',
        'make_config',
        'setup_upgrade',
        'compile_code',
        'static_content',
        'reindexes',
        'magento_cache_flush',
        'error',
        'build_status',
        'full_log',
        'meta_update',
    ];

    // 1 = Failure, 0 = Success
    public function getFailuareStatusListAttribute()
    {
        $failuareStatusList = [];

        if ($this->clone_repository === 1) {
            $failuareStatusList[] = 'Clone Repository';
        }
        if ($this->lock_build === 1) {
            $failuareStatusList[] = 'Lock Build';
        }
        if ($this->update_code === 1) {
            $failuareStatusList[] = 'Update Code';
        }
        if ($this->composer_install === 1) {
            $failuareStatusList[] = 'Composer Install';
        }
        if ($this->make_config === 1) {
            $failuareStatusList[] = 'Make Config';
        }
        if ($this->setup_upgrade === 1) {
            $failuareStatusList[] = 'Setup Upgrade';
        }
        if ($this->compile_code === 1) {
            $failuareStatusList[] = 'Compile Code';
        }
        if ($this->static_content === 1) {
            $failuareStatusList[] = 'Static Content';
        }
        if ($this->reindexes === 1) {
            $failuareStatusList[] = 'Reindexes';
        }
        if ($this->magento_cache_flush === 1) {
            $failuareStatusList[] = 'Magento Cache Flus';
        }
        if ($this->build_status === 1) {
            $failuareStatusList[] = 'Build Status';
        }
        if ($this->meta_update === 1) {
            $failuareStatusList[] = 'meta_update';
        }

        return implode(', ', $failuareStatusList);
    }
}
