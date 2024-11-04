<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\KeywordToCategory;
use App\Models\CustomerWithCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class ImportCustomerToCategoryByKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers-by-keyword-to-category';

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

            $keywordsToCategories = KeywordToCategory::all();

            Customer::where('is_categorized_for_bulk_messages', 0)->with('messageHistory')->chunk(100, function ($customers) use ($keywordsToCategories) {
                foreach ($customers as $customer) {
                    $customerLastThreeMessages = $customer->messageHistory;
                    foreach ($customerLastThreeMessages as $message) {
                        foreach ($keywordsToCategories as $keywordsToCategory) {
                            if (stripos(strtolower($message->message), strtolower($keywordsToCategory->keyword_value)) !== false) {
                                $customer->is_categorized_for_bulk_messages = 1;
                                $customer->save();
                                $this->saveCustomerWithCategory($customer, $keywordsToCategory);
                                break 2;
                            }
                        }
                    }
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function saveCustomerWithCategory($customer, $keywordToCategory)
    {
        CustomerWithCategory::where('customer_id', $customer->id)->delete();
        CustomerWithCategory::insert([
            'customer_id' => $customer->id,
            'category_type' => $keywordToCategory->category_type,
            'model_id' => $keywordToCategory->model_id,
        ]);
    }
}
