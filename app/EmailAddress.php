<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class EmailAddress extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="from_name",type="string")
     * @SWG\Property(property="from_address",type="string")
     * @SWG\Property(property="driver",type="string")
     * @SWG\Property(property="send_grid_token",type="string")
     * @SWG\Property(property="host",type="string")
     * @SWG\Property(property="port",type="string")
     * @SWG\Property(property="encryption",type="string")
     * @SWG\Property(property="username",type="string")
     * @SWG\Property(property="template",type="string")
     * @SWG\Property(property="additional_data",type="string")
     * @SWG\Property(property="password",type="datetime")
     * @SWG\Property(property="store_website_id",type="integer")
     */
    protected $fillable = [
        'from_name',
        'from_address',
        'incoming_driver',
        'driver',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'store_website_id',
        'send_grid_token',
        'recovery_phone',
        'recovery_email',
        'signature_name',
        'signature_title',
        'signature_email',
        'signature_phone',
        'signature_website',
        'signature_address',
        'signature_logo',
        'signature_image',
        'signature_social',
        'twilio_recovery_code',
    ];

    public function website(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function email_run_history(): HasMany
    {
        return $this->hasMany(EmailRunHistories::class, 'email_address_id', 'id');
    }

    public function email_assignes(): HasMany
    {
        return $this->hasMany(EmailAssign::class, 'email_address_id', 'id');
    }

    public function history_last_message(): HasOne
    {
        return $this->hasOne(EmailRunHistories::class, 'email_address_id', 'id')->latest();
    }

    public function history_last_message_error(): HasOne
    {
        return $this->hasOne(EmailRunHistories::class, 'email_address_id', 'id')->where('is_success', 0)->latest();
    }
}
