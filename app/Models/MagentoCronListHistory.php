<?php

namespace App\Models;
use App\StoreWebsite;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoCronListHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cron_id', 'user_id', 'store_website_id', 'server_ip', 'request_data', 'response_data', 'job_id', 'status', 'working_directory', 'last_execution_time',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_ids');
    }
}
