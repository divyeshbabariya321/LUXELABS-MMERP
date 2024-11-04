<?php

namespace App;
use App\User;
use App\Email;
use App\Customer;
use App\ChatMessage;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\TicketsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Tickets extends Model
{
    use SoftDeletes;

    /**
     * @var string
     *
     * @SWG\Property(property="tickets",type="string")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="ticket_id",type="integer")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="message",type="string")
     * @SWG\Property(property="assigned_to",type="integer")
     * @SWG\Property(property="source_of_ticket",type="integer")
     * @SWG\Property(property="status_id",type="integer")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="phone_no",type="string")
     * @SWG\Property(property="type_of_inquiry",type="string")
     * @SWG\Property(property="order_no",type="string")
     * @SWG\Property(property="country",type="string")
     * @SWG\Property(property="last_name",type="string")
     * @SWG\Property(property="notify_on",type="string")
     * @SWG\Property(property="amount",type="float")
     * @SWG\Property(property="sku",type="string")
     */
    protected $fillable = [
        'customer_id', 'ticket_id', 'subject', 'message', 'assigned_to', 'source_of_ticket', 'status_id', 'date', 'name', 'email', 'phone_no', 'order_no',
        'type_of_inquiry', 'country', 'last_name', 'notify_on', 'amount', 'sku', 'lang_code',
    ];

    public function getTicketList($params = [])
    {
        $selectArray[] = $this->table.'.*';
        $query = DB::table($this->table);

        $query->select($selectArray);

        $record_per_page = (isset($params['record_per_page']) && $params['record_per_page'] != '' && $params['record_per_page'] > 0) ? $params['record_per_page'] : 10;

        return $query->paginate($record_per_page);
    }

    public function ticketStatus(): BelongsTo
    {
        return $this->belongsTo(TicketStatuses::class, 'status_id', 'id');
    }

    public function whatsappAll($needBroadcast = false): HasMany
    {
        if ($needBroadcast) {
            return $this->hasMany(ChatMessage::class, 'ticket_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(ChatMessage::class, 'ticket_id')->latest();
        }
    }

    public function sendMessageToSite(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'ticket_id')->where('send_to_tickets', 1)->select(['id', 'message', 'created_at'])->latest();
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    public function getImages()
    {
        return TicketsImages::where('ticket_id', $this->getAttribute('id'))->get();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id')->where('model_type', Tickets::class)->orderBy('created_at', 'desc');
    }
}
