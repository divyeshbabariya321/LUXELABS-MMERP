<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFeedbackStatusUpdate extends Model
{
    protected $fillable = [
        'user_id',
        'user_feedback_status_id',
        'user_feedback_category_id',
    ];
}
