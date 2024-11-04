<?php

namespace App\Hubstaff;
use App\User;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubstaffMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bill_rate',
        'created_at',
        'currency',
        'hubstaff_user_id',
        'id',
        'pay_rate',
        'updated_at',
        'user_id',
        'email',
    ];

    public static function linkUser($hubstaffId, $userId)
    {
        self::where('hubstaff_user_id', $hubstaffId)
            ->update([
                'user_id' => $userId,
            ]);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
