<?php

namespace App;
use App\Supplier;
use App\StatusChange;
use App\PurchaseProduct;
use App\Product;
use App\OrderProduct;
use App\Message;
use App\File;
use App\Email;
use App\Customer;
use App\CommunicationHistory;
use App\Agent;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="communication",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     */
    use SoftDeletes;

    protected $communication = '';

    protected $fillable = ['whatsapp_number'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'moduleid')->where('moduletype', 'purchase')->latest()->first();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'purchase_products', 'purchase_id', 'product_id');
    }

    public function orderProducts(): BelongsToMany
    {
        return $this->belongsToMany(OrderProduct::class, 'purchase_products', 'purchase_id', 'order_product_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'model_id')->where('model_type', Purchase::class);
    }

    public function purchase_supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id')->where('model_type', Purchase::class)->orWhere('model_type', Supplier::class);
    }

    public function status_changes(): HasMany
    {
        return $this->hasMany(StatusChange::class, 'model_id')->where('model_type', Purchase::class)->latest();
    }

    public function is_sent_in_italy()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Purchase::class)->where('type', 'purchase-in-italy')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_in_dubai()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Purchase::class)->where('type', 'purchase-in-dubai')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_dubai_to_india()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Purchase::class)->where('type', 'purchase-dubai-to-india')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_in_mumbai()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Purchase::class)->where('type', 'purchase-in-mumbai')->count();

        return $count > 0 ? true : false;
    }

    public function is_sent_awb_actions()
    {
        $count = $this->hasMany(CommunicationHistory::class, 'model_id')->where('model_type', Purchase::class)->where('type', 'purchase-awb-generated')->count();

        return $count > 0 ? true : false;
    }

    public function cashFlows(): MorphMany
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'purchase_order_customer', 'purchase_id', 'customer_id');
    }

    public function purchaseProducts(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class, 'purchase_id', 'id');
    }
}
