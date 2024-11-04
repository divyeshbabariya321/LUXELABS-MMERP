<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMessages extends Model
{
    protected $fillable = [
        'social_contact_id',
        'from',
        'to',
        'message',
        'reactions',
        'is_unsupported',
        'message_id',
        'attachments',
        'created_time',
    ];

    protected function casts(): array
    {
        return [
            'from'           => 'json',
            'to'             => 'json',
            'attachments'    => 'json',
            'is_unsupported' => 'boolean',
            'created_time'   => 'datetime',
        ];
    }
}
