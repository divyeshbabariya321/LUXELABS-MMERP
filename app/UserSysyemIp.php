<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UserSysyemIp extends Model
{
    protected $table = 'user_system_ip';

    protected $fillable = ['index_txt', 'ip', 'user_id', 'other_user_name', 'is_active', 'notes', 'source', 'command', 'status', 'message'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
