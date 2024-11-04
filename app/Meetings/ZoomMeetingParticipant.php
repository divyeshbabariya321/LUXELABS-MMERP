<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomMeetingParticipant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'name',
        'email',
        'join_time',
        'leave_time',
        'duration',
        'zoom_user_id',
        'leave_reason',
        'participant_uuid',
        'recording_path',
        'zoom_recording_id',
    ];

    public function recording(): BelongsTo
    {
        return $this->belongsTo(ZoomMeetingDetails::class, 'zoom_recording_id', 'download_url_id');
    }
}
