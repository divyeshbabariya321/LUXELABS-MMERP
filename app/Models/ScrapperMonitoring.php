<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\DeveloperTask;
use App\User;

class ScrapperMonitoring extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'scrapper_name',
        'need_proxy',
        'move_to_aws',
        'remarks',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
