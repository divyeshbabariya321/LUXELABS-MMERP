<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReplyPushStore extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'reply_id', 'store_id', 'platform_id',
    ];
}
