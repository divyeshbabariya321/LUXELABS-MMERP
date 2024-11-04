<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FixSpecialCharactersInDescription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-special-characters';

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

            Product::where('is_approved', 0)->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    dump($product->id);
                    $description = str_replace(['&nbsp;', '\n', "\n", '&eacute;', '&egrave;', '&Egrave;'], ' ', $product->short_description);
                    $composition = str_replace(['&nbsp;', '\n', "\n", '&eacute;', '&egrave;', '&Egrave;'], ' ', $product->composition);
                    $product->short_description = $description;
                    $product->composition = $composition;
                    $product->save();
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
