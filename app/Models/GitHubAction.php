<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitHubAction extends Model
{
    use HasFactory;

    protected $table = 'github_actions';
    protected $guarded = [];
    
}
