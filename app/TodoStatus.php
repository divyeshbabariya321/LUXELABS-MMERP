<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoStatus extends Model
{

    protected $fillable = ['id', 'name', 'created_at', 'updated_at'];
}
