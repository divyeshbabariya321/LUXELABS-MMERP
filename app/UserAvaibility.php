<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserAvaibility extends Model
{
    protected $fillable = [
        'user_id',
        'from',
        'to',
        'status',
        'note',
        'date',
        'start_time',
        'end_time',
        'lunch_time',
        'lunch_time_from',
        'lunch_time_to',
        'is_latest',
    ];

    public static function getAvailableDays($str)
    {
        return $str ? explode(',', str_replace(' ', '', $str)) : [];
    }

    public static function getAvailableDates($stDate, $enDate, $days, $dates = [])
    {
        $return = [];
        $range = dateRangeArr($stDate, $enDate);
        foreach ($range as $value) {
            if (in_array($value['day'], $days)) {
                $return[] = $value['date'];
            }
        }
        if ($dates) {
            foreach ($return as $key => $value) {
                if (! in_array($value, $dates)) {
                    unset($return[$key]);
                }
            }
            $return = array_values($return);
        }

        return $return;
    }

    public static function dateWiseHourlySlots($dateArr, $stTimer, $enTimer, $lunchTimer = null)
    {
        $slots = [];

        foreach ($dateArr as $date) {
            if ($stTimer < $enTimer) {
                $stDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$stTimer));
                $enDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$enTimer));
            } else {
                $stDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$stTimer));
                $enDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$enTimer.' + 1 day'));
            }

            $lunchTime = null;
            if ($lunchTimer) {
                $lunchTime = date('Y-m-d H:i:00', strtotime($date.' '.$lunchTimer));
                if ($lunchTime < date('Y-m-d H:i:00', strtotime($stDatetime))) {
                    $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime.' + 1 day'));
                } else {
                    $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime));
                }

                $lunchTime = ($stDatetime <= $lunchTime && $lunchTime <= $enDatetime) ? $lunchTime : null;

            }

            if ($lunchTime) {
                $stTime1 = $stDatetime;
                $enTime1 = $lunchTime;
                $slots = array_merge_recursive($slots, getHourlySlots($stTime1, $enTime1));

                $temp = getHourlySlots($lunchTime, date('Y-m-d H:i:00', strtotime($lunchTime.' +1 hour')));
                foreach ($temp as $key => $value) {
                    $temp[$key]['type'] = 'LUNCH';
                }
                $slots = array_merge_recursive($slots, $temp);

                $stTime2 = date('Y-m-d H:i:00', strtotime($lunchTime.' +1 hour'));
                $enTime2 = $enDatetime;
                $slots = array_merge_recursive($slots, getHourlySlots($stTime2, $enTime2));
            } else {
                $slots = array_merge_recursive($slots, getHourlySlots($stDatetime, $enDatetime));
            }
        }

        if ($slots) {
            $temp = [];
            foreach ($slots as $key => $value) {
                $value['type'] = $value['type'] ?? ($value['en'] < date('Y-m-d H:i:s') ? 'PAST' : 'AVL');
                $temp[date('Y-m-d', strtotime($value['st']))][] = $value;
            }
            $slots = $temp;
        }

        return $slots;
    }

    public static function dateWiseHourlySlotsV2($dateArr, $stTimer, $enTimer, $lunchTimer, $availability)
    {
        $slots = [];

        foreach ($dateArr as $date) {
            if ($stTimer < $enTimer) {
                $stDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$stTimer));
                $enDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$enTimer));
            } else {
                $stDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$stTimer));
                $enDatetime = date('Y-m-d H:i:00', strtotime($date.' '.$enTimer.' + 1 day'));
            }

            $lunchTime = null;

            if ($lunchTime) {
                $stTime1 = $stDatetime;
                $enTime1 = $lunchTime;
                $slots = array_merge_recursive($slots, getHourlySlots($stTime1, $enTime1));

                $temp = getHourlySlots($lunchTime, date('Y-m-d H:i:00', strtotime($lunchTime.' +1 hour')));
                foreach ($temp as $key => $value) {
                    $temp[$key]['type'] = 'LUNCH';
                }
                $slots = array_merge_recursive($slots, $temp);

                $stTime2 = date('Y-m-d H:i:00', strtotime($lunchTime.' +1 hour'));
                $enTime2 = $enDatetime;
                $slots = array_merge_recursive($slots, getHourlySlots($stTime2, $enTime2));
            } else {
                $slots = array_merge_recursive($slots, getHourlySlots($stDatetime, $enDatetime));
            }
        }

        if ($slots) {
            $temp = [];
            foreach ($slots as $key => $value) {
                $value['type'] = $value['type'] ?? ($value['en'] < date('Y-m-d H:i:s') ? 'PAST' : 'AVL');
                $value['slot_type'] = $value['type'];
                $temp[date('Y-m-d', strtotime($value['st']))][] = $value;
            }
            $slots = $temp;
        }

        foreach ($slots as $d => $slot) {
            $LStart = Carbon::parse($d.' '.$availability->lunch_time_from);
            $LEnd = Carbon::parse($d.' '.$availability->lunch_time_to);

            foreach ($slot as $key => $s) {
                $SStart = Carbon::parse($s['st']);
                $SEnd = Carbon::parse($s['en']);
                if ($LStart->gte($SStart) && $LEnd->lte($SEnd)) {
                    $slots[$d][$key]['type'] = 'SMALL-LUNCH';
                } elseif ($LStart->lte($SStart) && $LEnd->gte($SEnd)) {
                    $slots[$d][$key]['type'] = 'FULL-LUNCH';
                } elseif ($LStart->gt($SStart) && $LStart->lt($SEnd)) {
                    $slots[$d][$key]['type'] = 'LUNCH-START';
                    $slots[$d][$key]['new_en'] = ($d.' '.$availability->lunch_time_from);
                } elseif ($LEnd->gt($SStart) && $LEnd->lt($SEnd)) {
                    $slots[$d][$key]['type'] = 'LUNCH-END';
                    $slots[$d][$key]['new_st'] = ($d.' '.$availability->lunch_time_to);
                }
                $slots[$d][$key]['lunch_time'] = [
                    'from' => ($d.' '.$availability->lunch_time_from),
                    'to' => ($d.' '.$availability->lunch_time_to),
                ];
            }
        }

        return $slots;
    }
}
