<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatabaseBackupMonitoring extends Model
{
    use HasFactory;

    protected $table = 'database_backup_monitoring';

    public function dbStatusColour(): BelongsTo
    {
        return $this->belongsTo(DatabaseBackupMonitoringStatus::class, 'db_status_id');
    }
}
