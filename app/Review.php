<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\ReviewSchedule;
use App\Account;
use App\Customer;
use App\StatusChange;
use App\Review;

class Review extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="account_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="posted_date",type="datetime")
     * @SWG\Property(property="review_link",type="string")
     * @SWG\Property(property="review",type="string")
     * @SWG\Property(property="serial_number",type="string")
     * @SWG\Property(property="platform",type="string")
     * @SWG\Property(property="title",type="string")
     */
    protected $fillable = [
        'account_id', 'customer_id', 'posted_date', 'review_link', 'review', 'serial_number', 'platform', 'title',
    ];

    public function review_schedule(): BelongsTo
    {
        return $this->belongsTo(ReviewSchedule::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', Review::class)->latest();
    }
}
