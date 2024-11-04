<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\Github\GithubRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubTokenHistory extends Model
{
    use HasFactory;

    protected $fillable = ['run_by', 'github_repositories_id', 'github_type', 'token_key', 'details'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'run_by')->select('name', 'id');
    }

    public function githubrepository(): BelongsTo
    {
        return $this->belongsTo(GithubRepository::class, 'github_repositories_id')->select('name', 'id');
    }
}
