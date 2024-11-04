<?php

namespace App\Github;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubTask extends Model
{
    use HasFactory;

    public $fillable = [
        'task_name',
        'assign_to',
    ];

    public function githubTaskPullRequests(): HasMany
    {
        return $this->hasMany(GithubTaskPullRequest::class);
    }
}
