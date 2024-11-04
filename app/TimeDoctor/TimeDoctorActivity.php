<?php

namespace App\TimeDoctor;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\DeveloperTask;
use App\TimeDoctor\TimeDoctorMember;

class TimeDoctorActivity extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'starts_at',
        'tracked',
        'overall',
        'time_doctor_payment_account_id',
        'status',
        'paid',
        'is_manual',
        'user_notes',
        'project_id',
    ];

    public static function getActivitiesForWeek($week, $year)
    {
        $result = getStartAndEndDate($week, $year);
        $start  = $result['week_start'];
        $end    = $result['week_end'];

        return self::leftJoin('time_doctor_members', 'time_doctor_members.hubstaff_user_id', '=', 'time_doctor_activities.user_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->select(['time_doctor_activities.*', 'time_doctor_members.user_id as system_user_id'])
            ->get();
    }

    /**
     * get the activities between start (inclusive)
     *
     * @param mixed $start
     * @param mixed $end
     */
    public static function getActivitiesBetween($start, $end)
    {
        return self::leftJoin('time_doctor_members', 'time_doctor_members.hubstaff_user_id', '=', 'time_doctor_activities.user_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->select(['time_doctor_activities.*', 'time_doctor_members.user_id as system_user_id'])
            ->get();
    }

    public static function getFirstUnaccounted()
    {
        return self::whereNull('time_doctor_payment_account_id')->orderBy('starts_at')->first();
    }

    public function developerTask(): HasMany
    {
        return $this->hasMany(DeveloperTask::class, 'hubstaff_task_id', 'task_id');
    }

    public static function getTrackedActivitiesBetween($start, $end, $user_id)
    {
        return self::leftJoin('time_doctor_members', 'time_doctor_members.time_doctor_user_id', '=', 'time_doctor_activities.user_id')->whereDate('time_doctor_activities.starts_at', '>=', $start)->whereDate('time_doctor_activities.starts_at', '<=', $end)->where('time_doctor_members.user_id', $user_id)->where('time_doctor_activities.status', 1)->where('time_doctor_activities.paid', 0)
        ->orderBy('time_doctor_activities.starts_at')
        ->select('time_doctor_activities.*')
        ->get();
    }

    /**
     * Get all activites,
     * which have approved and does not paid yet.
     */
    public static function getAllTrackedActivities(): array
    {
        return self::leftJoin('time_doctor_members', 'time_doctor_members.hubstaff_user_id', '=', 'time_doctor_activities.user_id')
            ->where('time_doctor_activities.status', 1)
            ->where('time_doctor_activities.paid', 0)
            ->orderByDesc('created_at')
            ->select('time_doctor_activities.*', 'time_doctor_members.user_id as hm_user_id')
            ->get();
    }

    public function getTimeDoctorAccount(): BelongsTo
    {
        return $this->belongsTo(TimeDoctorMember::class, 'user_id', 'time_doctor_user_id');
    }
}
