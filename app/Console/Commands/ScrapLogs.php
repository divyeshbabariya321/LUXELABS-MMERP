<?php

namespace App\Console\Commands;

use App\Scraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScrapLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraplogs:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All scraplogs insert to the databases';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $searchVal = '';
        $files = File::allFiles(config('env.SCRAP_LOGS_FOLDER'));

        $yesterdayDate = date('j', strtotime('-1 days'));
        foreach ($files as $val) {
            $day_of_file = explode('-', $val->getFilename());
            if (Str::contains(end($day_of_file), $yesterdayDate) && (Str::contains($val->getFilename(), $searchVal) || empty($searchVal))) {
                $file_path_new = config('env.SCRAP_LOGS_FOLDER').'/'.$val->getRelativepath().'/'.$val->getFilename();

                $file = file($file_path_new);

                $log_msg = '';
                for ($i = max(0, count($file) - 100); $i < count($file); $i++) {
                    $log_msg .= $file[$i];
                }
                if ($log_msg == '') {
                    $log_msg = 'Log data not found.';
                }
                $file_path_info = pathinfo($val->getFilename());

                $search_scraper = substr($file_path_info['filename'], 0, -3);
                $search_scraper = str_replace('-', '_', $search_scraper);
                $scrapers_info = Scraper::select('id')
                    ->where('scraper_name', 'like', $search_scraper)
                    ->get();

                if (count($scrapers_info) > 0) {
                    $scrap_logs_info = self::select('id', 'scraper_id')
                        ->where('scraper_id', '=', $scrapers_info[0]->id)
                        ->get();
                    $scrapers_id = $scrapers_info[0]->id;
                } else {
                    $scrapers_id = 0;
                }

                if (isset($scrap_logs_info) && count($scrap_logs_info) == 0) {
                    $file_list_data = [
                        'scraper_id' => $scrapers_id,
                        'folder_name' => $val->getRelativepath(),
                        'file_name' => $val->getFilename(),
                        'log_messages' => $log_msg,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    self::insert($file_list_data);
                }
            }
        }
    }
}
