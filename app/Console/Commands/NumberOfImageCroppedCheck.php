<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CroppedImageReference;
use App\Http\Controllers\WhatsAppController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class NumberOfImageCroppedCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'numberofimages:cropped';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there is 1000 images cropped in an hour , if no then it will send whatsapp message to number';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $date = new Carbon;

            $count = CroppedImageReference::where('created_at', '>', $date->subHours(1))->count();

            if ($count < 1000) {
                $message = 'Images are scraped less then 1000';
                app(WhatsAppController::class)->sendWithThirdApi('+918082488108', '', $message);
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
