<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectKeyword extends Model
{
    protected $fillable = ['keyword_id', 'project_id'];
}
