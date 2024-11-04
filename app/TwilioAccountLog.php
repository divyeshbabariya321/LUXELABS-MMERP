<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioAccountLog extends Model
{
    protected $fillable = ['email', 'sid', 'log'];

    public static function log($email, $sid, $log)
    {
        static::create([
            'email' => $email,
            'sid'   => $sid,
            'log'   => $log,
        ]);
    }
}
