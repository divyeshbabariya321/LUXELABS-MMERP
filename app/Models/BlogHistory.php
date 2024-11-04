<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class BlogHistory extends Model
{
    public $fillable = [
        'blog_id',
        'plaglarism',
        'internal_link',
        'external_link',
        'user_id',
        'create_time',
        'no_index',
        'no_follow',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
