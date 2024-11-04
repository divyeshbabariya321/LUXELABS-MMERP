<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\StoreWebsite;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ReconsileBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconsile:brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconsile brand Everyday';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was updated.']);

            $storeWebsites = StoreWebsite::where('website_source', 'magento')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'store website query was finished.']);
            if (! $storeWebsites->isEmpty()) {
                foreach ($storeWebsites as $storeWebsite) {
                    $requestData = new Request;
                    $requestData->setMethod('POST');
                    $requestData->request->add(['store_website_id' => $storeWebsite->id]);
                    app('Modules\StoreWebsite\Http\Controllers\BrandController')->reconsileBrands($requestData);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
