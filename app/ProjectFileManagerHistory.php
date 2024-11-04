<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectFileManagerHistory extends Model
{
    public $table = 'project_file_managers_history';

    protected $fillable = ['project_id', 'name', 'old_size', 'new_size', 'user_id'];
}
