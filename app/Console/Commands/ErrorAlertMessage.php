<?php

namespace App\Console\Commands;

use App\CronJob;
use App\DeveloperTask;
use App\Helpers\LogHelper;
use App\Http\Controllers\DevelopmentController;
use App\LogKeyword;
use App\Setting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorAlertMessage extends Command
{
    const CRON_ISSUE_MODULE_NAME = '268';

    const CRON_ISSUE_PRIORITY = 1;

    const CRON_ISSUE_STATUS = 'Planned';

    const DEFAULT_ASSIGNED_TO = 1;

    //scrappersImagesDelete
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'errorAlertMessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to user 6 if error occured.';

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
            $filename = '/laravel-'.now()->format('Y-m-d').'.log';
            $path = storage_path('logs');
            $fullPath = $path.$filename;

            $content = File::get($fullPath);
            preg_match_all("/\[(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})\](.*)/", $content, $match);
            $logKeywords = LogKeyword::all();
            if (isset($match[7])) {
                foreach ($match[7] as $value) {
                    foreach ($logKeywords as $logKeyword) {
                        if (strpos(strtolower($value), strtolower($logKeyword->text)) !== false) {
                            $message = "You have error which matched the keyword  '".$logKeyword->text."'";
                            $message .= ' | '.$value;
                            $subject = "You have error which matched the keyword  '".$logKeyword->text."'";
                            $hasAssignedIssue = DeveloperTask::where('subject', 'like', "%{$subject}%")->whereDate('created_at', date('Y-m-d'))->where('is_resolved', 0)->first();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'developer task query finished.']);
                            if (! $hasAssignedIssue) {
                                $requestData = new Request;
                                $requestData->setMethod('POST');
                                $requestData->request->add([
                                    'log_keyword_id' => $logKeyword->id,
                                    'priority' => self::CRON_ISSUE_PRIORITY,
                                    'issue' => $message,
                                    'status' => self::CRON_ISSUE_STATUS,
                                    'module' => self::CRON_ISSUE_MODULE_NAME,
                                    'subject' => $subject,
                                    'assigned_to' => Setting::get('cron_issue_assinged_to', self::DEFAULT_ASSIGNED_TO),
                                ]);
                                app(DevelopmentController::class)->issueStore($requestData, 'issue');
                            }
                        }
                    }
                }
            }
            $this->output->write('Cron Done', true);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());

            $this->output->write('Error is caught here! => '.$e->getMessage(), true);
        }
    }
}
