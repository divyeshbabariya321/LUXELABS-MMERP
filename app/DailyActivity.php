<?php

namespace App;
use App\Remark;
use App\GeneralCategory;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="time_slot",type="datetime")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="is_admin",type="boolean")
     * @SWG\Property(property="assist_msg",type="string")
     * @SWG\Property(property="activity",type="string")
     * @SWG\Property(property="for_date",type="datetime")
     * @SWG\Property(property="general_category_id",type="integer")
     * @SWG\Property(property="actual_start_date",type="datetime")
     */
    protected $fillable = [
        'time_slot',
        'user_id',
        'is_admin',
        'assist_msg',
        'activity',
        'for_date',
        'general_category_id',
        'actual_start_date',
        'repeat_type',
        'repeat_on',
        'repeat_end',
        'repeat_end_date',
        'parent_row',
        'timezone',
        'status',
        'type',
        'type_table_id',
        'next_run_at',
    ];

    public function remarks(): HasMany
    {
        return $this->hasMany(Remark::class, 'taskid')->where('module_type', 'task')->latest();
    }

    public function generalCategory(): HasOne
    {
        return $this->hasOne(GeneralCategory::class, 'id', 'general_category_id');
    }
}
