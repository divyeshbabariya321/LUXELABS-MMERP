<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Helpers\GithubTrait;
use Illuminate\Database\Eloquent\Model;

class BuildProcessStatusHistories extends Model
{
    use GithubTrait;

    protected $fillable = ['id', 'project_id', 'build_process_history_id', 'build_number', 'old_status', 'status'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
