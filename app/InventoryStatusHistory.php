<?php

namespace App;
use App\Supplier;
use App\Product;
use App\BrandScraperResult;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class InventoryStatusHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="in_stock",type="boolean")
     * @SWG\Property(property="product_id",type="integer")
     * @SWG\Property(property="date",type="datetime")
     * @SWG\Property(property="prev_in_stock",type="integer")
     * @SWG\Property(property="supplier_id",type="integer")
     */
    protected $fillable = ['product_id', 'date', 'in_stock', 'prev_in_stock', 'supplier_id'];

    public static function getInventoryHistoryFromProductId($product_id)
    {
        $columns = ['in_stock', 'prev_in_stock', 'date', 'supplier_id'];

        return InventoryStatusHistory::where('product_id', $product_id)->get($columns);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function product_count(): HasMany
    {
        return $this->hasMany(InventoryStatusHistory::class, 'supplier_id', 'supplier_id');
    }

    public function totalBrandsLink($date, $brandID = 0)
    {
        $supplier = $this->supplier;
        $scps     = [];
        if ($supplier) {
            $scrapers = $this->scrapers;
            if (! $scrapers->isEmpty()) {
                foreach ($scrapers as $scraper) {
                    $scps[] = $scraper->scraper_name;
                }
            }
        }

        $brandStatus = BrandScraperResult::whereDate('date', $date)->where('brand_id', $brandID)->whereIn('scraper_name', $scps)->groupBy('date')->select(DB::raw('SUM(total_urls) as count'))->first();

        return $brandStatus ? $brandStatus->count : 0;
    }
}
