<?php

namespace App\Console\Commands;

use App\CronJob;
use App\scraperImags;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class CompareScrapperImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare-scrapper-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare Scrapper Images';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            Log::info('Start Compare Scrapper Images');

            $scraperImagsData = scraperImags::where('compare_flag', 0)->where('url', '!=', '')->orderByDesc('id')->get();
            if (! empty($scraperImagsData)) {
                foreach ($scraperImagsData as $scraperImag) {
                    if (! empty($scraperImag->img_name)) {
                        Log::info('Main - '.$scraperImag->url);

                        $scraperImagscData = scraperImags::where('url', $scraperImag->url)->where('id', '!=', $scraperImag->id)->orderByDesc('id')->first();

                        if (! empty($scraperImagscData)) {
                            Log::info($scraperImagscData->url);

                            if (! empty($scraperImagscData->img_name) && ! empty($scraperImag->img_name)) {
                                // Load the images
                                $image1 = asset('scrappersImages/'.$scraperImag->img_name);
                                $image2 = asset('scrappersImages/'.$scraperImagscData->img_name);

                                // Calculate hashes for both images
                                $imageHash = new ImageHash(new DifferenceHash);
                                $hash1 = $imageHash->hash($image1);
                                $hash2 = $imageHash->hash($image2);

                                // Compare the hashes
                                $hammingDistance = $hash1->distance($hash2);
                                $similarityThreshold = 5; // Set a threshold for similarity

                                if ($hammingDistance <= $similarityThreshold) {
                                    $scraperImagscData->manually_approve_flag = 0;
                                    $scraperImagscData->si_status = 2;
                                } else {
                                    $scraperImagscData->manually_approve_flag = 1;
                                }
                            }

                            $scraperImagscData->compare_flag = 1;

                            $scraperImagscData->save();
                        }
                    }
                }
            }

            Log::info('End Compare Scrapper Images');
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
