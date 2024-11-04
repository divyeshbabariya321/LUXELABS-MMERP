<?php

namespace App\Helpers;
use App\Currency;

use App\AutoReply;
use App\ChatMessage;
use App\CommunicationHistory;
use App\Customer;
use App\Email;
use App\Jobs\CallHelperForZeroStockQtyUpdate;
use App\Jobs\SendEmail;
use App\Mails\Manual\OrderConfirmation;
use App\Order;
use App\OrderCustomerAddress;
use App\OrderProduct;
use App\Product;
use App\ProductSizes;
use App\StoreWebsiteOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use seo2websites\MagentoHelper\MagentoHelperv2 as MagentoHelper;

class MagentoOrderHandleHelper extends Model
{
    /**
     * Create magento order
     *
     * @param Order [ object ], Website [ object ]
     * @param  mixed  $orders
     * @param  mixed  $website
     */
    public static function createOrder($orders, $website): bool
    {
        try {
            if (isset($orders->items)) {

                $totalOrders = $orders->items;
                foreach ($totalOrders as $order) {
                    $balance_amount = $order->base_grand_total;
                    $firstName = isset($order->customer_firstname) ? $order->customer_firstname : 'N/A';
                    $lastName = isset($order->customer_lastname) ? $order->customer_lastname : 'N/A';

                    $full_name = $firstName.' '.$lastName;
                    $customer_phone = '';

                    $customer = Customer::where('email', $order->customer_email)->where('store_website_id', $website->id)->first();
                    if (! $customer) {
                        $customer = new Customer;
                    }

                    $customer->name = $full_name;
                    $customer->email = $order->customer_email;
                    $customer->address = $order->billing_address->street[0];
                    $customer->city = $order->billing_address->city;
                    $customer->country = $order->billing_address->country_id;
                    $customer->pincode = $order->billing_address->postcode;
                    $customer->pincode = $order->billing_address->postcode;
                    $customer->store_website_id = $website->id;
                    $customer->save();

                    $customer_id = $customer->id;
                    $order_status = OrderHelper::$orderRecieved;
                    $payment_method = '';

                    if ($order->payment->method == 'paypal') {
                        if ($order->state == 'processing') {
                            $balance_amount = 0;
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }

                        $payment_method = 'paypal';
                    } elseif ($order->payment->method == 'banktransfer') {
                        if ($order->state == 'processing') {
                            $balance_amount = 0;
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }
                        $payment_method = 'banktransfer';
                    } elseif ($order->payment->method == 'cashondelivery') {
                        if ($order->state == 'processing') {
                            $balance_amount = 0;
                            $order_status = OrderHelper::$prepaid;
                        } else {
                            $order_status = OrderHelper::$followUpForAdvance;
                        }
                        $payment_method = 'cashondelivery';
                    }

                    $allStatus = OrderHelper::getStatus();

                    $magentoId = $order->increment_id;
                    $orderModel = Order::create(
                        [
                            'customer_id' => $customer_id,
                            'order_id' => $order->increment_id,
                            'order_magento_id' => $order->entity_id,
                            'order_type' => 'online',
                            'order_status' => isset($allStatus[$order_status]) ? $allStatus[$order_status] : $order_status,
                            'order_status_id' => $order_status,
                            'payment_mode' => $payment_method,
                            'order_date' => $order->created_at,
                            'client_name' => $order->billing_address->firstname.' '.$order->billing_address->lastname,
                            'city' => $order->billing_address->city,
                            'advance_detail' => $order->base_grand_total,
                            'contact_detail' => $order->billing_address->telephone,
                            'balance_amount' => $balance_amount,
                            'store_currency_code' => $order->order_currency_code,
                            'store_id' => $order->store_id,
                            'store_name' => $order->store_name,
                            'created_at' => $order->created_at,
                            'updated_at' => $order->created_at,
                            'payload' => json_encode($order),
                        ]
                    );

                    $id = $orderModel->id;

                    $items = $order->items;
                    foreach ($items as $item) {
                        if (round($item->price) > 0) {
                            $size = '';
                            if (! empty($item->product_size)) {
                                $size = $item->product_size;
                            }

                            $splitted_sku = explode('-', $item->sku);

                            $skuAndColor = MagentoHelper::getSkuAndColor($item->sku);
                            $sku = isset($splitted_sku[0]) ? $splitted_sku[0] : $skuAndColor['sku'];

                            OrderProduct::create([
                                'order_id' => $id,
                                'product_id' => ! empty($skuAndColor['product_id']) ? $skuAndColor['product_id'] : null,
                                'sku' => isset($splitted_sku[0]) ? $splitted_sku[0] : $skuAndColor['sku'],
                                'product_price' => round($item->price),
                                'currency' => $order->order_currency_code,
                                'eur_price' => Currency::convert(round($item->price), 'EUR', $order->order_currency_code),
                                'qty' => round($item->qty_ordered),
                                'size' => $size,
                                'color' => isset($splitted_sku[1]) ? $splitted_sku[1] : $skuAndColor['sku'],
                                'created_at' => $order->created_at,
                                'updated_at' => $order->created_at,
                                'item_id' => $item->item_id,
                            ]);

                            // check the splitted sku here to remove the stock from the products
                            $product = Product::where('sku', $sku)->select('id')->first();
                            $totalOrdered = round($item->qty_ordered);
                            if ($product->id) {
                                $productSizesM = ProductSizes::where('product_id', $product->id);
                                if (! empty($size)) {
                                    $productSizesM = $productSizesM->where('size', $size);
                                }
                                $mqty = 0;
                                $productSizesM = $productSizesM->get();
                                if (! $productSizesM->isEmpty()) {
                                    //check if more then one the minus else delete
                                    foreach ($productSizesM as $psm) {
                                        $mqty += $psm->quantity;
                                        if ($totalOrdered > 0) {
                                            // update qty as based on the request
                                            $psmqty = $psm->quantity;
                                            $psmqty -= $totalOrdered;
                                            if ($psmqty > 0) {
                                                $totalOrdered -= $psm->quantity;
                                                $psm->quantity = $psmqty;
                                                $psm->save();
                                            } else {
                                                $totalOrdered -= $psm->quantity;
                                                $psm->delete();
                                            }
                                        }
                                    }
                                }

                                if ($mqty <= $totalOrdered || $mqty == 0) {
                                    // start to delete from magento
                                    $needToCheck = [];
                                    $needToCheck[] = ['id' => $product->id, 'sku' => $item->sku];
                                    CallHelperForZeroStockQtyUpdate::dispatch($needToCheck)->onQueue('MagentoHelperForZeroStockQtyUpdate');
                                }
                            }
                        }
                    }

                    if (! empty($order->billing_address)) {
                        $customerAddress = [
                            [
                                'order_id' => $id ?? null,
                                'address_type' => $order->billing_address->address_type ?? null,
                                'city' => $order->billing_address->city ?? null,
                                'country_id' => $order->billing_address->country_id ?? null,
                                'customer_id' => $order->billing_address->customer_id ?? null,
                                'email' => $order->billing_address->email ?? null,
                                'entity_id' => $order->billing_address->entity_id ?? null,
                                'firstname' => $order->billing_address->firstname ?? null,
                                'lastname' => $order->billing_address->lastname ?? null,
                                'parent_id' => $order->billing_address->parent_id ?? null,
                                'postcode' => $order->billing_address->postcode ?? null,
                                'street' => $order->billing_address->street ? implode("\n", $order->billing_address->street) : null,
                                'telephone' => $order->billing_address->telephone ?? null,
                            ],
                        ];
                        try {
                            OrderCustomerAddress::insert($customerAddress);
                        } catch (\Throwable $th) {
                            //
                        }
                    }

                    if (! empty($order->shipping_address)) {
                        $customerAddress = [
                            [
                                'order_id' => $id ?? null,
                                'address_type' => $order->shipping_address->address_type ?? null,
                                'city' => $order->shipping_address->city ?? null,
                                'country_id' => $order->shipping_address->country_id ?? null,
                                'customer_id' => $order->shipping_address->customer_id ?? null,
                                'email' => $order->shipping_address->email ?? null,
                                'entity_id' => $order->shipping_address->entity_id ?? null,
                                'firstname' => $order->shipping_address->firstname ?? null,
                                'lastname' => $order->shipping_address->lastname ?? null,
                                'parent_id' => $order->shipping_address->parent_id ?? null,
                                'postcode' => $order->shipping_address->postcode ?? null,
                                'street' => $order->shipping_address->street ? implode("\n", $order->shipping_address->street) : null,
                                'telephone' => $order->shipping_address->telephone ?? null,
                            ],
                        ];
                        try {
                            OrderCustomerAddress::insert($customerAddress);
                        } catch (\Throwable $th) {
                            //
                        }
                    }

                    $orderSaved = Order::find($id);
                    
                    if ($order->payment->method == 'cashondelivery') {
                        $product_names = '';
                        foreach (OrderProduct::where('order_id', $id)->get() as $order_product) {
                            $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
                        }

                        $delivery_time = $orderSaved->estimated_delivery_date ? Carbon::parse($orderSaved->estimated_delivery_date)->format('d \of\ F') : Carbon::parse($orderSaved->order_date)->addDays(15)->format('d \of\ F');

                        $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();

                        $auto_message = str_replace('/{product_names}/i', $product_names, $auto_reply->reply);
                        $auto_message = str_replace('/{delivery_time}/i', $delivery_time, $auto_message);

                        $params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message' => $auto_message,
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;

                        $params['message'] = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;

                        $chat_message = ChatMessage::create($params);

                        CommunicationHistory::create([
                            'model_id' => $orderSaved->id,
                            'model_type' => Order::class,
                            'type' => 'initial-advance',
                            'method' => 'whatsapp',
                        ]);
                    } elseif ($orderSaved->order_status_id == OrderHelper::$prepaid) {
                        $params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply,
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;

                        CommunicationHistory::create([
                            'model_id' => $orderSaved->id,
                            'model_type' => Order::class,
                            'type' => 'online-confirmation',
                            'method' => 'whatsapp',
                        ]);
                    }

                    if ($order->state != 'processing' && $order->payment->method != 'cashondelivery') {
                        $autoReplyMsg = AutoReply::where('type', 'auto-reply')->where('keyword', 'order-payment-not-processed')->first();
                        $params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 1,
                            'status' => 2,
                            'customer_id' => $orderSaved->customer->id,
                            'message' => ($autoReplyMsg) ? $autoReplyMsg->reply : '',
                        ];

                        $chat_message = ChatMessage::create($params);

                        $whatsapp_number = $orderSaved->customer->whatsapp_number != '' ? $orderSaved->customer->whatsapp_number : null;
                    }

                    //Store Order Id Website ID and Magento ID

                    $websiteOrder = new StoreWebsiteOrder;
                    $websiteOrder->website_id = $website->id;
                    $websiteOrder->status_id = $order_status;
                    $websiteOrder->order_id = $orderSaved->id;
                    $websiteOrder->platform_order_id = $magentoId;
                    $websiteOrder->save();

                    $customer = $orderSaved->customer;

                    $emailClass = (new OrderConfirmation($orderSaved))->build();
                    try {
                        $email = Email::create([
                            'model_id' => $orderSaved->id,
                            'model_type' => Order::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $orderSaved->customer->email,
                            'subject' => $emailClass->subject,
                            'message' => $emailClass->render(),
                            'template' => 'order-confirmation',
                            'additional_data' => $orderSaved->id,
                            'status' => 'pre-send',
                            'is_draft' => 1,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    } catch (Exception $e) {
                        //
                    }

                }

                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }

        return false;
    }
}
