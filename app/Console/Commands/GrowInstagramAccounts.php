<?php

namespace App\Console\Commands;

use App\Account;
use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GrowInstagramAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:grow-accounts';

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

            $accounts = Account::where('is_seeding', 1)->get();

            foreach ($accounts as $account) {
                $username = $account->last_name;

                $this->warn($username);

                $stage = $account->seeding_stage;

                $account->manual_comment = 1;
                $account->save();

                if ($stage >= 7) {
                    $account->bulk_comment = 1;
                    $account->manual_comment = 0;
                    $account->is_seeding = 0;
                    $account->save();

                    continue;
                }

                $imageSet = [
                    0 => ['1', '2'],
                    1 => ['3', '4'],
                    2 => ['5', '6'],
                    3 => ['7', '8'],
                    4 => ['9', '10'],
                    5 => ['11', '12'],
                    6 => ['13', '14'],
                ];

                $imagesToPost = $imageSet[$stage];

                foreach ($imagesToPost as $i) {
                    $filename = __DIR__.'/images/'.$i.'.jpeg';
                    $source = imagecreatefromjpeg($filename);
                    [$width, $height] = getimagesize($filename);

                    $newwidth = 800;
                    $newheight = 800;

                    $destination = imagecreatetruecolor($newwidth, $newheight);
                    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    imagejpeg($destination, __DIR__.'/images/'.$i.'.jpeg', 100);

                }

                $account->seeding_stage++;
                $account->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
