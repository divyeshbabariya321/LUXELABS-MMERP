<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UicheckUserAccess extends Model
{
    protected $fillable = ['user_id', 'uicheck_id', 'lock_developer', 'lock_admin'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function provideAccess($uicheck_id, $user_id)
    {
        try {
            UicheckUserAccess::updateOrCreate([
                'user_id' => $user_id,
                'uicheck_id' => $uicheck_id,
            ], []);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => 'Opps! Something went wrong, Please try again.']);
        }
    }
}
