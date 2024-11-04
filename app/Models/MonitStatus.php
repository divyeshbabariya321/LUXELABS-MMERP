<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\AssetsManager;

class MonitStatus extends Model
{
    use HasFactory;

    protected $table = 'monit_status';

    protected $fillable = ['service_name', 'status', 'uptime', 'memory', 'url', 'username', 'password', 'xmlid', 'ip', 'monit_api_id', 'asset_management_id'];

    public function assetsManager(): BelongsTo
    {
        return $this->belongsTo(AssetsManager::class, 'asset_management_id')->select('id', 'ip', 'ip_name');
    }
}
