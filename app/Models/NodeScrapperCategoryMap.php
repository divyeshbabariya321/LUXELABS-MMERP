<?php

namespace App\Models;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class NodeScrapperCategoryMap extends Model
{
    protected $casts = [
        'category_stack'    => 'array',
        'product_urls'      => 'array',
        'mapped_categories' => 'array',
    ];

    public function getCategoryStackDisplayAttribute()
    {
        if ($this->category_stack && is_array($this->category_stack)) {
            return implode(' > ', $this->category_stack);
        } else {
            return '';
        }
    }

    public function getProductUrlsDisplayAttribute()
    {
        if ($this->product_urls && is_array($this->product_urls)) {
            return implode(' , ', $this->product_urls);
        } else {
            return '';
        }
    }

    public function categories()
    {
        if (is_array($this->mapped_categories)) {
            $new_arr = [];
            foreach ($this->mapped_categories as $category_id) {
                $cat = Category::find($category_id)->select('id', 'title');
                if ($cat) {
                    $new_arr[] = $cat->title;
                }
            }

            return $new_arr;
        } else {
            return null;
        }
    }

    public function getMappedCategoryDisplayAttribute()
    {
        $cat = $this->categories();
        if ($cat && is_array($cat)) {
            return implode(' > ', $cat);
        } else {
            return '';
        }
    }
}
