<?php

namespace App\Services\Listing;

class SizesChecker implements CheckerInterface
{
    private static $allowedSizes = [
        'S', 'L', 'M', 'XL', 'XXL', 'XS',
        '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43',
        '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57',
        'TU', 'UNI', 'ONE SIZE',
    ];

    public function check($product)
    {
        $data = $product->size;
        if (! $data) {
            return false;
        }

        return true;
    }

    public function improvise($data, $data2 = null)
    {
        if (in_array($data, self::$allowedSizes)) {
            return $data; // Return the valid size
        }
    
        return 'Size not recognized'; // Provide a default message
    }
}
