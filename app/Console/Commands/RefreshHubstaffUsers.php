<?php

namespace App\Console\Commands;

use App\CronJobReport;
use App\Helpers\LogHelper;
use App\Hubstaff\HubstaffMember;
use App\Library\Hubstaff\Src\Hubstaff;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use App\CronJob;
use App\User;

class RefreshHubstaffUsers extends Command
{
    public $HUBSTAFF_TOKEN_FILE_NAME;

    public $SEED_REFRESH_TOKEN;

    public $hubstaff;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:refresh_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the Hubstaff users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';
        $this->SEED_REFRESH_TOKEN = config('env.HUBSTAFF_SEED_PERSONAL_TOKEN');
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);
            $this->refreshUserList();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'refresh user list finished.']);
            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshUserList()
    {
        // 1. try to get the list of users
        // 2. If users recieved update in database
        // 3. if users not recieved due to token failure refresh the token and retry

        // start hubstaff section from here
        $hubstaff = Hubstaff::getInstance();
        try {
            $this->hubstaff = $hubstaff->authenticate(false);
            $this->startGetUser();
        } catch (Exception $e) {
            $this->hubstaff = $hubstaff->authenticate(true);
            $this->startGetUser();
        }
    }

    private function startGetUser($startId = 665240)
    {
        $organizationUsers = $this->hubstaff->getRepository('organization')->getOrgUsers(config('env.HUBSTAFF_ORG_ID'), 0, $startId);

        if (! empty($organizationUsers->members)) {
            $record = count($organizationUsers->members);
            echo 'Total Record :'.$record;
            foreach ($organizationUsers->members as $member) {
                echo $member->user_id.' Record started';
                echo PHP_EOL;
                $memeberExist = HubstaffMember::where('hubstaff_user_id', $member->user_id)->first();
                if (! $memeberExist) {
                    $userDetails = $this->hubstaff->getRepository('user')->getUserDetail($member->user_id);
                    if (! empty($userDetails)) {
                        $member->email = $userDetails->user->email;
                    }

                    if (! empty($member->email)) {
                        $userExist = User::where('email', $member->email)->first();
                        HubstaffMember::create([
                            'hubstaff_user_id' => $member->user_id,
                            'email' => $member->email,
                            'user_id' => ($userExist) ? $userExist->id : null,
                        ]);
                    }
                }

                echo $member->user_id.' Record eneded';
                echo PHP_EOL;
                echo 'Total Record Left :'.$record--;
                echo PHP_EOL;
            }

            if (! empty($organizationUsers->pagination) && ! empty($organizationUsers->pagination->next_page_start_id)) {
                $this->startGetUser($organizationUsers->pagination->next_page_start_id);
            }
        }
    }
}
