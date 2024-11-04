<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Plank\Mediable\Media;

class ReplaceJpegImageWithJpg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:jpegtojpg';

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

            $medias = Media::where('extension', 'jpeg')->get();

            foreach ($medias as $media) {
                $absolutePath = $media->getAbsolutePath();
                $newAbsolutePath = substr($absolutePath, 0, -4).'jpg';

                if (file_exists($absolutePath)) {
                    dump('exists..');
                }

                try {
                    rename($absolutePath, $newAbsolutePath);
                    $media->extension = 'jpg';
                    $media->save();
                    dump('done...');
                } catch (Exception $exception) {
                    //
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
