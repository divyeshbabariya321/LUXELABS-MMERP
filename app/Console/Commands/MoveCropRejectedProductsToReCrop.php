<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class MoveCropRejectedProductsToReCrop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recrop:send-to-recrop-and-fix';

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

            $products = Product::where('is_crop_rejected', 1)->where('crop_remark', 'LIKE', '%sequence%')->get();

            foreach ($products as $key => $product) {
                dump('Reverting....'.$key);
                $product->is_crop_approved = 1;
                $product->crop_approved_at = Carbon::now()->toDateTimeString();
                $product->crop_approved_by = 109;
                $product->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
