<?php
/**
 * Created by PhpStorm.
 * User: mustafaflexwala
 * Date: 13/10/18
 * Time: 7:12 PM
 */

namespace App\ReadOnly;

use App\ReadOnlyBase;

class PurchaseStatus extends ReadOnlyBase
{
    protected $data = [
        'Replaced'                                => 'Replaced',
        'Pending Purchase'                        => 'Pending Purchase',
        'Request Sent to Supplier'                => 'Request Sent to Supplier',
        'Price under Negotiation'                 => 'Price under Negotiation',
        'Price Confirmed - Payment in Process'    => 'Price Confirmed - Payment in Process',
        'Payment Made - Awaiting Courier Details' => 'Payment Made - Awaiting Courier Details',
        'AWB Details Received'                    => 'AWB Details Received',
        'In Transit from Italy to Dubai'          => 'In Transit from Italy to Dubai',
        'Shipment Received in Dubai'              => 'Shipment Received in Dubai',
        'Shipment in Transit from Dubai to India' => 'Shipment in Transit from Dubai to India',
        'Shipment Received in India'              => 'Shipment Received in India',
    ];
}
