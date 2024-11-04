<?php

namespace App\Console\Commands;

use App\GoogleDeveloper;
use App\GoogleDeveloperLogs;
use App\Helpers\LogHelper;
use Google\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DevAPIReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DevAPIReport:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crash and ANR report using playdeveloperreporting check and store DB every hour';

    const PLAY_DEVELOPER_URL = 'https://playdeveloperreporting.googleapis.com/v1beta1/apps/';

    const GOOGLE_DEVELOPER_LOGS = 'saved google developer logs record by ID:';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->logCronStart();

            if ($this->shouldTruncateLogs()) {
                $this->truncateGoogleDeveloperLogs();
            }

            $client = $this->initializeGoogleClient();
            $this->logGoogleApiConnection();

            $token = $this->getAccessToken($client);

            if (! $token && ! isset($_SESSION['token'])) {
                $client->createAuthUrl();

            } else {
                $this->processApps($client);
            }

        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function logCronStart()
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron started to run']);
    }

    private function shouldTruncateLogs(): bool
    {
        return config('settings.google_play_store_delete_logs') == '1';
    }

    private function truncateGoogleDeveloperLogs()
    {
        GoogleDeveloperLogs::truncate();
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'truncate all the records from google_developer_logs table']);
    }

    private function initializeGoogleClient(): Client
    {
        $client = new Client;
        $client->setApplicationName(config('settings.google_play_store_app_id'));
        $client->setDeveloperKey(config('settings.google_play_store_dev_key'));
        $client->setClientId(config('settings.google_play_store_client_id'));
        $client->setClientSecret(config('settings.google_play_store_client_secret'));
        $client->setRedirectUri('https://erpstage.theluxuryunlimited.com/google/developer-api/crash');
        $client->setAuthConfig(storage_path().config('settings.google_play_store_service_credentials'));
        $client->setSubject(config('settings.google_play_store_service_account'));
        $client->setScopes([config('settings.google_play_store_scopes')]);

        return $client;
    }

    private function logGoogleApiConnection()
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'set all the required params for connecting to google api']);
    }

    private function getAccessToken(Client $client): ?array
    {
        $token = $client->isAccessTokenExpired() ? $client->fetchAccessTokenWithAssertion() : $client->getAccessToken();
        $_SESSION['token'] = $token;
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'connected to google api and token stored to session']);

        return $token;
    }

    private function processApps(Client $client)
    {
        $array_app = explode(',', config('settings.google_play_store_app'));
        foreach ($array_app as $app_value) {
            $this->fetchAndProcessCrashReports($app_value);
            $this->fetchAndProcessAnrReports($app_value);
        }
    }

    private function fetchAndProcessCrashReports(string $appValue)
    {
        $accessToken = $_SESSION['token']['access_token'];
        $response = Http::get(self::PLAY_DEVELOPER_URL.$appValue.'/crashRateMetricSet?access_token='.$accessToken);

        if (gettype($response) != 'string' && ! isset($response['error'])) {
            $this->saveCrashReport($response, $appValue);
        } else {
            $this->handleErrorResponse($response, $appValue, 'crash');
        }
    }

    private function fetchAndProcessAnrReports(string $appValue)
    {
        $accessToken = $_SESSION['token']['access_token'];
        $response = Http::get(self::PLAY_DEVELOPER_URL.$appValue.'/anrRateMetricSet?access_token='.$accessToken);

        if (gettype($response) != 'string' && ! isset($response['error'])) {
            $this->saveAnrReport($response, $appValue);
        } else {
            $this->handleErrorResponse($response, $appValue, 'anr');
        }
    }

    private function saveCrashReport($response, $appValue)
    {
        $year = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['year'];
        $day = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['day'];
        $month = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['month'];
        $date = $year.'-'.$month.'-'.$day;

        $report = new GoogleDeveloper;
        $report->name = $response['name'];
        $report->aggregation_period = $response['freshnessInfo']['freshnesses'][0]['aggregationPeriod'];
        $report->latestEndTime = $date;
        $report->timezone = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['timeZone']['id'];
        $report->report = 'crash';
        $report->save();

        LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer crash report by ID: '.$report->id]);

        $postData = [
            'timeline_spec' => [
                'aggregation_period' => 'DAILY',
                'start_time' => ['year' => $year, 'month' => $month, 'day' => $day - 2],
                'end_time' => ['year' => $year, 'month' => $month, 'day' => $day - 1],
            ],
            'dimensions' => ['apiLevel'],
            'metrics' => ['crashRate', 'distinctUsers', 'crashRate28dUserWeighted'],
        ];

        $this->sendCurlRequest($appValue, $postData, 'crash report', 'CRASH REPORT');
        echo 'Crash report of '.$appValue.' added';
    }

    private function saveAnrReport($response, $appValue)
    {
        $year = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['year'];
        $day = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['day'];
        $month = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['month'];
        $date = $year.'-'.$month.'-'.$day;

        $report = new GoogleDeveloper;
        $report->name = $response['name'];
        $report->aggregation_period = $response['freshnessInfo']['freshnesses'][0]['aggregationPeriod'];
        $report->latestEndTime = $date;
        $report->timezone = $response['freshnessInfo']['freshnesses'][0]['latestEndTime']['timeZone']['id'];
        $report->report = 'anr';
        $report->save();

        LogHelper::createCustomLogForCron($this->signature, ['message' => 'saved google developer ANR report by ID: '.$report->id]);

        $postData = [
            'timeline_spec' => [
                'aggregation_period' => 'DAILY',
                'start_time' => ['year' => $year, 'month' => $month, 'day' => $day - 2],
                'end_time' => ['year' => $year, 'month' => $month, 'day' => $day - 1],
            ],
            'dimensions' => ['apiLevel'],
            'metrics' => ['distinctUsers'],
        ];

        $this->sendCurlRequest($appValue, $postData, 'anr report', 'ANR REPORT');
        echo 'ANR report of '.$appValue.' added';
    }

    private function handleErrorResponse($response, $appValue, $reportType)
    {
        if (isset($response['error']) && $response['error']['code'] == 401) {
            session_unset();
            echo '401 error';
        } else {
            $log = new GoogleDeveloperLogs;
            $log->api = $reportType.' error';
            $log->log_name = strtoupper($reportType).' ERROR';
            $log->result = $response;
            $log->save();

            LogHelper::createCustomLogForCron($this->signature, ['message' => self::GOOGLE_DEVELOPER_LOGS.$log->id]);
            echo $reportType.' report of '.$appValue.' failed';
        }
    }

    private function handleException(\Exception $e)
    {
        LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);
        \App\CronJob::insertLastError($this->signature, $e->getMessage());
    }
}
