<?php

namespace App\Models;
use App\StoreWebsite;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MagentoCronList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cron_name', 'last_execution_time', 'last_message', 'cron_status', 'frequency',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'website_ids');
    }
}
