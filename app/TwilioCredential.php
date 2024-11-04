<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\TwilioActiveNumber;

class TwilioCredential extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="twilio_credentials",type="string")
     * @SWG\Property(property="account_id",type="integer")
     * @SWG\Property(property="twilio_email",type="string")
     * @SWG\Property(property="auth_token",type="string")
     */

    protected $fillable = ['twilio_email', 'account_id', 'auth_token', 'twilio_recovery_code'];

    public function numbers(): HasMany
    {
        return $this->hasMany(TwilioActiveNumber::class, 'account_sid', 'account_id');
    }
}
