<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\CronJob;
use App\CroppedImageReference;
use App\DatabaseHistoricalRecord;
use App\DatabaseTableHistoricalRecord;
use App\Job;
use App\Library\Github\GithubClient;
use App\LogRequest;
use App\MemoryUsage;
use App\ProductPushErrorLog;
use App\ProjectFileManager;
use App\Scraper;
use App\ScraperProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Laravel\Horizon\Contracts\JobRepository;

class MasterDevTaskController extends Controller
{
    public function index(Request $request): View
    {
        $enddate = date('Y-m-d 23:59:59');
        $startdate = date('Y-m-d 00:00:00', strtotime('-7 day', strtotime($enddate)));

        $productErrors = ProductPushErrorLog::latest('count')->groupBy('message')->select(DB::raw('*,COUNT(message) AS count'));
        $productErrors->whereDate('created_at', '>=', $startdate)->whereDate('created_at', '<=', $enddate);
        $productErrors->where('response_status', '!=', 'success');
        $productErrors = $productErrors->get();

        $memory_use = MemoryUsage::whereDate('created_at', now()->format('Y-m-d'))
            ->orderByDesc('used')
            ->first();

        $currentSize = DatabaseHistoricalRecord::orderByDesc('created_at')->first();
        $sizeBefore = null;
        if (! empty($currentSize)) {
            $sizeBefore = DatabaseHistoricalRecord::whereRaw(DB::raw("DATE(created_at) = DATE('".$currentSize->created_at."' - INTERVAL 1 DAY)"))
                ->first();
        }

        $topFiveTables = DatabaseTableHistoricalRecord::whereDate('created_at', date('Y-m-d'))->groupBy('database_name')->orderByDesc('size')->limit(5)->get();
        // find the open branches
        $repoArr = [];
        $github = new GithubClient;
        $repository = $github->getRepository();

        if (! empty($repository)) {
            foreach ($repository as $i => $repo) {
                $repoId = $repo->full_name;
                $pulls = $github->getPulls($repoId, 'q=is%3Aopen+is%3Apr');
                $repoArr[$i]['name'] = $repoId;
                if (! empty($pulls)) {
                    foreach ($pulls as $pull) {
                        $repoArr[$i]['pulls'][] = [
                            'title' => $pull->title,
                            'no' => $pull->number,
                            'url' => $pull->html_url,
                            'user' => $pull->user->login,
                        ];
                    }
                }
            }
        }
        $cronjobReports = null;

        $cronjobReports = CronJob::join('cron_job_reports as cjr', 'cron_jobs.signature', 'cjr.signature')
            ->where('cjr.start_time', '>', DB::raw('NOW() - INTERVAL 24 HOUR'))
            ->where('cron_jobs.last_status', 'error')
            ->groupBy('cron_jobs.signature')
            ->get();

        $scraper1hrsReports = null;
        $scraper1hrsReports = CroppedImageReference::where('created_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 1 HOUR)'))->select(
            [DB::raw('count(*) as cnt')]
        )->first();
        $scraper24hrsReports = null;
        $scraper24hrsReports = CroppedImageReference::where('created_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 24 HOUR)'))->select(
            [DB::raw('count(*) as cnt')]
        )->first();

        $last3HrsMsg = null;
        $last24HrsMsg = null;

        $last3HrsMsg = ChatMessage::where('created_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 3 HOUR)'))->select(
            [DB::raw('count(*) as cnt')]
        )->first();

        $last24HrsMsg = ChatMessage::where('created_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 24 HOUR)'))->select(
            [DB::raw('count(*) as cnt')]
        )->first();

        $threehours = strtotime(date('Y-m-d H:i:s', strtotime('-3 hours')));
        $twentyfourhours = strtotime(date('Y-m-d H:i:s', strtotime('-24 hours')));

        $last3HrsJobs = Job::where('created_at', '>=', $threehours)->select(
            [DB::raw('count(*) as cnt')]
        )->first();

        $last24HrsJobs = Job::whereDate('created_at', '>=', $twentyfourhours)->select(
            [DB::raw('count(*) as cnt')]
        )->first();

        // Get scrape data
        $sql = '
            SELECT
                s.id,
                s.supplier,
                COUNT(ls.id) AS total,
                SUM(IF(ls.validated=0,1,0)) AS failed,
                SUM(IF(ls.validated=1,1,0)) AS validated,
                SUM(IF(ls.validation_result LIKE "%[error]%",1,0)) AS errors
            FROM
                suppliers s
            JOIN
                scrapers sc
            ON 
                sc.supplier_id = s.id    
            JOIN
                scraped_products ls 
            ON  
                sc.scraper_name=ls.website
            WHERE
                ls.website != "internal_scraper" AND
                ls.last_inventory_at > DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)
            ORDER BY
                sc.scraper_priority desc
        ';
        $scrapeData = DB::select($sql);

        //DB Image size management#3118
        // $projectDirectorySql = 'select * FROM `project_file_managers` where size > notification_at or display_dev_master = 1';

        // $projectDirectoryData = DB::select($projectDirectorySql);

        $projectDirectoryData = ProjectFileManager::where('size', '>', 'notification_at')
            ->orWhere('display_dev_master', 1)
            ->get();

        $logRequest = LogRequest::where('status_code', '!=', 200)->whereDate('created_at', date('Y-m-d'))->groupBy('status_code')->select(['status_code', DB::raw('count(*) as total_error')])->get();

        $failedJobs = app(JobRepository::class)->getFailed();

        $scraper_proc = [];

        $scraper_process = ScraperProcess::where('scraper_name', '!=', '')->orderByDesc('started_at')->get()->unique('scraper_id');
        foreach ($scraper_process as $sp) {
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $sp->started_at);
            $from = \Carbon\Carbon::now();
            $diff_in_hours = $to->diffInMinutes($from);
            if ($diff_in_hours > 1440) {
                array_push($scraper_proc, $sp);
            }
        }
        $scrapers = Scraper::where('scraper_name', '!=', '')->whereNotIn('id', $scraper_process->pluck('scraper_id'))->get();

        return view('master-dev-task.index', compact(
            'currentSize',
            'sizeBefore',
            'repoArr',
            'cronjobReports',
            'last3HrsMsg',
            'last24HrsMsg',
            'scrapeData',
            'scraper1hrsReports',
            'scraper24hrsReports',
            'projectDirectoryData',
            'last3HrsJobs',
            'last24HrsJobs',
            'topFiveTables',
            'memory_use',
            'logRequest',
            'failedJobs',
            'scraper_process',
            'scrapers',
            'productErrors'
        ));
    }
}
