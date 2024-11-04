<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class TwilioCallBlock extends Model
{
    protected $fillable = ['id', 'customer_id', 'user_agent_id', 'twilio_credentials_id', 'customer_website_id', 'twilio_number_website_id', 'customer_number', 'twilio_number'];

    public function customerUser(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function twilioCredentials(): HasOne
    {
        return $this->hasOne(TwilioCredential::class, 'id', 'twilio_credentials_id');
    }

    public function customerWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'customer_website_id');
    }

    public function twilioNumberWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'twilio_number_website_id');
    }
}
