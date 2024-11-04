<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\StoreWebsite;
use App\WebsiteLog;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WebsiteCreateLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:websitelog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create website error log from database file';

    public $webLog;

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $mainPath = config('settings.websites_logs_folder');
            $ifPathExist = file_exists($mainPath);
            if ($ifPathExist) {
                $this->info('Found Path');
                $filesDirectories = scandir($mainPath);
                foreach ($filesDirectories as $websiteName) {
                    // find the Directory
                    if (File::isDirectory($mainPath) && $websiteName != '.' && $websiteName != '..') {
                        $website = StoreWebsite::select('website')->where('website', 'like', '%'.$websiteName.'%')->first();
                        if (! $website) {
                            $this->info('Website not found '.$websiteName);
                        } else {
                            $fullPath = File::allFiles($mainPath);

                            foreach ($fullPath as $val) {
                                if (file_exists($mainPath.$websiteName.'/'.$val->getFilename()) && $val->getFilename() == 'debug.log') {

                                    if ($val->getFilename() == 'debug.log') {
                                        $fileTypeName = 'debug';
                                    } else {
                                        $fileTypeName = $val->getFilename();
                                    }

                                    $content = File::get($mainPath.$websiteName.'/'.$val->getFilename());
                                    $logs = preg_split('/\n\n/', $content);

                                    foreach ($logs as $log) {
                                        $entries = explode(PHP_EOL, $log);
                                        $sql = null;
                                        $time = null;
                                        $module = null;

                                        foreach ($entries as $entry) {
                                            if (strpos($entry, 'SQL') !== false) {
                                                $sql = str_replace('SQL:', '', $entry);
                                            }
                                            if (strpos($entry, 'TIME') !== false) {
                                                $time = str_replace('TIME:', '', $entry);
                                            }
                                            if (strpos($entry, '#8') !== false) {
                                                $module = str_replace('#8:', '', $entry);
                                            }
                                            if (! is_null($sql) && ! is_null($time) && ! is_null($module)) {
                                                $find = WebsiteLog::where([['sql_query', '=', $sql], ['time', '=', $time], ['module', '=', $module]])->first();
                                                if (empty($find)) {
                                                    $ins = new WebsiteLog;
                                                    $ins->sql_query = $sql;
                                                    $ins->time = $time;
                                                    $ins->module = $module;
                                                    $ins->website_id = $website->website ?? '';
                                                    $ins->type = $fileTypeName;
                                                    $ins->save();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $this->info('DB log not found for '.$websiteName);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->info('Cannot find the logs folder');
            }
            DB::commit();
            echo PHP_EOL.'=====DONE===='.PHP_EOL;
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());

            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL.'=====FAILED===='.PHP_EOL;
        }
    }
}
