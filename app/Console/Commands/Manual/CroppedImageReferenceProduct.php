<?php

namespace App\Console\Commands\Manual;

use App\CronJob;
use App\CronJobReport;
use App\CroppedImageReference;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CroppedImageReferenceProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crop-reference:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach Crop Reference to Product';

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
            //Getting Images
            $images = CroppedImageReference::where('product_id', 0)->get();

            foreach ($images as $image) {
                //Getting Media Id
                $media = $image->original_media_id;

                //Searching From Media Table
                $mediable = DB::table('mediables')->where('mediable_type', Product::class)->where('media_id', $media)->first();

                //Media is not null
                if ($mediable != null) {
                    //Getting product
                    $product = Product::select('id')->where('id', $mediable->mediable_id)->first();
                    $cropped = CroppedImageReference::find($image->id);
                    $cropped->product_id = $product->id;
                    $cropped->save();
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
