<?php

namespace App\Models;

use App\DeveloperTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapperValues extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'task_type', 'scrapper_values', 'added_by'];

    public function tasks(): BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class, 'task_id')->select('id', 'subject');
    }

    public function scrappervalueshistory()
    {
        return $this->hasMany(ScrapperValuesHistory::class, 'task_id', 'task_id');
    }

    public function scrappervaluesremarkshistory()
    {
        return $this->hasMany(ScrapperValuesRemarksHistory::class, 'task_id', 'task_id');
    }
}
