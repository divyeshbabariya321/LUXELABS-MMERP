<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HubstaffHistory extends Model
{
    protected $table = 'hubstaff_historys';

    protected $fillable = [
        'developer_task_id', 'old_value', 'new_value', 'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
