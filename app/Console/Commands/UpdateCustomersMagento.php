<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class UpdateCustomersMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:magento-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $options = [
                'trace' => true,
                'connection_timeout' => 120,
                'wsdl_cache' => WSDL_CACHE_NONE,
            ];

            $proxy = new \SoapClient(config('magentoapi.url'), $options);
            $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));
            $orderlist = $proxy->salesOrderList($sessionId);

            for ($j = 0; $j < count($orderlist); $j++) {
                $results = json_decode(json_encode($proxy->salesOrderInfo($sessionId, $orderlist[$j]->increment_id)), true);

                unserialize($results['items'][0]['product_options']);

                $full_name = $results['billing_address']['firstname'].' '.$results['billing_address']['lastname'];

                $customer_phone = (int) str_replace(' ', '', $results['billing_address']['telephone']);
                $final_phone = '';

                if ($customer_phone != null) {
                    if ($results['billing_address']['country_id'] == 'IN') {
                        if (strlen($customer_phone) <= 10) {
                            $customer_phone = '91'.$customer_phone;
                        }
                    }

                    $customer = Customer::where('phone', $customer_phone)->first();
                } else {
                    $customer = Customer::where('name', 'LIKE', "%$full_name%")->first();
                }

                if ($customer) {
                    dump("$j - UPDATING Customer");

                    if ($customer_phone != null) {
                        $final_phone = $customer_phone;
                    }

                    if ($customer->email == '' || $customer->address == '' || $customer->city == '' || $customer->country == '' || $customer->pincode == '') {
                        $customer->name = $full_name;
                        $customer->email = $results['customer_email'];
                        $customer->address = $results['billing_address']['street'];
                        $customer->city = $results['billing_address']['city'];
                        $customer->country = $results['billing_address']['country_id'];
                        $customer->pincode = $results['billing_address']['postcode'];
                        $customer->phone = $final_phone;
                    }

                    $customer->save();
                } else {
                    dump("$j - NOT UPDATING");
                }
                dump('______________');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
