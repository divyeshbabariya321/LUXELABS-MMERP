<?php

namespace App;
use App\Account;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class FacebookPost extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="account_id",type="integer")
     * @SWG\Property(property="caption",type="string")
     * @SWG\Property(property="post_body",type="string")
     * @SWG\Property(property="post_by",type="string")
     * @SWG\Property(property="posted_on",type="datetime")
     * @SWG\Property(property="status",type="string")
     */
    use Mediable;

    protected $fillable = [
        'account_id',
        'caption',
        'post_body',
        'post_by',
        'posted_on',
        'status',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
