<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleAdsLog extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'type',
        'module',
        'message',
        'response',
        'user_ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
