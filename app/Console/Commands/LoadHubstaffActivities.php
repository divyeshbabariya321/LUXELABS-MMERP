<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\Helpers\HubstaffTrait;
use App\Hubstaff\HubstaffActivity;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class LoadHubstaffActivities extends Command
{
    use HubstaffTrait;

    private $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:load_activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load activities for users per task from Hubstaff';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client;
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $time = strtotime(date('c'));
            $time = $time - (60 * 60); //one hour
            $startTime = date('c', strtotime(gmdate('Y-m-d H:i:s', $time)));
            $time = strtotime($startTime);
            $time = $time + (10 * 60); //10 mins
            $stopTime = date('c', $time);

            $activities = $this->getActivitiesBetween($startTime, $stopTime);
            if ($activities === false) {
                echo 'Error in activities'.PHP_EOL;

                return;
            }
            echo 'Got activities(count): '.count($activities).PHP_EOL;
            foreach ($activities as $id => $data) {
                HubstaffActivity::updateOrCreate(
                    [
                        'id' => $id,
                    ],
                    [
                        'user_id' => $data['user_id'],
                        'task_id' => is_null($data['task_id']) ? 0 : $data['task_id'],
                        'starts_at' => $data['starts_at'],
                        'tracked' => $data['tracked'],
                        'keyboard' => $data['keyboard'],
                        'mouse' => $data['mouse'],
                        'overall' => $data['overall'],
                    ]
                );

            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function getActivitiesBetween($start, $stop)
    {
        try {
            $response = $this->doHubstaffOperationWithAccessToken(
                function ($accessToken) use ($start, $stop) {
                    $url = 'https://api.hubstaff.com/v2/organizations/'.config('env.HUBSTAFF_ORG_ID').'/activities?time_slot[start]='.$start.'&time_slot[stop]='.$stop;

                    echo $url.PHP_EOL;

                    return $this->client->get(
                        $url,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer '.$accessToken,
                            ],
                        ]
                    );
                },
                true
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $activities = [];

            foreach ($responseJson->activities as $activity) {
                $activities[$activity->id] = [
                    'user_id' => $activity->user_id,
                    'task_id' => $activity->task_id,
                    'starts_at' => $activity->starts_at,
                    'tracked' => $activity->tracked,
                    'keyboard' => $activity->keyboard,
                    'mouse' => $activity->mouse,
                    'overall' => $activity->overall,
                ];
            }

            return $activities;
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;

            return false;
        }
    }
}
