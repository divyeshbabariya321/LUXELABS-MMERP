<?php

namespace App;
use App\Setting;
use App\Http\Controllers\DevelopmentController;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CronJob extends Model
{
    const CRON_ISSUE_SUBJECT_PREFIX = 'URGENT-CRON-ISSUE-';

    const CRON_ISSUE_MODULE_NAME = 'Cron';

    const CRON_ISSUE_PRIORITY = 1;

    const CRON_ISSUE_STATUS = 'Planned';

    const DEFAULT_ASSIGNED_TO = 1;

    public function index()
    {
        $cron = CronJob::orderBy('id')->get();

        return $cron;
    }

    public static function insertLastError($signature, $error = '')
    {
        $cron = self::where('signature', $signature)->first();

        if (! $cron) {
            $cron            = new self;
            $cron->signature = $signature;
            $cron->schedule  = 'N/A';
        }
        $cron->last_status = 'error';
        $cron->error_count += 1;
        $cron->last_error = $error;
        $cron->save();

        $issueName = strtoupper(self::CRON_ISSUE_SUBJECT_PREFIX . $signature);

        $hasAssignedIssue = DeveloperTask::where('subject', $issueName)->where('is_resolved', 0)->first();

        if (! $hasAssignedIssue) {
            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add([
                'priority'    => self::CRON_ISSUE_PRIORITY,
                'issue'       => $error,
                'status'      => self::CRON_ISSUE_STATUS,
                'module'      => self::CRON_ISSUE_MODULE_NAME,
                'subject'     => $issueName,
                'assigned_to' => Setting::get('cron_issue_assinged_to', self::DEFAULT_ASSIGNED_TO),
            ]);
            app(DevelopmentController::class)->issueStore($requestData, 'issue');

            $cronJobErroLog            = new CronJobErroLog();
            $cronJobErroLog->signature = $signature;
            $cronJobErroLog->priority  = self::CRON_ISSUE_PRIORITY;
            $cronJobErroLog->error     = $error;
            $cronJobErroLog->error_count += 1;
            $cronJobErroLog->status      = self::CRON_ISSUE_STATUS;
            $cronJobErroLog->module      = self::CRON_ISSUE_MODULE_NAME;
            $cronJobErroLog->subject     = $issueName;
            $cronJobErroLog->assigned_to = Setting::get('cron_issue_assinged_to', self::DEFAULT_ASSIGNED_TO);
            $cronJobErroLog->save();
        }
        // once cron error done we need to assign that error to the related person
    }
}
