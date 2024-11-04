<?php

namespace App\Console\Commands;

use App\ColdLeads;
use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class FilterColdLeadByPostCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filter:cold-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */

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

            $coldLeads = ColdLeads::orderByDesc('id')->get();

            $instagram = new Instagram;
            $instagram->login('sololuxury.official', "NcG}4u'z;Fm7");

            foreach ($coldLeads as $coldLead) {
                $username = $coldLead->username;

                try {
                    $coldLeadInstagram = $instagram->people->getInfoByName($username)->asArray();
                } catch (Exception $exception) {
                    continue;
                }

                echo "$username \n";

                $user = $coldLeadInstagram['user'];

                if ($user['media_count'] < 20) {
                    try {
                        echo "DELETE \n";
                        $coldLead->delete();
                    } catch (Exception $exception) {
                        continue;
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
