<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MergeDuplicateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:duplicate-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merging duplicate products';

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

            $products = Product::selectRaw('sku, COUNT(*) as duplicates')->groupBy('sku')->having('duplicates', '>', 1)->get();

            foreach ($products as $key => $product) {
                dump("$key - Product");

                $duplicates = Product::where('sku', $product->sku)->get();

                foreach ($duplicates as $key2 => $duplicate) {
                    if ($key2 == 0) {
                        dump("$key - $key2 - Main Product");

                        $main_product = $duplicate;
                    } else {
                        if ($duplicate->purchases()->count() > 0) {
                            dump("$key - $key2 - Transferring Purchases");

                            foreach ($duplicate->purchases as $purchase) {
                                $main_product->purchases()->syncWithoutDetaching($purchase);
                            }

                            $duplicate->purchases()->detach();
                        }

                        if ($duplicate->references()->count() > 0) {
                            dump("$key - $key2 - Transferring References");

                            foreach ($duplicate->references as $reference) {
                                $reference->product_id = $main_product->id;
                                $reference->save();
                            }

                            $duplicate->references()->delete();
                        }

                        if ($duplicate->suggestions()->count() > 0) {
                            dump("$key - $key2 - Transferring Suggestions");

                            foreach ($duplicate->suggestions as $suggestion) {
                                $main_product->suggestions()->syncWithoutDetaching($suggestion);
                            }

                            $duplicate->suggestions()->detach();
                        }

                        if ($duplicate->private_views()->count() > 0) {
                            dump("$key - $key2 - Transferring Private Views");

                            foreach ($duplicate->private_views as $private_view) {
                                $main_product->private_views()->syncWithoutDetaching($private_view);
                            }

                            $duplicate->private_views()->detach();
                        }

                        if ($duplicate->user()->count() > 0) {
                            dump("$key - $key2 - Transferring Users");

                            foreach ($duplicate->user as $user) {
                                $main_product->user()->syncWithoutDetaching($user);
                            }

                            $duplicate->user()->detach();
                        }

                        if ($duplicate->hasMedia(config('constants.media_tags'))) {
                            dump("$key - $key2 - Has Images");

                            foreach ($duplicate->getMedia(config('constants.media_tags')) as $image) {
                                $image_path = $image->getAbsolutePath();

                                if (File::exists($image_path)) {
                                    dump("$key - $key2 - Deleting Image on server");
                                    File::delete($image_path);
                                    // unlink($image_path);
                                }

                                $image->delete();
                            }
                        } else {
                            dump("$key - $key2 - NO IMAGES");
                        }

                        $duplicate->suppliers()->detach();
                        $duplicate->forceDelete();
                    }
                }

                dump('------------------');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
