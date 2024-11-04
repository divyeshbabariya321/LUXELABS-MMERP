<?php

namespace App\Console\Commands;

use App\Product;
use Exception;
use Illuminate\Console\Command;
use Plank\Mediable\Media;

class ConvertImageIntoThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertImage:toThumbnail';

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
        Media::where('is_processed', 0)
            ->join('mediables', 'mediables.media_id', 'media.id')
            ->where('mediable_type', Product::class)
            ->where('aggregate_type', 'image')->orderBy('id')->chunk(1000, function ($medias) {
                foreach ($medias as $media) {
                    try {
                        $m_url = $media->getAbsolutePath();
                        $file = @file_get_contents($m_url);

                        if (! $file || ! $m_url) {
                            $media->is_processed = 2;
                            $media->save();

                            continue;
                        }

                        $file_info = pathinfo($m_url);

                        $thumb_file_name = $file_info['filename'].'_thumb.'.$file_info['extension'];
                        $thumb_folder = $file_info['dirname'].'/thumbnail';

                        if (! is_dir($thumb_folder)) {
                            mkdir($thumb_folder);
                        }

                        $thumb_file_path = $thumb_folder.'/'.$thumb_file_name;

                        [$original_width, $original_height] = getimagesize($m_url);
                        $thumbnail_width = 150;
                        $thumbnail_height = ($original_height / $original_width) * $thumbnail_width;
                        $is_thumbnail_made = resizeCropImage($thumbnail_width, $thumbnail_height, $m_url, $thumb_file_path, 80);

                        if ($is_thumbnail_made) {
                            $media->is_processed = 1;
                            $media->save();
                        } else {
                            $media->is_processed = 3;
                            $media->save();
                        }
                    } catch (Exception $exception) {
                        $media->is_processed = 3;
                        $media->save();
                    }
                }
            });
    }
}
