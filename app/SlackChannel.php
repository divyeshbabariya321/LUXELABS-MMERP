<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlackChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'channel_name',
        'description',
        'status',
        'entry_by',
        'entry_ip',
        'update_by',
        'update_ip',
    ];

    protected $dates = ['deleted_at'];
}
