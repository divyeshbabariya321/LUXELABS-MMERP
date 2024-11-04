<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorRatingQAStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'question_id', 'old_value', 'new_value', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newValue(): BelongsTo
    {
        return $this->belongsTo(VendorRatingQAStatus::class, 'new_value');
    }

    public function oldValue(): BelongsTo
    {
        return $this->belongsTo(VendorRatingQAStatus::class, 'old_value');
    }
}
