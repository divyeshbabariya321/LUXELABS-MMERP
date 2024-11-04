<?php

namespace App\Console\Commands;

use App\ColdLeads;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Helpers\LogHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use App\Marketing\WhatsappConfig;

class MoveColdLeadsToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold-leads:move-to-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move cold leads to the customer databas';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report was added.']);

            // Get cold leads
            $coldLeads = ColdLeads::where('is_imported', 1)->where('customer_id', null)->inRandomOrder()->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cold leads query was finished.']);

            // Set count to 0 and maxcount to 50
            $count = 0;
            $maxCount = 500000;

            // Get all numbers from config
            $config = WhatsappConfig::getWhatsappConfigs();

            // Loop over numbers
            $arrCustomerNumbers = [];
            foreach ($config as $arrNumber) {
                if ($arrNumber['customer_number']) {
                    $arrCustomerNumbers[] = $arrNumber['customer_number'];
                }
            }

            // Loop over coldLeads
            if ($coldLeads !== null) {
                foreach ($coldLeads as $coldLead) {
                    // Reached maxCount
                    if ($count >= $maxCount) {
                        return;
                    }

                    // Add cold lead to customers table
                    if (! $coldLead->customer && ! $coldLead->whatsapp) {
                        // Check for existing customer
                        $customer = Customer::where('phone', $coldLead->platform_id)->get();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer query was finished.']);

                        // Nothing found?
                        if ($customer == null && ! empty($coldLead->name)) {
                            // Create new customer
                            $customer = new Customer;
                            $customer->name = $coldLead->name;
                            $customer->phone = $coldLead->platform_id;
                            $customer->whatsapp_number = $arrCustomerNumbers[rand(0, count($arrCustomerNumbers) - 1)];
                            $customer->city = $coldLead->address;
                            $customer->country = 'IN';
                            try {
                                $customer->save();
                            } catch (Exception $e) {
                                echo $e->getMessage();
                                $coldLead->customer_id = 1;
                                $coldLead->save();
                            }

                            if (! empty($customer->id)) {
                                $coldLead->customer_id = $customer->id;
                                $coldLead->save();
                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer saved.']);
                            }
                        } else {
                            $coldLead->customer_id = 1;
                            $coldLead->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Customer saved.']);
                        }

                        $count++;
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
