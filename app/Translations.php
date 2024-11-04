<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class Translations extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="text",type="string")
     * @SWG\Property(property="text_original",type="string")
     * @SWG\Property(property="from",type="string")
     * @SWG\Property(property="to",type="string")
     * @SWG\Property(property="created_at",type="datetime")
     * @SWG\Property(property="updated_at",type="datetime")
     */
    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $fillable = [
        'text',
        'text_original',
        'from',
        'to',
    ];

    /**
     * Protected Date
     *
     * @var array
     *
     * @param mixed $textOriginal
     * @param mixed $text
     * @param mixed $from
     * @param mixed $to
     */
    /**
     * This static method will create new translation
     */
    public static function addTranslation(string $textOriginal, string $text, string $from, string $to)
    {
        $obj                = new Translations();
        $obj->text_original = $textOriginal;
        $obj->text          = $text;
        $obj->from          = $from;
        $obj->to            = $to;

        $obj->save();
    }
}
