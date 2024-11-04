<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class ColorNamesReference extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="color_code",type="string")
     * @SWG\Property(property="color_name",type="string")
     */
    protected $fillable = ['color_code', 'color_name'];

    public static function getProductColorFromObject($productObject)
    {
        $mainColorNames = ColorNamesReference::distinct('color_name')->get(['color_name', 'erp_name']);

        if ($colorName = self::findColorInProperties($productObject, $mainColorNames)) {
            return $colorName;
        }

        if ($colorName = self::findColorInAttributes($productObject, $mainColorNames)) {
            return $colorName;
        }

        return '';
    }

    private static function findColorInProperties($productObject, $mainColorNames)
    {
        if (isset($productObject->properties['color']) || isset($productObject->properties->color)) {
            $colorRef = $productObject->properties['color'];
            if (! empty($colorRef) && is_string($colorRef)) {
                foreach ($mainColorNames as $colorName) {
                    if (self::colorMatch($colorRef, $colorName)) {
                        return $colorName->erp_name;
                    }
                }
                self::addNewColorReference($colorRef);
            }
        }

        return null;
    }

    private static function findColorInAttributes($productObject, $mainColorNames)
    {
        $attributes = ['url', 'title', 'description'];
        foreach ($attributes as $attribute) {
            if (! empty($productObject->$attribute)) {
                foreach ($mainColorNames as $colorName) {
                    if (self::colorMatch(self::_replaceKnownProblems($productObject->$attribute), $colorName)) {
                        return $colorName->erp_name;
                    }
                }
            }
        }

        return null;
    }

    private static function colorMatch($colorRef, $colorName)
    {
        return ! empty($colorName->color_name) && stristr($colorRef, $colorName->color_name);
    }

    private static function addNewColorReference($colorRef)
    {
        $existingReference = ColorNamesReference::where('color_name', $colorRef)->first();
        if (! $existingReference) {
            ColorNamesReference::create([
                'color_code' => '',
                'color_name' => $colorRef,
            ]);
        }
    }

    public static function getColorRequest($color = '', $url = '', $title = '', $description = '')
    {
        $mainColorNames = ColorNamesReference::distinct('color_name')->get(['color_name', 'erp_name']);

        if ($colorName = self::findColorMatch($color, $mainColorNames)) {
            return $colorName;
        }

        self::addNewColorReferences($color);

        $attributes = [$url, $title, $description];
        foreach ($attributes as $attribute) {
            if ($colorName = self::findColorMatch(self::_replaceKnownProblems($attribute), $mainColorNames)) {
                return $colorName;
            }
        }

        return '';
    }

    private static function findColorMatch($text, $mainColorNames)
    {
        if (empty($text)) {
            return null;
        }

        foreach ($mainColorNames as $colorName) {
            if (! empty($colorName->color_name) && stristr($text, $colorName->color_name)) {
                return $colorName->erp_name;
            }
        }

        return null;
    }

    private static function addNewColorReferences($color)
    {
        if (! empty($color)) {
            $existingReference = ColorNamesReference::where('color_name', $color)->first();
            if (! $existingReference) {
                ColorNamesReference::create([
                    'color_code' => '',
                    'color_name' => $color,
                ]);
            }
        }
    }

    private static function _replaceKnownProblems($text)
    {
        $knownProblems = ['off-white', 'off+white', 'off%20white', 'off white', 'offwhite'];

        return str_ireplace($knownProblems, '', $text);
    }
}
