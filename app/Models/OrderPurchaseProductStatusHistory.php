<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPurchaseProductStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'old_value', 'new_value',  'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newValue(): BelongsTo
    {
        return $this->belongsTo(OrderPurchaseProductStatus::class, 'new_value');
    }

    public function oldValue(): BelongsTo
    {
        return $this->belongsTo(OrderPurchaseProductStatus::class, 'old_value');
    }
}
