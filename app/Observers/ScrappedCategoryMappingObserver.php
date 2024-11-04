<?php

namespace App\Observers;

use App\Category;
use App\ScrappedCategoryMapping;

class ScrappedCategoryMappingObserver
{
    protected function create($category)
    {
        $unKnownCategory   = Category::where('title', 'LIKE', '%Unknown Category%')->first();
        $unKnownCategories = explode(',', $unKnownCategory->references);
        $unKnownCategories = array_unique($unKnownCategories);

        $exist_data = ScrappedCategoryMapping::whereIn('name', $unKnownCategories)->get()->toArray();

        foreach ($unKnownCategories as $key => $val) {
            if (! in_array($val, $exist_data)) {
                ScrappedCategoryMapping::updateOrCreate([
                    'name' => $val,
                ], [
                    'name' => $val,
                ]);
            }
        }
    }
}
