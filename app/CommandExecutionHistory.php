<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommandExecutionHistory extends Model
{
    protected $table = 'command_execution_historys';

    protected $fillable = ['command_name', 'command_answer', 'user_id', 'status'];
}
