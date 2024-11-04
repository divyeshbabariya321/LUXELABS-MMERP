<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;

class TwilioCallForwarding extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="twilio_call_forwarding",type="string")
     * @SWG\Property(property="twilio_number_sid",type="integer")
     * @SWG\Property(property="twilio_number",type="string")
     * @SWG\Property(property="forwarding_on",type="string")
     * @SWG\Property(property="twilio_active_number_id",type="integer")
     */
    protected $table = 'twilio_call_forwarding';

    protected $fillable = ['twilio_number_sid', 'twilio_number', 'forwarding_on', 'twilio_active_number_id'];

    public function forwarded_number_details(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'forwarding_on');
    }
}
