<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class DescriptionChange extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="keyword",type="string")
     * @SWG\Property(property="replace_with",type="string")
     */
    protected $fillable = [
        'keyword',
        'replace_with',
    ];

    public static function getErpName($name)
    {
        $mc   = self::all();
        $text = $name;
        foreach ($mc as $replace) {
            if (strpos($name, $replace->keyword) !== false) {
                $text = str_replace(strtolower($replace->keyword), strtolower($replace->replace_with), strtolower($name));
            }
        }

        return ucwords($text);
    }

    public static function replaceKeyword($description)
    {
        // Split the description into individual words
        $words = explode(' ', $description);

        // Use whereIn to find all matching keywords
        $matchingKeywords = self::whereIn('keyword', $words)->get(['keyword', 'replace_with'])->toArray();

        if (! empty($matchingKeywords)) {
            // Replace matching keywords in the $description string
            foreach ($matchingKeywords as $matchingKeyword) {
                $description = str_replace($matchingKeyword['keyword'], $matchingKeyword['replace_with'], $description);
            }

            return $description;
        } else {
            return $description;
        }
    }
}
