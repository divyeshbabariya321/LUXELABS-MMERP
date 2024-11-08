<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Product;
use App\Services\Products\SizeReferences;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RefineSizes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sizes:refine';

    private $enricher;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SizeReferences $enricher)
    {
        $this->enricher = $enricher;
        parent::__construct();
    }

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

            Product::where('size', '!=', '')->where('is_crop_ordered', 1)->whereNotNull('size')->where('is_approved', '0')->chunk(1000, function ($products) {
                foreach ($products as $product) {
                    $this->enricher->basicRefining($product);
                    sleep(0.2);
                    $this->enricher->refineSizeToPintFive($product);
                    sleep(0.2);
                    $this->enricher->refineSizeForIt($product);
                }
            });

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
