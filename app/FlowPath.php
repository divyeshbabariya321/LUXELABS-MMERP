<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowPath extends Model
{

    protected $fillable = [
        'flow_id',
        'deleted',
        'parent_action_id',
        'path_for',
    ];
}
