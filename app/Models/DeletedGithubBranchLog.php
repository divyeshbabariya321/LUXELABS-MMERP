<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeletedGithubBranchLog extends Model
{
    use HasFactory;

    protected $table = 'deleted_github_branches_log';

    protected $fillable = [
        'repository_id',
        'branch_name',
        'deleted_by',
        'status',
        'error_message',
    ];
}
