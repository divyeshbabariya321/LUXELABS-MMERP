<?php

namespace App\Console\Commands\Manual;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\StatusHelper;
use App\ScrapedProducts;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class GetProductImageForScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:image-scraper {website?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the images for product';

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
            //Getting All Products
            if (! empty($this->argument('website'))) {
                $scrapedProducts = ScrapedProducts::select('images')->with('product')->where('website', $this->argument('website'))->get();
            } else {
                // $scrapedProducts = ScrapedProducts::all();
                $scrapedProducts = ScrapedProducts::select('images')->with('product')->get();
            }

            foreach ($scrapedProducts as $scrapedProduct) {
                //get products from scraped products
                $product = $scrapedProduct->product;

                //check if scraped product has product
                if ($product != null && $product != '') {
                    //check if product has media
                    if ($product->hasMedia(\Config('constants.media_tags'))) {
                        dump('Product has media');
                    } else {
                        //check if scrapedProduct has images
                        if ($scrapedProduct->images == null && $scrapedProduct->images == '') {
                            continue;
                        }
                        //if product does not have media loop over images
                        $countImageUpdated = 0;
                        foreach ($scrapedProduct->images as $image) {
                            //check if image has http or https link
                            if (strpos($image, 'http') === false) {
                                continue;
                            }

                            try {
                                //generating image from image
                                $jpg = \Image::make($image)->encode('jpg');
                            } catch (Exception $e) {
                                // if images are null
                                $jpg = null;
                            }
                            if ($jpg != null) {
                                $filename = substr($image, strrpos($image, '/'));
                                $filename = str_replace(['/', '.JPEG', '.JPG', '.jpeg', '.jpg', '.PNG', '.png'], '', $filename);

                                //save image to media
                                $media = MediaUploader::fromString($jpg)->toDirectory('/product/'.floor($product->id / 10000).'/'.$product->id)->useFilename($filename)->upload();
                                $product->attachMedia($media, config('constants.media_tags'));
                                $countImageUpdated++;
                            }
                        }
                        if ($countImageUpdated != 0) {
                            // Call status update handler
                            StatusHelper::updateStatus($product, StatusHelper::$autoCrop);
                            dump('images saved for product ID '.$product->id);
                        }
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
