<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\SkuFormat;
class SkuFormatHistory extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="sku_format_id",type="integer")
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="old_sku_format",type="string")
     * @SWG\Property(property="sku_format",type="string")
     */
    protected $fillable = ['sku_format_id', 'old_sku_format', 'sku_format', 'user_id'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function skuFormat(): HasOne
    {
        return $this->hasOne(SkuFormat::class, 'id', 'sku_format_id');
    }
}
