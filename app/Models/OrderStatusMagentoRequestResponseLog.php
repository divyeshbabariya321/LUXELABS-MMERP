<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatusMagentoRequestResponseLog extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'order_product_id', 'request', 'response', 'url', 'method', 'status_code', 'time_taken', 'start_time', 'end_time', 'message', 'method_name', 'updated_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->select('name', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id')->select('order_id', 'id');
    }
}
