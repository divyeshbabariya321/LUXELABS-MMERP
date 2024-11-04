<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Scraper;
use App\ScrapRemark;
use App\Services\Whatsapp\ChatApi\ChatApi;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckScrapersLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckScrapersLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'for error and empty log file';

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
            $yesterdayDate = date('d', strtotime('-1 days'));
            $root = config('env.SCRAP_LOGS_FOLDER');

            foreach (File::allFiles($root) as $file) {
                $needed = explode('-', $file->getFilename());
                if (isset($needed[1])) {
                    $day = explode('.', $needed[1]);
                    if ($day[0] === $yesterdayDate) {
                        $filePath = $root.'/'.$file->getRelativePath().'/'.$needed[0].'-'.$day[0].'.'.$day[1];
                        $result = File::get($filePath);
                        if (empty($result) ||
                            (strpos($result, 'exception') || strpos($result, 'Exception')) ||
                            (strpos($result, 'error') || strpos($result, 'Error'))) {
                            $suplier = Scraper::where('scraper_name', $needed[0])->first();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scraper query finished.']);
                            if (! is_null($suplier)) {
                                $user = User::where('id', $suplier->scraper_made_by)->first();
                                LogHelper::createCustomLogForCron($this->signature, ['message' => 'User query finished.']);
                                if (! is_null($user)) {
                                    $whatsappNumber = $user->phone;
                                    $message = 'scraper log file '.$filePath.' has issue.';
                                    $data = [
                                        'phone' => $whatsappNumber, // Receivers phone
                                        'body' => $message, // Message
                                    ];
                                    ChatApi::sendMessage($data);
                                    ScrapRemark::create([
                                        'scraper_name' => $suplier->scraper_name,
                                        'scrap_id' => 0,
                                        'module_type' => '',
                                        'remark' => $message,
                                    ]);
                                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scrap remark was added.']);
                                }
                            }
                        }
                    }
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
