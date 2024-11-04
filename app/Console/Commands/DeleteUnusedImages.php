<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Plank\Mediable\Media;

class DeleteUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:unused-images';

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

            dd('stap');
            $file_types = [
                'gif',
                'jpg',
                'jpeg',
                'png',
            ];
            $directory = public_path('uploads');
            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $file_types)) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);

                    if (Media::where('filename', '=', $filename)->count()) {
                        dump('in-use');

                        continue; // continue if the picture is in use
                    }

                    echo 'removed'.basename($file).'<br />';
                    unlink($file); // delete if picture isn't in use
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
