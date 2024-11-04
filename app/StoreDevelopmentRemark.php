<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\User;
class StoreDevelopmentRemark extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="remarks",type="string")
     * @SWG\Property(property="store_development_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     */
    protected $fillable = ['remarks', 'store_development_id', 'user_id', 'admin_flagged', 'user_flagged'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
