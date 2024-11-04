<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitPullRequestErrorLog extends Model
{
    use HasFactory;

    protected $table = 'git_pr_error_logs';
}
