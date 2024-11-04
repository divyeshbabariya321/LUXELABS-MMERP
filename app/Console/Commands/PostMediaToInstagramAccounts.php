<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class PostMediaToInstagramAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:post-media-to-accounts';

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

            $accounts = 'shrikirtiraha23,balachander83,ashnauppalapati81,vinayafalodiya55';
            $accounts = explode(',', $accounts);

            foreach ($accounts as $account) {
                try {
                    //
                } catch (Exception $exception) {
                    continue;
                }

                for ($i = 1; $i < 10; $i++) {
                    echo "FOR $account \n";
                    $filename = __DIR__.'/images/'.$i.'.jpeg';
                    $source = imagecreatefromjpeg($filename);
                    [$width, $height] = getimagesize($filename);

                    $newwidth = 800;
                    $newheight = 800;

                    $destination = imagecreatetruecolor($newwidth, $newheight);
                    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                    imagejpeg($destination, __DIR__.'/images/'.$i.'.jpeg', 100);

                    sleep(10);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
