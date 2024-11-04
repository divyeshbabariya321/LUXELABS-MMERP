<?php

namespace App;
use App\Product;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Mediables extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="mediables",type="string")
     */

    public $timestamps = false;

    public static function getMediasFromProductId($product_id)
    {
        $columns = ['directory', 'filename', 'extension', 'disk', 'created_at'];

        return  Mediables::leftJoin('media as m', function ($query) {
            $query->on('media_id', 'm.id');
        })->where('mediable_id', $product_id)->where('mediable_type', Product::class)->get($columns);
    }
}
