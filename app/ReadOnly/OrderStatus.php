<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 7:12 PM
 */

namespace App\ReadOnly;

use App\ReadOnlyBase;

class OrderStatus extends ReadOnlyBase
{
    protected $data = [
        2  => 'Follow up for advance',
        4  => 'Proceed without advance',
        13 => 'Advance received',
        11 => 'Cancel',
        3  => 'Prepaid',
        7  => 'Product shipped from italy',
        14 => 'In Transist from Italy',
        9  => 'Product shipped to client',
        10 => 'Delivered',
        15 => 'Refund to be processed',
        16 => 'Refund Dispatched',
        17 => 'Refund Credited',
        18 => 'VIP',
        19 => 'HIGH PRIORITY',
    ];
}
