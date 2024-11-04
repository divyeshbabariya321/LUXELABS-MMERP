<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFeedbackRemark extends Model
{
    use HasFactory;

    public $fillable = [
        'user_feedback_category_id',
        'user_feedback_vendor_id',
        'remarks',
        'added_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
