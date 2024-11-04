<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppointmentRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'requested_user_id', 'remarks', 'request_status', 'requested_time', 'is_view', 'decline_remarks'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userrequest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_user_id');
    }
}
