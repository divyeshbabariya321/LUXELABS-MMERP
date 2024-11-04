<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\DeliveryApproval;
use App\OrderProduct;
use App\Product;
use App\StatusChange;

class PrivateView extends Model
{
    use Mediable;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function delivery_approval(): HasOne
    {
        return $this->hasOne(DeliveryApproval::class);
    }

    public function order_product(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'private_view_products', 'private_view_id', 'product_id');
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', PrivateView::class)->latest();
    }
}
