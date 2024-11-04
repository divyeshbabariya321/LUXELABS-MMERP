<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class DatabaseExportCommandLog extends Model
{

    protected $fillable = [
        'user_id', 'command', 'response',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
