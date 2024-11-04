<?php

namespace App\Console\Commands;

use App\CronJob;
use App\Helpers\LogHelper;
use App\Http\Controllers\DevelopmentController;
use App\ProjectFileManager;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ProjectDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project_directory:manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It go through with all directories and update to db';

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
            // start throgh tree program
            $output = shell_exec("tree --du -h . -f -L 6 --sort=size|grep 'M]\|G]' | egrep -v '[0-9]*\.[0-9]M'");
            if (! empty($output)) {
                $lastFolder = null;
                $this->info('Output : '.$output);

                foreach (explode(PHP_EOL, $output) as $o) {
                    $directory = explode(']', $o);
                    if (isset($directory[0]) && isset($directory[1])) {
                        $directoryStr = trim($directory[1]);
                        $parent = null;
                        if (substr($directoryStr, 0, strlen($lastFolder)) == $lastFolder) {
                            $parent = $lastFolder;
                        }

                        $size = explode(']', (explode('[', $o)[1]))[0];
                        if (strpos($size, 'G') !== false) {
                            $size = str_replace('G', '', $size);
                            $size = $size * 1024;
                        } elseif (strpos($size, 'M') !== false) {
                            $size = str_replace('M', '', $size);
                        }

                        $projectManager = ProjectFileManager::where('name', $directoryStr)->first();
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Project file manager query was finished.']);
                        if ($projectManager) {
                            $projectManager->size = trim($size);
                            $projectManager->save();
                            if (str_replace('M', '', $projectManager->size) > str_replace('M', '', $projectManager->notification_at) && ! empty($projectManager->notification_at)) {
                                $requestData = new Request;
                                $requestData->setMethod('POST');
                                $requestData->request->add(['priority' => 1, 'issue' => "Error With folder size {$directoryStr} which is more then {$projectManager->size} and expected size is {$projectManager->notification_at}", 'status' => 'Planned', 'module' => 'cron', 'subject' => "Error With folder size {$directoryStr}", 'assigned_to' => 6]);
                                app(DevelopmentController::class)->issueStore($requestData, 'issue');
                            }
                        } else {
                            ProjectFileManager::create(['name' => $directoryStr, 'project_name' => 'erp', 'size' => trim($size), 'parent' => $parent]);
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Project file manager was added.']);
                        }
                        $lastFolder = $directoryStr;
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
