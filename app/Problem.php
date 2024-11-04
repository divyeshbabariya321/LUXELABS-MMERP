<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    const SEVERITY = [
        0 => 'Not classified',
        1 => 'Information',
        2 => 'Warning',
        3 => 'Average',
        4 => 'High',
        5 => 'Disaster',
    ];

    protected $fillable = ['eventid', 'objectid', 'name', 'hostname'];

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        if (! empty($data['datetime'])) {
            $data['datetime'] = date('Y-m-d H:i:s', $data['datetime']);
        }

        if (! empty($data['recovery_time'])) {
            $data['recovery_time'] = date('Y-m-d H:i:s', $data['recovery_time']);
        }

        if (! empty($data['time_duration'])) {
            $data['time_duration'] = date('Y-m-d H:i:s', $data['time_duration']);
        }

        return $data;
    }
}
