<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'message_id',
        'subject',
        'message',
        'timestamp',
        'body',
        'headers',
        'subscription_response',
    ];

    protected function casts(): array
    {
        return [
            'body'    => 'array',
            'headers' => 'array',
        ];
    }
}
