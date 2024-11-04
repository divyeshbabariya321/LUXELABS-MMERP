<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class LogChatMessage extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="log_case_id",type="integer")
     * @SWG\Property(property="task_id",type="integer")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="log_msg",type="string")
     */
    protected $fillable = ['log_case_id', 'task_id', 'message', 'log_msg'];
}
