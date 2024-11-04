<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowAction extends Model
{

    protected $fillable = [
        'path_id',
        'type_id',
        'rank',
        'after_seconds',
        'message_title',
        'deleted',
        'parent_action_id',
        'condition',
    ];
}
