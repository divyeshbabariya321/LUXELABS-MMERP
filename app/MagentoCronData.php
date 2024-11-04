<?php

namespace App;
use App\CronStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagentoCronData extends Model
{
    protected $table = 'magento_cron_datas';

    protected $fillable = [
        'id',
        'store_website_id',
        'cron_id',
        'job_code',
        'cron_message',
        'website',
        'cronstatus',
        'cron_created_at',
        'cron_scheduled_at',
        'cron_executed_at',
        'cron_finished_at',
    ];

    public function cronStatus(): BelongsTo
    {
        return $this->belongsTo(CronStatus::class, 'cronstatus', 'name');
    }
}
