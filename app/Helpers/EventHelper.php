<?php

namespace App\Helpers;

use App\UserEvent\UserEvent;
use App\UserEvent\UserEventParticipant;

class EventHelper
{
    public static function getUserEventByDailyActivityID($daily_activity_id)
    {
        return UserEvent::where('daily_activity_id', $daily_activity_id)->with('attendees')->first();
    }

    public static function getUserEventParticipantByUserEventyID($user_event_id)
    {
        return UserEventParticipant::where('user_event_id', $user_event_id)->pluck('object_id')->toArray();
    }
}
