<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\ChatMessage;
use App\Agent;

class Old extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="old",type="string")
     * @SWG\Property(property="serial_no",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="amount",type="integer")
     * @SWG\Property(property="commitment",type="string")
     * @SWG\Property(property="communication",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="is_blocked",type="boolean")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="gst",type="float")
     * @SWG\Property(property="account_number",type="integer")
     * @SWG\Property(property="account_iban",type="string")
     * @SWG\Property(property="account_swift",type="string")
     * @SWG\Property(property="catgory_id",type="integer")
     * @SWG\Property(property="pending_payment",type="string")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="account_name",type="string")
     * @SWG\Property(property="is_payable",type="boolean")
     */
    protected $table = 'old';

    protected $primaryKey = 'serial_no';

    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'amount', 'commitment', 'communication', 'status', 'is_blocked', 'phone', 'gst', 'account_number', 'account_iban', 'account_swift', 'catgory_id', 'pending_payment', 'currency', 'account_name', 'is_payable',
    ];

    /**
     * Protected Date
     *
     * @var array
     */
    /**
     * Get Status
     */
    public static function getStatus(): Response
    {
        return OldStatus::all()->pluck('status');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id', 'serial_no');
    }

    public function category(): HasOne
    {
        return $this->hasOne(OldCategory::class, 'id', 'category_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OldPayment::class, 'old_id', 'serial_no');
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        if ($needBroadCast) {
            return $this->hasMany(ChatMessage::class, 'old_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(ChatMessage::class, 'old_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'model_id')->where('model_type', Old::class);
    }
}
