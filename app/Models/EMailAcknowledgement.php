<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\EmailAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EMailAcknowledgement extends Model
{
    use HasFactory;

    protected $fillable = ['email_addresses_id', 'start_date', 'end_date', 'ack_message', 'ack_status', 'added_by'];

    public function email_address_record(): BelongsTo
    {
        return $this->belongsTo(EmailAddress::class, 'email_addresses_id');
    }
}
