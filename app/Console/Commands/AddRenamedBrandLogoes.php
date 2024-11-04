<?php

namespace App\Console\Commands;

use App\BrandLogo;
use App\CronJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AddRenamedBrandLogoes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:brandLogo';

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
        $path = public_path('brand_logo');

        BrandLogo::truncate();
        try {

            $files = File::allFiles($path);
            foreach ($files as $file) {
                $fileName = basename($file);

                $brand_logo = BrandLogo::where('logo_image_name', $fileName)->first();

                if (! $brand_logo) {
                    $params['logo_image_name'] = $fileName;
                    $params['user_id'] = Auth::id();

                }
            }
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
