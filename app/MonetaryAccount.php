<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Events\MonetaryAccountCreated;
use App\Events\MonetaryAccountUpdated;
use Illuminate\Database\Eloquent\Model;
use App\MonetaryAccountHistory;

class MonetaryAccount extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="currency",type="string")
     * @SWG\Property(property="amount",type="float")
     * @SWG\Property(property="type",type="string")
     * @SWG\Property(property="created_by",type="integer")
     * @SWG\Property(property="updated_by",type="integer")
     * @SWG\Property(property="short_note",type="string")
     * @SWG\Property(property="description",type="string")
     * @SWG\Property(property="other",type="string")
     */
    protected $fillable = ['name', 'date', 'currency', 'amount', 'type', 'created_by', 'updated_by', 'short_note', 'description', 'other'];

    protected $dispatchesEvents = [
        'created' => MonetaryAccountCreated::class,
        'updated' => MonetaryAccountUpdated::class,
    ];

    public function MonetaryAccountHistory(): MorphMany
    {
        return $this->morphMany(MonetaryAccountHistory::class, 'model');
    }
}
