<?php

namespace App\Console\Commands;

use App\ColorNamesReference;
use Illuminate\Console\Command;

class ChangeErpColorCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-color:erp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Erp Color';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $colors = ColorNamesReference::where('color_code', '')->get();
        if (! $colors->isEmpty()) {
            foreach ($colors as $color) {
                $str = self::stringToColorCode($color);
                if (! empty($str)) {
                    $color->color_code = $str;
                    $color->save();
                }
            }
        }
    }

    public static function stringToColorCode($str)
    {
        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);

        return $code;
    }
}
