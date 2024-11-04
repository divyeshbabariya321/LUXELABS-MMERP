<?php

namespace App\Console\Commands;

use App\DeveloperTask;
use App\Http\Controllers\WhatsAppController;
use App\Models\ScrapedProductMissingLog;
use App\ScrapedProducts;
use App\Scraper;
use App\ScrapLog;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class FetchScrapeMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:FetchScrapeMissing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Scrape Missing Quatity';

    const MISSING_DATA = 'missing data';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $date = date('Y-m-d');
        $scrapped_query = ScrapedProducts::selectRaw(' count(*) as total_product ,
				   sum(CASE WHEN category = "" OR category IS NULL THEN 1 ELSE 0 END) AS missing_category,
			       sum(CASE WHEN color = "" OR color IS NULL THEN 1 ELSE 0 END) AS missing_color,
			       sum(CASE WHEN composition = "" OR composition IS NULL THEN 1 ELSE 0 END) AS missing_composition,
			       sum(CASE WHEN title = "" OR title IS NULL THEN 1 ELSE 0 END) AS missing_name,
			       sum(CASE WHEN description = "" OR description IS NULL THEN 1 ELSE 0 END) AS missing_short_description,
			       sum(CASE WHEN price = "" OR price IS NULL THEN 1 ELSE 0 END) AS missing_price,
			       sum(CASE WHEN size = "" OR size IS NULL THEN 1 ELSE 0 END) AS missing_size,
			       supplier,
			       id,
			       website
				')
            ->where('website', '<>', '')
            ->whereRaw(" date(created_at) = date('$date') ");
        $scrapped_query = $scrapped_query->groupBy('website')->havingRaw('missing_category > 1 or missing_color > 1 or missing_composition > 1 or missing_name > 1 or missing_short_description >1 ');

        $scrappedReportData = $scrapped_query->get();
        foreach ($scrappedReportData as $d) {
            $missingdata = '';
            $data = [
                'website' => $d->website,
                'total_product' => $d->total_product,
                'missing_category' => $d->missing_category,
                'missing_color' => $d->missing_color,
                'missing_composition' => $d->missing_composition,
                'missing_name' => $d->missing_name,
                'missing_short_description' => $d->missing_short_description,
                'missing_price' => $d->missing_price,
                'missing_size' => $d->missing_size,
                'created_at' => date('Y-m-d H:m'),
            ];

            $missingdata .= 'Total Product - '.$d->total_product.', ';
            $missingdata .= 'Missing Category - '.$d->missing_category.', ';
            $missingdata .= 'Missing Color - '.$d->missing_color.', ';
            $missingdata .= 'Missing Composition - '.$d->missing_composition.', ';
            $missingdata .= 'Missing Name - '.$d->missing_name.', ';
            $missingdata .= 'Missing Short Description - '.$d->missing_short_description.', ';
            $missingdata .= 'Missing Price - '.$d->missing_price.', ';
            $missingdata .= 'Missing Size - '.$d->missing_size.', ';

            $scrapers = Scraper::where('scraper_name', $d->website)->get();
            foreach ($scrapers as $scrapperDetails) {
                $hasAssignedIssue = DeveloperTask::where('scraper_id', $scrapperDetails->id)
                    ->whereNotNull('assigned_to')->where('is_resolved', 0)->first();
                if ($hasAssignedIssue != null) {
                    $userName = User::where('id', $hasAssignedIssue->assigned_to)->pluck('name')->first();
                    $requestData = new Request;
                    $requestData->setMethod('POST');
                    $requestData->request->add(['issue_id' => $hasAssignedIssue->id, 'message' => 'Missing data', 'status' => 1]);
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => self::MISSING_DATA, 'log_messages' => $missingdata]);
                    try {
                        app(WhatsAppController::class)->sendMessage($requestData, 'issue');
                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => self::MISSING_DATA, 'log_messages' => $missingdata.' and message sent to '.$userName]);
                    } catch (Exception $e) {
                        ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => self::MISSING_DATA, 'log_messages' => "Coundn't send message to ".$userName]);
                    }
                } else {
                    ScrapLog::create(['scraper_id' => $scrapperDetails->id, 'type' => self::MISSING_DATA, 'log_messages' => 'Not assigned to any user']);
                }
            }

            $s = ScrapedProductMissingLog::where('website', $d->website)
                ->whereRaw(" date(created_at) = date('$date') ")->first();
            if ($s) {
                ScrapedProductMissingLog::where('id', $s->id)->update($data);
            } else {
                ScrapedProductMissingLog::insert($data);
            }
        }
    }
}
