<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Account;
use App\Customer;
use App\Review;
class ReviewSchedule extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="account_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="posted_date",type="datetime")
     * @SWG\Property(property="platform",type="string")
     * @SWG\Property(property="review_count",type="string")
     * @SWG\Property(property="review_link",type="string")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = [
        'account_id', 'customer_id', 'date', 'posted_date', 'platform', 'review_count', 'review_link', 'status',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'review_schedule_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
