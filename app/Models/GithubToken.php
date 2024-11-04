<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\Github\GithubRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GithubToken extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['created_by', 'github_repositories_id', 'github_type', 'token_key', 'expiry_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }

    public function githubrepository(): BelongsTo
    {
        return $this->belongsTo(GithubRepository::class, 'github_repositories_id')->select('name', 'id');
    }
}
