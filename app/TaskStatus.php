<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="name",type="string")
     */
    protected $fillable = ['name'];

    public static $allDataArr = [];

    public static function dataArr()
    {
        if (self::$allDataArr) {
            return self::$allDataArr;
        }
        $temp             = self::orderBy('name')->pluck('name', 'id')->toArray();
        self::$allDataArr = $temp;
        foreach ($temp as $key => $value) {
            self::$allDataArr[$value] = $value;
        }

        return self::$allDataArr;
    }

    public static function printName($status)
    {
        $arr = self::dataArr();

        return $arr[$status] ?? '';
    }
}
