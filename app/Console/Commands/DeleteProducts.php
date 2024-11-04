<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:products';

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

            $products = Product::withTrashed()->where('supplier', 'Les Market')->get();
            foreach ($products as $key => $product) {
                dump("$key - Product");

                if ($product->hasMedia(config('constants.media_tags'))) {
                    dump("$key - Has Images");

                    foreach ($product->getMedia(config('constants.media_tags')) as $image) {
                        $image_path = $image->getAbsolutePath();

                        if (File::exists($image_path)) {
                            dump("$key - Deleting Image on server");
                            File::delete($image_path);
                            // unlink($image_path);
                        }

                        $image->delete();
                    }
                } else {
                    dump("$key - NO IMAGES");
                }

                $product->suppliers()->detach();

                if ($product->user()) {
                    dump('user');
                    $product->user()->detach();
                }

                $product->references()->delete();
                $product->suggestions()->detach();
                $product->forceDelete();
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
