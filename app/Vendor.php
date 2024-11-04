<?php

namespace App;
use App\VendorStatusDetail;
use App\VendorProduct;
use App\VendorCategory;;
use App\Models\VendorFrameworks;
use App\Email;
use App\ChatMessage;
use App\Agent;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="address",type="string")
     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="default_phone",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="social_handle",type="string")
     * @SWG\Property(property="website",type="string")
     * @SWG\Property(property="login",type="string")
     * @SWG\Property(property="password",type="string")
     * @SWG\Property(property="gst",type="string")
     * @SWG\Property(property="account_name",type="string")
     * @SWG\Property(property="account_iban",type="string")
     * @SWG\Property(property="is_blocked",type="boolean")
     * @SWG\Property(property="frequency",type="string")
     * @SWG\Property(property="reminder_last_reply",type="string")
     * @SWG\Property(property="reminder_message",type="string")
     * @SWG\Property(property="frequency_of_payment",type="string")
     * @SWG\Property(property="updated_by",type="integer")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="bank_name",type="string")
     * @SWG\Property(property="bank_address",type="string")
     * @SWG\Property(property="city",type="string")
     * @SWG\Property(property="country",type="string")
     * @SWG\Property(property="staifsc_codetus",type="string")
     * @SWG\Property(property="remark",type="string")
     */
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'address',
        'phone',
        'default_phone',
        'whatsapp_number',
        'email',
        'social_handle',
        'website',
        'login',
        'password',
        'gst',
        'account_name',
        'account_swift',
        'account_iban',
        'is_blocked',
        'frequency',
        'reminder_message',
        'reminder_last_reply',
        'reminder_from',
        'updated_by',
        'status',
        'feeback_status',
        'frequency_of_payment', 'bank_name', 'bank_address', 'city', 'country', 'ifsc_code', 'remark', 'chat_session_id', 'type', 'framework', 'flowcharts', 'flowchart_date', 'fc_status', 'question_status', 'rating_question_status', 'price', 'currency', 'price_remarks',
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::saving(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::creating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(VendorProduct::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'model_id')->where('model_type', Vendor::class);
    }

    public function chat_messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderByDesc('id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VendorCategory::class);
    }

    public function vendorStatusDetail(): BelongsTo
    {
        return $this->belongsTo(VendorStatusDetail::class, 'vendor_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        if ($needBroadCast) {
            return $this->hasMany(ChatMessage::class, 'vendor_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(ChatMessage::class, 'vendor_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id', 'id');
    }

    public function whatsappLastTwentyFourHours(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->orderByDesc('id');
    }

    /**
     *  Get information by ids
     *
     *  @param []
     * @param mixed $ids
     * @param mixed $fields
     * @param mixed $toArray
     *
     *  @return mixed
     */
    public static function getInfoByIds($ids, $fields = ['*'], $toArray = false)
    {
        $list = self::whereIn('id', $ids)->select($fields)->get();

        if ($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(VendorFrameworks::class, 'framework');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'vendor_id')
            ->orderByDesc('created_at')
            ->select('vendor_id', 'message', 'status', 'created_at')
            ->limit(1);
    }
}
