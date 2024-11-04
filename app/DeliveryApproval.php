<?php

namespace App;
use App\Voucher;
use App\User;
use App\StatusChange;
use App\PrivateView;
use App\Order;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class DeliveryApproval extends Model
{
    use Mediable;

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', DeliveryApproval::class)->latest();
    }

    public function private_view(): BelongsTo
    {
        return $this->belongsTo(PrivateView::class);
    }
}
