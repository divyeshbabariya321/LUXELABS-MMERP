<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJob;
use App\CronJobReport;
use App\Customer;
use App\Helpers\LogHelper;
use App\Product;
use App\SuggestedProduct;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendProductSuggestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:product-suggestion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates automatic product suggestions based on customer preferences';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $suggestions = SuggestedProduct::all();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'SuggestedProduct model query finished']);

            foreach ($suggestions as $suggestion) {
                $customer = Customer::find($suggestion->customer_id);

                if ($customer) {
                    $brands = json_decode($suggestion->brands);
                    $categories = json_decode($suggestion->categories);
                    $sizes = json_decode($suggestion->size);
                    $suppliers = json_decode($suggestion->supplier);

                    if ($brands[0] != null) {
                        $products = Product::whereIn('brand', $brands);
                    }

                    if ($categories[0] != null && $categories[0] != 1) {
                        if ($brands[0] != null) {
                            $products = $products->whereIn('category', $categories);
                        } else {
                            $products = Product::whereIn('category', $categories);
                        }
                    }

                    if ($sizes[0] != null) {
                        if ($brands[0] != null || ($categories[0] != 1 && $categories[0] != null)) {
                            $products = $products->where(function ($query) use ($sizes) {
                                foreach ($sizes as $size) {
                                    $query->orWhere('size', 'LIKE', "%$size%");
                                }

                                return $query;
                            });
                        } else {
                            $products = Product::where(function ($query) use ($sizes) {
                                foreach ($sizes as $size) {
                                    $query->orWhere('size', 'LIKE', "%$size%");
                                }

                                return $query;
                            });
                        }
                    }

                    if ($suppliers[0] != null) {
                        if ($brands[0] != null || ($categories[0] != 1 && $categories[0] != null) || $sizes[0] != null) {
                            $products = $products->whereHas('suppliers', function ($query) use ($suppliers) {
                                return $query->where(function ($q) use ($suppliers) {
                                    foreach ($suppliers as $supplier) {
                                        $q->orWhere('suppliers.id', $supplier);
                                    }
                                });
                            });
                        } else {
                            $products = Product::whereHas('suppliers', function ($query) use ($suppliers) {
                                return $query->where(function ($q) use ($suppliers) {
                                    foreach ($suppliers as $supplier) {
                                        $q->orWhere('suppliers.id', $supplier);
                                    }
                                });
                            });
                        }
                    }

                    if ($brands[0] == null && ($categories[0] == 1 || $categories[0] == null) && $sizes[0] == null && $suppliers[0] == null) {
                        $products = (new Product)->newQuery();
                    }

                    $products = $products->where('is_scraped', 1)->where('category', '!=', 1)->latest()->take($suggestion->number)->get();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Product model query finished']);

                    if (count($products) > 0) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Products records found']);

                        $params = [
                            'number' => null,
                            'user_id' => 6,
                            'approved' => 0,
                            'status' => 1,
                            'message' => 'Suggested images',
                            'customer_id' => $customer->id,
                        ];

                        $count = 0;

                        foreach ($products as $product) {
                            if (! $product->suggestions->contains($suggestion->id)) {
                                if ($image = $product->getMedia(config('constants.media_tags'))->first()) {
                                    if ($count == 0) {
                                        $chat_message = ChatMessage::create($params);

                                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved chat message record by ID:'.$chat_message->id]);
                                    }

                                    $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                                    $count++;
                                }

                                $product->suggestions()->attach($suggestion->id);
                            }
                        }
                    }
                } else {
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Deleted SuggestedProduct model query record by ID:'.$suggestion->id]);

                    $suggestion->products()->detach();
                    $suggestion->delete();
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
