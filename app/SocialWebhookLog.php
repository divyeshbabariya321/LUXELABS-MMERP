<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialWebhookLog extends Model
{
    const SUCCESS = 1;

    const ERROR = 2;

    const INFO = 3;

    const WARNING = 4;

    const TYPE = [
        self::SUCCESS => 'SUCCESS',
        self::ERROR   => 'ERROR',
        self::INFO    => 'INFO',
        self::WARNING => 'WARNING',
    ];

    protected $fillable = ['type', 'log', 'context'];

    public static function log($type, $log, $context = null)
    {
        static::create([
            'type'    => $type,
            'log'     => $log,
            'context' => json_encode($context),
        ]);
    }
}
