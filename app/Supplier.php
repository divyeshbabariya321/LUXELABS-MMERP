<?php

namespace App;
use App\User;
use App\SupplierStatus;
use App\SupplierOrderInquiryData;
use App\SupplierCategory;
use App\Scraper;
use App\Purchase;
use App\Product;
use App\InventoryStatusHistory;
use App\Email;
use App\ChatMessage;
use App\Agent;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="is_updated",type="boolean")
     * @SWG\Property(property="supplier",type="string")
     * @SWG\Property(property="size_system_id",type="integer")

     * @SWG\Property(property="address",type="string")

     * @SWG\Property(property="phone",type="string")
     * @SWG\Property(property="default_phone",type="string")
     * @SWG\Property(property="whatsapp_number",type="string")
     * @SWG\Property(property="email",type="string")
     * @SWG\Property(property="default_email",type="string")
     * @SWG\Property(property="social_handle",type="string")
     * @SWG\Property(property="instagram_handle",type="string")
     * @SWG\Property(property="website",type="string")
     * @SWG\Property(property="gst",type="string")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="supplier_category_id",type="integer")
     * @SWG\Property(property="supplier_sub_category_id",type="integer")
     * @SWG\Property(property="scrapper",type="string")
     * @SWG\Property(property="supplier_status_id",type="integer")
     * @SWG\Property(property="is_blocked",type="boolean")
     * @SWG\Property(property="supplier_price_range_id",type="integer")
     * @SWG\Property(property="est_delivery_time",type="datetime")
     */
    use SoftDeletes;

    protected $fillable = [
        'is_updated',
        'supplier',
        'size_system_id',
        'address',
        'language_id',
        'phone',
        'default_phone',
        'whatsapp_number',
        'email',
        'default_email',
        'social_handle',
        'instagram_handle',
        'website',
        'gst',
        'status',
        'supplier_category_id',
        'supplier_sub_category_id',
        'scrapper',
        'supplier_status_id',
        'is_blocked',
        'supplier_price_range_id',
        'est_delivery_time',
        'product_type',
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::saving(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
        self::creating(function ($model) {
            if (! empty(Auth::id())) {
                $model->updated_by = Auth::id();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'notes' => 'array',
        ];
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'model_id')->where('model_type', Supplier::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_suppliers', 'supplier_id', 'product_id');
    }

    public function myproducts(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(InventoryStatusHistory::class, 'supplier_id', 'id');
    }

    public function lastProduct(): HasOne
    {
        return $this->hasOne(Product::class, 'supplier_id', 'id')->latest();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'model_id')->where(function ($query) {
            $query->where('model_type', Purchase::class)->orWhere('model_type', Supplier::class);
        });
    }

    public function whatsapps(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'supplier_id')->whereNotIn('status', ['7', '8', '9'])->latest();
    }

    public function category(): HasMany
    {
        return $this->hasMany(SupplierCategory::class);
    }

    public function supplier_category(): BelongsTo
    {
        return $this->belongsTo(SupplierCategory::class, 'supplier_category_id', 'id');
    }

    public function status(): BelongsToMany
    {
        return $this->belongsToMany(SupplierStatus::class, 'supplier_status', 'supplier_status_id', 'id');
    }

    public function whatsappAll($needBroadCast = false): HasMany
    {
        if ($needBroadCast) {
            return $this->hasMany(ChatMessage::class, 'supplier_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(ChatMessage::class, 'supplier_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function whoDid(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function scraperMadeBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'scraper_madeby');
    }

    public function scraperParent(): HasOne
    {
        return $this->hasOne(Supplier::class, 'id', 'scraper_parent_id');
    }

    public function scraper(): HasOne
    {
        return $this->hasOne(Scraper::class, 'supplier_id', 'id');
    }

    public function scrapers(): HasMany
    {
        return $this->hasMany(Scraper::class, 'supplier_id', 'id');
    }

    public function getSupplierExcelFromSupplierEmail()
    {
        if ($this->scraper != null) {
            if (strpos($this->scraper->scraper_name, 'excel') !== false) {
                return $this->scraper->scraper_name;
            }
        }
        $supplier_array = [
            'birba_excel', 'colognese_excel', 'cologneseSecond_excel', 'cologneseThird_excel',
            'cologneseFourth_excel', 'distributionet_excel', 'gru_excel', 'ines_excel', 'le-lunetier_excel',
            'lidia_excel', 'maxim_gucci_excel', 'lidiafirst_excel', 'modes_excel', 'mv1_excel', 'tory_excel', 'valenti_excel', 'dna_excel', 'master',
        ];
        foreach ($supplier_array as $supp) {
            $supp = str_replace('_excel', '', $supp);
            if (strpos($this->email, $supp) !== false) {
                if ($supp != 'master') {
                    return $supplier = $supp . '_excel';
                } else {
                    return $supplier = $supp;
                }
            }
        }

        return $supplier = 'master';
    }

    /**
     *  Get information by ids
     *
     *  @param []
     * @param mixed $ids
     * @param mixed $fields
     * @param mixed $toArray
     *
     *  @return mixed
     */
    public static function getInfoByIds($ids, $fields = ['*'], $toArray = false)
    {
        $list = self::whereIn('id', $ids)->select($fields)->get();

        if ($toArray) {
            $list = $list->toArray();
        }

        return $list;
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id', 'id');
    }

    // START - Purpose : Product Inquiry Data -DEVTASK-4048
    public function inquiryproductdata(): HasMany
    {
        return $this->hasMany(SupplierOrderInquiryData::class, 'supplier_id', 'id');
    }
    // END -DEVTASK-4048

    public function supplier_detail(): BelongsTo
    {
        return $this->belongsTo(CodeShortcut::class);
    }

    public static function getProductSuppliers()
    {
        $list = self::select('suppliers.id', 'suppliers.supplier')
                    ->join('product_suppliers', 'suppliers.id', '=', 'product_suppliers.supplier_id')
                    ->groupBy('product_suppliers.supplier_id')
                    ->get();

        return $list;
    }
}
