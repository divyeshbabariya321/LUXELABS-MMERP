<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Plank\Mediable\Media;

class addImagesInMissingPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addImageInMissingPath:forLocalTest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     *
     * @param  mixed  $path
     * @return mixed
     */
    public function createPath($path)
    {
        $path = explode('/', $path);
        $dir = '/';
        foreach ($path as $key => $f) {
            if ($key == 0 || $key == count($path) - 1) {
                continue;
            }
            $dir .= $f.'/';
            if (! is_dir($dir)) {
                mkdir($dir);
            }
        }
    }

    public function handle(): void
    {
        $files = File::allFiles(public_path('test-images'));
        Media::where('aggregate_type', 'image')->orderBy('id')->chunk(1000, function ($medias) use ($files) {
            foreach ($medias as $media) {
                if ($media->id == 1537928) {
                    continue;
                }
                $randomFile = $files[rand(0, count($files) - 1)];
                $r_file = $randomFile->getRealPath();

                $m_url = $media->getAbsolutePath();
                $file_info = pathinfo($m_url);
                $file_name = $file_info['filename'].'.'.$file_info['extension'];
                $file_full_folder = $file_info['dirname'].'/'.$file_name;
                $this->createPath($m_url);
                copy($r_file, $file_full_folder);
            }
        });
    }
}
