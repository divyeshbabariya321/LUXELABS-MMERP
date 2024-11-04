<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPushInformation extends Model
{
    protected $table = 'product_push_informations';
    protected $guarded = [];

    protected $fillable = ['product_id', 'sku', 'status', 'quantity', 'stock_status', 'deleted_at', 'store_website_id', 'is_added_from_csv', 'real_product_id', 'is_available'];

    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::updated(function (ProductPushInformation $p) {
            $dirties      = $p->getDirty();
            $old_contents = $p->getOriginal();
            $user_id      = Auth::id();

            $old_arr    = [];
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id', 'real_product_id', 'is_available'];
            foreach ($old_contents as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;

                    continue;
                }
                if ($key === 'store_website_id') {
                    $old_arr['store_website_id'] = $oldValue;

                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
            }

            if (! empty($dirties['is_added_from_csv']) && ! empty($old_contents['is_added_from_csv'])) {
                $dirties['is_added_from_csv'] = ! $old_contents['is_added_from_csv'];
            } else {
                $dirties['is_added_from_csv'] = $old_contents['is_added_from_csv'];
            }

            if (! empty($dirties['is_available']) && ! empty($old_contents['is_available'])) {
                $dirties['is_available'] = ! $old_contents['is_available'];
            } else {
                $dirties['is_available'] = $old_contents['is_available'];
            }

            $new_values            = array_merge($old_arr, $dirties);
            $new_values['user_id'] = $user_id ?? 'command';
            unset($new_values['real_product_id']);
            unset($new_values['id']);
            ProductPushInformationHistory::create($new_values);
        });

        static::created(function (ProductPushInformation $p) {
            $old_arr    = [];
            $user_id    = Auth::id();
            $remove_key = ['deleted_at', 'created_at', 'updated_at', 'id', 'real_product_id', 'is_available', 'real_product_id'];
            foreach ($p->toArray() as $key => $oldValue) {
                if (in_array($key, $remove_key)) {
                    continue;
                }
                if ($key === 'product_id') {
                    $old_arr['product_id'] = $oldValue;

                    continue;
                }
                if ($key === 'store_website_id') {
                    $old_arr['store_website_id'] = $oldValue;

                    continue;
                }

                $old_arr['old_' . $key] = $oldValue;
                $old_arr['user_id']     = $user_id ?? 'command';
            }
            $old_arr['is_added_from_csv'] = $p->is_added_from_csv;
            $old_arr['is_available']      = $p->is_available;
            ProductPushInformationHistory::create($old_arr);
            $productSku = explode('-', $p->sku)[0];
        });
    }

    public function storeWebsite(): HasOne
    {
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'real_product_id');
    }

    public static function filterProductSku($categories, $brands)
    {
        $sku = [];
        if ($categories) {
            $categories = Category::whereIn('id', $categories)->get();
            foreach ($categories as $cat) {
                $products = $cat->products;
                if (count($products)) {
                    foreach ($products as $pro) {
                        if (! empty($pro->sku)) {
                            $sku[] = $pro->sku;
                        }
                    }
                }
            }
        }
        if ($brands) {
            $brands = Brand::with('products')->whereIn('id', $brands)->get();
            foreach ($brands as $b) {
                $products = $b->products;
                if (count($products)) {
                    foreach ($products as $pro) {
                        if (! empty($pro->sku)) {
                            $sku[] = $pro->sku;
                        }
                    }
                }
            }
        }

        return $sku;
    }
}
