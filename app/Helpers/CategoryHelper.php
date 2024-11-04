<?php

namespace App\Helpers;

class CategoryHelper
{
    public static function getParentCategoryNamesRecursive($category)
    {
        $names = [];
        $names[] = $category->title;
        $parent = $category->getSupParent;
        if ($parent) {
            while ($parent) {
                $names[] = $parent->title;
                $parent = $parent->getSupParent;
            }
        }

        return implode(' > ', array_reverse($names));
    }
}
