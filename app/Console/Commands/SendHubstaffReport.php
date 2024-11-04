<?php

namespace App\Console\Commands;
use App\CronJob;

use App\ChatMessage;
use App\CronJobReport;
use App\Helpers\HubstaffTrait;
use App\Helpers\LogHelper;
use App\Library\Hubstaff\Src\Hubstaff;
use Carbon\Carbon;
use Exception;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendHubstaffReport extends Command
{
    use HubstaffTrait;

    private $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:send_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends hubstaff report to whatsapp based every hour with details of past hour and today';

    const DATE_FORMATE = 'Y-m-d H:i:s';

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
    public function handle(): int
    {
        //STOPPED CERTAIN MESSAGES
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $userPastHour = $this->getActionsForPastHour();
            $userToday = $this->getActionsForToday();
            $users = User::join('hubstaff_members', 'hubstaff_members.user_id', '=', 'users.id')
                ->select(['hubstaff_user_id', 'name'])
                ->get();

            $hubstaffReport = [];
            foreach ($users as $user) {
                $pastHour = (isset($userPastHour[$user->hubstaff_user_id])
                    ? $this->formatSeconds($userPastHour[$user->hubstaff_user_id])
                    : '0');

                $today = (isset($userToday[$user->hubstaff_user_id])
                    ? $this->formatSeconds($userToday[$user->hubstaff_user_id])
                    : '0');

                if ($today != '0') {
                    $message = $user->name.' '.$pastHour.' '.$today;
                    $hubstaffReport[] = $message;
                }
            }

            $message = implode(PHP_EOL, $hubstaffReport);

            ChatMessage::sendWithChatApi('971502609192', null, $message);
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function formatSeconds($seconds)
    {
        $t = round($seconds);

        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    }

    private function getActionsForPastHour()
    {
        return self::getActivity(date(self::DATE_FORMATE, strtotime('-1 hour')), date(self::DATE_FORMATE));
    }

    private function getActionsForToday()
    {
        return self::getActivity(date('Y-m-d 00:00:00'), date(self::DATE_FORMATE));
    }

    private static function getActivity($startTime, $endTime)
    {
        // start hubstaff section from here
        $hubstaff = Hubstaff::getInstance();
        $hubstaff = $hubstaff->authenticate();
        $organizationAct = $hubstaff->getRepository('organization')->getActivity(
            // env("HUBSTAFF_ORG_ID"),
            config('env.HUBSTAFF_ORG_ID'),
            $startTime,
            $endTime
        );

        $users = [];
        // assign activity to user
        if (! empty($organizationAct->activities)) {
            foreach ($organizationAct->activities as $activity) {
                if (isset($users[$activity->user_id])) {
                    $users[$activity->user_id] += $activity->tracked;
                } else {
                    $users[$activity->user_id] = $activity->tracked;
                }
            }
        }

        return $users;
    }
}
