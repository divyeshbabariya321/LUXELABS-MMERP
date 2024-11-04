<?php

namespace App\Github;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubTaskPullRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'task_id',
        'github_organization_id',
        'github_repository_id',
        'pull_number',
        'created_at',
        'updated_at',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
