<?php

namespace App\Console\Commands;

use App\AutoReply;
use App\ChatMessage;
use App\Colors;
use App\CommunicationHistory;
use App\CronJobReport;
use App\Customer;
use App\Helpers\OrderHelper;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class GetOrdersFromnMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getorders:magento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Orders From Magento And Store In Database Running Every Fifteen Minutes For Now';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = $this->initializeCronReport();
            $proxy = $this->initializeSoapClient();
            $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));
            $orderlist = $this->fetchOrders($proxy, $sessionId);

            foreach ($orderlist as $order) {
                $this->processOrder($proxy, $sessionId, $order);
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function initializeCronReport()
    {
        return CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);
    }

    private function initializeSoapClient()
    {
        $options = [
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
        ];

        return new \SoapClient(config('magentoapi.url'), $options);
    }

    private function fetchOrders($proxy, $sessionId)
    {
        $lastid = Setting::get('lastid');
        $filter = [
            'complex_filter' => [
                [
                    'key' => 'order_id',
                    'value' => ['key' => 'gt', 'value' => $lastid],
                ],
            ],
        ];

        return $proxy->salesOrderList($sessionId, $filter);
    }

    private function processOrder($proxy, $sessionId, $order)
    {
        $results = json_decode(json_encode($proxy->salesOrderInfo($sessionId, $order->increment_id)), true);
        $customer = $this->findOrCreateCustomer($results);
        $balanceAmount = $this->calculateBalance($results, $customer);
        $orderId = $this->createOrder($results, $customer, $balanceAmount);
        $this->processOrderProducts($results, $orderId);
        $this->handleOrderCommunication($results, $orderId);
        Setting::add('lastid', $order->order_id, 'int');
    }

    private function findOrCreateCustomer($orderData)
    {
        // Extract and refactor logic for finding or creating a customer
        $fullName = $orderData['billing_address']['firstname'].' '.$orderData['billing_address']['lastname'];
        $customerPhone = (int) str_replace(' ', '', $orderData['billing_address']['telephone']);

        if ($customerPhone) {
            if ($orderData['billing_address']['country_id'] == 'IN' && strlen($customerPhone) <= 10) {
                $customerPhone = '91'.$customerPhone;
            }


            $customer = Customer::where('phone', $customerPhone)->first();
        } else {
            $customer = Customer::where('name', 'LIKE', "%$fullName%")->first();
        }


        if (! $customer) {
            $customer = new Customer;
            $customer->name = $fullName;
            $customer->email = $orderData['customer_email'];
            $customer->address = $orderData['billing_address']['street'];
            $customer->city = $orderData['billing_address']['city'];
            $customer->country = $orderData['billing_address']['country_id'];
            $customer->pincode = $orderData['billing_address']['postcode'];
            $customer->phone = $this->validatePhone(['phone' => $customerPhone ?: self::generateRandomString()]);
            $customer->save();
        }

        return $customer;
    }

    private function calculateBalance($orderData, $customer)
    {
        $paid = $orderData['total_paid'] ?? 0;
        $balanceAmount = $orderData['base_grand_total'] - $paid;

        if ($customer->credit > 0) {
            if (($balanceAmount - $customer->credit) < 0) {
                $customer->credit = ($balanceAmount - $customer->credit) * -1;
                $balanceAmount = 0;
            } else {
                $balanceAmount -= $customer->credit;
                $customer->credit = 0;
            }
            $customer->save();
        }

        return $balanceAmount;
    }

    private function createOrder($orderData, $customer, $balanceAmount)
    {
        return Order::insertGetId([
            'customer_id' => $customer->id,
            'order_id' => $orderData['increment_id'],
            'order_type' => 'online',
            'order_status' => $this->determineOrderStatus($orderData),
            'payment_mode' => $this->determinePaymentMethod($orderData),
            'order_date' => $orderData['created_at'],
            'client_name' => $orderData['billing_address']['firstname'].' '.$orderData['billing_address']['lastname'],
            'city' => $orderData['billing_address']['city'],
            'advance_detail' => $orderData['total_paid'] ?? 0,
            'contact_detail' => $customer->phone,
            'balance_amount' => $balanceAmount,
            'created_at' => $orderData['created_at'],
            'updated_at' => $orderData['created_at'],
        ]);
    }

    private function processOrderProducts($orderData, $orderId)
    {
        $size = '';
        foreach ($orderData['items'] as $item) {
            if (round($item['price']) > 0) {
                $skuAndColor = self::getSkuAndColor($item['sku']);
                OrderProduct::insert([
                    'order_id' => $orderId,
                    'product_id' => $skuAndColor['product_id'] ?? null,
                    'sku' => $skuAndColor['sku'],
                    'product_price' => round($item['price']),
                    'qty' => round($item['qty_ordered']),
                    'size' => $size,
                    'color' => $skuAndColor['color'],
                    'created_at' => $orderData['created_at'],
                    'updated_at' => $orderData['created_at'],
                ]);
            }
        }
    }

    private function handleOrderCommunication(Order $order, array $results): void
    {
        if ($results['payment']['method'] == 'cashondelivery') {
            $product_names = '';
            foreach (OrderProduct::where('order_id', $order->id)->get() as $order_product) {
                $product_names .= $order_product->product ? $order_product->product->name.', ' : '';
            }

            $delivery_time = $order->estimated_delivery_date ?
                Carbon::parse($order->estimated_delivery_date)->format('d \of\ F') :
                Carbon::parse($order->order_date)->addDays(15)->format('d \of\ F');

            $auto_reply = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-confirmation')->first();
            $auto_message = str_replace('/{product_names}/i', $product_names, $auto_reply->reply);
            $auto_message = str_replace('/{delivery_time}/i', $delivery_time, $auto_message);

            $params = [
                'number' => null,
                'user_id' => 6,
                'approved' => 1,
                'status' => 2,
                'customer_id' => $order->customer->id,
                'message' => $auto_message,
            ];

            ChatMessage::create($params);
            $params['message'] = AutoReply::where('type', 'auto-reply')->where('keyword', 'cod-online-followup')->first()->reply;
            ChatMessage::create($params);

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'initial-advance',
                'method' => 'whatsapp',
            ]);
        } elseif ($order->order_status_id == OrderHelper::$prepaid && $results['state'] == 'processing') {
            $params = [
                'number' => null,
                'user_id' => 6,
                'approved' => 1,
                'status' => 2,
                'customer_id' => $order->customer->id,
                'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'prepaid-order-confirmation')->first()->reply,
            ];

            ChatMessage::create($params);

            CommunicationHistory::create([
                'model_id' => $order->id,
                'model_type' => Order::class,
                'type' => 'online-confirmation',
                'method' => 'whatsapp',
            ]);
        }

        if ($results['state'] != 'processing' && $results['payment']['method'] != 'cashondelivery') {
            $params = [
                'number' => null,
                'user_id' => 6,
                'approved' => 1,
                'status' => 2,
                'customer_id' => $order->customer->id,
                'message' => AutoReply::where('type', 'auto-reply')->where('keyword', 'order-payment-not-processed')->first()->reply,
            ];

            ChatMessage::create($params);
        }
    }

    private function determineOrderStatus(array $results): string
    {
        $order_status = '';

        switch ($results['payment']['method']) {
            case 'paypal':
            case 'banktransfer':
            case 'cashondelivery':
                $order_status = ($results['state'] == 'processing') ?
                    OrderHelper::$prepaid :
                    OrderHelper::$followUpForAdvance;
                break;
        }

        return $order_status;
    }

    private function determinePaymentMethod(array $results): string
    {
        $payment_method = '';

        switch ($results['payment']['method']) {
            case 'paypal':
                $payment_method = 'paypal';
                break;
            case 'banktransfer':
                $payment_method = 'banktransfer';
                break;
            case 'cashondelivery':
                $payment_method = 'cashondelivery';
                break;
        }

        return $payment_method;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function validatePhone($phone)
    {
        $validator = Validator::make($phone, [
            'phone' => 'unique:customers,phone',
        ]);

        if ($validator->fails()) {
            $phone['phone'] = self::generateRandomString();

            self::validatePhone($phone);
        }

        return $phone['phone'];
    }

    public static function getSkuAndColor($original_sku)
    {
        $result = [];
        $colors = (new Colors)->all();

        $splitted_sku = explode('-', $original_sku);

        foreach ($colors as $color) {
            if (strpos($splitted_sku[0], $color)) {
                $result['color'] = $color;
                $sku = str_replace($color, '', $splitted_sku[0]);

                $product = Product::where('sku', 'LIKE', "%$sku%")->select('id', 'sku')->first();

                if ($product) {
                    $result['product_id'] = $product->id;
                    $result['sku'] = $product->sku;
                } else {
                    $result['sku'] = $sku;
                }

                return $result;
            }
        }

        $result['color'] = null;
        $sku = $splitted_sku[0];

        $product = Product::where('sku', 'LIKE', "%$sku%")->select('id', 'sku')->first();

        if ($product) {
            $result['product_id'] = $product->id;
            $result['sku'] = $product->sku;
        } else {
            $result['sku'] = $sku;
        }

        return $result;
    }
}
