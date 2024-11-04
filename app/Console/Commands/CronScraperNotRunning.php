<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Http\Controllers\WhatsAppController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CronScraperNotRunning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:not-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to admin if scraper is not running.';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Create cron job report
        try {

            CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // Get all suppliers
            $sql = '
            SELECT
                s.id,
                s.supplier,
                sp.scraper_name,
                MAX(ls.last_inventory_at) AS last_update,
                sp.scraper_name,
                sp.inventory_lifetime
            FROM
                suppliers s
            JOIN
                scrapers sp on sp.supplier_id = s.id
            LEFT JOIN
                scraped_products ls
            ON
                ls.website=sp.scraper_name
            WHERE
                s.supplier_status_id=1
            GROUP BY
                s.id
            HAVING
                last_update < DATE_SUB(NOW(), INTERVAL sp.inventory_lifetime DAY) OR
                last_update IS NULL
            ORDER BY
                s.supplier
        ';
            $allSuppliers = DB::select($sql);

            // Do we have results?
            if (count($allSuppliers) > 0) {
                // Loop over suppliers
                foreach ($allSuppliers as $supplier) {
                    // Create message
                    $message = '['.date('d-m-Y H:i:s').'] Scraper not running: '.$supplier->supplier;

                    // Output debug message
                    dump('Scraper not running: '.$supplier->supplier);

                    // Try to send message
                    try {
                        // Output debug message
                        dump('Sending message');

                        // Send message
                        app(WhatsAppController::class)->sendWithThirdApi('34666805119', '971502609192', $message);
                        app(WhatsAppController::class)->sendWithThirdApi('971569119192', '971502609192', $message);
                    } catch (Exception $e) {
                        // Output error
                        dump($e->getMessage());
                    }
                }
            }

        } catch (Exception $e) {

            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
