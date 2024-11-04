<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleQuery extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="schedule_name",type="text")
     * @SWG\Property(property="query",type="text")
     * @SWG\Property(property="description",type="text")
     */
    protected $fillable = [
        'schedule_name', 'query', 'description',
    ];
}
